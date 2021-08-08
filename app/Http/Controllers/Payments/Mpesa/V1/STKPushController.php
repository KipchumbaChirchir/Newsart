<?php

namespace App\Http\Controllers\Payments\Mpesa\V1;

use App\Http\Controllers\Controller;
use App\Models\Payments\Mpesa\STKFailedTransactions;
use Illuminate\Support\Str;
use App\Models\Payments\Mpesa\STKTransactions;
use Illuminate\http\Request;
use Illuminate\Support\Carbon;

class STKPushController extends Controller
{
    // function check if imternet is available
    public function ifConnected()
    {
        if (!$sock = @fsockopen('www.google.com', 80)) {
            echo 'Not Connected';
        } else {
            echo 'Connected';
        }
    }
    /*
        Generate access token
    */
    public function generateAccessToken()
    {

        $consumer_key = env('MPESA_CONSUMER_KEY');
        $consumer_secret = env('MPESA_CONSUMER_SECRET');

        if (!$consumer_key || !$consumer_secret) {
            die("Something happened with the keys");
        }

        $credentials = base64_encode($consumer_key . ":" . $consumer_secret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);
        if (!$access_token) {
            return "Something else";
        } else {
            $token = $access_token->access_token;
            return $token;
        }
    }
    /*
    Generate Password
    */
    public function lipaNaMpesaPassword()
    {
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = env('MPESA_PASS_KEY');
        $BusinessShortCode = env('MPESA_BUSINESS_SHORTCODE');
        $timestamp = $lipa_time;
        $lipa_na_mpesa_password = base64_encode($BusinessShortCode . $passkey . $timestamp);
        return $lipa_na_mpesa_password;
    }
    /*
    Initiate Transaction on Customer's Behalf
    */
    public function customerMpesaSTKPush()
    {
        $env = env('MPESA_ENV');

        if ($env == "sandbox") {
            $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        } elseif ($env == "live") {

            $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        } else {
            return "Sorry... undefined environment";
        }

        $phone_number = 254715738974;
        $string_value = Str::random(4);
        $string_value_upper = strtoupper($string_value);
        $integer_value = rand(1001, 9999);
        $account_reference = $integer_value . $string_value_upper;
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $this->generateAccessToken()));

        $curl_post_data = [

            'BusinessShortCode' => env('MPESA_BUSINESS_SHORTCODE'),
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => 1000,
            'PartyA' => $phone_number,
            'PartyB' => env('MPESA_BUSINESS_SHORTCODE'),
            'PhoneNumber' => $phone_number,
            'CallBackURL' => env('CALLBACK_URL') . '/api/callback/url',
            'AccountReference' => $account_reference,
            'TransactionDesc' => "Testing STK Push"
        ];

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);

        if (!$curl_response) {
            return "An error occured!";
        } else {
            return $curl_response;
        }
    }

    public function mpesaResponse(Request $request)
    {
        $content = json_decode($request->getContent());
        $result_code = $content->Body->stkCallback->ResultCode;
        $result_description = $content->Body->stkCallback->ResultDesc;

        if ($result_code == 0) {

            $transaction = new STKTransactions();
            $transaction->merchant_request_id = $content->Body->stkCallback->MerchantRequestID;
            $transaction->checkout_request_id = $content->Body->stkCallback->CheckoutRequestID;
            $transaction->result_description = $content->Body->stkCallback->ResultDesc;
            $transaction->result_code = $content->Body->stkCallback->ResultCode;
            $transaction->amount = $content->Body->stkCallback->CallbackMetadata->Item[0]->Value;
            $transaction->transaction_id = $content->Body->stkCallback->CallbackMetadata->Item[1]->Value;
            $transaction->transaction_date = $content->Body->stkCallback->CallbackMetadata->Item[3]->Value;
            $transaction->phone_number = $content->Body->stkCallback->CallbackMetadata->Item[4]->Value;
            // $transaction->status = json_encode($content);

            $transaction->save();
        } elseif ($result_code == 1) {
            $transaction = new STKFailedTransactions();

            $transaction->merchant_request_id = $content->Body->stkCallback->MerchantRequestID;
            $transaction->checkout_request_id = $content->Body->stkCallback->CheckoutRequestID;
            $transaction->result_description = $content->Body->stkCallback->ResultDesc;
            $transaction->result_code = $content->Body->stkCallback->ResultCode;
            $transaction->status = json_encode($content);

            $transaction->save();
        } else {
            $transaction = new STKFailedTransactions();
            $transaction->status = json_encode($content);
            return $result_description;
        }
    }
}

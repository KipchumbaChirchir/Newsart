<?php

namespace App\Http\Controllers\Payments\Mpesa\V1;

use App\Http\Controllers\Controller;
use App\Models\Payments\Mpesa\MpesaTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class MpesaC2BController extends Controller
{
    /*
        Generate access token
    */
    public function generateAccessToken()
    {
        $consumer_key = env('MPESA_CONSUMER_KEY');
        $consumer_secret = env('MPESA_CONSUMER_SECRET');
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
            return json_encode("Something went wrong");
        }else{
        return $access_token->access_token;
        }
    }

    /**
     * J-son Response to M-pesa API feedback - Success or Failure
     */
    public function createValidationResponse($result_code, $result_description){
        $result = json_encode([
            "ResultCode"=>$result_code,
            "ResultDesc"=>$result_description
        ]);

        $response = new Response();
        $response->headers->set("Content-Type","application/json; charset=utf-8");
        $response->setContent($result);
        return $response;
    }
    /**
     *  M-pesa Validation Method
     *  Accepting Payments from the clients
     */
    public function mpesaValidation(Request $request)
    {
        $result_code = "0";
        $result_description = "Accepted validation request.";
        return $this->createValidationResponse($result_code, $result_description);
    }
    /**
     * M-pesa Transaction confirmation method, we save the transaction in the databases
     */
    public function mpesaConfirmation(Request $request)
    {
        $content = json_decode($request->getContent());

        $mpesa_transaction = new MpesaTransactions();

        // $mpesa_transaction->transaction_type = $content->TransactionType;
        // $mpesa_transaction->transaction_id = $content->TransID;
        // $mpesa_transaction->transaction_time = $content->TransTime;
        // $mpesa_transaction->amount = $content->TransAmount;
        // $mpesa_transaction->business_short_code = $content->BusinessShortCode;
        // $mpesa_transaction->bill_ref_number = $content->BillRefNumber;
        // $mpesa_transaction->invoice_number = $content->InvoiceNumber;
        // $mpesa_transaction->organization_acc_balance = $content->OrgAccountBalance;
        // $mpesa_transaction->third_party_transaction_id = $content->ThirdPartyTransID;
        // $mpesa_transaction->phone_number = $content->MSISDN;
        // $mpesa_transaction->first_name = $content->FirstName;
        // $mpesa_transaction->middle_name = $content->MiddleName;
        // $mpesa_transaction->last_name = $content->LastName;
        $mpesa_transaction->status = json_encode($content);
        $mpesa_transaction->save();

        // Responding to the confirmation request
        $response = new Response();
        $response->headers->set("Content-Type","text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult"=>"Success"]));

        return $response;
    }

    /**
     * M-pesa Register Validation and Confirmation method
     */
    public function mpesaRegisterUrls()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '. $this->generateAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => "600141",
            'ResponseType' => 'Completed',
            'ConfirmationURL' => env('CALLBACK_URL').'/api/transaction/confirmation',
            'ValidationURL' => env('CALLBACK_URL').'/api/validation'
        )));
        $curl_response = curl_exec($curl);
        echo $curl_response;
    }
}

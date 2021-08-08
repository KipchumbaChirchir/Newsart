<?php

namespace App\Http\Controllers\SMS\ORACOM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSController extends Controller
{
    // Setting API Login Credentials
    public function smsPassword(){
        $username = env('SMS_USERNAME');
        $password = env('SMS_PASSWORD');

        $credentials = base64_encode($username.":".$password);

        return $credentials;
    }

    // Send SMS Now
    public function sendSMS(){
        // $url = env('SMS_BASE_URL').'/restapi/sms/1/text/single';
        $url = "https://sms.xreal.co.ke/restapi/sms/1/text/single";

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            // 'Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ=='
            'Authorization: Basic' . $this->smsPassword()
        ]);

        $curl_post_data = [
            'from' => 'InfoSMS',
            'to' => '254715738974',
            'text' => 'Test'
        ];

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        if (!$curl_response) {
            return json_encode([
                'errorId' => 400,
                'errorMessage' => "Cannot send message!"
            ]);
        }else {
            return $curl_response;
        }
    }
}

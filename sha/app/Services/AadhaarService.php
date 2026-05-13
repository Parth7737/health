<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\{ AadhaarOtpVerification, AadhaarInformation };
use Carbon\Carbon;


class AadhaarService
{
    protected $client;
    protected $apiBaseUrl;
    protected $apiKey;
    protected $apiSecret;
    protected $apiVersion;
    protected $authToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiBaseUrl = env('AADHAAR_API_BASE_URL', 'https://api.sandbox.co.in');
        $this->apiKey = env('AADHAAR_API_KEY');
        $this->apiSecret = env('AADHAAR_API_SECRET');
        $this->apiVersion = '2.0';
        // $this->authToken = $this->authenticate();
    }

    private function authenticate()
    {
       
        $url = "{$this->apiBaseUrl}/authenticate";
        $headers = [
            'accept'        => 'application/json',
            'x-api-key'     => $this->apiKey,
            'x-api-secret'  => $this->apiSecret,
            'x-api-version' => $this->apiVersion,
        ];
       
        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
            ]);
           
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['access_token'] ?? null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
           
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $responseBody['message'] ?? 'Unknown error from API.';
            } else {
                $errorMessage = "No response from API.";
            }
    
            return ["success" => false, "message" => $errorMessage];
        }
    }

    public function sendOtp($aadhaarNumber)
    {
        $authToken = $this->authenticate();
        if (!$authToken) {
            return ["success" => false, "message" => 'Authentication failed.'];
        }

        $url = "{$this->apiBaseUrl}/kyc/aadhaar/okyc/otp";

        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => $authToken,
            'X-Api-Key'     => $this->apiKey,
            'X-Api-Version' => $this->apiVersion,
            'Content-Type'  => 'application/json',
        ];

        $body = [
            '@entity'        => 'in.co.sandbox.kyc.aadhaar.okyc.otp.request',
            'aadhaar_number' => $aadhaarNumber,
            'consent'        => 'Y',
            'reason'         => 'For KYC',
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json'    => $body,
            ]);
            $data = json_decode($response->getBody()->getContents(), true);

            if($data['data']['message'] == "OTP sent successfully") {
                $adata = AadhaarInformation::updateOrCreate(["aadhaar_no" => $aadhaarNumber],["reference_id" => $data['data']["reference_id"]]);

                $verificationdata = AadhaarOtpVerification::updateOrCreate(["aadhaar_no" => $aadhaarNumber, "aadhaar_id" => $adata->id, 'reference_id' => $data['data']["reference_id"]],["reference_id" => $data['data']["reference_id"]]);

                return ["success" => true, "message" => $data['data']['message'], 'reference_id' => base64_encode($data['data']["reference_id"])];
            } else {
                $message = "Something went wrong!!";
                if (isset($data['data']['message'])) {
                    $message = $data['data']['message'];
                } elseif (isset($data['message'])) {
                    $message = $data['message'];
                }

                return ["success" => false, "message" => $message];
            }

            // return json_decode($response->getBody()->getContents(), true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
           
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $responseBody['message'] ?? 'Unknown error from API.';
            } else {
                $errorMessage = "No response from API.";
            }
    
            return ["success" => false, "message" => $errorMessage];
        }
    }

    public function verifyOtp($aadhaarNumber, $otp, $reference_id)
    {
        $authToken = $this->authenticate();
        if (!$authToken) {
            return ["success" => false, "message" => 'Authentication failed.'];
        }

        $url = "{$this->apiBaseUrl}/kyc/aadhaar/okyc/otp/verify";

        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => $authToken,
            'X-Api-Key'     => $this->apiKey,
            'X-Api-Version' => $this->apiVersion,
            'Content-Type'  => 'application/json',
        ];

        $body = [
            '@entity'        => 'in.co.sandbox.kyc.aadhaar.okyc.request',
            'reference_id' => $reference_id,
            'otp'            => $otp,
        ];

        try {
            $response = $this->client->post($url, [
                'headers' => $headers,
                'json'    => $body,
            ]);

            $responsedata = json_decode($response->getBody()->getContents(), true);
            
            $data = $responsedata['data'];
            if($data && isset($data['status']) && $data['status'] == "VALID") {
                $birthdate = isset($data['date_of_birth']) ? $data['date_of_birth'] : '';
                if($birthdate) {
                    $age = Carbon::parse($birthdate)->age;
                } else {
                    $age = '';
                }

                $aadhaar = AadhaarInformation::updateOrCreate(["aadhaar_no" => $aadhaarNumber], [
                    'status' => isset($data['status']) ? $data['status'] : '',
                    'care_of' => isset($data['care_of']) ? $data['care_of'] : '',
                    'full_address' => isset($data['full_address']) ? $data['full_address'] : '',
                    'date_of_birth' => $birthdate,
                    'email_hash' => isset($data['email_hash']) ? $data['email_hash'] : '',
                    'age' => $age,
                    'gender' => isset($data['gender']) ? $data['gender'] : '',
                    'name' => isset($data['name']) ? $data['name'] : '',
                    'country' => isset($data['address']['country']) ? $data['address']['country'] : '',
                    'district' => isset($data['address']['district']) ? $data['address']['district'] : '',
                    'house' => isset($data['address']['house']) ? $data['address']['house'] : '',
                    'landmark' => isset($data['address']['landmark']) ? $data['address']['landmark'] : '',
                    'pincode' => isset($data['address']['pincode']) ? $data['address']['pincode'] : '',
                    'post_office' => isset($data['address']['post_office']) ? $data['address']['post_office'] : '',
                    'state' => isset($data['address']['state']) ? $data['address']['state'] : '',
                    'street' => isset($data['address']['street']) ? $data['address']['street'] : '',
                    'subdistrict' => isset($data['address']['subdistrict']) ? $data['address']['subdistrict'] : '',
                    'vtc' => isset($data['address']['vtc']) ? $data['address']['vtc'] : '',
                    'year_of_birth' => isset($data['year_of_birth']) ? $data['year_of_birth'] : '',
                    'mobile_hash' => isset($data['mobile_hash']) ? $data['mobile_hash'] : '',
                    'photo' => isset($data['photo']) ? $data['photo'] : '',
                    'is_verify' => 1,
                ]);

                $verificationdata = AadhaarOtpVerification::updateOrCreate(["aadhaar_no" => $aadhaarNumber, "aadhaar_id" => $aadhaar->id, 'reference_id' => $reference_id],["is_verify" => 1]);

                return ["success" => true, "message" => $data['message']];
            } else {
                $message = "Something went wrong!!";

                if (isset($data['message'])) {
                    $message = $data['message'];
                } elseif (isset($responsedata['message'])) {
                    $message = $responsedata['message'];
                }
                return ["success" => false, "message" => $message];
            }
                
        } catch (\GuzzleHttp\Exception\RequestException $e) {
           
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $responseBody['message'] ?? 'Unknown error from API.';
            } else {
                $errorMessage = "No response from API.";
            }
    
            return ["success" => false, "message" => $errorMessage];
        }
    }
}

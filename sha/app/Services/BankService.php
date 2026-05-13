<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Models\{ AadhaarOtpVerification, AadhaarInformation };
use Carbon\Carbon;


class BankService
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
        } catch (RequestException $e) {
            return null;
        }
    }
    
    public function getBankDetails($ifsc)
    {
        $authToken = $this->authenticate();
        if (!$authToken) {
            return ["success" => false, "message" => 'Authentication failed.'];
        }

        $url = "{$this->apiBaseUrl}/bank/{$ifsc}";
        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => $authToken,
            'X-Api-Key'     => $this->apiKey,
            'X-Api-Version' => $this->apiVersion,
            'x-accept-cache' => 'true',
        ];

        try {
            $response = $this->client->get($url, ['headers' => $headers]);
            $data = json_decode($response->getBody()->getContents(), true);
            $bankdetails = [];
            if($data) {
                $bankdetails['branch'] = isset($data['BRANCH']) ? $data['BRANCH'] : '';
                $bankdetails['centre'] = isset($data['CENTRE']) ? $data['CENTRE'] : '';
                $bankdetails['district'] = isset($data['DISTRICT']) ? $data['DISTRICT'] : '';
                $bankdetails['state'] = isset($data['STATE']) ? $data['STATE'] : '';
                $bankdetails['address'] = isset($data['ADDRESS']) ? $data['ADDRESS'] : '';
                $bankdetails['cotact'] = isset($data['CONTACT']) ? $data['CONTACT'] : '';
                $bankdetails['micr'] = isset($data['MICR']) ? $data['MICR'] : '';
                $bankdetails['rtgs'] = isset($data['RTGS']) ? $data['RTGS'] : '';
                $bankdetails['city'] = isset($data['CITY']) ? $data['CITY'] : '';
                $bankdetails['neft'] = isset($data['NEFT']) ? $data['NEFT'] : '';
                $bankdetails['imps'] = isset($data['IMPS']) ? $data['IMPS'] : '';
                $bankdetails['bank'] = isset($data['BANK']) ? $data['BANK'] : '';
                $bankdetails['bankcode'] = isset($data['BANKCODE']) ? $data['BANKCODE'] : '';
                $bankdetails['ifsc'] = isset($data['IFSC']) ? $data['IFSC'] : '';
                $nftrgs = 'No';
                if($data['NEFT'] || $data['RTGS']) {
                    $nftrgs = 'Yes';
                }
                $bankdetails['neft_rtgs'] = $nftrgs;
                return ["success" => true, "data" => $bankdetails, 'message' => 'Bank Details Found!!'];
            } else {
                return ['success' => false, 'message' => "Not Found!!"];
            }
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
           
            if ($e->hasResponse()) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $responseBody['message'] ?? 'Unknown error from API.';
            } else {
                $errorMessage = "No response from API.";
            }
    
            return ["success" => false, "message" => "API error: " . $errorMessage];
        }
    }

    public function getaccountdetails($ifsc, $accountnumber)
    {
        $authToken = $this->authenticate();
        if (!$authToken) {
            return ["success" => false, "message" => 'Authentication failed.'];
        }

        $url = "{$this->apiBaseUrl}/bank/{$ifsc}/accounts/{$accountnumber}/penniless-verify";
        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => $authToken,
            'X-Api-Key'     => $this->apiKey,
            'X-Api-Version' => $this->apiVersion,
            'x-accept-cache' => 'true',
        ];

        try {

            $response = $this->client->get($url, ['headers' => $headers]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['code']) && $data['code'] == 503 || $data['code'] == 500 || $data['code'] == 422) {
                return ['success' => false, 'message' => $data['message']];
            }
    
            $bankdetails = [];

            $message = "Something went wrong!!";
            if (isset($data['data']['message'])) {
                $message = $data['data']['message'];
            } elseif (isset($data['message'])) {
                $message = $data['message'];
            }

            if($data['data']) {
                $bankdetails['name_at_bank'] = isset($data['data']['name_at_bank']) ? $data['data']['name_at_bank'] : '';
                return ["success" => true, "data" => $bankdetails, 'message' => $message];
            } else {
                return ['success' => false, 'message' => $message];
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

    public function getGstDetails($gstnumber) {
        $authToken = $this->authenticate();
        if (!$authToken) {
            return ["success" => false, "message" => 'Authentication failed.'];
        }

        $url = "{$this->apiBaseUrl}/gst/compliance/public/gstin/search";
        $headers = [
            'Accept'        => 'application/json',
            'Authorization' => $authToken,
            'X-Api-Key'     => $this->apiKey,
            'X-Api-Version' => $this->apiVersion,
            'x-accept-cache' => 'true',
        ];

        $body = [
            'gstin'        => $gstnumber,
        ];

        try {

            $response = $this->client->post($url, [
                'headers' => $headers,
                'json'    => $body,
            ]);
                        
            $data = json_decode($response->getBody()->getContents(), true);
            if (isset($data['code']) && $data['code'] == 503 || $data['code'] == 500 || $data['code'] == 422) {
                return ['success' => false, 'message' => $data['message']];
            }
    
            $bankdetails = [];

            $message = "Something went wrong!!";
            if (isset($data['data']['status_cd']) && $data['data']['status_cd'] == 1) {
                $message = "GST Number Valid!!";
            } elseif (isset($data['data']['status_cd']) && $data['data']['status_cd'] == 0) {
                $message = "GST Number Not Found!!";
            }

            if($data['data']['data']) {
                $bankdetails['gstname'] = isset($data['data']['data']['lgnm']) ? $data['data']['data']['lgnm'] : '';
                return ["success" => true, "data" => $bankdetails, 'message' => $message];
            } else {
                return ['success' => false, 'message' => $message];
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

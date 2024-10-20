<?php

namespace App\Gateways;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BocGateway 
{
    public static function getToken()
    {
        // Replace with your actual client id and secret
        $clientId = '4c13ca5d5234603dfb3228c381d7d3ac';
        $clientSecret = '405141d105a140cc3afe5ada3b543bdd';

        // Prepare the request data
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'TPPOAuth2Security',
        ];

        // Send the POST request
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/token', $data);

        // Check the response status
        if ($response->successful()) {
            // Handle the successful response
            $accessToken = $response->json()['access_token'];
            return $accessToken;
        } else {
            // Handle the error response
            return false;
        }
    }


    public static function createSubscription($oauthToken, $timestamp, $guid) {
        $client = new Client();
    
        // Prepare the request data
        $data = [
            "accounts" => [
                "transactionHistory" => true,
                "balance" => true,
                "details" => true,
                "checkFundsAvailability" => true
            ],
            "payments" => [
                "limit" => 99999999,
                "currency" => "EUR",
                "amount" => 999999999
            ]
        ];
    
        try {
            // Send the POST request
            $response = $client->post('https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/v1/subscriptions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $oauthToken,
                    'Content-Type' => 'application/json',
                    'timeStamp' => $timestamp,
                    'journeyId' => $guid,
                ],
                'json' => $data // Automatically converts to JSON
            ]);
    
            // Handle the successful response
            $responseBody = json_decode($response->getBody(), true);
            return $responseBody;
    
        } catch (RequestException $e) {
            // Handle the error response
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                return [
                    'error' => true,
                    'message' => json_decode($errorResponse->getBody(), true),
                    'status' => $errorResponse->getStatusCode(),
                ];
            }
    
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }


    public static function requestToken($authCode){
        $clientId = '4c13ca5d5234603dfb3228c381d7d3ac';
        $clientSecret = '405141d105a140cc3afe5ada3b543bdd';
    
        // Prepare the request data
        $data = [
            'grant_type' => 'authorization_code',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $authCode, // The authorization code
            'scope' => 'UserOAuth2Security',
        ];
    
        // Send the POST request
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/token', $data);
    
        // Check the response status
        if ($response->successful()) {
            // Handle the successful response
            $accessToken = $response->json()['access_token'];
            return $accessToken;
        } else {
         // Handle the error response
         $statusCode = $response->status(); // HTTP status code
         $errorMessage = $response->json()['error'] ?? 'An error occurred'; // Error message
         $errorDescription = $response->json()['error_description'] ?? 'No error description provided';
 
         // Return a detailed error message with status code, error, and description
         return [
             'status' => $statusCode,
             'error' => $errorMessage,
             'description' => $errorDescription
         ];
        }
    }


    public static function updateSubscriptionData($subscriptionId, $oauthCode, $guid, $timestamp)
    {
        // Construct the URL with the subscription ID
        $url = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/v1/subscriptions/{$subscriptionId}";
    
        // Send the GET request
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$oauthCode}",
            'Content-Type' => 'application/json',
            'journeyid' => $guid,
            'timestamp' => $timestamp,
        ])->get($url);
    
        // Check the response status
        if ($response->successful()) {
            // Handle the successful response
            return $response->json()[0]['subscriptionId'];
        } else {
            // Handle the error response
            return [
                'status' => $response->status(),
                'error' => $response->json()['error'] ?? 'An error occurred',
                'description' => $response->json()['error_description'] ?? 'No error description provided',
            ];
        }
    }



    public static function patchSubscription($subscriptionId, $oauthCode, $guid, $timestamp){
    // Construct the URL with the subscription ID
    $url = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/v1/subscriptions/{$subscriptionId}";

    // Prepare the request data
    $data = [
        'accounts' => [
            'transactionHistory' => true,
            'balance' => true,
            'details' => true,
            'checkFundsAvailability' => true,
        ],
        'payments' => [
            'limit' => 50,
            'currency' => 'string', // Replace with the actual currency
            'amount' => 50,
        ],
    ];

    // Send the PATCH request
    $response = Http::withHeaders([
        'Authorization' => "Bearer {$oauthCode}",
        'Content-Type' => 'application/json',
        'journeyId' => $guid,
        'timestamp' => $timestamp,
    ])->patch($url, $data);

    // Check the response status
    if ($response->successful()) {
        // Handle the successful response
        $result = $response->json();
        return ['subscriptionId'=> $result['subscriptionId'],'accountId'=> $result['selectedAccounts'][0]['accountId']];
    } else {
        // Handle the error response
        return [
            'status' => $response->status(),
            'error' => $response->json()['error'] ?? 'An error occurred',
            'description' => $response->json()['error_description'] ?? 'No error description provided',
        ];
    }
}

public static function getAccountDetails($accountNumber, $oauthCode, $subscriptionId, $uuid, $timestamp)
{
    // Define the URL for the API endpoint
    $url = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/v1/accounts/{$accountNumber}";

    // Send the GET request
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => "Bearer {$oauthCode}",
        'subscriptionId' => $subscriptionId,
        'journeyId' => $uuid,
        'timestamp' => $timestamp,
    ])->get($url);

    // Check the response status
    if ($response->successful()) {
        // Handle the successful response
        return $response->json();
    } else {
        // Handle the error response
        return [
            'status' => $response->status(),
            'error' => $response->json()['error'] ?? 'An error occurred',
            'description' => $response->json()['error_description'] ?? 'No error description provided',
        ];
    }
}
    public static function getAccounts($oauthToken, $subscriptionId, $timestamp,$uuid) {
        $client = new Client();
    
        try {
            // Send the GET request
            $response = $client->get('https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/v1/accounts', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $oauthToken,
                    'subscriptionId' => $subscriptionId,
                    'journeyId' => $uuid,
                    'timestamp' => $timestamp,
                ],
            ]);
    
            // Handle the successful response
            $responseBody = json_decode($response->getBody(), true);
            return $responseBody;
    
        } catch (RequestException $e) {
            // Handle the error response
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                return [
                    'error' => true,
                    'message' => json_decode($errorResponse->getBody(), true),
                    'status' => $errorResponse->getStatusCode(),
                ];
            }
    
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
    
}
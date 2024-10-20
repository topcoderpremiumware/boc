<?php

namespace App\Http\Controllers;

use App\Gateways\ChatGPTGateway;
use App\Gateways\BocGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ChatController extends Controller
{
    /**
     * @param int $user_id some user identifier
     * @param Request $request
     * @return JsonResponse
     *
     * @unauthenticated
     * @response array{answer: string}
     * @requestMediaType multipart/form-data
     */
    public function message(int $user_id, Request $request): JsonResponse
    {
        $request->validate([
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:1024|mimes:txt'
        ]);

        $file_upload = $request->file('file');
        if($file_upload){
            $content = $file_upload->getContent();
        }else{
            $content = '';
        }

        $answer = ChatGPTGateway::answer($user_id,$request->text.' '.$content);
        return response()->json(['answer' => $answer]);
    }

    public function getBocData(Request $request){
        $token = BocGateway::getToken();
        $result = BocGateway::createSubscription($token,time(),"4ba4a45c-e10d-422d-89e2-2d840c056dbd");
        $subscriptionId = $result['subscriptionId'];
        // Prepare the authorization URL with the subscription ID
        $clientId = '4c13ca5d5234603dfb3228c381d7d3ac'; // Replace with your actual client ID
        $redirectUri = 'https://boc.vasilkoff.info/'; // Replace with your actual redirect URI
        $scope = 'UserOAuth2Security';

        // Build the authorization URL
        $baseUrl = "https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/authorize";

        // Parameters for the query string
        $params = [
            "response_type" => "code",
            "redirect_uri" => $redirectUri,  // Your redirect URI
            "scope" => "UserOAuth2Security",
            "client_id" => $clientId,        // Your client ID
            "subscriptionId" => $subscriptionId
        ];
        
        // Build the full URL with query parameters
        $authorizationUrl = $baseUrl . '?' . http_build_query($params);
        // $authorizationUrl = sprintf(
        //     'https://sandbox-apis.bankofcyprus.com/df-boc-org-sb/sb/psd2/oauth2/authorize?response_type=code&redirect_uri=%s&scope=%s&client_id=%s&subscriptionid=%s',
        //     urlencode($redirectUri),
        //     urlencode($scope),
        //     urlencode($clientId),
        //     urlencode($subscriptionId)
        // );
        

        // return redirect($authorizationUrl);
        // return response()->json([
        //     'authorizationUrl' => $authorizationUrl
        // ]);
        // $secondToken = BocGateway::requestSecondOAuthToken();

        // $patchSubscription = BocGateway::patchSubscription($token,$subscriptionId,time(),"9687567465");

        // $account = BocGateway::getAccounts($token,$subscriptionId,time(),"9687567465");
        return response()->json($authorizationUrl);

    }
}

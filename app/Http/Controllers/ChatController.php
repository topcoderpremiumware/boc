<?php

namespace App\Http\Controllers;

use App\Gateways\ChatGPTGateway;
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
}

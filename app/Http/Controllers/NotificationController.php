<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    protected function sendNotification(Request $request)
    {
            $user_id = auth()->id();
            $fcm_token = User::find($user_id)->fcm_token;
            $server_key = env("FCM_SERVER_KEY");

            $fcm = Http::acceptJson()->withToken($server_key)->post(
                "https://fcm.googleapis.com/fcm/send",
                [
                    "to" => $fcm_token,
                    "notification" => [
                        "title" => "$request->title",
                        "body" => "$request->message",
                    ],
                ]
            );
            return json_decode($fcm);
    }
    public function refreshToken(Request $request)
    {

        $user_id = auth()->id();
        $fcm_token = $request->fcm_token;
        $user = User::where("id" , $user_id)->first();
        $user->update(
            [
                "fcm_token" => $fcm_token,
            ]
            );

    }
}

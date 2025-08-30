<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google_Client;
use Google_Service_Gmail;


class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri('http://localhost:8000/oauth/google/callback');
        $client->addScope([
            Google_Service_Gmail::GMAIL_READONLY,
            Google_Service_Gmail::GMAIL_SEND,
            Google_Service_Gmail::GMAIL_MODIFY,
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return redirect($client->createAuthUrl());
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri('http://localhost:8000/oauth/google/callback');

        $client->authenticate($request->get('code'));

        $tokens = $client->getAccessToken();
        dd($tokens);

        // Save tokens securely (preferably encrypted in DB)
        $user = Auth::user();
        $user->update([
            'gmail_access_token' => $tokens['access_token'],
            'gmail_refresh_token' => $tokens['refresh_token'] ?? null,
        ]);

        return response()->json(['message' => 'Gmail connected successfully!']);
    }
}

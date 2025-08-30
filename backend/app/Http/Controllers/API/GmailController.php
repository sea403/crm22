<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webklex\IMAP\Facades\Client;

class GmailController extends Controller
{
    protected function getClient()
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setIncludeGrantedScopes(true);
        $client->addScope(Google_Service_Gmail::GMAIL_READONLY);
        $client->addScope(Google_Service_Gmail::GMAIL_SEND);

        return $client;
    }

    public function redirectToGoogle()
    {
        $client = $this->getClient();

        $userId = auth()->id();

        // Add state param with user ID (encode it to avoid tampering, optional)
        $authUrl = $client->createAuthUrl() . '&state=' . $userId;

        return response()->json(['url' => $authUrl]);
    }

    public function handleCallback(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $client = $this->getClient();

        if ($request->has('code')) {
            $client->authenticate($request->code);
            $token = $client->getAccessToken();

            $user->gmail_access_token     = $token['access_token'];
            $user->gmail_refresh_token    = $token['refresh_token'] ?? $user->gmail_refresh_token;
            $user->gmail_token_expires_at = now()->addSeconds($token['expires_in']);
            $user->save();

            return response()->json(['message' => 'Gmail connected successfully.']);
        }

        return response()->json(['error' => 'Authorization failed'], 400);
    }


    public function fetchEmails()
    {
        $user = Auth::user();
        $client = $this->getClient();
        $client->setAccessToken([
            'access_token' => $user->gmail_access_token,
            'refresh_token' => $user->gmail_refresh_token,
            'expires_in' => Carbon::parse($user->gmail_token_expires_at)->diffInSeconds(now())
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $token = $client->getAccessToken();
            $user->gmail_access_token = $token['access_token'];
            $user->gmail_token_expires_at = now()->addSeconds($token['expires_in']);
            $user->save();
        }

        $service = new Google_Service_Gmail($client);
        $messages = $service->users_messages->listUsersMessages('me', ['maxResults' => 10]);

        $emails = [];
        foreach ($messages->getMessages() as $message) {
            $msg = $service->users_messages->get('me', $message->getId(), ['format' => 'metadata']);
            $emails[] = [
                'id' => $msg->getId(),
                'snippet' => $msg->getSnippet(),
            ];
        }

        return response()->json($emails);
    }

    public function fetchImapEmails()
    {
        try {
            $client = Client::account('default');
            $client->connect();

            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()->all()->get();

            $emails = [];

            return $messages;
            
            foreach ($messages as $message) {
                $emails[] = [
                    'subject' => $message->getSubject(),
                    'from' => $message->getFrom()[0]->mail,
                    'date' => $message->getDate(),
                    'body' => $message->getTextBody(),
                ];
            }

            return response()->json($emails);
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'to'      => 'required|email',
            'subject' => 'required|string',
            'body'    => 'required|string',
        ]);

        return;

        $user = Auth::user();
        $client = $this->getClient();
        $client->setAccessToken([
            'access_token' => $user->gmail_access_token,
            'refresh_token' => $user->gmail_refresh_token,
            'expires_in' => Carbon::parse($user->gmail_token_expires_at)->diffInSeconds(now())
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $token = $client->getAccessToken();
            $user->gmail_access_token = $token['access_token'];
            $user->gmail_token_expires_at = now()->addSeconds($token['expires_in']);
            $user->save();
        }

        $service = new Google_Service_Gmail($client);

        $strRawMessage = "To: {$request->to}\r\n";
        $strRawMessage .= "From: something@mydomain.com\r\n"; // 👈
        $strRawMessage .= "Reply-To: something@mydomain.com\r\n";
        $strRawMessage .= "Subject: {$request->subject}\r\n";
        $strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
        $strRawMessage .= $request->body;


        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        $msg = new Google_Service_Gmail_Message();
        $msg->setRaw($mime);

        $service->users_messages->send('me', $msg);

        return response()->json(['success' => true, 'message' => 'Email sent successfully']);
    }
}

<?php

namespace App\Http\Controllers\API\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webklex\IMAP\Facades\Client;

class EmailConfigController extends Controller
{
    private function smtpValidations()
    {
        return [
            'host'       => 'required|string',
            'username'   => 'required|email',
            'port'       => 'required|integer',
            'encryption' => 'required|in:tls,ssl',
            'password'   => 'required|string',
        ];
    }

    /**
     * Save the config of email smtp
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function saveSMTPConfig(Request $request)
    {
        $validator = Validator::make($request->all(), $this->smtpValidations());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        $user   = auth()->user();
        $config = $user->config ?? [];

        $config['email'] = $config['email'] ?? [];

        $config['email']['smtp'] = [
            'host'       => $request->host,
            'username'   => $request->username,
            'port'       => (int) $request->port,
            'encryption' => $request->encryption,
            'password'   => $request->password,
        ];

        $user->config = $config;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'SMTP configuration saved successfully'
        ]);
    }


    /**
     * Update the configuuration of imap for the email server
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function saveImapConfig(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validations());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        $user   = auth()->user();
        $config = $user->config;

        $config['email']['imap'] = [
            'host'       => $request->host,
            'username'   => $request->username,
            'port'       => $request->port,
            'encryption' => $request->encryption,
            'password'   => $request->password,
        ];

        $user->config = $config;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'IMAP Configuration saved successfully'
        ]);
    }

    /**
     * Connection test for IMAP
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function imapConnectionTest(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validations());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        try {

            $config = [
                'host'          => $request->input('host'),
                'port'          => $request->input('port'),
                'encryption'    => $request->input('encryption') !== 'none' ? $request->input('encryption') : null,
                'validate_cert' => true,
                'username'      => $request->input('username'),
                'password'      => $request->input('password'),
                'protocol'      => 'imap',
            ];

            $client = Client::make($config);
            $client->connect();

            return response()->json([
                'success' => true,
                'message' => 'IMAP connection successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'IMAP connection failed.',
                'error'   => $e->getMessage()
            ]);
        }
    }

    private function validations()
    {
        return [
            'host'       => 'required|string',
            'username'   => 'required|email',
            'password'   => 'required|string',
            'port'       => 'required|numeric',
            'encryption' => 'nullable|in:ssl,tls,starttls,none',
        ];
    }
}

<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Configuration\EmailConfigController;
use App\Http\Controllers\API\GmailController;
use App\Http\Controllers\API\Mail\MailController;
use App\Http\Controllers\JsonRpcController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::post('/jsonrpc', [JsonRpcController::class, 'handle']);

    Route::get('/emails/index', [MailController::class, 'inbox']);

    Route::post('configuration/email/connection-test', [EmailConfigController::class, 'imapConnectionTest']);
    Route::post('configuration/email/imap', [EmailConfigController::class, 'saveImapConfig']);
    Route::post('configuration/email/smtp', [EmailConfigController::class, 'saveSMTPConfig']);

    Route::post('gmail/callback', [GmailController::class, 'handleCallback']);
    Route::get('gmail/connect', [GmailController::class, 'redirectToGoogle']);
    Route::get('emails', [GmailController::class, 'fetchImapEmails']);

    Route::get('gmail/emails', [GmailController::class, 'fetchEmails']);
    Route::post('gmail/send', [GmailController::class, 'sendEmail']);

    Route::get('/settings/general', [\App\Http\Controllers\API\SettingController::class, 'show']);
    Route::put('/settings/general', [\App\Http\Controllers\API\SettingController::class, 'update']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
});

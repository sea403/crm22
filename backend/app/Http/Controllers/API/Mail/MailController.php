<?php

namespace App\Http\Controllers\API\Mail;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\ClientManager;

class MailController extends Controller
{
    /**
     * Get the emails through imap connection
     */
    public function inbox()
    {
        $user = auth()->user();

        try {
            return Cache::remember('mails', now()->addHour(), function () use ($user) {

                $client = Client::make($user->getImapConfig());
                $client->connect();

                $cm = new ClientManager([
                    'options' => [
                        'fetch'       => \Webklex\PHPIMAP\IMAP::FT_PEEK,
                        'fetch_order' => 'desc',
                        'sequence'    => \Webklex\PHPIMAP\IMAP::FT_UID,
                    ],
                ]);

                $imap = $cm->make($user->getImapConfig());

                $folders = $imap->getFolders();

                $emails = [];

                /** @var \Webklex\PHPIMAP\Folder $folder */
                foreach ($folders as $folder) {

                    //Get all Messages of the current Mailbox $folder
                    /** @var \Webklex\PHPIMAP\Support\MessageCollection $messages */
                    $messages = $folder->messages()->all()->get();


                    /** @var \Webklex\PHPIMAP\Message $message */
                    foreach ($messages as $message) {
                        $subject = $message->getSubject();

                        $subject = mb_convert_encoding($message->getSubject(), 'UTF-8', 'UTF-8');

                        $from = $message->getFrom();
                        $sender = $from[0] ?? null;

                        $emails[] = [
                            'html'         => $message->getHTMLBody(),
                            'mail_subject' => $subject,
                            'body'         => $message->getTextBody(),
                            'sender_name'  => $sender?->personal,
                            'sender_email' => $sender?->mail,
                            'time' => Carbon::parse($message->getDate())->format('d/m/Y h:ma')
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'mails'   => $emails
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success'  => false,
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ], 500);
        }
    }
}

<?php

namespace App\JsonRpc\Handlers;

use App\Services\CustomSMTPMailer;
use Illuminate\Support\Facades\Validator;
use App\JsonRpc\Contracts\HandlerInterface;
use App\Models\Email;

class EmailHandler implements HandlerInterface
{
    public function getModule(): string
    {
        return 'Email';
    }

    public function create(array $data, int $userId): array
    {
        $user = auth()->user(); 
        
        $data['created_by'] = $userId;

        $validator = Validator::make($data, [
            'to'      => 'required|email',
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $mailer = new CustomSMTPMailer();

        $body = view('emails.base', ['content' => $data['body']])->render();

        $result = $mailer->send(
            $user,
            $data['to'],
            $data['subject'],
            $body
        );

        // change this lateer
        $fromMail = $user->config['email']['smtp']['username'] ?? 'crm@xyz.com';

        $email          = new Email();
        $email->subject = $data['subject'];
        $email->to      = $data['to'];
        $email->from    = $fromMail;
        $email->body    = $body;
        $email->user_id = $userId;
        $email->save();

        return $email->toArray();
    }

    /** NO NEED THIS MAY BE */
    public function update(int $id, array $data, int $userId): array
    {
        return [];
    }

    public function get(int $id, int $userId): array
    {
        $contact = Email::where('user_id', $userId)->findOrFail($id);

        return $contact->toArray();
    }

    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array
    {
        $query = Email::where('user_id', $userId);

        foreach ($filters as $field => $value) {
            $query->where($field, 'like', "%{$value}%");
        }

        $contacts = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $contacts->items(),
            'pagination' => [
                'total'        => $contacts->total(),
                'current_page' => $contacts->currentPage(),
                'last_page'    => $contacts->lastPage(),
                'per_page'     => $contacts->perPage(),
            ]
        ];
    }
}

<?php

namespace App\JsonRpc\Handlers;

use App\Models\Contact;
use Illuminate\Support\Facades\Validator;
use App\JsonRpc\Contracts\HandlerInterface;
use Illuminate\Database\Eloquent\Builder;

class ContactHandler implements HandlerInterface
{
    public function getModule(): string
    {
        return 'Contact';
    }

    public function create(array $data, int $userId): array
    {
        $data['created_by'] = $userId;

        $validator = Validator::make($data, [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:contacts,email',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $contact = Contact::create($data);

        return $contact->toArray();
    }

    public function update(int $id, array $data, int $userId): array
    {
        $contact = Contact::where('created_by', $userId)->findOrFail($id);

        // prevent ownership change
        unset($data['created_by']);

        $validator = Validator::make($data, [
            'name'         => 'sometimes|string|max:255',
            'email'        => 'sometimes|email|unique:contacts,email,' . $id,
            'phone'        => 'sometimes|nullable|string|max:50',
            'company_name' => 'sometimes|nullable|string|max:255',
            'position'     => 'sometimes|nullable|string|max:255',
            'address'      => 'sometimes|nullable|string|max:255',
            'notes'        => 'sometimes|nullable|string|max:1000',
            'city'         => 'sometimes|nullable|string|max:100',
            'zipcode'      => 'sometimes|nullable|string|max:20',
            'state'        => 'sometimes|nullable|string|max:100',
            'country_code' => 'sometimes|nullable|string|max:3',
            'account_id'   => 'sometimes|nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $contact->update($data);

        return $contact->toArray();
    }

    public function get(int $id, int $userId): array
    {
        $contact = Contact::with('account')->where('created_by', $userId)->findOrFail($id);

        return $contact->toArray();
    }

    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array
    {
        $query = Contact::with('account')->where('created_by', $userId);

        foreach ($filters as $field => $value) {
            if (in_array($field, ['id', 'account_id'])) {
                $query->where($field, $value);
            } else {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        $contacts = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $contacts->items(),
            'pagination' => [
                'total' => $contacts->total(),
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
            ]
        ];
    }
}

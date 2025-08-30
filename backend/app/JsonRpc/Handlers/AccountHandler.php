<?php

namespace App\JsonRpc\Handlers;

use App\JsonRpc\Contracts\HandlerInterface;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;

class AccountHandler implements HandlerInterface
{
    public function getModule(): string
    {
        return 'Account';
    }

    public function create(array $data, int $userId): array
    {
        $data['created_by'] = $userId;

        $validator = Validator::make($data, [
            'name'             => 'required|string|max:255',
            'website'          => 'nullable|url|max:255',
            'phone'            => 'nullable|string|max:50',
            'industry'         => 'nullable|string|max:100',
            'billing_address'  => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'state'            => 'nullable|string|max:100',
            'country_code'     => 'nullable|string|max:3',
            'zipcode'          => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $account = Account::create($data);

        return $account->toArray();
    }

    public function update(int $id, array $data, int $userId): array
    {
        $account = Account::where('created_by', $userId)->findOrFail($id);
        unset($data['created_by']);

        $validator = Validator::make($data, [
            'name'             => 'sometimes|string|max:255',
            'website'          => 'sometimes|nullable|url|max:255',
            'phone'            => 'sometimes|nullable|string|max:50',
            'industry'         => 'sometimes|nullable|string|max:100',
            'billing_address'  => 'sometimes|nullable|string|max:255',
            'shipping_address' => 'sometimes|nullable|string|max:255',
            'city'             => 'sometimes|nullable|string|max:100',
            'state'            => 'sometimes|nullable|string|max:100',
            'country_code'     => 'sometimes|nullable|string|max:3',
            'zipcode'          => 'sometimes|nullable|string|max:20',
            'notes'            => 'sometimes|nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $account->update($data);

        return $account->toArray();
    }

    public function get(int $id, int $userId): array
    {
        $account = Account::where('created_by', $userId)->findOrFail($id);
        return $account->toArray();
    }

    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array
    {
        $query = Account::where('created_by', $userId);

        foreach ($filters as $field => $value) {
            $query->where($field, 'like', "%{$value}%");
        }

        $accounts = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $accounts->items(),
            'pagination' => [
                'total'        => $accounts->total(),
                'current_page' => $accounts->currentPage(),
                'last_page'    => $accounts->lastPage(),
                'per_page'     => $accounts->perPage(),
            ]
        ];
    }
}


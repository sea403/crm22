<?php

namespace App\JsonRpc\Handlers;

use App\JsonRpc\Contracts\HandlerInterface;
use App\Models\Expense;
use Illuminate\Support\Facades\Validator;

class ExpenseHandler implements HandlerInterface
{
    public function getModule(): string
    {
        return 'Expense';
    }

    public function create(array $data, int $userId): array
    {
        $data['created_by'] = $userId;

        $validator = Validator::make($data, [
            'title'    => 'required|string|max:255',
            'amount'   => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'date'     => 'nullable|date',
            'notes'    => 'nullable|string|max:1000',
            'account_id'=> 'nullable|exists:accounts,id',
            'project_id'=> 'nullable|exists:projects,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $expense = Expense::create($data);

        return $expense->toArray();
    }

    public function update(int $id, array $data, int $userId): array
    {
        $expense = Expense::where('created_by', $userId)->findOrFail($id);
        unset($data['created_by']);

        $validator = Validator::make($data, [
            'title'    => 'sometimes|string|max:255',
            'amount'   => 'sometimes|numeric|min:0',
            'category' => 'sometimes|nullable|string|max:100',
            'date'     => 'sometimes|nullable|date',
            'notes'    => 'sometimes|nullable|string|max:1000',
            'account_id'=> 'sometimes|nullable|exists:accounts,id',
            'project_id'=> 'sometimes|nullable|exists:projects,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $expense->update($data);

        return $expense->toArray();
    }

    public function get(int $id, int $userId): array
    {
        $expense = Expense::with(['account','project'])->where('created_by', $userId)->findOrFail($id);
        return $expense->toArray();
    }

    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array
    {
        $query = Expense::with(['account','project'])->where('created_by', $userId);

        foreach ($filters as $field => $value) {
            if (in_array($field, ['id', 'account_id', 'project_id'])) {
                $query->where($field, $value);
            } else {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        $items = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $items->items(),
            'pagination' => [
                'total'        => $items->total(),
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
            ]
        ];
    }
}

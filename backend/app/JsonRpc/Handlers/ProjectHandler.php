<?php

namespace App\JsonRpc\Handlers;

use App\JsonRpc\Contracts\HandlerInterface;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class ProjectHandler implements HandlerInterface
{
    public function getModule(): string
    {
        return 'Project';
    }

    public function create(array $data, int $userId): array
    {
        $data['created_by'] = $userId;

        $validator = Validator::make($data, [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:planned,in_progress,completed,on_hold,canceled',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'budget'      => 'nullable|numeric|min:0',
            'account_id'  => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $project = Project::create($data);

        return $project->toArray();
    }

    public function update(int $id, array $data, int $userId): array
    {
        $project = Project::where('created_by', $userId)->findOrFail($id);

        unset($data['created_by']);

        $validator = Validator::make($data, [
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status'      => 'sometimes|string|in:planned,in_progress,completed,on_hold,canceled',
            'start_date'  => 'sometimes|nullable|date',
            'end_date'    => 'sometimes|nullable|date|after_or_equal:start_date',
            'budget'      => 'sometimes|nullable|numeric|min:0',
            'account_id'  => 'sometimes|nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException(json_encode($validator->errors()->all()));
        }

        $project->update($data);

        return $project->toArray();
    }

    public function get(int $id, int $userId): array
    {
        $project = Project::with('account')->where('created_by', $userId)->findOrFail($id);
        return $project->toArray();
    }

    public function list(array $filters, int $page, int $perPage, string $sortBy, string $sortOrder, int $userId): array
    {
        $query = Project::with('account')->where('created_by', $userId);

        foreach ($filters as $field => $value) {
            if (in_array($field, ['id', 'account_id'])) {
                $query->where($field, $value);
            } else {
                $query->where($field, 'like', "%{$value}%");
            }
        }

        $projects = $query->orderBy($sortBy, $sortOrder)
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $projects->items(),
            'pagination' => [
                'total'        => $projects->total(),
                'current_page' => $projects->currentPage(),
                'last_page'    => $projects->lastPage(),
                'per_page'     => $projects->perPage(),
            ]
        ];
    }
}

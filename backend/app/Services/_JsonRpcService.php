<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class JsonRpcService
{
    protected array $allowedModules = ['Contact'];

    public function handle(array $requestData, int $currentUserId): array
    {
        // Detect if batch request (array of requests)
        if (isset($requestData[0]) && is_array($requestData[0])) {
            $responses = [];
            foreach ($requestData as $request) {
                $responses[] = $this->handleRequest($request, $currentUserId);
            }
            return $responses;
        } else {
            return $this->handleRequest($requestData, $currentUserId);
        }
    }


    /**
     * Process the JSON-RPC request.
     *
     * @param array $requestData
     * @param int $currentUserId
     * @return array JSON-RPC response array
     */
    public function handleRequest(array $requestData, int $currentUserId): array
    {
        $id     = $requestData['id'] ?? null;
        $method = $requestData['method'] ?? null;
        $params = $requestData['params'] ?? [];

        if (!$method) {
            return $this->errorResponse($id, -32600, 'Invalid Request: Missing method');
        }

        try {
            switch ($method) {
                case 'listRecords':
                    return $this->handleList($id, $params, $currentUserId);

                case 'createRecord':
                    return $this->handleCreate($id, $params, $currentUserId);

                case 'updateRecord':
                    return $this->handleUpdate($id, $params, $currentUserId);

                case 'getRecord':
                    return $this->handleGet($id, $params, $currentUserId);

                default:
                    return $this->errorResponse($id, -32601, 'Method not found');
            }
        } catch (Exception $e) {
            return $this->errorResponse($id, -32000, 'Server error: ' . $e->getMessage());
        }
    }

    protected function handleCreate($id, array $params, int $currentUserId): array
    {
        $module = $params['module'] ?? null;
        $data = $params['data'] ?? [];

        if (!$this->isValidModule($module)) {
            return $this->errorResponse($id, -32602, 'Invalid module');
        }

        $rules = $this->getValidationRulesForModule($module);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($id, -32602, 'Invalid params', $validator->errors()->all());
        }

        $modelClass = $this->getModelClass($module);

        $data['created_by'] = $currentUserId;

        $record = $modelClass::create($data);

        return $this->successResponse($id, $record);
    }

    protected function handleUpdate($id, array $params, int $currentUserId): array
    {
        $module = $params['module'] ?? null;
        $recordId = $params['id'] ?? null;
        $data = $params['data'] ?? [];

        if (!$this->isValidModule($module)) {
            return $this->errorResponse($id, -32602, 'Invalid module');
        }

        if (!$recordId) {
            return $this->errorResponse($id, -32602, 'Missing record ID');
        }

        $rules = $this->getValidationRulesForModule($module);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->errorResponse($id, -32602, 'Invalid params', $validator->errors()->all());
        }

        $modelClass = $this->getModelClass($module);

        // Only find record belonging to current user
        $record = $modelClass::where('created_by', $currentUserId)->find($recordId);

        if (!$record) {
            return $this->errorResponse($id, -32602, 'Record not found or access denied');
        }

        $record->update($data);

        return $this->successResponse($id, $record);
    }

    protected function handleGet($id, array $params, int $currentUserId): array
    {
        $module = $params['module'] ?? null;
        $recordId = $params['id'] ?? null;

        if (!$this->isValidModule($module)) {
            return $this->errorResponse($id, -32602, 'Invalid module');
        }

        if (!$recordId) {
            return $this->errorResponse($id, -32602, 'Missing record ID');
        }

        $modelClass = $this->getModelClass($module);

        // Only find record belonging to current user
        $record = $modelClass::where('created_by', $currentUserId)->find($recordId);

        if (!$record) {
            return $this->errorResponse($id, -32602, 'Record not found or access denied');
        }

        return $this->successResponse($id, $record);
    }

    protected function isValidModule(?string $module): bool
    {
        return in_array($module, $this->allowedModules);
    }

    protected function getValidationRulesForModule(string $module): array
    {
        $rules = [
            'Contact' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:contacts,email',
            ]
        ];

        return $rules[$module] ?? [];
    }

    protected function getModelClass(string $module): string
    {
        return "\\App\\Models\\{$module}";
    }

    protected function successResponse($id, $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'result' => $result,
            'id' => $id,
        ];
    }

    protected function errorResponse($id, int $code, string $message, $data = null): array
    {
        $error = [
            'code' => $code,
            'message' => $message,
        ];

        if ($data !== null) {
            $error['data'] = $data;
        }

        return [
            'jsonrpc' => '2.0',
            'error' => $error,
            'id' => $id,
        ];
    }

    protected function handleList($id, array $params, int $currentUserId): array
    {
        $module    = $params['module'] ?? null;
        $filters   = $params['filters'] ?? [];
        $page      = $params['page'] ?? 1;
        $perPage   = $params['perPage'] ?? 10;
        $sortBy    = $params['sortBy'] ?? 'id';
        $sortOrder = strtolower($params['sortOrder'] ?? 'asc');

        if (!$this->isValidModule($module)) {
            return $this->errorResponse($id, -32602, 'Invalid module');
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $modelClass = $this->getModelClass($module);

        // Build query scoped to current user
        $query = $modelClass::where('created_by', $currentUserId);

        // Apply filters (basic example: where field LIKE '%value%')
        foreach ($filters as $field => $value) {
            if (is_string($value) && $value !== '') {
                $query->where($field, 'LIKE', "%{$value}%");
            }
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Format the paginated result
        $result = [
            'data' => $paginator->items(),
            'pagination' => [
                'total'       => $paginator->total(),
                'perPage'     => $paginator->perPage(),
                'currentPage' => $paginator->currentPage(),
                'lastPage'    => $paginator->lastPage(),
                'from'        => $paginator->firstItem(),
                'to'          => $paginator->lastItem(),
            ],
        ];

        return $this->successResponse($id, $result);
    }
}

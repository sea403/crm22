<?php

namespace App\Services;

use App\JsonRpc\Contracts\HandlerInterface;
use Illuminate\Support\Collection;
use Exception;

class JsonRpcService
{
    /**
     * @var Collection<string, HandlerInterface>
     */
    protected Collection $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = collect();

        foreach ($handlers as $handler) {
            if ($handler instanceof HandlerInterface) {
                $this->handlers->put($handler->getModule(), $handler);
            }
        }
    }

    /**
     * Handle a JSON-RPC request
     */
    public function handleRequest(array $requestData, int $userId): array
    {
        $id     = $requestData['id'] ?? null;
        $method = $requestData['method'] ?? null;
        $params = $requestData['params'] ?? [];

        if (!$method) {
            return $this->errorResponse($id, -32600, 'Invalid Request: Missing method');
        }

        try {
            $module = $params['module'] ?? null;
            if (!$module || !$this->handlers->has($module)) {
                return $this->errorResponse($id, -32602, 'Invalid or unsupported module');
            }

            $handler = $this->handlers->get($module);

            return match ($method) {
                'createRecord' => $this->successResponse($id, $handler->create($params['data'] ?? [], $userId)),
                'updateRecord' => $this->successResponse($id, $handler->update($params['id'] ?? 0, $params['data'] ?? [], $userId)),
                'getRecord'    => $this->successResponse($id, $handler->get($params['id'] ?? 0, $userId)),
                'listRecords'  => $this->successResponse($id, $handler->list(
                    $params['filters'] ?? [],
                    $params['page'] ?? 1,
                    $params['perPage'] ?? 10,
                    $params['sortBy'] ?? 'id',
                    $params['sortOrder'] ?? 'asc',
                    $userId
                )),
                default => $this->errorResponse($id, -32601, 'Method not found'),
            };
        } catch (Exception $e) {
            return $this->errorResponse($id, -32000, 'Server error: ' . $e->getMessage());
        }
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
}

<?php

namespace App\Http\Controllers;

use App\Services\JsonRpcService;
use Illuminate\Http\Request;

class JsonRpcController extends Controller
{
    public function handle(Request $request, JsonRpcService $rpcService)
    {
        $userId = auth()->id();
        $data = $request->json()->all();

        $response = $rpcService->handleRequest($data, $userId);

        return response()->json($response);
    }
}

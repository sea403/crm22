<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {

        return response()->json(
            [
                'errors' => [
                    'status' => 401,
                    'message' => 'Unauthenticated',
                ]
            ],
            401
        );

        if ($exception instanceof ValidationException) {
            $errors = $exception->errors();

            $formattedErrors = [];
            foreach ($errors as $field => $messages) {
                // Join multiple messages for a field into one string
                $formattedErrors[$field] = implode(' ', $messages);
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $formattedErrors,
            ], 422);
        }

        return parent::render($request, $exception);
    }
}

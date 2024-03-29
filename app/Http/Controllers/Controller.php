<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

abstract class Controller
{
    /**
     * success response method.
     *
     */
    public function sendResponse($result, $code = 200, $message = ''): JsonResponse
    {
        if(!in_array($code, [200,201,202,204,205])){
            throw new \InvalidArgumentException('Invalid response code.');
        }

        $message = $code == 201 ? __('messages.store.success') : $message;

        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return Response::json($response, $code);
    }

    /**
     * return error response.
     *
     */
    public function sendError($error, $code = 404, $errorMessages = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }

        return Response::json($response, $code);
    }
}

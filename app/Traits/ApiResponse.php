<?php

namespace App\Traits;

trait ApiResponse
{
    public function success($message, $data = null)
    {
        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }
    public function error($message, $code = 400, $data = null)
    {
        return response()->json([
            'code' => $code,
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

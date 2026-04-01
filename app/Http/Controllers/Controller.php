<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
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

<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function sendResponse($data, $message, $code = 200)
    {
        return response()->json([
            'status_code' => $code,
            'status'      => 'success',
            'message'     => $message,
            'data'        => $data,
            'errors'      => null
        ], $code);
    }

    public function sendError($message, $errors = null, $code = 400)
    {
        return response()->json([
            'status_code' => $code,
            'status'      => 'error',
            'message'     => $message,
            'data'        => null,
            'errors'      => $errors
        ], $code);
    }
}

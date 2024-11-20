<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public static function successResponse(String $message, $result = null, $code = Response::HTTP_OK)
    {
        return response()->json([
            'message' => $message,
            'data' => $result,
        ], $code);
    }

    public static function errorResponse(String $message, $result = null, $code = Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'message' => $message,
            'data' => $result,
        ], $code);
    }
}

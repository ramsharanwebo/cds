<?php
namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper {

    static function successHandler($data=[], $message=null, $status_code=null): JsonResponse
    {
        return response()->json(['payload'=> $data, 'message'=>$message, 'status_code'=>$status_code]);
    }

    static function errorHandling($message=null, $status_code=null): JsonResponse
    {
        return response()->json(['message'=>$message, 'status_code'=>$status_code]);
    }
}
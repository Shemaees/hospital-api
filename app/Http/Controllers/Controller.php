<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function returnJsonResponse($message, $data = [], $status = TRUE, $response = Response::HTTP_OK)
    {
        Session::put('response_status', $status);

        return response()->json([
            'message'           =>$message,
            'data'              => $data,
            'status'            =>$status,
        ], $response)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Content-Type', 'application/json;charset=UTF-8')
            ->header('Access-Control-Allow-Methods', 'POST, GET, PATCH, PUT')
            ->header('Charset', 'utf-8');
    }

    public function generateRandomString($length = 10,$small_letters='all',$capital_letters='all')
    {
        $characters = '0123456789';
        if($small_letters=='all') {
            $characters .= 'abcdefghijklmnopqrstuvwxy';
        }
        if($capital_letters=='all'){
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

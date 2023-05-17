<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class GenericEventController extends Controller
{
    public function sendEventLog(Request $request){
        dd($request->all());
        $client = new Client();

        // Send the POST request to the API
        $response = $client->post('http://localhost:9595/api/v1/logs', [
            'form_params' => [
                'param1' => $request->input('param1'),
                'param2' => $request->input('param2'),
            ],
        ]);

        // Get the response body or perform any necessary processing
        $responseData = $response->getBody()->getContents();

        // Return a response or perform any additional actions
        return response()->json($responseData, $response->getStatusCode());
    
    }
}

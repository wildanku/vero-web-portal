<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatasetController extends Controller
{
    public function get(Request $request)
    {
        // crawl data from remote API and save to DB
        $this->crawlData();

        // get data from database
        $datasets = new Dataset();
        
        if($request->q) {
            $datasets = $datasets
                            ->where('task','like','%'.$request->q.'%')
                            ->orWhere('title','like','%'.$request->q.'%')
                            ->orWhere('description','like','%'.$request->q.'%')
                            ->orWhere('colorCode',$request->q);
        }

        if($request->offset) {
            $datasets = $datasets->paginate($request->offset);
        } else {
            $datasets = $datasets->get();
        }

        return response([
            'success'   => true,
            'data'      => $datasets
        ],200);
    }

    public function crawlData()
    {
        $loginRequest = $this->login();
        $loginJson = $loginRequest ? json_decode($loginRequest) : null;

        $token = $loginJson?->oauth?->access_token ?? null;

        // get data 
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.baubuddy.de/index.php/v1/tasks/select",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "{\"username\":\"365\", \"password\":\"1\"}",
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer ".$token ?? null,
            "Content-Type: application/json"
        ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $resJson = $response ? json_decode($response) : null;

        // Save to database 
        if($resJson) {
            DB::transaction(function() use($resJson) {
                foreach($resJson as $res) {
                    Dataset::updateOrCreate(['title' => $res?->title], (array) $res);
                }
            });
        }
    }

    public function login() 
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.baubuddy.de/index.php/login",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"username\":\"365\", \"password\":\"1\"}",
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic QVBJX0V4cGxvcmVyOjEyMzQ1NmlzQUxhbWVQYXNz",
            "Content-Type: application/json"
        ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        return $response;
    }
}

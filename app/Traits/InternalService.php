<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait InternalService
{
    public function request($service, $method, $requestUrl, $formParams = [], $returnData = false, $headers = [])
    {
        $client = Http::$service()->timeout(30);

        if ($headers) {
            $client->withHeaders($headers);
        }

        $res = $client->$method($requestUrl, $formParams);

        if ($returnData) {
            return [$res->successful(), $res->successful() ? $res->collect('data') : $res->json('error')];
        }

        return response()->json($res->json(), $res->status());
    }
}

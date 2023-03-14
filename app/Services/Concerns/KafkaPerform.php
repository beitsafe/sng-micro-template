<?php

namespace App\Services\Concerns;

use App\Jobs\ReadScanJSON;
use App\Models\Scan;
use Illuminate\Support\Facades\Artisan;

trait KafkaPerform
{
    use KafkaResponse;

    public function handleArtisanCall($payload)
    {
        Artisan::call($payload['command'], $payload['params']);
    }
}

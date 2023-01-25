<?php

namespace App\Models;

use App\Services\KafkaService;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use Uuids, SoftDeletes;

    public $timestamps = true;

    protected $perPage = 10;

    protected $dates = ['deleted_at'];

    public array $searchable = [];

    public function triggerWs($channel, $event, $data)
    {
        KafkaService::produce('websockets_topic', 'ws.broadcast', compact('channel', 'event', 'data'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\Concerns\KafkaPerform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class HealthController extends Controller
{
    use KafkaPerform;

    /**
     * @OA\Get(
     *     path="/healthz",
     *     tags={"Health"},
     *     summary="Health Check",
     *     description="Health Check",
     *     operationId="health-check",
     *     @OA\Response(response=200, description="Health Check", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *     @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     * )
     */

    public function healthz()
    {
        //All good let's go Mario
        return response()->json([
            'result'  => "success",
            'heading' => "Health Response",
            'message' => "This scan app is healthy"
        ]);
    }


    /**
     * @OA\Post(
     *      path="/kafka/consume",
     *      operationId="kafkaConsume",
     *      tags={"Kafka"},
     *      summary="Consume kafka from API",
     *      security={ {"bearerAuth": {}} },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="event", type="string", example="option"),
     *              @OA\Property(property="payload", type="string", example="value"),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Consumed", @OA\JsonContent()),
     *      @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     * )
     */
    public function kafkaConsume(Request $request)
    {
        $method = 'handle' . Str::studly(str_replace('.', '_', $request->get('event')));

        if (!method_exists($this, $method)) {
            return response()->json('method not exists');
        }

        try {
            $this->{$method}($request->get('payload'));

            return response()->json('success');
        } catch (\Exception $e) {
            return response()->json("{$e->getMessage()} @ {$e->getFile()} in {$e->getLine()}", 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/artisan",
     *      operationId="artisanCall",
     *      tags={"Artisan"},
     *      summary="Call Artisan from API",
     *      security={ {"bearerAuth": {}} },
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="command", type="string", example="sync:scan"),
     *              @OA\Property(property="params", type="string", example={"--id":"02304df0-b96b-4f60-a2f5-24266f715765"}),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Consumed", @OA\JsonContent()),
     *      @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     *      @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent()),
     *      @OA\Response(response=403, description="Forbidden", @OA\JsonContent()),
     * )
     */
    public function callArtisan(Request $request)
    {
        try {
            Artisan::call($request->get('command'), $request->get('params', []));

            return response()->json('success');
        } catch (\Exception $e) {
            return response()->json("{$e->getMessage()} @ {$e->getFile()} in {$e->getLine()}", 400);
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        //
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function (ResponseReceived $event) {
            $response = $event->response;

            if (config('app.debug') || $response->failed()) {
                $request = $event->request;

                Log::info(sprintf("[HTTP][REQ] ==> [%s][%s] => Payload %s => %s ",
                    $request->method(),
                    $request->url(),
                    json_encode($request->data()),
                    $response->status()
                ));

                if ($response->failed()) {
                    Log::error(sprintf('[HTTP][ERROR] ==> %s', $response->body()));
                } else {
                    Log::info(sprintf('[HTTP][RES] ==> %s', json_encode($response->collect())));
                }
            }
        });

        Event::listen(function (ConnectionFailed $event) {
            $request = $event->request;

            Log::error(sprintf('[HTTP][FAIL] ==> [%s][%s] => Payload %s',
                $request->url(),
                $request->method(),
                json_encode($request->data())
            ));
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}

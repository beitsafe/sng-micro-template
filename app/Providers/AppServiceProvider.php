<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Internal Service Macros
        $internalServices = config("services.internal");

        if ($internalServices) {
            foreach ($internalServices as $service => $baseUri) {
                Http::macro($service, function () use ($baseUri) {
                    return Http::withHeaders([
                        'Authorization' => env('INTERNAL_SECRET'),
                    ])->baseUrl($baseUri);
                });
            }
        }

        // share contextual information
        Log::shareContext([
            'id' => strtoupper(Str::random(8)),
        ]);
    }
}

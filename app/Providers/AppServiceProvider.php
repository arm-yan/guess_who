<?php

namespace App\Providers;

use App\Services\MovieService\ActorsService;
use App\Services\MovieService\Transformers\ActorTransformer;
use GuzzleHttp\Client;
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
        $this->app->bind(ActorsService::class, function ($app) {
            $httpClient = new Client();
            $actorTransformer = new ActorTransformer();
            return new ActorsService($httpClient, $actorTransformer);
        });

        $this->app->alias(ActorsService::class, 'actors-service');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

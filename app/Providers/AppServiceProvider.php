<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Redis;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->singleton(Redis::class, function ($app) {
            $redis = new Redis();
            $redis->pconnect(
                env('REDIS_HOST'),
                env('REDIS_PORT')
            );

            $redis->select(6);

            if ($redisAuth = env('REDIS_AUTH')) {
                $redis->auth($redisAuth);
            }

            return $redis;
        });
    }
}

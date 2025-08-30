<?php

namespace App\Providers;

use App\JsonRpc\Handlers\ContactHandler;
use App\JsonRpc\Handlers\EmailHandler;
use App\Services\JsonRpcService;
use App\JsonRpc\Handlers\ProjectHandler;
use App\JsonRpc\Handlers\AccountHandler;
use App\JsonRpc\Handlers\ExpenseHandler;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(JsonRpcService::class, function ($app) {
            return new JsonRpcService([
                new ContactHandler(),
                new EmailHandler(),
                new ProjectHandler(),
                new AccountHandler(),
                new ExpenseHandler(),
            ]);
        });
    }
}

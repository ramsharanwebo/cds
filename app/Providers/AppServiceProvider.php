<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use App\Helpers\ResponseHelper;

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
        Response::macro('SuccessResponse', function ($data = null, $message, $responseCode) {
            $response = new ResponseHelper();
            return $response->successHandler($data, $message, $responseCode);
        });

        Response::macro('ErrorResponse', function ($message, $responseCode) {
            $response = new ResponseHelper();
            return $response->errorHandling($message, $responseCode);
        });
    }
}

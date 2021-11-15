<?php

namespace App\Providers;

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
        // \Illuminate\Foundation\Applicationで"validator"エイリアスを呼び出しそこから"replacer"メソッドを呼び出している
        app()->validator->replacer('max',
            function ($message, $attribute, $rule, $parameters) {
                return str_replace(':max', number_format($parameters[0],0,","), $message);
        });
    }
}

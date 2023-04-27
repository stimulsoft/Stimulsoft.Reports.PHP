<?php

namespace Stimulsoft\Laravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class StiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::get('/vendor/stimulsoft/reports-php/scripts/{file}', function ($file) {
            return file_get_contents(__DIR__ . "/../../scripts/$file");
        });
    }
}

<?php

namespace Egretos\RestModel;

use Illuminate\Support\ServiceProvider;

class RestModelServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->publishes([__DIR__ . '/config/' => config_path() . '/']);
    }
}
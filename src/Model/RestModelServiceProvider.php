<?php

namespace Egretos\RestModel;

use Illuminate\Support\ServiceProvider;

final class RestModelServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->publishes([__DIR__ . '/../config/' => config_path() . '/']);
    }
}
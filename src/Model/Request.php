<?php

namespace Egretos\RestModel;

final class Request
{
    const
        METHOD_GET = 'GET',
        METHOD_HEAD = 'HEAD',
        METHOD_POST = 'POST',
        METHOD_PUT = 'PUT',
        METHOD_DELETE = 'DELETE',
        METHOD_CONNECT = 'CONNECT',
        METHOD_OPTIONS = 'OPTIONS',
        METHOD_TRACE = 'TRACE',
        METHOD_PATCH = 'PATCH';

    public $domain;

    public $route;

    public $method = self::METHOD_GET;

    public $headers = [];

    public $form_params = null;

    public $json = null;

    public function toGuzzleOptions() {
        $options = [];

        return $options;
    }
}
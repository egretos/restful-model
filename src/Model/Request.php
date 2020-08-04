<?php

namespace Egretos\RestModel;

use GuzzleHttp\RequestOptions;

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

    public $form_params;

    public $auth;

    public $json;

    public $cookies;

    public $query_params;

    public $multipart;

    public $body;

    public function toGuzzleOptions() {
        $options = [];

        if ($this->auth) {
            $options[RequestOptions::AUTH] = $this->auth;
        }

        if ($this->headers) {
            $options[RequestOptions::HEADERS] = $this->headers;
        }

        if ($this->form_params) {
            $options[RequestOptions::FORM_PARAMS] = $this->form_params;
        }

        if ($this->json) {
            $options[RequestOptions::JSON] = $this->json;
        }

        if ($this->cookies) {
            $options[RequestOptions::COOKIES] = $this->cookies;
        }

        if ($this->query_params) {
            $options[RequestOptions::QUERY] = $this->query_params;
        }

        if ($this->multipart) {
            $options[RequestOptions::MULTIPART] = $this->multipart;
        }

        if ($this->body) {
            $options[RequestOptions::BODY] = $this->body;
        }

        return $options;
    }
}
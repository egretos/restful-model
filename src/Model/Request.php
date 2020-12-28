<?php

/** @noinspection PhpUnused */

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

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     * @return Request
     */
    public function setDomain($domain): Request
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     * @return Request
     */
    public function setRoute($route): Request
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Request
     */
    public function setMethod(string $method): Request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Request
     */
    public function setHeaders(array $headers): Request
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormParams()
    {
        return $this->form_params;
    }

    public function setFormParam($param, $value) {
        $this->form_params[$param] = $value;
    }

    /**
     * @param mixed $form_params
     * @return Request
     */
    public function setFormParams($form_params): Request
    {
        $this->form_params = $form_params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param mixed $auth
     * @return Request
     */
    public function setAuth($auth): Request
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * @param mixed $json
     * @return Request
     */
    public function setJson($json): Request
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param mixed $cookies
     * @return Request
     */
    public function setCookies($cookies): Request
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQueryParams()
    {
        return $this->query_params;
    }

    /**
     * @param mixed $query_params
     * @return Request
     */
    public function setQueryParams($query_params): Request
    {
        $this->query_params = $query_params;
        return $this;
    }

    /**
     * @param $key
     * @param $query_params
     * @return $this
     */
    public function setQueryParam($key, $query_params): Request
    {
        $this->query_params[$key] = $query_params;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMultipart()
    {
        return $this->multipart;
    }

    /**
     * @param mixed $multipart
     * @return Request
     */
    public function setMultipart($multipart): Request
    {
        $this->multipart = $multipart;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return Request
     */
    public function setBody($body): Request
    {
        $this->body = $body;
        return $this;
    }



    public function toGuzzleOptions(): array
    {
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
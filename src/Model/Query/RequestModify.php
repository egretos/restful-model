<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Request;

/**
 * Trait RequestModify
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait RequestModify
{
    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setQueryParams (array $params) {
        $this->request->query_params = $params;
        return $this;
    }

    /**
     * @param string $key
     * @param $param
     * @return $this
     */
    public function addQueryParam(string $key, $param) {
        $this->request->query_params[$key] = (string) $param;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addQueryParams(array $params) {
        foreach ($params as $key => $param) {
            $this->addQueryParam($key, $param);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeQueryParam(string $key) {
        if (isset($this->request->query_params[$key])) {
            unset($this->request->query_params[$key]);
        }

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers) {
        $this->request->headers = $headers;
        return $this;
    }

    /**
     * @param string $key
     * @param $header
     * @return $this
     */
    public function addHeader(string $key, $header) {
        $this->request->headers[$key] = (string) $header;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers) {
        foreach ($headers as $key => $header) {
            $this->addHeader($key, $header);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeHeader(string $key) {
        if (isset($this->request->headers[$key])) {
            unset($this->request->headers[$key]);
        }

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setFormParams(array $params) {
        $this->request->form_params = $params;
        return $this;
    }

    /**
     * @param string $key
     * @param $param
     * @return $this
     */
    public function addFormParam(string $key, $param) {
        $this->request->form_params[$key] = (string) $param;
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addFormParams(array $params) {
        foreach ($params as $key => $param) {
            $this->addFormParam($key, $param);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeFormParam(string $key) {
        if (isset($this->request->form_params[$key])) {
            unset($this->request->form_params[$key]);
        }

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setJsonBody(array $data) {
        $this->request->json = $data;
        return $this;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setRawBody(string $body) {
        $this->request->body = $body;
        return $this;
    }
}
<?php

/** @noinspection PhpUnused */

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
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): RequestModify
    {
        $this->getRequest()->setMethod($method);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setQueryParams(array $params): RequestModify
    {
        $this->getRequest()->setQueryParams($params);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $param
     * @return $this
     */
    public function addQueryParam(string $key, $param): RequestModify
    {
        $this->getRequest()->setQueryParam($key, (string) $param);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addQueryParams(array $params): RequestModify
    {
        foreach ($params as $key => $param) {
            $this->addQueryParam($key, $param);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeQueryParam(string $key): RequestModify
    {
        if (isset($this->getRequest()->query_params[$key])) {
            unset($this->getRequest()->query_params[$key]);
        }

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): RequestModify
    {
        $this->getRequest()->setHeaders($headers);
        return $this;
    }

    /**
     * @param string $key
     * @param $header
     * @return $this
     */
    public function addHeader(string $key, $header): RequestModify
    {
        $this->getRequest()->headers[$key] = (string) $header;
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function addHeaders(array $headers): RequestModify
    {
        foreach ($headers as $key => $header) {
            $this->addHeader($key, $header);
        }
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeHeader(string $key): RequestModify
    {
        if (isset($this->getRequest()->headers[$key])) {
            unset($this->getRequest()->headers[$key]);
        }

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setFormParams(array $params): RequestModify
    {
        $this->getRequest()->setFormParams($params);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function addFormParams(array $params): RequestModify
    {
        $this
            ->getRequest()
            ->setFormParams( array_merge($params, $this->getRequest()->getFormParams()) );
        return $this;
    }

    /**
     * @param string $key
     * @param string $param
     * @return $this
     */
    public function setFormParam(string $key, string $param): RequestModify
    {
        $this->getRequest()->setFormParam($key, $param);
        return $this;
    }


    /**
     * @param string $key
     * @param string $param
     * @return $this
     */
    public function addFormParam(string $key, string $param): RequestModify
    {
        $this->getRequest()->setFormParam($key, $param);
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function removeFormParam(string $key): RequestModify
    {
        if (isset($this->getRequest()->form_params[$key])) {
            unset($this->getRequest()->form_params[$key]);
        }

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setJsonBody(array $data): RequestModify
    {
        $this->getRequest()->setJson($data);
        return $this;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setRawBody(string $body): RequestModify
    {
        $this->getRequest()->setBody($body);
        return $this;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain(string $domain): RequestModify
    {
        $this->getRequest()->setDomain($domain);
        return $this;
    }

    public function setRoute(string $route): RequestModify
    {
        $this->getRequest()->setRoute($route);
        return $this;
    }
}
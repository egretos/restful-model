<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Model;
use Egretos\RestModel\Request;

/**
 * Trait ApiQueries
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait ApiQueries
{
    public function index(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->connection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->request->method = Request::METHOD_GET;

        $response = $this->send();

        return $this->normalizeResponse($response, true);
    }

    public function show(string $id = null) {
        if ($this->model instanceof Model && $id) {
            $this->model->setAttribute($this->model->getRouteKeyName(), $id);
        }

        if (!$this->model->getRouteKey()) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->request->method = Request::METHOD_GET;

        $response = $this->send();

        return $this->normalizeResponse($response);
    }

    public function create(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->connection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->request->method = Request::METHOD_POST;
        $this->prepareModelParams($model);

        $response = $this->send();
        $this->model->wasRecentlyCreated = true;

        return $this->normalizeResponse($response);
    }

    public function update(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->connection();
        }

        $this->request->method = Request::METHOD_PUT;
        $this->prepareModelParams($model);

        $response = $this->send();

        return $this->normalizeResponse($response);
    }

    public function delete(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->connection();
        }

        $this->request->method = Request::METHOD_DELETE;

        $response = $this->send();

        return $this->normalizeResponse($response);
    }
}
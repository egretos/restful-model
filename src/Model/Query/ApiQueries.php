<?php

/** @noinspection PhpUndefinedClassInspection */

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Request;
use Illuminate\Support\Collection;
use JsonException;

/**
 * Trait ApiQueries
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait ApiQueries
{
    /**
     * @param Model|null $model
     * @return Connection|Model|Model[]|Collection
     * @throws JsonException
     */
    public function index(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->request->method = Request::METHOD_GET;

        $response = $this->send();

        return $this->normalizeResponse($response, true);
    }

    /**
     * @param string|null $id
     * @return Connection|Model|Model[]|Collection
     * @throws JsonException
     */
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

    /**
     * @param Model|null $model
     * @return Connection|Model|Model[]|Collection
     * @throws JsonException
     */
    public function create(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->request->method = Request::METHOD_POST;
        $this->prepareModelSaving($model);

        $response = $this->send();
        $this->model->wasRecentlyCreated = true;

        return $this->normalizeResponse($response);
    }

    /**
     * @param Model|null $model
     * @return bool|Connection|Model|Model[]|Collection
     * @throws JsonException
     */
    public function update(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model && !$model->exists) {
            return false;
        }

        $this->request->method = Request::METHOD_PUT;
        $this->prepareModelSaving($model);

        $response = $this->send();

        return $this->normalizeResponse($response);
    }

    /**
     * @param Model|null $model
     * @return Connection|Model|Model[]|Collection
     * @throws JsonException
     */
    public function delete(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        $this->request->method = Request::METHOD_DELETE;

        $response = $this->send();

        return $this->normalizeResponse($response);
    }
}
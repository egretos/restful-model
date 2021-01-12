<?php

/** @noinspection PhpUndefinedClassInspection */

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Request;
use Illuminate\Support\Collection;

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
     */
    public function index(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        $this->setMethod(Request::METHOD_GET);

        $response = $this->send();

        return $this->normalizeResponse($response, true);
    }

    /**
     * @param string|null $id
     * @return Connection|Model|Model[]|Collection
     */
    public function show(string $id = null) {
        if ($this->model instanceof Model && $id) {
            $this->model->setAttribute($this->model->getRouteKeyName(), $id);
            $this->resetRoute();
        }

        $this->setMethod(Request::METHOD_GET);

        $response = $this->send();

        return $this->normalizeResponse($response);
    }

    /**
     * @param Model|null $model
     * @return Connection|Model|Model[]|Collection|bool
     */
    public function create(Model $model = null) {
        if ($model) {
            $this->setModel( $model );
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model) {
            $this->model->setAttribute($this->model->getRouteKeyName(), null);
        }

        if ($this->model->fireModelEvent('creating') === false) {
            return false;
        }

        $this->setMethod( Request::METHOD_POST );
        $this->prepareModelSaving($model);

        $response = $this->send();

        $this->model->exists = true;
        $this->model->wasRecentlyCreated = true;
        $this->model->fireModelEvent('created', false);

        return $this->normalizeResponse($response);
    }

    public function forceCreate($attributes) {
        $this->model->forceFill($attributes);
        return $this->create();
    }

    /**
     * @param Model|null $model
     * @return bool|Connection|Model|Model[]|Collection
     */
    public function update(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model && !$model->exists) {
            return false;
        }

        if ($this->model->fireModelEvent('updating') === false) {
            return false;
        }

        $this->setMethod(Request::METHOD_PUT);
        $this->prepareModelSaving($model);

        $response = $this->send();

        $this->model->fireModelEvent('updated', false);

        return $this->normalizeResponse($response);
    }

    /**
     * @param Model|null $model
     * @return Connection|Model|Model[]|Collection|bool
     */
    public function delete(Model $model = null) {
        if ($model) {
            $this->model = $model;
            $this->connection = $this->model->getConnection();
        }

        if ($this->model instanceof Model && !$model->exists) {
            return false;
        }

        if ($this->model->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->setMethod(Request::METHOD_DELETE);

        $response = $this->send();

        $this->model->fireModelEvent('deleted', false);

        return $this->normalizeResponse($response);
    }
}
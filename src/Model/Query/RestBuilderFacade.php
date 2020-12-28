<?php

namespace Egretos\RestModel\Query;

use Closure;
use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Trait RestBuilderFacade
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait RestBuilderFacade
{
    public function where($column, $value = null) {
        if (is_array($column)) {
            return $this->addArrayOfWheres($column);
        }

        return $this->addQueryParam($column, $value);
    }

    public function addArrayOfWheres($column): RestBuilderFacade
    {
        foreach ($column as $key => $value) {
            if (is_numeric($key) && is_array($value)) {
                $this->addArrayOfWheres($value);
            } else {
                $this->where($key, $value);
            }
        }

        return $this;
    }

    /**
     * @return Model|mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function first() {
        $response = $this->send();

        $result = $this->normalizeResponse($response, true);

        return array_shift($result);
    }

    /**
     * @return Model|mixed
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function firstOrFail() {
        if (!$model = $this->first()) {
            throw (new ModelNotFoundException)->setModel(get_class($this->getModel()));
        } else {
            return $model;
        }
    }

    public function firstOrNew($attributes = []) {
        if (!$model = $this->first()) {
            throw (new ModelNotFoundException)->setModel(get_class($this->getModel()));
        } else {
            return $this->getModel()->newInstance($attributes);
        }
    }

    /**
     * @param Closure $callback
     * @return mixed
     */
    public function firstOr(Closure $callback) {
        if (!$model = $this->first()) {
            throw (new ModelNotFoundException)->setModel(get_class($this->getModel()));
        } else {
            return $callback();
        }
    }

    /**
     * @param $column
     * @return bool|float|Collection|int|mixed|string|null
     */
    public function value($column) {
        if ($result = $this->first()) {
            return $result->{$column};
        } else {
            return null;
        }
    }

    /**
     * @param array $attributes
     * @return Connection|Model|null
     */
    public function newModelInstance($attributes = [])
    {
        if ($this->getModel() instanceof Model) {
            return $this->getModel()->newInstance($attributes);
        } else {
            return null;
        }
    }
}
<?php

namespace Egretos\RestModel\Traits;

use Closure;
use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Facade Trait
 * @package Egretos\RestModel\Traits
 *
 * @mixin Model
 */
trait RestModelFacade
{
    /**
     * @param $param
     * @param string|null $value
     * @return Builder
     */
    public static function where($param, string $value = null) {
        if (is_array($param)) {
            return static::query()->addFormParams($param);
        } else {
            return static::query()->addFormParam($param, $value);
        }
    }

    /**
     * @param string $id
     * @return Builder
     */
    public static function whereKey(string $id) {
        return static::where(static::make()->getRouteKeyName(), $id);
    }

    /**
     * @param $id
     * @return Connection|Model|Model[]|Collection
     */
    public static function find($id)
    {
        if (is_array($id)) {
            return static::findMany($id);
        }

        return static::query()->show($id);
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public static function findMany(array $ids) {
        $models = collect();

        foreach ($ids as $id) {
            $models->push( static::find($id) );
        }

        return $models;
    }

    public static function findOrFail($id) {
        if ($model = static::find( $id ) ) {
            return $model;
        } else {
            throw (new ModelNotFoundException)->setModel(
                get_class(static::class), $id
            );
        }
    }

    public static function findOrNew( $id ) {
        if ($model = static::find( $id ) ) {
            return $model;
        } else {
            return static::make()->newInstance();
        }
    }

    public function findOr(string $id, Closure $callback) {
        if ($model = static::find( $id ) ) {
            return $model;
        } else {
            return $callback();
        }
    }

    /**
     * @param array $params
     * @return Connection|Model|Model[]|Collection
     */
    public static function get(array $params) {
        return static::query()
            ->setQueryParams($params)
            ->index();
    }

    /**
     * @param array $attributes
     * @return Connection|Model|Model[]|Collection
     */
    public function create(array $attributes = []) {
        $this->fill($attributes);
        return $this->newQuery()->create();
    }

    public function forceCreate(array $attributes) {
        $this->forceFill($attributes);
        return $this->newQuery()->create();
    }

    public function update(array $values = []) {
        $this->fill($values);
        return $this->newQuery()->update();
    }

    public function forceUpdate(array $values) {
        $this->fill($values);
        return $this->newQuery()->update();
    }

    public function save() {
        if ($this->exists) {
            return $this->create();
        } else {
            return $this->update();
        }
    }

    public function increment(string $column, $amount = 1, array $extra = []) {
        $this->$column += $amount;
        return $this->forceUpdate($extra);
    }

    public function decrement(string $column, $amount = 1, array $extra = []) {
        $this->$column -= $amount;
        return $this->forceUpdate($extra);
    }
}
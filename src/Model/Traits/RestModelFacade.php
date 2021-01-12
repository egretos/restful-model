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
    public static function all() {
        return static::query()->index();
    }

    /**
     * @param $param
     * @param string|null $value
     * @return Builder
     */
    public static function where($param, string $value = null): Builder
    {
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
    public static function whereKey(string $id): Builder
    {
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
    public static function findMany(array $ids): Collection
    {
        $models = collect();

        foreach ($ids as $id) {
            $models->push( static::find( $id ) );
        }

        return $models;
    }

    /**
     * @param string $id
     * @return Connection|Model|Model[]|Collection
     */
    public static function findOrFail( string $id ) {
        if ($model = static::find( $id ) ) {
            return $model;
        } else {
            throw (new ModelNotFoundException)->setModel(
                get_class(static::class), $id
            );
        }
    }

    /**
     * @param string $id
     * @param array $attributes
     * @return Connection|Model|Model[]|RestModelFacade|Collection|\Jenssegers\Model\Model
     */
    public static function findOrNew( string $id, array $attributes = []) {
        if ($model = static::find( $id ) ) {
            return $model;
        } else {
            return static::make()->newInstance($attributes);
        }
    }

    /**
     * @param string $id
     * @param Closure $callback
     * @return Connection|Model|Model[]|Collection|mixed
     */
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
        return $this->fill($attributes)->newQuery()->create();
    }

    public function forceCreate(array $attributes) {
        return $this->forceFill($attributes)->newQuery()->create();
    }

    public function update(array $values = []) {
        return $this->fill($values)->newQuery()->update();
    }

    public function forceUpdate(array $values) {
        return $this->forceFill($values)->newQuery()->update();
    }

    /**
     * Create the new instance or update it when exists
     *
     * @return bool
     */
    public function save(): bool
    {
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        if ($this->exists) {
            $saved = (bool) $this->create();
        } else {
            $saved = (bool) $this->update();
        }

        if ($saved) {
            $this->finishSave();
        }

        return $saved;
    }

    public function increment(string $column, $amount = 1, array $extra = []) {
        return $this->forceUpdate([
            $column => $this->$column += $amount
            ] + $extra);
    }

    public function decrement(string $column, $amount = 1, array $extra = []) {
        return $this->forceUpdate([
                $column => $this->$column -= $amount
            ] + $extra);
    }
}
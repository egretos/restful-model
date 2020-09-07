<?php

namespace Egretos\RestModel\Traits;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Query\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * Trait Facade
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

    public static function findOrFail( $id ) {
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
}
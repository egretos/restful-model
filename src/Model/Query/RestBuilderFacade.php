<?php

namespace Egretos\RestModel\Query;

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

    public function addArrayOfWheres($column) {
        foreach ($column as $key => $value) {
            if (is_numeric($key) && is_array($value)) {
                $this->addArrayOfWheres($value);
            } else {
                $this->where($key, $value);
            }
        }

        return $this;
    }

    public function whereKey($id) {

    }

    public function find($id)
    {
        $this->getRequest()->route .= "/$id";

        return $this->send();
    }
}
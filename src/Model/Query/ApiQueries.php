<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Request;

/**
 * Trait ApiQueries
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait ApiQueries
{
    public function index() {

    }

    public function show($id) {
        $this->resetRequest();
        $this->request->method = Request::METHOD_GET;

    }

    public function create() {

    }

    public function update() {

    }

    public function delete() {

    }
}
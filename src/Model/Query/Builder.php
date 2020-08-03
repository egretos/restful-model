<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Request;

/**
 * Class Builder
 * @package Egretos\RestModel\Query
 */
final class Builder
{
    use ApiQueries;

    /** @var Connection */
    private $connection;

    /** @var Request */
    private $request;

    /** @var Connection|Model  */
    private $model;

    public function __construct($handled)
    {
        if ($handled instanceof Connection) {
            $this->connection = $handled;
        }

        if ($handled instanceof Model) {
            $this->model = $handled;
            $this->connection = $this->model->connection();
        }
    }

    /**
     * @param bool $resetData
     * @return $this
     */
    public function resetRequest(bool $resetData = true) {
        $this->request = new Request();

        if ($resetData) {
            return $this
                ->resetDomain()
                ->resetRoute();
        }

        return $this;
    }

    public function resetDomain(string $domain = null) {
        if ($domain) {
            $this->request->domain = $domain;
        } elseif ($this->connection instanceof Connection) {
            $this->request->domain = $this->connection->getDomain();
        }
        return $this;
    }

    public function resetRoute(string $route = null) {
        if ($route) {
            $this->request->route = $route;
        } elseif ($this->model instanceof Model) {
            $this->request->route = $this->model->getRoute();
        }

        return $this;
    }
}
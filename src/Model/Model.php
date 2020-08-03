<?php

namespace Egretos\RestModel;

use Egretos\RestModel\Query\Builder;
use Illuminate\Contracts\Routing\UrlRoutable;

/**
 * Class Model
 * @package Egretos\RestModel
 */
abstract class Model extends \Jenssegers\Model\Model implements UrlRoutable
{
    /** @var mixed used for primary key definition */
    protected $primaryKey = 'id';

    /**
     * @var string|null name of endpoint like 'users'
     */
    protected $resource;

    /**
     * @var string|null Connection name in config
     */
    protected $connection;

    /**
     * @var string|null Array index which used for in main index response
     */
    protected $responseArrayIndex;

    /**
     * @var string|null Array index which used in resource responses
     */
    protected $responseIndex;

    /**
     * @var string[] attributes which will be used as header options
     */
    protected $headerAttributes = [];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * @var string $urlPrefix used as path between domain and resource string
     * @var string $urlPostfix used as string on the end of route
     */
    public $urlPrefix;
    public $urlPostfix;

    /**
     * @return string|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     * @return static
     */
    public function setConnection(string $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return Builder
     */
    public function query() {
        return new Builder($this);
    }

    public function connection() {
        return new Connection($this->connection);
    }

    public function getRouteKey() {
        return $this->getAttribute($this->getRouteKeyName());
    }

    public function getRouteKeyName() {
        return $this->getPrimaryKey();
    }

    public function resolveRouteBinding($value, $field = null) {
        $this->setAttribute($this->getRouteKeyName(), $value);
        return $this->query()->show($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field) {
        // TODO something here
    }

    public function getRoute() {
        return implode('/', [
            $this->urlPrefix,
            $this->resource,
            $this->urlPostfix,
            $this->getRouteKey(),
        ]);
    }
}

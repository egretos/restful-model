<?php

namespace Egretos\RestModel;

use Egretos\RestModel\Query\Builder;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

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
    public $responseArrayIndex;

    /**
     * @var string|null Array index which used in resource responses
     */
    public $responseIndex;

    /**
     * @var string[] attributes which will be used as header options
     */
    protected $headerAttributes = [];

    /**
     * @var string[] attributes which will be used for sending data like create or update actions
     */
    protected $sendAbleAttributes = ['*'];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 15;

    /**
     * Indicates if the model exists.
     *
     * @var bool
     */
    public $exists = false;

    /**
     * Indicates if the model was inserted during the current request lifecycle.
     *
     * @var bool
     */
    public $wasRecentlyCreated = false;

    /** @var Request */
    public $lastRequest;

    /** @var ResponseInterface */
    public $lastResponse;

    /**
     * @var string $urlPrefix used as path between domain and resource string
     * @var string $urlPostfix used as string on the end of route
     */
    public $urlPrefix;
    public $urlPostfix;

    public function __construct(array $attributes = [])
    {
        $this->resetResponseIndexes();

        parent::__construct($attributes);
    }

    /**
     * @return string|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get name of usable endpoint
     * @return string|null
     */
    public function getResource()
    {
        return $this->resource ?? Str::snake(Str::pluralStudly(class_basename($this)));
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
        // TODO implement this when it will be used in nested query route
    }

    public function getRoute() {
        $resources = [];

        !$this->connection()->getPrefix() ?: $resources[] = $this->connection()->getPrefix();
        !$this->urlPrefix ?: $resources[] = $this->urlPrefix;
        $resources[] = $this->getResource();
        !$this->urlPostfix ?: $resources[] = $this->urlPostfix;
        !$this->getRouteKey() ?: $resources[] = $this->getRouteKey();

        return implode('/', $resources);
    }

    public function getSendAbleAttributes() {
        if ($this->sendAbleAttributes == ['*']) {
            return $this->getAttributes();
        }

        $attributes = [];

        foreach ($this->sendAbleAttributes as $sendAbleAttributeKey) {
            if ($attribute = $this->getAttribute($sendAbleAttributeKey)) {
                $attributes[$sendAbleAttributeKey] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @return array
     */
    public function getHeaderAttributes() {
        $attributes = [];

        foreach ($this->headerAttributes as $headerAttribute) {
            if ($attribute = $this->getAttribute($headerAttribute)) {
                $attributes[$headerAttribute] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function fillHeaderAttributes(array $headers) {
        foreach ($this->headerAttributes as $headerAttribute) {

            /** GuzzleHttp put response headers to array in array, so we use [0] pointer */
            if (isset($headers[$headerAttribute][0])) {
                $this->setAttribute($headerAttribute, $headers[$headerAttribute][0]);
            }
        }

        return $this;
    }

    public function resetResponseIndexes() {
        $this->responseIndex = $this->connection()->getConfiguration('response_index');

        $this->responseArrayIndex = $this
            ->connection()
            ->getConfiguration('response_array_index', $this->responseIndex);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}

<?php

namespace Egretos\RestModel;

use Egretos\RestModel\Query\Builder;
use Egretos\RestModel\Traits\RestModelFacade;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Model
 * @package Egretos\RestModel
 *
 * TODO model event
 * TODO model -before and -after actions
 * TODO relations with API
 * TODO relations with eloquent
 * TODO Model Facades (from ide-helper)
 * TODO add scopes
 * TODO add casts attributes
 */
abstract class Model extends \Jenssegers\Model\Model implements UrlRoutable
{
    use RestModelFacade;

    /** @var mixed used for primary key definition */
    protected $primaryKey = 'id';

    /**
     * @var string|null name of endpoint like 'users'
     */
    protected $resource;

    /**
     * @var string|null Connection name in config
     */
    public $connection;

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
     * The model attribute's original state.
     *
     * @var array
     */
    protected $original = [];

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
    public function newQuery() {
        return new Builder($this);
    }

    public static function query() {
        return (new static)->newQuery();
    }

    public function getConnection() {
        return new Connection($this->connection);
    }

    /**
     * @return bool|float|\Illuminate\Support\Collection|int|mixed|string|null
     */
    public function getRouteKey() {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * @param string $id
     * @return Model
     */
    public function setRouteKey(string $id) {
        return $this->setAttribute($this->getRouteKeyName(), $id);
    }

    public function getRouteKeyName() {
        return $this->getPrimaryKey();
    }

    public function getKeyName() {
        return $this->getRouteKeyName();
    }

    public function resolveRouteBinding($value, $field = null) {
        $this->setAttribute($this->getRouteKeyName(), $value);
        return $this->newQuery()->show($value);
    }

    public function resolveChildRouteBinding($childType, $value, $field) {
        // TODO implement this when it will be used in nested query route
    }

    public function getRoute() {
        $resources = [];

        !$this->getConnection()->getPrefix() ?: $resources[] = $this->getConnection()->getPrefix();
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
        $this->responseIndex = $this->getConnection()->getConfiguration('response_index');

        $this->responseArrayIndex = $this
            ->getConnection()
            ->getConfiguration('response_array_index', $this->responseIndex);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public static function on(string $connection = null) {
        $instance = new static;

        return $instance->setConnection($connection)->newQuery();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

    /**
     * Get a subset of the model's attributes.
     *
     * @param  array|mixed  $attributes
     * @return array
     */
    public function only(array $attributes)
    {
        $results = [];

        foreach (is_array($attributes) ? $attributes : func_get_args() as $attribute) {
            $results[$attribute] = $this->getAttribute($attribute);
        }

        return $results;
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal()
    {
        $this->original = $this->getAttributes();

        return $this;
    }

    /**
     * Sync multiple original attribute with their current values.
     *
     * @param  array|string  $attributes
     * @return $this
     */
    public function syncOriginalAttributes($attributes)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();

        $modelAttributes = $this->getAttributes();

        foreach ($attributes as $attribute) {
            $this->original[$attribute] = $modelAttributes[$attribute];
        }

        return $this;
    }

    /**
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute(string $attribute)
    {
        return $this->syncOriginalAttributes($attribute);
    }

    /**
     * @param array $attributes
     * @return \Jenssegers\Model\Model|static
     */
    public static function make($attributes = []) {
        return (new static())->newInstance($attributes);
    }
}

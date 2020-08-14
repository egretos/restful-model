<?php

/** @noinspection PhpUndefinedClassInspection */

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Request;
use Illuminate\Support\Collection;
use JsonException;
use LogicException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Builder
 * @package Egretos\RestModel\Query
 */
final class Builder
{
    use ApiQueries, RequestModify;

    /** @var Connection */
    protected $connection;

    /** @var Request */
    protected $request;

    /** @var Connection|Model  */
    protected $model;

    public function __construct($handled)
    {
        if ($handled instanceof Connection) {
            $this->connection = $handled;
        }

        if ($handled instanceof Model) {
            $this->model = $handled;
            $this->connection = $this->model->getConnection();
        }

        $this->resetRequest();
    }

    /**
     * @param bool $resetData
     * @return $this
     */
    public function resetRequest(bool $resetData = true) {
        $this->request = new Request();

        if ($resetData) {
            return $this
                ->resetAuth()
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

    public function resetAuth(array $authData = null, $type = 'basic_auth') {
        if (!$authData) {
            $authData = $this->connection->getConfiguration()->get('auth', null);
            if (!$authData) {
                /** Quite exit when no auth required */
                return $this;
            }

            $type = $authData['type'];
        }

        switch ($type) {
            case 'basic_auth':
                $this->request->auth = [$authData['login'], $authData['password']];
                break;
            default:
                break;
        }

        return $this;
    }

    public function send(Request $request = null) {
        if (!$request) {
            $request = $this->request;
        }

        if (!($this->connection instanceof Connection)) {
            throw new LogicException('Request cannot be sent without connection');
        }

        if (!($request instanceof Request)) {
            throw new LogicException('Request is bad!');
        }

        if ($this->model instanceof Model) {
            $this->model->lastRequest = $request;
        }

        return $this->connection->send($request);
    }

    /**
     * @param ResponseInterface $response
     * @param bool $isArray
     * @return Connection|Model|Model[]|Collection
     * @throws JsonException
     */
    public function normalizeResponse(ResponseInterface $response, $isArray = false) {
        $normalizer = $this->connection->getConfiguration('normalizer', null);

        if (!$normalizer) {
            $normalizer = 'json';
        }

        if ($isArray) {
            return $this->loadMassJsonResponse($this->model, $response);
        }

        switch ($normalizer) {
            case 'json':
                return $this->loadJsonResponse($this->model, $response);
                break;
            case 'body':
                return $this->loadBodyResponse($this->model, $response);
                break;
        }

        return $this->model;
    }

    /**
     * @param Model $model
     * @param ResponseInterface $response
     * @return Collection|Model[]
     * @throws JsonException
     */
    public function loadMassJsonResponse(Model $model, ResponseInterface $response) {
        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            throw new JsonException('Response body has invalid JSON string');
        }

        if ($index = $model->responseArrayIndex) {
            $data = $data[$index];
        }

        $models = [];

        foreach ($data as $modelData) {
            /** @var Model $model */
            $model = $model->newInstance();
            $model->lastRequest = $this->request;
            $model->lastResponse = $response;
            $model->forceFill($modelData);
            $model->fillHeaderAttributes( $response->getHeaders() );
            $model->exists = true;

            $models[] = $model;
        }

        return $model->newCollection($models);
    }

    /**
     * @param Model $model
     * @param ResponseInterface $response
     * @return Model
     * @throws JsonException
     */
    public function loadJsonResponse(Model $model, ResponseInterface $response) {
        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            throw new JsonException('Response body has invalid JSON string');
        }

        if ($index = $model->responseIndex) {
            $data = $data[$index];
        }

        $model->lastResponse = $response;
        $model->forceFill($data);
        $model->fillHeaderAttributes( $response->getHeaders() );
        $model->exists = true;

        return $model;
    }

    /**
     * @param Model $model
     * @param ResponseInterface $response
     * @return Model
     */
    public function loadBodyResponse(Model $model, ResponseInterface $response) {
        $data = json_decode($response->getBody()->getContents(), true);

        $model->lastResponse = $response;
        $model->setAttribute('body', $data);
        $model->exists = true;

        return $model;
    }

    public function prepareModelSaving($model = null) {
        if (!$model) {
            $model = $this->model;
        }

        if (!($model instanceof Model)) {
            throw new LogicException('Model is not exists!');
        }

        $model->newInstance([$model->getRouteKeyName() => $model->getRouteKey()]);

        /** Reset this value before every request */
        $model->wasRecentlyCreated = false;

        $this->addHeaders($model->getHeaderAttributes());

        $model->syncOriginal();

        switch ($this->connection->getConfiguration('content-type')) {
            case 'www-form':
                $this->setFormParams( $model->getSendAbleAttributes() );
                break;
            case 'json':
                $this->setJsonBody( $model->getSendAbleAttributes() );
                break;
        }
    }

    public function prepareModelShowing($model = null) {
        if (!$model) {
            $model = $this->model;
        }

        if (!($model instanceof Model)) {
            throw new LogicException('Model is not exists!');
        }

        /** Reset this value before every request */
        $model->wasRecentlyCreated = false;
    }
}
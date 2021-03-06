<?php

/** @noinspection PhpUndefinedClassInspection */

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Connection;
use Egretos\RestModel\Model;
use Egretos\RestModel\Request;
use GuzzleHttp\Exception\RequestException;
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
    use ApiQueries, RequestModify, BearerAuth, RestBuilderFacade;

    /** @var Connection */
    protected $connection;

    /** @var Request */
    protected $request;

    /** @var Connection|Model  */
    protected $model;

    public function __construct($handled = null)
    {
        if ($handled instanceof Connection) {
            $this->setConnection( $handled );
        }

        if ($handled instanceof Model) {
            $this->setModel( $handled ) ;
            $this->setConnection( $this->model->getConnection() );
        }

        if ($this->getConnection()) {
            $this->resetRequest();
        }
    }

    /**
     * @return Connection|null
     */
    public function getConnection(): ?Connection
    {
        return $this->connection;
    }

    /**
     * @return bool
     */
    public function connectionExists(): bool
    {
        return $this->getConnection() !== null;
    }

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection): Builder
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request): Builder
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Connection|Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel(Model $model): Builder
    {
        $this->model = $model;
        return $this;
    }

    /**
     * @param bool $resetData
     * @return $this
     */
    public function resetRequest(bool $resetData = true): Builder
    {
        $this->setRequest( new Request() );

        if ($resetData) {
            return $this
                ->resetAuth()
                ->resetDomain()
                ->resetRoute();
        }

        return $this;
    }

    public function resetDomain(string $domain = null): Builder
    {
        if ($domain) {
            $this->getRequest()->domain = $domain;
        } elseif ($this->getConnection() instanceof Connection) {
            $this->getRequest()->domain = $this->connection->getDomain();
        }
        return $this;
    }

    public function resetRoute(string $route = null): Builder
    {
        if ($route) {
            $this->setRoute($route);
        } elseif ($this->getModel() instanceof Model) {
            $this->setRoute( $this->model->getRoute() );
        }

        return $this;
    }

    /**
     * @param array|null $authData
     * @param string $type
     * @return $this
     * @throws
     */
    public function resetAuth(array $authData = null, $type = 'basic_auth'): Builder
    {
        if (!$authData) {
            $authData = $this->getConnection()->getConfiguration()->get('auth');
            if (!$authData) {
                /** Quite exit when no auth required */
                return $this;
            }

            $type = $authData['type'];
        }

        switch ($type) {
            case 'basic_auth':
                $this
                    ->getRequest()
                    ->setAuth([$authData['login'], $authData['password']]);
                break;

            case 'form_data':
                $this
                    ->setFormParam( $authData['login_field'], $authData['login'] )
                    ->setFormParam( $authData['password_field'], $authData['password'] );
                break;

            case 'bearer':
                $this
                    ->refreshToken()
                    ->addHeader('Authorization', "Bearer ".$this->getToken());
                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * @param Request|null $request
     * @return ResponseInterface|null
     * @throws
     */
    public function send(Request $request = null): ?ResponseInterface
    {
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

        try {
            $result = $this->connection->send($request);
        } catch (RequestException $exception) {
            $result = $exception->getResponse();
        }

        if ($this->model instanceof Model) {
            $this->model->lastResponse = $result;
            $this->model->fireModelEvent('retrieved', false);

            if (isset($exception)) {
                $this->model->fireModelEvent('faulted', false);
            }
        }

        return $result ?? null;
    }

    /**
     * @param ResponseInterface $response
     * @param bool $isArray
     * @return Connection|Model|Model[]|Collection
     * @throws
     */
    public function normalizeResponse(ResponseInterface $response, $isArray = false) {
        $normalizer = $this->connection->getConfiguration('normalizer');

        if (!$normalizer) {
            $normalizer = 'json';
        }

        if ($isArray) {
            return $this->loadMassJsonResponse($this->model, $response);
        }

        switch ($normalizer) {
            case 'json':
                return $this->loadJsonResponse($this->model, $response);
            case 'body':
                return $this->loadBodyResponse($this->model, $response);
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
            $model = $model
                ->newInstance()
                ->forceFill( $modelData )
                ->fillFromResponseHeader( $response->getHeaders() );
            $model->lastRequest = $this->request;
            $model->lastResponse = $response;
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
    public function loadJsonResponse(Model $model, ResponseInterface $response): Model
    {
        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            throw new JsonException('Response body has invalid JSON string');
        }

        if ($index = $model->responseIndex) {
            $data = $data[$index];
        }

        $model->lastResponse = $response;
        $model->forceFill($data);
        $model->fillFromResponseHeader( $response->getHeaders() );
        $model->exists = true;

        return $model;
    }

    /**
     * @param Model $model
     * @param ResponseInterface $response
     * @return Model
     */
    public function loadBodyResponse(Model $model, ResponseInterface $response): Model
    {
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
            case 'www-form': case 'x-www-form-urlencoded':
                $this->addFormParams( $model->getSendAbleAttributes() );
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

    /**
     * Get the default key name of the table.
     *
     * @return string
     */
    protected function defaultKeyName(): string
    {
        return $this->getModel()->getRouteKeyName();
    }
}
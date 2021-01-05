<?php

namespace Egretos\RestModel;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

final class Connection
{
    /** @var string */
    public $connection;

    /** @var array|Collection  */
    public $config;

    public function __construct($connection)
    {
        $this->connection = $connection;

        $this->loadConfig();
    }

    public function loadConfig(): self
    {
        $this->config = collect( config('rest_connections') );

        return $this;
    }

    /**
     * @return string|array|mixed
     */
    public function getDomain() {
        return $this->getConfiguration('domain');
    }

    /**
     * @param string|null $param
     * @param null $default
     * @return Collection|mixed
     *
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getConfiguration(string $param = null, $default = null)
    {
        $connection = $this->connection;

        if (!$connection) {
            $connection = $this->config['default_connection'];
        }

        $config = collect($this->config->toArray())
            ->merge($this->config['connections'][$connection]);

        if ($param) {
            if (isset($config[$param])) {
                return $config[$param];
            } elseif ($default) {
                return $default;
            } else {
                return null;
            }
        } else {
            return $config;
        }
    }

    /**
     * @return string
     */
    public function getPrefix(): ?string
    {
        if ($this->getConfiguration()) {
            return  $this->getConfiguration()->get('prefix');
        }

        return (string) $this->config->get('prefix');
    }

    /**
     * @param Request $request
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function send(Request $request): ResponseInterface
    {
        $client = new Client(['base_uri' => $this->getDomain()]);

        return $client->request($request->method, $request->route, $request->toGuzzleOptions());
    }
}
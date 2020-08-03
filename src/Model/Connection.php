<?php

namespace Egretos\RestModel;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

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

    public function loadConfig() {
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
     * @return array|mixed
     */
    public function getConfiguration(string $param = null) {
        $connection = $this->connection;

        if (!$connection) {
            $connection = $this->config['default_connection'];
        }

        $config = array_merge($this->config, $this->config['connections'][$connection]);

        if ($param) {
            return $config['param'];
        } else {
            return $config;
        }
    }

    public function send(Request $request) {
        $client = new Client(['base_uri' => $this->getDomain()]);

        dd($request);

        $response = $client->request($request->method, $request->route, $request->toGuzzleOptions());
    }
}
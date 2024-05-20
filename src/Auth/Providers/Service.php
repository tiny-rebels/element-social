<?php

namespace app\handlers\auth\social;

use GuzzleHttp\Client;
use Noodlehaus\Config;

abstract class Service {

    /**
     * The container instance.
     *
     * @var \Interop\Container\ContainerInterface
     */
    protected $c;

    /**
     * The client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Service constructor.
     * @param Client $client
     * @param Config $config
     */
    public function __construct(Client $client, Config $config) {

        $this->client = $client;
        $this->config = $config;
    }

    abstract public function getAuthorizeUrl();
    abstract public function getUserByCode($code);

    public function authorizeUrl() {

        return $this->getAuthorizeUrl();
    }

    public function getUser($code) {

        return $this->getUserByCode($code);
    }
}

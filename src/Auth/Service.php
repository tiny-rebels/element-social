<?php

namespace Element\Social\Auth;

use GuzzleHttp\Client;

abstract class Service
{

    /**
     * The client instance.
     *
     * @var Client
     */
    protected $client;

    /**
     * Service constructor.
     *
     * @param Client $client
     * @param array $config
     */
    public function __construct(Client $client, $config) {

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
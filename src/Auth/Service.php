<?php

namespace Auth;

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
     */
    public function __construct(Client $client) {

        $this->client = $client;
    }

    abstract public function getAuthorizeUrl($client_id, $redirect_uri);
    abstract public function getUserByCode($code);

    public function authorizeUrl($client_id, $redirect_uri) {

        return $this->getAuthorizeUrl($client_id, $redirect_uri);
    }

    public function getUser($code) {

        return $this->getUserByCode($code);
    }

}
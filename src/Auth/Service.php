<?php

namespace Element\Social\Auth;

use GuzzleHttp\Client as HttpClient;

abstract class Service {

    /**
     * Service dependencies
     */
    protected HttpClient $httpClient;
    protected array $config;

    /**
     * Service constructor.
     *
     * @param HttpClient $httpClient
     * @param array $config
     */
    public function __construct(HttpClient $httpClient, array $config) {

        $this->httpClient   = $httpClient;
        $this->config       = $config;
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
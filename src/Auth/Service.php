<?php

namespace Element\Social\Auth;

use GuzzleHttp\Client as HttpClient;

abstract class Service {

    /**
     * BaseController dependencies
     */
    protected HttpClient $httpClient;

    /**
     * BaseController constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient) {

        $this->httpClient = $httpClient;
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
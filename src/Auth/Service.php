<?php

namespace Element\Social\Auth;

use GuzzleHttp\Client;

abstract class Service {

    abstract public function getAuthorizeUrl($client_id, $redirect_uri);
    abstract public function getUserByCode($code);

    public function authorizeUrl($client_id, $redirect_uri) {

        return $this->getAuthorizeUrl($client_id, $redirect_uri);
    }

    public function getUser($code) {

        return $this->getUserByCode($code);
    }

}
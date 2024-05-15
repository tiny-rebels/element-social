<?php

namespace Element\Social;

use Exception;

use Element\Social\Auth\{
    Config,
    Providers\Github
};

class Authenticator {

    public function __construct($config = null) {

        if (is_string($config)) {

            $this->config = new Config($config);

        } else {

            $this->config = $config ?: new Config();

        }
    }

    public static function withGithub() {

        $config = new Config();

        $client_id      = $config['gh']['client_id'];
        $redirect_uri   = $config['gh']['client_secret'];

        $github = new Github();

        try {

            $github->getAuthorizeUrl($client_id, $redirect_uri);

        } catch (Exception $error) {

            //...do something
            die($error);
        }
    }
}
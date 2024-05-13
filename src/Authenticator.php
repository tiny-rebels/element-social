<?php

use Auth\Providers\Github;
use Auth\Config;

class Authenticator {

    public function __construct($config = null) {

        if (is_string($config)) {

            $this->config = new Config($config);

        } else {

            $this->config = $config ?: new Config();

        }
    }

    public function loginWithGithub() {

        $client_id      = $this->config['gh']['client_id'];
        $redirect_uri   = $this->config['gh']['client_secret'];

        try {

            Github::class->authorizeUrl($client_id, $redirect_uri);

        } catch (Exception $error) {

            //...do something
            die($error);
        }
    }
}
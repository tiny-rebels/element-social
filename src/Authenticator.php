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

        $clientID       = $this->config['users']['model'];
        $redirectUri    = $this->config['roles']['model'];

        try {

            Github::class->authorizeUrl('', '');

        } catch (Exception $error) {

            //...do something

        }
    }
}
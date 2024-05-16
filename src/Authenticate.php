<?php

namespace Element\Social;

use Exception;

use Element\Social\Auth\{
    Providers\Github
};

class Authenticate {

    /**
     * @param $service
     *
     * @return Github|void
     */
    public static function with($service) {

        switch ($service) {
            case 'github':
                return new Github();
        }
    }
}
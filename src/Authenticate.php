<?php

namespace Element\Social;

use GuzzleHttp\Client as HttpClient;

use Element\Social\Auth\{
    Providers\Facebook,
    Providers\Github,
    Providers\Google,
    Providers\Linkedin,
    Providers\Microsoft
};

class Authenticate {

    /**
     * @param $service
     * @param array $config
     *
     * @return Facebook|Github|Google|Linkedin|Microsoft|void
     */
    public static function with($service, array $config = []) {

        $httpClient = new HttpClient;

        switch ($service) {

            case 'facebook':
                return new Facebook($httpClient, $config);

            case 'github':
                return new Github($httpClient, $config);

            case 'google':
                return new Google($httpClient, $config);

            case 'linkedin':
                return new Linkedin($httpClient, $config);

            case 'microsoft':
                return new Microsoft($httpClient, $config);
        }
    }
}
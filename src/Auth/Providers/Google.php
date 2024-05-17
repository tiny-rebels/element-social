<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |--------------------------------------------------------------------------------------------|
 * | Visit the official docs :                                                                  |
 * | https://developers.google.com/identity/protocols/oauth2/openid-connect#createxsrftoken     |
 * |--------------------------------------------------------------------------------------------|
 */
class Google extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://accounts.google.com/o/oauth2/v2/auth"
                . "?response_type=code"
                . "&client_id=" . $this->config['client_id']
                . "&scope=openid%20profile%20email"
                . "&redirect_uri=" . $this->config['redirect_uri']
                . "&state=" . bin2hex(random_bytes(16))
                . "&nonce=" . bin2hex(random_bytes(16));

        } catch (\Exception $error) {

            return $error->getMessage();
        }
    }

    /**
     * @param $code
     *
     * @return object
     */
    public function getUserByCode($code): object {

        $token = $this->getAccessTokenFromCode($code);

        return $this->normalizeUser($this->getUserByToken($token));
    }

    protected function getAccessTokenFromCode($code) {

        try {

            $response = $this->httpClient->request('POST', 'https://oauth2.googleapis.com/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'redirect_uri'  => $this->config['redirect_uri'],
                    'grant_type'    => 'authorization_code'
                ]
            ])->getBody();

        } catch (GuzzleException $error) {

            dump($error->getMessage());
            die;

        }

        return json_decode($response)->access_token;
    }

    protected function getUserByToken($token) {

        try {

            $response = $this->httpClient->request('GET', 'https://openidconnect.googleapis.com/v1/userinfo', [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ]
            ])->getBody();

        } catch (GuzzleException $error) {

            dump($error->getMessage());
            die;

        }

        return json_decode($response);
    }

    /**
     * @param $user
     *
     * @return object
     */
    protected function normalizeUser($user): object {

        return (object) [

            'uid'       => $user->sub,
            'username'  => null,
            'name'      => $user->name,
            'email'     => $user->email,
            'photo'     => $user->picture,
        ];
    }
}

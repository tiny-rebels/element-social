<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |--------------------------------------------------------------------------------------------|
 * | Visit the official docs :                                                                  |
 * | https://github.com/settings/developers                                                     |
 * |--------------------------------------------------------------------------------------------|
 */
class Github extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://github.com/login/oauth/authorize"
                . "?client_id="
                . $this->config['client_id']
                . "&redirect_uri="
                . $this->config['redirect_uri']
                . "&scopes="
                . $this->config['scopes']
                . "&state=" . bin2hex(random_bytes(16));

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

            $response = $this->httpClient->request('GET', 'https://github.com/login/oauth/access_token', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'client_id'     => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'redirect_uri'  => $this->config['redirect_uri'],
                    'code'          => $code,
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

            $response = $this->httpClient->request('GET', 'https://api.github.com/user', [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ],
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

            'uid'       => $user->id,
            'username'  => $user->login,
            'name'      => $user->name,
            'email'     => $user->email,
            'photo'     => $user->avatar_url,
        ];
    }
}

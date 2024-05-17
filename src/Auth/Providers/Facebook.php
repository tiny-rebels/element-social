<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |--------------------------------------------------------------------------------------------|
 * | Visit the official docs :                                                                  |
 * | https://developers.facebook.com/apps/                                                      |
 * |--------------------------------------------------------------------------------------------|
 */
class Facebook extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://www.facebook.com/dialog/oauth"
                . "?client_id=" . $this->config['client_id']
                . "&redirect_uri=" . $this->config['redirect_uri']
                . "&scope=email,public_profile"
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

            $response = $this->httpClient->request('GET', 'https://graph.facebook.com/v2.3/oauth/access_token', [
                'query' => [
                    'client_id'     => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'redirect_uri'  => $this->config['redirect_uri'],
                    'code' => $code,
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

            $response = $this->httpClient->request('GET', 'https://graph.facebook.com/me', [
                'query' => [
                    'access_token' => $token,
                    'fields' => 'id,name,email,picture'
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
            'username'  => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'photo'     => $user->picture->data->url,
        ];
    }
}

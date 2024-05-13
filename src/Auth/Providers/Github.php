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
    public function getAuthorizeUrl() {

        try {

            return "https://github.com/login/oauth/authorize?client_id="
                . $this->config->get('sso.gh.client_id')
                . "&redirect_uri="
                . $this->config->get('sso.gh.redirect_uri')
                . "&scopes=user,user:email"
                . "&state=" . bin2hex(random_bytes(16));

        } catch (\Exception $e) {

            return $e;
        }
    }

    public function getUserByCode($code) {

        $token = $this->getAccessTokenFromCode($code);

        return $this->normalizeUser($this->getUserByToken($token));
    }

    protected function getAccessTokenFromCode($code) {

        try {

            $response = $this->client->request('GET', 'https://github.com/login/oauth/access_token', [
                'headers' => [
                    'accept' => 'application/json',
                ],
                'query' => [
                    'client_id' => $this->config->get('sso.gh.client_id'),
                    'client_secret' => $this->config->get('sso.gh.client_secret'),
                    'redirect_uri' => $this->config->get('sso.gh.redirect_uri'),
                    'code' => $code,
                ]
            ])->getBody();

        } catch (GuzzleException $e) {

            // return exception

        }

        return json_decode($response)->access_token;
    }

    protected function getUserByToken($token) {

        try {

            $response = $this->client->request('GET', 'https://api.github.com/user', [
                'headers' => [
                    "Authorization" => "Bearer " . $token
                ],
            ])->getBody();

        } catch (GuzzleException $e) {

            // return exception

        }

        return json_decode($response);
    }

    protected function normalizeUser($user) {

        return (object) [

            'uid'       => $user->id,
            'username'  => $user->login,
            'name'      => $user->name,
            'email'     => $user->email,
            'photo'     => $user->avatar_url,
        ];
    }
}

<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |--------------------------------------------------------------------------------------------|
 * | Visit the official docs                                                                    |
 * | https://docs.microsoft.com/en-us/azure/active-directory/develop/v2-oauth2-auth-code-flow   |
 * |--------------------------------------------------------------------------------------------|
 */
class Microsoft extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://login.microsoftonline.com/consumers/oauth2/v2.0/authorize"
                . "?client_id="
                . $this->config['client_id']
                . "&response_type=code"
                . "&redirect_uri="
                . $this->config['redirect_uri']
                . "&response_mode=query"
                . "&scope="
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

            $response = $this->httpClient->request('POST', 'https://login.microsoftonline.com/consumers/oauth2/v2.0/token', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'tenant'        => $this->config['tenant'],
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $this->config['client_id'],
                    'client_secret' => $this->config['client_secret'],
                    'redirect_uri'  => $this->config['redirect_uri'],
                    'code'          => $code
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

            $response = $this->httpClient->request('GET', 'https://graph.microsoft.com/v1.0/me', [
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

            'uid'       => $user->id,
            'username'  => null,
            'name'      => $user->displayName,
            'email'     => $user->userPrincipalName,
            'photo'     => 'src/Assets/img_user_default.png',
        ];
    }
}

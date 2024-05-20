<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |----------------------------------------------------------------------------------------------------------------------------|
 * | Visit the official docs :                                                                                                  |
 * | https://learn.microsoft.com/en-us/linkedin/consumer/integrations/self-serve/sign-in-with-linkedin?source=recommendations   |
 * |----------------------------------------------------------------------------------------------------------------------------|
 */
class Linkedin extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://www.linkedin.com/oauth/v2/authorization"
                . "?response_type=code"
                . "&client_id="
                . $this->config['client_id']
                . "&redirect_uri="
                . $this->config['redirect_uri']
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

            $response = $this->httpClient->request('POST', 'https://www.linkedin.com/oauth/v2/accessToken', [
                'headers' => [
                    'Content-Type' => 'x-www-form-urlencoded',
                ],
                'query' => [
                    'grant_type' => 'authorization_code',
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

        $authObject = json_decode($response);

        return $authObject->access_token;
    }

    protected function getUserByToken($token) {

        try {

            $response = $this->httpClient->request('GET', 'https://api.linkedin.com/v2/userinfo', [
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

        // TODO : Vi skal have mappet værdierne korrekt her!

        dump("normalizeUser: ");
        dump($user);
        die;

        return (object) [

            'uid'       => $user->r_lite->id,
            'username'  => 'TODO',
            'name'      => $user->r_lite->localizedFirstName . " " . $user->r_lite->localizedLastName,
            'email'     => $user->r_emailaddress->{"handle~"}->emailAddress,
            'photo'     => 'src/Assets/img_user_default.png',
        ];
    }
}

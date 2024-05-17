<?php

namespace Element\Social\Auth\Providers;

use Element\Social\Auth\Service;
use GuzzleHttp\Exception\GuzzleException;

/*
 * |--------------------------------------------------------------------------------------------|
 * | Visit the official docs :                                                                  |
 * | ?                                                                                          |
 * |--------------------------------------------------------------------------------------------|
 */
class Linkedin extends Service {

    /**
     * @return string
     */
    public function getAuthorizeUrl(): string {

        try {

            return "https://www.linkedin.com/oauth/v2/authorization"
                . "?response_type=code"
                . "&client_id=" . $this->config['client_id']
                . "&redirect_uri=" . $this->config['redirect_uri']
                . "&scope=r_liteprofile%20r_emailaddress"
                //. "&scope=r_liteprofile%20r_emailaddress%20w_member_social"
                //. "&scope=r_basicprofile%20r_emailaddress"
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

        return json_decode($response);
    }

    protected function getUserByToken($token) {

        try {

            $r_lite = $this->httpClient->request('GET', 'https://api.linkedin.com/v2/me', [
                'headers' => [
                    "Authorization" => "Bearer " . $token->access_token,
                    "Content-Type"  => "application/json",
                    "x-li-format"   =>"json"
                ],
            ])->getBody();

            $userProfile = json_decode($r_lite);

            $r_emailaddress = $this->httpClient->request('GET', 'https://api.linkedin.com/v2/clientAwareMemberHandles?q=members&projection=(elements*(primary,EMAIL,handle~))', [
                'headers' => [
                    "Authorization" => "Bearer " . $token->access_token,
                    "Content-Type"  => "application/json",
                    "x-li-format"   =>"json"
                ],
            ])->getBody();

            $userEmail = json_decode($r_emailaddress);

            $user = (object) [
                "r_lite"            => $userProfile,
                "r_emailaddress"    => $userEmail->elements[0]
            ];

        } catch (GuzzleException $error) {

            dump($error->getMessage());
            die;

        }

        return $user;
    }

    /**
     * @param $user
     *
     * @return object
     */
    protected function normalizeUser($user): object {

        return (object) [

            'uid'       => $user->r_lite->id,
            'username'  => 'TODO',
            'name'      => $user->r_lite->localizedFirstName . " " . $user->r_lite->localizedLastName,
            'email'     => $user->r_emailaddress->{"handle~"}->emailAddress,
            'photo'     => 'src/Assets/img_user_default.png',
        ];
    }
}

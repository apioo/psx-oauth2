<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Oauth2;

use PSX\Http\Client\ClientInterface;
use PSX\Http\Client\PostRequest;
use PSX\Json;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Uri\Url;
use RuntimeException;

/**
 * AuthorizationAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class AuthorizationAbstract
{
    const AUTH_BASIC = 0x1;
    const AUTH_POST  = 0x2;

    protected ClientInterface $httpClient;
    protected Url $url;

    protected ?string $clientId = null;
    protected ?string $clientSecret = null;
    protected ?int $type = null;
    protected ?string $accessTokenClass = null;

    public function __construct(ClientInterface $httpClient, Url $url)
    {
        $this->httpClient = $httpClient;
        $this->url        = $url;
    }

    public function setClientPassword(string $clientId, string $clientSecret, int $type = 0x1)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->type         = $type;
    }

    /**
     * Sets the class which is created when an access token gets returned.
     * Should be an instance of PSX\Oauth2\AccessToken. This can be used to
     * handle custom parameters
     *
     * @param string $accessTokenClass
     */
    public function setAccessTokenClass(string $accessTokenClass)
    {
        $this->accessTokenClass = $accessTokenClass;
    }

    /**
     * Tries to refresh an access token if an refresh token is available.
     * Returns the new received access token or throws an excepion
     */
    public function refreshToken(AccessToken $accessToken): AccessToken
    {
        // request data
        $refreshToken = $accessToken->getRefreshToken();
        $scope        = $accessToken->getScope();

        if (empty($refreshToken)) {
            throw new RuntimeException('No refresh token was set');
        }

        $data = array(
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        );

        if (!empty($scope)) {
            $data['scope'] = $scope;
        }

        // authentication
        $header = array();

        if ($this->type == self::AUTH_BASIC) {
            $header['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
        }

        if ($this->type == self::AUTH_POST) {
            $data['client_id']     = $this->clientId;
            $data['client_secret'] = $this->clientSecret;
        }

        $request  = new PostRequest($this->url, $header, $data);
        $response = $this->httpClient->request($request);

        $data = Json\Parser::decode($response->getBody(), true);

        if ($response->getStatusCode() == 200) {
            return $this->newToken($data);
        } else {
            throw new RuntimeException('Could not refresh access token');
        }
    }

    protected function request(array $headers, mixed $data): AccessToken
    {
        $request  = new PostRequest($this->url, $headers, $data);
        $response = $this->httpClient->request($request);

        $data = Json\Parser::decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {
            self::throwErrorException($data);
        }

        return $this->newToken($data);
    }

    protected function newAccessToken(): AccessToken
    {
        if ($this->accessTokenClass != null) {
            return new $this->accessTokenClass();
        } else {
            return new AccessToken();
        }
    }

    private function newToken(array $data): AccessToken
    {
        $record = $this->newAccessToken();

        foreach ($data as $key => $value) {
            $record->setProperty($key, $value);
        }

        return $record;
    }

    /**
     * Each class which extends PSX\Oauth2\Authorization should have the method
     * getAccessToken(). Since the method can have different arguments we can
     * not declare the method as abstract but it will stay here for reference
     *
     * @return \PSX\Oauth2\AccessToken
     */
    //abstract public function getAccessToken();

    /**
     * Parses the $data array for an error response and throws the most fitting
     * exception including also the error message and url if available
     *
     * @param array $data
     * @throws ErrorExceptionAbstract
     */
    public static function throwErrorException(array $data)
    {
        // unfortunately facebook doesnt follow the oauth draft 26 and set in the
        // response error key the correct error string instead the error key
        // contains an object with the type and message. Temporary we will use
        // this hack since the spec is not an rfc. If the rfc is released we
        // will strictly follow the spec and remove this hack hopefully facebook
        // too
        if (isset($data['error']) && is_array($data['error']) && isset($data['error']['type']) && isset($data['error']['message'])) {
            $data['error_description'] = $data['error']['message'];
            $data['error'] = 'invalid_request';
        }

        $error = isset($data['error']) ? strtolower($data['error']) : null;
        $desc  = isset($data['error_description']) ? htmlspecialchars($data['error_description']) : null;

        switch ($error) {
            case 'access_denied':
                throw new Authorization\Exception\AccessDeniedException($desc);
            case 'invalid_client':
                throw new Authorization\Exception\InvalidClientException($desc);
            case 'invalid_grant':
                throw new Authorization\Exception\InvalidGrantException($desc);
            case 'invalid_request':
                throw new Authorization\Exception\InvalidRequestException($desc);
            case 'invalid_scope':
                throw new Authorization\Exception\InvalidScopeException($desc);
            case 'server_error':
                throw new Authorization\Exception\ServerErrorException($desc);
            case 'temporarily_unavailable':
                throw new Authorization\Exception\TemporarilyUnavailableException($desc);
            case 'unauthorized_client':
                throw new Authorization\Exception\UnauthorizedClientException($desc);
            case 'unsupported_grant_type':
                throw new Authorization\Exception\UnsupportedGrantTypeException($desc);
            case 'unsupported_response_type':
                throw new Authorization\Exception\UnsupportedResponseTypeException($desc);
            default:
                throw new RuntimeException('Invalid error type');
        }
    }
}

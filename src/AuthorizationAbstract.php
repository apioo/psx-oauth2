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
 * @link    https://phpsx.org
 */
abstract class AuthorizationAbstract
{
    public const AUTH_BASIC = 0x1;
    public const AUTH_POST  = 0x2;

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

    public function setClientPassword(string $clientId, string $clientSecret, int $type = self::AUTH_BASIC)
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

        $data = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        if (!empty($scope)) {
            $data['scope'] = $scope;
        }

        // authentication
        $headers = [];

        if ($this->type === self::AUTH_BASIC) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
        }

        if ($this->type === self::AUTH_POST) {
            $data['client_id']     = $this->clientId;
            $data['client_secret'] = $this->clientSecret;
        }

        $request  = new PostRequest($this->url, $headers, $data);
        $response = $this->httpClient->request($request);

        $data = Json\Parser::decode($response->getBody(), true);

        if ($response->getStatusCode() == 200) {
            return AccessToken::fromArray($data);
        } else {
            throw new RuntimeException('Could not refresh access token');
        }
    }

    /**
     * @throws \JsonException
     * @throws ErrorExceptionAbstract
     */
    protected function request(array $headers, mixed $data): AccessToken
    {
        $request  = new PostRequest($this->url, $headers, $data);
        $response = $this->httpClient->request($request);

        $data = Json\Parser::decode($response->getBody(), true);

        if ($response->getStatusCode() != 200) {
            self::throwErrorException($data);
        }

        return AccessToken::fromArray($data);
    }

    /**
     * This method requests an access token for the provided grant type
     */
    abstract public function getAccessToken(GrantInterface $grant): AccessToken;

    /**
     * Parses the $data array for an error response and throws the most fitting
     * exception including also the error message and url if available
     *
     * @throws ErrorExceptionAbstract
     */
    public static function throwErrorException(array $data)
    {
        $error = Error::fromArray($data);

        switch ($error->getError()) {
            case 'access_denied':
                throw new Authorization\Exception\AccessDeniedException($error->getErrorDescription());
            case 'invalid_client':
                throw new Authorization\Exception\InvalidClientException($error->getErrorDescription());
            case 'invalid_grant':
                throw new Authorization\Exception\InvalidGrantException($error->getErrorDescription());
            case 'invalid_request':
                throw new Authorization\Exception\InvalidRequestException($error->getErrorDescription());
            case 'invalid_scope':
                throw new Authorization\Exception\InvalidScopeException($error->getErrorDescription());
            case 'server_error':
                throw new Authorization\Exception\ServerErrorException($error->getErrorDescription());
            case 'temporarily_unavailable':
                throw new Authorization\Exception\TemporarilyUnavailableException($error->getErrorDescription());
            case 'unauthorized_client':
                throw new Authorization\Exception\UnauthorizedClientException($error->getErrorDescription());
            case 'unsupported_grant_type':
                throw new Authorization\Exception\UnsupportedGrantTypeException($error->getErrorDescription());
            case 'unsupported_response_type':
                throw new Authorization\Exception\UnsupportedResponseTypeException($error->getErrorDescription());
            default:
                throw new RuntimeException('Invalid error type');
        }
    }
}

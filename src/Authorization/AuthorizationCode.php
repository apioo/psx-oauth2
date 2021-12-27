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

namespace PSX\Oauth2\Authorization;

use PSX\Http\Exception as StatusCode;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Grant;
use PSX\Oauth2\AuthorizationAbstract;
use PSX\Oauth2\GrantInterface;
use PSX\Uri\Url;

/**
 * AuthorizationCode
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AuthorizationCode extends AuthorizationAbstract
{
    public function getAccessToken(GrantInterface $grant): AccessToken
    {
        if (!$grant instanceof Grant\AuthorizationCode) {
            throw new \RuntimeException('Provided an invalid grant type');
        }

        $data = $grant->toArray();

        $headers = [
            'Accept'     => 'application/json',
            'User-Agent' => __CLASS__,
        ];

        if ($this->type === self::AUTH_BASIC) {
            $headers['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
        }

        if ($this->type === self::AUTH_POST) {
            $data['client_id']     = $this->clientId;
            $data['client_secret'] = $this->clientSecret;
        }

        return $this->request($headers, $data);
    }

    /**
     * Helper method to start the flow by redirecting the user to the
     * authentication server. The getAccessToken method must be used when the
     * server redirects the user back to the redirect uri
     */
    public static function redirect(Url $url, string $clientId, ?string $redirectUri = null, ?string $scope = null, ?string $state = null): void
    {
        $parameters = $url->getParameters();
        $parameters['response_type'] = 'code';
        $parameters['client_id']     = $clientId;

        if (isset($redirectUri)) {
            $parameters['redirect_uri'] = $redirectUri;
        }

        if (isset($scope)) {
            $parameters['scope'] = $scope;
        }

        if (isset($state)) {
            $parameters['state'] = $state;
        }

        throw new StatusCode\TemporaryRedirectException($url->withScheme('https')->withParameters($parameters)->toString());
    }
}

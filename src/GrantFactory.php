<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\OAuth2;

use PSX\OAuth2\Exception\MissingParameterException;

/**
 * The factory create a grant based on the provided parameters
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GrantFactory
{
    public static function factory(array $parameters): GrantInterface
    {
        $grantType = $parameters['grant_type'] ?? null;
        switch ($grantType) {
            case 'authorization_code':
                return new Grant\AuthorizationCode(
                    $parameters['code'] ?? throw new MissingParameterException('Parameter code is missing'),
                    $parameters['redirect_uri'] ?? null,
                    $parameters['client_id'] ?? null,
                    $parameters['client_secret'] ?? null
                );

            case 'client_credentials':
                return new Grant\ClientCredentials($parameters['scope'] ?? null);

            case 'password':
                return new Grant\Password(
                    $parameters['username'] ?? throw new MissingParameterException('Parameter username is missing'),
                    $parameters['password'] ?? throw new MissingParameterException('Parameter password is missing'),
                    $parameters['scope'] ?? null
                );

            case 'refresh_token':
                return new Grant\RefreshToken(
                    $parameters['refresh_token'] ?? throw new MissingParameterException('Parameter refresh_token is missing'),
                    $parameters['scope'] ?? null
                );

            default:
                throw new \RuntimeException('Provided an invalid grant type');
        }
    }
}

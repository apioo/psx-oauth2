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

namespace PSX\Oauth2\Grant;

use PSX\Oauth2\GrantInterface;

/**
 * RefreshToken
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RefreshToken implements GrantInterface
{
    private string $grantType = 'refresh_token';
    private string $refreshToken;
    private ?string $scope;

    public function __construct(string $refreshToken, ?string $scope = null)
    {
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function toArray(): array
    {
        $data = [
            'grant_type'    => $this->grantType,
            'refresh_token' => $this->refreshToken,
        ];

        if (!empty($this->scope)) {
            $data['scope'] = $this->scope;
        }

        return $data;
    }
}

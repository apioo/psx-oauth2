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

use PSX\Record\Record;

/**
 * AccessToken
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessToken extends Record
{
    public function setAccessToken(string $accessToken): void
    {
        $this->setProperty('access_token', $accessToken);
    }

    public function getAccessToken(): ?string
    {
        return $this->getProperty('access_token');
    }

    public function setTokenType(string $tokenType): void
    {
        $this->setProperty('token_type', $tokenType);
    }

    public function getTokenType(): ?string
    {
        return $this->getProperty('token_type');
    }

    public function setExpires(int $expiresIn): void
    {
        $this->setProperty('expires_in', $expiresIn);
    }

    public function setExpiresIn($expiresIn): void
    {
        $this->setProperty('expires_in', (int) $expiresIn);
    }

    public function getExpiresIn(): ?int
    {
        return $this->getProperty('expires_in');
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->setProperty('refresh_token', $refreshToken);
    }

    public function getRefreshToken(): ?string
    {
        return $this->getProperty('refresh_token');
    }

    public function setScope(string $scope): void
    {
        $this->setProperty('scope', $scope);
    }

    public function getScope(): ?string
    {
        return $this->getProperty('scope');
    }
}

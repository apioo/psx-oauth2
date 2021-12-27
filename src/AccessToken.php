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

/**
 * AccessToken
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AccessToken
{
    private string $accessToken;
    private string $tokenType;
    private ?int $expiresIn;
    private ?string $refreshToken;
    private ?string $scope;
    private ?string $state;

    public function __construct(string $accessToken, string $tokenType, ?int $expiresIn = null, ?string $refreshToken = null, ?string $scope = null, ?string $state = null)
    {
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->expiresIn = $expiresIn;
        $this->refreshToken = $refreshToken;
        $this->scope = $scope;
        $this->state = $state;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['access_token'])) {
            throw new \InvalidArgumentException('Provided token response does not contain a "access_token" key');
        }

        if (!isset($data['token_type'])) {
            throw new \InvalidArgumentException('Provided token response does not contain a "token_type" key');
        }

        return new self(
            $data['access_token'],
            $data['token_type'],
            $data['expires_in'] ?? null,
            $data['refresh_token'] ?? null,
            $data['scope'] ?? null,
            $data['state'] ?? null,
        );
    }
}

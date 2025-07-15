<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\OAuth2\Grant;

use PSX\OAuth2\GrantInterface;

/**
 * Password
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Password implements GrantInterface
{
    private string $grantType = 'password';
    private string $username;
    private string $password;
    private ?string $scope;

    public function __construct(string $username, string $password, ?string $scope = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->scope = $scope;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function toArray(): array
    {
        $data = [
            'grant_type' => $this->grantType,
            'username'   => $this->username,
            'password'   => $this->password,
        ];

        if (!empty($this->scope)) {
            $data['scope'] = $this->scope;
        }

        return $data;
    }
}

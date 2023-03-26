<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * AuthorizationCode
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AuthorizationCode implements GrantInterface
{
    private string $grantType = 'authorization_code';
    private string $code;
    private ?string $redirectUri;
    private ?string $clientId;

    public function __construct(string $code, ?string $redirectUri = null, ?string $clientId = null)
    {
        $this->code = $code;
        $this->redirectUri = $redirectUri;
        $this->clientId = $clientId;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function toArray(): array
    {
        $data = [
            'grant_type' => $this->grantType,
            'code'       => $this->code,
        ];

        if (!empty($this->redirectUri)) {
            $data['redirect_uri'] = $this->redirectUri;
        }

        return $data;
    }
}

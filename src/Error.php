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

namespace PSX\Oauth2;

use PSX\Schema\Attribute\Key;

/**
 * Error
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Error
{
    private string $error;
    #[Key('error_description')]
    private ?string $errorDescription;
    #[Key('error_uri')]
    private ?string $errorUri;

    public function __construct(string $error, ?string $errorDescription = null, ?string $errorUri = null)
    {
        $this->error = $error;
        $this->errorDescription = $errorDescription;
        $this->errorUri = $errorUri;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorDescription(): ?string
    {
        return $this->errorDescription;
    }

    public function getErrorUri(): ?string
    {
        return $this->errorUri;
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['error'])) {
            throw new \InvalidArgumentException('Provided error message does not contain a required "error" key');
        }

        return new self(
            strtolower($data['error']),
            $data['error_description'] ?? null,
            $data['error_uri'] ?? null
        );
    }
}

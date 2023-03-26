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

namespace PSX\OAuth2\Tests;

use PHPUnit\Framework\TestCase;
use PSX\OAuth2\Authorization\Exception;
use PSX\OAuth2\AuthorizationAbstract;

/**
 * AuthorizationAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class AuthorizationAbstractTest extends TestCase
{
    /**
     * @dataProvider errorProvider
     */
    public function testNormalErrorException($error, $class)
    {
        try {
            AuthorizationAbstract::throwErrorException(array(
                'error' => $error,
                'error_description' => 'Foobar',
                'error_uri' => 'http://foo.bar'
            ));

            $this->fail('Must throw an exception');
        } catch (\PSX\OAuth2\Exception\ErrorExceptionAbstract $e) {
            $this->assertInstanceOf($class, $e);
            $this->assertEquals($error, $e->getType());
        }
    }

    public function errorProvider()
    {
        return [
            ['access_denied', \PSX\OAuth2\Exception\AccessDeniedException::class],
            ['invalid_client', \PSX\OAuth2\Exception\InvalidClientException::class],
            ['invalid_grant', \PSX\OAuth2\Exception\InvalidGrantException::class],
            ['invalid_request', \PSX\OAuth2\Exception\InvalidRequestException::class],
            ['invalid_scope', \PSX\OAuth2\Exception\InvalidScopeException::class],
            ['server_error', \PSX\OAuth2\Exception\ServerErrorException::class],
            ['temporarily_unavailable', \PSX\OAuth2\Exception\TemporarilyUnavailableException::class],
            ['unauthorized_client', \PSX\OAuth2\Exception\UnauthorizedClientException::class],
            ['unsupported_grant_type', \PSX\OAuth2\Exception\UnsupportedGrantTypeException::class],
            ['unsupported_response_type', \PSX\OAuth2\Exception\UnsupportedResponseTypeException::class],
        ];
    }

    public function testEmptyErrorException()
    {
        $this->expectException(\InvalidArgumentException::class);

        AuthorizationAbstract::throwErrorException([]);
    }

    public function testUnknownErrorException()
    {
        $this->expectException(\RuntimeException::class);

        AuthorizationAbstract::throwErrorException(array(
            'error' => 'foobar',
        ));
    }
}

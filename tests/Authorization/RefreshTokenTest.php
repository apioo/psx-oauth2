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

namespace PSX\OAuth2\Tests\Authorization;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PSX\Http\Client\Client;
use PSX\OAuth2\AccessToken;
use PSX\OAuth2\Authorization\AuthorizationCode;
use PSX\OAuth2\Grant;
use PSX\Uri\Url;

/**
 * RefreshTokenTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class RefreshTokenTest extends TestCase
{
    public const CLIENT_ID     = 's6BhdRkqt3';
    public const CLIENT_SECRET = 'gX1fBat3bV';

    public function testRequest()
    {
        $body = <<<BODY
{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "example_parameter":"example_value"
}
BODY;

        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);

        $container = [];
        $history = Middleware::history($container);

        $stack = HandlerStack::create($mock);
        $stack->push($history);

        $client = new Client(['handler' => $stack]);
        $oauth  = new AuthorizationCode($client, new Url('http://127.0.0.1/api'));
        $oauth->setClientPassword(self::CLIENT_ID, self::CLIENT_SECRET);

        $accessToken = new AccessToken('SplxlOBeZQQYbYS6WxSbIA', 'Bearer', null, 'SplxlOBeZQQYbYS6WxSbIA', 'foo,bar');

        $accessToken = $oauth->refreshToken($accessToken);

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('2YotnFZFEjr1zCsicMWpAA', $accessToken->getAccessToken());
        $this->assertEquals('example', $accessToken->getTokenType());
        $this->assertEquals(3600, $accessToken->getExpiresIn());

        $this->assertEquals(1, count($container));
        $transaction = array_shift($container);

        $this->assertEquals('POST', $transaction['request']->getMethod());
        $this->assertEquals('http://127.0.0.1/api', (string) $transaction['request']->getUri());
        $this->assertEquals(['Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW'], $transaction['request']->getHeader('Authorization'));
        $this->assertEquals(['application/x-www-form-urlencoded'], $transaction['request']->getHeader('Content-Type'));
        $this->assertEquals('grant_type=refresh_token&refresh_token=SplxlOBeZQQYbYS6WxSbIA&scope=foo%2Cbar', (string) $transaction['request']->getBody());
    }
}

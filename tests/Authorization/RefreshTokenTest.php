<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Oauth2\Tests\Authorization;

use PSX\Http;
use PSX\Http\Exception\TemporaryRedirectException;
use PSX\Http\Handler\Callback;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Authorization\AuthorizationCode;
use PSX\Uri\Url;

/**
 * RefreshTokenTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RefreshTokenTest extends \PHPUnit_Framework_TestCase
{
    const CLIENT_ID     = 's6BhdRkqt3';
    const CLIENT_SECRET = 'gX1fBat3bV';

    public function testRequest()
    {
        $httpClient = new Http\Client(new Callback(function (RequestInterface $request) {
            $this->assertEquals('/api', $request->getUri()->getPath());
            $this->assertEquals('Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW', $request->getHeader('Authorization'));
            $this->assertEquals('application/x-www-form-urlencoded', $request->getHeader('Content-Type'));
            $this->assertEquals('grant_type=refresh_token&refresh_token=SplxlOBeZQQYbYS6WxSbIA&scope=foo+bar', (string) $request->getBody());

            $response = <<<TEXT
HTTP/1.1 200 OK
Content-Type: application/json;charset=UTF-8
Cache-Control: no-store
Pragma: no-cache

{
  "access_token":"2YotnFZFEjr1zCsicMWpAA",
  "token_type":"example",
  "expires_in":3600,
  "example_parameter":"example_value"
}
TEXT;

            return ResponseParser::convert($response, ResponseParser::MODE_LOOSE)->toString();
        }));

        $oauth = new AuthorizationCode($httpClient, new Url('http://127.0.0.1/api'));
        $oauth->setClientPassword(self::CLIENT_ID, self::CLIENT_SECRET);

        $accessToken = new AccessToken();
        $accessToken->setAccessToken('SplxlOBeZQQYbYS6WxSbIA');
        $accessToken->setRefreshToken('SplxlOBeZQQYbYS6WxSbIA');
        $accessToken->setScope('foo bar');

        $accessToken = $oauth->refreshToken($accessToken);

        $this->assertInstanceOf(AccessToken::class, $accessToken);
        $this->assertEquals('2YotnFZFEjr1zCsicMWpAA', $accessToken->getAccessToken());
        $this->assertEquals('example', $accessToken->getTokenType());
        $this->assertEquals(3600, $accessToken->getExpiresIn());
    }
}

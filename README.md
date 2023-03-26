
# OAuth2

## About

This package provides an OAuth2 client implementation and it provides also common DTOs and
exceptions to build an OAuth2 server implementation

## Usage

```php
<?php

// at first for the authorization code flow you need to redirect your user to the OAuth2 server
AuthorizationCode::redirect('[auth_url]', '[client_id]', '[redirect_url]');

// if the customer returns you can obtain an access token
$client = new Http\Client();
$code = new AuthorizationCode($client, new Url('[token_url]'));
$code->setClientPassword('[client_id]', '[client_secret]', AuthorizationCode::AUTH_POST);

$accessToken = $code->getAccessToken('[redirect_url]');

// if we have an access token we can request the api using the access token
$header = [
	'Authorization' => TokenAbstract::factory($accessToken)->getHeader()
];

$request  = new GetRequest('[api_url]', $header);
$response = $http->request($request);

if ($response->getStatusCode() == 200) {
    // request worked
}
```

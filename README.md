# Spotify Provider for OAuth 2.0 Client

[![Latest Stable Version](https://img.shields.io/packagist/v/dnklbgn/oauth2-spotify)](https://github.com/dnklbgn/oauth2-spotify/releases)
[![License](https://img.shields.io/packagist/l/dnklbgn/oauth2-spotify)](LICENSE)
[![Build Status](https://github.com/dnklbgn/oauth2-spotify/actions/workflows/ci.yml/badge.svg)](https://github.com/dnklbgn/oauth2-spotify/actions/workflows/ci.yml)
[![Code Coverage](https://img.shields.io/codecov/c/gh/dnklbgn/oauth2-spotify)](https://app.codecov.io/gh/dnklbgn/oauth2-spotify)
[![Downloads](https://img.shields.io/packagist/dt/dnklbgn/oauth2-spotify)](https://packagist.org/packages/dnklbgn/oauth2-spotify)

This package provides Spotify OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Requirements

The following versions of PHP are supported:

* PHP 8.3

Please follow the [Spotify instructions](https://developer.spotify.com/documentation/web-api/concepts/apps) to create the app and obtain the required credentials.

## Installation

You can install this package using Composer:

```
composer require dnklbgn/oauth2-spotify
```

Or you can add the following to your `composer.json` file:

```json
{
    "require": {
        "dnklbgn/oauth2-spotify": "^1.0.0"
    }
}
```

## Usage

### Authorization Code Flow

```php
$provider = new \Dnklbgn\OAuth2\Client\Provider\Spotify([
    'clientId' => '{spotify-client-id}',
    'clientSecret' => '{spotify-client-secret}',
    'redirectUri' => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {
    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: ' . $authUrl);
    exit;
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    // State is invalid, possible CSRF attack in progress
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
} else {
    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    try {
        // Optional: Now you have a token you can look up a users profile data
        // We got an access token, let's now get the user's details
        /** @var \Dnklbgn\OAuth2\Client\Provider\SpotifyResourceOwner $user */
        $resourceOwnerDetails = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $resourceOwnerDetails->getDisplayName());
    } catch (\Exception $e) {
        // Failed to get user details
        exit('Something went wrong: ' . $e->getMessage());
    }

    // Use this to interact with an API on the users behalf
    var_dump($token->getToken());
    # string(217) "CAADAppfn3msBAI7tZBLWg..."

    // Use this to get a new access token if the old one expires
    var_dump($token->getRefreshToken());
    # string(217) "CAADAppfn3msBAI7tZBLWg..."

    // Unix timestamp at which the access token expires
    var_dump($token->getExpires());
    # int(1436825866)
}
```

### Managing Scopes

When creating your Spotify authorization URL, you can specify the state and scopes your application may authorize.

```php
$options = [
    'scope' => [
        \Dnklbgn\OAuth2\Client\Provider\SpotifyScope::USER_READ_PRIVATE->value,
        \Dnklbgn\OAuth2\Client\Provider\SpotifyScope::USER_READ_EMAIL->value,
    ],
];

$authUrl = $provider->getAuthorizationUrl($options);
```

If neither are defined, the provider will utilize internal defaults.
At the time of authoring this documentation, the [following scopes are available](https://developer.spotify.com/documentation/web-api/concepts/scopes).

* __Images__
  * ugc-image-upload (`SpotifyScope::UGS_IMAGE_UPLOAD`)
* __Spotify Connect__
  * user-read-playback-state (`SpotifyScope::USER_READ_PLAYBACK_STATE`)
  * user-modify-playback-state (`SpotifyScope::USER_MODIFY_PLAYBACK_STATE`)
  * user-read-currently-playing (`SpotifyScope::USER_READ_CURRENTLY_PLAYING`)
* __Playback__
  * app-remote-control (`SpotifyScope::APP_REMOTE_CONTROL`)
  * streaming (`SpotifyScope::STREAMING`)
* __Playlists__
  * playlist-read-private (`SpotifyScope::PLAYLIST_READ_PRIVATE`)
  * playlist-read-collaborative (`SpotifyScope::PLAYLIST_READ_COLLABORATIVE`)
  * playlist-modify-private (`SpotifyScope::PLAYLIST_MODIFY_PRIVATE`)
  * playlist-modify-public (`SpotifyScope::PLAYLIST_MODIFY_PUBLIC`)
* __Follow__
  * user-follow-modify (`SpotifyScope::USER_FOLLOW_MODIFY`)
  * user-follow-read (`SpotifyScope::USER_FOLLOW_READ`)
* __Listening History__
  * user-read-playback-position (`SpotifyScope::USER_READ_PLAYBACK_POSITION`)
  * user-top-read (`SpotifyScope::USER_TOP_READ`)
  * user-read-recently-played (`SpotifyScope::USER_READ_RECENTLY_PLAYED`)
* __Library__
  * user-library-modify (`SpotifyScope::USER_LIBRARY_MODIFY`)
  * user-library-read (`SpotifyScope::USER_LIBRARY_READ`)
* __Users__
  * user-read-email (`SpotifyScope::USER_READ_EMAIL`)
  * user-read-private (`SpotifyScope::USER_READ_PRIVATE`)
* __Open Access__
  * user-soa-link (`SpotifyScope::USER_SOA_LINK`)
  * user-soa-unlink (`SpotifyScope::USER_SOA_UNLINK`)
  * user-manage-entitlements (`SpotifyScope::USER_MANAGE_ENTITLEMENTS`)
  * user-manage-partner (`SpotifyScope::USER_MANAGE_PARTNER`)
  * user-create-partner (`SpotifyScope::USER_CREATE_PARTNER`)

### Retrieving Spotify user information

The `getResourceOwner()` method will return an instance of `\Dnklbgn\OAuth2\Client\Provider\SpotifyResourceOwner`,
which has some helpful getter methods to access basic authorized user details.

```php
$resourceOwnerDetails = $provider->getResourceOwner($token);

// The country of the user, as set in the user's account profile
$country = $resourceOwnerDetails->getCountry();
var_dump($country);
// string(2) "ID"

// The name displayed on the user's profile
$displayName = $resourceOwnerDetails->getDisplayName();
var_dump($displayName);
// string(6) "dnkbgn"

// The user's email address, as entered by the user when creating their account
$email = $resourceOwnerDetails->getEmail();
var_dump($email);
// string(19) "dnklbgn@example.com"

// The user's explicit content settings
$explicitContent = $resourceOwnerDetails->getExplicitContent();
var_dump($explicitContent);
// array(2) {
//   ["filter_enabled"]=>
//   bool(false)
//   ["filter_locked"]=>
//   bool(false)
//}

// Known external URLs for this user
$externalUrls = $resourceOwnerDetails->getExternalUrls();
var_dump($externalUrls);
// array(1) {
//   ["spotify"]=>
//   string(44) "https://open.spotify.com/user/abcd0123456789"
// }

// Information about the followers of the user
$followers = $resourceOwnerDetails->getFollowers();
var_dump($followers);
// array(2) {
//   ["href"]=>
//   NULL
//   ["total"]=>
//   int(3)
// }

// A link to the Web API endpoint for this user
$href = $resourceOwnerDetails->getHref();
var_dump($href);
// string(47) "https://api.spotify.com/v1/users/abcd0123456789"

// The Spotify user ID for the user
$id = $resourceOwnerDetails->getId();
var_dump($id);
// string(14) "abcd0123456789"

// The user's profile image
$images = $resourceOwnerDetails->getImages();
var_dump($images);
// array(1) {
//   [0]=>
//   array(3) {
//     ["url"]=>
//     string(64) "https://i.scdn.co/image/ab67616d00001e02ff9ca10b55ce82ae553c8228"
//     ["height"]=>
//     int(300)
//     ["width"]=>
//     int(300)
//   }
// }

// The user's Spotify subscription level: "premium", "free", etc.
$product = $resourceOwnerDetails->getProduct();
var_dump($product);
// string(7) "premium"

// The object type: "user"
$type = $resourceOwnerDetails->getType();
var_dump($type);
// string(4) "user"

// The Spotify URI for the user
$uri = $resourceOwnerDetails->getUri();
var_dump($uri);
// string(27) "spotify:user:abcd0123456789"
```

### Refreshing a Token

If your access token expires you can refresh them with the refresh token.

```php
if ($accessToken->hasExpired()) {
    $refreshedAccessToken = $provider->getAccessToken(
        new \League\OAuth2\Client\Grant\RefreshToken(),
        ['refresh_token' => $accessToken->getRefreshToken()],
    );
}
```

## Contributing

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](https://github.com/dnklbgn/oauth2-spotify/blob/master/CONTRIBUTING.md) for details.

## Credits

* [Nikolay Kuzmin](https://github.com/dnklbgn)

## License

The MIT License (MIT). Please see [License File](https://github.com/dnklbgn/oauth2-spotify/blob/master/LICENSE) for more information.

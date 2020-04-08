# Twitch Login

This is a simple PHP library that shows you how to handle authentication through Twitch by allowing access to your application, grabbing the access token, then grabbing the user information from there.

# Usage

To instantiate:

```
$Twitch = new Twitch($credentials);
```

`$credentials` is an array with the following:

```
[
  'CLIENT_ID'     => '',
  'CLIENT_SECRET' => '',
  'REDIRECT_URI'  => ''
]
```

To login echo the following on your page:

```
$Twitch->authUrl();
```

Then, when the code is returned, you can get user information by the following:

```
$Twitch->accessToken = $_GET['code'];
$userInfo = $Twitch->fetchUser('username');
```

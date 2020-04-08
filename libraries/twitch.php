<?php

/**
 * Handles authentication through Twitch.
 */
class Twitch {

  // Twitch credentials array
  private $credentials = [];

  // Access token that is set after token is retrieved
  public $accessToken = false;

  // Set twitch api endpoints
  private $endpoints = [
    'token' => 'https://id.twitch.tv/oauth2/authorize',
    'users' => 'https://api.twitch.tv/helix/users'
  ];

  // Default scopes
  private $scopes = [
    'user:read:email' => 1
  ];

  /**
   * Class constructor, set class variables and more.
   * @param array $credentials
   */
  public function __construct(
    array $credentials = []
  ) {

    // Configuration validation
    if (!isset($credentials['CLIENT_ID']))     throw new Exception('You must provide a client id.');
    if (!isset($credentials['CLIENT_SECRET'])) throw new Exception('You must provide a client secret.');

    // Override variables
    if (isset($credentials['TOKEN_URL'])) $this->tokenUrl = $credentials['TOKEN_URL'];
    if (isset($credentials['SCOPES']))    $this->scopes   = $credentials['SCOPES'];

    // Set credentials.
    $this->credentials = $credentials;
  }

  /**
   * Build an Authorization URL
   */
  public function authUrl() {

    // Builds the URL
    $url = $this->_build($this->endpoints['token'], [
      'response_type' => 'code',
      'client_id' => $this->credentials['CLIENT_ID'],
      'redirect_uri' => $this->credentials['REDIRECT_URI'],
      'scope' => $this->_stringifyScopes()
    ]);

    return $url;
  }

  /**
   * Fetches user data for a specific user from
   * Twitch API.
   * @param  string $login username to fetch
   * @return object response from api
   */
  public function fetchUser($login) {

    // Make sure there is a token
    $valid = $this->_validateToken();

    // If not, return false
    if (!$valid) return false;

    // Return the authorized request.
    return $this->_authorizedRequest($this->endpoints['users'], ['login' => $login]);
  }

  /**
   * Validates the token
   */
  private function _validateToken() {
    if (!$this->accessToken) return false;
    return true;
  }

  /**
   * Converts scopes array to string that can be
   * passed to the Twitch API.
   */
  private function _stringifyScopes() {
    $scopes= '';
    foreach ($this->scopes as $scope => $allow) if ($allow) $scopes .= $scope . '+';
    return substr($scopes, 0, -1);
  }

  /**
   * Builds a URL based on
   * $url Endpoint to the API
   * $parts URI Parts
   */
  private function _build(
    string $url,
    array $parts
  ) {
    $url .= '?';
    $url .= http_build_query($parts);
    return $url;
  }

  /**
   * A request to the Twitch API that also includes
   * the required headers for authentication.
   * @param  string
   * @param  array  $vars     [description]
   * @return [type]           [description]
   */
  private function _authorizedRequest(
    string $endpoint,
    array $vars = array(),
    string $methodRequest = 'GET'
  ) {

    // Initiate CURL
    $ch = curl_init();

    // Start setting up the link
    $link = $endpoint;

    // Set GET variables if this is a get request.
    if ($methodRequest == 'GET' && count($vars) >= 1) {
      $link = $link . '?' . http_build_query($vars);
    }

    // Set the URL
    curl_setopt($ch, CURLOPT_URL, $link);

    // If it is POST or PUT, set it up
    if ($methodRequest == 'POST' || $methodRequest == 'PUT') {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
    }

    // Set Twitch Required headers.
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/vnd.twitchtv.v3+json',
      'Client-ID: ' . $this->credentials['CLIENT_ID'],
      'Authorization: OAuth ' . $this->accessToken
    ]);

    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Get the response
    $response = curl_exec($ch);

    // Close CURL
    curl_close ($ch);

    // Decode the response
    $response = json_decode($response);

    // Return thre response from CURL.
    return $response;
  }
}

<?php

/**
 * This is an example of how to login with Twitch
 * via PHP.
 */

// Define the base path.
define('BASEPATH', __DIR__);

// Include the credentials.
$credentials = require BASEPATH . '/credentials.php';

// Include Twitch Library
require_once BASEPATH .'/libraries/twitch.php';

// Instantiate Twitch with Credentials
$Twitch = new Twitch($credentials);

?>

<h1>Twitch Login</h1>

<?php if (isset($_GET['code'])): ?>
  <?php $Twitch->accessToken = $_GET['code']; ?>
  <pre>
    <?php print_r($Twitch->fetchUser('IrateTV')); ?>
  </pre>
<?php else: ?>
  <a href="<?= $Twitch->authUrl(); ?>">Login with Twitch</a>
<?php endif; ?>

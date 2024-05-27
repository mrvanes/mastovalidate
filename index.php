<?php
session_start();
require 'vendor/autoload.php';
require 'config.php';
require 'db.php';

use Jumbojett\OpenIDConnectClient;


function short($sub, $link = "") {
  global $db;
  $c = "bcdfghjklmnpqrstvwBCDFGHJKLMNPQRSTVW0123456789";

  $query = "SELECT short, profile_link from users where sub = '$sub';";
  $result = $db->querySingle($query, true) ?? array();
  $short = $result['short'] ?? NULL;
  $profile_link = $result['profile_link'] ?? NULL;
  $link = $db->escapeString($link);

  if (!$short && $sub) {
    $success = False;
    while (!$success) {
      $short = "";
      for ($i=0; $i<8; $i++) {
        // $s = substr($sub, $i*8, 8);
        // $n = base_convert($s, 16, 10);
        $short .= substr($c, rand(0, strlen($c)), 1);
      }
      $query = "INSERT INTO users (sub, short, time, profile_link) VALUES ('$sub', '$short', strftime('%s', 'now'), '$link');";
      if ($db->query($query)) $success = True;
    }
  }

  if ($link) {
      $query = "UPDATE users SET profile_link='$link', time=strftime('%s', 'now') WHERE sub = '$sub';";
      if ($db->query($query)) $profile_link = $link;
  }

  return [$short, $profile_link];
}

function me($me) {
  global $db;
  $query = "SELECT short, profile_link from users where short = '$me';";
  $result = $db->querySingle($query, true) ?? array();
  $short = $result['short'] ?? NULL;
  $profile_link = $result['profile_link'] ?? NULL;
  return [$short, $profile_link];
}

$authenticated = $_SESSION['authenticated'] ?? False;
$base_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['SERVER_NAME'];

$oidc_op = $config['oidc_op'];
$client_id = $config['client_id'];
$client_secret = $config['client_secret'];
$redirect_url = "$base_url/";
$response_type = 'code';
$scopes = array($config['scope'] . ' persistent');
$claims = '';

$oidc = new OpenIDConnectClient($oidc_op, $client_id, $client_secret);
# For debug purposes on local dev
$oidc->setVerifyHost(false);
$oidc->setVerifyPeer(false);

$oidc->setRedirectURL($redirect_url);
$oidc->setResponseTypes(array($response_type));
#$oidc->setAllowImplicitFlow(true);
#$oidc->addAuthParam(array('response_mode' => 'form_post'));
if ($scopes) $oidc->addScope($scopes);
if ($claims) $oidc->addAuthParam(array('claims' => $claims));

$error = NULL;
$action = $_POST['action'] ?? NULL;
$state = $_REQUEST['state'] ?? NULL;
$sub = $_SESSION['sub'] ?? NULL;
$me = $_REQUEST['me'] ?? NULL;

$profile_link = $_POST['profile_link'] ?? $_SESSION['profile_link'] ?? NULL;
$_SESSION['profile_link'] = $profile_link;

if (($action || $state)) {
  error_log("Auth");
  if (!$authenticated) {
    try {
        $oidc->authenticate();
        $claims = $oidc->getVerifiedClaims();
        $userinfo = $oidc->requestUserInfo();
        $sub = $userinfo->sub ?? 'me';
        $_SESSION['sub'] = $sub;
        $_SESSION['authenticated'] = True;
        header("Location: " . $base_url . "/");
    } catch (Exception $e) {
        $error = htmlspecialchars(json_encode($e->getMessage(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
  }
  [$short, $profile_link] = short($sub, $profile_link);
} else {
  [$short, $profile_link] = short($sub);
}

if ($action) {
     header("Location: " . $_SERVER['HTTP_REFERER']);
}

if ($me) {
  [$short, $profile_link] = me($me);
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Validate</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
if ($error) {
  echo "<pre id=result>$error</pre>\n";
} else if ($short ) {
  $me_url = "$base_url/$short";
  echo "Copy this URL to your Mastodon profile page Extra Fields section<br>";
  echo "<a href=\"$me_url\" target=_blank>$me_url</a><br><br>\n";
}

require 'form.php';
?>
</body></html>

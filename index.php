<?php

use Jumbojett\OpenIDConnectClient;

session_start();

require 'vendor/autoload.php';
require 'config.php';
require 'db.php';
require 'validate.php';


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

if ($me) {
  [$short, $profile_link] = me($me);
} else {
  $short = $_SESSION['short'] ?? NULL;
}

if ($action || $state || $me) {
  if (!$authenticated) {
    error_log("Auth");
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

$_SESSION['profile_link'] = $profile_link;
$_SESSION['short'] = $short;

require 'template.php';

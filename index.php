<?php

use Jumbojett\OpenIDConnectClient;

session_start();

require 'vendor/autoload.php';
require 'config.php';
require 'db.php';
require 'validate.php';

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
$me = $_REQUEST['me'] ?? $_SESSION['me'] ?? NULL;
$short = NULL;
$old = NULL;

if ($state == 'logout') {
  session_destroy();
  header("Location: " . $base_url . "/");
  exit();
}

// Get profile_link from me, form or session
if ($me) {
  [$profile_link, $time] = profile_link($me);
  $_SESSION['me'] = $me;
} else {
  $profile_link = $_POST['profile_link'] ?? $_SESSION['profile_link'] ?? NULL;
}

$_SESSION['profile_link'] = $profile_link;


// @error_log("me A: $me");
// @error_log("sub A: $sub");
// @error_log("profie_link A: $profile_link");

if ($action || $state || $me) {
  if (!$sub) {
    try {
        $oidc->authenticate();
        $claims = $oidc->getVerifiedClaims();
        $userinfo = $oidc->requestUserInfo();
        $sub = $userinfo->sub ?? NULL;
        $_SESSION['sub'] = $sub;
        header("Location: " . $base_url . "/");
        exit();
    } catch (Exception $e) {
        $error = htmlspecialchars(json_encode($e->getMessage(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
  }
}

// @error_log("me B: $me");
// @error_log("sub B: $sub");
// @error_log("profie_link B: $profile_link");

if ($sub) {
  $short = short($sub);

  if ($me && $short != $me) {
    $profile_link = NULL;
    unset($_SESSION['me']);
  }

  [$profile_link, $timestamp] = profile_link($short, $profile_link);
  unset($_SESSION['profile_link']);

  $date = date('d-m-Y H:i:s', $timestamp);
  $dt = new DateTime($date, new DateTimeZone('GMT'));
  $dt->setTimeZone(new DateTimeZone('CEST'));
  $time = $dt->format('d-m-Y H:i:s T');
  $old = (time() - $timestamp > $config['refresh']);
}


// @error_log("me C: $me");
// @error_log("sub C: $sub");
// @error_log("profie_link C: $profile_link");


require 'template.php';

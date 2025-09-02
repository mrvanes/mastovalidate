<?php
require 'config.php';
require 'db.php';
require 'validate.php';

session_start();

// $authenticated = $_SESSION['authenticated'] ?? False;
$request_uri = $_SERVER['REQUEST_URI'];
$me = substr($request_uri, 1);

[$profile_link, $timestamp] = profile_link($me);

// $query = "SELECT time, profile_link from users where short = '$me'";
// $result = $db->querySingle($query, true);
// if (!$result) exit();

// $timestamp = $result['time'];
// $profile_link = $result['profile_link'];

if (!$profile_link) {
  http_response_code(404);
  exit();
}

$date = date('d-m-Y H:i:s', $timestamp);
$dt = new DateTime($date, new DateTimeZone('GMT'));
$dt->setTimeZone(new DateTimeZone('CEST'));
$time = $dt->format('d-m-Y H:i:s T');
$old = (time() - $timestamp > $config['refresh']);

?>
<!DOCTYPE html>
<html>
<head>
  <title>InAcademia Validation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="style/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
  <script src="scripts/copy.js" defer></script>
</head>
<body>

<body>

<div class='header'></div>
  <div class='content'>

  <?php
  echo "Last validation: $time<br>\n";
  if (!$old) {
    echo "link: $profile_link<br>\n";
  } else {
    unset($_SESSION['authenticated']);
    echo "Please <a href=\"/?me=$me\">revalidate!</a><br>\n";
  }
  ?>
  </div>

  <div class='sidebar'>
    <img src="/images/geant_logo.svg"><br>
    <img src="/images/eu_flag.svg"><br>
    <p>
      <b>How does InAcademia work?</b><br>
      <a href="https://inacademia.org">Click here</a> to find out more
    </p>
  </div>

</body>
</html>

<?php
require 'config.php';
require 'db.php';

session_start();
$authenticated = $_SESSION['authenticated'] ?? False;
$request_uri = $_SERVER['REQUEST_URI'];
$me = substr($request_uri, 1);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Validate</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
$query = "SELECT time, profile_link from users where short = '$me'";

$result = $db->querySingle($query, true);
if (!$result) exit();

$timestamp = $result['time'];
$profile_link = $result['profile_link'];

$date = date('d-m-Y H:i:s', $timestamp);
$dt = new DateTime($date, new DateTimeZone('GMT'));
$dt->setTimeZone(new DateTimeZone('CEST'));
$time = $dt->format('d-m-Y H:i:s T');

echo "Last validation: $time<br>\n";

$old = (time() - $timestamp > $config['refresh']);

if (!$old) {
    echo "link: $profile_link<br>\n";
} else {
    if ($authenticated) {
        echo "Please revalidate!<br>\n";
        require 'form.php';
    } else {
        echo "Please <a href=\"/?me=$me\" target=_blank>revalidate!</a><br>\n";
    }
}
?>
</body>
</html>

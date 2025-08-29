<?php
function short($sub, $link = "") {
  global $db;
  $c = "bcdfghjklmnpqrstvwBCDFGHJKLMNPQRSTVW0123456789";

  $query = "SELECT short, profile_link from users where sub = '$sub';";
  $result = $db->querySingle($query, true) ?? array();
  $short = $result['short'] ?? NULL;
  $profile_link = $result['profile_link'] ?? NULL;

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

  $link = $db->escapeString($link);
  $query = "UPDATE users SET profile_link='$link', time=strftime('%s', 'now') WHERE sub = '$sub';";
  if ($db->query($query)) $profile_link = $link;

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

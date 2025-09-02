<?php
function short($sub) {
  global $db;
  $c = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

  $query = "SELECT short from users where sub = '$sub';";
  $result = $db->querySingle($query, true) ?? array();
  $short = $result['short'] ?? NULL;

  if (!$short) {
    $success = False;
    while (!$success) {
      $short = "";
      for ($i=0; $i<8; $i++) {
        // $s = substr($sub, $i*8, 8);
        // $n = base_convert($s, 16, 10);
        $short .= substr($c, rand(0, strlen($c)), 1);
      }
      // $query = "INSERT INTO users (sub, short, time, profile_link) VALUES ('$sub', '$short', strftime('%s', 'now'), '$link');";
      $query = "INSERT INTO users (sub, short) VALUES ('$sub', '$short');";
      if ($db->query($query)) $success = True;
    }
  }

  return $short;
}

function profile_link($short, $link = NULL) {
  global $db;
  // $query = "SELECT short, profile_link from users where short = '$short';";
  $query = "SELECT profile_link, time from users where short = '$short';";
  $result = $db->querySingle($query, true) ?? array();
  // $short = $result['short'] ?? NULL;
  $profile_link = $result['profile_link'] ?? NULL;
  $time = $result['time'] ?? NULL;

  if ($link) {
    $link = $db->escapeString($link);
    $time = time();
    $query = "UPDATE users SET profile_link='$link', time=$time WHERE short = '$short';";
    if ($db->query($query)) $profile_link = $link;
  }

  return [$profile_link, (int)$time];
}

<?php
if ($_GET['reset']) {
  unlink('db/mastodon.db');
}
$db = new SQLite3('db/mastodon.db');
if (!$db->querySingle("SELECT name FROM sqlite_master WHERE type = 'table'")) {
  $db->query('CREATE TABLE "users" ( "sub" TEXT NOT NULL, "short" TEXT NOT NULL UNIQUE, "time" INTEGER, "profile_link" TEXT, PRIMARY KEY("sub") )');
}

<?php
$db = new SQLite3('mastodon.db');
if (!$db->querySingle("SELECT name FROM sqlite_master WHERE type = 'table'")) {
  $db->query('CREATE TABLE "users" ( "sub" TEXT NOT NULL, "short" TEXT NOT NULL UNIQUE, "time" INTEGER NOT NULL, "profile_link" TEXT NOT NULL, PRIMARY KEY("sub") )');
}

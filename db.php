<?php
require 'config.php';

class DBConnectionManager {
  static public function connect() {
    $link = mysql_connect('localhost', DB_USER, DB_PASS);
    if (!$link) {
      die('Not connected: ' . mysql_error());
    }
    mysql_select_db(DB_NAME);
  }
}
?>
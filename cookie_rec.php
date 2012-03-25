<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $expire = time() + 28 * 24 * 3600; // 4 weeks
  $path = '/TeXBBS/';
  setcookie('writer', $_POST['writer'], $expire, $path);
  setcookie('color', $_POST['color'], $expire, $path);
  setcookie('twitterID', $_POST['twitterID'], $expire, $path);
  setcookie('mixiID', $_POST['mixiID'], $expire, $path);
  setcookie('facebookID', $_POST['facebookID'], $expire, $path);
}
?>

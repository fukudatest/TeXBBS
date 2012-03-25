<?php
session_start();
/* set cookie values -- start */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $expire = time() + 28 * 24 * 3600; // 4 weeks
  $path = '/TeXBBS/';
  setcookie('writer', $_POST['writer'], $expire, $path);
  setcookie('color', $_POST['color'], $expire, $path);
  setcookie('twitterID', $_POST['twitterID'], $expire, $path);
  setcookie('mixiID', $_POST['mixiID'], $expire, $path);
  setcookie('facebookID', $_POST['facebookID'], $expire, $path);
}
/* set cookie values -- end */
/* get cookie values -- start */
if (isset($_COOKIE['writer'])) {
  $writer = $_COOKIE['writer'];
} else {
  $writer = '';
}
if (isset($_COOKIE['mixiID'])) {
  $mixi_id = $_COOKIE['mixiID'];
} else {
  $mixi_id = '';
}
if (isset($_COOKIE['twitterID'])) {
  $twitter_id = $_COOKIE['twitterID'];
} else {
  $twitter_id = '';
}
if (isset($_COOKIE['facebookID'])) {
  $facebook_id = $_COOKIE['facebookID'];
} else {
  $facebook_id = '';
}
if (isset($_COOKIE['color'])) {
  $color = $_COOKIE['color'];
} else {
  $color = '';
}
/* get cookie values -- end */

require_once('constants.php');
require_once('connection.php');

$title = '';
$input_type = InputType::None;
if (isset($_GET[GetParam::TopicId]) && ctype_digit($_GET[GetParam::TopicId])) {
  $topic_id = $_GET[GetParam::TopicId];
  if (isset($_GET[GetParam::PostId]) && ctype_digit($_GET[GetParam::PostId]) && $_GET[GetParam::PostId] != 0) {
    $pattern = PageType::Comment;
    $input_type =InputType::CommentReply;
    $post_id = $_GET[GetParam::PostId];
  } else {
    $pattern = PageType::Topic;
    $input_type = InputType::TopicReply;
    $post_id = 0;
  }
  $sqlForm = "SELECT title FROM BBSposts "
            ."WHERE topic_id = {$topic_id} AND id = {$post_id}";
  $rowsForm = mysql_query($sqlForm);
  if (!$rowsForm) {
    echo mysql_error();
  } else if ($rowForm = mysql_fetch_array($rowsForm)) {
    $title = $rowForm['title'] . ' - ';
  }
  if (isset($_GET[GetParam::Mode]) && $_GET[GetParam::Mode] == ModeType::Edit) {
    $title = '[編集] '.$title;
    $input_type = InputType::Edit;
  }
} else if (isset($_GET[GetParam::SummeryNumber]) && ctype_digit($_GET[GetParam::SummeryNumber]) && $_GET[GetParam::SummeryNumber] != 0) {
  $pattern = PageType::Summery;
  $title = 'トピック一覧 - ';
} else if (isset($_GET[GetParam::PageNumber]) && ctype_digit($_GET[GetParam::PageNumber]) && $_GET[GetParam::PageNumber] != 0) {
  $pattern = PageType::Board;
} else {
  $pattern = PageType::Unknown;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<?php include('meta.php'); ?>
  <title><?php echo $title.ConstText::BBStitle; ?></title>
<?php
  include('headerScript.php');
  include('css.php');
?>
</head>
<body>
  <h1><a href="<?php echo $_SERVER['PHP_SELF'];?>"><?php echo ConstText::BBStitle; ?></a></h1>
<?php
  include('headerPanel.php');
  require('inputArea.php');
  include('centerAdSense.php');
?>
  <hr />
<?php require('list.php'); ?>
</body>
</html>

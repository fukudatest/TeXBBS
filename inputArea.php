<?php

$error_message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (md5($_SESSION['taskId']) == $_POST['taskId']) {
    $title = $_POST['title'];
    if (empty($title) || (mb_strlen($title) > 30)) {
      $error_message .= "タイトルを30文字以内で入力してください。<br />";
    } else {
      $title = htmlspecialchars($title);
      $title = mysql_real_escape_string($title);
    }
 
    $writer = $_POST['writer'];
    unset($_SESSION['taskId']);
    if (empty($writer) || (mb_strlen($writer) > 20)) {
      $error_message .= "タイトルを20文字以内で入力してください。<br />";
    } else {
      $writer = htmlspecialchars($writer);
      $writer = mysql_real_escape_string($writer);
    }
    $message = $_POST['message'];
    if (empty($message) || (mb_strlen($message) > 20000)) {
      $error_message .= "メッセージを20000文字以内で入力してください。<br />";
    } else {
      //$message = htmlspecialchars($message);
      $message = str_replace('<', '&lt;', $message);
      $message = str_replace('>', '&gt;', $message);
      $message = mysql_real_escape_string($message);
    }
    //$message = nl2br($message);
    $twitter_id = htmlspecialchars($_POST['twitterID']);
    $twitter_id = mysql_real_escape_string($twitter_id);
    $mixi_id = $_POST['mixiID'];
    if (preg_match('/^[1-9][0-9]{0,9}$/', $mixi_id) == 1) {
      $mixi_id = htmlspecialchars($mixi_id);
    } else {
      $error_message .= "mixiIDは10桁以内の整数値で入力してください。<br />";
    }
    $facebook_id = htmlspecialchars($_POST['facebookID']);
    $facebook_id = mysql_real_escape_string($facebook_id);
    //$url = htmlspecialchars($_POST['url']);
    $color = htmlspecialchars($_POST['color']);
    $password = $_POST['password'];
    if (mb_strlen($password, "utf-8") <= 20 &&
        preg_match('/[a-zA-Z0-9]*/', $password)) {
      $password = htmlspecialchars($password);
      $password = mysql_real_escape_string($password);
      $password = sha1($password.ConstText::PasswordSalt);
    } else {
      $error_message .= 'パスワードは半角英数20文字で入力してください。<br />';
    }

    $post_pattern = PostType::Topic;
    if (isset($_POST['topic_id']) && ctype_digit($_POST['topic_id']) && !empty($_POST['topic_id'])) {
      $topic_id = $_POST['topic_id'];
      if (isset($_POST['post_id']) && ctype_digit($_POST['post_id'])) {
        $post_pattern = PostType::Edit;
        $post_id = $_POST['post_id'];
      } else {
        $post_pattern = PostType::Reply;
      }
    }

    switch ($post_pattern) {
      case PostType::Edit:
        $result = mysql_query("SELECT password FROM BBSposts WHERE topic_id = ".$topic_id." AND id = ".$post_id);
        if ($result && ($temp = mysql_fetch_array($result))) {
          if (!($temp['password'] == $password)) {
            $error_message =  "パスワードが違います。<br />";
            break;
          }
        } else {
          echo mysql_error();
          echo "その投稿は存在しません。";
          break;
        } 

        $sqlPost = "UPDATE BBSposts SET writer = '{$writer}', title = '{$title}', "
                  ."title = '{$title}', message = '{$message}', "
                  ."twitter_id = '{$twitter_id}', mixi_id = {$mixi_id}, facebook_id = '{$facebook_id}', "
                  ."color = '{$color}', modified = now() "
                  ."WHERE topic_id = {$topic_id} and id = {$post_id} and password = '{$password}'";
        if (!mysql_query($sqlPost)) {
          echo mysql_error();
          $error_mesage .= "更新に失敗しました。<br />";
          break;
        }
        $sqlPost = "UPDATE BBStopics SET updated = now() WHERE id = {$topic_id}";
        if (!mysql_query($sqlPost)) {
          echo mysql_error();
          echo "トピック時刻更新に失敗しました。";
          break;
        }
        break;

      case PostType::Reply:
        $result = mysql_query("SELECT max(id) max_id FROM BBSposts WHERE topic_id = ".$topic_id);
        if (!$result) {
          echo mysql_error();
          echo "そのトピックは存在しません。";
          break;
        }

        $post_id = 0;
        if ($temp = mysql_fetch_array($result)) {
          $post_id = $temp['max_id'] + 1;
        } else {
          echo "そのトピックは存在しません。";
          break;
        }

        $sqlPost = "INSERT INTO BBSposts(id, topic_id, writer, title, message, "
                  ."twitter_id, mixi_id, facebook_id, url, color, password, "
                  ."created) ";
        $sqlPost .= "VALUES({$post_id}, {$topic_id}, '{$writer}', '{$title}', '{$message}', "
                  ."'{$twitter_id}', '{$mixi_id}', '{$facebook_id}', '{$url}', '{$color}', "
                  ."'{$password}', now())";
        if (!mysql_query($sqlPost)) {
          echo mysql_error();
          $error_message = "投稿に失敗しました。<br />";
          break;
        }
        $sqlPost = "UPDATE BBStopics SET updated = now() WHERE id = {$topic_id}";
        if (!mysql_query($sqlPost)) {
          echo mysql_error();
          echo "トピック時刻更新に失敗しました。";
          break;
        }
        break;

      case PostType::Topic:
        if (!mysql_query("INSERT INTO BBStopics(updated) VALUES(now())")) {
          die(mysql_error());
        }

        $result = mysql_query("SELECT max(id) max_id FROM BBStopics");
        $topic_id = 0;
        if (!$result) {
          echo mysql_error();
          break;
        } else {
          global $topic_id, $result;
          if ($temp = mysql_fetch_array($result)) {
            global $topic_id;
            $topic_id = $temp['max_id'];
          }
        }
        $sqlPost = "INSERT INTO BBSposts(id, topic_id, writer, title, message, "
                  ."twitter_id, mixi_id, facebook_id, url, color, password, "
                  ."created) ";
        $sqlPost .= "VALUES(0, {$topic_id}, '{$writer}', '{$title}', '{$message}', "
                  ."'{$twitter_id}', '{$mixi_id}', '{$facebook_id}', '{$url}', '{$color}', "
                  ."'{$password}', now())";
        if (!mysql_query($sqlPost)) {
          echo mysql_error();
          mysql_query("DELETE FROM BBStopics WHERE id = {$topic_id}");
          echo mysql_error();
          break;
        }
        break;
    }
  } else {
    $error_message = "セッションが切れたため、投稿できませんでした。<br />";
  }
} 
if ($error_message != '') {
  echo $error_message;
}
switch ($input_type) {
  case InputType::None:
    echo outputForm('index.php', '', $writer, '', $color,
                   $mixi_id, $tweeter_id, $facebook_id);
    break;

  case InputType::Edit:
    $topic_id = $_GET[GetParam::TopicId];
    if (isset($_GET[GetParam::PostId])) {
      $post_id = $_GET[GetParam::PostId];
    } else {
      $post_id = 0;
    }
      
    $sqlForm = "SELECT writer, title, message, twitter_id, mixi_id, facebook_id, color FROM BBSposts "
              ."WHERE topic_id = {$topic_id} AND id = {$post_id}";
    $rowsForm = mysql_query($sqlForm);
    if (!$rowsForm) {
      echo mysql_error();
      break;
    }
    
    if ($rowForm = mysql_fetch_array($rowsForm)) {
      echo outputForm($_SESSION['PHP_SELF']."?".GetParam::TopicId."=".$topic_id."&".GetParam::PostId."=".$post_id, 
                     $rowForm['title'], $rowForm['writer'], $rowForm['message'], $rowForm['color'],
                     $rowForm['mixi_id'], $rowForm['twitter_id'], $rowForm['facebook_id'], $topic_id, $post_id);
    }
    break;

  case InputType::CommentReply:
    $topic_id = $_GET[GetParam::TopicId];
    $post_id = $_GET[GetParam::PostId];
    $preMessage = "\n\n---- No. {$topic_id} Comment {$post_id} ----\n";
      
    $sqlForm = "SELECT title, message FROM BBSposts "
              ."WHERE topic_id = {$topic_id} AND id = {$post_id}";
    $rowsForm = mysql_query($sqlForm);
    if (!$rowsForm) {
      echo mysql_error();
      break;
    }
    if ($rowForm = mysql_fetch_array($rowsForm)) {
      $title = "Re: ".$rowForm['title'];
      $title = mb_substr($title, 0, 30, "utf-8");
      echo outputForm($_SESSION['PHP_SELF']."?".GetParam::TopicId."=".$_GET['topic_id'], $title, $writer, $preMessage.$rowForm['message'], $color,
                     $mixi_id, $twitter_id, $facebook_id, $_GET['topic_id']);
    }
    break;
    
  case InputType::TopicReply:
    $topic_id = $_GET[GetParam::TopicId];
    $preMessage = "\n\n------------ No. {$topic_id} ------------\n";
    $post_id = 0;
      
    $sqlForm = "SELECT title, message FROM BBSposts "
              ."WHERE topic_id = {$topic_id} AND id = {$post_id}";
    $rowsForm = mysql_query($sqlForm);
    if (!$rowsForm) {
      echo mysql_error();
      break;
    }
    if ($rowForm = mysql_fetch_array($rowsForm)) {
      $title = "Re: ".$rowForm['title'];
      $title = mb_substr($title, 0, 30, "utf-8");
      echo outputForm($_SESSION['PHP_SELF']."?".GetParam::TopicId."=".$_GET['topic_id'], $title, $writer, $preMessage.$rowForm['message'], $color,
                     $mixi_id, $twitter_id, $facebook_id, $_GET['topic_id']);
    }
    break;
}

?>

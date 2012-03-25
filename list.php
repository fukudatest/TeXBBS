<?php

switch ($pattern) {
  case PageType::Board: case PageType::Unknown:
    $n = 0;
    $page_number = 1;
    if ($pattern == PageType::Board) {
      $page_number = $_GET['page_number'];
      $n = ($page_number - 1) * ConstParam::BoardTopicCount;
    }

    $sqlHead = "SELECT count(BBStopics.id) id_count FROM BBStopics";
    $rowsHead = mysql_query($sqlHead);
    if (!$rowsHead) {
      die(mysql_error());
    }
    if ($rowHead = mysql_fetch_array($rowsHead)) {
      if ($rowHead['id_count'] >= $page_number) {
        $pager = createPager($page_number, $rowHead['id_count'], ConstParam::BoardTopicCount, ConstParam::Pager, GetParam::PageNumber);
      } else {
        $pager = "<p></p>";
      }
    }
    echo $pager;
    echo "<hr />";

    $sqlHead = "SELECT postsA.topic_id, postsA.writer,"
              ."postsA.title, postsA.message, postsA.twitter_id,"
              ."postsA.mixi_id, postsA.facebook_id, postsA.url,"
              ."postsA.color, postsA.created, postsA.modified, postsC.id_count ";
    $sqlHead .= "FROM (SELECT id, updated FROM BBStopics "
               ."ORDER BY updated DESC LIMIT ".(string)$n.", ".(string)ConstParam::BoardTopicCount.") "
               ."BBStopics "
               ."JOIN BBSposts postsA ON postsA.topic_id = BBStopics.id AND postsA.id = 0 "
               ."JOIN (SELECT postsB.topic_id topic_id, count(postsB.id) id_count FROM BBSposts postsB GROUP BY postsB.topic_id) postsC "
               ."  ON postsC.topic_id = BBStopics.id "
               ."ORDER BY BBStopics.updated DESC";

    $rowsHead = mysql_query($sqlHead, $myCon);
    if (!$rowsHead) {
      die(mysql_error());
    }
    while($rowHead = mysql_fetch_array($rowsHead)) {
      echo "<div class=\"topic\">";
      echo createTopicHtml($rowHead['topic_id'], $rowHead['title'], $rowHead['writer'], 
                         $rowHead['twitter_id'], $rowHead['mixi_id'], $rowHead['facebook_id'], 
                         $rowHead['color'], nl2br($rowHead['message']), $rowHead['created'], $rowHead['modified']);

      $sqlPost = "SELECT BBSposts.id post_id, BBSposts.writer,"
                ."BBSposts.title, BBSposts.message, BBSposts.twitter_id,"
                ."BBSposts.mixi_id, BBSposts.facebook_id, BBSposts.url,"
                ."BBSposts.color, BBSposts.created, BBSposts.modified ";
      $sqlPost .= "FROM BBSposts WHERE BBSposts.topic_id = {$rowHead['topic_id']} "
                 ."AND BBSposts.id != 0 "
                 ."ORDER BY BBSposts.id DESC LIMIT 0, ".(string)ConstParam::BoardTopicCommentCount;
      $rowsPost = mysql_query($sqlPost, $myCon);
      if (!$rowsPost) {
        die(mysql_error());
      }
      $html = "";
      while($rowPost = mysql_fetch_array($rowsPost)) {
        $html = createCommentHtml($rowHead['topic_id'], $rowPost['post_id'], $rowPost['title'], $rowPost['writer'], 
                               $rowPost['twitter_id'], $rowPost['mixi_id'], $rowPost['facebook_id'],
                               $rowPost['color'], nl2br($rowPost['message']), $rowPost['created'], $rowPost['modified'])
              .$html;
      }
      if ($rowHead['id_count'] > ConstParam::BoardTopicCommentCount) {
        $html = "<div style=\"text-align:center;\">(コメント一部省略)</div>".$html;
      }
      echo $html;
      echo "  </div>";
    }
    echo "  </div>";

    echo "<hr />";
    echo $pager;

    break;

  case PageType::Comment:
    $topic_id = (int)$_GET[GetParam::TopicId];
    /* create pager - start - */
    $sqlHead = "SELECT count(BBSposts.id) id_count FROM BBSposts WHERE BBSposts.topic_id = {$topic_id}";
    $rowsHead = mysql_query($sqlHead);
    if (!$rowsHead) {
      die(mysql_error());
    }
    $sqlPost = "SELECT count(BBSposts.id) id_count FROM BBSposts WHERE BBSposts.topic_id = {$topic_id} AND id BETWEEN 1 AND {$_GET[GetParam::PostId]}";
    $rowsPost = mysql_query($sqlPost);
    if (!$rowsPost) {
      die(mysql_error());
    }
    if ($rowHead = mysql_fetch_array($rowsHead)) {
      if ($rowPost = mysql_fetch_array($rowsPost)) {
        $page_number = ceil($rowPost['id_count'] / ConstParam::BoardTopicCount);

        if ($rowHead['id_count'] >= $page_number) {
          echo createPager($page_number, $rowHead['id_count'], ConstParam::TopicCommentCount, ConstParam::Pager,
                         GetParam::TopicId."={$topic_id}&".GetParam::PageNumber);
        } else {
          echo "<p></p>";
        }
      }
    }
    /* create pager - end -*/
    echo $pager;
    echo "<hr />";

    $sqlHead = "SELECT BBSposts.topic_id, BBSposts.id post_id, BBSposts.writer,"
              ."BBSposts.title, BBSposts.message, BBSposts.twitter_id,"
              ."BBSposts.mixi_id, BBSposts.facebook_id, BBSposts.url,"
              ."BBSposts.color, BBSposts.created, BBSposts.modified ";
    $sqlHead .= "FROM BBStopics "
               ."JOIN BBSposts ON BBSposts.topic_id = BBStopics.id AND BBSposts.id = 0 "
               ."WHERE BBStopics.id = {$topic_id}";
    $rowsHead = mysql_query($sqlHead, $myCon);
    if (!$rowsHead) {
      die(mysql_error());
    }
    if ($rowHead = mysql_fetch_array($rowsHead)) {
      echo "  <div class=\"topic\">";
      echo createTopicHtml($topic_id, $rowHead['title'], $rowHead['writer'], 
                         $rowHead['twitter_id'], $rowHead['mixi_id'], $rowHead['facebook_id'], 
                         $rowHead['color'], nl2br($rowHead['message']), $rowHead['created'], $rowHead['modified']);

      $sqlPost = "SELECT BBSposts.id post_id, BBSposts.writer,"
                ."BBSposts.title, BBSposts.message, BBSposts.twitter_id,"
                ."BBSposts.mixi_id, BBSposts.facebook_id, BBSposts.url,"
                ."BBSposts.color, BBSposts.created, BBSposts.modified ";
      $sqlPost .= "FROM BBSposts WHERE BBSposts.topic_id = {$topic_id} "
                 ."AND BBSposts.id != 0 "
                 ."ORDER BY BBSposts.id DESC LIMIT 0, ".(string)ConstParam::TopicCommentCount;
      $rowsPost = mysql_query($sqlPost, $myCon);
      if (!$rowsPost) {
        die(mysql_error());
      }
      $post_id = 1;
      $html = "";
      while($rowPost = mysql_fetch_array($rowsPost)) {
        $post_id = $rowPost['post_id'];
        $html = createCommentHtml($rowHead['topic_id'], $rowPost['post_id'], $rowPost['title'], $rowPost['writer'], 
                               $rowPost['twitter_id'], $rowPost['mixi_id'], $rowPost['facebook_id'],
                               $rowPost['color'], nl2br($rowPost['message']), $rowPost['created'], $rowPost['modified'])
               .$html;
      }
      echo $html;
      echo "  </div>\n";
      echo "  </div>\n";
    } else {
      echo "<p>指定されたトピックがありません。</p>";
    }

    echo "<hr />";
    $sqlHead = "SELECT count(BBSposts.id) id_count FROM BBSposts WHERE BBSposts.topic_id = {$topic_id}";
    $rowsHead = mysql_query($sqlHead);
    if (!$rowsHead) {
      die(mysql_error());
    }
    $sqlPost = "SELECT count(BBSposts.id) id_count FROM BBSposts WHERE BBSposts.topic_id = {$topic_id} AND id BETWEEN 1 AND {$_GET[GetParam::PostId]}";
    $rowsPost = mysql_query($sqlPost);
    if (!$rowsPost) {
      die(mysql_error());
    }

    if ($rowHead = mysql_fetch_array($rowsHead)) {
      if ($rowPost = mysql_fetch_array($rowsPost)) {
        $page_number = ceil($rowPost['id_count'] / ConstParam::BoardTopicCount);

        if ($rowHead['id_count'] >= $page_number) {
          echo createPager($page_number, $rowHead['id_count'], ConstParam::TopicCommentCount, ConstParam::Pager,
                         GetParam::TopicId."={$topic_id}&".GetParam::PageNumber);
        } else {
          echo "<p></p>";
        }
      }
    }
    break;

  case PageType::Topic:
    $topic_id = (int)$_GET['topic_id'];
    if (isset($_GET[GetParam::PageNumber]) && ctype_digit($_GET[GetParam::PageNumber]) && $_GET[GetParam::PageNumber] != 0) {
      $page_number = $_GET[GetParam::PageNumber];
    } else {
      $page_number = 1;
    }

    $sqlPost = "SELECT count(BBSposts.id) id_count FROM BBSposts WHERE BBSposts.topic_id = {$topic_id}";
    $rowsPost = mysql_query($sqlPost);
    if (!$rowsPost) {
      die(mysql_error());
    }
    if ($rowPost = mysql_fetch_array($rowsPost)) {
      if ($rowPost['id_count'] >= $page_number) {
        $pager = createPager($page_number, $rowPost['id_count'], ConstParam::TopicCommentCount, ConstParam::Pager, 
                        GetParam::TopicId."={$topic_id}&".GetParam::PageNumber);
      } else {
        $pager = "<p></p>";
      }
    }
    echo $pager;
    echo "<hr />";

    $sqlHead = "SELECT BBSposts.topic_id, BBSposts.id post_id, BBSposts.writer,"
              ."BBSposts.title, BBSposts.message, BBSposts.twitter_id,"
              ."BBSposts.mixi_id, BBSposts.facebook_id, BBSposts.url,"
              ."BBSposts.color, BBSposts.modified ";
    $sqlHead .= "FROM BBStopics "
               ."JOIN BBSposts ON BBSposts.topic_id = BBStopics.id AND BBSposts.id = 0 "
               ."WHERE BBStopics.id = {$topic_id}";

    $rowsHead = mysql_query($sqlHead, $myCon);
    if (!$rowsHead) {
      die(mysql_error());
    }
    $n = ($page_number - 1) * ConstParam::TopicCommentCount;
    if ($rowHead = mysql_fetch_array($rowsHead)) {
      $htmlTitle = $rowHead['title'];
      echo "  <div class=\"topic\">";
      echo createTopicHtml($rowHead['topic_id'], $rowHead['title'], $rowHead['writer'], 
                         $rowHead['twitter_id'], $rowHead['mixi_id'], $rowHead['facebook_id'], 
                         $rowHead['color'], nl2br($rowHead['message']), $rowHead['created'], $rowHead['modified']);

      $sqlPost = "SELECT BBSposts.id post_id, BBSposts.writer,"
                ."BBSposts.title, BBSposts.message, BBSposts.twitter_id,"
                ."BBSposts.mixi_id, BBSposts.facebook_id, BBSposts.url,"
                ."BBSposts.color, BBSposts.modified ";
      $sqlPost .= "FROM BBSposts WHERE BBSposts.topic_id = {$topic_id} "
                 ."AND BBSposts.id != 0 "
                 ."ORDER BY BBSposts.id DESC LIMIT {$n}, ".(string)ConstParam::TopicCommentCount;
      $rowsPost = mysql_query($sqlPost, $myCon);
      if (!$rowsPost) {
        die(mysql_error());
      }
      $post_id = 1;
      $html = "";
      while($rowPost = mysql_fetch_array($rowsPost)) {
        $post_id = $rowPost['post_id'];
        $html = createCommentHtml($topic_id, $rowPost['post_id'], $rowPost['title'], $rowPost['writer'], 
                               $rowPost['twitter_id'], $rowPost['mixi_id'], $rowPost['facebook_id'],
                               $rowPost['color'], nl2br($rowPost['message']), $rowPost['created'], $rowPost['modified'])
              .$html;
      }
      if ($post_id != 1) {
        $html = "(一部省略)<br />".$html;
      }
      echo $html;
      echo "  </div>\n";
      echo "  </div>\n";
    } else {
      echo "<p>指定されたトピックがありません。</p>";
    }

    echo "<hr />";
    echo $pager;
    break;

  case PageType::Summery:
    $html = '';
    $page_number = $_GET['summery_number'];
    
    /* create pager - start - */
    $sqlHead = "SELECT count(BBStopics.id) id_count FROM BBStopics";
    $rowsHead = mysql_query($sqlHead);
    if (!$rowsHead) {
      die(mysql_error());
    }
    if ($rowHead = mysql_fetch_array($rowsHead)) {
      if ($rowHead['id_count'] >= $page_number) {
        $pager = createPager($page_number, $rowHead['id_count'], ConstParam::SummeryCount, ConstParam::Pager, GetParam::SummeryNumber);
      } else {
        $pager = "<p>トップページに戻ってやり直してください。</p>";
      }
    }
    /* create pager - end - */
    echo $pager;
    $html .= $pager."\n<hr />";
    echo "<hr />";

    $n = ($page_number - 1) * ConstParam::SummeryCount;
    $sqlHead = "SELECT BBStopics.id, A.title title, count(B.id) - 1 posts_count, "
              ."BBStopics.updated ";
    $sqlHead .= "FROM (SELECT id, updated FROM BBStopics "
               ."ORDER BY updated DESC LIMIT ".(string)$n.", ".(string)ConstParam::SummeryCount.") "
               ."BBStopics "
               ."JOIN BBSposts A ON A.topic_id = BBStopics.id AND A.id = 0 "
               ."JOIN BBSposts B ON B.topic_id = BBStopics.id ";
    $sqlHead .= "GROUP BY BBStopics.id ORDER BY BBStopics.updated DESC";
    $rowsHead = mysql_query($sqlHead, $myCon);
    if (!$rowsHead) {
      die(mysql_error());
    }

    echo '<div id="topicList">';
    $html .= '<div id="topicList">';
    while ($rowHead = mysql_fetch_array($rowsHead)) {
      echo $rowHead['id']." <a href=\"{$_SERVER['PHP_SELF']}?topic_id={$rowHead['id']}\">".$rowHead['title']."(".$rowHead['posts_count'].")</a><br />\n";
      $html .= $rowHead['id']." <a href=\"{$_SERVER['PHP_SELF']}?topic_id={$rowHead['id']}\">".$rowHead['title']."(".$rowHead['posts_count'].")</a><br />\n";
    }
    echo '</div>';
    $html .= "</div>\n<hr />\n".$pager;

    echo "<hr />";
    echo $pager;
    break;
}

?>

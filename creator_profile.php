<?php
  require_once('constants.php');
?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo ConstText::BBStitle; ?></title>
<?php
  include('meta.php');
  include('headerScript.php');
  include('css.php');
?>
</head>
<body>
<h1><?php echo ConstText::BBStitle; ?></h1>
<?php
  include('headerPanel.php');
?>


<?php
  $title = "作者プロフィール";
  $message = "数式の表示できる日本の掲示板がないな〜と思って作ってみました。（後から調べたら・・・ありました (・△・；)）<br /><br />";
  $message .= "数式を表示するのに、今までは Σ&lt;sub&gt;k=1&lt;/sub&gt;&lt;sup&gt;10&lt;/sup&gt;(Σ<sub>k=1</sub><sup>n</sup>)なんてやってました。";
  $message .= "でも、頑張って書いてもだいぶ見にくい式になってしまいます。また、$\TeX$ ( $\LaTeX$ ) を簡単に試せる環境があれば とも思っていました。";
  $message .= " $$ e^{\pi i} = -1 $$ ";
  $message .= "仕事では PHP なんかまったく使わないのですが、Apache, MySQL, PHP で作りました。<br /><br />";
  $message .= "やることはまだまだ残っています。javascript を外部ファイルにすること、改行が重なった場合の制御、検索機能追加、ヘルプページ作成、コメント投稿後の編集・削除機能追加などなど。できるだけ早くやっていきます。";
  echo createIntroductionHtml($title, 'escamilloIII', '16198161', '', 'black', $message);
?>


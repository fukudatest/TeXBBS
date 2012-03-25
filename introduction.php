<?php
  require_once('constants.php');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>説明 - <?php echo ConstText::BBStitle; ?></title>
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
  $title = "説明";
  $message = "この掲示板では $\TeX$ を利用して数式を書くことができます。$ \\$ $ 〜 $ \\$ $ で数式を囲むとインラインで、$ \\$\\$ $ 〜 $ \\$\\$ $, $\backslash [$ 〜 $\backslash ] $ などで囲むと別の行として数式を表示できます。";
  $message .= "<br /><br />ただし、$\TeX$ のすべての機能が使えるわけではありません。";
  $message .= "<br /><br />ちなみに作者は、FireFox を使用しています。";
  echo createIntroductionHtml($title, '', '', '', 'black', $message);
  
  $title = '$\TeX$ とは';
  $message = "世界中で広く使われている、高性能でフリーのオープンソース組版ソフトです。";
  $message .= "スタンフォード大学のクヌース教授が自分自身の著書をコンピュータを使って作成するために作ったのが始まりで、今や大学の数学では $\TeX$ が使えないと話になりません。";
  $message .= "Microsoft Office の Word でも数式はだいぶ使えるようになりましたが、数式をヘビーに使う人たちにとってはまだまだで、長い積分評価を書けばメモリを食いまくってしまいます。";
  $message .= "<br /><br />その $\TeX$ に早く慣れるためにも、この掲示板では $\TeX$ を使えるようにしました。といっても一部ですが。";
  $message .= "<br /><br />$\TeX$ に似た奴に $\LaTeX$, $ \LaTeX 2 \epsilon$ などがあります。これらは $\TeX$ から派生して作られたもので、$\TeX$ をより便利にしたものです。";
  $message .= "ちなみにアメリカ数学会は AMS-LaTeX を作りました。まとめて $\TeX$ と呼ばれます。";
  $message .= "パッケージも豊富にあり、<strong>化学</strong>の構造式から、<strong>将棋</strong>の絵を書いたり<strong>楽譜</strong>を書いたりするものまでさまざまです。";
  $message .= "<hr />リンク: <a href=\"http://www.latex-project.org/\" target=\"_blank\" title=\"LaTeX – A document preparation system\">$\LaTeX$ のページ</a> ";
  $message .= ', <a target="_blank" href="http://www.amazon.co.jp/mn/search/?_encoding=UTF8&x=0&tag=mezurashinews-22&linkCode=ur2&y=0&camp=247&creative=7399&field-keywords=latex&url=search-alias%3Daps">$\TeX$ の参考書</a><img src="http://www.assoc-amazon.jp/e/ir?t=mezurashinews-22&l=ur2&o=9" width="1" height="1" border="0" alt="" style="border:none !important; margin:0px !important;" />';
  echo createIntroductionHtml($title, '', '', '', 'black', $message);
  
  $title = '$\TeX$ の書き方';
  $message = "現在準備中です。ひとまず <a href=\"http://www12.plala.or.jp/ksp/tex/symbol/mathsymbols.html\" target=\"_blank\">物理のかぎしっぽ</a> を参考にしてください。";
  echo createIntroductionHtml($title, '', '', '', 'black', $message);
?>

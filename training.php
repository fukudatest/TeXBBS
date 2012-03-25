<?php
  require_once('constants.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>TeX 練習場 - <?php echo ConstText::BBStitle; ?></title>
<?php
  include('meta.php');
  include('headerScript.php');
  include('css.php');
?>
<!--
<script type="text/javascript" src="mathjax/MathJax.js"></script>
<script type="text/javascript" src="mathjax/MathJax.js?config=TeX-AMS_HTML-full"></script>
-->
</head>
<body>
<h1><?php echo ConstText::BBStitle; ?> - TeX 練習場</h1>
<?php
  include('headerPanel.php');
?>
<script type="text/javascript">
var preMessage = "";
// Use a closure to hide the local variables from the
// global namespace
(function () {
  window.Sanitize = function(text) {
    var result = text;
    result = result.replace(/\</g,'&lt;');
    result = result.replace(/\>/g,'&gt;');
    result = result.replace(/\r\n|\r|\n/g, '<br />');
    return result;
  }
window.UpdateMath = function () {
var text = document.getElementById('InputTextarea').value;
text = Sanitize(text);
document.getElementById("TrainMathOutput").innerHTML = text;
MathJax.Hub.Queue(["Typeset",MathJax.Hub,"TrainMathOutput"]);
}
  window.changeRealTime = function (isRealTime) {
    if (isRealTime) {
      document.getElementById('previewButton').disabled = true;
      messageOnKeyUp();
    } else {
      document.getElementById('previewButton').disabled = false;
    }
  }
  window.getRealTime = function () {
    return document.getElementById('isRealTime').checked;
  }
})();
  function messageOnKeyUp() {
    if(getRealTime()) {
      var message = encodeURIComponent(document.getElementById('InputTextarea').value);
      if ((message != preMessage) || changed) {
        preMessage = message;
        UpdateMath();
      }
    }
  }
</script>

<?php
  $title = "$\TeX$ 練習場";
  $message = "ここでは、TeX の練習ができます。<br />";
  $message .= "リアルタイムプレビューにチェックを入れると、テキストを編集する度に、随時結果が右側に表示されます。ただしその場合、処理が増えるためレスポンスが遅くなります。<br />";
  $message .= "表示されている数式を右クリックして Show source を選択すると、 $\TeX$ での数式の書き方が表示されます。<br />";
  $message .= "javascript を使用しています。";
  echo createIntroductionHtml($title, '', '', '', 'black', $message);
?>

<table id="EditTable">
<tbody>
<tr><td colspan="2">
<div id="EditPanel">
  <form action="">
    <input type="button" id="previewButton" disabled="disabled" value="プレビュー" onclick="UpdateMath();" />
    <input type="checkbox" id="isRealTime" checked="checked" name="isRealTime" value="1" onclick="changeRealTime(this.checked);" />リアルタイムプレビュー<br />
  </form>
</div>
</td></tr>
<tr><td>
<div id="TrainMathInput">
<textarea cols="50" rows="30" id="InputTextarea" name="InputTextarea" onkeyup="messageOnKeyUp();">
$$f(x) = x^2 + ax$$
\[
 \left\{  
         \begin{array}{c c l}  
           z & = & rx'+sy' \\  
           y & = & ts'+uy'  
         \end{array}  
 \right.
\]
$x \in \mathbb{N}$ なので $x \div 3 \neq 2$.

</textarea>
</div>
</td>
<td>
<div id="TrainMathOutput">
$$f(x) = x^2 + ax$$ <br />
\[
 \left\{  
         \begin{array}{c c l}  
           z & = & rx'+sy' \\  
           y & = & ts'+uy'  
         \end{array}  
 \right.
\]
<br />
$x \in \mathbb{N}$ なので $x \div 3 \neq 2$.
</div>
</td></tr>
</body>
</table>

</body>
</body>
</html>


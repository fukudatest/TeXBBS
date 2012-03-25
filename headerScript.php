<script type="text/x-mathjax-config">
  MathJax.Hub.Config({
    extensions: ["tex2jax.js"],
    jax: ["input/TeX","output/HTML-CSS"],
    tex2jax: {inlineMath: [["$","$"],["\\(","\\)"]],
              displayMath: [ ['$$','$$'], ["\\[","\\]"]]}
  });
</script>
<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js">
</script>
<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_full">
</script>
<?php
  if ($pattern == PageType::Unknown || $pattern == PageType::Comment ||
      $pattern == PageType::Board || $pattern == PageType::Topic) {
    echo '  <script type="text/javascript" src="inputScript.js"></script>';
  }
?>

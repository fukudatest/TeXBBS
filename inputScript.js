var preMessage = "";
var changed = false;
var ChangeCount = 0;

function getXHR() {
  var req;
  try {
    req = new XMLHttpRequest();
  } catch (e) {
    try {
      req = new ActiveXObject('Msxml2.XMLHTTP');
    } catch (e) {
      req = new ActiveXObject('Microsoft.XMLHTTP');
    }
  }
  return req;
}

function isZealNumber(text) {
  return text.toString().match(/^[1-9][0-9]*$/);
}
function isAlphaNumberString(text) {
  return text.toString().match(/[a-zA-Z0-9]*/);
}

  (function () {
    window.UpdateMath = function () {
      var text = document.MathPad.message.value;
      document.getElementById("MathOutput").innerHTML = Sanitize(text);
      MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathOutput"]);
    }
    window.Sanitize = function(text) {
      var result = text;
      result = result.replace(/\</g,'&lt;');
      result = result.replace(/\>/g,'&gt;');
      //result = result.replace(/(\\$\\$[^\\$\\r\\n]*[(\\r\\n)\\r\\n])/g, ' $$ ');
      //result = result.replace(/([(\\r\\n)\\r|\\n][^\\$\\r\\n]*\\$\\$)/g, ' $$ ');
      //result = result.replace(/[(\\r\\n)\\r\\n]/g, '<br />');
      result = result.replace(/\r\n/g, '<br />');
      result = result.replace(/\r/g, '<br />');
      result = result.replace(/\n/g, '<br />');
      return result;
    }
    window.getRealTime = function () {
      return document.getElementById('isRealTime').checked;
    }
  })();

  function asyncSend() {
    var req = getXHR();
    req.onreadystatechange = function() {
      var result = document.getElementById('PostStyle');
      if (req.readyState == 4) {
        if (req.status == 200) {
          result.innerHTML = req.responseText;
          MathJax.Hub.Queue(["Typeset",MathJax.Hub,"PostStyle"]);
          changed = true;
        } else {
          result.innerHTML = "Server Error!";
        }
      } else {
        result.innerHTML = "☆ただいま通信中☆";
      }
    }
    req.open('POST', 'verify.php', true);
    req.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');
    var param = 'title=' + encodeURIComponent(document.getElementById('title').value);
    param += '&writer=' + encodeURIComponent(document.getElementById('writer').value);
    param += '&message=' + encodeURIComponent(document.getElementById('message').value);
    param += '&twitterID=' + encodeURIComponent(document.getElementById('twitterID').value);
    param += '&mixiID=' + encodeURIComponent(document.getElementById('mixiID').value);
    param += '&facebookID=' + encodeURIComponent(document.getElementById('facebookID').value);
    param += '&color=' + encodeURIComponent(document.getElementById('color').value);
    param += '&topic_id=' + encodeURIComponent(document.getElementById('topic_id').value);
    param += '&post_id=' + encodeURIComponent(document.getElementById('post_id').value);
    req.send(param);
  }
    function verify(inStrict) {
      var errMsg = "";
      if (inStrict && (document.MathPad.title.value.replace(/\s|　/g, '') == "")) {
        errMsg += "タイトルを30文字以内で入力してください。<br />";
      } else if (document.MathPad.title.value.length > 30) {
        errMsg += "タイトルは30文字以内で入力してください。<br />";
      }
      if (inStrict && (document.MathPad.writer.value.replace(/\s|　/g, '') == "")) {
        errMsg += "名前を20文字以内で入力してください。<br />";
      } else if (document.MathPad.writer.value.length > 20) {
        errMsg += "名前は20文字以内で入力してください。<br />";
      }
      if (inStrict && (document.MathPad.message.value.replace(/\s|　/g, '') == "")) {
        errMsg += "メッセージを20,000文字以内で入力してください。<br />";
      } else if (document.MathPad.message.value.length > 20000) {
        errMsg += "メッセージは20,000文字以内で入力してください。<br />";
      }
      if (document.MathPad.twitterID.value.length > 30)
        errMsg += "twitterID は30文字以内で入力してください。<br />";
      if ((document.MathPad.mixiID.value.length != 0) &&
          ((document.MathPad.mixiID.value.length > 10) ||
            !isZealNumber(document.MathPad.mixiID.value.toString())))
        errMsg += "mixiID は整数10桁以内で入力してください。<br />";
      if (document.MathPad.facebookID.value.length > 20)
        errMsg += "facebookID は20文字以内で入力してください。<br />";
      if (document.MathPad.password.value.length > 20 ||
          !isAlphaNumberString(document.MathPad.password.value)) {
        errMsg += \"パスワードは半角英数20文字以内で入力してください。<br />\";
      }
      document.getElementById("Error").innerHTML = errMsg;
      if (errMsg.length == 0) {
        document.getElementById('Error').style.display = 'none';
        return true;
      } else {
        document.getElementById('Error').style.display = 'block';
        return false;
      }
    }
    function controlConfirmBox(value){
      document.getElementById('overlay').style.display = value;
      document.getElementById('PostStyle').style.display = value;
    }

  function updateMessage() {
    ChangeCount = Math.max(ChangeCount, 1) - 1;
    if (ChangeCount == 0) {
      var message = encodeURIComponent(document.MathPad.message.value);
      if ((message != preMessage) || changed) {
        changed = false;
        preMessage = message;
        UpdateMath();
      }
    }
  }
  function messageOnKeyUp() {
    if(getRealTime()) {
      ChangeCount++;
      setTimeout( function() {updateMessage();}, 200);
    }
  } 
  function changeRealTime(isRealTime) {
    if (isRealTime) {
      document.getElementById('MathOutput').style.display = 'block';
      document.getElementById('previewButton').disabled = true;
      UpdateMath();
    } else {
      document.getElementById('previewButton').disabled = false;
    }
    ChangeCount = 0;
  } 
    function previewButtonClick() {
      document.getElementById('MathOutput').style.display = 'block';
      verify(false);
      UpdateMath();
    }
    function formOnConfirm() {
      if (!verify(true)) return false;
      if (document.getElementById('overlay').style.display != 'block') {
        window.scroll(0,0);
        controlConfirmBox('block');
        asyncSend();
        return false;
      } else {
        return true;
      }
    }
    function cancelOnClick() {
      controlConfirmBox('none');
    }
    function formOnPost() {
      document.getElementById('taskId').value = document.getElementById('preTaskId').value;
      document.MathPad.submit();
      return false;
    }

window.onload = function() {
  document.getElementById('MathPad').onsubmit = function() {return formOnConfirm();};
  document.getElementById('previewButton').onclick = function() {previewButtonClick();};
  document.getElementById('isRealTime').onclick = function() {changeRealTime(document.getElementById('isRealTime').checked);};
  document.getElementById('message').onkeyup = function() {messageOnKeyUp();};
}

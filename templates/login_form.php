<html>
<head>
<title>
<? pv($DOC_TITLE) ?>
</title>
<!--
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css">
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel=stylesheet type=text/css>
<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" /> -->
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel=stylesheet type=text/css>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/button-min.js"></script>
<style type="text/css">
body {
	margin:0;
	padding:0;
	/* TODO: Replace with club logo - So users are not confused */
	background-image: url(images/squash-scotstoun.jpg);
	background-repeat: no-repeat;
	background-position: -650px -670px;
}
#overlayHld {
	height:100%;
	background-color:rgba(255,255,255,0.2);
}
#contentHld {
	padding-top:250px;
}
#loginWindow {
	width:400px;
	background-color:rgba(0,0,0,0.8);
	color:#FFF;
	text-align: left;
	padding:10px;
}
a {
	color:#FFF;
}
</style>
</head>
<?php
	$rememberLast = $_COOKIE["remembercookie"];
	$checked = "";
	if( isset($rememberLast) ){
		$checked = "checked";
	}
	
//	print "This remember: $rememberLast";
//	print "This is the username: $username, $pass";
	
?>

<body OnLoad="document.entryform.username.focus();">
<div id="overlayHld">
  <div id="contentHld" align="center">
    <form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php" autocomplete="off">
      <div id="loginWindow">
        <h1 align="center">
          <? pv($DOC_TITLE) ?>
        </h1>
        <div style="height:15px;"> <!-- Spacer --> </div>
        <? if (! empty($errormsg)) { ?>
        <div align="center">
          <? pv($errormsg) ?>
        </div>
        <? } ?>
        Username:
        <input id="username" type="text" name="username" value="<?=$_COOKIE["username"]; ?>" style="width:100%;">
        <br />
        Password:
        <input type="password" name="password" value="<?=$_COOKIE["pass"]; ?>" style="width:100%;">
        <br />
        <div style="text-align:center;"> Remember Me:
          <input type="checkbox" name="remember" <?=$checked?> >
        </div>
        <div style="height:15px;"> <!-- Spacer --> </div>
        <div style="text-align:center;">
          <input type="submit" value="Login" id="submitbutton1" style="font-size:16px;">
          <input type="button" value="Cancel" onClick="javascript: history.go(-1)" id="cancelbutton1" style="font-size:16px;">
        </div>
        <div style="height:15px;"> <!-- Spacer --> </div>
        <span style="float:right;">Powered by<br />
        <img src="images/01.gif" width="88" height="15"></span>
        <? if(!isSiteAutoLogin()){ ?>
        <div class="normal" style="margin-top: 15px"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/forgot_password.php">Forgot my password</a> </div>
        <? } ?>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">

    YAHOO.example.init = function () {

        // "contentready" event handler for the "submitbuttonsfrommarkup" <fieldset>
        
        YAHOO.util.Event.onContentReady("formtable", function () {

            // Create a Button using an existing <input> element as a data source
            var oSubmitButton1 = new YAHOO.widget.Button("submitbutton1", { value: "submitbutton1value" });
            var oCancelButton1 = new YAHOO.widget.Button("cancelbutton1", { value: "cancelbutton1value" });

        });

    } ();

    </script>
</body>
</html>

<?
  /*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
?>
<html>
<head>
<title><? pv($DOC_TITLE) ?></title>

<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css"> 
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel=stylesheet type=text/css>

<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" />
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/button-min.js"></script>

</head>



<?
	$rememberLast = $_COOKIE["remembercookie"];
	$checked = "";
	if( isset($rememberLast) ){
		$checked = "checked";
	}
	
//	print "This remember: $rememberLast";
//	print "This is the username: $username, $pass";
	
?>


<body style="margin-left: 1.5em"  OnLoad="document.entryform.username.focus();" class="yui-skin-sam">


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


 <form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php" autocomplete="off">

<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
 <tr class="borderow loginth">
    <td>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>
        <? if (! empty($errormsg)) { ?>
                <div class=warning align=center ><? pv($errormsg) ?></div>
        <? } ?>

       
        
        <table>
        <tr>
                <td class="label">Username:</td>
                <td><input id="username" type="text" name="username" size=20 value="<?=$_COOKIE["username"]; ?>">  </td>
        </tr>
        <tr>
                <td class=label>Password:</td>
                <td><input type="password" name="password" size=20 value="<?=$_COOKIE["pass"]; ?>"> </td>
        </tr>
       
        <tr>
                <td></td>
                <td align="center"><input type="submit" value="Login" id="submitbutton1">
                        <input type="button" value="Cancel" onClick="javascript: history.go(-1)" id="cancelbutton1">
                       <? if(!isSiteAutoLogin()){ ?>
                        <div class="normal" style="margin-top: 15px">
                        	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/forgot_password.php">Forgot my password</a>
                        </div>
                        <? } ?>
                </td>
          </tr>
           <tr>
            	<td></td>
                <td class=normal align="center">Remember Me: <input type="checkbox" name="remember" <?=$checked?> ></td>
        </tr>
        </table>
      
</td>
</tr>
</table>

  </form>
  
</body>
</html>
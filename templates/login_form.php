<?php
  /*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>
<html>
<head>
<title>ClubPro Login</title>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.css" rel=stylesheet type=text/css>
</head>

<?
	$rememberLast = $_COOKIE["remembercookie"];
	
	if( isset($rememberLast) ){
		$checked = "checked";
	}
	
//	print "This remember: $rememberLast";
//	print "This is the username: $username, $pass";
	
?>


<body bgcolor="#ffffff" link="#0000ff" vlink="#000099" alink="#ff0000">

<p>
<table cellspacing="0" cellpadding="20" width="400" >


 <tr>
    <td class=loginth><font class=whiteh1><div align="center">Login Screen</div></font></td>
 </tr>

 <tr>
    <td class=generictable>
        <? if (! empty($errormsg)) { ?>
                <div class=warning align=center><? pv($errormsg) ?></div>
        <? } ?>

        <form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">
        <table>
        <tr>
                <td class=label>Username:</td>
                <td><input type="text" name="username" size=20 value="<?=$_COOKIE["username"]; ?>">  </td>
        </tr>
        <tr>
                <td class=label>Password:</td>
                <td><input type="password" name="password" size=20 value="<?=$_COOKIE["pass"]; ?>"> </td>
        </tr>
       
        <tr>
                <td></td>
                <td align="center"><input type="submit" value="Login">
                        <input type="button" value="Cancel" onClick="javascript: history.go(-1)">
                       <? if(!isSiteAutoLogin()){ ?>
                        <p class=normal>
                        	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/forgot_password.php">Forgot my password</a>
                        </p>
                        <? } ?>
                </td>
          </tr>
           <tr>
            	<td></td>
                <td class=normal align="center">Remember Me: <input type="checkbox" name="remember" <?=$checked?> ></td>
        </tr>
        </table>
        </form>
</td>
</tr>
</table>

</body>
</html>
<?php
  /*
 * $LastChangedRevision: 319 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2007-09-03 17:14:31 -0500 (Mon, 03 Sep 2007) $

*/
?>
<html>
<head>
<title>ClubPro Login</title>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel=stylesheet type=text/css>
</head>


<body bgcolor=#ffffff link=#0000ff vlink=#000099 alink=#ff0000>


<p>
<table cellspacing="0" cellpadding="20" width="400"  class="generictable">


 <tr class="borderow">
    <td class="loginth">
    	<span class="whiteh1">
    		<div align="center">Please Select User</div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>


        <form name="entryform" method="post" action="<?=$_SESSION["wantsurl"]?>">
        <table>
        <tr>
                <td class="label">User:</td>
                <td>

					<select name="userid">
					
								<? 
									//variable set in login.php
									while($user = mysql_fetch_array($usersResult)){ ?>
                        			<option value="<?=$user['userid']?>" ><? print $user['firstname']?></option>
                        	   <? } ?>
                    </select>

			    </td>
        </tr>
        <tr>
        	<td class="normal"> This Id is assciated to multiple players, please select who you would like to login as.</td>
        	<td><input type="hidden" name="frompickform" value=""></td>
        </tr>
        <tr>
        	<td>
        		<input type="submit" value="Submit">
        	</td>
        </tr>
        
        </table>
        </form>
</td>
</tr>
</table>

</body>
</html>
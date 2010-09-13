<?php


/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $
 */

include ("../application.php");
$DOC_TITLE = "Player Administration";


/* form has been submitted, try to create the new role */
//Set the http variables
$searchname = $_REQUEST["searchname"];

if (isset ($searchname)) {

	$errormsg = validate_form($searchname);
	$backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/player_admin.php";

	if ($errormsg) {
		include ($_SESSION["CFG"]["templatedir"]."/header.php");
		include ($_SESSION["CFG"]["includedir"]."/errorpage.php");
		include ($_SESSION["CFG"]["templatedir"]."/footer.php");
		die;
	} else {
		$playerResults = get_admin_player_search($searchname);
		include ($_SESSION["CFG"]["templatedir"]."/header.php");
		print_players($searchname, $playerResults, $DOC_TITLE, $ME);
		include ($_SESSION["CFG"]["templatedir"]."/footer.php");
		die;
	}
}

include ($_SESSION["CFG"]["templatedir"]."/header.php");
include ($_SESSION["CFG"]["templatedir"]."/player_admin_form.php");
include ($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($searchname) {
	/* validate the signup form, and return the error messages in a string.  if
	 * the string is empty, then there are no errors */

	$errors = new Object;
	$msg = "";

	if (strpos($searchname, "'") != false) {
		
		$msg .= "No speical characters please. ";

	}

	return $msg;
}

function print_players($searchname, $playerresult,$DOC_TITLE, $ME) {


	if (mysql_num_rows($playerresult) < 1) {

		$errormsg = "Sorry, no results found.";
		include ($_SESSION["CFG"]["includedir"]."/errorpage.php");

	} else {
?>
       <table cellspacing="0" cellpadding="5" width="710" align="center" border="0">
       <tr>
        <td colspan="7">

          <table cellspacing="0" cellpadding="20" width="400" >
                <tr>
                    <td class="clubid<?=get_clubid()?>th"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
                </tr>
                <tr>
                    <td class="generictable">
                    <form name="entryform" method="post" action="<?=$ME?>">
                     <table width="400">
                         <tr>
                                <td class="label">Member Name:</td>
                                <td><input type="text" name="searchname" value="<? pv($searchname) ?>" size=25>
                                </td>
                        </tr>
                         <tr>
                             <td colspan="2"><p class="normal">
                             Search for the first or last name of a member. *Note partial string are supported.
                             <i> i.e. Smi for Smith or Pet for Peter</i>
                             </p>
                             </td>
                         </tr>
                         <tr>
                             <td></td>
                             <td><input type="submit" name="submit" value="Search"></td>
                         </tr>
                     </table>
                       </form>
                   </td>
                   </tr>
          </table>




           <br>
           <br>
           <hr>
           <br>
       </td>
       </tr>


        <?

		mysql_data_seek($playerresult, 0);
		$num_fields = mysql_num_fields($playerresult);
		$num_rows = mysql_num_rows($playerresult);
?>

                       <tr class=loginth>
                           <td height="25"><font class="whitenorm"><div align="center">First Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Last Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Email</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Work Phone</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Home Phone</div></font></td>
                           <td></td>
                           <td></td>
                       </tr>

                <?


		$rownum = mysql_num_rows($playerresult);
		while ($row = mysql_fetch_array($playerresult)) {

			$rc = (($rownum / 2 - intval($rownum / 2)) > .1) ? "C0C0C0" : "DBDDDD";
			?>
			
			<tr bgcolor="<?=$rc?>" class="normal"><form name="playerform<?=$rownum?>" method="post">

			<td><div align="center"><?=$row[firstname]?></div></td>
			<td><div align="center"><?=$row[lastname]?></div></td>
			<td><div align="center"><a href="mailto:<?=$row[email]?>"><?=$row[email]?></a></div></td>
			<td><div align="center"><?=$row[workphone]?></div></td>
			<td><div align="center"><?=$row[homephone]?></div></td>
			<td><div align="center"><a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/change_settings.php')">Edit</a></div></td>
			<td><div align="center"><a href="javascript:submitFormWithAction('playerform<?=$rownum?>','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_delete.php')">Delete</a></div></td>
			<input type="hidden" name="userid" value="<?=$row[userid]?>">
			<input type="hidden" name="searchname" value="<?=$searchname?>">
			<input type="hidden" name="DOC_TITLE" value="Player Administration">
			</form></tr>
			<?
			$rownum = $rownum -1;
		}
?>
                 <tr>
                    <td colspan="6" height="20">
                    <!-- Spacer -->
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6" align="right" class="normal">
                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php"><< New Search</a> &nbsp;&nbsp;&nbsp;
                    </td>
                  </tr>
                <?


		echo "</table>\n";
	}

}
	//return $result;
?>
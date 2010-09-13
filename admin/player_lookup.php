<?php


/*
 * $LastChangedRevision: 643 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-30 08:51:17 -0800 (Tue, 30 Dec 2008) $
 */

include ("../application.php");
$DOC_TITLE = "Account Maintenance";
require_loginwq();

/* form has been submitted, try to create the new role */
$searchname = $_REQUEST['searchname'];


//Check to see if the view is empty

	if (isset ($searchname)) {

		$errormsg = validate_form($searchname);
		$backtopage = $_SESSION["CFG"]["wwwroot"]."/admin/player_lookup.php";

		if ($errormsg) {
			include ($_SESSION["CFG"]["templatedir"]."/header.php");
			include ($_SESSION["CFG"]["includedir"]."/errorpage.php");
			include ($_SESSION["CFG"]["templatedir"]."/footer.php");
			die;
		} else {
			$playerResults = get_all_player_search($searchname);
			
			if(isDebugEnabled(1) ) logMessage("player_lookup: Found ".mysql_num_rows($playerResults)." results");
			
			include ($_SESSION["CFG"]["templatedir"]."/header.php");
			print_players($searchname, $playerResults, $DOC_TITLE, $ME);
			include ($_SESSION["CFG"]["templatedir"]."/footer.php");
			die;
		}
	}

include ($_SESSION["CFG"]["templatedir"]."/header.php");
include ($_SESSION["CFG"]["templatedir"]."/player_lookup_form.php");
include ($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($searchname) {
	/* validate the signup form, and return the error messages in a string.  if
	 * the string is empty, then there are no errors */

	$errors = new Object;
	$msg = "";

	if (empty ($searchname)) {
		//$errors->searchname = true;
		$msg .= "You did not specify a name to search";
	}
	elseif (strpos($searchname, "'") != false) {
		//$errors->searchname = true;
		$msg .= "No speical characters please. ";

	}

	return $msg;
}

/******************************************************************************
 * Print_players
 *****************************************************************************/

function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {

	$wwwroot = $_SESSION["CFG"]["wwwroot"];

	if(isDebugEnabled(1) ) logMessage("player_lookup.print_players: searchname: $searchname");
	
	if (mysql_num_rows($playerResults) < 1) {
		$errormsg = "Sorry, no results found.";
		include ($_SESSION["CFG"]["includedir"]."/errorpage.php");
		
	} else {
?>
          <table cellspacing="0" cellpadding="5" width="710" align="center">
          <tr>
          <td colspan="6">
           <table cellspacing="0" cellpadding="20" width="400" >
                   <tr>
                      <td class="clubid<?=get_clubid()?>th"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
                   </tr>

                   <tr>
                    <td class=generictable>
                    <form name="entryform" method="post" action="<?=$ME?>">
                        <table width="400">
                            <tr>
                                        <td class="label">Member Name:</td>
                                        <td><input type="text" name="searchname" size=25 value="<? pv($searchname) ?>">

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


		mysql_data_seek($playerResults, 0);
		$num_fields = mysql_num_fields($playerResults);
		$num_rows = mysql_num_rows($playerResults);
?>



                       <tr class=loginth>
                           <td height="25"><font class="whitenorm"><div align="center">First Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Last Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Club</div></font></td>

                           <td></td>
                       </tr>





                <?


		$rownum = mysql_num_rows($playerResults);
		while ($playerarray = mysql_fetch_array($playerResults)) {

			$rc = (($rownum / 2 - intval($rownum / 2)) > .1) ? "C0C0C0" : "DBDDDD";
			echo "<tr bgcolor='", $rc, "' class=normal><form name=\"playerform$rownum\" method=\"post\" action=$wwwroot/admin/player_info.php>\n";
			echo "<td><div align=\"center\">$playerarray[firstname]</div> </td>";
			echo "<td><div align=\"center\">$playerarray[lastname]</div> </td>";
			echo "<input type=\"hidden\" name=\"userid\" value=\"$playerarray[0]\"><input type=\"hidden\" name=\"searchname\" value=\"$searchname\">";
			echo "<td><div align=\"center\">$playerarray[clubname]</div> </td>";
			echo "<td><div align=\"center\"><a href=\"javascript:submitForm('playerform$rownum');\">Info</a></td>\n";
			echo "</form></tr>\n";
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
                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php"><< New Search</a> &nbsp;&nbsp;&nbsp;
                    </td>
                  </tr>
                <?
    
	
	
    
		echo "</table>\n";
	}
}

	/******************************************************************************
	 * Get_player_search
	 *****************************************************************************/

	function get_all_player_search($searchname) {

		$playerquery = "SELECT users.userid, users.firstname, users.lastname,  clubs.clubname
		                        FROM tblUsers users, tblClubs clubs, tblClubUser clubuser
		                        WHERE clubuser.clubid = clubs.clubid
								AND users.userid = clubuser.userid
		                        AND (users.firstname Like '$searchname%'
		                        OR users.lastname Like '$searchname%')
		                        AND clubuser.roleid != 4
								AND clubuser.enddate IS NULL
		                        ORDER BY users.lastname";
		if(isDebugEnabled(1) ) logMessage($playerquery);
		
		return db_query($playerquery);

	}
?>
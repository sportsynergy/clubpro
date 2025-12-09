<?php

include ("../application.php");
$DOC_TITLE = "User Account Maintenance";
require_loginwq();

/* form has been submitted, try to create the new role */
$searchname = $_REQUEST['searchname'];

//Check to see if the view is empty

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/admin/player_lookup.php";
    
    if (empty($errormsg)) {
        $playerResults = get_all_player_search($searchname);
        
        if (isDebugEnabled(1)) logMessage("player_lookup: Found " . mysqli_num_rows($playerResults) . " results");
        include ($_SESSION["CFG"]["templatedir"] . "/header.php");
        print_players($searchname, $playerResults, $DOC_TITLE, $ME);
        include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
        die;
    }
}
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($searchname) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (empty($searchname)) {

        //$errors->searchname = true;
        $msg.= "You did not specify a name to search";
    } elseif (strpos($searchname, "'") !== false) {

        //$errors->searchname = true;
        $msg.= "No speical characters please. ";
    }
    return $msg;
}

/******************************************************************************
 * Print_players
 *****************************************************************************/
function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {
    $wwwroot = $_SESSION["CFG"]["wwwroot"];
    
    if (isDebugEnabled(1)) logMessage("player_lookup.print_players: searchname: $searchname");
    
    if (mysqli_num_rows($playerResults) < 1) {
        $errormsg = "Sorry, no results found.";
        include ($_SESSION["CFG"]["includedir"] . "/errorpage.php");
    } else {
        include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
        mysqli_data_seek($playerResults, 0);
        $num_fields = mysqli_num_fields($playerResults);
        $num_rows = mysqli_num_rows($playerResults);
?>

				<table cellpadding="20" width="650" class="table table-striped" >
                <thead>       
                <tr>
                           <th>First Name</th>
                           <th>Last Name</th>
                           <th>Club</th>
                           <th></th>
                       </tr>
                </thead>
            <tbody>
                <?


		$rownum = mysqli_num_rows($playerResults);
		while ($playerarray = mysqli_fetch_array($playerResults)) {

			
			?>
			
			<tr >
			<form name="playerform<?=$rownum?>" method="get" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_info.php">
			<td><div align="center"><?=$playerarray['firstname']?></div> </td>
			<td><div align="center"><?=$playerarray['lastname']?></div> </td>
				<input type="hidden" name="userid" value="<?=$playerarray[0]?>">
				<input type="hidden" name="searchname" value="<?=$searchname?>">
			<td><div align="center"><?=$playerarray['clubname']?></div> </td>
			<td><div align="center"><a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
			</form>
			</tr>
			
			<?
			$rownum = $rownum -1;
		}
?>
            </tbody>
                  </table>
                  
                 <div style="height: 2em;"></div>
                 <div>
                 	<span style="text-align: right;"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">New Search</a>  </span>
                 </div> 
                 
                <?
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
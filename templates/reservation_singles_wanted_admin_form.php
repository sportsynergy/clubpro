<?php
/*
 * $LastChangedRevision: 650 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-01-12 17:10:42 -0800 (Mon, 12 Jan 2009) $

*/
?>
<?
         //reservation_singles_wanted_form.php
         $DOC_TITLE = "Singles Court Reservation";

         //Get matchtype
         $matchTypeQuery = "SELECT matchtype from tblReservations WHERE time=$time AND courtid=$courtid AND enddate IS NULL";
         $matchTypeQueryResult =  db_query($matchTypeQuery);
         $matchtype = mysql_result($matchTypeQueryResult,0);

         $lookingForMatchQuery = "SELECT tblkpUserReservations.userid
                                  FROM tblReservations
                                  INNER  JOIN tblkpUserReservations ON tblReservations.reservationid = tblkpUserReservations.reservationid
                                  WHERE ( ( ( tblReservations.courtid ) = $courtid ) 
								  AND ( ( tblReservations.time ) = $time ) ) 
								  AND (tblkpUserReservations.userid != 0)
								  AND tblReservations.enddate IS NULL";

         $lookingformatchResult = db_query($lookingForMatchQuery);
         $guylookingformatch = mysql_result($lookingformatchResult,0);

		//Normall we we call upon one of the drop down functions (in applicationlib.php), but in this case we also want to exclude the userid stored as $userid
		$excludeUserIDUserQuery =   "SELECT DISTINCT users.userid, users.firstname, users.lastname
					                FROM tblUsers users, tblUserRankings rankings, tblClubUser clubuser, tblkupSiteAuth siteAuth
									WHERE users.userid = rankings.userid
									AND clubuser.userid = users.userid
									AND siteAuth.userid = users.userid
									AND users.userid = clubuser.userid
									AND clubuser.clubid = ".get_clubid()."
					                AND clubuser.roleid!= 4
					                AND users.userid!=$userid
					                AND clubuser.clubid=".get_clubid()."
					                AND users.lastname != 'Guest'
					                AND users.firstname != 'Club'
					                AND siteAuth.siteid=$currentCourtSiteID
					                AND rankings.courttypeid=".get_courtTypeForCourt($courtid)."
					                AND rankings.usertype=0
					                AND clubuser.enable='y'
									AND clubuser.enddate IS NULL
					                ORDER BY users.lastname";
		


		 $playerDropDown = db_query($excludeUserIDUserQuery);;
		 
		 //Get Full Name for user who made this resevation
		 $personWhoMadeThisReservationQuery = "SELECT user.firstname, user.lastname from tblUsers user WHERE user.userid=$userid";
		 $personWhoMadeThisReservationResult = db_query($personWhoMadeThisReservationQuery);
		 $firstName = mysql_result($personWhoMadeThisReservationResult,0,"firstname");
		 $lastName = mysql_result($personWhoMadeThisReservationResult,0,"lastname");
		 $userFullName = $firstName." ".$lastName;
		 

?>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">


       <tr>
        <td><? echo "<input type=\"hidden\" name=\"time\" value=\"$time\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"courtid\" value=\"$courtid\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"courttype\" value=\"courttype\">" ?>


               <?echo "<input type=\"hidden\" name=\"matchtype\" value=\"$matchtype\">" ?>
               <?echo "<input type=\"hidden\" name=\"guylookingformatch\" value=\"$guylookingformatch\">" ?>
         </td>
       </tr>
        <tr>
        <td><? echo "<input type=\"hidden\" name=\"partner\" value=\"frompwform\">" ?><td>
         <td><td>
         <td></td>
       </tr>
      
       <tr>
           <td>Select Opponent for <?=$userFullName?></td>
           <td>

           <input id="name1" name="opponentname" type="text" size="30" class="form-autocomplete" />
            <input id="id1" name="opponent" type="hidden" size="30" />
            <script>
                <?
                 
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
    		</td>
           <td></td>
    </tr>
    <tr>
       	<td height='20' colspan='3'></td>
    </tr>
    <tr>
    		<td>
    			<input type="submit" name="submit" value="Submit"> &nbsp;
            	<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?=gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'"></td>   		
    		<td></td>
    		<td></td>
    		
    </tr>
 </table>

</table>
</form>


</td>
</tr>
</table>
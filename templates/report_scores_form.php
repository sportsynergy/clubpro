<?php
/*
 * $LastChangedRevision: 671 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-02-17 10:26:59 -0800 (Tue, 17 Feb 2009) $

*/
?>
<?
 //Initialize script variables
  $userarray = array();
?>

<script language="Javascript">
<!--
// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

//-->
</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="20" width="400" >
 <tr>
    <td class="clubid<?=get_clubid()?>th"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class="generictable">

     <table cellspacing="0" cellpadding="5" width="400" >


        <tr>

               			<td class="label">Winner:</td>
                        <td>
						<?
                       $residquery = "SELECT * from tblkpUserReservations
                                     WHERE reservationid='".$reservationid."'";
                       $residresult = db_query($residquery);

                       $restypequery = "SELECT tblReservations.usertype,tblReservations.matchtype, tblReservations.time, tblReservations.courtid
                                          FROM tblReservations
                                          WHERE (((tblReservations.reservationid)=$reservationid)) ";

                       $restyperesult = db_query($restypequery);
                       $restypearray =  db_fetch_array($restyperesult);


                       echo "<select name=\"winner\">";

                       if ($restypearray[0]==1){

                       while ($reobj = db_fetch_object($residresult)){
                                //For Each userid get the First and Last Name

                                $teamnamequery = "SELECT tblUsers.firstname, tblUsers.lastname
                                                  FROM tblUsers INNER JOIN tblkpTeams ON tblUsers.userid = tblkpTeams.userid
                                                  WHERE ((tblkpTeams.teamid)=$reobj->userid)";

                                $teamnameresult = db_query($teamnamequery);
                                $teamnamearray = db_fetch_array($teamnameresult);
                                //echo "<option value=$reobj->userid>$reobj->userid </option>";
                                echo "<option value=$reobj->userid>$teamnamearray[0] $teamnamearray[1] - ";
                                $teamnamearray = db_fetch_array($teamnameresult);
                                echo "$teamnamearray[0] $teamnamearray[1] </option>\n";
                                array_push($userarray, $reobj->userid);

                        }

                       }
                       else {

                       while ($reobj = db_fetch_object($residresult)){
                                //For Each userid get the First and Last Name
                               $useridquery = "SELECT * from tblUsers
                                              WHERE userid='".$reobj->userid."'";
                               $useridresult = db_query($useridquery);
                               $userobj = db_fetch_object($useridresult);
                               echo "<option value=$reobj->userid>$userobj->firstname $userobj->lastname </option>";
                               array_push($userarray, $reobj->userid);

                       }
                       }



                       ?>
                </select>
            </td>
       </tr>
        <tr>
        <td class="label">Score:</td>
           <td>
             <select name="score">
                     <option value="0">3-0</option>
                     <option value="1">3-1</option>
                     <option value="2">3-2</option>
             </select>
           </td>

       </tr>
      <tr>
         <td class="label">Match Type:</td>
           <td><select name="matchtype">
          
           
            <? 
            //Default Selection
            $practiceSelection = $restypearray[1]==0?"selected":"";
            $leagueSelection = $restypearray[1]==1?"selected":"";
            $challengeSelection = $restypearray[1]==2?"selected":"";
            
            if( isUnscoredBoxLeagueReservation( $reservationid ) || are_boxplayers($userarray[0],$userarray[1] ) ){  ?>
            <option value="1" <?=$leagueSelection?> >Box League</option>
             <? } ?>
            <option value="2" <?=$challengeSelection?> >Challenge</option>
            <option value="0" <?=$practiceSelection?>>Practice</option>

           </select>
           </td>
       </tr>
       <tr>
       		<td class="italitcsm" colspan="2"><br><br>If these are not the player's that played click <a href="court_cancelation.php?time=<?=$restypearray[2]?>&courtid=<?=$restypearray[3]?>">here</a>.</td>
       </tr>
       <tr>
           <td colspan="2">
           		<input type="hidden" name="usertype" value="<?=$restypearray[0] ?>">
               <input type="hidden" name="Player1" value="<? echo $userarray[0] ?>">
			   <input type="hidden" name="Player2" value="<? echo $userarray[1] ?>">
			   <input type="hidden" name="reservationid" value="<? echo $reservationid ?>">
           </td>

       </tr>
       <tr>
	       <td>
	           <input type="submit" name="submit" value="Submit">
	       </td>

    </tr>
 </table>

</td>
</tr>
</table>

</form>


</td>
</tr>
</table>
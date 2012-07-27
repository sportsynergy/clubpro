<?php

 //Initialize script variables
  $userarray = array();
?>

<script language="Javascript">

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


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>




<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
 <tr>
    <td class="clubid<?=get_clubid()?>th">
    	<div class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</div>
    </td>
 </tr>

 <tr>
    <td >

     <table cellspacing="0" cellpadding="5" width="400">


        <tr>

               			<td >
               			<span class="label">Winner:</span>
                        
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
	        <td>
	        <span class="label">Score:</span>
	             <select name="score">
	                     <option value="0">3-0</option>
	                     <option value="1">3-1</option>
	                     <option value="2">3-2</option>
	             </select>
	           </td>

       </tr>
      
       <tr>
       		<td  >
       		<br><br>If these are not the player's that played click <a href="court_cancelation.php?time=<?=$restypearray[2]?>&courtid=<?=$restypearray[3]?>">here</a>.</td>
       </tr>
      
       <tr>
	       <td>
	           <input type="button" name="submit" value="Put this score in" id="submitbutton">
	       </td>

    </tr>
 </table>

</td>
</tr>
</table>

				<input type="hidden" name="usertype" value="<?=$restypearray['usertype'] ?>">
               <input type="hidden" name="Player1" value="<?=$userarray[0] ?>">
			   <input type="hidden" name="Player2" value="<?=$userarray[1] ?>">
			   <input type="hidden" name="reservationid" value="<?=$reservationid ?>">
			   <input type="hidden" name="matchtype" value="<?=$restypearray['matchtype'] ?>">
			   <input type="hidden" name="source" value="<?=$source ?>">
			   <input type="hidden" name="submitme" value="submitme">

</form>



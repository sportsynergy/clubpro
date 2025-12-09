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

function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>">


<div class="mb-3">
      <label for="gender" class="form-label">Winner</label>
      <select class="form-select" aria-label="Winner" name="winner" id="winner">
       <?
                       $residquery = "SELECT * from tblkpUserReservations
                                     WHERE reservationid='".$reservationid."'";
                       $residresult = db_query($residquery);

                       $restypequery = "SELECT tblReservations.usertype,tblReservations.matchtype, tblReservations.time, tblReservations.courtid
                                          FROM tblReservations
                                          WHERE (((tblReservations.reservationid)=$reservationid)) ";

                       $restyperesult = db_query($restypequery);
                       $restypearray =  db_fetch_array($restyperesult);

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

                       } else {

                          while ($reobj = db_fetch_object($residresult)){
                                    //For Each userid get the First and Last Name
                                  $useridquery = "SELECT * from tblUsers
                                                  WHERE userid='".$reobj->userid."'";
                                  $useridresult = db_query($useridquery);
                                  $userobj = db_fetch_object($useridresult);
                                  echo "<option value=$reobj->userid>$userobj->firstname $userobj->lastname </option>";
                                  array_push($userarray, $reobj->userid);
                          }
                       } ?>
                </select>
 
    </div>

  <div class="mb-3">
      <label for="score" class="form-label">Score</label>
      <select class="form-select" aria-label="Score" name="score" id="score">
        <? while($matchscore = mysqli_fetch_array($matchscores)){  
                  $gameswon = $matchscore['gameswon'];  ?>
                  <option value="<?=$matchscore['gameslost']?>"><?=$matchscore['gameswon']?>-<?=$matchscore['gameslost']?></option>
        <? } ?>       
	      </select>
    </div>

    <div class="mb-3">
      <div id="emailHelp" class="form-text">
        If these are not the player's that played or if you need to change the match type, click <a href="court_cancelation.php?time=<?=$restypearray[2]?>&courtid=<?=$restypearray[3]?>">here</a>
      </div>
    </div>
                        
	<div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Put this score in</button>
  </div>			

    <input type="hidden" name="usertype" value="<?=$restypearray['usertype'] ?>">
    <input type="hidden" name="Player1" value="<?=$userarray[0] ?>">
    <input type="hidden" name="Player2" value="<?=$userarray[1] ?>">
    <input type="hidden" name="reservationid" value="<?=$reservationid ?>">
    <input type="hidden" name="gameswon" value="<?=$gameswon ?>">
    <input type="hidden" name="matchtype" value="<?=$restypearray['matchtype'] ?>">
    <input type="hidden" name="source" value="<?=$source ?>">
    <input type="hidden" name="submitme" value="submitme">
        

</form>



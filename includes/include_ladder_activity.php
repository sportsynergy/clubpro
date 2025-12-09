
<script type="text/javascript">

function removeChallengeMatch(matchid,challengerid, challengeeid){

	var answer = confirm ("Do you really want to remove this challenge");
	
	if (answer){
		document.removeChallengeMatchForm.challengematchid.value = matchid;
		document.removeChallengeMatchForm.challengerid.value = challengerid;
		document.removeChallengeMatchForm.challengeeid.value = challengeeid;
		document.removeChallengeMatchForm.submit();
	}
	
}

function recordScore(matchid,laddertype){

		document.recordScoreForm.challengematchid.value = matchid;
		document.recordScoreForm.laddertype.value = laddertype;
		document.recordScoreForm.submit();
}

</script>

<form name="removeChallengeMatchForm" action="<?=$ME?>" method="post">
  <input type="hidden" name="cmd" value="removechallenge">
  <input type="hidden" name="challengematchid" value="">
  <input type="hidden" name="challengerid" value="">
  <input type="hidden" name="challengeeid" value="">
</form>
<form name="recordScoreForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder.php" method="post">
  <input type="hidden" name="laddertype" value="">
  <input type="hidden" name="source" value="ladder">
  <input type="hidden" name="challengematchid" value="">
</form>
<div>
  <h2 >Recent Challenge Matches</h2>
  <hr class="hrline"/>
  <?php

// TODO: Fix this
// hard coding courttype id for now

$challengeMatchResult = getChallengeMatches( get_siteid(), $ladderid, 15 );

if(mysqli_num_rows($challengeMatchResult) > 0){ ?>
  <table class="activitytable" width="450">
    <tr>
      <th>Date</th>
      <th>Challenger</th>
      <th>Challengee</th>
      <th>Winner</th>
    </tr>
    <?
	
while($challengeMatch = mysqli_fetch_array($challengeMatchResult)){ 
		
		$scored = $challengeMatch['score'];
		
		$challenger = new clubpro_obj;
		$challenger->fullname =  $challengeMatch['challenger_first']." ". $challengeMatch['challenger_last'];
		$challenger->id = $challengeMatch['challenger_id'];
		
		$challengee = new clubpro_obj;
		$challengee->fullname =  $challengeMatch['challengee_first']." ". $challengeMatch['challengee_last'];
		$challengee->id = $challengeMatch['challengee_id'];
		
		$inreservation = get_userid() == $challengeMatch['challenger_id'] || get_userid() == $challengeMatch['challengee_id'] ? true : false;
		
		//don't include timestamp
		$challengeDate = explode(" ",$challengeMatch['date']);
		
		printLadderEventRow($challengeMatch['id'], $challenger, $challengee, $challengeDate[0], $scored, $inreservation, true);
	    


  } ?>
  </table>
  <div style="margin-top: 20px"> <span class="smallbold">note:</span> <span class="normalsm">mouse over winner's name to see the score</span> </div>
  <? } else { ?>

    
  No challenge matches found.
  <? } ?>
</div>

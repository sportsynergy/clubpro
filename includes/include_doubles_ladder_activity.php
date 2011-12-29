
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

</script>

 <form name="removeChallengeMatchForm" action="<?=$ME?>" method="post">
       <input type="hidden" name="cmd" value="removechallenge">
       <input type="hidden" name="challengematchid" value="">
       <input type="hidden" name="challengerid" value="">
       <input type="hidden" name="challengeeid" value="">
</form>

<div>

<h2>Recent Challenge Matches</h2>
<hr class="hrline"/>

<?

//hard coding courttype id for now
$challengeMatchArray = getDoublesChallengeMatches( get_siteid(), $courttypeid, 10 );

if( count($challengeMatchArray) > 0){ ?>
	


<table class="activitytable" width="450">
<tr>
	<th>Date</th>
	<th>Challenger</th>
	<th>Challengee</th>
	<th>Winner</th>
</tr>

<?
	
for($i=0; $i < count($challengeMatchArray); ++$i){
	$challengeMatch = $challengeMatchArray[$i];
	
	$scored = $challengeMatch['score'];
	
	$challenger = new Object;
	$challenger->fullname =  $challengeMatch['challenger_team'];
	$challenger->id = $challengeMatch['challenger_team_id'];
	
	$challengee = new Object;
	$challengee->fullname =  $challengeMatch['challengee_team'];
	$challengee->id = $challengeMatch['challengee_team_id'];

	$userid = get_userid();
	$inreservation = in_array($userid,$challengeMatch['ids']) ;
	
	//don't include timestamp
	$challengeDate = explode(" ",$challengeMatch['date']);
	
	printLadderEventRow($challengeMatch['id'], $challenger, $challengee, $challengeDate[0], $scored, $inreservation, false);
	
} ?>


</table>

<div style="margin-top: 20px">
	<span class="smallbold">note:</span>
	<span class="normalsm">mouse over winner's name to see the score</span>
</div>

<? } else{ ?>
	
	No challenge matches found.
	
<? } ?>


</div>
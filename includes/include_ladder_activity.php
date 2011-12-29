
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

<h2 >Recent Challenge Matches</h2>
<hr class="hrline"/>

<?

//hard coding courttype id for now

$challengeMatchResult = getChallengeMatches( get_siteid(), $courttypeid, 15 );

if(mysql_num_rows($challengeMatchResult) > 0){ ?>
	
<table class="activitytable" width="450">
<tr>
	<th>Date</th>
	<th>Challenger</th>
	<th>Challengee</th>
	<th>Winner</th>
</tr>

<?
	
while($challengeMatch = mysql_fetch_array($challengeMatchResult)){ 
		
		$scored = $challengeMatch['score'];
		
		$challenger = new Object;
		$challenger->fullname =  $challengeMatch['challenger_first']." ". $challengeMatch['challenger_last'];
		$challenger->id = $challengeMatch['challenger_id'];
		
		$challengee = new Object;
		$challengee->fullname =  $challengeMatch['challengee_first']." ". $challengeMatch['challengee_last'];
		$challengee->id = $challengeMatch['challengee_id'];
		
		$inreservation = get_userid() == $challengeMatch['challenger_id'] || get_userid() == $challengeMatch['challengee_id'] ? true : false;
		
		//don't include timestamp
		$challengeDate = explode(" ",$challengeMatch['date']);
		
		printLadderEventRow($challengeMatch['id'], $challenger, $challengee, $challengeDate[0], $scored, $inreservation, true);
	    


  } ?>
	
</table>
<div style="margin-top: 20px">
	<span class="smallbold">note:</span>
	<span class="normalsm">mouse over winner's name to see the score</span>
</div>

<? } else { ?>


No challenge matches found.

<? } ?>


</div>
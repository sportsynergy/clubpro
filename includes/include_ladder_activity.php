


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
		$challenger =  $challengeMatch['challenger_first']." ". $challengeMatch['challenger_last'];
		$challengee =  $challengeMatch['challengee_first']." ". $challengeMatch['challengee_last'];
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
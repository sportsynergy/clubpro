


<div>


<?

//hard coding courttype id for now

$challengeMatchResult = getChallengeMatches( get_siteid(), $courttypeid, 10 );

if(mysql_num_rows($challengeMatchResult) > 0){ ?>
	

<h2 style="padding-top: 15px">Recent Challenge Matches</h2>
<hr class="hrline"/>
<ul class="ladderactivity">	

<?
	
while($challengeMatch = mysql_fetch_array($challengeMatchResult)){ 
		
		$scored = $challengeMatch['score'];
		$challenger =  $challengeMatch['challenger_first']." ". $challengeMatch['challenger_last'];
		$challengee =  $challengeMatch['challengee_first']." ". $challengeMatch['challengee_last'];
		$inreservation = get_userid() == $challengeMatch['challenger_id'] || get_userid() == $challengeMatch['challengee_id'] ? true : false;
		
		//don't include timestamp
		$challengeDate = explode(" ",$challengeMatch['date']);
		
		printLadderEvent($challengeMatch['id'], $challenger, $challengee, $challengeDate[0], $scored, $inreservation, true);
	    


  } ?>
	
</ul>

<? } ?>





</div>
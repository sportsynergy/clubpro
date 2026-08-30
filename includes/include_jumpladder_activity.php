

<div>
  <h2 >Recent Ladder Matches</h2>
  <hr class="hrline"/>

  <?php

$ladderMatchResult = getLadderMatches($ladderid, 40 );

if(mysqli_num_rows($ladderMatchResult) > 0){  ?>

<table class="activitytable sortable">
    <tr>
      <th>Date</th>
      <th>Winner</th>
      <th>Loser</th>
      <th>Score</th>
    </tr>
    
    <?
$rownum = mysqli_num_rows($ladderMatchResult);	
while($challengeMatch = mysqli_fetch_array($ladderMatchResult)){ 

  $scored = $challengeMatch['score'];
		
		$winner_obj = new clubpro_obj;
		$winner_obj->fullname =  $challengeMatch['winner_first']." ". $challengeMatch['winner_last'];
		$winner_obj->id = $challengeMatch['winner_id'];
		
		$loser_obj = new clubpro_obj;
		$loser_obj->fullname =  $challengeMatch['loser_first']." ". $challengeMatch['loser_last'];
		$loser_obj->id = $challengeMatch['loser_id'];
	
		//don't include timestamp
		$challengeDate = explode(" ",$challengeMatch['match_time']);

		printLadderMatchRow($challengeMatch['id'], $winner_obj, $loser_obj, $challengeDate[0], $scored, $challengeMatch['league'], $challengeMatch['processed']);
	    
}
?>
</table>

  <div style="margin-top: 20px">
    <img src="<?=$_SESSION["CFG"]["imagedir"]?>/boxleague.gif "\> Indicates League Match
  </div>
  <div > 
    Ladders last updated:
  <?php
    
    if( is_null($ladderdetails->lastUpdated) ){
      $lastupdated = "Never";
    } else {
      //$lastupdated = ladderdetails['lastUpdated'];
      $lastupdated = $ladderdetails->lastUpdated;
    }
    
  ?>
  <?=$lastupdated ?>


  </div>
  

<? }  else { ?>
  <table class="activitytable" >
<tr>
  <td style="text-align: left">No challenge matches found.</td>
</tr>
</table>
  
 <? } ?>
  
</div>
  


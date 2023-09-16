<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
 * Class and Function List:
 * Function list:
 * Classes list:
 */
?>
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

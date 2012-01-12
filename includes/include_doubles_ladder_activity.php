<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
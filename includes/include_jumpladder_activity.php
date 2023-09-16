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

<div>
  <h2 >Recent Ladder Matches</h2>
  <hr class="hrline"/>

  <?php

$ladderMatchResult = getLadderMatches($ladderid, 15 );

if(mysqli_num_rows($ladderMatchResult) > 0){ 

  ?>

  <table class="activitytable" width="400">
    <tr>
      <th>Date</th>
      <th>Winner</th>
      <th>Loser</th>
      <th>Score</th>
    </tr>
    
    <?
	
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
		
		printLadderMatchRow($challengeMatch['id'], $winner_obj, $loser_obj, $challengeDate[0], $scored);
	    
}
?>
</table>
  <div style="margin-top: 20px"> <span class="smallbold">Ladders last updated</span> Never


  </div>
  

<? }  else { ?>
  <table class="activitytable" width="400">
<tr>
  <td style="text-align: left">No challenge matches found.</td>
</tr>
</table>
  
 <? } ?>
  
</div>
  


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


				<table width="500" class="table table-striped" >
			    <thead>   
				<tr>
						<th>Date</th>
						<th>Opponent</th>
						<th>Outcome</th>
					</tr>
				</thead>
<tbody>

					<?php

					$outcome_indicator = "W";
						
					

						//get the other player
						$query = "SELECT users.userid, users.firstname, users.lastname, details.outcome, reservations.time
									FROM tblReservations reservations
									INNER JOIN tblkpUserReservations details ON reservations.reservationid = details.reservationid
									INNER JOIN tblUsers users ON details.userid = users.userid
									WHERE reservations.reservationid = $reservationid
									ORDER BY details.outcome";
						
						// run the query on the database
						$history_result = db_query($query);
						
						$history_row = mysqli_fetch_array($history_result);
						$time  = gmdate("l F j",$history_row['time']) ;
						$loser_score = $history_row['outcome'];
						
							// This means that the person this page is loading for lost
							if( $history_row['userid']==$userid ){
								$outcome_indicator = "L";
								
							} else{
								$opponent = $history_row['firstname']." ".$history_row['lastname'];
								$opponentid = $history_row['userid'];
								$outcome_indicator = "W";
							}
							
						$history_row = mysqli_fetch_array($history_result);	
							
							if( $history_row['userid']==$userid ){
								$outcome_indicator = "W";
								
							} else{
								$opponent = $history_row['firstname']." ".$history_row['lastname'];
								$opponentid = $history_row['userid'];
								$outcome_indicator = "L";
								$outcome = $history_row['outcome'];
							}
						
						$outcome_code = $outcome_indicator.$loser_score;
						
						
					?>
					<tr>
						<td><?=$time?></td>
						<td ><?=$opponent?></td>
						<td >
							<select name="new_outcome_code" class="form-select" aria-label="Select Outcome">
				                <option value="W0" <?=$outcome_code=="W0"?"selected=\"selected\"":""?>>Won: 3-0</option>
				                <option value="W1" <?=$outcome_code=="W1"?"selected=\"selected\"":""?>>Won: 3-1</option>
								<option value="W2" <?=$outcome_code=="W2"?"selected=\"selected\"":""?>>Won: 3-2</option>
								<option value="L2" <?=$outcome_code=="L2"?"selected=\"selected\"":""?>>Lost: 3-2</option>
								<option value="L1" <?=$outcome_code=="L1"?"selected=\"selected\"":""?>>Lost: 3-1</option>
								<option value="L0" <?=$outcome_code=="L0"?"selected=\"selected\"":""?>>Lost: 3-0</option>
				              </select> 
						</td>
						
					</tr>
					
						</tbody>
			     </table>
	
 <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Update Score</button>
    <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
  </div> 

	<input type="hidden" name="reservationid" value="<?=$reservationid?>" >
	<input type="hidden" name="userid" value="<?=$userid?>" >
	<input type="hidden" name="boxid" value="<?=$boxid?>" >
	<input type="hidden" name="submitme" value="submitme">
	<input type="hidden" name="orig_outcome_code" value="<?=$outcome_code?>">
	<input type="hidden" name="opponentid" value="<?=$opponentid?>">
</form>



<script type="text/javascript">

function onSubmitButtonClicked(){
	submitForm('entryform');
}
function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$boxid?>&userid=<?=$userid?>'
}



</script>
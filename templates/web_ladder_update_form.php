<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<table width="500" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
     <tr>
         <td class=clubid<?=get_clubid()?>th>
         	<span class="whiteh1">
         		<div align="center"><? pv($DOC_TITLE) ?></div>
         	</span>
         </td>
    </tr>

 <tr>
    <td>

      <table width="550" cellspacing="5" cellpadding="0" class="borderless" id="formtable">
     	<tr>
      		<td>
				<table width="500" class="borderless">
			       <tr>
						<th>Date</th>
						<th>Opponent</th>
						<th>Outcome</th>
					</tr>

					<?php

					$outcome_indicator = "W";
						
					 $row = mysqli_fetch_array($result);

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
						<td align="center"><?=$time?></td>
						<td align="center"><?=$opponent?></td>
						<td align="center">
							<select name="new_outcome_code">
				                <option value="W0" <?=$outcome_code=="W0"?"selected=\"selected\"":""?>>Won: 3-0</option>
				                <option value="W1" <?=$outcome_code=="W1"?"selected=\"selected\"":""?>>Won: 3-1</option>
								<option value="W2" <?=$outcome_code=="W2"?"selected=\"selected\"":""?>>Won: 3-2</option>
								<option value="L2" <?=$outcome_code=="L2"?"selected=\"selected\"":""?>>Lost: 3-2</option>
								<option value="L1" <?=$outcome_code=="L1"?"selected=\"selected\"":""?>>Lost: 3-1</option>
								<option value="L0" <?=$outcome_code=="L0"?"selected=\"selected\"":""?>>Lost: 3-0</option>
				              </select> 
						</td>
						
					</tr>
					<tr>
						<td colspan="3" height="25"><!--spacer --></td>
					</tr>
					<tr>
						<td colspan="3" align="right">
							<input type="button" name="cancel" value="Cancel, Go back" id="cancelbutton">
							<input type="button" name="submit" value="Update Score" id="submitbutton" >
						</td>
					</tr>
					
			     </table>
				
	</td>
     </tr>       

   </td>
</tr>
</tr>
	
</table>

</td>
</tr>
</table>
	<input type="hidden" name="reservationid" value="<?=$reservationid?>" >
	<input type="hidden" name="userid" value="<?=$userid?>" >
	<input type="hidden" name="boxid" value="<?=$boxid?>" >
	<input type="hidden" name="submitme" value="submitme">
	<input type="hidden" name="orig_outcome_code" value="<?=$outcome_code?>">
	<input type="hidden" name="opponentid" value="<?=$opponentid?>">
</form>



<script type="text/javascript">

YAHOO.example.init = function () {
    YAHOO.util.Event.onContentReady("formtable", function () {
        
		var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked); 

  		var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbuttonvalue" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();
function onSubmitButtonClicked(){
	submitForm('entryform');
}
function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$boxid?>&userid=<?=$userid?>'
}



</script>
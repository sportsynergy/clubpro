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

      <table width="550" cellspacing="5" cellpadding="0" class="borderless">
      
     <tr>
      <td>
				<table width="500" class="borderless">
			       <tr>
						<th>Date</th>
						<th>Opponent</th>
						<th>Outcome</th>
						<th>Points Scored</th>
					</tr>

					<?php

					$total_points = 0;
						
					 while($row = mysql_fetch_array($result) ) {

						//get the other player
						$query = "SELECT users.userid, users.firstname, users.lastname, details.outcome, reservations.time
									FROM tblReservations reservations
									INNER JOIN tblkpUserReservations details ON reservations.reservationid = details.reservationid
									INNER JOIN tblUsers users ON details.userid = users.userid
									WHERE reservations.reservationid = $row[reservationid]
									ORDER BY details.outcome";
						
						// run the query on the database
						$history_result = db_query($query);
						
						$history_row = mysql_fetch_array($history_result);
						$time  = gmdate("l F j",$history_row['time']) ;
						$loser_score = $history_row['outcome'];
							
							// This means that the person this page is loading for lost
							if( $history_row['userid']==$userid ){
								$outcome_indicator = "Lost";
							}  
							else {
								$outcome_indicator = "Won";
								$opponent = $history_row['firstname']." ".$history_row['lastname'];
							}
						
						$history_row = mysql_fetch_array($history_result);
						
							if( $history_row['userid']==$userid ){
								$outcome_indicator = "Won";
							}  
							else {
								$outcome_indicator = "Lost";
								$opponent = $history_row['firstname']." ".$history_row['lastname'];
							}
						
						
						$outcome_code = "$outcome_indicator: 3-$loser_score";
						
						if($outcome_code == "Won: 3-0"){
							$points_scored = 6;
						}elseif($outcome_code == "Won: 3-1"){
							$points_scored = 5;
						}elseif($outcome_code == "Won: 3-2"){
							$points_scored = 4;
						}elseif($outcome_code == "Lost: 3-2"){
							$points_scored = 3;
						}elseif($outcome_code == "Lost: 3-1"){
							$points_scored = 2;
						}elseif($outcome_code == "Lost: 3-0"){
							$points_scored = 1;
						}
						
						$total_points += $points_scored;
					?>
					<tr>
						<td align="center"><?=$time?></td>
						<td align="center"><?=$opponent?></td>
						<td align="center">
							<?=$outcome_code?>
						</td>
						<td align="center">
							<?=$points_scored?>
						</td>
						<td align="center"> 
								<? if( has_priv("2") ){ ?>
								<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_update.php?reservationid=<?=$row[reservationid]?>&userid=<?=$userid?>" >Edit</a>
								<? } ?>
						</td>
					</tr>
					
				<?php } ?>
					
						
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td align="center">
								<span style="text-decoration:underline">
								<?=$total_points?>
								</span>
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


</form>


<div style="height: 2em;"></div>
<div>
    <span style="text-align: right;"> 
	<? if(isset($page) && $page == "admin"){ ?>

		<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_manage.php?boxid=<?=$boxid?>">  << Back  </a> 
	<? } else {?>
    	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">  << Back  </a> 
	<? } ?>
    </span>
</div>

<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/sorttable.js" type="text/javascript"></script>
<?

//Set some important variables

$playerposition = 0;
$playerlocked = false;

while ( $playerarray = db_fetch_array($ladderplayers)){
		
	if($playerarray['userid']==get_userid()){
		$playerposition = $playerarray['ladderposition'];

		if($playerarray['locked']=='y'){
			$playerlocked = true;
		}

		break;
	}
		
}

// Rest the pointer
mysqli_data_seek($ladderplayers,0);

?>


<form name="addmetoladderform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>"> <input
		type="hidden" name="courttypeid" value="<?=$courttypeid?>"> <input
		type="hidden" name="cmd" value="addtoladder">
</form>

<form name="deleteform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>"> <input
		type="hidden" name="courttypeid" value="<?=$courttypeid?>"> <input
		type="hidden" name="cmd" value="removefromladder">
</form>

<form name="moveform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>"> <input
		type="hidden" name="courttypeid" value="<?=$courttypeid?>"> <input
		type="hidden" name="cmd" value="moveupinladder">
</form>


<?
$numrows = mysqli_num_rows($ladderplayers);
if($numrows ==0) { ?>

Nobody has signed up for the ladder yet.
<?  if(get_roleid()==2 || get_roleid==4){ ?>


Click <span id="show"><a style="text-decoration: underline; cursor: pointer">here</a> </span> to add somone now.

<? } else {?>

Click <a href="javascript:submitForm('addmetoladderform')">here</a> to add your name now.

<? } ?>

<? } else{  // has people siged up for a ladder?>

<div id="ladderControlPanel" style="padding-bottom: 5px;">

	<?  if(get_roleid()==2 || get_roleid()==4){ ?>
			 <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_ladder_player.php">Add Player</a> 
				| <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder_score.php">Report Score</a> 

	<? } ?>		
	<?  if(get_roleid()==1 && $playingInLadder){ ?>
			 <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/report_ladder_score.php">Report Score</a> 
	<? } ?>

</div>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="container">
	<div class="row">
		<div class="col-6">

		<table class="table table-striped sortable">
		<tr>
			<th width="25%">
				<span class="bold">Place</span>
			</th>
			<th>
				<span class="bold">Name</span>
			</th>
			<th>
				<span class="bold">Rating</span>
			</th>
		</tr>
			<? 
			//Reset pointer
			mysqli_data_seek($ladderplayers,0);

			$numrows = mysqli_num_rows($ladderplayers);
			while ( $playerarray = db_fetch_array($ladderplayers)){
				$rc = (($numrows/2 - intval($numrows/2)) > .1) ? "lightrow_plain" : "darkrow_plain";

				?>

			<form name="playerform<?=$numrows?>" method="get"
				action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php">
				<input type="hidden" name="userid"
					value="<?=$playerarray['userid']?>"> <input type="hidden"
					name="origin" value="ladder">
			</form>


			<tr>
				<td><?=$playerarray['ladderposition']?> <? if($playerarray['going']=="up"){ ?>
					<img src="<?=$_SESSION["CFG"]["imagedir"]?>/raise.png"
					title="Moved up the ladder after winning the last match"> <? } else if($playerarray['going']=="down"){ ?>
					<img src="<?=$_SESSION["CFG"]["imagedir"]?>/fall.png"
					title="Moved down the ladder after losing the last  match"> <? } ?>
				</td>
				<td><a href="javascript:submitForm('playerform<?=$numrows?>')"><?=$playerarray['firstname']?>
				<?=$playerarray['lastname']?> </a> <? if(get_roleid()==2 || get_roleid()==4){?>
					<a
					href="javascript:removeFromLadder(<?=$playerarray['userid']?>);"><img
						src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png"
						title="remove this person from the ladder" /></a> <a
					href="javascript:moveUpInLadder(<?=$playerarray['userid']?>);"><img
						src="<?=$_SESSION["CFG"]["imagedir"]?>/gtk_media_forward_ltr.png"
						title="bump this guy up one spot"> </a> <?}
						?> <? if($playerarray['locked']=='y') {?> <img
					src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"
					title="I am locked because I am currently being challenged or I am challenging someone else. In any event, once the scores have been put in, the lock will be removed." />
					<? } else if( !$playerlocked && isLadderChallengable( $playerposition, $playerarray['ladderposition'])  ){?>
					<span id="challenge-<?=$playerarray['ladderposition']?>"> <img
						src="<?=$_SESSION["CFG"]["imagedir"]?>/add_small.png"
						title="click me to challenge" />
				</span> <?} 

				?></td>
				<td>
				<?=$playerarray['ranking']?>
					</td>
			</tr>

			<?
			$numrows = $numrows - 1;
			}  ?>

		</table>

	</div> <!-- col -->
	<div class="col">

		<? 
			if ( isLadderRankingScheme() ){
				//include($_SESSION["CFG"]["includedir"]."/include_ladder_activity.php");
			} else{
				include($_SESSION["CFG"]["includedir"]."/include_jumpladder_activity.php");
			} ?>
		
	</div><!-- col -->
	</div> <!-- row -->
</div> <!-- container -->
		
		
<? } ?> <!--  has people siged up for a ladder-->



<script>

	
		
		/**
		 * Defaults the challenge dialog
		 */
		function defaultChallengeDialog(e, obj){

			allownewlines = true;
			var msg = document.getElementById("challengemessage");
			msg.value = 'Hello '+obj.firstname+',\n\nI would like to challenge you in the ladder.  Please let me know what time works best for you.\n\nSee you on the court.';
			
			
			document.getElementById("to_name").value =  obj.fullname;
			document.getElementById("to_email").value = obj.email;
			document.getElementById("challengeeid").value = obj.userid;
		}

		function disablenewlines(){
			allownewlines = false;	
		}
		
		function removeFromLadder(userid){
			document.deleteform.userid.value = userid;
			document.deleteform.submit();
		}
		
		function moveUpInLadder(userid){
			document.moveform.userid.value = userid;
			document.moveform.submit();
		}
		
		document.onkeypress = function (aEvent)
		{
		    if(!aEvent) aEvent=window.event;
		  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
		    if( key == 13 && !allownewlines) // enter key
		    {
		        return false; // this will prevent bubbling ( sending it to children ) the event!
		    }
		  	
		};

		function limitText(limitField, limitCount, limitNum) {
			if (limitField.value.length > limitNum) {
				limitField.value = limitField.value.substring(0, limitNum);
			} else {
				limitCount.value = limitNum - limitField.value.length;
			}
		}

</script>


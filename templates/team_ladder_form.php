<?php

//Initialize some important variables

$playerposition = 0;
$playerlocked = false;
$myteamid =0;
$userid = get_userid();
$teams = getTeamsForUser($userid);
$teamrows = mysqli_num_rows($teams);
$teamINClause = array();

//build in clause
for ($i = 0; $i < $teamrows; ++$i) {
	$team = mysqli_fetch_assoc($teams);
	$teamINClause[] = $team['teamid']; // teamid
}

if(count($teamINClause) > 0 ){
	$rawQuery = "SELECT ladder.ladderposition, ladder.locked, ladder.userid
						FROM tblClubLadder ladder
						WHERE ladder.userid IN (%s) 
						AND ladder.courttypeid = %s 
						AND ladder.clubid = %s 
						AND enddate IS NULL";
	$query = sprintf($rawQuery, implode(',',$teamINClause), $courttypeid, get_clubid() );

	$result = db_query($query);
	if( mysqli_num_rows($result) > 0 ){
		$ladderplayer = mysqli_fetch_array($result);
		$playerposition = $ladderplayer['ladderposition'];
		$playerlocked = $ladderplayer['locked']=="y" ? true : false;
		$myteamid = $ladderplayer['userid'];
	}
}

?>


<form name="deleteform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>"> 
	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>"> 
	<input type="hidden" name="cmd" value="removefromladder">
</form>

<form name="moveform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value=""> 
	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>"> 
	<input type="hidden" name="cmd" value="moveupinladder">
</form>

<form name="removechallenge" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value=""> 
	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>"> 
	<input type="hidden" name="cmd" value="removechallenge">
</form>


<?
$numrows = count($ladderplayers);
if($numrows ==0) { ?>

Nobody has signed up for the ladder yet.
<?  if(get_roleid()==2 || get_roleid==4){ ?>

Click
<span id="show"><a style="text-decoration: underline; cursor: pointer">here</a>
</span>
to add the first team now.


<? } else {?>

Ask your club pro to get the ball rolling with this.


<? } ?>

<? } else{ ?>

<div id="ladderControlPanel" style="padding-bottom: 5px;">
	<span class="normal"> 	<a href="javascript:newWindow('../help/club_ladders.html')">
			Ladders explained</a> <?  if(get_roleid()==2 || get_roleid()==4){ ?>
		| <span class="normal" id="show"><a
			style="text-decoration: underline; cursor: pointer">Add Team</a> </span>
			<? } ?>

	</span>
</div>


<table width="650">
	<tr>
		<td valign="top">

			<table cellspacing="0" cellpadding="20" width="300"
				class="generictable" id="formtable">
				<tr>
					<td class="clubid<?=get_clubid()?>th">
						<span class="whiteh1">
							<div align="center">
							<? pv($DOC_TITLE) ?>
							</div>
					</span>
					</td>
				</tr>

				<tr>
					<td>

						<table cellspacing="1" cellpadding="5" width="300"
							class="borderless">
							<tr>
								<td><span class="bold">Place</span>
								
								</th>
								<td><span class="bold">Name</span>
								
								</th>
							</tr>
							<?

							//Reset pointer


							$numrows = count($ladderplayers);
							for($i=0; $i< count($ladderplayers); ++$i){
									
								$playerarray = $ladderplayers[$i];
								$rc = (($numrows/2 - intval($numrows/2)) > .1) ? "lightrow_plain" : "darkrow_plain";

								?>

							<tr class="<?=$rc?>">
								<td><?=$playerarray['ladderposition']?> <? if($playerarray['going']=="up"){ ?>
									<img src="<?=$_SESSION["CFG"]["imagedir"]?>/raise.png"
									title="Won the last challenge match"> <? } else if($playerarray['going']=="down"){ ?>
									<img src="<?=$_SESSION["CFG"]["imagedir"]?>/fall.png"
									title="Lost the last challenge match"> <? } ?>
								</td>
								<td><?=$playerarray['firstplayer']?> and <?=$playerarray['secondplayer']?>
								<? if(get_roleid()==2 || get_roleid()==4){?> <a
									href="javascript:removeFromLadder(<?=$playerarray['userid']?>);"><img
										src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png"
										title="remove these chumpts from the ladder" /> </a> 
										<a href="javascript:moveUpInLadder(<?=$playerarray['userid']?>);">
											<img src="<?=$_SESSION["CFG"]["imagedir"]?>/gtk_media_forward_ltr.png" title="bump these guys up one spot"></a> <?}

										?> <? if($playerarray['locked']=='y') {?> <img
									src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"
									title="We are locked because we are currently being challenged or we are challenging another team. In any event, once the scores have been put in, the lock will be removed." />
									<? } else if(  !$playerlocked && isLadderChallengable( $playerposition, $playerarray['ladderposition'])  ){?>
									<span id="challenge-<?=$playerarray['ladderposition']?>"> <img
										src="<?=$_SESSION["CFG"]["imagedir"]?>/add_small.png"
										title="click me to challenge" />
								</span> <?} 



								?></td>
							</tr>

							<?
							$numrows = $numrows - 1;
							}  ?>


						</table>

					</td>
				</tr>
			</table>

		</td>
		<td valign="top">
			<div style="padding-left: 1em;">
			<? include($_SESSION["CFG"]["includedir"]."/include_doubles_ladder_activity.php"); ?>
			</div>

		</td>
	</tr>
</table>

			<? } ?>


<div id="challengedialog" class="yui-pe-content">

	<div class="bd">
		<form method="POST" action="<?=$ME?>">
			<label for="from_name">From:</label><input type="textbox"
				name="firstname" value="<?=get_userfullname()?>" disabled="disabled" />
			<label for="to_name">To:</label><input type="textbox" name="lastname"
				disabled="disabled" id="to_name" size="38" /> <label for="to_email">E-mail:</label><input
				type="textbox" name="email" disabled="disabled" id="to_email"
				size="50">

			<div class="clear"></div>
			<label for="textarea">Message:</label>
			<textarea id="challengemessage" name="textarea" cols="50" rows="10"
				onKeyDown="limitText(this.form.textarea,this.form.countdown,250);"
				onKeyUp="limitText(this.form.textarea,this.form.countdown,250);"></textarea>

			<div class="clear"></div>
			<span class="normalsm"> You have <input readonly type="text"
				name="countdown" size="3" value="85"> characters left.
			</span> <input type="hidden" name="cmd" value="challengeplayer"> <input
				type="hidden" name="challengeeid" id="challengeeid"> <input
				type="hidden" name="challengerid" id="challengerid"> <input
				type="hidden" name="firstplayer" id="firstplayer"> <input
				type="hidden" name="firstemail" id="firstemail"> <input
				type="hidden" name="secondplayer" id="secondplayer"> <input
				type="hidden" name="secondemail" id="secondemail">

			<script>
   
   	</script>

		</form>
	</div>
</div>

<div id="dialog1" class="yui-pe-content">


	<div class="bd">

		<form method="POST" action="<?=$ME?>">

			<div>
				<input id="name1" name="playeronename" type="text" size="30"
					class="form-autocomplete" /> <input id="id1" name="userid"
					type="hidden" /> and <input id="name2" name="playertwoname"
					type="text" size="30" class="form-autocomplete" /> <input id="id2"
					name="userid2" type="hidden" />
			</div>
			<div>
				Spot: <select name="placement">
				<?
				$selected = "";

				for ( $i = 1; $i<= count($ladderplayers)+1; ++$i){

					if($i == count($ladderplayers)+1 ){
						$selected = "selected=\"selected\"";
					}

					?>
					<option value="<?=$i?>" <?=$selected?>>
					<?=$i?>
					</option>
					<? } ?>

				</select>
			</div>
			<input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
			<input type="hidden" name="cmd" value="addtoladder"> <input
				type="hidden" name="courttypeid" value="<?=$courttypeid?>">
			<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
						
				 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name2',
						'target'=>'id2',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));		
           
                 ?>

                </script>



		</form>
	</div>
</div>

<script>

		var allownewlines = false;
		
		YAHOO.namespace("clubladder.container");

		YAHOO.clubladder.container.wait = 
            new YAHOO.widget.Panel("wait",  
                                            { width: "240px", 
                                              fixedcenter: true, 
                                              close: false, 
                                              draggable: false, 
                                              zindex:4,
                                              modal: true,
                                              visible: false
                                            } 
                                        );

	    YAHOO.clubladder.container.wait.setHeader("Loading, please wait...");
	    YAHOO.clubladder.container.wait.setBody("<img src=\"http://l.yimg.com/a/i/us/per/gr/gp/rel_interstitial_loading.gif\"/>");
	    YAHOO.clubladder.container.wait.render(document.body);
		
		YAHOO.util.Event.onDOMReady(function () {
			
			// Define various event handlers for Dialog
			var handleSubmit = function() {
				YAHOO.clubladder.container.wait.show();
				this.submit();
			};
			var handleCancel = function() {
				this.cancel();
			};
			var handleSuccess = function(o) {
				window.location.href=window.location.href;
			};
		
			var handleFailure = function(o) {
				alert("Submission failed: " + o.status);
			};
		
		    // Remove progressively enhanced content class, just before creating the module
		    YAHOO.util.Dom.removeClass("dialog1", "yui-pe-content");
		
			// Instantiate the Dialog
			YAHOO.clubladder.container.dialog1 = new YAHOO.widget.Dialog("dialog1", 
									{ width : "40em",
									  fixedcenter : true,
									  modal: true,
									  visible : false, 
									  constraintoviewport : true,
									  buttons : [ { text:"Add Team", handler:handleSubmit, isDefault:true } ]
									});
		
			YAHOO.clubladder.container.dialog1.setHeader('Add Team to Ladder');
		
			// Validate the entries in the form to require that both first and last name are entered
			YAHOO.clubladder.container.dialog1.validate = function() {
				var data = this.getData();
		
				if (data.userid == "" || data.userid2 == "" ) {
					alert("Please pick a name from the list.");
					return false;
				} 
				else if(data.userid == data.userid2){
					alert("Please pick seperate from the list.");
					return false;
				}
				else {
					return true;
				}
			};
		
			// Wire up the success and failure handlers
			YAHOO.clubladder.container.dialog1.callback = { success: handleSuccess,
								     failure: handleFailure };
			
			// Render the Dialog
			YAHOO.clubladder.container.dialog1.render();
		
			YAHOO.util.Event.addListener("show", "click", YAHOO.clubladder.container.dialog1.show, YAHOO.clubladder.container.dialog1, true);
			YAHOO.util.Event.addListener("show", "click", disablenewlines, false, true);
			
			
		});

			
		
		YAHOO.util.Event.onDOMReady(function () {

			
			// Define various event handlers for Dialog
			var handleSubmit = function() {
				YAHOO.clubladder.container.wait.show();
				this.submit();
			};
			var handleCancel = function() {
				this.cancel();
			};
			var handleSuccess = function(o) {
				window.location.href=window.location.href;
			};
			var handleFailure = function(o) {
				alert("Submission failed: " + o.status);
			};
		
		    // Remove progressively enhanced content class, just before creating the module
		    YAHOO.util.Dom.removeClass("challengedialog", "yui-pe-content");
		
			// Instantiate the Dialog
			YAHOO.clubladder.container.challengedialog = new YAHOO.widget.Dialog("challengedialog", 
									{ width : "30em",
									  fixedcenter : true,
									  visible : false, 
									  modal: true,
									  constraintoviewport : true,
									  buttons : [ { text:"Send Challenge", handler:handleSubmit, isDefault:true },
										      { text:"Cancel", handler:handleCancel } ]
									});
		
			
		
			// Wire up the success and failure handlers
			YAHOO.clubladder.container.challengedialog.callback = { success: handleSuccess,
								     failure: handleFailure };
			
			// Render the Dialog
			YAHOO.clubladder.container.challengedialog.render();

			<? 
	
			//build javascript for event listeners
			for($i=0; $i< count($ladderplayers); ++$i){
			
				$playerarray = $ladderplayers[$i];
				
				if( !$playerlocked && isLadderChallengable( $playerposition, $playerarray['ladderposition'])  ){
				?>
			personObj_<?=$playerarray['ladderposition']?>={firstplayer:'<?=rtrim($playerarray['firstplayer'])?>',firstemail:"<?=$playerarray['firstemail']?>",secondplayer:'<?=rtrim($playerarray['secondplayer'])?>',secondemail:"<?=$playerarray['secondemail']?>",userid:<?=$playerarray['userid']?>,myteamid:<?=$myteamid?>};	
			YAHOO.util.Event.addListener("challenge-<?=$playerarray['ladderposition']?>", "click", YAHOO.clubladder.container.challengedialog.show, YAHOO.clubladder.container.challengedialog, true);
			YAHOO.util.Event.addListener("challenge-<?=$playerarray['ladderposition']?>", "click", defaultChallengeDialog,personObj_<?=$playerarray['ladderposition']?>,true);
			
			<? } 
			
			 }
			?>
			
		});
		
		/**
		 * Defaults the challenge dialog
		 */
		function defaultChallengeDialog(e, obj){

			allownewlines = true;
			var msg = document.getElementById("challengemessage");
			msg.value = 'Hello,\n\nMy partner and I would like to challenge you and your partner on the doubles ladder.  Please let me know what time works best for you.\n\nSee you on the court.';
			
			
			document.getElementById("to_name").value =  obj.firstplayer+", "+obj.secondplayer;
			document.getElementById("to_email").value = obj.firstemail + ", "+obj.secondemail;
			document.getElementById("challengeeid").value = obj.userid;
			document.getElementById("challengerid").value = obj.myteamid;

			document.getElementById("firstplayer").value = obj.firstplayer;
			document.getElementById("firstemail").value = obj.firstemail;
			document.getElementById("secondplayer").value = obj.secondplayer;
			document.getElementById("secondemail").value = obj.secondemail;
			
		}

		function disablenewlines(){

			allownewlines = false;
			
		}
		
		function removeFromLadder(userid){
			YAHOO.clubladder.container.wait.show();
			document.deleteform.userid.value = userid;
			document.deleteform.submit();
		}
		
		function moveUpInLadder(userid){
			YAHOO.clubladder.container.wait.show();
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

<div style="height: 2em"></div>

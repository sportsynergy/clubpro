
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


Click
<span id="show"><a style="text-decoration: underline; cursor: pointer">here</a>
</span>
to add somone now.


<? } else {?>


Click
<a href="javascript:submitForm('addmetoladderform')">here</a>
to add your name now.


<? } ?>

<? } else{ ?>

<div id="ladderControlPanel" style="padding-bottom: 5px;">

	<?  if(get_roleid()==2 || get_roleid()==4){ ?>
		<span class="normal" id="show"><a
			style="text-decoration: underline; cursor: pointer"> Add Player</a> </span>
			| <span class="normal" id="showreportscores"><a
			style="text-decoration: underline; cursor: pointer"> Report Score</a></span>
	<? } ?>		
	<?  if(get_roleid()==1 && $playingInLadder){ ?>
			 <span class="normal" id="showreportscoresplayer"><a
			style="text-decoration: underline; cursor: pointer"> Report Score</a></span>
	<? } ?>

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
							<? pv($ladderdetails->name ) ?>
							</div>
					</span>
					</td>
				</tr>

				<tr>
					<td>

						<table cellspacing="1" cellpadding="5" width="350" class="sortable" >
							<tr>
								<td width="25%"><span class="bold">Place</span>
								
								</th>
								<td><span class="bold">Name</span>
								
								</th>
								<td><span class="bold">Ranking</span>
								
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


							<tr class="<?=$rc?>">
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

					</td>
				</tr>
			</table>

		</td>
		<td valign="top">
			<div style="padding-left: 1em;">
			
			<? 
			if ( isLadderRankingScheme() ){
			
				//include($_SESSION["CFG"]["includedir"]."/include_ladder_activity.php");
			} else{
				include($_SESSION["CFG"]["includedir"]."/include_jumpladder_activity.php");
			}
				 ?>
			</div>

		</td>
	</tr>
</table>

<? } ?>

<div id="reportscoredialog" class="yui-pe-content">

<div class="bd">
		<form method="POST" action="<?=$ME?>" autocomplete="off">
			
			<div>
				<input id="rsname1" name="" type="text" size="30"
					class="form-autocomplete" autocomplete="off"/> 
					<input id="rsid1" name="rsuserid" type="hidden" />

				<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'rsname1',
						'target'=>'rsid1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={rsname1}&userid=".get_userid()."&ladderid=$ladderid",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>
			</div>

			<div style="margin:10px"> 
				<span>Defeated</span>
			</div>
			<div>
				<input id="rsname2" name="" type="text" size="30"
					class="form-autocomplete" autocomplete="off"/> 
					<input id="rsid2" name="rsuserid2" type="hidden" />

					<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'rsname2',
						'target'=>'rsid2',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={rsname2}&userid=".get_userid()."&ladderid=$ladderid",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>

                </script>
			</div>

			<div style="margin:10px"> 
			<select name="score">
					<option value="3-2">3-2</option>
					<option value="3-1" selected>3-1</option>
					<option value="2-1" selected>2-1</option>
					<option value="3-0" selected>3-0</option>
				</select>
			</div>

			<div style="margin:10px"> 
				<span>At</span>
			</div>

			<div>
				<select name="hourplayed">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8" selected>8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="00">12</option>
				</select>

				<select name="minuteofday">
					<option value="00">00</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="45">45</option>
				</select>

				<select name="timeofday">
					<option value="AM">AM</option>
					<option value="PM" selected>PM</option>
				</select>
			</div>

		
				<input type="hidden" name="cmd" value="reportladderscore">
		</form>

</div>
	
</div>

<div id="reportscoredialogplayer" class="yui-pe-content">

<div class="bd">
		<form method="POST" action="<?=$ME?>" autocomplete="off">
			

		 <div style="margin:10px"> 
			<span class="label">Outcome:</span>
			<select name="outcome">
					<option value="defeated">Won</option>
					<option value="lostto">Lost</option>
				</select>
			</div>

			<div style="margin:10px"> 
			<span class="label">Opponent:</span>
				<input id="rsname3" name="" type="text" size="30"
					class="form-autocomplete" autocomplete="off"/> 
					<input id="rsid3" name="rsuserid3" type="hidden" />

				<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'rsname3',
						'target'=>'rsid3',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={rsname3}&userid=".get_userid()."&ladderid=$ladderid",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>

                </script>
			</div>

			<div style="margin:10px"> 
			<span class="label">Score:</span>
			<select name="score">
					<option value="3-2">3-2</option>
					<option value="3-1" selected>3-1</option>
					<option value="2-1" selected>2-1</option>
					<option value="3-0" selected>3-0</option>
				</select>
			</div>

			<div style="margin:10px"> 
			<span class="label"> Time </span>
				<select name="hourplayed">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8" selected>8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="00">12</option>
				</select>

				<select name="minuteofday">
					<option value="00">00</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="45">45</option>
				</select>

				<select name="timeofday">
					<option value="AM">AM</option>
					<option value="PM" selected>PM</option>
				</select>
			</div>

		
				<input type="hidden" name="cmd" value="reportladderscore">
		</form>

</div>
	
</div>


<div id="challengedialog" class="yui-pe-content">

	<div class="bd">
		<form method="POST" action="<?=$ME?>" autocomplete="off">
			<label for="from_name">From:</label><input type="textbox"
				name="firstname" value="<?=get_userfullname()?>" disabled="disabled" />
			<label for="to_name">To:</label><input type="textbox" name="lastname"
				disabled="disabled" id="to_name" /> <label for="to_email">E-mail:</label><input
				type="textbox" name="email" disabled="disabled" id="to_email"
				size="50">

			<div class="clear"></div>
			<label for="textarea">Message:</label>
			<textarea id="challengemessage" name="textarea" cols="50" rows="10"
				onKeyDown="limitText(this.form.textarea,this.form.countdown,250);"
				onKeyUp="limitText(this.form.textarea,this.form.countdown,250);"></textarea>

			<div class="clear"></div>
			<span class="normalsm"> You have <input readonly type="text"
				name="countdown" size="3" value="120"> characters left.
			</span> <input type="hidden" name="cmd" value="challengeplayer"> <input
				type="hidden" name="challengeeid" id="challengeeid">
			<script>
   
   	</script>

		</form>
	</div>
</div>

<div id="dialog1" class="yui-pe-content">

	<div class="bd">

		<form method="POST" action="<?=$ME?>" autocomplete="off">

			<div>
				<input id="name1" name="playeronename" type="text" size="30"
					class="form-autocomplete" autocomplete="off"/> 
					<input id="id1" name="userid" type="hidden" />
			</div>
			<div>
				Spot: <select name="placement">
				<?
				mysqli_data_seek($ladderplayers,0);


				$lastspot = mysqli_num_rows($ladderplayers) + 1;

				for ( $i = 1; $i<= mysqli_num_rows($ladderplayers)+1; ++$i){

					$selected = "";
					if($i == $lastspot ){
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
           
                 ?>

                </script>

		</form>
	</div>
</div>

<script>

		var allownewlines = false;
		
		YAHOO.namespace("clubladder.container");


		/*
		* Report score dialoge
		*/

		YAHOO.util.Event.onDOMReady(function () {
			
			// Define various event handlers for Dialog
			var handleSubmit = function() {
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
		    YAHOO.util.Dom.removeClass("reportscoredialog", "yui-pe-content");
			YAHOO.util.Dom.removeClass("reportscoredialogplayer", "yui-pe-content");
		
			// Instantiate the Dialog
			YAHOO.clubladder.container.reportscoredialog = new YAHOO.widget.Dialog("reportscoredialog", 
									{ width : "30em",
									  fixedcenter : true,
									  modal: true,
									  visible : false, 
									  constraintoviewport : true,
									  buttons : [ { text:"Record Score", handler:handleSubmit, isDefault:true } ]
									});
			YAHOO.clubladder.container.reportscoredialogplayer = new YAHOO.widget.Dialog("reportscoredialogplayer", 
								{ width : "30em",
									fixedcenter : true,
									modal: true,
									visible : false, 
									constraintoviewport : true,
									buttons : [ { text:"Record Score", handler:handleSubmit, isDefault:true } ]
								});
		
			YAHOO.clubladder.container.reportscoredialog.setHeader('Record Score');
			YAHOO.clubladder.container.reportscoredialogplayer.setHeader('Record Score');
		
			// Validate the entries in the form to require that both first and last name are entered
			YAHOO.clubladder.container.reportscoredialog.validate = function() {
				var data = this.getData();
		
				if (data.userid == "" ) {
					alert("Please pick a name from the list.");
					return false;
				} else {
					return true;
				}
			};
		
			// Wire up the success and failure handlers
			YAHOO.clubladder.container.reportscoredialog.callback = { success: handleSuccess,
								     failure: handleFailure };

			YAHOO.clubladder.container.reportscoredialogplayer.callback = { success: handleSuccess,
									failure: handleFailure };
			
			// Render the Dialog
			YAHOO.clubladder.container.reportscoredialog.render();
			YAHOO.clubladder.container.reportscoredialogplayer.render();
		
			YAHOO.util.Event.addListener("showreportscores", "click", YAHOO.clubladder.container.reportscoredialog.show, YAHOO.clubladder.container.reportscoredialog, true);
			YAHOO.util.Event.addListener("showreportscores", "click", disablenewlines, false, true);
			
			YAHOO.util.Event.addListener("showreportscoresplayer", "click", YAHOO.clubladder.container.reportscoredialogplayer.show, YAHOO.clubladder.container.reportscoredialogplayer, true);
			YAHOO.util.Event.addListener("showreportscoresplayer", "click", disablenewlines, false, true);
		});
		

		/*
		* Add player dialoge
		*/

		YAHOO.util.Event.onDOMReady(function () {
			
			// Define various event handlers for Dialog
			var handleSubmit = function() {
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
									{ width : "30em",
									  fixedcenter : true,
									  modal: true,
									  visible : false, 
									  constraintoviewport : true,
									  buttons : [ { text:"Add Player", handler:handleSubmit, isDefault:true } ]
									});
		
			YAHOO.clubladder.container.dialog1.setHeader('Add Player to Ladder');
		
			// Validate the entries in the form to require that both first and last name are entered
			YAHOO.clubladder.container.dialog1.validate = function() {
				var data = this.getData();
		
				if (data.userid == "" ) {
					alert("Please pick a name from the list.");
					return false;
				} else {
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
	    
		/*
		* Challenge dialoge
		*/

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
			//TODO make this better
			
			//Reset pointer
			 mysqli_data_seek($ladderplayers,0);
			
			//build javascript for event listeners
			while ( $playerarray = db_fetch_array($ladderplayers)){ 
			
				if( !$playerlocked && isLadderChallengable( $playerposition, $playerarray['ladderposition'])  ){
				?>
			personObj_<?=$playerarray['ladderposition']?>={firstname:"<?=$playerarray['firstname']?>",fullname:"<?=rtrim($playerarray['fullname'])?> ",email:"<?=$playerarray['email']?>",userid:<?=$playerarray['userid']?>};	
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

<div style="height: 2em"></div>

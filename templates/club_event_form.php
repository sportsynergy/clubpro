<?

$clubEvent = mysqli_fetch_array($clubEventResult);

?>


<form name="removemefromeventform" method="post" action="<?=$ME?>">
   <input type="hidden" name="userid" value="<?=get_userid()?>">
   <input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
   <input type="hidden" name="cmd" value="removefromevent">
</form>

<form name="addtoeventform" method="post" action="<?=$ME?>">
   <input type="hidden" name="userid" value="<?=get_userid()?>">
   <input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
   <input type="hidden" name="cmd" value="addtoevent">
</form>


<div>

<span class="club_event_title"><?=$clubEvent['name']?></span><br/>
<span class="italicnorm"><?=formatDateString($clubEvent['eventdate'])?></span>


<div style="padding-top: 15px; padding-bottom: 40px">
<span class="club_event_text">
<?=$clubEvent['description']?>
</span>
</div>


</div>



<? if( is_logged_in() ){


// if registerteam is enable pick the partner

if ($clubEvent['registerteam']=='y' ){   
	// Allow for teams

	?>

	<form method="POST" action="<?=$ME?>" name="registerteam" id="registerteam" autocomplete="off">
	<table width="400" class="generictable">
	<tr class="borderow" >
		<td class=clubid<?=get_clubid()?>th colspan="2">
		<span class="whiteh1">
          <div align="center">
            Sign Up
          </div>
		  </span>
		</td>
      </tr>
	<? if ( get_roleid()==2 || get_roleid()==4){ ?>
	  <tr>
			<td class="label">User</td>
			<td>
				<input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
				<input id="id1" name="userid" type="hidden" />
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
			</td>
		</tr>
		<? } else { ?>
			<input  name="userid" type="hidden" value="<?=get_userid()?>"/>
		<? } ?>
		<tr>
			<td class="label">Partner</td>
			<td>
				<input id="name2" name="playeronename" type="text" size="30" class="form-autocomplete" />
				<input id="id2" name="partnerid" type="hidden" />
					<script>
					<?
					$wwwroot =$_SESSION["CFG"]["wwwroot"] ;
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
			</td>
		</tr>
		<? if ($clubEvent['registerdivision']=='y' ){ ?>
		<tr>
			<td class="label">Division</td>
			<td>
				<select name="division">
					<option value="">--</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="C">C</option>
					<option value="D">D</option>
				</select>
			</td>
		</tr>
		<? } ?>
		<tr>
			<td>
			<input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
			<input type="hidden" name="cmd" value="addtoeventasteam">
			</td>
			<td><input type="button" name="submit" value="Sign up" id="submitbutton" </td>
		</tr>
	</table>
		
	</form>

	
<?
}?>

	
<? } ?>

<div id="dialog1" class="yui-pe-content">
<div class="bd">
<form method="POST" action="<?=$ME?>">
	<input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" autocomplete="off"/>
    <input id="id1" name="userid" type="hidden" />
	<input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
	<input type="hidden" name="cmd" value="addtoevent">
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
		<? if ($clubEvent['registerdivision']=='y' ){ ?>

			<span class="smallbold">Division<span>
			<select name="division">
				<option value="">--</option>
				<option value="A">A</option>
				<option value="B">B</option>
				<option value="C">C</option>
				<option value="D">D</option>
			</select>

	<? } ?>
</form>
</div>
</div>


<div style="padding-top: 55px">
<span class="biglabel">Who is coming </span>  

<?  
if (get_roleid()==1){

	
	if( $alreadySignedUp   ){ ?>
		<span class="normal"><a href="javascript:submitForm('removemefromeventform');">Take me out</a></span>
	<? } else if ($clubEvent['registerteam']!='y') { // team registration have a seperate signup form ?>
		<span class="normal"><a href="javascript:submitForm('addtoeventform');">Put me down</a></span>
	<? } ?>
	
	<? } elseif( get_roleid()==2 || get_roleid()==4)  { 

	// only provide this option for club events with single user reservations.
	if( $clubEvent['registerteam']!='y'){ ?>
		<span class="normal" id="show"><a style="text-decoration: underline; cursor: pointer">Add Player</a></span>
	<? } 


	} ?>


<div id="peoplelistpanel" style="padding-left: 1em; padding-top: 10px;">


<? 

if( mysqli_num_rows($clubEventParticipants)==0){ ?>
	<span class="normal">
	No one has signed up yet. 
	
	<? if( is_logged_in() ){ ?>
	 	Why don't you be the first one?
	<? } ?>
	</span >
	
<? }else{
	
	$count = 1;
	
	while($participant = mysqli_fetch_array($clubEventParticipants)){?>
		
		<? if ($clubEvent['registerteam']=='y' ){  ?>

		<div class="normal" style="white-space:nowrap;">
			<?=chop($participant['firstname'])?> <?=rtrim($participant['lastname'])?> - 
			<?=chop($participant['partner firstname'])?> <?=rtrim($participant['partner lastname'])?>
			<? if(!empty($participant['division'])){ ?>
				<span> (<?=$participant['division']?>)</span>
			<? } ?>
			
			<? if( get_roleid() ==2 || get_roleid() ==4){ ?>
			<a href="javascript:removeFromEvent(<?=$participant['userid']?>);">
	 			<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" >
			</a>
			<? } ?>
		</div>
	
		<? } else{ ?>
			<div class="normal" style="white-space:nowrap;">
				<?=chop($participant['firstname'])?> <?=rtrim($participant['lastname'])?>
				<? if(!empty($participant['division'])){ ?>
					<span> (<?=$participant['division']?>)</span>
				<? } ?>
				
				<? if( get_roleid() ==2 || get_roleid() ==4){ ?>
				<a href="javascript:removeFromEvent(<?=$participant['userid']?>);">
	 				<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" >
				</a>
				<? } ?>
			</div>
	
	   <? }?>
		
	  		
	
	<? } }?>


</div>

</div>


<script>


YAHOO.namespace("clubevent.container");

YAHOO.example.init = function () {

YAHOO.util.Event.onContentReady("registerteam", function () {

	document.getElementById('name1').setAttribute("autocomplete", "off");
	document.getElementById('name2').setAttribute("autocomplete", "off");

	var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
	oSubmitButton1.on("click", onSubmitButtonClicked);

});

} ();


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
	YAHOO.clubevent.container.dialog1 = new YAHOO.widget.Dialog("dialog1", 
							{ width : "30em",
							  fixedcenter : true,
							  modal: true,
							  visible : false, 
							  constraintoviewport : true,
							  buttons : [ { text:"Add Player", handler:handleSubmit, isDefault:true } ]
							});

	YAHOO.clubevent.container.dialog1.setHeader('Pick A Player');

	// Validate the user has selected the name from the drop down
	YAHOO.clubevent.container.dialog1.validate = function() {
		var data = this.getData();

		if (!data.userid ) {
			alert("Please pick a name from the list.");
			return false;
		} else {
			return true;
		}
	};

	// Wire up the success and failure handlers
	YAHOO.clubevent.container.dialog1.callback = { success: handleSuccess,
						     failure: handleFailure };
	
	// Render the Dialog
	YAHOO.clubevent.container.dialog1.render();


	document.getElementById('name1').setAttribute("autocomplete", "off");

	YAHOO.util.Event.addListener("show", "click", YAHOO.clubevent.container.dialog1.show, YAHOO.clubevent.container.dialog1, true);
	YAHOO.util.Event.addListener("hide", "click", YAHOO.clubevent.container.dialog1.hide, YAHOO.clubevent.container.dialog1, true);
});

function removeFromEvent(userid){
	document.removemefromeventform.userid.value = userid;
	document.removemefromeventform.submit();
	
}

document.onkeypress = function (aEvent)
{
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
};

function onSubmitButtonClicked(){
	submitForm('registerteam');
}

</script>
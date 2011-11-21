

<div id="dialog1" class="yui-pe-content">


<div class="bd">

<form method="POST" action="<?=$ME?>">
	<input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
             <input id="id1" name="userid" type="hidden" />
                <input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
   				<input type="hidden" name="cmd" value="addtoladder">
   				<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
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


YAHOO.namespace("clubladder.container");

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

	YAHOO.clubladder.container.dialog1.setHeader('Pick A Player');

	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.clubladder.container.dialog1.validate = function() {
		var data = this.getData();

		if (data.playeroneid == "" ) {
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
	YAHOO.util.Event.addListener("hide", "click", YAHOO.clubladder.container.dialog1.hide, YAHOO.clubladder.container.dialog1, true);
});

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
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
};

</script>

 <form name="addmetoladderform" method="post" action="<?=$ME?>">
     <input type="hidden" name="userid" value="<?=get_userid()?>">
     <input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
     <input type="hidden" name="cmd" value="addtoladder">
</form>

 <form name="deleteform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>">
	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
	<input type="hidden" name="cmd" value="removefromladder">
</form>

 <form name="moveform" method="post" action="<?=$ME?>">
	<input type="hidden" name="userid" value="<?=get_userid()?>">
	<input type="hidden" name="courttypeid" value="<?=$courttypeid?>">
	<input type="hidden" name="cmd" value="moveupinladder">
</form>


<? 
$numrows = mysql_num_rows($ladderplayers);
if($numrows ==0) { ?>

Nobody has signed up for the ladder yet.
	<?  if(get_roleid()==2 || get_roleid==4){ ?>
		 Click <span id="show"><a style="text-decoration: underline; cursor: pointer">here</a></span> to add somone now.

	<? } else {?>
		 Click <a href="javascript:submitForm('addmetoladderform')">here</a> to add your name now.

	<? } ?>

<? } else{ ?>

<div id="ladderControlPanel" style="padding-bottom: 5px;">
<span class="normalsm">
 <a href=javascript:newWindow('../help/club_ladders.html')> Ladders explained</a> |
<?  if(get_roleid()==2 || get_roleid()==4){ ?>
	 <span class="normalsm" id="show"><a style="text-decoration: underline; cursor: pointer">Add Player</a></span>
<? } else if($playingInLadder){ ?>
 <a href="javascript:submitForm('deleteform')">Take me out of this</a>
<? } else{ ?>
  <a href="javascript:submitForm('addmetoladderform')">Add me to this, please</a>
<? } 


?>

</span>
</div>


<table>
	<tr>
	<td valign="top">

<table cellspacing="0" cellpadding="20" width="400" class="generictable" >
 <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td >

     <table cellspacing="1" cellpadding="5" width="400" class="borderless" >
 		<tr>
                 <td ><span class="bold">Place</span></th>
                 <td><span class="bold">Name</span></th>
             </tr>
			 <?
				$numrows = mysql_num_rows($ladderplayers);
                while ( $playerarray = db_fetch_array($ladderplayers)){
                	$rc = (($numrows/2 - intval($numrows/2)) > .1) ? "lightrow_plain" : "darkrow_plain"; 
                 	
                   ?>
                 	
                 	<form name="playerform<?=$numrows?>" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
                            <input type="hidden" name="userid" value="<?=$playerarray['userid']?>">
                       		<input type="hidden" name="origin" value="ladder">
                     </form>
                                   
                 	<tr class="<?=$rc?>">
                 		<td ><?=$playerarray['ladderposition']?>
                 			<? if($playerarray['going']=="up"){ ?>
                 			<img src="<?=$_SESSION["CFG"]["imagedir"]?>/raise.png" title="has won at least two in a row" >
                 			<? } else if($playerarray['going']=="down"){ ?>
                 				<img src="<?=$_SESSION["CFG"]["imagedir"]?>/fall.png" title="has lost at least two in a row" >
                 			<? } ?>
                 		</td>
                 		<td >
                 		<a href="javascript:submitForm('playerform<?=$numrows?>')"><?=$playerarray['firstname']?> <?=$playerarray['lastname']?></a>
                 		<? if(get_roleid()==2 || get_roleid()==4){?>
                 		
                 		<a href="javascript:removeFromLadder(<?=$playerarray['userid']?>);">
 							<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" title="remove this chump from the ladder" />
						</a>
						
                 		<a href="javascript:moveUpInLadder(<?=$playerarray['userid']?>);">
                 			<img src="<?=$_SESSION["CFG"]["imagedir"]?>/gtk_media_forward_ltr.png" title="bump this guy up one spot" >
                 		</a>

						<?}?>
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
	<td valign="top" >
		<div style="padding-left: 3em;"><? include($_SESSION["CFG"]["includedir"]."/include_ladder_activity.php"); ?></div>
	 				  		
	</td>
</tr>
</table>

<? } ?>
<div style="height: 2em"></div>

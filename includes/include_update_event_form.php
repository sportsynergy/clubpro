<?
/*
 * 
 * The following variables are required before loading this form:
 * 
 * 		$userid
 * 		$time
 * 		$courtid
 * 		$reservation
 */


  $eventQuery = "SELECT events.eventid, events.playerlimit, reservations.locked FROM tblReservations reservations, tblEvents events
									WHERE reservations.time=$time AND reservations.courtid=$courtid
									AND events.eventid = reservations.eventid
									AND reservations.enddate IS NULL";
                 
                 $eventIdResult = db_query($eventQuery);
                 $eventArray = mysql_fetch_array($eventIdResult);
                 
?>

<script>

YAHOO.namespace("clubevent.container");

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

	YAHOO.clubevent.container.dialog1.setHeader('Add a player to this event');

	// Validate the user has selected a name from the drop down
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

	YAHOO.util.Event.addListener("show", "click", YAHOO.clubevent.container.dialog1.show, YAHOO.clubevent.container.dialog1, true);
	YAHOO.util.Event.addListener("hide", "click", YAHOO.clubevent.container.dialog1.hide, YAHOO.clubevent.container.dialog1, true);
});

function addToReservation(userid)
{

      document.manageform.action.value = 'add';
      document.manageform.userid.value = userid;
      document.manageform.submit();
}

function removeFromReservation(userid)
{
      document.manageform.action.value = 'remove';
      document.manageform.userid.value = userid;
      document.manageform.submit();
}

function enableEvent(updateEvent)
{

	if(updateEvent=="true"){
		document.entryform.events.disabled = ""; 
	}
	document.entryform.lock.disabled = "";  
}

function disableevent(disableIt)
{
  document.entryform.events.disabled = disableIt;
  document.entryform.lock.disabled = disableIt;
 	
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

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>


<form name="manageform" method="post" action="<?=$MEWQ?>">
<input type="hidden" name="action"/>
<input type="hidden" name="courtid" value="<?=$courtTypeArray['courtid']?>"/>
<input type="hidden" name="time" value="<?=$courtTypeArray['time']?>"/>
<input type="hidden" name="userid" />
<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>"/>
<input type="hidden" name="cmd" value="managecourtevent"/>
</form>

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center">
    		<? if($eventArray['locked']=='y'){ ?>
	    	 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	    	<?}?>
<? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>
    
		<table>
		 <? 
		 
		 $allowChangeEvent = true;
		 
		 if(  $eventArray['playerlimit'] > 0  ){ 
		 
		 	$eventplayerResult = getCourtEventParticipants($courtTypeArray['reservationid']);
		 	$amISignedup = isCourtEventParticipant($eventplayerResult);
		 	
		 	?>
				 <?
				 if(isInPast( $courtTypeArray['time']) || ($eventArray['locked']=='y' && get_roleid()==1)){
				 ?>
				 <tr>
				 	<td>
				 		<span class="label">Here is who signed up for this: </span>
				 		
				 	</td> 
				  </tr>
				 
				 <? } else { ?>
				 <tr>
				 	<td>
				 		<span class="label">Here is who is coming to this: </span>
				 		<? if($eventArray['playerlimit'] != mysql_num_rows($eventplayerResult )
				 		|| $amISignedup 
				 		) { ?>
				 		<span class="normalsm">
				 		
				 		<? if( get_roleid() ==2 || get_roleid() ==4) {?>
				 			<span class="normalsm" id="show"><a style="text-decoration: underline; cursor: pointer">Add Player</a></span>
				 		<?} else {?>
				 		
						 		<?if($amISignedup){ ?>
						 			<a href="javascript:removeFromReservation(<?=get_userid()?>);">Take me out</a></span>
						 		<? }else{ ?>
						 			<a href="javascript:addToReservation(<?=get_userid()?>);">Put me down, I will be there!</a></span>
						 		<? } ?>
						 <? } ?>
				 		</span>
				 		
				 		<? } ?>
				 	</td> 
				  </tr>
				  
				 <?
				 }
				 
				 	if( mysql_num_rows($eventplayerResult) > 0 ){ 
				 		
				 		//If anyone has signed up, don't let the administrator change the event
				 		$allowChangeEvent = false;
				 		
						while($player = mysql_fetch_array($eventplayerResult)){ ?>
							<tr>
								<td style="padding: 1px">
								<?=$player['firstname']?> <?=$player['lastname']?>
								<? if( get_roleid() ==2 || get_roleid() ==4){ ?>
								  <span class="normalsm">
								  <a href="javascript:removeFromReservation(<?=$player['userid']?>);">
								 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/recyclebin_empty.png" >
								</a></span>
								<? }?>
								
								</td>
							</tr>
						
		
				 	<? } ?>
				 
				
				 <? }  else{ ?>
				 	
				 	<tr>
						 	<td>
						 		<span class="normal">
						 			<?=isInPast( $courtTypeArray['time'])?"There were no takers":"Noone has signed up yet "?>
						 		</span>
						 	</td> 
						  </tr>
				 	 
				 	  <?	}?>
				 	 
				 <?	}   ?>

			
       		<? 
       		//Only display this to administrators
       		if( get_roleid()==2 || get_roleid==4){  
       		
       			if(isReoccuringReservation($time, $courtid)){ ?>
       			
       			<? if( $eventArray['playerlimit'] > 0  ) {?>
	       		<tr>
	       			<td><hr/></td>
	       		</tr>	
	       		<? } ?>
		 	<tr><td>
		       
       		This is a reoccuring event.  What do you want to do?<br><br>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked>&nbsp; Cancel just this occurrence <br>	
       		<input type="radio" name="cancelall" value="9" onclick="disableevent(this.checked)" >&nbsp; Cancel all occurrences <br>
       			
       		<? } else{ ?>
       			<? if( $eventArray['playerlimit'] > 0  ) {?>
	       		<tr>
	       			<td><hr/></td>
	       		</tr>	
	       		<? } ?>
       		<tr><td>
       		<input type="radio" name="cancelall" value="3" onclick="disableevent(this.checked)" checked> &nbsp;Cancel the event <br>	
       		<? } ?>
       	 
       	 
	   	 <input type="radio" name="cancelall" value="10" onclick="javascript:enableEvent('<?=$allowChangeEvent?"true":"false"?>')">&nbsp;Update this event occurrence
			<select name="events" disabled>
               

                <?
                //Get Club Players
                 $eventDrpDown = get_site_events(get_siteid());
                 
                 while($row = mysql_fetch_row($eventDrpDown)) {

					$selected = "";
                      
				 	if($row[0] == $eventArray['eventid']){
	                    $selected = "selected";
	                 }
					 
					 echo "<option value=\"$row[0]\" $selected>$row[1]</option>\n";
                     unset($selected);
                     
                 }            
       ?>
       </select>
         </td>
       </tr>
       			<?
                	// Set set if its locked	
       				$selected="";
                 	if($eventArray['locked']=='y'){
                 		$selected = "checked=checked"; 
                 	}
                 	
                 ?>
       	<tr>
	    	<td colspan="2">
	    		<input type="checkbox" name="lock"  <?=$selected?> disabled="disabled"/>
	    		<span class="normal">Lock reservation</span>
	    		
	    	</td>
	    </tr>
        
      <tr>
       <td>
	       <br>
	       <input type="button" name="submit" id="submitbutton" value="Update Court Reservation">
	       <input type="button" id="cancelbutton" value="Back to Court Reservations">
	       <input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>">
	       <input type="hidden" name="courtid" value="<?=$courtid?>">
	       <input type="hidden" name="time" value="<?=$time?>">
       </td>
      </tr> 
      <? } else{ ?>
       <tr style="height: 30px">
       	<td></td>
       </tr>
       <tr>
       	<td>
       		<input type="button" id="cancelbutton" value="Back to Court Reservations" >
	   </td>
       </tr>
         
      	
     <?  }?>       
	</table>
	
	</td>
	</tr>
	
      
  
</table>	


</form>

<div id="dialog1" class="yui-pe-content">


<div class="bd">

<form method="POST" action="<?=$ME?>">
	<input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
             <input id="id1" name="userid" type="hidden" />
   				<input type="hidden" name="cmd" value="managecourtevent">
   				<input type="hidden" name="action" value="add">
   				<input type="hidden" name="reservationid" value="<?=$courtTypeArray['reservationid']?>">
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


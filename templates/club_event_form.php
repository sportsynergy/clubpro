<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/

$clubEvent = mysql_fetch_array($clubEventResult);

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
							  buttons : [ { text:"Submit", handler:handleSubmit, isDefault:true } ]
							});

	YAHOO.clubevent.container.dialog1.setHeader('Pick A Player');

	// Validate the entries in the form to require that both first and last name are entered
	YAHOO.clubevent.container.dialog1.validate = function() {
		var data = this.getData();

		if (data.playeroneid == "" ) {
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

function removeFromEvent(userid){
	document.removemefromeventform.userid.value = userid;
	document.removemefromeventform.submit();
	
}

</script>

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



		

<div id="leftPanel" style="float:left; width: 330px; margin-right: 20px">

<span class="biglabel"><?=$clubEvent['name']?></span><br/>
<span class="italitcsm"><?=formatDateString($clubEvent['eventdate'])?></span>
<div style="background-color: #DBDDDD;">
<span class="normal">
	<?=$clubEvent['description']?>
</span>
</div>


</div>


<div id="rightPanel" style="float: left;">
<span class="biglabel" >Who is coming </span> 
<? if( is_logged_in() ){

	 if( get_roleid()==2 || get_roleid()==4){ ?>
		 <span class="normalsm" id="show"><a style="text-decoration: underline; cursor: pointer">Add Player</a></span>
	<? } else { 
	
	 	if( $alreadySignedUp ){ ?>
			<span class="normalsm"><a href="javascript:submitForm('removemefromeventform');">Take me out</a></span>
		<? }else{ ?>
			<span class="normalsm"><a href="javascript:submitForm('addtoeventform');">Put me down</a></span>
		<? } ?>
	
	<? } ?>
	
<? } ?>

<div id="dialog1" class="yui-pe-content">


<div class="bd">

<form method="POST" action="<?=$ME?>">
	<input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
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

	
</form>
</div>
</div>

<div id="peoplelistpanel" style="padding-left: 2em;">
<ul id="peoplelist" class="whoscoming" >

<? 

if( mysql_num_rows($clubEventParticipants)==0){ ?>
	
	<li>No one has signed up yet. 
	
	<? if( is_logged_in() ){ ?>
	 	Why don't you be the first one?
	<? } ?>
	
	</li>
<? }else{
	while($participant = mysql_fetch_array($clubEventParticipants)){?>
	<li><?=$participant['firstname']?> <?=$participant['lastname']?> 
	
	<? if( get_roleid() ==2 || get_roleid() ==4){ ?>
	  <span class="normalsm"><a href="javascript:removeFromEvent(<?=$participant['userid']?>);">x</a></span>
	<? }?>
	</li>
	<? } }?>



</ul>

</div>

</div>







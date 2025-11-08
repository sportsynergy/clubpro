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

<span class="bigbanner"><?=$clubEvent['name']?></span><br/>
<span class="italicnorm"><?=formatDateString($clubEvent['eventdate'])?></span>

<div class="mt-3 mb-5">
	<?=$clubEvent['description']?>
</div>

</div>

<? if( is_logged_in() ){


// if registerteam is enable pick the partner
if ($clubEvent['registerteam']=='y' ){   ?>

	<form method="POST" action="<?=$ME?>" name="registerteam" id="registerteam" autocomplete="off">
	
	<? if ( get_roleid()==2 || get_roleid()==4){ ?>
	 
		<label for="name1" class="form-label">User</label>
		<input id="name1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" style="width:50%;"/>
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
			
		<? } else { ?>
			<input  name="userid" type="hidden" value="<?=get_userid()?>"/>
		<? } ?>
		<label for="name2" class="form-label">Partner</label>
		<input id="name2" name="playeronename" type="text" size="30" class="form-control form-autocomplete" style="width:50%;"/>
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
			
		<? if ($clubEvent['registerdivision']=='y' ){ ?>
			<label for="name2" class="form-label">Division</label>
			<select name="division" class="form-select" style="width:50%;">
					<option value="--">--</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="C">C</option>
					<option value="D">D</option>
					<option value="60">60+</option>
					<option value="Centuries">Centuries</option>
					<option value="Father & Son - Open">Father & Son (Open)</option>
					<option value="Father & Son - U19">Father & Son (Junior U19)</option>
				</select>
			
		<? } ?>
		
		<input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
		<input type="hidden" name="cmd" value="addtoeventasteam">
		<div class="mt-2 mb-3">
  			<button type="submit" class="btn btn-primary" id="submitbutton" onclick="onSubmitButtonClicked()">Sign up</button>
      	</div>	
	</form>
<? }?>	
<? } ?>




<div>
<span class="biglabel">Who is coming </span>  

<?  if( $alreadySignedUp   ){ ?>
		<a href="javascript:submitForm('removemefromeventform');">Take me out</a>
	<? } else if ($clubEvent['registerteam']!='y') { // team registration have a seperate signup form ?>
		
		<? if ($clubEvent['registerdivision']=='y' ){ ?>
			<span>I'll play in the </span> the  
				<form name="addtoeventformwithdivision" method="post" action="<?=$ME?>" style="display: inline;">
				<select name="division" >
					<option value="">--</option>
					<option value="A">A</option>
					<option value="B">B</option>
					<option value="C">C</option>
					<option value="D">D</option>
					<option value="60">60+</option>
				</select>
				<input type="hidden" name="userid" value="<?=get_userid()?>">
				<input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
				<input type="hidden" name="cmd" value="addtoevent">
				division. 
		</form>
		<a href="javascript:submitForm('addtoeventformwithdivision');">Put me down</a>
		<? } else { ?>
			<a href="javascript:submitForm('addtoeventform');">Put me down</a>
		<? }  ?>

	<? } ?>
	



<div id="peoplelistpanel">

<? 

if( mysqli_num_rows($clubEventParticipants)==0){ ?>

	No one has signed up yet. 
	
	<? if( is_logged_in() ){ ?>
	 	Why don't you be the first one?
	<? } ?>
	
	
<? }else{
	
	$count = 1;
	
	while($participant = mysqli_fetch_array($clubEventParticipants)){?>
		
		<? if ($clubEvent['registerteam']=='y' ){  ?>

		<div >
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
			<div>
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

document.getElementById('name1').setAttribute("autocomplete", "off");
document.getElementById('name2').setAttribute("autocomplete", "off");

function submitAddToEventForm()
{ 
	var form = eval("document." + theForm);
	form.submit();
}

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
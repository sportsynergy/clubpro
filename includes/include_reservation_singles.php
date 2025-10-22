
<form name="singlesform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
 
	<div class="mb-3">
		<label for="name1" class="form-label">Player One</label>
    	<input type="text" size="35" id="name1" name="playeronename" class="form-control form-autocomplete" aria-label="Player One">
		<input id="id1" name="playeroneid" type="hidden" />
		<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
	</div>

      
	<div class="mb-3">
		<label for="name2" class="form-label">Player Two</label>
    	<input type="text" size="35" id="name2" name="playertwoname" class="form-control form-autocomplete" aria-label="Player Two">
		<input id="id2" name="playertwoid" type="hidden" />
		<script>
                <?php
					$wwwroot = $_SESSION["CFG"]["wwwroot"];
					pat_autocomplete(array(
						'baseUrl' => "$wwwroot/users/ajaxServer.php",
						'source' => 'name2',
						'target' => 'id2',
						'className' => 'autocomplete',
						'parameters' => "action=autocomplete&name={name2}&userid=" . get_userid() . "&courtid=$courtid&siteid=" . get_siteid() . "&clubid=" . get_clubid() . "",
						'progressStyle' => 'throbbing',
						'minimumCharacters' => 3,
					));
					?>

        </script>
	</div>

	 <div class="mb-3">
           <div id="emailHelp" class="form-text">
            To book a reservation, type in the name of the each player and select from the list of club members.  If you don't
        know who will be playing yet, don't worry, just fill in what you know now.  We will ask you about how to advertise for any open spots on the 
        next screen.
            </div>
        </div>


    <div class="mb-3">
		<label for="matchtype" class="form-label">Match Type</label>
		<select class="form-select" aria-label="Match Type" name="matchtype" id="matchtype" onchange="disablePlayerDropDownWithSoloSelection(this);onlyAllowLessonReoccuring(this)">
			 <? if( isSiteBoxLeageEnabled() && isLadderRankingScheme()){ ?>
          <option value="1">Box League</option>
          <? } ?>
          <? if ( isPointRankingScheme() ) {?>
          <option value="2">Challenge</option>
          <? } ?>
          <? if( get_roleid() ==2 || get_roleid()==4) {?>
          <option value="4">Lesson</option>
          <? } ?>
          <option value="0" selected>Practice</option>
          <option value="5">Solo</option>
        </select>

	 <div class="form-text"> 
		 <? if( get_roleid() == 2){ ?>
        If you want to put yourself down as available for a lesson, leave your name in as Player One, leave Player Two blank and set
        the matchtype as Lesson.
        <? } ?>
	 	For more infomation on match types, click 
        <a href="javascript:newWindow('../help/squash-matchtypes.html')">here</a>.
		
	</div>

	</div>
	  
	<? if( get_roleid()==2 || get_roleid()==4) {?>
	<div class="mb-3">
			<label for="matchtype" class="form-label">Repeat</label>
			<select class="form-select" aria-label="Match Type" name="matchtype" id="repeat" onchange="disableSinglesOptions(this)" disabled="true">
				<option value="norepeat">None</option>
				<option value="daily">Daily</option>
				<option value="weekly">Weekly</option>
				<option value="biweekly">Bi-Weekly</option>
				<option value="monthly">Monthly</option>
			
			    </select>
	<? is_object($errors) ? err($errors->repeat) : ""?>
	</div>

	<div class="mb-3">
			<label for="matchtype" class="form-label">Frequency</label>
			<select class="form-select" aria-label="Match Type" name="frequency" id="frequency" disabled="true">
				<option value="">Select Option</option>
				<option value="">----------------------------</option>
				<option value="week">For a Week</option>
				<option value="month">For a Month</option>
				<option value="year">For a Year</option>
			  <? is_object($errors) ? err($errors->duration) : ""?>
			</select>	

	<? is_object($errors) ? err($errors->repeat) : ""?>
	</div>

	<? } ?>

	

	<? if($variableDuration == 'y' || ($variableDuration_admin == 'y' && get_roleid() == 2)){ ?>
	
	<div class="mb-3">
		<label for="duration" class="form-label">Duration</label>
		<select class="form-select" aria-label="Duration" name="duration">
				<?
				$timetonext = $nexttime - $time; 
				
				if($timetonext >= 900 || $nexttime == null ){ ?>
					<option value=".25">15 Minutes</option>
				<?}
				
				if($timetonext >= 1800 || $nexttime == null ){ ?>
					<option value=".5">30 Minutes</option>
				<?}
					
				if($timetonext >= 2700 || $nexttime == null){ ?>
					<option value=".75">45 Minutes</option>
				<?}

				if($timetonext >= 3000 || $nexttime == null){ ?>
					<option value=".83334">50 Minutes</option>
				<?}
						
				if($timetonext >= 3600 || $nexttime == null){ ?>
					<option selected="selected" value="1">60 Minutes</option>
				<?}

				if($timetonext >= 5400 || $nexttime == null ){ ?>
					<option value="1.5">90 Minutes</option>
				<? } ?>
					
			</select>
	
	
	<? }  else { ?>
		<div>
			<input type="hidden" name="duration" value="<?=$reservation_duration  ?>">
		</div>
		
	<? }  ?>
    
	
		
	</div>

    

    <? if( get_roleid()==2 || get_roleid() ==4){ ?>
   
	<div class="form-check">
	  <input class="form-check-input" type="checkbox" name="lock" />
	  <label for="lock" class="form-label">Lock Reservation</label>
	</div>

	
	
    <?}?>

	<div class="mt-5">
		<button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
	</div>

    
  <input type="hidden" name="courttype" value="singles">
  <input type="hidden" name="eventid" value="0">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
</form>
<script>

	document.getElementById('name1').setAttribute("autocomplete", "off");
	document.getElementById('name2').setAttribute("autocomplete", "off");


function cancelCourt(){
	 var submitForm = document.createElement("FORM");
	 document.body.appendChild(submitForm);
	 submitForm.method = "POST";
	 submitForm.action = "<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php";
	 submitForm.submit();

}

function disablePlayerDropDownWithSoloSelection(matchtype)
{
     if(matchtype.value == "5"){
          document.singlesform.playertwoname.disabled = true;
          document.singlesform.playertwoname.disabled = true;
     }
     else{
     	document.singlesform.playertwoname.disabled = "";
     	document.singlesform.playertwoname.disabled = "";
     }

}

function defaultform() {

	document.singlesform.playeronename.value = "<?= addslashes(get_userfullname()) ?>";
	document.singlesform.playeroneid.value = <?= get_userid() ?>;
	document.singlesform.playertwoname.focus();
	
    <? if(get_roleid() == 1){ ?>
		document.singlesform.playeronename.disabled=true;
    	
   <? } ?>
    	
   
    
}

defaultform();

</script> 

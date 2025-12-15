
<form name="doubles_reservation_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
  

<div class="container">
  <div class="row">
    <div class="col">
      <div class="mb-3">
        <label for="dname1" class="form-label">Player One</label>
        <input id="dname1" name="playeronename" type="text" size="30" class="form-autocomplete form-control" 
             <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userfullname()?>" disabled
              <? }else{ ?>
              	value="<? pv($frm["playeronename"]) ?>"
              <? } ?>
              />
        <input id="did1" name="playeroneid" type="hidden" 
              <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userid()?>"
             <? } else {?>
             	value="<? pv($frm["playeroneid"]) ?>"
             <? } ?>
             />
        <? is_object($errors) ? err($errors->playeronename) : ""?>
        <script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'dname1',
						'target'=>'did1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={dname1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>
        </div>
    </div>

    <div class="col">
            <div class="mb-3">
              <label for="dname2" class="form-label">Player Two</label>
              <input id="dname2" name="playertwoname" type="text" size="30" class="form-autocomplete form-control" onchange="javascript:unsetplayertwo();" value="<? pv($frm["playertwoname"]) ?>"/>
        <? is_object($errors) ? err($errors->playertwoname) : ""?>
        
        <input id="did2" name="playertwoid" type="hidden" value="<? pv($frm["playertwoid"]) ?>" />
        <script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'dname2',
							'target'=>'did2',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={dname2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
	                </script>
          </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
        <div class="mb-3">
        <label for="dname3" class="form-label">Player Three</label>
         <input id="dname3" name="playerthreename" type="text" size="30" class="form-autocomplete form-control" onchange="javascript:unsetplayerthree();" value="<? pv($frm["playerthreename"]) ?>"/>
          <? is_object($errors) ? err($errors->playerthreename) : ""?>
        
          <input id="id3" name="playerthreeid" type="hidden" value="<? pv($frm["playerthreeid"]) ?>"/>
          <script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'dname3',
							'target'=>'id3',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={dname3}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
	          </script>
        </div> <!-- mb-3  -->
    </div> <!-- col  -->

    <div class="col">
        <div class="mb-3">
          <label for="name4" class="form-label">Player Four </label>
        <input id="name4" name="playerfourname" type="text" size="30" class="form-autocomplete form-control" onchange="javascript:unsetplayerfour();" value="<? pv($frm["playerfourname"]) ?>"/>
        <? is_object($errors) ? err($errors->playerfourname) : ""?>
        <input id="id4" name="playerfourid" type="hidden" value="<? pv($frm["playerfourid"]) ?>"/>
        <script>
            <?
              $wwwroot = $_SESSION["CFG"]["wwwroot"];
              pat_autocomplete( array(
              'baseUrl'=> "$wwwroot/users/ajaxServer.php",
              'source'=>'name4',
              'target'=>'id4',
              'className'=>'autocomplete',
              'parameters'=> "action=autocomplete&name={name4}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
              'progressStyle'=>'throbbing',
              'minimumCharacters'=>3,
              ));
            
              ?>
            </script>
        </div> <!-- mb-3  -->
    </div> <!-- col  -->
  </div>  <!-- row  -->
  </div> <!-- container  -->



        
    <div class="mb-3">
        <div id="emailHelp" class="form-text">
        To book a reservation, type in the name of the each player and select from the list of club members.  If you don't
    know who all four players will be yet, don't worry, just fill in what you know now.  We will ask you about how to advertise for any open spots on the 
    next screen.
        </div>
    </div>
            
                  
                  
	
	<? if($variableDuration == 'y' || ($variableDuration_admin == 'y' && get_roleid() == 2)){ ?>
    <div class="mb-3">
      <label for="duration" class="form-label">Duration</label>
      <select name="duration" class="form-select" aria-label="Duration">
        <? 
        $timetonext = $nexttime - $time;

				if($timetonext == 900 ){ ?>
					<option value=".25">15 Minutes</option>
				<?}
				 if($timetonext >= 1800 ){ ?>
					<option value=".5">30 Minutes</option>
				<?}
				if($timetonext >= 2700 || $nexttime == null){ ?>
					<option value=".75">45 Minutes</option>
				<?}

				if($timetonext >= 3600 || $nexttime == null ){ ?>
					<option value="1" selected="selected">60 Minutes</option>
				<?}
        if($timetonext >= 4500 || $nexttime == null ){ ?>
          <option value="1.25">75 Minutes</option>
        <?}
				if($timetonext >= 5400 || $nexttime == null ){ ?>
					<option value="1.5">90 Minutes</option>
         <?}
        if($timetonext >= 7200 || $nexttime == null ){ ?>
          <option value="2">2 Hours</option>
         <?}
        if($timetonext >= 10800 || $nexttime == null ){ ?>
          <option value="3">3 Hours</option>
        <? } ?>
				
			</select>
        </div>
  <? }  else { ?>
    <div>
      <input type="hidden" name="duration" value="<?=$reservation_duration  ?>">
    </div>
	<? }  ?>


  <div class="mb-3">
    <label for="matchtype" class="form-label">Match Type</label>
    <select name="matchtype" class="form-select" aria-label="Match Type">
          <option value="0" selected>Practice</option>
          <option value="0">Tournament</option>
          <? if ( isPointRankingScheme() ) {?>
          <option value="2">Challenge</option>
          <? } ?>
          <? if( get_roleid() ==2 || get_roleid()==4) {?>
          <option value="4">Lesson</option>
          <? } ?>
        </select>

         <div id="matchtypehelp" class="form-text"> 
          For more infomation on match types, click 
          <a href="javascript:newWindow('../help/squash-matchtypes.html')">here</a>
        </div>
  </div>

 <? if( get_roleid()==2 || get_roleid() ==4){ ?>
  <div class="form-check">
    
	  <input class="form-check-input" type="checkbox" name="lock" />
	  <label for="lock" class="form-label">Lock Reservation</label>
	</div>
  <? }?>

   <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
    <button type="button" class="btn btn-secondary" onclick="onEventCancelButtonClicked()">Cancel</button>
  </div> 

  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="courttype" value="doubles">
  <input type="hidden" name="partner" value="needfour">
  <input type="hidden" name="team" value="needtwo">
  <input type="hidden" name="action" value="create">
</form>



<script language="Javascript">

  document.getElementById('dname1').focus();
  document.getElementById('dname1').setAttribute("autocomplete", "off");
  document.getElementById('dname2').setAttribute("autocomplete", "off");
  document.getElementById('dname3').setAttribute("autocomplete", "off");
  document.getElementById('dname4').setAttribute("autocomplete", "off");

function onDoublesSubmitButtonClicked(){

	submitForm('doubles_reservation_form');
}

 function onDoublesCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script> 
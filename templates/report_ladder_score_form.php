
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php" >
            <? pv($ladderdetails->name ) ?>
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Report Ladder Score</li>
  </ol>
</nav>

<form method="POST" action="<?=$ME?>" autocomplete="off">

<div class="mb-3">


    <input id="rsname1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" autocomplete="off"  <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userfullname()?>" disabled
              <? }else{ ?>
              	value="<? pv($frm["playeronename"]) ?>"
              <? } ?>/> 
    <input id="rsid1" name="rsuserid" type="hidden"  <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userid()?>"
             <? } else {?>
             	value="<? pv($frm["rsuserid"]) ?>"
             <? } ?>/>
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
    <? is_object($errors) ? err($errors->winner) : ""?>
</div>
			
<div class="mb-3"> 
    <label class="form-label">Defeated</label>
</div>

<div class="mb-3">
        <input id="rsname2" name="" type="text" size="30" class="form-control form-autocomplete" autocomplete="off"/> 
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
        <? is_object($errors) ? err($errors->loser) : ""?>
</div>

<div class="mb-3">
        <select name="score" class="form-select">
            <option value="3-2">3-2</option>
            <option value="3-1" selected>3-1</option>
            <option value="2-1" selected>2-1</option>
            <option value="3-0" selected>3-0</option>
        </select>
</div>

<div class="mb-3"> 
    <label class="form-label">At</label>
</div>	

<div id="allinoneline" style="display: inline">

    <select name="hourplayed" class="form-control d-inline-block" style="width: 120px">
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

    <select name="minuteofday" class="form-control d-inline-block" style="width: 120px">
        <option value="00">00</option>
        <option value="15">15</option>
        <option value="30">30</option>
        <option value="45">45</option>
    </select>

    <select name="timeofday" class="form-control d-inline-block" style="width: 120px">
        <option value="AM">AM</option>
        <option value="PM" selected>PM</option>
    </select>

</div>
				
<div class="mb-3">
    <div class="form-check">
    <input class="form-check-input" type="checkbox" name="league" id="league" checked>
    <label class="form-check-label" for="league">
       Box League Match
    </label>
</div>
</div>
				
  <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">
        Report Score
    </button>
     <input type="hidden" name="submitme" value="submitme">
     <input id="ladderid" name="ladderid" value="<?=$ladderid?>" type="hidden" />
</div>
				
</form>

<script type="text/javascript">

document.getElementById('rsname1').setAttribute("autocomplete", "off");
document.getElementById('rsname2').setAttribute("autocomplete", "off");

function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 


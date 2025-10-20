


<form name="resource_reservation_form" method="post" action="<?=$ME?>" autocomplete="off">
  

<div class="mb-3">
  <label for="resourcename" class="form-label">Name</label>
  <input type="text" id="resourcename" class="form-control" placeholder="<?=$courtname ?>" disabled>
</div>

<div class="mb-3">
  <label for="duration" class="form-label">Duration</label>
  <select class="form-select" aria-label="Duration" name="duration" id="duration">
     <?
        $timetonext = $nexttime - $time; 
        
        if($timetonext == 900 ){ ?>
          <option value=".25">15 Minutes</option>
        <?}
        
        if($timetonext >= 1800 || $nexttime == null ){ ?>
          <option value=".5">30 Minutes</option>
        <?}
          
        if($timetonext >= 2700 || $nexttime == null){ ?>
          <option value=".75">45 Minutes</option>
        <?}
            
        if($timetonext >= 3600 || $nexttime == null){ ?>
          <option value="1">60 Minutes</option>
        <? } 

        if($timetonext >= 5400 || $nexttime == null){ ?>
          <option value="1.5">90 Minutes</option>
        <? } 

        if($timetonext >= 7200 || $nexttime == null){ ?>
          <option value="2">2 Hours</option>
        <? } 
        
        if($timetonext >= 10800 || $nexttime == null){ ?>
          <option value="3">3 Hours</option>
        <? } ?> 
      </select>
</div>

<div class="mb-3">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
  </div>

    
 
  <input type="hidden" name="courttype" value="resource">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="matchtype" value="0">
</form>


<script language="JavaScript">


function onResourceSubmitButtonClicked(){
  submitForm('resource_reservation_form');
}

function onResourceCancelButtonClicked(){

  parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
}

</script>
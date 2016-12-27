<?php

/**
 * Class and Function List:
 * Function list:
 * Classes list:
 */
?>
<script language="JavaScript">

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("resource_formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("resource_submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onResourceSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("resource_cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onResourceCancelButtonClicked);

    });

} ();

function onResourceSubmitButtonClicked(){
  submitForm('resource_reservation_form');
}

function onResourceCancelButtonClicked(){

  parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
}

</script>

<form name="resource_reservation_form" method="post" action="<?=$ME?>" autocomplete="off">
  <table cellspacing="10" cellpadding="0" width="400" class="tabtable" id="resource_formtable">
    
    <tr>
     <td class="label" >Name</td>
    <td><?=$courtname ?></td>
    </tr>
    <tr> 
     <td class="label">Duration:</td>
     <td>
      <select name="duration">
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
      </td>
    </tr>
    
    <tr>
      <td colspan="2" style="height: 20px;"></td>
    </tr>

    <tr>
      <td colspan="2"><input type="button" name="submit" value="Make Reservation" id="resource_submitbutton">
        <input type="button" value="Cancel" id="resource_cancelbutton"></td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="courttype" value="resource">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
  <input type="hidden" name="matchtype" value="0">
</form>

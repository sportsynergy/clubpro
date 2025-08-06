<script language="Javascript">

 function disable(disableIt)
{
        document.skill_entryform.opponentplayer1.disabled = disableIt;
        document.skill_entryform.opponentplayer2.disabled = disableIt;
}

function toggle()
{

        if(document.skill_entryform.starttime.disabled != "" ){
             document.skill_entryform.starttime.disabled = "";
             document.skill_entryform.endtime.disabled = "";
        }else{
              document.skill_entryform.starttime.disabled = "true";
             document.skill_entryform.endtime.disabled = "true";
        }
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	document.skill_entryform.submit();
}

</script>

<form name="skill_entryform" method="post" action="<?=$ME?>" autocomplete="off">
  
<div style="padding-bottom: 20px"> 
  <a href="javascript:newWindow('../help/skill_range_policies_explained.html')">Help with Skill Range Policies</a>  
 </div>
  
<table cellspacing="0" cellpadding="20" width="550" class="generictable" id="formtable">
    <tr>
      <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
        <div align="center">
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
    <tr>
      <td><table cellpadding="5" cellspacing="2">
          <tr>
            <td class="label">Name:</td>
            <td><input type="text" name="name" value="<?=$skillRangePolicy['policyname']?>" maxlength="60" size="30">
              <? is_object($errors) ? err($errors->name) : ""?>
            
            </td>
          </tr>
          <tr>
            <td class="label">Description:</td>
            <td><textarea cols="25" rows="4" name="description" ><?=$skillRangePolicy['description']?>
</textarea>
            <? is_object($errors) ? err($errors->description) : ""?>
            </td>
          </tr>
          <tr>
            <td class="label">Skill Range:</td>
            <td><select name="skillrange">
                <option value="">Select Skill Range</option>
                <option value="">-----------------------------</option>
                <option value=".25" <?=$skillRangePolicy['skillrange']==".25" ? "selected" : "" ?> >.25</option>
                <option value=".5" <?=$skillRangePolicy['skillrange']==".5" ? "selected" : "" ?>  >.5</option>
                <option value=".75" <?=$skillRangePolicy['skillrange']==".75" ? "selected" : "" ?>  >.75</option>
                <option value="1" <?=$skillRangePolicy['skillrange']=="1" ? "selected" : "" ?>  >1</option>
              </select>
            <? is_object($errors) ? err($errors->skillrange) : ""?>
            </td>
          </tr>
          <tr>
            <td class="label">Court:</td>
            <td><select name="courtid">
                <? is_object($errors) ? err($errors->courtid) : ""?>
                <option value="">Select Court</option>
                <option value="">-----------------------------</option>
                <option value="all" <?=$skillRangePolicy['courtid'] == null ? "selected" : "" ?>>All Courts</option>
                <option value="">-----------------------------</option>
                <?
                      //Get Club Players
                 $courtsDropDown = get_sitecourts_dropdown(get_siteid());

                 while($row = mysqli_fetch_array($courtsDropDown)) { ?>
                <option value="<?=$row['courtid']?>" <?=$skillRangePolicy['courtid']==$row['courtid'] ? "selected" : "" ?> >
                <?=$row['courtname']?>
                </option>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td class="label">Day of Week:</td>
            <td><select name="dow">
                <? is_object($errors) ? err($errors->dow) : ""?>
                <option value="">Select Day</option>
                <option value="">-----------------------------</option>
                <option value="all" <?=$skillRangePolicy['dayid'] == null ? "selected" : ""?>>All Days</option>
                <option value="">-----------------------------</option>
                <?
                      //Get Club Players
                 $dowList = get_dow_dropdown();

                 while($row = mysqli_fetch_array($dowList)) { ?>
                <option value="<?=$row['dayid']?>" <?=$skillRangePolicy['dayid'] == $row['dayid'] ? "selected" : ""?>>
                <?=$row['name']?>
                </option>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td class=label>Specifiy Window: </td>
            <td><input type="checkbox" name="reservationwindow" value="yes" onclick="toggle(this.checked)" <?=$skillRangePolicy['starttime']!=null ? "checked" : ""?>>
              <? is_object($errors) ? err($errors->window) : ""?>
            </td>
          </tr>
          <tr>
            <td class="label">Start Time:</td>
            <td><select name="starttime" <?= $skillRangePolicy['starttime']==null ? "disabled" : "" ?>>
                <? is_object($errors) ? err($errors->starttime) : ""?>
                <option value="">Select Start Time</option>
                <option value="">--------------------</option>
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                <option value="<?=$hourtime?>" <?=$skillRangePolicy['starttime']==$hourtime ? "selected" : ""?>>
                <?=$hourtime?>
                </option>
                <? } ?>
              </select></td>
          </tr>
          <tr>
            <td class="label">End Time:</td>
            <td><select name="endtime" <?= $skillRangePolicy['endtime']==null ? "disabled" : "" ?>>
                <? is_object($errors) ? err($errors->endtime) : ""?>
                <option value="">Select End Time</option>
                <option value="">--------------------</option>
                <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                <option value="<?=$hourtime?>" <?=$skillRangePolicy['endtime']==$hourtime ? "selected" : ""?>>
                <?=$hourtime?>
                </option>
                <? } ?>
              </select>
              <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid']?>">
              <input type="hidden" name="submitme" value="submitme"></td>
          </tr>
          <tr>
            <td><input type="button" name="submit" value="<?=$buttonLabel?>" id="submitbutton"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<div style="height: 2em;"></div>
<div> <span class="normal"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#skill" > << Back to Reservation Policies </a> </span> </div>

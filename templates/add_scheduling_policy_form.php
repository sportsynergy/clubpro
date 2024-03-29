<script language="Javascript">

 function disable(disableIt)
{
        document.addschedulepolicyform.opponentplayer1.disabled = disableIt;
        document.addschedulepolicyform.opponentplayer2.disabled = disableIt;
}

function toggle()
{

        if(document.addschedulepolicyform.starttime.disabled != "" ){
             document.addschedulepolicyform.starttime.disabled = "";
             document.addschedulepolicyform.endtime.disabled = "";
        }else{
              document.addschedulepolicyform.starttime.disabled = "true";
             document.addschedulepolicyform.endtime.disabled = "true";
        }
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	document.addschedulepolicyform.submit();
}

</script>

<div align="center">
  <div style="text-align:left">
    <form name="addschedulepolicyform" method="post" action="<?=$ME?>" autocomplete="off">
      <div style="padding-bottom: 20px"> 
      <a href="javascript:newWindow('../help/scheduling_policies_explained.html')">Help with Scheduling Policies</a>
      </div>
      
      
      <table cellspacing="0" cellpadding="20" width="550" class="generictable" id="formtable">
        <tr class="borderow">
          <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
            <div align="center">
              <? pv($DOC_TITLE) ?>
            </div>
            </span></td>
        </tr>
        <tr>
          <td ><table width="550" cellpadding="5" cellspacing="2">
              <tr>
                <td class="label">Name:</td>
                <td><input type="text" name="name" maxlength="30" size="30" value="<?=$schedulePolicy['policyname']?>">
                  <?err($errors->name)?></td>
              </tr>
              <tr>
                <td class="label">Description:</td>
                <td><textarea cols="25" rows="4" name="description"><?=$schedulePolicy['description']?>
</textarea></td>
              </tr>
              <tr>
                <td class="label">Reservation Limit:</td>
                <td><select name="limit">
                    <?
            		for($i = 0; $i < 10 ; ++$i){ ?>
                    <option value="<?=$i?>" <?=$i == $schedulePolicy['schedulelimit']? "selected" : "" ?>>
                    <?=$i?>
                    </option>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td class="label">Court:</td>
                <td><select name="courtid">
                    <?err($errors->courtid)?>
                    <option value="">Select Court</option>
                    <?
            if($schedulePolicy['courtid'] == null){
            	$allcourts="selected";
            }
            
            ?>
                    <option value="">-----------------------------</option>
                    <option value="all" <?=$allcourts?>>All Courts</option>
                    <option value="">-----------------------------</option>
                    <?
                 $courtsDropDown = get_sitecourts_dropdown(get_siteid());

                 while($row = mysqli_fetch_array($courtsDropDown)) { ?>
                    <option value="<?=$row['courtid']?>" <?=$schedulePolicy['courtid']==$row['courtid']? "selected": ""?>>
                    <?=$row['courtname']?>
                    </option>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td class="label">Day of Week:</td>
                <td><select name="dow">
                    <?err($errors->dow)?>
                    <option value="">Select Day</option>
                    <?
            if($schedulePolicy['dayid'] == null){
            	$alldays="selected";;
            }
           
            ?>
                    <option value="">-----------------------------</option>
                    <option value="all" <?=$alldays?>>All Days</option>
                    <option value="">-----------------------------</option>
                    <?
                 $dowList = get_dow_dropdown();

                 while($row = mysqli_fetch_array($dowList)) { ?>
                    <option value="<?=$row['dayid']?>" <?=$schedulePolicy['dayid']==$row['dayid'] ? "selected": "" ?>>
                    <?=$row['name']?>
                    </option>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td class="label">Allow Looking for Match:</td>
                <td><select name="allowlooking">
                    <option value="yes" >Yes</option>
                    <option value="no" <?=$schedulePolicy['allowlooking']=='n' ? "selected" : ""?>>Nope</option>
                  </select></td>
              </tr>
              <tr>
                <td class="label">Allow Back to Back Reservations:</td>
                <td><select name="allowback2back">
                    <option value="yes" >Yes</option>
                    <option value="no" <?=$schedulePolicy['allowback2back']=='n' ? "selected" : ""?>>Nope</option>
                  </select></td>
              </tr>
              <tr>
                <td class="label">Specifiy Window: </td>
                <td><input type="checkbox" name="reservationwindow" value="yes" onclick="toggle(this.checked)" <?=$schedulePolicy['starttime']!=null ? "checked" : ""?>></td>
              </tr>
              <tr>
                <td class=label>Start Time:</td>
                <td><select name="starttime" <?= $schedulePolicy['starttime']==null ? "disabled" : "" ?>>
                    <?err($errors->starttime)?>
                    <option value="">Select Start Time</option>
                    <option value="">--------------------</option>
                    <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                    <option value="<?=$hourtime?>" <?=$schedulePolicy['starttime']==$hourtime ? "selected" : ""?>>
                    <?=$hourtime?>
                    </option>
                    <? } ?>
                  </select></td>
              </tr>
              <tr>
                <td class="label" >End Time:</td>
                <td><select name="endtime" <?= $schedulePolicy['endtime']==null ? "disabled" : "" ?>>
                    <?err($errors->endtime)?>
                    <option value="">Select End Time</option>
                    <option value="">--------------------</option>
                    <?
                for($i=1; $i < 24; ++$i){ 
					$hourtime = sprintf("%02d" , $i ).":00:00"
                	?>
                    <option value="<?=$hourtime?>" <?=$schedulePolicy['endtime']==$hourtime ? "selected" : ""?>>
                    <?=$hourtime?>
                    </option>
                    <? } ?>
                  </select>
                  <input type="hidden" name="policyid" value="<?=$schedulePolicy['policyid']?>">
                  <input type="hidden" name="submitme" value="submitme"></td>
              </tr>
              <tr>
                <td>
					<input type="button" name="submit" value="<?=$buttonLabel?>" id="submitbutton">
				</td>
              </tr>
            </table></td>
        </tr>
      </table>
    </form>
    <div style="height: 2em;"></div>
    <div> <span class="normal"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#schedule"> &lt;&lt; Back to Reservation Policies </a> </span> </div>
  </div>
</div>

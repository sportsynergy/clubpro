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


function onSubmitButtonClicked(){
	document.skill_entryform.submit();
}

</script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="mb-3"> 
  <a href="javascript:newWindow('../help/skill_range_policies_explained.html')">Help with Skill Range Policies</a>  
 </div>

<form name="skill_entryform" method="post" action="<?=$ME?>" autocomplete="off">
  

  
 <div class="mb-3">
    <label for="name" class="form-label">Name:</label>
    <input class="form-control" id="name" name="name" type="text" aria-label="Skill Range Policy Name" value="<?=$skillRangePolicy['policyname']?>">
    <? is_object($errors) ? err($errors->name) : ""?>
  </div>

   <div class="mb-3">
    <label for="description" class="form-label">Description:</label>
    <textarea cols="25" rows="4" class="form-control" id="description" name="description" ><?=$skillRangePolicy['description']?></textarea>
    <? is_object($errors) ? err($errors->description) : ""?>
  </div>

   <div class="mb-3">
      <label for="skillrange" class="form-label">Skill Range:</label>
      <select name="skillrange" class="form-select" id="skillrange">
                <option value="">Select Skill Range</option>
                <option value="">-----------------------------</option>
                <option value=".25" <?=$skillRangePolicy['skillrange']==".25" ? "selected" : "" ?> >.25</option>
                <option value=".5" <?=$skillRangePolicy['skillrange']==".5" ? "selected" : "" ?>  >.5</option>
                <option value=".75" <?=$skillRangePolicy['skillrange']==".75" ? "selected" : "" ?>  >.75</option>
                <option value="1" <?=$skillRangePolicy['skillrange']=="1" ? "selected" : "" ?>  >1</option>
              </select>
      <? is_object($errors) ? err($errors->skillrange) : ""?>
    </div>

  <div class="mb-3">
      <label for="courtid" class="form-label">Court</label>
      <select name="courtid" class="form-select" id="courtid">
                
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
            </select>
            <? is_object($errors) ? err($errors->courtid) : ""?>
    </div>

    <div class="mb-3">
      <label for="dow" class="form-label">Day of Week</label>
      <select name="dow" class="form-select" id="dow">
                
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
              </select>
              <? is_object($errors) ? err($errors->dow) : ""?>
    </div>

    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="yes" id="reservationwindow" onclick="toggle(this.checked)" <?=$skillRangePolicy['starttime']!=null ? "checked" : ""?> name="reservationwindow">
      <label class="form-check-label" for="reservationwindow">Specifiy Window</label>
      <? is_object($errors) ? err($errors->window) : ""?>
</div>

    <div class="mb-3">
      <label for="starttime" class="form-label">Start Time</label>
      <select name="starttime" <?= $skillRangePolicy['starttime']==null ? "disabled" : "" ?> class="form-select" id="starttime">
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
              </select>
              <? is_object($errors) ? err($errors->starttime) : ""?>
    </div>       
         
     <div class="mb-3">
      <label for="endtime" class="form-label">End Time</label>
      <select name="endtime" <?= $skillRangePolicy['endtime']==null ? "disabled" : "" ?> class="form-select" id="endtime">
                
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
              <? is_object($errors) ? err($errors->endtime) : ""?>
    </div>     
          
         
             
  <div class="mt-5">
      <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()" name="submit"  id="submitbutton">
          <?=$buttonLabel?>
        </button>
         <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid']?>">
        <input type="hidden" name="submitme" value="submitme"></td>
      </div>
  </form>    
           
         

</form>

<div class="mt-3"> 
  <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#skill" > << Back to Reservation Policies </a> 
</div>

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


function onSubmitButtonClicked(){
	document.addschedulepolicyform.submit();
}

</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

 <div class> 
    <a href="javascript:newWindow('../help/scheduling_policies_explained.html')">Help with Scheduling Policies</a>
</div>

<form name="addschedulepolicyform" method="post" action="<?=$ME?>" autocomplete="off">
     
  <div class="mb-3">
    <label for="name" class="form-label">Name:</label>
    <input class="form-control" type="text" name="name" id="name" maxlength="30" size="30" aria-label="Scheduling Policy Name" value="<?=$schedulePolicy['policyname']?>">
    <? is_object($errors) ? err($errors->name) : ""?>
  </div>     

   <div class="mb-3">
    <label for="description" class="form-label">Description:</label>
    <textarea cols="25" rows="4" class="form-control" id="description" name="description" ><?=$schedulePolicy['description']?></textarea>
    <? is_object($errors) ? err($errors->description) : ""?>
  </div>    

  <div class="mb-3">
      <label for="limit" class="form-label">Reservation Limit</label>
      <select name="limit" class="form-select" id="limit">
                    <?
            		for($i = 0; $i < 10 ; ++$i){ ?>
                    <option value="<?=$i?>" <?=$i == $schedulePolicy['schedulelimit']? "selected" : "" ?>>
                    <?=$i?>
                    </option>
                    <? } ?>
                  </select>
      <? is_object($errors) ? err($errors->limit) : ""?>
    </div>


    <div class="mb-3">
      <label for="courtid" class="form-label">Court</label>
      <select name="courtid" class="form-select" id="courtid">
                   
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
                  </select>
       <? is_object($errors) ? err($errors->courtid) : ""?>
    </div>
              
     <div class="mb-3">
      <label for="limit" class="form-label">Day of Week</label>
      <select name="dow">
                    
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
                  </select>
      <? is_object($errors) ? err($errors->dow) : ""?>
    </div>    
    
    
    <div class="mb-3">
      <label for="allowlooking" class="form-label">Allow Looking for Match</label>
      <select name="allowlooking" class="form-select" id="allowlooking">
                    <option value="yes" >Yes</option>
                    <option value="no" <?=$schedulePolicy['allowlooking']=='n' ? "selected" : ""?>>Nope</option>
                  </select>
      <? is_object($errors) ? err($errors->allowlooking) : ""?>
    </div>

    <div class="mb-3">
      <label for="allowback2back" class="form-label">Allow Back to Back Reservations</label>
      <select name="allowback2back" class="form-select" id="allowback2back">
                    <option value="yes" >Yes</option>
                    <option value="no" <?=$schedulePolicy['allowback2back']=='n' ? "selected" : ""?>>Nope</option>
                  </select>
      <? is_object($errors) ? err($errors->allowback2back) : ""?>
    </div>
               
              
     <div class="form-check">
      <input class="form-check-input" type="checkbox" value="yes" id="reservationwindow" onclick="toggle(this.checked)" <?=$schedulePolicy['starttime']!=null ? "checked" : ""?> name="reservationwindow">
      <label class="form-check-label" for="reservationwindow">Specifiy Window</label>
      <? is_object($errors) ? err($errors->window) : ""?>
</div>      
             
   <div class="mb-3">
      <label for="starttime" class="form-label">Start Time</label>
      <select name="starttime" <?= $schedulePolicy['starttime']==null ? "disabled" : "" ?> class="form-select" id="starttime">
                    <? is_object($errors) ? err($errors->starttime) : ""?>
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
                  </select>
      <? is_object($errors) ? err($errors->starttime) : ""?>
    </div>           
                
<div class="mb-3">
      <label for="endtime" class="form-label">End Time</label>
      <select name="endtime" <?= $schedulePolicy['endtime']==null ? "disabled" : "" ?> class="form-select" id="endtime">
                    
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
      <? is_object($errors) ? err($errors->endtime) : ""?>
    </div>  

   <div class="mt-5">
    <input type="hidden" name="policyid" value="<?=$schedulePolicy['policyid']?>">
    <input type="hidden" name="submitme" value="submitme">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()" name="submit"  id="submitbutton">
        <?=$buttonLabel?>
     </button>      
  </div>
                  
  </form>
    
  <div class="mt-3"> 
    <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#schedule"> &lt;&lt; Back to Reservation Policies </a>
  </div>


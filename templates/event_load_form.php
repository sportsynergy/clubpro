<?
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/
?>


<script language="Javascript">

function disableEventOptions(repeat)
{
        
         if(repeat.value == "norepeat"){
            
             document.entryform.reoccurringduration.disabled = true;

        }
        else{
        	document.entryform.reoccurringduration.disabled = "";
        }
        
}

function updateDurationOptions(){

	if(document.entryform.repeat.value == 'daily'){
	
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For Two Days", "twodays");
		addOption(document.entryform.reoccurringduration,"For Three Days", "threedays");
		addOption(document.entryform.reoccurringduration,"For Four Days", "fourdays");
		addOption(document.entryform.reoccurringduration,"For Five Days", "fivedays");
		addOption(document.entryform.reoccurringduration,"For Six Days", "sixdays");
		addOption(document.entryform.reoccurringduration,"For a Week", "week");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else if(document.entryform.repeat.value == 'weekly'){
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else if(document.entryform.repeat.value == 'monthly'){
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
		
	}
	else{
		removeAllOptions(document.entryform.reoccurringduration);
		addOption(document.entryform.reoccurringduration,"Select Option", "");
		addOption(document.entryform.reoccurringduration,"----------------------------", "");
		addOption(document.entryform.reoccurringduration,"For a Week", "week");
		addOption(document.entryform.reoccurringduration,"For a Month", "month");
		addOption(document.entryform.reoccurringduration,"For a Year", "year");
	}

}

function removeAllOptions(selectbox){
	var i;
	for(i=selectbox.options.length-1;i>=0;i--)
	{
	selectbox.remove(i);
	}
}

function addOption(selectbox,text,value )
{
	var optn = document.createElement("OPTION");
	optn.text = text;
	optn.value = value;
	selectbox.options.add(optn);
}

</script>

<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="0" width="550" class="generictable">
 
 
 <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th height=60>
    	<span class="whiteh1">
    		<div align="center">Event Reservation</div>
    	</span>
    </td>
 </tr>
 <tr>
    <td>

     <table cellspacing="10" cellpadding="0" width="500">
        <tr>
             <td height="20"></td>
        </tr>
        <tr>
            <td class="label">
            	<span class="required">* </span>
            	Event:
            </td>
            <td><select name="eventid">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>

                 <?  //Get Club Players
               $query = "SELECT eventid, eventname
                          FROM tblEvents
                          WHERE siteid = ".get_siteid()."";

                // run the query on the database
                $result = db_query($query);


                 while($row = mysql_fetch_row($result)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
                <?err($errors->eventid)?>
                </select>

                </td>
       </tr>
       <tr>
       		<td class="label">
       			<span class="required">*</span> 
       			First Reservation:
       		</td>
       		<td>
  
       			<select name="starttime">
       				<option value="">Select Option</option>
       				<option value="">----------------------------</option>
       				<? 
       				for($i=0; $i<count($reservationWindowArray); ++$i){ ?>
       					
	       				<option value="<?=$reservationWindowArray[$i]?>"><?=gmdate("g:i",$reservationWindowArray[$i])?></option>
	       				
	       		  <? } ?>
       
       				
       			</select>
       		</td>
       </tr>
       <tr>
       		<td class="label">
       			<span class="required">* </span>
       			Last Reservation:
       		</td>
       		<td>
       			<select name="endtime">
       				<option value="">Select Option</option>
       				<option value="">----------------------------</option>
       				<? 
       				for($i=0; $i<count($reservationWindowArray); ++$i){ ?>
       					
	       				<option value="<?=$reservationWindowArray[$i]?>"><?=gmdate("g:i",$reservationWindowArray[$i])?></option>
	       				
	       		  <? } ?>
       			</select>
       		</td>
       </tr>
       <tr>
       	<td class="label">Repeat:</td> 
       	<td>
       			<select name="repeat" onchange="updateDurationOptions();disableEventOptions(this);">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="norepeat">None</option>
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="biweekly">Bi-Weekly</option>
                <option value="monthly">Monthly</option>
                <?err($errors->repeat)?>
                </select>
       </td>
       </tr>
       <tr>
         <td><span class="label">Duration:</span> </td>
           <td><select name="reoccurringduration">
                <option value="">Select Option</option>
                <option value="">----------------------------</option>
                <option value="week">For a Week</option>
                <option value="month">For a Month</option>
                <option value="year">For a Year</option>
                <?err($errors->reoccuringduration)?>
                </select>

                </td>

       </tr>
       <tr>
       	<td colspan="2">
	       	<span class="italitcsm">
		       	If you want to make a block of reservations that reoccur, use the Repeat and Duration dropdowns.  For example, if you have lessons  from 
		       	2:00 - 5:00 that will be held every week for a month, set the 'First Reservation' to 2:00 and the 'Last Reservation' to 4:00 then set Repeat to 'Weekly' 
		       	and Duration for 'For A Month'. And so on.
			</span>
       	</td>
       </tr>
       <tr>
       		<td colspan="2" class="normal">
       			<input type="checkbox" name="cancelconflicts"> 
						<span class="normal">
							Remove any existing reservations. By leaving this checkbox unchecked any
       						reservations that were already out there will be left alone.
						</span>
						<br/><br/>
       		</td>
       		
       </tr>
     <tr>
    	<td>
    		<input type="checkbox" name="lock" />
    		<span class="normal">Lock reservation</span>
    	</td>
    </tr>

       <tr>
            <td colspan="2">
            	<span class="required">*</span>
            	<span class="normalsm">indicates a required field.</span><br/>
            <br/>
            </td>
        </tr>
       <tr>
           <td colspan="2">
           		<input type="submit" name="submit" value="Submit">
           		<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
           </td>
    	</tr>
 </table>

</td>
</tr>
</table>
<?

// The court reservation span is how long a resevation is made for, this is a 
// fixed period.  Here the duration is derived by taking the difference
// between the first and the 

$courtduration = "";
if(count($reservationWindowArray)>1){
	$courtduration = $reservationWindowArray[1]-$reservationWindowArray[0];
}
?>

<input type="hidden" name="courtduration" value="<?=$courtduration?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="time" value="<?=$time?>">

</form>


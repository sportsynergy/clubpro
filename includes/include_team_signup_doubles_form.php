<?

/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $
*/

?>

<script language="Javascript">

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}

//please keep these lines on when you copy the source
//made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
if (document.getElementById) {
for (var sch = 0; sch < dform.length; sch++) {
 if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
}
}
return true;
}


</script>


<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
        <tr>
         <td class="label">Partner:</td>
         <td>
             <input id="partnername" name="partnername" type="text" size="30" class="form-autocomplete" />
             <input id="partnerid" name="partnerid" type="hidden"  />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'partnername',
						'target'=>'partnerid',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={partnername}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>
        
		</td>

       </tr>
      
    <tr>
    	<td colspan="2" class="italitcsm">
    		To search for a name type in the player's first or last name.  To just put your name in and look for a partner, leave this box empty and click 'Make Reservation'.  If you are looking for one or more players you will be 
    		prompted for how you would like to advertise this match on the next screen.
    		<br><br>
    	</td>
    </tr>
 	<tr>
           <td class="normal" colspan="2">
				<input type="submit" name="Submit" value="Make Reservation">
	            <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
	       </td>   
    </tr>


 </table>


 </td>
 </tr>

</table>

<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="courttype" value="doubles">
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="action" value="addteam">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">


	         

</form>
<?php
/*
 * $LastChangedRevision: 675 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-02-27 14:46:32 -0800 (Fri, 27 Feb 2009) $

*/
?>


<script language="Javascript">
<!--
// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

//-->

function disablePlayerDropDownWithSoloSelection(matchtype)
{
        if(matchtype.value == "5"){
             document.entryform.name.disabled = true;
        }
        else{
        	document.entryform.name.disabled = "";
        }
  
}

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}

</script>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

        <tr>
        <td>

<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">



<table cellspacing="0" cellpadding="0" width="550" >
 <tr>
    <td class=clubid<?=get_clubid()?>th height="60"><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
           <? if(get_roleid()==4 || get_roleid()==2 || $reservationType == 1){ ?>
            <tr>
                    <td>
                            <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
                                    <tr>
                                             <? if (get_roleid()==2 || get_roleid()==4){?>
                                              <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=event"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/eventTabOff.gif" border="0"></a></td>
                                             <?} ?>
                                              <? if (($reservationType == 2 && (get_roleid()==2 || get_roleid()==4)) || $reservationType == 1){ ?>
                                             <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=doubles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/doublesTabOff.gif" border="0"></a></td>
                                               <?} ?>
                                               <? if (($reservationType == 0 && (get_roleid()==2 || get_roleid()==4)) || $reservationType == 1){ ?>
                                            <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=singles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/singlesTabOn.gif" border="0"></a></td>
                                              <?} ?>
                                            <td width=100%></td>
                                    </tr>
                            </table>
                    </td>
            </tr>
            <? } ?>
    <td class="generictable">

     <table cellspacing="10" cellpadding="0" width="550">

        <tr>
             <td height="20"></td>
        </tr>
        <tr>
            <td class="biglabel">Opponent:</td>
            <td>
            
             
             <input id="name" name="name" type="text" size="30" class="form-autocomplete"  />
             <input id="id" name="opponent" type="hidden" size="30" />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name',
						'target'=>'id',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>

                </td>
       </tr>
		<tr>
			<td colspan="3" class="italitcsm">
			To search for a name type in the player's first or last name.  To look for a player, leave this box empty.  If you are looking for a player you will be 
    		prompted for how you would like to advertise this match on the next screen.
			</td>
		</tr>

        <tr>
         <td class="biglabel">Match Type:</td>
           <td><select name="matchtype" onchange="disablePlayerDropDownWithSoloSelection(this)">
          
           
            <? if(is_inabox($courtid, get_userid())){  ?>
            <option value="1">Box League</option>
             <? } ?>
            <option value="2">Challenge</option>
             <? if(get_roleid()==2){ ?>
             <option value="4">Lesson</option>
             <? } ?>
            <option value="0" selected>Practice</option>
             <? if(get_roleid()!=4 && isSoloReservationEnabled() ){ ?>
		    <option value="5">Solo</option>
		    <? } ?>
           </select>

           </td>
       </tr>
		<tr>
        <td colspan="2" class="italitcsm">The match type really only has do with what happens when the score is reported.  The main differce between a 
        Practice match and a Challenge match is that when reporting the results of a Challenge match the players positions will be adjusted in the Club
        Ladder.  Oh, and with a Challenge match the ranking adjustments are weighted twice those of a Practice match.
        </td>
    </tr>
       <tr>
           
           <td>
           <br/><br/>
           <input type="submit" name="submit" value="Make Reservation" ></td>
           <td>
           	<input type="hidden" name="courttype" value="singles">
           	<input type="hidden" name="partner" value="single">
           	<input type="hidden" name="usertype" value="nonfrontdesk">
            <input type="hidden" name="time" value="<?=$time?>">
			<input type="hidden" name="courtid" value="<?=$courtid?>">
           </td>
    </tr>
 </table>

</td>
</tr>
</table>



</form>

<script language="Javascript">
function setfocus() {
        document.entryform.name.focus()
    }

setfocus();


			
			
</script>


</td>
</tr>
</table>
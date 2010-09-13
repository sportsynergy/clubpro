<?php
/*
 * $LastChangedRevision: 755 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-12-28 12:38:50 -0800 (Mon, 28 Dec 2009) $

*/
?>
<?
  $DOC_TITLE = "Singles Court Reservation";
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
             document.entryform.playertwoname.disabled = true;
             document.entryform.playertwoname.disabled = true;
        }
        else{
        	document.entryform.playertwoname.disabled = "";
        	document.entryform.playertwoname.disabled = "";
        }
  
}

document.onkeypress = function (aEvent)
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



<table cellspacing="0" cellpadding="0" width="570" >
 <tr>
    <td class=clubid<?=get_clubid()?>th height=60><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
  <? if($reservationType == 1 || $reservationType == 0){ ?>
 <tr>
        <td>
             <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
                <tr>
                    <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=event"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/eventTabOff.gif" border="0"></a></td>
                    <? if($reservationType == 2 || $reservationType == 1){?>
                    <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=doubles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/doublesTabOff.gif" border="0"></a></td>
                    <? } ?>
                    <? if($reservationType == 0 || $reservationType == 1){?>
                    <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=singles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/singlesTabOn.gif" border="0"></a></td>
                    <? } ?>
                    <td width="100%"></td>
                 </tr>
               </table>
        </td>
</tr>
 <? } ?>
 <tr>
    <td class="generictable">

     <table cellspacing="10" cellpadding="0" width="440">


        <tr>
            <td class="label">Player&nbsp;One:</td>
             
            <td>
             <input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
             <input id="id1" name="playeroneid" type="hidden" />
    			<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
            
		    </td>
       </tr>


       <tr>
            <td class=label>Player&nbsp;Two:</td>
              
            <td>
            
            <input id="name2" name="playertwoname" type="text" size="30" class="form-autocomplete" />
             <input id="id2" name="playertwoid" type="hidden"/>
    			<script>
                <?
                 $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name2',
						'target'=>'id2',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>

                </td>

       </tr>

        <tr>
         <td class=label>Match Type:</td>
           <td><select name="matchtype" onchange="disablePlayerDropDownWithSoloSelection(this)">
             <option value="1">Box League</option>
             <option value="2">Challenge</option>
             <option value="4">Lesson</option>
             <option value="0" selected>Practice</option>
             <option value="5">Solo</option>

           </select>
           </td>
           <td></td>

       </tr>
       <tr>
        <td colspan="4" class="italitcsm">To book a reservation, type in the name of the each player then select from the list.
        For more infomation on the match types, go ahead and click <A HREF=javascript:newWindow('../help/squash-matchtypes.html')>here</a>.  
        <? if( get_roleid() == 2){ ?>
        <p>If you want to put yourself down as available for a lesson, leave your name in as Player One, leave Player Two blank and set
        the matchtype as <b>Lesson</b>.</p>
        
        <? } ?>
        </td>
    </tr>
       <tr>
           <td><input type="hidden" name="courttype" value="singles"></td>
           <td> <input type="submit" name="submit" value="Submit">  <input type="Reset"></td>
           <td>
	           <input type="hidden" name="partner" value="single">
	           <input type="hidden" name="ct" value="singles">
			   <input type="hidden" name="usertype" value="frontdesk">
	           <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
			   <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
           </td>
           <td></td>
    </tr>

 </table>

</td>
</tr>
</table>



</form>

<script language="Javascript">
function defaultform() {
        
        <? if(get_roleid() == 2){ ?>
        	
        	document.entryform.playertwoname.focus();
        	document.entryform.playeronename.value = "<?=getFullNameForUserId( get_userid() )?>";
        	document.entryform.playeroneid.value = <?= get_userid() ?>;
        	
       <? }else{   ?>
        	document.entryform.playeronename.focus();
        
        <? } ?>
        
    }

defaultform();

</script>


</td>
</tr>
</table>
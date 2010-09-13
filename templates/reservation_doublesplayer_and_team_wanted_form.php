<?php
  /*
 * $LastChangedRevision:  $
 * $LastChangedBy:  $
 * $LastChangedDate:  $

*/
?>
<?
         //reservation_doubles_wanted_form.php
         $DOC_TITLE = "Doubles Court Reservation";
?>

<?
 // Get the first and last name of the player who needs a partner
 $getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE (((tblUsers.userid)=$userid))";

 $getfirstandlastresult = db_query($getfirstandlastquery);
 $getfirstandlastobj = db_fetch_object($getfirstandlastresult);


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

function disablePartnerList(disableIt)
{
        document.entryform.partnername.disabled = disableIt;
        document.entryform.partnername.disabled = disableIt;
}

function enablePartnerList()
{
        document.entryform.partnername.disabled = "";
        document.entryform.partnername.disabled = "";
}
//-->

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


<table cellspacing="0" cellpadding="20" width="400" >
  <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="400">

		<tr>
		<td><input type="radio" name="playwith" value="1"  checked="checked" onclick="disablePartnerList(this.checked)"></td>
           <td colspan="3" class="normal">Play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?</td>

       </tr>
       <tr>
        <td><? echo "<input type=\"hidden\" name=\"time\" value=\"$time\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"courtid\" value=\"$courtid\">" ?><td>
         <td><?echo "<input type=\"hidden\" name=\"opponent\" value=\"fromdoublespwandtwform\">";
               echo "<input type=\"hidden\" name=\"courttype\" value=\"doubles\">";
               echo "<input type=\"hidden\" name=\"opponentplayer1\" value=\"opponentplayer1\">";
                echo "<input type=\"hidden\" name=\"opponentplayer2\" value=\"opponentplayer2\">";?>
         </td>
       </tr>
        <tr>
        <td><input type="radio" name="playwith" value="2" onclick="enablePartnerList()"></td>
         <td class="normal">Play with Partner:<td>

         <td>
         
          <input id="name1" name="partnername" type="text" size="30" class="form-autocomplete" disabled="disabled"/>
             <input id="id1" name="partner" type="hidden"  />
    			<script>
                <?
                $wwwroot = $_SESSION["CFG"]["wwwroot"];
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
       <td></td>
           <td class="label" colspan="2">
           <input type="hidden" name="userid" value="<?=$userid?>">
           <input type="hidden" name="matchtype" value="<?=$matchtype?>">
    	</tr> 
    <tr> 
	    <td colspan="3">
	    <br>
	           <input type="submit" name="submit" value="Submit">
	           <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?=gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
	           </td>
    </tr>



 </table>


 </td>
 </tr>

</table>
</form>



</td>
</tr>
</table>
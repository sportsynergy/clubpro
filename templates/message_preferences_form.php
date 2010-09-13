<?php
  /*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>
<?


?>

<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="0" width="450" >
  <tr>
    <td class=clubid<?=get_clubid()?>th height=60><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>
     <td>
              <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
               <tr>
                  <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/ReservationPolicyOff.gif" border="0"></a></td>
                  <td align=left width=84><img src="<?=$_SESSION["CFG"]["imagedir"]?>/MessagesOn.gif" border="0"></td>
                  <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/general_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/GeneralPreferencesOff.gif" border="0"></a></td>
                    <td width=100%></td>
               </tr>
           </table>
     </td>
 </tr>

 <tr>
    <td class=generictable>

     <table width="450" cellpadding="6" cellspacing="6" border="0">
        <tr>
             <td>

                  <table width="450" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td class=label>Message Display:</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="normal">On<input type="radio" name="messagedisplay" value="on" <? if($frm["enable"] ==1){ echo "checked";} ?>></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="normal">Off<input type="radio" name="messagedisplay" value="off" <? if($frm["enable"] ==0){ echo "checked";} ?>></td>
                            <td></td>
                        </tr>
                         <tr>
                            <td colspan="2" height="10"><!-- Spacer --></td>
                        </tr>
                        <tr>
                            <td class=label>Message:(supports html)</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><textarea cols="45" rows="4" name="Messagetextarea"><? pv($frm["message"]) ?></textarea>
                            <?err($errors->Messagetextarea)?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="submit" value="Submit"></td>
                            <td></td>
                        </tr>
                  </table>

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
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


<table width="600" cellpadding="20" cellspacing="0">
    <tr>
    <td class="clubid<?=get_clubid()?>th"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class="generictable">

       <form name="entryform" method="post" action="<?=$ME?>">
       <table cellspacing="5" cellpadding="1" width="600" >


        <tr>
            <td class="label">Username:</td>
            <td class="normal"><? pv($frm["username"]) ?></td>
        </tr>

        <tr>
            <td class="label"><font color="Red" class="normalsm">* </font>First Name:</td>
            <td><input type="text" name="firstname" size=25 value="<? pv($frm["firstname"]) ?>">
                <?err($errors->firstname)?>
            </td>
        </tr>
        <tr>
            <td class="label"><font color="Red" class=normalsm>* </font>Last Name:</td>
            <td><input type="text" name="lastname" size=25 value="<? pv($frm["lastname"]) ?>">
                <?err($errors->lastname)?>
                </td>
        </tr>

        <tr>
            <td class="label"><font color="Red" class=normalsm>* </font>Email:</td>
            <td><input type="text" name="email" size=25 value="<? pv($frm["email"]) ?>">
                <?err($errors->email)?>
                </td>
        </tr>

        <tr>
            <td class="label"><font color="Red" class=normalsm>* </font> Home Phone:</td>
            <td><input type="text" name="homephone" size=25 value="<? pv($frm["homephone"]) ?>">
                <?err($errors->homephone)?>
                </td>
        </tr>

        <tr>
            <td class="label"><font color="Red" class=normalsm>* </font>Work Phone:</td>
            <td><input type="text" name="workphone" size=25 value="<? pv($frm["workphone"]) ?>">
                <?err($errors->workphone)?>
            </td>
        </tr>
         <tr>
            <td class="label">Cell Phone:</td>
            <td><input type="text" name="cellphone" size="25" value="<? pv($frm["cellphone"]) ?>">
                <?err($errors->cellphone)?>
            </td>
        </tr>
         <tr>
            <td class="label">Pager:</td>
            <td><input type="text" name="pager" size=25 value="<? pv($frm["pager"]) ?>">
                <?err($errors->pager)?>
            </td>
        </tr>

        <tr>
            <td class="label">Address:</td>
            <td><textarea name="useraddress" cols=50 rows=5><? pv($frm["useraddress"]) ?></textarea>
                <?err($errors->address)?>
            </td>
        </tr>
       <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <tr>
            <td class="label">Receive Players Wanted Notifications:</td>
            <td><select name="recemail">
                <?

                if  ($frm["recemail"]=='y'){

                echo "<option value=\"y\">Yes</option>";
                echo "<option value=\"n\">No</option>";

                }
                else {
                echo "<option value=\"n\">No</option>";
                echo "<option value=\"y\">Yes</option>";
                }
                echo "</select>";
                ?>
                <?err($errors->recemail)?>
                </td>
        </tr>

        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" name="submit" value="Update Settings">
            <input type="hidden" name="userid" value="<?pv($userid) ?>"></td>
            <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
            <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
            <input type="hidden" name="username" value="<? pv($frm["username"]) ?>">
            </td>
        </tr>
        <tr>
            <td colspan="2"><font color="Red" class=normalsm>* </font><font class=normalsm>indicates a required field</font></td>
        </tr>
        </table>
       </form>

    </td>
</tr>

   <tr>
       <td colspan="6" align="right" class="normal">
         <?if($DOC_TITLE == "Player Administration"){ ?>
         <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
         <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
         <input type="hidden" name="searchname" value="<? pv($searchname) ?>">
         </form>
         <? }?>
      </td>
   </tr>
</table>


</td>
</tr>
</table>
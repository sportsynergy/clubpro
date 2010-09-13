<?php
/*
 * $LastChangedRevision: 750 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-11-17 00:08:34 -0800 (Tue, 17 Nov 2009) $

*/
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<table cellspacing="0" cellpadding="20" width="600" >
 <tr>
    <td class=clubid<?=get_clubid()?>th bgcolor="#ff8000"><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class="generictable">

    <form name="entryform" method="post" action="<?=$ME?>">



        <table width="550">
         <? if(!isSiteAutoLogin()){ ?>
        <tr>
                <td class="label"><font color="Red" class="normalsm">* </font>Username:</td>
                <td><input type="text" name="username" size=35 value="<? pv($frm["username"]) ?>">
                        <?err($errors->username)?>
                </td>
        </tr>
      
        <tr>
                <td class="label"><font color="Red" class="normalsm">* </font>Password:</td>
                <td><input type="password" name="password" size="35">
                        <?err($errors->password)?>
                </td>
        </tr>
        <? }?>
        <tr>
                <td class="label"><font color="Red" class="normalsm">* </font>First Name:</td>
                <td><input type="text" name="firstname" size="35" value="<? pv($frm["firstname"]) ?>">
                        <?err($errors->firstname)?>
                </td>
        </tr>
        <tr>
                <td class="label"><font color="Red" class="normalsm">* </font>Last Name:</td>
                <td><input type="text" name="lastname" size="35"value="<? pv($frm["lastname"]) ?>">
                        <?err($errors->lastname)?>
                </td>
        </tr>
        <tr>
                <td class="label"></font>Home Phone:</td>
                <td><input type="text" name="homephone" size="35"value="<? pv($frm["homephone"]) ?>">
                        <?err($errors->homephone)?>
                </td>
        </tr>
        <tr>
                <td class="label"></font>Work Phone:</td>
                <td><input type="text" name="workphone" size="35" value="<? pv($frm["workphone"]) ?>">
                        <?err($errors->workphone)?>
                </td>
        </tr>
        <tr>
                <td class="label"></font>Email:</td>
                <td><input type="text" name="email" size="35" value="<? pv($frm["email"]) ?>">
                        <?err($errors->email)?>
                </td>
        </tr>
        <tr>
                <td class="label">Cell Phone:</td>
                <td><input type="text" name="cellphone" size="35" value="<? pv($frm["cellphone"]) ?>">
                        <?err($errors->cellphone)?>
                </td>
        </tr>
        <tr>
                <td class="label">Pager:</td>
                <td><input type="text" name="pager" size=35 value="<? pv($frm["pager"]) ?>">
                        <?err($errors->pager)?>
                </td>
        </tr>
        <tr valign=top>
                <td class="label">Address:</td>
                <td><textarea name="useraddress" cols=35 rows=5><? pv($frm["useraddress"]) ?></textarea>
                        <?err($errors->useraddress)?>
                </td>
        </tr>
        <tr>
                <td class="label">Date Joined:</td>
                <td><input type="text" name="msince" size=35 value="<? pv($frm["msince"]) ?>">
                        <?err($errors->msince)?>
                </td>
        </tr>
        <tr>
                <td class="label"></td>
                <td>
                 <i><font size=-1> I.E January 2, 1988 </font></i>

                </td>
        </tr>
        <tr>
            <td class="label">Gender</td>
            <td>
                <select name="gender">
                        <option value="1">Male</option>
                        <option value="0">Female</option>
                </select>
          </td>

        </tr>
        <tr>
            <td class="label">
            <? if(isSiteAutoLogin()){ ?>
            	<font color="Red" class=normalsm>* </font>
            <? } ?>
            Membership ID:</td>
            <td><input type="text" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>"> 
            <?err($errors->memberid)?>
            </td>

        </tr>
        <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <?
        //Get Available courttypes
             while($availbleSportsArray = db_fetch_array($availbleSports)){


         ?>
         <tr valign=top>
                <td class="label"><? echo "$availbleSportsArray[courttypename] Ranking" ?>:</td>
                <td><input type="text" name="<? echo "courttype$availbleSportsArray[courttypeid]"?>" size=15 value="">

                </td>
        </tr>
         <?
           //While closing bracket - DO NOT remove
           }
         ?>


        <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <tr>
            <td class=label valign="top">Authorized Sites:</td>
            <td class="normal">

             <?

             while($availableSitesArray = mysql_fetch_array($availableSites)){
                       $checked = "checked";

                       if(!amiValidForSite($availableSitesArray['siteid'])){
                           $disabled = "disabled";
                           $checked = "";
                        }
                        else{
                             $disabled = "";

                        }


                    print "<input type=\"checkbox\" name=\"clubsite$availableSitesArray[siteid]\" value=\"$availableSitesArray[siteid]\" $checked $disabled> $availableSitesArray[sitename] <br>\n";

                    unset($disabled);
                    unset($checked);
                 }

              //Done with Authorized Sites
             ?>

            </td>
        </tr>

          <tr>
            <td class="label" valign="top">User Type:</td>
            <td>
                <select name="usertype">
                        <option value="1">Player</option>
                        <option value="5">Limited Access Player</option>
                        <option value="4">Desk User</option>
                        <option value="2">Club Admin</option>
                        
                 </select>
            </td>
          </tr>
          <tr>
            <td colspan="2"><font color="Red" class="normalsm">* </font><font class="normalsm">indicates a required field</font></td>
        </tr>
        <tr>
            <td colspan="2" height="20"></td>
        </tr>

        <tr>
        <td></td>
        <td><input type="submit" name="submit" value="Signup"></td>
        </tr>
</table>
</form>

</td>
</tr>
</table>

</td>

</tr>
</table>
<?php

?>
<table class="table">
  
        <tr>
          <td class="label">User Name:</td>
          <td><?=$frm["username"] ?></td>
        </tr>
        <tr>
          <td class="label">First Name:</td>
          <td ><?=$frm["firstname"] ?></td>
        </tr>
        <tr>
          <td class="label">Last Name:</td>
          <td ><?=$frm["lastname"] ?></td>
        </tr>
        <tr>
          <td class="label">Email:</td>
          <td ><?=$frm["email"] ?></td>
        </tr>
        <tr>
          <td class="label">Home Phone:</td>
          <td ><? if($frm["homephone"]==0)
			                       echo "Not Specified";
			                  else
			                      pv($frm["homephone"]);
			                  ?></td>
        </tr>
        <? if(!empty($frm["workphone"])){?>
        <tr>
          <td class="label">Work Phone:</td>
          <td ><?=$frm["workphone"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["cellphone"])){?>
        <tr>
          <td class="label">Mobile Phone:</td>
          <td ><?=$frm["cellphone"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["pager"])){?>
        <tr>
          <td class="label">Pager:</td>
          <td ><?=$frm["pager"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["msince"])){?>
        <tr>
          <td class="label">Member Since:</td>
          <td ><?=$frm["msince"]?></td>
        </tr>
        <? } ?>
        <tr>
          <td class="label" valign="top">Address:</td>
          <td ><textarea name="useraddress" cols=35 rows=5 disabled><? pv($frm["useraddress"]) ?>
</textarea></td>
        </tr>
        <tr>
          <td colspan="2" height="20"><!-- Spacer --></td>
        </tr>
        <tr>
          <td class="label" valign="top">Rankings:</td>
          <td><table width="300" class="skinnytable">
              <?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
              <tr>
                <td class="normal"><?=$registeredArray[courttypename]?></td>
                <td class="normal"><?=$registeredArray[ranking]?></td>
              </tr>
              <?  }
			                    ?>
            </table></td>
        </tr>
      
</table>

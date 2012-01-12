<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 * $LastChangedRevision: 783 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-11-01 17:45:17 -0500 (Mon, 01 Nov 2010) $

*/
?>
<table width="600" cellpadding="0" cellspacing="0" class="tabtable">
  <tr>
    <td><table width="600" class="mediumbonedtable">
        <tr>
          <td class="label">User Name:</td>
          <td class="normal"><?=$frm["username"] ?></td>
        </tr>
        <tr>
          <td class="label">First Name:</td>
          <td class="normal"><?=$frm["firstname"] ?></td>
        </tr>
        <tr>
          <td class="label">Last Name:</td>
          <td class="normal"><?=$frm["lastname"] ?></td>
        </tr>
        <tr>
          <td class="label">Email:</td>
          <td class="normal"><?=$frm["email"] ?></td>
        </tr>
        <tr>
          <td class="label">Home Phone:</td>
          <td class="normal"><? if($frm["homephone"]==0)
			                       echo "Not Specified";
			                  else
			                      pv($frm["homephone"]);
			                  ?></td>
        </tr>
        <? if(!empty($frm["workphone"])){?>
        <tr>
          <td class="label">Work Phone:</td>
          <td class="normal"><?=$frm["workphone"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["cellphone"])){?>
        <tr>
          <td class="label">Mobile Phone:</td>
          <td class="normal"><?=$frm["cellphone"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["pager"])){?>
        <tr>
          <td class="label">Pager:</td>
          <td class="normal"><?=$frm["pager"]?></td>
        </tr>
        <? } ?>
        <? if(!empty($frm["msince"])){?>
        <tr>
          <td class="label">Member Since:</td>
          <td class="normal"><?=$frm["msince"]?></td>
        </tr>
        <? } ?>
        <tr>
          <td class="label" valign="top">Address:</td>
          <td class="normal"><textarea name="useraddress" cols=35 rows=5 disabled><? pv($frm["useraddress"]) ?>
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
      </table></td>
  </tr>
</table>

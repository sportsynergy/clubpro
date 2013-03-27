<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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
 * $LastChangedRevision: 854 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-08 20:15:00 -0600 (Tue, 08 Mar 2011) $
*/
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
  <tr>
    <td class="normal"> The player was successfully removed.  Click <a href="javascript:submitForm('backtosearchresultsform')">here</a> to go back to the list. </td>
  </tr>
</table>
<form name="backtosearchresultsform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
  <input type="hidden" name="searchname" value="<?=$searchname?>">
</form>

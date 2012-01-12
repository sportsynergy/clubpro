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
?>
<div style="padding-top: 15px">
  <ul class="clubnews">
    <?

$clubNewsResult = getClubNews( get_siteid() );

if(mysql_num_rows($clubNewsResult)>0){   ?>
    <h2 style="padding-top: 15px">Club News</h2>
    <hr class="hrline"/>
    <?
$counter = 0;
while($clubNews = mysql_fetch_array($clubNewsResult)){ ?>
    <? 
	if($counter>0){ ?>
    <hr class="hrlinesm"/>
    <?} ?>
    <li>
      <?=$clubNews['message']?>
    </li>
    <? 
++$counter;
}

} ?>
  </ul>
</div>

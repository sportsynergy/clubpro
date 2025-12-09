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
$siteActivityResult = getRecentSiteActivity(get_siteid());

if (mysqli_num_rows($siteActivityResult) > 0) { ?>

<h2 style="padding-top: 15px">Recent Activity</h2>
<hr class="hrline"/>

<div>
<ul class="recentavtivity" id="activityfeed">

<?
$lastactivity = "";
while($siteActivity = mysqli_fetch_array($siteActivityResult)){ ?>
	
	<li><?=$siteActivity['description']?></li>
	
<? 
	$lastactivity = $siteActivity['activitydate'];
} ?>

</ul>

</div>




<? } ?>




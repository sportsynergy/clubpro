<?php

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




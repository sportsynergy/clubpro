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


<span class="normalsm" id="moresection">
<a href="javascript:makeRequest();">more</a>
</span>

<? } ?>


<script>
var div = document.getElementById('activityfeed');
var recentActivity = '<?=$lastactivity?>';
var siteid = '<?=get_siteid()?>';

var handleSuccess = function(o){

	if(o.responseText !== undefined){

		 messages = YAHOO.lang.JSON.parse(o.responseText);

		if(messages.length < 3){
			var moresection = document.getElementById('moresection');
			moresection.className = "hideme";
			
		}
		  
		 for (var i = 0, len = messages.length; i < len; ++i) { 
			     var m = messages[i]; 
			     var item = document.createElement('li');
			     var message_text = document.createTextNode(m.description);
	                item.appendChild(message_text);
	                div.appendChild(item); 
					//set the activity date
	                recentActivity = m.activitydate;
	                         
		} 
		
		
		
	}
}

var handleFailure = function(o){

	if(o.responseText !== undefined){
		div.innerHTML = "<ul><li>Transaction id: " + o.tId + "</li>";
		div.innerHTML += "<li>HTTP status: " + o.status + "</li>";
		div.innerHTML += "<li>Status code message: " + o.statusText + "</li></ul>";
	}
}

var callback =
{
  success:handleSuccess,
  failure:handleFailure
};


function makeRequest(){
	var sUrl = "<?=$_SESSION["CFG"]["wwwroot"]?>/api/recent_activity.php?start="+recentActivity+"&siteid="+siteid; 
	var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	
}



</script>


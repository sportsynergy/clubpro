





<?
$siteActivityResult = getRecentSiteActivity( get_siteid() );

if(mysql_num_rows($siteActivityResult) > 0){ ?>

<h2 style="padding-top: 15px">Recent Activity</h2>
<hr class="hrline"/>

<div>
<ul class="recentavtivity" id="container">

<?
	
while($siteActivity = mysql_fetch_array($siteActivityResult)){ ?>
	
	<li><?=$siteActivity['description']?></li>
	
<? } ?>

</ul>

</div>


<span class="normalsm" id="moresection">
<a href="javascript:makeRequest();">more</a>
</span>

<? } ?>


<script>
var div = document.getElementById('container');
var recentActivity = '<?=formatDate($_SESSION["current_time"])?>';
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
	var sUrl = "<?=$_SESSION["CFG"]["wwwroot"]?>/users/recent_activity.php?start="+recentActivity+"&siteid="+siteid; 
	var request = YAHOO.util.Connect.asyncRequest('GET', sUrl, callback);
	
}



</script>


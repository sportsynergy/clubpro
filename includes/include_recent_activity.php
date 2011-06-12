<?

/*
 * $LastChangedRevision: 861 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-16 12:42:52 -0500 (Wed, 16 Mar 2011) $

*/
?>

<h2 style="padding-top: 15px">Recent Activity</h2>
<hr class="hrline"/>

<div>
<ul class="recentavtivity">

<?
$siteActivityResult = getRecentSiteActivity( get_siteid() );

if(mysql_num_rows($siteActivityResult)==0){ ?>
	<li>No activity logged...yet</li>
<?}else{
	
while($siteActivity = mysql_fetch_array($siteActivityResult)){ ?>
	
	<li><?=$siteActivity['description']?></li>
	
<? }} ?>



</ul>

</div>
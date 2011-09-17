



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
	
	<li><?=$clubNews['message']?></li>
	
<? 
++$counter;
}

} ?>






</ul>

</div>



<<<<<<< HEAD
<div style="padding-top: 15px">
=======
<div>
>>>>>>> 839d309743f2e7c9b25bed4f55a857f6ac505526
<ul class="clubnews">

<?

$news = get_twitterHandle();

if(isDebugEnabled(1) ) logMessage("include_news: the news is: $news");

if( ! empty($news) ) { ?>
	
<<<<<<< HEAD
<h2 >Club News</h2>
<hr class="hrline"/>
<li>
<?=getTwitterStatus(get_twitterHandle(), "\\1"); ?>
</li>
<? } ?>
</li>
=======
<h2>Club News</h2>
<hr class="hrline"/>

<?=getTwitterStatus(get_twitterHandle(), "\\1");


}
?>

>>>>>>> 839d309743f2e7c9b25bed4f55a857f6ac505526


</ul>

</div>
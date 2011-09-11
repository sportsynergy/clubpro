


<div>
<ul class="clubnews">

<?

$news = get_twitterHandle();

if(isDebugEnabled(1) ) logMessage("include_news: the news is: $news");

if( ! empty($news) ) { ?>
	
<h2>Club News</h2>
<hr class="hrline"/>

<?=getTwitterStatus(get_twitterHandle(), "\\1");


}
?>



</ul>

</div>
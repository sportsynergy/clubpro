



<div style="padding-top: 15px">

<ul class="clubnews">

<?

$news = get_twitterHandle();

if(isDebugEnabled(1) ) logMessage("include_news: the news is: $news");

if( ! empty($news) ) { ?>
	
<h2 style="padding-top: 15px">Club News</h2>
<hr class="hrline"/>
<li>
<?=getTwitterStatus(get_twitterHandle(), "\\1"); ?>
</li>
<? } ?>






</ul>

</div>
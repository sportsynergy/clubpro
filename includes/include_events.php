<?

/*
 * $LastChangedRevision: 823 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-06 22:50:11 -0600 (Sun, 06 Feb 2011) $

*/

?>



<h2>Club Events</h2>
<hr class="hrline"/>

<div>
<ul class="clubevents">

<?
$clubEventsResult = getClubEvents( get_clubid() );

if(mysql_num_rows($clubEventsResult)==0){ ?>
	<li>No club events setup...yet</li>
<?}else{
	
while($clubEvent = mysql_fetch_array($clubEventsResult)){ ?>
	
	<li>
	<a href="javascript:submitForm('loadClubEventForm<?=$clubEvent['id']?>')">
		<?=$clubEvent['name']?>
	</a> <br/>
	<span class="italitcsm"><?=formatDateString($clubEvent['eventdate'])?> </span>
	</li>
	 <form name="loadClubEventForm<?=$clubEvent['id']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/club_event.php" method="post">
           <input type="hidden" name="clubeventid" value="<?=$clubEvent['id'] ?>">
     </form>
<? }} ?>



</ul>

</div>
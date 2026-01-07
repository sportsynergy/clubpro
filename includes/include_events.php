
<div>


<?
$clubEventsResult = getClubEvents( get_clubid() );

if(mysqli_num_rows($clubEventsResult) > 0){ ?>
	
	
<h2 style="padding-top: 15px">Club Events</h2>
<hr class="hrline"/>

<ul class="clubevents">
<?
	
while($clubEvent = mysqli_fetch_array($clubEventsResult)){ ?>
	
	<li>
	<a href="javascript:safeSubmit('loadClubEventForm<?=$clubEvent['id']?>')" >
		<?=$clubEvent['name']?>
	</a> 
	<div class="italic">
		<?=formatDateString($clubEvent['eventdate'])?> 
</div>
	</li>

	 <form name="loadClubEventForm<?=$clubEvent['id']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/club_event.php" method="post">
           <input type="hidden" name="clubeventid" value="<?=$clubEvent['id'] ?>">	
     </form>
<? }} ?>



</ul>

</div>
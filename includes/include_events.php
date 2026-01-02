
<div>


<?
$clubEventsResult = getClubEvents( get_clubid() );

if(mysqli_num_rows($clubEventsResult) > 0){ ?>
	
	
<h2 style="padding-top: 15px">Club Events</h2>
<hr class="hrline"/>

<ul class="clubevents">
<?
	
while($clubEvent = mysqli_fetch_array($clubEventsResult)){ ?>

	 <form name="loadClubEventForm<?=$clubEvent['id']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/club_event.php" method="post">
           <input type="hidden" name="clubeventid" value="<?=$clubEvent['id'] ?>">
		   <input type="hidden" name="clubid" value="<?=get_clubid() ?>">
		   <input type="submit" value="<?=$clubEvent['name']?>" name="submit" class="buttonlink" >	
     </form>
	 <div class="italic">
		<?=formatDateString($clubEvent['eventdate'])?> 
	</div>
<? }} ?>



</ul>

</div>
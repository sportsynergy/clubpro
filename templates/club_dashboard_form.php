<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>



		
		<table class="table table-striped" style="width:70%;">
			<thead>
			<tr>
				<th colspan='2'></td>
				<th>Number of Members</td>
				<th >Number of Reservations</td>
			</tr>
</thead>
<tbody>
			
			<? 
			
			$memberTotal = 0;
			$resrevationsTotal = 0;
			
			$clubs = get_allclubs_dropdown();
			while($club = mysqli_fetch_array($clubs)){ 
				
			if($club['clubname']=="System"){
				continue;
			}
				
			?>
			
			 <tr>
			     <td aligh='right' colspan='4'>
					<span class=h2><?=$club['clubname']?></span>
				</td>
			 </tr>
			<? 
			
			$sites = loadClubSites($club['clubid']);
			
			while ($site = mysqli_fetch_array($sites)) { 
				
				$members = countSiteMembers($site['siteid']);
				$resrevations = countSiteReservations($site['siteid']);
				$memberTotal = $memberTotal + $members;
				$resrevationsTotal = $resrevationsTotal + $resrevations;
			?>
			 <tr>
			 	<td width='15'></td>
			 	<td>
					<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/site_info.php?siteid=<?=$site['siteid']?>">
					<?=$site['sitename']?></a>
			 	</td>
			     <td><?=$members?></td>
			     <td><?=$resrevations?></td>
			 </tr>
			<? } ?>
		
		
		<? } ?>
			
			<tr>
				<td colspan="4" height="60"></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td ><?=$memberTotal?></td>
				<td ><?=$resrevationsTotal?></td>
			</tr>
			</tbody>
		</table>


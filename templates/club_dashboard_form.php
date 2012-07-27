
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>
		<?  $clubs = get_allclubs_dropdown() ?>
		
		<table cellspacing="0" cellpadding="0" width="700" >
			<tr>
				<td colspan='2'></td>
				<td align='center' class='normalsm'>Number of Members</td>
				<td align='center' class='normalsm'>Number of Reservations</td>
			</tr>
			
			<? 
			
			$memberTotal = 0;
			$resrevationsTotal = 0;
			
			while($club = mysql_fetch_array($clubs)){ 
				
			if($club['clubname']=="System"){
				continue;
			}
				
			?>
			
			 <tr>
			     <td aligh='right' colspan='4'><span class=h2><?=$club['clubname']?></span></td>
			 </tr>
			<? 
			
			$sites = loadClubSites($club['clubid']);
			
			while ($site = mysql_fetch_array($sites)) { 
				
				$members = countSiteMembers($site['siteid']);
				$resrevations = countSiteReservations($site['siteid']);
				$memberTotal = $memberTotal + $members;
				$resrevationsTotal = $resrevationsTotal + $resrevations;
			?>
			 <tr>
			 	<td width='15'></td>
			 	<td>
			 		<span class=normalsm>
					<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/site_info.php?siteid=<?=$site['siteid']?>">
					<?=$site['sitename']?></a></span>
			 	</td>
			     <td align='center'><span class=normalsm><?=$members?></span></td>
			     <td align='center'><span class=normalsm><?=$resrevations?></span></td>
			 </tr>
			<? } ?>
		
		
		<? } ?>
			
			<tr>
				<td colspan="4" height="60"></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td align='center'><span class=normalsm><?=$memberTotal?> </span></td>
				<td align='center'><span class=normalsm><?=$resrevationsTotal?></span></td>
			</tr>
		</table>

</td>
</tr>


</table>
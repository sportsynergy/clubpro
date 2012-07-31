<?
/*	siteid	clubid	sitename	sitecode	allowselfcancel	enableautologin	rankingadjustment	allowsoloreservations	allowselfscore	daysahead	displaytime	password	isliteversion	enable	lastmodified	allowallsiteadvertising	enableguestreservation	displaysitenavigation	displayrecentactivity	allownearrankingadvertising	rankingscheme	challengerange	facebookurl
*/
?>
<div align="center">
  <table width="650" cellpadding="20" cellspacing="0" class="generictable">
    <tr>
      <td class="clubid<?=get_clubid()?>th" ><span class="whiteh1">
        <div align="center">
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
	<tr>
		<td>
		<table>
			<tr>
				<td class="label">Name:</td>
				<td><?=$sitedetail['sitename']?></td>
			</tr>
			<tr>
				<td class="label">Site Code:</td>
				<td><?=$sitedetail['sitecode']?></td>
			</tr>
			<tr>
				<td class="label">Allow Self Cancel:</td>
				<td><?=$sitedetail['allowselfcancel']?></td>
			</tr>
			<tr>
				<td class="label">Auto Login:</td>
				<td><?=$sitedetail['enableautologin']?></td>
			</tr>
			<tr>
				<td class="label">Ranking Adjustment:</td>
				<td><?=$sitedetail['rankingadjustment']?></td>
			</tr>
			<tr>
				<td class="label">Allow Solo Reservations:</td>
				<td><?=$sitedetail['allowsoloreservations']?></td>
			</tr>
			<tr>
				<td class="label">How Many Days Can Members Book a Court:</td>
				<td><?=$sitedetail['daysahead']?></td>
			</tr>
			<tr>
				<td class="label">How Many Days Can Members Book a Court:</td>
				<td><?=$sitedetail['daysahead']?></td>
			</tr>	
			<tr>
				<td class="label" valign="top">Courts: <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court.php">add</a></td>
				<td>
				<?
				while($court = mysql_fetch_array($sitecourts)){ ?>
					<div><?=$court['courtname']?></div>
				<? } ?>	
	
				</td>
			</tr>
		</table>	
			
		</td>
	</tr>
	</table>
</div>



    
        
        
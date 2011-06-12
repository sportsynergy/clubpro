<?php
/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $

 */
?>



<table width="600" cellpadding="20" cellspacing="0" class="generictable">
	<tr>
		<td class="clubid<?=get_clubid()?>th" >
			<span class=whiteh1>
				<div align="center"><? pv($DOC_TITLE) ?></div>
			</span>
		</td>
	</tr>

	<tr>
		<td>

		<table cellspacing="5" cellpadding="1" width="600" class="borderless">


			<tr>
				<td class=label>First Name:</td>
				<td class="normal"><?=$frm["firstname"] ?></td>
			</tr>
			<tr>
				<td class=label>Last Name:</td>
				<td class="normal"><?=$frm["lastname"] ?></td>
			</tr>

			<tr>
				<td class=label>Email:</td>
				<td class="normal"><a href="mailto:<?=$frm["email"] ?>"><?=$frm["email"] ?></a></td>
			</tr>

			<tr>
				<td class=label>Home Phone:</td>
				<td class="normal"><? if($frm["homephone"]==0)
				echo "Not Specified";
				else
				pv($frm["homephone"]);
				?></td>
			</tr>
			<? if(!empty($frm["workphone"])){?>
			<tr>
				<td class=label>Work Phone:</td>
				<td class="normal"><? pv($frm["workphone"]);?></td>
			</tr>
			<? } ?>
			<? if(!empty($frm["cellphone"])){?>
			<tr>
				<td class=label>Mobile Phone:</td>
				<td class="normal"><?  pv($frm["cellphone"]); ?></td>
			</tr>
			<? } ?>
			<? if(!empty($frm["pager"])){?>
			<tr>
				<td class=label>Pager:</td>
				<td class="normal"><? pv($frm["pager"]);?></td>
			</tr>
			<? } ?>
			<? if(!empty($frm["msince"])){?>
			<tr>
				<td class="label">Member Since:</td>
				<td class="normal"><?=$frm["msince"] ?></td>
			</tr>
			<? } ?>

			<tr>
				<td class="label" valign=top>Address:</td>
				<td class="normal"><textarea name="useraddress" cols="60" rows="5" disabled="disabled"><? pv($frm["useraddress"]) ?></textarea></td>
			</tr>
			<tr>
				<td colspan="2" height="20"><!-- Spacer --></td>
			</tr>
			<tr>
			
			<?
		   // Get the Custom Parameters
		  while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );

			
			if($parameterArray['parametertypename'] == "text" && !empty($parameterValue) ){ ?>
				<tr>
					<td class="label"><? pv($parameterArray['parameterlabel'])?>:</td>
					<td>
						<? pv($parameterValue) ?>
					</td>
				</tr>
					
					
				<? } elseif($parameterArray['parametertypename'] == "select" && !empty($parameterValue) ) { ?>
					
					<tr>
						<td class="label"><? pv($parameterArray['parameterlabel'])?>:</td>
						<td>
							<? pv( load_parameter_option_name($parameterArray['parameterid'], $parameterValue)  ) ?>	
						</td>
					</tr>
				
				<? } ?>
				
			<? } ?>

			<? if( $frm["lastlogin"] != null){ ?>
				<tr>
					<td class="label">Last Login:</td>
					<td class="normal"><?=determineLastLoginText($frm["lastlogin"], get_clubid()) ?>
					</td>
				</tr>
				<? } ?>
				<td class="label" valign="top">Rankings:</td>
				<td>
				<table width="300">
				<?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
					<tr>
						<td style="padding: 0px"><?=$registeredArray['courttypename']?></td>
						<td style="padding: 0px"><?=$registeredArray['ranking']?></td>
					</tr>
					<?  }
					?>
				</table>
				</td>
			</tr>
			<tr height="15">
				<td colspan="2"></td>
			</tr>

			


		</table>


		</td>
	</tr>

</table>

<div style="height: 2em;"></div>
<div style="text-align: left;">
   <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">
						<a href="javascript:submitForm('backtolistform');"><< Back to List</a>
						<input type="hidden" name="searchname" value="<?=$searchname?>">
	</form>
 </div> 


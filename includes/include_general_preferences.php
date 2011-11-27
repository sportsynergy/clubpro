<?
/*
 * $LastChangedRevision: 836 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-22 17:35:17 -0600 (Tue, 22 Feb 2011) $
 */
?>

<form name="general_preferences_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">


<div style="height: 25px;"></div>

<table width="550" class="tabtable">
	<tr>
		<td>
			<table class="skinnytable">
				
				<tr>
					<td class="label">Display Recent Activity:</td>
					<td>
						<select name="displayrecentactivity">
							<option value="y"
							<? if($generalPreferences["displayrecentactivity"] =='y'){ echo "selected";} ?>>Yes</option>
							<option value="n"
							<? if($generalPreferences["displayrecentactivity"] =='n'){ echo "selected";} ?>>No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span class="normal"> The Recent Activity panel will display the results of the matches that were recently recorded. </span>
						<div class="spacer"/>
					</td>
				</tr>
				
				<tr>
					<td class="label">Allow Players To Cancel Their Own Reservations:</td>
					<td>
						<select name="allowselfcancel">
							<option value="y"
							<? if($generalPreferences["allowselfcancel"] =='y'){ echo "selected";} ?>>Yes</option>
							<option value="n"
							<? if($generalPreferences["allowselfcancel"] =='n'){ echo "selected";} ?>>No</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span class="normal"> Pretty much just as what is
						says. When Allow Player Cancelation is set to 'Yes', players will be
						allowed to cancel their own reservations. When set to no, all
						cancelations will need to be made by an Administrator. </span>
						<div class="spacer"/>
					</td>
				</tr>
				<tr>
					<td class="label">How far in advanced Players can make a reservation:</td>
					<td>
						<select name="daysahead">
						<? for($i=1; $i<30; ++$i){ ?>
				
						<?
						//Default to the current daysahead setting.
						if($generalPreferences["daysahead"] == $i){
							$selected = "selected";
						}
						?>
				
							<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
				
							<? unset($selected)?>
							<? } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="spacer">
						<span class="normal">
							Basically this is the number of days in advance a player can view the court schedule in the drop down on the main scheduling page. 
						</span>
						<div class="spacer"/>
					</td>
				</tr>
				<tr>
					<td class="label">Player Inactivty Ranking Adjustment:</td>
					<td><select name="inactivity">
						<option value="0" <?=$generalPreferences["rankingadjustment"]==0?"selected":""?>>0%</option>
						<option value="3" <?=$generalPreferences["rankingadjustment"]==3?"selected":""?>>3%</option>
						<option value="5" <?=$generalPreferences["rankingadjustment"]==5?"selected":""?>>5%</option>
						<option value="10" <?=$generalPreferences["rankingadjustment"]==10?"selected":""?>>10%</option>
						<option value="20" <?=$generalPreferences["rankingadjustment"]==20?"selected":""?>>20%</option>
						<option value="25" <?=$generalPreferences["rankingadjustment"]==25?"selected":""?>>25%</option>
					</select></td>
				</tr>
				<tr>
					<td colspan="2" class="spacer">
						<span class="normal">
							This is a friendly way to encourge people to keep playing and recording their scores. If a
							member does not record a score in thirty days (from the date you set
							this), their ranking will be adjusted downward by this percentage.
							Don't worry emails will be sent out to them so that they know. Not
							only that, a week before we go and adjust their ranking a email will
							be sent letting them know they need to record a score if they don't
							want their ranking to be adjust. 
						</span>
						<div class="spacer"/>
					</td>
				</tr>
				
					<tr>
						<td class="label">Allow Players To Score Their Own Reservations:</td>
						<td><select name="allowselfscore">
							<option value="y"
							<? if($generalPreferences["allowselfscore"] =='y'){ echo "selected";} ?>>Yes</option>
							<option value="n"
							<? if($generalPreferences["allowselfscore"] =='n'){ echo "selected";} ?>>No</option>
						</select></td>
					</tr>
					<tr>
						<td colspan="2" class="spacer">
							<span class="normal">
								Sometime you may want to
								crack down on how scores are being recorded. This little setting is
								pretty much the best way to do just that. When this is set to 'No'
								players will not be able to record the score of any match they play
								in. Administrations, like yourself, will be able to record scores of
								anyone no matter what. 
							</span>
							<div class="spacer"/>
						</td>
					</tr>
					
					<? if(isLadderRankingScheme() ){ ?>
					
					<tr>
						<td class="label">Number of spots ahead people can challenge in the ladder:</td>
						<td>
						<select name="challengerange">
						<? for($i=1; $i<13; ++$i){ ?>
				
						<?
						//Default to the current daysahead setting.
						if($generalPreferences["challengerange"] == $i){
							$selected = "selected";
						}
						?>
				
							<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
				
							<? unset($selected)?>
							<? } ?>
						</select>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="spacer">
							<span class="normal">
								The Challenge Range is how many spots ahead a person can challenge someone else in the ladder. 
							</span>
							<div class="spacer"/>
						</td>
					</tr>
					<? } ?>
					
					<tr>
						<td><input type="submit" name="submit" value="Submit"></td>
						<td></td>
					</tr>
		
			</table>
		</td>
	</tr>
</table>
	

<input type="hidden" name="preferenceType" value="general">

</form>

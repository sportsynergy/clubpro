
<script language="JavaScript">

function removeHoursPolicy(policyid)
{

      document.entryform.policyid.value = policyid;
      document.entryform.submit();


}//end function submitForm()

</script>

<div class="normal"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_open_policy.php">Add Policy</a> </div>



<form name="entryform" method="post" action="<?=$ME?>">


<table width="650" cellpadding="0" cellspacing="0">
<?
//Get Club Policies
	$clubPolicies = get_clubs_policies();
	?>

		<tr>
			<td class="medbold">Club</td>
			<td class="medbold">Site</td>
			<td class="medbold">Date</td>
			<td class="medbold">Open Time</td>
			<td class="medbold">Close Time</td>
			<td></td>
		</tr>
		<? while($clubPolicy = mysqli_fetch_array($clubPolicies)){?>

		<tr>
			<td class="normal"><?=$clubPolicy['clubname'] ?></td>
			<td class="normal"><?=$clubPolicy['sitename'] ?></td>
			<td class="normal"><?=getMonthName($clubPolicy['month']) ?> <?=$clubPolicy['day'] ?>, <?=$clubPolicy['year'] ?></td>
			<td class="normal"><?=$clubPolicy['opentime'] ?></td>
			<td class="normal"><?=$clubPolicy['closetime'] ?></td>
			<td class="normal"><a href="javascript:removeHoursPolicy(<?=$clubPolicy['policyid'] ?>);">Delete</a> </td>
		</tr>

		<? } ?>

</table>
<input type="hidden" name="policyid" value="">
</form>

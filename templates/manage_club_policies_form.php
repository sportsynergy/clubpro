<script language="JavaScript">

function removeHoursPolicy(policyid)
{
      document.entryform.policyid.value = policyid;
      document.entryform.submit();


}//end function submitForm()

</script>

<div><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_open_policy.php">Add Policy</a> </div>



<form name="entryform" method="post" action="<?=$ME?>">


<table class="table table-striped"  >
<?
//Get Club Policies
	$clubPolicies = get_clubs_policies();
	?>

		<thead>
			<tr>
				<td class="medbold">Club</td>
				<td class="medbold">Site</td>
				<td class="medbold">Date</td>
				<td class="medbold">Open Time</td>
				<td class="medbold">Close Time</td>
				<td></td>
			</tr>
		</thead>
		<tbody>
		<? while($clubPolicy = mysqli_fetch_array($clubPolicies)){?>

		<tr>
			<td><?=$clubPolicy['clubname'] ?></td>
			<td><?=$clubPolicy['sitename'] ?></td>
			<td><?=getMonthName($clubPolicy['month']) ?> <?=$clubPolicy['day'] ?>, <?=$clubPolicy['year'] ?></td>
			<td><?=$clubPolicy['opentime'] ?></td>
			<td><?=$clubPolicy['closetime'] ?></td>
			<td><a href="javascript:removeHoursPolicy(<?=$clubPolicy['policyid'] ?>);">Delete</a> </td>
		</tr>

		<? } ?>
		</tbody>
</table>
<input type="hidden" name="policyid" value="">
</form>

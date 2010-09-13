<?php
  /*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>
<script language="JavaScript">

function removeHoursPolicy(policyid)
{



      document.entryform.policyid.value = policyid;
      document.entryform.submit();


}//end function submitForm()

</script>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
	<tr>
		<td>
			<div class="normalsm"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_open_policy.php">Add Policy</a> </div>
			<br/>
		</td>
	</tr>
<tr>
	<td>
		<form name="entryform" method="post" action="<?=$ME?>">
		<table width="550" cellpadding="0" cellspacing="0">
		<?
		//Get Club Policies
		  $clubPolicies = get_clubs_policies();
		 ?>
		
		        <tr>
		            <td class="smallbold">Club</td>
		            <td class="smallbold">Site</td>
		            <td class="smallbold">Date</td>
		            <td class="smallbold">Open Time</td>
		            <td class="smallbold">Close Time</td>
		            <td></td>
		       </tr>
		       <? while($clubPolicy = mysql_fetch_array($clubPolicies)){?>
		
		        <tr>
		            <td class="normalsm"><?=$clubPolicy['clubname'] ?></td>
		            <td class="normalsm"><?=$clubPolicy['sitename'] ?></td>
		            <td class="normalsm"><?=getMonthName($clubPolicy['month']) ?> <?=$clubPolicy['day'] ?>, <?=$clubPolicy['year'] ?></td>
		            <td class="normalsm"><?=$clubPolicy['opentime'] ?></td>
		            <td class="normalsm"><?=$clubPolicy['closetime'] ?></td>
		            <td class="normalsm"><a href="javascript:removeHoursPolicy(<?=$clubPolicy['policyid'] ?>);">Delete</a> </td>
		       </tr>
		
		       <? } ?>
		
		</table>
		<input type="hidden" name="policyid" value="">
		</form>
		</td>
	</tr>
</table>
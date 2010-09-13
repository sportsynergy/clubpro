<?php
/*
 * $LastChangedRevision: 732 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-08-25 23:55:08 -0700 (Tue, 25 Aug 2009) $

*/
?>
<script language="JavaScript">

function removeSchedulingPolicy(policyid)
{

      document.schedulepolicyform.schedulepolicyid.value = policyid;
      document.schedulepolicyform.submit();


}

function removeSkillRangePolicy(policyid)
{

      document.skillpolicyform.skillpolicyid.value = policyid;
      document.skillpolicyform.submit();


}

</script>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>
 
 <form name="skillpolicyform" method="post" action="<?=$ME?>">
    <input type="hidden" name="skillpolicyid" value="">
</form>
<form name="schedulepolicyform" method="post" action="<?=$ME?>">
   <input type="hidden" name="schedulepolicyid" value="">
</form>


<table cellspacing="0" cellpadding="0" width="450" >
  <tr>
    <td class=clubid<?=get_clubid()?>th height=60><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <tr>
     <td>
              <table cellpadding="0" cellspacing="0" class=clubid<?=get_clubid()?>th>
               <tr>
                  <td align=left width=84><img src="<?=$_SESSION["CFG"]["imagedir"]?>/ReservationPolicyOn.gif" border="0"></td>
                  <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/message_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/MessagesOff.gif" border="0"></a></td>
                  <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/general_preferences.php"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/GeneralPreferencesOff.gif" border="0"></a></td>
                    <td width=100%></td>
               </tr>
           </table>
     </td>
 </tr>


  <tr>
   <td class="generictable">

      <table width="450" cellpadding="6" cellspacing="6" border="0">
             <tr>
                 <td>

                      <table width="450" cellpadding="1" cellspacing="0" border="0">
                        <tr>
                           <td align="left">
                               <span class="label">Scheduling Policies</span> 
                               <span class=normal><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php">Add</a></span>
                               <br/>
                           </td>
                         </tr>
                      </table>
                      
                       <table width="400" cellpadding="1" cellspacing="0" border="0">
                           <?  if( mysql_num_rows($reservationPolicies) < 1){   ?>
                               <tr>
                                   <td width="15"></td>
                                   <td class=normal>No Scheduling Policies Defined.</td>
                               </tr>
                     <?   } else {    ?>

                                <? 
                                	$rownum = mysql_num_rows($reservationPolicies);
                                
                                	while($reservationPolicy = mysql_fetch_array($reservationPolicies)){
                                
                                	  
                                	?>

                                 <tr class="normal">
                                     <td width="15"></td>
                                     <td class=normal align="left" width="300">
                                     	
                                     		<span style="text-decoration: underline">
	                                     		<?=$reservationPolicy['policyname'] ?>
	                                     	</span>
	                                     		
                                     	
                                     	<span class="normalsm"><?=$reservationPolicy['description'] ?></span>
                                     </td>
                                     <td class=normal align="left" style="vertical-align: top;">
                                     	<a href="javascript:submitForm('manageReservationPolicyForm<?=$reservationPolicy['policyid']?>')">Edit</a>
                                     	<a href="javascript:removeSchedulingPolicy(<?=$reservationPolicy['policyid'] ?>);">Delete</a>
                                     	<form name="manageReservationPolicyForm<?=$reservationPolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php" method="post">
                                     		<span style="font-weight: bold">
                                     		<input type="hidden" name="policyid" value="<?=$reservationPolicy['policyid'] ?>">
                                     	</form>
                                     </td>
                                </tr>

                                <? } ?>
                       <? } ?>
                       </table>

                 </td>
             </tr>
           
             <tr>
                 <td>

                    <table width="450" cellpadding="1" cellspacing="0" border="0">
                        <tr>
                           <td align="left">
                               <span class="label">Skill Range Policies</span> 
                               <span class=normal><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php">Add</a></span>
                               <br/>
                           </td>
                         </tr>
                      </table>
                      
                       <table width="400" cellpadding="1" cellspacing="0" border="0">

                      <?  if( mysql_num_rows($skillRangePolicies) < 1){   ?>
                             <tr>
                               <td width="15"></td>
                               <td class=normal>No Skill Range Policies Defined.</td>

                          </tr>
                     <?   } else {    ?>


                                <? while($skillRangePolicy = mysql_fetch_array($skillRangePolicies)){?>

                                 <tr>
                                     <td width="15"></td>
                                     <td class="normal" align="left" width="300">
	                                    
	                                     	<span style="text-decoration: underline">
		                                     	<?=$skillRangePolicy['policyname'] ?>
		                                     </span>
	                                    
	                                     <span class="normalsm"><?=$skillRangePolicy['description'] ?></span>
                                     </td>
                                     <td class=normal align="left" style="vertical-align: top">
                                     	<a href="javascript:submitForm('manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>')">Edit</a>
                                     	<a href="javascript:removeSkillRangePolicy(<?=$skillRangePolicy['policyid'] ?>);">Delete</a>
                                     	<form name="manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php" method="post">
		                                    <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid'] ?>">
	                                     </form> 	
                                     </td>

                                </tr>

                                <? } ?>
                       <? } ?>
                       </table>

                 </td>
             </tr>
               <tr>
                 <td height="20"></td>
             </tr>
      </table>

     </td>
     </tr>
</table>



</td>
</tr>
</table>
<?
/*
 * $LastChangedRevision: 815 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-01-30 17:00:33 -0600 (Sun, 30 Jan 2011) $
 */
?>

<script type="text/javascript">

function removeSkillRangePolicy(policyid)
{

      document.skillpolicyform.skillpolicyid.value = policyid;
      document.skillpolicyform.submit();


}

</script>


 <form name="skillpolicyform" method="post" action="<?=$ME?>">
    <input type="hidden" name="skillpolicyid" value="">
    <input type="hidden" name="preferenceType" value="skill">
</form>


<table width="400" cellpadding="5" cellspacing="2" class="tabtable">
<tr>
	<td>
      
       				<table width="450" cellpadding="1" cellspacing="0" border="0">
                        <tr>
                           <td align="left">
                               <span class="biglabel">Skill Range Policies</span> 
	                               <span class="normal">
	                               		<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php">Add</a>
	                               	</span>
                               <br/>
                           </td>
                         </tr>
                      </table>
                      
                       <table width="450" cellpadding="1" cellspacing="0" border="0">

                      <?  if( mysql_num_rows($skillRangePolicies) < 1){   ?>
                             <tr>
                              
                               <td class="normal" colspan="2">No Skill Range Policies Defined.</td>

                          </tr>
                     <?   } else {    ?>


                                <? 
                                
                                $rownum = mysql_num_rows($skillRangePolicies);
                                
                                while($skillRangePolicy = mysql_fetch_array($skillRangePolicies)){
                                		$rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                                ?>

                                  <tr class="<?=$rc?>" >
                                     
                                     <td class="normal" align="left" >
	                                    
	                                     	<span style="text-decoration: underline">
		                                     	<?=$skillRangePolicy['policyname'] ?>
		                                     </span>
	                                    
	                                     <span class="normalsm"><?=$skillRangePolicy['description'] ?></span>
                                     </td>
                                     <td class=normal align="left" style="vertical-align: top" width="75px">
                                     	<a href="javascript:submitForm('manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>')">Edit</a>
                                     	<a href="javascript:removeSkillRangePolicy(<?=$skillRangePolicy['policyid'] ?>);">Delete</a>
                                     	<form name="manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php" method="post">
		                                    <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid'] ?>">
	                                     </form> 	
                                     </td>

                                </tr>

                                <? 
                                $rownum = $rownum - 1;
                                } ?>
                       <? } ?>
                       </table>
            </td>
           </tr>
                       
 </table>

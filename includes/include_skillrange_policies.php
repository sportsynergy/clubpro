
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


<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php">Add</a> 


      <table class="table table-bordered table-striped">
        <?  if( mysqli_num_rows($skillRangePolicies) < 1){   ?>
        <tr>
          <td colspan="2">No Skill Range Policies Defined.</td>
        </tr>
        <?   } else {    
                              
            while($skillRangePolicy = mysqli_fetch_array($skillRangePolicies)){ ?>
        <tr>
          <td>
            <?=$skillRangePolicy['policyname'] ?>
            
            <?=$skillRangePolicy['description'] ?>
          </td>
          <td>
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
      



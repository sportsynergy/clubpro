
<script type="text/javascript">
		
			function removeSchedulingPolicy(policyid)
			{
			      document.schedulepolicyform.schedulepolicyid.value = policyid;
			      document.schedulepolicyform.submit();
			}
		
		</script>

<form name="schedulepolicyform" method="post" action="<?=$ME?>">
  <input type="hidden" name="schedulepolicyid" value="">
  <input type="hidden" name="preferenceType" value="schedule">
</form>

<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php">Add</a> 

<table class="table table-bordered table-striped">

      
        <?  if( mysqli_num_rows($reservationPolicies) < 1){   ?>
        <tr>
          <td colspan="2">No Scheduling Policies Defined.</td>
        </tr>
        <?   } else {    ?>
        <? 
                                	
           while($reservationPolicy = mysqli_fetch_array($reservationPolicies)){ ?>
        <tr >
          <td>
            <?=$reservationPolicy['policyname'] ?>
            
            <?=$reservationPolicy['description'] ?>
            </td>
          <a href="javascript:submitForm('manageReservationPolicyForm<?=$reservationPolicy['policyid']?>')">Edit</a> 
          <a href="javascript:removeSchedulingPolicy(<?=$reservationPolicy['policyid'] ?>);">Delete</a>
            <form name="manageReservationPolicyForm<?=$reservationPolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php" method="post">
              <input type="hidden" name="policyid" value="<?=$reservationPolicy['policyid'] ?>">
            </form>
          </td>
        </tr>
        <? } ?>
        <? } ?>
      </table>
    
   
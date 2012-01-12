<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 * $LastChangedRevision: 815 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-01-30 17:00:33 -0600 (Sun, 30 Jan 2011) $
*/
?>
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
<table width="550" cellpadding="6" cellspacing="6" border="0" class="tabtable">
  <tr>
    <td><table width="550" cellpadding="1" cellspacing="0" border="0">
        <tr>
          <td align="left"><span class="biglabel">Scheduling Policies</span> <span class=normal> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php">Add</a> </span> <br/></td>
        </tr>
      </table>
      <table width="400" cellpadding="1" cellspacing="0" border="0">
        <?  if( mysql_num_rows($reservationPolicies) < 1){   ?>
        <tr>
          <td class="normal" colspan="2">No Scheduling Policies Defined.</td>
        </tr>
        <?   } else {    ?>
        <? 
                                	$rownum = mysql_num_rows($reservationPolicies);
                                	
                                	
                                	while($reservationPolicy = mysql_fetch_array($reservationPolicies)){
                                		$rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                                	  
                                	?>
        <tr class="<?=$rc?>">
          <td class="normal" align="left" ><span style="text-decoration: underline">
            <?=$reservationPolicy['policyname'] ?>
            </span> <span class="normalsm">
            <?=$reservationPolicy['description'] ?>
            </span></td>
          <td class=normal align="left" style="vertical-align: top;" width="75px"><a href="javascript:submitForm('manageReservationPolicyForm<?=$reservationPolicy['policyid']?>')">Edit</a> <a href="javascript:removeSchedulingPolicy(<?=$reservationPolicy['policyid'] ?>);">Delete</a>
            <form name="manageReservationPolicyForm<?=$reservationPolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_scheduling_policy.php" method="post">
              <span style="font-weight: bold">
              <input type="hidden" name="policyid" value="<?=$reservationPolicy['policyid'] ?>">
            </form></td>
        </tr>
        <? 
                                	 $rownum = $rownum - 1;
                                	} 
                                ?>
        <? } ?>
      </table></td>
  </tr>
</table>

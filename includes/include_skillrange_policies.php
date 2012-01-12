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
    <td><table width="450" cellpadding="1" cellspacing="0" border="0">
        <tr>
          <td align="left"><span class="biglabel">Skill Range Policies</span> <span class="normal"> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php">Add</a> </span> <br/></td>
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
          <td class="normal" align="left" ><span style="text-decoration: underline">
            <?=$skillRangePolicy['policyname'] ?>
            </span> <span class="normalsm">
            <?=$skillRangePolicy['description'] ?>
            </span></td>
          <td class=normal align="left" style="vertical-align: top" width="75px"><a href="javascript:submitForm('manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>')">Edit</a> <a href="javascript:removeSkillRangePolicy(<?=$skillRangePolicy['policyid'] ?>);">Delete</a>
            <form name="manageSkillRangePolicyForm<?=$skillRangePolicy['policyid']?>" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_skill_range_policy.php" method="post">
              <input type="hidden" name="policyid" value="<?=$skillRangePolicy['policyid'] ?>">
            </form></td>
        </tr>
        <? 
                                $rownum = $rownum - 1;
                                } ?>
        <? } ?>
      </table></td>
  </tr>
</table>

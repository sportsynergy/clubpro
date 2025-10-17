<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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
?>


<p class="bigbanner"><? pv($DOC_TITLE) ?></p>

<form name="entryform" method="post" action="<?=$ME?>">

      <div class="mb-3">
           <select name="report" class="form-select" aria-label="Report Selection">
                <option value="">Select Report</option>

                <? if ($frm["report"]== "memberactivity") {
                    $selected_report1 = "selected";
                } elseif ($frm["report"]== "courtutil"){
                    $selected_report2 = "selected";
                }
                    ?>
                <option value="memberactivity" <?=$selected_report1?>>Member Activity Report</option>
                <option value="courtutil" <?=$selected_report2?>>Court Utilization Report</option>
                
                <? if ( isJumpLadderRankingScheme() ) { 
                    
                    // for each ladder 
                    $result = getLaddersForSite( get_siteid() );
                    while($ladder = mysqli_fetch_array($result)){  
                        
                        if ( $ladder['id'] == $ladderid ){
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        
                        ?>
                        <option value="ladderreport-<?=$ladder['id']?>" <?=$selected?>><?=$ladder['name'] ?> Score Report</option>
                        <option value="ladderexport-<?=$ladder['id']?>" <?=$selected?>><?=$ladder['name'] ?> Full Export</option>
                   
                    <? } ?>
               
                <? }  ?>
               </select>

        </div>

          <input type="hidden" name="submitme" value="submitme">
          <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Run Report</button>
          
          
</form>

<script type="text/javascript">

function onSubmitButtonClicked(){
	submitForm('entryform');
}


</script>                 
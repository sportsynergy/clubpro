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
<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}



</script>

<form name="entryform" method="post" action="<?=$ME?>">
<table cellspacing="0" cellpadding="5" width="700" id="formtable">

       <tr>
           <td><font class="reportSubTitle">
           <?pv($reportName)?>
           </font>
           </td>
           <td><select name="report">
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
                   
                    <? } ?>
               
                <? }  ?>
               </select>
          <input type="hidden" name="submitme" value="submitme">
          <input type="button" name="submit" value="Run Report" id="submitbutton">
          </td>

       </tr>
 </table>
</form>
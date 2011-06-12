<?php
/*
 * $LastChangedRevision: 819 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-01 07:44:09 -0600 (Tue, 01 Feb 2011) $
 */
?>

<form name="entryform" method="post" action="<?=$ME?>">
<table cellspacing="0" cellpadding="5" width="700" >

       <tr>
           <td><font class=reportTitle>
           <?pv($reportName)?>
           </font>
           </td>
           <td><select name="report">
                <option value="">Select Report</option>
                <option value="memberactivity">Member Activity Report</option>
                <option value="courtutil">Court Utilization Report</option>
               </select>
          <input type="submit" name="submit" value="Run Report">
          </td>

       </tr>
 </table>
</form>
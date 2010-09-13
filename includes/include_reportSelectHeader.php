<?php
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 13:46:27 -0700 (Fri, 26 May 2006) $
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
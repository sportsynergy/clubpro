<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

*/
?>


<form name="entryform" method="post" action="<?=$ME?>">

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>

<td>

<?
$reportName = "Club Reports";
include($_SESSION["CFG"]["includedir"]."/include_reportSelectHeader.php");
?>


</td>
</tr>
        <tr>
            <td class="normal">
                Welcome to the club reporting environment.  This is a powerful tool to manage club resources in order to maximize club efficiency.   To run a report simply select from the drop down and menu and hit run report.

             </td>
        </tr>
</table>

</form>

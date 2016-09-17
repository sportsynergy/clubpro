


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
                Welcome to the club reports.  This is a little tool to manage club resources in order to maximize club efficiency.   To run a report simply select from the drop down and menu and hit run report.

             </td>
        </tr>
</table>

</form>

<?php
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 13:46:27 -0700 (Fri, 26 May 2006) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<?


if (!empty($errormsg)) {
        echo "<h2 style='color: #ff0000'>$errormsg</h2>";

}
?>

<?
if (!empty($noticemsg)) {
        echo "<div class=notice>";
        echo $noticemsg;
        echo "</div>";
}

?>

</td>
</tr>
</table>
<?
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $

*/

$msg = "";
$class = "none";

if (!empty($errormsg)) {

	$class = "problem";
	$msg = $errormsg;

 } elseif(!empty($noticemsg)){
 	$class = "notice";
 	$msg = $noticemsg;
 	
 }

 ?>


<div class="<?=$class?>" style="height: 30px" id="message_div">
	<?=$msg?>
</div>


<?


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


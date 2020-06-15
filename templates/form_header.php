<?php

$msg = "";
$class = "none";

if (!empty($errormsg)) {
	$class = "problem";
	$msg = $errormsg;
} else if (!empty($noticemsg)) {
 	$class = "notice";
 	$msg = $noticemsg;
}

?>
<div class="<?php echo $class; ?>" style="height:45px" id="message_div">
	<?php echo $msg; ?>
</div>
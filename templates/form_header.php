<?php

$msg = "";
$class = "none";

if (!empty($errormsg)) {
	$class = "alert alert-danger";
	$msg = $errormsg;
} else if (!empty($noticemsg)) {
 	$class = "alert alert-success";
 	$msg = $noticemsg;
}

?>
<div class="<?php echo $class; ?>"  id="message_div" role="alert">
  <?php echo $msg; ?>
	
</div>


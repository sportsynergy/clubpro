<?php

$msg = "";
$close = "";
$class = "none";


if (!empty($errormsg)) {
	$class = "alert alert-danger alert-dismissible fade show";
	$close = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
	$msg = $errormsg;
} else if (!empty($noticemsg)) {
 	$class = "alert alert-warning alert-dismissible fade show";
	$close = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
 	$msg = $noticemsg;
}

?>
<div class="<?php echo $class; ?>"  id="message_div" role="alert">
  <?php echo $msg; ?>
 <?php echo $close; ?>
  
</div>


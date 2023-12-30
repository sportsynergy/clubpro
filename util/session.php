<?php
session_start();
print("here is my session: ");
print($_SESSION['LAST_ACTIVITY']);

$timeleft = time() - $_SESSION['LAST_ACTIVITY'];

print("here is the time left $timeleft");

?>
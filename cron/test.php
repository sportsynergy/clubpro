<?php

$curtime = time();
$currYear = date("Y", $curtime);
$currMonth = date("n", $curtime);
$currDay = date("j", $curtime);

print("currDay $currDay, currMonth $currMonth currYear $currYear");

$yesterday = date('Y-m-d', time() - 60 * 60 * 24);

print("yesterday: $yesterday");

?>
<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");




$winner = $_REQUEST['winner'];
$loser = $_REQUEST['loser'];
$ladder = $_REQUEST['ladder'];

if( empty($winner) || empty($loser) || empty($ladder)){
	echo "please specify all of the variables";
} else{
	adjustClubLadder($winner,$loser,$ladder);
    echo "done. look at the logs now."	;
}



?>
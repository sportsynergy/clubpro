<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/ladderlib.php");

$ladderid = $_REQUEST['ladderid'];

header('Content-type: text/csv');
header('Content-disposition: attachment;filename=bookings.csv');

// get site parameters
$csvHeaderArray = array();

// Start with the headers
array_push($csvHeaderArray, "Date");
array_push($csvHeaderArray, "Winner");
array_push($csvHeaderArray, "Loser");
array_push($csvHeaderArray, "Score");
array_push($csvHeaderArray, "League");


//Print the Headers
print implode(",", $csvHeaderArray);
print "\n";


$ladderMatchResult = getLadderMatches($ladderid, 1000000 );

// Print the Data
while ($row = mysqli_fetch_array($ladderMatchResult)) {

    $csvDataArray = array();
    $challengeDate = explode(" ",$row['match_time']);
    $winner =  $row['winner_first']." ". $row['winner_last'];
    $loser =  $row['loser_first']." ". $row['loser_last'];
    $score = '="'.$row['score'].'"';
    $match_time = $row['match_time'];
    $winner_id = $row['winner_id'];
    $loser_id = $row['loser_id'];


    logMessage("ladder_score_log: processing match with winner $winner and loser $loser and score $score and match_time $match_time and winner_id $winner_id and loser_id $loser_id");

    if(  $row['league'] == 1 ){
        $league = getBoxLeagueForPlayers($row['winner_id'], $row['loser_id'], $ladderid, $match_time);
    } else {
            $league = "";
     }
   

    array_push($csvDataArray, wrapWithDoubleQuotes( formatDateStringSimple( $challengeDate[0] )));
    array_push($csvDataArray, wrapWithDoubleQuotes($winner));
    array_push($csvDataArray, wrapWithDoubleQuotes($loser));
    array_push($csvDataArray, $score);
    array_push($csvDataArray, wrapWithDoubleQuotes($league));
    
    print implode(",", $csvDataArray);
    print "\n";
}
/**
 *
 * @param unknown_type $string
 */
function wrapWithDoubleQuotes($string) {
    return '"' . trim($string, '"') . '"';
}
?>
<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/clubadminlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

$DOC_TITLE = "Club Event";

//Load in Date
$eventid = $_REQUEST["clubeventid"];

//put this in session

if (isset($eventid)) {
    $_SESSION["clubeventid"] = $eventid;
} else{
         // go to timeout link
         $timeoutlink = $_COOKIE["timeoutlink"];
         header("Location: $timeoutlink");
}

if (match_referer() && isset($_POST['cmd'])) {
    $frm = $_POST;

    // Add user to Club Event
    
    if ($frm['cmd'] == 'addtoevent') {
        logMessage("club_event.validate_form: adding user to club event");
        $userid = $frm['userid'];
        $division = $frm['division'];
        $clubeventid = $frm['clubeventid'];
        addToClubEvent($userid, $clubeventid, $division);
    }

    // Remove user from Club Event
    
    if ($frm['cmd'] == 'removefromevent') {
        logMessage("club_event.validate_form: removing user/team to club event");
        $userid = $frm['userid'];
        $clubeventid = $frm['clubeventid'];
        removeFromClubEvent($userid, $clubeventid);
    }
    
    // Add a player and a guest
    if ($frm['cmd'] == 'addtoeventasteam') {
        logMessage("club_event.validate_form: adding team to club event");
        $userid = $frm['userid'];
        $partnerid = $frm['partnerid'];
        $clubeventid = $frm['clubeventid'];
        $division = $frm['division'];

        $errormsg = validate_form($frm, $errors);

        if (empty($errormsg)) {
            addToClubEventAsTeam($userid, $partnerid, $clubeventid, $division);
        } 

    }
}


$clubEventResult = loadClubEvent($_SESSION["clubeventid"]);
$clubEventParticipants = getClubEventParticipants($_SESSION["clubeventid"]);
$alreadySignedUp = isClubEventParticipant(get_userid(), $clubEventParticipants, $division);

include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/club_event_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form(&$frm, &$errors) {
    $errors = new clubpro_obj;

    logMessage("club_event.validate_form: checking if parter for ". $frm['userid']." is alredy signed up");

    $clubEventParticipants = getClubEventParticipants($_SESSION["clubeventid"]);
    
    
    if ( empty($frm["userid"]) ){
        return "Please select a user from the dropdown menu.";
    } 

    if ( empty($frm["partnerid"]) ){
        return "Please select a user from the dropdown menu.";
    } 

    $userSignedUp = isClubEventParticipant(trim($frm['userid']), $clubEventParticipants, $frm["division"]);

    if ( $userSignedUp ){
        return "I am sorry but you're already signed up for this event.";
    } 

    if ( $frm["division"]=="--"  ){
        return "Please select a division";
    } 

    $partnerSignedUp = isClubEventParticipant(trim($frm['partnerid']), $clubEventParticipants, $frm["division"]);
    
    if ( $partnerSignedUp ){
        return "I am sorry but your partner is already signed up for this event.";
    } 




    

}
?>


<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* - validate_form()
* - update_reservation()
* - insert_reservation()
* - makeEventReservation()
* - makeSoloReservation()
* - makeDoublesReservation()
* - makeSinglesReservation()
* Classes list:
*/
/*
 A user can come into this page from a link in an email where the reservation has since changed
sice the email was sent out.  This email is sent when players are looking for a match.  Here
is an example of this kind of link
http://localhost/clubpro/users/court_reservation.php?time=1285426800&courtid=5&user=2

Now, the first time this link is clicked on the system will ask if they user wants to sign up for the court.  The problem happens when that first user adds hisself to the reservation and a second players comes in later and clicks on the same link. So as to prevent the second persom from being able to signup for this court, a little check needs to be made that makes sure that:

1.) if this page is being loaded from the link in a players wanted email
2.) The reservation has alredy has the maximum number or people in it

then

an error message will result notifing the user that the reservation is full.

*/

/*****************************************************************************
 *
 * Do some administrative things
 *
/*****************************************************************************/

include ("../application.php");
require "../vendor/autoload.php";

require ($_SESSION["CFG"]["libdir"] . "/reservationlib.php");
require ($_SESSION["CFG"]["libdir"] . "/courtlib.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}


$DOC_TITLE = "Court Reservation";

//Set the http variables
$courtid = $_REQUEST["courtid"];
$time = $_REQUEST["time"];
$userid = $_REQUEST["userid"];
$courttype = $_REQUEST["courttype"];
$courtid = $_REQUEST["courtid"];

if (isDebugEnabled(1)) {
    logMessage("court_reservation: loading page with the these variables: 
    courtid: $courtid, time: $time, userid: $userid, courttype: $courttype, courtid: $courtid");
}


/* In case the user is loading this page from a link on an email,
we have to load in the site preferences (normally this is done in
the scheduler content. */

$siteprefs = getSitePreferencesForCourt($courtid);
$_SESSION["siteprefs"] = $siteprefs;
require_loginwq();

if (isDebugEnabled(1)) {
    logMessage("court_reservation: user(".get_userid().") setting site preference for court $courtid");
}

/*****************************************************************************
 *
 * Process Form Post
 *
 ******************************************************************************/

if ( match_referer() && isset( $courttype ) ) {

    // Set some variables
    $frm = $_POST;
    $wwwroot = $_SESSION["CFG"]["wwwroot"];

    // Validate
    $errormsg = validate_form($frm, $errors);
    
    if (empty($errormsg)) {
        
        //Actually Make the Reservation
        if ($frm['action'] == "create") {
            $resid = insert_reservation($frm);
        } else {
            $resid = update_reservation($frm);
        }

        if (isDebugEnabled(1)) logMessage("Inserting the reservation $resid");

        /**
         * ReDirect the user to the reservation_details screen so they can advertise the match
         */
        
        if ($frm['action'] == "create" && $frm['playertwoid'] == "" && $frm['matchtype'] == 1) {
            
            if (isDebugEnabled(1)) {
                logMessage("court_reservation: prompting for more details as matchtype is 1 and opponent is not set");
            }
            $boxid = getBoxIdForUser(get_userid());
            header("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time&boxid=$boxid");
        } elseif ($frm['action'] == "create" && $frm['courttype'] == "singles" && ($frm['playeroneid'] == "" || $frm['playertwoid'] == "") && !isGuestPlayer($frm['playeroneid'], $frm['playeronename']) && !isGuestPlayer($frm['playertwoid'], $frm['playertwoname']) && ($frm['matchtype'] == 0 || $frm['matchtype'] == 1 || $frm['matchtype'] == 2 || $frm['matchtype'] == 4)) {
            
            if (isDebugEnabled(1)) {
                logMessage("court_reservation: courttype is singles and opponent is empty");
            }
            header("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
        }

        // If any spots are open for doubles
        else 
        if (

        // If any of the spots are empty
        $frm['courttype'] == "doubles" && $frm['action'] == "create" && (!isGuestPlayer($frm['playeroneid'], $frm['playeronename']) && empty($frm['playeroneid']) || !isGuestPlayer($frm['playertwoid'], $frm['playertwoname']) && empty($frm['playertwoid']) || !isGuestPlayer($frm['playerthreeid'], $frm['playerthreename']) && empty($frm['playerthreeid']) || !isGuestPlayer($frm['playerfourid'], $frm['playerfourname']) && empty($frm['playerfourid']))) {
            
            if (isDebugEnabled(1)) {
                logMessage("Prompting for more details as this is a front desk doubles reservation and a name is empty");
            }
            header("Location: $wwwroot/users/reservation_details.php?resid=$resid&time=$time");
        } else {
            $tzdelta = get_tzdelta();
            header("Location: $wwwroot/clubs/" . get_sitecode() . "/index.php?daysahead=" . gmmktime(0, 0, 0, gmdate("n", $time + $tzdelta) , gmdate("j", $time + $tzdelta) , gmdate("Y", $time + $tzdelta)) . "");
        }
        die;
    } else {

        // This did not pass the form validation, set these variables for the same form
        $userid = $frm['userid'];
    }
} else {

    if (isDebugEnabled(1)) {
        logMessage("court_reservation: not processing for user(".get_userid().") - courttype is not set: " . $_POST['courttype']);
    }

}

/******************************************************************************
 * Get the type of reservation.  This is used a lot.
 ******************************************************************************/
$newReservation = FALSE;
$userTypeQuery = "SELECT usertype, matchtype, guesttype, lastmodified, reservationid, locked, creator
					FROM tblReservations reservations
					WHERE reservations.time = $time 
					AND reservations.courtid = $courtid
					AND reservations.enddate is NULL";


$userTypeResult = db_query($userTypeQuery);

if (mysqli_num_rows($userTypeResult) > 0) {
    $reservationArray = mysqli_fetch_array($userTypeResult);
    $usertype = $reservationArray['usertype'];
    $matchtype = $reservationArray['matchtype'];
    $guesttype = $reservationArray['guesttype'];
    $lastupdated = $reservationArray['lastmodified'];
    $reservationid = $reservationArray['reservationid'];
    $locked = $reservationArray['locked'];
	$creator = $reservationArray['creator'];
    
    if (isDebugEnabled(1)) {
        logMessage("court_reservation: setting usertype: $usertype, matchtype: $matchtype, guesttype: $guesttype, and lastupdated:$lastupdated ");
    }
} else {
    
    if (isDebugEnabled(1)) {
        logMessage("court_reservation: this is a new reservation ");
    }
    $newReservation = TRUE;
}

/******************************************************************************
 *  Run Form Load Validation, stuff to do before loading first form
 ******************************************************************************/

if (get_roleid() == 1) {
    
    $courttypeid = getCourtTypeIdForCourtId($courtid);
    $reservationtypeid = getReservationTypeIdForCourtId($courtid);
    
    if ( $reservationtypeid != 3 && 
        (
        !amiValidForSite(get_siteid()) || !isValidForCourtType($courttypeid, get_userid())
        )
    ) {
        $errormsg = "Sorry, you are not authorized to reserve this court.";
        include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
        include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
        die;
    }
}

if (get_roleid() == 5) {
    $errormsg = "Sorry, you are not authorized to reserve this court.  Talk to the pro about getting set up to do this.";
    include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
    include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");
    die;
}

/******************************************************************************
 * Load Forms
 ******************************************************************************/
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
/**
 * Determine what form to display
 */

if ($newReservation) {
             
     $currDOW = getDOW(gmdate("l", $time));
     $hoursquery = "SELECT duration from tblCourtHours WHERE courtid='$courtid' AND dayid ='$currDOW' ";
     $hoursresult = db_query($hoursquery);
     $reservation_duration = mysqli_result($hoursresult,0);

    include ($_SESSION["CFG"]["templatedir"] . "/reservation_form.php");
} elseif ($usertype == 0 && isSinglesReservationNeedPlayers($time, $courtid)) {
    
    if ($userid == get_userid() || get_roleid() == 2 || get_roleid() == 4) {
        include ($_SESSION["CFG"]["includedir"] . "/include_update_singles_form.php");
    } else {
        include ($_SESSION["CFG"]["includedir"] . "/include_signup_singles_form.php");
    }
} else 
if ($usertype == 1 && isDoublesReservationNeedPlayers($time, $courtid)) {

    $teamQuery = "SELECT reservationdetails.userid, reservationdetails.usertype
                        FROM tblReservations reservations, tblkpUserReservations reservationdetails
                        WHERE reservations.reservationid = reservationdetails.reservationid
                        AND reservations.time=$time
						AND reservations.courtid=$courtid
						AND reservations.enddate IS NULL
						ORDER BY reservationdetails.usertype DESC, reservationdetails.userid DESC";
    $teamResult = db_query($teamQuery);
    $teamRow = mysqli_fetch_array($teamResult);
    $player1userId = $teamRow['userid'];
    $player1userType = $teamRow['usertype'];
    $teamRow = mysqli_fetch_array($teamResult);
    $player2userId = $teamRow['userid'];
    $player2userType = $teamRow['usertype'];

    // if curent user is an admin or they are they user where a single person is needed
    // or where they are on the team where a doubles team is needed

    
    if ((get_roleid() == 2 || get_roleid() == 4) || ($player1userType == 0 && $player1userId == get_userid()) || ($player2userType == 0 && $player2userId == get_userid()) || ($player1userType == 1 && isCurrentUserOnTeam($player1userId)) || ($player2userType == 1 && isCurrentUserOnTeam($player2userId))) {
        include ($_SESSION["CFG"]["includedir"] . "/include_update_doubles_form.php");
    } elseif ($player1userType == 1 && $player2userType == 0 && $player2userId == 0) {
        include ($_SESSION["CFG"]["includedir"] . "/include_team_signup_doubles_form.php");
    } elseif ($player1userType == 0 && $player1userId != 0 && $player2userType == 0 && $player2userId != 0) {
        include ($_SESSION["CFG"]["includedir"] . "/include_players_signup_doubles_form.php");
    } elseif ($player1userType == 0 && $player1userId != 0 && $player2userType == 0 && $player2userId == 0) {
        include ($_SESSION["CFG"]["includedir"] . "/include_player_team_signup_doubles_form.php");
    } elseif ($player1userType == 1 && $player1userId != 0 && $player2userType == 0 && $player2userId != 0) {
        include ($_SESSION["CFG"]["includedir"] . "/include_doublesplayer_wanted_form.php");
    } else {
        $supportemail = $_SESSION["CFG"]["support"];
        print 'This reservation is messed up. Please contact <a href="mailto:'.$supportemail.'">'.$supportemail.'</a> to sort this out.';
    }
} else {
    print "Sorry, that match is already fully subscribed. Better luck next time!";
}
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * Validate the FORMS
 *
 * These are the the forms for creating a singles reservation:
 *
 * \includes\include_reservation_singles.php
 *
 * These are the forms for updating a singles reservation:
 *
 * \includes\include_update_singles.php (for admins or players in the reservation) action:create
 * \includes\include_signup_singles.php (for people signing up to play) action: addpartner
 *
 * or
 *
 * These are the forms for creating a doubles reservation:
 *
 * \includes\include_reservation_doubles.php (for creating a new reservation) action:create
 *
 * These are the forms for updating a doubles reservation
 *
 * \includes\include_team_signup_doubles.php (for signing up another team)  action: addteam
 * \includes\include_doublesplayer_wanted_form.php (for signing up the last person) action: addpartner
 * \includes\include_players_signup_doubles_form.php (for signing one of the two teams) action: addpartners
 * \includes\include_player_team_doubles_form.php (for signing one of the two teams) action: addplayerorteam
 *
 *****************************************************************************/
function validate_form(&$frm, &$errors) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $msg = "";
    $errors = new Object;
    
    if (isDebugEnabled(1)) logMessage("court_reservation_validate_form() ");

    //First we have to get the reservationid
    $residquery = "SELECT tblReservations.lastmodified
	                             FROM tblReservations
	                             WHERE tblReservations.courtid=" . $frm['courtid'] . "
	                             AND tblReservations.time=" . $frm['time'] . "
								 AND tblReservations.enddate IS NULL";

    //this is just a way to know if this is a new reservation.
    $residresult = db_query($residquery);
    $resArray = mysqli_fetch_array($residresult);
    $reservationTimeStamp = $resArray['lastmodified'];

    /******************************************
     * Validate Event Reservation
     ******************************************/
    
    if ($frm['courttype'] == "event" || $frm['matchtype'] == "4" ) {
        
        if ($frm["repeat"] != "norepeat" && empty($frm["frequency"]) && $frm["action"] != "addpartner") {
            $errors->duration = true;
            $msg.= "You did not specify the duration interval ";
        }
    }

    /******************************************
     * Validate Singles Reservation
     ******************************************/
    elseif ($frm['courttype'] == "singles") {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating a Singles Reservation " . $frm["playertwoid"] . ".");
        
        if ($frm['action'] == "addpartner" && $frm["lastupdated"] != $reservationTimeStamp) {
            $msg = "Sorry, somebody out there just reserved this court. Please refresh the page and try again.";
        } elseif ($frm['action'] == "create" && get_roleid() == 1 && !empty($frm["playeroneid"]) && !empty($frm["playertwoid"]) && !validateSkillPolicies($frm["playeroneid"], $frm["playertwoid"], $frm['courtid'], $frm['courttype'], $frm['time'])) {
            $msg = "A skill range policy is preventing you from reserving this court with this opponent.";
        }

        //If a solo rervation by an admin make sure that playername is specified
        elseif ($frm['action'] == "create" && $frm['matchtype'] == 5 && empty($frm['playeroneid'])) {
            $msg.= "Please specify a player for the solo reservation.";
        }

        // Validate Box Leagues
        elseif ($frm['action'] == "create" && $frm['matchtype'] == 1 && !empty($frm['playeroneid']) && !empty($frm['playertwoid'])) {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating a new singles box league reservation");
            $boxid = getBoxIdForUser($frm["playeroneid"]);
            
            if ($boxid > 0) {
                
                if (isBoxExpired($frm['time'], $boxid)) {
                    $msg.= "We are sorry but by the time these guys end up playing this match the box league will be done.";
                }
            }

            //Check that if player one is specified that he is in box
            
            if (!empty($frm['playeroneid']) && !is_inabox($frm['courtid'], $frm['playeroneid'])) {
                $msg.= "$frm[playeronename] is not currently setup to play in a box league.";
            }

            //Check that if player two is specified that he is in box
            elseif (!empty($frm["playertwoid"]) && !is_inabox($frm["courtid"], $frm["playertwoid"])) {
                $msg.= "$frm[playertwoname] is not currently setup to play in a box league.";
            }

            //if there are both players are specified that at least they are in a box togehter.
            elseif (!empty($frm["playeroneid"]) && !empty($frm["playertwoid"]) && !are_boxplayers($frm["playeroneid"], $frm["playertwoid"])) {
                $msg.= "$frm[playeronename] and $frm[playertwoname] are not in a box league together.";
            } elseif (hasPlayedBoxWith($frm["playeroneid"], $frm["playertwoid"], $boxid)) {
                $msg.= "Hold on, we just checked and  $frm[playeronename] and $frm[playertwoname] are already scheduled to play or have already played in this box. ";
            }
        }

        //Validate form when SIGNING UP for a box league reservation
        elseif ($frm['action'] == "addpartner" && $frm['matchtype'] == 1) {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating Singles Box League Reservation");
            
            if (!are_boxplayers($frm["guylookingformatch"], get_userid())) {
                $msg.= "You don't seem to be in a box league with this person";
            }
        }

        //If this is is made as a buddy reservation make sure that this person is in fact....a buddy
        elseif (get_roleid() == 1 && $frm['action'] == "addpartner" && $frm['matchtype'] == 3) {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating Singles Buddy Reservation");
            
            if (!amIaBuddyOf($frm['guylookingformatch'])) {
                $fullnameResult = db_query("SELECT firstname, lastname from tblUsers WHERE userid=" . $frm['guylookingformatch']);
                $buddyArray = mysqli_fetch_array($fullnameResult);
                $msg.= "I am sorry but $buddyArray[firstname] $buddyArray[lastname] is looking for a match with a buddy";
            }
        }

        // People can't play themselves, except for with special accounts (Club Member, Club Guest)
        elseif ($frm['action'] == "create" && !isClubMemberName($frm["playeronename"]) && !isClubGuestName($frm["playeronename"]) //Club Members and Club Guests can play each other

         && $frm["playeroneid"] == $frm["playertwoid"] //Names aren't the same

        ) {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating that singles players can't play each other");
            $msg.= "Please specify different players";
        }

        //Club Members or Club Guests cannot look for matches
        elseif ($frm['action'] == "create" && isClubMemberName($frm["playeronename"]) && empty($frm["playertwoname"])) {
            $msg.= "Please register the club member to advertise for this match";
        }

        //Nor can club guests
        elseif ($frm['action'] == "create" && isClubGuestName($frm["playeronename"]) && empty($frm["playertwoname"])) {
            $msg.= "Please register the club guest to advertise for this match";
        }

        // Guest Reservations are only allowed on sites that are setup for  it.
        elseif ($frm['action'] == "create" && !isSiteGuestReservationEnabled() && get_roleid() == 1 && (isGuestPlayer($frm["playeroneid"], $frm["playeronename"]) || isGuestPlayer($frm["playertwoid"], $frm["playertwoname"]))) {
            $msg.= "It appears that you did not select your opponent correctly from the dropdown menu";
        } else 
        if (get_roleid() == 1 || get_roleid() == 5) {
            $msg = validateSchedulePolicies($frm['courtid'], $frm['time'], $frm['playeroneid']);
        }
    }

    /******************************************
     * Validate Doubles Reservation
     ******************************************/
    else if ($frm['courttype'] == "doubles") {
        
        if (isDebugEnabled(1)) {
            logMessage("court_reservation.validate_form(): Validating Doubles Reservation");
        }
        
        if (($frm['action'] == "addteam" || $frm['action'] == "addpartner" || $frm['action'] == "addpartners" || $frm['action'] == "addplayerorteam") && $frm["lastupdated"] != $reservationTimeStamp) {
            $msg = "Sorry, somebody out there just reserved this court. Please refresh the page and try again.";
        }

        //for validating reservation_doublesplayer_and_team_wanted_form
        else 
        if ($frm['action'] == "addplayerorteam" && $frm['userid'] == $frm['partner']) {
            $msg.= "The person you want to be partners with is already in the reservation.";
        }

        // For buddy reservations, regular players have to be a buddy of at least one of the people already playing
        else 
        if (get_roleid() == 1 && $frm['matchtype'] == 3 && $frm['action'] == "addteam") {
            
            if (isDebugEnabled(1)) logMessage("court_reservation_validate_form(): Validating Buddy Reservation.");
            $iHaveABuddy = FALSE;
            $fullNameSearchQuery = "SELECT teamdetails.userid, users.firstname, users.lastname
					FROM tblUsers users, tblkpTeams teamdetails 
					WHERE users.userid = teamdetails.userid
					AND teamdetails.teamid=" . $frm['teamid'];
            $fullNameSearchResult = db_query($fullNameSearchQuery);
            $playerArray = mysqli_fetch_array($fullNameSearchResult);
            
            if (amIaBuddyOf($playerArray[0])) {
                $iHaveABuddy = TRUE;
            }
            $firstName0 = $playerArray[1];
            $lastName0 = $playerArray[2];
            $playerArray = mysqli_fetch_array($fullNameSearchResult);
            
            if (amIaBuddyOf($playerArray[0])) {
                $iHaveABuddy = TRUE;
            }
            $firstName1 = $playerArray[1];
            $lastName1 = $playerArray[2];
            
            if (!$iHaveABuddy) {
                $msg.= "We're sorry but $firstName0 $lastName0 and $firstName1 $lastName1 are looking for a match with a buddy.";
            }
        }

        // Make sure at least two people when solo reservation is enabled
        elseif ($frm['action'] == "create") {
            
            if (isDebugEnabled(1)) logMessage("court_reservation_validate_form(): Validating a player making a new doubles reservation");
            
            if ((isGuestPlayer($frm['playeroneid'], $frm['playeronename']) || isGuestPlayer($frm['playertwoid'], $frm['playertwoname']) || isGuestPlayer($frm['playerthreeid'], $frm['playerthreename']) || isGuestPlayer($frm['playerfourid'], $frm['playerfourname']))) {
                $guestReservation = true;
            } else {
                $guestReservation = false;
            }

            // Dont' allow regular players to type in names, they have to pick them from the drop down.
            
            if (!isSiteGuestReservationEnabled() && $guestReservation && (get_roleid() == 1 || get_roleid() == 5)) {
                return "Please pick all of the player names from the drop down list";
            }
            
            if (isSiteGuestReservationEnabled() && (get_roleid() == 1 || get_roleid() == 5)) {

                //Set the playerone name
                
                if (get_roleid() == 1 || get_roleid() == 5) {
                    $playerOneName = get_userfullname();
                } else {
                    $playerOneName = $frm['playeronename'];
                }

                // If any of the players are guests, all players must be specified
                
                if ($guestReservation && (empty($playerOneName) || empty($frm['playertwoname']) || empty($frm['playerthreename']) || empty($frm['playerfourname']))) {
                    return "Please type in all names from the drop down list";
                }
            }
            
            if (isSoloReservationEnabled()) {
                
                if (!$guestReservation && !isAtLeastOnePlayerSpecifiedForDoubles($frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'])) {
                    return "For doubles, please specify at least one person";
                }
            } else {

                //for a regular player, they have to put in at least two people
                
                if (get_roleid() == 1 && !$guestReservation && !isAtLeastTwoPlayerSpecifiedForDoubles($frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'])) {
                    return "For doubles, please specify at least two people";
                }

                //Administrators can still make a solo reservation
                
                if ((get_roleid() == 2 || get_roleid == 4) && !isAtLeastOnePlayerSpecifiedForDoubles($frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'])) {
                    return "For doubles, please specify at least one person";
                }
            }
        }

        // Check for duplicates on create form
        elseif ($frm['action'] == "create" && !isAtLeastOnePlayerDuplicatedForDoubles($frm['playeroneid'], $frm['playertwoid'], $frm['playerthreeid'], $frm['playerfourid'])) {
            $msg.= "Please specify different people in the opposing team.";
        }

        // Check for duplicates on addteam form
        elseif ($frm['action'] == "addteam") {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form: Validating the addteam form");
            $teamThatsAlreadyPlayingResult = getUserIdsForTeamId($frm["userid"]);
            $playerRow = mysqli_fetch_array($teamThatsAlreadyPlayingResult);
            $teamPlayerOne = $playerRow['userid'];
            $playerRow = mysqli_fetch_array($teamThatsAlreadyPlayingResult);
            $teamPlayerTwo = $playerRow['userid'];

            //make sure they aren't signing up with someone already in the reservation
            
            if (!empty($frm['partnerid']) && ($teamThatsAlreadyPlaying[0] == $frm['partnerid'] || $teamThatsAlreadyPlaying[1] == $frm['partnerid'])) {
                $msg.= "It looks like " . getFullNameForUserId($partnerId) . " is already playing.  Try picking someone else";
            }
            
            if (isGuestPlayer($frm['partnerid'], $frm['partnername'])) {
                $msg = "Please select " . $frm['partnername'] . " from the dropdown list";
            }
        }

        // Check for duplicates on addplayerorteam form
        elseif ($frm['action'] == "addplayerorteam") {
            
            if (isDebugEnabled(1)) logMessage("court_reservation.validate_form: Validating the addpartners form");

			// Make sure that the skill range policies are ok
			if(!validateSkillPolicies($frm["userid"], $frm["creator"], $frm['courtid'], $frm['courttype'], $frm['time'])){
				$msg = "A skill range policy is preventing you from reserving this court with this opponent.";
				return $msg;
			}
			
			//for buddy matches
			if($frm['matchtype'] == 3 && (get_roleid() == 1 || get_roleid() == 2 || get_roleid() == 5)){
				
				 if (isDebugEnabled(1)) logMessage("court_reservation.validate_form(): Validating Singles Buddy Reservation");

		            if (!amIaBuddyOf($frm['creator'])) {
		                $fullnameResult = db_query("SELECT firstname, lastname from tblUsers WHERE userid=" . $frm['creator']);
		                $buddyArray = mysqli_fetch_array($fullnameResult);
		                $msg.= "I am sorry but $buddyArray[firstname] $buddyArray[lastname] is looking for a match with a buddy";
		            	return $msg;
					}
			}
			
            // Make sure that partner is not userid
            if (isGuestPlayer($frm["partner"], $frm["partnername"])) {
                $msg.= "Please select your partner from the drop down menu";
            }

            // Make sure that partner is not userid
            if (!empty($frm["partner"]) && $frm["partner"] == $frm["userid"]) {
                $msg.= "Please select a different partner";
				return $msg;
            }
        }

        //Make sure that if player three and four are left empty that ids one or two are set (that team one is no guest)
        elseif ($frm['action'] == "create" && empty($frm["playerthreename"]) && empty($frm["playerfourname"]) && empty($frm["playeroneid"]) && empty($frm["playertwoid"])) {
            $msg.= "You have to put at least one name in";
        }

        // Check guest reservations
        elseif ($frm['action'] == "create" && !isSiteGuestReservationEnabled() && get_roleid() == 1 && (isGuestPlayer($frm["playeroneid"], $frm["playeronename"]) || isGuestPlayer($frm["playertwoid"], $frm["playertwoname"]) || isGuestPlayer($frm["playerthreeid"], $frm["playerthreename"]) || isGuestPlayer($frm["playerfourid"], $frm["playerfourname"]))) {
            $msg.= "It appears that you did not select all of the players from the dropdown menu.";
        }

        // If one name is typed in, they all have to be
        elseif ($frm['action'] == "create" && isFrontDeskGuestDoublesReservation($frm["playeroneid"], $frm["playeronename"], $frm["playertwoid"], $frm["playertwoname"], $frm["playerthreeid"], $frm["playerthreename"], $frm["playerfourid"], $frm["playerfourname"]) && isDoublesSpotAvailable($frm["playeronename"], $frm["playertwoname"], $frm["playerthreename"], $frm["playerfourname"])) {
            $msg.= "If you type in at least one name, you aren't allowed to leave any spot open.";
        } else {
            $msg = validateSchedulePolicies($courtid, $time);
        }
    } 
    else if( $frm['courttype'] == "resource" ){
        // nothing to validate with resource reservations
    }
    else {
        die("no valid forms error 202");
    }
    return $msg;
}

/******************************************
 *
 * Update a reservation
 *
 *******************************************/
function update_reservation(&$frm) {

    /* Update old reservation */
    
    if (isDebugEnabled(1)) logMessage("court_reservation.update_reservation:");

    /* First thing we do is check to see if this was made from a
     players wanted form if so will we update the court appropriately. */

    //First we have to get the reservationid
    $residquery = "SELECT tblReservations.reservationid,tblReservations.matchtype
                             FROM tblReservations
                             WHERE tblReservations.courtid='$frm[courtid]'
                             AND tblReservations.time='$frm[time]'
							 AND tblReservations.enddate IS NULL";
    $residresult = db_query($residquery);

    //this is just a way to know if this is a new reservation.
    $reservation = mysqli_num_rows($residresult);
    $resArray = mysqli_fetch_array($residresult);
    $residval = $resArray['reservationid'];
    $matchtype = $resArray['matchtype'];
    
    if ($frm['courttype'] == "singles" && $frm['action'] == "addpartner") {

        // Now we just need to update that reservation
        $qid = db_query("UPDATE tblkpUserReservations SET userid = " . get_userid() . "
      					WHERE reservationid = $residval
                       	AND userid = 0");

        // Now we just need to update that reservation
        $qid = db_query("UPDATE tblReservations SET lastmodifier = " . get_userid() . "
      					WHERE reservationid = $residval");

        //Update the boxhistory table
        
        if ($matchtype == 1) {
            $boxid = getBoxIdForUser(get_userid());
            $query = "INSERT INTO tblBoxHistory (
		                     boxid, reservationid
		                     ) VALUES (
			$boxid
		                            ,'$residval')";
            $result = db_query($query);
        }

        //Send out update emails
        confirm_singles($residval, false);
        return $residval;
    }

    // This is the form that allows a player to sign up either with
    // an player or pick a partner from a list.

    // reservation_doublesplayer_and_team_wanted_form.php

    elseif ($frm['courttype'] == "doubles" && $frm['action'] == "addplayerorteam") {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.update_reservation: A Player is updating reservation where one player is looking for a partner and a team is needed too (action: addplayerorteam )");

        // Get Court Type for making the team
        $courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
        $qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = " . get_userid() . "
                              WHERE reservationid = $residval");

        // Playwith is variable only used on this page for indicating who the player wants
        // to play with, either the person who made the reservation or someone else.

        
        if ($frm['playwith'] == "1") {
            $currentTeamID = getTeamIDForCurrentUser($courtTypeId, $frm['userid']);

            //Replace the individual with the new team
            $qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[userid]");
        }

        //Sign up with a partner of this persons choosing
        else {
            $currentTeamID = getTeamIDForCurrentUser($courtTypeId, $frm['partner']);

            // Now we just need to update that reservation
            $qid = db_query("UPDATE tblkpUserReservations
                       SET userid =$currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = 0");
        }

        //Send out update emails
        confirm_doubles($residval, false);
        return $residval;
    }

    // This is the form that allows a player to sign up either with
    // include_doublesplayer_wanted_form.php

    elseif ($frm['courttype'] == "doubles" && $frm['action'] == "addpartners") {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.update_reservation: A player is updating reservation where two players were looking for a partner (action: addpartners)");

        // Get Court Type for making the team
        $courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
        $currentTeamID = getTeamIDForCurrentUser($courtTypeId, $frm['partner']);

        // Update the last modifier
        $qid1 = db_query("UPDATE tblReservations
							  SET lastmodifier = " . get_userid() . "
	                          WHERE reservationid = $residval");

        // Now we just need to update that reservation
        $qid = db_query("UPDATE tblkpUserReservations
                       SET userid = $currentTeamID,
                       usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $frm[partner]
					   AND usertype = 0");
        return $residval;
    }

    //Check to see if we are to add a team to a reservation
    elseif ($frm['courttype'] == "doubles" && $frm['action'] == "addteam") {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.update_reservation: A Player is updating reservation where a team was looking for another team (action: addteam)");
        
        if (empty($frm['partnerid'])) {
            
            if (isDebugEnabled(1)) logMessage("\tThe partner is not set, just adding the user");

            // Now we just need to update that reservation
            $qid = db_query("UPDATE tblkpUserReservations
		                       SET userid = " . get_userid() . ",usertype = 0
		                       WHERE reservationid = $residval
		                       AND userid = 0");
        } else {
            
            if (isDebugEnabled(1)) {
                logMessage("\tThe partner is set, adding the team.");
            }
            $courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
            $currentTeamID = getTeamIDForCurrentUser($courtTypeId, $frm['partnerid']);
            $updateQuery = "UPDATE tblkpUserReservations
		                       SET userid =$currentTeamID, usertype=1
		                       WHERE reservationid = $residval
		                       AND userid = 0";

            // Now we just need to update that reservation
            $qid = db_query($updateQuery);
        }

        // Update the last modifier
        $qid1 = db_query("UPDATE tblReservations
						  SET lastmodifier = " . get_userid() . "
                          WHERE reservationid = $residval");

        //Send out update emails
        confirm_doubles($residval, false);
        return $residval;
    }

    // Check to see if we are to add a single player to a team in the reservtion.  To do this we
    // will first add the make the team if it doesn't exist already.  Then we will reset the

    // player with the new or existing teamid.  Finally we will reset the usertype on the tlbkpReservation table

    elseif ($frm['courttype'] == "doubles" && $frm['action'] == "addpartner") {
        
        if (isDebugEnabled(1)) logMessage("court_reservation.update_reservation: A Player is updating reservation where a player was looking for a partner (addpartner)");
        $courtTypeId = getCourtTypeIdForCourtId($frm['courtid']);
        $currentTeamID = getTeamIDForCurrentUser($courtTypeId, $frm['userid']);

        //Update the last modifier
        $qid1 = db_query("UPDATE tblReservations
						  SET lastmodifier = " . get_userid() . "
                          WHERE reservationid = $residval");

        //UPdate the tblkpUserReservation Table
        $qid = db_query("UPDATE tblkpUserReservations SET userid =$currentTeamID
                       WHERE reservationid = $residval
                       AND userid = $frm[userid]
                       AND usertype = 0");

        //Now set the usertype to reflect a team reservation
        $qid = db_query("UPDATE tblkpUserReservations SET usertype = 1
                       WHERE reservationid = $residval
                       AND userid = $currentTeamID");

        //Send out update emails
        confirm_doubles($residval, false);
        return $residval;
    }
}

/******************************************
 *
 * Insert a reservation
 * @return the reservation id
 *
 *******************************************/
function insert_reservation(&$frm) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.insert_reservation");

    // Initialize Guest Type
    $guesttype = 0;
    
    if ($frm['courttype'] == "event" ||
		( $frm['matchtype']==4 && $frm['courttype'] == "singles") //also allow reoccuring singles
		) {
        
        return makeReoccuringReservation($frm);
    }
	
    
    if ($frm['courttype'] == "singles" && $frm['action'] == "create" && (isGuestPlayer($frm['playeroneid'], $frm['playeronename']) || isGuestPlayer($frm['playertwoid'], $frm['playertwoname']))) {
        $guesttype = 1;
    }

    /* Or This is a guest reservation if its a a doubles reservation and
    */
    elseif ($frm['courttype'] == "doubles" &&

    // if ther is a player name with no player id on any of the players
    isFrontDeskGuestDoublesReservation($frm['playeroneid'], $frm['playeronename'], $frm['playertwoid'], $frm['playertwoname'], $frm['playerthreeid'], $frm['playerthreename'], $frm['playerfourid'], $frm['playerfourname'])) {
        $guesttype = 1;
    }
    $lock = "n";
    
    if (isset($frm['lock'])) {
        $lock = "y";
    }

	if( isset($frm['duration'])  && $frm['duration'] != 0 ){
		$duration = $frm['duration'] * 3600;
	}else{
		$duration = "NULL";
	}

    // Add the Reservation
    $resquery = "INSERT INTO tblReservations (
	                courtid, time, matchtype, guesttype, lastmodifier, creator, createdate, locked, duration
	                ) VALUES (
	                          '$frm[courtid]'
	                          ,'$frm[time]'
	                          ,'$frm[matchtype]'
	                          ,$guesttype
							  , " . get_userid() . "
							  , " . get_userid() . "
							  , now() 
							  , '$lock'
							  ,$duration)";
    db_query($resquery);

    //Now we need to get the reservationid.  (This is what we just inserted )
    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
	                       AND time='$frm[time]' AND enddate IS NULL";
    $residresult = db_query($residquery);
    $residvarresult = db_fetch_object($residresult);
    
    if ($frm['courttype'] == "doubles") {
        makeDoublesReservation($frm, $guesttype, $residvarresult->reservationid);
    } elseif ($frm['courttype'] == "singles") {
        
        if ($frm['matchtype'] == "5") {
            makeSoloReservation($frm, $residvarresult->reservationid);
        } else {
            makeSinglesReservation($frm, $guesttype, $residvarresult->reservationid);
        }
    }
    return $residvarresult->reservationid;
}



/**
 * Makes an makeReoccuringReservation reservations
 * @return the last reservation id made
 */
function makeReoccuringReservation(&$frm) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.makeReoccuringReservation: Making a Reoccuring Reservation");
    
$clubquery = "SELECT timezone from tblClubs WHERE clubid=" . get_clubid() . "";
    $clubresult = db_query($clubquery);
    $clubobj = db_fetch_object($clubresult);
    $courtid = $frm['courtid'];
    $tzdelta = $clubobj->timezone * 3600;
    $locked = "n";
    
    if (isset($frm["lock"])) {
        $locked = "y";
    }
    
	if(isset($frm['duration'])){
		$duration = $frm['duration'] * 3600;
	}else{
		$duration = "NULL";
	}
	
	
    if (isDebugEnabled(1)) logMessage("court_reservation.makeReoccuringReservation: Repeat interval is " . $frm['repeat'] . " and frequency inverval is " . $frm['frequency']. " and playertwoid is ". $frm['playertwoid']);

        $resquery = "INSERT INTO tblReservations (
	                 courtid, eventid, time, lastmodifier, creator, createdate, locked, matchtype, duration
	                 ) VALUES (
	                           '$frm[courtid]'
	                           ,'$frm[eventid]'
	                           ,'$frm[time]'
							   , " . get_userid() . "
							   ,  " . get_userid() . "
							   , now() 
							   ,'$locked'
							   , '$frm[matchtype]'
							   , $duration)";
							
				
        $resresult = db_query($resquery);

		//Now we need to get the reservationid.  (This is what we just inserted )
	    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
		                       AND time='$frm[time]' AND enddate IS NULL";
	    $residresult = db_query($residquery);
	    $residvarresult = db_fetch_object($residresult);
		$reservationid = $residvarresult->reservationid;

		if( empty($frm['playeroneid'])  ){
            $playeroneid = 0;
        } else {
             $playeroneid = $frm['playeroneid'];
        }
			 
			$query = "INSERT INTO tblkpUserReservations (
			                                reservationid, userid, usertype
			                                ) VALUES (
			                                          '$reservationid'
			                                          ,'$playeroneid'
			                                          ,0)";

		        // run the query on the database
		        $result = db_query($query);
		
		if( empty($frm['playertwoid']) ){
            $playertwoid = 0;
        } else {
            $playertwoid = $frm['playertwoid'];
        }
		
        $query = "INSERT INTO tblkpUserReservations (
					                          reservationid, userid, usertype
					                           ) VALUES (
					                                     '$reservationid'
					                                      ,'$playertwoid'
					                                      ,0)";

    	 // run the query on the database
    	 $result = db_query($query);
		


		confirm_singles($reservationid, true);

    // Add the daily event
    if ($frm['repeat'] == "daily") {
        
		$initialHourstart = 0;

        //Set the occurance interval
        
        if ($frm['frequency'] == "week") $numdays = 7;
        
        elseif ($frm['frequency'] == "month") $numdays = 30;
        
        elseif ($frm['frequency'] == "year") $numdays = 365;

	if (isDebugEnabled(1)) logMessage("court_reservation.booking a weekly reservation: ". $numdays);

        for ($i = 0; $i < $numdays; $i++) {
            $nextday = gmmktime(gmdate("H", $frm['time']) , gmdate("i", $frm['time']) , gmdate("s", $frm['time']) , gmdate("n", $frm['time']) , gmdate("j", $frm['time']) + $i, gmdate("Y", $frm['time']));

            // Set the event interval.  This will be the duration for the court for that day
            $dayOfWeek = gmdate("w", $nextday);
            $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
            $courtHourResult = db_query($courtHourQuery);
            $courtHourArray = mysqli_fetch_array($courtHourResult);

            //Save off the first reservation time
            
            if ($i > 0) {
                $hourstart = $initialHourstart - $courtHourArray["hourstart"];
                $nextday-= ($hourstart * 60);
            } else {
                $startday = $nextday;
                $initialHourstart = $courtHourArray["hourstart"];
            }

            
            if (!isCourtAlreadyReserved($frm['courtid'], $nextday)) {

                //Add as reservation
                $resquery = "INSERT INTO tblReservations (
					                 courtid, eventid, time, lastmodifier, creator, locked, matchtype, duration
					                 ) VALUES (
					                           '$frm[courtid]'
					                           ,'$frm[eventid]'
					                           ,$nextday
											   , " . get_userid() . "
											   , " . get_userid() . "
											   , '$locked'
											   , '$frm[matchtype]'
											   , $duration)";
                $resresult = db_query($resquery);

					//Now we need to get the reservationid.  (This is what we just inserted )
				    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
					                       AND time='$nextday' AND enddate IS NULL";
				    $residresult = db_query($residquery);
				    $residvarresult = db_fetch_object($residresult);
					$reservationid = $residvarresult->reservationid;
					
				if( isset($frm['playeroneid'])  ){

					if (isDebugEnabled(1)) logMessage("court_reservation:booking for playerone:  $reservationid");	
					
					$query = "INSERT INTO tblkpUserReservations (
					                                reservationid, userid, usertype
					                                ) VALUES (
					                                          '$reservationid'
					                                          ,'$frm[playeroneid]'
					                                          ,0)";

				        // run the query on the database
				        $result = db_query($query);
				}
					if( isset($frm['playertwoid']) ){
					 
					if (isDebugEnabled(1)) logMessage("court_reservation:booking for playertwo: $reservationid");
					
						$query = "INSERT INTO tblkpUserReservations (
							                          reservationid, userid, usertype
							                           ) VALUES (
							                                     '$reservationid'
							                                      ,'$frm[playertwoid]'
							                                      ,0)";

						 // run the query on the database
						 $result = db_query($query);
				}
				


            }else{
				if (isDebugEnabled(1)) logMessage("court_reservation:this court is already reserved.");	
			}
        }

        //Add as reoccuring event
        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
								courtid, eventinterval, starttime, endtime
							) VALUES (
								$frm[courtid],
								86400,
								$startday,
								$nextday)";
        db_query($reoccuringQuery);
    }

    //Add the weekly event
    elseif ($frm['repeat'] == "weekly") {

        //Set the occurance interval
        
        if ($frm['frequency'] == "week") $numdays = 7;
        
        if ($frm['frequency'] == "month") $numdays = 30;
        
        if ($frm['frequency'] == "year") $numdays = 365;
        $initialHourstart = 0;

        for ($i = 0; $i < $numdays; $i+= 7) {
            $nextday = gmmktime(gmdate("H", $frm['time']) , gmdate("i", $frm['time']) , gmdate("s", $frm['time']) , gmdate("n", $frm['time']) , gmdate("j", $frm['time']) + $i, gmdate("Y", $frm['time']));

            // Set the event interval.  This will be the duration for the court for that day
            $dayOfWeek = gmdate("w", $nextday);
            $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
            $courtHourResult = db_query($courtHourQuery);
            $courtHourArray = mysqli_fetch_array($courtHourResult);

            //Save off the first reservation time
            
            if ($i > 0) {
                $hourstart = $initialHourstart - $courtHourArray["hourstart"];
                $nextday-= ($hourstart * 60);
            } else {
                $startday = $nextday;
                $initialHourstart = $courtHourArray["hourstart"];
            }
            
            if (!isCourtAlreadyReserved($frm['courtid'], $nextday)) {

                //Add as reservation
                $resquery = "INSERT INTO tblReservations (
			                 courtid, eventid, time, lastmodifier, creator, locked, matchtype, duration
			                 ) VALUES (
			                           '$frm[courtid]'
			                           ,'$frm[eventid]'
			                           ,$nextday
									   , " . get_userid() . "
									   , " . get_userid() . "
									   , '$locked'
									, '$frm[matchtype]'
									 , $duration)";
									
                $resresult = db_query($resquery);

				//Now we need to get the reservationid.  (This is what we just inserted )
			    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
				                       AND time='$nextday' AND enddate IS NULL";
			    $residresult = db_query($residquery);
			    $residvarresult = db_fetch_object($residresult);
				$reservationid = $residvarresult->reservationid;
				
				if( isset($frm['playeroneid'])  ){

					$query = "INSERT INTO tblkpUserReservations (
					                                reservationid, userid, usertype
					                                ) VALUES (
					                                          '$reservationid'
					                                          ,'$frm[playeroneid]'
					                                          ,0)";

				        // run the query on the database
				        $result = db_query($query);
				}
					if( isset($frm['playertwoid']) ){
						$query = "INSERT INTO tblkpUserReservations (
							                          reservationid, userid, usertype
							                           ) VALUES (
							                                     '$reservationid'
							                                      ,'$frm[playertwoid]'
							                                      ,0)";

						 // run the query on the database
						 $result = db_query($query);
				}
				
			
            }
        }

        //Add as reoccuring event
        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
								courtid, eventinterval, starttime, endtime
									) VALUES (
										$frm[courtid],
										604800,
										$startday,
										$nextday)";
        db_query($reoccuringQuery);
    }

    //Add the weekly event
    elseif ($frm['repeat'] == "biweekly") {

        //Set the occurance interval
        
        if ($frm['frequency'] == "week") $numdays = 7;
        
        if ($frm['frequency'] == "month") $numdays = 28;
        
        if ($frm['frequency'] == "year") $numdays = 365;
        $initialHourstart = 0;
        for ($i = 0; $i < $numdays; $i+= 14) {
            $nextday = gmmktime(gmdate("H", $frm['time']) , gmdate("i", $frm['time']) , gmdate("s", $frm['time']) , gmdate("n", $frm['time']) , gmdate("j", $frm['time']) + $i, gmdate("Y", $frm['time']));

            // Set the event interval.  This will be the duration for the court for that day
            $dayOfWeek = gmdate("w", $nextday);
            $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
            $courtHourResult = db_query($courtHourQuery);
            $courtHourArray = mysqli_fetch_array($courtHourResult);

            //Save off the first reservation time
            
            if ($i > 0) {
                $hourstart = $initialHourstart - $courtHourArray["hourstart"];
                $nextday-= ($hourstart * 60);
            } else {
                $startday = $nextday;
                $initialHourstart = $courtHourArray["hourstart"];
            }
            
            if (!isCourtAlreadyReserved($frm['courtid'], $nextday)) {

                //Add as reservation
                $resquery = "INSERT INTO tblReservations (
					                 courtid, eventid, time, lastmodifier, creator, locked, matchtype, duration
					                 ) VALUES (
					                           '$frm[courtid]'
					                           ,'$frm[eventid]'
					                           ,$nextday
											   , " . get_userid() . "
											   , " . get_userid() . "
											   , '$locked'
												, '$frm[matchtype]'
												, $duration)";
												
                $resresult = db_query($resquery);

				//Now we need to get the reservationid.  (This is what we just inserted )
			    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
				                       AND time='$nextday' AND enddate IS NULL";
			    $residresult = db_query($residquery);
			    $residvarresult = db_fetch_object($residresult);
				$reservationid = $residvarresult->reservationid;
					
				if( isset($frm['playeroneid'])  ){

					$query = "INSERT INTO tblkpUserReservations (
					                                reservationid, userid, usertype
					                                ) VALUES (
					                                          '$reservationid'
					                                          ,'$frm[playeroneid]'
					                                          ,0)";

				        // run the query on the database
				        $result = db_query($query);
				}
					if( isset($frm['playertwoid']) ){
						$query = "INSERT INTO tblkpUserReservations (
							                          reservationid, userid, usertype
							                           ) VALUES (
							                                     '$reservationid'
							                                      ,'$frm[playertwoid]'
							                                      ,0)";

						 // run the query on the database
						 $result = db_query($query);
				}
				

            }
        }

        //Add as reoccuring event
        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
								courtid, eventinterval, starttime, endtime
								) VALUES (
									$frm[courtid],
									604800,
									$startday,
									$nextday)";
        db_query($reoccuringQuery);
    }

    //Add the monthly event
    elseif ($frm['repeat'] == "monthly") {

        //Set the occurance interval
        
        if ($frm['frequency'] == "week") $numdays = 1;
        
        if ($frm['frequency'] == "month") $numdays = 1;
        
        if ($frm['frequency'] == "year") $numdays = 12;
        $initialHourstart = 0;
        for ($i = 0; $i < $numdays; $i++) {
            $nextday = gmmktime(gmdate("H", $frm['time']) , gmdate("i", $frm['time']) , gmdate("s", $frm['time']) , gmdate("n", $frm['time']) + $i, gmdate("j", $frm['time']) , gmdate("Y", $frm['time']));

            // Set the event interval.  This will be the duration for the court for that day
            $dayOfWeek = gmdate("w", $nextday);
            $courtHourQuery = "SELECT * from tblCourtHours WHERE dayid = $dayOfWeek AND courtid = $courtid";
            $courtHourResult = db_query($courtHourQuery);
            $courtHourArray = mysqli_fetch_array($courtHourResult);

            //Save off the first reservation time
            
            if ($i > 0) {
                $hourstart = $initialHourstart - $courtHourArray["hourstart"];
                $nextday-= ($hourstart * 60);
            } else {
                $startday = $nextday;
                $initialHourstart = $courtHourArray["hourstart"];
            }
            
            if (!isCourtAlreadyReserved($frm['courtid'], $nextday)) {

                //Add as reservation
                $resquery = "INSERT INTO tblReservations (
				                 courtid, eventid, time, lastmodifier, creator, locked, matchtype, duration
				                 ) VALUES (
				                           '$frm[courtid]'
				                           ,'$frm[eventid]'
				                           ,$nextday
										   , " . get_userid() . "
										   , " . get_userid() . "
										   , '$locked'
										   , '$frm[matchtype]'
									       , $duration)";
									
                $resresult = db_query($resquery);

				//Now we need to get the reservationid.  (This is what we just inserted )
			    $residquery = "SELECT reservationid FROM tblReservations WHERE courtid= '$frm[courtid]'
				                       AND time='$nextday' AND enddate IS NULL";
			    $residresult = db_query($residquery);
			    $residvarresult = db_fetch_object($residresult);
				$reservationid = $residvarresult->reservationid;
				
				if( isset($frm['playeroneid'])  ){

					$query = "INSERT INTO tblkpUserReservations (
					                                reservationid, userid, usertype
					                                ) VALUES (
					                                          '$reservationid'
					                                          ,'$frm[playeroneid]'
					                                          ,0)";

				        // run the query on the database
				        $result = db_query($query);
				}
					if( isset($frm['playertwoid']) ){
						$query = "INSERT INTO tblkpUserReservations (
							                          reservationid, userid, usertype
							                           ) VALUES (
							                                     '$reservationid'
							                                      ,'$frm[playertwoid]'
							                                      ,0)";

						 // run the query on the database
						 $result = db_query($query);
				}
				


            }
        }

        //Add as reoccuring event
        $reoccuringQuery = "INSERT INTO tblReoccuringEvents (
								courtid, eventinterval, starttime, endtime
							) VALUES (
								$frm[courtid],
								2419200,
								$startday,
								$nextday)";
        db_query($reoccuringQuery);
    }

    return $reservationid;
}
/**
 * Make a solo reservation
 *
 */
function makeSoloReservation($frm, $reservationid) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.makeSoloReservation");

    //if this is empty that means that the user typed something in, a guest reservation
    
    if (empty($frm['playeroneid'])) {
        $userid = $frm['playeronename'];

        //If there is name is set, that means that
        $query = "INSERT INTO tblkpGuestReservations (
		                                reservationid, name
		                                ) VALUES (
		                                          '$reservationid'
		                                          ,'$userid')";

        // run the query on the database
        $result = db_query($query);
    }

    //Else the playeroneid is set and we can use it for a normal reservation
    else {
        $userid = $frm['playeroneid'];

        //Make Player Ones Reservation
        $query = "INSERT INTO tblkpUserReservations (
	                                reservationid, userid, usertype
	                                ) VALUES (
	                                          '$reservationid'
	                                          ,$userid
	                                          ,0)";

        // run the query on the database
        $result = db_query($query);

        //ONly send out emails to registered users
        confirm_singles($reservationid, true);
    }
}
/**
 *
 * Make a doubles reservation
 * pass in the frm object, guesttype (which was already figured out), reservation id
 *
 */
function makeDoublesReservation($frm, $guesttype, $reservationid) {
    
    if (isDebugEnabled(1)) logMessage("court_reservation.makeDoublesReservation");

    //Set the User type value to 1 to indicate that the userid specify teamids
    // not userids.  A usertype of 0 will be the default where userids are userids

    // in the tblkpUserReservations.

    $qid = db_query("UPDATE tblReservations
                          SET usertype = 1
                          WHERE reservationid = $reservationid");

    //Get the tblCourtType id for this court
    $courttypeid = getCourtTypeIdForCourtId($frm['courtid']);

    // Now update the users (either front guest type or regular)
    //check if userids are presetn

    
    if ($guesttype == 0) {

        /*
         * If playertwoid = nopartner, then that means that the team one is actually a single
         * looking for a partner, in that case usertype will be 0 (single)
        */
        
        if (empty($frm['playertwoid'])) {
            $teamid1 = $frm['playeroneid'];
            $teamone_usertype = 0;
        } elseif (empty($frm['playeroneid'])) {
            $teamid1 = $frm['playertwoid'];
            $teamone_usertype = 0;
        } else {
            $teamid1 = getTeamIDForPlayers($courttypeid, $frm['playeroneid'], $frm['playertwoid']);
            $teamone_usertype = 1;
        }
        $query = "INSERT INTO tblkpUserReservations (
	                                reservationid, userid, usertype
	                                ) VALUES (
	                                       '$reservationid'
	                                        ,$teamid1
	                                        ,$teamone_usertype)";

        // run the query on the database
        $result = db_query($query);

        /* There is the distinct possibility that a club administrator or front desk user
         * left playerthree and playerfour empty.  what this means is that they will be
         * making this reservation looking for another team
         *
        */
        
        if (empty($frm['playerthreeid']) && empty($frm['playerfourid'])) {
            $teamid2 = 0;
            $teamtwo_usertype = 0;
        } else {

            /*
             * Something has been entered, now we just check if team two has two players
             * looking for a partner, in that case usertype will be 0 (single)
            */
            
            if (empty($frm['playerfourid'])) {
                $teamid2 = $frm['playerthreeid'];
                $teamtwo_usertype = 0;
            } elseif (empty($frm['playerthreeid'])) {
                $teamid2 = $frm['playerfourid'];
                $teamtwo_usertype = 0;
            } else {
                $teamid2 = getTeamIDForPlayers($courttypeid, $frm['playerthreeid'], $frm['playerfourid']);
                $teamtwo_usertype = 1;
            }
        }
        $query = "INSERT INTO tblkpUserReservations (
	                            reservationid, userid, usertype
	                            ) VALUES (
	                                   '$reservationid'
	                                    ,$teamid2
	                                    ,$teamtwo_usertype)";

        // run the query on the database
        $result = db_query($query);
        confirm_doubles($reservationid, true);
    } elseif ($guesttype == 1) {

        // Set the player one name
        
        if (get_roleid() == 1) {
            $playerOneName = get_userfullname();
        } else {
            $playerOneName = $frm['playeronename'];
        }
        $query = "INSERT INTO tblkpGuestReservations (
                                           reservationid, name
                                           ) VALUES (
                                           '$reservationid'
                                           ,'$playerOneName - $frm[playertwoname]')";

        // run the query on the database
        $result = db_query($query);
        $query = "INSERT INTO tblkpGuestReservations (
                                                 reservationid, name
                                                 ) VALUES (
                                                 '$reservationid'
                                                 ,'$frm[playerthreename] - $frm[playerfourname]')";

        // run the query on the database
        $result = db_query($query);
    }
}
/**
 * Make a plain old singles reservation.  Thats right, a plain old singles reservation
 * This takes a form object, a guesttype, and the reservationid
 */
function makeSinglesReservation($frm, $guesttype, $reservationid) {
    $playerone = $frm['playeroneid'];
    $playertwo = $frm['playertwoid'];
    
    if (isDebugEnabled(1)) logMessage("court_reservation.makeSinglesReservation: playerone: $playerone playertwo $playertwo and guesttype is $guesttype");

    //Set these puppies
    
    if (empty($playerone)) {
        $playerone = 0;
    }
    
    if (empty($playertwo)) {
        $playertwo = 0;
    }

    /*Right now the rule is if either one of the players here are club guests then
    both of them will be considered club guests (even if one of them is a member).
    The way we check this is if a playerid is sent.  If we don't have in our
    possession both playeroneid and playertwoid we are going to update the guest table with
    two new names.
    */
    
    if ($guesttype == 0) {

        //Make Player Ones Reservation
        $query = "INSERT INTO tblkpUserReservations (
                                    reservationid, userid, usertype
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playerone'
                                              ,0)";

        // run the query on the database
        $result = db_query($query);

        //Make Player Two Reservation
        $query = "INSERT INTO tblkpUserReservations (
                                    reservationid, userid, usertype
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playertwo'
                                              ,0)";

        // run the query on the database
        $result = db_query($query);

        /*
        If this was made a a box league resevation update tblBoxHistory.  At this point
        we have already validated that these users are one in a box and two in a box
        together so we are not going to mess around with that here.
        */
        
        if ($frm['matchtype'] == 1) {
            $boxid = getBoxIdForUser($frm['playeroneid']);
            $query = "INSERT INTO tblBoxHistory (
                                  boxid, reservationid
                        ) VALUES (
			$boxid
                                  ,'$reservationid')";
            $result = db_query($query);
        }
        
        /**
            send users with 'ccnew' a copy of the email notice, request from marinas
        **/
        audit_singles($reservationid, true);

		confirm_singles($reservationid, true);

    } elseif ($guesttype == 1) {

        // playeronename won't be set with regular players, its disabled.
        
        if (get_roleid() == 1) {
            $playerOneName = get_userfullname();
        } else {

            // Strip Slashes
            
            if (get_magic_quotes_gpc()) {
                $playerOneName = stripslashes($frm['playeronename']);
            } else {
                $playerOneName = addslashes($frm['playeronename']);
            }
        }

        //We don't have both of the playerids so we are going to book a guest reservation.
        $query = "INSERT INTO tblkpGuestReservations (
                                    reservationid, name
                                    ) VALUES (
                                              '$reservationid'
                                              ,'$playerOneName')";

        // run the query on the database
        $result = db_query($query);

        //Make Player Two Reservation (if not a solo reservation)
        
        if ($frm['matchtype'] != 5) {

            // Strip Slashes
            
            if (get_magic_quotes_gpc()) {
                $playerTwoName = stripslashes($frm['playertwoname']);
            } else {
                $playerTwoName = addslashes($frm['playertwoname']);
            }
            $query = "INSERT INTO tblkpGuestReservations (
                                            reservationid, name
                                            ) VALUES (
                                                      '$reservationid'
                                                      ,'$playerTwoName')";

            // run the query on the database
            $result = db_query($query);
        }
    }
}
?>
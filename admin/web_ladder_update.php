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

include ("../application.php");

//Set the http variables
$reservationid = $_REQUEST["reservationid"];
$userid = $_REQUEST["userid"];
$wwwroot = $_SESSION["CFG"]["wwwroot"];

//put in validation that current user has access to
require_login();
require_priv("2");
require_priv_reservation($reservationid);


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
	$boxid = $frm["boxid"];

	if($frm["orig_outcome_code"] == $frm["new_outcome_code"]){
		
		$noticemsg = "Your profile was saved.  Good Job!<br/><br/>";
		header("Location: $wwwroot/users/web_ladder_history.php?boxid=$boxid&userid=$userid");
		die;
	}

	//if (isDebugEnabled(2)) logMessage("web_ladder_update.clearScoredResults: reservationid: $reservationid ");
	
	// update the reservation outcome
	clearScoredResults($reservationid);
	
	if($frm["new_outcome_code"] == "W0"){
		$user_score = 3;
		$opponent_score = 0; 
	} elseif($frm["new_outcome_code"] == "W1"){
		$user_score = 3;
		$opponent_score = 1;
	} elseif($frm["new_outcome_code"] == "W2"){
		$user_score = 3;
		$opponent_score = 2;
	} elseif($frm["new_outcome_code"] == "L2"){
		$user_score = 2;
		$opponent_score = 3;
	} elseif($frm["new_outcome_code"] == "L1"){
		$user_score = 1;
		$opponent_score = 3;
	}elseif($frm["new_outcome_code"] == "L0"){
		$user_score = 0;
		$opponent_score = 3;
	}
	
	
	updateMatchScore($reservationid, $userid, $user_score);
	updateMatchScore($reservationid, $frm["opponentid"], $opponent_score);
	
	
	$differential = calculateAdjustedDifferential($frm["orig_outcome_code"],$frm["new_outcome_code"]);
	
	updateBoxPoints($frm["boxid"], $userid, $differential);
	updateBoxPoints($frm["boxid"], $frm["opponentid"], -$differential);
	
	
    header("Location: $wwwroot/users/web_ladder_history.php?boxid=$boxid&userid=$userid");
	die;
}

$boxuserquery = "SELECT concat(users.firstname,' ',users.lastname) AS `name`
                 	FROM tblUsers users
                    WHERE userid = $userid";

// run the query on the database
$box_user_result = db_query($boxuserquery);
$box_user_array = mysqli_fetch_array($box_user_result);

$boxleagueequery = "SELECT boxid FROM tblBoxHistory WHERE reservationid = $reservationid";
// run the query on the database
$box_id_result = db_query($boxleagueequery);
$box_id_array = mysqli_fetch_array($box_id_result);
$boxid = $box_id_array[0];



//Set some variables for the form
$DOC_TITLE = "Box League History for ".$box_user_array[0];

include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_update_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function clearScoredResults($reservationid){
	
	 if (isDebugEnabled(2)) logMessage("web_ladder_update.clearScoredResults: reservationid: $reservationid ");
	
	$query = "UPDATE tblkpUserReservations SET outcome  = 0 WHERE reservationid = $reservationid";
	db_query($query);
	
}

// just returns the net difference in points in what was originally scored and the updated scored
// (from the users perspective)
function calculateAdjustedDifferential($original_outcomecode, $new_outcomecode){
	
	if (isDebugEnabled(2)) logMessage("web_ladder_update.calculateAdjustedDifferential: original_outcomecode $original_outcomecode, new_outcomecode $new_outcomecode ");
	
	$differential = 0; 
	
	//set user to 4 opponent to 3
	if($original_outcomecode == "W0"){
		
		if($new_outcomecode == "W0"){
			$differential=0;
		} elseif($new_outcomecode == "W1"){
			$differential= -1;
		} elseif($new_outcomecode == "W2"){
			$differential= -2;
		} elseif($new_outcomecode == "L2"){
			$differential= -3;
		} elseif($new_outcomecode == "L1"){
			$differential= -4;
		} elseif($new_outcomecode == "L0"){
			$differential= -5;
		}
		
	}
	//set user to 5 opponent to 2
	elseif($original_outcomecode == "W1"){
		
		if($new_outcomecode == "W0"){
			$differential= 1;
		} elseif($new_outcomecode == "W1"){
			$differential= 0;
		} elseif($new_outcomecode == "W2"){
			$differential= -1;
		} elseif($new_outcomecode == "L2"){
			$differential= -2;
		} elseif($new_outcomecode == "L1"){
			$differential= -3;
		} elseif($new_outcomecode == "L0"){
			$differential= -4;
		}
	}
	//set user to 6 opponent to 1
	elseif($original_outcomecode == "W2"){
	
		if($new_outcomecode == "W0"){
			$differential= 2;
		} elseif($new_outcomecode == "W1"){
			$differential= 1;
		} elseif($new_outcomecode == "W2"){
			$differential= 0;
		} elseif($new_outcomecode == "L2"){
			$differential= -1;
		} elseif($new_outcomecode== "L1"){
			$differential= -2;
		} elseif($new_outcomecode == "L0"){
			$differential= -3;
		}
	}
	
	//set user to 3 opponent to 0
	elseif($original_outcomecode == "L2"){
		
		if($new_outcomecode == "W0"){
			$differential= 3;
		} elseif($new_outcomecode == "W1"){
			$differential= 2;
		} elseif($new_outcomecode== "W2"){
			$differential= 1;
		} elseif($new_outcomecode == "L2"){
			$differential= 0;
		} elseif($new_outcomecode == "L1"){
			$differential= -1;
		} elseif($new_outcomecode == "L0"){
			$differential= -2;
		}
	}
	//set user to 3 opponent to 0
	elseif($original_outcomecode == "L1"){
	
		if($new_outcomecode == "W0"){
			$differential= 4;
		} elseif($new_outcomecode == "W1"){
			$differential= 3;
		} elseif($new_outcomecode == "W2"){
			$differential= 2;
		} elseif($new_outcomecode == "L2"){
			$differential= 1;
		} elseif($new_outcomecode == "L1"){
			$differential= 0;
		} elseif($new_outcomecode == "L0"){
			$differential= -1;
		}
		
	}
	//set user to 3 opponent to 0
	elseif($original_outcomecode == "L1"){

		if($new_outcomecode == "W0"){
			$differential= 5;
		} elseif($new_outcomecode == "W1"){
			$differential= 4;
		} elseif($new_outcomecode == "W2"){
			$differential= 3;
		} elseif($new_outcomecode == "L2"){
			$differential= 2;
		} elseif($new_outcomecode == "L1"){
			$differential= 1;
		} elseif($new_outcomecode == "L0"){
			$differential= 0;
		}
	}
	
	if (isDebugEnabled(2)) logMessage("web_ladder_update.calculateAdjustedDifferential: differential: $differential ");
	
	return $differential;
	
}

function updateMatchScore($reservationid, $userid, $games_won){
	
	if (isDebugEnabled(2)) logMessage("web_ladder_update.updateMatchScore: reservationid $reservationid, userid $userid, games_won $games_won ");
	
	$query = "UPDATE tblkpUserReservations SET outcome = $games_won WHERE userid = $userid and reservationid =  $reservationid";
	db_query($query);
	
}


function updateBoxPoints($boxid, $userid, $differential){
	
	if (isDebugEnabled(2)) logMessage("web_ladder_update.updateBoxPoints: boxid $boxid, userid $userid, differential $differential ");
	$query = "UPDATE tblkpBoxLeagues SET score = score+ $differential WHERE boxid = $boxid AND userid = $userid";
	logMessage($query);
	db_query($query);
	
}



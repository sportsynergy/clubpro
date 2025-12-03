<?php


include ("../application.php");

//Set the http variables
$userid = $_REQUEST["userid"];
$boxid = $_REQUEST["boxid"];
$page = $_REQUEST["page"];

//put in validation that current user has access to
require_login();
require_priv_user($userid);
require_priv_box($boxid);


/* form has been submitted, check if it the user login information is correct */
if (match_referer() && isset($_POST['submitme'])) {
    $frm = $_POST;
    $errormsg = validate_form($frm);
    
    if (empty($errormsg) && empty($action)) {
		$successmsg = "Box League Updated.  Good Job!";
    }
}

// Get the box history for that user
$query = "SELECT reservations.reservationid 
            FROM tblBoxHistory history
			INNER JOIN tblReservations reservations ON reservations.reservationid = history.reservationid
			INNER JOIN tblkpUserReservations details ON details.reservationid = reservations.reservationid
			WHERE boxid = $boxid AND details.userid = $userid
             AND (SELECT sum(outcome) 
                    FROM tblkpUserReservations 
                    WHERE reservationid = reservations.reservationid GROUP BY reservationid) > 0 
            GROUP BY reservationid
            ORDER BY reservationid";



// run the query on the database
$result = db_query($query);


$boxuserquery = "SELECT concat(users.firstname,' ',users.lastname) AS `name`
                 	FROM tblUsers users
                    WHERE userid = $userid";

// run the query on the database
$box_name_result = db_query($boxuserquery);



//Set some variables for the form
$DOC_TITLE = "Box League History";

include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/web_ladder_history_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/




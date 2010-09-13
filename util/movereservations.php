<?php
/*
 * Created on Sep 21, 2008
 *
 */
 include("../application.php");
 
 if (isset($_POST['submit'])) {
 	$frm = $_POST;
       $errormsg = validate_form($frm, $errors);
       $courtid = $frm["courtid"];
       printSQL($courtid);
 	    die;
 	
 }
 


?>
 <form name="entryform" method="post" action="./movereservations.php">
  
  <table>
    <tr>
      <td>Enter the courtid:
		<input type="text" name="courtid"/>
	</td>
      <td><input type="submit" name="submit"></td>
    </tr>
   
  </table>
  
  </form>

<?




function validate_form(&$frm) {
/* validate the forgot password form, and return the error messages in a string.
 * if the string is empty, then there are no errors */

        $msg = "";
        if (empty($frm["courtid"])) {

                $msg .= "You did not specify a courtid";

        }
}

/**
 * Prints the sql to the screen.
 */
function printSQL($courtid){
	

	
	$now = mktime();
	$now = $now - (60*60*24*14);
	// Gets all of the reservations from two weeks ago
	
	$query = "SELECT * from tblReservations where courtid = $courtid and time > $now";
	$result = db_query($query);
	
	while( $reservationArray = mysql_fetch_array($result)){
		
		if( gmdate("l",$reservationArray['time']) == "Friday"){
			$reservationid = $reservationArray['reservationid'];
			$time = $reservationArray['time'] + (15 * 60);
			print "<font color='red'>UPDATE tblReservations SET time = $time WHERE reservationid = $reservationid</font>;";
			print "<br/>";
		}
		else{
			$reservationid = $reservationArray['reservationid'];
			$time = $reservationArray['time'] - (15 * 60);
			print "<font color='blue'>UPDATE tblReservations SET time = $time WHERE reservationid = $reservationid</font>;";
			print "<br/>";
			
		}
		
		
	}
}
?>

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
* Classes list:
*/
/*
 * Created on Sep 21, 2008
 *
*/
include ("../application.php");

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
	
	while( $reservationArray = mysqli_fetch_array($result)){
		
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

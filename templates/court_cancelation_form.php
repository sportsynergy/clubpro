<?

       if( empty($time)){
              $time = $_GET['time'];
       }

       if( empty($courtid)){
              $courtid = $_GET['courtid'];
       }


       $courtTypeQuery = "SELECT reservationid, eventid, usertype, guesttype, matchtype, lastmodifier, creator, locked, duration, courttype.reservationtype, time
                     FROM tblReservations reservations
                     INNER JOIN tblCourts courts ON reservations.courtid = courts.courtid
                     INNER JOIN tblCourtType courttype ON courts.courttypeid = courttype.courttypeid
                     WHERE reservations.courtid=$courtid
                     AND reservations.time=$time
                     AND reservations.enddate IS NULL";

       $courtTypeResult = db_query($courtTypeQuery);
       $courtTypeArray = mysqli_fetch_array($courtTypeResult);
        

       if($courtTypeArray['eventid']!=0){
			include($_SESSION["CFG"]["includedir"]."/include_update_event_form.php");   
       }
       elseif($courtTypeArray['reservationtype']==3){
              include($_SESSION["CFG"]["templatedir"]."/court_cancelation_cancelall_only_form.php");   
       }
       //if singles (courtype 0)
       elseif($courtTypeArray['usertype']==0){   
       			include($_SESSION["CFG"]["includedir"]."/include_update_singles_form.php");        
       }

       //else this is a doubles reservation
       elseif($courtTypeArray['usertype']==1) {
				include($_SESSION["CFG"]["includedir"]."/include_update_doubles_form.php");   
       }

 ?>




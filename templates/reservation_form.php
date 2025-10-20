<?php
  $DOC_TITLE = "Court Reservation";

  $courtformquery = "SELECT courttype.courttypeid, courts.courtid, courttype.reservationtype,courts.variableduration,courts.variableDuration_admin,courts.courtname
                   FROM tblCourtType courttype, tblCourts courts
                   WHERE courttype.courttypeid = courts.courttypeid
                   AND courts.courtid=$courtid";

	$courtformresult = db_query($courtformquery);
	
	// Determine what kind of court reservation form to display
  $row = mysqli_fetch_array($courtformresult);
  $reservationType = $row[2];
	$variableDuration = $row[3];
  $variableDuration_admin = $row[4];
  $courtname = $row[5];
	
	//next reservation 
	$nextreservationquery = "SELECT time FROM tblReservations 
  WHERE courtid = '$courtid' AND enddate IS NULL 
				AND TIME > $time
				ORDER BY  `tblReservations`.`time`  
				LIMIT 1";
				
	$result = db_query($nextreservationquery);
	
	//account for no reservations
	$nexttime = mysqli_result($result,0);

?>

<p class="bigbanner"><? pv($DOC_TITLE) ?></p>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#singles" type="button" role="tab" aria-controls="singles" aria-selected="true">Singles</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="doubles-tab" data-bs-toggle="tab" data-bs-target="#doubles" type="button" role="tab" aria-controls="doubles" aria-selected="false">Doubles</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#resources" type="button" role="tab" aria-controls="resources" aria-selected="false">Resources</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="events" aria-selected="false">Events</button>
  </li>
</ul>



<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="singles" role="tabpanel" aria-labelledby="singles-tab">
    <? include($_SESSION["CFG"]["includedir"]."/include_reservation_singles.php");?>
  </div>
  <div class="tab-pane fade" id="doubles" role="tabpanel" aria-labelledby="doubles-tab">
    <? include($_SESSION["CFG"]["includedir"]."/include_reservation_doubles.php");?>
  </div>
  <div class="tab-pane fade" id="resources" role="tabpanel" aria-labelledby="resources-tab">
     <?  include($_SESSION["CFG"]["includedir"]."/include_reservation_resource.php");?>
  </div>
  <div class="tab-pane fade" id="events" role="tabpanel" aria-labelledby="events-tab">
     <? include($_SESSION["CFG"]["includedir"]."/include_reservation_event.php");?>
  </div>
</div>








  



<script language="Javascript">



//please keep these lines on when you copy the source
//made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
if (document.getElementById) {
	for (var sch = 0; sch < dform.length; sch++) {
 		if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
		if (dform.elements[sch].type.toLowerCase() == "button") dform.elements[sch].disabled = true;
	}
}
	return true;
}


function unsetplayerone(fieldname)
{
       if( document.doubles_reservation_form.dname1.value.length == 0){
       	 document.doubles_reservation_form.did1.value = "";
       }
}

function unsetplayertwo(fieldname)
{
       if( document.doubles_reservation_form.dname2.value.length == 0){
       	 document.doubles_reservation_form.did2.value = "";
       }
}

function unsetplayerthree(fieldname)
{
       if( document.doubles_reservation_form.name3.value.length == 0){
       	 document.doubles_reservation_form.id3.value = "";
       }
}

function unsetplayerfour(fieldname)
{
       if( document.doubles_reservation_form.name4.value.length == 0){
       	 document.doubles_reservation_form.id4.value = "";
       }
}

function disableSinglesOptions(repeat)
{
   if(repeat.value == "norepeat"){
       document.singlesform.frequency.disabled = true;
   }
   else{
      document.singlesform.frequency.disabled = "";
   }       
}

function onlyAllowLessonReoccuring(matchtype)
{
   if(matchtype.value == "4"){
       document.singlesform.repeat.disabled = "";
   }
   else{
      document.singlesform.repeat.disabled = true;
   }       
}

function disableEventOptions(repeat)
{
   if(repeat.value == "norepeat"){
       document.event_reservation_form.frequency.disabled = true;
   }
   else{
      document.event_reservation_form.frequency.disabled = "";
   }       
}
</script>
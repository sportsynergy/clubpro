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

<div id="reservations" class="yui-navset">
  <ul class="yui-nav">
    <? if($reservationType==0 || $reservationType==1) { ?>
    <li class="selected"><a href="#singles"><em>Singles</em></a></li>
    <? } ?>
    <? if($reservationType==2 || $reservationType==1) { ?>
    <li <?=$reservationType=="2"?"class=\"selected\"":""?>><a href="#doubles"><em>Doubles</em></a></li>
    <? } ?>
    <? if($reservationType==3 ) { ?>
    <li class="selected"><a href="#"><em>Resource</em></a></li>
    <? } ?>
    <? if(get_roleid()==2 && ($reservationType==1 || $reservationType==2 || $reservationType==3) ) { ?>
    <li><a href="#events"><em>Events</em></a></li>
    <? } ?>
  </ul>
  <div class="yui-content">
    <? if($reservationType==1 || $reservationType==0) { ?>
    <div id="singles">
      <? include($_SESSION["CFG"]["includedir"]."/include_reservation_singles.php");?>
    </div>
    <? } ?>
    <? if($reservationType==2 || $reservationType==1) { ?>
    <div id="doubles">
      <? include($_SESSION["CFG"]["includedir"]."/include_reservation_doubles.php");?>
    </div>
    <? } ?>

    <? if($reservationType==3 ) {  ?>
    
     <div id="resources">
    <?  include($_SESSION["CFG"]["includedir"]."/include_reservation_resource.php");?>
   </div>
    <? } ?>
    

    <? if(get_roleid()==2 && ($reservationType==1 || $reservationType==2 || $reservationType==3)) { ?>
    <div id="events">
      <? include($_SESSION["CFG"]["includedir"]."/include_reservation_event.php");?>
    </div>
    <? } ?>
    
    
  </div>
</div>
<?

// Calculate current tab index
if($_REQUEST["courttype"]=="event" && $reservationType=="1"){
	$currentTabIndex = 2;
}
else if($_REQUEST["courttype"]=="event" 
		&& ($reservationType=="0" || $reservationType=="2")  ) {
	$currentTabIndex = 1;
}
else if($_REQUEST["courttype"]=="doubles" && $reservationType=="1") {
	$currentTabIndex = 1;
} 

?>
<form name="tabIndexForm">
  <input type="hidden" name="tabIndex" value="<?=$currentTabIndex?>">
  </input>
</form>
<script language="Javascript">


(function() {
    var myTabs = new YAHOO.widget.TabView("reservations");

    var url = location.href.split('#');
    if (url[1]) {
        //We have a hash
        var tabHash = url[1];
        var tabs = myTabs.get('tabs');
        for (var i = 0; i < tabs.length; i++) {
            if (tabs[i].get('href') == '#' + tabHash) {
                myTabs.set('activeIndex', i);
                break;
            }
        }
    }
    else{
		if(document.tabIndexForm.tabIndex.value != ""){
			myTabs.set('activeIndex', document.tabIndexForm.tabIndex.value );
		}
    }
    
})();


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
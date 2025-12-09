 <?php

$DOC_TITLE = "Doubles Court Reservation";

// Get the first and last name of the player one.  The player one
// and player two variables are set in court_reservation.php when

//determining what form to display.

$needpartnerquery = "SELECT reservationdetails.userid, reservationdetails.usertype
									                     FROM tblReservations reservations, tblkpUserReservations reservationdetails
														 WHERE reservations.reservationid = reservationdetails.reservationid
									                     AND reservations.courtid='$courtid'
									                     AND reservations.time='$time'
														 AND reservations.enddate is NULL
														ORDER BY reservationdetails.usertype, reservationdetails.userid";

// run the query on the database
$needpartnerresult = db_query($needpartnerquery);
$playerOneArray = mysqli_fetch_array($needpartnerresult);
$playerTwoArray = mysqli_fetch_array($needpartnerresult);
$playerOneQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$playerOneArray[userid]";
$playerOneResult = db_query($playerOneQuery);
$playerOneNameArray = db_fetch_array($playerOneResult);

// Get the first and last name of the player two
$playerTwoQuery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$playerTwoArray[userid]";
$playerTwoResult = db_query($playerTwoQuery);
$playerTwoNameArray = db_fetch_array($playerTwoResult);
?>

<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">

<div class="mb-3">

<div class="form-check">
  <input class="form-check-input" type="radio"  id="partner" name="partner" value="<?=$playerOneArray['userid']?>" checked="checked" onclick="disablePartnerList(this.checked)">
   		Play with <?echo "$playerOneNameArray[firstname] $playerOneNameArray[lastname]"?>
</div>

<div class="form-check">
  <input class="form-check-input" type="radio"  id="partner" name="partner" value="<?=$playerTwoArray['userid']?>"  onclick="disablePartnerList(this.checked)">
   		Play with <?echo "$playerTwoNameArray[firstname] $playerTwoNameArray[lastname]"?>
</div>

 <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Make Reservation</button>
	<button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
  </div>
	
	<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
	<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
	<input type="hidden" name="courttype" value="doubles">
	<input type="hidden" name="action" value="addpartners">
	<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">

</div>

	
		         
	

</form>


<?

  //Get the skill range for players wanted advertising for the club.
  $rankdevquery = "SELECT  tblClubs.rankdev
                    FROM tblClubs
                    WHERE (((tblClubs.clubid)=".get_clubid()."))";

   // run the query on the database
   $rankdevresult = db_query($rankdevquery);
   $rankdev = mysqli_result($rankdevresult, 0)

?>


<script language="Javascript">



function onSubmitButtonClicked(){
	
	var myButton = YAHOO.widget.Button.getButton('submitbutton'); 		
	myButton.set('disabled', true);
	submitForm('entryform');
}



</script>




<form name="entryform" method="post" action="<?=$ME?>">

<div class="mb-5">
  <p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


        <? 
        $resid = $_REQUEST['resid'];
        $time = $_REQUEST['time'];
        $matchType = getMatchType($resid);
        
        if ( $matchType != 1 ){ ?>

        
        	 <? if ($matchType == 4 ){ ?>
        	 
           <div class="form-check">
            <input class="form-check-input" type="radio" name="resdetails" id="lesson" value="5" checked>
            <label class="form-check-label" for="lesson">
              Advertise this reservation as a lesson.
            </label>
          </div>	
        	 	
        	 <? } ?>	
        
         <? 
       //Only let admins do this
       if( (get_roleid()==4 || get_roleid()==2 || isNearRankingAdvertising() )  &&  $matchType != 4){
       ?>
        
        <div class="form-check">
            <input class="form-check-input" type="radio" name="resdetails" id="range" value="1" <?=$matchType!=4 ? "checked":"" ?>>
            <label class="form-check-label" for="range">
             Advertise this reservation to players within <? pv($rankdev)?> of my skill level
            </label>
          </div>	
        

        <? } else {
        	// Make sure this defaults the right way
        	$advertBuddies = "checked";
        
       }?>
       
       <? if($matchType != 4){?>
        
         <div class="form-check">
            <input class="form-check-input" type="radio" name="resdetails" id="buddies" value="2" <?=$advertBuddies ?> >
            <label class="form-check-label" for="buddies">
             Advertise this reservation to my buddies:
            </label>
          </div>
        
       
       <? } 
	       //Only let admins do this
	       if( (get_roleid()==4 || get_roleid()==2 || isAllowAllSiteAdvertising() ) && $matchType != 4 ){
	       ?>
	     
          <div class="form-check">
            <input class="form-check-input" type="radio" name="resdetails" id="wholeclub" value="2"  >
            <label class="form-check-label" for="wholeclub">
             Advertise this reservation to the whole club
            </label>
          </div>
       
	          
	         
	       <? } ?>
	       
		    <div class="form-check">
            <input class="form-check-input" type="radio" name="resdetails" id="noadvertise" value="0"  >
            <label class="form-check-label" for="noadvertise">
             Don't advertise this one 
            </label>
          </div>
       
        <? }  else {  ?>

     
          <div class="normal">Advertise this reservation to players in my Box League:</div>

          <div class="form-check">
            <input class="form-check-input" type="radio" name="boxid" id="yesbox" value="adv"  >
            <label class="form-check-label" for="yesbox">
             Yes
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="radio" name="boxid" id="nobox" value="dontdoit"  >
            <label class="form-check-label" for="nobox">
             No
            </label>
          </div>

        
     <? } ?>
     

    <div class="my-5">
      <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Complete Reservation</button>
    </div>
  
    <input type="hidden" name="resid" value="<? pv($resid) ?>">
    <input type="hidden" name="time" value="<? pv($time) ?>">


</form>



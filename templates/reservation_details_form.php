
<?

  //Get the skill range for players wanted advertising for the club.
  $rankdevquery = "SELECT  tblClubs.rankdev
                    FROM tblClubs
                    WHERE (((tblClubs.clubid)=".get_clubid()."))";

   // run the query on the database
   $rankdevresult = db_query($rankdevquery);
   $rankdev = mysql_result($rankdevresult, 0)

?>


<script language="Javascript">

// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}



</script>




<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">




<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
 <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table cellspacing="10" cellpadding="0" width="450">

        <? 
        $resid = $_REQUEST['resid'];
        $time = $_REQUEST['time'];
        $matchType = getMatchType($resid);
        
        if ( $matchType != 1 ){ ?>

        
        	 <? if ($matchType == 4 ){ ?>
        	 	 <tr>
            		<td><div class="normal">Advertise this reservation as a lesson.</div></td>
            		<td>
                		<input type="radio" name="resdetails" value="5" checked>
                </td>
       </tr>
        	 	
        	 	
        	 <? } ?>	
        
         <? 
       //Only let admins do this
       if( (get_roleid()==4 || get_roleid()==2 || isNearRankingAdvertising() )  &&  $matchType != 4){
       ?>
        <tr>
            <td><div class="normal">Advertise this reservation to players within <? pv($rankdev)?> of my skill level:</div></td>
            <td>
            
                <input type="radio" name="resdetails" value="1" <?=$matchType!=4 ? "checked":"" ?>>
                </td>
       </tr>
        <? } else {
        	// Make sure this defaults the right way
        	$advertBuddies = "checked";
        
       }?>
       
       <? if($matchType != 4){?>
        <tr>
         <td>
           <div class=normal>Advertise this reservation to my buddies:</div></td>
           <td>
           <input type="radio" name="resdetails" value="2" <?=$advertBuddies ?>>
         </td>
       </tr>
       <? } 
	       //Only let admins do this
	       if( (get_roleid()==4 || get_roleid()==2 || isAllowAllSiteAdvertising() ) && $matchType != 4 ){
	       ?>
	       <tr>  
	         <td>
	           <div class=normal>Advertise this reservation to the whole club:</div></td>
	           <td>
	           <input type="radio" name="resdetails" value="3">
	         </td>
	       </tr>
	       <? } ?>
	       
		 <tr>
            <td><div class=normal>Don't advertise this one</div></td>
            <td>
                <input type="radio" name="resdetails" value="0" >
                </td>
       </tr>
       
        <? }
        else{
        ?>

     <tr>
               <td colspan="2"><div class="normal">Advertise this reservation to players in my Box League:</div></td>
     </tr>
     <tr>
               <td colspan="2" class="normal">Yes <input type="radio" name="boxid" value="adv" checked> &nbsp; &nbsp; No <input type="radio" name="boxid" value="dontdoit"></td>

     </tr>
     <? } ?>
     <tr>
        <td colspan="2">
        <br>
            <input type="button" value="Complete Reservation" id="submitbutton">
        </td>
     </tr>
     <tr>
       <td colspan="2">
            <input type="hidden" name="resid" value="<? pv($resid) ?>">
            <input type="hidden" name="time" value="<? pv($time) ?>">
        </td>
     </tr>

 </table>

</td>
</tr>
</table>

</form>



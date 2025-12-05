<script language="Javascript">

document.onkeypress = function (aEvent)
{
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
}


function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php"
 }



<?

$thisYear = gmdate("Y", $curtime);
$daysinFeb = gmdate("t", gmmktime(0,0,0,2,1,$thisYear));

print "var thisyear = new Array(13);
       thisyear[1] = 31\n
       thisyear[2] = $daysinFeb\n
       thisyear[3] = 31\n
       thisyear[4] = 30\n
       thisyear[5] = 31\n
       thisyear[6] = 30\n
       thisyear[7] = 31\n
       thisyear[8] = 31\n
       thisyear[9] = 30\n
       thisyear[10] = 31\n
       thisyear[11] = 30\n
        thisyear[12] = 31\n\n";

 $daysinFeb = gmdate("t", gmmktime(0,0,0,2,1,$thisYear+1));

 print "var nextyear = new Array(13);
       nextyear[1] = 31\n
       nextyear[2] = $daysinFeb\n
       nextyear[3] = 31\n
       nextyear[4] = 30\n
       nextyear[5] = 31\n
       nextyear[6] = 30\n
       nextyear[7] = 31\n
       nextyear[8] = 31\n
       nextyear[9] = 30\n
       nextyear[10] = 31\n
       nextyear[11] = 30\n
        nextyear[12] = 31\n";

?>

     function getDaysForMonth() {

          if(document.entryform.enddateday.value != null){
               document.entryform.enddateday.options.length = 0;
          }

          var month = document.entryform.enddatemonth.value;

           if(document.entryform.enddateyear.selectedIndex == 0){
                 for (i=0; i<thisyear[month]; i++) {
                          var myDayValue = new String(i+1);
                          document.entryform.enddateday.options[i] = new Option(i+1, myDayValue);

						if(document.entryform.boxenddatedate.value == i+1){
							document.entryform.enddateday.options[i].selected=true;
						}
						  
                  }
           }

           else{
                  for (i=0; i<nextyear[month]; i++) {
                          var myDayValue = new String(i+1);
                          document.entryform.enddateday.options[i] = new Option(i+1, myDayValue);

							if(document.entryform.boxenddatedate.value == i+1){
								document.entryform.enddateday.options[i].selected=true;
							}
                  }
           }
     }


	
	
</script>

<?

// Set some timezone variables
$clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
$clubresult = db_query($clubquery);
$clubobj = db_fetch_object($clubresult);

$curtime = $_SESSION["current_time"];



//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];
$userid = $_REQUEST["userid"];

 if($action=="remove"){
	
   // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxplace
                             FROM tblkpBoxLeagues
                             WHERE boxid = $boxid
                             AND userid = $userid ");

   $boxplaceval = mysqli_result($oldboxplaceresult, 0);


   //Remove Buddy
   $qid1 = db_query("DELETE FROM tblkpBoxLeagues
                     WHERE boxid = $boxid
                     AND userid = $userid");


   // Now adjust all players who were ranked below this player

   $adjustquery = "SELECT tblkpBoxLeagues.userid, tblkpBoxLeagues.boxplace
                   FROM tblkpBoxLeagues
                   WHERE boxid = $boxid
                   AND (((tblkpBoxLeagues.boxplace)>$boxplaceval))
                   ORDER BY tblkpBoxLeagues.boxplace";


    // run the query on the database
    $adjustresult = db_query($adjustquery);

   //Update the rankings
   while ($adjustobj = db_fetch_object($adjustresult)) {

       $newboxplaceval = $adjustobj->boxplace - 1;

       $qid = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$newboxplaceval'
        WHERE userid = '$adjustobj->userid'
        AND boxid = $boxid");


   }
  }

  if($action=="moveup"){

    // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxplace
                             FROM tblkpBoxLeagues
                             WHERE boxid = $boxid
                             AND userid = $userid ");


   $boxplaceval = mysqli_result($oldboxplaceresult, 0);

   // Get around the problem of moving up a play who is in first
   if ( $boxplaceval > 1){

   $newboxplaceval = $boxplaceval - 1;

        // MOve the player ahead down
        $qid1 = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$boxplaceval'
        WHERE boxplace = '$newboxplaceval'
        AND boxid = $boxid");


       // MOve the player up
       $qid2 = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$newboxplaceval'
        WHERE userid = '$userid'
        AND boxid = $boxid");

  }
 }



  ?>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<div class="mb-3">
      <label for="enddate" class="form-label">End Date:</label>
      <? $datesArray = explode("-", $boxarray["enddate"]); ?>

     <select name="enddatemonth" class="form-select" onChange="getDaysForMonth();" style="width: 150px; display: inline;">
               <?
                
				// remove leading 0 
				$currentMonth = ltrim($datesArray[1], "0");
				$months = get_months();

                 for($i=0; $i<count($months); ++$i){

                      if($currentMonth==($i+1)){
                          $selected = "selected";
                       }
                       else{
                          $selected = " ";
                      }
                 ?>
                    <option value="<?=$i+1?>" <?=$selected?>> <?=$months[$i]?></option>

                     <? unset($selected)?>
                <? } ?>
          </select>

           <select name="enddateday" class="form-select" style="width: 150px; display: inline;"> </select>
            <select name="enddateyear" class="form-select" onChange="getDaysForMonth();" style="width: 150px; display: inline;">
               <?

               $currYear = gmdate("Y", $curtime);

               for($i=0; $i<2; ++$i){
                   $year = $currYear + $i;

                   if($year == $datesArray[0]){
                    $selected = "selected";
                  } else{
                    $selected = "";
                  }
               ?>
                   <option value="<?=$year ?>" <?=$selected?> ><?=$year ?></option>
              <? }
                unset($selected);
               ?>
          </select>

    </div>

    <div class="mb-3" style="width: 70%">
        <label for="name1" class="form-label">Player:</label>
        <input id="name1" name="name1" type="text" size="30" class="form-control form-autocomplete" />
                <input id="id1" name="boxuser" type="hidden" />

          <? if( isset($boxarray["ladderid"]) ) { ?>
    			<script>
                <?
                 $ladderid = $boxarray["ladderid"];
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
                  'baseUrl'=> "$wwwroot/users/ajaxServer.php",
                  'source'=>'name1',
                  'target'=>'id1',
                  'className'=>'autocomplete',
                  'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&ladderid=$ladderid",
                  'progressStyle'=>'throbbing',
                  'minimumCharacters'=>3,
                  )); ?>
            </script>

            <? }  else { ?>

              <script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courttype=$courttype&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						)); ?>
            </script>
          <? }  ?>
    </div>
		
     
  <?php
  if ( isJumpLadderRankingScheme() ) {
  ?>
      <div class="mb-3">
        <label for="ladder" class="form-label">Ladder:</label>
        <input class="form-control-plaintext" id="ladder" type="text" aria-label="ladder" value="<?= $boxarray["name"]?>" readonly> 
     </div>

     <div class="mb-3">
        <label for="autoschedule" class="form-label">Autoschedule:</label>
        <input class="form-control-plaintext" id="autoschedule" type="text" aria-label="autoschedule" value="<?= $boxarray["autoschedule"] == 1 ? "Yes": "No" ?>" readonly> 
     </div>

     <div class="mb-3">
        <label for="autoschedule" class="form-label">Rollover Scores:</label>
        <input class="form-control-plaintext" id="autoschedule" type="text" aria-label="autoschedule" value="<?= $boxarray["ladder_type"] == "extended" ? "Yes": "No" ?>" readonly> 
     </div>
   
    <? } ?>

      <input type="hidden" name="ladderid" value="<?=$boxarray["ladderid"] ?>">
      <input type="hidden" name="boxid" value="<?=$boxid ?>">
      <input type="hidden" name="courttype" value="<?=$courttype?>">
			<input type="hidden" name="boxenddatemonth" value="<?=ltrim($datesArray[1], '0')?>">
			<input type="hidden" name="boxenddatedate" value="<?=ltrim($datesArray[2], '0')?>">
			<input type="hidden" name="boxenddateyear" value="<?=ltrim($datesArray[0], '0')?>">
      <input type="hidden" name="submitme" value="submitme">
			
       
  <div class="mt-5 mb-3">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Submit</button>
    <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Back to web ladder registration page</button>
  </div>

</form>

     <?
       //List out all of the players in the box league

       $query = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.userid, tblkpBoxLeagues.boxplace, tblkpBoxLeagues.games
                 FROM tblUsers INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                 WHERE (tblkpBoxLeagues.boxid)=$boxid
                 ORDER BY tblkpBoxLeagues.boxplace";

       // run the query on the database
       $result = db_query($query); 
       if (mysqli_num_rows($result) > 0) {
       ?>

       <table class="table table-striped" >
       <thead>
       <tr>
	       	<th>Place</th>
	       	<th>Player</th>
	       	<th>Games Played</th>
	       <th></th>
	       <th></th>
       </tr>
      </thead>
       <tbody>
       <? 
        $rownum = mysqli_num_rows($result);
      
        while($row = mysqli_fetch_row($result)) { ?>

          <tr>
	         <td> <?=$row[3]?></td>
	         <td>	<? print "$row[0] $row[1]" ?></td>
	         <td><?=$row[4]?></td>
	         <td>
	         	<a href="web_ladder_manage.php?action=remove&boxid=<?=$boxid?>&userid=<?=$row[2]?>"> Remove </a>
	         	|<a href="web_ladder_manage.php?action=moveup&boxid=<?=$boxid?>&userid=<?=$row[2]?>"> Moveup</a>

            <? if ( isPointRankingScheme() || isLadderRankingScheme() ) {?>
				|<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$boxid?>&userid=<?=$row[2]?>&page=admin"> View History</a>
              <? }  ?>
      </td>
	        
         </tr>
            
       <? 
        $rownum = $rownum - 1;
       } ?>
</tbody>
     </table>

     <? } ?>







  <script language="JavaScript">

          document.entryform.name1.focus();

          document.getElementById('name1').setAttribute("autocomplete", "off");
          getDaysForMonth();
   </script>

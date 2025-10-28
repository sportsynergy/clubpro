<?



//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];

 if($action=="remove"){
   // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxrank
                             FROM tblBoxLeagues
                             WHERE boxid = $boxid
                             AND siteid = ".get_siteid()." ");

   $boxrankval = mysqli_result($oldboxplaceresult, 0);

   //Remove Box
   $qid1 = db_query("UPDATE tblBoxLeagues
                        SET tblBoxLeagues.enable = FALSE
                     WHERE boxid = $boxid");


   //Set all current reservations for this box to practice.

   $historyQuery = "SELECT reservationid from tblBoxHistory WHERE boxid = $boxid";
   $historyResult = db_query($historyQuery);

   while($historyArray = mysqli_fetch_array($historyResult)){
          markMatchType($historyArray[reservationid],0);
   }

   //Finally Clean up with box league history
   $qid1 = db_query("DELETE FROM tblBoxHistory
                     WHERE boxid = $boxid");


   // Now adjust all boxes who were ranked below this box

   $adjustquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxrank
                   FROM tblBoxLeagues
                   WHERE (((tblBoxLeagues.boxrank)>$boxrankval))
                   AND siteid=".get_siteid()."
                   AND tblBoxLeagues.enable = TRUE
                   ORDER BY tblBoxLeagues.boxrank";


    // run the query on the database
    $adjustresult = db_query($adjustquery);

   //Update the rankings
   while ($adjustobj = db_fetch_object($adjustresult)) {

       $newboxrankval = $adjustobj->boxrank - 1;

       $qid = db_query("
        UPDATE tblBoxLeagues
        SET boxrank = '$newboxrankval'
        WHERE boxid = '$adjustobj->boxid'");

   }


  }
 if($action=="disable"){

   //Remove Buddy
   $qid1 = db_query("UPDATE tblBoxLeagues
                     SET tblBoxLeagues.enable = 0
                     WHERE tblBoxLeagues.boxid=$boxid");
 }
 if($action=="enable"){

   //Remove Buddy
   $qid1 = db_query("UPDATE tblBoxLeagues
                     SET tblBoxLeagues.enable = 1
                     WHERE tblBoxLeagues.boxid=$boxid");
 }
 //Move a box up
 if($action=="moveup"){

    // Get the box place of the guy who is being removed.
    $oldboxrankresult = db_query("SELECT boxrank
                             FROM tblBoxLeagues
                             WHERE boxid = $boxid");


   $boxrankval = mysqli_result($oldboxrankresult, 0);

   // Get around the problem of moving up a box who is in first
   if ( $boxrankval > 1){

   $newboxrankval = $boxrankval - 1;

        // MOve the box ahead down
        $qid1 = db_query("
        UPDATE tblBoxLeagues
        SET boxrank = '$boxrankval'
        WHERE boxrank = '$newboxrankval'
        AND siteid = ".get_siteid()."");
        //echo "Moved the box ahead down one from $boxrankval to $newboxrankval.\n\n";


       // MOve the box up
       $qid2 = db_query("
        UPDATE tblBoxLeagues
        SET boxrank = '$newboxrankval'
        WHERE boxid = $boxid
        AND siteid = ".get_siteid()."");
        //echo "Moved me box ahead up one from $newboxrankval on box $boxid\n";



  }
 }

  $clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
  $clubresult = db_query($clubquery);
  $clubobj = db_fetch_object($clubresult);

  $gmtime =   time();
  $tzdelta = $clubobj->timezone*3600;
  $curtime =   $gmtime+$tzdelta;

  ?>



<script language="JavaScript">

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
                  }
           }

           else{
                  for (i=0; i<nextyear[month]; i++) {
                          var myDayValue = new String(i+1);
                          document.entryform.enddateday.options[i] = new Option(i+1, myDayValue);
                  }
           }
     }


     YAHOO.example.init = function () {

    	    YAHOO.util.Event.onContentReady("formtable", function () {

                document.getElementById('boxname').setAttribute("autocomplete", "off");

    	        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
    	        oSubmitButton1.on("click", onSubmitButtonClicked);

    	    });

    	} ();


    	function onSubmitButtonClicked(){
    		submitForm('entryform');
    	}
</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<div class="container">
    <div class="row">
      <div class="col-6">
      
      <form name="entryform" method="post" action="<?=$ME?>">

      <div class="mb-3">
            <label  class="form-label">Box Name</label>
            <input type="text" name="searchname" id="boxname" class="form-control" aria-label="Box name">
            <? is_object($errors) ? err($errors->boxname) : ""?>
        </div>

      <div class="mb-3">
        <label for="courttypeid" class="form-label">Court Type</label>
        <select class="form-select" name="courttypeid" id="courttypeid" aria-label="Court Type">
                <?  
                    $query = "SELECT courttypeid,courttypename FROM tblCourtType";
                    $result = get_singlesCourtTypesForSite( get_siteid() );

                    while($row = mysqli_fetch_row($result)) {
                    echo "<option value=\"$row[0]\">$row[1]</option>";
                    }
                 ?>
            </select>
            <? is_object($errors) ? err($errors->courtypeid) : ""?>
      </div>

      <div class="mb-3">
        <label for="courttypeid" class="form-label">End Date</label>
        <select class="form-select" name="enddatemonth" id="enddatemonth" aria-label="End Date Month">
                <?
                 $currentMonth =  gmdate("n", $curtime);
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

          <select name="enddateday" class="form-select" aria-label="End Date Day">
                    <!-- Days will be populated by javascript function -->
          </select>

           <select name="enddateyear" class="form-select" onChange="getDaysForMonth();">
               <?
               $currYear = gmdate("Y", $curtime);

               for($i=0; $i<2; ++$i){
                   $year = $currYear + $i;
               ?>
                   <option value="<?=$year ?>"><?=$year ?></option>
              <? }  ?>
          </select>
      </div>   
    
  <? if( isJumpLadderRankingScheme() ){ // only available for jump ladders ?>
    <div class="mb-3">
        <label for="courttypeid" class="form-label">Ladder</label>
            <select name="ladder" class="form-select" aria-label="Ladder">
                <option value="0"></option>
            <? 
             $ladders = getClubSiteLadders( get_siteid() );
             for ($i=0; $i < count($ladders); ++$i) { ?>
                <option value="<?=$ladders[$i]['id'] ?>"><?=$ladders[$i]['name'] ?></option>
            <?  } ?>
             </select>
            <? is_object($errors) ? err($errors->ladder) : ""?>
    </div>

    <div class="mb-3">
        <label for="autoschedule" class="form-label">Auto Schedule</label>
            <select name="autoschedule" id="autoschedule" class="form-select" aria-label="Auto Schedule">
                <option value="no">No</option>
                <option value="yes">Yes</option>
             </select>
    </div>

     <div class="mb-3">
        <label for="ladder_type" class="form-label">Rollover Scores</label>
            <select name="ladder_type" id="ladder_type" class="form-select" aria-label="Rollover Scores">
                <option value="basic">No</option>
                <option value="extended">Yes</option>
             </select>
    </div>


    <? }  else {  # default to this for non jumpladder situations?>
    <input type="hidden" name ="laddertype" value="manual" >

    <? }   ?>

        <button type="submit" class="btn btn-primary" id="submitbutton">Submit</button>
        <input type="hidden" name ="submitme" value="submitme" >

      </form>

       <script language="JavaScript">
              getDaysForMonth();
       </script>


    
    </div> <!-- col -->

    <div class="col">

      <?

       $query = "SELECT tblBoxLeagues.boxname, tblBoxLeagues.boxid, tblBoxLeagues.enable, tblBoxLeagues.boxrank
                FROM tblBoxLeagues
                WHERE tblBoxLeagues.siteid=".get_siteid()."
                AND tblBoxLeagues.enable = TRUE
                ORDER BY tblBoxLeagues.boxrank";

       // run the query on the database
       $result = db_query($query);
       
       ?>
        <table class="table table-striped">
       <tr>
            <th colspan="5">
                Name
            </th>
        </tr>
        
        <?
       $numrows = mysqli_num_rows($result);
       $rowcount =  $numrows;
       $i=1;
       while($row = mysqli_fetch_row($result)) { 
		
       	?>
            <tr >
            <td ></td>
            <td ><?=$row[0]?></td>

            <td>
                <form name="removeWebLadder<?=$rowcount?>" method="post" action="<?=$ME?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="boxid" value="<?=$row[1]?>">
                </form>

                 <form name="manageWebLadder<?=$rowcount?>" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_manage.php">
                  <input type="hidden" name="boxid" value="<?=$row[1]?>">
                 </form>

                 <form name="moveupWebLadder<?=$rowcount?>" method="post" action="<?=$ME?>">
                 <input type="hidden" name="action" value="moveup">
                 <input type="hidden" name="boxid" value="<?=$row[1]?>">
                  </form>
               

              <a href="javascript:submitForm('removeWebLadder<?=$rowcount?>')"><span>Remove</span></a> 
				| <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_manage.php?boxid=<?=$row[1]?>" ><span>Manage</span></a>
				| <a href="javascript:submitForm('moveupWebLadder<?=$rowcount?>')"><span>Move Up</span></a>
				
				</td>
            </tr>

            
        <? }  if ($numrows==0){ ?>
           <tr>
           <td colspan="6">
                <span class="normalsm">
                    There are currently no box leagues configured.
                </span>
            </td>
           </tr>
      
        <?  }  ?>
      
     </table>

     </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->
        
    

        
       

       
     




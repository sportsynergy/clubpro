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

   $boxrankval = mysql_result($oldboxplaceresult, 0);

   //Remove Box
   $qid1 = db_query("DELETE FROM tblBoxLeagues
                     WHERE boxid = $boxid");


   //Remove the people from the box
   $qid1 = db_query("DELETE FROM tblkpBoxLeagues
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


   $boxrankval = mysql_result($oldboxrankresult, 0);

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

  $gmtime =   gmmktime();
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

    	        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
    	        oSubmitButton1.on("click", onSubmitButtonClicked);

    	    });

    	} ();


    	function onSubmitButtonClicked(){
    		submitForm('entryform');
    	}
</script>

<form name="entryform" method="post" action="<?=$ME?>">

<table width="600" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
     <tr>
         <td class="clubid<?=get_clubid()?>th">
         	<span class="whiteh1">
         		<div align="center"><? pv($DOC_TITLE) ?></div>
         	</span>
         </td>
    </tr>

 <tr>
    <td >



      <table width="550" cellspacing="5" cellpadding="0"  >
      <tr>

       <td class="label">Box Name:</td>
        <td><input type="text" name="boxname" size=25>
                <?err($errors->boxname)?>
        </td>
       </tr>
        <tr>

        <td class="label">Court Type:</td>
            <td><select name="courttypeid">
                 <option value="">-------------------------------------</option>
                <?  //Get all registered Court Types
                $query = "SELECT courttypeid,courttypename FROM tblCourtType";

                // run the query on the database
                $result = get_singlesCourtTypesForSite( get_siteid() );


                 while($row = mysql_fetch_row($result)) {
                  echo "<option value=\"$row[0]\">$row[1]</option>";
                 }
                 ?>
                <?err($errors->courtypeid)?>
                </select>

                </td>

      </tr>
      <tr>
          <td class="label">End Date:</td>
          <td>
          <select name="enddatemonth" onChange="getDaysForMonth();">
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
          <?

             //$todaystart = gmmktime (0,0,0,$currMonth,0,$currYear);
          ?>
          <select name="enddateday">

          </select>

          <select name="enddateyear" onChange="getDaysForMonth();">
               <?

               $currYear = gmdate("Y", $curtime);

               for($i=0; $i<2; ++$i){
                   $year = $currYear + $i;
               ?>
                   <option value="<?=$year ?>"><?=$year ?></option>
              <? }

               ?>

          </select>
          </td>
      </tr>
     <tr>
      <td colspan="2">
      <input type="button" name ="submit" value="Submit" id="submitbutton">
      <input type="hidden" name ="submitme" value="submitme" >
      </form>

       <script language="JavaScript">
              getDaysForMonth();
       </script>

      </td>

     </tr>
     <tr>
      <td colspan="2">
          <hr>
      </td>

     </tr>
     <?
       //List out all of the players Buddies


       $query = "SELECT tblBoxLeagues.boxname, tblBoxLeagues.boxid, tblBoxLeagues.enable, tblBoxLeagues.boxrank
                FROM tblBoxLeagues
                WHERE (((tblBoxLeagues.siteid)=".get_siteid()."))
                ORDER BY tblBoxLeagues.boxrank";

       // run the query on the database
       $result = db_query($query);
       
       ?>
        <table width="550" class="borderless">
       <tr>
        <td colspan="5">
        	<h2>Box League Name</h2>
        </td>

        </tr>
        
        <?
       $numrows = mysqli_num_rows($result);
       $rowcount =  $numrows;
       $i=1;
       while($row = mysql_fetch_row($result)) { 
		
       	 $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
       	 
       	?>


            <tr >
            <td class="normal"><?=$row[3]?></td>
            <td class="normal"><?=$row[0]?></td>

            <td align="right">
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

            <? $rowcount = $rowcount - 1; 
                $i++;
            ?>
        <? }

        if ($numrows==0){ ?>
           <tr>
           <td colspan="6"><font class="normalsm">There are currently no box leagues configured.</font></td>
           </tr>
      
        <?  }  ?>
      
      
     </table>


   </td>
 </tr>
</table>


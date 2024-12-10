<?



//Set the http variables
$action = $_REQUEST["action"];


 if($action=="remove"){
   // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxrank
                             FROM tblBoxLeagues
                             WHERE boxid = $boxid
                             AND siteid = ".get_siteid()." ");

   $boxrankval = mysqli_result($oldboxplaceresult, 0);

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
 
 

  $clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
  $clubresult = db_query($clubquery);
  $clubobj = db_fetch_object($clubresult);

  $gmtime =   gmmktime();
  $tzdelta = $clubobj->timezone*3600;
  $curtime =   $gmtime+$tzdelta;

  ?>



<script language="JavaScript">


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

       <td class="label">Club Team Name:</td>
        <td><input type="text" name="clubteam" size=25>
                <?err($errors->clubteam)?>
        </td>
       </tr>

       <tr>
        <td class="label">Ladder:</td>
        <td>
            <select name="ladder">
                <option value="0"></option>
            <? 
             $ladders = getClubSiteLadders( get_siteid() );
             for ($i=0; $i < count($ladders); ++$i) { ?>
                <option value="<?=$ladders[$i]['id'] ?>"><?=$ladders[$i]['name'] ?></option>
            <?  } ?>
             </select>
            <?err($errors->ladder)?>
        </td>
    </tr>

        <tr>

     <tr>
      <td colspan="2">
      <input type="button" name ="submit" value="Submit" id="submitbutton">
      <input type="hidden" name ="submitme" value="submitme" >
      </form>

       

      </td>

     </tr>
     <tr>
      <td colspan="2">
          <hr>
      </td>

     </tr>
     
        <table width="350" class="borderless">
            <tr>
                <th>Team Name</th>
                <th>Ladder Name</th>
            </tr>
        
        <?
        
        $query = "SELECT tCLT.name AS teamname, tCSL.name AS laddername
                    FROM tblClubLadderTeam tCLT
                    INNER JOIN clubpro_main.tblClubSiteLadders tCSL ON tCLT.ladderid = tCSL.id
                    WHERE tCSL.siteid = ".get_siteid().";";

        // run the query on the database
        $result = db_query($query);

       $numrows = mysqli_num_rows($result);
       $rowcount =  $numrows;
       $i=1;
       while($row = mysqli_fetch_array($result)) { 
		
       	 $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
       	 
       	?>
            
            <tr class="<?=$rc?>">
            <td class="normal"><?=$row[teamname]?></td>
            <td class="normal"><?=$row[laddername]?></td>

            </tr>

            <? $rowcount = $rowcount - 1; 
                $i++;
            ?>
        <? }

        if ($numrows==0){ ?>
           <tr>
           <td colspan="2"><font class="normalsm">There are currently no club teams configured.</font></td>
           </tr>
      
        <?  }  ?>
      
      
     </table>


   </td>
 </tr>
</table>


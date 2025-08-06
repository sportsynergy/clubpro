<?

  $clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
  $clubresult = db_query($clubquery);
  $clubobj = db_fetch_object($clubresult);

  $gmtime =   time();
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
                <? is_object($errors) ? err($errors->clubteam) : ""?>
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
            <? is_object($errors) ? err($errors->ladder) : ""?>
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
                <th></th>
            </tr>
        
        <?
        
        $query = "SELECT tCLT.name AS teamname, tCSL.name AS laddername, tCLT.id AS teamid
                    FROM tblClubLadderTeam tCLT
                    INNER JOIN clubpro_main.tblClubSiteLadders tCSL ON tCLT.ladderid = tCSL.id
                    WHERE tCLT.enddate IS NULL
                    AND tCSL.siteid = ".get_siteid().";";

        // run the query on the database
        $result = db_query($query);

       $numrows = mysqli_num_rows($result);
       $rowcount =  $numrows;
       $i=1;
       while($row = mysqli_fetch_array($result)) { 
		
       	 $rc = (($i/2 - intval($i/2)) > .1) ? "lightrow" : "darkrow";
       	 
       	?>
            <form name="removeteam<?=$rowcount?>" method="post" 
            action="<?=$ME?>">
            <input type="hidden" name="teamid" value="<?=$row[teamid]?>">
            <input type="hidden" name ="submitme" value="submitme" >
            <input type="hidden" name="action" value="remove">
            </form>

            <form name="manageteam<?=$rowcount?>" method="post" 
            action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_team_manage.php">
            <input type="hidden" name="teamid" value="<?=$row[teamid]?>">
            </form>
      
            <tr class="<?=$rc?>">
            <td class="normal"><?=$row[teamname]?></td>
            <td class="normal"><?=$row[laddername]?></td>
            <td class="normal">
            
            <a href="javascript:submitForm('manageteam<?=$rowcount?>')">Manage</a>
            | <a href="javascript:submitForm('removeteam<?=$rowcount?>')">Delete</a></td>

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


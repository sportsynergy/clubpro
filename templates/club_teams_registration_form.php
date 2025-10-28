<?

  $clubquery = "SELECT timezone from tblClubs WHERE clubid=".get_clubid()."";
  $clubresult = db_query($clubquery);
  $clubobj = db_fetch_object($clubresult);

  $gmtime =   time();
  $tzdelta = $clubobj->timezone*3600;
  $curtime =   $gmtime+$tzdelta;

  ?>

<script language="JavaScript">


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
            <label for="clubteam" class="form-label">Club Team Name</label>
            <input type="text" name="clubteam" id="clubteam" class="form-control" aria-label="Box name">
            <? is_object($errors) ? err($errors->clubteam) : ""?>
        </div>

      
     <div class="mb-3">
        <label for="courttypeid" class="form-label">Court Type</label>
        <select class="form-select" name="ladder" id="ladder" aria-label="Ladder">
            <option value="0"></option>
            <? 
             $ladders = getClubSiteLadders( get_siteid() );
             for ($i=0; $i < count($ladders); ++$i) { ?>
                <option value="<?=$ladders[$i]['id'] ?>"><?=$ladders[$i]['name'] ?></option>
            <?  } ?>
             </select>
        <? is_object($errors) ? err($errors->ladder) : ""?>
    </div>

      
    
      <button type="submit" class="btn btn-primary" id="submitbutton">Submit</button>
      <input type="hidden" name ="submitme" value="submitme" >
      </form>

             </div> <!-- col-6 -->
            <div class="col-6">
     
    <div class="mb-5"> 
        <table class="table table-striped">
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
       while($row = mysqli_fetch_array($result)) { 
		
       	 
       	?>
            <form name="removeteam<?=$rowcount?>" method="post" action="<?=$ME?>">
                <input type="hidden" name="teamid" value="<?=$row['teamid']?>">
                <input type="hidden" name ="submitme" value="submitme" >
                <input type="hidden" name="action" value="remove">
            </form>

            <form name="manageteam<?=$rowcount?>" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_team_manage.php">
                <input type="hidden" name="teamid" value="<?=$row['teamid']?>">
            </form>
      
            <tr>
                <td><?=$row['teamname']?></td>
                <td><?=$row['laddername']?></td>
            <td>
            
            <a href="javascript:submitForm('manageteam<?=$rowcount?>')">Manage</a>
            | <a href="javascript:submitForm('removeteam<?=$rowcount?>')">Delete</a></td>

            </tr>

            
        <? }

        if ($numrows==0){ ?>
           <tr>
            <td colspan="2">There are currently no club teams configured.</td>
           </tr>
        <?  }  ?>
     </table>

    </div> <!-- mb-5 -->
    </div> <!-- col-6 -->
   </div> <!-- row -->
</div> <!-- container -->


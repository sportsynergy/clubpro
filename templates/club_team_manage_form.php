

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



 function onCancelButtonClicked(){
	parent.location="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_teams.php"
 }

function onSubmitButtonClicked(){
	submitForm('entryform');
}

  document.entryform.name1.focus();
  document.getElementById('name1').setAttribute("autocomplete", "off");
</script>

<?

//Set the http variables
$action = $_REQUEST["action"];
$userid = $_REQUEST["userid"];

?>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


<div class="mb-3" style="width: 70%;">
        <label for="name1" class="form-label">Player Name:</label>

<input id="name1" name="name1" type="text" class="form-autocomplete form-control" />
<input id="id1" name="teamplayer" type="hidden" />
      <script>
            <?
            
              $wwwroot = $_SESSION["CFG"]["wwwroot"];
              pat_autocomplete( array(
              'baseUrl'=> "$wwwroot/users/ajaxServer.php",
              'source'=>'name1',
              'target'=>'id1',
              'className'=>'autocomplete',
              'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&ladderid=$ladderid",
              'progressStyle'=>'throbbing',
              'minimumCharacters'=>3,
              ));
              ?>
        </script>

        <input type="hidden" name="teamid" value="<?=$teamid ?>">
        <input type="hidden" name="ladderid" value="<?=$ladderid ?>">
        <input type="hidden" name="submitme" value="submitme">
			
      </div>

    <div class="my-3">
        <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Add Player</button>
        <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
    </div>

    </form>



     <?
       //List out all of the players in the team
       $query = "SELECT concat(tU.firstname, ' ', tU.lastname) AS teamplayername, tCLT.id, tU.userid
                    FROM tblClubLadderTeam tCLT
                    INNER JOIN tblClubLadderTeamMember tCLTm ON tCLT.id = tCLTm.teamid
                    INNER JOIN tblUsers tU ON tCLTm.userid = tU.userid
                    WHERE tCLT.id = $teamid
                    AND tCLTm.enddate IS NULL";

       // run the query on the database
       $result = db_query($query); ?>

        <div class="mb-4" style="width: 70%;">
           
       <table  class="table table-striped">

       <?  $rownum = db_num_rows($result);
        
        while($row = db_fetch_array($result)) { 	?>
         
          <tr>
	         <td >  
	         	<span><?=$row['teamplayername']?></span>
	         </td> 
             <td >
             <form name="removeplayer<?=$rownum?>" method="post" action="<?=$ME?>" >
                <input type="hidden" name="userid" value="<?=$row['userid']?>">
                <input type="hidden" name ="submitme" value="submitme" >
                <input type="hidden" name ="teamid" value="<?=$row['id']?>" >
                <input type="hidden" name="action" value="remove">
              </form>

	         	<span>
                 <a href="javascript:submitForm('removeplayer<?=$rownum?>')">Remove</a>
                </span>
	         </td> 
         </tr>
        
       <? 
        $rownum = $rownum - 1;
       } ?>

     </table>
      </div>





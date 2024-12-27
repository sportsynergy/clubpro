

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

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        document.getElementById('name1').setAttribute("autocomplete", "off");

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>

<?

//Set the http variables
$action = $_REQUEST["action"];
$userid = $_REQUEST["userid"];

?>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<table width="500" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
     <tr>
         <td class=clubid<?=get_clubid()?>th>
         	<span class="whiteh1">
         		<div align="center"><? pv($DOC_TITLE) ?></div>
         	</span>
         </td>
    </tr>

 <tr>
    <td>
      <table width="550" cellspacing="5" cellpadding="0" class="borderless">
		<tr>
        <td>
            <input type="button" value="Add Player" name="submit" id="submitbutton">
        </td>
        <td>
 				<input id="name1" name="name1" type="text" size="30" class="form-autocomplete" />
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

        </td>
 
       <tr>
            <td></td>
            <td>
            	<input type="hidden" name="teamid" value="<?=$teamid ?>">
              <input type="hidden" name="ladderid" value="<?=$ladderid ?>">
            	<input type="hidden" name="submitme" value="submitme">
			</td>
       </tr>
       
     <tr>
      <td></td>
      <td> </td>
     </tr>
     <tr>
      <td colspan="2">
          <hr>
      </td>

     </tr>
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

       <table width="500" class="borderless">

       <? 
        $rownum = db_num_rows($result);
        
        while($row = db_fetch_array($result)) {

       	$rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
       	
       	?>
         
          <tr class="<?=$rc?>" >
	         <td >
                
	         	<span class="normal"><?=$row[teamplayername]?></span>
	         </td> 
             <td >
             <form name="removeplayer<?=$rownum?>" method="post" action="<?=$ME?>" >
                <input type="hidden" name="userid" value="<?=$row[userid]?>">
                <input type="hidden" name ="submitme" value="submitme" >
                <input type="hidden" name ="teamid" value="<?=$row[id]?>" >
                <input type="hidden" name="action" value="remove">
              </form>

	         	<span class="normal">
                 <a href="javascript:submitForm('removeplayer<?=$rownum?>')">Remove</a>
                </span>
	         </td> 
         </tr>
        
       <? 
        $rownum = $rownum - 1;
       } ?>

     </table>


   </td>
</tr>
</tr>

</table>

<div style="height: 2em;"></div>
<div>
    <span style="text-align: right;"> 
    	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_teams.php" > << Back to club teams registration page </a> 
    </span>
</div> 

</td>
</tr>
</table>
</form>





<form name="singles_entryform" method="post" action="<?=$ME?>" autocomplete="off">


      <div class="my-3">
        <input id="name1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" onchange="enableMatchType()"/>
        <input id="id1" name="player1" type="hidden" />
        <script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

        </script>
      </div>
    
      <div class="mb-3">
        Defeated
      </div>

      <div class="mb-3">
      
      <input id="name2" name="playertwoname" type="text" size="30" class="form-control form-autocomplete" onchange="enableMatchType()"/>
        <input id="id2" name="player2" type="hidden" />
        <script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name2',
						'target'=>'id2',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
          </div>

          <div class="mb-3">
        <label for="score" class="form-label">Score:</label>
        <select name="score" id="score" class="form-select"> </select>
        </div>

      <div class="mb-3">
        <label for="matchtype" class="form-label">Match Type:</label>
        <select name="matchtype" id="matchtype" class="form-select">
          <option value="practice">Practice</option>
          <option value="ladder">Ladder</option>
        </select>
        </div>  

      <div class="mb-3">
        <label for="courttype" class="form-label">Court Type:</label>
        <select name="courttype" id="courttype" class="form-select" onchange="unsetplayers();setMatchScore()">
          <?
                     while($row = mysqli_fetch_row($singlesCourtTypeDropDown)) {
                          echo "<option value=\"$row[0]\">$row[1]</option>\n";
                     }
                     ?>
        </select>
        </div>  
      <div class="mb-3">
        <input type="button" name="submit" value="Report Score" id="ssubmitbutton" class="btn btn-primary" onclick="onSinglesSubmitButtonClicked()">
        <input type="hidden" name="submitme" value="submitme">
        <input type="hidden" name="usertype" value="singles">
      </div>  

</form>




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

function unsetplayers()
{
        document.singles_entryform.playeronename.value = "";
        document.singles_entryform.player1.value = "";
        document.singles_entryform.playertwoname.value = "";
        document.singles_entryform.player2.value = "";
}

function enableMatchType()
{
  if(document.singles_entryform.player1.value != "" &&
    document.singles_entryform.player2.value != ""){
    document.singles_entryform.player2.value
  }
    
}


</script>


<form name="doubles_entryform" method="post" action="<?=$ME?>" autocomplete="off">



<div class="my-3">
        <input id="dname1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" />
                <input id="did1" name="player1" type="hidden" />
                <script>
                <?
                $wwwroot = $_SESSION["CFG"]["wwwroot"];
                pat_autocomplete( array(
                        'baseUrl'=> "$wwwroot/users/ajaxServer.php",
                        'source'=>'dname1',
                        'target'=>'did1',
                        'className'=>'autocomplete',
                        'parameters'=> "action=autocomplete&name={dname1}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
                        'progressStyle'=>'throbbing',
                        'minimumCharacters'=>3,
                        ));

                ?>

                </script>
</div>



<div class="mb-3">
        <input id="dname2" name="playertwoname" type="text" size="30" class="form-control form-autocomplete" />
                <input id="did2" name="player2" type="hidden" />
    			<script>
                <?
                  $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'dname2',
						'target'=>'did2',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={dname2}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
</div>

<div class="mb-3">
    <label class="form-label">Defeated</label> 
</div>

<div class="mb-3">
 <input id="name3" name="playerthreename" type="text" size="30" class="form-control form-autocomplete" />
                <input id="id3" name="player3" type="hidden" />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name3',
						'target'=>'id3',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name3}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
                    
</div>



<div class="mb-3">
  <input id="name4" name="playerfourname" type="text" size="30" class="form-control form-autocomplete" />
        <input id="id4" name="player4" type="hidden" />
        <script>
        <?
            $wwwroot = $_SESSION["CFG"]["wwwroot"];
            pat_autocomplete( array(
                'baseUrl'=> "$wwwroot/users/ajaxServer.php",
                'source'=>'name4',
                'target'=>'id4',
                'className'=>'autocomplete',
                'parameters'=> "action=autocomplete&name={name4}&userid=".get_userid()."&courttype={courttype}&siteid=".get_siteid()."&clubid=".get_clubid()."",
                'progressStyle'=>'throbbing',
                'minimumCharacters'=>3,
                ));

            ?>

        </script>
</div>

<div class="mb-3">
    <label for="score" class="form-label">Score:</label>
    <select name="score" class="form-select"></select>
</div>
<div class="mb-3">
    <label for="matchtype" class="form-label">Match Type:</label>
    <select name="matchtype" class="form-select">
        <option value="practice">Practice</option>
        <option value="challenge" >Challenge</option>
    </select>
</div>
<div class="mb-3">
    <label for="courttype" class="form-label">Court Type:</label>
    <select name="courttype" onchange="unsetplayers();setMatchScore()" id="courttype" class="form-select">
        <?
        while($row = mysqli_fetch_row($doublesCourtTypeDropDown)) {
            echo "<option value=\"$row[0]\">$row[1]</option>\n";
        }
        ?>
    </select>
</div>

<div class="mb-3">  
    <input type="button" name="submit" value="Report Score" id="dsubmitbutton" class="btn btn-primary">
    <input type="hidden" name="usertype" value="doubles">
    <input type="hidden" name="submitme" value="submitme">
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
        document.doubles_entryform.playeronename.value = "";
        document.doubles_entryform.player1.value = "";
        document.doubles_entryform.playertwoname.value = "";
        document.doubles_entryform.player2.value = "";
        document.doubles_entryform.playerthreename.value = "";
        document.doubles_entryform.player3.value = "";
        document.doubles_entryform.playerfourname.value = "";
        document.doubles_entryform.player4.value = "";
}


</script>
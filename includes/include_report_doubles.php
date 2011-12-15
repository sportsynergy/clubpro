

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

<div style="height: 25px;"></div>


<form name="doubles_entryform" method="post" action="<?=$ME?>" autocomplete="off">


     <table cellspacing="10" cellpadding="0" class="tabtable" id="formtable-doubles">
			
                 <tr>
                 <td><span class="label">Team One: </span></td>
				 <td>
                
                <input id="dname1" name="playeronename" type="text" size="30" class="form-autocomplete" />
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
                </td>
                 <td>
                 
                <input id="dname2" name="playertwoname" type="text" size="30" class="form-autocomplete" />
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
                </td>
                </tr>
                 
                 <tr>
                 	<td colspan="2" align="right"><span class="label">Defeated</span>
                 </tr>   
                    
                               
                 <tr>
                 <td><span class="label">Team Two: </span></td>
                 <td>
                
                <input id="name3" name="playerthreename" type="text" size="30" class="form-autocomplete" />
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
                </td>
                 <td>
                 
                 <input id="name4" name="playerfourname" type="text" size="30" class="form-autocomplete" />
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
                
                </td>
                </tr>
                 <tr>
                	<td> <span class="label">Score:</span></td>
                	<td colspan="2">
						  <select name="score">
						    <option value="2">3-2</option>
						    <option value="1">3-1</option>
						    <option value="0">3-0</option>
						  </select>
   					</td>
                </tr>
                <tr>
                	<td> <span class="label">Match Type:</span></td>
                	<td colspan="2">
						  <select name="matchtype">
						  <option value="practice"  >Practice</option>
						    <option value="challenge" >Challenge</option>
						  </select>
   					</td>
                </tr>
                <tr>
                	<td><span class="label">Court Type:</span></td>
                	 
		            <td>   
		                 <select name="courttype" onchange="unsetplayers()" id="courttype">
		                 <?
		                 while($row = mysql_fetch_row($doublesCourtTypeDropDown)) {
		                      echo "<option value=\"$row[0]\">$row[1]</option>\n";
		                 }
		                 ?>
                 
                 </select>
                	
                	</td>
                </tr>
                <tr>
                	<td>
  						<input type="button" name="submit" value="Report Score" id="dsubmitbutton">
  						 <input type="hidden" name="usertype" value="doubles">
  						 <input type="hidden" name="submitme" value="submitme">
                	<td>
                <tr>
 	</table>





</form>
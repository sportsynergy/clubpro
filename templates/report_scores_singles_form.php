<?php
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 15:46:27 -0500 (Fri, 26 May 2006) $

*/
?>
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
        document.entryform.playeronename.value = "";
        document.entryform.player1.value = "";
        document.entryform.playertwoname.value = "";
        document.entryform.player2.value = "";
}

function enableMatchType()
{
	if(document.entryform.player1.value != "" &&
		document.entryform.player2.value != ""){
		document.entryform.player2.value
	}
		
	
	
}

</script>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


<table cellspacing="0" cellpadding="5" width="710" align="center">
<tr>
<td>

<table cellspacing="0" cellpadding="0" width="550" class="clubid<?=get_clubid()?>th">
 <tr>
    <td height="60">
    	<font class=whiteh1>
    		<div align="center">
    			<? pv($DOC_TITLE) ?>
    		</div>
    	</font>
    </td>
 </tr>
 <? 
 
 //If the site only supports singles, don't display the tab for doubles
 if( mysql_num_rows($doublesCourtTypeDropDown) > 0){ ?>
 <tr>
    <td>
    	<table cellpadding="0" cellspacing="0" class="clubid<?=get_clubid()?>th">
    		<tr>
    			<td align="left" width="84"" ><img src="<?=$_SESSION["CFG"]["imagedir"]?>/singlesTabOn.gif" border="0"></a></td>
    			<td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/report_scores.php?ct=doubles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/doublesTabOff.gif" border="0"></td>
    		</tr>
    	</table>
    </td>
 </tr>
<? } ?>
<tr>
    <td class="generictable">

     <table cellspacing="10" cellpadding="0" width="400">
		
                 <tr>
                 <td>
                 
                <input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" onchange="enableMatchType()"/>
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
                </td>
                <td><span class="label"">Defeated</span></td>
                
                 <td>
                 
                 <input id="name2" name="playertwoname" type="text" size="30" class="form-autocomplete" onchange="enableMatchType()"/>
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
						  <select name="matchtype" >
						     <option value="practice">Practice</option>
						    <option value="challenge">Challenge</option>
							 <option value="boxleague">Box League</option>
						  </select>
   					</td>
                </tr>
                <tr>
                	<td><span class="label">Court Type:</span></td>

		                
		            <td colspan="2">   
		                 <select name="courttype" id="courttype" onchange="unsetplayers()">
		                 <?
		                 while($row = mysql_fetch_row($singlesCourtTypeDropDown)) {
		                      echo "<option value=\"$row[0]\">$row[1]</option>\n";
		                 }
		                 ?>
                 
                 </select>
                	
                	</td>
                </tr>
                
                <tr>
                	<td>
  						<input type="submit" name="submit" value="Submit">
  						 <input type="hidden" name="ct" value="<?=$ct?>">
  						 <input type="hidden" name="usertype" value="0">
  						
                	<td>
                <tr>
                <tr>
					<td colspan="3">
						<font class="normalsm">Remember, to record a score with a Match Type of Box League both players must be in the same box league.</font>
					</td>
				</tr>
        
 	</table>

</td>
</tr>



</table>


</td>
</tr>



</table>

</form>
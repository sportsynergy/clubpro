<?
/*
 * $LastChangedRevision: 840 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-26 18:52:13 -0600 (Sat, 26 Feb 2011) $

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

</script>

<form name="doubles_reservation_form" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
 
<table cellspacing="10" cellpadding="0" width="540" class="tabtable">
 		<tr>
            <td class="biglabel" colspan="2">Team 1</td>
        </tr>
        <tr>
            <td class="normal">Player One:</td>
            <td>
             <input id="dname1" name="playeronename" type="text" size="30" class="form-autocomplete"  
             <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userfullname()?>" disabled
              <? }else{ ?>
              	value="<? pv($frm["playeronename"]) ?>"
              <? } ?>
              />
             <input id="did1" name="playeroneid" type="hidden" 
              <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userid()?>"
             <? } else {?>
             	value="<? pv($frm["playeroneid"]) ?>"
             <? } ?>
             />
             <?err($errors->playeronename)?>
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'dname1',
						'target'=>'did1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={dname1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>

            </td>
       </tr>


       <tr>
            <td class="normal">Player Two:</td>
              <td>  
              		<input id="dname2" name="playertwoname" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayertwo();" value="<? pv($frm["playertwoname"]) ?>"/>
             		<?err($errors->playertwoname)?>
             		<input id="did2" name="playertwoid" type="hidden" value="<? pv($frm["playertwoid"]) ?>" />
	    			<script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'dname2',
							'target'=>'did2',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={dname2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
	                </script>
                </td>

       </tr>



       <tr>
            <td class="biglabel" colspan="2"><br>Team 2</td>
        </tr>
        <tr>
            <td class="normal">Player One:</td>
             <td>  
              		<input id="name3" name="playerthreename" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayerthree();" value="<? pv($frm["playerthreename"]) ?>"/>
             		<?err($errors->playerthreename)?>
             		<input id="id3" name="playerthreeid" type="hidden" value="<? pv($frm["playerthreeid"]) ?>"/>
	    			<script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'name3',
							'target'=>'id3',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={name3}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
	                </script>
                </td>
       </tr>


       <tr>
            <td class="normal">Player Two:</td>
              <td> 
					<input id="name4" name="playerfourname" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayerfour();" value="<? pv($frm["playerfourname"]) ?>"/>
             		<?err($errors->playerfourname)?>
             		<input id="id4" name="playerfourid" type="hidden" value="<? pv($frm["playerfourid"]) ?>"/>
	    			<script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'name4',
							'target'=>'id4',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={name4}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
							'progressStyle'=>'throbbing',
							'minimumCharacters'=>3,
							));
	           
	                 ?>
	                </script>
              </td>

       </tr>



       <tr>
        <td colspan="2" class="italitcsm">To book a reservation, type in the name of the each player and select from the list of club members.  If you don't
        know who all four players will be yet, don't worry, just fill in what you know now.  We will ask you about how to advertise for any open spots on the 
        next screen.
        </td>
    </tr>
    <tr>
         <td height="15" colspan="2"><hr></td>
    </tr>
    <tr>
    	  <td class="biglabel"> Match Type:</td>
    	<td class="normal" >
    		<select name="matchtype" >
    			 <option value="0" selected>Practice</option>
    			 <option value="2">Challenge</option>
    		</select>
    	</td>
    </tr>
    <tr>
        <td colspan="2">
        	<span class="italitcsm" title="test">The match type really only has do with what happens when the score is reported.  The main differce between a 
        Practice match and a Challenge match is that when reporting the results of a Challenge match the players positions will be adjusted in the Club
        Ladder. Also, with a Challenge match the ranking adjustments are weighted twice those of a Practice match.
        </span>
        </td>
    </tr>
       <tr>
           <td colspan="2"> 
	           	<br/><br/>
	           	<input type="submit" name="submit" value="Make Reservation">  
	           	<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
	        </td>
    </tr>

 </table>

</td>
</tr>
</table>

<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
<input type="hidden" name="courttype" value="doubles">
<input type="hidden" name="partner" value="needfour">
<input type="hidden" name="team" value="needtwo">
<input type="hidden" name="action" value="create">


</form>
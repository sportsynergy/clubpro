<?php
  /*
 * $LastChangedRevision: 675 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-02-27 14:46:32 -0800 (Fri, 27 Feb 2009) $

*/
?>
<?
  $DOC_TITLE = "Doubles Court Reservation";
?>

<script language="Javascript">
<!--
// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

//-->

function unsetplayerone(fieldname)
{
       if( document.entryform.name1.value.length == 0){
       	 document.entryform.id1.value = "";
       }

}

function unsetplayertwo(fieldname)
{
       if( document.entryform.name2.value.length == 0){
       	 document.entryform.id2.value = "";
       }

}

function unsetplayerthree(fieldname)
{
       if( document.entryform.name3.value.length == 0){
       	 document.entryform.id3.value = "";
       }

}

function unsetplayerfour(fieldname)
{
       if( document.entryform.name4.value.length == 0){
       	 document.entryform.id4.value = "";
       }

}

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


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

<tr>
    <td>

     <form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">

<table cellspacing="0" cellpadding="0" width="570" >
 <tr>
    <td class=clubid<?=get_clubid()?>th height="60"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>
 <? 
 // Display the tabs for administrators on courttypes 1 or 2
 if( ($reservationType == 1 || $reservationType == 2 ) && (get_roleid()==2 || get_roleid()==5 )){ ?>
 <tr>
        <td>
            <table cellpadding="0" cellspacing="0" class="clubid<?=get_clubid()?>th">
                   <tr>
                        <td align="left" width="84"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=event"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/eventTabOff.gif" border="0"></a></td>
                       <? if($reservationType == 2 || $reservationType == 1){?>
                        <td align="left" width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=doubles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/doublesTabOn.gif" border="0"></a></td>
                        <? } ?>
                         <? if($reservationType == 0 || $reservationType == 1){?>
                        <td align=left width=84><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php?time=<?pv($time)?>&courtid=<?pv($courtid)?>&ct=singles"><img src="<?=$_SESSION["CFG"]["imagedir"]?>/singlesTabOff.gif" border="0"></a></td>
                        <? } ?>
                        <td width=100%></td>
                   </tr>
           </table>
       </td>
</tr>
 <? } ?>
 <tr>
    <td class="generictable">

     <table cellspacing="10" cellpadding="0" width="440">
 		<tr>
            <td class="biglabel" colspan="2">Team 1</td>
        </tr>
        <tr>
            <td class="normal">Player One:</td>
            <td>
             <input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayerone();" 
             <?
             // If regular player default the first player as them selves, don't allow them to edit this either
             if( get_roleid() == 1){ ?>
             	value="<?=get_userfullname()?>" disabled
              <? }else{ ?>
              	value="<? pv($frm["playeronename"]) ?>"
              <? } ?>
              />
             <input id="id1" name="playeroneid" type="hidden" 
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
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
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
              		<input id="name2" name="playertwoname" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayertwo();" value="<? pv($frm["playertwoname"]) ?>"/>
             		<?err($errors->playertwoname)?>
             		<input id="id2" name="playertwoid" type="hidden" value="<? pv($frm["playertwoid"]) ?>" />
	    			<script>
	                <?
	                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
	                 pat_autocomplete( array(
							'baseUrl'=> "$wwwroot/users/ajaxServer.php",
							'source'=>'name2',
							'target'=>'id2',
							'className'=>'autocomplete',
							'parameters'=> "action=autocomplete&name={name2}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
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
        <td colspan="2" class="italitcsm">The match type really only has do with what happens when the score is reported.  The main differce between a 
        Practice match and a Challenge match is that when reporting the results of a Challenge match the players positions will be adjusted in the Club
        Ladder.  Oh, and with a Challenge match the ranking adjustments are weighted twice those of a Practice match.
        </td>
    </tr>
       <tr>
           <td colspan="2"> 
           <br/><br/>
           		<input type="submit" name="submit" value="Make Reservation">  
           		<input type="hidden" name="partner" value="single">
          		<input type="hidden" name="usertype" value="frontdesk">
          		<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
				<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
				<input type="hidden" name="ct" value="doubles">
	           <input type="hidden" name="courttype" value="doubles">
           </td>
    </tr>

 </table>

</td>
</tr>
</table>


</form>



</td>
</tr>
</table>
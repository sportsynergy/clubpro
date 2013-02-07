<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $
*/
?>

<script language="Javascript">

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}

//please keep these lines on when you copy the source
//made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
if (document.getElementById) {
for (var sch = 0; sch < dform.length; sch++) {
 if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
}
}
return true;
}


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbuttonvalue" });   
        oCancelButton.on("click", onCancelButtonClicked);
    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }
 
</script>


<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
        <tr>
         <td class="label">Partner:</td>
         <td>
             <input id="partnername" name="partnername" type="text" size="30" class="form-autocomplete" />
             <input id="partnerid" name="partnerid" type="hidden"  />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'partnername',
						'target'=>'partnerid',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={partnername}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>
                </script>
        
		</td>

       </tr>
      
    <tr>
    	<td colspan="2" class="italitcsm">
    		To search for a name type in the player's first or last name.  To just put your name in and look for a partner, leave this box empty and click 'Make Reservation'.  If you are looking for one or more players you will be 
    		prompted for how you would like to advertise this match on the next screen.
    		<br><br>
    	</td>
    </tr>
 	<tr>
           <td class="normal" colspan="2">
				<input type="button" name="Submit" value="Make Reservation" id="submitbutton">
	            <input type="button" value="Cancel" id="cancelbutton">
	       </td>   
    </tr>


 </table>


 </td>
 </tr>

</table>

<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="courttype" value="doubles">
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="action" value="addteam">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="creator" value="<?=$creator?>">



	         

</form>
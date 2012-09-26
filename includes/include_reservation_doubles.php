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
?>
<form name="doublesform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
  <table cellspacing="5" cellpadding="0" width="540" class="tabtable" id="doubles-formtable">
    <tr>
      <td><input id="dname1" name="playeronename" type="text" size="30" class="form-autocomplete"  
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
                </script></td>
      <td><input id="dname2" name="playertwoname" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayertwo();" value="<? pv($frm["playertwoname"]) ?>"/>
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
	                </script></td>
    </tr>
    <tr>
      <td><input id="name3" name="playerthreename" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayerthree();" value="<? pv($frm["playerthreename"]) ?>"/>
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
	                </script></td>
      <td><input id="name4" name="playerfourname" type="text" size="30" class="form-autocomplete" onchange="javascript:unsetplayerfour();" value="<? pv($frm["playerfourname"]) ?>"/>
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
	                </script></td>
    </tr>
    <tr>
      <td colspan="2"><span class="normal">To book a reservation, type in the name of the each player and select from the list of club members.  If you don't
        know who all four players will be yet, don't worry, just fill in what you know now.  We will ask you about how to advertise for any open spots on the 
        next screen. </span></td>
    </tr>
    <tr>
      <td height="3" colspan="2"><hr></td>
    </tr>

	<? if( get_roleid()==2 || get_roleid()==4) {?>
		<tr>
	      <td class="biglabel">Repeat:</td>
	      <td><select name="repeat" onchange="disableSinglesOptions(this)">
	          <option value="norepeat">None</option>
	          <option value="daily">Daily</option>
	          <option value="weekly">Weekly</option>
	          <option value="biweekly">Bi-Weekly</option>
	          <option value="monthly">Monthly</option>
	          <?err($errors->repeat)?>
	        </select></td>
	    </tr>
		<tr>
	      <td class="biglabel">Frequency:</td>
	      <td><select name="frequency" disabled="true">
	          <option value="">Select Option</option>
	          <option value="">----------------------------</option>
	          <option value="week">For a Week</option>
	          <option value="month">For a Month</option>
	          <option value="year">For a Year</option>
	          <?err($errors->duration)?>
	        </select></td>
	    </tr>
	<? } ?>
	
	<? if($variableDuration == 'y') { ?>
	<tr> 
		 <td class="biglabel">Duration:</td>
		 <td>
			<select name="duration">
				<? 
				$timetonext = $nexttime - $time;
				
				if($timetonext == 900 ){ ?>
					<option value=".25">15 Minutes</option>
				<?}
				 if($timetonext == 1800 ){ ?>
					<option value=".5">30 Minutes</option>
				<?}
				if($timetonext >= 2700 || $nexttime == null){ ?>
					<option value=".75">45 Minutes</option>
				<?}
				if($timetonext >= 3600 || $nexttime == null ){ ?>
					<option value="1">60 Minutes</option>
				<?}
				if($timetonext >= 5400 || $nexttime == null ){ ?>
					<option value="1.5">90 Minutes</option>
				<? } ?>
			
					
			</select>
			</td>
	</tr>
	<? } ?>

    <tr>
      <td ><span  class="biglabel">Match Type:</span></td>
      <td class="normal" ><select name="matchtype" >
          <option value="0" selected>Practice</option>
          <? if ( isPointRankingScheme() ) {?>
          <option value="2">Challenge</option>
          <? } ?>
          <? if( get_roleid() ==2 || get_roleid()==4) {?>
          <option value="4">Lesson</option>
          <? } ?>
        </select></td>
    </tr>
    <tr>
      <td colspan="2"><span class="normal" >
        <? if( isLadderRankingScheme() ){?>
        Select Challenge Match you plan on recording the score for the ladder.
        <? }else{?>
        Select Challenge Match you plan on recording the score
        <? } ?>
        For more infomation on the match types, click <a href="../help/squash-matchtypes.html?iframe=true&width=600&height=450" rel="prettyPhoto[iframe]">here</a>. </span></td>
    </tr>
    <? if( get_roleid()==2 || get_roleid() ==4){ ?>
    <tr>
      <td colspan="2"><input type="checkbox" name="lock" />
        <span class="normal">Lock reservation</span></td>
    </tr>
    <?}?>
    <tr>
      <td colspan="2"><br/>
        <br/>
        <input type="button" name="submit" value="Make Reservation" id="doubles-submitbutton">
        <input type="button" value="Cancel" id="doubles-cancelbutton"></td>
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

    YAHOO.util.Event.onContentReady("doubles-formtable", function () {

        var dblSubmitButton = new YAHOO.widget.Button("doubles-submitbutton", { value: "doubles-submitvalue" });
        dblSubmitButton.on("click", onDoublesSubmitButtonClicked);

        var dblCancelButton = new YAHOO.widget.Button("doubles-cancelbutton", { value: "doubles-cancelbuttonvalue" });   
        dblCancelButton.on("click", onDoublesCancelButtonClicked);
    });

} ();


function onDoublesSubmitButtonClicked(){
	var myButton = YAHOO.widget.Button.getButton('doubles-submitbutton'); 		
	myButton.set('disabled', true);
	submitForm('doublesform');
}

 function onDoublesCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
 }

</script> 
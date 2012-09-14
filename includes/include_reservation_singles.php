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

<form name="singlesform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">
  <table cellspacing="10" cellpadding="0" width="440" class="tabtable" id="singles-formtable">
    <tr>
      <td class="label">Player&nbsp;One:</td>
      <td><input id="name1" name="playeronename" type="text" size="35" class="form-autocomplete" />
        <input id="id1" name="playeroneid" type="hidden" />
        <script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
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

                </script></td>
    </tr>
    <tr>
      <td class="label">Player&nbsp;Two:</td>
      <td><input id="name2" name="playertwoname" type="text" size="35" class="form-autocomplete" />
        <input id="id2" name="playertwoid" type="hidden"/>
        <script>
                <?php
$wwwroot = $_SESSION["CFG"]["wwwroot"];
pat_autocomplete(array(
    'baseUrl' => "$wwwroot/users/ajaxServer.php",
    'source' => 'name2',
    'target' => 'id2',
    'className' => 'autocomplete',
    'parameters' => "action=autocomplete&name={name2}&userid=" . get_userid() . "&courtid=$courtid&siteid=" . get_siteid() . "&clubid=" . get_clubid() . "",
    'progressStyle' => 'throbbing',
    'minimumCharacters' => 3,
));
?>

                </script></td>
    </tr>


	<? if($variableDuration == 'y') { ?>
	<tr> 
		 <td class="label">Duration:</td>
		 <td>
			<select name="duration">
				<?
				$timetonext = $nexttime - $time; 
				
				if($timetonext == 900 ){ ?>
					<option value=".25">15 Minutes</option>
				<?}
				
				if($timetonext >= 1800 || $nexttime == null ){ ?>
					<option value=".5">30 Minutes</option>
				<?}
					
				if($timetonext >= 2700 || $nexttime == null){ ?>
					<option value=".75">45 Minutes</option>
				<?}
						
				if($timetonext >= 3600 || $nexttime == null){ ?>
					<option value="1">60 Minutes</option>
				<? } ?>	
					
			</select>
			</td>
	</tr>
	<? } ?>
	
    <tr>
      <td class="label">Match Type:</td>
      <td><select name="matchtype" onchange="disablePlayerDropDownWithSoloSelection(this)">
          <? if( isSiteBoxLeageEnabled() ){ ?>
          <option value="1">Box League</option>
          <? } ?>
          <? if ( isPointRankingScheme() ) {?>
          <option value="2">Challenge</option>
          <? } ?>
          <? if( get_roleid() ==2 || get_roleid()==4) {?>
          <option value="4">Lesson</option>
          <? } ?>
          <option value="0" selected>Practice</option>
          <option value="5">Solo</option>
        </select></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2"><span class="normalsm"> To book a reservation, type in the name of the each player then select from the list.
        For more infomation on the match types, click <a href="../help/squash-matchtypes.html?iframe=true&width=600&height=450" rel="prettyPhoto[iframe]">here</a>.
        <? if( get_roleid() == 2){ ?>
        If you want to put yourself down as available for a lesson, leave your name in as Player One, leave Player Two blank and set
        the matchtype as Lesson.
        <? } ?>
        </span></td>
    </tr>


    <? if( get_roleid()==2 || get_roleid() ==4){ ?>
    <tr>
      <td colspan="2"><input type="checkbox" name="lock" />
        <span class="normal">Lock reservation</span></td>
    </tr>
    <?}?>
    <tr>
      <td></td>
      <td><input type="button" name="submit" value="Make Reservation" id="singles-submitbutton"  >
        <input type="button" value="Cancel" id="singles-cancelbutton" ></td>
      <td></td>
      <td></td>
    </tr>
  </table>
  <input type="hidden" name="courttype" value="singles">
  <input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
  <input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
  <input type="hidden" name="action" value="create">
</form>
<script>

 YAHOO.example.init = function () {

	    YAHOO.util.Event.onContentReady("singles-formtable", function () {

	        var sinSubmitButton1 = new YAHOO.widget.Button("singles-submitbutton", { value: "submitbuttonvalue" });
	        sinSubmitButton1.on("click", onSinglesSubmitButtonClicked);
			
				
	        var sinCancelButton = new YAHOO.widget.Button("singles-cancelbutton", { value: "cancelbuttonvalue" });   
	        sinCancelButton.on("click", onSinglesCancelButtonClicked);
	
	    });

	} ();


	function onSinglesSubmitButtonClicked(){

		var myButton = YAHOO.widget.Button.getButton('singles-submitbutton'); 		
		myButton.set('disabled', true); 
		submitForm('singlesform');
	}

	 function onSinglesCancelButtonClicked(){

		parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'
	 }
 


function cancelCourt(){
	 var submitForm = document.createElement("FORM");
	 document.body.appendChild(submitForm);
	 submitForm.method = "POST";
	 submitForm.action = "<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_cancelation.php";
	 submitForm.submit();

}

function disablePlayerDropDownWithSoloSelection(matchtype)
{
     if(matchtype.value == "5"){
          document.singlesform.playertwoname.disabled = true;
          document.singlesform.playertwoname.disabled = true;
     }
     else{
     	document.singlesform.playertwoname.disabled = "";
     	document.singlesform.playertwoname.disabled = "";
     }

}

function defaultform() {

	document.singlesform.playeronename.value = "<?= addslashes(get_userfullname()) ?>";
	document.singlesform.playeroneid.value = <?= get_userid() ?>;
	document.singlesform.playertwoname.focus();
	
    <? if(get_roleid() == 1){ ?>
		document.singlesform.playeronename.disabled=true;
    	
   <? } ?>
    	
   
    
}

defaultform();

</script> 

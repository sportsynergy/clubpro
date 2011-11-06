<?
/*
 * $LastChangedRevision: 840 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-26 18:52:13 -0600 (Sat, 26 Feb 2011) $

*/
?>


<form name="singlesform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">

     <table cellspacing="10" cellpadding="0" width="440" class="tabtable">
        <tr>
            <td class="label">Player&nbsp;One:</td>
             
            <td>
            
            
             <input id="name1" name="playeronename" type="text" size="30" class="form-autocomplete" />
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

                </script>
            
		    </td>
       </tr>


       <tr>
            <td class=label>Player&nbsp;Two:</td>
              
            <td>
            
            <input id="name2" name="playertwoname" type="text" size="30" class="form-autocomplete" />
             <input id="id2" name="playertwoid" type="hidden"/>
    			<script>
                <?
                 $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
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
         <td class="label">Match Type:</td>
           <td><select name="matchtype" onchange="disablePlayerDropDownWithSoloSelection(this)">
             <option value="1">Box League</option>
             <option value="2">Challenge</option>
             <option value="4">Lesson</option>
             <option value="0" selected>Practice</option>
             <option value="5">Solo</option>

           </select>
           </td>
           <td></td>

       </tr>
       <tr>
        <td colspan="4">
        <span class="italitcsm">
        To book a reservation, type in the name of the each player then select from the list.
        For more infomation on the match types, click <A HREF=javascript:newWindow('../help/squash-matchtypes.html')>here</a>.  
        <? if( get_roleid() == 2){ ?>
        If you want to put yourself down as available for a lesson, leave your name in as Player One, leave Player Two blank and set
        the matchtype as Lesson.
        
        <? } ?>
        </span> 
        </td>
    </tr>
     <? if( get_roleid()==2 || get_roleid() ==4){ ?>
    <tr>
    	<td>
    		<input type="checkbox" name="lock" />
    		<span class="normal">Lock reservation</span>
    	</td>
    </tr>
    <?}?>
       <tr>
           <td></td>
           <td> 
           		<input type="submit" name="submit" value="Make Reservation"> 
           		<input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta())) ?>'">
            </td>
           <td>
	          
           </td>
           <td></td>
    </tr>

 </table>
 
 <input type="hidden" name="courttype" value="singles">
<input type="hidden" name="time" value="<?=$_REQUEST["time"]?>">
<input type="hidden" name="courtid" value="<?=$_REQUEST["courtid"]?>">
<input type="hidden" name="action" value="create">

 
 </form>


 
 <script>

 
 
document.onkeypress = function (aEvent)
{
 if(!aEvent) aEvent=window.event;
	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
 if( key == 13 ) // enter key
 {
     return false; // this will prevent bubbling ( sending it to children ) the event!
 }
	
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

	document.singlesform.playeronename.value = "<?= get_userfullname() ?>";
	document.singlesform.playeroneid.value = <?= get_userid() ?>;
	document.singlesform.playertwoname.focus();
	
    <? if(get_roleid() == 1){ ?>
		document.singlesform.playeronename.disabled=true;
    	
   <? } ?>
    	
   
    
}

defaultform();

</script>


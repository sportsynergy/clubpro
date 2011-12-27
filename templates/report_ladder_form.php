
<?
 //Initialize script variables
  $userarray = array();
?>

<script language="Javascript">

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

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbuttonvalue" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	<? if($laddertype=="player"){ ?>
	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php';
	<? } else{ ?>
	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/users/team_ladder.php';
	<? } ?>
	
 }
</script>




<form name="entryform" method="post" action="<?=$ME?>">



	<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
	 <tr>
	    <td class="clubid<?=get_clubid()?>th">
	    <font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
	 </tr>
	
	 <tr>
	    <td>
			     <table cellspacing="0" cellpadding="5" width="400">
			
			
			        <tr>
						<td class="label">Winner:</td>
			            <td>		
			                <select name="winner">
			                 <option value="challenger"><?=$ladderMatchArray['challenger_full']?></option>   
			                  <option value="challengee"><?=$ladderMatchArray['challengee_full']?></option>   
			                </select>
			            </td>
			       </tr>
			        <tr>
			        <td class="label">Score:</td>
			           <td>
			             <select name="score">
			                     <option value="3">3-0</option>
			                     <option value="2">3-1</option>
			                     <option value="1">3-2</option>
			             </select>
			           </td>
			
			       </tr>
			      
			      <tr>
			      	<td height="35"><!-- Spacer --></td>
			      </tr>
			     
			       <tr>
				       <td colspan="2">
				           <input type="button" name="submit" value="Record Score" id="submitbutton">
				           <input type="hidden" name="submitme" value="submitme" >
				           <input type="button" value="No, Go Back" id="cancelbutton">
				       </td>
			
			    </tr>
			    
			 </table>
	
	</td>
	</tr>
	</table>

	<input type="hidden" name="challengematchid" value="<?=$ladderMatchArray['id'] ?>">
	<input type="hidden" name="laddertype" value="<?=$laddertype ?>">
	<input type="hidden" name="challengerid" value="<?=$ladderMatchArray['challenger_id'] ?>">
	<input type="hidden" name="challengeeid" value="<?=$ladderMatchArray['challengee_id'] ?>">
	<input type="hidden" name="courttypeid" value="<?=$ladderMatchArray['courttypeid'] ?>">

</form>



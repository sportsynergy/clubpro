<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
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
  
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center">
    		<? if($locked=='y'){ ?>
	    	 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	    	<?}?>
			<? pv($DOC_TITLE) ?>
			</div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">

       <tr>
           <td class="normal">Do you want to sign up for this court?</td>
           <td>
	            <?
		       //if its locked and its just a player disable the submit button
		       $disabled="";
		       if( $locked=='y' && get_roleid()==1){
		       	
		       	$disabled = "disabled=disabled";
		       }
		       
		       ?>
           		<input type="button" name="cancel" <?=$disabled?> value="Yes" id="submitbutton"> 
            	<input type="button" value="No, go back" id="cancelbutton">
            </td>
    </tr>
 </table>

</table>


<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="guylookingformatch" value="<?=$userid?>">
<input type="hidden" name="courttype" value="singles">
<input type="hidden" name="matchtype" value="<?=$matchtype?>">
<input type="hidden" name="action" value="addpartner">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
            
            
</form>

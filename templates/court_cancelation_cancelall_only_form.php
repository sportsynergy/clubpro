
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


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable" >
  
   <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    <span class="whiteh1">
    	<div align="center"><? pv($DOC_TITLE) ?></div>
    </span>
   </td>
 </tr>
	<tr>
           <td>
           		<div class="normal">Are you sure you want to cancel this court?</div>
        	</td>
    </tr>
    <tr>
     <td>
      	<input type="button" name="cancel" value="Yes" id="submitbutton">
	    <input type="button" value="No" id="cancelbutton">
     </td>
    </tr>




</table>

<input type="hidden" name="cancelall" value="3">
<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
</form>


</td>
</tr>
</table>
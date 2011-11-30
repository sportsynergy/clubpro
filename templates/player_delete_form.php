

<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
       <tr>
           <td class="normal">Are you sure you want to Delete this user? You know, if there is any chance of this player coming back a better idea is to disable them.</td>
           
    </tr>
    <tr>
    	<td>
           		<input type="submit" name="cancel" value="Yes, I know. Delete this player" id="submitbutton">
           		<input type="button" value="No, go back" id="cancelbutton">
           </td>
    </tr>
 </table>

</table>

<input type="hidden" name="searchname" value="<?=$searchname?>">
<input type="hidden" name="userid" value="<?=$userid?>">

</form>


<script type="text/javascript">

document.entryform.searchname.focus();

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
	submitFormWithAction('entryform','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php');
}




</script>
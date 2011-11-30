<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script>

<form name="entryform" method="post" action="<?=$ME?>">


<table width="400" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
    <tr class="borderow">
         <td class=clubid<?=get_clubid()?>th>
         	<font class=whiteh1>
         		<div align="center"><? pv($DOC_TITLE) ?></div>
         	</font>
         </td>
    </tr>

 <tr>
    <td >
      
      <table cellspacing="5" cellpadding="0" class="borderless">
      <tr>
        <td class="label">Old Password:</td>
        <td><input type="password" name="oldpassword" size=25>
                <?err($errors->oldpassword)?>
        </td>
      </tr>

      <tr>
        <td class="label">New Password:</td>
        <td><input type="password" name="newpassword" size=25>
                <?err($errors->newpassword)?>
        </td>
      </tr>
      <tr>
        <td class="label">Confirm Password:</td>
        <td><input type="password" name="newpassword2" size=25>
                <?err($errors->newpassword2)?>
        </td>
     </tr>
     <tr>
        <td></td>
        <td><input type="button" name="submit" value="Change Password" id="submitbutton">

        </td>
     </table>
     <!-- Spacer -->
     
    
    
     

   </td>
 </tr>
</table>

</form>


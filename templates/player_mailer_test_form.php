


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

function onSubmitButtonClicked(){
	submitForm('entryform');
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();

</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    			<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td >

     <table width="400">
		
		<tr>
        <td class="label">To:</td>
        <td colspan="3">
            <input type="text" name="email_address" size="35">
        </td>
        
       </tr> 
       
       <tr>
        	<td class="label" >Subject:</td>
	        <td colspan="3">
          <input type="text" name="subject" size=67 value="<? pv($frm["subject"]) ?>">
	                <?err($errors->subject)?>
	        </td>
       </tr>
       <tr>
           <td class=label valign="top">Message:</td>
                 <td colspan="3">
                 <textarea name="message" cols="80" rows="15"></textarea>      
                </td>
        </tr>
       <tr>
           <td></td>
           <td><input type="button" name="submit" value="Send Email" id="submitbutton"></td>
            <td></td>
    </tr>
 </table>

<input type="hidden" name="submitme" value="submitme">

</table>
</form>


</td>
</tr>
</table>
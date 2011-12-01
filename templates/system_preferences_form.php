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


<table cellspacing="0" cellpadding="0" width="450" class="generictable" id="formtable">
  <tr class="borderow">
    <td class="clubid<?=get_clubid()?>th">
		<span class="whiteh1">
			<div align="center"><? pv($DOC_TITLE) ?></div>
		</span>
	</td>
 </tr>


 <tr>
    <td>

     <table width="450" cellpadding="6" cellspacing="6" border="0">
        <tr>
             <td>
                  <table width="450" cellpadding="2" cellspacing="2" border="0">
                       
                        	<td class="label">Footer Message:</td>
                        	<td>
                        		<input type="text" name="message" size="40" value="<? pv($_SESSION["footermessage"]) ?>">
                        	</td>
                        	
                        </tr>
                        <tr>
                        	<td colspan="2">	
                        		<span class="normal">
                        			This is the message that will appear at the footer of all pages. One thing to keep in mind with putting links in 
                        			here is to include a target="_blank" so that the link is opened up in a new window.
                        	
                        		</span>
                        	</td>
                        </tr>
                       <tr>
	                       	<td height="25" colspan="2">
	                       	</td>
                       </tr>
                       <tr>
                            <td colspan="2">
                            	<input type="button" name="submit" value="Update Footer Message" id="submitbutton">
                            
                            </td>
                        </tr>
                  </table>
             </td>
        </tr>
     </table>
  </td>
  </tr>

</table>
</form>


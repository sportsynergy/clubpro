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

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/login.php'
 }

</script>
 
<form name="entryform" method="post" action="<?=$ME?>">


<table cellpadding="20" > 
<tr valign="top">


<td>
        

        <table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">


         <tr>
             <td class="loginth">
             	<span class="whiteh1">
             		<div align="center"><? pv($DOC_TITLE) ?></div>
             	</span>
             </td>
          </tr>

          <tr>
          <td>

        <table>
   
        <tr>
                <td class=label>Email Address:</td>
                <td><input type="text" name="email" size=25 value="<? pv($frm["email"]) ?>"></td>
        </tr>
        
        <tr>
        
        	<td colspan="2">
        		 Enter in your email address to recover your password.  When you submit
        this request, your password will be reset, and a new password will be sent
        to you via email.
        	</td>
        </tr>
        
        <tr>
              
                <td colspan="2">
                <input type="button" name="submit" value="Send me a new password" id="submitbutton">
                        <input type="button" value="Go Back" id="cancelbutton">
                </td>
        </table>

        </td>
        </tr>
       </table>
      
</td>
</tr>
</table>


  </form>
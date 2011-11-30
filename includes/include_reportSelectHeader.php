
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
<table cellspacing="0" cellpadding="5" width="700" id="formtable">

       <tr>
           <td><font class="reportTitle">
           <?pv($reportName)?>
           </font>
           </td>
           <td><select name="report">
                <option value="">Select Report</option>
                <option value="memberactivity">Member Activity Report</option>
                <option value="courtutil">Court Utilization Report</option>
               </select>
          <input type="hidden" name="submitme" value="submitme">
          <input type="button" name="submit" value="Run Report" id="submitbutton">
          </td>

       </tr>
 </table>
</form>
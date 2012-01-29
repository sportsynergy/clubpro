
<div align="left">
  <form name="entryform" method="get" action="<?=$ME?>" autocomplete="off">
    <table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
      <tr class="borderow">
        <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
          <div align="center">
            <? pv($DOC_TITLE) ?>
          </div>
          </span></td>
      </tr>
      <tr>
        <td><table width="400" class="borderless">
            <tr>
              <td class="label">Member Name:</td>
              <td><input type="text" name="searchname" size="25" value="<? pv($searchname)?>">
                <?err($errors->searchname)?></td>
            </tr>
            <tr>
              <td colspan="2"><span class="normal"> Search for the first or last name of a member. *Note partial string are supported. <span style="font-style: italic;"> i.e. Smi for Smith or Pet for Peter</span> </span></td>
            </tr>
            <tr>
              <td></td>
              <td><input type="button" name="submit" value="Search" id="submitbutton"></td>
            </tr>
          </table></td>
      </tr>
    </table>
  </form>
  <div style="height: 30px"></div>
</div>
<script type="text/javascript">
document.entryform.searchname.focus();
YAHOO.example.init = function () {
    YAHOO.util.Event.onContentReady("formtable", function () {
        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked);   
    });

} ();
function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 

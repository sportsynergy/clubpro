

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

function onCancelButtonClicked(){

	submitForm('courtEventsForm');
 }

</script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="courtEventsForm" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php" method="post">
  <input type="hidden" name="preferenceType" value="court_events">
</form>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


 <div class="mb-3" style="width: 50%">
    <label for="name" class="form-label">Name:</label>
    <input class="form-control" name="name" id="name" type="text" aria-label="Name" value="<?=$courtEventName?>">
    <? is_object($errors) ? err($errors->name) : ""?>
  </div>

 <div class="mb-3" style="width: 50%">
      <label for="playerlimit" class="form-label">Player Limit:</label>
      <select name="playerlimit" id="playerlimit" class="form-select">
        <?
        for($i = 0; $i < 11; ++$i){ ?>
            <option value="<?=$i?>" <?=$i == $courtEvent['playerlimit']? "selected" : "" ?>><?=$i?></option>  
        <? } ?>         
        </select>
    
    </div>

    <div class="mb-3">
            <button type="submit" name="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()"><?=$DOC_TITLE?></button>
           	<input type="hidden" name="policyid" value="<?=$courtEvent['eventid']?>"/>
            <button type="button" name="back" class="btn btn-secondary" onclick="onCancelButtonClicked()">Back to Club Preferences</button>
           	<input type="hidden" name="submitme" value="submitme"/>
    </div>
         
</form>


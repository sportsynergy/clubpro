

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

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


 <div class="mb-3">
    <label for="username" class="form-label">Name:</label>
    <input class="form-control" id="username" type="text" aria-label="Name" value="<?=$courtEventName?>">
    <? is_object($errors) ? err($errors->name) : ""?>
  </div>

 <div class="mb-3">
      <label for="playerlimit" class="form-label">Player Limit:</label>
      <select name="playerlimit" id="playerlimit" class="form-select">
        <?
        for($i = 0; $i < 11; ++$i){ ?>
            <option value="<?=$i?>" <?=$i == $courtEvent['playerlimit']? "selected" : "" ?>><?=$i?></option>  
        <? } ?>         
        </select>
    
    </div>

    <div class="mb-3">
            <button type="submit"  name="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()"><?=$DOC_TITLE?></button>
           	<input type="hidden" name="policyid" value="<?=$courtEvent['eventid']?>"/>
           	<input type="hidden" name="submitme" value="submitme"/>
    </div>
         
</form>



<div class="mb-3">
	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#court_events" > << Back to Court Events </a> 
</div>
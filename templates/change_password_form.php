
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

  <div class="mb-3">
    <label for="oldpassword" class="form-label">Password</label>
    <input type="password" class="form-control" id="oldpassword">
    <? is_object($errors) ? err($errors->oldpassword) : ""?>
  </div>

  <div class="mb-3">
    <label for="onewpassword" class="form-label">Password</label>
    <input type="password" class="form-control" id="newpassword">
    <? is_object($errors) ? err($errors->newpassword) : ""?>
  </div>

   <div class="mb-3">
    <label for="onewpassword2" class="form-label">Confirm Password</label>
    <input type="password" class="form-control" id="newpassword2">
    <? is_object($errors) ? err($errors->newpassword2) : ""?>
  </div>

  <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Change Password</button>
      <input type="hidden" name="submitme" value="submitme">
  </form>
</div>


<script type="text/javascript">

function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script>
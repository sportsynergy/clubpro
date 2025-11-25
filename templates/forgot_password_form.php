<script type="text/javascript">




function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/login.php'
 }

</script>
 
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

  <div class="mb-3" style="width: 70%;">
    <label for="email" class="form-label">Email Address:</label>
    <input class="form-control" id="email" type="email"  aria-label="Email" value="<? pv($frm["email"]) ?>" name="email" >
    <div id="emailHelp" class="form-text">Enter in your email address to recover your password.  When you submit
        this request, your password will be reset, and a new password will be sent
        to you via email.</div>
    <? is_object($errors) ? err($errors->email) : ""?>
  </div>

  <div class="mt-5">
    <button type="submit" class="btn btn-primary" >Send me a new password</button>
    <button type="button" class="btn btn-secondary" id="cancelbutton" onclick="onCancelButtonClicked()">Go Back</button>
    <input type="hidden" value="submitme" name="submitme" />
  </div>
               

  </form>
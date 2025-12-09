
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>">


<div class="mb-3">
Are you sure you want to Delete this user? You know, if there is any chance of this player coming back a better idea is to disable them.
          
</div>

  <div class="my-5">
        <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Yes, I know. Delete this player</button>
        <button type="button" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
    </div>



<input type="hidden" name="searchname" value="<?=$searchname?>">
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="submitme" value="submitme">

</form>


<script type="text/javascript">

document.entryform.searchname.focus();


function onSubmitButtonClicked(){
  document.entryform.submit();
}

function onCancelButtonClicked(){
	submitFormWithAction('entryform','<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php');
}




</script>
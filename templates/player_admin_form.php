

<p class="bigbanner"><? pv($DOC_TITLE) ?></p>



  <form name="entryform" method="get" action="<?=$ME?>" autocomplete="off">

    <div class="mb-3" style="width: 50%">
      <input type="text" name="searchname" class="form-control" aria-label="Member name">
    </div>

    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Search</button>

  </form>

    
        
<script type="text/javascript">

document.entryform.searchname.focus();



function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script>
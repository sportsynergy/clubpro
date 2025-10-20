




<p class="bigbanner"><? pv($DOC_TITLE) ?></p>

<div class="mainpanel">
  
<form name="entryform">


 <div class="mb-3" style="width: 50%">
    <input type="text" name="searchname" class="form-control" aria-label="Member name">
  </div>
  
  <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Search</button>

</form>
</div>

<script type="text/javascript" >

function onSubmitButtonClicked(){
  console.log('submit');
	submitForm('entryform');
}
</script> 



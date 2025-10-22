




<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="mainpanel">
  
<form name="entryform">

 <div class="mb-3">
    <input type="text" name="searchname" class="form-control" aria-label="Member name">
  </div>
  
   <div class="mb-5">
      <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Search</button>
  </div>
</form>
</div>

<script type="text/javascript" >

function onSubmitButtonClicked(){
  console.log('submit');
	submitForm('entryform');
}
</script> 



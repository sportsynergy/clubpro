




<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="mainpanel">
  
<form name="entryform">

 <div class="mb-3" style="width: 50%">
    <input type="text" name="searchname" id="searchname" class="form-control" aria-label="Member name">
  </div>
  
   <div class="mb-5">
      <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Search</button>
  </div>
</form>
</div>

<script type="text/javascript" >

  document.getElementById('searchname').focus();
  document.getElementById('searchname').setAttribute("autocomplete", "off");

function onSubmitButtonClicked(){
  console.log('submit');
	submitForm('entryform');
}
</script> 






<div class="bd-example" style="margin-top: 50px; margin-bottom: 20px;">




<form name="entryform">


 <div class="mb-3" style="width: 50%">
    <input type="text" name="searchname" class="form-control" placeholder="First name" aria-label="Member name">
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



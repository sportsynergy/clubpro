

<script type="text/javascript">

function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){
	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/site_info.php?siteid=<?=$_SESSION["selected_site"]?>';
 }

</script>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

	<div class="mb-3">
      <label for="sitename" class="form-label">Name</label>
      <input class="form-control" id="courtname" type="text" aria-label="courtname" name="courtname" >  
  </div>

<div class="mb-3">
      <label for="gender" class="form-label">Court Type</label>
      <select class="form-select" aria-label="courttypeid" name="courttypeid" id="courttypeid">
        <?  while($courttype = mysqli_fetch_array($courttypes)){ ?>
				<option value="<?=$courttype['courttypeid']?>"> <?=$courttype['courttypename']?></option>
			<? } ?>        
        </select> 
</div>

<div class="mb-3">
      <label for="gender" class="form-label">Open Time</label>
      <select class="form-select" aria-label="opentime" name="opentime" id="opentime">
       <? for($i = 0; $i < 23; ++$i){ ?>
            <option value="<?=$i?>:00:00"><?=$i?>:00:00</option>
         <? } ?>
        </select>
</div>

<div class="mb-3">
      <label for="gender" class="form-label">Close Time</label>
      <select class="form-select" aria-label="closetime" name="closetime" id="closetime">
      <?
			for($i = 0; $i < 23; ++$i){ ?>
				<option value="<?=$i?>:00:00"><?=$i?>:00:00</option>

			<? } ?>
        </select>
</div>

   <div class="mt-5">
    <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()" id="submitbutton"><?=$DOC_TITLE?></button>
	<button type="button" class="btn btn-secondary" id="cancelbutton" onclick="onCancelButtonClicked()">Cancel</button>
	<input type="hidden" name="siteid" value="<?=$_SESSION["selected_site"]?>"/>
  </div>      
       
			
</form>

<script language="Javascript">

// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
 if (document.getElementById) {
  for (var sch = 0; sch < dform.length; sch++) {
   if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
  }
 }
return true;
}

function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>">


<div class="mb-3">
      <label for="courttypeid" class="form-label">Sport Type</label>
      <select name="courttypeid" class="form-select">
          <?  while ( $sportarray = db_fetch_array($availbleSports)){ ?>
             	<option value="<? pv($sportarray['courttypeid'] ) ?>"><? pv($sportarray['courttypename'] ) ?></option>
         <?} ?>
</select>
</div>

<div class="mb-3">
      <label for="displayoption" class="form-label">Display</label>
      <select name="displayoption" class="form-select" id="displayoption" onchange="disableSortByDropDown(this)">
         <option value="all">All Players</option>
         <option value="5+">5.0 and up</option>
         <option value="4">4.0</option>
         <option value="3">3.0</option>
         <option value="2">2.0</option>
         <option value="2-">2.0 and below</option>
      </select>
</div>

<div class="mb-3">
   <label for="sortoption" class="form-label">Sort By</label>
       <select name="sortoption" id="sortoption" class="form-select">
            <option value="rank" >Rank</option>
            <option value="fname">First Name</option>
            <option value="lname">Last Name</option> 
      </select>
</div>


<div class="mt-5">
   <button type="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()">Submit</button>
   <input type="hidden" name="submitme" value="submitme">
</div>
  

</form>


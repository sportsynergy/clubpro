<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


  <form name="entryform" method="post" action="<?=$ME?>">
    
   <div class="mb-3">
    <label for="username" class="form-label">Username:</label>
    <input class="form-control" id="username" type="text" aria-label="Username" value="<? pv($frm["username"]) ?>" disabled>
  </div>

  <div class="mb-3">
    <label for="sportsynergyid" class="form-label">Sportsynergy Id:</label>
    <input class="form-control" id="sportsynergyid" type="text" aria-label="Username" value="<? pv($frm["userid"]) ?>" disabled>
  </div>

  <div class="mb-3">
    <label for="firstname" class="form-label">First Name:</label>
    <input class="form-control" id="firstname" type="text" aria-label="Firstname" value="<? pv($frm["firstname"]) ?>">
    <? is_object($errors) ? err($errors->firstname) : ""?>
  </div>

  <div class="mb-3">
    <label for="lastname" class="form-label">Last Name:</label>
    <input class="form-control" id="lastname" type="text" aria-label="Lastname" value="<? pv($frm["lastname"]) ?>">
    <? is_object($errors) ? err($errors->lastname) : ""?>
  </div>
  
  <div class="mb-3">
    <label for="email" class="form-label">Email:</label>
    <input class="form-control" id="email" type="email"  aria-label="Email" value="<? pv($frm["email"]) ?>">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
    <? is_object($errors) ? err($errors->email) : ""?>
  </div>

  <div class="mb-3">
    <label for="homephone" class="form-label">Home Phone:</label>
    <input class="form-control" id="homephone" type="text" aria-label="Home Phone" value="<? pv($frm["homephone"]) ?>">
    <? is_object($errors) ? err($errors->homephone) : ""?>
  </div>
  
  <div class="mb-3">
    <label for="workphone" class="form-label">Work Phone:</label>
    <input class="form-control" id="workphone" type="text"  aria-label="Work Phone" value="<? pv($frm["workphone"]) ?>">
    <? is_object($errors) ? err($errors->workphone) : ""?>
  </div>
          
   <div class="mb-3">
    <label for="mobilephone" class="form-label">Mobile Phone:</label>
    <input class="form-control" id="mobilephone" type="text"  aria-label="Mobile Phone" value="<? pv($frm["mobilephone"]) ?>">
    <? is_object($errors) ? err($errors->mobilephone) : ""?>
  </div>       
       
  <div class="mb-3">
    <label for="address" class="form-label">Address:</label>
    <textarea class="form-control" id="address" rows="3"><? pv($frm["address"]) ?></textarea>
     <? is_object($errors) ? err($errors->useraddress) : ""?>
  </div>
  
  <div class="mb-3">
      <label for="gender" class="form-label">Receive Email Notifications:</label>
      <select name="recemail" class="form-select" aria-label="Receive Email Notifications">
          <?
              if  ($frm["recemail"]=='y'){

              echo "<option value=\"y\">Yes</option>";
              echo "<option value=\"n\">No</option>";

              }
              else {
              echo "<option value=\"n\">No</option>";
              echo "<option value=\"y\">Yes</option>";
              }
              echo "</select>";
              ?>
      </select>
    </div>

  <?  if ( isJumpLadderRankingScheme() ){  
    
      if($frm['recleaguematchnotifications']=='y'){
        $leaguematchselected = "selected"; 
      } 

    }
    ?>
   
    <div class="mb-3">
      <label for="recleaguematchnotifications" class="form-label">Receive League Reminder Notifications:</label>
      <select name="recleaguematchnotifications" id="recleaguematchnotifications" class="form-select" aria-label="Receive League Match Notifications">
         <option value="n">No</option>
          <option value="y" <?=$leaguematchselected?> >Yes</option>
      </select>
    </div>


   
    <? 

    if($frm['available_at_5']==true){
        $selected5 = "checked=checked"; 
    }
    if($frm['available_at_6']==true){
        $selected6 = "checked=checked"; 
    }
    if($frm['available_at_7']==true){
        $selected7 = "checked=checked"; 
    }
    
    ?>

    <div class="form-check">
      Match Availability
	  <input class="form-check-input" type="checkbox" name="available_5pm" id="available_5pm"/>
	  <label for="available_5pm" class="form-label">5pm</label>
    <input class="form-check-input" type="checkbox" name="available_6pm" id="available_6pm" />
	  <label for="available_6pm" class="form-label">6pm</label>
    <input class="form-check-input" type="checkbox" name="available_6pm" id="available_7pm" />
	  <label for="available_7pm" class="form-label">7pm</label>
	</div>


  <div class="mt-5">
    <button type="submit" name="submit" class="btn btn-primary" id="submitbutton"onclick="onSubmitButtonClicked()">Update Settings</button>
  </div>
      
  <input type="hidden" name="userid" value="<?pv($userid) ?>">
  <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
  <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
  <input type="hidden" name="username" value="<? pv($frm["username"]) ?>">
  <input type="hidden" name="submitme" value="submitme">

</form>


  


<script type="text/javascript">

function onSubmitButtonClicked(){

	document.entryform.submit();
}

</script>
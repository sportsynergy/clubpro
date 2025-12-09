 <div class="mb-3">
      <label for="username" class="form-label">User Name</label>
      <input class="form-control-plaintext" id="username" type="text" aria-label="Username" value="<?=$frm["username"] ?>" readonly>  
  </div>

   <div class="mb-3">
      <label for="firstname" class="form-label">First Name</label>
      <input class="form-control-plaintext" id="firstname" type="text" aria-label="First Name" value="<?=$frm["firstname"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="lastname" class="form-label">Last Name</label>
      <input class="form-control-plaintext" id="lastname" type="text" aria-label="Last Name" value="<?=$frm["lastname"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="lastname" class="form-label">Last Name</label>
      <input class="form-control-plaintext" id="lastname" type="text" aria-label="Last Name" value="<?=$frm["lastname"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input class="form-control-plaintext" id="lastname" type="text" aria-label="Email" value="<?=$frm["email"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="email" class="form-label">Home Phone</label>
      <input class="form-control-plaintext" id="homephone" type="text" aria-label="Home Phone" value="<?=$frm["homephone"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="workphone" class="form-label">Work Phone</label>
      <input class="form-control-plaintext" id="workphone" type="text" aria-label="Work Phone" value="<?=$frm["workphone"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="mobile" class="form-label">Mobile Phone</label>
      <input class="form-control-plaintext" id="mobilephone" type="text" aria-label="Mobile Phone" value="<?=$frm["mobilephone"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <label for="msince" class="form-label">Member Since</label>
      <input class="form-control-plaintext" id="msince" type="text" aria-label="Member Since" value="<?=$frm["msince"] ?>" readonly>  
  </div>

  <div class="mb-3">
      <textarea class="form-control" id="address" rows="3" ><? pv($frm["useraddress"]) ?></textarea>
  </div>
  
  <div class="mb-3">
     <label for="rankings" class="form-label">Rankings</label>

      <?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
        <div class=" mb-2">
          <span>
          <?=$registeredArray['courttypename']?>: <?=$registeredArray['ranking']?>   
      </span>
          
        </div>
      <?  } ?>
  </div>

      

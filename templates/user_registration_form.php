<p class="h3"><? pv($DOC_TITLE) ?></p>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


 <div class="mb-3">
    <label for="username" class="form-label">Username:</label>
    <input class="form-control" id="username" type="text" placeholder="Default input" aria-label="Username">
    <? is_object($errors) ? err($errors->username) : ""?>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password">
    <? is_object($errors) ? err($errors->password) : ""?>
  </div>

<div class="mb-3">
    <label for="firstname" class="form-label">First Name:</label>
    <input class="form-control" id="firstname" type="text" placeholder="Default input" aria-label="Firstname">
    <? is_object($errors) ? err($errors->username) : ""?>
  </div>

  <div class="mb-3">
    <label for="lastname" class="form-label">Last Name:</label>
    <input class="form-control" id="lastname" type="text" placeholder="Default input" aria-label="Lastname">
    <? is_object($errors) ? err($errors->username) : ""?>
  </div>

   <div class="mb-3">
    <label for="homephone" class="form-label">Home Phone:</label>
    <input class="form-control" id="homephone" type="text" placeholder="Default input" aria-label="Home Phone">
    <? is_object($errors) ? err($errors->homephone) : ""?>
  </div>

  <div class="mb-3">
    <label for="workphone" class="form-label">Work Phone:</label>
    <input class="form-control" id="workphone" type="text" placeholder="Default input" aria-label="Work Phone">
    <? is_object($errors) ? err($errors->workphone) : ""?>
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Email:</label>
    <input class="form-control" id="workphone" type="email" placeholder="Default input" aria-label="Email">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
    <? is_object($errors) ? err($errors->workphone) : ""?>
  </div>




  <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Register</button>

  </form>
  <div style="height: 30px">
  <span class="warning">* </span> <span class="normal">indicates a required field</span> </div>
</div>


<script type="text/javascript">


function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 

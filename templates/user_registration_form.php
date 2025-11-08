<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<div class="mainpanel">

 <div class="mb-3">
    <label for="username" class="form-label">Username:</label>
    <input class="form-control" id="username" type="text" aria-label="Username">
    <? is_object($errors) ? err($errors->username) : ""?>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password">
    <? is_object($errors) ? err($errors->password) : ""?>
  </div>

<div class="mb-3">
    <label for="firstname" class="form-label">First Name:</label>
    <input class="form-control" id="firstname" type="text" aria-label="Firstname">
    <? is_object($errors) ? err($errors->firstname) : ""?>
  </div>

  <div class="mb-3">
    <label for="lastname" class="form-label">Last Name:</label>
    <input class="form-control" id="lastname" type="text" aria-label="Lastname">
    <? is_object($errors) ? err($errors->lastname) : ""?>
  </div>

   <div class="mb-3">
    <label for="homephone" class="form-label">Home Phone:</label>
    <input class="form-control" id="homephone" type="text" aria-label="Home Phone">
    <? is_object($errors) ? err($errors->homephone) : ""?>
  </div>

  <div class="mb-3">
    <label for="workphone" class="form-label">Work Phone:</label>
    <input class="form-control" id="workphone" type="text"  aria-label="Work Phone">
    <? is_object($errors) ? err($errors->workphone) : ""?>
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">Email:</label>
    <input class="form-control" id="email" type="email"  aria-label="Email">
    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
    <? is_object($errors) ? err($errors->email) : ""?>
  </div>

  <div class="mb-3">
    <label for="address" class="form-label">Address:</label>
    <textarea class="form-control" id="address" rows="3"></textarea>
     <? is_object($errors) ? err($errors->useraddress) : ""?>
  </div>

   <div class="mb-3">
    <label for="msince" class="form-label">Date Joined:</label>
    <input class="form-control" id="msince" type="text" aria-label="Work Phone" value="<?=$frm["msince"]?>">
    <div id="msincelHelp" class="form-text"> e.g. January 2, 1988</div>
    <? is_object($errors) ? err($errors->msince) : ""?>
  </div>

    <div class="mb-3">
      <label for="gender" class="form-label">Gender:</label>
      <select class="form-select" aria-label="Gender" name="gender" id="gender">
        <option value="0">Female</option>
        <option value="1">Male</option>
        <option value="2">Other</option>
      </select>
      <? is_object($errors) ? err($errors->gender) : ""?>  
    </div>

    <? if(isSiteAutoLogin()){ ?>

      <div class="mb-3">
        <label for="memberid" class="form-label">Membership ID:</label>
        <input type="text" class="form-control" id="memberid" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>">
          <? is_object($errors) ? err($errors->memberid) : ""?>
      </div>

      <? } ?>

    <?
      // Display the custom parameters
			while( $parameterArray = db_fetch_array($extraParametersResult)){  ?>
        <div class="mb-3">
				<? if($parameterArray['parametertypename'] == "text"){ ?>
            <label for="<?="parameter-".$parameterArray['parameterid']?>" class="form-label"><? pv($parameterArray['parameterlabel'])?></label>
            <input type="text" class="form-select" name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="<? pv($frm[$parameterArray['parameterid']]) ?>" maxlength="40">     
            <? }
            
            elseif($parameterArray['parametertypename'] == "select") { ?>
            
              <label for="<?="parameter-".$parameterArray['parameterid']?>" class="form-label"><? pv($parameterArray['parameterlabel'])?></label>
              <select class="form-select" name="<?="parameter-".$parameterArray['parameterid']?>">
                <?
              // Get Parameter Options
              $parameterOptionResult = load_parameter_options($parameterArray['parameterid']);
              
              while($parameterOptionArray = db_fetch_array($parameterOptionResult)){ ?>
                <option value="<? pv($parameterOptionArray['optionvalue']) ?>">
                <? pv($parameterOptionArray['optionname']) ?>
                </option>
                <? } ?>
              </select>
            <? }  ?>
             </div>
            <? }  ?>
         

          <div class="mb-3">
             <?
			//Get Available courttypes
			while($availbleSportsArray = db_fetch_array($availbleSportsResult)){ ?>
            <label  class="form-label"><? echo "$availbleSportsArray[courttypename] Ranking" ?></label>
              <input type="text" class="form-control" name="<? echo "courttype$availbleSportsArray[courttypeid]"?>"
					size="15" value="3">
            
            <? } ?>
          </div>

      <div class="mb-3">

      <label  class="form-label">Authorized Sites:</label>
				<? while($availableSitesArray = mysqli_fetch_array($availableSitesResult)){
					$checked = "checked";

					if(!amiValidForSite($availableSitesArray['siteid'])){
						$disabled = "disabled";
						$checked = "";
					}
					else{
						$disabled = "";

					}

					print "<input class=\"form-check-input\" type=\"checkbox\" name=\"clubsite$availableSitesArray[siteid]\" value=\"$availableSitesArray[siteid]\" $checked $disabled> $availableSitesArray[sitename] <br>\n";

					unset($disabled);
					unset($checked);
				}

				//Done with Authorized Sites
				?>
      </div>


      <div class="mb-3">
      <label for="usertype" class="form-label">User Type:</label>
      <select class="form-select" name="usertype">
            <option value="1">Player</option>
				    <option value="6">Junior</option>
            <option value="4">Desk User</option>
            <option value="2">Club Admin</option>
      </select>
        <div id="usertypeHelp" class="form-text"> A Desk User can book courts and see player details. A Club Admin can also add/edit players and change site settings.</div>
      </div>

  <div class="mt-5">
  <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Add New User</button>
      </div>
  </form>
  

  </div>

<script type="text/javascript">


function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 

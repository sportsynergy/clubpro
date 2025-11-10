
<script type="text/javascript">


function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){
	 submitForm('backtolistform');
 }

</script>

<form name="photoform" action="<?=$ME?>" method="post" enctype="multipart/form-data">
    <input type="file" name="image">
    <input type="hidden" name="formname" value="photoform">
    <input type="hidden" name="userid" value="<?pv($userid) ?>">
    <input type="submit" value="Upload photo" id="submitbutton1" >
</form>

<form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
    <input type="hidden" name="searchname" value="<? pv($searchname)?>">
</form>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="container">
    <div class="row">
        <div class="col">

         <form name="entryform" method="post" action="<?=$ME?>">

         <div class="mb-3">
            <label for="username" class="form-label">Sportsynergy Id</label>
            <input class="form-control" id="username" type="text" aria-label="Username" value="<?=$frm["userid"] ?>" readonly>
        </div>

        <?if(!isSiteAutoLogin()){ ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input class="form-control" name="username" size="35" id="username" type="text" aria-label="Username" value="<? pv($frm["username"]) ?>">
                <? is_object($errors) ? err($errors->username) : ""?>
            </div>

             <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password">
                <div id="passwordHelp" class="form-text">By leaving this field blank, the password will not be updated.</div>
                <? is_object($errors) ? err($errors->password) : ""?>
            </div>  
         <? } ?> <!-- End of isSiteAutoLogin() check -->

         <div class="mb-3">
            <label for="firstname" class="form-label">First Name:</label>
            <input class="form-control" id="firstname" type="text" aria-label="Firstname" value="<? pv($frm["firstname"]) ?>">
            <? is_object($errors) ? err($errors->firstname) : ""?>
        </div>

        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name:</label>
            <input class="form-control" id="lastname" type="text" aria-label="Last Name" value="<? pv($frm["lastname"]) ?>">
            <? is_object($errors) ? err($errors->lastname) : ""?>
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
            <label for="email" class="form-label">Email:</label>
            <input class="form-control" id="email" type="email"  aria-label="Email" value="<? pv($frm["email"]) ?>">
            <? is_object($errors) ? err($errors->email) : ""?>
        </div>

         <div class="mb-3">
            <label for="msince" class="form-label">Date Joined:</label>
            <input class="form-control" name="msince" id="msince"  size="35" value="<? pv($frm["msince"]) ?>" type="text"  aria-label="Member Since">
            <? is_object($errors) ? err($errors->mobilephone) : ""?>
        </div>

        <div class="mb-3">
            <label for="useraddress" class="form-label">Address:</label>
            <textarea name="useraddress" class="form-control" id="useraddress" rows="3" cols="50" rows="5"><? pv($frm["useraddress"]) ?></textarea>
            <? is_object($errors) ? err($errors->useraddress) : ""?>
        </div>

         <div class="mb-3">
            <label for="memberid" class="form-label"> Membership ID:</label>
            <input class="form-control" name="memberid" id="memberid"  size="35" value="<? pv($frm["memberid"]) ?>" type="text"  aria-label="Member Id">
            <? is_object($errors) ? err($errors->memberid) : ""?>
        </div>

          <div class="mb-3">
            <label for="gender" class="form-label">Gender:</label>
            <select class="form-select" aria-label="Gender" name="gender" id="gender">
                <option value="0">Female</option>
                <option value="1"  <? if($frm["gender"]==1) print "selected" ?>>Male</option>
                <option value="2"  <? if($frm["gender"]==2) print "selected" ?>>Other</option>
            </select>
            <? is_object($errors) ? err($errors->gender) : ""?>  
            </div>

             <?
		// Get the Custom Parameters
		while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );
			if($parameterArray['parametertypename'] == "text"){ ?>
				<div class="mb-3">
                <label for="<?="parameter-".$parameterArray['parameterid']?>" class="form-label"> <? pv($parameterArray['parameterlabel'])?></label>
                <input class="form-control" type="text" id="<?="parameter-".$parameterArray['parameterid']?>"  name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="<? pv($parameterValue) ?>" maxlength="40"> 
				</div>	
				<? } elseif($parameterArray['parametertypename'] == "select") { ?>
                    <div class="mb-3">
                    <label for="<?="parameter-".$parameterArray['parameterid']?>" class="form-label"> <? pv($parameterArray['parameterlabel'])?></label>
				    <select name="<?="parameter-".$parameterArray['parameterid']?>" class="form-select" aria-label="<?="parameter-".$parameterArray['parameterlabel']?>">
                        <option value=""></option>
                            <?
                            // Get Parameter Options
                            $parameterOptionResult = load_parameter_options($parameterArray['parameterid']);
                            
                            while($parameterOptionArray = db_fetch_array($parameterOptionResult)){
                                
                                if($parameterValue == $parameterOptionArray['optionvalue']){
                                    $selected = "selected=selected";	
                                }else{
                                    $selected = "";
                                }
                                ?>
                                <option value="<? pv($parameterOptionArray['optionvalue']) ?>" <? pv($selected) ?>><? pv($parameterOptionArray['optionname']) ?></option>
                            <? } ?>
							</select>
						</div>
				
                    <? } ?>
			<? } ?>

             <div class="mb-3">
            <label for="recemail" class="form-label">Receive Email Notifications:</label>
            <select name="recemail" id="recemail" class="form-select" aria-label="Receive Email Notifications"> 
                 <option value="y">Yes</option>
                <option value="n" <? if($frm["recemail"]=='n') print "selected" ?> >No</option>
            </select>
            <? is_object($errors) ? err($errors->recemail) : ""?>  
            </div>

            <?  if ( isJumpLadderRankingScheme() ){  
               
               if($frm['recleaguematchnotifications']=='y'){
                $leaguematchselected = "selected"; 

               } ?>
             <label for="recleaguematchnotifications" class="form-label">Receive League Reminder Notifications:</label>  
             <select name="recleaguematchnotifications" id="recleaguematchnotifications" class="form-select" aria-label="Receive League Reminder Notifications">
                <option value="n">No</option>
                <option value="y" <?=$leaguematchselected?> >Yes</option>
            </select>

        <? } ?>

        <div class="mb-3">

            <label for="Role" class="form-label">Role:</label>
            <select name="roleid" id="roleid" class="form-select" aria-label="Role"> 
                <option value="1" <? if($frm["roleid"]==1) print "selected" ?> >Player</option>
                <option value="6" <? if($frm["roleid"]==6) print "selected" ?>>Junior</option>
                <option value="4" <? if($frm["roleid"]==4) print "selected" ?>>Desk User</option>
                <option value="2" <? if($frm["roleid"]==2) print "selected" ?> >Club Admin</option>
            </select>
        </div>

        <div class="mb-3">
               <label  class="form-label">Authorized Sites:</label>
               <? //Select only the authorized sites

             $autSitesStack = array();
            
             for($i=0; $i<mysqli_num_rows($authSites); ++$i){
                
                $authSitesArray = mysqli_fetch_array($authSites);
                array_push($autSitesStack, $authSitesArray['siteid']);

                if($i==0){
                    $mysites =  $authSitesArray['siteid'];
                }
                else{
                    $mysites .=  ",$authSitesArray[siteid]";
                }
             }


            for($j=0; $j<mysqli_num_rows($availableSites); ++$j){
            
                $row = mysqli_fetch_array($availableSites);

                if(in_array($row['siteid'],$autSitesStack)){
                    $selected = "checked";
                    
                }
                else{
                    $selected = "";
                } ?>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="clubsite" name="clubsite<? pv($row['siteid'])?>" value="<? pv($row['siteid']) ?>" <?pv($selected)?>   />
                <label  class="form-check-label" for="clubsite<? pv($row['siteid'])?>"> 
                    <? pv($row['sitename']) ?> 
                </label>
            </div>
            <? unset($selected); ?>
            <?  } ?>
            </div> <!-- .mb-3 -->

            <div class="mb-3">
                <label for="enable" class="form-label">Enable</label>
                 <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="enable" value="y" name="enable" id="enable" checked>
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" name="submit" id="submitbutton" class="btn btn-primary" onclick="onSubmitButtonClicked()">Update Settings</button>
                <button type="button" name="cancel" id="cancelbutton" class="btn btn-secondary" onclick="onCancelButtonClicked()">Cancel</button>
                <input type="hidden" name="userid" value="<?pv($userid) ?>">
                <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
                <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
                <input type="hidden" name="searchname" value="<? pv($searchname)?>">
                <input type="hidden" name="formname" value="entryform">
            </div>
       </form>

        </div> <!-- .col -->    
        <div class="col">
         <?  if( isset($frm["photo"]) ){ ?>
            <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($frm["photo"]); ?>" width="180" height="180">
        <?   } else{  ?>
        <img src="<?=get_gravatar($frm["email"],180 )?>" />
        <?   }   ?>

        </div> <!-- .col -->
</div> <!-- .row -->
</div> <!-- .container -->  

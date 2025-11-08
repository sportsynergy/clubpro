
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



<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <form name="photoform" action="<?=$ME?>" method="post" enctype="multipart/form-data">
            <input type="file" name="image">
            <input type="hidden" name="formname" value="photoform">
            <input type="hidden" name="userid" value="<?pv($userid) ?>">
            <input type="submit" value="Upload photo" id="submitbutton1" >
        </form>

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
            <label for="mobilephone" class="form-label">Mobile Phone:</label>
            <input class="form-control" id="mobilephone" type="text"  aria-label="Mobile Phone">
            <? is_object($errors) ? err($errors->mobilephone) : ""?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input class="form-control" id="email" type="email"  aria-label="Email">
            <? is_object($errors) ? err($errors->email) : ""?>
        </div>

         <div class="mb-3">
            <label for="msince" class="form-label">Date Joined:</label>
            <input class="form-control" name="msince" id="msince"  size="35" value="<? pv($frm["msince"]) ?>" type="text"  aria-label="Member Since">
            <? is_object($errors) ? err($errors->mobilephone) : ""?>
        </div>


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

 


        <tr>
            <td class=label>Address:</td>
            <td colspan="2"><textarea name="useraddress" cols="50" rows="5"><? pv($frm["useraddress"]) ?></textarea>
                <? is_object($errors) ? err($errors->address) : ""?>
            </td>
        </tr>
        <tr>
            <td class=label>
            <?if(isSiteAutoLogin()){ ?>
            	<font color="Red" class=normalsm>* 
             <? } ?>	
            Membership ID:</td>
            <td><input type="text" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>">
                <? is_object($errors) ? err($errors->memberid) : ""?>
            </td>
        </tr>
        <tr>
            <td class=label>Gender:</td>
            <td><select name="gender">
            	<option value="1">Male</option>
            	<option value="0" <? if($frm["gender"]==0) print "selected" ?> >Female</option>
            </select>
                <? is_object($errors) ? err($errors->gender) : ""?>
            </td>
        </tr>

       
        
        <?
		// Get the Custom Parameters
		while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );
			
			if($parameterArray['parametertypename'] == "text"){ ?>
				<tr>
					<td class="label"><? pv($parameterArray['parameterlabel'])?>:</td>
					<td>
						<input type="text" name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="<? pv($parameterValue) ?>" maxlength="40"> 
					</td>
				</tr>
					
					
				<? } elseif($parameterArray['parametertypename'] == "select") { ?>
					
					<tr>
						<td class="label"><? pv($parameterArray['parameterlabel'])?>:</td>
						<td>
							<select name="<?="parameter-".$parameterArray['parameterid']?>" >
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
						</td>
					</tr>
				
                    <? } elseif($parameterArray['parametertypename'] == "date") { ?>
                  <td class="label"><? pv($parameterArray['parameterlabel'])?>:
                  <td>
                   <span style="padding-left: 10px"> <img src="<?=$_SESSION["CFG"]["imagedir"]?>/cal.png"  id="show" title="Click here to change the date"> </span >
                   
                   <input type="text" id="date-param" name="<?="parameter-".$parameterArray['parameterid']?>" size="30" value="<? pv($parameterValue) ?>"  readonly>
                  </td>
            <? } ?>

			<? } ?>
				
			
        
        
       <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <tr>
            <td class="label">Receive Email Notifications:</td>
            <td><select name="recemail"> 
                 <option value="y">Yes</option>
                <option value="n" <? if($frm["recemail"]=='n') print "selected" ?> >No</option>
                <? is_object($errors) ? err($errors->recemail) : ""?>
                
                </td>
        </tr>
        <?  if ( isJumpLadderRankingScheme() ){  
              
              if($frm['recleaguematchnotifications']=='y'){
                $leaguematchselected = "selected"; 
              } 
            
            ?>
        <tr>
              <td class="label medwidth">Receive League Reminder Notifications:</td> 
              <td>
                  <select name="recleaguematchnotifications">
                      <option value="n">No</option>
                      <option value="y" <?=$leaguematchselected?> >Yes</option>
                </select>
              </td>
        </tr>
        <? } ?>
		<tr>
            <td class="label">Role:</td>
            <td>
            		<select name="roleid"> 
                        <option value="1" <? if($frm["roleid"]==1) print "selected" ?> >Player</option>
                        <option value="6" <? if($frm["roleid"]==6) print "selected" ?>>Junior</option>
                        <option value="4" <? if($frm["roleid"]==4) print "selected" ?>>Desk User</option>
                        <option value="2" <? if($frm["roleid"]==2) print "selected" ?> >Club Admin</option>
                      </select>
                </td>
        </tr>
        <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
          <tr>
            <td class=label valign="top">Authorized Sites:</td>
            <td class="normal">

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
                        }

                        print "<input type=\"checkbox\" name=\"clubsite$row[siteid]\" value=\"$row[siteid]\" $selected> $row[sitename] <br>\n";

                     unset($selected);
                 }

              //Done with Authorized Sites
             ?>

            </td>
          </tr>
        <tr>
            <td colspan="2" height="20"></td>
        </tr>

        <?



        for ( $i=0; $i<mysqli_num_rows($availbleSports); ++$i){

        	$availbleSportsArray = db_fetch_array($availbleSports);

                  //Match up the users ranking with the availbe court types
                 for ($j=0; $j<mysqli_num_rows($registeredSports); ++$j){

                   $registeredArray = db_fetch_array($registeredSports);
                       //Put the results in a nice little string to be passed up in the post vars.

                       if($j==0){
                           $mycourtTypes =  $registeredArray['courttypeid'];
                       }
                       else{
                           $mycourtTypes .=  ",$registeredArray[courttypeid]";
                       }

					   if($availbleSportsArray['courttypeid'] == $registeredArray['courttypeid']){
					   	 $ranking = $registeredArray['ranking'];
					   }
                         
                 }


               if(mysqli_num_rows($registeredSports)>0){
                    mysqli_data_seek($registeredSports,0);
               }

         ?>
         <tr valign="top">
                <td class=label><? echo "$availbleSportsArray[courttypename] Ranking" ?>:</td>
                <td><input type="text" name="<? echo "courttype$availbleSportsArray[courttypeid]" ?>" size=25 value="<?=$ranking ?>">

                </td>
        </tr>
         <?
           unset($ranking);
           //While closing bracket - DO NOT remove
           }
         ?>
       <tr>
            <td colspan="2" height="20"></td>
        </tr>
          <tr>
            <td class=label>Enable:</td>

            <?

            if  ( $frm["enable"]=='y' ){
            echo "<td><input type=\"checkbox\" name=\"enable\" value=\"y\" checked></td>";
            }
            else{
            echo "<td><input type=\"checkbox\" name=\"enable\" value=\"1\" ></td>";
            }
            ?>
                <? is_object($errors) ? err($errors->enable) : ""?>
        </tr>

        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td colspan="2">
            <input type="button" name="submit" value="Update Settings" id="submitbutton">
            <input type="button" value="Cancel" id="cancelbutton" >
            <input type="hidden" name="userid" value="<?pv($userid) ?>">
            <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
            <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
            <input type="hidden" name="searchname" value="<? pv($searchname)?>">
            <input type="hidden" name="formname" value="entryform">
            </td>
        </tr>


        </table>
        
        
            
       </form>

    </td>
</tr>

</table>

<div style="margin-top: 20px">
	<span class="normal warning">* </span>
	<span class="normal">indicates a required field</span>
</div>

            	

 <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
    <input type="hidden" name="searchname" value="<? pv($searchname)?>">
</form>
         
         



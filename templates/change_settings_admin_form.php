
<script type="text/javascript">

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbuttonvalue" });   
        oCancelButton.on("click", onCancelButtonClicked);
    });

} ();


YAHOO.util.Event.onDOMReady(function(){

        var Event = YAHOO.util.Event,
            Dom = YAHOO.util.Dom,
            dialog,
            calendar;

        var showBtn = Dom.get("show");

        Event.on(showBtn, "click", function() {

            // Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
            if (!dialog) {

                // Hide Calendar if we click anywhere in the document other than the calendar
                Event.on(document, "click", function(e) {
                    var el = Event.getTarget(e);
                    var dialogEl = dialog.element;
                    if (el != dialogEl && !Dom.isAncestor(dialogEl, el) && el != showBtn && !Dom.isAncestor(showBtn, el)) {
                        dialog.hide();
                    }
                });

                function resetHandler() {
                    // Reset the current calendar page to the select date, or 
                    // to today if nothing is selected.
                    var selDates = calendar.getSelectedDates();
                    var resetDate;
        
                    if (selDates.length > 0) {
                        resetDate = selDates[0];
                    } else {
                        resetDate = calendar.today;
                    }
        
                    calendar.cfg.setProperty("pagedate", resetDate);
                    calendar.render();
                }
        
                function closeHandler() {
                    dialog.hide();
                }

                dialog = new YAHOO.widget.Dialog("container", {
                    visible:false,
                    context:["show", "tl", "bl"],
                    draggable:false,
                    close:false
                });
                
                dialog.setBody('<div id="cal"></div>');
                dialog.render(document.body);

                dialog.showEvent.subscribe(function() {
                    if (YAHOO.env.ua.ie) {
                        // Since we're hiding the table using yui-overlay-hidden, we 
                        // want to let the dialog know that the content size has changed, when
                        // shown
                        dialog.fireEvent("changeContent");
                    }
                });
            }

            // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
            if (!calendar) {

                calendar = new YAHOO.widget.Calendar("cal", {
        
                    iframe:false,          // Turn iframe off, since container has iframe support.
                    hide_blank_weeks:true  // Enable, to demonstrate how we handle changing height, using changeContent
                    
                });
                calendar.render();

                calendar.selectEvent.subscribe(function() {
                    if (calendar.getSelectedDates().length > 0) {

                        var selDate = calendar.getSelectedDates()[0];

                        // Pretty Date Output, using Calendar's Locale values: Friday, 8 February 2008
                        var wStr = calendar.cfg.getProperty("WEEKDAYS_LONG")[selDate.getDay()];
                        var dStr = selDate.getDate();
                        var monStr = selDate.getMonth() + 1;
                        var mStr = calendar.cfg.getProperty("MONTHS_LONG")[selDate.getMonth()];
                        var yStr = selDate.getFullYear();

                        document.getElementById("date-param").value = monStr + "/" + dStr + "/" + yStr;
                        
                       
                    } else {
                        //Dom.get("date").value = "";
                    }
                    dialog.hide();
                });

                calendar.renderEvent.subscribe(function() {
                    // Tell Dialog it's contents have changed, which allows 
                    // container to redraw the underlay (for IE6/Safari2)
                    dialog.fireEvent("changeContent");
                });
            }

            var seldate = calendar.getSelectedDates();

            if (seldate.length > 0) {
                // Set the pagedate to show the selected date if it exists
                calendar.cfg.setProperty("pagedate", seldate[0]);
                calendar.render();
            }

            dialog.show();
        });
    });

function onSubmitButtonClicked(){
	submitForm('entryform');
}

 function onCancelButtonClicked(){
	 submitForm('backtolistform');
 }

</script>



<table width="650" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
    <tr>
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

       <form name="entryform" method="post" action="<?=$ME?>">
       <table cellspacing="5" cellpadding="1" width="650" >
       
       <tr>
			<td class="label medwidth">Sportsynergy Id:</td>
			<td class="normal"><? pv($frm["userid"]) ?></td>
			 <td rowspan="9" valign="top" >
					<div align="center">
						<img src="<?=get_gravatar($frm["email"],120 )?>" />
					</div>
	
				</td>
		</tr>
		
		
  <?if(!isSiteAutoLogin()){ ?>

        <tr>
 			<td class=label><font color="Red" class=normalsm>* </font>Username:</td>
            <td><input type="text" name="username" size="35" value="<? pv($frm["username"]) ?>">
                <?err($errors->username)?>
            </td>
        </tr>
     
       <tr>
 			<td class=label>Password:</td>
            <td><input type="text" name="password" size="35" value="">
            <br>
            <span class="normalsm" > By leaving this field blank, the password will not be updated.</span>
            </td>
        </tr>
         <? } ?>
        <tr>
            <td class=label><font color="Red" class="normalsm">* </font>First Name:</td>
            <td><input type="text" name="firstname" size="35" value="<? pv($frm["firstname"]) ?>">
                <?err($errors->firstname)?>
            </td>
        </tr>
        
        <tr>
            <td class=label><font color="Red" class=normalsm>* </font>Last Name:</td>
            <td><input type="text" name="lastname" size="35" value="<? pv($frm["lastname"]) ?>">
                <?err($errors->lastname)?>
                </td>
        </tr>

        <tr>
            <td class=label></font>Email:</td>
            <td><input type="text" name="email" size="35" value="<? pv($frm["email"]) ?>">
                <?err($errors->email)?>
                </td>
        </tr>

        <tr>
            <td class=label></font> Home Phone:</td>
            <td><input type="text" name="homephone" size="35" value="<? pv($frm["homephone"]) ?>">
                <?err($errors->homephone)?>
                </td>
        </tr>

        <tr>
            <td class=label></font> Work Phone:</td>
            <td><input type="text" name="workphone" size="35" value="<? pv($frm["workphone"]) ?>">
                <?err($errors->workphone)?>
            </td>
        </tr>
         <tr>
            <td class=label>Mobile Phone:</td>
            <td><input type="text" name="cellphone" size="35" value="<? pv($frm["cellphone"]) ?>">
                <?err($errors->cellphone)?>
            </td>
        </tr>
         <tr>
            <td class=label>Pager:</td>
            <td><input type="text" name="pager" size="35" value="<? pv($frm["pager"]) ?>">
                <?err($errors->pager)?>
            </td>
        </tr>
 		<tr>
            <td class=label>Date Joined:</td>
            <td><input type="text" name="msince" size="35" value="<? pv($frm["msince"]) ?>">
                <?err($errors->msince)?>
            </td>
        </tr>

        <tr>
            <td class=label>Address:</td>
            <td colspan="2"><textarea name="useraddress" cols="50" rows="5"><? pv($frm["useraddress"]) ?></textarea>
                <?err($errors->address)?>
            </td>
        </tr>
        <tr>
            <td class=label>
            <?if(isSiteAutoLogin()){ ?>
            	<font color="Red" class=normalsm>* 
             <? } ?>	
            Membership ID:</td>
            <td><input type="text" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>">
                <?err($errors->memberid)?>
            </td>
        </tr>
        <tr>
            <td class=label>Gender:</td>
            <td><select name="gender">
            	<option value="1">Male</option>
            	<option value="0" <? if($frm["gender"]==0) print "selected" ?> >Female</option>
            </select>
                <?err($errors->gender)?>
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
                   
                   <input type="text" id="date-param" name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="" maxlength="40" >
                  </td>
            <? } ?>

			<? } ?>
				
			
        
        
       <tr>
            <td colspan="2" height="20"><hr></td>
        </tr>
        <tr>
            <td class="label">Receive Players Wanted Notifications:</td>
            <td><select name="recemail"> 
                 <option value="y">Yes</option>
                <option value="n" <? if($frm["recemail"]=='n') print "selected" ?> >No</option>
                <?err($errors->recemail)?>
                </td>
        </tr>

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
                <?err($errors->enable)?>

        </tr>

        <tr>
            <td colspan="2" height="20"></td>
        </tr>
        <tr>
            <td colspan="2">
            <input type="button" name="submit" value="Update Settings" id="submitbutton">
            <input type="button" value="Cancel" id="cancelbutton" >

            </td>
        </tr>


        </table>
        
        <input type="hidden" name="userid" value="<?pv($userid) ?>"></td>
        <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
        <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
        <input type="hidden" name="searchname" value="<? pv($searchname)?>">
        <input type="hidden" name="submitme" value="submitme">
            
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
         
         



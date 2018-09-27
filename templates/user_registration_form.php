<div align="left">
  <form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">
    <table cellspacing="0" cellpadding="20" width="600" class="generictable" id="formtable">
      <tr class="borderow">
        <td class=clubid<?=get_clubid()?>th><span class="whiteh1">
          <div align="center">
            <? pv($DOC_TITLE) ?>
          </div>
          </span></td>
      </tr>
      <tr>
        <td><table width="550" border="0" cellpadding="0" cellspacing="2" >
            <? if(!isSiteAutoLogin()){ ?>
            <tr>
              <td class="label"><span class="warning">* </span>Username:
              <td><input type="text" name="username" size=35 value="<?=$frm["username"] ?>">
                <?err($errors->username)?></td>
            </tr>
            <tr>
              <td class="label"><span class="warning">* </span>Password:
              <td><input type="password" name="password" size="35">
                <?err($errors->password)?></td>
            </tr>
            <? }?>
            <tr>
              <td class="label"><span class="warning">* </span>First Name: </td>
              <td><input type="text" name="firstname" size="35" value="<?=$frm["firstname"]?>">
                <?err($errors->firstname)?></td>
            </tr>
            <tr>
              <td class="label"><span class="warning">* </span>Last Name: </td>
              <td><input type="text" name="lastname" size="35" value="<? pv($frm["lastname"]) ?>">
                <?err($errors->lastname)?></td>
            </tr>
            <tr>
              <td class="label">Home Phone:</td>
              <td><input type="text" name="homephone" size="35" value="<?=$frm["homephone"] ?>">
                <?err($errors->homephone)?></td>
            </tr>
            <tr>
              <td class="label">Work Phone:</td>
              <td><input type="text" name="workphone" size="35" value="<?=$frm["workphone"] ?>">
                <?err($errors->workphone)?></td>
            </tr>
            <tr>
              <td class="label">Email:</td>
              <td><input type="text" name="email" size="35" value="<?=$frm["email"] ?>">
                <?err($errors->email)?></td>
            </tr>
            <tr>
              <td class="label">Mobile Phone:</td>
              <td><input type="text" name="cellphone" size="35" value="<?=$frm["cellphone"] ?>">
                <?err($errors->cellphone)?></td>
            </tr>
            <tr>
              <td class="label">Pager:</td>
              <td><input type="text" name="pager" size=35 value="<?=$frm["pager"]?>">
                <?err($errors->pager)?></td>
            </tr>
            <tr valign=top>
              <td class="label">Address:</td>
              <td><textarea name="useraddress" cols=35 rows=5><?=$frm["useraddress"] ?>
</textarea>
                <?err($errors->useraddress)?></td>
            </tr>
            <tr>
              <td class="label">Date Joined:</td>
              <td><input type="text" name="msince" size=35 value="<?=$frm["msince"]?>">
                <?err($errors->msince)?></td>
            </tr>
            <tr>
              <td class="label"></td>
              <td><span class="italitcsm"> I.E January 2, 1988 </span></td>
            </tr>
            <tr>
              <td class="label">Gender</td>
              <td><select name="gender">
                  <option value="1">Male</option>
                  <option value="0">Female</option>
                </select></td>
            </tr>

            <tr>
              <td class="label"><? if(isSiteAutoLogin()){ ?>
                <span class="warning">* </span>
                <? } ?>
                Membership ID:</td>
              <td><input type="text" name="memberid" size=35 value="<? pv($frm["memberid"]) ?>">
                <?err($errors->memberid)?></td>
            </tr>
            <?
			

			// Display the custom parameters
			while( $parameterArray = db_fetch_array($extraParametersResult)){ 

				if($parameterArray['parametertypename'] == "text"){ ?>
            <tr>
              <td class="label"><? pv($parameterArray['parameterlabel'])?>
                :</td>
              <td><input type="text" name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="<? pv($frm[$parameterArray['parameterid']]) ?>" maxlength="40"></td>
            </tr>
            <? } elseif($parameterArray['parametertypename'] == "select") { ?>
            <tr>
              <td class="label"><? pv($parameterArray['parameterlabel'])?>
                :</td>
              <td><select name="<?="parameter-".$parameterArray['parameterid']?>">
                  <?
								// Get Parameter Options
								$parameterOptionResult = load_parameter_options($parameterArray['parameterid']);
								
								while($parameterOptionArray = db_fetch_array($parameterOptionResult)){ ?>
                  <option value="<? pv($parameterOptionArray['optionvalue']) ?>">
                  <? pv($parameterOptionArray['optionname']) ?>
                  </option>
                  <? } ?>
                </select></td>
            </tr>
            <? } elseif($parameterArray['parametertypename'] == "date") { ?>
              <td class="label"><? pv($parameterArray['parameterlabel'])?>:
              <td>
               <span style="padding-left: 10px"> <img src="<?=$_SESSION["CFG"]["imagedir"]?>/cal.png"  id="show" title="Click here to change the date"> </span >
               
               <input type="text" id="date-param" name="<?="parameter-".$parameterArray['parameterid']?>" size="30" value=""  readonly>
              </td>
            <? } ?>
            

            <? } ?>
            <tr>
              <td colspan="2" height="20"><hr></td>
            </tr>
            <?
			//Get Available courttypes
			while($availbleSportsArray = db_fetch_array($availbleSportsResult)){


				?>
            <tr valign=top>
              <td class="label"><? echo "$availbleSportsArray[courttypename] Ranking" ?>:</td>
              <td><input type="text"
					name="<? echo "courttype$availbleSportsArray[courttypeid]"?>"
					size="15" value="3"></td>
            </tr>
            <?
			//While closing bracket - DO NOT remove
			}
			?>
            <tr>
              <td colspan="2" height="20"><hr></td>
            </tr>
            <tr>
              <td class="label" valign="top">Authorized Sites:</td>
              <td class="normal"><?

				while($availableSitesArray = mysqli_fetch_array($availableSitesResult)){
					$checked = "checked";

					if(!amiValidForSite($availableSitesArray['siteid'])){
						$disabled = "disabled";
						$checked = "";
					}
					else{
						$disabled = "";

					}


					print "<input type=\"checkbox\" name=\"clubsite$availableSitesArray[siteid]\" value=\"$availableSitesArray[siteid]\" $checked $disabled> $availableSitesArray[sitename] <br>\n";

					unset($disabled);
					unset($checked);
				}

				//Done with Authorized Sites
				?></td>
            </tr>
            <tr>
              <td class="label" valign="top">User Type:</td>
              <td><select name="usertype">
                  <option value="1">Player</option>
				  <option value="6">Junior</option>
                  <option value="4">Desk User</option>
                  <option value="2">Club Admin</option>
                </select></td>
            </tr>
			<tr>
              <td colspan="2"><span class="italitcsm">Most regular members will should be set up as "Players".   Use the Junior user type for kids who may not have their own email address.  Desk Users are those guys who generally make reservations on behalf of other people, you know, like the people who sit at the front desk.  Finally, Club Admins are for the racquet pros and or other folks who administer the system for the club.</span></td>
            </tr>
            <tr>
              <td colspan="2" height="20"></td>
            </tr>
            <tr>
              <td colspan="2"><input type="button" name="submit" value="Add New User" id="submitbutton"></td>
            </tr>
        </table></td>
      </tr>
    </table>

    
  </form>
  <div style="height: 30px">
  <span class="warning">* </span> <span class="normal">indicates a required field</span> </div>
</div>
<script type="text/javascript">


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

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

                        document.getElementById("date-param").value = pad(monStr,2) + "/" + pad(dStr,2) + "/" + yStr;
                        
                        
                       
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

function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 

<script type="text/javascript">
YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){

	document.entryform.submit();
}

</script>
<div align="center">
  <form name="entryform" method="post" action="<?=$ME?>">
    <table width="600" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
      <tr>
        <td class=clubid<?=get_clubid()?>th><font class=whiteh1>
          <div align="center">
            <? pv($DOC_TITLE) ?>
          </div>
          </font></td>
      </tr>
        <tr>
      
        <td >
      
      <table cellspacing="5" cellpadding="1" width="600" class="borderless">
        <tr >
          <td class="label medwidth" >Username:</td>
          <td class="normal"><? pv($frm["username"]) ?></td>
          <td rowspan="9" valign="top" ><div align="center"> <img src="<?=get_gravatar($frm["email"],120 )?>" /> </div>
            <div align="center"> <span class="normalsm">update your gravatar <a href="http://www.gravatar.com" target="_blank">here</a></span> </div></td>
        </tr>
        <tr>
          <td class="label medwidth">Sportsynergy Id:</td>
          <td class="normal"><? pv($frm["userid"]) ?></td>
        </tr>
        <tr>
          <td class="label medwidth" ><font color="Red" class="normalsm">* </font>First Name:</td>
          <td><input type="text" name="firstname" size="35" value="<? pv($frm["firstname"]) ?>">
            <?err($errors->firstname)?></td>
        </tr>
        <tr>
          <td class="label medwidth" ><font color="Red" class=normalsm>* </font>Last Name:</td>
          <td><input type="text" name="lastname" size="35" value="<? pv($frm["lastname"]) ?>">
            <?err($errors->lastname)?></td>
        </tr>
        <tr>
          <td class="label medwidth"><font color="Red" class=normalsm>* </font>Email:</td>
          <td><input type="text" name="email" size="35" value="<? pv($frm["email"]) ?>">
            <?err($errors->email)?></td>
        </tr>
        <tr>
          <td class="label medwidth"><font color="Red" class=normalsm>* </font> Home Phone:</td>
          <td><input type="text" name="homephone" size="35" value="<? pv($frm["homephone"]) ?>">
            <?err($errors->homephone)?></td>
        </tr>
        <tr>
          <td class="label medwidth"><font color="Red" class=normalsm>* </font>Work Phone:</td>
          <td><input type="text" name="workphone" size="35" value="<? pv($frm["workphone"]) ?>">
            <?err($errors->workphone)?></td>
        </tr>
        <tr>
          <td class="label medwidth">Mobile Phone:</td>
          <td><input type="text" name="cellphone" size="25" value="<? pv($frm["cellphone"]) ?>">
            <?err($errors->cellphone)?></td>
        </tr>
        <tr>
          <td class="label medwidth">Pager:</td>
          <td><input type="text" name="pager" size="35" value="<? pv($frm["pager"]) ?>">
            <?err($errors->pager)?></td>
        </tr>
        <tr>
          <td class="label medwidth">Address:</td>
          <td colspan="2"><textarea name="useraddress" cols="50" rows="5"><? pv($frm["useraddress"]) ?>
</textarea>
            <?err($errors->address)?></td>
        </tr>
        <tr>
          <td colspan="2" height="20"><hr></td>
        </tr>
          <tr>
        
        <td class="label medwidth">Receive Players Wanted Notifications:</td>
          <td>
        
        <select name="recemail">
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
          <?err($errors->recemail)?>
            </td>
          
            </tr>
          
          <?
		// Get the Custom Parameters
		while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], get_userid() );
			
			if($parameterArray['parameteraccesstypename']=="read"){
				$disabled = "disabled=\"disabled\"";
			}
			
			if($parameterArray['parametertypename'] == "text"){ ?>
            <tr>
          
            <td class="label">
          
          <? pv($parameterArray['parameterlabel'])?>
            
          :
          
            </td>
          
            <td>
          
            <input type="text" name="<?="parameter-".$parameterArray['parameterid']?>" size="35" value="<? pv($parameterValue) ?>" <? pv($disabled)?> maxlength="40">
          
            </td>
          
            </tr>
          
          <? } elseif($parameterArray['parametertypename'] == "select") { ?>
            <tr>
          
            <td class="label">
          
          <? pv($parameterArray['parameterlabel'])?>
            
          :
          
            </td>
          
            <td>
          
            <select name="<?="parameter-".$parameterArray['parameterid']?>" <? pv($disabled)?>>
          
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
          <option value="<? pv($parameterOptionArray['optionvalue']) ?>" <? pv($selected) ?>>
          <? pv($parameterOptionArray['optionname']) ?>
          </option>
          <? } ?>
        </select>
          </td>
        
          </tr>
        
        <? } ?>
        <? } ?>
        <tr>
          <td colspan="2" height="20"></td>
        </tr>
        <tr>
          <td colspan="2">
          	<input type="button" name="submit" value="Update Settings" id="submitbutton">
          </td>
        </tr>
      </table>
        </td>
      
        </tr>
      
    </table>
    <div align="left" style="margin-left: 90px; margin-top: 20px"> <span class="normal warning">* </span> <span class="normal">indicates a required field</span> </div>
    <input type="hidden" name="userid" value="<?pv($userid) ?>">
    </td>
    <input type="hidden" name="mycourttypes" value="<? pv($mycourtTypes) ?>">
    <input type="hidden" name="mysites" value="<? pv($mysites) ?>">
    <input type="hidden" name="username" value="<? pv($frm["username"]) ?>">
    <input type="hidden" name="submitme" value="submitme">
  </form>
  <?if($DOC_TITLE == "Player Administration"){ ?>
  <form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">
    <span style="text-align: rit"><a href="javascript:submitForm('backtolistform');"><< Back to List</a></span>
    <input type="hidden" name="searchname" value="<? pv($searchname) ?>">
  </form>
  <? }?>
</div>
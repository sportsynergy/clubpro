<html>
<head>
<title>Clubpro Login</title>


<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css"> 
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.css" rel=stylesheet type=text/css>

<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" />
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script>

<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/button-min.js"></script>

<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>

</head>



<body bgcolor=#ffffff link=#0000ff vlink=#000099 alink=#ff0000 class="yui-skin-sam">

<script type="text/javascript" >


function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center" id="formtable">
<tr>
	<td>
		<img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/0.gif" >
	</td>
</tr>
<tr>
    <td height="25">
    <!-- Spacer row -->
    </td>
</tr>
<tr>
	<td>
		<span class="normal">
		Thanks for looking into our online court reservation service.  Trying out this software before you buy it is a 
		great way to see how our online service will fit in at your club.  As many of our current customers have found, once you see
		how simple this system is you're not going to want to go back.  So, go head, take the first step and register.  
		After you submit this form you will be sent an email with some more instructions.  
		</span>
	</td>
</tr>

<tr>
    <td height="25">
    <? include($_SESSION["CFG"]["templatedir"]."/form_header.php"); ?>
    </td>
</tr>
<tr>
<td>

	<table width="600" cellpadding="20" cellspacing="0">
	     
	     <tr class="borderow">
	         <td class=clubid0th>
	         	<span class="whiteh1">
	         		<div align="center"><? pv($DOC_TITLE) ?></div>
	         	</span>
	         </td>
	    </tr>
	
	 	<tr>
		    <td class="generictable">
		
		      <form name="entryform" method="post" action="<?=$ME?>">
		      
		      	<table border="0" cellpadding="2" cellspacing="2">
				  <tr>
				    <td class="label">Club Name:</td>
				    <td><input name="clubname" type="text" value="<? pv($frm["clubname"]) ?>"/>
					 <? is_object($errors) ? err($errors->clubname) : ""?>
					
					</td>
				  </tr>
				  <tr>
				    <td class="label">Club Code:<br/>
				    <span class="italitcsm">Think of this as your call sign</span></td>
				    <td><input name="clubcode" type="text" value="<? pv($frm["clubcode"]) ?>"/>
				    <? is_object($errors) ? err($errors->clubcode) : ""?>
					</td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Username:</td>
				    <td><input name="adminuser" type="text" value="<? pv($frm["adminuser"]) ?>"/>
					<? is_object($errors) ? err($errors->adminuser) : ""?>
					
					</td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Password:</td>
				    <td><input name="adminpass1" type="password" value="<? pv($frm["adminpass1"]) ?>"/>
					<? is_object($errors) ? err($errors->adminpass1) : ""?>
					</td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Password (again):</td>
				    <td><input name="adminpass2" type="password" value="<? pv($frm["adminpass2"]) ?>"/>
					<? is_object($errors) ? err($errors->adminpass2) : ""?>
					</td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Email:</td>
				    <td><input name="adminemail" type="text" value="<? pv($frm["adminemail"]) ?>"/>
					<? is_object($errors) ? err($errors->adminemail) : ""?>
					</td>
				  </tr>
				   <tr>
				    <td class="label">Administrator First Name:</td>
				    <td><input name="adminfirstname" type="text" value="<? pv($frm["adminfirstname"]) ?>"/>
					<? is_object($errors) ? err($errors->adminfirstname) : ""?>
					</td>
				  </tr>
				   <tr>
				    <td class="label">Administrator Last Name:</td>
				    <td><input name="adminlastname" type="text" value="<? pv($frm["adminlastname"]) ?>"/>
					<? is_object($errors) ? err($errors->adminlastname) : ""?>
					</td>
				  </tr>
				  <tr>
				  		<td class="label">Timezone:</td>
				    <td>
				    <select name="timezone">
				       <? while($timezoneArray = mysqli_fetch_array($availbleTimezones)){ ?>
				       	<option value="<?=$timezoneArray['offset']?>"><?=$timezoneArray['name']?></option>
				       <? }?>
				    </select>
				    </td>
				  </tr>
				  <tr>
				    <td class="label">Number of Courts:</td>
				    <td>
				    <select name="courtnumber">
				        <option value="1">1</option>
				        <option value="2">2</option>
				        <option value="3">3</option>
				        <option value="4">4</option>
				        <option value="5">5</option>
				        <option value="6">6</option>
				        <option value="7">7</option>
				        <option value="8">8</option>
				        <option value="9">9</option>
				        <option value="10">10</option>
				        <option value="11">11</option>
				        <option value="12">12</option>
				        <option value="13">13</option>
				        <option value="14">14</option>
				        <option value="15">15</option>
				        <option value="16">16</option>
				        <option value="17">17</option>
				        <option value="18">18</option>
				        <option value="19">19</option>
				        <option value="20">20</option>
				    </select>
				    </td>
				  </tr>
			
				  <tr>
				    <td class="label">Type of Courts:</td>
				    <td>
				    <select name="courttype">
				        <option value="2">Softball Singles Squash</option>
				        <option value="3">Hardball Doubles Squash</option>
				        <option value="4">Hardball Singles Squash</option>
				        <option value="6">Racquetball</option>
				        <option value="7">Tennis</option>
				    </select>
				    </td>
				  </tr>
				  <tr>
				  	<td>
				  		<div style="padding-top: 25px">
					  		<input type="button" name="submit" value="Register Club" id="submitbutton">
					  		<input type="hidden" name="submitme" value="submitme" >
					  	</div>
				  	</td>
				  </tr>
				</table>
		      	
		      </form>
			</td>
		</tr>
	</table>
</td>
</tr>
</table>



</body>

</html>
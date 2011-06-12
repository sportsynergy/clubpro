<?

/*
 * $LastChangedRevision: 799 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-01-02 20:23:24 -0600 (Sun, 02 Jan 2011) $

*/

?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

<tr>
	<td>
		<span class="normal">
		Thanks for looking into our online court reservation service.  Trying out this software before you buy it is a 
		great way to see how it will fit in at your club.  As many of our current customers have found, once you see
		how simple this system is you're not going to want to go back.  So, go head, take the first step and register.  
		After you submit this form you will be sent an email with some more instructions.  
		</span>
	</td>
</tr>
<tr>
    <td height="15">
    <!-- Spacer row -->
    </td>
</tr>
<tr>
<td>

	<table width="600" cellpadding="20" cellspacing="0">
	     <tr>
	         <td class=clubid0th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
	    </tr>
	
	 	<tr>
		    <td class="generictable">
		
		      <form name="entryform" method="post" action="<?=$ME?>">
		      
		      	<table border="0" cellpadding="2" cellspacing="2">
				  <tr>
				    <td class="label">Club Name:</td>
				    <td><input name="clubname" type="text" value="<? pv($frm["clubname"]) ?>"/>
				     <?err($errors->clubname)?></td>
				  </tr>
				  <tr>
				    <td class="label">Club Code:<br/>
				    <span class="italitcsm">Think of this as your call sign</span></td>
				    <td><input name="clubcode" type="text" value="<? pv($frm["clubcode"]) ?>"/>
				     <?err($errors->clubcode)?></td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Username:</td>
				    <td><input name="adminuser" type="text" value="<? pv($frm["adminuser"]) ?>"/>
				     <?err($errors->adminuser)?></td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Password:</td>
				    <td><input name="adminpass1" type="password" value="<? pv($frm["adminpass1"]) ?>"/>
				     <?err($errors->adminpass1)?></td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Password (again):</td>
				    <td><input name="adminpass2" type="password" value="<? pv($frm["adminpass2"]) ?>"/>
				     <?err($errors->adminpass2)?></td>
				  </tr>
				  <tr>
				    <td class="label">Administrator Email:</td>
				    <td><input name="adminemail" type="text" value="<? pv($frm["adminemail"]) ?>"/>
				     <?err($errors->adminemail)?></td>
				  </tr>
				   <tr>
				    <td class="label">Administrator First Name:</td>
				    <td><input name="adminfirstname" type="text" value="<? pv($frm["adminfirstname"]) ?>"/>
				     <?err($errors->adminfirstname)?></td>
				  </tr>
				   <tr>
				    <td class="label">Administrator Last Name:</td>
				    <td><input name="adminlastname" type="text" value="<? pv($frm["adminlastname"]) ?>"/>
				     <?err($errors->adminlastname)?></td>
				  </tr>
				  <tr>
				  		<td class="label">Timezone:</td>
				    <td>
				    <select name="timezone">
				       <? while($timezoneArray = mysql_fetch_array($availbleTimezones)){ ?>
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
				  		<input type="submit" name="submit" value="Submit">
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






<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">



<table cellspacing="0" cellpadding="20" width="550" class="generictable" >
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>
 <tr>

    <td >

     <table width="550" cellpadding="5" cellspacing="2">
        
        <tr>
            <td class="label">Name:</td>
            <td>
            	<input type="text" name="name" maxlength="30" size="30" value="<?=$courtEvent['eventname']?>"><?err($errors->name)?>
            </td>
        </tr>
       
        <tr>
            <td class="label">Player Limit:</td>
            <td><select name="playerlimit">
            		<?
            		for($i = 0; $i < 11; ++$i){ ?>
            			<option value="<?=$i?>" <?=$i == $courtEvent['playerlimit']? "selected" : "" ?>><?=$i?></option>
            			
            		<? } ?>
                        
                </select>
            </td>
        </tr>
   
     
       <tr>
           <td>
           		<input type="submit" name="submit" value="Submit"/>
           		<input type="hidden" name="policyid" value="<?=$courtEvent['eventid']?>"/>
          </td>
       </tr>
       	
 </table>
</td>

</tr>

</table>
</form>


<div style="height: 2em;"></div>
<div>
	<span class="normal"> 
		<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php#court_events" > << Back to Court Events </a> 
	</span>
</div>
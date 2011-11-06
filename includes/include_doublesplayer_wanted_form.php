<?
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $
*/

  //reservation_doubles_wanted_form.php
   $DOC_TITLE = "Doubles Court Reservation";


?>



<script language="Javascript">


// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}


</script>
<?
 // Get the first and last name of the player who needs a partner
 $getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
                     FROM tblUsers
                     WHERE tblUsers.userid=$userid";

 $getfirstandlastresult = db_query($getfirstandlastquery);
 $getfirstandlastobj = db_fetch_object($getfirstandlastresult);


?>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center">
    		<? if($locked=='y'){ ?>
	    	 	<img src="<?=$_SESSION["CFG"]["imagedir"]?>/lock.png"> 
	    	<?}?>
	    	<? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">
	       <tr>
	           <td class="normal">Are you sure you want to sign up to play with <?echo "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?</td>
	       </tr>
	       <tr>
	           <td>
			           <?
			       //if its locked and its just a player disable the submit button
			       $disabled="";
			       if( $locked=='y' && get_roleid()==1){
			       	
			       	$disabled = "disabled=disabled";
			       }
			       
			       ?>
	            	<input type="submit" name="cancel" value="Yes" <?=$disabled?>>
	            	<input type="button" value="No, go back" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?= gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
	       		</td>
	       </tr>
 	</table>


 </td>
 </tr>

</table>


<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="courttype" value="doubles">
<input type="hidden" name="action" value="addpartner">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="userid" value="<?=$userid?>">


</form>






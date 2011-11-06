<?
  /*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $
*/



 //reservation_doubles_wanted_form.php
 $DOC_TITLE = "Doubles Court Reservation";

// Get the first and last name of the player who needs a partner
 $getfirstandlastquery = "SELECT tblUsers.firstname, tblUsers.lastname
 	                     FROM tblUsers
 	                     WHERE tblUsers.userid=$userid";
 	
$getfirstandlastresult = db_query($getfirstandlastquery);
$getfirstandlastobj = db_fetch_object($getfirstandlastresult);
 	


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

function disablePartnerList(disableIt)
{
        document.entryform.partnername.disabled = disableIt;
        document.entryform.partnername.disabled = disableIt;
}

function enablePartnerList()
{
        document.entryform.partnername.disabled = "";
        document.entryform.partnername.disabled = "";
}

document.onkeypress = function (aEvent)
{
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
  	
}
</script>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  <tr>
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
				<td>
					<input type="radio" name="playwith" value="1" checked="checked" onclick="disablePartnerList(this.checked)">
				</td>
	         	<td colspan="2">
	         		Play with <?print "$getfirstandlastobj->firstname $getfirstandlastobj->lastname"?>?
	         	</td>
	       </tr>
	        <tr>
		        <td>
		        	<input type="radio" name="playwith" value="2" onclick="enablePartnerList()">
		        </td>
		         <td>
		         	Play with Partner:
		         </td>
		         <td>
		         
		          <input id="name1" name="partnername" type="text" size="30" class="form-autocomplete" disabled="disabled"/>
		          <input id="id1" name="partner" type="hidden" />
		    			<script>
		                <?
		                $wwwroot = $_SESSION["CFG"]["wwwroot"];
		                 pat_autocomplete( array(
								'baseUrl'=> "$wwwroot/users/ajaxServer.php",
								'source'=>'name1',
								'target'=>'id1',
								'className'=>'autocomplete',
								'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courtid=$courtid&siteid=".get_siteid()."&clubid=".get_clubid()."",
								'progressStyle'=>'throbbing',
								'minimumCharacters'=>3,
								));
		                 ?>
		                </script>
					</td>
	        </tr>
		    <tr> 
			    <td colspan="3">
				    <br>
			         <?
	       //if its locked and its just a player disable the submit button
	       $disabled="";
	       if( $locked=='y' && get_roleid()==1){
	       	
	       	$disabled = "disabled=disabled";
	       }
	       
	       ?>
	       <input type="submit" name="submit" value="Make Reservation"  <?=$disabled?>>
			        <input type="button" value="Cancel" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?=gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
			    </td>
		    </tr>


 		</table>


 </td>
 </tr>

</table>

<input type="hidden" name="time" value="<?=$time?>"/>
<input type="hidden" name="courtid" value="<?=$courtid?>"/>
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
<input type="hidden" name="courttype" value="doubles"/>
<input type="hidden" name="userid" value="<?=$userid?>">
<input type="hidden" name="matchtype" value="<?=$matchtype?>">
<input type="hidden" name="action" value="addplayerorteam">
        	
        	
</form>
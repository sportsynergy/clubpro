<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/

?>

<form name="entryform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/court_reservation.php" onSubmit="SubDisable(this);" autocomplete="off">


<table cellspacing="0" cellpadding="20" width="400" class="generictable">
  
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400">

       <tr>
           <td class="normal">Do you want to sign up for this court?</td>
           <td>
           		<input type="submit" name="cancel" value="Yes"> 
            	<input type="button" value="No" onClick="parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/index.php?daysahead=<?=gmmktime (0,0,0,gmdate("n",$time+get_tzdelta() ),gmdate("j", $time+get_tzdelta()),gmdate("Y", $time+get_tzdelta()))?>'">
            </td>
    </tr>
 </table>

</table>


<input type="hidden" name="time" value="<?=$time?>">
<input type="hidden" name="courtid" value="<?=$courtid?>">
<input type="hidden" name="guylookingformatch" value="<?=$userid?>">
<input type="hidden" name="courttype" value="singles">
<input type="hidden" name="matchtype" value="<?=$matchtype?>">
<input type="hidden" name="action" value="addpartner">
<input type="hidden" name="lastupdated" value="<?=$lastupdated?>">
            
            
</form>

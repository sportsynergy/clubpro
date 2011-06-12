<?php
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $

*/
?>
<script language="Javascript">
<
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

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="20" width="400" >
 <tr>
    <td class=clubid<?=get_clubid()?>th><font class=whiteh1><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class=generictable>

     <table cellspacing="1" cellpadding="5" width="400" >


        <tr>
        <td class=label>Sport Type:</td>
        <td>
         <select name="sporttype">
         <option value="">Select Option</option>

          <?
         //check to see if this is a multiplayer court

         while ( $sportarray = db_fetch_array($availbleSports)){

             ?>
             <option value="<? pv($sportarray[courttypeid] ) ?>"><? pv($sportarray[courttypename] ) ?></option>




         <?} ?>

        </select>

                <?err($errors->searchname)?>
        </td>
        </tr>


       <tr>
           <td class=label>Sort By:</td>
           <td>
           <select name="sortoption">

                       <option value="rank">Rank</option>
                       <option value="fname">First Name</option>
                       <option value="lname">Last Name</option>
              </select>
           </td>

    </tr>

       <tr>
           <td></td>
           <td><input type="submit" name="submit" value="Submit"></td>

    </tr>
 </table>

</table>
</form>

</td>
</tr>
</table>
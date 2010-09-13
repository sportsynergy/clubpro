<?php
/*
 * $LastChangedRevision: 739 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-09-16 21:35:43 -0700 (Wed, 16 Sep 2009) $

*/
?>
<script language="Javascript">
<!--
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

function disableSortByDropDown(listselection)
{
        
        if(listselection.value == "ladder" ){
             document.entryform.sortoption.disabled = true;
        }
        else{
        	document.entryform.sortoption.disabled = "";
        }
        

}



//-->
</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>


<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="20" width="400" >
 <tr>
    <td class=clubid<?=get_clubid()?>th><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td class="generictable">

     <table cellspacing="1" cellpadding="5" width="400" >


        <tr>
        <td class="label">Sport Type:</td>
        <td>
         <select name="courttypeid">
         <option value="">Select Option</option>

          <?  while ( $sportarray = db_fetch_array($availbleSports)){ ?>
             	<option value="<? pv($sportarray['courttypeid'] ) ?>"><? pv($sportarray['courttypename'] ) ?></option>
         <?} ?>

        </select>

        </td>
        </tr>
        <tr>
           <td class="label">Display:</td>
           <td>
               <select name="displayoption" onchange="disableSortByDropDown(this)">
                       <option value="all">All Players</option>
                       <option value="ladder">Club Ladder</option>
                       <option value="5+">5.0 and up</option>
                       <option value="4">4.0</option>
                       <option value="3">3.0</option>
                       <option value="2">2.0</option>
                       <option value="2-">2.0 and below</option>
              </select>
           </td>

    </tr>

       <tr>
           <td class="label">Sort By:</td>
           <td>
           <select name="sortoption" >
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
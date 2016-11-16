<?
/*
 * $LastChangedRevision: 847 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-01 10:26:06 -0600 (Tue, 01 Mar 2011) $

*/
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

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbutton1value" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}


</script>



<form name="entryform" method="post" action="<?=$ME?>">



<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
 <tr>
    <td class=clubid<?=get_clubid()?>th><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
 </tr>

 <tr>
    <td >

     <table cellspacing="1" cellpadding="5" width="400" class="borderless" >


        <tr>
        <td class="label">Sport Type:</td>
        <td>
         <select name="courttypeid">

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
           <select name="sortoption">
                   <option value="rank" >Rank</option>
                   <option value="fname">First Name</option>
                   <option value="lname">Last Name</option> 
              </select>
           </td>

    </tr>
	<tr>
           <td colspan="2" height="20"><!--  Spacer --></td>
    </tr>

       <tr>
       
           <td colspan="2">
           
           <input type="button" name="submit" value="Submit" id="submitbutton">
           <input type="hidden" name="submitme" value="submitme">
           
           </td>
    </tr>
 </table>

</table>
</form>

<div style="height: 2em"></div>

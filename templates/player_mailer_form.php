


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

function disableRankingDropDown()
{
        
        var listselection = document.getElementById('sportselect');
        if(listselection.value == "all" ){
             document.entryform.ranking.disabled = true;
        }
        else{
        	document.entryform.ranking.disabled = "";
        }
        

}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        YAHOO.util.Event.addListener("sportselect", "change", disableRankingDropDown); 

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}




</script>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class="whiteh1">
    			<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td >

     <table width="400">
		
		<tr>
        <td class="label">To:</td>
        <td colspan="3">
         <select name="who">>
            <option value="allplayers">All Players</option>
            <option value="allWomen">All Women</option>
            <option value="allMen">All Men</option>
            <option value="boxleaguePlayers">Box League Players</option>
            <option value="myBuddies">My Buddies</option>
          </select>
        
        </td>
        
       </tr> 
       <tr>
       		<td class=label align="left">Sport:</td>
	        <td align="left">
	        	 <select name="sport" id="sportselect">
	        	    <option value="all">All</option>
	        	  	<?  while ( $sportarray = db_fetch_array($availbleSports)){ ?>
             			<option value="<? pv($sportarray['courttypeid'] ) ?>"><? pv($sportarray['courttypename'] ) ?>   </option>
        			 <?} ?>
	        	 </select>
	        </td>
       		<td class=label align="left">Ranking:</td>
	        <td align="left">
				 <select name="ranking" disabled>
				 		<option value="all">All</option>
            			<option value="2.5">2.5</option>
            			<option value="3.0">3.0</option>
            			<option value="3.5">3.5</option>
            			<option value="4.0">4.0</option>
            			<option value="4.5">4.5</option>
            			<option value="5.0">5.0</option>
            			<option value="5.5">5.5</option>
          		</select>
	            <?err($errors->ranking)?>
	        </td>
       </tr> 
       <tr>
        	<td class=label >Subject:</td>
	        <td colspan="3"><input type="text" name="subject" size=67 value="<? pv($frm["subject"]) ?>">
	                <?err($errors->subject)?>
	        </td>
       </tr>
       <tr>
           <td class=label valign="top">Message:</td>
                 <td colspan="3"><textarea name="message" cols=50 rows=5><? pv($frm["message"]) ?></textarea>
                      <?err($errors->useraddress)?>
                </td>
        </tr>
       <tr>
           <td></td>
           <td><input type="button" name="submit" value="Send Email" id="submitbutton"></td>
            <td></td>
    </tr>
 </table>

<input type="hidden" name="submitme" value="submitme">

</table>
</form>


</td>
</tr>
</table>
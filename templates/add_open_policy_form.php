<script language="JavaScript">

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

        var oCancelButton = new YAHOO.widget.Button("cancelbutton", { value: "cancelbutton1value" });   
        oCancelButton.on("click", onCancelButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}

function onCancelButtonClicked(){

	parent.location='<?=$_SESSION["CFG"]["wwwroot"]?>/admin/manage_club_policies.php'
 }

<?


$thisYear = gmdate("Y");
$daysinFeb = gmdate("t", gmmktime(0,0,0,2,1,$thisYear));

print "var thisyear = new Array(13);
       thisyear[1] = 31\n
       thisyear[2] = $daysinFeb\n
       thisyear[3] = 31\n
       thisyear[4] = 30\n
       thisyear[5] = 31\n
       thisyear[6] = 30\n
       thisyear[7] = 31\n
       thisyear[8] = 31\n
       thisyear[9] = 30\n
       thisyear[10] = 31\n
       thisyear[11] = 30\n
        thisyear[12] = 31\n\n";

 $daysinFeb = gmdate("t", gmmktime(0,0,0,2,1,$thisYear+1));

 print "var nextyear = new Array(13);
       nextyear[1] = 31\n
       nextyear[2] = $daysinFeb\n
       nextyear[3] = 31\n
       nextyear[4] = 30\n
       nextyear[5] = 31\n
       nextyear[6] = 30\n
       nextyear[7] = 31\n
       nextyear[8] = 31\n
       nextyear[9] = 30\n
       nextyear[10] = 31\n
       nextyear[11] = 30\n
        nextyear[12] = 31\n";


?>

     function getDaysForMonth() {

          if(document.entryform.day.value != null){
               document.entryform.day.options.length = 0;
          }

          var month = document.entryform.month.value;


           if(document.entryform.year.selectedIndex == 0){

                 for (i=0; i<thisyear[month]; i++) {
                          var myDayValue = new String(i+1);
                          document.entryform.day.options[i] = new Option(i+1, myDayValue);
                  }
           }

           else{
                  for (i=0; i<nextyear[month]; i++) {
                          var myDayValue = new String(i+1);
                          document.entryform.day.options[i] = new Option(i+1, myDayValue);
                  }
           }
     }


</script>




<form name="entryform" method="post" action="<?=$ME?>">


<table cellspacing="0" cellpadding="20" width="400" class="generictable" id="formtable">
  <tr class="borderow">
    <td class=clubid<?=get_clubid()?>th>
    	<span class=whiteh1>
    		<div align="center"><? pv($DOC_TITLE) ?></div>
    	</span>
    </td>
 </tr>

 <tr>
    <td>

     <table width="400" cellpadding="5" cellspacing="2">


        <tr>
            <td class="label">Club:</td>
            <td>
            <select name="siteid">
            <option value="">Select Club Site</option>
            <option value="">-----------------------------</option>
            <?
                      //Get Club Players
                 $clubsDropDown = get_allsites_dropdown();

                 while($row = mysql_fetch_array($clubsDropDown)) {
                  echo "<option value=\"$row[siteid]\">$row[sitename] ($row[clubname])</option>\n";
                 }
                 ?>
                <?err($errors->siteid)?>
                </select>
                </td>
       </tr>
        <tr>
            <td class=label>Date:</td>
            <td>

            <select name="month" onChange="getDaysForMonth();">

               <?
                 $currentMonth =  gmdate("n");
                 $months = get_months();

                 for($i=0; $i<count($months); ++$i){

                             if($currentMonth==($i+1)){
                                  $selected = "selected";
                             }
                             else{
                                 $selected = "";
                             }
                 ?>
                    <option value="<?=$i+1?>" <?=$selected?>> <?=$months[$i]?></option>

                     <? unset($selected)?>
                <? } ?>
          </select>
           <select name="day">
           </select>
           <select name="year" onChange="getDaysForMonth();">
               <?

               $currYear = gmdate("Y");

               for($i=0; $i<2; ++$i){
                   $year = $currYear + $i;
               ?>
                   <option value="<?=$year ?>"><?=$year ?></option>
              <? }

               ?>

          </select>
            </td>
       </tr>
       <tr>
            <td class=label>Open Time:</td>
            <td>
                <select name="opentime">
                <option value="">Select Open Time</option>
                <option value="">--------------------</option>
                <option value="01:00:00">01:00:00</option>
                <option value="02:00:00">02:00:00</option>
                <option value="03:00:00">03:00:00</option>
                <option value="04:00:00">04:00:00</option>
                <option value="05:00:00">05:00:00</option>
                <option value="06:00:00">06:00:00</option>
                <option value="07:00:00">07:00:00</option>
                <option value="08:00:00">08:00:00</option>
                <option value="09:00:00">09:00:00</option>
                <option value="10:10:00">10:00:00</option>
                <option value="11:00:00">11:00:00</option>
                <option value="12:00:00">12:00:00</option>
                <option value="13:00:00">13:00:00</option>
                <option value="14:00:00">14:00:00</option>
                <option value="15:00:00">15:00:00</option>
                <option value="16:00:00">16:00:00</option>
                <option value="17:00:00">17:00:00</option>
                <option value="18:00:00">18:00:00</option>
                <option value="19:00:00">19:00:00</option>
                <option value="20:00:00">20:00:00</option>
                <option value="21:00:00">21:00:00</option>
                <option value="22:00:00">22:00:00</option>
                <option value="23:00:00">23:00:00</option>
                <option value="24:00:00">24:00:00</option>
                </select>

                </td>
       </tr>
       <tr>
            <td class=label>Close Time:</td>
            <td>
                <select name="closetime">
                <option value="">Select Close Time</option>
                <option value="">--------------------</option>
                <option value="01:00:00">01:00:00</option>
                <option value="02:00:00">02:00:00</option>
                <option value="03:00:00">03:00:00</option>
                <option value="04:00:00">04:00:00</option>
                <option value="05:00:00">05:00:00</option>
                <option value="06:00:00">06:00:00</option>
                <option value="07:00:00">07:00:00</option>
                <option value="08:00:00">08:00:00</option>
                <option value="09:00:00">09:00:00</option>
                <option value="10:10:00">10:00:00</option>
                <option value="11:00:00">11:00:00</option>
                <option value="12:00:00">12:00:00</option>
                <option value="13:00:00">13:00:00</option>
                <option value="14:00:00">14:00:00</option>
                <option value="15:00:00">15:00:00</option>
                <option value="16:00:00">16:00:00</option>
                <option value="17:00:00">17:00:00</option>
                <option value="18:00:00">18:00:00</option>
                <option value="19:00:00">19:00:00</option>
                <option value="20:00:00">20:00:00</option>
                <option value="21:00:00">21:00:00</option>
                <option value="22:00:00">22:00:00</option>
                <option value="23:00:00">23:00:00</option>
                <option value="24:00:00">24:00:00</option>
                </select>
             </td>
       </tr>

       <tr>
           <td colspan="2">
           	<input type="button" name="submit" value="Add Open Policy" id="submitbutton">
           	<input type="button" name="back" value="Go Back" id="cancelbutton">
           </td>
    </tr>
 </table>

</table>
</form>

<script language="JavaScript">
              getDaysForMonth();
       </script>


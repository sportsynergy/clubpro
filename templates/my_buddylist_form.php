

<script language="Javascript">

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("submitbutton", { value: "submitbuttonvalue" });
        oSubmitButton1.on("click", onSubmitButtonClicked);

    });

} ();


function onSubmitButtonClicked(){
	submitForm('entryform');
}


 
</script>

<?
//Set the http variables
$action = $_REQUEST["action"];
$bid = $_REQUEST["bid"];

 if(isset($action) && $action=="remove"){

   //Remove Buddy
   $qid1 = db_query("DELETE FROM tblBuddies
                     WHERE bid = $bid");
 }
  ?>



<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this)" autocomplete="off">

<table width="550" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
     <tr class="borderow">
         <td class=clubid<?=get_clubid()?>th>
         	<span class="whiteh1">
         		<div align="center"><? pv($DOC_TITLE) ?></div>
         	</span>
         </td>
    </tr>

	 <tr>
	 <td>

      <table width="550" cellspacing="5" cellpadding="0" class="borderless">
      <tr>
        <td class="label">Add A Buddy:</td>
	        <td>
			        <input id="name" name="name" type="text" size="30" class="form-autocomplete" />
			             <input id="id" name="buddy" type="hidden" size="30" />
			    			<script>
			                <?
			                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
			                 pat_autocomplete( array(
									'baseUrl'=> "$wwwroot/users/ajaxServer.php",
									'source'=>'name',
									'target'=>'id',
									'className'=>'autocomplete',
									'parameters'=> "action=autocomplete&name={name}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
									'progressStyle'=>'throbbing',
									'minimumCharacters'=>3,
									));
			           
			                 ?>
			
			                </script>
	        </td>
        <td><input type="button" name="submit" value="Submit" id="submitbutton"></td>
      </tr>


     <tr>
      <td colspan="3">
          <hr>
      </td>
     </tr>
     
      <?
       //List out all of the players Buddies
       $query = "SELECT buddies.bid, users.firstname, users.lastname, users.userid
                 FROM tblUsers users, tblClubUser clubuser, tblBuddies buddies
                 WHERE users.userid = buddies.buddyid
				 AND clubuser.userid = users.userid
                 AND  buddies.userid=".get_userid()."
				 AND clubuser.enddate IS NULL
                 AND clubuser.enable = 'y'
                 AND clubuser.clubid = ".get_clubid();

       // run the query on the database
       $result = db_query($query); 
       
        if( isDebugEnabled(1) ) logMessage("my_buddylist_form: found ". mysql_num_rows($result). "buddies");
        
        
        if( mysql_num_rows($result) == 0 ){ ?>
        	<tr>
        	<td colspan="2">
        	You don't have any buddies.  Why don't add one now.
        	</td>
        	</tr>
       <? }  else { ?>
				       
     <tr>
         <td colspan="3">
               <table width="450" cellpadding="0" cellspacing="0">
				    
				
				       <tr>
				       <td></td>
				              <?
				              $sportsResult = load_registered_sports(get_userid());
				               while($sportRow = mysql_fetch_array($sportsResult)){  ?>
				                   <? if($sportRow['reservationtype']<2){?>
				                          <td align="center"><span class="medbold"><?=$sportRow['courttypename']?></span></td>
				                   <? } ?>
				             <?  } ?>
				             <td></td>
				       </tr>
				      <?
				       $rownum = mysql_num_rows($result);
				       while($row = mysql_fetch_row($result)) { 
				        $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "lightrow" : "darkrow";
				       	?>
				
				            <tr class="<?=$rc?>">
				              <td width="100">
				              	<span class="normal">
				
					              <?=$row[1]?>&nbsp;<?=$row[2]?></span></td>
					              <?
					                mysql_data_seek($sportsResult,0);
					                while($sportRow = mysql_fetch_array($sportsResult)){
					                        $historyArray = get_record_history(get_userid(),$row[3], $sportRow['courttypeid']);
					                        if($sportRow['reservationtype']<2){
					                        ?>
					                          <td align="center">
					                          	<span class="normal">
					                          		<? print "$historyArray[0] - $historyArray[1] ($historyArray[2]%)";?>
					                          	</span>
					                          </td>
					                        <?  } ?>
					
					               <? } ?>
				              <td width="70" > 	
				              	<a href="my_buddylist.php?action=remove&bid=<?=$row[0]?>">
				              		Remove
				              	</a> 
				              </td>
				             </tr>
				
				       <?
							$rownum = $rownum - 1;
				       } ?>
				
						     
					   
				
				     </table>

			   </td>
			 </tr>
			 
		
			  <tr>
				<td colspan="3">
					<span style="color: red;">*</span><span class=normalsm> <span style="font-weight: bold;">Key:</span> Matches I Have Won - Matches My Buddy Has Won (My Winning Percentage)</span>
				</td>
			</tr>
			
		 <? } ?>
				 
		</table>
		

	</td>
	</tr>
	
	</table>


</form>

<?   if( mysql_num_rows($result) > 0 ){  ?>
<div style="height: 2em;"></div>
<div>

      <?
      //Get the email addresses of the players to put into the mailto
	    $emailArray = getBuddyEmailAddresses(get_userid());
	    $emailString = implode(",", $emailArray);
    
     ?>
	<span> <a href="mailto:<?=$emailString?>">Send an email to these schmucks</a> </span>
	</div>
<? } ?>

      

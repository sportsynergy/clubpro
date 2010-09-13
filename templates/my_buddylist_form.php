<?php
  /*
 * $LastChangedRevision: 774 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-09-11 11:30:35 -0500 (Sat, 11 Sep 2010) $

*/
?>
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


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">
<tr>
<td>

<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this)" autocomplete="off">

<table width="550" cellpadding="20" cellspacing="0">
     <tr>
         <td class=clubid<?=get_clubid()?>th><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
    </tr>

 <tr>
    <td class="generictable">



      <table width="550" cellspacing="5" cellpadding="0" >
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
        <td><input type="submit" name="submit" value="Submit"></td>
      </tr>


     <tr>
      <td colspan="3">
          <hr>
      </td>
     </tr>
     
     <tr>
         <td colspan="3">
               <table width="450" cellpadding="1" cellspacing="1">
     <?
       //List out all of the players Buddies
       $query = "SELECT buddies.bid, users.firstname, users.lastname, users.userid
                 FROM tblUsers users, tblClubUser clubuser, tblBuddies buddies
                 WHERE users.userid = buddies.buddyid
				 AND clubuser.userid = users.userid
                 AND  buddies.userid=".get_userid()."
				 AND clubuser.enddate IS NULL
                 AND clubuser.enable = 'y'";

       // run the query on the database
       $result = db_query($query); 
       
        if( isDebugEnabled(1) ) logMessage("my_buddylist_form: found ". mysql_num_rows($result). "buddies");
        
       ?>

       <tr>
       <td></td>
              <?
              $sportsResult = load_registered_sports(get_userid());
               while($sportRow = mysql_fetch_array($sportsResult)){  ?>
                   <? if($sportRow['reservationtype']<2){?>
                          <td align="center"><font class="smallbold"><?=$sportRow['courttypename']?></font></td>
                   <? } ?>
             <?  } ?>
             <td></td>
       </tr>
      <?
       while($row = mysql_fetch_row($result)) { ?>

            <tr>
              <td width="100"><font class="normalsm">

              <?=$row[1]?>&nbsp;<?=$row[2]?></font></td>
              <?
                mysql_data_seek($sportsResult,0);
                while($sportRow = mysql_fetch_array($sportsResult)){
                        $historyArray = get_record_history(get_userid(),$row[3], $sportRow['courttypeid']);
                        if($sportRow['reservationtype']<2){
                        ?>
                          <td align="center"><font class="normalsm"><? print "$historyArray[0] - $historyArray[1] ($historyArray[2]%)";?></font></td>
                        <?  } ?>

               <? } ?>
              <td width="70" align="right"> <a href="my_buddylist.php?action=remove&bid=<?=$row[0]?>"><font class="normalsm">Remove</font></a> </td>
              </tr>

       <? } ?>

		     
	     <tr>
	     	<?
	     	//Get the email addresses of the players to put into the mailto
		    $emailArray = getBuddyEmailAddresses(get_userid());
		    $emailString = implode(",", $emailArray);
		    
	     	?>
	     	<td colspan="2" class="normalsm" align="left"><br/><a href="mailto:<?=$emailString?>">Send an email to these schmucks</a></td>
	     </tr>

     </table>

   </td>
 </tr>

</table>
</form>

</td>
</tr>
<tr>
      <td align="right">
          <font color="red">*</font><font class=normalsm> <b>Key:</b> Matches I Have Won - Matches My Buddy Has Won (My Winning Percentage)</font>
      </td>
 </tr>
</table>
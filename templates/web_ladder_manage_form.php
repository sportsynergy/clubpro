<script language="Javascript">

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
<?

/*
 * $LastChangedRevision: 650 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-01-12 17:10:42 -0800 (Mon, 12 Jan 2009) $

*/

//Set the http variables
$action = $_REQUEST["action"];
$boxid = $_REQUEST["boxid"];
$userid = $_REQUEST["userid"];

 if($action=="remove"){
   // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxplace
                             FROM tblkpBoxLeagues
                             WHERE boxid = $boxid
                             AND userid = $userid ");

   $boxplaceval = mysql_result($oldboxplaceresult, 0);


   //Remove Buddy
   $qid1 = db_query("DELETE FROM tblkpBoxLeagues
                     WHERE boxid = $boxid
                     AND userid = $userid");


   // Now adjust all players who were ranked below this player

   $adjustquery = "SELECT tblkpBoxLeagues.userid, tblkpBoxLeagues.boxplace
                   FROM tblkpBoxLeagues
                   WHERE boxid = $boxid
                   AND (((tblkpBoxLeagues.boxplace)>$boxplaceval))
                   ORDER BY tblkpBoxLeagues.boxplace";


    // run the query on the database
    $adjustresult = db_query($adjustquery);

   //Update the rankings
   while ($adjustobj = db_fetch_object($adjustresult)) {

       $newboxplaceval = $adjustobj->boxplace - 1;

       $qid = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$newboxplaceval'
        WHERE userid = '$adjustobj->userid'
        AND boxid = $boxid");


   }
  }

  if($action=="moveup"){

    // Get the box place of the guy who is being removed.
    $oldboxplaceresult = db_query("SELECT boxplace
                             FROM tblkpBoxLeagues
                             WHERE boxid = $boxid
                             AND userid = $userid ");


   $boxplaceval = mysql_result($oldboxplaceresult, 0);

   // Get around the problem of moving up a play who is in first
   if ( $boxplaceval > 1){

   $newboxplaceval = $boxplaceval - 1;



        // MOve the player ahead down
        $qid1 = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$boxplaceval'
        WHERE boxplace = '$newboxplaceval'
        AND boxid = $boxid");


       // MOve the player up
       $qid2 = db_query("
        UPDATE tblkpBoxLeagues
        SET boxplace = '$newboxplaceval'
        WHERE userid = '$userid'
        AND boxid = $boxid");

  }
 }



  ?>


<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

<tr>
<td>
<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<table width="500" cellpadding="20" cellspacing="0">
     <tr>
         <td class=clubid<?=get_clubid()?>th><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
    </tr>

 <tr>
    <td class=generictable>



      <table width="350" cellspacing="5" cellpadding="0" >
      <tr>

        <td class="label">Add A User:</td>
        <td>
            
 				<input id="name1" name="name1" type="text" size="30" class="form-autocomplete" />
                <input id="id1" name="boxuser" type="hidden" />
    			<script>
                <?
                 $wwwroot = $_SESSION["CFG"]["wwwroot"];
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'name1',
						'target'=>'id1',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&courttype=$courttype&siteid=".get_siteid()."&clubid=".get_clubid()."",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
           
                 ?>

                </script>
        </td>

       <tr>
            <td></td>
            <td>
            	<input type="hidden" name="boxid" value="<?=$boxid ?>">
            	
            	
            	<input type="hidden" name="courttype" value="<?=$courttype?>">
               
                </td>
       </tr>
       <tr>
			<td colspan="2" class="normal">
			To search for a name type in the players first or last name. <br><br>
			</td>
		</tr>
     <tr>
      <td><input type="submit" value="Submit" name="submit"></td>
      <td></form> </td>
     </tr>
     <tr>
      <td colspan="2">
          <hr>
      </td>

     </tr>
     <?
       //List out all of the players in the box league

       $query = "SELECT tblUsers.firstname, tblUsers.lastname, tblUsers.userid, tblkpBoxLeagues.boxplace, tblkpBoxLeagues.games
                 FROM tblUsers INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                 WHERE (((tblkpBoxLeagues.boxid)=$boxid))
                 ORDER BY tblkpBoxLeagues.boxplace";


       // run the query on the database
       $result = db_query($query);

       echo "<table width=\"350\">\n";
       echo "<tr>\n";
       echo "<td align=\"center\"><font class=\"smallbold\">Place</font></td>\n";
       echo "<td><font class=\"smallbold\">Player</font></td>\n";
       echo "<td align=\"center\"><font class=\"smallbold\">Games Played</font></td>\n";
       echo "<td></td>\n";
       echo "<td></td>\n";
       echo "</tr>\n";
       while($row = mysql_fetch_row($result)) {

         echo "<tr>\n";
         echo "<td align=\"center\"><font class=\"normalsm\">$row[3]</font></td>\n";
         echo "<td><font class=\"normalsm\">$row[0] $row[1]</font></td>\n";
         echo "<td align=\"center\"><font class=\"normalsm\">$row[4]</font></td>\n";
         echo "<td><a href=\"web_ladder_manage.php?action=remove&boxid=$boxid&userid=$row[2]\"><font class=\"normalsm\">Remove</font></a></td>";
         echo "<td><a href=\"web_ladder_manage.php?action=moveup&boxid=$boxid&userid=$row[2]\"><font class=\"normalsm\">Moveup</a></font></td>";
         echo "</tr>\n";
         }


      ?>

     </table>


   </td>
</tr>
</tr>
	
<tr>
 <td align="right" colspan="2">
 <br>
 <font class=normal> <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php" > << Back to web ladder registration page </a> </font>

 </td>
</tr>
</table>



</td>
</tr>
</table>
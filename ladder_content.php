<?
/*
 * $LastChangedRevision: 763 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2010-03-16 12:37:46 -0700 (Tue, 16 Mar 2010) $

*/

 //Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"] = getSitePreferences($siteid);

//Set the footer message
if( !isset($_SESSION["footermessage"]) ){
	$footerMessage = getFooterMessage();
	$_SESSION["footermessage"] = $footerMessage;
}

//Display the multiuser login form
if(isset($username) && isset($password) && !is_logged_in() ){
	$usersResult = getAllUsersWithIdResult($username, $clubid);
	if( mysql_num_rows($usersResult) > 1  ){
        	 include($_SESSION["CFG"]["templatedir"]."/pick_user_form.php"); 
        	 die;
     }else{
	$user = verify_login($username, $password, false);
		if($user){
			$_SESSION["user"] = $user;
			set_view();
    	}else{
    		print "bad login: $username/$password";
    	}
    	
	}
}
	
//Get user log in the user in from the multiuser login form
 if( isset($_POST["frompickform"] ) ){
    	$user = load_user($_POST["userid"] );
    	if($user){
			$_SESSION["user"] = $user;
			set_view();
    	}
  }


$DOC_TITLE = "Sportsynergy Box League";
include($_SESSION["CFG"]["templatedir"]."/header.php");

if ($clubid){

//Get all of the web ladders for the club

$getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.enable
                      FROM tblBoxLeagues
                      WHERE (((tblBoxLeagues.siteid)=$siteid))
                      ORDER BY tblBoxLeagues.boxrank";



$getwebladdersresult = db_query($getwebladdersquery);
$imagedir = $_SESSION["CFG"]["imagedir"];

echo "<table width=\"710\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\n";

echo "<tr>";
echo "<td align=\"right\" colspan=\"2\">";

echo "<table cellspacing=\"0\" cellpadding=\"0\" align=\"right\">";
echo "<tr>";
echo "<td valign=\"middle\"><font class=\"normalsm\"><A HREF=\"javascript:newWindow('../../help/box_leagues.html')\">Box Leagues Explained</a></font></td>";
echo "<td><img src=\"$imagedir/help.jpg\"></td>";
echo "</tr>";
echo "</table>";
echo "<br><br>";
echo "</td>";
echo "</tr>";


$resultcounter = 0;

while ($wlobj = db_fetch_object($getwebladdersresult)) {
$datestring = explode("-",$wlobj->enddate);
       if ($resultcounter==0){
               echo "<tr valign=\"top\">\n";
           }
      echo "\t<td width=350  nowrap>\n";

              echo "\t\t<table width=350 border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bordercolor=\"Gray\">\n";
              echo "\t\t<tr valign=\"top\">\n";
              echo "\t\t\t<th class=clubid".$clubid."th colspan=\"4\"><font class=whiteh1><div align=\"center\">$wlobj->boxname</div></font></th>\n";
              echo "\t\t</tr>\n";
              echo "\t\t<tr>\n";
              echo "\t\t\t<td class=clubid".$clubid."th colspan=\"4\"><font class=whitenorm><div align=\"center\">End Date: $datestring[1].$datestring[2].$datestring[0]</div></font></td>\n";
              echo "\t\t</tr>\n";
              echo "\t\t<tr>\n";

              echo "\t\t\t<td colspan=\"4\">\n";
    		  echo "\t\t\t\t<font class=\"normalsm\">&nbsp</font>\n";
              //}
              echo "\t\t\t</td>\n";
              echo "\t\t</tr>\n";

              echo "\t\t<tr>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t</tr>\n";

              echo "\t\t<tr align=\"center\">\n";
              echo "\t\t\t<td><font class=\"smallbold \">Place</font></td>\n";
              echo "\t\t\t<td colspan=\"2\"><font class=\"smallbold \">Player</font></td>\n";
              echo "\t\t\t<td><font class=\"smallbold \">Points</font></td>\n";
              echo "\t\t</tr>\n";

              echo "\t\t<tr>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t</tr>\n";

               // Now list the players in the ladder
               $webladderuserquery = "SELECT tblkpBoxLeagues.boxplace,tblUsers.firstname, tblUsers.lastname, tblkpBoxLeagues.score
                                      FROM tblUsers
                                      INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                                      WHERE (((tblkpBoxLeagues.boxid)=$wlobj->boxid))
                                      ORDER BY tblkpBoxLeagues.score DESC";

                $webladderuserresult = db_query($webladderuserquery);
                //Set the place variable
                $n=1;
                while ($wluserobj = db_fetch_object($webladderuserresult)) {

                        echo "\t\t<tr align=\"center\">\n";
                        echo "\t\t\t<td><font class=\"normalsm\">$n.</font></td>\n";

                        echo "\t\t\t<td><font class=\"normalsm\">$wluserobj->firstname</font></td>\n";

                        echo "\t\t\t<td><font class=\"normalsm\">$wluserobj->lastname</font></td>\n";

                        echo "\t\t\t<td><font class=\"normalsm\">$wluserobj->score</font></td>\n";

                        echo "\t\t</tr>\n";
                        $n++;

                }


               echo "\t\t<tr>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t\t<td>&nbsp</td>\n";
              echo "\t\t</tr>\n";
              echo "\t\t</table>\n";

      echo "</td>\n";

       if ($resultcounter==2){
               echo "</tr>\n";
           }

       ++$resultcounter;

       //Reset the result counter
       if ($resultcounter==2){
         $resultcounter = 0;

       }
      }
    if ($resultcounter<2){
               echo "</tr>\n";
           }



echo "</table>";

}

include($_SESSION["CFG"]["templatedir"]."/footer.php");
?>
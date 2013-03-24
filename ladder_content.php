<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */

/**
* Class and Function List:
* Function list:
* Classes list:
*/
/*
 * $LastChangedRevision: 856 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 13:29:36 -0500 (Mon, 14 Mar 2011) $
*/

//Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"] = getSitePreferences($siteid);

//Set the footer message

if (!isset($_SESSION["footermessage"])) {

    $footerMessage = getFooterMessage();
    $_SESSION["footermessage"] = $footerMessage;
}

//Display the multiuser login form

if (isset($username) && isset($password) && !is_logged_in()) {

    $usersResult = getAllUsersWithIdResult($username, $clubid);
    
    if (mysql_num_rows($usersResult) > 1) {

        include ($_SESSION["CFG"]["templatedir"] . "/pick_user_form.php");
        die;
    } else {
        $user = verify_login($username, $password, false);
        
        if ($user) {

            $_SESSION["user"] = $user;
        } else {
            print "bad login: $username/$password";
        }
    }
}

//Get user log in the user in from the multiuser login form

if (isset($_POST["frompickform"])) {

    $user = load_user($_POST["userid"]);
    
    if ($user) {

        $_SESSION["user"] = $user;
    }
}
$DOC_TITLE = "Sportsynergy Box Leagues";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");

if ($clubid) {


    //Get all of the web ladders for the club
    $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.enable
                      FROM tblBoxLeagues
                      WHERE (((tblBoxLeagues.siteid)=$siteid))
                      ORDER BY tblBoxLeagues.boxrank";
    $getwebladdersresult = db_query($getwebladdersquery);
?>

<table width="710" cellspacing="0" cellpadding="0" align="center" class="borderless">

<tr>
<td align="right" colspan="2">


		<span class="normal">
			<a href="javascript:newWindow('../../help/box_leagues.html')">Box Leagues Explained</a>
		</span>

	
	<br><br>
	</td>
	</tr>

<?

$resultcounter = 0;

while ($wlobj = db_fetch_object($getwebladdersresult)) {
$datestring = explode("-",$wlobj->enddate);
       if ($resultcounter==0){ ?>
               <tr valign="top">
          <? } ?> 
           
           
      <td width="350"  nowrap>

              <table width="350" cellpadding="0" cellspacing="0" class="bordertable">
              	<tr valign="top">
              		<td class=clubid<?=get_clubid()?>th colspan="3">
              			<span class="whiteh1"><div align="center"><?=$wlobj->boxname?></div></span>
              		</td>
               	</tr>
               	<tr>
              		<td class=clubid<?=get_clubid()?>th colspan="3">
              			<span class="whitenorm">
              				<div align="center">End Date: <?=$datestring[1]."-".$datestring[2]."-".$datestring[0]?></div>
              	</span>
              	<div style="height: .5em"></div>
              	</td>
              </tr>
              
             
              

              <tr align="center" class=clubid<?=get_clubid()?>th>
	              	<td>
	              		<span class="whitenorm">Place</span>
	              	</td>
	              	<td>
	              		<span class="whitenorm">Player</span>
	              	</td>
	              	<td>
	              		<span class="whitenorm">Points</span>
	              	</td>
              </tr>

             

<?
               // Now list the players in the ladder
               $webladderuserquery = "SELECT tblkpBoxLeagues.boxplace,
										tblUsers.firstname, 
										tblUsers.lastname, 
										tblUsers.email, 
										tblkpBoxLeagues.score,
										tblUsers.userid,
										tblkpBoxLeagues.boxid
                                      FROM tblUsers
                                      INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                                      WHERE tblkpBoxLeagues.boxid=$wlobj->boxid
                                      ORDER BY tblkpBoxLeagues.score DESC, tblUsers.lastname";

                $webladderuserresult = db_query($webladderuserquery);
                //Set the place variable
                $n=1;
                
                $rownum = mysql_num_rows($webladderuserresult);
                while ($wluserobj = db_fetch_object($webladderuserresult)) {

                	 $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                	 
                	?>
                        <tr align="center" class="<?=$rc?>">
	                        <td>
	                        	<span class="normal"><?=$n?></span>
	                        </td>
	                        <td>
	                        	<span class="normal"><a href="mailto:<?=$wluserobj->email?>"><?=$wluserobj->firstname?> <?=$wluserobj->lastname?></a></span>
	                        </td>
	                       
	                        <td>
	                        	<span class="normal">
									<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$wluserobj->boxid?>&userid=<?=$wluserobj->userid?>" title="view history">
									<?=$wluserobj->score?></span>
									</a>
	                        </td>

                        </tr>
                     <?
                        $n++;
						$rownum = $rownum - 1;
                }

				?>
               		<!-- Space things out -->
               		
              </table>
			<div style="height: 1em"></div>
      </td>
<?
       if ($resultcounter==2){
               echo "</tr>";
           }

       ++$resultcounter;

       //Reset the result counter
       if ($resultcounter==2){
         $resultcounter = 0;

       }
      }
    if ($resultcounter<2){
               echo "</tr>";
           }



echo "</table>";

}

include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
?>
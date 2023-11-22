<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * 
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

//Load necessary libraries and set the wanturl to get back here.
$_SESSION["wantsurl"] = qualified_mewithq();
$_SESSION["siteprefs"] = getSitePreferences($siteid);

if (isRequireLogin()) require_login();

//Set the footer message

if (!isset($_SESSION["footermessage"])) {

    $footerMessage = getFooterMessage();
    $_SESSION["footermessage"] = $footerMessage;
}

//Display the multiuser login form

if (isset($username) && isset($password) && !is_logged_in()) {

    $usersResult = getAllUsersWithIdResult($username, $clubid);
    
    if (mysqli_num_rows($usersResult) > 1) {

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
    $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.enable, tCSL.leaguesUpdated AS lastupdated
                      FROM tblBoxLeagues
                      INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                      WHERE tblBoxLeagues.siteid=$siteid
                      ORDER BY tblBoxLeagues.boxrank";
    $getwebladdersresult = db_query($getwebladdersquery);
?>

<table width="710" cellspacing="0" cellpadding="0" align="center" class="borderless">

<tr>
<td align="right" colspan="2">

        <? if (isLadderRankingScheme() ) { ?>
		<span class="normal">
			<a href="javascript:newWindow('../../help/box_leagues.html')">Box Leagues Explained</a>
		</span>

        <? } ?>

	
	<br><br>
	</td>
	</tr>

<?

$resultcounter = 0;
$playercounter = 0;

// hacky thing to get tables to look right
if ( isJumpLadderRankingScheme() ){
    $colspan = 4;
} else {
    $colspan = 3;
}

while ($wlobj = db_fetch_object($getwebladdersresult)) {
$datestring = explode("-",$wlobj->enddate);
$lastupdatestring = $wlobj->lastupdated;
       if ($resultcounter==0){ ?>
               <tr valign="top">
          <? }     
    ?> 
            
      <td width="350"  nowrap>

              <table width="350" cellpadding="0" cellspacing="0" class="bordertable">
              	<tr valign="top">
              		<td class=clubid<?=get_clubid()?>th colspan="<?=$colspan?>">
              			<span class="whiteh1"><div align="center">
                            <?=$wlobj->boxname?></div>
                        </span>
              		</td>
               	</tr>
               	<tr>
              		<td class=clubid<?=get_clubid()?>th colspan="<?=$colspan?>">
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
                    <? if( isJumpLadderRankingScheme() ) { ?>
                      <td>
	              		<span class="whitenorm">Games Won</span>
	              	</td>
                    <?  } ?>

              </tr>
        
<?
               // Now list the players in the ladder
               $webladderuserquery = "SELECT tblkpBoxLeagues.boxplace,
										tblUsers.firstname, 
										tblUsers.lastname, 
										tblUsers.email, 
										tblkpBoxLeagues.score,
                                        tblkpBoxLeagues.gameswon,
										tblUsers.userid,
										tblkpBoxLeagues.boxid
                                      FROM tblUsers
                                      INNER JOIN tblkpBoxLeagues ON tblUsers.userid = tblkpBoxLeagues.userid
                                      WHERE tblkpBoxLeagues.boxid=$wlobj->boxid
                                      ORDER BY tblkpBoxLeagues.score DESC, tblkpBoxLeagues.gameswon DESC, tblUsers.lastname";

                $webladderuserresult = db_query($webladderuserquery);
                //Set the place variable
                $n=1;
                
                $rownum = mysqli_num_rows($webladderuserresult);
                while ($wluserobj = db_fetch_object($webladderuserresult)) {

                	 $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                	 
                	?>
                        <tr align="center" class="<?=$rc?>">
	                        <td>
	                        	<span class="normal"><?=$n?></span>
	                        </td>
	                        <td>
                            <form name="playerform<?=$playercounter?>" method="get"
								action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php">
								<input type="hidden" name="userid"
									value="<?=$wluserobj->userid?>"> 
                                    <input type="hidden" name="origin" value="league">
							</form>
	                        	<span class="normal">
                                    <a href="javascript:submitForm('playerform<?=$playercounter?>')">
                                    <?=$wluserobj->firstname?> <?=$wluserobj->lastname?>
                                    </a>
                                    
                                    </span>
	                        </td>
	                       
	                        <td>
	                        	<span class="normal">
                                    <? if ( isLadderRankingScheme() ) { ?>
									<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/web_ladder_history.php?boxid=<?=$wluserobj->boxid?>&userid=<?=$wluserobj->userid?>" title="view history">
									<? } ?>
                                    <?=$wluserobj->score?></span>
									</a>
	                        </td>
                            <td>
                                    <span class="normal">
                                    <?=$wluserobj->gameswon?></span>
                                    </span>
                            </td>
                                

                        </tr>
                     <?
                        $n++;
						$rownum = $rownum - 1;
                        ++$playercounter;
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

if( isJumpLadderRankingScheme() ){
?>

<div style="margin-top: 20px"> <span class="smallbold">Box leagues last updated:</span> 
  <?php
    
    if( is_null($lastupdatestring) ){
      $lastupdated = "Never";
    } else {
      //$lastupdated = ladderdetails['lastUpdated'];
      $lastupdated = $lastupdatestring;
    } ?>

  <span class="smallreg">
  <?=$lastupdated?>
  </span>


  </div>

<?
}
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
?>
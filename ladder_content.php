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

$DOC_TITLE = "Sportsynergy Box Leagues";
include ($_SESSION["CFG"]["templatedir"] . "/header_yui.php");

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

if ($siteid) {

    
    // filter on ladder if this is passed in
    if (isset($_POST["ladderid"])) {

        $ladderid = $_POST["ladderid"];

        //Get all of the web ladders for the club
        $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.enable, tCSL.name as ladder_name, tCSL.leaguesUpdated AS lastupdated
                        FROM tblBoxLeagues
                        INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                        WHERE tblBoxLeagues.siteid=$siteid
                        AND tCSL.id = $ladderid
                        ORDER BY tblBoxLeagues.boxrank";

    } else {

        //Get all of the web ladders for the club
        $getwebladdersquery = "SELECT tblBoxLeagues.boxid, tblBoxLeagues.boxname, tblBoxLeagues.enddate, tblBoxLeagues.enable, tCSL.name as ladder_name, tCSL.leaguesUpdated AS lastupdated
                        FROM tblBoxLeagues
                        INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                        WHERE tblBoxLeagues.siteid=$siteid
                        ORDER BY tblBoxLeagues.boxrank";
    }

    $getwebladdersresult = db_query($getwebladdersquery);

    $getleaguesquery = "SELECT DISTINCT tCSL.name as ladder_name, tCSL.id
                            FROM tblBoxLeagues
                            INNER JOIN tblClubSiteLadders tCSL ON tblBoxLeagues.ladderid = tCSL.id
                            WHERE tblBoxLeagues.siteid=$siteid";
    $getleagueresult = db_query($getleaguesquery);

?>

<table width="710" cellspacing="0" cellpadding="0" align="center" class="borderless">

<tr>
<td align="right" colspan="2">

        <? if (isLadderRankingScheme() ) { ?>
		<span class="normal">
			<a href="javascript:newWindow('../../help/box_leagues.html')">Box Leagues Explained</a>
		</span>

        <? } ?>

	</td>
	</tr>

    <? if (mysqli_num_rows($getleagueresult)>0) { ?>
        <tr>
            <td align="right" colspan="2">
            <span class="normal" id="showreportscoresplayer" >
                <a style="text-decoration: underline; cursor: pointer">Record Score</a> |
            </span> 
            <a href="">All Ladders</a> | 
            <? 
            // Create the links to filter
            $counter = 0;
            while ($leagueobj = db_fetch_object($getleagueresult)) {
                
                if($counter>0){ ?>
                    |
               <? } ?>
            <a href="javascript:submitLeagueForm(<?=$leagueobj->id?>)"><?=$leagueobj->ladder_name?></a>
            <?  
               $counter++ ;
             } ?>
            <div style="height: 1em"></div>
            </td>
        </tr>
    <? } ?>
   
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
  <form name="league_form" method="POST" action="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">
  <input type="hidden" name="ladderid">
</form>

<div id="reportscoredialogplayer" class="yui-pe-content">

<div class="bd">
		<form method="POST" action="<?=$ME?>" autocomplete="off">
			

		 <div style="margin:10px"> 
			<span class="label">Outcome:</span>
			<select name="outcome">
					<option value="defeated">Won</option>
					<option value="lostto">Lost</option>
				</select>
			</div>

			<div style="margin:10px"> 
			<span class="label">Opponent:</span>
				<input id="rsname3" name="" type="text" size="30"
					class="form-autocomplete" autocomplete="off"/> 
					<input id="rsid3" name="rsuserid3" type="hidden" />

				<script>
                <?
                $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
                 pat_autocomplete( array(
						'baseUrl'=> "$wwwroot/users/ajaxServer.php",
						'source'=>'rsname3',
						'target'=>'rsid3',
						'className'=>'autocomplete',
						'parameters'=> "action=autocomplete&name={rsname3}&userid=".get_userid()."&ladderid=$ladderid",
						'progressStyle'=>'throbbing',
						'minimumCharacters'=>3,
						));
                 ?>

                </script>
			</div>

			<div style="margin:10px"> 
			<span class="label">Score:</span>
			<select name="score">
					<option value="3-2">3-2</option>
					<option value="3-1" selected>3-1</option>
					<option value="2-1" selected>2-1</option>
					<option value="3-0" selected>3-0</option>
				</select>
			</div>

			<div style="margin:10px"> 
			<span class="label"> Time </span>
				<select name="hourplayed">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8" selected>8</option>
					<option value="9">9</option>
					<option value="10">10</option>
					<option value="11">11</option>
					<option value="00">12</option>
				</select>

				<select name="minuteofday">
					<option value="00">00</option>
					<option value="15">15</option>
					<option value="30">30</option>
					<option value="45">45</option>
				</select>

				<select name="timeofday">
					<option value="AM">AM</option>
					<option value="PM" selected>PM</option>
				</select>
			</div>

		
				<input type="hidden" name="cmd" value="reportladderscore">
		</form>

</div>
	
</div>

  <script type="text/javascript" >

    function submitLeagueForm( ladderid){
        
    document.league_form.ladderid.value = ladderid;
    document.league_form.submit();
    }

    var allownewlines = false;
    

    <? 
        // hide the report score link if ladderid isn't set
        if( !isset($ladderid)) { ?>
        var moresection = document.getElementById('showreportscoresplayer');
        moresection.style.display = "none";
    <? } ?>

    /*
    * Report score dialoge
    */
    YAHOO.namespace("clubladder.container");
    YAHOO.util.Event.onDOMReady(function () {
        
        
        // Define various event handlers for Dialog
        var handleSubmit = function() {
            this.submit();
        };
        var handleCancel = function() {
            this.cancel();
        };
        var handleSuccess = function(o) {
            window.location.href=window.location.href;
        };
    
        var handleFailure = function(o) {
            alert("Submission failed: " + o.status);
        };
    
        // Remove progressively enhanced content class, just before creating the module
        YAHOO.util.Dom.removeClass("reportscoredialogplayer", "yui-pe-content");
    
        // Instantiate the Dialog

        YAHOO.clubladder.container.reportscoredialogplayer = new YAHOO.widget.Dialog("reportscoredialogplayer", 
                            { width : "30em",
                                fixedcenter : true,
                                modal: true,
                                visible : false, 
                                constraintoviewport : true,
                                buttons : [ { text:"Record Score", handler:handleSubmit, isDefault:true } ]
                            });
    

        YAHOO.clubladder.container.reportscoredialogplayer.setHeader('Record Score');
    
 
    
        // Wire up the success and failure handlers
        YAHOO.clubladder.container.reportscoredialogplayer.callback = { success: handleSuccess,
                                failure: handleFailure };
        
        // Render the Dialog
        YAHOO.clubladder.container.reportscoredialogplayer.render();
        YAHOO.util.Event.addListener("showreportscoresplayer", "click", YAHOO.clubladder.container.reportscoredialogplayer.show, YAHOO.clubladder.container.reportscoredialogplayer, true);
        YAHOO.util.Event.addListener("showreportscoresplayer", "click", disablenewlines, false, true);
    });

</script> 


<?
}
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
?>
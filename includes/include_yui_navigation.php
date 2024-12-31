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
/**
* Class and Function List:
* Function list:
* Classes list:
*/
?>
<div id="productsandservices" class="yuimenubar yuimenubarnav">
  <div class="bd">
    <ul class="first-of-type">
      <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">Directory</a></li>
      <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/">Bookings</a></li>
      <? if(isLadderRankingScheme() || isJumpLadderRankingScheme() ) {?>
      <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Club Rankings</a>
        <div id="ladder" class="yuimenu">
          <div class="bd">
            <ul class="first-of-type">
              <? for ($i=0; $i < count($_SESSION["ladders"]); ++$i) {?>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="javascript:submitLadderForm('<?=$_SESSION["ladders"][$i]['courttypeid']?>','<?=$_SESSION["ladders"][$i]['id']?>')">
                <?=$_SESSION["ladders"][$i]['name']?>
                </a></li>
              <? } ?>
            </ul>
          </div>
        </div>
      </li>
      <? } if(isPointRankingScheme()  )  { ?>
      <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Rankings</a></li>
      <? }?>
      <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php">Players Wanted</a></li>
      <? if( isSiteBoxLeageEnabled() ){ ?>
        
        <? if( isJumpLadderRankingScheme() ){ ?>
          <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Leagues</a>
          <div id="account" class="yuimenu">
          <div class="bd">
            <ul class="first-of-type">
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">Box Leagues</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/club_teams.php">Club Teams</a></li>
            </ul>
          <? } else {?>
            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">Leagues</a></li>
          <? } ?>
        <? } ?>
      <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">My Account</a>
        <div id="account" class="yuimenu">
          <div class="bd">
            <ul class="first-of-type">
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_settings.php">Edit Account</a></li>
              <? if(! isSiteAutoLogin() ) {?>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php">Change Password</a></li>
              <? } ?>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_buddylist.php">My Buddies</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_reservations.php">My Reservations</a></li>
            </ul>
          </div>
        </div>
      </li>


      <?php if( get_roleid() == 2 ) { ?>
      <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Tools</a>
        <div id="administration" class="yuimenu">
          <div class="bd">
            <ul>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/user_registration.php">Add Player</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">Account Maintenance</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">Email Players</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php">Box League Setup</a></li>
               <? if( isJumpLadderRankingScheme() ) { ?>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_teams.php">Club Teams</a></li>
              <? } ?>

              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php">Club Preferences</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/report_scores.php">Report Scores</a></li>
             
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php">Club Events</a></li>
              <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_reports.php">Club Reports</a></li>
            </ul>
          </div>
        </div>
      </li>
      <?php } ?>
    </ul>
  </div>
</div>
<form name="ladder_form" method="POST" >
  <input type="hidden" name="courttypeid">
  <input type="hidden" name="ladderid">
</form>
<script type="text/javascript" >

function submitLadderForm(courttypeid, ladderid){

	if(courttypeid == 3){
		document.ladder_form.action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/team_ladder.php"
	}else {
		document.ladder_form.action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php"
	}
	
	 document.ladder_form.courttypeid.value = courttypeid;
   document.ladder_form.ladderid.value = ladderid;
   document.ladder_form.submit();
}

</script> 

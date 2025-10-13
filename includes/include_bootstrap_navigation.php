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


<nav class="navbar navbar-expand-lg navbar-light bg-light" >
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top">
    </a>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">Directory</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/">Bookings</a>
        </li>
        
        <? if(isLadderRankingScheme() || isJumpLadderRankingScheme() ) {?>
           <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Club Rankings
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

           <? for ($i=0; $i < count($_SESSION["ladders"]); ++$i) {?>
               <li>
                  <a class="dropdown-item" href="javascript:submitLadderForm('<?=$_SESSION["ladders"][$i]['courttypeid']?>','<?=$_SESSION["ladders"][$i]['id']?>')">
                    <?=$_SESSION["ladders"][$i]['name']?>
                  </a>
                </li>
            <? } ?>

            
          </ul>
        </li>

          <? } if(isPointRankingScheme()  )  { ?>
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Rankings</a>
            </li>
          <? }?>
          <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php">Players Wanted</a>
            </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            My Account
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_settings.php">Edit Account</a></li>
            <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php">Change Password</a></li>
            <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_buddylist.php">My Buddies</a></li>
            <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_reservations.php">My Reservations</a></li>
            
          </ul>
        </li>
        
      </ul>
    <? if( !is_logged_in() ){ ?>
      <span class="navbar-text">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>
      </span>
     <? } else { ?>
      <span class="navbar-text">
        You are logged in as: <? p($_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"]) ?> | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
      </span>
     <? } ?>
    </div>
  </div>
</nav>


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




<nav class="navbar navbar-expand navbar-light bg-white" >
  <div class="container-fluid">
       <a class="navbar-brand" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/">
        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.png" alt="" width="80" height="80" class="d-inline-block align-text-top">
    </a>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">Directory</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/">Bookings</a>
        </li>
        
        <? if(isLadderRankingScheme() || isJumpLadderRankingScheme() ) {?>
           <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Rankings
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
              <a class="nav-link" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Rankings</a>
            </li>
          <? }?>
          <li class="nav-item">
              <a class="nav-link" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php">Players Wanted</a>
            </li>

        <? if( isSiteBoxLeageEnabled() ){ ?>

          <? if( isJumpLadderRankingScheme() ){ ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Leagues
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">Box Leagues</a></li>
             <? if( isSiteClubTeam( get_siteid() ) ) { ?>
              <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/club_teams.php">Club Teams</a></li>
              <? } ?>
               <? if(isinBoxLeague( get_userid() ) && isOnClubTeam(  get_userid() )) { ?>
              <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_league_schedule.php">My Team Matches</a></li>
                <? } ?>
            </ul>
        </li>
        <? } else {?>
          <li class="nav-item">
              <a class="nav-link" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">Leagues</a>
            </li>
            <? } ?>
            <? } ?>
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

        <?php if( get_roleid() == 2 ) { ?>

          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tools
          </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">

                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/user_registration.php">Add Player</a></li>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">Account Maintenance</a></li>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">Email Players</a></li>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php">Box League Setup</a></li>

                 <? if( isJumpLadderRankingScheme() ) { ?>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_teams.php">Club Teams</a></li>
                <? } ?>

                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php">Club Preferences</a></li>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/report_scores.php">Report Scores</a></li>
             
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php">Club Events</a></li>
                <li><a class="dropdown-item" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_reports.php">Club Reports</a></li>
                
            </ul>
          <? } ?>
        
      </ul>
    <? if( !is_logged_in() ){ ?>
      <span class="navbar-text">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>
      </span>
     <? } else { ?>
      <span class="navbar-text">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
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

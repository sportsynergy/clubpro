<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">
     <a class="navbar-brand" href="<?=$_SESSION["CFG"]["wwwroot"]?>/system/">
        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/system/logo.png" alt="" width="80" height="80" class="d-inline-block align-text-top">
    </a>

  <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_dashboard.php">Club Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php">Change Password</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">Account Maintenance</a>
        </li>
        <li class="nav-item">
           <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/manage_club_policies.php">Club Policies</a>
        </li>
         <li class="nav-item">
           <a class="nav-link" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/system_preferences.php">System Preferences</a>
        </li>
      </ul>
       <? if( is_logged_in() ){ ?>
     
      <span class="navbar-text">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
      </span>
     <? } ?>
    </div>
    
   
    
  </div>
</nav>

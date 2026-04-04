


<nav class="navbar navbar-expand-lg navbar-light bg-white">
  <div class="container-fluid">

    <a class="navbar-brand" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/">
        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.png" alt="Logo" class="d-inline-block align-text-top clublogo">
    </a>
    
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
</nav>



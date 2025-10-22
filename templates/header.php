<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title><? pv($DOC_TITLE) ?></title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="refresh" content="1830">
    <meta name="referrer" content="always">


    
    <!-- Misc -->
    <link rel="icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/twitter1.png" type="image/x-icon" />

    <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel="stylesheet" type="text/css" />
    <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/ajaxtags.css" rel="stylesheet" type="text/css" />
    <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/displaytag.css" rel="stylesheet" type="text/css" />
    <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/calendar.css" rel="stylesheet" type="text/css" />
    
    <?php if(isset( $_SESSION["siteprefs"]["siteid"]) ){ ?>
    <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?php echo get_sitecode(); ?>/main.css" rel="stylesheet" type="text/css" />
    <?php } ?>

    <script src="https://code.jquery.com/jquery-3.7.1.js" type="text/javascript"></script>
    <script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/prototype-1.3.1.js" type="text/javascript"></script>
    <script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/ajaxtags-1.1.5.js" type="text/javascript"></script>

  </head>
  
  <body>
   

  <?php
    if( isset($trackingid) ){
      include_once("analyticstracking.php") ;
    }
    //
    // When site is disabled display the gone fishing sign,
    // but not for the system administration console.
    if( ! isSiteEnabled() && ! isSystemAdministrationConsole()){



      include($_SESSION["CFG"]["includedir"]."/include_gonefishin.php");
      include($_SESSION["CFG"]["templatedir"]."/footer.php");
      die;
    } 
    ?>

    <div class="container">
      <div class="row">
        <div class="col">
           <? if( is_logged_in() ){ ?>
          <? include($_SESSION["CFG"]["includedir"]."/include_bootstrap_navigation.php"); ?>
          <? } else { ?>
          <? include($_SESSION["CFG"]["includedir"]."/include_bootstrap_loginbar.php"); ?>
          <? } ?> 
        </div>
        
      </div>

      <div class="row">
        <div class="col-3">
       <div class="d-none d-lg-block">		
        

        <?php if( isSystemAdministrationConsole() ){ ?>
        <p>
          <?php include($_SESSION["CFG"]["includedir"]."/include_admin_activity.php"); ?>
        </p>
        <?php } else{ ?>
        <p>
          <?php include($_SESSION["CFG"]["includedir"]."/include_news.php"); ?>
        </p>
        <p>
          <?php include($_SESSION["CFG"]["includedir"]."/include_events.php"); ?>
        </p>
        <?php
      if( isDisplayRecentActivity() ){ ?>
        <p>
          <?php include($_SESSION["CFG"]["includedir"]."/include_recent_activity.php"); ?>
        </p>
        <?php } ?>
        <?php } ?>
	  </div>

    </div>
    <div class="col" style="padding-left: 25px">		
      <? include($_SESSION["CFG"]["templatedir"]."/form_header.php"); ?>


   


    
    

 
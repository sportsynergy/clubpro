<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta http-equiv="refresh" content="600">
<title><? pv($DOC_TITLE) ?>
</title>

<!-- Misc -->
<link rel="icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/icon.ico"
	type="image/x-icon" />

<!-- Dependency source files -->

<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/tabview/assets/skins/sam/tabview.css" />
<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/assets/skins/sam/container.css" />
<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/calendar/assets/skins/sam/calendar.css" />
<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" />
<link rel="stylesheet" type="text/css"
	href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/menu/assets/skins/sam/menu.css">

<!-- JS for Menu -->
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/animation/animation.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/container_core.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/container-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/dragdrop/dragdrop-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/calendar/calendar-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/json/json-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/connection/connection-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/button-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/tabview/tabview-min.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/menu/menu.js"></script>
<script type="text/javascript"
	src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/navigation.js"></script>









<?php if (!defined("_JQUERY_")){ ?>
<!-- Page-specific styles -->
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/prototype-1.3.1.js" type="text/javascript"></script>
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/ajaxtags-1.1.5.js" type="text/javascript"></script>
<?php } ?>

<?php if (defined("_JQUERY_") && _JQUERY_ == true){ ?>
<!-- jQuery -->
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/jquery-1.6.1.min.js" type="text/javascript"></script>
	<?php if (defined("_PRETTYPHOTO_") && _PRETTYPHOTO_ == true){ ?>
<!-- PrettyPhoto -->
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/prettyPhoto.css" rel="stylesheet" type="text/css" media="screen" charset="utf-8" />
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/jquery.prettyPhoto.js" type="text/javascript"></script>
    <?php } ?>
<?php } ?>

<!-- Standard reset, fonts and grids -->
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css" />
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel="stylesheet" type="text/css" />
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/ajaxtags.css" rel="stylesheet" type="text/css" />
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/displaytag.css" rel="stylesheet" type="text/css" />
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/calendar.css" rel="stylesheet" type="text/css" />
<?php if(isset( $_SESSION["siteprefs"]["siteid"]) ){ ?>
<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?php echo get_sitecode(); ?>/main.css" rel="stylesheet" type="text/css" />
<?php } ?>
</head>

<body class="yui-skin-sam" id="main-com">




<?php
if( isset($trackingid) ){
	include_once("analyticstracking.php") ;
}
//
// When site is disabled display the gone fishing sign,
// but not for the system administration console.
if( ! isSiteEnabled() && ! isSystemAdministrationConsole()){

	if( isDebugEnabled(1) ) logMessage("header_yui: This site is not enabled");

	include($_SESSION["CFG"]["includedir"]."/include_gonefishin.php");
	include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
	die;
} ?>
	<div id="doc2" class="yui-t1 mainpanel">
		<div id="hd">
			
			
		<?php
		// If there are more than one site, list it here
		if( ! isSystemAdministrationConsole() ) {
			// Get Sites and list them (where the club has more than one)
			// Set the club id from the session
			// (this is set from the scheduler_content or web_ladder)
			$currentClubId = get_clubid();


			//Display Site List (not for system administrators)
			if( isDisplaySiteNavigation() ) {
					
				$siteQuery = "SELECT site.sitename, site.sitecode, site.siteid, site.enable
		                      						FROM tblClubSites site 
		                      						WHERE clubid = $currentClubId
		                      							AND site.displaysitenavigation='y'";
				$siteResult = db_query($siteQuery);
					
				if(mysql_num_rows($siteResult)>1){

					$sitecounter = 0;
					while($siteRow = mysql_fetch_array($siteResult)){

						// Display Pipes between Site Links
						if($sitecounter!=0){
							echo '&nbsp;|&nbsp;';
						}
							
						if($siteRow[2]!=get_siteid()){
							echo '<a class="normal" href="'
							.$_SESSION["CFG"]["wwwroot"]
							.'/clubs/'.$siteRow[1].'">'
							.$siteRow[0].'</a>';
						}else{
							echo $siteRow[0];
						}
						++$sitecounter;
					}
				}
			}
		}
		?>

			<!-- start: your content here -->
			<div style="float: right; padding: 5px" id="loginPanel">
			<? if( !is_logged_in() ){ ?>
				<a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>
				<? } else { ?>
				<span>You are logged in as: <? p($_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"]) ?>
				</span> | <a class="normal"
					href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
					<? }?>
			</div>

			<!-- Updated to float-left, and increase -headspace- of the page -->
			<div id="logoPanel"
				style="padding-bottom: 20px; padding-left: 0px; text-align: center; float: left; display: block;">
				<?php
				$fburl = get_facebookurl();
				if( !empty( $fburl ) ) { ?>
				&nbsp; <a href="<?=$fburl?>" target="_blank"> <img
					src="<?=$_SESSION["CFG"]["imagedir"]?>/fb.png" border="0">
				</a>
				<!-- <br /> -->
				<? } ?>
			</div>
			<!-- start: primary column from outer template -->
			<div id="yui-main">
				<div class="yui-b">
					
					
					
					
				<?php
		 	    //Only show when logged in
				if( is_logged_in() ){
					if( isSystemAdministrationConsole() ){
						include($_SESSION["CFG"]["includedir"]."/include_yui_admin_navigation.php");
					}else{
						include($_SESSION["CFG"]["includedir"]."/include_yui_navigation.php");
							
					}
				}
				?>
					<div id="scrolling" style="height: 20px; margin-top: 3px">




					<?php include($_SESSION["CFG"]["includedir"]."/include_scrollingmessage.php"); ?>
					</div>
				</div>
			</div>

			<!-- end: your content here -->
		</div>
		<div id="bd">
			<div class="yui-b">
				<div style="padding-right: 20px">
					<p>
						<!-- site-logo -->
						
						
						
						
	  <?php if(isSystemAdministrationConsole()){ ?>
      <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/0.gif" width="148" height="148" />
      <?php } else{ ?>
      <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?php echo get_sitecode(); ?>" >
      	<img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.png" width="148" height="148" />
      </a>
      <?php } ?>
    </p>
					<!-- END site-logo -->
					
					
					
					
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

			<!-- end: primary column from outer template -->

			<!-- start: secondary column from outer template -->
			<div id="yui-main">
				<div class="yui-b">
					<div style="height: .5em"></div>
					<? include($_SESSION["CFG"]["templatedir"]."/form_header.php"); ?>
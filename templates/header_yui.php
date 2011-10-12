<?

/*
 * $LastChangedRevision: 836 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-22 17:35:17 -0600 (Tue, 22 Feb 2011) $

*/

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd"> 
        
<html> 
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=utf-8"> 
        <meta http-equiv="refresh" content="600">
        <title><? pv($DOC_TITLE) ?></title> 
        
    
        <!-- Misc -->
        <link rel="icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/icon.ico" type="image/x-icon" />
 
        <!-- Dependency source files --> 
        <script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/yahoo-dom-event/yahoo-dom-event.js"></script> 
        <script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/animation/animation.js"></script> 
        <script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/container_core.js"></script> 
        <script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/container-min.js"></script>
		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/dragdrop/dragdrop-min.js"></script> 
		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/calendar/calendar-min.js"></script>
		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/connection/connection-min.js"></script>
		

		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/element/element-min.js"></script>
		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/tabview/tabview-min.js"></script>
		
		<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/fonts/fonts-min.css" />
		<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/tabview/assets/skins/sam/tabview.css" />
		<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/container/assets/skins/sam/container.css" /> 
		<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/calendar/assets/skins/sam/calendar.css" />
		<link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/button/assets/skins/sam/button.css" /> 
	
		
 		 <!-- CSS for Menu --> 
        <link rel="stylesheet" type="text/css" href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/menu/assets/skins/sam/menu.css"> 
 
 
 		<!-- Menu source file --> 
        <script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/menu/menu.js"></script> 
 		<script type="text/javascript" src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/navigation.js"></script> 
 
 
        <!-- Page-specific styles --> 
		<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>
		<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/prototype-1.3.1.js" type="text/javascript"></script>
		<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/ajaxtags-1.1.5.js" type="text/javascript"></script>
	
		
		<!-- Standard reset, fonts and grids --> 
        <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/yui/reset-fonts-grids/reset-fonts-grids.css" rel="stylesheet" type="text/css"> 

        <link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.new.css" rel=stylesheet type=text/css>
		<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/ajaxtags.css" rel=stylesheet type=text/css>
		<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/displaytag.css" rel=stylesheet type=text/css>
		
		<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/calendar.css" rel=stylesheet type=text/css>
		
		<? if(isset( $_SESSION["siteprefs"]["siteid"]) ){ ?>
		<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/main.css" rel=stylesheet type=text/css>
		<?} ?>
 
 		
 
    </head> 
    
    
  <body class="yui-skin-sam" id="main-com"> 
  
  		           <?
                //When site is disabled display the gone fishing sign, but not for the system administration console.
                if( ! isSiteEnabled() && ! isSystemAdministrationConsole()){ 
                	include($_SESSION["CFG"]["includedir"]."/include_gonefishin.php");
               		include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
               		die;
               } ?>
               
        <div id="doc2" class="yui-t1 mainpanel" > 
            <div id="hd"> 
                

				<!-- If there are more than one site, list it here -->
				<?
				if( ! isSystemAdministrationConsole() ) { 
                      	//Get Sites and list them (where the club has more than one)
                      	
					    // Set the club id from the session (this is set from the scheduler_content or web_ladder)
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
			                      		while($siteRow = mysql_fetch_array($siteResult)){  ?>
			                      		
			                      		<?
			                      		//Display horization seperator things
			                      		if($sitecounter!=0){
			                      		?>	
			                      		&nbsp;|&nbsp;
			                      		
			                      		<?}?>
			                      		
				                      		<? 
				                      		if($siteRow[2]!=get_siteid()){
				                      		?>
			                      				<a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=$siteRow[1]?>"><?=$siteRow[0]?></a>
			                      			<?}else{?>
			                      				<?=$siteRow[0]?>
			                      			<?}?>
			                      		<?++$sitecounter?>
			                      		
			                      		<?}?>
		                        <?}?>
                      	 <?} } ?>

                <!-- start: your content here --> 
               <div style="text-align: right; width: 915px; padding: 5px" id="loginPanel"> 
	    		<? if( !is_logged_in() ){ ?>
	    			 <a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>
	    		<? } else { ?>
	    		  	<span>You are logged in as: <? p($_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"]) ?></span>
					 | <a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
	    		<? }?>
    			</div>
    			 <div id="logoPanel" style="padding-bottom: 20px; padding-left: 185px; text-align: left">
    			<? if(isSystemAdministrationConsole()){ ?>
    				 <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/0.gif" >
			    <? } else{ ?>	
			    	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>" >
			    		<img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.gif" >
			    	</a>
			    <? } ?>
			    </div>
                     <!-- start: primary column from outer template --> 
                <div id="yui-main"> 
                    <div class="yui-b"> 
                        
 					<?
                       //Only show when logged in
			            if( is_logged_in() ){
			            	
				            if( isSystemAdministrationConsole() ){
				            	include($_SESSION["CFG"]["includedir"]."/include_yui_admin_navigation.php");
				            }else{
				            	include($_SESSION["CFG"]["includedir"]."/include_yui_navigation.php"); 
				            	 
				            }  
			            } 
                        ?>
                        
                           <div id="scrolling" style="height: 20px; margin-top: 15px">
     							<? include($_SESSION["CFG"]["includedir"]."/include_scrollingmessage.php"); ?>
							</div>
				
                    </div> 
                   
                </div> 
        
                <!-- end: your content here --> 
            </div> 
             
            <div id="bd"> 
 				  <div class="yui-b">
	 				  <div  style="padding-right: 20px">
	 				  <? if( isSystemAdministrationConsole() ){ ?>
	 				  	
		                    <p> <? include($_SESSION["CFG"]["includedir"]."/include_admin_activity.php"); ?></p> 
	 				  	
	 				  <? } else{ ?>
	 				  		
	 				  		<p> <? include($_SESSION["CFG"]["includedir"]."/include_news.php"); ?></p> 
	 				  		<p> <? include($_SESSION["CFG"]["includedir"]."/include_events.php"); ?></p> 
	 				  		
	 				  		<?
	 				  		if( isDisplayRecentActivity() ){ ?>
	 				  			<p> <? include($_SESSION["CFG"]["includedir"]."/include_recent_activity.php"); ?></p> 
	 				  		<? } ?>
		                    
	 				  <? } ?>
	 				  </div>
 				  </div>
 				  
               
                <!-- end: primary column from outer template --> 
 
                <!-- start: secondary column from outer template --> 
                <div id="yui-main">
                
              
                
                <div class="yui-b"> 
 					 <div style="height: .5em"></div>
 					 <? include($_SESSION["CFG"]["templatedir"]."/form_header.php"); ?>
                
 
       


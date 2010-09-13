<?php
  /*
 * $LastChangedRevision: 750 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2009-11-17 00:08:34 -0800 (Tue, 17 Nov 2009) $

*/


//Set and check the views


if (isset($_REQUEST["view"]) && has_priv("2")){
 if($_REQUEST["view"]=="2"){
      $_SESSION["view"] = 2;
     header("Location:  ".$_SESSION["baseurl"]."");

      }
   if($_REQUEST["view"]=="1"){
      $_SESSION["view"] = 1;
     header("Location:  ".$_SESSION["baseurl"]."");
      }
}
else{

    $_SESSION["baseurl"] = qualified_mewithq();

    }
      
    
?>

<html>
<head>
<meta http-equiv="refresh" content="600">
<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT">
<!-- // Hide script from old browsers-->

function newWindow(newContent)
 {
  winContent = window.open(newContent, 'nextWin', 'right=0, top=20,width=350,height=500, toolbar=no,scrollbars=yes, resizable=no')
 }

 //Stop hiding script from old browsers -->


 function submitFormWithAction(theForm, action)
{

        var form = eval("document." + theForm);
        form.action = action;

        // SUBMIT
        form.submit();

}//end function submitForm()

function submitForm(theForm)
{

      var form = eval("document." + theForm);
      form.submit();

}//end function submitForm()





 </SCRIPT>
<title><? pv($DOC_TITLE) ?></title>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.css" rel=stylesheet type=text/css>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/ajaxtags.css" rel=stylesheet type=text/css>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/displaytag.css" rel=stylesheet type=text/css>
<? if(isset( $_SESSION["siteprefs"]["siteid"]) ){ ?>
<LINK href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/main.css" rel=stylesheet type=text/css>
<?} ?>
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/prototype-1.3.1.js" type="text/javascript"></script>
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/ajaxtags-1.1.5.js" type="text/javascript"></script>
<link rel="icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/icon.ico" type="image/x-icon" />
</head>



<!-- <body bgcolor=#DFDFDF link=#0000ff vlink=#000099 alink=#ff0000 leftmargin="0" topmargin="10"> -->
<body class="mainbody" link=#0000ff vlink=#000099 alink=#ff0000 leftmargin="0" topmargin="10">
<table width="100%">
  <tr>
      <td>
<table cellspacing="0" cellpadding="0" border="0" class=bannertable align="center">
                 <tr>
                     <td colspan="3" bgcolor="black"></td>
                 </tr>
                 <tr>
                     <td rowspan="3" width="1" bgcolor="black"></td>
                     <td ></td>
                     <td rowspan="3" width="1" bgcolor="Black"></td>
                 </tr>
                 <tr>
                      <td bgcolor="white">




       <table width="800" align="center" cellspacing="0" cellpadding="0" border="0">





        <tr>
            <td height="30">
                <!-- Spacer row -->
            </td>
        </tr>

        <tr>
         <td class="normal">

             <table cellspacing="0" cellpadding="0" border="0" width="710" align="center" >
                <tr>
                      
                      <td align="left" class="normal"">
                      <?

                      	//Get Sites and list them (where the club has more than one)
                      	if(is_logged_in()){
                      		$currentClubId = get_clubid();
                      	}else{
                      		//Must be set either through an entry point (scheduler or web_ladder)
                      		$currentClubId = $clubid;
                      	}
                      	
                      	
                      	
                      	//Display Site List (not for system administrators)
                      	if( ! isSystemAdministrationConsole() 
                      	&& $DOC_TITLE != "Password Recovery" 
                      	&& $DOC_TITLE != "Error Page") {
                      	
		                      	$siteQuery = "SELECT site.sitename, site.sitecode, site.siteid, site.enable from tblClubSites site WHERE clubid = $currentClubId";
		                      	$siteResult = db_query($siteQuery);
		                      	
		                      	if(mysql_num_rows($siteResult)>1){
		                      		
		                      		$sitecounter = 0;
			                      		while($siteRow = mysql_fetch_array($siteResult)){  
			                      		
			                      		?>
			                      		
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
                      	 <?}?>
                      	
                      

                      </td>
                   
                      
                      <? if (  isSystemAdministrationConsole() || isSiteEnabled()  ){ ?>
                      	
                      <td align="right" class="normal">
                        <? if (is_logged_in()) { ?>
                          <font class="smallbold">You are logged in as: <? p($_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"]) ?></font>
                          | <a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>

                           <? } else { ?>
                          <a class="normal" href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>

                          <? } ?>
                       </td>
                       
                       <? } else{ ?>
                       		
                       		<td align="right" class="normal"></td>
                       		
                       <? } ?>
               </tr>        
                <?
                //When site is disabled display the gone fishing sign, but not for the system administration console.
                if( ! isSiteEnabled() && ! isSystemAdministrationConsole()){ ?>
                	
                	<tr valign="top">
 						<td width="100%">
                	
               <? 
               		include($_SESSION["CFG"]["includedir"]."/include_gonefishin.php");
               		include($_SESSION["CFG"]["templatedir"]."/footer.php");
               		die;
               } 
               
               ?>
                
                <tr><?

                   if(is_logged_in()){
                       
                       //Role ID 3 is system administrators
                       if( get_roleid() == 3) { ?>
                       
                       		<td align="left" colspan="2">
                       		<br/><br/>
                            <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/0.gif" >
                            <br/><br/><br/>
                            </td>

                        <? }
                        else{ ?>
                                                   
	                        <td align="left" colspan="2">
	                        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.gif"></td>
                       <? }
                      }
                    else{
                         if( isset($clubid) ){ ?>
                             <td align="left" colspan="2">
                             <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.gif" alt="" border="0"></td>
                            <? }
                           else{
                           	
                           	//System administrators won't have a club set(for non system administrators this is set in root index.php)
                           	?>
                             <td align="left" colspan="2">
                             <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/0.gif"></td>
						<?
                           }
                     }
                   ?>
               </tr>

        </table>

         </td>
        </tr>

        <tr>
            <td height="15">
                <!-- Spacer row -->
            </td>
        </tr>

        <tr>
        <td class="normal">


         <? if (is_logged_in()){ 
         	
         	
         	?>
          <table cellspacing="0" cellpadding="0" border="0" class="bannertable" width="710" align="center">
                 <tr>
                     <td colspan="3" bgcolor="Black"></td>
                 </tr>
                 <tr>
                     <td rowspan="3" width="1" bgcolor="Black"></td>
                     <td ></td>
                     <td rowspan="3" width="1" bgcolor="Black"></td>
                 </tr>
                 <tr>
                      <td>
                       <!-- Start the body here -->
                       <table cellpadding="2" cellspacing="2" width="700" align="center">
                              <tr>
                              <? if ($_SESSION["view"]==3) { ?>
                                 
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_dashboard.php"><font class="normalsm">Club Dashboard</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php"><font class="normalsm">Change Password</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php"><font class="normalsm">Account Maintenance</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/manage_club_policies.php"><font class="normalsm">Club Policies</font></a></td>
                              	  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/system_preferences.php"><font class="normalsm">System Preferences</font></a></td>
                              
                              <? } ?>
                               <? if ($_SESSION["view"]==2) { ?>
                               	  
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/user_registration.php"><font class="normalsm">Add Player</font></a></td>
                                  <td> | </td>   
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php" ><font class="normalsm">Account Maintenance</font></a></td>

                                  <td> | </td>
                                  <td valign="middle">
                                  <? if ( !isLiteVersion() ) {?>
                                  	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php" >
                                  		<font class="normalsm">Email Players</font>
                                  	</a>
                                  	<? } else { ?>
                                  		<font class="normalsm">Email Players</font>
                                  	<? } ?>
                                  </td>
                                  <td> | </td>
                                  
                                 
                                  <td valign="middle">
                                   <?  if ( !isLiteVersion() ) { ?>
                                  	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php">
                                  		<font class="normalsm">Box League Setup</font>
                                  	</a>
                                  	<? } else { ?>
                                  		<font class="normalsm">Box League Setup</font>
                                  	<? } ?>
                                  </td>
                                  <td> | </td>
                                  
                                  
                                  <td valign="middle">
                                  <?  if ( !isLiteVersion() ) {?>
	                                  <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php">
	                                  	<font class="normalsm">Club Preferences</font>
	                                  </a>
                                  <? } else { ?>
                                  	<font class="normalsm">Club Preferences</font>
                                  <? } ?>
                                  </td>
                                  <td> | </td>
                                  
                                  
                                  <td valign="middle">
                                   	<?  if ( !isLiteVersion() ) {?>
	                                  	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/report_scores.php">
	                                  		<font class="normalsm">Record Scores</font>
	                                  	</a>
	                                  <? } else { ?>
	                                  	<font class="normalsm">Record Scores</font>
	                                  <? } ?>
                                  	</td>
                                  <td> | </td>
                                  
                                 
                                  <td valign="middle">
                                   <?  if ( !isLiteVersion() ) {?>
                                  	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_reports.php">
                                  		<font class="normalsm">Club Reports</font>
                                  	</a>
                                  	<? } else { ?>
                                  		<font class="normalsm">Club Reports</font>
                                  	<? } ?>
                                  </td>
								   
                              <? } ?>
                              <? if ($_SESSION["view"]==1) { ?>

                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php"><font class="normalsm">Member Directory</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_settings.php"><font class="normalsm">Edit Account</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/"><font class="normalsm">Court Reservation</font></a></td>
                                  <? if(!isSiteAutoLogin()){ ?>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php"><font class="normalsm">Change Password</font></a></td>
                                  <? } ?>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php"><font class="normalsm">Player Rankings</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_reservations.php"><font class="normalsm">My Reservations</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php"><font class="normalsm">Players Wanted</font></a></td>
                                   <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_buddylist.php"><font class="normalsm">My Buddies</font></a></td>
                                  
                                   
                                   <?
                                    $anyboxesquery = "SELECT tblBoxLeagues.boxid
                                                      FROM tblBoxLeagues
                                                      WHERE (((tblBoxLeagues.siteid)=".get_siteid()."))";

                                    $anyboxesresult = db_query($anyboxesquery);

                                    if(mysql_num_rows($anyboxesresult)>0){
                                    $num = mysql_num_rows($anyboxesresult);
                                  ?>
                                 <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php"><font class="normalsm">Box Leagues</font></a></td>
                                   <? } ?>
                              <? } ?>
                               <? if ($_SESSION["view"]==4) { ?>

                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php"><font class="normalsm">Member Directory</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>"><font class="normalsm">Court Reservation</font></a></td>
                                   <? if(!isSiteAutoLogin()){ ?>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php"><font class="normalsm">Change Password</font></a></td>
                                  <? } ?>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php"><font class="normalsm">Player Rankings</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php"><font class="normalsm">Players Wanted</font></a></td>
                                  <td> | </td>
                                  <td valign="middle"><a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php"><font class="normalsm">Box Leagues</font></a></td>

                              <? }  ?>

                              </tr>
                       </table>
                       <!-- End the body here -->
                       </td>
                 </tr>

                                 <tr>
                     <td></td>
                 </tr>
                 <tr>
                     <td colspan="3" bgcolor="Black"></td>
                 </tr>
                 </table>

           <? }?>

        </td>

</tr>
<tr>
    <td>
        <table cellspacing="0" cellpadding="0" border="0"  width="710" align="center">
           <tr align="right">
               <td>
                  <?

                   if (has_priv("2")) {
                       if ($ME == $MEWQ) {

                           echo "<a href=\"$ME?view=2\"><font class=normalsm>System View</font></a> | <a href=\"$ME?view=1\"><font class=normalsm>Player View</font></a>";
                       }
                       else{
                          echo "<a href=\"$MEWQ&view=2\"><font class=normalsm>System View</font></a> | <a href=\"$MEWQ&view=1\"><font class=normalsm>Player View</font></a>";
                       }
                  }

                  ?>
               </td>
           </tr>
       </table>
     </td>

 </tr>
 <tr>
     <td height="30" valign="top">
     	<? include($_SESSION["CFG"]["includedir"]."/include_scrollingmessage.php"); ?>
     </td>
 </tr>
<tr valign="top">

 <td width="100%">
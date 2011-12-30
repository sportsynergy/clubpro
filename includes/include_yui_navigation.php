
				<div id="productsandservices" class="yuimenubar yuimenubarnav"> 
                            <div class="bd"> 
                                <ul class="first-of-type"> 
                                    <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php">Member Directory</a></li> 
                                    <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/">Reservations</a></li> 
                                    <? if(isLadderRankingScheme() ) {?>
                                   <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">Club Ladder</a> 
                                    		<div id="ladder" class="yuimenu"> 
												<div class="bd"> 
												<ul class="first-of-type">
												<? for ($i=0; $i < count($_SESSION["ladders"]); ++$i) {?>
		                                           <li class="yuimenuitem"><a class="yuimenuitemlabel" href="javascript:submitLadderForm('<?=$_SESSION["ladders"][$i]['courttypeid']?>')"><?=$_SESSION["ladders"][$i]['name']?></a></li>  
		                                         <? } ?>
		                                         </ul>	
		                                    </div> 
		                                </div>      
		                            </li> 
                                    <? } else { ?>
                                     <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Rankings</a></li> 
                                    <? }?>
                                    <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/players_wanted.php">Players Wanted</a></li> 
                                    <? if( isSiteBoxLeageEnabled() ){ ?>
                                    <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">Box Leagues</a></li> 
                              		
                              		<? } ?>
                                    <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">My Account</a> 
                                    		<div id="account" class="yuimenu"> 
												<div class="bd"> 
												<ul class="first-of-type">
		                                           <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_settings.php">Edit Account</a></li> 
		                                           <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/change_password.php">Change Password</a></li> 
		                                           <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_buddylist.php">My Buddies</a></li> 
		                                           <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_reservations.php">My Reservations</a></li> 
		                                        </ul>	
		                                    </div> 
		                                </div>      
		                            </li> 
		                            <? if( get_roleid() == 2 ) { ?>
		                            
		                            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#">Administration</a> 
                
                                        <div id="administration" class="yuimenu"> 
                                            <div class="bd">                    
                                                <ul> 
                                                    <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/user_registration.php">Add Player</a></li> 
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_admin.php">Account Maintenance</a></li> 
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">Email Players</a></li> 
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/web_ladder_registration.php">Box League Setup</a></li> 
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/policy_preferences.php">Club Preferences</a></li> 
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/report_scores.php">Report Scores</a></li>
		                                            <? 
				                    				//At least let admins see this.  This isn't displayed otherwise
				                    				if(isLadderRankingScheme()){?>
				                    					<li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_rankings.php">Rankings</a></li> 
				                    				<? }?>
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_events.php">Club Events</a></li>  
		                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/club_reports.php">Club Reports</a></li>  
                                                </ul> 
                                            </div> 
                                        </div>                    
                                    </li> 
		                          <? } ?>
                              </ul>
                        </div>
                     </div> 
                     
<form name="ladder_form" method="POST" >
	<input type="hidden" name="courttypeid">
</form>

<script type="text/javascript" >

function submitLadderForm(courttypeid){

	if(courttypeid == 3){
		document.ladder_form.action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/team_ladder.php"
	}else {
		document.ladder_form.action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php"
	}
	
	 document.ladder_form.courttypeid.value = courttypeid;
     document.ladder_form.submit();
}

</script>
                     
                      
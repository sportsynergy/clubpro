

<div align="center">
  <table width="650" cellpadding="20" cellspacing="0" class="generictable">
    <tr>
      <td class="clubid<?=get_clubid()?>th" ><span class=whiteh1>
        <div align="center">
          <? pv($DOC_TITLE) ?>
        </div>
        </span></td>
    </tr>
    <tr>
      <td><table cellspacing="0" cellpadding="1" width="650" class="borderless">
          <tr>
            <td class=label>First Name:</td>
            <td class="normal"><?=$frm["firstname"] ?></td>
            <td rowspan="6" valign="top">
              <div align="center"> 

                   <?  if( isset($frm["photo"]) ){ ?>
                            <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($frm["photo"]); ?>" width="180" height="180">
                        <?   } else{  ?>
						                <img src="<?=get_gravatar($frm["email"],180 )?>" />
                        <?   }   ?>
              </div>
          </td>
          </tr>
          <tr>
            <td class=label>Last Name:</td>
            <td class="normal"><?=$frm["lastname"] ?></td>
          </tr>
          <tr>
            <td class=label>Email:</td>
            <td class="normal"><a href="mailto:<?=$frm["email"] ?>">
              <?=$frm["email"] ?>
              </a></td>
          </tr>
         
          <? if(!empty($frm["cellphone"])){?>
          <tr>
            <td class=label>Mobile Phone:</td>
            <td class="normal"><?  pv($frm["cellphone"]); ?></td>
          </tr>
          <? } ?>
          <? if(!empty($frm["msince"])){?>
          <tr>
            <td class="label">Member Since:</td>
            <td class="normal"><?=$frm["msince"] ?></td>
          </tr>
          <? } ?>
          
          <tr>
            <?
		   // Get the Custom Parameters
		  while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );
	
			if($parameterArray['parametertypename'] == "text" && !empty($parameterValue) ){ ?>
          <tr>
            <td class="label"><? pv($parameterArray['parameterlabel'])?>
              :</td>
            <td><? pv($parameterValue) ?></td>
          </tr>
          <? } elseif($parameterArray['parametertypename'] == "select" && !empty($parameterValue) ) { ?>
          <tr>
            <td class="label"><? pv($parameterArray['parameterlabel'])?>
              :</td>
            <td><? pv( load_parameter_option_name($parameterArray['parameterid'], $parameterValue)  ) ?></td>
          </tr>
          <? } ?>
          <? } ?>
          
          <? if( isPointRankingScheme()  ) { ?>
          <tr>
            <td class="label" valign="top">Rankings:</td>
            <td >
              <table width="300">
                <?  while ($registeredArray = db_fetch_array($registeredSports)){ ?>
                <tr>
                  <td><?=$registeredArray['courttypename']?>:
                  <?=$registeredArray['ranking']?></td>
                </tr>
                <?  }
							?>
              </table>
            </td>
          </tr>

          <? } ?>
          <tr height="15">
            <td colspan="2"></td>
          </tr>
          
          <?php
              # if player is on the ladder
              if ( isJumpLadderRankingScheme() ){ 
                $laddersforuserResult = getLadders($userid);
               
                while($ladder = mysqli_fetch_array($laddersforuserResult)){ 

              ?>

              <tr>
              <td class="label" valign="top" colspan="2"><?=$ladder['name'] ?> Ladder Results</td>
              </tr>
              <tr>
              <td class="label" valign="top" >Position</td>
              <td> <?=$ladder['ladderposition'] ?> </td>
              </tr>
              <tr>
              <td colspan="2">

                <?
                $ladderMatchResult = getLadderMatchesForUser($ladder['id'], $userid, 40 );
                
                if(mysqli_num_rows($ladderMatchResult) > 0){  ?>

                  <table class="activitytable" width="400">
                    <tr>
                      <th>Date</th>
                      <th>Winner</th>
                      <th>Loser</th>
                      <th>Score</th>
                    </tr>
                    
                <?
                while($challengeMatch = mysqli_fetch_array($ladderMatchResult)){ 

                  $scored = $challengeMatch['score'];
                  $winner_obj = new clubpro_obj;
                  $winner_obj->fullname =  $challengeMatch['winner_first']." ". $challengeMatch['winner_last'];
                  $winner_obj->id = $challengeMatch['winner_id'];
                  
                  $loser_obj = new clubpro_obj;
                  $loser_obj->fullname =  $challengeMatch['loser_first']." ". $challengeMatch['loser_last'];
                  $loser_obj->id = $challengeMatch['loser_id'];
                  
                  //don't include timestamp
                  $challengeDate = explode(" ",$challengeMatch['match_time']);

                  printLadderMatchRow($challengeMatch['id'], $winner_obj, $loser_obj, $challengeDate[0], $scored, $challengeMatch['league']);
                      
                } ?>
                </table>
                <td>
              </tr>
             <? } 
             
              }
              ?>

              </td>
              </tr>
              <?  }  ?>
        
          </tr>
         
        </table>
      
      </td>
    </tr>
   
  </table>
  <table width="650" cellpadding="20" cellspacing="0" >
  <tr height="15">
            <td colspan="2">
            <div class="spacer"></div>
            <?php
            if( $origin == 'ladder') { ?>
              <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php">< Back to ladder</a>
            <? } elseif ($origin == 'league') { ?>
              <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">< Back to leagues</a>
              
              <? }  ?>
            </td>
          </tr>
            </table>
  <div style="height: 2em;">


</div>
  <div style="text-align: left;">
  
  </div>
</div>

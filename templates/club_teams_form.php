
<table class="table table-striped">

<?

$count = 0;
$result = getClubTeams( get_siteid() );

$rownum = db_num_rows($webladderuserresult);

$lastUpdated = NULL;

while ($clubteam = db_fetch_array( $result )) {

    if (isDebugEnabled(1)) logMessage("club_team_manage: The last time this was updated was: $clubteam[lastUpdated]"); 
    $lastUpdated = $clubteam[lastUpdated];

    if ( $count == 0 ){ ?>
        <tr valign="top" id="newrow">
    <?  } ?>

   
           
           <table width="350" cellpadding="0" cellspacing="0" class="bordertablethick">
               <tr valign="top">
                   <td class=clubid<?=get_clubid()?>th>
                       <span class="whiteh1">
                           <div align="center">
                               <?=$clubteam['name']?>
                           </div>
                           <div align="center">
                                <span class="whitenormsm">Total Points: <?=$clubteam['score']?> </span>
                                <span class="whitenormsm">Total Games: <?=$clubteam['games']?></span>
                            </div>
                       </span>
                   </td>
               </tr>
               
               <? 
                $playersresult = getClubTeamMembers( $clubteam['id'] );
                $rownum = mysqli_num_rows($playersresult);

                while ($clubteamplayer = db_fetch_array( $playersresult )) { 
                    $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
               ?>
               <tr align="center" class="<?=$rc?>">
                <td> <?=$clubteamplayer['teamplayername']?></td>
                </tr>
                <? 
                $rownum = $rownum - 1;
                } 
                ?>
           </table>

       </td>
    
    <?  
    // update counter
    ++$count;
    if($count==2) { 
       
        $count = 0; ?>
        </tr >
    <? }
    
    

    } ?>

<tr>
    <td>
        <span class="smallbold">Last Updated: </span>
        
        <?

if (isDebugEnabled(1)) logMessage("club_team_manage: lastUpdated is is $lastUpdated");
        if( is_null($lastUpdated) ){
            $lastupdated_label = "Never";
          } else {
            //$lastupdated = ladderdetails['lastUpdated'];
            $lastupdated_label = $lastUpdated;
          }
          ?>
          <span class="smallreg"> <?=$lastupdated_label?> </span>
    </td>
</tr>
</table>

<? 



?>

<table width="710" cellspacing="0" cellpadding="0" align="center" class="borderless">

<?

$count = 0;
$result = getClubTeams( get_siteid() );

$rownum = mysqli_num_rows($webladderuserresult);
while ($clubteam = db_fetch_array( $result )) { 

    if ( $count == 0 ){
        if (isDebugEnabled(1)) logMessage("club_team_manage: resultcounter is $resultcounter. New row"); ?>
        <tr valign="top" id="newrow">
    <?  } ?>

    <td width="350" nowrap id="tablecontainer">
           
           <table width="350" cellpadding="0" cellspacing="0" class="bordertable">
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
    ++ $count;
    if(count==2) { 
        if (isDebugEnabled(1)) logMessage("club_team_manage: resultcounter is resetting");
        $count = 0; ?>
        </tr >
    <? }
    
    

    } ?>

</table>

<? 



?>
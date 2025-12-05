
<table width="710"  class="borderless">

<?

$count = 0;
$result = getClubTeams( get_siteid() );



$lastUpdated = NULL;

while ($clubteam = db_fetch_array( $result )) {

    if (isDebugEnabled(1)) logMessage("club_team_manage: The last time this was updated was: $clubteam[lastUpdated]"); 
    $lastUpdated = $clubteam['lastUpdated'];

    if ( $count == 0 ){ ?>
        <tr valign="top" id="newrow">
    <?  } ?>

    <td nowrap id="tablecontainer">
           
        <table width="350" class="table table-striped">
        <thead>
               <tr> 
                   <th>
                           <div class="bigbanner">
                               <?=$clubteam['name']?>
                           </div>
                           <div class="boxdateheader">
                              Total Points: <?=$clubteam['score']?> 
                              Total Games: <?=$clubteam['games']?>
                            </div>
                   </td>
               </tr>
                <thead>
                    <tbody>
               
               <? 
                $playersresult = getClubTeamMembers( $clubteam['id'] );
                $rownum = mysqli_num_rows($playersresult);

                while ($clubteamplayer = db_fetch_array( $playersresult )) { 
                    $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
               ?>
               <tr>
                <td> <?=$clubteamplayer['teamplayername']?></td>
                </tr>
                <?  }  ?>
                </tbody>
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
    </table>

<div class="mb-3">
        Last Updated: 
        
        <?

if (isDebugEnabled(1)) logMessage("club_team_manage: lastUpdated is is $lastUpdated");
        if( is_null($lastUpdated) ){
            $lastupdated_label = "Never";
          } else {
            //$lastupdated = ladderdetails['lastUpdated'];
            $lastupdated_label = $lastUpdated;
          }
          ?>
          <?=$lastupdated_label?> 
        </div>

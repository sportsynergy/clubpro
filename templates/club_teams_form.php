

<table class="table table-striped">

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
 
         <thead>
               <tr>
                   <th>
                       
                           <div class="bigbanner">  <?=$clubteam['name']?> </div>
                           <div class="boxdateheader">
                                Total Points: <?=$clubteam['score']?> 
                                Total Games: <?=$clubteam['games']?>
                            </div>
                       
                   </th>
               </tr>
               </thead>
               <tbody>
               
               <? 
                $playersresult = getClubTeamMembers( $clubteam['id'] );
                $rownum = mysqli_num_rows($playersresult);

                while ($clubteamplayer = db_fetch_array( $playersresult )) {  ?>
               <tr>
                <td> <?=$clubteamplayer['teamplayername']?></td>
                </tr>
                <?  }  ?>
                </tbody>
           </table>

   
    <?  
    // update counter
    ++$count;
    if($count==2) { 
       
        $count = 0; ?>
        </tr >
    <? } ?>
  <?  } ?>

<div class="mb-3">
        <span >Last Updated: </span>
        
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


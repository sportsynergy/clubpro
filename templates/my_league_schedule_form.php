<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/sorttable.js" type="text/javascript"></script>

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<?php
// For each box league that the person is in

$boxesforuserresult = getBoxLeaguesForUser( get_userid() );
while ($boxesforuserarray = db_fetch_array($boxesforuserresult)) {

?>



<div class="smallbanner"> 
    <?=$boxesforuserarray['boxname'] ?> - <?=$boxesforuserarray['teamname'] ?>
</div>


<table class="table table-striped sortable">
        <thead>
        <tr >
                <th >Opponent</th>
                <th>Team</th>
                <th>Match 1</th>
                <th>Match 2</span></th>
            </tr>
        </thead>
        <tbody>

            <?php

            $league_result = load_league_schedule( $boxesforuserarray['boxid']);
            $rownum = mysqli_num_rows($league_result);
             while ($league_array = db_fetch_array($league_result)) {
                $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";

                $recent = getRecentLadderMatches( $league_array['userid'] , $boxesforuserarray['boxid'])
            ?>
                <form name="playerform<?=$rownum?>" method="get"
                    action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php">
                    <input type="hidden" name="userid"
                        value="<?=$league_array['userid']?>"> <input type="hidden"
                        name="origin" value="schedule">
                </form>

            <tr class="<?=$rc?>">
               <td> 
                
                <a href="javascript:submitForm('playerform<?=$rownum?>')"><?=$league_array['fullname']?> </a>
                </td>
               <td> <?=$league_array['name'] ?> </td>
               <td>  <?=$recent[0] ?></td>
               <td> <?=$recent[1] ?> </td>
            </tr>
            <? 
            $rownum = $rownum - 1;
            } 
            ?>
           </tbody>
</table>

<? } ?>


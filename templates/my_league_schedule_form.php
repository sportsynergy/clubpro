<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/sorttable.js" type="text/javascript"></script>

<p>
Here are your box league results:
</p>
<br>

<?php
// For each box league that the person is in

$boxesforuserresult = getBoxLeaguesForUser( get_userid() );
while ($boxesforuserarray = db_fetch_array($boxesforuserresult)) {

?>



<font class="smallbanner"> 
    <?=$boxesforuserarray['boxname'] ?> - <?=$boxesforuserarray['teamname'] ?>

</font>


<table width="350" cellpadding="0" cellspacing="0" class="bordertable sortable">
            <tr valign="top" class=clubid<?=get_clubid()?>th >
                <td >
                    <span class="whitenorm">Opponent</span>
                </td>
                <td>
                    <span class="whitenorm">Team</span>
                </td>
                <td>
                    <span class="whitenorm">Match 1</span>
                </td>
                <td>
                    <span class="whitenorm">Match 2</span>
                </td>
            </tr>

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
           
</table>

<? } ?>


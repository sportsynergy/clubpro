
<?php
// For each box league that the person is in
if (isDebugEnabled(1)) logMessage("my_league_schedule:getting boxes for user");
while ($boxesforuserarray = db_fetch_array($boxesforuserresult)) {

?>

<table width="350" cellpadding="0" cellspacing="0" class="bordertable">
            <tr valign="top" class=clubid<?=get_clubid()?>th >
                <th >Opponent</th>
                <th>Team</th>
                <th>Match 1</th>
                <th>Match 2</th>
            </tr>

            <?php

            $league_result = load_league_schedule( $boxesforuserarray['boxid']);
             while ($league_array = db_fetch_array($league_result)) {
            ?>
            <tr>
               <td> <?=$league_array['fullname'] ?></td>
               <td> <?=$league_array['name'] ?> </td>
               <td>1-2</td>
               <td>2-3</td>
            </tr>
            <? } ?>
           
</table>

<? } ?>


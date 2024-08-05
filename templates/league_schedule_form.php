<link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css" />
<script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
  



<div>
  <form name="entryform" method="post" action="<?=$ME?>">
    <table width="80%" cellpadding="20" cellspacing="0" class="generictable" id="formtable">
      <tr class="borderow">
        <td class=clubid<?=get_clubid()?>th><font class=whiteh1>
          <div align="center">
            <? pv($DOC_TITLE) ?>
          </div>
          </font></td>
      </tr>
      <tr>
        <td><table cellspacing="5" cellpadding="0" class="borderless">
            
            <tr>
              <th>Players</th>
              <th>League</th>
              <th>Score</td>
            </td>

            <? 
             while ($leagues = mysqli_fetch_array($league_schedule)) {
            ?>
            <tr>
              <td><?=$leagues['player1'] ?> vs. <?=$leagues['player2']  ?></td>
              <td><?=$leagues['boxname'] ?></td>
              <td><a href="">Score</a></td>
            </td>
            <? } ?>
          </table>
        </td>
      </tr>
    </table>
  </form>
</div>

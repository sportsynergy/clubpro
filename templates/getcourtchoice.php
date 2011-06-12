<?php
/*
 * $LastChangedRevision: 2 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2006-05-26 15:46:27 -0500 (Fri, 26 May 2006) $

*/
?>
<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

     <tr>
      <td>

            <form name="courtchoiceform" method="post" action="<?=$ME?>">
                  Select Court Type Reservation: <br><br>
                  <?
                   while($row = mysql_fetch_object($courtformresult)) {
                    if( $row->playernum==2){
                     echo "Singles &nbsp; <input type=\"radio\" name=\"courtchoice\" value=\"singles\"><br>";
                    }
                    if( $row->playernum==4){
                      echo "Doubles <input type=\"radio\" name=\"courtchoice\" value=\"doubles\"><br>";
                    }
                   }
                  if(get_roleid()>1){
                     echo "Event &nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"courtchoice\" value=\"event\"><br>";
                  }

                  ?>

                  <input type="hidden" name="time" value="<?pv($time)?>">
                  <input type="hidden" name="courtid" value="<?pv($courtid)?>">
                  <br>
                  <input type="submit" name="submitchoice" value="Submit">
            </form>

        </td>
      </tr>
</table>
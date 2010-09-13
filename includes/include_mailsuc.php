<?php
/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $
 */
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

     <tr>
      <td class="normal">
        
         
         <? if( mysql_num_rows($emailResult)> 0){  ?>
         	
         	 Your message was sent successfully to these people.  To send another one click <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">here</a><br><br>
	         	<? mysql_data_seek($emailResult, 0);
	             while($row = mysql_fetch_array($emailResult)){ ?>
	             	<?=$row[0]?> <?=$row[1]?><br>  	
	       		<? } ?>

       <? } 
       //No one found
       else{ ?>
       	
       	Hey, nobody was found at your club like this.  Go ahead, give it another shot and click <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">here</a></td>
       	
       <? } ?>
      
         
       
         
         
         </td>
      </tr>
</table>
<?
/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
 */
?>

<table cellspacing="0" cellpadding="0" border="0" width="710" align="center">

     <tr>
      <td class="normal">
        
         
         <? if( mysql_num_rows($emailResult)> 0){  ?>
         	
         	 Your message was sent successfully to these people.  To send another one click <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">here</a><br><br>
	         	<? mysql_data_seek($emailResult, 0);
	            $counter = 0; 
	         	while($row = mysql_fetch_array($emailResult)){ 
	             
	         		if($counter!=0){
	         		print ",";
	         		}
	             	?>
	             	<?=$row[0]?> <?=$row[1]?> 	
	       		<? 
	         	++$counter;
	         	} 
	         	
	         	?>

       <? } 
       //No one found
       else{ ?>
       	
       	Hey, nobody was found at your club like this.  Go ahead, give it another shot and click <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_mailer.php">here</a></td>
       	
       <? } ?>
      
         
       
         
         
         </td>
      </tr>
</table>
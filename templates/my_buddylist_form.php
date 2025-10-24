
<?php
//Set the http variables
$action = $_REQUEST["action"];
$bid = $_REQUEST["bid"];

if(isset($action) && $action=="remove"){

	//Remove Buddy
	$qid1 = db_query("DELETE FROM tblBuddies
                     WHERE bid = $bid");
}
?>


<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" onSubmit="SubDisable(this)" autocomplete="off">
	
<label for="name" class="form-label">Add A Buddy</label>
<input id="name" name="name" type="text" size="30" class="form-control form-autocomplete"/> 
<input id="id" name="buddy" type="hidden" size="30" /> 
<script>
	<?php
		$wwwroot = $_SESSION["CFG"]["wwwroot"];
		pat_autocomplete( array(
			'baseUrl'=> "$wwwroot/users/ajaxServer.php",
			'source'=>'name',
			'target'=>'id',
			'className'=>'autocomplete',
			'parameters'=> "action=autocomplete&name={name}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
			'progressStyle'=>'throbbing',
			'minimumCharacters'=>3,
			));

		?>
	</script>

	<div class="mt-2 mb-5">
		<button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Add Buddy</button>
	</div>

</form>
					
		<?php
       //List out all of the players Buddies
       $query = "SELECT buddies.bid, users.firstname, users.lastname, users.userid
                 FROM tblUsers users, tblClubUser clubuser, tblBuddies buddies
                 WHERE users.userid = buddies.buddyid
				 AND clubuser.userid = users.userid
                 AND  buddies.userid=".get_userid()."
				 AND clubuser.enddate IS NULL
                 AND clubuser.enable = 'y'
                 AND clubuser.clubid = ".get_clubid();

       // run the query on the database
       $result = db_query($query); 
       
        if( isDebugEnabled(1) ) logMessage("my_buddylist_form: found ". mysqli_num_rows($result). " buddies");
        
    
        if( mysqli_num_rows($result) == 0 ){ ?>
         <div class="mb-3">
            
		</div>
        
          <?php } else {  ?>
          
			<table class="table table-striped">
               
				 <? while($row = mysqli_fetch_row($result)) {  ?>

                <tr>
                  <td >
                    	<?=$row[1]?>  <?=$row[2]?>
                  </td>
                 
                  	<td>
						<a href="my_buddylist.php?action=remove&bid=<?=$row[0]?>"> Remove </a>
					</td>
                </tr>
                
				<? } ?>
             
				
			</td>
          </tr>
		</table>
          <? } ?>
    


		<?php   if( mysqli_num_rows($result) > 0 ){  ?>
		<div style="height: 2em;"></div>
		<div>
		<?php
		//Get the email addresses of the players to put into the mailto
		$emailArray = getBuddyEmailAddresses(get_userid());
		$emailString = implode(",", $emailArray);

		?>
			<span> 
				<a href="mailto:<?=$emailString?>">Send an email to these guys</a>
			</span>
		</div>
		<?php } ?>




<script language="Javascript">


document.getElementById('name').setAttribute("autocomplete", "off");

document.onkeypress = function(aEvent)
{
    
    if(!aEvent) aEvent=window.event;
  	key = aEvent.keyCode ? aEvent.keyCode : aEvent.which ? aEvent.which : aEvent.charCode;
    if( key == 13 ) // enter key
    {
        return false; // this will prevent bubbling ( sending it to children ) the event!
    }
}


function onSubmitButtonClicked(){
	submitForm('entryform');
}


 
</script>

<?

/*
 * $LastChangedRevision: 838 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-02-23 00:14:23 -0600 (Wed, 23 Feb 2011) $
*/

$clubEvent = mysql_fetch_array($clubEventResult);

?>


<form name="removemefromeventform" method="post" action="<?=$ME?>">
   <input type="hidden" name="userid" value="<?=get_userid()?>">
   <input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
   <input type="hidden" name="cmd" value="removefromevent">
</form>

<form name="addmetoeventform" method="post" action="<?=$ME?>">
   <input type="hidden" name="userid" value="<?=get_userid()?>">
   <input type="hidden" name="clubeventid" value="<?=$clubEvent['id']?>">
   <input type="hidden" name="cmd" value="addtoevent">
</form>
		

<div id="leftPanel" style="float:left; width: 350px; padding-right: 10px">

<span class="biglabel"><?=$clubEvent['name']?></span><br/>
<span class="italitcsm"><?=formatDateString($clubEvent['eventdate'])?></span>
<div>
<span class="normal">
	<?=$clubEvent['description']?>
</span>
</div>


</div>


<div id="rightPanel" style="float: left">
<span class="biglabel">Who is coming </span> 
<? if( is_logged_in() ){

	if( $alreadySignedUp ){ ?>
		<span class="normalsm"><a href="javascript:submitForm('removemefromeventform');">Take me out</a></span>
	<? }else{ ?>
		<span class="normalsm"><a href="javascript:submitForm('addmetoeventform');">Put me down</a></span>
	<? } ?>
<? } ?>
<ul class="whoscoming">

<? 

if( mysql_num_rows($clubEventParticipants)==0){ ?>
	
	<li>No one has signed up yet. 
	
	<? if( is_logged_in() ){ ?>
	 	Why don't you be the first one?
	<? } ?>
	
	</li>
<? }else{
	while($participant = mysql_fetch_array($clubEventParticipants)){?>
	<li><?=$participant['firstname']?> <?=$participant['lastname']?></li>
	<? } }?>



</ul>
</div>





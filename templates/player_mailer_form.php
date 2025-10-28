
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">


 <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">To:</label>
    <select name="who" id="whoselect" class="form-select" aria-label="Default select example">
            <option value="allplayers">All Players</option>
            <option value="allWomen">All Women</option>
            <option value="allMen">All Men</option>
           <? if( isSiteBoxLeageEnabled() ){ ?>
            <option value="boxleaguePlayers">Box League Players</option>
            <? } ?>
            <option value="myBuddies">My Buddies</option>
            <? if(isLadderRankingScheme() ){?>
            <option value="ladderPlayers">Ladder Players</option>
            <? } ?>
    </select>
  </div>

   <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Sport:</label>
     <select name="sport" id="sportselect" class="form-select" aria-label="Default select example">
              
	        	    <option value="all">All</option>
	        	  	<?  while ( $sportarray = db_fetch_array($availbleSports)){ ?>
             			<option value="<? pv($sportarray['courttypeid'] ) ?>"><? pv($sportarray['courttypename'] ) ?>   </option>
        			 <?} ?>
	    </select>
  </div>
		

   <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Ranking:</label>
     <select name="ranking" id="rankingselect" class="form-select" aria-label="Default select example">
              
	        	    <option value="all">All</option>
            			<option value="2.5">2.5</option>
            			<option value="3.0">3.0</option>
            			<option value="3.5">3.5</option>
            			<option value="4.0">4.0</option>
            			<option value="4.5">4.5</option>
            			<option value="5.0">5.0</option>
            			<option value="5.5">5.5</option>
	    </select>
      <? is_object($errors) ? err($errors->ranking) : ""?>
  </div>
       
	 <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Subject:</label>
    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" value="<? pv($frm["subject"]) ?>">
    <? is_object($errors) ? err($errors->subject) : ""?>
  </div>    
  
  <div class="mb-3">
    <textarea name="message" class="form-control" cols="80" rows="15"><? pv($frm["message"]) ?></textarea>
    <? is_object($errors) ? err($errors->useraddress) : ""?>
  </div>   
      
    <button type="submit" class="btn btn-primary">Submit</button>


<input type="hidden" name="submitme" value="submitme">



</form>
</div>


<script language="Javascript">

// please keep these lines on when you copy the source
// made by: Nicolas - http://www.javascript-page.com

function SubDisable(dform) {
  if (document.getElementById) {
   for (var sch = 0; sch < dform.length; sch++) {
    if (dform.elements[sch].type.toLowerCase() == "submit") dform.elements[sch].disabled = true;
   }
  }
return true;
}

function disableRankingDropDown()
{
        
        var listselection = document.getElementById('sportselect');
        if(listselection.value == "all" ){
             document.entryform.ranking.disabled = true;
        }
        else{
        	document.entryform.ranking.disabled = "";
        }
        

}

function disableSportAndRankingDropDown()
{

        var listselection = document.getElementById('whoselect');

        if(listselection.value == "ladderPlayers" ){
             document.entryform.ranking.disabled = true;
             document.entryform.sport.disabled = true;
        }
        else{
        	document.entryform.ranking.disabled = "";
        	document.entryform.sport.disabled = "";
        }
        

}




function onSubmitButtonClicked(){
	submitForm('entryform');
}

</script>
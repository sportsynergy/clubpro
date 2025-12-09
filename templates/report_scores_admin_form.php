


<p class="bigbanner"><? pv($DOC_TITLE) ?></p>

<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#singles" type="button" role="tab" aria-controls="singles" aria-selected="true">
        Singles
    </button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#doubles" type="button" role="tab" aria-controls="doubles" aria-selected="false">
        Doubles
    </button>
  </li>
</ul>


<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active" id="singles" role="tabpanel" aria-labelledby="singles-tab">
   <? include($_SESSION["CFG"]["includedir"]."/include_report_singles.php");?>
  </div>

   <div class="tab-pane fade" id="doubles" role="tabpanel" aria-labelledby="doubles-tab">
    <? include($_SESSION["CFG"]["includedir"]."/include_report_doubles.php");?>
  </div>

</div>





<script>



function onDoublesSubmitButtonClicked(){
	submitForm('doubles_entryform');
}

function onSinglesSubmitButtonClicked(){
	submitForm('singles_entryform');
}

function setMatchScore(){

    document.singles_entryform.score.options.length = 0;
 
    // Singles
    var optioncount = 0;
    for (i=0; i < matchscores.length; i++) {

        if(matchscores[i].courttypeid == document.singles_entryform.courttype.value){
            var opt = new Option( matchscores[i].gameswon+' - '+matchscores[i].gameslost, matchscores[i].gameslost);
            document.singles_entryform.score.options[optioncount] = opt;
            ++optioncount;
        }
      }  

    //Doubles
    var optioncount = 0;
    for (i=0; i < matchscores.length; i++) {

        if(matchscores[i].courttypeid == document.doubles_entryform.courttype.value){
            var opt = new Option( matchscores[i].gameswon+' - '+matchscores[i].gameslost, matchscores[i].gameslost);
            document.doubles_entryform.score.options[optioncount] = opt;
            ++optioncount;
        }
      }        
}

var matchscores = new Array();

<? 
//print out the java script
while ($matchscore = db_fetch_array($allmatchscores)) { ?>
    matchscores.push({courttypeid:<?=$matchscore['courttypeid']?>, gameswon:<?=$matchscore['gameswon']?>, gameslost:<?=$matchscore['gameslost']?>});
 <? } ?>

setMatchScore();


</script>





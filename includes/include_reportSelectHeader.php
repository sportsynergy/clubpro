
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<form name="entryform" method="post" action="<?=$ME?>">

      <div class="mb-3">
           <select name="report" class="form-select" aria-label="Report Selection">
                <option value="">Select Report</option>

                <? if ($frm["report"]== "memberactivity") {
                    $selected_report1 = "selected";
                } elseif ($frm["report"]== "courtutil"){
                    $selected_report2 = "selected";
                }
                    ?>
                <option value="memberactivity" <?=$selected_report1?>>Member Activity Report</option>
                <option value="courtutil" <?=$selected_report2?>>Court Utilization Report</option>
                
                <? if ( isJumpLadderRankingScheme() ) { 
                    
                    // for each ladder 
                    $result = getLaddersForSite( get_siteid() );
                    while($ladder = mysqli_fetch_array($result)){  
                        
                        if ( $ladder['id'] == $ladderid ){
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        
                        ?>
                        <option value="ladderreport-<?=$ladder['id']?>" <?=$selected?>><?=$ladder['name'] ?> Score Report</option>
                        <option value="ladderexport-<?=$ladder['id']?>" <?=$selected?>><?=$ladder['name'] ?> Full Export</option>
                   
                    <? } ?>
               
                <? }  ?>
               </select>

        </div>
<div class="mb-3">
          <input type="hidden" name="submitme" value="submitme">
          <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Run Report</button>
                    </div>
          
          
</form>

<script type="text/javascript">

function onSubmitButtonClicked(){
	submitForm('entryform');
}


</script>                 
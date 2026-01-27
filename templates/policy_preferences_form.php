

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<ul class="nav nav-tabs" id="policy" role="tablist">

    	<li class="nav-item selected" role="presentation" >
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true" onclick="clearMessage()">General Preferences</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="skill-tab" data-bs-toggle="tab" data-bs-target="#skill" type="button" role="tab" aria-controls="skill" aria-selected="true" onclick="clearMessage()">Skill Range Policies</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="true" onclick="clearMessage()">Scheduling Policies</button>
        </li>
  
       <li class="nav-item" role="presentation">
            <button class="nav-link" id="court-events-tab" data-bs-toggle="tab" data-bs-target="#court_events" type="button" role="tab" aria-controls="court events" aria-selected="true" onclick="clearMessage()">Court Events</button>
        </li>
    </ul>  
    
    
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
			 <? include($_SESSION["CFG"]["includedir"]."/include_general_preferences.php");?>
		</div>
        <div class="tab-pane fade show" id="skill" role="tabpanel" aria-labelledby="skill-tab"> 
        	 <? include($_SESSION["CFG"]["includedir"]."/include_skillrange_policies.php");?>
        </div>
        <div class="tab-pane fade show" id="schedule" role="tabpanel" aria-labelledby="schedule-tab"> 
			 <? include($_SESSION["CFG"]["includedir"]."/include_scheduling_policies.php");?>
		</div>
        <div class="tab-pane fade show" id="message" role="tabpanel" aria-labelledby="message-tab"> 
			 <? include($_SESSION["CFG"]["includedir"]."/include_messages_policies.php");?>
		</div>
        <div class="tab-pane fade show" id="court_events" role="tabpanel" aria-labelledby="court-events-tab"> 
			 <? include($_SESSION["CFG"]["includedir"]."/include_court_events.php");?>
		</div>
    </div>
</div>

<?



// Calculate current tab index
if($_REQUEST["preferenceType"]=="skill"){
	$currentTabIndex = 1;
}
if($_REQUEST["preferenceType"]=="schedule"){
	$currentTabIndex = 2;
}
else if($_REQUEST["preferenceType"]=="message"  ) {
	$currentTabIndex = 3;
}
else if($_REQUEST["preferenceType"]=="court_events"  ) {
	$currentTabIndex = 4;
}


?>

<form name="tabIndexForm">
	<input type="hidden" name="tabIndex" value="<?=$currentTabIndex?>"></input>
</form>



<script type="text/javascript"> 


function clearMessage(){
	document.getElementById("message_div").innerHTML = "";
}

function onMessageSubmitButtonClicked(){
	submitForm('message_preferences_form');
}


function onGeneralSubmitButtonClicked(){
	submitForm('general_preferences_form');
}


</script> 
 







 <div id="policy" class="yui-navset" style="width: 680px">
    <ul class="yui-nav">
    	<li class="selected"><a href="#general" onclick="clearMessage()"><em>General Preferences</em></a></li>
        <li ><a href="#skill" onclick="clearMessage()"><em>Skill Range Policies</em></a></li>
        <li><a href="#schedule" onclick="clearMessage()"><em>Scheduling Policies</em></a></li>
        <li><a href="#message" onclick="clearMessage()"><em>Messages</em></a></li>
       <li><a href="#court_events" onclick="clearMessage()"><em>Court Events</em></a></li>
    </ul>            
    <div class="yui-content">
        <div id="general">
			 <? include($_SESSION["CFG"]["includedir"]."/include_general_preferences.php");?>
		</div>
        <div id="skill"> 
        	 <? include($_SESSION["CFG"]["includedir"]."/include_skillrange_policies.php");?>
        </div>
        <div id="schedule">
			 <? include($_SESSION["CFG"]["includedir"]."/include_scheduling_policies.php");?>
		</div>
        <div id="message">
			 <? include($_SESSION["CFG"]["includedir"]."/include_messages_policies.php");?>
		</div>
		 <div id="court_events">
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

(function() {
    var myTabs = new YAHOO.widget.TabView("policy");
    
    var url = location.href.split('#');
    if (url[1]) {
        //We have a hash
        var tabHash = url[1];
        var tabs = myTabs.get('tabs');
        for (var i = 0; i < tabs.length; i++) {
            if (tabs[i].get('href') == '#' + tabHash) {
                myTabs.set('activeIndex', i);
                break;
            }
        }
    }
    else{
		if(document.tabIndexForm.tabIndex.value != ""){
			myTabs.set('activeIndex', document.tabIndexForm.tabIndex.value );
		}
    }
})();


function clearMessage(){
	document.getElementById("message_div").innerHTML = "";
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("message-formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("message-submitbutton", { value: "message-submitbuttonvalue" });
        oSubmitButton1.on("click", onMessageSubmitButtonClicked);

    });

} ();


function onMessageSubmitButtonClicked(){
	submitForm('message_preferences_form');
}

YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("general-formtable", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("general-submitbutton", { value: "general-submitbuttonvalue" });
        oSubmitButton1.on("click", onGeneralSubmitButtonClicked);

    });

} ();


function onGeneralSubmitButtonClicked(){
	submitForm('general_preferences_form');
}


</script> 
 









<div id="recordscores" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Singles</em></a></li>
        <li><a href="#tab2"><em>Doubles</em></a></li>

    </ul>            
    <div class="yui-content">
        <div id="tab1"> 
        	<? include($_SESSION["CFG"]["includedir"]."/include_report_singles.php");?>
        </div>
        <div id="tab2">
			 <? include($_SESSION["CFG"]["includedir"]."/include_report_doubles.php");?>
		</div>
        
    </div>
</div>
<?
// Calculate current tab index
 if($_REQUEST["usertype"]=="doubles" ) {
	$currentTabIndex = 1;
}


?>

<form name="tabIndexForm">
	<input type="hidden" name="tabIndex" value="<?=$currentTabIndex?>"></input>
</form>



<script>
(function() {
    var myTabs = new YAHOO.widget.TabView('recordscores');

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


YAHOO.example.init = function () {

    YAHOO.util.Event.onContentReady("formtable-doubles", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("dsubmitbutton", { value: "dsubmitbutton1value" });
        oSubmitButton1.on("click", onDoublesSubmitButtonClicked);

    });

    YAHOO.util.Event.onContentReady("formtable-singles", function () {

        var oSubmitButton1 = new YAHOO.widget.Button("ssubmitbutton", { value: "ssubmitbutton1value" });
        oSubmitButton1.on("click", onSinglesSubmitButtonClicked);

    });

} ();


function onDoublesSubmitButtonClicked(){
	submitForm('doubles_entryform');
}

function onSinglesSubmitButtonClicked(){
	submitForm('singles_entryform');
}


</script>





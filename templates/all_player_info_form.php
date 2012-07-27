
<div id="recordscores" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>Profile</em></a></li>
        <li><a href="#tab2"><em>History</em></a></li>

    </ul>            
    <div class="yui-content">
        <div id="tab1"> 
        	<? include($_SESSION["CFG"]["includedir"]."/include_admin_player_info.php");?>
        </div>
        <div id="tab2">
			 <? include($_SESSION["CFG"]["includedir"]."/include_admin_player_history.php");?>
		</div>
        
    </div>
</div>

<script>
(function() {
    var tabView = new YAHOO.widget.TabView('recordscores');

   
})();
</script>

<div style="height: 2em;"></div>
<div style="text-align: left;">
<form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">
     <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
     <input type="hidden" name="searchname" value="<?=$searchname?>">
  </form>
 </div>




    
        
        
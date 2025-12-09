
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<ul class="nav nav-tabs" id="policy" role="tablist">

    	<li class="nav-item selected" role="presentation" >
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true" >Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="true" >History</button>
        </li>
    </ul> 

     <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
			 <? include($_SESSION["CFG"]["includedir"]."/include_admin_player_info.php");?>
		</div>
        <div class="tab-pane fade show" id="history" role="tabpanel" aria-labelledby="history-tab"> 
        	 <? include($_SESSION["CFG"]["includedir"]."/include_admin_player_history.php");?>
        </div>
    </div>



<div style="height: 2em;"></div>
<div style="text-align: left;">
<form name="backtolistform" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/player_lookup.php">
     <a href="javascript:submitForm('backtolistform');"><< Back to List</a></td>&nbsp;&nbsp;&nbsp;
     <input type="hidden" name="searchname" value="<?=$searchname?>">
  </form>
 </div>




    
        
        

<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<div class="container">
  <div class="row justify-content-center">
    <div class="col">

    <div class="mb-3">
      <label for="username" class="form-label">First Name:</label>
      <input class="form-control" id="username" type="text" aria-label="Username" value="<?=$frm["firstname"] ?>" readonly>  
  </div>
  <div class="mb-3">
      <label for="lastname" class="form-label">Last Name:</label>
      <input class="form-control" id="lastname" type="text" aria-label="Lastname" value="<?=$frm["lastname"] ?>" readonly>  
  </div>
  <div class="mb-3">
      <label for="email" class="form-label">Email:</label>
      <input class="form-control" id="email" type="email"  aria-label="Email" value="<?=$frm["email"] ?>" readonly>  
  </div>

  <? if(!empty($frm["cellphone"])){?>

    <div class="mb-3">
      <label for="mobilephone" class="form-label">Mobile Phone:</label>
      <input class="form-control" id="mobilephone" type="text"  aria-label="Mobile Phone" value="<?=$frm["cellphone"] ?>" readonly>  
  </div>

  <? } ?>

  <? if(!empty($frm["msince"])){?>
      <label for="membersince" class="form-label">Member Since:</label>
      <input class="form-control" id="membersince" type="text"  aria-label="Member Since" value="<?=$frm["msince"] ?>" readonly>  
    <? } ?>

     <?
		   // Get the Custom Parameters
		  while( $parameterArray = db_fetch_array($extraParametersResult)){ 
			
			$parameterValue = load_site_parameter($parameterArray['parameterid'], $userid );
	
			if($parameterArray['parametertypename'] == "text" && !empty($parameterValue) ){ ?>
          <div class="mb-3">
          <label for="<? pv($parameterArray['parameterlabel'])?>" class="form-label"><? pv($parameterArray['parameterlabel'])?></label>
           <input class="form-control" id="<? pv($parameterArray['parameterlabel'])?>" type="text" aria-label="<? pv($parameterArray['parameterlabel'])?>" value="<? pv($parameterValue) ?>" readonly>   
      </div>  
               
      <? } elseif($parameterArray['parametertypename'] == "select" && !empty($parameterValue) ) { ?>
        <div class="mb-3">
        <label for="<? pv($parameterArray['parameterlabel'])?>" class="form-label"><? pv($parameterArray['parameterlabel'])?></label>
        <input class="form-control" id="<? pv($parameterArray['parameterlabel'])?>" type="text" aria-label="<? pv($parameterArray['parameterlabel'])?>" value="<? pv( load_parameter_option_name($parameterArray['parameterid'], $parameterValue)  ) ?>" readonly>
        </div>
      <? }  ?>
          


      <? } ?>
          
  

      </div> <!-- .col-md-8 -->
      <div class="col">

      <div class="mb-3">

        <?  if( isset($frm["photo"]) ){ ?>
                            <img src="data:image/jpg;charset=utf8;base64,<?php echo base64_encode($frm["photo"]); ?>" width="180" height="180">
                        <?   } else{  ?>
						                <img src="<?=get_gravatar($frm["email"],180 )?>" />
                        <?   }   ?>
                        </div>
        </div>
</div> <!-- .row justify-content-center -->
</div> <!-- .container -->

    

          
<!-- TODO: Put in the jump ladder recent matches -->
           
  
      <?php
      if( $origin == 'ladder') { ?>
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php">< Back to ladder</a>
      <? } elseif ($origin == 'league') { ?>
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?echo get_sitecode()?>/web_ladder.php">< Back to leagues</a>
        
        <? } elseif ($origin == 'schedule') { ?>
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/my_league_schedule.php">< Back to the ladder schedule</a>
        
        <? } ?>
            





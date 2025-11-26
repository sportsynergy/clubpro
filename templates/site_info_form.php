
<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>


<div class="container">


    <div>
      <label for="sitename" class="form-label">Name:</label>
      <input class="form-control-plaintext" id="sitename" type="text" aria-label="sitename" value="<?=$sitedetail['sitename']?>" readonly>  
  </div>

  <div>
      <label for="sitecode" class="form-label">Site Code:</label>
      <input class="form-control-plaintext" id="sitecode" type="text" aria-label="sitecode" value="<?=$sitedetail['sitecode']?>" readonly>  
  </div>

  <div>
      <label for="selfcancel" class="form-label">Allow Self Cancel:</label>
      <input class="form-control-plaintext" id="selfcancel" type="text" aria-label="selfcancel" value="<?=$sitedetail['allowselfcancel']?>" readonly>  
  </div>

  <div>
      <label for="autologin" class="form-label">Auto Login:</label>
      <input class="form-control-plaintext" id="autologin" type="text" aria-label="autologin" value="<?=$sitedetail['enableautologin']?>" readonly>  
  </div>

  <div>
      <label for="rankingadjustment" class="form-label">Ranking Adjustment:</label>
      <input class="form-control-plaintext" id="rankingadjustment" type="text" aria-label="rankingadjustment" value="<?=$sitedetail['rankingadjustment']?>" readonly>  
  </div>

  <div>
      <label for="soloreservations" class="form-label">Allow Solo Reservations</label>
      <input class="form-control-plaintext" id="soloreservations" type="text" aria-label="soloreservations" value="<?=$sitedetail['allowsoloreservations']?>" readonly>  
  </div>

  <div>
      <label for="daysahead" class="form-label">How Many Days Can Members Book a Court:</label>
      <input class="form-control-plaintext" id="daysahead" type="text" aria-label="daysahead" value="<?=$sitedetail['allowsoloreservations']?>" readonly>  
  </div>

	<div>
	<label class="form-label">Courts</label> 
	<a href="<?=$_SESSION["CFG"]["wwwroot"]?>/admin/add_court.php">add</a>
	<ul>
	<? while($court = mysqli_fetch_array($sitecourts)){ ?>
			<li><?=$court['courtname']?></li>
		<? } ?>	
	</ul>
	</div>

	</div>


				



    
        
        
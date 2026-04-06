<!doctype html>
<html lang="en">
  <head>
    <?php
      if( isset($trackingid) ){
      include_once("analyticstracking.php") ;
    }
    ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
   	

	<!-- Misc -->
	<link rel="icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/favicon.ico" type="image/x-icon" />
	<link rel="apple-touch-icon" href="<?=$_SESSION["CFG"]["imagedir"]?>/twitter1.png" type="image/x-icon" />
	<link href="<?=$_SESSION["CFG"]["wwwroot"]?>/css/main.css" rel="stylesheet" type="text/css" />
	<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js?123" type="text/javascript"></script>



 <?php
      if( isset($trackingid) ){
      include_once("analyticstracking.php") ;
    }
    ?>

</head>


<body>
<div class="container">
<div class="row mt-4" ></div>

<div class="row">
		<div class="col-2">	</div>
		<div class="col-8">

		<? include($_SESSION["CFG"]["templatedir"]."/form_header.php"); ?>
		<div class="mb-5">
			<div style="float:left; margin-right: 20px;">
				<img src="<?=$_SESSION["CFG"]["wwwroot"]?>/images/logo.png?123" class="img-fluid" alt="ClubPro Logo" width="50" height="50">
			</div>	
			<div>
				<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
			</div>
		</div>

		<div class="mb-5 mt-5">


		Thanks for looking into our online court reservation service.  Trying out this software before you buy it is a 
		great way to see how our online service will fit in at your club.  As many of our current customers have found, once you see
		how simple this system is you're not going to want to go back.  So, go head, take the first step and register.  
		After you submit this form you will be sent an email with some more instructions.  
		</div>

			<form name="entryform" method="post" action="<?=$ME?>">
						
			<div class="mb-3">
				<label for="clubname" class="form-label">Club Name:</label>
				<input class="form-control" name="clubname" id="clubname" value="<? pv($frm["clubname"]) ?>"type="text" aria-label="Club Name">
				<? is_object($errors) ? err($errors->clubname) : ""?>
			</div>

			<div class="mb-3">
				<label for="clubname" class="form-label">Club Code:</label>
				<input class="form-control" name="clubcode" id="clubcode" value="<? pv($frm["clubcode"]) ?>" type="text" aria-label="Club Code">
				<div id="codeHelp" class="form-text">Think of this as your call sign</div>
				<? is_object($errors) ? err($errors->clubcode) : ""?>
			</div>

				<div class="mb-3">
				<label for="clubname" class="form-label">Administrator Username:</label>
				<input class="form-control" name="adminuser" id="adminuser" value="<? pv($frm["adminuser"]) ?>" type="text" aria-label="Administrator Username">
				<? is_object($errors) ? err($errors->adminuser) : ""?>
			</div>

			<div class="mb-3">
				<label for="clubname" class="form-label">Administrator Password:</label>
				<input class="form-control" name="adminpass1" id="adminpass1" value="<? pv($frm["adminpass1"]) ?>" type="password" aria-label="Administrator Password">
				<? is_object($errors) ? err($errors->adminpass1) : ""?>
			</div>

			<div class="mb-3">
				<label for="clubname" class="form-label">Administrator Password (again):</label>
				<input class="form-control" name="adminpass2" id="adminpass2" value="<? pv($frm["adminpass2"]) ?>" type="password" aria-label="Administrator Password (again)">
				<? is_object($errors) ? err($errors->adminpass2) : ""?>
			</div>
			
			<div class="mb-3">
				<label for="clubname" class="form-label">Administrator Email:</label>
				<input class="form-control" name="adminemail" id="adminemail" value="<? pv($frm["adminemail"]) ?>" type="text" aria-label="Administrator Email">
				<? is_object($errors) ? err($errors->adminemail) : ""?>
			</div>

			<div class="mb-3">
				<label for="clubname" class="form-label">Administrator First Name:</label>
				<input class="form-control" name="adminfirstname" id="adminfirstname" value="<? pv($frm["adminfirstname"]) ?>" type="text" aria-label="Administrator First Name">
				<? is_object($errors) ? err($errors->adminfirstname) : ""?>
			</div>

				<div class="mb-3">
				<label for="clubname" class="form-label">Administrator Last Name:</label>
				<input class="form-control" name="adminlastname" id="adminlastname" value="<? pv($frm["adminlastname"]) ?>" type="text" aria-label="Administrator Last Name">
				<? is_object($errors) ? err($errors->adminlastname) : ""?>
			</div>
						
			<div class="mb-3">
				<label for="timezone" class="form-label">Timezone</label>			 
				<select name="timezone" class="form-select" id="timezone">
									<option value="">Select Timezone</option>
								<? while($timezoneArray = mysqli_fetch_array($availbleTimezones)){ ?>
									<option value="<?=$timezoneArray['offset']?>"><?=$timezoneArray['name']?></option>
								<? }?>
								</select>
			</div>

			<div class="mb-3">
				<label for="courts" class="form-label">Number of Courts:</label>
							
								<select name="courtnumber" class="form-select" id="courtnumber">
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
									<option value="6">6</option>
									<option value="7">7</option>
									<option value="8">8</option>
									<option value="9">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
								</select>
			</div>

			<div class="mb-3">
				<label for="courttype" class="form-label">Court Type:</label>
								
							
								<select name="courttype" class="form-select" id="courttype">
									<option value="2">Softball Singles Squash</option>
									<option value="3">Hardball Doubles Squash</option>
									<option value="4">Hardball Singles Squash</option>
									<option value="6">Racquetball</option>
									<option value="7">Tennis</option>
								</select>
			</div>

			<div class="mt-5">
				<input type="button" class="btn btn-primary" onclick="onSubmitButtonClicked()" value="Register Club">
				<input type="hidden" name="submitme" value="submitme" >
			</div>
			</form>

		</div>
		<div class="col-2"> </div>
	</div>
</div>	<!-- end of row -->

<div class="row mb-4" ></div>
<div> <!-- end of container -->



<script type="text/javascript" >

	function onSubmitButtonClicked(){
		submitForm('entryform');
	}

	// don't want the browser to auto fill any of these fields, so turn off autocomplete for all of them
	document.getElementById('clubname').setAttribute("autocomplete", "off");
	document.getElementById('clubcode').setAttribute("autocomplete", "off");
	document.getElementById('adminuser').setAttribute("autocomplete", "off");
	document.getElementById('adminpass1').setAttribute("autocomplete", "off");
	document.getElementById('adminpass2').setAttribute("autocomplete", "off");
	document.getElementById('adminemail').setAttribute("autocomplete", "off");
	document.getElementById('adminfirstname').setAttribute("autocomplete", "off");
	document.getElementById('adminlastname').setAttribute("autocomplete", "off");
	
	
</script>



</body>
</html>
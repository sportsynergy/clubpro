<div class="mb-5">
<p class="bigbanner"><? pv($DOC_TITLE) ?></p>
</div>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_ladder.php" >
            <? pv($ladderdetails->name ) ?>
        </a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Add Ladder Player</li>
  </ol>
</nav>

<form name="entryform" method="post" action="<?=$ME?>" autocomplete="off">

<div class="mainpanel">

 <div class="mb-3" style="width: 50%">
    <label for="username" class="form-label">Player:</label>
    <input id="name1" name="playeronename" type="text" size="30" class="form-control form-autocomplete" autocomplete="off"/> 
	<input id="id1" name="userid" type="hidden" />
    <script>
        <?
        $wwwroot =$_SESSION["CFG"]["wwwroot"] ;
            pat_autocomplete( array(
                'baseUrl'=> "$wwwroot/users/ajaxServer.php",
                'source'=>'name1',
                'target'=>'id1',
                'className'=>'autocomplete',
                'parameters'=> "action=autocomplete&name={name1}&userid=".get_userid()."&siteid=".get_siteid()."&clubid=".get_clubid()."",
                'progressStyle'=>'throbbing',
                'minimumCharacters'=>3,
                ));
    
            ?>

        </script>
    <? is_object($errors) ? err($errors->playeronename) : ""?>
  </div>


    <div class="mb-3" style="width: 50%">
      <label for="spot" class="form-label">Spot:</label>
     <select name="placement" class="form-select">
				<?
				mysqli_data_seek($ladderplayers,0);

				$lastspot = mysqli_num_rows($ladderplayers) + 1;

				for ( $i = 1; $i<= mysqli_num_rows($ladderplayers)+1; ++$i){

					$selected = "";
					if($i == $lastspot ){
						$selected = "selected=\"selected\"";
					}

					?>
					<option value="<?=$i?>" <?=$selected?>>
					<?=$i?>
					</option>
					<? } ?>

				</select>
      <? is_object($errors) ? err($errors->spot) : ""?>  
    </div>


  <div class="mt-5">
  <button type="submit" class="btn btn-primary" onclick="onSubmitButtonClicked()">Add Player</button>
    <input id="ladderid" name="ladderid" value="<?=$ladderid?>" type="hidden" />
    </div>
  </form>
  
  </div> <!-- mainpanel -->

<script type="text/javascript">


document.getElementById('name1').setAttribute("autocomplete", "off");

function onSubmitButtonClicked(){
	submitForm('entryform');
}
</script> 

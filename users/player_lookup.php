<?php

include ("../application.php");
require ($_SESSION["CFG"]["libdir"] . "/UserClubRelation.php");
$DOC_TITLE = "Member Directory";
require_loginwq();

// Log user out if they are in the wrong club
$userRelation = new UserClubRelation();
if($userRelation->isUserLoggedin()){
	if($userRelation->IsClubMember() == false){
		$userRelation->KillUserSession();
	}
}

// Set the http variables
// Updated to GET method

$searchname = $_GET["searchname"];

//Check to see if the view is empty

if (isset($searchname)) {
    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"] . "/users/player_lookup.php";
    
        if (empty($errormsg)) {

          $playerResults = get_player_search($searchname);

          if (mysqli_num_rows($playerResults) < 1) {
            $noticemsg = "Nobody by that name here.";
            include ($_SESSION["CFG"]["templatedir"] . "/header.php");
           include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
        } else {
            include ($_SESSION["CFG"]["templatedir"] . "/header.php");
            include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
            print_players($searchname, $playerResults, $DOC_TITLE, $ME);
        }
            include ($_SESSION["CFG"]["templatedir"] . "/footer.php");
            die;
        
}
}
include ($_SESSION["CFG"]["templatedir"] . "/header.php");
include ($_SESSION["CFG"]["templatedir"] . "/player_lookup_form.php");
include ($_SESSION["CFG"]["templatedir"] . "/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/
function validate_form($searchname) {

    /* validate the signup form, and return the error messages in a string.  if
     * the string is empty, then there are no errors */
    $errors = new clubpro_obj;
    $msg = "";
    
    if (strpos($searchname, "'") !== false) {

        //$errors->searchname = true;
        $msg.= "No speical characters please. ";
    }
    
  
    return $msg;
}
/**
 *
 * @param unknown_type $searchname
 * @param unknown_type $playerResults
 * @param unknown_type $DOC_TITLE
 * @param unknown_type $ME
 */
function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {
    
        mysqli_data_seek($playerResults, 0);
        $num_fields = mysqli_num_fields($playerResults);
        $num_rows = mysqli_num_rows($playerResults);
?>


<table class="table table-striped" id="playerlookuptable">
  <thead>
<tr>
    <th height="25"><span>First Name</span></th>
    <th height="25"><span>Last Name</span></th>
    <th height="25"><span>Email</span></th>
    <th height="25"><span>Mobile Phone</span></th>
    <th></th>
  </tr>
    </thead>
    <tbody>
  <?php
       $rownum = mysqli_num_rows($playerResults);
       while ($playerarray = mysqli_fetch_array($playerResults)) {      
  ?>
  <tr >
    <form name="playerform<?=$rownum?>" method="get" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
      <td><div >
          <?=$playerarray[1]?>
        </div></td>
      <td><div >
          <?=$playerarray[2]?>
        </div></td>
      <td><div ><a href="mailto:<?=$playerarray[3]?>">
          <?=$playerarray[3]?>
          </a></div></td>
      <td><div >
          <?=$playerarray[6]?>
        </div></td>
      <input type="hidden" name="userid" value="<?=$playerarray[0]?>">
      <input type="hidden" name="searchname" value="<?=$searchname?>">
      <input type="hidden" name="origin" value="lookup">
      <td><div>
        <a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
    </form>
  </tr>
  				<?php 
          
        $rownum = $rownum - 1;} 
          
          ?>
          </tbody>
</table>



<?  } ?>






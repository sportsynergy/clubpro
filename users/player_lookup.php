<?

/*
 * $LastChangedRevision: 857 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2011-03-14 23:08:03 -0500 (Mon, 14 Mar 2011) $

*/

include("../application.php");

$DOC_TITLE = "Member Directory";
require_loginwq();


//Set the http variables
$searchname = $_REQUEST["searchname"];

//Check to see if the view is empty


if (isset($searchname)) {

    $errormsg = validate_form($searchname);
    $backtopage = $_SESSION["CFG"]["wwwroot"]."/users/player_lookup.php";

           if ( empty($errormsg) ) {
               	   $playerResults = get_player_search($searchname);
                   include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
                   print_players($searchname, $playerResults, $DOC_TITLE, $ME);
                   include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");
                   die;
           }
          
}

include($_SESSION["CFG"]["templatedir"]."/header_yui.php");
include($_SESSION["CFG"]["templatedir"]."/player_lookup_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer_yui.php");


/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($searchname) {


/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

       	if(strpos ($searchname, "'")!==false){
                //$errors->searchname = true;
                $msg .= "No speical characters please. ";

         }

	   if( get_roleid()==1 && empty($searchname)){
                $errors->searchname = true;
                $msg .= "Wait...who are you looking for again?. ";

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

        if( mysql_num_rows($playerResults)<1 ){
        	$errormsg = "D'oh, nobody by that name here.";
        	include($_SESSION["CFG"]["includedir"]."/errorpage.php");
        }
        else{
        	

		include($_SESSION["CFG"]["templatedir"]."/player_lookup_form.php");

        mysql_data_seek($playerResults,0);
        $num_fields = mysql_num_fields($playerResults);
        $num_rows = mysql_num_rows($playerResults);


                ?>


				<table cellpadding="20" width="650" class="bordertable">
                       <tr class="loginth">
                           <th height="25"><span class="whitenorm">First Name</span></th>
                           <th height="25"><span class="whitenorm">Last Name</span></th>
                           <th height="25"><span class="whitenorm">Email</span></th>
                           <th height="25"><span class="whitenorm">Work Phone</span></th>
                           <th height="25"><span class="whitenorm">Home Phone</span></th>
                           <th height="25"><span class="whitenorm">Mobile Phone</span></th>
                           <th></th>
                       </tr>





                <?


                $rownum = mysql_num_rows($playerResults);
                while ($playerarray = mysql_fetch_array($playerResults)) {

                        $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "darkrow" : "lightrow";
                        
                        ?>
                       <tr class="<?=$rc?>" >
                       		<form name="playerform<?=$rownum?>" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
	                       		<td><div align="center"><?=$playerarray[1]?></div> </td>
	                       		<td><div align="center"><?=$playerarray[2]?></div> </td>
	                       		<td><div align="center"><a href="mailto:<?=$playerarray[3]?>"><?=$playerarray[3]?></a></div> </td>
	                       		<td><div align="center"><?=$playerarray[4]?></div> </td>
	                       		<td><div align="center"><?=$playerarray[5]?></div> </td>
	                       		<td><div align="center"><?=$playerarray[6]?></div> </td>
	                       		<input type="hidden" name="userid" value="<?=$playerarray[0]?>">
	                       		<input type="hidden" name="searchname" value="<?=$searchname?>">
	                       		<input type="hidden" name="origin" value="lookup">
	                        	<td><div align="center"><a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
                        	</form>
                        </tr>
                        
                        <?
                        $rownum = $rownum - 1;
                }

                ?>
                </table>
               
                  
                <?  }           
      }

?>




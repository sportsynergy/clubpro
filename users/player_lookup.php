<?php

/*
 * $LastChangedRevision: 642 $
 * $LastChangedBy: Adam Preston $
 * $LastChangedDate: 2008-12-28 18:32:46 -0800 (Sun, 28 Dec 2008) $

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

           if ($errormsg){
                include($_SESSION["CFG"]["templatedir"]."/header.php");
                include($_SESSION["CFG"]["includedir"]."/errorpage.php");
                include($_SESSION["CFG"]["templatedir"]."/footer.php");
                die;
           }
           else {
                   $playerResults = get_player_search($searchname);
                   include($_SESSION["CFG"]["templatedir"]."/header.php");
                   print_players($searchname, $playerResults, $DOC_TITLE, $ME);
                   include($_SESSION["CFG"]["templatedir"]."/footer.php");
                   die;
           }
}

include($_SESSION["CFG"]["templatedir"]."/header.php");
include($_SESSION["CFG"]["templatedir"]."/player_lookup_form.php");
include($_SESSION["CFG"]["templatedir"]."/footer.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function validate_form($searchname) {


/* validate the signup form, and return the error messages in a string.  if
 * the string is empty, then there are no errors */

        $errors = new Object;
        $msg = "";

       	if(strpos ($searchname, "'")!=false){
                //$errors->searchname = true;
                $msg .= "No speical characters please. ";

         }

        return $msg;
}

function print_players($searchname, $playerResults, $DOC_TITLE, $ME) {

        if( mysql_num_rows($playerResults)<1 ){
        	$errormsg = "Sorry, no results found.";
        	include($_SESSION["CFG"]["includedir"]."/errorpage.php");
        }
        else{
        	

        ?>
          <table cellspacing="0" cellpadding="5" width="710" align="center">
          <tr>
          <td colspan="6">
           <table cellspacing="0" cellpadding="20" width="400" >
                   <tr>
                      <td class="clubid<?=get_clubid()?>th"><font class="whiteh1"><div align="center"><? pv($DOC_TITLE) ?></div></font></td>
                   </tr>

                   <tr>
                    <td class="generictable">
                    <form name="entryform" method="post" action="<?=$ME?>">
                        <table width="400">
                            <tr>
                                        <td class="label">Member Name:</td>
                                        <td><input type="text" name="searchname" size="25"value="<? pv($searchname) ?>">

                                        </td>
                            </tr>
                            <tr>
                                  <td colspan="2"><p class="normal">
                                  Search for the first or last name of a member. *Note partial string are supported.
                                  <i> i.e. Smi for Smith or Pet for Peter</i>
                                  </p>
                                  </td>
                            </tr>
                            <tr>
                                  <td></td>
                                  <td><input type="submit" name="submit" value="Search"></td>

                            </tr>
                        </table>
                        </form>
                   </td>
                 </tr>
           </table>

           <br>
           <br>
           <hr>
           <br>

           </td>
           </tr>
        <?




        mysql_data_seek($playerResults,0);
        $num_fields = mysql_num_fields($playerResults);
        $num_rows = mysql_num_rows($playerResults);


                ?>



                       <tr class=loginth>
                           <td height="25"><font class="whitenorm"><div align="center">First Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Last Name</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Email</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Work Phone</div></font></td>
                           <td height="25"><font class="whitenorm"><div align="center">Home Phone</div></font></td>
                           <td></td>
                       </tr>





                <?


                $rownum = mysql_num_rows($playerResults);
                while ($playerarray = mysql_fetch_array($playerResults)) {

                        $rc = (($rownum/2 - intval($rownum/2)) > .1) ? "C0C0C0" : "DBDDDD";
                        
                        ?>
                       <tr bgcolor="<?=$rc?>" class="normal">
                       		<form name="playerform<?=$rownum?>" method="post" action="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_info.php" >
	                       		<td><div align="center"><?=$playerarray[1]?></div> </td>
	                       		<td><div align="center"><?=$playerarray[2]?></div> </td>
	                       		<td><div align="center"><a href="mailto:<?=$playerarray[3]?>"><?=$playerarray[3]?></a></div> </td>
	                       		<td><div align="center"><?=$playerarray[4]?></div> </td>
	                       		<td><div align="center"><?=$playerarray[5]?></div> </td>
	                       		<input type="hidden" name="userid" value="<?=$playerarray[0]?>">
	                       		<input type="hidden" name="searchname" value="<?=$searchname?>">
	                        	<td><div align="center"><a href="javascript:submitForm('playerform<?=$rownum?>')">Info</a></td>
                        	</form>
                        </tr>
                        
                        <?
                        $rownum = $rownum - 1;
                }

                ?>
                  <tr>
                    <td colspan="6" height="20">
                    <!-- Spacer -->
                    </td>
                  </tr>
                  <tr>
                    <td colspan="6" align="right" class="normal">
                        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/users/player_lookup.php"><< New Search</a> &nbsp;&nbsp;&nbsp;
                    </td>
                  </tr>
                <?
                echo "</table>\n";
                }
                
      }


?>
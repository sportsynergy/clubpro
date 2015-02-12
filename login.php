<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * $Id:$
 */
/**
* Class and Function List:
* Function list:
* Classes list:
*/
include ("./application.php");
$DOC_TITLE = "Login Screen";

/* form has been submitted, check if it the user login information is correct */

if (match_referer() && isset($_POST)) {
    if (isset($_POST["frompickform"])) {
        $user = load_user($_POST["userid"]);
    } else {
        $user = verify_login($_POST["username"], $_POST["password"], true);
    }
    
    if ($user) {
        //Already through this once...skipping
        if (!isset($_POST["frompickform"])) {

            $usersResult = getAllUsersWithIdResult($_POST["username"], $user['clubid']);
            $username = $_POST["username"];
            
            if (mysql_num_rows($usersResult) > 1) {

                include ($_SESSION["CFG"]["templatedir"] . "/pick_user_form.php");
                die;
            }
        }

        //Update the last login time thing
        $clubquery = "SELECT timezone from tblClubs WHERE clubid='" . get_clubid() . "'";
        $clubresult = db_query($clubquery);
        $timezoneval = mysql_result($clubresult, 0);
        $tzdelta = $timezoneval * 3600;
        $curtime = mktime() + $tzdelta;
        $updateLastLoginQuery = "UPDATE tblClubUser SET lastlogin = $curtime WHERE userid = $user[userid] AND clubid = " . get_clubid() . "";
        db_query($updateLastLoginQuery);
        $_SESSION["user"] = $user;
        
        if (isset($_POST["remember"])) {

            setcookie("username", $_POST['username'], time() + 31536000);
            setcookie("pass", $_POST['password'], time() + 31536000);
            setcookie("remembercookie", "on", time() + 31536000);
        }

        //Unset the remember me thingy and cookies.
        else {
            setcookie("remembercookie", "");
            setcookie("username", "");
            setcookie("pass", "");
        }

        /* if wantsurl is set, that means we came from a page that required
         * log in, so let's go back there.  otherwise go back to the main page */
        
        if (get_roleid() == 3) {

            $goto = $_SESSION["CFG"]["wwwroot"] . "/system/";
        } else {
            $goto = empty($_SESSION["wantsurl"]) ? $_SESSION["CFG"]["wwwroot"] : $_SESSION["wantsurl"];
        }
        header("Location: $goto");
        die;
    } else {
        $errormsg = "Invalid login, please try again";
        $frm["username"] = $_POST["username"];
    }
}

$rSql = "SELECT tblClubs.clubname,tblClubSites.sitecode 
FROM tblClubs 
INNER JOIN tblClubSites on tblClubs.clubid = tblClubSites.clubid
WHERE tblClubs.clubid =%s";

$clubNameSql = sprintf($rSql,get_clubid());
unset($rSql);
list($clubName, $sitecode) = mysql_fetch_row(db_query($clubNameSql));

include ($_SESSION["CFG"]["templatedir"] . "/login_form.php");
?>
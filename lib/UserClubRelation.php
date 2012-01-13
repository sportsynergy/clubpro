<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/* ====================================================================
 * GNU Lesser General Public License
 * Version 2.1, February 1999
 * 
 * <one line to give the library's name and a brief idea of what it does.>
 *
 * Copyright (C) 2001~2012 Adam Preston
 * Copyright (C) 2012 Nicolas Wegener
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
* - __construct()
* - isUserLoggedin()
* - getClubId()
* - IsClubMember()
* - KillUserSession()
* Classes list:
* - UserClubRelation
*/
/**
 * Checks the User Club Relationship
 * @author Nicolas J. Wegener
 */

class UserClubRelation {
    public function __construct() {
    }
    /**
     * Checks to see if a user is logged in
     */
    public function isUserLoggedin() {
        $_id = $_SESSION["user"]["userid"];
        $id = (int)$_id; // force int.

        
        if ($id > 0) return $id;
        return false;
    }
    /**
     * Get the club ID
     */
    private function getClubId() {
        return $_SESSION["siteprefs"]["clubid"];
    }
    /**
     * Will check if the current User (will check for being logged in)
     * is a current memeber of the club.
     *
     * @return boolean
     */
    public function IsClubMember() {
        
        if ($this->isUserLoggedin() == false) return false;
        $rSql = "select id from tblClubUser where userid=%s and clubid=%s";
        $sql = sprintf($rSql, $this->isUserLoggedin() , $this->getClubId());
        list($_id) = mysql_fetch_row(db_query($sql));
        $id = (int)$_id;
        
        if ($id > 0) return true;
        return false;
    }
    /**
     *
     * Enter description here ...
     */
    public function KillUserSession() {
        unset($_SESSION["user"]);
    }
}
?>
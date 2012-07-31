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


include ("../application.php");
require_login();

$_SESSION["selected_site"] = $_REQUEST["siteid"];
$siteid = $_REQUEST["siteid"];

$sitedetail = getSiteDetail($siteid);
$sitecourts = getSiteCourts($siteid);


$DOC_TITLE = "Site Info";
include ($_SESSION[CFG][templatedir] . "/header_yui.php");
include ($_SESSION[CFG][templatedir] . "/site_info_form.php");
include ($_SESSION[CFG][templatedir] . "/footer_yui.php");

/******************************************************************************
 * FUNCTIONS
 *****************************************************************************/

function getSiteCourts($siteid){
	
	$query = "SELECT * from tblCourts where siteid = $siteid";
	return db_query($query);
}

function getSiteDetail($siteid){
	
	$query = "SELECT * from tblClubSites where siteid = '$siteid' ";
	$result = db_query($query);
	return mysql_fetch_array($result);
}

?>
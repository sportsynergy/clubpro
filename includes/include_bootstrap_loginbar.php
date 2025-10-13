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


?>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">

    <a class="navbar-brand" href="#">
        <img src="<?=$_SESSION["CFG"]["wwwroot"]?>/clubs/<?=get_sitecode()?>/logo.png" alt="" width="30" height="30" class="d-inline-block align-text-top">
    </a>
    
    <? if( !is_logged_in() ){ ?>
      <span class="navbar-text">
        <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/login.php">Login</a>
      </span>
     <? } else { ?>
      <span class="navbar-text">
        You are logged in as: <? p($_SESSION["user"]["firstname"] . " " . $_SESSION["user"]["lastname"]) ?> | <a href="<?=$_SESSION["CFG"]["wwwroot"]?>/logout.php">Logout</a>
      </span>
     <? } ?>
    
  </div>
</nav>



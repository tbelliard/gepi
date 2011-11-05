<?php
/* $Id$ */
/*
* Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


// Initialisations files
$niveau_arbo = 2;
$mode_ooo="imprime";



include('init_secure.inc.php');



include('fiches_brevet.php');		// calcul du tableau de données



$tempdir=get_user_temp_directory();

$fb_dezip_ooo=getSettingValue("fb_dezip_ooo");
switch($fb_dezip_ooo){
  case '2':
	// pclzip.lib.php
	include('imprime_ooo_2.php');
default:
  // ziparchive ou zip-unzip
	include('imprime_ooo_1.php');
}

?>

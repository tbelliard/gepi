<?php
/*
* $Id: menu.php 7744 2011-08-14 13:07:15Z dblanqui $
*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};
?>
<ol id="essaiMenu">
<li><a href='index.php'><img src='img/back.png' alt='Retour' class='back_link'/> Accueil module </a></li>
<li><a href='index.php?ctrl=import'><img src='img/table_add.png' alt='importation' class='back_link'/> Import de données </a></li>
<li><a href='index.php?ctrl=maj'><img src='img/zoom.png' alt='mise_a_jour' class='back_link'/> Mise à jour de données </a></li>
<li><a href='index.php?ctrl=cvsent'><img src='img/table_add.png' alt='ent' class='back_link'/> CVS export ENT</a></li>
<li><a href='index.php?ctrl=nettoyage'><img src='img/database_delete.png' alt='nettoyage' class='back_link'/> Nettoyage correspondances</a></li>
<li><a href='index.php?ctrl=help'><img src='img/help.png' alt='aide' class='back_link'/> Aide</a></li>
</ol>

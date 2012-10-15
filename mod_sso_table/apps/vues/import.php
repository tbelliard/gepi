<?php
/*
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
[onload;file=menu.php]
<p>Vous allez mettre en place les correspondances entre les logins de gépi et ceux d'un logiciel tiers :</p>
<p class="title-page">Veuillez fournir le fichier csv :</p>
<form action="index.php?ctrl=import&action=result" enctype='multipart/form-data' method="post">
    <p>
        <input type="radio" name="choix" value="erreur" checked="checked" />Recherche des erreurs : seules les erreurs sont affichées, aucune donnée n'est écrite dans la base<br/>
        <input type="radio" name="choix" value="test" />Test : toutes les entrées sont listées avec leur état, aucune donnée n'est écrite dans la base<br/>
        <input type="radio" name="choix" value="ecrit" />Inscription dans la base : toutes les entrées sont traitées puis listées avec leur état. Les données sont écrites dans la base <br/>
    </p>
    <input type='file'  name='fichier'  />
    <input type='submit' value='Télechargement' />
</form>
</body>
</html>
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
<p>A partir de cette page vous pouvez mettre à jour manuellement une correspondance :</p>
<p>Rechercher un utilisateur dans Gépi. Attention son compte doit être activé et paramétré en sso</p>
<form action="index.php?ctrl=maj&action=search"  method="post">
          <input type="text" name="nom" id="nom" value="" />
          <input type="submit" name="action" value="Rechercher" class="submit"/>
</form>

<div style="text-align: center;">
    <table class="boireaus sortable" style="margin:1em auto;">
        <caption>Liste des utilisateurs sans correspondance</caption>
        <tr>
            <th title="Cliquer pour trier" style="cursor:pointer">Nom Prénom</th>
            <th title="Cliquer pour trier" style="cursor:pointer">Statut</th>
            <th title="Cliquer pour trier" style="cursor:pointer">Login</th>
        </tr>
        <tr class="lig[sso1.ligne]">
            <td style="padding-left: .2em; padding-right: .2em;"> [sso1.nom;block=tr;bmagnet=table]</td>
            <td style="padding-left: .2em; padding-right: .2em;"> [sso1.statut]</td>
            <td style="padding-left: .2em; padding-right: .2em;"> [sso1.login_gepi]</td>
        </tr>
    </table>
</div>


</body>
 </html>
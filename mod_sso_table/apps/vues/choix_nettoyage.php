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
<p>Cette section permet de nettoyer la table de correspondance; Plusieurs options sont possibles mais tout nettoyage est irreversible</p>
<p class="title-page">Attention , Une fois leur correspondance nettoyée les utilisateurs ne pourront plus se connecter en SSO avec ce module</p>
<form action="index.php?ctrl=nettoyage&action=choix" enctype='multipart/form-data' method="post">
<p>
	<input type="radio" name="choix" value="vidage_complet" checked="checked" />Vider complètement la table de correspondances<br/>
	<input type="radio" name="choix" value="anciens_comptes" />Supprimer de la table les comptes n'existant plus dans Gepi<br/>
	<input type="radio" name="choix" value="profil" />Supprimer les correspondances pour un profil (enseignant,eleve,tuteur....) <br/>
    <input type="radio" name="choix" value="classe" />Supprimer les correspondances pour une classe<br/>
</p>
<input type='submit' value='Vider la table' />
</form>
</body>
 </html>

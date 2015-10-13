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
	<input type="radio" name="choix" id="choix_vidage_complet" value="vidage_complet" onchange="change_style_radio()" checked="checked" /><label for="choix_vidage_complet" id="texte_choix_vidage_complet" style="font-weight:bold;"> Vider complètement la table de correspondances</label><br/>
	<input type="radio" name="choix" id="choix_anciens_comptes" value="anciens_comptes" onchange="change_style_radio()" /><label for="choix_anciens_comptes" id="texte_choix_anciens_comptes"> Supprimer de la table les comptes n'existant plus dans Gepi</label><br/>
	<input type="radio" name="choix" id="choix_profil" value="profil" onchange="change_style_radio()" /><label for="choix_profil" id="texte_choix_profil"> Supprimer les correspondances pour un profil (<em>enseignant, eleve, tuteur,...</em>)</label><br/>
    <input type="radio" name="choix" id="choix_classe" value="classe" onchange="change_style_radio()" /><label for="choix_classe" id="texte_choix_classe"> Supprimer les correspondances pour une classe</label><br/>
</p>
<input type='submit' value='Vider la table' />
</form>
</body>
 </html>

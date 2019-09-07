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
<div>[onload;block=div; when [var.choix_info]='affich_result']
<p>Résultats du traitement :</p>

<table width="80%" border="1" align="center" cellpadding="2" cellspacing="0">
    <tr bgcolor="#CACACA">
        <td width="20%"><strong>Login Gepi</strong></td>
        <td width="20%"><strong>Login sso</strong></td>
        <td width="60%">
            <strong>Résultat</strong>
            <a href='#' onclick="afficher_masquer_lignes_identiques(true); return false;" title="Afficher les lignes pour lesquelles l'entrée est identique."><img src='../images/icons/visible.png' class='icone16' /></a>
            <a href='#' onclick="afficher_masquer_lignes_identiques(false); return false;" title="Masquer les lignes pour lesquelles l'entrée est identique."><img src='../images/icons/invisible.png' class='icone16' /></a>
        </td>
        <td width="60%"><strong title="Supprimer pour le login Gepi indiqué la correspondance SSO existante de la table 'sso_table_correspondance'">Supprimer</strong></td>
    </tr>
    <tr id='tr_[b1.login_sso]' bgcolor="#F0F0F0">
        <td><a href='../utilisateurs/cherche_user.php?login_user=[b1.login_gepi]' target='_blank' title="Voir la fiche">[b1.login_gepi]</a></td>
        <td>[b1.login_sso;block=tr]</td>
        <td id='td_msg_[b1.login_sso]'><div>[b1.couleur;att=div#class][b1.message]</div></td>
        <td id='td_[b1.login_sso;block=tr]' style='text-align:center'><a href='#' onclick="delete_corresp_sso('[b1.login_gepi;block=tr]', '[b1.login_sso;block=tr]')"><img src="../images/delete16.png" class='icone16' alt='Supprimer' /></a></td>
    </tr>
    <tr id='tr_[b1.login_sso]' bgcolor="#E6E6E6" >
        <td><a href='../utilisateurs/cherche_user.php?login_user=[b1.login_gepi]' target='_blank' title="Voir la fiche">[b1.login_gepi]</a></td>
        <td>[b1.login_sso;block=tr]</td>
        <td id='td_msg_[b1.login_sso]'><div>[b1.couleur;att=div#class][b1.message]</div></td>
        <td id='td_[b1.login_sso;block=tr]' style='text-align:center'><a href='#' onclick="delete_corresp_sso('[b1.login_gepi;block=tr]', '[b1.login_sso;block=tr]')"><img src="../images/delete16.png" class='icone16' alt='Supprimer' /></a></td>
    </tr>
</table>
<script type='text/javascript'>
	function afficher_masquer_lignes_identiques(mode) {
		if(mode==true) {
			tr=document.getElementsByTagName('tr');
			for(i=0;i<tr.length;i++) {
				id=tr[i].getAttribute('id');
				//alert(id);
				if((id!=null)&&(id.substring(0, 3)=='tr_')) {
					document.getElementById(id).style.display='';
				}
			}
		}
		else {
			td_msg=document.getElementsByTagName('td');
			for(i=0;i<td_msg.length;i++) {
				id=td_msg[i].getAttribute('id');
				//alert(id);
				if((id!=null)&&(id.substring(0, 7)=='td_msg_')) {
					contenu=td_msg[i].innerHTML;
					//alert(contenu);
					if(contenu.search('Une entrée identique existe déjà dans la table pour ce login Gepi')!=-1) {
						login=id.substring(7);
						/*
						if(i<20) {
							alert(login);
						}
						*/
						if(document.getElementById('tr_'+login)) {
							document.getElementById('tr_'+login).style.display='none';
						}
					}
				}
			}
		}
	}
</script>
</div>
<div>[onload;block=div; when [var.choix_info]='no_error']
    <p> Apparemment aucune erreur n'est a signaler </p>
</div>
<div>[onload;block=div; when [var.choix_info]='no_data']
    <p> Apparemment le fichier ne contient pas de données a fusionner !! </p>
</div>
</body>
</html>

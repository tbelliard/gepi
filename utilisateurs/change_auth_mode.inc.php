<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

echo "<div id='div_changer_auth_mode' style='position: absolute; top: 220px; right: 20px; width: 250px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";

	echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 250px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_changer_auth_mode')\">\n";
		echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
		echo "<a href='#' onClick=\"cacher_div('div_changer_auth_mode');return false;\">\n";
		echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
		echo "</a>\n";
		echo "</div>\n";

		echo "<div id='titre_entete_changer_auth_mode'>AA</div>\n";
	echo "</div>\n";
	
	echo "<div id='corps_changer_auth_mode' class='infobulle_corps' style='color: #000000; cursor: auto; padding: 0px; height: 7em; width: 250px; overflow: auto;'>";


	$tab_auth_mode=array('gepi', 'ldap', 'sso');
	echo "<form name='form_changer_auth_mode' id='form_changer_auth_mode' action ='ajax_modif_utilisateur.php' method='post' target='_blank'>\n";
	echo "<input type='hidden' name='auth_mode_login_user' id='auth_mode_login_user' value='' />\n";
	for($loop=0;$loop<count($tab_auth_mode);$loop++) {
		echo "<input type='radio' name='auth_mode_user' id='auth_mode_user_$loop' value='".$tab_auth_mode[$loop]."' ";
		//if($eleve_auth_mode==$tab_auth_mode[$loop]) {}
		echo "/><label for='auth_mode_user_$loop'> $tab_auth_mode[$loop]</label><br />\n";
	}
	echo add_token_field();
	echo "<input type='button' onclick='valider_changement_auth_mode()' name='Valider' value='Valider' />\n";
	echo "</form>\n";

	echo "</div>\n";

echo "</div>\n";

echo "<script type='text/javascript'>

	function afficher_changement_auth_mode(login_user, auth_mode) {
		// auth_mode est le auth_mode actuel de l'utilisateur
		document.getElementById('titre_entete_changer_auth_mode').innerHTML='Auth_mode de '+login_user;
		document.getElementById('auth_mode_login_user').value=login_user;

		// On coche le auth_mode actuel de l'utilisateur
		for(i=0;i<".count($tab_auth_mode).";i++) {
			if(document.getElementById('auth_mode_user_'+i).value==auth_mode) {
				document.getElementById('auth_mode_user_'+i).checked=true;
			}
		}

		afficher_div('div_changer_auth_mode','y',-20,20);
	}


	function valider_changement_auth_mode() {
		if(document.getElementById('auth_mode_login_user')) {
			login_user=document.getElementById('auth_mode_login_user').value;

			for (var i=0; i<document.forms['form_changer_auth_mode'].auth_mode_user.length;i++) {
				if (document.forms['form_changer_auth_mode'].auth_mode_user[i].checked) {
					auth_mode_user=document.forms['form_changer_auth_mode'].auth_mode_user[i].value;
				}
			}

			//alert(auth_mode_user);

			new Ajax.Updater($('auth_mode_'+login_user),'ajax_modif_utilisateur.php?login_user='+login_user+'&auth_mode_user='+auth_mode_user+'&mode=changer_auth_mode".add_token_in_url(false)."',{method: 'get'});
		}
		else {
			alert('document.getElementById(\'auth_mode_login_user\') n est pas affecté.')
		}

		cacher_div('div_changer_auth_mode');

	}
</script>\n";
?>

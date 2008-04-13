<?php
/*
 * @version $Id$
 *
 * Ce fichier peut servir à faire une demande à un annuaire ldap pour qu'il renvoie une information
 * juste après l'authentification CAS du fichier lib/cas.inc.php
 *
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

	//Connexion LDAP (connect)
	$ds = ldap_connect("ent-ldap.ac-bordeaux.fr");

	if ($ds){

		$sr = ldap_bind($ds); // connexion anonyme, typique
		// pour un accès en lecture seule.

		$dn = "o=personne,dc=ac-bordeaux,dc=fr, c=fr";
		//$uid = "uid=".phpCAS::getUser();
		$uid = "uid=".$login;
		$filter = "(|(uid=$uid))";
		$justthese = array( "ou," );

		//Interrogation LDAP (ldapsearch)
		$sr = ldap_search($ds, "ou=personnes,dc=ac-bordeaux,dc=fr",$uid) ;
		$info = ldap_get_entries($ds, $sr);

		// DEBUG
		//print_r($info);
		$RNE = $info[0]["ou"][0];


		//Fermeture connexion LDAP
		ldap_close($ds);

		/*/fabrication url de redirection avec ticket, status (teacher ou student), rne
		// exemple : http://194.199.33.3/educhorus/servlet/com.bloobyte.girafe.DoLogin?ticket=&status=teacher&rne=0330135T
		// url de redieection http://194.199.33.3/educhorus/servlet/com.bloobyte.girafe.DoLogin
		$urlRedirectBase = 'http://194.199.33.3/educhorus/servlet/com.bloobyte.girafe.DoLogin?' ;
		$urlRedirect = $urlRedirectBase . 'ticket=' . $HTTP_GET_VARS['ticket'] . '&status=' . $info[0]["edupersonaffiliation"][0] . '&rne=' . $info[0]["ou"][0] ;
        	header ("Location: ".$urlRedirect);
*/
	}else{

		echo '<h4>Impossible de se connecter au serveur LDAP.</h4>';
	}
?>
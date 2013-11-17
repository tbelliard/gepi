<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the  warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}


//INSERT INTO droits VALUES ('/utilisateurs/password_csv.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Export des identifiants et mots de passe', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!isset($_SESSION['donnees_export_csv_password'])) { $MargeHaut = false ; } else {$donnees_personne_csv =  $_SESSION['donnees_export_csv_password'];}

$date_heure = gmdate('d-m-y-H:i:s');

$nom_fic = "export_csv_password_".$date_heure . ".csv";


//send_file_download_headers('text/x-csv',$nom_fic);

$fd = '';

$nb_enr_tableau = sizeof ($donnees_personne_csv['login']);
//echo $nb_enr_tableau;

if (($donnees_personne_csv)) {
    // On rechercher par rapport au premier login si c'est un eleve ou un parent. ==> format de sortie CSV différent.
	//$login=$donnees_personne_csv['login'][1];
	$login=$donnees_personne_csv['login'][0];
	$sql_statut="SELECT statut FROM utilisateurs WHERE login='$login'";
	$resultat_statut = mysqli_query($GLOBALS["mysqli"], $sql_statut);
	$statut = mysql_result($resultat_statut, 0, "statut");

	switch ($statut) {
	case 'eleve':
			//pour un élève
			$fd.="CLASSE;IDENTIFIANT;NOM;PRENOM;MOT_DE_PASSE;COURRIEL\n";
			for ($i=0 ; $i<$nb_enr_tableau ; $i++) {
				if(isset($donnees_personne_csv['login'][$i])){
					$classe = $donnees_personne_csv['classe'][$i];
					$login = $donnees_personne_csv['login'][$i];
					$nom = $donnees_personne_csv['nom'][$i];
					$prenom = $donnees_personne_csv['prenom'][$i];
					$password = $donnees_personne_csv['new_password'][$i];
					$email = $donnees_personne_csv['user_email'][$i];
					$fd.="$classe;$login;$nom;$prenom;$password;$email\n";
				}
			}
	break;
	case 'responsable':
			//pour un responsable
			$fd.="CLASSE;IDENTIFIANT;NUM_LEGAL;CIVILITE;NOM;PRENOM;MOT_DE_PASSE;COURRIEL;ARD1;ADR2;ADR3;ADR4;CP;COMMUNE;PAYS;ELV1;ELV1_LOGIN;ELV2;ELV2_LOGIN;ELV3;ELV3_LOGIN;ELV4;ELV4_LOGIN;ELV5;ELV5_LOGIN;ELV6;ELV6_LOGIN;ELV7;ELV7_LOGIN\n";
			for ($i=0 ; $i<$nb_enr_tableau ; $i++) {
				if(isset($donnees_personne_csv['login'][$i])){
					$classe = $donnees_personne_csv['classe'][$i];
					$login = $donnees_personne_csv['login'][$i];
					$num_legal = $donnees_personne_csv['resp_legal'][$i];
					$nom = $donnees_personne_csv['nom'][$i];
					$prenom = $donnees_personne_csv['prenom'][$i];
					$password = $donnees_personne_csv['new_password'][$i];
					$email = $donnees_personne_csv['user_email'][$i];
					$civilite = $donnees_personne_csv['civilite'][$i];
					$adr1 = $donnees_personne_csv['adr1'][$i];
					$adr2 = $donnees_personne_csv['adr2'][$i];
					$adr3 = $donnees_personne_csv['adr3'][$i];
					$adr4 = $donnees_personne_csv['adr4'][$i];
					$cp = $donnees_personne_csv['cp'][$i];
					$commune = $donnees_personne_csv['commune'][$i];
					$pays = $donnees_personne_csv['pays'][$i];
					$elv1 = isset($donnees_personne_csv['elv1'][$i]) ? $donnees_personne_csv['elv1'][$i] : "";
					$elv2 = isset($donnees_personne_csv['elv2'][$i]) ? $donnees_personne_csv['elv2'][$i] : "";
					$elv3 = isset($donnees_personne_csv['elv3'][$i]) ? $donnees_personne_csv['elv3'][$i] : "";
					$elv4 = isset($donnees_personne_csv['elv4'][$i]) ? $donnees_personne_csv['elv4'][$i] : "";
					$elv5 = isset($donnees_personne_csv['elv5'][$i]) ? $donnees_personne_csv['elv5'][$i] : "";
					$elv6 = isset($donnees_personne_csv['elv6'][$i]) ? $donnees_personne_csv['elv6'][$i] : "";
					$elv7 = isset($donnees_personne_csv['elv7'][$i]) ? $donnees_personne_csv['elv7'][$i] : "";
					$elv1_login = isset($donnees_personne_csv['elv1_login'][$i]) ? $donnees_personne_csv['elv1_login'][$i] : "";
					$elv2_login = isset($donnees_personne_csv['elv2_login'][$i]) ? $donnees_personne_csv['elv2_login'][$i] : "";
					$elv3_login = isset($donnees_personne_csv['elv3_login'][$i]) ? $donnees_personne_csv['elv3_login'][$i] : "";
					$elv4_login = isset($donnees_personne_csv['elv4_login'][$i]) ? $donnees_personne_csv['elv4_login'][$i] : "";
					$elv5_login = isset($donnees_personne_csv['elv5_login'][$i]) ? $donnees_personne_csv['elv5_login'][$i] : "";
					$elv6_login = isset($donnees_personne_csv['elv6_login'][$i]) ? $donnees_personne_csv['elv6_login'][$i] : "";
					$elv7_login = isset($donnees_personne_csv['elv7_login'][$i]) ? $donnees_personne_csv['elv7_login'][$i] : "";

					$fd.="$classe;$login;$num_legal;$civilite;$nom;$prenom;$password;$email;$adr1;$adr2;$adr3;$adr4;$cp;$commune;$pays;$elv1;$elv1_login;$elv2;$elv2_login;$elv3;$elv3_login;$elv4;$elv4_login;$elv5;$elv5_login;$elv6;$elv6_login;$elv7;$elv7_login\n";
				}
			}
	break;
	default:
			// ni élève ni responsable
			$fd.="IDENTIFIANT;NOM;PRENOM;MOT_DE_PASSE;COURRIEL\n";
			for ($i=0 ; $i<$nb_enr_tableau ; $i++) {
				if(isset($donnees_personne_csv['login'][$i])){
					$login = $donnees_personne_csv['login'][$i];
					$nom = $donnees_personne_csv['nom'][$i];
					$prenom = $donnees_personne_csv['prenom'][$i];
					$password = $donnees_personne_csv['new_password'][$i];
					$email = $donnees_personne_csv['user_email'][$i];
					$fd.="$login;$nom;$prenom;$password;$email\n";
				}
			}
	break;
    }

} else {
  echo "Erreur de session";
}

send_file_download_headers('text/x-csv',$nom_fic);

echo echo_csv_encoded($fd);
?>
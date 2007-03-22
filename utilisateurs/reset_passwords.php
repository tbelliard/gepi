<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Ajout ERIC
$mode_impression = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : false);

//comme il y a une redirection pour une page Csv ou PDF, il ne faut pas envoyer les entêtes dans ces 2 cas
if (!(($mode_impression=='csv') or ($mode_impression=='pdf'))) {
//**************** EN-TETE *****************************
//$titre_page = "Gestion des utilisateurs | Réinitialisation des mots de passe";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
}

// On appelle la lib utilisée pour la génération des mots de passe
include("randpass.php");


$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : false);
$user_status = isset($_POST["user_status"]) ? $_POST["user_status"] : (isset($_GET["user_status"]) ? $_GET["user_status"] : false);
$user_classe = isset($_POST["user_classe"]) ? $_POST["user_classe"] : (isset($_GET["user_classe"]) ? $_GET["user_classe"] : false);


// Il faut être sûr que l'on ne fait pas de réinitialisation accidentelle de tous les utilisateurs...
// On bloque donc l'opération si jamais un des trois paramètres n'a pas été passé correctement, pour une raison ou une autre.

if ($user_login AND $user_login == $_SESSION['login']) {
	$user_login = false;
	echo "<p>ERREUR ! Utilisez l'interface 'Gérer mon compte' pour changer votre mot de passe !</p>";
	echo "</div></body></html>";
	die();
}

if ($user_status and $user_status != "scolarite" and $user_status != "professeur" and $user_status != "cpe" and $user_status != "secours" and $user_status != "responsable" and $user_status != "eleve") {
	echo "<p>ERREUR ! L'identifiant de statut est erroné. L'opération ne peut pas continuer.</p>";
	echo "</div></body></html>";
	die();
}

if ($user_classe AND !is_numeric($user_classe)) {
	echo "<p>ERREUR ! L'identifiant de la classe est erroné. L'opération ne peut pas continuer.</p>";
	echo "</div></body></html>";
	die();
}
//----

//Ajout Eric ==> les données à sortir sont différentes suivant la demande de réinitialisation faite (elv / resp) et au niveau du responsable en fonction du fait classe / tous (dans ce cas, il faut rechercher la classe
$cas_traite = 0;

//TODO: Sans doute faudrait-il ajouter des tests ici, si jamais un jour quelqu'un d'autre que l'administrateur peut accéder à la page.
if ($user_login) {
	// Si on est ici, c'est qu'on a demandé la réinitialisation du mot de passe d'un seul utilisateur. C'est simple :)
		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
				"login = '" . $user_login ."' and " .
				"etat='actif' and " .
				"statut != 'administrateur')");
} else {

	if ($user_status) {
		if ($user_classe) {
			// On a un statut et une classe. Cette opération s'applique soit aux élèves soit aux parents
			
			if ($user_status == "responsable") {
				// Sélection de tous les responsables d'élèves de la classe donnée
				/*$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email " .
						"FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e WHERE (" .
						"u.login = r.login AND " .
						"u.statut = 'responsable' AND " .
						"r.pers_id = re.pers_id AND " .
						"re.ele_id = e.ele_id AND " .
						"e.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."')");
				*/
				$sql_user_resp="SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email, re.pers_id, jec.id_classe, ra.*
								FROM utilisateurs u, resp_pers r, responsables2 re, classes c, j_eleves_classes jec, eleves e, resp_adr ra 
								WHERE ( u.login = r.login AND 
								u.statut = 'responsable' AND 
								r.pers_id = re.pers_id AND 
								re.ele_id = e.ele_id AND 
								e.login = jec.login AND
								r.adr_id = ra.adr_id AND 
								jec.id_classe = '$user_classe')";
				$call_user_info = mysql_query($sql_user_resp);
				//echo $sql_user_resp;
				$cas_traite=1;
				
				$sql_classe = "SELECT * FROM classes WHERE id=$user_classe";
				$data_user_classe = mysql_query($sql_classe);
				$classe_resp= mysql_result($data_user_classe, 0, "classe");
				
			} elseif ($user_status == "eleve") {
				// Sélection de tous les utilisateurs élèves de la classe donnée
				$call_user_info = mysql_query("SELECT distinct(u.login), u.nom, u.prenom, u.statut, u.password, u.email " .
						"FROM utilisateurs u, classes c, j_eleves_classes jec WHERE (" .
						"u.login = jec.login AND " .
						"jec.id_classe = '".$user_classe."')");
			}
		} else {
			// Ici, on ne s'occupe pas de la classe, donc on sélectionne tous les utilisateurs pour le statut considéré,
			// quel qu'il soit
			//pour les différentes impressions, on va trier les informations par classe (pour faciliter la distribution) problème avec les ajouts en cours d'année
			if ($user_status == "responsable") {
			    /*$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
					"login != '" . $_SESSION['login'] . "' AND " .
					"etat = 'actif' AND " .
					"statut = '" . $user_status . "')");*/
				$sql_user_info =   "SELECT DISTINCT (e.ele_id), u.civilite, u.statut, u.password, u.email, rp.login, rp.nom, rp.prenom, rp.pers_id, ra. * , r2.ele_id, e.login, jec.id_classe
									FROM utilisateurs u, resp_pers rp, resp_adr ra, responsables2 r2, eleves e, j_eleves_classes jec
									WHERE (
									u.login != 'ADMIN'
									AND u.etat = 'actif'
									AND u.statut = 'responsable'
									AND rp.login = u.login
									AND rp.adr_id = ra.adr_id
									AND rp.pers_id = r2.pers_id
									AND r2.ele_id = e.ele_id
									AND jec.login = e.login )
									ORDER BY jec.id_classe";	
				//echo $sql_user_info;
				$call_user_info = mysql_query($sql_user_info);
				$cas_traite=2;
				
			} elseif ($user_status == "eleve"){
			    $login_en_cours = $_SESSION['login'];
			    $sql_user_info = "SELECT DISTINCT (u.login), u.nom, u.prenom, u.statut, u.password, u.email, jec.id_classe
								  FROM utilisateurs u, j_eleves_classes jec
								  WHERE ( u.login != 'ADMIN'
								  AND jec.login = u.login
								  AND u.etat = 'actif'
								  AND u.statut = 'eleve' )
								  ORDER BY jec.id_classe ASC, u.nom ASC";
				//echo $sql_user_info;
			    $call_user_info = mysql_query($sql_user_info);
			}
		}
	} else {
		// Ni statut ni classe ni login n'ont été transmis. On sélectionne alors tous les personnels de l'établissement,
		// c'est à dire tout le monde sauf l'administrateur connecté actuellement, les parents, et les élèves.
		
		$call_user_info = mysql_query("SELECT * FROM utilisateurs WHERE (" .
				"login!='" . $_SESSION['login'] . "' and " .
				"etat='actif' and " .
				"(statut = 'professeur' OR " .
				"statut = 'scolarite' OR " .
				"statut = 'cpe' OR " .
				"statut = 'secours'))");
	}
}

$nb_users = mysql_num_rows($call_user_info);
$p = 0;
$saut = 1;
while ($p < $nb_users) {

    $user_login = mysql_result($call_user_info, $p, "login");
    $user_nom = mysql_result($call_user_info, $p, "nom");
    $user_prenom = mysql_result($call_user_info, $p, "prenom");
    $user_password = mysql_result($call_user_info, $p, "password");
    $user_statut = mysql_result($call_user_info, $p, "statut");
    $user_email = mysql_result($call_user_info, $p, "email");

	//Pour les responsables :
	if ($cas_traite!=0) {
	
	  $resp_adr1=mysql_result($call_user_info, $p, "adr1");
	  $resp_adr1=mysql_result($call_user_info, $p, "adr1");
	  $resp_adr2=mysql_result($call_user_info, $p, "adr2");
	  $resp_adr3=mysql_result($call_user_info, $p, "adr3");	  
	  $resp_adr4=mysql_result($call_user_info, $p, "adr4");
	  $resp_cp=mysql_result($call_user_info, $p, "cp");
	  $resp_commune=mysql_result($call_user_info, $p, "commune");
	  $resp_pays=mysql_result($call_user_info, $p, "pays");
	  $resp_pers_id=mysql_result($call_user_info, $p, "pers_id");
	  
	  //recherche des élèves +  leur classe associés aux responsables
	  $sql_resp_eleves="SELECT DISTINCT c.id, e. * , c. *
						FROM responsables2 r2, eleves e, classes c, j_eleves_classes jec
						WHERE (
						r2.pers_id = '$resp_pers_id'
						AND r2.ele_id = e.ele_id
						AND e.login = jec.login
						AND jec.id_classe = c.id
						)";
	  //echo "<br>".$sql_resp_eleves;
	  $call_resp_eleves=mysql_query($sql_resp_eleves);
	  $nb_elv_resp = mysql_num_rows($call_resp_eleves);
	  
	  //init du tableau elv_resp
	  for ($i=0;$i<7;$i++) {
	      $elv_resp['nom'][$i] = '';
		  $elv_resp['prenom'][$i] = '';
		  $elv_resp['classe'][$i] = '';
		  $elv_resp['nom_complet_classe'][$i] = '';
	  }
	  
	  $i = 0;
      while ($i < $nb_elv_resp){
          $elv_resp['nom'][$i] = mysql_result($call_resp_eleves, $i, "nom");
		  $elv_resp['prenom'][$i] = mysql_result($call_resp_eleves, $i, "prenom");
		  $elv_resp['classe'][$i] = mysql_result($call_resp_eleves, $i, "classe");
		  $elv_resp['nom_complet_classe'][$i] = mysql_result($call_resp_eleves, $i, "nom_complet");
		  
    	  $i++;
      }
	  
	  // il va y avoir la classe à récuperer
	  if ($cas_traite==2) {
		$user_classe = $resp_pers_id=mysql_result($call_user_info, $p, "id_classe");
		//recherche du nom court de la classe de la prsonne en cours
		$sql_classe = "SELECT * FROM classes WHERE id=$user_classe";
		$data_user_classe = mysql_query($sql_classe);
		$classe_resp= mysql_result($data_user_classe, 0, "classe");
	  }	

	  
	}

    // On réinitialise le mot de passe
    $new_password = pass_gen();
    $save_new_pass = mysql_query("UPDATE utilisateurs SET password='" . md5($new_password) . "', change_mdp = 'y' WHERE login='" . $user_login . "'");


    $call_matieres = mysql_query("SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysql_num_rows($call_matieres);
    $k = 0;
    while ($k < $nb_mat) {
        $user_matiere[$k] = mysql_result($call_matieres, $k, "id_matiere");
        $k++;
    }

    $call_data = mysql_query("SELECT * FROM classes");
    $nombre_classes = mysql_num_rows($call_data);
    $i = 0;
    while ($i < $nombre_classes){
        $classe[$i] = mysql_result($call_data, $i, "classe");
        $i++;
    }
	
// Ajout Eric
	switch ($mode_impression) {
	
	case 'html':
		if ($user_statut == "responsable") {
			$impression = getSettingValue("ImpressionFicheParent");
			$nb_fiches = getSettingValue("ImpressionNombreParent");
		} elseif ($user_statut == "eleve") {
			$impression = getSettingValue("ImpressionFicheEleve");
			$nb_fiches = getSettingValue("ImpressionNombreEleve");
		} else {
			$impression = getSettingValue("Impression");
			$nb_fiches = getSettingValue("ImpressionNombre");
		}
		echo "<p>A l'attention de  <span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span>";
		echo "<br />Nom de login : <span class = \"bold\">" . $user_login . "</span>";
		echo "<br />Mot de passe : <span class = \"bold\">" . $new_password . "</span>";
		echo "<br />Adresse E-mail : <span class = \"bold\">" . $user_email . "</span>";
		echo "</p>";
		echo $impression;
		if ($saut == $nb_fiches) {
			echo "<p class=saut>&nbsp</p>";
			$saut = 1;
		} else {
			$saut++;
		}
		
		break;
		
	case 'csv' :
		// création d'un tableau contenant toutes les informations à exporter
		$donnees_personne_csv['login'][$p] = $user_login;
		$donnees_personne_csv['nom'][$p] = $user_nom;
		$donnees_personne_csv['prenom'][$p] = $user_prenom;
		$donnees_personne_csv['new_password'][$p] = $new_password ;
		$donnees_personne_csv['user_email'][$p] = $user_email;
		
		
		if ($user_status) {
		
		    //recherche de la classe de l'élève si mode 
			if ($user_status == 'eleve') {
				$sql_classe = "SELECT DISTINCT classe FROM `classes` c, `j_eleves_classes` jec WHERE (jec.login='".$user_login."' AND jec.id_classe=c.id)";
				$data_user_classe = mysql_query($sql_classe);
				$classe_eleve = mysql_result($data_user_classe, 0, "classe");
				$donnees_personne_csv['classe'][$p] = $classe_eleve;
			}
			
			//on poursuit le tableau $donnees_personne_csv avec l'adresse pour un mailling et des élèves associées 
			if ($user_status =='responsable') {
			
			    $donnees_personne_csv['classe'][$p] = $classe_resp;
			
				$resp_adr1=mysql_result($call_user_info, $p, "adr1");
				$resp_adr1=mysql_result($call_user_info, $p, "adr1");
				$resp_adr2=mysql_result($call_user_info, $p, "adr2");
				$resp_adr3=mysql_result($call_user_info, $p, "adr3");	  
				$resp_adr4=mysql_result($call_user_info, $p, "adr4");
				$resp_cp=mysql_result($call_user_info, $p, "cp");
				$resp_commune=mysql_result($call_user_info, $p, "commune");
				$resp_pays=mysql_result($call_user_info, $p, "pays");
				
				//on met les données dans le tableau 
				$donnees_personne_csv['adr1'][$p] = $resp_adr1;
				$donnees_personne_csv['adr2'][$p] = $resp_adr2;
				$donnees_personne_csv['adr3'][$p] = $resp_adr3;
				$donnees_personne_csv['adr4'][$p] = $resp_adr4;
				$donnees_personne_csv['cp'][$p] = $resp_cp;
				$donnees_personne_csv['commune'][$p] = $resp_commune;
				$donnees_personne_csv['pays'][$p] = $resp_pays;
				
				// On crée une chaine de carctères par élèves (Prénom, Nom, classe nom long et classe nom court)
				$nb_elv=sizeof($elv_resp['nom']);
			    $i=0;
				while ($i < $nb_elv){
                  $chaine_elv = "";
				  $chaine_elv.=$elv_resp['prenom'][$i];
				  $chaine_elv.=" ".$elv_resp['nom'][$i];
				  $chaine_elv.=" ".$elv_resp['nom_complet_classe'][$i];
				  if ($elv_resp['nom'][$i]!='') {$chaine_elv.=" (".$elv_resp['classe'][$i].")";}
				  
				  switch ($i) {
				  case 0 : $donnees_personne_csv['elv1'][$p] = $chaine_elv; Break;
				  case 1 : $donnees_personne_csv['elv2'][$p] = $chaine_elv; Break;
				  case 2 : $donnees_personne_csv['elv3'][$p] = $chaine_elv; Break;
				  case 3 : $donnees_personne_csv['elv4'][$p] = $chaine_elv; Break;
				  case 4 : $donnees_personne_csv['elv5'][$p] = $chaine_elv; Break;
				  case 5 : $donnees_personne_csv['elv6'][$p] = $chaine_elv; Break;
				  case 6 : $donnees_personne_csv['elv7'][$p] = $chaine_elv; Break;
				  }
                  $i++;
				}
			}
		}
		
		
		break;
		
	case 'pdf': //uniquement pour les élèves
		// création d'un tableau contenant toutes les informations à exporter
		$donnees_personne_csv['login'][$p] = $user_login;
		$donnees_personne_csv['nom'][$p] = $user_nom;
		$donnees_personne_csv['prenom'][$p] = $user_prenom;
		$donnees_personne_csv['new_password'][$p] = $new_password ;
		$donnees_personne_csv['user_email'][$p] = $user_email;
		
		//recherche de la classe de l'élève si mode 
		if ($user_status) {
			if ($user_status == 'eleve') {
				$sql_classe = "SELECT DISTINCT classe FROM `classes` c, `j_eleves_classes` jec WHERE (jec.login='".$user_login."' AND jec.id_classe=c.id)";
				$data_user_classe = mysql_query($sql_classe);
				$classe_eleve = mysql_result($data_user_classe, 0, "classe");
			}
		}
		
		$donnees_personne_csv['classe'][$p] = $classe_eleve;
		break;
		
	default:
		if ($user_statut == "responsable") {
			$impression = getSettingValue("ImpressionFicheParent");
			$nb_fiches = getSettingValue("ImpressionNombreParent");
		} elseif ($user_statut == "eleve") {
			$impression = getSettingValue("ImpressionFicheEleve");
			$nb_fiches = getSettingValue("ImpressionNombreEleve");
		} else {
			$impression = getSettingValue("Impression");
			$nb_fiches = getSettingValue("ImpressionNombre");
		}
		echo "<p>A l'attention de  <span class = \"bold\">" . $user_prenom . " " . $user_nom . "</span>";
		echo "<br />Nom de login : <span class = \"bold\">" . $user_login . "</span>";
		echo "<br />Mot de passe : <span class = \"bold\">" . $new_password . "</span>";
		echo "<br />Adresse E-mail : <span class = \"bold\">" . $user_email . "</span>";
		echo "</p>";
		echo $impression;
		if ($saut == $nb_fiches) {
			echo "<p class=saut>&nbsp</p>";
			$saut = 1;
		} else {
			$saut++;
		}
		
	} //fin switch
    	
    $p++;

}

// redirection à la fin de la génération des mots de passe
	switch ($mode_impression) {
	case 'csv' :
	    //sauvegarde des données dans la session Admin	
	   $_SESSION['donnees_export_csv_password']=$donnees_personne_csv;
	   
	    //redirection vers password_csv.php
		header("Location: ./password_csv.php"); die();
		break;
	case 'pdf' : 
         //sauvegarde des données dans la session Admin	
	   $_SESSION['donnees_export_csv_password']=$donnees_personne_csv;
	   
	    //redirection vers password_csv.php
		header("Location: ../impression/password_pdf.php"); die();
		break;	
	}
require("../lib/footer.inc.php");
?>
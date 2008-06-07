<?php

/*
 *
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

$titre_page = "Consultation d'un élève";

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles


// Resume session
$resultat_session = resumeSession();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
// SQL : INSERT INTO droits VALUES ( '/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/eleves/visu_eleve.php', 'V', 'V', 'V', 'V', 'F', 'F', 'V', 'F', 'Consultation_d_un_eleve', '');";
//
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ======================== CSS et js particuliers ========================
$utilisation_win = "oui";
$utilisation_jsdivdrag = "oui";
//$javascript_specifique = ".js";
$style_specifique = "eleves/visu_eleve";


$ele_login=isset($_POST['ele_login']) ? $_POST['ele_login'] : (isset($_GET['ele_login']) ? $_GET['ele_login'] : NULL);
$onglet=isset($_POST['onglet']) ? $_POST['onglet'] : (isset($_GET['onglet']) ? $_GET['onglet'] : NULL);
$onglet2=isset($_POST['onglet2']) ? $_POST['onglet2'] : (isset($_GET['onglet2']) ? $_GET['onglet2'] : NULL);

// ===================== entete Gepi ======================================//
require_once("../lib/header.inc");
// ===================== fin entete =======================================//

//debug_var();

echo "<div class='norme'><p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

//if(!isset($ele_login)) {
if((!isset($ele_login))&&(!isset($_POST['Recherche_sans_js']))) {
	echo "</p>\n";
	echo "</div>\n";

	// Formulaire pour navigateur SANS Javascript:
	echo "<noscript>
	<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire1'>
		Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' value='' />
		<input type='submit' name='Recherche_sans_js' value='Rechercher' />
	</form>
</noscript>\n";

	// Portion d'AJAX:
	echo "<script type='text/javascript'>
	function cherche_eleves() {
		rech_nom=document.getElementById('rech_nom').value;

		var url = 'liste_eleves.php';
		var myAjax = new Ajax.Request(
			url,
			{
				method: 'post',
				postBody: 'rech_nom='+rech_nom,
				onComplete: affiche_eleves
			});
	}

	function affiche_eleves(xhr) {
		if (xhr.status == 200) {
			document.getElementById('liste_eleves').innerHTML = xhr.responseText;
		}
		else {
			document.getElementById('liste_eleves').innerHTML = xhr.status;
		}
	}
</script>\n";

	// DIV avec formulaire pour navigateur AVEC Javascript:
	echo "<div id='recherche_avec_js' style='display:none;'>\n";

	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' onsubmit='cherche_eleves();return false;' method='post' name='formulaire'>";
	echo "Afficher les élèves dont le <b>nom</b> contient: <input type='text' name='rech_nom' id='rech_nom' value='' />\n";
	echo "<input type='button' name='Recherche' value='Rechercher' onClick='cherche_eleves()' />\n";
	echo "</form>\n";

	echo "<div id='liste_eleves'></div>\n";

	echo "</div>\n";
	echo "<script type='text/javascript'>document.getElementById('recherche_avec_js').style.display='';</script>\n";

}
elseif(isset($_POST['Recherche_sans_js'])) {
	// On ne passe ici que si JavaScript est désactivé
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	include("recherche_eleve.php");
}
else {
	echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir un autre élève</a>\n";
	echo "</p>\n";
	echo "</div>\n";

	// Affichage des onglets pour l'élève choisi

	// Couleurs pour les onglets:
	$tab_couleur['eleve']="moccasin";
	$tab_couleur['responsables']="mintcream";
	$tab_couleur['enseignements']="whitesmoke";
	$tab_couleur['bulletins']="lightyellow";
	$tab_couleur['bulletin']="lemonchiffon";
	$tab_couleur['releves']="papayawhip";
	$tab_couleur['releve']="seashell";

	// On vérifie que l'élève existe
	$sql="SELECT 1=1 FROM eleves WHERE login='$ele_login';";
	$res_ele=mysql_query($sql);

	if(mysql_num_rows($res_ele)==0){
		// On ne devrait pas arriver là.
		echo "<p>L'élève dont le login serait $ele_login n'est pas dans la table 'eleves'.</p>\n";
	}
	else{

		// A FAIRE:
		// Contrôler si la personne connectée a le droit de consulter les infos sur cet élève
		$acces_eleve="n";
		$acces_responsables="n";
		$acces_enseignements="n";
		$acces_releves="n";
		$acces_bulletins="n";

		if($_SESSION['statut']=='administrateur') {
			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";
			$acces_releves="n";
			$acces_bulletins="y";
		}
		elseif($_SESSION['statut']=='scolarite') {
			$sql="SELECT 1=1 FROM j_scol_classes jsc, j_eleves_classes jec WHERE jec.id_classe=jsc.id_classe AND jsc.login='".$_SESSION['login']."' AND jec.login='".$ele_login."';";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0) {
				echo "<p>Vous n'êtes pas responsable d'un élève dont le login serait $ele_login.</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}

			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";

			$GepiAccesReleveScol=getSettingValue('GepiAccesReleveScol');
			if($GepiAccesReleveScol=="yes") {
				$acces_releves="y";
			}

			$acces_bulletins="y";
		}
		elseif($_SESSION['statut']=='cpe') {
			$sql="SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='".$_SESSION['login']."' AND e_login='".$ele_login."';";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)==0) {
				echo "<p>Vous n'êtes pas responsable d'un élève dont le login serait $ele_login.</p>\n";
				require_once("../lib/footer.inc.php");
				die();
			}

			$acces_eleve="y";
			$acces_responsables="y";
			$acces_enseignements="y";

			$GepiAccesReleveCpe=getSettingValue('GepiAccesReleveCpe');
			if($GepiAccesReleveCpe=="yes") {
				$acces_releves="y";
			}

			$acces_bulletins="y";
		}
		elseif($_SESSION['statut']=='professeur') {

			$acces_eleve="y";
			$acces_responsables="n";
			$acces_enseignements="y";
			$acces_releves="n";
			$acces_bulletins="n";

			$sql="SELECT 1=1 FROM j_eleves_professeurs WHERE login='".$ele_login."' AND professeur='".$_SESSION['login']."';";
			$test=mysql_query($sql);

			if(mysql_num_rows($test)>0) {
				$is_pp="y";
			}
			else {
				$is_pp="n";
			}

			// Contrôle de l'accès à l'onglet Responsables
			$GepiAccesGestElevesProfP=getSettingValue('GepiAccesGestElevesProfP');
			if(($GepiAccesGestElevesProfP=="yes")&&($is_pp=="y")) {
				$acces_responsables="y";
			}

			// Contrôle de l'accès du prof au relevé de notes:
			$GepiAccesReleveProfP=getSettingValue('GepiAccesReleveProfP');
			if(($GepiAccesReleveProfP=="yes")&&($is_pp=="y")) {
				$acces_releves="y";
			}

			if($acces_releves=='n') {
				$GepiAccesReleveProfToutesClasses=getSettingValue('GepiAccesReleveProfToutesClasses');
				if($GepiAccesReleveProfToutesClasses=='yes') {
					$acces_releves="y";
				}
				else {
					$GepiAccesReleveProfTousEleves=getSettingValue('GepiAccesReleveProfTousEleves');
					//echo "\$GepiAccesReleveProfTousEleves=$GepiAccesReleveProfTousEleves<br />";
					if($GepiAccesReleveProfTousEleves=='yes') {
						$sql="SELECT 1=1 FROM j_eleves_classes jec,
											j_groupes_classes jgc,
											j_groupes_professeurs jgp
										WHERE jec.login='".$ele_login."' AND
												jec.id_classe=jgc.id_classe AND
												jgc.id_groupe=jgp.id_groupe AND
												jgp.login='".$_SESSION['login']."';";
						//echo "$sql<br />";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)>0) {
							$acces_releves="y";
						}
					}

					if($acces_releves=='n') {
						//echo "\$GepiAccesReleveProf=$GepiAccesReleveProf<br />";
						$GepiAccesReleveProf=getSettingValue('GepiAccesReleveProf');
						if($GepiAccesReleveProf=='yes') {
							$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
												j_groupes_professeurs jgp
											WHERE jeg.login='".$ele_login."' AND
													jeg.id_groupe=jgp.id_groupe AND
													jgp.login='".$_SESSION['login']."';";
							//echo "$sql<br />";
							$test=mysql_query($sql);

							if(mysql_num_rows($test)>0) {
								$acces_releves="y";
							}
						}
					}
				}
			}

			//echo "\$acces_releves=$acces_releves<br />";

			// Contrôle de l'accès du prof aux bulletins:
			$GepiAccesBulletinSimplePP=getSettingValue('GepiAccesBulletinSimplePP');
			if(($GepiAccesBulletinSimplePP=="yes")&&($is_pp=="y")) {
				$acces_bulletins="y";
			}

			if($acces_bulletins=='n') {
				$GepiAccesBulletinSimpleProfToutesClasses=getSettingValue('GepiAccesBulletinSimpleProfToutesClasses');
				if($GepiAccesBulletinSimpleProfToutesClasses=='yes') {
					$acces_bulletins="y";
				}
				else {
					$GepiAccesBulletinSimpleProfTousEleves=getSettingValue('GepiAccesBulletinSimpleProfTousEleves');
					if($GepiAccesBulletinSimpleProfTousEleves=='yes') {
						$sql="SELECT 1=1 FROM j_eleves_classes jec,
											j_groupes_classes jgc,
											j_groupes_professeurs jgp
										WHERE jec.login='".$ele_login."' AND
												jec.id_classe=jgc.id_classe AND
												jgc.id_groupe=jgp.id_groupe AND
												jgp.login='".$_SESSION['login']."';";
						//echo "$sql<br />";
						$test=mysql_query($sql);

						if(mysql_num_rows($test)>0) {
							$acces_bulletins="y";
						}
					}

					if($acces_bulletins=='n') {
						$GepiAccesBulletinSimpleProf=getSettingValue('GepiAccesBulletinSimpleProf');
						if($GepiAccesBulletinSimpleProf=='yes') {
							$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
												j_groupes_professeurs jgp
											WHERE jeg.login='".$ele_login."' AND
													jeg.id_groupe=jgp.id_groupe AND
													jgp.login='".$_SESSION['login']."';";
							//echo "$sql<br />";
							$test=mysql_query($sql);

							if(mysql_num_rows($test)>0) {
								$acces_bulletins="y";
							}
						}
					}
				}
			}
		}


		//===========================================
		// Extraction de quelques données sur l'établissement
		$RneEtablissement=getSettingValue("gepiSchoolRne") ? getSettingValue("gepiSchoolRne") : "";
		$gepiSchoolName=getSettingValue("gepiSchoolName") ? getSettingValue("gepiSchoolName") : "gepiSchoolName";
		$gepiSchoolAdress1=getSettingValue("gepiSchoolAdress1") ? getSettingValue("gepiSchoolAdress1") : "";
		$gepiSchoolAdress2=getSettingValue("gepiSchoolAdress2") ? getSettingValue("gepiSchoolAdress2") : "";
		$gepiSchoolZipCode=getSettingValue("gepiSchoolZipCode") ? getSettingValue("gepiSchoolZipCode") : "";
		$gepiSchoolCity=getSettingValue("gepiSchoolCity") ? getSettingValue("gepiSchoolCity") : "";
		$gepiSchoolPays=getSettingValue("gepiSchoolPays") ? getSettingValue("gepiSchoolPays") : "";
		$gepiYear = getSettingValue("gepiYear");

		$gepi_prof_suivi=getSettingValue("prof_suivi") ? getSettingValue("prof_suivi") : "";

		// Photo si module trombino actif
		$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes");
		//$bull_photo_largeur_max=getSettingValue("bull_photo_largeur_max") ? getSettingValue("bull_photo_largeur_max") : 100;
		//$bull_photo_hauteur_max=getSettingValue("bull_photo_hauteur_max") ? getSettingValue("bull_photo_hauteur_max") : 100;
		$photo_largeur_max=150;
		$photo_hauteur_max=150;

		// Lieu de naissance (peut ne pas être activé):
		$ele_lieu_naissance=getSettingValue("ele_lieu_naissance") ? getSettingValue("ele_lieu_naissance") : "n";

		//===========================================
		// Initialisations concernant les relevés de notes
		$p_releve_margin=getSettingValue("p_releve_margin") ? getSettingValue("p_releve_margin") : "";
		$releve_textsize=getSettingValue("releve_textsize") ? getSettingValue("releve_textsize") : 10;
		$releve_titlesize=getSettingValue("releve_titlesize") ? getSettingValue("releve_titlesize") : 16;
/*
		echo "<style type='text/css'>
	.releve_grand {
		color: #000000;
		font-size: ".$releve_titlesize."pt;
		font-style: normal;
	}

	.releve {
		color: #000000;
		font-size: ".$releve_textsize."pt;
		font-style: normal;\n";
		if($p_releve_margin!=""){
			echo "      margin-top: ".$p_releve_margin."pt;\n";
			echo "      margin-bottom: ".$p_releve_margin."pt;\n";
		}
		echo "}\n";

		echo "td.releve_empty{
		width:auto;
		padding-right: 20%;
	}

	.boireaus td {
		text-align:left;
	}\n";
		*/

		// Récupération des variables du bloc adresses:
		// Liste de récupération à extraire de la boucle élèves pour limiter le nombre de requêtes... A FAIRE
		// Il y a d'autres récupération de largeur et de positionnement du bloc adresse à extraire...
		// PROPORTION 30%/70% POUR LE 1er TABLEAU ET ...
		$releve_addressblock_logo_etab_prop=getSettingValue("releve_addressblock_logo_etab_prop") ? getSettingValue("releve_addressblock_logo_etab_prop") : 40;
		$releve_addressblock_autre_prop=100-$releve_addressblock_logo_etab_prop;

		// Taille des polices sur le bloc adresse:
		$releve_addressblock_font_size=getSettingValue("releve_addressblock_font_size") ? getSettingValue("releve_addressblock_font_size") : 12;

		// Taille de la cellule Classe et Année scolaire sur le bloc adresse:
		$releve_addressblock_classe_annee=getSettingValue("releve_addressblock_classe_annee") ? getSettingValue("releve_addressblock_classe_annee") : 35;
		// Calcul du pourcentage par rapport au tableau contenant le bloc Classe, Année,...
		$releve_addressblock_classe_annee2=round(100*$releve_addressblock_classe_annee/(100-$releve_addressblock_logo_etab_prop));

		// Débug sur l'entête pour afficher les cadres
		$releve_addressblock_debug=getSettingValue("releve_addressblock_debug") ? getSettingValue("releve_addressblock_debug") : "n";

		// Nombre de sauts de lignes entre le tableau logo+etab et le nom, prénom,... de l'élève
		$releve_ecart_bloc_nom=getSettingValue("releve_ecart_bloc_nom") ? getSettingValue("releve_ecart_bloc_nom") : 0;

		// Afficher l'établissement d'origine de l'élève:
		$releve_affiche_etab=getSettingValue("releve_affiche_etab") ? getSettingValue("releve_affiche_etab") : "n";

		// Bordure classique ou trait-noir:
		$releve_bordure_classique=getSettingValue("releve_bordure_classique") ? getSettingValue("releve_bordure_classique") : "y";
		if($releve_bordure_classique!="y"){
			$releve_class_bordure=" class='uneligne' ";
		}
		else{
			$releve_class_bordure="";
		}

		$releve_addressblock_length=getSettingValue("releve_addressblock_length") ? getSettingValue("releve_addressblock_length") : 6;
		$releve_addressblock_padding_top=getSettingValue("releve_addressblock_padding_top") ? getSettingValue("releve_addressblock_padding_top") : 0;
		$releve_addressblock_padding_text=getSettingValue("releve_addressblock_padding_text") ? getSettingValue("releve_addressblock_padding_text") : 0;
		$releve_addressblock_padding_right=getSettingValue("releve_addressblock_padding_right") ? getSettingValue("releve_addressblock_padding_right") : 0;



		// Affichage ou non du nom et de l'adresse de l'établissement
		$releve_affich_nom_etab=getSettingValue("releve_affich_nom_etab") ? getSettingValue("releve_affich_nom_etab") : "y";
		$releve_affich_adr_etab=getSettingValue("releve_affich_adr_etab") ? getSettingValue("releve_affich_adr_etab") : "y";
		if(($releve_affich_nom_etab!="n")&&($releve_affich_nom_etab!="y")) {$releve_affich_nom_etab="y";}
		if(($releve_affich_adr_etab!="n")&&($releve_affich_adr_etab!="y")) {$releve_affich_adr_etab="y";}

		$releve_ecart_entete=getSettingValue("releve_ecart_entete") ? getSettingValue("releve_ecart_entete") : 0;


		$releve_mention_doublant=getSettingValue("releve_mention_doublant") ? getSettingValue("releve_mention_doublant") : "n";


		$releve_cellspacing=getSettingValue("releve_cellspacing") ? getSettingValue("releve_cellspacing") : 2;
		$releve_cellpadding=getSettingValue("releve_cellpadding") ? getSettingValue("releve_cellpadding") : 5;


		$releve_affiche_numero=getSettingValue("releve_affiche_numero") ? getSettingValue("releve_affiche_numero") : "n";


		$releve_affiche_signature=getSettingValue("releve_affiche_signature") ? getSettingValue("releve_affiche_signature") : "y";

		$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "n";
		$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "Relevé à conserver précieusement. Aucun duplicata ne sera délivré. - GEPI : solution libre de gestion et de suivi des résultats scolaires.";


		$releve_col_hauteur=getSettingValue("releve_col_hauteur") ? getSettingValue("releve_col_hauteur") : 0;
		$releve_largeurtableau=getSettingValue("releve_largeurtableau") ? getSettingValue("releve_largeurtableau") : 800;
		$releve_col_matiere_largeur=getSettingValue("releve_col_matiere_largeur") ? getSettingValue("releve_col_matiere_largeur") : 150;

		$gepi_prof_suivi=getSettingValue("gepi_prof_suivi") ? getSettingValue("gepi_prof_suivi") : "professeur principal";

		$releve_affiche_eleve_une_ligne=getSettingValue("releve_affiche_eleve_une_ligne") ? getSettingValue("releve_affiche_eleve_une_ligne") : "n";
		$releve_mention_nom_court=getSettingValue("releve_mention_nom_court") ? getSettingValue("releve_mention_nom_court") : "y";

		$releve_photo_largeur_max=getSettingValue("releve_photo_largeur_max") ? getSettingValue("releve_photo_largeur_max") : 100;
		$releve_photo_hauteur_max=getSettingValue("releve_photo_hauteur_max") ? getSettingValue("releve_photo_hauteur_max") : 100;

		$releve_categ_font_size=getSettingValue("releve_categ_font_size") ? getSettingValue("releve_categ_font_size") : 10;
		$releve_categ_bgcolor=getSettingValue("releve_categ_bgcolor") ? getSettingValue("releve_categ_bgcolor") : "";

		$releve_affiche_tel=getSettingValue("releve_affiche_tel") ? getSettingValue("releve_affiche_tel") : "n";
		$releve_affiche_fax=getSettingValue("releve_affiche_fax") ? getSettingValue("releve_affiche_fax") : "n";

		if($releve_affiche_fax=="y"){
			$gepiSchoolFax=getSettingValue("gepiSchoolFax");
		}

		if($releve_affiche_tel=="y"){
			$gepiSchoolTel=getSettingValue("gepiSchoolTel");
		}

		$releve_affiche_INE_eleve=getSettingValue("releve_affiche_INE_eleve") ? getSettingValue("releve_affiche_INE_eleve") : "n";

		$genre_periode=getSettingValue("genre_periode") ? getSettingValue("genre_periode") : "M";

		$activer_photo_releve=getSettingValue("activer_photo_releve") ? getSettingValue("activer_photo_releve") : "n";
		$active_module_trombinoscopes=getSettingValue("active_module_trombinoscopes") ? getSettingValue("active_module_trombinoscopes") : "n";
		//===========================================





		// Bibliothèque de fonctions:
		include("visu_ele_func.lib.php");

		// On extrait un tableau de l'ensemble des infos sur l'élève (bulletins, relevés de notes,... inclus)
		$tab_ele=info_eleve($ele_login);

		// Initialisation
		if(!isset($onglet)) {
			$onglet="eleve";
		}

		//====================================
		// Onglet Informations générales sur l'élève
		echo "<div id='t_eleve' class='t_onglet' style='";
		if($onglet=='eleve') {
			echo "border-bottom-color: ".$tab_couleur['eleve']."; ";
		}
		else {
			echo "border-bottom-color: black; ";
		}
		echo "background-color: ".$tab_couleur['eleve']."; ";
		echo "'>";
		echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=eleve' onClick=\"affiche_onglet('eleve');return false;\">";
		echo "<b>".$tab_ele['nom']." ".$tab_ele['prenom']." (<i>".$tab_ele['liste_classes']."</i>)</b>";
		echo "</a>";
		echo "</div>\n";

		// Onglet Informations responsables
		if($acces_responsables=="y") {
			echo "<div id='t_responsables' class='t_onglet' style='";
			if($onglet=='responsables') {
				echo "border-bottom-color: ".$tab_couleur['responsables']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['responsables']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=responsables' onClick=\"affiche_onglet('responsables');return false;\">Responsables</a>";
			echo "</div>\n";
		}

		// Onglet Enseignements suivis
		if($acces_enseignements=="y") {
			echo "<div id='t_enseignements' class='t_onglet' style='";
			if($onglet=='enseignements') {
				echo "border-bottom-color: ".$tab_couleur['enseignements']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['enseignements']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=enseignements' onClick=\"affiche_onglet('enseignements');return false;\">Enseignements</a>";
			echo "</div>\n";
		}

		// Onglet Bulletins
		if($acces_bulletins=="y") {
			echo "<div id='t_bulletins' class='t_onglet' style='";
			if($onglet=='bulletins') {
				echo "border-bottom-color: ".$tab_couleur['bulletins']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['bulletins']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=bulletins' onClick=\"affiche_onglet('bulletins');return false;\">Bulletins</a>";
			echo "</div>\n";
		}

		// Onglet Relevés de notes
		if($acces_releves=="y") {
			echo "<div id='t_releves' class='t_onglet' style='";
			if($onglet=='releves') {
				echo "border-bottom-color: ".$tab_couleur['releves']."; ";
			}
			else {
				echo "border-bottom-color: black; ";
			}
			echo "background-color: ".$tab_couleur['releves']."; ";
			echo "'>";
			echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=releves' onClick=\"affiche_onglet('releves');return false;\">Relevés de notes</a>";
			echo "</div>\n";
		}
		//=====================================================================================

		//====================================
		echo "<div style='clear:both;'></div>\n";
		//====================================

		//=====================================================================================

		// On passe aux cadres contenu des onglets

		//===================
		// Onglet ELEVE
		//===================

		echo "<div id='eleve' class='onglet' style='";
		if($onglet!="eleve") {echo " display:none;";}
		echo "background-color: ".$tab_couleur['eleve']."; ";
		echo "'>";
		echo "<h2>Informations sur l'élève ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";

			echo "<table class='boireaus'>\n";
			echo "<tr><th style='text-align: left;'>Nom:</th><td>".$tab_ele['nom']."</td></tr>\n";
			echo "<tr><th style='text-align: left;'>Prénom:</th><td>".$tab_ele['prenom']."</td></tr>\n";
			echo "<tr><th style='text-align: left;'>Sexe:</th><td>".$tab_ele['sexe']."</td></tr>\n";
			echo "<tr><th style='text-align: left;'>Né le:</th><td>".$tab_ele['naissance']."</td></tr>\n";
			if(isset($tab_ele['lieu_naissance'])) {echo "<tr><th style='text-align: left;'>à:</th><td>".$tab_ele['lieu_naissance']."</td></tr>\n";}

			echo "<tr><th style='text-align: left;'>Régime:</th><td>";
			if ($tab_ele['regime'] == "d/p") {echo "Demi-pensionnaire";}
			if ($tab_ele['regime'] == "ext.") {echo "Externe";}
			if ($tab_ele['regime'] == "int.") {echo "Interne";}
			if ($tab_ele['regime'] == "i-e"){
				echo "Interne&nbsp;externé";
				if (strtoupper($tab_ele['sexe'])!= "F") {echo "e";}
			}
			echo "</td></tr>\n";

			echo "<tr><th style='text-align: left;'>Redoublant:</th><td>";
			if ($tab_ele['doublant'] == 'R'){
				echo "Oui";
			}
			else {
				echo "Non";
			}
			echo "</td></tr>\n";

			if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='cpe')) {
				echo "<tr><th style='text-align: left;'>Elenoet:</th><td>".$tab_ele['elenoet']."</td></tr>\n";
				echo "<tr><th style='text-align: left;'>Ele_id:</th><td>".$tab_ele['ele_id']."</td></tr>\n";
				echo "<tr><th style='text-align: left;'>N°INE:</th><td>".$tab_ele['no_gep']."</td></tr>\n";
			}

			//echo "<tr><th>:</th><td>".$tab_ele['']."</td></tr>\n";
			echo "</table>\n";
		echo "</td>\n";

		if($active_module_trombinoscopes=="y") {
			echo "<td valign='top'>\n";
				$photo=nom_photo($tab_ele['elenoet']);
				if("$photo"!=""){
					$photo="../photos/eleves/".$photo;
					if(file_exists($photo)){
						$dimphoto=redimensionne_image_releve($photo);
						echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />'."\n";
					}
				}
			echo "</td>\n";
		}
		echo "</tr>\n";
		echo "</table>\n";

		if(isset($tab_ele['etab_id'])) {
			if ($tab_ele['etab_id'] != '990') {
				if ($RneEtablissement != $tab_ele['etab_id']) {
					echo "<p>Etablissement d'origine : ";
					echo $tab_ele['etab_niveau_nom']." ".$tab_ele['etab_type']." ".$tab_ele['etab_nom']." (".$tab_ele['etab_cp']." ".$tab_ele['etab_ville'].")\n";
				}
			} else {
				echo "<p>Etablissement d'origine : ";
				echo "hors de France\n";
			}
			echo "</p>\n";
		}
		echo "</div>\n";

		//===================================================

		//=======================
		// Onglet RESPONSABLES
		//=======================

		if($acces_responsables=="y") {
			echo "<div id='responsables' class='onglet' style='";
			if($onglet!="responsables") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['responsables']."; ";
			echo "'>";
			echo "<h2>Responsables de l'élève ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			echo "<table border='0'>\n";
			echo "<tr>\n";
			$cpt_resp_legal0=0;
			for($i=0;$i<count($tab_ele['resp']);$i++) {
				if($tab_ele['resp'][$i]['resp_legal']!=0) {
					echo "<td valign='top'>\n";
					echo "<p>Responsable légal <b>".$tab_ele['resp'][$i]['resp_legal']."</b></p>\n";

					echo "<table class='boireaus'>\n";
					echo "<tr><th style='text-align: left;'>Nom:</th><td>".$tab_ele['resp'][$i]['nom']."</td></tr>\n";
					echo "<tr><th style='text-align: left;'>Prénom:</th><td>".$tab_ele['resp'][$i]['prenom']."</td></tr>\n";
					echo "<tr><th style='text-align: left;'>Civilité:</th><td>".$tab_ele['resp'][$i]['civilite']."</td></tr>\n";
					if($tab_ele['resp'][$i]['tel_pers']!='') {echo "<tr><th style='text-align: left;'>Tél.pers:</th><td>".$tab_ele['resp'][$i]['tel_pers']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['tel_port']!='') {echo "<tr><th style='text-align: left;'>Tél.port:</th><td>".$tab_ele['resp'][$i]['tel_port']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['tel_prof']!='') {echo "<tr><th style='text-align: left;'>Tél.prof:</th><td>".$tab_ele['resp'][$i]['tel_prof']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['mel']!='') {echo "<tr><th style='text-align: left;'>Courriel:</th><td>".$tab_ele['resp'][$i]['mel']."</td></tr>\n";}

					if(!isset($tab_ele['resp'][$i]['etat'])) {
						echo "<tr><th style='text-align: left;'>Dispose d'un compte:</th><td>Non</td></tr>\n";
					}
					else {
						echo "<tr><th style='text-align: left;'>Dispose d'un compte:</th><td>Oui (";
						if($tab_ele['resp'][$i]['etat']=='actif') {
							echo "<span style='color:green;'>";
						}
						else{
							echo "<span style='color:red;'>";
						}
						echo $tab_ele['resp'][$i]['etat'];
						echo "</span>)\n";
						echo "</td></tr>\n";
					}

					if($tab_ele['resp'][$i]['adr1']!='') {echo "<tr><th style='text-align: left;'>Ligne 1 adresse:</th><td>".$tab_ele['resp'][$i]['adr1']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['adr2']!='') {echo "<tr><th style='text-align: left;'>Ligne 2 adresse:</th><td>".$tab_ele['resp'][$i]['adr2']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['adr3']!='') {echo "<tr><th style='text-align: left;'>Ligne 3 adresse:</th><td>".$tab_ele['resp'][$i]['adr3']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['adr4']!='') {echo "<tr><th style='text-align: left;'>Ligne 4 adresse:</th><td>".$tab_ele['resp'][$i]['adr4']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['cp']!='') {echo "<tr><th style='text-align: left;'>Code postal:</th><td>".$tab_ele['resp'][$i]['cp']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['commune']!='') {echo "<tr><th style='text-align: left;'>Commune:</th><td>".$tab_ele['resp'][$i]['commune']."</td></tr>\n";}
					if($tab_ele['resp'][$i]['pays']!='') {echo "<tr><th style='text-align: left;'>Pays:</th><td>".$tab_ele['resp'][$i]['pays']."</td></tr>\n";}

					echo "</table>\n";
					echo "</td>\n";
				}
				else {
					$cpt_resp_legal0++;
				}
			}
			echo "</tr>\n";
			echo "</table>\n";

			// Simples contacts non responsables légaux
			if($cpt_resp_legal0>0) {
				echo "<table border='0'>\n";
				echo "<tr>\n";
				for($i=0;$i<count($tab_ele['resp']);$i++) {

					if($tab_ele['resp'][$i]['resp_legal']==0) {
						echo "<td valign='top'>\n";
						echo "<p>Contact (<i>non responsable légal</i>)</p>\n";

						echo "<table class='boireaus'>\n";
						echo "<tr><th style='text-align: left;'>Nom:</th><td>".$tab_ele['resp'][$i]['nom']."</td></tr>\n";
						echo "<tr><th style='text-align: left;'>Prénom:</th><td>".$tab_ele['resp'][$i]['prenom']."</td></tr>\n";
						echo "<tr><th style='text-align: left;'>Civilité:</th><td>".$tab_ele['resp'][$i]['civilite']."</td></tr>\n";
						if($tab_ele['resp'][$i]['tel_pers']!='') {echo "<tr><th style='text-align: left;'>Tél.pers:</th><td>".$tab_ele['resp'][$i]['tel_pers']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['tel_port']!='') {echo "<tr><th style='text-align: left;'>Tél.port:</th><td>".$tab_ele['resp'][$i]['tel_port']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['tel_prof']!='') {echo "<tr><th style='text-align: left;'>Tél.prof:</th><td>".$tab_ele['resp'][$i]['tel_prof']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['mel']!='') {echo "<tr><th style='text-align: left;'>Courriel:</th><td>".$tab_ele['resp'][$i]['mel']."</td></tr>\n";}

						if(!isset($tab_ele['resp'][$i]['etat'])) {
							echo "<tr><th style='text-align: left;'>Dispose d'un compte:</th><td>Non</td></tr>\n";
						}
						else {
							echo "<tr><th style='text-align: left;'>Dispose d'un compte:</th><td>Oui (";
							if($tab_ele['resp'][$i]['etat']=='actif') {
								echo "<span style='color:green;'>";
							}
							else{
								echo "<span style='color:red;'>";
							}
							echo $tab_ele['resp'][$i]['etat'];
							echo "</span>)\n";
							echo "</td></tr>\n";
						}

						if($tab_ele['resp'][$i]['adr1']!='') {echo "<tr><th style='text-align: left;'>Ligne 1 adresse:</th><td>".$tab_ele['resp'][$i]['adr1']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['adr2']!='') {echo "<tr><th style='text-align: left;'>Ligne 2 adresse:</th><td>".$tab_ele['resp'][$i]['adr2']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['adr3']!='') {echo "<tr><th style='text-align: left;'>Ligne 3 adresse:</th><td>".$tab_ele['resp'][$i]['adr3']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['adr4']!='') {echo "<tr><th style='text-align: left;'>Ligne 4 adresse:</th><td>".$tab_ele['resp'][$i]['adr4']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['cp']!='') {echo "<tr><th style='text-align: left;'>Code postal:</th><td>".$tab_ele['resp'][$i]['cp']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['commune']!='') {echo "<tr><th style='text-align: left;'>Commune:</th><td>".$tab_ele['resp'][$i]['commune']."</td></tr>\n";}
						if($tab_ele['resp'][$i]['pays']!='') {echo "<tr><th style='text-align: left;'>Pays:</th><td>".$tab_ele['resp'][$i]['pays']."</td></tr>\n";}

						echo "</table>\n";
						echo "</td>\n";
					}
				}
				echo "</tr>\n";
				echo "</table>\n";
			}
			echo "</div>\n";
		}

		//===================================================

		//========================
		// Onglet ENSEIGNEMENTS
		//========================

		if($acces_enseignements=="y") {
			echo "<div id='enseignements' class='onglet' style='";
			if($onglet!="enseignements") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['enseignements']."; ";
			echo "'>";
			echo "<h2>Enseignements suivis par l'élève ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			echo "<table class='boireaus'>\n";
			echo "<tr>\n";
			echo "<th>Enseignement</th>\n";
			echo "<th>Professeur(s)</th>\n";
			for($j=0;$j<count($tab_ele['periodes']);$j++) {
				echo "<th>\n";
				echo $tab_ele['periodes'][$j]['nom_periode'];
				echo "</th>\n";
			}
			echo "</tr>\n";

			$alt=1;
			for($i=0;$i<count($tab_ele['groupes']);$i++) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<th>".htmlentities($tab_ele['groupes'][$i]['name'])."<br /><span style='font-size: x-small;'>".htmlentities($tab_ele['groupes'][$i]['description'])."</span></th>\n";
				echo "<td>\n";
				for($j=0;$j<count($tab_ele['groupes'][$i]['prof']);$j++) {
					echo ucfirst(strtolower($tab_ele['groupes'][$i]['prof'][$j]['prenom']));
					echo " ";
					echo ucfirst(strtolower($tab_ele['groupes'][$i]['prof'][$j]['nom']));
					echo "<br />\n";
				}
				echo "</td>\n";
				for($j=0;$j<count($tab_ele['periodes']);$j++) {
					echo "<td";
					if(in_array($tab_ele['periodes'][$j]['num_periode'],$tab_ele['groupes'][$i]['periodes'])) {
						echo ">\n";
						//echo "X";
						echo $tab_ele['periodes'][$j]['classe'];
					}
					else {
						echo " style='background-color: gray;";
						echo "'>\n";
						echo "&nbsp;";
					}
					echo "</td>\n";
				}
				echo "</tr>\n";
			}
			echo "</table>\n";

			echo "</div>\n";
		}
		//===================================================

		//===================
		// Onglet BULLETINS
		//===================

		$tab_onglets_bull=array();
		if($acces_bulletins=="y") {
			echo "<div id='bulletins' class='onglet' style='";
			if($onglet!="bulletins") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['bulletins']."; ";
			echo "'>";

			echo "<h2>Bulletins de l'élève ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			$sql="SELECT MIN(periode) AS min_per, MAX(periode) AS max_per FROM matieres_notes WHERE login='".$ele_login."';";
			//echo "$sql<br />";
			$res_per=mysql_query($sql);
			if(mysql_num_rows($res_per)>0) {
				$lig_per=mysql_fetch_object($res_per);
				// Afficher les trois trimestres sur le bulletin simplifié affiche des infos erronées quant au nom des professeurs si l'élève a changé de classe.
				$periode_numero_1=$lig_per->min_per;
				$periode_numero_2=$lig_per->max_per;

				include "../lib/bulletin_simple.inc.php";

				//$tab_onglets_bull=array();
				for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
					$periode1=$n_per;
					$tab_onglets_bull[]="bulletin_$periode1";

					echo "<div id='t_bulletin_$periode1' class='t_onglet' style='";
					if(isset($onglet2)) {
						if($onglet2=="bulletin_$periode1") {
							echo "border-bottom-color: ".$tab_couleur['bulletin']."; ";
						}
					}
					else {
						if($n_per==$periode_numero_1) {
							echo "border-bottom-color: ".$tab_couleur['bulletin']."; ";
						}
					}
					echo "background-color: ".$tab_couleur['bulletin']."; ";
					echo "'>";

					echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=bulletins&amp;onglet2=bulletin_$periode1' onClick=\"affiche_onglet('bulletins');affiche_onglet_bull('bulletin_$periode1');return false;\">";
					echo "Période $periode1";
					echo "</a>";
					echo "</div>\n";

				}

				//====================================
				echo "<div style='clear:both;'></div>\n";
				//====================================

				for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
					$periode1=$n_per;
					$periode2=$n_per;

					$index_per=-1;
					for($loop=0;$loop<count($tab_ele['periodes']);$loop++) {
						if($tab_ele['periodes'][$loop]['num_periode']==$n_per) {
							$index_per=$loop;
							break;
						}
					}

					$id_classe=$tab_ele['periodes'][$index_per]['id_classe'];

					// Boucle sur la liste des classes de l'élève pour que $id_classe soit fixé avant l'appel: periodes.inc.php
					include "../lib/periodes.inc.php";


					// On teste la présence d'au moins un coeff pour afficher la colonne des coef
					$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
					// Apparemment, $test_coef est réaffecté plus loin dans un des include()
					$nb_coef_superieurs_a_zero=$test_coef;

					// On regarde si on affiche les catégories de matières
					$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
					if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

					// Si le rang des élèves est demandé, on met à jour le champ rang de la table matieres_notes
					$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");
					if ($affiche_rang == 'y') {
						$periode_num=$periode1;
						while ($periode_num < $periode2+1) {
							include "../lib/calcul_rang.inc.php";
							$periode_num++;
						}
					}

					$coefficients_a_1="non";
					$affiche_graph = 'n';

					unset($tab_moy_gen);
					//unset($tab_moy_cat_classe);
					for($loop=$periode1;$loop<=$periode2;$loop++) {
						$periode_num=$loop;
						include "../lib/calcul_moy_gen.inc.php";
						$tab_moy_gen[$loop]=$moy_generale_classe;
					}

					$display_moy_gen=sql_query1("SELECT display_moy_gen FROM classes WHERE id='".$id_classe."'");

					echo "<div id='bulletin_$periode1' class='onglet' style='";
					echo " background-color: ".$tab_couleur['bulletin'].";";
					if((isset($onglet2))&&(substr($onglet2,0,9)=='bulletin_')) {
						if('bulletin_'.$n_per!=$onglet2) {
							echo " display:none;";
						}
					}
					else {
						if($n_per!=$periode_numero_1) {
							echo " display:none;";
						}
					}
					echo "'>\n";

					bulletin($ele_login,1,1,$periode1,$periode2,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$nb_coef_superieurs_a_zero,$affiche_categories,'y');

					echo "</div>\n";
				}

			}
			else {
				// Il ne faut pas proposer de bulletin
				echo "<p>Aucun bulletin à ce jour.</p>\n";
			}

			echo "</div>\n";
		}
		//===================================================

		//===================================================

		//==========================
		// Onglet RELEVES DE NOTES
		//==========================

		$tab_onglets_rel=array();
		if($acces_releves=="y") {
			echo "<div id='releves' class='onglet' style='";
			if($onglet!="releves") {echo " display:none;";}
			echo "background-color: ".$tab_couleur['releves']."; ";
			echo "'>";

			$sql="SELECT MIN(ccn.periode) AS min_per, MAX(ccn.periode) AS max_per FROM cn_cahier_notes ccn,j_eleves_groupes jeg WHERE jeg.login='".$ele_login."' AND jeg.id_groupe=ccn.id_groupe AND jeg.periode=ccn.periode;";
			//echo "$sql<br />";
			$res_per=mysql_query($sql);
			if(mysql_num_rows($res_per)>0) {
				$lig_per=mysql_fetch_object($res_per);
				$periode_numero_1=$lig_per->min_per;
				$periode_numero_2=$lig_per->max_per;
			}
			else {
				// Il ne faut pas proposer de relevé de notes?
			}

			echo "<h2>Relevés de notes de l'élève ".$tab_ele['nom']." ".$tab_ele['prenom']."</h2>\n";

			$id_releve_1="";

			//$tab_onglets_rel=array();
			for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
				$periode1=$n_per;
				$tab_onglets_rel[]="releve_$periode1";

				echo "<div id='t_releve_$periode1' class='t_onglet' style='";
				if(isset($onglet2)) {
					if($onglet2=="releve_$periode1") {
						echo "border-bottom-color: ".$tab_couleur['releve']."; ";
					}
				}
				else {
					if($n_per==$periode_numero_1) {
						echo "border-bottom-color: ".$tab_couleur['releve']."; ";
					}
				}
				echo "background-color: ".$tab_couleur['releve']."; ";
				echo "'>";
				echo "<a href='".$_SERVER['PHP_SELF']."?ele_login=$ele_login&amp;onglet=releves&amp;onglet2=releve_$periode1' onClick=\"affiche_onglet('releves');affiche_onglet_rel('releve_$periode1');return false;\">";
				echo "Période $periode1";
				echo "</a>";
				echo "</div>\n";
			}

			//====================================
			echo "<div style='clear:both;'></div>\n";
			//====================================

			// Liste des infos à faire apparaitre sur le relevé de notes:
			$tab_ele['rn_app']='n';
			$tab_ele['rn_nomdev']='y';
			$tab_ele['rn_toutcoefdev']='y';
			$tab_ele['rn_coefdev_si_diff']='y';
			$tab_ele['rn_datedev']='y';
			$tab_ele['rn_sign_chefetab']='n';
			$tab_ele['rn_sign_pp']='n';
			$tab_ele['rn_sign_resp']='n';
			$tab_ele['rn_formule']='';

			for($n_per=$periode_numero_1;$n_per<=$periode_numero_2;$n_per++) {
				$periode1=$n_per;
				$periode2=$n_per;

				$index_per=-1;
				for($loop=0;$loop<count($tab_ele['periodes']);$loop++) {
					if($tab_ele['periodes'][$loop]['num_periode']==$n_per) {
						$index_per=$loop;
						break;
					}
				}

				// On récupère la classe de l'élève sur la période considérée
				$id_classe=$tab_ele['periodes'][$index_per]['id_classe'];
				//echo "\$id_classe=$id_classe<br />";

				// Boucle sur la liste des classes de l'élève pour que $id_classe soit fixé avant l'appel: periodes.inc.php
				include "../lib/periodes.inc.php";

				// On teste la présence d'au moins un coeff pour afficher la colonne des coef
				$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
				//echo "\$test_coef=$test_coef<br />";
				// Apparemment, $test_coef est réaffecté plus loin dans un des include()
				$nb_coef_superieurs_a_zero=$test_coef;

				// On regarde si on affiche les catégories de matières
				$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
				if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

				echo "<div id='releve_$periode1' class='onglet' style='";
				echo " background-color: ".$tab_couleur['releve'].";";
				if((isset($onglet2))&&(substr($onglet2,0,7)=='releve_')) {
					if('releve_'.$n_per!=$onglet2) {
						echo " display:none;";
					}
				}
				else {
					if($n_per!=$periode_numero_1) {
						echo " display:none;";
					}
				}
				echo "'>\n";
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				// IL MANQUE UN PAQUET D'INITIALISATIONS POUR LES APPELS global DANS releve_html()
				releve_html($tab_ele,$id_classe,$periode1,$index_per);
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				echo "</div>\n";
			}

			echo "</div>\n";
		}
		//===================================================

		//=====================================================================================

		//========================
		// Bricolages Javascript
		//========================

		// Liste des onglets de niveau 1
		$tab_onglets=array('eleve','responsables','enseignements','releves','bulletins');
		$chaine_tab_onglets="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets);$i++) {
			if($i>0) {$chaine_tab_onglets.=", ";}
			$chaine_tab_onglets.="'".$tab_onglets[$i]."'";
		}
		$chaine_tab_onglets.=");";

		// Liste des onglets dans l'onglet bulletins
		$chaine_tab_onglets_bull="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets_bull);$i++) {
			if($i>0) {$chaine_tab_onglets_bull.=", ";}
			$chaine_tab_onglets_bull.="'".$tab_onglets_bull[$i]."'";
		}
		$chaine_tab_onglets_bull.=");";

		// Liste des onglets dans l'onglet bulletins
		$chaine_tab_onglets_rel="tab_onglets=new Array(";
		for($i=0;$i<count($tab_onglets_rel);$i++) {
			if($i>0) {$chaine_tab_onglets_rel.=", ";}
			$chaine_tab_onglets_rel.="'".$tab_onglets_rel[$i]."'";
		}
		$chaine_tab_onglets_rel.=");";



		echo "<script type='text/javascript'>
	function affiche_onglet(id) {
		$chaine_tab_onglets

		for(i=0;i<tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';

			document.getElementById('t_'+id).style.borderBottomWidth='0px';
		}
	}

	function affiche_onglet_bull(id) {
		$chaine_tab_onglets_bull

		for(i=0;i<tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';
			document.getElementById('t_'+id).style.borderBottomWidth='0px';
		}
	}

	function affiche_onglet_rel(id) {
		$chaine_tab_onglets_rel

		for(i=0;i<tab_onglets.length;i++) {
			if(document.getElementById(tab_onglets[i])) {
				document.getElementById(tab_onglets[i]).style.display='none';
			}
			if(document.getElementById('t_'+tab_onglets[i])) {
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomColor='black';
				document.getElementById('t_'+tab_onglets[i]).style.borderBottomWidth='1px';
			}
		}

		if(document.getElementById(id)) {
			document.getElementById(id).style.display='';
		}
		if(document.getElementById('t_'+id)) {
			document.getElementById('t_'+id).style.borderBottomColor='white';
			document.getElementById('t_'+id).style.borderBottomWidth='0px';
		}
	}
</script>\n";

		echo "<p><br /></p>\n";
	}
}
?>

<?php
// Inclusion du bas de page
require_once("../lib/footer.inc.php");
?>

<?php
/*
* $Id$
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_discipline/mod_discipline_extraction_ooo.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_discipline/mod_discipline_extraction_ooo.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Discipline : Extrait OOo des incidents',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// SQL : INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');
// maj : $tab_req[] = "INSERT INTO droits VALUES ( '/mod_discipline/mod_discipline_extraction_ooo.php', 'V', 'F', 'V', 'V', 'F', 'F', 'F', 'F', 'Discipline : Extrait OOo des incidents', '');";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// debug_var();

$prefixe_generation_hors_dossier_mod_ooo="../mod_ooo/";
include_once('../mod_ooo/lib/lib_mod_ooo.php'); // les fonctions
$nom_fichier_modele_ooo =''; // variable a initialiser a blanc pour inclure le fichier suivant et eviter une notice. Pour les autres inclusions, cela est inutile.
include_once('../mod_ooo/lib/chemin.inc.php'); // le chemin des dossiers contenant les  modèles

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if((isset($mode))&&($mode=="choix")) {
	//**************** EN-TETE *******************************
	$titre_page = "Extraction discipline";
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE ****************************

	echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post' target='_blank'>
	<fieldset id='infosPerso' style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); '>
		<legend style='border: 1px solid grey; background-color: white; '>Choix de l'extraction</legend>

		<p>Extraire les incidents&nbsp;:<br />
		<select name='id_classe_incident'>
			<option value='' selected='true'>Toutes classes confondues</option>";
$sql="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->id'>$lig->classe ($lig->nom_complet)</option>";
}
echo "
		</select><br />
		Uniquement les incidents concernant (<em>pas nécessairement en responsable</em>) l'élève suivant&nbsp;:<br />
		<select name='protagoniste_incident'>
			<option value='' selected='true'>Tous élèves confondus</option>";
$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login ORDER BY e.nom, e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->login'>".casse_mot($lig->nom,"maj")." ".casse_mot($lig->prenom,"majf2")."</option>";
}
echo "
		</select>
		</p>

		<input type='submit' value='Extraire' />
	</fieldset>
</form>\n";


echo "<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50' style='margin-top:1em; '>
		<legend style='border: 1px solid grey; background-color: white; '>Autre mode d'extraction</legend>

		<p>Extraire les incidents";
/*
echo "&nbsp;:<br />
		<select name='id_classe_incident'>
			<option value='' selected='true'>Toutes classes confondues</option>";
$sql="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe, nom_complet;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->id'>$lig->classe ($lig->nom_complet)</option>";
}
echo "
		</select><br />
		Uniquement les incidents";
*/
echo " concernant l'élève suivant (<em>en tant que responsable</em>)&nbsp;:<br />
		<select name='protagoniste_incident'>
			<!--option value='' selected='true'>Tous élèves confondus</option-->
			<option value='' selected='true'>---</option>";

//$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login ORDER BY e.nom, e.prenom;";
// A FAIRE: Permettre de choisir les 'qualite'
$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, s_protagonistes sp WHERE e.login=sp.login AND sp.qualite='Responsable' ORDER BY e.nom, e.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig=mysqli_fetch_object($res)) {
	echo "
			<option value='$lig->login'>".casse_mot($lig->nom,"maj")." ".casse_mot($lig->prenom,"majf2")."</option>";
}
echo "
		</select><br />
		<input type='checkbox' name='avec_bloc_adresse_resp' id='avec_bloc_adresse_resp' value='y' /><label for='avec_bloc_adresse_resp' id='texte_avec_bloc_adresse_resp'> Avec le bloc adresse responsable/parent.</label><br />
		<input type='checkbox' name='anonymer_autres_protagonistes_eleves' id='anonymer_autres_protagonistes_eleves' value='y' /><label for='anonymer_autres_protagonistes_eleves' id='texte_anonymer_autres_protagonistes_eleves'> Anonymer (<em>remplacer par des XXXXXXXXX</em>) ce qui concerne les autres protagonistes élèves des incidents extraits.</label><br />
		<input type='checkbox' name='cacher_autres_protagonistes_eleves' id='cacher_autres_protagonistes_eleves' value='y' /><label for='cacher_autres_protagonistes_eleves' id='texte_cacher_autres_protagonistes_eleves'> Cacher ce qui concerne les autres protagonistes élèves des incidents extraits.</label><br />

		<!--input type='checkbox' name='debug' id='debug' value='y' /><label for='debug' id='texte_mode_test' style='color:red'> Debug pour contrôler les indices du tableau produit</label><br />

		<input type='checkbox' name='mode_test' id='mode_test' value='y' /><label for='mode_test' id='texte_mode_test' style='color:red'> Mode test...<br />
		Pour le moment, ça ne fonctionne pas.<br />
		C'est le même problème que pour l'adresse<br />
		Editer/modifier le fichier mod_discipline_liste_incidents2b.odt</label><br /-->

		<input type='hidden' name='mode' value='extract_responsable' />
		</p>

		<input type='submit' value='Extraire' />
	</fieldset>
</form>\n";

	// A FAIRE: Permettre de choisir les 'qualite', des dates,...

	require("../lib/footer.inc.php");
	die();
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
elseif((isset($mode))&&($mode=="extract_responsable")) {

	// A FAIRE : Ajouter un test sur l'accès aux infos parents pour la personne connectée.
	$avec_bloc_adresse_resp=isset($_POST['avec_bloc_adresse_resp']) ? $_POST['avec_bloc_adresse_resp'] : (isset($_GET['avec_bloc_adresse_resp']) ? $_GET['avec_bloc_adresse_resp'] : "n");
	$anonymer_autres_protagonistes_eleves=isset($_POST['anonymer_autres_protagonistes_eleves']) ? $_POST['anonymer_autres_protagonistes_eleves'] : (isset($_GET['anonymer_autres_protagonistes_eleves']) ? $_GET['anonymer_autres_protagonistes_eleves'] : "n");
	$cacher_autres_protagonistes_eleves=isset($_POST['cacher_autres_protagonistes_eleves']) ? $_POST['cacher_autres_protagonistes_eleves'] : (isset($_GET['cacher_autres_protagonistes_eleves']) ? $_GET['cacher_autres_protagonistes_eleves'] : "n");


	$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

	require_once("../mod_discipline/sanctions_func_lib.php");

	// Ce champ n'est plus posté
	$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");

	$chaine_criteres="";
	$date_incident="";
	$heure_incident="";
	$nature_incident="---";
	$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");
	$declarant_incident="---";
	$incidents_clos="y";

	// Actuellement on limite l'accès à un protagoniste en particulier
	if($protagoniste_incident=="") {
		echo "<p style='color:red'>Aucun protagoniste n'a été choisi.</p>";
		die();
	}

	if((!isset($id_classe_incident))||($id_classe_incident=="")) {
		$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
	}
	else {
		$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";
	}

	$ajout_sql="";
	if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
	if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
	if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
	if($protagoniste_incident!="") {$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}


	// A FAIRE : Permettre de choisir les 'qualite', des dates,...
	//           Actuellement, on n'extrait par ce mode que les responsables
	$qualite="Responsable";

	$ajout_sql.=" AND sp.qualite='$qualite'";
	$chaine_criteres.="&amp;qualite=$qualite";

	//echo "\$declarant_incident=$declarant_incident<br />";

	if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

	if($id_classe_incident!="") {
		$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
	}

	$sql.=$ajout_sql;
	$sql2=$sql;
	if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

	$sql.=")";
	$sql2.=")";

	$sql.=" ORDER BY date DESC, heure DESC;";
	$sql2.=" ORDER BY date DESC, heure DESC;";

	//echo "$sql<br />";
	//echo "$sql2<br />";

	$tab_lignes_OOo_eleve=array();
	$tab_lignes_OOo=array();

	// Test
	//$tab_lignes_OOo_eleve['eleve']['etab']=getSettingValue("gepiSchoolName");

	$tab_lignes_OOo_eleve['etab']=getSettingValue("gepiSchoolName");
	$tab_lignes_OOo_eleve['acad']=getSettingValue("gepiSchoolAcademie");
	$tab_lignes_OOo_eleve['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
	$tab_lignes_OOo_eleve['cp']=getSettingValue("gepiSchoolZipCode");
	$tab_lignes_OOo_eleve['ville']=getSettingValue("gepiSchoolCity");

	// Extraire l'adresse des responsables/parents...
	// get_adresse_responsable($pers_id) retourne $tab_adresse
	// Voir bull_func.lib.php
	$sql_resp="SELECT rp.* FROM resp_pers rp, responsables2 r, eleves e WHERE e.ele_id=r.ele_id AND r.resp_legal='1' AND r.pers_id=rp.pers_id AND e.login='".$protagoniste_incident."';";
	$res_resp=mysqli_query($GLOBALS["mysqli"], $sql_resp);
	if(mysqli_num_rows($res_resp)==0) {
		$tab_lignes_OOo_eleve['responsable']["civilite"]="";
		$tab_lignes_OOo_eleve['responsable']["nom"]="";
		$tab_lignes_OOo_eleve['responsable']["prenom"]="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr_id']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr1']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr2']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['adr3']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['cp']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['commune']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['pays']="";
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]['en_ligne']="";

		$tab_lignes_OOo_eleve["resp_civilite"]="";
		$tab_lignes_OOo_eleve["resp_nom"]="";
		$tab_lignes_OOo_eleve["resp_prenom"]="";
		$tab_lignes_OOo_eleve['resp_adr_id']="";
		$tab_lignes_OOo_eleve['resp_adr1']="";
		$tab_lignes_OOo_eleve['resp_adr2']="";
		$tab_lignes_OOo_eleve['resp_adr3']="";
		$tab_lignes_OOo_eleve['resp_cp']="";
		$tab_lignes_OOo_eleve['resp_commune']="";
		$tab_lignes_OOo_eleve['resp_pays']="";
		$tab_lignes_OOo_eleve['resp_adr_en_ligne']="";
	}
	else {
		$lig_resp=mysqli_fetch_object($res_resp);
		$tab_lignes_OOo_eleve['responsable']["civilite"]=$lig_resp->civilite;
		$tab_lignes_OOo_eleve['responsable']["nom"]=$lig_resp->nom;
		$tab_lignes_OOo_eleve['responsable']["prenom"]=$lig_resp->prenom;
		$tab_adr_courante=get_adresse_responsable($lig_resp->pers_id);
		$tab_lignes_OOo_eleve['responsable']["tab_adresse"]=$tab_adr_courante;

		$tab_lignes_OOo_eleve["resp_civilite"]=$lig_resp->civilite;
		$tab_lignes_OOo_eleve["resp_nom"]=$lig_resp->nom;
		$tab_lignes_OOo_eleve["resp_prenom"]=$lig_resp->prenom;
		$tab_lignes_OOo_eleve['resp_adr_id']=$tab_adr_courante['adr_id'];
		$tab_lignes_OOo_eleve['resp_adr1']=$tab_adr_courante['adr1'];
		$tab_lignes_OOo_eleve['resp_adr2']=$tab_adr_courante['adr2'];
		$tab_lignes_OOo_eleve['resp_adr3']=$tab_adr_courante['adr3'];
		$tab_lignes_OOo_eleve['resp_cp']=$tab_adr_courante['cp'];
		$tab_lignes_OOo_eleve['resp_commune']=$tab_adr_courante['commune'];
		$tab_lignes_OOo_eleve['resp_pays']=$tab_adr_courante['pays'];
		$tab_lignes_OOo_eleve['resp_adr_en_ligne']=$tab_adr_courante['en_ligne'];
	}

	$nb_ligne=0;
	$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_incident=mysqli_fetch_object($res_incident)) {
		$tab_lignes_OOo[$nb_ligne]=array();

		$tab_lignes_OOo[$nb_ligne]['etab']=getSettingValue("gepiSchoolName");
		$tab_lignes_OOo[$nb_ligne]['acad']=getSettingValue("gepiSchoolAcademie");
		$tab_lignes_OOo[$nb_ligne]['adr1']=getSettingValue("gepiSchoolAdress1")." ".getSettingValue("gepiSchoolAdress2");
		$tab_lignes_OOo[$nb_ligne]['cp']=getSettingValue("gepiSchoolZipCode");
		$tab_lignes_OOo[$nb_ligne]['ville']=getSettingValue("gepiSchoolCity");

		$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
		$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
		$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
		$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
		$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
		$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
		$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

		// Lieu
		$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

		// Protagonistes
		$tab_protagonistes_eleves=array();
		$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
		}
		else {
			$liste_protagonistes="";
			while($lig2=mysqli_fetch_object($res2)) {

				if(($lig2->statut=='eleve')&&($cacher_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
					if($lig2->statut=='eleve') {
						if(($anonymer_autres_protagonistes_eleves=="y")&&($lig2->login!=$protagoniste_incident)) {
							$liste_protagonistes.="XXX";
						}
						else {
							$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
						}
						$tab_protagonistes_eleves[]=$lig2->login;
					}
					else {
						$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
					}

					if($lig2->qualite!='') {
						$liste_protagonistes.=" $lig2->qualite";
					}
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

		$id_incident_courant=$lig_incident->id_incident;

		// Mesures prises
		$texte="";
		$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
		//$texte.="<br />$sql";
		$res_t_incident=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

		if($nb_login_ele_mesure_prise>0) {
			while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
				if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
					$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_mes_ele=mysqli_num_rows($res_mes_ele);

					if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
						$texte.="XXX :";
					}
					else {
						$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
					}
					while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
						$texte.=" ".$lig_mes_ele->mesure;
					}
					$texte.="\n";
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

		// Mesures demandees
		$texte="";
		$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
		//$texte.="<br />$sql";
		$res_t_incident2=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

		if($nb_login_ele_mesure_demandee>0) {
			while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
				if(($cacher_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
					// On n'affiche pas du tout l'info.
				}
				else {
					$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
					$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_mes_ele=mysqli_num_rows($res_mes_ele);

					if(($anonymer_autres_protagonistes_eleves=="y")&&($lig_t_incident->login_ele!=$protagoniste_incident)) {
						$texte.="XXX :";
					}
					else {
						$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
					}
					while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
						$texte.=" ".$lig_mes_ele->mesure;
					}
					$texte.="\n";
				}
			}
		}
		$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


		// Sanctions
		$texte_sanctions="";
		for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
			$ele_login=$tab_protagonistes_eleves[$i];

			if(($cacher_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
				// On n'affiche pas du tout l'info.
			}
			else {

				if(($anonymer_autres_protagonistes_eleves=="y")&&($ele_login!=$protagoniste_incident)) {
					$designation_eleve="XXX";
				}
				else {
					$designation_eleve=civ_nom_prenom($ele_login,'');
				}

				// Retenues
				$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Retenue ";
						$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

						$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
						if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

						$texte_sanctions.=formate_date($lig_sanction->date);
						$texte_sanctions.=" $lig_sanction->heure_debut";
						$texte_sanctions.=" (".$lig_sanction->duree."H)";
						$texte_sanctions.=" $lig_sanction->lieu";
						//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}

						$texte_sanctions.=" : ".$texte."\n";
					}
				}
	
				// Exclusions
				$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
				//$retour.="$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Exclusion ";
						$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

						$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
						$texte_sanctions.=" ".$lig_sanction->heure_debut;
						$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
						$texte_sanctions.=" ".$lig_sanction->heure_fin;
						$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}
						$texte_sanctions.=" : ".$texte;
					}
				}
	
				// Simple travail
				$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
				//$retour.="$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						//$texte_sanctions.=" : Travail pour le ";
						$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." pour le ";
						$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
							$texte="Aucun travail";
						}
						else {
							$texte=$lig_sanction->travail;
							if($tmp_doc_joints!="") {
								if($texte!="") {$texte.="\n";}
								$texte.=$tmp_doc_joints;
							}
						}
						$texte_sanctions.=" : ".$texte;
					}
				}
	
				// Autres sanctions
				$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
				//echo "$sql<br />\n";
				$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_sanction)>0) {
					$texte_sanctions.=$designation_eleve;

					while($lig_sanction=mysqli_fetch_object($res_sanction)) {
						$texte_sanctions.=" : $lig_sanction->description ";

						$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
						if($tmp_doc_joints!="") {
							$texte_sanctions.=$tmp_doc_joints;
						}
						$texte_sanctions.="\n";
					}
				}
			}
		}

		$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;


		$nb_ligne++;
	}

	$tab_lignes_OOo_eleve['incident']=$tab_lignes_OOo;

	if(isset($_POST['debug'])) {
		echo "<hr />tab_lignes_OOo_eleve<pre>";
		print_r($tab_lignes_OOo_eleve);
		echo "</pre>";
	}

	//die();

	$mode_ooo="imprime";
	
	include_once('../tbs/tbs_class.php');
	include_once('../tbs/plugins/tbs_plugin_opentbs.php');
	
	// Création d'une classe  TBS OOo class

	$OOo = new clsTinyButStrong;
	$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
	
	//$mode_test=isset($_POST['mode_test']) ? $_POST['mode_test'] : (isset($_GET['mode_test']) ? $_GET['mode_test'] : NULL);
	//if(isset($mode_test)) {
	if($avec_bloc_adresse_resp=="y") {
		$fichier_a_utiliser="mod_discipline_liste_incidents_bloc_adresse.odt";

		$tableau_a_utiliser=$tab_lignes_OOo_eleve;
		$tab_tmp_test['eleve']=$tab_lignes_OOo_eleve;
		$tableau_a_utiliser=$tab_tmp_test;

		$nom_a_utiliser="eleve";
	}
	else {
		$fichier_a_utiliser="mod_discipline_liste_incidents.odt";
		$tableau_a_utiliser=$tab_lignes_OOo;
		$nom_a_utiliser="incident";
	}
	
	// le chemin du fichier est indique a partir de l'emplacement de ce fichier
	$nom_dossier_modele_a_utiliser = $path."/";
	$nom_fichier_modele_ooo = $fichier_a_utiliser;
	$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);
	
	
	// $OOo->MergeBlock('eleves',$tab_eleves_OOo);
	$OOo->MergeBlock($nom_a_utiliser,$tableau_a_utiliser);
	
	$nom_fic = $fichier_a_utiliser;
	
	$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);
	
	
	
	$OOo->remove(); //suppression des fichiers de travail
	
	$OOo->close();

		die();



}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// Mode historique:

$path='../mod_ooo/'.$nom_dossier_modele_a_utiliser;

require_once("../mod_discipline/sanctions_func_lib.php");

$id_classe_incident=isset($_POST['id_classe_incident']) ? $_POST['id_classe_incident'] : (isset($_GET['id_classe_incident']) ? $_GET['id_classe_incident'] : "");
$chaine_criteres="";
$date_incident="";
$heure_incident="";
$nature_incident="---";
$protagoniste_incident=isset($_POST['protagoniste_incident']) ? $_POST['protagoniste_incident'] : (isset($_GET['protagoniste_incident']) ? $_GET['protagoniste_incident'] : "");
$declarant_incident="---";
$incidents_clos="y";

if((!isset($id_classe_incident))||($id_classe_incident=="")) {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp WHERE sp.id_incident=si.id_incident";
}
else {
	$sql="(SELECT DISTINCT si.* FROM s_incidents si, s_protagonistes sp, j_eleves_classes jec WHERE sp.id_incident=si.id_incident AND jec.id_classe='$id_classe_incident' AND jec.login=sp.login";
}

$ajout_sql="";
if($date_incident!="") {$ajout_sql.=" AND si.date='$date_incident'";$chaine_criteres.="&amp;date_incident=$date_incident";}
if($heure_incident!="") {$ajout_sql.=" AND si.heure='$heure_incident'";$chaine_criteres.="&amp;heure_incident=$heure_incident";}
if($nature_incident!="---") {$ajout_sql.=" AND si.nature='$nature_incident'";$chaine_criteres.="&amp;nature_incident=$nature_incident";}
if($protagoniste_incident!="") {$ajout_sql.=" AND sp.login='$protagoniste_incident'";$chaine_criteres.="&amp;protagoniste_incident=$protagoniste_incident";}

//echo "\$declarant_incident=$declarant_incident<br />";

if($declarant_incident!="---") {$ajout_sql.=" AND si.declarant='$declarant_incident'";$chaine_criteres.="&amp;declarant_incident=$declarant_incident";}

if($id_classe_incident!="") {
	$chaine_criteres.="&amp;id_classe_incident=$id_classe_incident";
}

$sql.=$ajout_sql;
$sql2=$sql;
if($incidents_clos!="y") {$sql.=" AND si.etat!='clos'";}

$sql.=")";
$sql2.=")";

$sql.=" ORDER BY date DESC, heure DESC;";
$sql2.=" ORDER BY date DESC, heure DESC;";

//echo "$sql<br />";
//echo "$sql2<br />";

$tab_lignes_OOo=array();

$nb_ligne=0;
$res_incident=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_incident=mysqli_fetch_object($res_incident)) {
	$tab_lignes_OOo[$nb_ligne]=array();
	
	$tab_lignes_OOo[$nb_ligne]['id_incident']=$lig_incident->id_incident;
	$tab_lignes_OOo[$nb_ligne]['declarant']=civ_nom_prenom($lig_incident->declarant,'');
	$tab_lignes_OOo[$nb_ligne]['date']=formate_date($lig_incident->date);
	$tab_lignes_OOo[$nb_ligne]['heure']=$lig_incident->heure;
	$tab_lignes_OOo[$nb_ligne]['nature']=$lig_incident->nature;
	$tab_lignes_OOo[$nb_ligne]['description']=$lig_incident->description;
	$tab_lignes_OOo[$nb_ligne]['etat']=$lig_incident->etat;

	// Lieu
	$tab_lignes_OOo[$nb_ligne]['lieu']=get_lieu_from_id($lig_incident->id_lieu);

	// Protagonistes
	$tab_protagonistes_eleves=array();
	$sql="SELECT * FROM s_protagonistes WHERE id_incident='$lig_incident->id_incident' ORDER BY statut,qualite,login;";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$tab_lignes_OOo[$nb_ligne]['protagonistes']="Aucun";
	}
	else {
		$liste_protagonistes="";
		while($lig2=mysqli_fetch_object($res2)) {
			if($liste_protagonistes!="") {$liste_protagonistes.=", ";}
			if($lig2->statut=='eleve') {
				$liste_protagonistes.=get_nom_prenom_eleve($lig2->login,'avec_classe');
				$tab_protagonistes_eleves[]=$lig2->login;
			}
			else {
				$liste_protagonistes.=civ_nom_prenom($lig2->login,'',"y");
			}

			if($lig2->qualite!='') {
				$liste_protagonistes.=" $lig2->qualite";
			}
		}
	}
	$tab_lignes_OOo[$nb_ligne]['protagonistes']=$liste_protagonistes;

	$id_incident_courant=$lig_incident->id_incident;

	// Mesures prises
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise'";
	//$texte.="<br />$sql";
	$res_t_incident=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_login_ele_mesure_prise=mysqli_num_rows($res_t_incident);

	if($nb_login_ele_mesure_prise>0) {
		while($lig_t_incident=mysqli_fetch_object($res_t_incident)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='prise' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_mes_ele=mysqli_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_prises']=$texte;

	// Mesures demandees
	$texte="";
	$sql="SELECT DISTINCT sti.login_ele FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' ORDER BY login_ele";
	//$texte.="<br />$sql";
	$res_t_incident2=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_login_ele_mesure_demandee=mysqli_num_rows($res_t_incident2);

	if($nb_login_ele_mesure_demandee>0) {
		while($lig_t_incident=mysqli_fetch_object($res_t_incident2)) {
			$sql="SELECT * FROM s_traitement_incident sti, s_mesures s WHERE sti.id_incident='$id_incident_courant' AND sti.id_mesure=s.id AND s.type='demandee' AND login_ele='$lig_t_incident->login_ele' ORDER BY s.mesure;";
			$res_mes_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			$nb_mes_ele=mysqli_num_rows($res_mes_ele);

			$texte.=civ_nom_prenom($lig_t_incident->login_ele,'')." :";
			while($lig_mes_ele=mysqli_fetch_object($res_mes_ele)) {
				$texte.=" ".$lig_mes_ele->mesure;
			}
			$texte.="\n";
		}
	}
	$tab_lignes_OOo[$nb_ligne]['mesures_demandees']=$texte;


	// Sanctions
	$texte_sanctions="";
	for($i=0;$i<count($tab_protagonistes_eleves);$i++) {
		$ele_login=$tab_protagonistes_eleves[$i];

		$designation_eleve=civ_nom_prenom($ele_login,'');

		// Retenues
		$sql="SELECT * FROM s_sanctions s, s_retenues sr WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sr.id_sanction=s.id_sanction ORDER BY sr.date, sr.heure_debut;";
		//echo "$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Retenue ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

				$nombre_de_report=nombre_reports($lig_sanction->id_sanction,0);
				if($nombre_de_report!=0) {$texte_sanctions.=" ($nombre_de_report reports)";}

				$texte_sanctions.=formate_date($lig_sanction->date);
				$texte_sanctions.=" $lig_sanction->heure_debut";
				$texte_sanctions.=" (".$lig_sanction->duree."H)";
				$texte_sanctions.=" $lig_sanction->lieu";
				//$texte_sanctions.="<td>".nl2br($lig_sanction->travail)."</td>\n";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}

				$texte_sanctions.=" : ".$texte."\n";
			}
		}
	
		// Exclusions
		$sql="SELECT * FROM s_sanctions s, s_exclusions se WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND se.id_sanction=s.id_sanction ORDER BY se.date_debut, se.heure_debut;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Exclusion ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." ";

				$texte_sanctions.=" ".formate_date($lig_sanction->date_debut);
				$texte_sanctions.=" ".$lig_sanction->heure_debut;
				$texte_sanctions.=" - ".formate_date($lig_sanction->date_fin);
				$texte_sanctions.=" ".$lig_sanction->heure_fin;
				$texte_sanctions.=" (".$lig_sanction->lieu.")";
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Simple travail
		$sql="SELECT * FROM s_sanctions s, s_travail st WHERE s.id_incident=$id_incident_courant AND s.login='".$ele_login."' AND st.id_sanction=s.id_sanction ORDER BY st.date_retour;";
		//$retour.="$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				//$texte_sanctions.=" : Travail pour le ";
				$texte_sanctions.=" : ".ucfirst($lig_sanction->nature)." pour le ";
				$texte_sanctions.=formate_date($lig_sanction->date_retour);
	
				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
					$texte="Aucun travail";
				}
				else {
					$texte=$lig_sanction->travail;
					if($tmp_doc_joints!="") {
						if($texte!="") {$texte.="\n";}
						$texte.=$tmp_doc_joints;
					}
				}
				$texte_sanctions.=" : ".$texte;
			}
		}
	
		// Autres sanctions
		$sql="SELECT * FROM s_sanctions s, s_autres_sanctions sa, s_types_sanctions2 sts WHERE s.id_incident='$id_incident_courant' AND s.login='".$ele_login."' AND sa.id_sanction=s.id_sanction AND sa.id_nature=sts.id_nature ORDER BY sts.nature;";
		//echo "$sql<br />\n";
		$res_sanction=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_sanction)>0) {
			$texte_sanctions.=$designation_eleve;

			while($lig_sanction=mysqli_fetch_object($res_sanction)) {
				$texte_sanctions.=" : $lig_sanction->description ";

				$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
				if($tmp_doc_joints!="") {
					$texte_sanctions.=$tmp_doc_joints;
				}
				$texte_sanctions.="\n";
			}
		}
	}

	$tab_lignes_OOo[$nb_ligne]['sanctions']=$texte_sanctions;


	$nb_ligne++;
}

/*
	echo "<pre>";
	print_r($tab_lignes_OOo);
	echo "</pre>";

	die();
*/

$mode_ooo="imprime";
	
include_once('../tbs/tbs_class.php');
include_once('../tbs/plugins/tbs_plugin_opentbs.php');

// Création d'une classe  TBS OOo class

$OOo = new clsTinyButStrong;
$OOo->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
	
$mode_test=isset($_POST['mode_test']) ? $_POST['mode_test'] : (isset($_GET['mode_test']) ? $_GET['mode_test'] : NULL);
if(isset($mode_test)) {
	$fichier_a_utiliser="mod_discipline_liste_incidents2b.odt";
	$tableau_a_utiliser=$tab_lignes_OOo_eleve;
	$nom_a_utiliser="eleve";
}
else {
	$fichier_a_utiliser="mod_discipline_liste_incidents.odt";
	$tableau_a_utiliser=$tab_lignes_OOo;
	$nom_a_utiliser="incident";
}

// le chemin du fichier est indique a partir de l'emplacement de ce fichier
//   $path."/".$tab_file[$num_fich]
$nom_dossier_modele_a_utiliser = $path."/";
$nom_fichier_modele_ooo = "mod_discipline_liste_incidents.odt";
$OOo->LoadTemplate($nom_dossier_modele_a_utiliser.$nom_fichier_modele_ooo, OPENTBS_ALREADY_UTF8);

$OOo->MergeBlock($nom_a_utiliser,$tableau_a_utiliser);

$nom_fic = $fichier_a_utiliser;

$OOo->Show(OPENTBS_DOWNLOAD, $nom_fic);


$OOo->remove(); //suppression des fichiers de travail

$OOo->close();

die();
?>

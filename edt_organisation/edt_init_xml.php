<?php
@set_time_limit(0);
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
* Ajout Sandrine Dangreville
* Divison Informatique -Rectorat de Creteil
* Importation de données des fichiers sts_emp et emp_sts
* pour injection dans gepi
*
*
* *
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

/*	// Resume session
	$resultat_session = $session_gepi->security_check();
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
*/
//**************** EN-TETE *****************
$titre_page = "Outil d'initialisation de l'année : Importation de l'emploi du temps";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//==================================================
$xml_sts_emp = simplexml_load_file('sts_emp.xml');
$xml_emp_sts = simplexml_load_file('emp_sts.xml');
//====================================================
//export des données de l etablissement


//initialisations des tableaux utilisés
$ar_prof=array();
$ar_etab=array();
$ar_matiere=array();
$ar_matiere_cdt=array();
$ar_matiere_temp=array();
$ar_classe=array();
$ar_alternance_temp=array();
$ar_alternance=array();
$ar_type_alternance=array();
$ar_salle_cours=array();
$ar_horaires=array();
$ar_periodes=array();
$ar_periodes_mktime=array();

//debut du décodage
foreach ($xml_sts_emp->xpath('//UAJ') as $rne_xml) {
$rne=(string)$rne_xml["CODE"];
/*echo "rne".strtolower($rne)."<br>";
   $ar_etab["CODE_ACAD"]=(string)$rne_xml->ACADEMIE->CODE;
    $ar_etab["LIBELLE"]=(string)$rne_xml->ACADEMIE->LIBELLE;
 echo "code acad" , $ar_etab["CODE_ACAD"] , "libelle" , $ar_etab["LIBELLE"],"<br>"; */
}

//export des données relatives a l annee
foreach ($xml_sts_emp->xpath('//ANNEE_SCOLAIRE') as $annee_xml) {
$s_annee=(string)$annee_xml["ANNEE"];
//echo "annee",$s_annee,"<br>";
}

//construction du tableau des jours de la semaine ' à partir de la table gepi

$query_horaires="select id_horaire_etablissement,jour_horaire_etablissement from horaires_etablissement";
$result_horaires=mysqli_query($GLOBALS["mysqli"], $query_horaires) or die(mysqli_error($GLOBALS["mysqli"]));
if ($result_horaires) {
while ($row_horaires=mysqli_fetch_row($result_horaires))
{
	$jour++;
	$ar_horaires["id_horaire_etab"][$jour]=$row_horaires[0];
	$ar_horaires["jour_horaire_etab"][$jour]=$row_horaires[1];
}
}

//construction du tableau des periodes
$query_periodes="select * from edt_creneaux";
$result_periodes=mysqli_query($GLOBALS["mysqli"], $query_periodes) or die(mysqli_error($GLOBALS["mysqli"]));
unset($i);
if ($result_periodes) {
while ($row_periodes=mysqli_fetch_row($result_periodes))
{
	$i++;
	$ar_periodes[$i]["id_definie_periode"]=$row_periodes[0];
	$ar_periodes[$i]["nom_definie_periode"]=$row_periodes[1];
	$ar_periodes[$i]["heuredebut_definie_periode"]=$row_periodes[2];
	list($heure,$minute,$seconde)=split(":",$row_periodes[2]);
	list($heure_fin,$minute_fin,$seconde_fib)=split(":",$row_periodes[3]);
	$ar_periodes[$i]["mktime_debut"]=mktime($heure,$minute,$seconde,0,0,0);
	$ar_periodes[$i]["mktime_fin"]=mktime($heure_fin,$minute_fin,$seconde_fin,0,0,0);
}
}
unset($i);

//export des matieres
$query_matiere="SELECT `matiere` FROM `matieres`;";
$result_matiere = mysqli_query($GLOBALS["mysqli"], $query_matiere) or die(mysqli_error($GLOBALS["mysqli"]));

//construction des tableaux des matieres deja présente dans l'emploi du temps
while ($ar_matiere_temp = mysqli_fetch_array($result_matiere,  MYSQLI_NUM)) {
   array_push($ar_matiere_cdt,$ar_matiere_temp[0]);
}

//construction du tableau des matieres a partir de sts_emp
foreach ($xml_sts_emp->xpath('//MATIERE') as $o_matiere) {
	$s_CODE_MATIERE=(string)$o_matiere["CODE"];
	$s_codegestion_matiere=(string)$o_matiere->CODE_GESTION;
	$s_NOM_matiere=(string)$o_matiere->LIBELLE_COURT;
	$s_NOM_matiere_long=(string)$o_matiere->LIBELLE_LONG;

 	$ar_matiere["$s_CODE_MATIERE"]=$s_codegestion_matiere;

 //integration eventuelle des matieres dans l'emploi du temps
if (in_array("$s_codegestion_matiere",$ar_matiere_cdt)) { //echo "La matiere existe deja<br>";
 }
else
{
$insert="INSERT INTO `matieres` (`matiere`,`nom_complet`,`priority`,`categorie_id`) VALUES ('$s_codegestion_matiere','$s_NOM_matiere_long','0','1');";
$result_insert=mysqli_query($GLOBALS["mysqli"], $insert) or die($insert.mysqli_error($GLOBALS["mysqli"]));
}
}

//export des données des profs
foreach ($xml_sts_emp->xpath('//INDIVIDU') as $o_nom)
	{
	$s_fonction=(string)$o_nom->FONCTION;
	$ID=(integer)$o_nom["ID"];
	if ($s_fonction == "ENS")
    {
 	   $ar_prof["$ID"]["NOM"]=(string)$o_nom->NOM_USAGE;
 	   $prenom=(string)$o_nom->PRENOM;
    	list($prenom1,$prenom2)=split(" ",$prenom);
//dans le cas ou on met un - au prenom composé
 //   	if ($prenom2) { $ar_prof["$ID"]["PRENOM"]=$prenom1."-".$prenom2; }
 //	 	else { $ar_prof["$ID"]["PRENOM"]=$prenom; }
//dans le cas ou on coupe le prénom
 	$ar_prof["$ID"]["PRENOM"]=$prenom1;


 		//export des disciplines du professeurs
 		$i=0;
 		foreach ($o_nom->DISCIPLINES as $o_discipline) {
 	   $ar_prof["$ID"]["DISCIPLINE"][$i]=(string)$o_discipline->DISCIPLINE->LIBELLE_COURT;
 	   $i++;
		}
     }
	}

//lecture des alternances
//construction d'un tableau pour chaque alternance
//construction d'un tableau avec les différents type d'alternances;
//***************cette partie ne semble pas correspodnre avec la demande de julien*******************
unset($i);

foreach ($xml_emp_sts->xpath('//ALTERNANCE') as $ar_alternance_temp)
	{
	$s_code=(string)$ar_alternance_temp["CODE"];
	array_push($ar_type_alternance,(string)$ar_alternance_temp["CODE"]);
	$ar_alternance["$s_code"]["CODE"]=(string)$ar_alternance_temp["CODE"];
	$ar_alternance["$s_code"]["LIBELLE_COURT"]=(string)$ar_alternance_temp->LIBELLE_COURT;
	$ar_alternance["$s_code"]["LIBELLE_LONG"]=(string)$ar_alternance_temp->LIBELLE_LONG;
	$$i=1;
/*	foreach ($ar_alternance_temp->SEMAINES->DATE_DEBUT_SEMAINE as $o_jour_semaine)
	{
		list($annee,$mois,$jour)=explode('-',(string)$o_jour_semaine);
		$ar_alternance["$s_code"][$i]=$annee.$mois.$jour;
		$query_alternance="select * from edt_semaines where type_edt_semaine='$s_code' and num_edt_semaine='".$ar_alternance["$s_code"][$i]."';";
		$result_alternance=mysql_query($query_alternance) or die(mysql_error());
		if (mysql_num_rows($result_alternance)<1)
		{
			$query_insert_alternance="insert into edt_semaines(num_edt_semaine,type_edt_semaine) values ('".$ar_alternance["$s_code"][$i]."','$s_code')";
			$result_insert_alternance=mysql_query($query_insert_alternance) or die(mysql_error());
		}
				$i++;
				echo "Insertion des semaines pour les cours ".$ar_alternance["$s_code"]["LIBELLE_LONG"]."<br>";
	}
//nombre d'éléments du tableau $ar_alternance["$s_code"][$i]
	$ar_alternance["$s_code"]["NOMBRE_SEMAINES"]=$i-1;
*/
	}
unset($i);


	//export des salles de cours
foreach ($xml_emp_sts->xpath('//SALLE_COURS') as $ar_salle_cours)
	{
		$salle=$ar_salle_cours["CODE"];
	$query_salle="select * from salle_cours where numero_salle='$salle'";
	$result_salle=mysqli_query($GLOBALS["mysqli"], $query_salle) or die(mysqli_error($GLOBALS["mysqli"]));

	if (mysqli_num_rows($result_salle)<1)
	{
//insertion de la salle si la salle n'a pas été trouvée
$query_ajout_salle="insert into salle_cours(`numero_salle`,`nom_salle`) values ('$salle','salle $salle');";
$result_insert_salle=mysqli_query($GLOBALS["mysqli"], $query_ajout_salle) or die(mysqli_error($GLOBALS["mysqli"]));
echo "Insertion de la salle $salle dans la base <br>";
	}
	}




//export des divisions et des services

echo "<h1>Traitement des classes entières</h1>";
	foreach ($xml_emp_sts->xpath('//DIVISION') as $o_classe)
	{
	$s_nom_classe=(string)$o_classe["CODE"];
	echo "<h2>Traitement de la classe".$s_nom_classe."</h2>";

	foreach ($o_classe->SERVICES->SERVICE as $o_service) {
$s_code_matiere=(string)$o_service["CODE_MATIERE"];


foreach ($o_service->ENSEIGNANTS->ENSEIGNANT as $o_prof) {

	//Recherche de l'id_groupe correspondant a ce cours

$ID_prof=$o_prof["ID"];
$s_nom_prof=$ar_prof["$ID_prof"]["NOM"];
$s_prenom_prof=$ar_prof["$ID_prof"]["PRENOM"];


$query="select login from utilisateurs where nom='$s_nom_prof' and prenom='$s_prenom_prof';";
$result_recherche_prof=mysqli_query($GLOBALS["mysqli"], $query);
if (mysqli_num_rows($result_recherche_prof)==0)
{ echo "<font color=red> Un professeur n'a pas été trouvé dans la base, vérifiez sa présence et relancez le script<br>Nom:$s_nom_prof et Prénom:$s_prenom_prof</font>"; next;}

$row_prof=mysqli_fetch_row($result_recherche_prof);
$query_classe="select j_groupes_classes.id_groupe from classes,j_groupes_classes,j_groupes_professeurs,j_groupes_matieres where classes.classe='$s_nom_classe' and classes.id=j_groupes_classes.id_classe and j_groupes_classes.id_groupe=j_groupes_professeurs.id_groupe and j_groupes_classes.id_groupe=j_groupes_matieres.id_groupe and j_groupes_professeurs.login='".$row_prof[0]."' and j_groupes_matieres.id_matiere='".$ar_matiere["$s_code_matiere"]."';";
$result_classe=mysqli_query($GLOBALS["mysqli"], $query_classe) or die(mysqli_error($GLOBALS["mysqli"]));
if (mysqli_num_rows($result_classe)>0) {
		$row_cours=mysqli_fetch_row($result_classe);
//		echo "<br>id_groupe :".$row_cours[0]."<br>";
		$id_groupe=$row_cours[0];
		}
	else {
			echo "<br> Pas de groupes correspondant avec les données  suivantes : Classe entiere $s_nom_classe  Prof : ".$row_prof[0]." Matiere ".$ar_matiere["$s_code_matiere"]." Traitement a faire ulterieurement <br>";
		continue;
		}
foreach ($o_service->ENSEIGNANTS->ENSEIGNANT->COURS_RATTACHES->COURS as $o_cours) {

//recherche de l'id de la salle
	$s_salle=(string)$o_cours->CODE_SALLE;
	$query_salle="select id_salle from salle_cours where numero_salle='$s_salle'";
	$result_salle=mysqli_query($GLOBALS["mysqli"], $query_salle) or die(mysqli_error($GLOBALS["mysqli"]));
	$row_salle=mysqli_fetch_row($result_salle);
	$id_salle=$row_salle[0];


	//recherche du champ jour_semaine
	$s_day_cours=(string)$o_cours->JOUR;

	//recherche des champs duree,heuredeb_dec
$s_heure_debut=(string)$o_cours->HEURE_DEBUT;
$s_heure=mb_substr($s_heure_debut,0,2);
$s_minute=mb_substr($s_heure_debut,2,2);
$s_duree_cours_brut=(string)$o_cours->DUREE;
$s_duree_h=mb_substr($s_duree_cours_brut,0,2);
$s_duree_min=intval(abs(mb_substr($s_duree_cours_brut,2,2)/30));
$i_duree=((integer)$s_duree_h*2+(integer)$s_duree_min);

$mktime_cours=mktime ($s_heure, $s_minute, 0, 0, 0, 0);

//************************* pour la recherche du code des semaines ou a lieu le cours
// j'extraie le code qui permet la lecture des semaines rattachées
$code_repetition_cours=(string)$o_cours->CODE_ALTERNANCE;

//recherche du champ id_definie_periode
//on s'appuie sur $s_heure_debut pour trouver le champ id_definie_periode
$heuredeb_cours=0;

foreach ($ar_periodes as $id_periodes=>$periodes)
{
if (($mktime_cours>=$periodes["mktime_debut"]) and ($mktime_cours<$periodes["mktime_fin"]))
{
$id_definie_periode=$periodes["id_definie_periode"];
// ******************les horaires effectifs et ceux indiqués dans l'emploi du temps different a cause des pause et des intercours
//on suppose donc que tous cours commencant 15 minutes après un créneau en fait partie
if ($mktime_cours>$periodes["mktime_debut"]+1500) { $heuredeb_cours=0.5; }
}
}

$query_verif_edt="select * from edt_cours where id_groupe='$id_groupe' and id_salle='$id_salle' and jour_semaine='".$ar_horaires["jour_horaire_etab"][$s_day_cours]."' and id_definie_periode='$id_definie_periode';";
$result_search_edt=mysqli_query($GLOBALS["mysqli"], $query_verif_edt) or die(mysqli_error($GLOBALS["mysqli"]));
if (!$result_search_edt) { continue;}
if (mysqli_num_rows($result_search_edt)>0)
{
echo "<br>Le groupe de la classe $s_nom_classe, matiere ".$ar_matiere["$s_code_matiere"].", prof $s_nom_prof,jour ".$ar_horaires["jour_horaire_etab"][$s_day_cours].", heure ".$ar_periodes[$id_definie_periode]["nom_definie_periode"]." , salle $s_salle existe déja <br>";
}
else
//insertion du cours qui n'est pas encore défini
{
//***************************************************************
//les champs  id_semaine, modif_edt ne sont pas correctement remplis
// a finir par julien
if ($heuredeb_cours==0)
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$id_definie_periode','$i_duree','$heuredeb_cours','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
else
//creation d'autre cours si heuredeb=0.5
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$id_definie_periode','1','$heuredeb_cours','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
$i_duree=$i_duree-1;
$increment_creneaux=0;
while ($i_duree>0)
{
$increment_creneaux++;
$new_id_periode=$id_definie_periode+$increment_creneaux;
if ($i_duree==0.5)
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$new_id_periode','$i_duree','0.5','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
else
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$new_id_periode','$i_duree','0','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
$i_duree=$i_duree-2;
}
// le cours peut aussi finir en milieu de créneaux, on rajoute une demi-heure sur le créneau suivant

}

	echo "<p><font color=green>Insertion du cours pour le groupe de la classe $s_nom_classe, matiere ".$ar_matiere["$s_code_matiere"].", prof $s_nom_prof,jour ".$ar_horaires["jour_horaire_etab"][$s_day_cours].", heure ".$ar_periodes[$id_definie_periode]["nom_definie_periode"]." , salle $s_salle pour la période ".$ar_alternance[$code_repetition_cours]["LIBELLE_LONG"]."</font></p>";
}
unset($periodes);
}
}
}
}



//construction des groupes et des services
echo "<h1>Traitement des groupes</h1>";

	foreach ($xml_emp_sts->xpath('//GROUPE') as $o_groupe)
	{
	$s_nom_groupe=(string)$o_groupe["CODE"];
	echo "<h2>Groupe ".$s_nom_groupe."</h2>";

	foreach ($o_groupe->DIVISIONS_APPARTENANCE->DIVISION_APPARTENANCE as $o_groupe_division)
	{
	$s_nom_classe=(string)$o_groupe_division["CODE"];
	echo "Ce groupe a des éléments dans la classe ".$s_nom_classe."<br>";


foreach ($o_groupe->SERVICES->SERVICE as $o_service_groupe) {
$s_code_matiere_groupe=(string)$o_service_groupe["CODE_MATIERE"];
foreach ($o_service_groupe->ENSEIGNANTS->ENSEIGNANT as $o_prof_groupe) {

	//Recherche de l'id_groupe correspondant a ce cours

$ID_prof=$o_prof_groupe["ID"];
$s_nom_prof=$ar_prof["$ID_prof"]["NOM"];
$s_prenom_prof=$ar_prof["$ID_prof"]["PRENOM"];

$query="select login from utilisateurs where nom='$s_nom_prof' and prenom = '$s_prenom_prof';";
$result_recherche_prof=mysqli_query($GLOBALS["mysqli"], $query);
if (mysqli_num_rows($result_recherche_prof)==0)
{ echo " <font color=red>Un professeur n'a pas été trouvé dans la base, vérifiez sa présence et relancez le script<br>Nom:$s_nom_prof et Prénom:$s_prenom_prof</font>"; next;}
$row_prof=mysqli_fetch_row($result_recherche_prof);
$query_classe="select j_groupes_classes.id_groupe from classes,j_groupes_classes,j_groupes_professeurs,j_groupes_matieres where classes.classe='$s_nom_classe' and classes.id=j_groupes_classes.id_classe and j_groupes_classes.id_groupe=j_groupes_professeurs.id_groupe and j_groupes_classes.id_groupe=j_groupes_matieres.id_groupe and  j_groupes_professeurs.login='".$row_prof[0]."' and j_groupes_matieres.id_matiere='".$ar_matiere["$s_code_matiere_groupe"]."';";
$result_classe=mysqli_query($GLOBALS["mysqli"], $query_classe) or die(mysqli_error($GLOBALS["mysqli"]));

if (mysqli_num_rows($result_classe)>0) {
		$row_cours=mysqli_fetch_row($result_classe);
//		echo "<br>id_groupe : ".$row_cours[0]."<br>";
		$id_groupe=$row_cours[0];
		}
	else {
		echo "<br> Pas de groupes correspondant avec les données  suivantes : Groupe  $s_nom_classe  Prof : ".$row_prof[0]." Matiere ".$ar_matiere["$s_code_matiere_groupe"]." Traitement a faire ulterieurement <br>";
		continue;
		}
foreach ($o_service_groupe->ENSEIGNANTS->ENSEIGNANT->COURS_RATTACHES->COURS as $o_cours_groupe) {

//recherche de l'id de la salle
	$s_salle=(string)$o_cours_groupe->CODE_SALLE;
	$query_salle="select id_salle from salle_cours where numero_salle='$s_salle'";
	$result_salle=mysqli_query($GLOBALS["mysqli"], $query_salle) or die(mysqli_error($GLOBALS["mysqli"]));
	$row_salle=mysqli_fetch_row($result_salle);
	$id_salle=$row_salle[0];

	//recherche du champ jour_semaine
	$s_day_cours=(string)$o_cours_groupe->JOUR;

	//************************* pour la recherche du code des semaines ou a lieu le cours
// j'extraie le code qui permet la lecture des semaines rattachées
$code_repetition_cours=(string)$o_cours_groupe->CODE_ALTERNANCE;


	//recherche des champs duree,heuredeb_dec
$s_heure_debut=(string)$o_cours_groupe->HEURE_DEBUT;
$s_heure=mb_substr($s_heure_debut,0,2);
$s_minute=mb_substr($s_heure_debut,2,2);
$s_duree_cours_brut=(string)$o_cours_groupe->DUREE;
$s_duree_h=mb_substr($s_duree_cours_brut,0,2);
$s_duree_min=intval(abs(mb_substr($s_duree_cours_brut,2,2)/30));
$i_duree=((integer)$s_duree_h*2+(integer)$s_duree_min);

$mktime_cours=mktime ($s_heure, $s_minute, 0, 0, 0, 0);


//recherche du champ id_definie_periode
//on s'appuie sur $s_heure_debut pour trouver le champ id_definie_periode
$heuredeb_cours=0;

foreach ($ar_periodes as $id_periodes=>$periodes)
{
if (($mktime_cours>=$periodes["mktime_debut"]) and ($mktime_cours<$periodes["mktime_fin"]))
{
$id_definie_periode=$periodes["id_definie_periode"];
// ******************les horaires effectifs et ceux indiqués dans l'emploi du temps different a cause des pause et des intercours
//on suppose donc que tous cours commencant 15 minutes après un créneau en fait partie
if ($mktime_cours>$periodes["mktime_debut"]+1500) { $heuredeb_cours=0.5; }
}
}

$query_verif_edt="select * from edt_cours where id_groupe='$id_groupe' and id_salle='$id_salle' and jour_semaine='".$ar_horaires["jour_horaire_etab"][$s_day_cours]."' and id_definie_periode='$id_definie_periode';";
$result_search_edt=mysqli_query($GLOBALS["mysqli"], $query_verif_edt) or die(mysqli_error($GLOBALS["mysqli"]));
if (!$result_search_edt) { continue;}
if (mysqli_num_rows($result_search_edt)>0)
{
echo "<br>Le groupe de la classe $s_nom_classe, matiere ".$ar_matiere["$s_code_matiere_groupe"].", prof $s_nom_prof,jour ".$ar_horaires["jour_horaire_etab"][$s_day_cours].", heure ".$ar_periodes[$id_definie_periode]["nom_definie_periode"]." , salle $s_salle existe déja <br>";
}
else
//insertion du cours qui n'est pas encore défini
{
//***************************************************************
//les champs  id_semaine, modif_edt ne sont pas correctement remplis
// a finir par julien
if ($heuredeb_cours==0)
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$id_definie_periode','$i_duree','$heuredeb_cours','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
else
//creation d'autre cours si heuredeb=0.5
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$id_definie_periode','1','$heuredeb_cours','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
$i_duree=$i_duree-1;
$increment_creneaux=0;
while ($i_duree>0)
{
$increment_creneaux++;
$new_id_periode=$id_definie_periode+$increment_creneaux;
if ($i_duree==0.5)
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$new_id_periode','$i_duree','0.5','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
else
{
$query_insert_edt="insert into edt_cours(id_groupe,id_salle,jour_semaine,id_definie_periode,duree,heuredeb_dec,id_semaine) values ('$id_groupe','$id_salle','".$ar_horaires["jour_horaire_etab"][$s_day_cours]."','$new_id_periode','$i_duree','0','***')";
$result_insert_edt=mysqli_query($GLOBALS["mysqli"], $query_insert_edt) or die(mysqli_error($GLOBALS["mysqli"]));
}
$i_duree=$i_duree-2;
}
// le cours peut aussi finir en milieu de créneaux, on rajoute une demi-heure sur le créneau suivant

}

	echo "<p><font color=green>Insertion du cours pour le groupe de la classe $s_nom_classe, matiere ".$ar_matiere["$s_code_matiere_groupe"].", prof $s_nom_prof,jour ".$ar_horaires["jour_horaire_etab"][$s_day_cours].", heure ".$ar_periodes[$id_definie_periode]["nom_definie_periode"]." , salle $s_salle pour la période ".$ar_alternance[$code_repetition_cours]["LIBELLE_LONG"]."</font></p>";
}
unset($periodes);
}
}
}
}
	}


 ?>
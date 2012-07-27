<?php
/*
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
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

$variables_non_protegees='yes';

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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_examen_blanc/saisie_notes.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_examen_blanc/saisie_notes.php',
administrateur='V',
professeur='V',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Examen blanc: Saisie devoir hors enseignement',
statut='';";
$insert=mysql_query($sql);
}




//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

include('lib_exb.php');

$id_exam=isset($_POST['id_exam']) ? $_POST['id_exam'] : (isset($_GET['id_exam']) ? $_GET['id_exam'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$matiere=isset($_POST['matiere']) ? $_POST['matiere'] : (isset($_GET['matiere']) ? $_GET['matiere'] : NULL);

$id_ex_grp=isset($_POST['id_ex_grp']) ? $_POST['id_ex_grp'] : (isset($_GET['id_ex_grp']) ? $_GET['id_ex_grp'] : NULL);

$reg_notes=isset($_POST['reg_notes']) ? $_POST['reg_notes'] : (isset($_GET['reg_notes']) ? $_GET['reg_notes'] : NULL);
$reg_eleves=isset($_POST['reg_eleves']) ? $_POST['reg_eleves'] : (isset($_GET['reg_eleves']) ? $_GET['reg_eleves'] : NULL);

$export_csv=isset($_GET['export_csv']) ? $_GET['export_csv'] : "n";
$import_csv=isset($_POST['import_csv']) ? $_POST['import_csv'] : "n";

// ATTENTION: Avec $id_exam/$id_groupe et $id_ex_grp on a une clé de trop...

//$modif_exam=isset($_POST['modif_exam']) ? $_POST['modif_exam'] : (isset($_GET['modif_exam']) ? $_GET['modif_exam'] : NULL);

$acces_mod_exb_prof="n";
if($_SESSION['statut']=='professeur') {

	if(!is_pp($_SESSION['login'])) {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if(getSettingValue('modExbPP')!='yes') {
		// A FAIRE: AJOUTER UN tentative_intrusion()...
		header("Location: ../logout.php?auto=1");
		die();
	}

	if((isset($id_exam))&&(!is_pp_proprio_exb($id_exam))) {
		header("Location: ../accueil.php?msg=".rawurlencode("Vous n'êtes pas propriétaire de l'examen blanc n°$id_exam."));
		die();
	}

	$acces_mod_exb_prof="y";
}

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {

	//if(isset($id_exam)) {
	if((isset($id_exam))&&(isset($matiere))) {
		$msg="";

		$sql="SELECT * FROM ex_examens WHERE id='$id_exam';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			$msg="L'examen choisi (<i>$id_exam</i>) n'existe pas.<br />\n";
			unset($reg_eleves);
			unset($reg_notes);
		}
		else {
			$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)==0) {
				$msg="Aucun groupe hors enseignement n'a été trouvé pour cet examen.<br />\n";
				unset($reg_eleves);
				unset($reg_notes);
			}
			else {
				$lig=mysql_fetch_object($res);
				$id_ex_grp=$lig->id;
			}
		}

		if($reg_eleves=='y') {
			check_token();

			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());

			//$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
			//$suppr=mysql_query($sql);
			$sql="SELECT login FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			$tab_ele_inscrits=array();
			$nb_suppr_ele=0;
			while($lig=mysql_fetch_object($res)) {
				$tab_ele_inscrits[]=$lig->login;
				if(!in_array($lig->login, $login_ele)) {
					//$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp' AND login='$login_ele[$i]';";
					$sql="DELETE FROM ex_notes WHERE id_ex_grp='$id_ex_grp' AND login='$lig->login';";
					//echo "$sql<br />\n";
					$suppr=mysql_query($sql);
					if($suppr) {$nb_suppr_ele++;}
				}
			}
			if($nb_suppr_ele>0) {$msg.="$nb_suppr_ele élève(s) retiré(s).<br />";}

			$nb_ajout_ele=0;
			for($i=0;$i<count($login_ele);$i++) {
				if(!in_array($login_ele[$i], $tab_ele_inscrits)) {
					$sql="INSERT INTO ex_notes SET id_ex_grp='$id_ex_grp', login='$login_ele[$i]', statut='v';";
					//echo "$sql<br />\n";
					$insert=mysql_query($sql);
					if($insert) {$nb_ajout_ele++;}
				}
			}
			if($nb_ajout_ele>0) {$msg.="$nb_ajout_ele élève(s) ajouté(s).<br />";}
		}
		elseif($reg_notes=='y') {
			check_token();

			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());
			$note=isset($_POST['note']) ? $_POST['note'] : (isset($_GET['note']) ? $_GET['note'] : array());
		
			$msg="";
		
			for($i=0;$i<count($login_ele);$i++) {
				$elev_statut='';
				if(($note[$i]=='disp')){
					$elev_note='0';
					$elev_statut='disp';
				}
				elseif(($note[$i]=='abs')){
					$elev_note='0';
					$elev_statut='abs';
				}
				elseif(($note[$i]=='-')){
					$elev_note='0';
					$elev_statut='-';
				}
				elseif(preg_match("/^[0-9\.\,]{1,}$/",$note[$i])) {
					$elev_note=str_replace(",", ".", "$note[$i]");
					if(($elev_note<0)||($elev_note>20)){
						$elev_note='';
						//$elev_statut='';
						$elev_statut='v';
					}
				}
				else{
					$elev_note='';
					//$elev_statut='';
					$elev_statut='v';
				}
				if(($elev_note!='')or($elev_statut!='')){
					$sql="UPDATE ex_notes SET note='$elev_note', statut='$elev_statut' WHERE id_ex_grp='$id_ex_grp' AND login='$login_ele[$i]';";
					//echo "$sql<br />\n";
					$res=mysql_query($sql);
					if(!$res) {
						$msg.="Erreur: $sql<br />";
					}
				}
			}
		
			if($msg=='') {
				$msg="Enregistrement effectué.";
			}

		}
	}




	if($export_csv=='y') {
		$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
		$res=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$msg="Le groupe hors enseignement n'a pas été identifié???";
		}
		else {
			$lig=mysql_fetch_object($res);
			$id_ex_grp=$lig->id;
	
			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="Aucune classe n'est associée à l'examen n°$id_exam???\n";
			}
			else {
				$csv="";
				$csv.="LOGIN;NOM_PRENOM;CLASSE;NOTE\n";

				while($lig_class=mysql_fetch_object($res_classes)) {
					$sql="SELECT DISTINCT e.login, e.nom, e.prenom, en.note, en.statut FROM j_eleves_classes jec, eleves e, ex_notes en WHERE jec.id_classe='$lig_class->id_classe' AND jec.login=e.login AND en.login=e.login AND en.id_ex_grp='$id_ex_grp' ORDER BY e.nom, e.prenom;";
					$res_ele=mysql_query($sql);
					$nb_ele=mysql_num_rows($res_ele);
					if($nb_ele>0) {
						while($lig=mysql_fetch_object($res_ele)) {

							$csv.=$lig->login.";".casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2').";".$lig_class->classe.";";

							if(($lig->statut=='v')) {$csv.="\n";}
							elseif($lig->statut!='') {$csv.=$lig->statut."\n";}
							else {$csv.=$lig->note."\n";}
						}
					}
				}

				$nom_fic="Examen_".$id_exam."_Groupe_Hors_Enseignement_".$matiere.".csv";
				$nom_fic=remplace_accents($nom_fic,'all');

				send_file_download_headers('text/x-csv',$nom_fic);

				//echo $csv;
				echo echo_csv_encoded($csv);
				die();

			}
		}
	}

	if($import_csv=='y') {
		check_token();

		$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
		//echo "$sql<br />\n";
		$res=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			$msg="Le groupe hors enseignement n'a pas été identifié???";
		}
		else {
			$lig=mysql_fetch_object($res);
			$id_ex_grp=$lig->id;
	
			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			//echo "$sql<br />\n";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				$msg="Aucune classe n'est associée à l'examen n°$id_exam???\n";
			}
			else {
				$tab_ele_inscrits=array();
				while($lig_class=mysql_fetch_object($res_classes)) {
					$sql="SELECT DISTINCT e.login, e.nom, e.prenom, en.note, en.statut FROM j_eleves_classes jec, eleves e, ex_notes en WHERE jec.id_classe='$lig_class->id_classe' AND jec.login=e.login AND en.login=e.login AND en.id_ex_grp='$id_ex_grp' ORDER BY e.nom, e.prenom;";
					//echo "$sql<br />\n";
					$res_ele=mysql_query($sql);
					$nb_ele=mysql_num_rows($res_ele);
					if($nb_ele>0) {
						while($lig=mysql_fetch_object($res_ele)) {
							$tab_ele_inscrits[]=$lig->login;
						}
					}
				}

				$csv_file=isset($_FILES["csv_file"]) ? $_FILES["csv_file"] : NULL;

				//foreach($csv_file as $key => $value) {
				//	echo "\$csv_file[$key]=$value<br />";
				//}

				if (trim($csv_file['name'])=='') {
					//echo "<p>Aucun fichier n'a été sélectionné !<br />\n";
					//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=import_csv'>Cliquer ici</a> pour recommencer !</center></p>\n";
					$msg="Aucun fichier n'a été sélectionné !<br />\n";
					$mode='import_csv';
				}
				else{
		
					//$fp = dbase_open($csv_file['tmp_name'], 0);
					$fp=fopen($csv_file['tmp_name'],"r");
		
					if(!$fp){
						//echo "<p>Impossible d'ouvrir le fichier CSV !</p>\n";
						//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=import_csv'>Cliquer ici</a> pour recommencer !</center></p>\n";
						$msg="Impossible d'ouvrir le fichier CSV !</p>\n";
						$mode='import_csv';
					}
					else{
		
						// on constitue le tableau des champs à extraire
						$tabchamps=array("LOGIN","NOTE");
		
						$ligne=fgets($fp, 4096);
						$temp=explode(";",$ligne);
						for($i=0;$i<sizeof($temp);$i++){
							$en_tete[$i]=preg_replace('/"/','',$temp[$i]);
						}
						$nbchamps=sizeof($en_tete);
						fclose($fp);
		
						// On range dans tabindice les indices des champs retenus
						$temoin=0;
						for($k=0;$k<count($tabchamps);$k++){
							for($i=0;$i<count($en_tete);$i++){
								if(trim($en_tete[$i])==$tabchamps[$k]){
									$tabindice[$k]=$i;
									//echo "\$tabindice[$k]=$tabindice[$k]<br />";
									$temoin++;
								}
							}
						}
		
						if($temoin!=count($tabchamps)){
							//echo "<p><b>ERREUR:</b> La ligne d'entête du fichier n'est pas conforme à ce qui est attendu.</p>\n";
							//echo "<p><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=import_csv'>Cliquer ici</a> pour recommencer !</center></p>\n";
							//require("../lib/footer.inc.php");
							//die();

							$msg="<b>ERREUR:</b> La ligne d'entête du fichier n'est pas conforme à ce qui est attendu.<br />\n";
							$mode='import_csv';
						}

						$msg="";

						$fp=fopen($csv_file['tmp_name'],"r");
						// On lit une ligne pour passer la ligne d'entête:
						$ligne = fgets($fp, 4096);
						//=========================
						unset($tab_dev);
						$tab_dev=array();
						$cpt_ele=0;
						$info_erreur="";
		
						while(!feof($fp)){
							$ligne = fgets($fp, 4096);
							if(trim($ligne)!=""){
								$ligne=trim($ligne);
								//echo "<p>ligne=$ligne<br />\n";
								$tabligne=explode(";",preg_replace('/"/','',$ligne));

								$current_ele_login=$tabligne[$tabindice[0]];
								if(!in_array($current_ele_login,$tab_ele_inscrits)) {
									$msg.="L'élève ".$current_ele_login." n'est pas associé à ce groupe hors enseignement.<br />";
								}
								else {
									$current_note=$tabligne[$tabindice[1]];

									$elev_note='';
									$elev_statut='';
									if(($current_note=='disp')){
										$elev_note='0';
										$elev_statut='disp';
									}
									elseif(($current_note=='abs')){
										$elev_note='0';
										$elev_statut='abs';
									}
									elseif(($current_note=='-')){
										$elev_note='0';
										$elev_statut='-';
									}
									elseif(preg_match("/^[0-9\.\,]{1,}$/",$current_note)) {
										$elev_note=str_replace(",", ".", "$current_note");
										if(($elev_note<0)||($elev_note>20)){
											$elev_note='';
											//$elev_statut='';
											$elev_statut='v';
										}
									}
									else{
										$elev_note='';
										//$elev_statut='';
										$elev_statut='v';
									}

									//echo "\$current_ele_login=$current_ele_login<br />";
									//echo "\$elev_note=$elev_note<br />";
									//echo "\$elev_statut=$elev_statut<br />";
									if(($elev_note!='')or($elev_statut!='')){
										$sql="UPDATE ex_notes SET note='$elev_note', statut='$elev_statut' WHERE id_ex_grp='$id_ex_grp' AND login='$current_ele_login';";
										//echo "$sql<br />\n";
										$res=mysql_query($sql);
										if(!$res) {
											$msg.="Erreur: $sql<br />";
										}
									}
								}
							}
						}

					}

				}
			}
		}
	}

}




//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Examen blanc: Saisie";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Examens blancs</a>";
echo " | <a href='index.php?id_exam=$id_exam&amp;mode=modif_exam'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Examen n°$id_exam</a>";
//echo "</p>\n";
//echo "</div>\n";

if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($acces_mod_exb_prof=='y')) {

	if(($id_groupe!=NULL)&&($matiere!=NULL)) {

		$sql="SELECT id FROM ex_groupes WHERE id_exam='$id_exam' AND id_groupe='0' AND matiere='$matiere' AND type='hors_enseignement';";
		$res=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			echo "</p>\n";

			echo "<p>ERREUR&nbsp;: Le devoir n'existe pas.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		$lig=mysql_fetch_object($res);
		$id_ex_grp=$lig->id;

		$sql="SELECT 1=1 FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {$mode='choix_eleves';}

		if(!isset($mode)) {
			echo "</p>\n";

			echo "<p class='bold'>Groupe Hors Enseignement en $matiere pour l'examen n°$id_exam</p>";

			echo "<p>Saisie de notes pour un devoir hors enseignements.</p>\n";

			echo "<p>Effectuez votre choix&nbsp;:</p>\n";
			echo "<ul>\n";
			echo "<li><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=choix_eleves'>Choisir les élèves</a></li>\n";
			echo "<li><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=saisie_notes'>Saisir les notes</a></li>\n";
			echo "<li><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;export_csv=y'>Exporter les notes saisies</a></li>\n";
			echo "<li><a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=import_csv'>Importer des notes depuis un CSV</a></li>\n";
			echo "<li>A FAIRE&nbsp;: Permettre de nommer ces notes hors enseignement pour pouvoir les re-sélectionner la note par la suite pour un autre examen.</li>\n";
			echo "<li>A FAIRE&nbsp;: Permettre de choisir un enseignement/période dans les Années antérieures si le module est actif.</li>\n";
			echo "</ul>\n";
			require("../lib/footer.inc.php");
			die();
		}
		elseif($mode=='choix_eleves') {
			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				echo "</p>\n";

				echo "<p>Aucune classe n'est associée à l'examen???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}


			$sql="SELECT login FROM ex_notes WHERE id_ex_grp='$id_ex_grp';";
			//echo "$sql<br />\n";
			$res=mysql_query($sql);
			$tab_ele_inscrits=array();
			if(mysql_num_rows($res)>0) {
				while($lig=mysql_fetch_object($res)) {
					$tab_ele_inscrits[]=$lig->login;
				}

				echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=saisie_notes'";
				echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
				echo ">Saisir les notes</a>";
			}
			echo "</p>\n";

			echo "<p class='bold'>Groupe Hors Enseignement en $matiere pour l'examen n°$id_exam</p>";

			echo "<p>Saisie de notes pour un devoir hors enseignements.</p>\n";

			echo "<p><a href='javascript:cocher_tous_eleves()'>Cocher</a> / <a href='javascript:decocher_tous_eleves()'>décocher</a> tous les élèves de toutes les classes associées à l'examen.</p>\n";

			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();
			echo "<p align='center'><input type='submit' name='valider0' value='Valider' /></p>\n";

			$max_eff_classe=0;
			//$cpt=0;
			$cpt_classe=0;

			$nb_classes_par_colonne=round($nb_classes/3);
			echo "<table width='100%' summary='Tableau des classes'>\n";
			echo "<tr valign='top' align='center'>\n";
			echo "<td align='left'>\n";

			while($lig_class=mysql_fetch_object($res_classes)) {

				if(($cpt_classe>0)&&(round($cpt_classe/$nb_classes_par_colonne)==$cpt_classe/$nb_classes_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<p class='bold'>Classe $lig_class->classe</p>\n";

				$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM j_eleves_classes jec, eleves e WHERE jec.id_classe='$lig_class->id_classe' AND jec.login=e.login ORDER BY e.nom, e.prenom;";
				$res_ele=mysql_query($sql);
				$nb_ele=mysql_num_rows($res_ele);
				if($nb_ele==0) {
					echo "<p>Aucun élève dans cette classe???</p>\n";
				}
				else {
					if($max_eff_classe<$nb_ele) {$max_eff_classe=$nb_ele;}

					$alt=1;
					echo "<table class='boireaus' border='1' summary='Elèves de $lig_class->classe'>\n";
					echo "<tr>\n";
					echo "<th>Elève</th>\n";

					echo "<th>\n";
					echo "<a href=\"javascript:cocher_decocher($cpt_classe,true);changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a> / <a href=\"javascript:cocher_decocher($cpt_classe,false);changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
					echo "</th>\n";

					echo "</tr>\n";
					$cpt=0;
					while($lig_ele=mysql_fetch_object($res_ele)) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td style='text-align:left;'>\n";
						echo "<label for='login_ele_".$cpt_classe."_$cpt'> ".casse_mot($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</label>\n";
						echo "</td>\n";

						echo "<td>\n";
						echo "<input type='checkbox' name='login_ele[]' id='login_ele_".$cpt_classe."_$cpt' value='$lig_ele->login' onchange='changement()' ";
						if(in_array($lig_ele->login,$tab_ele_inscrits)) {echo "checked ";}
						echo "/>\n";
						echo "</td>\n";

						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
				}
				$cpt_classe++;
			}
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";

			echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
			echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
			echo "<input type='hidden' name='id_ex_grp' value='$id_ex_grp' />\n";
			echo "<input type='hidden' name='matiere' value='$matiere' />\n";
			echo "<input type='hidden' name='mode' value='choix_eleves' />\n";
			echo "<input type='hidden' name='reg_eleves' value='y' />\n";
			echo "<p align='center'><input type='submit' name='valider' value='Valider' /></p>\n";
			echo "</form>\n";

			echo "<script type='text/javascript'>
function cocher_decocher(i,mode) {
	for (var k=0;k<$max_eff_classe;k++) {
		if(document.getElementById('login_ele_'+i+'_'+k)){
			document.getElementById('login_ele_'+i+'_'+k).checked=mode;
		}
	}
}

function cocher_tous_eleves() {
";
			for($i=0;$i<$cpt_classe;$i++) {
				echo "cocher_decocher($i,true);\n";
			}
		
			echo "}
function decocher_tous_eleves() {
";
			for($i=0;$i<$cpt_classe;$i++) {
				echo "cocher_decocher($i,false);\n";
			}
			echo "}
</script>\n";

			require("../lib/footer.inc.php");
			die();
		}
		elseif($mode=='saisie_notes') {

			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=choix_eleves'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Sélectionner les élèves</a>";
	
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=saisie_notes&amp;export_csv=y'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Export CSV</a>";
	
			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=import_csv'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Import CSV</a>";
	
			echo "</p>\n";

			echo "<p class='bold'>Groupe Hors Enseignement en $matiere pour l'examen n°$id_exam</p>";

			// Couleurs utilisées
			$couleur_devoirs = '#AAE6AA';
			$couleur_fond = '#AAE6AA';
			$couleur_moy_cn = '#96C8F0';
	
			// PROBLEME AVEC LA PERIODE... ET LES ELEVES QUI CHANGENT DE CLASSE EN COURS D'ANNEE
			//$sql="SELECT DISTINCT e.* FROM j_eleves_groupes jeg, eleves e WHERE jeg.id_groupe='$id_groupe' AND jeg.login=e.login ORDER BY e.nom, e.prenom, e.naissance;";
			//$sql="SELECT DISTINCT e.nom, e.prenom, en.* FROM ex_groupes eg, ex_notes en, eleves e WHERE eg.id_groupe='$id_groupe' AND eg.id=en.id_ex_grp AND en.login=e.login ORDER BY e.nom, e.prenom, e.naissance;";
			$sql="SELECT DISTINCT 1=1 FROM ex_notes en WHERE en.id_ex_grp='$id_ex_grp';";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				echo "<p>Erreur&nbsp;: Aucun élève inscrit.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
	
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
			echo add_token_field();

			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				echo "</p>\n";
	
				echo "<p>Aucune classe n'est associée à l'examen???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			$cpt=0;
			while($lig_class=mysql_fetch_object($res_classes)) {
				echo "<p class='bold'>Classe $lig_class->classe</p>\n";
				echo "<blockquote>\n";
	
				$sql="SELECT DISTINCT e.login, e.nom, e.prenom, en.note, en.statut FROM j_eleves_classes jec, eleves e, ex_notes en WHERE jec.id_classe='$lig_class->id_classe' AND jec.login=e.login AND en.login=e.login AND en.id_ex_grp='$id_ex_grp' ORDER BY e.nom, e.prenom;";
				$res_ele=mysql_query($sql);
				$nb_ele=mysql_num_rows($res_ele);
				if($nb_ele==0) {
					echo "<p>Aucun élève de cette classe n'est inscrit.</p>\n";
				}
				else {
	
					echo "<table border='1' cellspacing='2' cellpadding='1' class='boireaus' summary='Saisie'>\n";
					echo "<tr>\n";
					echo "<th>Nom Prénom</th>\n";
					//echo "<th>Classe(s)</th>\n";
					echo "<th style='width:5em;'>Note</th>\n";
					echo "</tr>\n";
	
					//$cpt=0;
					$alt=1;
					while($lig=mysql_fetch_object($res_ele)) {
						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td style='text-align:left;'>\n";
						//echo get_nom_prenom_eleve($lig->login)."\n";
						echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
						echo "<input type='hidden' name='login_ele[$cpt]' value='$lig->login' />\n";
						echo "</td>\n";
			
						echo "<td id=\"td_".$cpt."\">\n";
						echo "<input id=\"n".$cpt."\" onKeyDown=\"clavier(this.id,event);\" type=\"text\" size=\"4\" ";
						echo "autocomplete=\"off\" ";
						echo "onfocus=\"javascript:this.select()\" onchange=\"verifcol($cpt);calcul_moy_med();changement()\" ";
						echo "name=\"note[$cpt]\" value='";
						if(($lig->statut=='v')) {echo "";}
						elseif($lig->statut!='') {echo "$lig->statut";}
						else {echo "$lig->note";}
						echo "' />\n";
						echo "</td>\n";
						echo "</tr>\n";
						$cpt++;
					}
					echo "</table>\n";
				}
				echo "</blockquote>\n";
	
			}
	
			echo "<div style='position: fixed; top: 200px; right: 200px;'>\n";
			javascript_tab_stat('tab_stat_',$cpt);
			echo "</div>\n";
	
			echo "<input type='hidden' name='id_exam' value='$id_exam' />\n";
			echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
			echo "<input type='hidden' name='id_ex_grp' value='$id_ex_grp' />\n";
			echo "<input type='hidden' name='matiere' value='$matiere' />\n";
			echo "<input type='hidden' name='mode' value='saisie_notes' />\n";
			echo "<input type='hidden' name='reg_notes' value='y' />\n";
	
			echo "<p><input type='submit' name='enregistrer' value='Enregistrer' /></p>\n";
			echo "</form>\n";
		
			echo "
<script type='text/javascript' language='JavaScript'>

function verifcol(num_id){
	document.getElementById('n'+num_id).value=document.getElementById('n'+num_id).value.toLowerCase();
	if(document.getElementById('n'+num_id).value=='a'){
		document.getElementById('n'+num_id).value='abs';
	}
	if(document.getElementById('n'+num_id).value=='d'){
		document.getElementById('n'+num_id).value='disp';
	}
	if(document.getElementById('n'+num_id).value=='n'){
		document.getElementById('n'+num_id).value='-';
	}
	note=document.getElementById('n'+num_id).value;

	if((note!='-')&&(note!='disp')&&(note!='abs')&&(note!='')){
		//if((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0))){
		if(((note.search(/^[0-9.]+$/)!=-1)&&(note.lastIndexOf('.')==note.indexOf('.',0)))||
	((note.search(/^[0-9,]+$/)!=-1)&&(note.lastIndexOf(',')==note.indexOf(',',0)))){
			if((note>20)||(note<0)){
				couleur='red';
			}
			else{
				couleur='$couleur_devoirs';
			}
		}
		else{
			couleur='red';
		}
	}
	else{
		couleur='$couleur_devoirs';
	}
	eval('document.getElementById(\'td_'+num_id+'\').style.background=couleur');
}
</script>
";

		}
		elseif($mode=='import_csv') {

			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=choix_eleves'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Sélectionner les élèves</a>";

			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=saisie_notes'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Saisie des notes</a>";

			echo " | <a href='".$_SERVER['PHP_SELF']."?id_exam=$id_exam&amp;id_groupe=0&amp;matiere=$matiere&amp;mode=saisie_notes&amp;export_csv=y'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo ">Export CSV</a>";

			echo "</p>\n";

			echo "<p class='bold'>Groupe Hors Enseignement en $matiere pour l'examen n°$id_exam</p>";

			$sql="SELECT DISTINCT 1=1 FROM ex_notes en WHERE en.id_ex_grp='$id_ex_grp';";
			//echo "$sql<br />\n";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0) {
				echo "<p>Erreur&nbsp;: Aucun élève inscrit.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
	

			$sql="SELECT c.classe, ec.id_classe FROM classes c, ex_classes ec WHERE ec.id_exam='$id_exam' AND c.id=ec.id_classe ORDER BY c.classe;";
			$res_classes=mysql_query($sql);
			$nb_classes=mysql_num_rows($res_classes);
			if($nb_classes==0) {
				echo "</p>\n";
	
				echo "<p>Aucune classe n'est associée à l'examen???</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p>Pour importer les notes, vous devez fournir un fichier correctement formaté...</p>";
			echo "<p>Veuillez préciser le nom complet du fichier <b>CSV</b> à importer.";

			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";
			echo add_token_field();

			echo "<p><input type=\"file\" size=\"80\" name=\"csv_file\" /></p>\n";

			echo "<input type='hidden' name='is_posted' value='yes' />\n";

			echo "<input type=\"hidden\" name=\"id_exam\" value=\"$id_exam\" />\n";
			echo "<input type=\"hidden\" name=\"id_ex_grp\" value=\"$id_ex_grp\" />\n";
			echo "<input type=\"hidden\" name=\"id_groupe\" value=\"0\" />\n";
			echo "<input type=\"hidden\" name=\"matiere\" value=\"$matiere\" />\n";

			echo "<input type=\"hidden\" name=\"import_csv\" value=\"y\" />\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"saisie_notes\" />\n";


			echo "<p><input type=submit value='Valider' /></p>\n";
			echo "</form>\n";
		
			echo "<p><br /</p>\n";
			echo "<p><i>NOTE&nbsp;</i>: Le fichier CSV nécessite une une ligne d'entête pour repérer les champs.<br />\n";
			echo "Les deux champs suivants sont requis&nbsp;:<br />\n";
			echo "LOGIN;NOTE\n";
			echo "Les autres champs éventuellement présents ne seront pas pris en compte.</p>\n";

		}

	}
	else {
		echo "<p style='color:red;'>Paramètres incorrects.</p>";
	}
}

echo "<p><br /></p>\n";

echo "<p><i>Remarque&nbsp;:</i> Si un élève est inscrit pour une même matière à la fois avec un devoir (<i>ou une moyenne de bulletin</i>) et avec une note hors enseignement, c'est la note hors enseignement qui est prise en compte.</p>\n";
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>

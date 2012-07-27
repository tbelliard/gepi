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

// Initialisations files
require_once("../lib/initialisations.inc.php");
include("../lib/initialisationsPropel.inc.php");
require_once("./fonctions_annees_anterieures.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();};

// INSERT INTO droits VALUES ('/mod_annees_anterieures/conservation_annee_anterieure.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'Conservation des données antérieures', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}



/*
$prof=isset($_POST['prof']) ? $_POST['prof'] : NULL;
$page=isset($_POST['page']) ? $_POST['page'] : NULL;
$enregistrer=isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
*/

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : NULL;
$deja_traitee_id_classe=isset($_POST['deja_traitee_id_classe']) ? $_POST['deja_traitee_id_classe'] : NULL;
$annee_scolaire=isset($_POST['annee_scolaire']) ? $_POST['annee_scolaire'] : NULL;
$confirmer=isset($_POST['confirmer']) ? $_POST['confirmer'] : NULL;

// Si le module n'est pas activé...
if($gepiSettings['active_annees_anterieures'] !="y"){
	// A DEGAGER
	// A VOIR: Comment enregistrer une tentative d'accès illicite?

	header("Location: ../logout.php?auto=1");
	die();
}

$msg="";
/*
if(isset($enregistrer)){

	if($msg==""){
		$msg="Enregistrement réussi.";
	}

	unset($page);
}
*/

// Suppression des données archivées pour une année donnée.
if (isset($_GET['action']) and ($_GET['action']=="supp_annee")) {
	check_token();

	$sql="DELETE FROM archivage_disciplines WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr1=mysql_query($sql);

	// Maintenant, on regarde si l'année est encore utilisée dans archivage_types_aid
	// Sinon, on supprime les entrées correspondantes à l'année dans archivage_eleves2 car elles ne servent plus à rien.
	$test = sql_query1("select count(annee) from archivage_types_aid where annee='".$_GET['annee_supp']."'");
	if ($test == 0) {
		$sql="DELETE FROM archivage_eleves2 WHERE annee='".$_GET["annee_supp"]."';";
		$res_suppr2=mysql_query($sql);
	} else {
		$res_suppr2 = 1;
	}

	$sql="DELETE FROM archivage_ects WHERE annee='".$_GET["annee_supp"]."';";
	$res_suppr3=mysql_query($sql);

	// Maintenant, il faut supprimer les données élèves qui ne servent plus à rien
	suppression_donnees_eleves_inutiles();

	if (($res_suppr1) and ($res_suppr2) and ($res_suppr3)) {
		$msg = "La suppression des données a été correctement effectuée.";
	} else {
		$msg = "Un ou plusieurs problèmes ont été rencontrés lors de la suppression.";
	}

}

if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

$themessage  = 'Etes-vous sûr de vouloir supprimer toutes les données concerant cette année ?';

//**************** EN-TETE *****************
$titre_page = "Conservation des données antérieures (autres que AID)";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

if(!isset($annee_scolaire)){
	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";
	echo "</p></div>\n";

	$sql="SELECT DISTINCT annee FROM archivage_disciplines ORDER BY annee";
	$res_annee=mysql_query($sql);
	//if(){
	if(mysql_num_rows($res_annee)==0){
		echo "<p>Concernant les données autres que les AIDs, aucune année n'est encore sauvegardée.</p>\n";
	}
	else{
		echo "<p>Voici la liste des années sauvegardées:</p>\n";
		echo "<ul>\n";
		while($lig_annee=mysql_fetch_object($res_annee)){
			$annee_scolaire=$lig_annee->annee;
			echo "<li><b>Année $annee_scolaire (<a href='".$_SERVER['PHP_SELF']."?action=supp_annee&amp;annee_supp=".$annee_scolaire.add_token_in_url()."'   onclick=\"return confirm_abandon (this, 'yes', '$themessage')\">Supprimer toutes les données archivées pour cette année</a>) :<br /></b> ";
			$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire' ORDER BY classe;";
			$res_classes=mysql_query($sql);
			if(mysql_num_rows($res_classes)==0){
				echo "Aucune classe???";
			}
			else{
				$lig_classe=mysql_fetch_object($res_classes);
				echo $lig_classe->classe;

				while($lig_classe=mysql_fetch_object($res_classes)){
					echo ", ".$lig_classe->classe;
				}
			}
			echo "</li>\n";
		}
		echo "</ul>\n";
		echo "<p><br /></p>\n";

	}
	echo "<p>Sous quel nom d'année voulez-vous sauvegarder l'année?</p>\n";
	$default_annee=getSettingValue('gepiYear');

	if($default_annee==""){
		$instant=getdate();
		$annee=$instant['year'];
		$mois=$instant['mon'];

		$annee2=$annee+1;
		$default_annee=$annee."-".$annee2;
	}

	echo "<p>Année&nbsp;: <input type='text' name='annee_scolaire' value='$default_annee' /></p>\n";

	echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

}
else{
	echo "<div class='norme'><p class=bold><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "../gestion/changement_d_annee.php";
	}
	else {
		echo "./index.php";
	}
	echo "'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";

	$sql="SELECT DISTINCT classe FROM archivage_disciplines WHERE annee='$annee_scolaire'";
	$res_test=mysql_query($sql);

	if(mysql_num_rows($res_test)>0){
		if(!isset($confirmer)){
			echo "</p></div>\n";

			$lig_classe=mysql_fetch_object($res_test);
			$chaine_classes=$lig_classe->classe;

			if(mysql_num_rows($res_test)>1){
				while($lig_classe=mysql_fetch_object($res_test)){
					$chaine_classes.=", ".$lig_classe->classe;
				}

				echo "<p>Des données ont déjà été sauvegardées pour l'année $annee_scolaire (<i>classes de $chaine_classes</i>).<br />Si vous confirmez, ces données seront écrasées avec les nouvelles données (<i>si vous ne cochez pas les mêmes classes, les données seront seulement complétées</i>).</p>\n";
			}
			else{
				echo "<p>Des données ont déjà été sauvegardées pour l'année $annee_scolaire (<i>classe de $chaine_classes</i>).<br />Si vous confirmez, ces données seront écrasées avec les nouvelles données (<i>si vous ne cochez pas les mêmes classes, les données seront seulement complétées</i>).</p>\n";
			}

			echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";

			echo "<center><input type=\"submit\" name='confirmer' value=\"Confirmer\" style=\"font-variant: small-caps;\" /></center>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}

	if(!isset($id_classe)) {
		echo "</p></div>\n";

		echo "<h2>Choix des classes</h2>\n";

		echo "<p>Conservation des données pour l'année scolaire: $annee_scolaire</p>\n";

		echo "<p>Choisissez les classes dont vous souhaitez archiver les résultats, appréciations,...</p>";
		echo "<p>Tout <a href='javascript:modif_coche(true)'>cocher</a> / <a href='javascript:modif_coche(false)'>décocher</a>.</p>";


		// Afficher les classes pour lesquelles les données sont déjà migrées...

		$sql="SELECT id,classe FROM classes ORDER BY classe";
		$res1=mysql_query($sql);
		$nb_classes=mysql_num_rows($res1);
		if($nb_classes==0){
			echo "<p>ERREUR: Il semble qu'aucune classe ne soit encore définie.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		// Affichage sur 3 colonnes
		$nb_classes_par_colonne=round($nb_classes/3);

		echo "<table width='100%' summary='Choix des classes'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = 0;

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb_classes) {

			if(($i>0)&&(round($i/$nb_classes_par_colonne)==$i/$nb_classes_par_colonne)){
				echo "</td>\n";
				echo "<td align='left'>\n";
			}

			$lig_classe=mysql_fetch_object($res1);

			echo "<input type='checkbox' id='classe".$i."' name='id_classe[]' value='$lig_classe->id' /><label for='classe".$i."' style='cursor:pointer;'> $lig_classe->classe</label><br />\n";

			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<script type='text/javascript'>
			function modif_coche(statut){
				for(k=0;k<$i;k++){
					if(document.getElementById('classe'+k)){
						document.getElementById('classe'+k).checked=statut;
					}
				}
				//changement();
			}
		</script>\n";

		echo add_token_field();

		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

	}
	else {
		echo "<a href='".$_SERVER['PHP_SELF']."'>Choisir d'autres classes</a> | ";
		echo "</div>\n";

		if(count($id_classe)==0){
			echo "<p>ERREUR: Vous n'avez pas coché de classe.</p>\n";
			echo "</form>\n";
			require("../lib/footer.inc.php");
			die();
		}

		check_token(false);

		/*
		echo "<p>Mise à jour du calcul du rang des élèves dans les matières...</p>\n";
		include "../lib/periodes.inc.php";
		include("../lib/calcul_rang.inc.php");
		*/


		$temoin_ects="n";
		$sql="SELECT 1=1 FROM ects_credits LIMIT 1";
		$test1=mysql_query($sql);
		if(mysql_num_rows($test1)>0) {$temoin_ects="y";}
		else {
			$sql="SELECT 1=1 FROM ects_global_credits LIMIT 1";
			$test2=mysql_query($sql);
			if(mysql_num_rows($test2)>0) {$temoin_ects="y";}
		}


		//===================================

		if(isset($deja_traitee_id_classe)){
			echo "<p>Classes déjà traitées: ";

			echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$deja_traitee_id_classe[0]' />";
			echo get_nom_classe($deja_traitee_id_classe[0]);

			for($i=1;$i<count($deja_traitee_id_classe);$i++){
				echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$deja_traitee_id_classe[$i]' />";
				echo ", ".get_nom_classe($deja_traitee_id_classe[$i]);
			}
			echo "</p>\n";
		}

		$temoin_encore_des_classes=0;
		//$chaine="";
		$chaine=get_nom_classe($id_classe[0]);
		for($i=1;$i<count($id_classe);$i++){
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$temoin_encore_des_classes++;

			$chaine.=", ".get_nom_classe($id_classe[$i]);
		}
		//if($chaine!=""){
		if($temoin_encore_des_classes>0) {
			// Pour faire sauter un "' ":
			//echo "<p>Classes restant à traiter: ".mb_substr($chaine,2)."</p>\n";
			echo "<p>Classes restant à traiter: ".$chaine."</p>\n";
		}
		else {
			echo "<p>Traitement de la dernière classe sélectionnée: <span id='annonce_fin_traitement' style='font-weight:bold; font-size:2em; color: green;'></span></p>\n";
		}
		//===================================



		$classe=get_nom_classe($id_classe[0]);

		echo "<p><b>Classe de $classe:</b></p>\n";

		//echo "<p>Classe de $classe</p>\n";

		// Boucle sur les périodes de la classe
		$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe[0]."' ORDER BY num_periode";
		$res_periode=mysql_query($sql);

		if(mysql_num_rows($res_periode)==0){
			echo "<p>Aucune période ne semble définie pour la classe $classe</p>\n";
		}
		else{
			unset($tab_periode);
			$tab_periode=array();
			//$cpt=0;
			$cpt=1;
			while($lig_periode=mysql_fetch_object($res_periode)){
				$tab_periode[$cpt]=$lig_periode->nom_periode;
				$cpt++;
			}



			$sql="SELECT DISTINCT e.* FROM eleves e,j_eleves_classes jec WHERE id_classe='".$id_classe[0]."' AND jec.login=e.login ORDER BY login";
			//echo "$sql<br />\n";
			$res_ele=mysql_query($sql);

			if(mysql_num_rows($res_ele)==0){
				echo "<p>Aucun élève dans la classe $classe???</p>\n";
			}
			else{
				unset($tab_eleve);
				$tab_eleve=array();
				$cpt=0;
				while($lig_ele=mysql_fetch_object($res_ele)){
					// Infos élève
					$tab_eleve[$cpt]=array();

					$tab_eleve[$cpt]['nom']=$lig_ele->nom;
					$tab_eleve[$cpt]['prenom']=$lig_ele->prenom;
					$tab_eleve[$cpt]['naissance']=$lig_ele->naissance;
					$tab_eleve[$cpt]['naissance2']=formate_date($lig_ele->naissance);
					$tab_eleve[$cpt]['login_eleve']=$lig_ele->login;

					$tab_eleve[$cpt]['ine']=$lig_ele->no_gep;

					if($tab_eleve[$cpt]['ine']==""){
						$tab_eleve[$cpt]['ine']="LOGIN_".$tab_eleve[$cpt]['login_eleve'];
						$tab_eleve[$cpt]['ine'] = cree_substitut_INE_unique($tab_eleve[$cpt]['ine']);
					}

					// On vérifie que l'élève est enregistré dans archive_eleves. Sinon, on l'enregistre
			
					$error_enregistre_eleve[$tab_eleve[$cpt]['login_eleve']] = insert_eleve($tab_eleve[$cpt]['login_eleve'],$tab_eleve[$cpt]['ine'],$annee_scolaire,'y');
					//echo "insert_eleve(\$tab_eleve[$cpt]['login_eleve'],\$tab_eleve[$cpt]['ine'],$annee_scolaire,'y') soit insert_eleve(".$tab_eleve[$cpt]['login_eleve'].",".$tab_eleve[$cpt]['ine'].",$annee_scolaire,'y')<br />";

					// Statut de redoublant ou non:
					$sql="SELECT * FROM j_eleves_regime WHERE login='".$tab_eleve[$cpt]['login_eleve']."'";
					$res_red=mysql_query($sql);

					if(mysql_num_rows($res_red)==0){
						$tab_eleve[$cpt]['doublant']="-";
					}
					else{
						$lig_red=mysql_fetch_object($res_red);
						$tab_eleve[$cpt]['doublant']=$lig_red->doublant;
					}


					// CPE associé(s) à l'élève
					$sql="SELECT jec.cpe_login FROM j_eleves_cpe jec WHERE jec.e_login='".$tab_eleve[$cpt]['login_eleve']."';";
					$res_cpe=mysql_query($sql);

					if(mysql_num_rows($res_cpe)==0){
						$tab_eleve[$cpt]['cpe']="";
					}
					else{
						$lig_cpe=mysql_fetch_object($res_cpe);
						$tab_eleve[$cpt]['cpe']=affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);

						while($lig_cpe=mysql_fetch_object($res_cpe)){
							$tab_eleve[$cpt]['cpe'].=", ".affiche_utilisateur($lig_cpe->cpe_login,$id_classe[0]);
						}
					}



					$cpt++;
				}



				// Personne assurant le suivi de la classe...
				$sql="SELECT suivi_par FROM classes WHERE id='$id_classe[0]'";
				$res_suivi=mysql_query($sql);
				if(mysql_num_rows($res_suivi)==0){
					$suivi_par="-";
				}
				else{
					$lig_suivi=mysql_fetch_object($res_suivi);
					$suivi_par=$lig_suivi->suivi_par;
				}



				echo "<table class='boireaus' border='1' summary='Tableau des élèves'>\n";

				// Boucle sur les périodes
				echo "<tr>\n";
				echo "<th>Élève</th>\n";
				//for($i=0;$i<count($tab_periode);$i++){
				for($i=1;$i<=count($tab_periode);$i++){
					echo "<th>$tab_periode[$i]</th>\n";
				}
				echo "</tr>\n";

				// Boucle sur les élèves
				$alt=1;
				for($j=0;$j<count($tab_eleve);$j++){
					$alt=$alt*(-1);
					echo "<tr class='lig$alt'>\n";
					echo "<td id='td_0_".$j."'>".$tab_eleve[$j]['nom']." ".$tab_eleve[$j]['prenom']."</td>\n";
					//for($i=0;$i<count($tab_periode);$i++){
					for($i=1;$i<=count($tab_periode);$i++){
						echo "<td id='td_".$i."_".$j."'>&nbsp;</td>\n";
					}
					echo "</tr>\n";
				}

				echo "</table>\n";


				// Début du traitement

				for($i=1;$i<=count($tab_periode);$i++){
					// Nettoyage:
					$sql="DELETE FROM archivage_disciplines WHERE annee='$annee_scolaire' AND classe='$classe' AND num_periode='$i'";
					$res_nettoyage=mysql_query($sql);

					if(!$res_nettoyage){
						echo "<p style='color:red'><b>ERREUR</b> lors du nettoyage</p>\n";
						echo "</form>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$erreur=0;

					$num_periode=$i;
					$nom_periode=$tab_periode[$i];


					// Calculer les moyennes de classe, rechercher min et max pour tous les groupes associés à la classe sur la période.
					//$sql="SELECT DISTINCT id_groupe FROM j_groupes_classes WHERE id_classe='".$id_classe[0]."'";
					$sql="SELECT DISTINCT id_groupe, priorite FROM j_groupes_classes WHERE id_classe='".$id_classe[0]."'";
					$res_groupes=mysql_query($sql);

					$moymin=array();
					$moymax=array();
					$moyclasse=array();
					$ordre_matiere=array();

					if(mysql_num_rows($res_groupes)==0){
						// Dans ce cas, il ne doit pas y avoir de note,... pour les élèves
					}
					else{
						while($lig_groupes=mysql_fetch_object($res_groupes)){
							$id_groupe=$lig_groupes->id_groupe;

							$ordre_matiere[$id_groupe]=$lig_groupes->priorite;

							$sql="SELECT AVG(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							//echo "$sql<br />\n";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moyclasse[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moyclasse[$id_groupe]=round($lig_moy->moyenne*10)/10;
							}

							$sql="SELECT MAX(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moymax[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moymax[$id_groupe]=$lig_moy->moyenne;
							}

							$sql="SELECT MIN(note) moyenne FROM matieres_notes WHERE id_groupe='$id_groupe' AND statut='' AND periode='$i'";
							$res_moy=mysql_query($sql);
							if(mysql_num_rows($res_moy)==0){
								$moymin[$id_groupe]="-";
							}
							else{
								$lig_moy=mysql_fetch_object($res_moy);
								$moymin[$id_groupe]=$lig_moy->moyenne;
							}
						}
					}


					// Boucle sur les élèves
					for($j=0;$j<count($tab_eleve);$j++){
						$ine=$tab_eleve[$j]['ine'];
						$nom=$tab_eleve[$j]['nom'];
						$prenom=$tab_eleve[$j]['prenom'];
						$naissance=$tab_eleve[$j]['naissance'];
						$naissance2=$tab_eleve[$j]['naissance2'];
						$login_eleve=$tab_eleve[$j]['login_eleve'];
						$doublant=$tab_eleve[$j]['doublant'];
						$cpe=$tab_eleve[$j]['cpe'];
						if ($error_enregistre_eleve[$login_eleve] != '') {
							echo "<script type='text/javascript'>
  	document.getElementById('td_0_".$j."').style.backgroundColor='red';
</script>\n";
						}




						// Absences, retards,... de l'élève
						$sql="SELECT * FROM absences WHERE login='".$login_eleve."' AND periode='$i'";
						$res_abs=mysql_query($sql);

						if(mysql_num_rows($res_abs)==0){
							$nb_absences="-";
							$non_justifie="-";
							$nb_retards="-";
							$appreciation="-";
						}
						else{
							$lig_abs=mysql_fetch_object($res_abs);
							$nb_absences=$lig_abs->nb_absences;
							$non_justifie=$lig_abs->non_justifie;
							$nb_retards=$lig_abs->nb_retards;
							$appreciation=$lig_abs->appreciation;
						}

						$sql="INSERT INTO archivage_disciplines SET
											annee='$annee_scolaire',
											ine='$ine',
											classe='".addslashes($classe)."',
											num_periode='$num_periode',
											nom_periode='".addslashes($nom_periode)."',
											special='ABSENCES',
											matiere='',
											prof='".addslashes($cpe)."',
											note='',
											moymin='',
											moymax='',
											moyclasse='',
											appreciation='".addslashes($appreciation)."',
											nb_absences='$nb_absences',
											non_justifie='$non_justifie',
											nb_retards='$nb_retards'
											";
						echo "<!-- $sql -->\n";
						$res_insert=mysql_query($sql);

						if(!$res_insert){
							$erreur++;

							//echo "<span style='color:red'>$sql</span><br />";

							echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
						}




						// Avis du conseil de classe
						$sql="SELECT * FROM avis_conseil_classe WHERE login='$login_eleve' AND periode='$num_periode'";
						$res_avis=mysql_query($sql);

						if(mysql_num_rows($res_avis)==0){
							$avis="-";
						}
						else{
							$lig_avis=mysql_fetch_object($res_avis);
							$avis=$lig_avis->avis;
							// A quoi sert le champ statut de la table avis_conseil_classe ?
						}

						// Insertion de l'avis dans archivage_disciplines
						$sql="INSERT INTO archivage_disciplines SET
											annee='$annee_scolaire',
											ine='$ine',
											classe='".addslashes($classe)."',
											num_periode='$num_periode',
											nom_periode='".addslashes($nom_periode)."',
											special='AVIS_CONSEIL',
											matiere='',
											prof='".addslashes($suivi_par)."',
											note='',
											moymin='',
											moymax='',
											moyclasse='',
											appreciation='".addslashes($avis)."',
											nb_absences='',
											non_justifie='',
											nb_retards=''
											";
						echo "<!-- $sql -->\n";
						$res_insert=mysql_query($sql);

						if(!$res_insert){
							$erreur++;

							//echo "<span style='color:red'>$sql</span><br />";

							echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
						}




						// Boucle sur les matières de l'élève
						/*
						$sql="SELECT mn.*,g.description FROM groupes g,matieres_notes mn
														WHERE login='$login_eleve' AND
																periode='$num_periode'";
						*/
						/*
						$sql="SELECT mn.*,m.nom_complet FROM j_groupes_matieres jgm,matieres m,matieres_notes mn
														WHERE mn.login='$login_eleve' AND
																mn.periode='$num_periode' AND
																jgm.id_groupe=mn.id_groupe AND
																jgm.id_matiere=m.matiere;";
						*/
						$sql="SELECT jeg.id_groupe, m.nom_complet FROM j_groupes_matieres jgm,matieres m,j_eleves_groupes jeg
														WHERE jeg.login='$login_eleve' AND
																jeg.periode='$num_periode' AND
																jgm.id_groupe=jeg.id_groupe AND
																jgm.id_matiere=m.matiere;";

						echo "<!-- $sql -->\n";
						$res_grp=mysql_query($sql);

						if(mysql_num_rows($res_grp)==0){
							// Que faire? Est-il possible qu'il y ait quelque chose dans matieres_appreciations dans ce cas?
							// Ca ne devrait pas...
							// Si... on peut avoir un professeur qui n'a pas saisi de note ni même un tiret (malheureusement), mais mis une appréciation
							//echo "<!-- Aucune note sur le bulletin de période $num_periode pour l'élève $login_eleve -->\n";

							echo "<!-- En période $num_periode, l'élève $login_eleve n'est associé à aucun enseignement -->\n";
						}
						else{
							while($lig_grp=mysql_fetch_object($res_grp)){

								$id_groupe=$lig_grp->id_groupe;
								$matiere=$lig_grp->nom_complet;

								$sql="SELECT mn.* FROM matieres_notes mn
														WHERE mn.login='$login_eleve' AND
																mn.periode='$num_periode' AND
																mn.id_groupe='$id_groupe';";
								$res_note=mysql_query($sql);
								if(mysql_num_rows($res_note)==0) {
									$note='';
									$rang=-1;
								}
								else {
									$lig_note=mysql_fetch_object($res_note);

									if($lig_note->statut!=''){
										$note=$lig_note->statut;
									}
									else{
										$note=$lig_note->note;
									}
									$rang=$lig_note->rang;
								}

								// Récupération de l'appréciation
								$sql="SELECT appreciation FROM matieres_appreciations
														WHERE login='$login_eleve' AND
																periode='$num_periode' AND
																id_groupe='$id_groupe'";
								echo "<!-- $sql -->\n";
								$res_app=mysql_query($sql);

								if(mysql_num_rows($res_app)==0){
									$appreciation="-";
								}
								else{
									$lig_app=mysql_fetch_object($res_app);
									$appreciation=$lig_app->appreciation;
								}

								if(($note!='')||($appreciation!='-')) {
									// Récupération des professeurs associés
									$sql="SELECT login FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' ORDER BY login";
									echo "<!-- $sql -->\n";
									$res_prof=mysql_query($sql);
	
									if(mysql_num_rows($res_prof)==0){
										$prof="";
									}
									else{
										$lig_prof=mysql_fetch_object($res_prof);
										$prof=affiche_utilisateur($lig_prof->login,$id_classe[0]);
										while($lig_prof=mysql_fetch_object($res_prof)){
											$prof.=", ".affiche_utilisateur($lig_prof->login,$id_classe[0]);
										}
									}
	
									// Insertion de la note, l'appréciation,... dans la matière,...
									if (!isset($moymin[$id_groupe])) $moymin[$id_groupe]="-";
									if (!isset($moymax[$id_groupe])) $moymax[$id_groupe]="-";
									if (!isset($moyclasse[$id_groupe])) $moyclasse[$id_groupe]="-";
	
									$sql="INSERT INTO archivage_disciplines SET
														annee='$annee_scolaire',
														ine='$ine',
														classe='".addslashes($classe)."',
														num_periode='$num_periode',
														nom_periode='".addslashes($nom_periode)."',
														matiere='".addslashes($matiere)."',
														special='',
														prof='".addslashes($prof)."',
														note='$note',
														moymin='".$moymin[$id_groupe]."',
														moymax='".$moymax[$id_groupe]."',
														moyclasse='".$moyclasse[$id_groupe]."',
														rang='".$rang."',
														appreciation='".addslashes($appreciation)."',
														nb_absences='',
														non_justifie='',
														nb_retards='',
														ordre_matiere='".$ordre_matiere[$id_groupe]."'
														";
									echo "<!-- $sql -->\n";
									$res_insert=mysql_query($sql);
	
									if(!$res_insert){
										$erreur++;

										//echo "<span style='color:red'>$sql</span><br />";

										echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='red';
</script>\n";
									}
								}

							} // Fin de la boucle matières


							echo "<!-- Avant les crédits ECTS de l'élève $login_eleve -->\n";

							if($temoin_ects=="y") {
								//--------------------
								// Les crédits ECTS
								//--------------------
	
								// On a besoin de : annee, ine, classe, num_periode, nom_periode, matiere, prof, valeur_ects, mention_ects
								// On a déjà pratiquement tout... ça ne va pas être compliqué !
								$Eleve = ElevePeer::retrieveByLOGIN($login_eleve);
								$Groupes = $Eleve->getGroupes($num_periode);
	
								foreach($Groupes as $Groupe) {
									
									$Ects = $Eleve->getEctsCredit($num_periode,$Groupe->getId());
	
									if ($Ects != null) {
										$Archive = new ArchiveEcts();
										$Archive->setAnnee($annee_scolaire);
										$Archive->setIne($ine);
										$Archive->setClasse($classe);
										$Archive->setNumPeriode($num_periode);
										$Archive->setNomPeriode($nom_periode);
										$Archive->setMatiere($Groupe->getDescription());
										$Archive->setSpecial('');
										$Archive->setProfs($prof);
										$Archive->setValeur($Ects->getValeur());
										$Archive->setMention($Ects->getMention());
										$Archive->save();
									}
								}
							}
							echo "<!-- Après les crédits ECTS de l'élève $login_eleve -->\n";

							if($erreur==0){
								echo "<script type='text/javascript'>
	document.getElementById('td_".$i."_".$j."').style.backgroundColor='green';
</script>\n";
							}
							flush();
						}

					}

				}

			}



//==================================================
//**************************************************
//==================================================

		}


		//===================================

		echo "<input type='hidden' name='deja_traitee_id_classe[]' value='$id_classe[0]' />\n";

		/*
		$temoin_encore_des_classes=0;
		$chaine="";
		for($i=1;$i<count($id_classe);$i++){
			echo "<input type='hidden' name='id_classe[]' value='$id_classe[$i]' />\n";
			$temoin_encore_des_classes++;

			$chaine.=", ".get_nom_classe($id_classe[$i]);
		}
		if($chaine!=""){
			echo "<p>Classes restant à traiter: ".mb_substr($chaine,2)."</p>\n";
		}
		*/

		if($temoin_encore_des_classes>0){
			echo "<script type='text/javascript'>
	setTimeout('document.formulaire.submit();', 5000);
</script>\n";
			echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";
		}
		else{
			echo "<p style='text-align:center; font-weight:bold; font-size:2em; color: green;'>Traitement terminé.</p>\n";
			echo "<script type='text/javascript'>
	document.getElementById('annonce_fin_traitement').innerHTML='Traitement terminé.';
</script>\n";

		}

		echo "<input type='hidden' name='annee_scolaire' value='$annee_scolaire' />\n";
		echo "<input type='hidden' name='confirmer' value='ok' />\n";
		echo add_token_field();
		//echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

	//===================================


	}
}

//echo "<center><input type=\"submit\" name='ok' value=\"Valider\" style=\"font-variant: small-caps;\" /></center>\n";

echo "</form>\n";
echo "<br />\n";
require("../lib/footer.inc.php");
?>
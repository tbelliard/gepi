<?php
/*
* $Id$
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

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
};

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
die();
}


// Initialistion
$id_groupe = isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
if (is_numeric($id_groupe) && $id_groupe > 0) {
	$current_group = get_group($id_groupe);
} else {
	$current_group = false;
}

if (count($current_group["classes"]["list"]) > 1) {
	$multiclasses = true;
} else {
	$multiclasses = false;
}

$periode_cn = isset($_POST["periode_cn"]) ? $_POST["periode_cn"] :(isset($_GET["periode_cn"]) ? $_GET["periode_cn"] :NULL);
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : (isset($_POST['order_by']) ? $_POST["order_by"] : "classe");

include "../lib/periodes.inc.php";


if ($_SESSION['statut'] != "secours") {
	if (!(check_prof_groupe($_SESSION['login'],$current_group["id"]))) {
		$mess=rawurlencode("Vous n'êtes pas professeur de cet enseignement !");
		header("Location: index.php?msg=$mess");
		die();
	}
}

if (isset($_POST['is_posted'])) {

	$indice_max_log_eleve=$_POST['indice_max_log_eleve'];

	$k=1;
	while ($k < $nb_periode) {
		//=========================
		// AJOUT: boireaus 20071003
		unset($log_eleve);
		$log_eleve=isset($_POST['log_eleve_'.$k]) ? $_POST['log_eleve_'.$k] : NULL;
		//=========================

		//=================================================
		// AJOUT: boireaus 20080201
		if(isset($_POST['app_grp_'.$k])){
			if($current_group["classe"]["ver_periode"]['all'][$k]!=0){
				if (isset($NON_PROTECT["app_grp_".$k])){
					$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT["app_grp_".$k]));
				}
				else{
					$app = "";
				}
				//echo "$k: $app<br />";
				// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
				$app=ereg_replace('(\\\r\\\n)+',"\r\n",$app);

				$test_grp_app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
				$test = mysql_num_rows($test_grp_app_query);
				if ($test != "0") {
					if ($app != "") {
						$register = mysql_query("UPDATE matieres_appreciations_grp SET appreciation='" . $app . "' WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					} else {
						$register = mysql_query("DELETE FROM matieres_appreciations_grp WHERE (id_groupe='" . $current_group["id"]."' AND periode='$k')");
					}
					if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";}

				} else {
					if ($app != "") {
						$register = mysql_query("INSERT INTO matieres_appreciations_grp SET id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
						if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour le groupe/classe<br />";}
					}
				}
			}
		}
		//=================================================

		if(isset($log_eleve)){
			//for($i=0;$i<count($log_eleve);$i++){
			for($i=0;$i<$indice_max_log_eleve;$i++){

				//echo "\$log_eleve[$i]=$log_eleve[$i]<br />\n";
				if(isset($log_eleve[$i])) {
					// On supprime le suffixe indiquant la période:
					$reg_eleve_login=ereg_replace("_t".$k."$","",$log_eleve[$i]);

					//echo "\$i=$i<br />";
					//echo "\$reg_eleve_login=$reg_eleve_login<br />";

					// La période est-elle ouverte?
					if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
						$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
						if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){

							$nom_log = "app_eleve_".$k."_".$i;

							//echo "\$nom_log=$nom_log<br />";

							if (isset($NON_PROTECT[$nom_log])){
								$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
							}
							else{
								$app = "";
							}

							//echo "\$app=$app<br />";

							// Contrôle des saisies pour supprimer les sauts de lignes surnuméraires.
							$app=ereg_replace('(\\\r\\\n)+',"\r\n",$app);

							$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							$test = mysql_num_rows($test_eleve_app_query);
							if ($test != "0") {
								if ($app != "") {
									$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								} else {
									$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
								}
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}

							} else {
								if ($app != "") {
									$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
									if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}
								}
							}
						}
					}
				}
			}
		}
		$k++;
	}
// A partir de là, toutes les appréciations ont été sauvegardées proprement, on vide la table tempo
$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
OR DIE('Erreur dans l\'effacement de la table temporaire (1) :'.mysql_error());


	/*
	foreach ($current_group["eleves"]["all"]["list"] as $reg_eleve_login) {
		$k=1;
		while ($k < $nb_periode) {
			if (in_array($reg_eleve_login, $current_group["eleves"][$k]["list"])) {
					$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$reg_eleve_login]["classe"]]["id"];
					if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] == "N"){
						$nom_log = $reg_eleve_login."_t".$k;
						if (isset($NON_PROTECT[$nom_log]))
						$app = traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$nom_log]));
						else
						$app = "";

						// Contrôle des saisies pour supprimer les sauts de lignes surnuméraire.
						$app=ereg_replace('(\\\r\\\n)+',"\r\n",$app);


						$test_eleve_app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
						$test = mysql_num_rows($test_eleve_app_query);
						if ($test != "0") {
							if ($app != "") {
								$register = mysql_query("UPDATE matieres_appreciations SET appreciation='" . $app . "' WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							} else {
								$register = mysql_query("DELETE FROM matieres_appreciations WHERE (login='$reg_eleve_login' AND id_groupe='" . $current_group["id"]."' AND periode='$k')");
							}
							if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}

						} else {
							if ($app != "") {
								$register = mysql_query("INSERT INTO matieres_appreciations SET login='$reg_eleve_login',id_groupe='" . $current_group["id"]."',periode='$k',appreciation='" . $app . "'");
								if (!$register) {$msg = $msg."Erreur lors de l'enregistrement des données de la période $k pour l'élève $reg_eleve_login<br />";}
							}
						}
					}
			}
			$k++;
		}
	}
	*/
	$affiche_message = 'yes';
}

if (!isset($periode_cn)) $periode_cn = 0;

$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_enregistrement = "Les modifications ont été enregistrées !";
$utilisation_prototype = "ok";
$javascript_specifique = "saisie/scripts/js_saisie";
//**************** EN-TETE *****************
$titre_page = "Saisie des appréciations";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

$tmp_timeout=(getSettingValue("sessionMaxLength"))*60;

?>
<script type="text/javascript" language="javascript">
change = 'no';

</script>
<?php



$matiere_nom = $current_group["matiere"]["nom_complet"];

echo "<form enctype=\"multipart/form-data\" action=\"saisie_appreciations.php\" method=\"post\">\n";

echo "<p class=bold>";
//if ($periode_cn != 0) {
if (($periode_cn != 0)&&($_SESSION['statut']!='secours')) {
	//echo "|<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour</a>";
	echo "<a href=\"../cahier_notes/index.php?id_groupe=$id_groupe&amp;periode_num=$periode_cn\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
} else {
	echo "<a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour accueil saisie</a>\n";
}
//echo "|<a href='saisie_notes.php?id_groupe=$id_groupe&periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
echo " | <a href='saisie_notes.php?id_groupe=$id_groupe&amp;periode_cn=$periode_cn' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les moyennes</a>";
// enregistrement du chemin de retour pour la fonction imprimer
$_SESSION['chemin_retour'] = $_SERVER['PHP_SELF']."?". $_SERVER['QUERY_STRING'];
echo " | <a href='../prepa_conseil/index1.php?id_groupe=$id_groupe'>Imprimer</a>\n";

//=========================
// AJOUT: boireaus 20071108
echo " | <a href='index.php?id_groupe=" . $current_group["id"] . "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Import/Export notes et appréciations</a>";
//=========================

/*
// =================================
// AJOUT: boireaus
// Pour proposer de passer à la classe suivante ou à la précédente
//$sql="SELECT id, classe FROM classes ORDER BY classe";
if($_SESSION['statut']=='secours'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
}
else
*/
if($_SESSION['statut']=='professeur'){
	//$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE p.id_classe = c.id AND jgc.id_classe=c.id AND jgp.id_groupe=jgc.id_groupe AND jgp.login='".$_SESSION['login']."' ORDER BY c.classe";


    $tab_groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
    //$tab_groups = get_groups_for_prof($_SESSION["login"]);

	if(!empty($tab_groups)) {
		$id_grp_prec=0;
		$id_grp_suiv=0;
		$temoin_tmp=0;
		//foreach($tab_groups as $tmp_group) {
		for($loop=0;$loop<count($tab_groups);$loop++) {
			if($tab_groups[$loop]['id']==$id_groupe){
				$temoin_tmp=1;
				if(isset($tab_groups[$loop+1])){
					$id_grp_suiv=$tab_groups[$loop+1]['id'];
				}
				else{
					$id_grp_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_grp_prec=$tab_groups[$loop]['id'];
			}
		}
		// =================================

		if(isset($id_grp_prec)){
			if($id_grp_prec!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_prec&amp;periode_cn=$periode_cn";
				echo "'>Enseignement précédent</a>";
			}
		}
		if(isset($id_grp_suiv)){
			if($id_grp_suiv!=0){
				echo " | <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_grp_suiv&amp;periode_cn=$periode_cn";
				echo "'>Enseignement suivant</a>";
				}
		}
	}
	// =================================
}


echo " | <input type='submit' value='Enregistrer' /></p>\n";
echo "<h2 class='gepi'>Bulletin scolaire - Saisie des appréciations</h2>\n";
//echo "<p><b>Groupe : " . $current_group["description"] ." | Matière : $matiere_nom</b></p>\n";
echo "<p><b>Groupe : " . htmlentities($current_group["description"]) ." (".$current_group["classlist_string"].")</b></p>\n";

if ($multiclasses) {
	echo "<p>Affichage :";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&order_by=classe'>Regrouper les élèves par classe</a>";
	echo "<br/>-> <a href='saisie_appreciations.php?id_groupe=$id_groupe&order_by=nom'>Afficher la liste par ordre alphabétique</a>";
	echo "</p>";
}

// On commence par mettre la liste dans l'ordre souhaité
if ($order_by != "classe") {
	$liste_eleves = $current_group["eleves"]["all"]["list"];
} else {
	// Ici, on tri par classe
	// On va juste créer une liste des élèves pour chaque classe
	$tab_classes = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$tab_classes[$classe_id] = array();
	}
	// On passe maintenant élève par élève et on les met dans la bonne liste selon leur classe
	foreach($current_group["eleves"]["all"]["list"] as $eleve_login) {
		$classe = $current_group["eleves"]["all"]["users"][$eleve_login]["classe"];
		$tab_classes[$classe][] = $eleve_login;
	}
	// On met tout ça à la suite
	$liste_eleves = array();
	foreach($current_group["classes"]["list"] as $classe_id) {
		$liste_eleves = array_merge($liste_eleves, $tab_classes[$classe_id]);
	}
}


// Fonction de renseignement du champ qui doit obtenir le focus après validation
echo "<script type='text/javascript'>

function focus_suivant(num){
	temoin='';
	// La variable 'dernier' peut dépasser de l'effectif de la classe... mais cela n'est pas dramatique
	dernier=num+".count($liste_eleves)."
	// On parcourt les champs à partir de celui de l'élève en cours jusqu'à rencontrer un champ existant
	// (pour réussir à passer un élève qui ne serait plus dans la période)
	// Après validation, c'est ce champ qui obtiendra le focus si on n'était pas à la fin de la liste.
	for(i=num;i<dernier;i++){
		suivant=i+1;
		if(temoin==''){
			if(document.getElementById('n'+suivant)){
				document.getElementById('info_focus').value=suivant;
				temoin=suivant;
			}
		}
	}

	document.getElementById('info_focus').value=temoin;
}

</script>\n";

	// ====================== Modif pour la sauvegarde en ajax =================
$restauration = isset($_GET["restauration"]) ? $_GET["restauration"] : NULL;
	// On teste s'il existe des données dans la table matieres_appreciations_tempo
	$sql_test = mysql_query("SELECT login FROM matieres_appreciations_tempo WHERE id_groupe = '" . $current_group["id"] . "'");
	$test = mysql_num_rows($sql_test);
		if ($test !== 0 AND $restauration == NULL) {
			// On envoie un message à l'utilisateur
			echo "
			<p class=\"red\">Certaines appréciations n'ont pas été enregistrées lors de votre dernière saisie.<br />
				Elles sont indiquées ci-dessous en rouge. Voulez-vous les restaurer ?
			</p>
			<p class=\"red\">
			<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=oui\">OUI</a>
			(elles remplaceront alors la saisie précédente)
			 -
			<a href=\"./saisie_appreciations.php?id_groupe=".$current_group["id"]."&amp;restauration=non\">NON</a>
			(elles seront alors définitivement perdues)
			</p>
			";
		}

	// Dans tous les cas, si $restauration n'est pas NULL, il faut vider la table tempo des appréciations de ce groupe

//=================================
// AJOUT: boireaus 20080201
$k=1;
$num_id = 10;
while ($k < $nb_periode) {

	$app_query = mysql_query("SELECT * FROM matieres_appreciations_grp WHERE (id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
	$app_grp[$k] = @mysql_result($app_query, 0, "appreciation");

	$call_moyenne_t[$k] = mysql_query("SELECT round(avg(n.note),1) moyenne FROM matieres_notes n, j_eleves_groupes j " .
								"WHERE (" .
								"n.id_groupe='" . $current_group["id"] ."' AND " .
								"n.login = j.login AND " .
								"n.statut='' AND " .
								"j.id_groupe = n.id_groupe AND " .
								"n.periode='$k' AND j.periode='$k'" .
								")");
	$moyenne_t[$k] = mysql_result($call_moyenne_t[$k], 0, "moyenne");

	if ($moyenne_t[$k]=='') {
		$moyenne_t[$k]="&nbsp;";
	}

	$mess[$k]="";
	$mess[$k].="<td>".$moyenne_t[$k]."</td>\n";
	$mess[$k].="<td>\n";
	if ($current_group["classe"]["ver_periode"]['all'][$k] == 0){
		$mess[$k].=htmlentities(nl2br($app_grp[$k]));
	}
	else {
		if(!isset($id_premier_textarea)) {$id_premier_textarea=$k.$num_id;}

		$mess[$k].="<input type='hidden' name='app_grp_".$k."' value=\"".$app_grp[$k]."\" />\n";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\"";
		//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' style='white-space: nowrap;' onchange=\"changement()\"";
		$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_grp_".$k."\" rows='2' cols='100' onchange=\"changement()\"";
		// onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"
		$mess[$k].=">".$app_grp[$k]."</textarea>\n";
	}

	// on affiche si besoin l'appréciation temporaire (en sauvegarde)
	//$mess[$k].=$eleve_app_t;
	$mess[$k].= "</td>\n";
	$k++;
}


echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Appréciation sur le groupe/classe\">\n";
echo "<tr>\n";
echo "<th width=\"200\"><div align=\"center\">&nbsp;</div></th>\n";
echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
echo "<th><div align=\"center\"><b>Appréciation sur le groupe/classe</b>\n";
echo "</div></th>\n";
echo "</tr>\n";
//=================================================


$num_id++;
$k=1;
$alt=1;
while ($k < $nb_periode) {
	$alt=$alt*(-1);
	if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
		echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
	} else {
		echo "<tr class='lig$alt'><td>$nom_periode[$k]</td>\n";
	}
	echo $mess[$k];
	$k++;
}
echo "</tr>\n";
echo "</table>\n";
echo "<br />\n";


//=================================



$prev_classe = null;
//=================================================
// COMMENTé: boireaus 20080201
//$num_id = 10;
//=================================================
//=========================
// AJOUT: boireaus 20071010
// Compteur pour les élèves
$i=0;
//=========================
foreach ($liste_eleves as $eleve_login) {

	$k=1;
	$temoin_photo="";

	while ($k < $nb_periode) {

		if (in_array($eleve_login, $current_group["eleves"][$k]["list"])) {
			//
			// si l'élève appartient au groupe pour cette période
			//
			$eleve_nom = $current_group["eleves"][$k]["users"][$eleve_login]["nom"];
			$eleve_prenom = $current_group["eleves"][$k]["users"][$eleve_login]["prenom"];
			$eleve_classe = $current_group["classes"]["classes"][$current_group["eleves"]["all"]["users"][$eleve_login]["classe"]]["classe"];
			$eleve_id_classe = $current_group["classes"]["classes"][$current_group["eleves"][$k]["users"][$eleve_login]["classe"]]["id"];

			//========================
			// AJOUT boireaus 20071115
			if($k==1){
				$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
				$res_ele=mysql_query($sql);
				$lig_ele=mysql_fetch_object($res_ele);
				$eleve_elenoet=$lig_ele->elenoet;

				// Photo...
				$photo=nom_photo($eleve_elenoet);
				//$temoin_photo="";
				if("$photo"!=""){
					$titre="$eleve_nom $eleve_prenom";

					$texte="<div align='center'>\n";
					$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
					$texte.="<br />\n";
					$texte.="</div>\n";

					$temoin_photo="y";

					$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');
				}
			}
			//========================

			$suit_option[$k] = 'yes';
			//
			// si l'élève suit la matière
			//
			if ($restauration != "oui" AND $restauration != "non") {
				// On récupère l'appréciation tempo pour la rajouter à $eleve_app
				$app_t_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE
					(login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				$verif_t = mysql_num_rows($app_t_query);
				if ($verif_t != 0) {
					$eleve_app_t = "\n".'<p>Appréciation non enregistrée : <span style="color: red;">'.@mysql_result($app_t_query, 0, "appreciation").'</span></p>';
				}else{
					$eleve_app_t = '';
				}
			}else{
				$eleve_app_t = '';
			}

			// Appel des appréciations (en vérifiant si une restauration est demandée ou non)
			if ($restauration == "oui") {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations_tempo WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				// Si la sauvegarde ne donne rien pour cet élève, on va quand même voir dans la table définitive
				$verif = mysql_num_rows($app_query);
				if ($verif == 0){
					$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
				}
			} else {
				$app_query = mysql_query("SELECT * FROM matieres_appreciations WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			}
			$eleve_app = @mysql_result($app_query, 0, "appreciation");
			// Appel des notes
			$note_query = mysql_query("SELECT * FROM matieres_notes WHERE (login='$eleve_login' AND id_groupe = '" . $current_group["id"] . "' AND periode='$k')");
			$eleve_statut = @mysql_result($note_query, 0, "statut");
			$eleve_note = @mysql_result($note_query, 0, "note");
			// Formatage de la note
			$note ="<center>";
			if ($eleve_statut != '') {
				$note .= $eleve_statut;
			} else {
				if ($eleve_note != '') {
					$note .= $eleve_note;
				} else {
					$note .= "&nbsp;";
				}
			}
			$note .= "</center>";

			$eleve_login_t[$k] = $eleve_login."_t".$k;
			if ($current_group["classe"]["ver_periode"][$eleve_id_classe][$k] != "N") {
				//
				// si la période est verrouillée
				//
				$mess[$k] = '';
				$mess[$k] =$mess[$k]."<td>".$note."</td>\n<td>";
				if ($eleve_app != '') {
					//$mess[$k] =$mess[$k].$eleve_app;
					if((strstr($eleve_app,">"))||(strstr($eleve_app,"<"))){
						$mess[$k] =$mess[$k].$eleve_app;
					}
					else{
									$mess[$k] =$mess[$k].nl2br($eleve_app);
					}
				} else {
					$mess[$k] =$mess[$k]."&nbsp;";
				}
				$mess[$k] =$mess[$k]."</td>\n";
			} else {

				// Ajout Eric affichage des notes au dessus de la saisie des appréciations
				$liste_notes ='';
				// Nombre de contrôles
				$sql="SELECT cnd.note FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE cnd.login='".$eleve_login."' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe='".$current_group["id"]."' AND ccn.periode='$k' AND cnd.statut='';";
				//echo "\n<!--sql=$sql-->\n";
				$result_nbct=mysql_query($sql);
				$current_eleve_nbct=mysql_num_rows($result_nbct);

				// on prend les notes dans $string_notes
				$liste_notes='';
				if ($result_nbct ) {
					while ($snnote =  mysql_fetch_assoc($result_nbct)) {
						if ($liste_notes != '') $liste_notes .= ", ";
						$liste_notes .= $snnote['note'];
					}
				}

				if ($current_eleve_nbct ==0) {
				$liste_notes='Pas de note dans le carnet pour cette période.';
				}

				//$mess[$k] = "<td>".$note."</td>\n<td><textarea id=\"".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows=2 cols=100 wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\">".$eleve_app."</textarea></td>\n";

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onfocus=\"document.getElementById('info_focus').value='n".$k.$num_id."'\">".$eleve_app."</textarea></td>\n";

				//=========================
				// MODIF: boireaus 20071003

				//$mess[$k] = "<td>".$note."</td>\n<td>Contenu du carnet de notes : ".$liste_notes."<br /><textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_".$eleve_login_t[$k]."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onfocus=\"focus_suivant(".$k.$num_id.");\">".$eleve_app."</textarea></td>\n";

				$mess[$k]="<td>".$note."</td>\n";
				$mess[$k].="<td>Contenu du carnet de notes : ".$liste_notes."<br />\n";
				$mess[$k].="<input type='hidden' name='log_eleve_".$k."[$i]' value=\"".$eleve_login_t[$k]."\" />\n";

				$mess[$k].="<input type='hidden' name='prenom_eleve_".$k."[$i]' id='prenom_eleve_".$k.$num_id."' value=\"".$eleve_prenom."\" />\n";

				//$mess[$k].="<textarea id=\"n".$k.$num_id."\" onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' wrap='virtual' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";
				//$mess[$k].="<textarea id=\"n".$k.$num_id."\" style='white-space: nowrap;' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";
				$mess[$k].="<textarea id=\"n".$k.$num_id."\" class='wrap' onKeyDown=\"clavier(this.id,event);\" name=\"no_anti_inject_app_eleve_".$k."_".$i."\" rows='2' cols='100' onchange=\"changement()\" onBlur=\"ajaxAppreciations('".$eleve_login_t[$k]."', '".$id_groupe."', 'n".$k.$num_id."');\"";

				//==================================
				// Rétablissement: boireaus 20080219
				// Pour revenir au champ suivant après validation/enregistrement:
				// MODIF: boireaus 20080520
				//$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");\"";
				$mess[$k].=" onfocus=\"focus_suivant(".$k.$num_id.");document.getElementById('focus_courant').value='".$k.$num_id."';\"";
				//==================================

				$mess[$k].=">".$eleve_app."</textarea>\n";
				// on affiche si besoin l'appréciation temporaire (en sauvegarde)
				$mess[$k].=$eleve_app_t;
				$mess[$k].= "</td>\n";

				//=========================

			}
		}
		else {
			//
			// si l'élève n'appartient pas au groupe pour cette période.
			//
			$suit_option[$k] = 'no';
			$mess[$k] = "<td>&nbsp;</td><td><p class='small'>non suivie</p></td>\n";
		}
		$k++;
	}
	//
	//Affichage de la ligne
	//
	$display_eleve='no';
	$k=1;
	while ($k < $nb_periode) {
		if ($suit_option[$k] != 'no') {$display_eleve='yes';}
		$k++;
	}
	if ($display_eleve=='yes') {

		if ($multiclasses && $prev_classe != $eleve_classe && $order_by == 'classe') {
			if ($prev_classe != null) {
				echo "<hr style='width: 95%;'/>\n";
			}
			echo "<h3>Classe de " . $eleve_classe . "</h3>\n";
		}
		$prev_classe = $eleve_classe;
		//echo "<table width=\"750px\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		/*
		echo "<table width=\"750\" border=\"1\" cellspacing=\"2\" cellpadding=\"5\">\n";
		echo "<tr>\n";
		echo "<td width=\"200\"><div align=\"center\">&nbsp;</div></td>\n";
		echo "<td width=\"30\"><div align=\"center\"><b>Moy.</b></div></td>\n";
		echo "<td><div align=\"center\"><b>$eleve_nom $eleve_prenom</b></div></td>\n";
		echo "</tr>\n";
		*/

		echo "<table width=\"750\" class='boireaus' cellspacing=\"2\" cellpadding=\"5\" summary=\"Tableau de $eleve_nom $eleve_prenom\">\n";
		echo "<tr>\n";
		echo "<th width=\"200\"><div align=\"center\">&nbsp;</div></th>\n";
		echo "<th width=\"30\"><div align=\"center\"><b>Moy.</b></div></th>\n";
		echo "<th><div align=\"center\"><b>$eleve_nom $eleve_prenom</b>\n";

		//==========================
		// AJOUT: boireaus 20071115
		// Lien photo...
		if($temoin_photo=="y"){
			//echo " <a href='#' onmouseover=\"afficher_div('photo_$eleve_login','y',-100,20);\"";
			echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-100,20,1000,10,10);\"";
			echo ">";
			echo "<img src='../images/icons/buddy.png' alt='$eleve_nom $eleve_prenom' />";
			echo "</a>";
		}
		//==========================

		/*

			$titre=$v_eleve_nom_prenom1;
			$texte="<div align='center'>\n";
			$photo=nom_photo($v_elenoet1);
			if("$photo"!=""){
				$texte.="<img src='../photos/eleves/".$photo."' width='150' alt=\"$v_eleve_nom_prenom1\" />";
				$texte.="<br />\n";
			}
			$texte.="</div>\n";

			$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve1',$titre,"",$texte,"",14,0,'y','y','n','n')
		*/

		echo "</div></th>\n";
		echo "</tr>\n";


		$num_id++;
		$k=1;
		$alt=1;
		while ($k < $nb_periode) {
			$alt=$alt*(-1);
			if ($current_group["classe"]["ver_periode"]["all"][$k] == 0) {
				echo "<tr class='lig$alt'><td><span title=\"$gepiClosedPeriodLabel\">$nom_periode[$k]</span></td>\n";
			} else {
				echo "<tr class='lig$alt'><td>$nom_periode[$k]</td>\n";
			}
			echo $mess[$k];
			$k++;
		}
		echo "</tr>\n";
		//echo"</table>\n<p></p>";
		echo "</table>\n";
		//echo"<p>&nbsp;</p>\n";
		//echo"<p></p>\n";
		echo "<br />\n";
	}
	$i++;

}

echo "<input type='hidden' name='indice_max_log_eleve' value='$i' />\n";
?>
<input type="hidden" name="is_posted" value="yes" />
<input type="hidden" name="id_groupe" value="<?php echo "$id_groupe";?>" />
<input type="hidden" name="periode_cn" value="<?php echo "$periode_cn";?>" />
<center><div id="fixe"><input type="submit" value="Enregistrer" /><br />

<!-- DIV destiné à afficher un décompte du temps restant pour ne pas se faire piéger par la fin de session -->
<div id='decompte'></div>

<?php
	//============================================
	// AJOUT: boireaus 20080520
	// Dispositif spécifique: décommenter la ligne pour l'activer
	if(getSettingValue('ctp')=='y') {include('ctp.php');}
	//============================================
?>

<!-- Champ destiné à recevoir la valeur du champ suivant celui qui a le focus pour redonner le focus à ce champ après une validation -->
<input type='hidden' id='info_focus' name='champ_info_focus' value='' />
<input type='hidden' id='focus_courant' name='focus_courant' value='' />
</div></center>
</form>

<?php
// ====================== SYSTEME  DE SAUVEGARDE ========================
// Dans tous les cas, suite à une demande de restauration, et quelle que soit la réponse, les sauvegardes doivent être effacées
if ($restauration == "oui" OR $restauration == "non") {
	$effacer = mysql_query("DELETE FROM matieres_appreciations_tempo WHERE id_groupe = '".$id_groupe."'")
	OR DIE('Erreur dans l\'effacement de la table temporaire (2) : '.mysql_error());
}
// Il faudra permettre de n'afficher ce décompte que si l'administrateur le souhaite.

echo "<script type='text/javascript'>
cpt=".$tmp_timeout.";
compte_a_rebours='y';

function decompte(cpt){
	if(compte_a_rebours=='y'){
		document.getElementById('decompte').innerHTML=cpt;
		if(cpt>0){
			cpt--;
		}

		setTimeout(\"decompte(\"+cpt+\")\",1000);
	}
	else{
		document.getElementById('decompte').style.display='none';
	}
}

decompte(cpt);

";

// Après validation, on donne le focus au champ qui suivait celui qui vient d'être rempli
if(isset($_POST['champ_info_focus'])){
	if($_POST['champ_info_focus']!=""){
		echo "// On positionne le focus...
	document.getElementById('n".$_POST['champ_info_focus']."').focus();
\n";
	}
}
elseif(isset($id_premier_textarea)) {
	echo "if(document.getElementById('n".$id_premier_textarea."')) {document.getElementById('n".$id_premier_textarea."').focus();}
if(document.getElementById('focus_courant')) {document.getElementById('focus_courant').value='$id_premier_textarea';}";
}

echo "</script>\n";

?>
<p><br /></p>
<?php require("../lib/footer.inc.php");?>
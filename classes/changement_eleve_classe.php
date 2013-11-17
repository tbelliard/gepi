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

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}


if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
if((mb_strlen(my_ereg_replace("[0-9]","",$id_classe))!=0)||($id_classe=='')) {unset($id_classe);}
$periode_num=isset($_POST['periode_num']) ? $_POST['periode_num'] : (isset($_GET['periode_num']) ? $_GET['periode_num'] : NULL);
if((mb_strlen(my_ereg_replace("[0-9]","",$periode_num))!=0)||($periode_num=='')) {unset($periode_num);}
$login_eleve=isset($_POST['login_eleve']) ? $_POST['login_eleve'] : (isset($_GET['login_eleve']) ? $_GET['login_eleve'] : NULL);

//debug_var();

if((!isset($login_eleve))||
(!isset($periode_num))||
(!isset($id_classe))) {
	$msg="Erreur: Une des variables n'était pas initialisée ou n'avait pas une valeur correcte.";
	header("Location: ../accueil.php?msg=".rawurlencode($msg));
	die();
}

$sql="SELECT 1=1 FROM j_eleves_classes WHERE id_classe='$id_classe' AND login='$login_eleve' AND periode='$periode_num';";
$verif=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($verif)==0) {
	$msg="Erreur: L'élève ne fait pas partie de la classe sur la période choisie.";
	header("Location: ../accueil.php?msg=".rawurlencode($msg));
	die();
}

include "../lib/periodes.inc.php";

$_SESSION['chemin_retour'] = $gepiPath."/classes/classes_const.php?id_classe=".$id_classe;

$id_future_classe=isset($_POST['id_future_classe']) ? $_POST['id_future_classe'] : (isset($_GET['id_future_classe']) ? $_GET['id_future_classe'] : NULL);
if((mb_strlen(my_ereg_replace("[0-9]","",$id_future_classe))!=0)||($id_future_classe=='')) {unset($id_future_classe);}

$id_grp=isset($_POST['id_grp']) ? $_POST['id_grp'] : (isset($_GET['id_grp']) ? $_GET['id_grp'] : NULL);
//if((mb_strlen(my_ereg_replace("[0-9]","",$grp))!=0)||($grp=='')) {unset($grp);}
if(!is_array($id_grp)) {unset($id_grp);}
else {
	for($i=0;$i<count($id_grp);$i++) {
		if((mb_strlen(my_ereg_replace("[0-9]","",$id_grp[$i]))!=0)||($id_grp[$i]=='')) {unset($id_grp);break;}
	}
}

$id_grp_fut=isset($_POST['id_grp_fut']) ? $_POST['id_grp_fut'] : (isset($_GET['id_grp_fut']) ? $_GET['id_grp_fut'] : NULL);
//if((mb_strlen(my_ereg_replace("[0-9]","",$grp_fut))!=0)||($grp_fut=='')) {unset($grp_fut);}
if(!is_array($id_grp_fut)) {unset($id_grp_fut);}
else {
	for($i=0;$i<count($id_grp_fut);$i++) {
		// $grp_fut[$i] peut être vide si on abandonne les notes de la matière...
		if(mb_strlen(my_ereg_replace("[0-9]","",$id_grp_fut[$i]))!=0) {unset($id_grp_fut);break;}
	}
}

$chgt_periode_sup=isset($_POST['chgt_periode_sup']) ? "y" : "n";

function affiche_debug($texte) {
	$debug="n";
	if($debug=="y") {
		echo "<span style='color:green;'>".$texte."</span>";
	}
}


function recherche_enfant2($id_parent_tmp, $current_group, $periode_num, $id_racine){
	$sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
	//echo "<!-- $sql -->\n";
	$res_enfant=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_enfant)>0){
		while($lig_conteneur_enfant=mysqli_fetch_object($res_enfant)){
			recherche_enfant2($lig_conteneur_enfant->id, $current_group, $periode_num, $id_racine);
		}
	}
	else{
		$arret = 'no';
		$id_conteneur_enfant=$id_parent_tmp;
		mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
	}
}


//$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';

//**************** EN-TETE **************************************
$titre_page = "Gestion des classes | Changement de classe";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

echo "<p class='bold'>\n";
echo "<a href='classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>\n";
echo "</p>\n";

$sql="SELECT classe FROM classes WHERE id = '$id_classe';";
$call_classe = mysqli_query($GLOBALS["mysqli"], $sql);
$classe = mysql_result($call_classe, "0", "classe");

?>

<?php
$sql="SELECT * FROM eleves WHERE login='$login_eleve';";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_ele)==0) {
	echo "<p><b>Erreur:</b> L'élève d'identifiant $login_eleve n'existe pas.</p>\n";
	require("../lib/footer.inc.php");
	die();
}
$lig_ele=mysqli_fetch_object($res_ele);

echo "<p>Vous souhaitez changer <b>".casse_mot($lig_ele->prenom,'majf2')." ".my_strtoupper($lig_ele->nom)."</b> de classe sur la période <b>".$nom_periode[$periode_num]."</b>";
if($chgt_periode_sup=='y') {echo " et suivantes";}
echo ".<br />\n";

//==============================================
// On vérifie qu'il n'y a pas de notes/app sur le bulletin pour cette période
$sql="SELECT 1=1 FROM matieres_notes WHERE login='".$login_eleve."' AND periode='".$periode_num."';";
$verif=mysqli_query($GLOBALS["mysqli"], $sql);
$sql="SELECT 1=1 FROM matieres_appreciations WHERE login='".$login_eleve."' AND periode='".$periode_num."';";
$verif2=mysqli_query($GLOBALS["mysqli"], $sql);

if((mysqli_num_rows($verif)>0)||(mysqli_num_rows($verif2)>0)) {
	echo "<p>L'élève a des notes et/ou appréciations sur le bulletin.<br />Il n'est plus possible de changer l'élève de classe pour cette période.</p>\n";
	require("../lib/footer.inc.php");
	die();
}
//==============================================

if(!isset($id_future_classe)) {
	if($lig_ele->sexe=='F') {
		echo "Elle";
	}
	else {
		echo "Il";
	}
	echo " est actuellement en <b>$classe</b>.</p>\n";

	echo "<p><b>ATTENTION:</b> Il est fortement recommandé de prendre soin de générer un relevé de notes de l'élève pour la période ".$nom_periode[$periode_num]." avec toutes les informations utiles avant de procéder au changement de classe.<br />Vous pouvez aussi faire une sauvegarde de la base.<br />Ces précautions vous permettront de revenir en arrière si un problème se produisait.</p>\n";

	echo "<p>Vers quelle classe souhaitez vous déplacer l'élève?</p>\n";
	$classes_list = mysqli_query($GLOBALS["mysqli"], "SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id AND p.num_periode='$periode_num' ORDER BY classe");
	$nb = mysqli_num_rows($classes_list);
	if ($nb !=0) {
		echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

		$nb_class_par_colonne=round($nb/3);
		//echo "<table width='100%' border='1'>\n";
		echo "<table width='100%'>\n";
		echo "<tr valign='top' align='center'>\n";

		$i = '0';

		echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
		echo "<td align='left'>\n";

		while ($i < $nb) {
			$t_id_classe = mysql_result($classes_list, $i, 'id');
			if($t_id_classe!=$id_classe) {
				$temp = "case_".$t_id_classe;
				$t_classe = mysql_result($classes_list, $i, 'classe');

				if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
					echo "</td>\n";
					echo "<td align='left'>\n";
				}

				echo "<label for='$temp' style='cursor: pointer;'>\n";
				echo "<input type='radio' name='id_future_classe' id='$temp' value='$t_id_classe' />\n";
				echo "Classe : $t_classe</label><br />\n";
			}
			$i++;
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";

		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
		echo "<input type='hidden' name='login_eleve' value='$login_eleve' />\n";

		echo "<p align='center'><input type='submit' value='Enregistrer' style='margin: 30px 0 60px 0;'/></p>\n";
		echo "</form>\n";
	}
}
else {
	$sql="SELECT classe FROM classes WHERE id='$id_future_classe';";
	$call_classe_future = mysqli_query($GLOBALS["mysqli"], $sql);
	$classe_future = mysql_result($call_classe_future, "0", "classe");

	echo "<p>Vous souhaitez déplacer l'élève de <b>$classe</b> vers <b>$classe_future</b> sur la période <b>".$nom_periode[$periode_num]."</b>";
	if($chgt_periode_sup=='y') {echo " et suivantes";}
	echo ".</p>\n";

	//$sql="SELECT * FROM j_groupes_classes WHERE id_classe='$id_classe';";

	//$groupes=get_groups_for_class($id_classe);
	//$groupes=get_groups_for_class($id_classe, "", "n");
	//for($i=0;$i<count($groupes);$i++) {

	// Groupes de la classe future:
	$sql="SELECT g.id FROM groupes g,
						j_groupes_classes jgc
			WHERE (g.id = jgc.id_groupe AND
					jgc.id_classe='$id_future_classe')
			ORDER BY jgc.priorite, g.name;";
	//echo "$sql<br />";
	affiche_debug("$sql<br />");
	$res_grp_fut=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_grp_fut)==0) {
		echo "<p>La classe future de l'élève n'a semble-t-il aucun enseignement associé.<br />Vous devez créer des enseignements dans la classe future avant de procéder au changement de classe.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}
	else {
		for($i=0;$i<mysqli_num_rows($res_grp_fut);$i++) {
			$lig_grp=mysqli_fetch_object($res_grp_fut);
			$tab_group_fut[]=get_group($lig_grp->id);

			$tab_id_group_fut[]=$lig_grp->id;
		}
	}

	// Groupes de la classe actuelle:
	$sql="SELECT g.id FROM groupes g,
						j_groupes_classes jgc
			WHERE (g.id = jgc.id_groupe AND
					jgc.id_classe='$id_classe')
			ORDER BY jgc.priorite, g.name;";
	$res_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	//echo "$sql<br />";
	affiche_debug("$sql<br />");
	if(mysqli_num_rows($res_grp)==0) {
		echo "<p>La classe actuelle de l'élève n'a semble-t-il aucun enseignement associé.<br />Vous pouvez retirer l'élève de l'ancienne classe et l'ajouter dans la nouvelle sans autre formalité.</p>\n";

		require("../lib/footer.inc.php");
		die();
	}


	if((!isset($id_grp))||(!isset($id_grp_fut))) {
		echo "<p>Veuillez compléter/confirmer les associations éventuellement proposées.</p>\n";

		echo "<div align='center'>\n";

		echo "<form enctype='multipart/form-data' name='form_assoc_grp' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

		echo add_token_field();

		echo "<table class='boireaus' border='1' summary='Tableau des enseignements de la classe actuelle et de leurs correspondances dans la classe future'>\n";
		echo "<tr>\n";
		echo "<th width='50%'>Enseignements de $classe</th>\n";
		echo "<th width='50%'>Enseignements de $classe_future</th>\n";
		echo "</tr>\n";
		$alt=1;
		$cpt=0;
		for($i=0;$i<mysqli_num_rows($res_grp);$i++) {
			$lig_grp=mysqli_fetch_object($res_grp);
			$group=get_group($lig_grp->id);

			// L'élève est-il dans le groupe sur la période choisie?
			if(in_array($login_eleve,$group["eleves"][$periode_num]["list"])) {
				$cpt_grp_mat=0;

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				echo "<td>\n";
				echo "<input type='hidden' name='id_grp[$cpt]' value='".$group['id']."' />\n";
				echo htmlspecialchars($group['name'])." (<i>".htmlspecialchars($group['matiere']['nom_complet'])."</i>)";
				echo " (<i style='color:green;'>$lig_grp->id</i>)";
				echo "</td>\n";

				echo "<td>\n";
				echo "<select name='id_grp_fut[$cpt]' id='id_grp_fut_$cpt'>\n";
				echo "<option value=''>---</option>\n";
				for($j=0;$j<count($tab_group_fut);$j++) {

					$chaine_profs="";
					foreach($tab_group_fut[$j]["profs"]["users"] as $tab_prof) {
						if($chaine_profs!="") {$chaine_profs.=", ";}
						$chaine_profs.=casse_mot($tab_prof['nom'],'majf2')." ".my_strtoupper(mb_substr($tab_prof['prenom'],0,1));
					}

					echo "<option value='".$tab_group_fut[$j]['id']."'";
					if(!in_array($lig_grp->id,$tab_id_group_fut)) {
						if($tab_group_fut[$j]['matiere']['nom_complet']==$group['matiere']['nom_complet']) {echo " selected";}
					}
					elseif($lig_grp->id==$tab_group_fut[$j]['id']) {
						echo " selected";
					}
					//echo ">".$tab_group_fut[$j]['name']." (<i>".$chaine_profs."</i>)</option>\n";
					echo ">".$tab_group_fut[$j]['name']." (".$chaine_profs.") (".$tab_group_fut[$j]['id'].")</option>\n";

					// Compteur des groupes de la classe future correspondant à la même matière que celle du groupe de l'actuelle classe
					if($tab_group_fut[$j]['matiere']['nom_complet']==$group['matiere']['nom_complet']) {$cpt_grp_mat++;}
				}
				echo "</select>\n";

				if($cpt_grp_mat>1) {
					echo "<br /><span style='color:red'>Attention: plusieurs groupes possibles</span>";
				}
				echo "</td>\n";

				echo "</tr>\n";
				$cpt++;
			}

		}

		echo "</table>\n";

		$sql="SELECT 1=1 FROM periodes WHERE id_classe='$id_classe' AND num_periode>$periode_num;";
		$test_per=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test_per)>0) {
			echo "<input type='checkbox' name='chgt_periode_sup' id='chgt_periode_sup' value='y' checked /><label for='chgt_periode_sup'> Changer l'élève de classe également pour les périodes qui suivent la période $periode_num<br />(<i>pour le reste de l'année scolaire en somme</i>)</label><br />\n";
		}

		echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
		echo "<input type='hidden' name='id_future_classe' value='$id_future_classe' />\n";
		echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";
		echo "<input type='hidden' name='login_eleve' value='$login_eleve' />\n";

		// Remplacer par un 'button' avec JavaScript de contrôle pour alerter si un des groupes destination n'est pas sélectionné
		// (avec confirm())
		//echo "<p align='center'><input type='submit' value='Valider' style='margin: 30px 0 60px 0;'/></p>\n";
		echo "<p align='center'><input type='button' value='Valider' style='margin: 30px 0 60px 0;' onClick=\"verifie_form()\" /></p>\n";
		echo "</form>\n";
		echo "</div>\n";

		// Recherche des inscriptions dans des AID pour afficher un avertissement
		$sql="SELECT a.nom, ac.nom_complet, jae.* FROM j_aid_eleves jae, aid a, aid_config ac WHERE jae.login='$login_eleve' AND jae.id_aid=a.id AND jae.indice_aid=a.indice_aid AND ac.indice_aid=a.indice_aid ORDER BY ac.nom_complet, a.nom";
		$res_aid=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_aid)>0) {
			echo "<p><b>".get_nom_prenom_eleve($login_eleve)."</b> est inscrit(e) dans un ou des <b>AID</b>.<br />\nIl faudra contrôler si les contraintes d'emploi du temps permettent de conserver ces inscriptions ou s'il convient d'effectuer des modifications.<br />\nVoici la liste&nbsp;:<br />\n";
			while($lig_aid=mysqli_fetch_object($res_aid)) {
				echo "&nbsp;&nbsp;&nbsp;-&nbsp;$lig_aid->nom (<i>$lig_aid->nom_complet</i>)";

				$sql="SELECT DISTINCT jec.login FROM j_eleves_classes jec, j_aid_eleves jae WHERE jec.login=jae.login AND jec.id_classe='$id_future_classe' AND jae.id_aid='$lig_aid->id_aid';";
				$test=mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_ele_fut_aid=mysqli_num_rows($test);
				if($nb_ele_fut_aid==1) {
					$lig_ele_fut_aid=mysqli_fetch_object($test);
					echo "&nbsp;: ".$nb_ele_fut_aid." élève de la classe future est inscrit dans cet AID (<span style='font-size:small; font-style:italic;'>".get_nom_prenom_eleve($lig_ele_fut_aid->login)."</span>).";
				}
				elseif($nb_ele_fut_aid>1) {
					echo "&nbsp;: ".$nb_ele_fut_aid." élèves de la classe future sont inscrits dans cet AID (<span style='font-size:small; font-style:italic;'>";
					$cpt_ele_aid=0;
					while($lig_ele_fut_aid=mysqli_fetch_object($test)) {
						if($cpt_ele_aid>0) {echo ", ";}
						echo get_nom_prenom_eleve($lig_ele_fut_aid->login);
						$cpt_ele_aid++;
					}
					echo "</span>)";
				}

				echo "<br />\n";
			}
			echo "</p>\n";
			echo "<p><br /></p>\n";
		}

		echo "<script type='text/javascript'>
	function verifie_form() {
		temoin_assoc='ok';
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('id_grp_fut_'+i)) {
				//alert(\"document.getElementById('id_grp_fut_\"+i+\"').value=\"+document.getElementById('id_grp_fut_'+i).value);

				if(document.getElementById('id_grp_fut_'+i).value=='') {
					temoin_assoc='n';
				}
			}
		}

		if(temoin_assoc=='n') {
			verif=confirm('ATTENTION:\\nUn enseignement de ".$classe." au moins n est pas associé à un enseignement de ".$classe_future.".\\nLes notes éventuelles de l élève à cet enseignement seront perdues si vous confirmez la volonté de procéder aux changements.');
			if(verif) {
				document.forms['form_assoc_grp'].submit();
			}
		}
		else {
			document.forms['form_assoc_grp'].submit();
		}
	}
</script>\n";


		echo "<p><b>ATTENTION:</b></p>
<blockquote>
<p>Dans le cas où des groupes sont en commun entre la $classe et la $classe_future, si l'élève fait partie de plusieurs enseignements correspondant à la même matière (<i>exemple: l'élève fait partie de 2 enseignements de DecP3 partagés entre les deux classes</i>), il faut éviter d'intervertir les groupes.<br />
Dans le cas contraire, lors de la migration du deuxième enseignement, l'inscription au premier est supprimé.</p>
<p>La page propose normalement de ne pas changer de groupe lorsque le groupe est partagé par les deux classes (<i>le numéro de groupe est indiqué entre parenthèses</i>).<br />
Evitez les 'fantaisies';o).</p>
</blockquote>\n";
	}
	else {
		check_token(false);

		$tab_per=array($periode_num);

		if($chgt_periode_sup=="y") {
			$sql="SELECT num_periode FROM periodes WHERE id_classe='$id_classe' AND num_periode>$periode_num ORDER BY num_periode;";
			$test_per=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_per)>0) {
				while($lig_per=mysqli_fetch_object($test_per)) {
					$tab_per[]=$lig_per->num_periode;
				}
			}

		}

		affiche_debug("count(\$tab_per)=".count($tab_per)."<br />\n");
		for($j=0;$j<count($tab_per);$j++) {
			affiche_debug("\$tab_per[$j]=$tab_per[$j]<br />");
		}

		$gepi_denom_boite=getSettingValue("gepi_denom_boite");

		for($i=0;$i<count($id_grp);$i++) {
			$group=get_group($id_grp[$i]);
			echo "<p><b>".htmlspecialchars($group['name'])." (<i>".htmlspecialchars($group['matiere']['nom_complet'])." en ".$group["classlist_string"]."</i>)"."</b><br />\n";

            affiche_debug("\$id_grp[$i]=$id_grp[$i]<br />");
            affiche_debug("\$id_grp_fut[$i]=$id_grp_fut[$i]<br />");

			if($id_grp[$i]==$id_grp_fut[$i]) {
				// C'était un groupe partagé entre les deux classes
				echo "L'enseignement était partagé entre les deux classes.<br />\n";
				echo "On ne modifie rien pour cet enseignement.<br />\n";
			}
			else {
				for($j=0;$j<count($tab_per);$j++) {
					$current_periode_num=$tab_per[$j];

					// Recherche du carnet de notes de l'ancien groupe
					$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_grp[$i]."' AND periode='$current_periode_num';";
					affiche_debug("$sql<br />");
					$res_ccn=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_ccn)==0) {
						echo "Aucune note n'était saisie (<i>carnet de notes non initialisé pour la période $current_periode_num</i>).<br />\n";
					}
					else {
						$lig_ccn=mysqli_fetch_object($res_ccn);
	
						// Recherche des devoirs de l'ancien groupe pour lesquels l'élève a au moins une note
						$sql="SELECT cd.*, cnd.login, cnd.note, cnd.comment, cnd.statut FROM cn_devoirs cd,
											cn_notes_devoirs cnd
							WHERE cd.id_racine='".$lig_ccn->id_cahier_notes."' AND
								cd.id=cnd.id_devoir AND
								cnd.login='$login_eleve';";
						affiche_debug("$sql<br />");
						$res_cd=mysqli_query($GLOBALS["mysqli"], $sql);
	
						if(mysqli_num_rows($res_cd)==0) {
							echo "Aucune note n'était saisie (<i>aucun devoir dans le carnet de notes pour la période $current_periode_num</i>).<br />\n";
						}
						else {
							if($id_grp_fut[$i]=='') {
								echo "Aucun groupe futur n'a été sélectionné: Les notes éventuelles seront perdues.<br />\n";
	
								// Insérer le ménage à ce niveau
	
								while($lig_cd=mysqli_fetch_object($res_cd)) {
									// Suppression de la note dans l'ancien carnet de notes
									$sql="DELETE FROM cn_notes_devoirs WHERE login='$login_eleve' AND id_devoir='$lig_cd->id';";
									affiche_debug("$sql<br />");
									$del=mysqli_query($GLOBALS["mysqli"], $sql);
								}
	
	
								// Suppression des anciennes notes de conteneurs
								$sql="SELECT * FROM cn_conteneurs WHERE id_racine='".$lig_ccn->id_cahier_notes."';";
								affiche_debug("$sql<br />");
								$res_cn=mysqli_query($GLOBALS["mysqli"], $sql);
	
								if(mysqli_num_rows($res_cn)>0) {
									while($lig_cn=mysqli_fetch_object($res_cn)) {
										$sql="DELETE FROM cn_notes_conteneurs WHERE login='$login_eleve' AND id_conteneur='".$lig_cn->id."';";
										affiche_debug("$sql<br />");
										$del=mysqli_query($GLOBALS["mysqli"], $sql);
									}
								}
	
	
							}
							else {
								$group_fut=get_group($id_grp_fut[$i]);
								echo "Transfert des notes/devoirs vers ".htmlspecialchars($group_fut['name'])." (<i>".htmlspecialchars($group_fut['matiere']['nom_complet'])." en ".$group_fut["classlist_string"]."</i>)"."<br />";
	
								// Recherche du carnet de notes du nouveau groupe
								$sql="SELECT * FROM cn_cahier_notes WHERE id_groupe='".$id_grp_fut[$i]."' AND periode='$current_periode_num'";
								$res_ccn_fut=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($res_ccn_fut)==0) {
									// On crée le carnet de notes dans le groupe futur
	
									$nom_complet_matiere_fut = $group_fut["matiere"]["nom_complet"];
									$nom_court_matiere_fut = $group_fut["matiere"]["matiere"];
	
									$sql="INSERT INTO cn_conteneurs SET id_racine='',
											nom_court='".traitement_magic_quotes($group_fut["description"])."',
											nom_complet='". traitement_magic_quotes($nom_complet_matiere_fut)."',
											description = '',
											mode = '2',
											coef = '1.0',
											arrondir = 's1',
											ponderation = '0.0',
											display_parents = '0',
											display_bulletin = '1',
											parent = '0'";
									affiche_debug("$sql<br />");
									$reg = mysqli_query($GLOBALS["mysqli"], $sql);
									if ($reg) {
										$id_racine_fut = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
										$sql="UPDATE cn_conteneurs SET id_racine='$id_racine_fut', parent = '0' WHERE id='$id_racine_fut';";
										affiche_debug("$sql<br />");
										$reg = mysqli_query($GLOBALS["mysqli"], $sql);
										// Je ne saisis pas l'intérêt de la requête au-dessus???
										$sql="INSERT INTO cn_cahier_notes SET id_groupe = '".$group_fut['id']."', periode = '$current_periode_num', id_cahier_notes='$id_racine_fut';";
										affiche_debug("$sql<br />");
										$reg = mysqli_query($GLOBALS["mysqli"], $sql);
										echo "Création du carnet de notes pour la période ".$current_periode_num." dans le groupe de $classe_future: ";
										if($reg) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
										echo "<br />\n";
									}
								}
								else {
									// On récupère l'identifiant du carnet de notes du groupe futur
									$lig_ccn_fut=mysqli_fetch_object($res_ccn_fut);
									$id_racine_fut=$lig_ccn_fut->id_cahier_notes;
	
									// On met un statut 'v' pour le nouvel élève sur tous les devoirs existants (avec des notes) du groupe futur
									$sql="SELECT DISTINCT cd.id FROM cn_devoirs cd,
																cn_notes_devoirs cnd
												WHERE (cd.id=cnd.id_devoir AND
													cd.id_racine='$id_racine_fut'
													);";
									affiche_debug("$sql<br />");
									$res_cd_fut=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_cd_fut)>0) {
										while($lig_cd_fut=mysqli_fetch_object($res_cd_fut)) {
											$sql="INSERT INTO cn_notes_devoirs SET id_devoir='$lig_cd_fut->id', login='$login_eleve', statut='v';";
											affiche_debug("$sql<br />");
											$insert_v=mysqli_query($GLOBALS["mysqli"], $sql);
										}
									}
								}
	
								// Boucle sur les devoirs de l'ancien carnet de notes
								while($lig_cd=mysqli_fetch_object($res_cd)) {
									if($lig_cd->statut!='v') {
										// Création du devoir si la note n'est pas vide
										$sql="INSERT INTO cn_devoirs SET id_conteneur='$id_racine_fut',
												id_racine='$id_racine_fut',
												nom_court='".addslashes($lig_cd->nom_court."_".$classe)."',
												nom_complet='".addslashes($lig_cd->nom_complet)."',
												description='".addslashes($lig_cd->description)."',
												facultatif='$lig_cd->facultatif',
												date='$lig_cd->date',
												coef='$lig_cd->coef',
												note_sur='$lig_cd->note_sur',
												ramener_sur_referentiel='$lig_cd->ramener_sur_referentiel',
												display_parents='$lig_cd->display_parents',
												display_parents_app='$lig_cd->display_parents_app';";
										affiche_debug("$sql<br />");
										$reg=mysqli_query($GLOBALS["mysqli"], $sql);
										$id_devoir_fut=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
										echo "Création d'un devoir ".$lig_cd->nom_court."_".$classe." dans le groupe de $classe_future: ";
										if($reg) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
										echo "<br />\n";
	
										if($id_devoir_fut>0) {
											// Insertion du statut 'v' pour tous les autres élèves du groupe futur
											foreach($group_fut['eleves'][$current_periode_num]["list"] as $grp_fut_ele_login) {
												$sql="INSERT INTO cn_notes_devoirs SET login='".$grp_fut_ele_login."', id_devoir='$id_devoir_fut', statut='v';";
												affiche_debug("$sql<br />");
												$reg=mysqli_query($GLOBALS["mysqli"], $sql);
											}
	
											// Insertion de la note:
											$sql="INSERT INTO cn_notes_devoirs SET login='$login_eleve', id_devoir='$id_devoir_fut', note='$lig_cd->note', comment='$lig_cd->comment', statut='$lig_cd->statut';";
											affiche_debug("$sql<br />");
											$reg=mysqli_query($GLOBALS["mysqli"], $sql);
											echo "Insertion de la note du devoir n°".$lig_cd->id." dans le groupe de $classe_future: ";
											if($reg) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
											echo "<br />\n";
										}
									}
	
									// Suppression de la note dans l'ancien carnet de notes
									$sql="DELETE FROM cn_notes_devoirs WHERE login='$login_eleve' AND id_devoir='$lig_cd->id';";
									affiche_debug("$sql<br />");
									$del=mysqli_query($GLOBALS["mysqli"], $sql);
									echo "Suppression de la note du devoir n°".$lig_cd->id.": ";
									if($del) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
									echo "<br />\n";
								}
	
								// Suppression des anciennes notes de conteneurs
								$sql="SELECT * FROM cn_conteneurs WHERE id_racine='".$lig_ccn->id_cahier_notes."';";
								affiche_debug("$sql<br />");
								$res_cn=mysqli_query($GLOBALS["mysqli"], $sql);
	
								if(mysqli_num_rows($res_cn)>0) {
									while($lig_cn=mysqli_fetch_object($res_cn)) {
										$sql="DELETE FROM cn_notes_conteneurs WHERE login='$login_eleve' AND id_conteneur='".$lig_cn->id."';";
										affiche_debug("$sql<br />");
										$del=mysqli_query($GLOBALS["mysqli"], $sql);
										echo "Suppression de la note de $gepi_denom_boite n°".$lig_cn->id.": ";
										if($del) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
										echo "<br />\n";
									}
								}
							}
						}
					}
	
					if($id_grp_fut[$i]!='') {
						// Inscription dans le groupe pour la période
						$sql="INSERT INTO j_eleves_groupes SET login='$login_eleve', id_groupe='".$id_grp_fut[$i]."', periode='$current_periode_num';";
						affiche_debug("$sql<br />");
						$inscription_grp=mysqli_query($GLOBALS["mysqli"], $sql);
						echo "Inscription dans le groupe de la $classe_future sur la période $current_periode_num: ";
						if($inscription_grp) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
						echo "<br />\n";
					}
	
					// Suppression de l'élève de l'ancien groupe
					$sql="DELETE FROM j_eleves_groupes WHERE login='$login_eleve' AND id_groupe='".$id_grp[$i]."' AND periode='$current_periode_num';";
					affiche_debug("$sql<br />");
					$desinscription_grp=mysqli_query($GLOBALS["mysqli"], $sql);
					echo "Suppression de l'appartenance au groupe de la $classe sur la période $current_periode_num: ";
					if($desinscription_grp) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
					echo "<br />\n";
	
					// S'il y avait un ancien carnet de notes... peut-être des moyennes à recalculer
					if(mysqli_num_rows($res_ccn)>0) {
						// Provoquer le recalcul des moyennes de conteneurs sur l'ancienne et la nouvelle classe
						$arret = 'no';
						affiche_debug("Ancien: mise_a_jour_moyennes_conteneurs($group, $current_periode_num,$lig_ccn->id_cahier_notes,$lig_ccn->id_cahier_notes,$arret);<br />");
						mise_a_jour_moyennes_conteneurs($group, $current_periode_num,$lig_ccn->id_cahier_notes,$lig_ccn->id_cahier_notes,$arret);
						recherche_enfant2($lig_ccn->id_cahier_notes, $group, $current_periode_num, $lig_ccn->id_cahier_notes);
						if(($id_grp_fut[$i]!='')&&(isset($group_fut))&&(isset($id_racine_fut))) {
							$arret = 'no';
							affiche_debug("Futur: mise_a_jour_moyennes_conteneurs($group_fut, $current_periode_num,$id_racine_fut,$id_racine_fut,$arret);<br />");
							mise_a_jour_moyennes_conteneurs($group_fut, $current_periode_num,$id_racine_fut,$id_racine_fut,$arret);
							recherche_enfant2($id_racine_fut, $group_fut, $current_periode_num, $id_racine_fut);
						}
					}
				}
			}
			echo "</p>\n";
		}

		affiche_debug("count(\$tab_per)=".count($tab_per)."<br />\n");
		for($j=0;$j<count($tab_per);$j++) {
			affiche_debug("\$tab_per[$j]=$tab_per[$j]<br />");
		}

		for($jj=0;$jj<count($tab_per);$jj++) {
			$current_periode_num=$tab_per[$jj];

			// Inscription dans la nouvelle classe pour la période
			$sql="INSERT INTO j_eleves_classes SET login='$login_eleve', id_classe='".$id_future_classe."', periode='$current_periode_num';";
			affiche_debug("$sql<br />");
			$inscription_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			echo "<p>Inscription dans la classe de $classe_future sur la période $current_periode_num: ";
			if($inscription_classe) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
			echo "<br />\n";
			// Il y aura des rangs à recalculer
			$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_future_classe."' and coef > 0)"));
			$affiche_categories=true;

			// calcul_rang.inc.php a besoin de $id_classe et $periode_num et $test_coef et $affiche_categorie
			$temp_id_classe=$id_classe;
			$id_classe=$id_future_classe;
			$temp_periode_num=$periode_num;
			$periode_num=$current_periode_num;
			include("../lib/calcul_rang.inc.php");
			$id_classe=$temp_id_classe;	
	
			// Désinscription de l'ancienne classe pour la période
			$sql="DELETE FROM j_eleves_classes WHERE login='$login_eleve' AND id_classe='".$id_classe."' AND periode='$current_periode_num';";
			affiche_debug("$sql<br />");
			$desinscription_classe=mysqli_query($GLOBALS["mysqli"], $sql);
			echo "Suppression de l'appartenance à la classe de $classe sur la période $current_periode_num: ";
			if($desinscription_classe) {echo "<span style='color:green'>OK</span>";} else {echo "<span style='color:red'>ECHEC</span>";}
			echo "</p>\n";
			// Il y aura des rangs à recalculer
			$affiche_categories=true;
			$test_coef = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));
			include("../lib/calcul_rang.inc.php");
			$periode_num=$temp_periode_num;
		}
		echo "<p><br /></p>\n";

		// CPE à modifier?
		// Prof principal à modifier?
		$ancre_login_eleve=my_ereg_replace("[^A-Za-z0-9_]","",$login_eleve);
		echo "<p>N'oubliez pas de contrôler/corriger les associations CPE et ".getSettingValue("gepi_prof_suivi")." pour cet élève: <a href='classes_const.php?id_classe=$id_future_classe#$ancre_login_eleve'>$classe_future</a></p>\n";


		// Ménage:
		$sql="SELECT id FROM infos_actions WHERE titre LIKE 'Changement de classe %($login_eleve)';";
		$res_actions=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_actions)>0) {
			while($lig_action=mysqli_fetch_object($res_actions)) {
				$menage=del_info_action($lig_action->id);
				if(!$menage) {$msg.="Erreur lors de la suppression de l'action en attente en page d'accueil à propos de $login_eleve<br />";}
			}
		}

	}
}


echo "<p><br /></p>\n";

echo "<p><i>NOTES:</i></p>
<ul>
<li><p>Les devoirs sont transférés, mais pas les conteneurs/boites/sous-matières.<br />Les notes se retrouvent \"en vrac\" à la racine du cahier de notes du groupe pour la période choisie.</p></li>
<li><p>Avant de procéder au changement de classe, il est bon d'imprimer le relevé de notes de la période pour l'élève (<i>compte scolarité</i>).<br />Effectuer une <a href='../gestion/accueil_sauve.php?action=dump' target='_blank'>sauvegarde préalable de la base</a> est aussi une sage précaution.</p></li>
</ul>\n";
//echo "<p>Il faudrait que le lien de retour mène à la nouvelle classe en fin de procédure et qu'un message invite à vérifier/changer le CPE et prof principal.</p>";

require("../lib/footer.inc.php");
?>

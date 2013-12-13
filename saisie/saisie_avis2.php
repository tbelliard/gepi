<?php
/*
*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$gepiYear = getSettingValue("gepiYear");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

include "../lib/bulletin_simple.inc.php";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// On teste si un professeur peut saisir les avis
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiRubConseilProf")!='yes') {
die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service scolarité peut saisir les avis
if (($_SESSION['statut'] == 'scolarite') and getSettingValue("GepiRubConseilScol")!='yes') {
die("Droits insuffisants pour effectuer cette opération");
}

// On teste si le service cpe peut saisir les avis
if (($_SESSION['statut'] == 'cpe') and getSettingValue("GepiRubConseilCpe")!='yes' and getSettingValue("GepiRubConseilCpeTous")!='yes') {
die("Droits insuffisants pour effectuer cette opération");
}

// initialisation
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :NULL);
$periode_num = isset($_POST["periode_num"]) ? $_POST["periode_num"] :(isset($_GET["periode_num"]) ? $_GET["periode_num"] :NULL);
$fiche = isset($_POST["fiche"]) ? $_POST["fiche"] :(isset($_GET["fiche"]) ? $_GET["fiche"] :NULL);
$current_eleve_login = isset($_POST["current_eleve_login"]) ? $_POST["current_eleve_login"] :(isset($_GET["current_eleve_login"]) ? $_GET["current_eleve_login"] :NULL);
$ind_eleve_login_suiv = isset($_POST["ind_eleve_login_suiv"]) ? $_POST["ind_eleve_login_suiv"] :(isset($_GET["ind_eleve_login_suiv"]) ? $_GET["ind_eleve_login_suiv"] :NULL);
$current_eleve_login_ap = isset($NON_PROTECT["current_eleve_login_ap"]) ? traitement_magic_quotes(corriger_caracteres($NON_PROTECT["current_eleve_login_ap"])) :NULL;
// **** AJOUT POUR LES MENTIONS ****
$current_eleve_mention = isset($_POST["current_eleve_mention"]) ? $_POST["current_eleve_mention"] : NULL;
// **** FIN D'AJOUT POUR LES MENTIONS ****
//================================
// AJOUT: boireaus 20070713
//$current_eleve_login_ap=nl2br($current_eleve_login_ap);
//================================
$affiche_message = isset($_GET["affiche_message"]) ? $_GET["affiche_message"] :NULL;

if(($_SESSION['statut']=='professeur')&&(!is_pp($_SESSION['login'], $id_classe))) {
	header("Location: ../accueil.php?msg=Accès non autorisé.");
	die();
}

include "../lib/periodes.inc.php";

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

//*******************************************************************************************************
$msg = '';

//$acces_classes_acces_appreciations=acces("/classes/acces_appreciations.php", $_SESSION['statut']);
$acces_classes_acces_appreciations=false;
if(($_SESSION['statut']=='administrateur')||
($_SESSION['statut']=='scolarite')||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('GepiAccesRestrAccesAppProfP'))&&(is_pp($_SESSION['login'], $id_classe)))) {
	$acces_classes_acces_appreciations=true;
}

if((isset($id_classe))&&(isset($periode_num))&&(isset($_GET['mode']))&&($_GET['mode']=='modifier_visibilite_parents')&&($acces_classes_acces_appreciations)) {
	check_token();

	$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
	if($acces_app_ele_resp=='manuel') {
		$acces_app_classe=acces_appreciations($periode_num, $periode_num, $id_classe, 'responsable');
		//echo "\$acces_app_classe[$periode_num]=$acces_app_classe[$periode_num]<br />";
		if($acces_app_classe[$periode_num]=="y") {
			$sql="UPDATE matieres_appreciations_acces SET acces='n' WHERE id_classe='$id_classe' AND periode='$periode_num';";
			$msg="L'accès parent/élève n'est pas/plus ouvert pour la période n°$periode_num.<br />";
		}
		else {
			$sql="UPDATE matieres_appreciations_acces SET acces='y' WHERE id_classe='$id_classe' AND periode='$periode_num';";
			$msg="L'accès parent/élève est maintenant ouvert pour la période n°$periode_num.<br />";
		}
		$res=mysql_query($sql);
		if(!$res) {
			$msg="Erreur lors de la modification de la visibilité parent/élève.<br />";
		}
	}
	else {
		$msg="L'accès ou non n'est pas modifié manuellement.<br />";
	}

	if(isset($_GET['mode_js'])) {
		echo "<strong>".$msg."</strong>";
		die();
	}
}

if (isset($_POST['is_posted'])) {
	check_token();
	//echo "PLIP";

	if (($periode_num < $nb_periode) and ($periode_num > 0) and ($ver_periode[$periode_num] != "O"))  {
		$reg = 'yes';
		// si l'utilisateur n'a pas le statut scolarité, on vérifie qu'il est prof principal de l'élève
		//if (($_SESSION['statut'] != 'scolarite') and ($_SESSION['statut'] != 'secours')) {
		if ($_SESSION['statut'] == 'professeur') {
			if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
				// Le prof est PP de la classe, c'est OK
			}
			elseif(is_pp($_SESSION['login'], $id_classe, $current_eleve_login)) {
				// Le prof est PP de cet élève en particulier, c'est OK
			}
			else {
				$msg = "Vous n'êtes pas ".getSettingValue('gepi_prof_suivi')." de cet élève.";
				$reg = 'no';
			}

			// On vérifie que l'élève est bien dans cette classe sur cette période pour éviter qu'un PP mette un avis à un élève qui a changé de classe
			$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='".$current_eleve_login."' AND id_classe='$id_classe' AND periode='$periode_num';";
			$test_ele_clas_per=mysql_query($sql);
			if(mysql_num_rows($test_ele_clas_per)==0) {
				$msg = "L'élève ".get_nom_prenom_eleve($current_eleve_login, "avec_classe")." n'est plus dans la classe de ".get_nom_classe($id_classe)." sur la période $periode_num.";
				$reg = 'no';
			}
		}
		elseif(($_SESSION['statut'] == 'cpe')&&(!getSettingAOui('GepiRubConseilCpeTous'))) {
			$test_cpe_suivi = sql_query1("select 1=1 from j_eleves_cpe
			where e_login = '$current_eleve_login' and
			cpe_login = '".$_SESSION['login']."'
			");
			if ($test_cpe_suivi == '-1') {
				$msg = "Vous n'êtes pas cpe de cet élève.";
				$reg = 'no';
			}
		}

		//echo "PLOP";
		if ($reg == 'yes') {

			$sql="DELETE FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num');";
			$menage=mysql_query($sql);

			if(($current_eleve_login_ap!='')||((isset($current_eleve_mention)&&($current_eleve_mention!=0)))) {
				$sql="INSERT INTO avis_conseil_classe SET login='$current_eleve_login',periode='$periode_num',avis='$current_eleve_login_ap',";
				if(isset($current_eleve_mention)) {$sql.="id_mention='$current_eleve_mention',";}
				$sql.="statut=''";
				$register = mysql_query($sql);

				if (!$register) {
					$msg = "Erreur lors de l'enregistrement des données.";
				} else {
					$affiche_message = 'yes';             }
				}
			}
	} else {
		$msg = "La période sur laquelle vous voulez enregistrer est verrouillée";
	}

	// Passage à l'élève suivant:
	if (isset($_POST['ok1']))  {
		if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours') or (($_SESSION['statut'] == 'cpe')&&(getSettingAOui('GepiRubConseilCpeTous')))) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
			WHERE (
			c.id_classe='$id_classe' AND
			c.login = e.login AND
			c.periode = '".$periode_num."'
			) ORDER BY nom,prenom";
		} elseif($_SESSION['statut'] == 'cpe') {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, j_eleves_cpe jecpe
			WHERE (jec.id_classe='$id_classe' AND
			jec.login = e.login AND
			jecpe.e_login = jec.login AND
			jecpe.cpe_login = '".$_SESSION['login']."' AND
			jec.periode = '".$periode_num."'
			) ORDER BY nom,prenom";
		} else {
			if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
				WHERE (c.id_classe='$id_classe' AND
				c.login = e.login AND
				c.periode = '".$periode_num."'
				) ORDER BY nom,prenom";
			}
			else {
				$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
				WHERE (c.id_classe='$id_classe' AND
				c.login = e.login AND
				p.login = c.login AND
				p.professeur = '".$_SESSION['login']."' AND
				c.periode = '".$periode_num."'
				) ORDER BY nom,prenom";
			}
		}
		//echo "$sql<br />";
		$appel_donnees_eleves = mysql_query($sql);
		$nb_eleve = mysql_num_rows($appel_donnees_eleves);
		$current_eleve_login = @mysql_result($appel_donnees_eleves, $ind_eleve_login_suiv, "login");
		$ind_eleve_login_suiv++;
		if ($ind_eleve_login_suiv >= $nb_eleve)  $ind_eleve_login_suiv = 0;
		//header("Location: saisie_avis2.php?periode_num=$periode_num&amp;id_classe=$id_classe&amp;current_eleve_login=$current_eleve_login&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv&amp;fiche=y&amp;msg=$msg&amp;affiche_message=$affiche_message#app");
		header("Location: saisie_avis2.php?periode_num=$periode_num&id_classe=$id_classe&current_eleve_login=$current_eleve_login&ind_eleve_login_suiv=$ind_eleve_login_suiv&fiche=y&msg=$msg&affiche_message=$affiche_message#app");
	}
}
//*******************************************************************************************************
$message_enregistrement = "Les modifications ont été enregistrées !";
$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
$titre_page = "Saisie des avis | Saisie";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

?>
<script type="text/javascript" language="javascript">
change = 'no';

</script>
<?php

// 20130722
if (isset($id_classe)) {
	if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
			(getSettingAOui('GepiAccesGraphParent'))||
			(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
			(getSettingAOui('GepiAccesGraphEleve'))) {
		$date_du_jour=strftime("%d/%m/%Y");
		// Si les parents ont accès aux bulletins ou graphes,... on va afficher un témoin
		$tab_acces_app_classe=array();
		// L'accès est donné à la même date pour parents et responsables.
		// On teste seulement pour les parents
		$date_ouverture_acces_app_classe=array();
		$tab_acces_app_classe[$id_classe]=acces_appreciations(1, $nb_periode, $id_classe, 'responsable');

		$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
		if($acces_app_ele_resp=='manuel') {
			$msg_acces_app_ele_resp="Les appréciations seront visibles après une intervention manuelle d'un compte de statut 'scolarité'.";
		}
		elseif($acces_app_ele_resp=='date') {
			$chaine_date_ouverture_acces_app_classe="";
			for($loop=0;$loop<count($date_ouverture_acces_app_classe);$loop++) {
				if($loop>0) {
					$chaine_date_ouverture_acces_app_classe.=", ";
				}
				$chaine_date_ouverture_acces_app_classe.=$date_ouverture_acces_app_classe[$loop];
			}
			if($chaine_date_ouverture_acces_app_classe=="") {$chaine_date_ouverture_acces_app_classe="Aucune date n'est encore précisée.
		Peut-être devriez-vous en poser la question à l'administration de l'établissement.";}
			$msg_acces_app_ele_resp="Les appréciations seront visibles soit à une date donnée (".$chaine_date_ouverture_acces_app_classe.").";
		}
		elseif($acces_app_ele_resp=='periode_close') {
			$delais_apres_cloture=getSettingValue('delais_apres_cloture');
			$msg_acces_app_ele_resp="Les appréciations seront visibles ".$delais_apres_cloture." jour(s) après la clôture de la période.";
		}
		else{
			$msg_acces_app_ele_resp="???";
		}
	}
}

// Première étape : la classe est définie, on definit la période
if (isset($id_classe) and (!isset($periode_num))) {
	$classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
	echo "<p class=bold><a href=\"saisie_avis.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Mes classes</a>";
	if((($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesPP')))||
	(($_SESSION['statut']=='scolarite')&&(getSettingAOui('CommentairesTypesScol')))||
	(($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesCpe')))) {
		echo " | <a href='commentaires_types.php'>Saisie de commentaires-types</a>";
	}
	echo "</p>\n";
	echo "<p><b>".$classe_suivi.", choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	echo "<ul>\n";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] != "O") {
			echo "<li><a href='saisie_avis2.php?id_classe=".$id_classe."&amp;periode_num=".$i."'>".ucfirst($nom_periode[$i])."</a>";
		} else {
			echo "<li>".ucfirst($nom_periode[$i])." (".$gepiClosedPeriodLabel.", édition impossible)";
		}

		if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
		(getSettingAOui('GepiAccesGraphParent'))||
		(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
		(getSettingAOui('GepiAccesGraphEleve'))) {
			if($tab_acces_app_classe[$id_classe][$i]=="y") {
				echo " <img src='../images/icons/visible.png' width='19' height='16' alt='Appréciations visibles des parents/élèves.' title='A la date du jour (".$date_du_jour."), les appréciations de la période ".$i." sont visibles des parents/élèves.' />";
			}
			else {
				echo " <img src='../images/icons/invisible.png' width='19' height='16' alt='Appréciations non encore visibles des parents/élèves.' title=\"A la date du jour (".$date_du_jour."), les appréciations de la période ".$i." ne sont pas encore visibles des parents/élèves.
		$msg_acces_app_ele_resp\" />";
			}
		}

		echo ".</li>\n";
		$i++;
	}
	echo "</ul>\n";
}

// Deuxième étape : la classe est définie, la période est définie, on affiche la liste des élèves
if (isset($id_classe) and (isset($periode_num)) and (!isset($fiche))) {
	$classe_suivi = sql_query1("SELECT nom_complet FROM classes WHERE id = '".$id_classe."'");
	?>

	<form enctype="multipart/form-data" action="saisie_avis2.php" name="form1" method='post'>

	<p class=bold><a href="saisie_avis2.php?id_classe=<?php echo $id_classe; ?>"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Choisir une autre période</a>

	<?php

	echo "<input type='hidden' name='periode_num' value='$periode_num' />\n";

// Ajout lien classe précédente / classe suivante
if($_SESSION['statut']=='scolarite'){
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
}
elseif($_SESSION['statut']=='professeur'){
	// On a filtré plus haut les profs qui n'ont pas getSettingValue("GepiRubConseilProf")=='yes'
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c,
										j_eleves_classes jec,
										j_eleves_professeurs jep
								WHERE jec.id_classe=c.id AND
										jep.login=jec.login AND
										jep.professeur='".$_SESSION['login']."'
								ORDER BY c.classe;";
}
elseif($_SESSION['statut']=='cpe'){
	// On ne devrait pas arriver ici en CPE...
	// Il n'y a pas de droit de saisie des avis du conseil.
	$sql="SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_eleves_classes jec, j_eleves_cpe jecpe WHERE
		p.id_classe = c.id AND
		jec.id_classe=c.id AND
		jec.periode=p.num_periode AND
		jecpe.e_login=jec.login AND
		jecpe.cpe_login='".$_SESSION['login']."'
		ORDER BY classe";
}
elseif($_SESSION['statut'] == 'autre'){
	// On recherche toutes les classes pour ce statut qui n'est accessible que si l'admin a donné les bons droits
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
}
elseif($_SESSION['statut'] == 'secours'){
	$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe";
}

$chaine_options_classes="";

$cpt_classe=0;
$num_classe=-1;

$res_class_tmp=mysql_query($sql);
$nb_classes_suivies=mysql_num_rows($res_class_tmp);
if($nb_classes_suivies>0){
	$id_class_prec=0;
	$id_class_suiv=0;
	$temoin_tmp=0;
	while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
		if($lig_class_tmp->id==$id_classe){
			// Index de la classe dans les <option>
			$num_classe=$cpt_classe;

			$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
			$temoin_tmp=1;
			if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
				$id_class_suiv=$lig_class_tmp->id;
			}
			else{
				$id_class_suiv=0;
			}
		}
		else {
			$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
		}
		if($temoin_tmp==0){
			$id_class_prec=$lig_class_tmp->id;
		}

		$cpt_classe++;

	}
}

// =================================
if(isset($id_class_prec)){
	if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";}
}

if(($chaine_options_classes!="")&&($nb_classes_suivies>1)) {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";

	//echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}

if(isset($id_class_suiv)){
	if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}
}
//fin ajout lien classe précédente / classe suivante

if(acces('/impression/avis_pdf.php', $_SESSION['statut'])) {
	echo "| <a href='../impression/avis_pdf.php?id_classe=$id_classe&amp;periode_num=$periode_num'>Impression PDF des avis</a>";
}

if((($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesPP')))||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('CommentairesTypesScol')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesCpe')))) {
	echo " | <a href='commentaires_types.php'>Saisie de commentaires-types</a>";
}

echo "</p>\n";

echo "</form>\n";

	?>

	<p class='grand'>Classe&nbsp;: <strong><?php echo $classe_suivi; ?></strong></p>

	<p>Cliquez sur le nom de l'élève pour lequel vous voulez entrer ou modifier l'appréciation.</p>
	<table class='boireaus' border="1" cellspacing="2" cellpadding="5" width="100%" summary="Choix de l'élève">
	<tr>
		<th width="20%"><b>Nom Prénom</b></th>
		<th<?php
			if(test_existence_mentions_classe($id_classe)) {
				$avec_mentions="y";
			}
			else {
				$avec_mentions="n";
			}

			if($avec_mentions=="y") {
				echo " width='60%'";
			}
		?>>
			<?php
				if($periode_num>1) {
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=".($periode_num-1)."'><img src='../images/icons/back.png' width='16' height='16' title='Afficher la période précédente' /></a> ";
				}
			?>
			<b><?php echo ucfirst($nom_periode[$periode_num]) ; ?> : avis du conseil de classe</b>
			<?php

				// 20130722
				if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
				(getSettingAOui('GepiAccesGraphParent'))||
				(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
				(getSettingAOui('GepiAccesGraphEleve'))) {
					if($tab_acces_app_classe[$id_classe][$periode_num]=="y") {
						echo " <img src='../images/icons/visible.png' width='19' height='16' alt='Appréciations visibles des parents/élèves.' title='A la date du jour (".$date_du_jour."), les appréciations de la période ".$periode_num." sont visibles des parents/élèves.' />";
					}
					else {
						echo " <img src='../images/icons/invisible.png' width='19' height='16' alt='Appréciations non encore visibles des parents/élèves.' title=\"A la date du jour (".$date_du_jour."), les appréciations de la période ".$periode_num." ne sont pas encore visibles des parents/élèves.
				$msg_acces_app_ele_resp\" />";
					}
				}

				if($periode_num<count($nom_periode)) {
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=".($periode_num+1)."'><img src='../images/icons/forward.png' width='16' height='16' title='Afficher la période suivante' /></a> ";
				}
			?>
		</th>
		<?php
			if($avec_mentions=="y") {
				echo "<th><b>".ucfirst($gepi_denom_mention)."</b></th>\n";
			}
		?>
	</tr>
	<?php
	if (($_SESSION['statut'] == 'scolarite') or ($_SESSION['statut'] == 'secours') or (($_SESSION['statut'] == 'cpe')&&(getSettingAOui('GepiRubConseilCpeTous')))) {
		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
		WHERE (c.id_classe='$id_classe' AND
		c.login = e.login AND
		c.periode = '".$periode_num."'
		) ORDER BY nom,prenom";
	} elseif($_SESSION['statut']=='cpe'){
		// On ne devrait pas arriver ici en CPE...
		// Il n'y a pas de droit de saisie des avis du conseil.
		$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec, j_eleves_cpe jecpe
		WHERE (jec.id_classe='$id_classe' AND
		jec.login = e.login AND
		jecpe.e_login = jec.login AND
		jecpe.cpe_login = '".$_SESSION['login']."' AND
		jec.periode = '".$periode_num."'
		) ORDER BY nom,prenom";
	} else {
		if((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $id_classe))) {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c
			WHERE (c.id_classe='$id_classe' AND
			c.login = e.login AND
			c.periode = '".$periode_num."'
			) ORDER BY nom,prenom";
		}
		else {
			$sql="SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c, j_eleves_professeurs p
			WHERE (c.id_classe='$id_classe' AND
			c.login = e.login AND
			p.login = c.login AND
			p.professeur = '".$_SESSION['login']."' AND
			c.periode = '".$periode_num."'
			) ORDER BY nom,prenom";
		}
	}
	//echo "<tr><td colspan='2'>$sql</td></tr>";
	$appel_donnees_eleves = mysql_query($sql);
	$nombre_lignes = mysql_num_rows($appel_donnees_eleves);
	$i = "0";
	$alt=1;
	$tab_mentions_distribuees=array();
	while($i < $nombre_lignes) {
		$current_eleve_login = mysql_result($appel_donnees_eleves, $i, "login");
		$ind_eleve_login_suiv = 0;
		if ($i < $nombre_lignes-1) $ind_eleve_login_suiv = $i+1;
		$current_eleve_nom = mysql_result($appel_donnees_eleves, $i, "nom");
		$current_eleve_prenom = mysql_result($appel_donnees_eleves, $i, "prenom");
		$current_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num')");
		$current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");
		// ***** AJOUT POUR LES MENTIONS *****
        $current_eleve_mention = @mysql_result($current_eleve_avis_query, 0, "id_mention");
		// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n<a href = 'saisie_avis2.php?periode_num=$periode_num&amp;id_classe=$id_classe&amp;fiche=y&amp;current_eleve_login=$current_eleve_login&amp;ind_eleve_login_suiv=$ind_eleve_login_suiv#app'>$current_eleve_nom $current_eleve_prenom</a></td>\n";
		echo "<td><span class=\"medium\">".nl2br($current_eleve_avis)."&nbsp;</span></td>\n";
		if($avec_mentions=="y") {
			// *** AJOUT POUR LES MENTIONS
			echo "<td><span class=\"medium\">";
			$tmp_mention_courante=traduction_mention($current_eleve_mention);
			echo $tmp_mention_courante;

			if(($tmp_mention_courante!='')&&($tmp_mention_courante!='-')) {
				$tab_mentions_distribuees[$current_eleve_mention]['mention']=$tmp_mention_courante;
				if(!isset($tab_mentions_distribuees[$current_eleve_mention]['effectif'])) {$tab_mentions_distribuees[$current_eleve_mention]['effectif']=0;}
				$tab_mentions_distribuees[$current_eleve_mention]['effectif']++;
			}
			echo "</span></td>\n";
			// *** FIN D'AJOUT POUR LES MENTIONS ****
		}
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";

	if(count($tab_mentions_distribuees)>0) {
		echo "<br />\n";
		echo "<p class='bold'>Récapitulatif des mentions distribuées&nbsp;:</p>\n";
		echo "<table class='boireaus'>\n";
		echo "<tr class='lig$alt'>\n";
		echo "<th>Mention</th>\n";
		echo "<th>Effectif</th>\n";
		echo "</tr>\n";
		$alt=1;
		foreach($tab_mentions_distribuees as $tab_mention) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td>".$tab_mention['mention']."</td>\n";
			echo "<td>".$tab_mention['effectif']."</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}

	$sql="SELECT * FROM synthese_app_classe WHERE (id_classe='$id_classe' AND periode='$periode_num');";
	$res_current_synthese=mysql_query($sql);
	$current_synthese= @mysql_result($res_current_synthese, 0, "synthese");
	if ($current_synthese=='') {$current_synthese='-';}

	echo "<br />\n";
	echo "<p><b>Synthèse des avis sur le groupe classe&nbsp;:</b></p>\n";
	echo "<table class='boireaus' border='1' cellspacing='2' cellpadding='5' width='100%' summary='Synthese'>";
	$alt=$alt*(-1);
	echo "<tr class='lig$alt'>\n";
	echo "<td width='20%'>\n<a href='saisie_synthese_app_classe.php?num_periode=$periode_num&amp;id_classe=$id_classe#synthese'>Saisir la synthèse</a></td>\n";
	echo "<td><p class=\"medium\">".nl2br($current_synthese)."</p></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

}


if (isset($fiche)) {

	echo "<p><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;periode_num=$periode_num' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link' /> Retour</a>";
	if((($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesPP')))||
	(($_SESSION['statut']=='scolarite')&&(getSettingAOui('CommentairesTypesScol')))||
	(($_SESSION['statut']=='professeur')&&(getSettingAOui('CommentairesTypesCpe')))) {
		echo " | <a href='commentaires_types.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisie de commentaires-types</a>";
	}
	echo "</p>\n";


/*
	// 20130722
	// Si les parents ont l'accès aux bulletins, graphes,... on affiche s'ils ont l'accès aux appréciations à ce jour
	if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
	(getSettingAOui('GepiAccesGraphParent'))||
	(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
	(getSettingAOui('GepiAccesGraphEleve'))) {
		if($tab_acces_app_classe[$id_classe][$k]=="y") {
			echo " <img src='../images/icons/visible.png' width='19' height='16' alt='Appréciations visibles des parents/élèves.' title='A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." sont visibles des parents/élèves.' />";
		}
		else {
			echo " <img src='../images/icons/invisible.png' width='19' height='16' alt='Appréciations non encore visibles des parents/élèves.' title=\"A la date du jour (".$date_du_jour."), les appréciations de la période ".$k." ne sont pas encore visibles des parents/élèves.
	$msg_acces_app_ele_resp\" />";
		}

*/

	// On teste la présence d'au moins un coeff pour afficher la colonne des coef
	$test_coef = mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef > 0)"));

	// On remonte $affiche_categories au-dessus de include "../lib/calcul_rang.inc.php"; sans quoi il se produit des erreurs.
	$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
	if ($affiche_categories == "y") { $affiche_categories = true; } else { $affiche_categories = false;}

	// on teste si le rang doit être affiché
	$affiche_rang = sql_query1("SELECT display_rang FROM classes WHERE id='".$id_classe."'");

	// Ajout: boireaus (sans cela le rang total n'est pas affiché.)
	if ($affiche_rang == 'y'){
		//include "../lib/calcul_rang.inc.php";}

		$periode_courante=$periode_num;
		$periode_num=1;
		while ($periode_num <= $periode_courante) {
			include "../lib/calcul_rang.inc.php";
			$periode_num++;
		}
		$periode_num=$periode_courante;
	}

	// Variable temporaire utilisée pour conserver le nombre de coef supérieurs à zéro parce que test_coef et réaffecté dans calcul_moy_gen.inc.php
	$nb_coef_superieurs_a_zero=$test_coef;

	//=====================================
	// Ajout pour faire apparaitre la moyenne générale
	//if($test_coef>0) {
	// On ne restreint plus ici: il faut lancer calcul_moy_gen pour extraire les moyennes mêmes si on n'affichera pas les moyennes générales.

		// Mise en réserve de variables modifiées dans le calcul de moyennes générales
		$periode_num_reserve=$periode_num;
		$current_eleve_login_reserve=$current_eleve_login;

		// On réinitialise $current_eleve_login qui est modifié dans le calcul de moyennes générales
		unset($current_eleve_login);

		$display_moy_gen="y";
		$coefficients_a_1="n";
		$affiche_graph="n";

//		unset($tab_moy_gen);
		//unset($tab_moy_cat_classe);
		for($loop=1;$loop<=$periode_num_reserve;$loop++) {
			$periode_num=$loop;
			include "../lib/calcul_moy_gen.inc.php";
//			$tab_moy_gen[$loop]=$moy_generale_classe;


			//==============================================
			//==============================================
			//==============================================
			$tab_moy['periodes'][$periode_num]=array();
			$tab_moy['periodes'][$periode_num]['tab_login_indice']=$tab_login_indice;         // [$login_eleve]
			$tab_moy['periodes'][$periode_num]['moy_gen_eleve']=$moy_gen_eleve;               // [$i]
			$tab_moy['periodes'][$periode_num]['moy_gen_eleve1']=$moy_gen_eleve1;             // [$i]
			//$tab_moy['periodes'][$periode_num]['moy_gen_classe1']=$moy_gen_classe1;           // [$i]
			$tab_moy['periodes'][$periode_num]['moy_generale_classe']=$moy_generale_classe;
			$tab_moy['periodes'][$periode_num]['moy_generale_classe1']=$moy_generale_classe1;
			$tab_moy['periodes'][$periode_num]['moy_max_classe']=$moy_max_classe;
			$tab_moy['periodes'][$periode_num]['moy_min_classe']=$moy_min_classe;
		
			// Il faudrait récupérer/stocker les catégories?
			$tab_moy['periodes'][$periode_num]['moy_cat_eleve']=$moy_cat_eleve;               // [$i][$cat]
			$tab_moy['periodes'][$periode_num]['moy_cat_classe']=$moy_cat_classe;             // [$i][$cat]
			$tab_moy['periodes'][$periode_num]['moy_cat_min']=$moy_cat_min;                   // [$i][$cat]
			$tab_moy['periodes'][$periode_num]['moy_cat_max']=$moy_cat_max;                   // [$i][$cat]
		
			$tab_moy['periodes'][$periode_num]['quartile1_classe_gen']=$quartile1_classe_gen;
			$tab_moy['periodes'][$periode_num]['quartile2_classe_gen']=$quartile2_classe_gen;
			$tab_moy['periodes'][$periode_num]['quartile3_classe_gen']=$quartile3_classe_gen;
			$tab_moy['periodes'][$periode_num]['quartile4_classe_gen']=$quartile4_classe_gen;
			$tab_moy['periodes'][$periode_num]['quartile5_classe_gen']=$quartile5_classe_gen;
			$tab_moy['periodes'][$periode_num]['quartile6_classe_gen']=$quartile6_classe_gen;
			$tab_moy['periodes'][$periode_num]['place_eleve_classe']=$place_eleve_classe;
		
			$tab_moy['periodes'][$periode_num]['current_eleve_login']=$current_eleve_login;   // [$i]
			//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
			//if($loop==$periode1) {
			if($loop==1) {
				$tab_moy['current_group']=$current_group;                                     // [$j]
			}
			$tab_moy['periodes'][$periode_num]['current_eleve_note']=$current_eleve_note;     // [$j][$i]
			$tab_moy['periodes'][$periode_num]['current_eleve_statut']=$current_eleve_statut; // [$j][$i]
			//$tab_moy['periodes'][$periode_num]['current_group']=$current_group;
			$tab_moy['periodes'][$periode_num]['current_coef']=$current_coef;                 // [$j]
			$tab_moy['periodes'][$periode_num]['current_classe_matiere_moyenne']=$current_classe_matiere_moyenne; // [$j]
		
			$tab_moy['periodes'][$periode_num]['current_coef_eleve']=$current_coef_eleve;     // [$i][$j] ATTENTION
			$tab_moy['periodes'][$periode_num]['moy_min_classe_grp']=$moy_min_classe_grp;     // [$j]
			$tab_moy['periodes'][$periode_num]['moy_max_classe_grp']=$moy_max_classe_grp;     // [$j]
			if(isset($current_eleve_rang)) {
				// $current_eleve_rang n'est pas renseigné si $affiche_rang='n'
				$tab_moy['periodes'][$periode_num]['current_eleve_rang']=$current_eleve_rang; // [$j][$i]
			}
			$tab_moy['periodes'][$periode_num]['quartile1_grp']=$quartile1_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['quartile2_grp']=$quartile2_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['quartile3_grp']=$quartile3_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['quartile4_grp']=$quartile4_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['quartile5_grp']=$quartile5_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['quartile6_grp']=$quartile6_grp;               // [$j]
			$tab_moy['periodes'][$periode_num]['place_eleve_grp']=$place_eleve_grp;           // [$j][$i]
		
			$tab_moy['periodes'][$periode_num]['current_group_effectif_avec_note']=$current_group_effectif_avec_note; // [$j]

			//==============================================
			//==============================================
			//==============================================


			//echo "\$id_classe=$id_classe<br />\n";
			//echo "\$periode_num=$periode_num<br />\n";
			//echo "\$moy_generale_classe=$moy_generale_classe<br />\n";
			//echo "\$tab_moy_gen[$loop]=$tab_moy_gen[$loop]<br />\n";
			//$tab_moy_cat_classe
		}

		// Rétablissement des variables après calcul des moyennes générales
		$periode_num=$periode_num_reserve;
		$current_eleve_login=$current_eleve_login_reserve;
	//}

	$test_coef=$nb_coef_superieurs_a_zero;

	//================================================================
	// Graphes
	/*
	echo "<pre>";
	echo print_r($tab_moy);
	echo "</pre>";
	*/
	unset($graphe_chaine_etiquette);
	unset($graphe_chaine_temp);
	unset($graphe_chaine_mgen);

	for($j=0;$j<count($tab_moy['current_group']);$j++) {
		$current_group=$tab_moy['current_group'][$j];

		if(in_array($current_eleve_login, $current_group["eleves"]["all"]["list"])) {

			if(!isset($graphe_chaine_etiquette)) {$graphe_chaine_etiquette="";}
			else {$graphe_chaine_etiquette.="|";}

			$graphe_chaine_etiquette.=$current_group["matiere"]["matiere"];

			for($loop=1;$loop<=$periode_num_reserve;$loop++) {
				if(!isset($graphe_chaine_temp[$loop])) {$graphe_chaine_temp[$loop]="";}
				else {$graphe_chaine_temp[$loop].="|";}

				/*
				if(($j==0)&&($loop==1)) {
				echo "<pre>";
				print_r($tab_moy['periodes'][$loop]);
				echo "</pre>";
				}

				echo "<br /><p>\$current_group : ".$current_group['name']."<br />";
				*/

				// Recherche de l'indice de l'élève:
				for($i=0;$i<count($tab_moy['periodes'][$loop]['current_eleve_login']);$i++) {
					if($tab_moy['periodes'][$loop]['current_eleve_login'][$i]==$current_eleve_login) {
						if(isset($tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i])) {
							//echo "\$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i]=".$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i]."<br />";
							//echo "\$tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i]=".$tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i]."<br />";
							if($tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i]=='') {
								$graphe_chaine_temp[$loop].=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
							}
							else {
								$graphe_chaine_temp[$loop].=$tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i];
							}
						}
						//echo "\$graphe_chaine_temp[$loop]=".$graphe_chaine_temp[$loop]."<br />";

						if(!isset($graphe_chaine_mgen[$loop])) {
							if(is_numeric($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i])) {
								$graphe_chaine_mgen[$loop]=number_format($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i],1);
							}
						}
						break;
					}
				}
			}
		}
	}

	$graphe_chaine_toutes_periodes="titre=Graphe&amp;".
	"v_legend1=".$current_eleve_login."&amp;".
	"v_legend2=Toutes_les_périodes&amp;".
	"compteur=0&amp;".
	"nb_series=".$periode_num_reserve."&amp;".
	"id_classe=".$id_classe."&amp;".
	"largeur_graphe=600&amp;".
	"hauteur_graphe=400&amp;".
	"taille_police=4&amp;".
	"epaisseur_traits=2&amp;".
	"epaisseur_croissante_traits_periodes=non&amp;".
	"tronquer_nom_court=4&amp;".
	"temoin_image_escalier=oui&amp;".
	"etiquette=".$graphe_chaine_etiquette;
	for($loop=1;$loop<=$periode_num_reserve;$loop++) {
		if(isset($graphe_chaine_temp[$loop])) {
			$graphe_chaine_toutes_periodes.="&amp;";
			$graphe_chaine_toutes_periodes.="temp".$loop."=".$graphe_chaine_temp[$loop];
		}

		if(isset($graphe_chaine_mgen[$loop])) {
			$graphe_chaine_toutes_periodes.="&amp;";
			$graphe_chaine_toutes_periodes.="mgen".$loop."=".$graphe_chaine_mgen[$loop];
		}
	}

	/*
	echo "Graphes : ";
	echo "<a href='../visualisation/draw_graphe.php?".
	$graphe_chaine_toutes_periodes.
	"' target='_blank'>Toutes périodes</a>";
	*/

	for($loop=1;$loop<=$periode_num_reserve;$loop++) {
		$graphe_chaine_etiquette="";
		$graphe_chaine_temp_eleve="";
		$graphe_chaine_temp_classe="";
		$graphe_chaine_mgen_eleve="";
		$graphe_chaine_mgen_classe="";
		$graphe_chaine_seriemin="";
		$graphe_chaine_seriemax="";

		// Recherche de l'indice de l'élève:
		$eleve_trouve="n";
		for($i=0;$i<count($tab_moy['periodes'][$loop]['current_eleve_login']);$i++) {
			if($tab_moy['periodes'][$loop]['current_eleve_login'][$i]==$current_eleve_login) {
				$eleve_trouve="y";
				break;
			}
		}

		if($eleve_trouve=="y") {
			/*
			for($j=0;$j<count($tab_moy['current_group']);$j++) {
				$current_group=$tab_moy['current_group'][$j];

				if(in_array($current_eleve_login, $current_group["eleves"][$loop]["list"])) {
					$current_group=$tab_moy['current_group'][$j];

					if($graphe_chaine_etiquette!="") {$graphe_chaine_etiquette.="|";}
					$graphe_chaine_etiquette.=$current_group["matiere"]["matiere"];

					if($graphe_chaine_temp_classe!="") {$graphe_chaine_temp_classe.="|";}
					$graphe_chaine_temp_classe.=$tab_moy['periodes'][$loop]['current_classe_matiere_moyenne'][$j];

					if($graphe_chaine_seriemin!="") {$graphe_chaine_seriemin.="|";}
					$graphe_chaine_seriemin.=$tab_moy['periodes'][$loop]['moy_min_classe_grp'][$j];

					if($graphe_chaine_seriemax!="") {$graphe_chaine_seriemax.="|";}
					$graphe_chaine_seriemax.=$tab_moy['periodes'][$loop]['moy_max_classe_grp'][$j];

					if($graphe_chaine_temp_eleve!="") {$graphe_chaine_temp_eleve.="|";}
					if(isset($tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i])) {
						$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
					}
				}
			}
			*/

			$compteur_groupes_eleve=0;
			for($j=0;$j<count($tab_moy['current_group']);$j++) {
				$current_group=$tab_moy['current_group'][$j];

				if(in_array($current_eleve_login, $current_group["eleves"][$loop]["list"])) {
					$current_group=$tab_moy['current_group'][$j];

					if($compteur_groupes_eleve>0) {
						$graphe_chaine_etiquette.="|";
						$graphe_chaine_temp_classe.="|";
						$graphe_chaine_seriemin.="|";
						$graphe_chaine_seriemax.="|";
						$graphe_chaine_temp_eleve.="|";
					}

					$graphe_chaine_etiquette.=$current_group["matiere"]["matiere"];
					$graphe_chaine_temp_classe.=$tab_moy['periodes'][$loop]['current_classe_matiere_moyenne'][$j];
					$graphe_chaine_seriemin.=$tab_moy['periodes'][$loop]['moy_min_classe_grp'][$j];
					$graphe_chaine_seriemax.=$tab_moy['periodes'][$loop]['moy_max_classe_grp'][$j];

					if(isset($tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i])) {
						//$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
						if($tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i]=='') {
							$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_note'][$j][$i];
						}
						else {
							$graphe_chaine_temp_eleve.=$tab_moy['periodes'][$loop]['current_eleve_statut'][$j][$i];
						}
					}
					$compteur_groupes_eleve++;
				}
			}

			if(is_numeric($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i])) {
				$graphe_chaine_mgen_eleve=number_format($tab_moy['periodes'][$loop]['moy_gen_eleve'][$i],1);
			}
			if(is_numeric($tab_moy['periodes'][$loop]['moy_generale_classe'])) {
				$graphe_chaine_mgen_classe=number_format($tab_moy['periodes'][$loop]['moy_generale_classe'],1);
			}


			$graphe_chaine_periode[$loop]=
			"temp1=".$graphe_chaine_temp_eleve."&amp;".
			"temp2=".$graphe_chaine_temp_classe."&amp;".
			"etiquette=".$graphe_chaine_etiquette."&amp;".
			"titre=Graphe&amp;".
			"v_legend1=".$current_eleve_login."&amp;".
			"v_legend2=moyclasse&amp;".
			"compteur=0&amp;".
			"nb_series=2&amp;".
			"id_classe=".$id_classe."&amp;".
			"largeur_graphe=600&amp;".
			"hauteur_graphe=400&amp;".
			"taille_police=4&amp;".
			"epaisseur_traits=2&amp;".
			"epaisseur_croissante_traits_periodes=non&amp;".
			"tronquer_nom_court=4&amp;".
			"temoin_image_escalier=oui&amp;".
			"seriemin=".$graphe_chaine_seriemin."&amp;".
			"seriemax=".$graphe_chaine_seriemax;
			if(isset($graphe_chaine_mgen_eleve)) {
				$graphe_chaine_periode[$loop].="&amp;"."mgen1=".$graphe_chaine_mgen_eleve;
			}
			if(isset($graphe_chaine_mgen_classe)) {
				$graphe_chaine_periode[$loop].="&amp;"."mgen2=".$graphe_chaine_mgen_classe;
			}
			/*
			echo " - <a href='../visualisation/draw_graphe.php?".
			$graphe_chaine_periode[$loop].
			"' target='_blank'>P".$loop."</a>";
			*/
		}
	}
	//================================================================

	//echo "\$test_coef=$test_coef<br />";
	//=====================================

	$affiche_coef=sql_query1("SELECT display_coef FROM classes WHERE id='".$id_classe."';");

	//bulletin($current_eleve_login,'',0,1,$periode_num,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories);
	bulletin($tab_moy,$current_eleve_login,'',0,1,$periode_num,$nom_periode,$gepiYear,$id_classe,$affiche_rang,$test_coef,$affiche_categories,'y');
	$current_eleve_avis_query = mysql_query("SELECT * FROM avis_conseil_classe WHERE (login='$current_eleve_login' AND periode='$periode_num')");
	$current_eleve_avis = @mysql_result($current_eleve_avis_query, 0, "avis");
	// ***** AJOUT POUR LES MENTIONS *****
	$current_eleve_mention = @mysql_result($current_eleve_avis_query, 0, "id_mention");
	// ***** FIN DE L'AJOUT POUR LES MENTIONS *****
	echo "<form enctype=\"multipart/form-data\" action=\"saisie_avis2.php\" method=\"post\">\n";
	echo add_token_field();
	echo "<table border='0' summary=\"Elève $current_eleve_login\">\n";
	echo "<tr>\n";
	echo "<td>\n";
	echo "<a name=\"app\"></a><textarea name='no_anti_inject_current_eleve_login_ap' id='no_anti_inject_current_eleve_login_ap' rows='5' cols='80' class='wrap' onchange=\"changement()\">";
	echo "$current_eleve_avis";
	echo "</textarea>\n";

	// ***** AJOUT POUR LES MENTIONS *****
	if(test_existence_mentions_classe($id_classe)) {
		echo "<br />\n";
		echo ucfirst($gepi_denom_mention)." : ";
		echo champ_select_mention('current_eleve_mention',$id_classe,$current_eleve_mention);
	}
	// **** FIN DE L'AJOUT POUR LES MENTIONS ****
	echo "</td>\n";


	//==========================
	// Photo
	$sql="SELECT elenoet, nom, prenom, sexe FROM eleves WHERE login='$current_eleve_login';";
	$res_ele=mysql_query($sql);
	$lig_ele=mysql_fetch_object($res_ele);
	$current_eleve_elenoet=$lig_ele->elenoet;
	$current_eleve_nom=$lig_ele->nom;
	$current_eleve_prenom=$lig_ele->prenom;
	$current_eleve_sexe=$lig_ele->sexe;

	// Photo...
	$photo=nom_photo($current_eleve_elenoet);
	$temoin_photo="";
	//if("$photo"!=""){
	if($photo){
		$titre="$current_eleve_nom $current_eleve_prenom";

		$texte="<div align='center'>\n";
		$texte.="<img src='".$photo."' width='150' alt=\"$current_eleve_nom $current_eleve_prenom\" title=\"$current_eleve_nom $current_eleve_prenom\" />";
		$texte.="<br />\n";
		$texte.="</div>\n";

		$temoin_photo="y";

		$tabdiv_infobulle[]=creer_div_infobulle('photo_'.$current_eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

		echo "<td valign='top'>\n";
		//echo " <a href='#' onmouseover=\"afficher_div('photo_$current_eleve_login','y',-100,20);\"";
		//echo " <a href='#' onmouseover=\"delais_afficher_div('photo_$current_eleve_login','y',-100,20,1000,10,10);\"";
		echo " <a href=\"$photo\" onmouseover=\"delais_afficher_div('photo_$current_eleve_login','y',-100,20,1000,10,10);\" onclick=\"afficher_div('photo_$current_eleve_login','y',-100,20); return false;\" target='_blank'";
		echo ">";
			echo "<img src='../mod_trombinoscopes/images/";
			if($current_eleve_sexe=="F") {
				echo "photo_f.png";
			}
			else{
				echo "photo_g.png";
			}
			echo "' class='icone20' alt='$current_eleve_nom $current_eleve_prenom' />";
		echo "</a>";
		echo "</td>\n";
	}
	//==========================


	//============================
	// Pour permettre la saisie de commentaires-type, renseigner la variable $commentaires_types dans /lib/global.inc
	// Et récupérer le paquet commentaires_types sur... ADRESSE A DEFINIR:
	//if((file_exists('saisie_commentaires_types.php'))&&($commentaires_types=='y')){
	if((file_exists('saisie_commentaires_types.php'))
		&&(($_SESSION['statut'] == 'professeur')&&(getSettingValue("GepiRubConseilProf")=='yes')&&(getSettingValue('CommentairesTypesPP')=='yes'))
		||(($_SESSION['statut'] == 'scolarite')&&(getSettingValue("GepiRubConseilScol")=='yes')&&(getSettingValue('CommentairesTypesScol')=='yes'))
		||(($_SESSION['statut'] == 'cpe')&&((getSettingValue("GepiRubConseilCpe")=='yes')||(getSettingValue("GepiRubConseilCpeTous")=='yes'))&&(getSettingValue('CommentairesTypesCpe')=='yes'))) {
		//include('saisie_commentaires_types.php');
		echo "<td align='center'>\n";
		include('saisie_commentaires_types2.php');
		echo "</td>\n";
	}
	//============================
	echo "</tr>\n";
	echo "</table>\n";


	//============================================================
	// Graphes
	$titre_infobulle="$current_eleve_nom $current_eleve_prenom (Toutes périodes)";

	$texte_infobulle="<div align='center'>\n";
	$texte_infobulle.="<img src='../visualisation/draw_graphe.php?".$graphe_chaine_toutes_periodes."' width='600' height='400' alt=\"$current_eleve_nom $current_eleve_prenom (Toutes périodes)\" title=\"$current_eleve_nom $current_eleve_prenom (Toutes périodes)\" />";
	$texte_infobulle.="<br />\n";
	$texte_infobulle.="</div>\n";

	$tabdiv_infobulle[]=creer_div_infobulle('graphe_toutes_periodes_'.$current_eleve_login,$titre_infobulle,"",$texte_infobulle,"",'610px','410px','y','y','n','n');

	echo "Graphes : ";
	echo "<a href='../visualisation/draw_graphe.php?".
	$graphe_chaine_toutes_periodes.
	"' onclick=\"afficher_div('graphe_toutes_periodes_".$current_eleve_login."','y',20,20); return false;\" target='_blank'>Toutes périodes</a>";

	for($loop=1;$loop<=$periode_num_reserve;$loop++) {
		$titre_infobulle="$current_eleve_nom $current_eleve_prenom (Période $loop)";

		$texte_infobulle="<div align='center'>\n";
		$texte_infobulle.="<img src='../visualisation/draw_graphe.php?".$graphe_chaine_periode[$loop]."' width='600' height='400' alt=\"$current_eleve_nom $current_eleve_prenom (Période $loop)\" title=\"$current_eleve_nom $current_eleve_prenom (Période $loop)\" />";
		$texte_infobulle.="<br />\n";
		$texte_infobulle.="</div>\n";

		$tabdiv_infobulle[]=creer_div_infobulle('graphe_periode_'.$loop.'_'.$current_eleve_login,$titre_infobulle,"",$texte_infobulle,"",'610px','410px','y','y','n','n');

		echo " - <a href='../visualisation/draw_graphe.php?".
		$graphe_chaine_periode[$loop].
		"' onclick=\"afficher_div('graphe_periode_".$loop."_".$current_eleve_login."','y',20,20); return false;\" target='_blank'>P".$loop."</a>";
	}
	echo "<br />\n";
	//============================================================
	?>

	<input type=hidden name=id_classe value=<?php echo "$id_classe";?> />
	<input type=hidden name=is_posted value="yes" />
	<input type=hidden name=periode_num value="<?php echo "$periode_num";?>" />
	<input type=hidden name=current_eleve_login value="<?php echo "$current_eleve_login";?>" />
	<input type=hidden name=ind_eleve_login_suiv value="<?php echo "$ind_eleve_login_suiv";?>" />
	<!--br /-->
	<?php
		if($ind_eleve_login_suiv!=0) {
			echo "<input type='submit' NAME='ok1' value=\"Enregistrer et passer à l'élève suivant\" />\n";
		}
	?>
	<input type="submit" NAME="ok2" value="Enregistrer et revenir à la liste" />
	<br /><br />&nbsp;

	<div id="debug_fixe" style="position: fixed; bottom: 20%; right: 5%;"></div>

	</form>
	<?php

		if((getSettingAOui('GepiAccesBulletinSimpleParent'))||
		(getSettingAOui('GepiAccesGraphParent'))||
		(getSettingAOui('GepiAccesBulletinSimpleEleve'))||
		(getSettingAOui('GepiAccesGraphEleve'))) {

			$date_du_jour=strftime("%d/%m/%Y");
			// Si les parents ont accès aux bulletins ou graphes,... on va afficher un témoin
			$tab_acces_app_classe=array();
			// L'accès est donné à la même date pour parents et responsables.
			// On teste seulement pour les parents
			$date_ouverture_acces_app_classe=array();
			$tab_acces_app_classe[$id_classe]=acces_appreciations($periode_num, $periode_num, $id_classe, 'responsable');

			$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
			if($acces_app_ele_resp=='manuel') {
				$msg_acces_app_ele_resp="Les appréciations seront visibles après une intervention manuelle d'un compte de statut 'scolarité'.";
			}
			elseif($acces_app_ele_resp=='date') {
				$chaine_date_ouverture_acces_app_classe="";
				for($loop=0;$loop<count($date_ouverture_acces_app_classe);$loop++) {
					if($loop>0) {
						$chaine_date_ouverture_acces_app_classe.=", ";
					}
					$chaine_date_ouverture_acces_app_classe.=$date_ouverture_acces_app_classe[$loop];
				}
				if($chaine_date_ouverture_acces_app_classe=="") {$chaine_date_ouverture_acces_app_classe="Aucune date n'est encore précisée.
			Peut-être devriez-vous en poser la question à l'administration de l'établissement.";}
				$msg_acces_app_ele_resp="Les appréciations seront visibles soit à une date donnée (".$chaine_date_ouverture_acces_app_classe.").";
			}
			elseif($acces_app_ele_resp=='periode_close') {
				$delais_apres_cloture=getSettingValue('delais_apres_cloture');
				$msg_acces_app_ele_resp="Les appréciations seront visibles ".$delais_apres_cloture." jour(s) après la clôture de la période.";
			}
			else{
				$msg_acces_app_ele_resp="???";
			}

			echo "<div id='div_etat_actuel_visibilite_parent'>\n";
			if($tab_acces_app_classe[$id_classe][$periode_num]=="y") {
				echo "<p>A la date du jour (".$date_du_jour."), les appréciations et avis de la période ".$periode_num." sont visibles des parents/élèves.</p>\n";
			}
			else {
				echo "<p>A la date du jour (".$date_du_jour."), les appréciations et avis de la période ".$periode_num." <strong>ne sont pas encore visibles des parents/élèves</strong>.<br />$msg_acces_app_ele_resp</p>\n";
			}
			echo "</div>\n";

			if(($acces_app_ele_resp=='manuel')&&($acces_classes_acces_appreciations)) {
				echo "<p>Alterner&nbsp;: <a href='./saisie_avis2.php?id_classe=$id_classe&periode_num=$periode_num&mode=modifier_visibilite_parents".add_token_in_url()."' onclick=\"alterner_visibilite_parent();return false;\" target='_blank'>Donner/enlever l'accès</a></p>

<script type='text/javascript'>
	function alterner_visibilite_parent() {

		new Ajax.Updater($('div_etat_actuel_visibilite_parent'),'./saisie_avis2.php?id_classe=$id_classe&periode_num=$periode_num&mode=modifier_visibilite_parents&mode_js=y".add_token_in_url(false)."',{method: 'get'});
	}
</script>\n";
			}

		}


		echo "<script type='text/javascript'>
	if(document.getElementById('no_anti_inject_current_eleve_login_ap')) {
		//alert('1')
		setTimeout(\"document.getElementById('no_anti_inject_current_eleve_login_ap').focus()\",500);
	}

	// onclick='verif_termes()'

	function verif_termes() {
		alert('plop');
		return false;
	}

</script>\n";

}

//**********************************************************************************************************
require("../lib/footer.inc.php");
?>

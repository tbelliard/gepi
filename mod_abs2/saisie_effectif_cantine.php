<?php
/*
*
* Copyright 2001-2012 Thomas Belliard, Stephane Boireau, Eric Lebrun
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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/saisie_effectif_cantine.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/saisie_effectif_cantine.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Effectif cantine: Saisie',
statut='';";
$insert=mysql_query($sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

$msg = '';

if ((isset($_POST['is_posted']))&&($_POST['is_posted']=='saisie_effectif_cantine')&&((is_numeric($id_groupe))||(is_numeric($id_classe)))) {
	check_token();

	$sql="CREATE TABLE IF NOT EXISTS cantine (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
		jour DATE NOT NULL ,
		login VARCHAR( 50 ) NOT NULL ,
		id_groupe INT NOT NULL ,
		id_classe INT NOT NULL ,
		effectif TINYINT NOT NULL ,
		login_ele VARCHAR( 50 ) NOT NULL ,
		instant DATETIME NOT NULL
		) ENGINE = MYISAM ;";
	$create_table=mysql_query($sql);


	$effectif_cantine=isset($_POST['effectif_cantine']) ? $_POST['effectif_cantine'] : NULL;
	$login_eleve_cantine=isset($_POST['login_eleve_cantine']) ? $_POST['login_eleve_cantine'] : array();

	$instant=strftime("%Y-%m-%d %H:%M:%S");

	if(count($login_eleve_cantine)>0) {
		for($i=0;$i<count($login_eleve_cantine);$i++) {
			$sql="INSERT INTO cantine SET jour='".strftime("%Y-%m-%d")."', login='".$_SESSION['login']."', ";
			if(isset($id_groupe)) {$sql.="id_groupe='$id_groupe', ";}
			elseif(isset($id_classe)) {$sql.="id_classe='$id_classe', ";}
			$sql.="effectif='-1', login_ele='".$login_eleve_cantine[$i]."', instant='".$instant."';";
			//echo "$sql<br />";
			$insert=mysql_query($sql);
			if(!$insert) {
				$msg.="Erreur lors de l'enregistrement de ".$login_eleve_cantine[$i].".<br />\n";
			}
		}
	}

	if(!is_numeric($effectif_cantine)) {
		$effectif_cantine=count($login_eleve_cantine);
	}

	$sql="INSERT INTO cantine SET jour='".strftime("%Y-%m-%d")."', login='".$_SESSION['login']."', ";
	if(isset($id_groupe)) {$sql.="id_groupe='$id_groupe', ";}
	elseif(isset($id_classe)) {$sql.="id_classe='$id_classe', ";}
	$sql.="effectif='$effectif_cantine', instant='".$instant."';";
	//echo "$sql<br />";
	$insert=mysql_query($sql);
	if($insert) {
		$msg.="Effectif enregistré: $effectif_cantine<br />\n";
	}
	else {
		$msg.="Erreur lors de l'enregistrement de l'effectif cantine.<br />\n";
	}
}

if((isset($_GET['mode']))&&($_GET['mode']=='suppr_saisie')) {
	check_token();

	$instant=isset($_GET['instant']) ? $_GET['instant'] : NULL;
	$id_groupe=isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL;

	if((isset($id_groupe))&&(isset($instant))) {
		$sql="DELETE FROM cantine WHERE login='".$_SESSION['login']."' AND id_groupe='$id_groupe' AND instant='$instant';";
		$suppr=mysql_query($sql);
		if($suppr) {
			$msg.="Suppression d'un enregistrement cantine<br />";
		}
		else {
			$msg.="Echec de la suppression d'un enregistrement cantine<br />";
		}
	}
}


function saisie_eff_cantine_groupe($id_groupe, $periode_num="", $action="") {

	if($action=="") {$action=$_SERVER['PHP_SELF'];}

	$current_group=get_group($id_groupe);

	$tab_saisie_prec=get_last_effectif_cantine($id_groupe);
	//echo print_r($tab_saisie_prec);
	
	$compteur_dp=0;
	$tab_regime_ele=array();
	$tab_regime=array();
	if($periode_num=="") {
		$sql="SELECT DISTINCT jeg.login, jer.regime FROM j_eleves_regime jer, j_eleves_groupes jeg WHERE jeg.login=jer.login AND jeg.id_groupe='$id_groupe';";
	}
	else {
		$sql="SELECT DISTINCT jeg.login, jer.regime FROM j_eleves_regime jer, j_eleves_groupes jeg WHERE jeg.login=jer.login AND jeg.id_groupe='$id_groupe' AND jeg.periode='$periode_num';";
	}
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$tab_regime_ele[$lig->login]=$lig->regime;
			if(!in_array($lig->regime, $tab_regime)) {
				$tab_regime[]=$lig->regime;
			}

			if($lig->regime=='d/p') {
				$compteur_dp++;
			}
		}
	}

	echo "<form action='".$action."' method='post'>\n";
	echo add_token_field();
	echo "<p class='bold'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</p>\n";
	echo "<p>\n";
	echo "<input type='submit' value='Enregistrer' >\n";
	echo "<br />\n";
	echo "<label for='effectif_cantine'>Saisir juste l'effectif cantine&nbsp;: </label><input type='text' name='effectif_cantine' id='effectif_cantine' value='";
	if(isset($tab_saisie_prec['effectif'])) {echo $tab_saisie_prec['effectif'];}
	echo "' size='3' onkeydown=\"clavier_2(this.id,event,0,";
	if($periode_num=="") {
		echo count($current_group["eleves"]["all"]["list"]);
	}
	else {
		echo count($current_group["eleves"][$periode_num]["list"]);
	}
	echo ");\" autocomplete='off' />\n";
	if($compteur_dp>0) {echo " (<em>".$compteur_dp." demi-pensionnaires dans la base</em>)";}

	echo "<br />\n";
	echo "Ou détailler&nbsp;:<br />\n";
	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th>Nom</th>\n";
	echo "<th>Prénom</th>\n";
	echo "<th>Régime<br />";
	for($i=0;$i<count($tab_regime);$i++) {
		if($i>0) {echo " | ";}
		echo "<a href=\"javascript:coche_regime('".$tab_regime[$i]."')\">".$tab_regime[$i]."</a>";
	}
	echo "</th>\n";
	echo "<th>&nbsp;</th>\n";
	echo "</tr>\n";

	if($periode_num=="") {
		$tab_eleves=$current_group["eleves"]["all"]["users"];
	}
	else {
		$tab_eleves=$current_group["eleves"][$periode_num]["users"];
	}

	$cpt=0;
	$cpt_regime=0;
	$alt=1;
	foreach($tab_eleves as $login_ele => $tab_ele) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td><label for='checkbox_eleve_cantine_$cpt'>".$tab_ele['nom']."</label></td>\n";
		echo "<td><label for='checkbox_eleve_cantine_$cpt'>".$tab_ele['prenom']."</label></td>\n";
		echo "<td>";
		if(isset($tab_regime_ele[$login_ele])) {
			echo "<span id='regime_eleve_$cpt' style='display:none'>".$tab_regime_ele[$login_ele]."</span>\n";
			echo $tab_regime_ele[$login_ele];
		}
		echo "</td>\n";
		echo "<td>";
		echo "<input type='checkbox' name='login_eleve_cantine[]' id='checkbox_eleve_cantine_$cpt' value='".$login_ele."' ";
		echo " onchange='calcule_effectif_cantine()'";
		if((isset($tab_saisie_prec['login_ele']))&&(in_array($login_ele,$tab_saisie_prec['login_ele']))) {echo " checked";}
		echo " />\n";
		echo "</td>\n";
		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";
	echo "<p>Effectif total de l'enseignement&nbsp;: ".count($tab_eleves)."</p>\n";

	echo "<input type='hidden' name='is_posted' value='saisie_effectif_cantine' />\n";
	echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";
	echo "</form>\n";

	echo "<script type='text/javascript'>
	var etat_regime=new Array();

	function coche_regime(chaine) {
		if(etat_regime[chaine]) {
			if(etat_regime[chaine]==1) {
				coche=false;
				etat_regime[chaine]=-1;
			}
			else {
				coche=true;
				etat_regime[chaine]=1;
			}
		}
		else {
			coche=true;
			etat_regime[chaine]=1;
		}

		for(i=0;i<$cpt;i++) {
			if(document.getElementById('regime_eleve_'+i)) {
				if(document.getElementById('regime_eleve_'+i).innerHTML==chaine) {
					if(document.getElementById('checkbox_eleve_cantine_'+i)) {
						document.getElementById('checkbox_eleve_cantine_'+i).checked=coche;
					}
				}
			}
		}

		calcule_effectif_cantine();
	}

	function calcule_effectif_cantine() {
		compteur=0;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('checkbox_eleve_cantine_'+i)) {
				if(document.getElementById('checkbox_eleve_cantine_'+i).checked) {
					compteur++;
				}
			}
		}
		document.getElementById('effectif_cantine').value=compteur;
	}
</script>\n";
}

function get_last_effectif_cantine($id_groupe) {
	$tab=array();

	$sql="SELECT * FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif!='-1' ORDER BY instant DESC LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		$tab['effectif']=$lig->effectif;
	}


	$sql="SELECT DISTINCT instant, login_ele FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif='-1' ORDER BY instant DESC LIMIT 1;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$lig=mysql_fetch_object($res);
		$sql="SELECT * FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif='-1' AND instant='$lig->instant';";
		$res2=mysql_query($sql);
		if(mysql_num_rows($res2)>0) {
			while($lig2=mysql_fetch_object($res2)) {
				$tab['login_ele'][]=$lig2->login_ele;
			}
		}
	}

	return $tab;
}

function show_effectif_cantine($id_groupe) {
	$sql="SELECT DISTINCT instant FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif!='-1' ORDER BY instant;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		while($lig=mysql_fetch_object($res)) {
			$sql="SELECT * FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif!='-1' AND instant='$lig->instant';";
			$res2=mysql_query($sql);

			echo "<p style='text-indent:-2em;margin-left:2em;'><strong>Effectif&nbsp;:</strong> ";
			$lig2=mysql_fetch_object($res2);
			echo "<span title=\"Saisi le ".get_date_heure_from_mysql_date($lig->instant)." par ".civ_nom_prenom($lig2->login)."\">$lig2->effectif</span>";

			if($_SESSION['login']==$lig2->login) {
				//echo "<a href='' title='Éditer la saise'><img src='' width='' height='' /></a>";
				echo " <a href='".$_SERVER['PHP_SELF']."?mode=suppr_saisie&amp;id_groupe=$id_groupe&amp;instant=$lig->instant".add_token_in_url()."' title='Supprimer la saise'><img src='../images/delete16.png' width='16' height='16' /></a>";
			}

			$sql="SELECT DISTINCT login_ele FROM cantine WHERE id_groupe='$id_groupe' AND jour='".strftime("%Y-%m-%d")."' AND effectif='-1' AND instant='$lig->instant';";
			$res3=mysql_query($sql);
			if(mysql_num_rows($res3)>0) {
				while($lig3=mysql_fetch_object($res3)) {
					echo "<br />";
					echo get_nom_prenom_eleve($lig3->login_ele);
				}
			}
			echo "</p>\n";
		}
	}
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
// End standart header
$titre_page = "Cantine";
if(isset ($themessage)) $messageEnregistrer = $themessage;
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='javascript:self.close()'>Fermer pour revenir au module Absences</a>";
if(($_SESSION['statut']!='professeur')) {
	echo " | <a href='consultation_effectif_cantine.php'>Consulter les saisies</a>";
}
echo "</p>\n";

if(!isset($id_groupe)) {
	if($_SESSION['statut']=='professeur') {
		$tab_group=get_groups_for_prof($_SESSION['statut']);
		if(count($tab_group)==0) {
			echo "<p style='color:red'>Aucun enseignement n'a été trouvé pour vous.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		echo "<p>Pour quel enseignement voulez-vous saisir l'effectif cantine?<br />\n";
		foreach($tab_group as $current_group) {
			echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</a><br />";
		}
	}
	else {
		if(!isset($id_classe)) {
			echo "<p style='color:red'>Mode de sélection à traiter pour ce statut...</p>\n";
			// style='text-indent:3em; margin-left:-3em;'
			echo "<p>Pour quelle classe voulez-vous saisir un effectif cantine&nbsp;:<br />\n";
			$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
			$res=mysql_query($sql);
			if(mysql_num_rows($res)>0) {
				$tab_txt=array();
				$tab_lien=array();
				while($lig=mysql_fetch_object($res)) {
					$tab_txt[]=$lig->classe;
					$page=$_SERVER['PHP_SELF'];
					//$page="saisie_effectif_cantine.php";
					$tab_lien[]=$page."?id_classe=$lig->id";
					//echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$lig-id'>$lig->classe</a><br />";
				}
				$nbcol=3;
				tab_liste($tab_txt,$tab_lien,$nbcol);
			}
		}
		else {
			$classe=get_nom_classe($id_classe);
			$groups=get_groups_for_class($id_classe);
			if(count($groups)==0) {
				echo "<p>Aucun groupe pour la classe $classe</p>\n";
				require("../lib/footer.inc.php");
				die();
			}

			echo "<p>Pour quel groupe de la classe $classe voulez-vous saisir l'effectif cantine?</p>\n";
			for($i=0;$i<count($groups);$i++) {
				$current_group=$groups[$i];
				$tab_txt[]=$current_group['name']." (<em>".$current_group['description']." </em>)";
				$tab_lien[]=$_SERVER['PHP_SELF']."?id_groupe=".$current_group['id'];
				//echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$lig-id'>$lig->classe</a><br />";
			}
			$nbcol=3;
			tab_liste($tab_txt,$tab_lien,$nbcol);
		}
	}

	require("../lib/footer.inc.php");
	die();
}

// Vérifier que le groupe est bien associé au prof

echo "<div style='float:right; width:20em;'>\n";
echo "<p class='bold'>Saisies du jour&nbsp;:</p>\n";
show_effectif_cantine($id_groupe);
echo "</div>\n";

saisie_eff_cantine_groupe($id_groupe);

require("../lib/footer.inc.php");
?>

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
}



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/attribuer_copies.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/attribuer_copies.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Attribuer les copies aux professeurs',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

$id_epreuve=isset($_POST['id_epreuve']) ? $_POST['id_epreuve'] : (isset($_GET['id_epreuve']) ? $_GET['id_epreuve'] : NULL);

$definition_salles=isset($_POST['definition_salles']) ? $_POST['definition_salles'] : (isset($_GET['definition_salles']) ? $_GET['definition_salles'] : NULL);

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if(isset($_POST['valide_affect_eleves'])) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$msg="L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
	}
	else {
		$lig=mysqli_fetch_object($res);
		$etat=$lig->etat;
	
		if($etat!='clos') {

			$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());
			$id_prof_ele=isset($_POST['id_prof_ele']) ? $_POST['id_prof_ele'] : (isset($_GET['id_prof_ele']) ? $_GET['id_prof_ele'] : array());
		
			$msg="";
			for($i=0;$i<count($login_ele);$i++) {
				$sql="UPDATE eb_copies SET login_prof='$id_prof_ele[$i]' WHERE id_epreuve='$id_epreuve' AND login_ele='$login_ele[$i]'";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {$msg.="Erreur lors de l'attribution de la copie de '$login_ele[$i]' à '$login_prof[$i]'.<br />";}
			}
			if((count($login_ele)>0)&&($msg=="")) {$msg="Attribution des copies enregistrée.";}
		}
		else {
			$msg="L'épreuve choisie (<i>$id_epreuve</i>) est close.\n";
		}
	}
}

include('lib_eb.php');

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Epreuve blanche: Attribution des copies";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Retour</a>";
echo "</p>\n";
//echo "</div>\n";

//==================================================================

echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig=mysqli_fetch_object($res);
$etat=$lig->etat;

$note_sur=$lig->note_sur;

echo "<blockquote>\n";
echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
if($lig->description!='') {
	echo nl2br(trim($lig->description))."<br />\n";
}
else {
	echo "Pas de description saisie.<br />\n";
}
echo "</blockquote>\n";

$sql="SELECT u.login,u.nom,u.prenom,u.civilite FROM eb_profs ep, utilisateurs u WHERE ep.id_epreuve='$id_epreuve' AND u.login=ep.login_prof ORDER BY u.nom,u.prenom;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>Aucun professeur n'est encore choisi.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//$liste_profs="";
$login_prof=array();
$info_prof=array();
$eff_habituel_prof=array();
while($lig=mysqli_fetch_object($res)) {
	//if($liste_profs!="") {$liste_profs.=",";}
	//$liste_profs.=$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1);
	$login_prof[]=$lig->login;
	$info_prof[]=$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1);

	$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$lig->login."';";
	$res_eff_prof=mysqli_query($GLOBALS["mysqli"], $sql);
	$eff_habituel_prof[]=mysqli_num_rows($res_eff_prof);
}

//$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "groupe");
$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "salle");
$pas_de_salle="n";
$sql="SELECT DISTINCT es.* FROM eb_salles es WHERE id_epreuve='$id_epreuve' ORDER BY es.salle;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	$pas_de_salle="y";
}

if(($tri=='salle')&&($pas_de_salle=="y")) {
	$tri='groupe';
}

echo "<p class='bold'>Trier les élèves par&nbsp;:</p>\n";
echo "<ul>\n";
if($pas_de_salle=="y") {
	echo "<li>Aucune salle n'est encore choisie</li>\n";
}
else {
	echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=salle'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">salle</a></li>\n";
}
echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=groupe'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">groupe/enseignement</a></li>\n";
echo "<li><a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve&amp;tri=n_anonymat'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">numéro anonymat</a></li>\n";
echo "</ul>\n";

if($etat!='clos') {
	echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
	echo add_token_field();
	echo "<input type='hidden' name='tri' value='$tri' />\n";
}

//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves1' value='Valider' /></p>\n";

if($tri=='groupe') {
	$tab_eleves_deja_affiches=array();

	$sql="SELECT DISTINCT g.* FROM eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name, g.description;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune groupe n'est encore associé à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_cpt_eleve=array();
	$tab_groupes=array();
	$cpt=0;
	$compteur_groupe=-1;
	if($etat!='clos') {
		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
	}
	while($lig=mysqli_fetch_object($res)) {
		$tab_cpt_eleve[]=$cpt;

		$compteur_groupe++;

		$tab_groupes[]=$lig->id;
	
		$current_group=get_group($lig->id);

		echo "<p>"."<b>".$current_group['classlist_string']."</b> ".htmlspecialchars($lig->name)." (<i>".htmlspecialchars($lig->description)."</i>) (<i>";
		for($k=0;$k<count($current_group["profs"]["list"]);$k++) {
			if($k>0) {echo ", ";}
			echo get_denomination_prof($current_group["profs"]["list"][$k]);
		}
		echo "</i>)</p>\n";
		echo "<blockquote>\n";
	
		//$sql="SELECT * FROM eb_copies ec, eb_groupes eg WHERE id_epreuve='$id_epreuve' AND...;";
	
		$sql="SELECT ec.login_ele,ec.login_prof FROM eb_copies ec, eb_groupes eg WHERE eg.id_epreuve='$id_epreuve' AND ec.id_epreuve=eg.id_epreuve AND eg.id_groupe='$lig->id';";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	
		$tab_ele_prof=array();
		while($lig2=mysqli_fetch_object($res2)) {
			$tab_ele_prof[$lig2->login_ele]=$lig2->login_prof;
		}

		echo "<table class='boireaus' summary='Choix des élèves du groupe $lig->id'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_groupe,true)'>\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			//echo "<input type='hidden' name='salle[$i]' value='$salle[$i]' />\n";
			// A FAIRE: Afficher effectif
			// style='color:red;'
			//echo "<br />(<span id='eff_prof_".$lig->id."_$i'>Effectif</span>)";

			echo "<br />\n";
			// coche(colonne,rang_groupe,mode)
			//echo "<a href='javascript:coche($i,$compteur_groupe,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			//echo "<a href='javascript:coche($i,$compteur_groupe,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_groupe,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "<br />\n";
		// coche(colonne,rang_groupe,mode)
		//echo "<a href='javascript:coche($i,$compteur_groupe,true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		//echo "<a href='javascript:coche($i,$compteur_groupe,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
		echo "</th>\n";
		echo "</tr>\n";


		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
				echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
				echo "/".$eff_habituel_prof[$i]."\n";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}
	
		$alt=1;
		for($j=0;$j<count($current_group["eleves"]["all"]["list"]);$j++) {
			if(!in_array($current_group["eleves"]["all"]["list"][$j],$tab_eleves_deja_affiches)) {
				$tab_eleves_deja_affiches[]=$current_group["eleves"]["all"]["list"][$j];
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td style='text-align:left;'>\n";
				$login_ele=$current_group["eleves"]["all"]["list"][$j];
				echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
				echo get_nom_prenom_eleve($login_ele);
				echo "</td>\n";
	
				echo "<td>\n";
				$tmp_tab_classe=get_class_from_ele_login($login_ele);
				echo $tmp_tab_classe['liste'];
				echo "</td>\n";
	
				$affect="n";
				for($i=0;$i<count($info_prof);$i++) {
					echo "<td>\n";
					if($etat=='clos') {
						if((isset($tab_ele_prof[$login_ele]))&&($tab_ele_prof[$login_ele]==$login_prof[$i])) {echo "X";$affect="y";}
					}
					else {
						echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
						echo "onchange='calcule_effectif();changement();' ";
						// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
						if((isset($tab_ele_prof[$login_ele]))&&($tab_ele_prof[$login_ele]==$login_prof[$i])) {echo "checked ";$affect="y";}
						echo "/>\n";
					}
					echo "</td>\n";
				}
				echo "<td>\n";
				if($etat=='clos') {
					if($affect=="n") {
						echo "X";
					}
				}
				else {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
					echo "onchange='calcule_effectif();changement();' ";
					if($affect=="n") {echo "checked ";}
					echo "/>\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
				$cpt++;
			}
		}
		echo "</table>\n";
		//$tab_cpt_eleve[]=$cpt;

		echo "</blockquote>\n";

		if($etat!='clos') {
			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
	}

	if($etat!='clos') {
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves2' value='Valider' /></p>\n";
		echo "</form>\n";
		
	
		$chaine_groupes="";
		for($i=0;$i<count($tab_groupes);$i++) {
			if($i>0) {$chaine_groupes.=",";}
			$chaine_groupes.="'$tab_groupes[$i]'";
		}
	
		/*
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			if($i>0) {$chaine_cpt0_eleves.=",";$chaine_cpt1_eleves.=",";}
			$j=2*$i;
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$j]'";
			$j=2*$i+1;
			$chaine_cpt1_eleves.="'$tab_cpt_eleve[$j]'";
		}
		*/
	
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			if($i>1) {$chaine_cpt1_eleves.=",";}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		$chaine_cpt1_eleves.=",'$cpt'";

		echo "<script type='text/javascript'>

function calcule_effectif() {
	var tab_groupes=new Array($chaine_groupes);
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		//alert('Salle i='+i+' eff='+eff)
		for(j=0;j<tab_groupes.length;j++) {
			if(document.getElementById('eff_prof_'+tab_groupes[j]+'_'+i)) {
				document.getElementById('eff_prof_'+tab_groupes[j]+'_'+i).innerHTML=eff;
				//alert('eff_prof_'+tab_groupes[j]+'_'+i+' eff='+eff);
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}

</script>\n";
	}
}
elseif($tri=='n_anonymat') {

	$tab_ele_prof_habituel=array();
	for($i=0;$i<count($login_prof);$i++) {
	
		$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$login_prof[$i]."';";
		$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_prof)>0) {
			while($lig=mysqli_fetch_object($res_ele_prof)) {
				$tab_ele_prof_habituel[$lig->login]=$login_prof[$i];
			}
		}
	}

	$sql="SELECT ec.*, e.nom, e.prenom FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login ORDER BY ec.n_anonymat;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucun élève n'est encore associé à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$cpt=0;
	$tab_eleves=array();
	while($lig=mysqli_fetch_object($res)) {
		$tab_eleves[$cpt]['login_prof']=$lig->login_prof;
		$tab_eleves[$cpt]['login_ele']=$lig->login_ele;
		$tab_eleves[$cpt]['nom']=$lig->nom;
		$tab_eleves[$cpt]['prenom']=$lig->prenom;
		$tab_eleves[$cpt]['n_anonymat']=$lig->n_anonymat;

		$tab_eleves[$cpt]['note']=$lig->note;
		$tab_eleves[$cpt]['statut']=$lig->statut;

		$tab_eleves[$cpt]['note_ou_statut']="";
		if($lig->statut!="v") {
			if($lig->statut!="") {
				$tab_eleves[$cpt]['note_ou_statut']=$lig->statut;
			}
			else {
				$tab_eleves[$cpt]['note_ou_statut']=$lig->note."/".$note_sur;
			}
		}

		$cpt++;
	}

	$largeur_tranche=10;
	$nb_tranches=ceil(count($tab_eleves)/$largeur_tranche);

	$cpt_tranche=0;
	$compteur_eleves_du_prof=array();
	$cpt=0;
	$compteur_tranche=0;

	
	if($etat!='clos') {
		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt_tranche' value='Valider' /></p>\n";
	}

	for($loop=0;$loop<count($tab_eleves);$loop++) {

		if(($loop==0)||($loop%$largeur_tranche==0)) {
			if($loop>0) {
				echo "</blockquote>\n";
			}

			$tab_cpt_eleve[]=$loop;

			$cpt_tranche++;
			$compteur_tranche++;

			$compteur_eleves_dans_la_tranche=1;

			echo "<p class='bold' style='margin-top:1em;'>Tranche $cpt_tranche/$nb_tranches&nbsp;:</p>\n";
			echo "<blockquote>\n";
			echo "<table class='boireaus boireaus_alt' summary='Elèves de la tranche $cpt_tranche'>\n";
			echo "<tr>\n";
			echo "<th title=\"Numéro anonymat\">Numéro</th>\n";
			echo "<th>Elèves</th>\n";
			echo "<th>Classes</th>\n";
			echo "<th title=\"La copie est-elle corrigée ou non\">État</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				$compteur_eleves_du_prof[$i]=0;
				echo "<th>\n";
				if($etat!='clos') {
					echo "<a href='javascript:coche($i,$compteur_tranche,true)'>\n";
					echo "$info_prof[$i]\n";
					echo "</a>\n";
				}
				else {
					echo "$info_prof[$i]\n";
				}
				echo "</th>\n";
			}
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_tranche,true)'>\n";
				echo "Non affecté";
				echo "</a>\n";
			}
			else {
				echo "Non affecté";
			}
			echo "</th>\n";
			echo "</tr>\n";
	
			if($etat!='clos') {
				echo "<tr>\n";
				echo "<th></th>\n";
				echo "<th></th>\n";
				echo "<th>Effectifs</th>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($info_prof);$i++) {
					echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
					//echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
					echo "<span id='eff_prof_".$compteur_tranche."_$i'>Effectif</span>";
					echo "/".$eff_habituel_prof[$i]."\n";
					echo "</th>\n";
				}
				echo "<th>\n";
				//$i++;
				echo "<span id='eff_prof_".$compteur_tranche."_$i'>Effectif</span>";
				echo "</th>\n";
				echo "</tr>\n";
			}
		}

		echo "<tr class='white_hover'>\n";
		echo "<td>".$tab_eleves[$loop]['n_anonymat']."</td>\n";
		echo "<td style='text-align:left;'>\n";
		$login_ele=$tab_eleves[$loop]['login_ele'];
		echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
		//echo get_nom_prenom_eleve($login_ele);
		echo casse_mot($tab_eleves[$loop]['nom'])." ".casse_mot($tab_eleves[$loop]['prenom'],'majf2');
		echo "</td>\n";

		echo "<td>\n";
		$tmp_tab_classe=get_class_from_ele_login($login_ele);
		echo $tmp_tab_classe['liste'];
		echo "</td>\n";

		if($tab_eleves[$loop]['statut']=="v") {
			echo "<td title=\"La copie n'est pas encore corrigée.\">\n";
			echo "</td>\n";
		}
		else {
			echo "<td title=\"La copie est corrigée : ".$tab_eleves[$loop]['note_ou_statut']."\">\n";
			echo "<img src='../images/edit16b.png' class='icone16' />\n";
			echo "</td>\n";
		}

		$affect="n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<td>\n";

			if((isset($tab_ele_prof_habituel[$login_ele]))&&($tab_ele_prof_habituel[$login_ele]==$login_prof[$i])) {
				echo "<div style='float:right; width:17px;'><img src='../images/icons/flag.png' width='17' height='18' title='Professeur habituel de cet élève' alt='Professeur habituel de cet élève' /></div>\n";
				$compteur_eleves_du_prof[$i]++;
			}

			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
				echo "onchange='calcule_effectif();changement();' ";
				// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
				if($tab_eleves[$loop]['login_prof']==$login_prof[$i]) {echo "checked ";$affect="y";}
				echo "/>\n";
			}
			else {
				if($tab_eleves[$loop]['login_prof']==$login_prof[$i]) {echo "X";$affect="y";}
			}

			echo "</td>\n";
		}
		echo "<td>\n";
		if($etat!='clos') {
			echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
			echo "onchange='calcule_effectif();changement();' ";
			if($affect=="n") {echo "checked ";}
			echo "/>\n";
		}
		else {
			if($affect=="n") {echo "X";}
		}
		echo "</td>\n";
		echo "</tr>\n";

		if((($loop>0)&&(($loop+1)%$largeur_tranche==0))||($loop==count($tab_eleves)-1)) {
			echo "<tr>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			echo "<th></th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Le professeur a ".$compteur_eleves_du_prof[$i]." élève(s) en cours parmi les $compteur_eleves_dans_la_tranche de cette tranche\">".$compteur_eleves_du_prof[$i]."/".$compteur_eleves_dans_la_tranche."</th>\n";
			}
			echo "<th></th>\n";
			echo "</tr>\n";
			echo "</table>\n";
		}

		$cpt++;

		$compteur_eleves_dans_la_tranche++;

	}
	echo "</blockquote>\n";



	if($etat!='clos') {
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		echo "</form>\n";

		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			if($i>1) {$chaine_cpt1_eleves.=",";}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		$chaine_cpt1_eleves.=",'$cpt'";

		echo "<script type='text/javascript'>

function calcule_effectif() {
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		//alert('Salle i='+i+' eff='+eff)
		for(j=0;j<=$cpt_tranche;j++) {
			if(document.getElementById('eff_prof_'+j+'_'+i)) {
				document.getElementById('eff_prof_'+j+'_'+i).innerHTML=eff;
				//alert('eff_prof_'+j+'_'+i+' eff='+eff);
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}

</script>\n";
	}
}
elseif($tri=='salle') {

	$tab_ele_prof_habituel=array();
	for($i=0;$i<count($login_prof);$i++) {
	
		$sql="SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg, j_groupes_professeurs jgp, eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id AND jgp.id_groupe=jeg.id_groupe AND jeg.id_groupe=g.id AND jgp.login='".$login_prof[$i]."';";
		$res_ele_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_ele_prof)>0) {
			while($lig=mysqli_fetch_object($res_ele_prof)) {
				$tab_ele_prof_habituel[$lig->login]=$login_prof[$i];
			}
		}
	}

	$sql="SELECT DISTINCT es.* FROM eb_salles es WHERE id_epreuve='$id_epreuve' ORDER BY es.salle;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Aucune salle n'est encore associée à l'épreuve.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_cpt_eleve=array();
	$tab_id_salle=array();
	$tab_salle=array();
	$cpt=0;
	$compteur_salle=-1;
	$compteur_eleves_du_prof=array();
	// Boucle sur les salles
	while($lig=mysqli_fetch_object($res)) {
		$tab_cpt_eleve[]=$cpt;

		$compteur_salle++;

		$tab_salle[]=$lig->salle;
		$tab_id_salle[]=$lig->id;

		if($etat!='clos') {
			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
		echo "<p>Salle <b>$lig->salle</b>&nbsp;:</p>\n";
		echo "<blockquote>\n";

		//echo "\$cpt=$cpt<br />";
		echo "<table class='boireaus' summary='Elèves de la salle $lig->id'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			$compteur_eleves_du_prof[$i]=0;
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			//echo "<input type='hidden' name='salle[$i]' value='$salle[$i]' />\n";
			// A FAIRE: Afficher effectif
			// style='color:red;'
			//echo "<br />(<span id='eff_prof_".$lig->id."_$i'>Effectif</span>)";
			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "</th>\n";
		echo "</tr>\n";
	
		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th title=\"Nombre de copies attribuées à ce professeur par rapport au nombre d'élèves qu'il a en cours.\">\n";
				echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
				echo "/".$eff_habituel_prof[$i]."\n";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_".$lig->id."_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}

		$sql="SELECT ec.*, e.nom, e.prenom FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login AND ec.id_salle='$lig->id' ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res2=mysqli_query($GLOBALS["mysqli"], $sql);
	
		$alt=1;
		//$tab_ele_prof=array();
		$compteur_eleves_dans_la_salle=0;
		while($lig2=mysqli_fetch_object($res2)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:left;'>\n";
			$login_ele=$lig2->login_ele;
			echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
			//echo get_nom_prenom_eleve($login_ele);
			echo casse_mot($lig2->nom)." ".casse_mot($lig2->prenom,'majf2');
			echo "</td>\n";
	
			echo "<td>\n";
			$tmp_tab_classe=get_class_from_ele_login($login_ele);
			echo $tmp_tab_classe['liste'];
			echo "</td>\n";
	
			$affect="n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<td>\n";

				if((isset($tab_ele_prof_habituel[$login_ele]))&&($tab_ele_prof_habituel[$login_ele]==$login_prof[$i])) {
					echo "<div style='float:right; width:17px;'><img src='../images/icons/flag.png' width='17' height='18' title='Professeur habituel de cet élève' alt='Professeur habituel de cet élève' /></div>\n";
					$compteur_eleves_du_prof[$i]++;
				}

				if($etat!='clos') {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
					echo "onchange='calcule_effectif();changement();' ";
					// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
					if($lig2->login_prof==$login_prof[$i]) {echo "checked ";$affect="y";}
					echo "/>\n";
				}
				else {
					if($lig2->login_prof==$login_prof[$i]) {echo "X";$affect="y";}
				}

				echo "</td>\n";
			}
			echo "<td>\n";
			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
				echo "onchange='calcule_effectif();changement();' ";
				if($affect=="n") {echo "checked ";}
				echo "/>\n";
			}
			else {
				if($affect=="n") {echo "X";}
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;

		$compteur_eleves_dans_la_salle++;

		}
		echo "<tr>\n";
		echo "<th></th>\n";
		echo "<th></th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th title=\"Le professeur a ".$compteur_eleves_du_prof[$i]." élève(s) en cours parmi les $compteur_eleves_dans_la_salle de cette salle\">".$compteur_eleves_du_prof[$i]."/".$compteur_eleves_dans_la_salle."</th>\n";
		}
		echo "<th></th>\n";
		echo "</table>\n";
		echo "</tr>\n";
		//echo "\$cpt=$cpt<br />";

		echo "</blockquote>\n";
	}
	if($etat!='clos') {
		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
	}

	$compteur_salle++;
	$tab_salle[]="Non affecté";
	$tab_id_salle[]='na';

	$sql="SELECT ec.*, e.nom, e.prenom FROM eb_copies ec,eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login AND ec.id_salle='-1' ORDER BY e.nom,e.prenom;";
	//echo "$sql<br />";
	$res2=mysqli_query($GLOBALS["mysqli"], $sql);

	if(mysqli_num_rows($res2)==0) {
		echo "<p>Tous les élèves sont affectés dans des salles.</p>\n";
	}
	else {
		echo "<p>Elèves <b>non affectés</b> dans une salle&nbsp;:</p>\n";
		echo "<blockquote>\n";
	
		//echo "\$cpt=$cpt<br />";
		echo "<table class='boireaus' summary='Elèves non affectés'>\n";
		echo "<tr>\n";
		echo "<th>Elèves</th>\n";
		echo "<th>Classes</th>\n";
		for($i=0;$i<count($info_prof);$i++) {
			echo "<th>\n";
			if($etat!='clos') {
				echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
				echo "$info_prof[$i]\n";
				echo "</a>\n";
			}
			else {
				echo "$info_prof[$i]\n";
			}
			echo "</th>\n";
		}
		echo "<th>\n";
		if($etat!='clos') {
			echo "<a href='javascript:coche($i,$compteur_salle,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
		}
		else {
			echo "Non affecté";
		}
		echo "</th>\n";
		echo "</tr>\n";
	
		if($etat!='clos') {
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<th>\n";
				echo "<span id='eff_prof_na_$i'>Effectif</span>";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_prof_na_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
		}
	
		$tab_cpt_eleve[]=$cpt;
		$alt=1;
		//$tab_ele_prof=array();
		while($lig2=mysqli_fetch_object($res2)) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>\n";
			$login_ele=$lig2->login_ele;
			echo "<input type='hidden' name='login_ele[$cpt]' value='$login_ele' />\n";
			//echo get_nom_prenom_eleve($login_ele);
			echo casse_mot($lig2->nom)." ".casse_mot($lig2->prenom,'majf2');
			echo "</td>\n";
	
			echo "<td>\n";
			$tmp_tab_classe=get_class_from_ele_login($login_ele);
			echo $tmp_tab_classe['liste'];
			echo "</td>\n";
	
			$affect="n";
			for($i=0;$i<count($info_prof);$i++) {
				echo "<td>\n";
				if($etat!='clos') {
					echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='$login_prof[$i]' ";
					echo "onchange='calcule_effectif();changement();' ";
					// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
					if($lig2->login_prof==$login_prof[$i]) {echo "checked ";$affect="y";}
					echo "/>\n";
				}
				else {
					if($lig2->login_prof==$login_prof[$i]) {echo "X";$affect="y";}
				}
				echo "</td>\n";
			}
			echo "<td>\n";
			if($etat!='clos') {
				echo "<input type='radio' name='id_prof_ele[$cpt]' id='id_prof_ele_".$i."_$cpt' value='' ";
				echo "onchange='calcule_effectif();changement();' ";
				if($affect=="n") {echo "checked ";}
				echo "/>\n";
			}
			else {
				if($affect=="n") {echo "X";}
			}
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";
		//echo "\$cpt=$cpt<br />";
		$tab_cpt_eleve[]=$cpt;
	
		echo "</blockquote>\n";
		if($etat!='clos') {
			echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
	}

	if($etat!='clos') {
		echo "<input type='hidden' name='tri' value='$tri' />\n";
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		//echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves2' value='Valider' /></p>\n";
		echo "</form>\n";
		
		$chaine_salles="";
		for($i=0;$i<count($tab_id_salle);$i++) {
			if($i>0) {$chaine_salles.=",";}
			$chaine_salles.="'$tab_id_salle[$i]'";
		}
	
		/*
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i+=2) {
			if($i>0) {$chaine_cpt0_eleves.=",";$chaine_cpt1_eleves.=",";}
			//$j=2*$i;
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
			//$j=2*$i+1;
			$j=$i+1;
			$chaine_cpt1_eleves.="'$tab_cpt_eleve[$j]'";
		}
		*/
	
		$chaine_cpt0_eleves="";
		$chaine_cpt1_eleves="";
		for($i=0;$i<count($tab_cpt_eleve);$i++) {
			if($i>1) {
				$chaine_cpt1_eleves.=",";
			}
			if($i>0) {
				$chaine_cpt0_eleves.=",";
				$chaine_cpt1_eleves.="'$tab_cpt_eleve[$i]'";
			}
			$chaine_cpt0_eleves.="'$tab_cpt_eleve[$i]'";
		}
		$chaine_cpt1_eleves.=",'$cpt'";
	
		//echo "\$chaine_cpt0_eleves=$chaine_cpt0_eleves<br />";
		//echo "\$chaine_cpt1_eleves=$chaine_cpt1_eleves<br />";
	
		echo "<script type='text/javascript'>

function calcule_effectif() {
	var tab_salles=new Array($chaine_salles);
	var eff;

	for(i=0;i<".count($login_prof)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_prof_ele_'+i+'_'+j)) {
				if(document.getElementById('id_prof_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		for(j=0;j<tab_salles.length;j++) {
			if(document.getElementById('eff_prof_'+tab_salles[j]+'_'+i)) {
				document.getElementById('eff_prof_'+tab_salles[j]+'_'+i).innerHTML=eff;
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	//for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
	for(k=eval(tab_cpt0_ele[rang_groupe]);k<eval(tab_cpt1_ele[rang_groupe]);k++) {
		if(document.getElementById('id_prof_ele_'+colonne+'_'+k)) {
			document.getElementById('id_prof_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}
</script>\n";
	}
}
else {
	echo "<p>Le mode de tri choisi ne convient pas.</p>\n";
}

//echo "<p style='color:red;'>Ajouter des confirm_abandon() sur les liens.</p>\n";

echo "<p style='color:red;'>A FAIRE: Permettre de ne pas attribuer les copies... pouvoir saisir en piochant dans la liste.</p>\n";

require("../lib/footer.inc.php");
?>

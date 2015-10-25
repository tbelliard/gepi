<?php
/**
 *
 *
 * Copyright 2010 Josselin Jacquard
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//mes fonctions
include("../edt_organisation/fonctions_calendrier.php");
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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_absence")!='2') {
    die("Le module n'est pas activé.");
}

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite") {
    die("acces interdit");
}

// Initialisation des variables
$date_absence_eleve = isset($_POST["date_absence_eleve"]) ? $_POST["date_absence_eleve"] :(isset($_GET["date_absence_eleve"]) ? $_GET["date_absence_eleve"] :(isset($_SESSION["date_absence_eleve"]) ? $_SESSION["date_absence_eleve"] : NULL));
if ($date_absence_eleve != null) {$_SESSION["date_absence_eleve"] = $date_absence_eleve;}
if ($date_absence_eleve != null) {
    $dt_date_absence_eleve = new DateTime(str_replace("/",".",$date_absence_eleve));
} else {
    $dt_date_absence_eleve = new DateTime('now');
}
$choix_creneau = isset($_POST["choix_creneau"]) ? $_POST["choix_creneau"] : (isset($_GET["choix_creneau"]) ? $_GET["choix_creneau"] : null);
if ($choix_creneau === null) {
    $choix_creneau_obj = EdtCreneauPeer::retrieveEdtCreneauActuel();
    if ($choix_creneau_obj != null) {
	$choix_creneau = $choix_creneau_obj->getIdDefiniePeriode();
    }
} else {
    $choix_creneau_obj= EdtCreneauPeer::retrieveByPK($choix_creneau);
}

$avec_js_et_css_edt="y";

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";

// 20150710
$style_specifique[] = "mod_abs2/lib/abs_style";
$javascript_specifique[] = "mod_abs2/lib/include";
$utilisation_scriptaculous="ok";
$utilisation_win = 'oui';

$dojo=true;
//**************** EN-TETE *****************
$titre_page = "Les absences";
require_once("../lib/header.inc.php");
include('menu_abs2.inc.php');
include('menu_bilans.inc.php');
?>
<div id="contain_div" class="css-panes">
<div class="legende">
    <h3 class="legende">Légende  </h3>
    <table class="legende">
        <tr >
            <td width="450px"><h4 class="legende">Saisies  </h4>
            Les saisies de l'élève sur le créneau sont numérotées après son nom. La couleur dépend du type de la saisie.</td>
            <td width="300px">
            <font color="orange">&#9632;</font> Retard<br />
            <font color="red">&#9632;</font> Manquement aux obligations de présence<br />
            <font color="blue">&#9632;</font> Non manquement aux obligations de présence
            </td>
        </tr>
        <tr>
            <td rowspan="2"><h4 class="legende">Appels  </h4>
            La couleur de fond de cellule indique si un appel enseignant a été effectué ou non.
            </td>
            <td style="background-color:#ddd;">
                Appel non fait.
            </td>    
        </tr>
        <tr>
            <td style="background-color:#9f9;">Appel fait.
            </td>
        </tr>
    </table>    
</div>    
<form dojoType="dijit.form.Form" id="choix_du_creneau" name="choix_du_creneau" action="<?php $_SERVER['PHP_SELF']?>" method="post">
<h2>Les appels du
    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve" name="date_absence_eleve" onchange="document.choix_date.submit()" value="<?php echo $dt_date_absence_eleve->format('Y-m-d')?>" />
    <button style="font-size:12px" dojoType="dijit.form.Button" type="submit">Changer</button></h2>

	<p>Vous devez choisir un cr&eacute;neau pour visionner les absents
	<select dojoType="dijit.form.Select"  id="choix_creneau" name="choix_creneau" onchange='document.choix_du_creneau.submit();'>
		<option value="rien">Choix du cr&eacute;neau</option>
<?php
	foreach (EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime() as $edtCreneau)	 {
	    //$edtCreneau = new EdtCreneau();
		if ($edtCreneau->getIdDefiniePeriode() == $choix_creneau) {
			$selected = ' selected="selected"';
		}else{
			$selected = '';
		}
		echo '<option value="'.$edtCreneau->getIdDefiniePeriode().'"'.$selected.'>'.$edtCreneau->getNomDefiniePeriode().'</option>';
	}
?>
	</select>
  </p>
<?php
$creneau_col = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();
foreach ($creneau_col as $creneau) {
    if ($creneau->getPrimaryKey() == $choix_creneau) {
	    $color_selected = 'style="color: red; font-weight: bold;"';
    }else{
	    $color_selected = '';
    }    
    echo '<a href="" '.$color_selected.' onclick="dijit.byId(\'choix_creneau\').attr(\'value\',String('.($creneau->getIdDefiniePeriode()).')); document.choix_du_creneau.submit(); return false;">'.$creneau->getNomDefiniePeriode();
    echo '</a>';
    if (!$creneau_col->isLast()) {
	echo '&nbsp;-&nbsp;';
    }
}
echo add_token_field(true);
?>
</form>
<br />
<?php
if ($choix_creneau_obj != null) {
	echo '<br/>Voir les absences de <span style="color: blue;">'.$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i').'</span> à <span style="color: blue;">'.$choix_creneau_obj->getHeurefinDefiniePeriode('H:i').'</span>.';
?>
<br />

<?php

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {
		$titre_infobulle="EDT de la classe de <span id='span_id_nom_classe'></span>";
		$texte_infobulle="";
		$tabdiv_infobulle[]=creer_div_infobulle('edt_classe',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		echo "<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_en_infobulle(id_classe, classe) {
		document.getElementById('span_id_nom_classe').innerHTML=classe;

		new Ajax.Updater($('edt_classe_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+id_classe+'&type_edt_2=classe&visioedt=classe1&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_classe','y',-20,20);
	}
</script>\n";
	}
?>


<!-- Affichage des réponses-->
<table class="tab_edt" summary="Liste des absents r&eacute;partie par classe">
<?php
// 20130416
$tab_dest_message=array();
$cpt_user=0;

// On affiche la liste des classes
$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->distinct()
					    ->leftJoinWith('Classe.JGroupesClasses')
					    ->leftJoinWith('JGroupesClasses.Groupe')
						->find();
$dt_debut_creneau = clone $dt_date_absence_eleve;
$dt_debut_creneau->setTime($choix_creneau_obj->getHeuredebutDefiniePeriode('H'), $choix_creneau_obj->getHeuredebutDefiniePeriode('i'));
$dt_fin_creneau = clone $dt_date_absence_eleve;
$dt_fin_creneau->setTime($choix_creneau_obj->getHeurefinDefiniePeriode('H'), $choix_creneau_obj->getHeurefinDefiniePeriode('i'));
foreach($classe_col as $classe){
    //$classe = new Classe();
	// On détermine si sur deux colonnes, le compte tombe juste
	$calc = $classe_col->count() / 2;
	$modulo = $classe_col->count() % 2;
	$num_id = 'id'.remplace_accents($classe->getNom(), 'all');
	$id_classe = $classe->getId();
	if ($classe_col->isEven()) {
	    echo '<tr>';
	}
//	echo '	<td>
//			<h4 style="color: red;"><a href="#" onclick="AfficheEdtClasseDuJour(\''.$id_classe.'\',\''.$num_id.'\', 1); return false;">'.$classe->getNom().'</a></h4>
//			<div id="'.$num_id.'" style="display: none; position: absolute; background-color: white; -moz-border-radius: 10px; padding: 10px;">
//			</div>
//		</td>';
	echo '	<td>';
	if((getSettingAOui('active_mod_alerte'))&&(getSettingAOui('PeutPosterMessage'.ucfirst($_SESSION['statut'])))) {
		echo "<div style='float:right; width:16px;'><a href='../mod_alerte/form_message.php?mode=rediger_message&sujet=".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')." : Appel en ".$classe->getNom()."&message=L appel en ".$classe->getNom()." sur le créneau ".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')." n a pas été effectué.' target='_blank'><img src='../images/icons/$icone_deposer_alerte' width='16' height='16' title=\"Rédiger un message à propos de la classe de ".$classe->getNom().".\" /></a></div>";
	}

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {
		echo "<div style='float:left; width:3em;'><a href='../edt_organisation/index_edt.php?login_edt=".$id_classe."&amp;type_edt_2=classe&amp;visioedt=classe1&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_en_infobulle($id_classe, '".$classe->getNom()."');return false;\" title=\"Emploi du temps de la classe de ".$classe->getNom()."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a></div>\n";
	}

	echo '<h4>'.$classe->getNom().'</h4>';
	// 20150710
	echo '<a name="ancre_classe_'.$classe->getId().'"></a>';
	echo '</td>';

	//la classe a-t-elle des cours actuellement ? On récupère la liste des cours pour cette période.
	//on regarde au debut du creneau et a la fin car il peut y avoir des demi creneau
	//on pourrait appeler $classe->getEdtEmplacementCours deux fois mais on va faire une optimisation à la place.
	$edtCoursCol = $classe->getEdtEmplacementCourssPeriodeCalendrierActuelle('now');
	require_once("../orm/helpers/EdtEmplacementCoursHelper.php");
	$cours_col = EdtEmplacementCoursHelper::getColEdtEmplacementCoursActuel($edtCoursCol, $dt_debut_creneau);
	$dt_presque_fin_creneau = clone $dt_fin_creneau;
	$dt_presque_fin_creneau->setTime($choix_creneau_obj->getHeurefinDefiniePeriode('H'), $choix_creneau_obj->getHeurefinDefiniePeriode('i') - 1);
	$cours_col->addCollection( EdtEmplacementCoursHelper::getColEdtEmplacementCoursActuel($edtCoursCol, $dt_presque_fin_creneau));
/*
echo "<td>";
echo "\$dt_presque_fin_creneau=";
echo "<pre>";
echo print_r($dt_presque_fin_creneau);
echo "</pre>";
echo "<br />";
echo "\$dt_debut_creneau=";
echo "<pre>";
echo print_r($dt_debut_creneau);
echo "</pre>";
echo "<br />";
echo "\$dt_fin_creneau=";
echo "<pre>";
echo print_r($dt_fin_creneau);
echo "</pre>";
echo "<br />";
echo "</td>";
*/
	//on teste si l'appel a été fait
	$appel_manquant = false;
	$echo_str = '';
	$classe_deja_sorties = Array();//liste des appels deja affiché sous la form [id_classe, id_utilisateur]
	$groupe_deja_sortis = Array();//liste des appels deja affiché sous la form [id_groupe, id_utilisateur]
	foreach ($cours_col as $edtCours) {//on regarde tous les cours enregistrés dans l'edt
		// 20130416
		$current_cours_appel_manquant=false;

		$echo_str .= "<span title=\"Cours dans l'emploi du temps";
		if ($edtCours->getGroupe() != null) {
			$echo_str .= " (id_groupe:".$edtCours->getIdGroupe().")";
		}
		$echo_str .= "\">";

//$echo_str .= "edt.id_groupe=".$edtCours->getIdGroupe()." - ";

		//$edtCours = new EdtEmplacementCours();
		$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
													->filterByEdtEmplacementCours($edtCours)->find();
		if ($abs_col->isEmpty()) {
			//====================================================
			// Vérifier si le prof a fait l'appel sans passer par le choix "Cours", par exemple par le choix "Groupe"
			$temoin_appel_non_fait_sur_le_groupe_courant=true;
			if ($edtCours->getIdGroupe() != null) {
				$tmp_abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
						->filterByGroupe($classe->getGroupes())
						->find();
				if (!$tmp_abs_col->isEmpty()) {
					foreach ($tmp_abs_col as $abs) {//$abs = new AbsenceEleveSaisie();
/*
if ($abs->getIdGroupe()!=null) {
$echo_str .= "abs.id_groupe=".$abs->getIdGroupe()." - ";
}
*/
						if (($abs->getIdGroupe()!=null)&&($abs->getIdGroupe()==$edtCours->getIdGroupe())) {
							$temoin_appel_non_fait_sur_le_groupe_courant=false;
							break;
						}
					}
				}
			}

			if($temoin_appel_non_fait_sur_le_groupe_courant) {
				$appel_manquant = true;
				$echo_str .= '<span style="color: red;">Non fait</span> - ';
				// 20130416
				$current_cours_appel_manquant=true;
			}
			//====================================================
		} else {
			$echo_str .= $abs_col->getFirst()->getCreatedAt('H:i').' ';
		}

		if ($edtCours->getGroupe() != null) {
			$echo_str .= $edtCours->getGroupe()->getName().' ';
			if ($abs_col->getFirst() !== null) {
				$groupe_deja_sortis[] = Array($edtCours->getIdGroupe(),  $abs_col->getFirst()->getUtilisateurId());
			}
		}
		if ($edtCours->getUtilisateurProfessionnel() != null) {
			$echo_str .= $edtCours->getUtilisateurProfessionnel()->getCivilite().' '
						.$edtCours->getUtilisateurProfessionnel()->getNom().' '
						.strtoupper(mb_substr($edtCours->getUtilisateurProfessionnel()->getPrenom(), 0 ,1)).'. ';
		}
		if ($edtCours->getEdtSalle() != null) {
			$echo_str .= '- <span style="font-style: italic;">('.$edtCours->getEdtSalle()->getNumeroSalle().')</span>';
		}

		// 20130416
		if ($edtCours->getUtilisateurProfessionnel() != null) {
			if($current_cours_appel_manquant) {
				$tab_dest_message[]=$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')."|".$classe->getNom()."|".$edtCours->getUtilisateurProfessionnel()->getLogin();

				if((getSettingAOui('active_mod_alerte'))&&(getSettingAOui('PeutPosterMessage'.ucfirst($_SESSION['statut'])))) {
					$echo_str.=" <span id='span_envoi_message_$cpt_user'><a href='../mod_alerte/form_message.php?message_envoye=y&login_dest=".$edtCours->getUtilisateurProfessionnel()->getLogin()."&sujet=".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')." : Appel en ".$classe->getNom()."&message=Sauf erreur, vous avez oublié de faire l appel en ".$classe->getNom()." sur le créneau ".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i').add_token_in_url()."'
					target='_blank'
					onclick=\"envoi_rappel_appel($cpt_user,
												'".$edtCours->getUtilisateurProfessionnel()->getLogin()."', 
												'".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')." : Appel en ".$classe->getNom()."', 
												'Sauf erreur, vous avez oublié de faire l appel en ".$classe->getNom()." sur le créneau ".$choix_creneau_obj->getHeuredebutDefiniePeriode('H:i')."-".$choix_creneau_obj->getHeurefinDefiniePeriode('H:i')."');return false;\"><img src='../images/icons/$icone_deposer_alerte' width='16' height='16' title='Envoyer un rappel.' /></a></span>";
				}
			}
			$cpt_user++;
		}

		$echo_str .= "</span>";
		$echo_str .= '<br/>';
	}

	//$classe = new Classe();
	//on regarde si il y a d'autres appels
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
			->filterByClasse($classe)->_or()->filterByGroupe($classe->getGroupes())
			->find();
    $test_saisies_sorti=false;
    foreach($abs_col as $abs){
        if($abs->getEleve()!=null && $abs->getEleve()->isEleveSorti($dt_debut_creneau)){
            $test_saisies_sorti=true;
        }else{
            $test_saisies_sorti=false;
            break;
        }
    }

	if ($abs_col->isEmpty()||$test_saisies_sorti) {
		if ($cours_col->isEmpty()) {
			$appel_manquant = true;
			$echo_str .= 'Appel non fait<br/>';
		}
	} else {
		if ($cours_col->isEmpty()) {
			$appel_manquant = false;
		}

		foreach ($abs_col as $abs) {//$abs = new AbsenceEleveSaisie();
			if($abs->getEleve()!=null && $abs->getEleve()->isEleveSorti($dt_debut_creneau)){
				continue;
			}
			$affiche = false;
			if ($abs->getIdClasse()!=null && !in_array(Array($abs->getIdClasse(), $abs->getUtilisateurId()), $classe_deja_sorties)) {
				$echo_str .= $abs->getCreatedAt('H:i').' ';
				$echo_str .= ' '.$abs->getClasse()->getNom().' ';
				$classe_deja_sorties[] = Array($abs->getClasse()->getId(), $abs->getUtilisateurId());
				$affiche = true;
			}
			//if ($abs->getIdGroupe()!=null && !in_array(Array($abs->getIdGroupe(), $abs->getUtilisateurId()), $groupe_deja_sortis) && (!$abs->getEleve())) {
			if ($abs->getIdGroupe()!=null && !in_array(Array($abs->getIdGroupe(), $abs->getUtilisateurId()), $groupe_deja_sortis) && ($abs->getEleve()==null)) {
				$echo_str .= "<span title=\"Saisie/appel pour le groupe n°".$abs->getIdGroupe()."\">";
				//$echo_str .= $abs->getPrimaryKey().' ';
				//$echo_str .= "abs.id_groupe=".$abs->getIdGroupe().' ';
				//if($abs->getEleve()) {$echo_str .= "- ".$abs->getEleve()->getId()." - ";}
				$echo_str .= $abs->getCreatedAt('H:i').' ';
				$echo_str .= ' '.$abs->getGroupe()->getName().' ';
				$echo_str .= "</span>";
				$groupe_deja_sortis[] = Array($abs->getIdGroupe(), $abs->getUtilisateurId());
				$affiche = true;
			}
			if ($affiche) {//on affiche un appel donc on va afficher les infos du prof
				$echo_str .= ' '.$abs->getUtilisateurProfessionnel()->getCivilite().' '
					.$abs->getUtilisateurProfessionnel()->getNom().' '
					.strtoupper(mb_substr($abs->getUtilisateurProfessionnel()->getPrenom(), 0 ,1)).'. ';
				$prof_deja_sortis[] = $abs->getUtilisateurProfessionnel()->getPrimaryKey();
				$echo_str .= '<br/>';
			}
		}
	}
	if ($appel_manquant) {
		echo '<td style="min-width: 350px;">';
	} else {
		echo '<td style="min-width: 350px; background-color:#9f9">';
	}
	echo $echo_str;


	//on affiche les saisies du creneau
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
			->useEleveQuery()->orderByNom()->useJEleveClasseQuery()->filterByClasse($classe)->endUse()->endUse()
			->leftJoinWith('AbsenceEleveSaisie.JTraitementSaisieEleve')
			->leftJoinWith('JTraitementSaisieEleve.AbsenceEleveTraitement')
			->leftJoinWith('AbsenceEleveTraitement.AbsenceEleveType')
			->find();
	//echo $td_classe1[$a].$td_classe[$a];
	if (!$abs_col->isEmpty()) {
	    echo '<br/>';
	}
        $current_eleve=Null;
	foreach ($abs_col as $absenceSaisie) {
        if($absenceSaisie->getEleve()!=null && (
                $absenceSaisie->getEleve()->isEleveSorti($dt_debut_creneau)
                || $absenceSaisie->getEleve()->getClasse($classe->getPeriodeNote($dt_debut_creneau)) != $classe) //on teste la période de l'élève qu'on va afficher
            ) {
            continue;
        }
        if($absenceSaisie->getManquementObligationPresenceSpecifie_NON_PRECISE()){
            continue;
        }
        if ($absenceSaisie->getEleve()->getId() !== $current_eleve) {
            if($current_eleve !=null) echo '<br/>';
            $num_saisie=1;
               if ($utilisateur->getAccesFicheEleve($absenceSaisie->getEleve())) {
                    echo "<a style='color: ".$absenceSaisie->getColor().";' href='../eleves/visu_eleve.php?ele_login=" . $absenceSaisie->getEleve()->getLogin() . "&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank' title=\"Voir la fiche élève (ouverture sur l'onglet Responsable)\">";
                    echo $absenceSaisie->getEleve()->getCivilite() . ' ' . $absenceSaisie->getEleve()->getNom() . ' ' . $absenceSaisie->getEleve()->getPrenom().' : ';
                    echo "</a>";
                } else {
                    echo $absenceSaisie->getEleve()->getCivilite() . ' ' . $absenceSaisie->getEleve()->getNom() . ' ' . $absenceSaisie->getEleve()->getPrenom().' : ';
                }
         }else{
                 echo'-';
             } 
            //echo "<a style='color: ".$absenceSaisie->getColor().";'  href='visu_saisie.php?id_saisie=".$absenceSaisie->getPrimaryKey()."' title=\"Voir la saisie.\">";             

		// 20150710
		echo '<a  style="color: '.$absenceSaisie->getColor().';" href="visu_saisie.php?id_saisie='.$absenceSaisie->getPrimaryKey().'" onClick="javascript:document.getElementById(\'choix_du_creneau\').action=\'tableau_des_appels.php#ancre_classe_'.$classe->getId().'\';showwindow_tda(\'visu_saisie.php?id_saisie='.$absenceSaisie->getPrimaryKey().'&menu=false\',\'Modifier,traiter ou notifier une saisie\');return false;" title="Voir la saisie n°'.$absenceSaisie->getPrimaryKey().'
Du '.get_date_heure_from_mysql_date($absenceSaisie->getDebutAbs()).' au '.get_date_heure_from_mysql_date($absenceSaisie->getFinAbs()).'">';

	    if($num_saisie==1){
                echo ('<b>S</b>aisie '.$num_saisie); 
            }else{
                echo ($num_saisie);
            }           
	    echo "</a>";
	    $current_eleve=$absenceSaisie->getEleve()->getId();
            $num_saisie++;
        if($abs_col->isLast()){
            echo '<br /><br />';
        }    
	}    
	echo '</td>';
	if ($classe_col->isOdd()) {
	    echo '</tr>';
	}else if ($classe_col->isLast()) {
	    echo '<td></td><td></td>';
	    echo '</tr>';
	}
}
?>
	<tr>
		<td>Les Aid</td>
		<td colspan="3">
<?php
	//on affiche les saisies du creneau
	$abs_col = AbsenceEleveSaisieQuery::create()->filterByPlageTemps($dt_debut_creneau, $dt_fin_creneau)
			->filterByIdAid(null, Criteria::NOT_EQUAL)
                        ->useEleveQuery()->orderByNom()->endUse()
			->useAidDetailsQuery()->orderByNom()->endUse()
			->find();
	if (!$abs_col->isEmpty()) {
        $aid_deja_sorties = Array();
        $current_eleve = Null;
        foreach ($abs_col as $absenceSaisie) {
            if($absenceSaisie->getEleve()!=null && $absenceSaisie->getEleve()->isEleveSorti($dt_debut_creneau)){
                continue;
            }
            if ($absenceSaisie->getManquementObligationPresenceSpecifie_NON_PRECISE()) {
                continue;
            }
            if ($absenceSaisie->getIdAid() !== null && !in_array($absenceSaisie->getIdAid(), $aid_deja_sorties)) {
                echo $absenceSaisie->getCreatedAt('H:i') . ' ';
                echo $absenceSaisie->getAidDetails()->getNom() . ' ';
                echo $absenceSaisie->getUtilisateurProfessionnel()->getCivilite() . ' '
                . $absenceSaisie->getUtilisateurProfessionnel()->getNom() . ' '
                . strtoupper(mb_substr($absenceSaisie->getUtilisateurProfessionnel()->getPrenom(), 0, 1)) . '. ';
                $aid_deja_sorties[] = $absenceSaisie->getAidDetails()->getId();
                echo '<br/>';
            }
            if ($absenceSaisie->getEleve() != null) {
                if ($absenceSaisie->getEleve()->getId() !== $current_eleve) {
                    if($current_eleve !=null) echo '<br/>';
                    $num_saisie = 1;
                    if ($utilisateur->getAccesFicheEleve($absenceSaisie->getEleve())) {
                        echo "<a style='color: " . $absenceSaisie->getColor() . ";' href='../eleves/visu_eleve.php?ele_login=" . $absenceSaisie->getEleve()->getLogin() . "&amp;onglet=responsable&amp;quitter_la_page=y' target='_blank'>";
                        echo $absenceSaisie->getEleve()->getCivilite() . ' ' . $absenceSaisie->getEleve()->getNom() . ' ' . $absenceSaisie->getEleve()->getPrenom() . ' : ';
                        echo "</a>";
                    } else {
                        echo $absenceSaisie->getEleve()->getCivilite() . ' ' . $absenceSaisie->getEleve()->getNom() . ' ' . $absenceSaisie->getEleve()->getPrenom() . ' : ';
                    }
                } else {
                    echo'-';
                }
                echo "<a style='color: " . $absenceSaisie->getColor() . ";'  href='visu_saisie.php?id_saisie=" . $absenceSaisie->getPrimaryKey() . "'>";
                echo ($num_saisie);
                echo "</a>";
                $current_eleve = $absenceSaisie->getEleve()->getId();
                $num_saisie++;
                if($abs_col->isLast()){
                    echo '<br /><br />';
               }
            }
        }
    }
?>
		</td>
	</tr>
</table>

<?php
	// 20130416
	if((getSettingAOui('active_mod_alerte'))&&(getSettingAOui('PeutPosterMessage'.ucfirst($_SESSION['statut'])))&&(count($tab_dest_message)>0)) {
		echo "<script type='text/javascript'>
	function envoi_rappel_appel(id, login_dest, sujet, message) {
		csrf_alea=document.getElementById('csrf_alea').value;

		new Ajax.Updater($('span_envoi_message_'+id),'$gepiPath/mod_alerte/form_message.php?envoi_js=y&message_envoye=y&login_dest='+login_dest+'&sujet='+sujet+'&message='+message+'&csrf_alea='+csrf_alea,{method: 'get'});
	}
</script>";

		// A FAIRE: Boucle sur le $tab_dest_message pour envoyer des messages à tous ceux qui ont oublié de faire l'appel.
	}

}
echo '</div>';

// 20150710
echo "<script type='text/javascript'>
	function showwindow_tda(url,title){
		var Height=document.documentElement.clientHeight-75;
		var Width=document.documentElement.clientWidth-75;
		var win = new Window({title: title, width:Width , height:Height, url: url, showEffectOptions: {duration:0.5}}); 
		win.showCenter(); 
		myObserver={
			onClose:function(){
				Windows.removeObserver(this);
				window.document.forms['choix_du_creneau'].submit();
			}
		}
		Windows.addObserver(myObserver); 
	}
</script>";

$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");    
    dojo.require("dijit.form.DateTextBox");    
    dojo.require("dijit.form.Select");
    </script>';
require_once("../lib/footer.inc.php");
?>

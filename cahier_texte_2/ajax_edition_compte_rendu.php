<?php
/*
 * $Id$
 *
 * Copyright 2009 Josselin Jacquard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";
require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
//echo("Debug Locale : ".setLocale(LC_TIME,0));

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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des parametres
//id du compte rendu
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
//si on vient d'enregistrer des modification, on va afficher un message de confirmation
$succes_modification = isset($_POST["succes_modification"]) ? $_POST["succes_modification"] :(isset($_GET["succes_modification"]) ? $_GET["succes_modification"] :NULL);
//si pas de compte rendu passé en paramètre, on récupère la date du jour pour se caler dessus, sinon on prendra la date du compte rendu
$today = isset($_POST["today"]) ? $_POST["today"] :(isset($_GET["today"]) ? $_GET["today"] :NULL);
$ajout_nouvelle_notice = isset($_POST["ajout_nouvelle_notice"]) ? $_POST["ajout_nouvelle_notice"] :(isset($_GET["ajout_nouvelle_notice"]) ? $_GET["ajout_nouvelle_notice"] :NULL);

$ctCompteRendu = CahierTexteCompteRenduPeer::retrieveByPK($id_ct);
if ($ctCompteRendu != null) {
	$groupe = $ctCompteRendu->getGroupe();
	$today = $ctCompteRendu->getDateCt();

	if ($groupe == null) {
		echo("Erreur enregistrement de devoir : Pas de groupe associé au compte-rendu");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}

} else {
	//si pas de notice précisé, récupération du groupe dans la requete et recherche d'une notice pour la date précisée ou création d'une nouvelle notice
	$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
	$groupe = GroupePeer::retrieveByPK($id_groupe);
	if ($groupe == null) {
		echo("Erreur edition de compte rendu : pas de groupe spécifié");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}

	if ($ajout_nouvelle_notice != "oui") {
		//on cherche si il y a une notice pour le groupe à la date précisée
		$criteria = new Criteria(CahierTexteCompteRenduPeer::DATABASE_NAME);
		$criteria->add(CahierTexteCompteRenduPeer::DATE_CT, $today, '=');
		$criteria->add(CahierTexteCompteRenduPeer::ID_LOGIN, $utilisateur->getLogin());
		$ctCompteRendus = $groupe->getCahierTexteCompteRendus($criteria);
		$ctCompteRendu = isset($ctCompteRendus[0]) ? $ctCompteRendus[0] : NULL;
	}
	if ($ctCompteRendu == null) {
		//pas de notices, on initialise un nouvel objet
		$ctCompteRendu = new CahierTexteCompteRendu();
		$ctCompteRendu->setIdGroupe($groupe->getId());
		$ctCompteRendu->setDateCt($today);
		$ctCompteRendu->setIdLogin($utilisateur->getLogin());
	}
}

// Vérification : est-ce que l'utilisateur a le droit de modifier cette entré ?
if((strtolower($ctCompteRendu->getIdLogin()) != strtolower($utilisateur->getLogin()))&&
(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
	echo("Erreur edition de compte rendu : vous n'avez pas le droit de modifier cette notice car elle appartient à un autre professeur.");
	die();
}
if ($ctCompteRendu->getVise() == 'y') {
	// interdire la modification d'un visa par le prof si c'est un visa
	echo("Erreur edition de compte rendu : Notices signée, edition impossible");
	die();
}

// Initialisation du type de couleur (voir global.inc.php)
if ($ctCompteRendu->getDateCt() == null) {
	//CompteRendu information générale
	$info='yes';
	$type_couleur = "i";
} else {
	//CompteRendu normal
	$type_couleur = "c";
}

//on mets le groupe dans le session, pour naviguer entre absence, cahier de texte et autres
$_SESSION['id_groupe_session'] = $ctCompteRendu->getIdGroupe();

// **********************************************
// Affichage des différents groupes du professeur
//\$A($('id_groupe_colonne_gauche').options).find(function(option) { return option.selected; }).value is a javascript trick to get selected value.
echo "<div id=\"div_chaine_edition_notice\" style=\"display:inline;\"><img id=\"chaine_edition_notice\" onLoad=\"updateChaineIcones()\" HEIGHT=\"16\" WIDTH=\"16\" style=\"border: 0px; vertical-align : middle\" src=\"../images/blank.gif\"  alt=\"Lier\" title=\"Lier la liste avec la liste des notices\" /></div>&nbsp;";
echo ("<select id=\"id_groupe_colonne_droite\" onChange=\"javascript:
			updateListeNoticesChaine();
			id_groupe = (\$A($('id_groupe_colonne_droite').options).find(function(option) { return option.selected; }).value);
			getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?today=".$today."&id_groupe=' + id_groupe,
      			 { onComplete:function() {initWysiwyg();}}
      		);
			compte_rendu_en_cours_de_modification('aucun');
		\">");
echo "<option value='-1'>choisissez un groupe</option>\n";
$groups = $utilisateur->getGroupes();
foreach ($groups as $group_iter) {
	echo "<option id='colonne_droite_select_group_option_".$group_iter->getId()."' value='".$group_iter->getId()."'";
	if ($groupe->getId() == $group_iter->getId()) echo " SELECTED ";
	echo ">";
	echo $group_iter->getDescriptionAvecClasses();
	echo "</option>\n";
}
echo "</select>&nbsp;&nbsp;";
//fin affichage des groupes

echo "<button style='background-color:".$color_fond_notices['t']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_devoir.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'devoir';
					\">Editer les devoirs</button>";

echo "<button style='background-color:".$color_fond_notices['p']."' onclick=\"javascript:
						getWinEditionNotice().setAjaxContent('./ajax_edition_notice_privee.php?id_groupe='+ ".$groupe->getId()." + '&today='+getCalendarUnixDate(),{ onComplete:function(transport) {initWysiwyg();}});
						object_en_cours_edition = 'notice_privee';
					\">Editer les notices priv&eacute;es</button><br><br>\n";

// Nombre de notices pour ce jour :
$num_notice = NULL;

echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices[$type_couleur].";\">\n";
if (isset($info)) {
	echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Informations générales - ".$groupe->getNameAvecClasses();
} else {
	echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Compte rendu - ".$groupe->getNameAvecClasses();
}
if (!$ctCompteRendu->isNew() || isset($info)) {
	echo " - <b><font color=\"red\">Modification de la notice</font></b> - 
			<a href=\"#\" onclick=\"javascript:
				getWinEditionNotice().setAjaxContent('ajax_edition_compte_rendu.php?id_groupe=".$groupe->getId()."&today=".$ctCompteRendu->getDateCt()."&ajout_nouvelle_notice=oui',
					{ onComplete:
						function(transport) {
							getWinEditionNotice().updateWidth();
						}
					}
				);
				compte_rendu_en_cours_de_modification('aucun');
				return false;
			\">
			Ajouter une notice
			</a> - ";
	echo "<a href=\"#\" onclick=\"javascript:
				new Ajax.Updater($('dupplication_notice'), 'ajax_affichage_duplication_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteCompteRendu&id_ct=".$ctCompteRendu->getIdCt()."',
					{ onComplete:
						function() {
							$('dupplication_notice').show();
							calendarDuplicationInstanciation = null;";
	if (!isset($info)) {
		//on affiche le calendrier de duplication uniquement si ce n'est pas une notice d'information generale
		echo "calendarDuplicationInstanciation = Calendar.setup({
										flat         : 'calendar-duplication-container', // ID of the parent element
										daFormat     : '%s' ,   			   //date format
										weekNumbers  : false
								})
								calendarDuplicationInstanciation.setDate(calendarInstanciation.date);";
	}
	echo "					
						}
					}
				);
				return false;
				\">
		Dupliquer la notice</a> - ";
} else {
	echo " - <b><font color=\"red\">Nouvelle notice</font></b> - \n";
}
echo "<a href=\"#\" onclick=\"javascript:
			new Ajax.Updater($('deplacement_notice'), 'ajax_affichage_deplacement_notice.php?id_groupe=".$groupe->getId()."&type=CahierTexteCompteRendu&id_ct=".$ctCompteRendu->getIdCt()."',
				{ onComplete:
					function() {
						$('deplacement_notice').show();
						calendarDeplacementInstanciation = null;";
if (!isset($info)) {
	//on affiche le calendrier de duplication uniquement si ce n'est pas une notice d'information generale
	echo "calendarDeplacementInstanciation = Calendar.setup({
									flat         : 'calendar-deplacement-container', // ID of the parent element
									daFormat     : '%s' ,   			   //date format
									weekNumbers  : false
							})
							calendarDeplacementInstanciation.setDate(calendarInstanciation.date);";
}
echo "
					}
				}
			);
			return false;
			\">
	Deplacer la notice</a>";

echo "</legend>\n";

echo "<div id=\"dupplication_notice\" style='display: none;'>oulalala</div>";
echo "<div id=\"deplacement_notice\" style='display: none;'>oulalala</div>";
echo "<form enctype=\"multipart/form-data\" name=\"modification_compte_rendu_form\" id=\"modification_compte_rendu_form\" action=\"ajax_enregistrement_compte_rendu.php\" method=\"post\" onsubmit=\"return AIM.submit(this, {'onComplete' : completeEnregistrementCompteRenduCallback})\" style=\"width: 100%;\">\n";
// uid de pour ne pas refaire renvoyer plusieurs fois le mÃªme formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid = md5(uniqid(microtime(), 1));
echo("<input type='hidden' name='uid_post' value='".$uid."' />");
//hidden input utilise pour indiquer a la fenetre ListeNotice a quel endroit mettre un petit texte rouge "modification"
echo("<input type='hidden' id='div_id_ct' value='compte_rendu_".$ctCompteRendu->getIdCt()."' />");
echo("<input type='hidden' id='id_groupe' name='id_groupe' value='".$groupe->getId()."' />");
echo("<input type='hidden' name='heure_entry' value=\"");
if ($ctCompteRendu->getHeureEntry() == null) {
	echo date('G:i');
} else {
	echo $ctCompteRendu->getHeureEntry();
}
echo "\" />\n";

if (isset($info)) {
	$titre = "Informations Générales : ";
} elseif (!isset($info)) {
	setlocale (LC_TIME, 'fr_FR','fra');
	$titre = strftime("%A %d %B %Y", $ctCompteRendu->getDateCt());
}

//si on vient d'efftuer un enregistrement, le label du bonton enregistrer devient Succès
$label_enregistrer = "Enregistrer";
if ($succes_modification == 'oui') $label_enregistrer='Succès';
?>
<table border="0" width="100%" summary="Tableau de saisie de notice">
	<tr>
		<td style="width: 80%"><b><?php echo $titre; ?></b>&nbsp;
		<button type="submit" id="bouton_enregistrer_1" name="Enregistrer"
			style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
			
		<?php if (!isset($info)) { ?>
		<button type="submit" style='font-variant: small-caps;'
			onClick="javascript:$('passer_a').value = 'passer_devoir';">Enr. et
		passer aux devoirs du lendemain</button>
		<?php } ?>
	
		<input type='hidden' id='passer_a' name='passer_a'
			value='compte_rendu' /> <input type="hidden" name="date_ct"
			value="<?php echo $ctCompteRendu->getDateCt(); ?>" /> <input
			type="hidden" id="id_ct" name="id_ct"
			value="<?php echo $ctCompteRendu->getIdCt(); ?>" /> <input
			type="hidden" name="id_groupe"
			value="<?php echo $groupe->getId(); ?>" /></td>
		<td><?php
		if (!isset($info)) {
			$hier = $today - 3600*24;
			$demain = $today + 3600*24;
			echo "</td><td><a title=\"Aller au jour précédent\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($hier);dateChanged(calendarInstanciation);'>&lt;&lt;</a></td>
			<td align=center>Aujourd'hui</td>
			<td align=right><a title=\"Aller au jour suivant\" href=\"#\" onclick='javascript:updateCalendarWithUnixDate($demain);dateChanged(calendarInstanciation);'>&gt;&gt;</a></td></tr>\n";	echo "\n";
		}
		echo "<tr>
				<td colspan='5'>";

		echo "<textarea name=\"contenu\" style=\"background-color: white;\" id=\"contenu\">".$ctCompteRendu->getContenu()."</textarea>";

		// lancement de FCKeditor
//		$oFCKeditor = new FCKeditor('contenu') ;
//		$oFCKeditor->BasePath = '../fckeditor/' ;
//		$oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
//		$oFCKeditor->ToolbarSet = 'Basic' ;
//		$oFCKeditor->Value = $ctCompteRendu->getContenu() ;
//		$oFCKeditor->Create() ;

		//// gestion des fichiers attaché
		echo '<div style="border-style:solid; border-width:1px; border-color: '.$couleur_bord_tableau_notice.'; background-color: '.$couleur_cellule[$type_couleur].';  padding: 2px; margin: 2px;">';
		echo "<b>Fichier(s) attaché(s) : </b><br />";
		echo '<div id="div_fichier">';
		$architecture= "/documents/cl_dev".$groupe->getId();
		// Affichage des documents joints
		//$document = new CtDocument(); //for ide completion
		$documents = $ctCompteRendu->getCahierTexteCompteRenduFichierJoints();
		echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; background-color: #000000; width: 100%\" cellspacing=\"1\" summary=\"Tableau des documents joints\">\n";
		"<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: $couleur_entete_fond[$type_couleur];\"><td style=\"text-align: center;\"><b>Titre</b></td><td style=\"text-align: center; width: 100px\"><b>Taille en Ko</b></td><td style=\"text-align: center; width: 100px\"></td></tr>\n";
		if (!empty($documents)) {
			foreach ($documents as $document) {
				//if ($ic=='1') { $ic='2'; $couleur_cellule_=$couleur_cellule[$type_couleur]; } else { $couleur_cellule_=$couleur_cellule_alt[$type_couleur]; $ic='1'; }
				echo "<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: #FFFFFF;\"><td>
						<a href='".$document->getEmplacement()."' target=\"_blank\">".$document->getTitre()."</a></td>
						<td style=\"text-align: center;\">".round($document->getTaille()/1024,1)."</td>
						<td style=\"text-align: center;\"><a href='#' onclick=\"javascript:suppressionDocument('suppression du document joint ".$document->getTitre()." ?', '".$document->getId()."', '".$ctCompteRendu->getIdCt()."')\">Supprimer</a></td></tr>\n";
			}
			echo "</table>\n";
			//gestion de modification du nom d'un documents

			echo "Nouveau nom <input type=\"text\" name=\"doc_name_modif\" size=\"25\" /> pour\n";
			echo "<select name=\"id_document\">";
			echo "<option value='-1'>(choisissez)</option>\n";
			foreach ($documents as $document) {
				echo "<option value='".$document->getId()."'>".$document->getTitre()."</option>\n";
			}
			echo "</select>\n<br /><br />";
		}
		?>
		<table style="border-style:solid; border-width:0px; border-color: <?php echo $couleur_bord_tableau_notice;?> ; background-color: #000000; width: 100%" cellspacing="1" summary="Tableau de...">
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
				<td style="font-weight: bold; text-align: center; width: 20%">Titre
				(facultatif)</td>
				<td style="font-weight: bold; text-align: center; width: 60%">Emplacement</td>
			</tr>
			<?php
			$nb_doc_choisi='3';
			$nb_doc_choisi_compte='0';
			while($nb_doc_choisi_compte<$nb_doc_choisi) { ?>
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
				<td style="text-align: center;"><input type="text" name="doc_name[]"
					size="20" /></td>
				<td style="text-align: center;"><input type="file" name="doc_file[]"
					size="20" /></td>
			</tr>
			<?php $nb_doc_choisi_compte++;
			}
			//fin gestion des fichiers attaché
			?>

			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice;?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
				<td colspan="2" style="text-align: center;">
				<button type="submit" id="bouton_enregistrer_2" name="Enregistrer"
					style='font-variant: small-caps;'><?php echo($label_enregistrer); ?></button>
				<?php if (!isset($info)) { ?>
				<button type="submit" style='font-variant: small-caps;'
					onClick="javascript:$('passer_a').value = 'passer_devoir';">Enr. et
				passer aux devoirs du lendemain</button>
				<?php } ?>
				</td>
			</tr>
			<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
				<td colspan="2" style="text-align: center;"><?php  echo "Tous les documents ne sont pas acceptés, voir <a href='javascript:centrerpopup(\"limites_telechargement.php?id_groupe=" . $groupe->getId() . "\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>les limites et restrictions</a>\n"; ?>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
			<?php echo "</form>";
			echo "</fieldset>";
			?>

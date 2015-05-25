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

if ($utilisateur->getStatut()!="cpe" && $utilisateur->getStatut()!="scolarite" && $utilisateur->getStatut()!="administrateur" ) {
    die("acces interdit");
}

include_once 'lib/function.php';

// Initialisation des variables
//récupération des paramètres de la requète
$nom_eleve = isset($_POST["nom_eleve"]) ? $_POST["nom_eleve"] :(isset($_GET["nom_eleve"]) ? $_GET["nom_eleve"] :(isset($_SESSION["nom_eleve"]) ? $_SESSION["nom_eleve"] : NULL));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] :(isset($_GET["id_classe"]) ? $_GET["id_classe"] :(isset($_SESSION["id_classe_abs"]) ? $_SESSION["id_classe_abs"] : NULL));
$date_absence_eleve_debut = isset($_POST["date_absence_eleve_debut"]) ? $_POST["date_absence_eleve_debut"] :(isset($_GET["date_absence_eleve_debut"]) ? $_GET["date_absence_eleve_debut"] :(isset($_SESSION["date_absence_eleve_debut"]) ? $_SESSION["date_absence_eleve_debut"] : NULL));
$date_absence_eleve_fin = isset($_POST["date_absence_eleve_fin"]) ? $_POST["date_absence_eleve_fin"] :(isset($_GET["date_absence_eleve_fin"]) ? $_GET["date_absence_eleve_fin"] :(isset($_SESSION["date_absence_eleve_fin"]) ? $_SESSION["date_absence_eleve_fin"] : NULL));
$type_extrait = isset($_POST["type_extrait"]) ? $_POST["type_extrait"] :(isset($_GET["type_extrait"]) ? $_GET["type_extrait"] : 1);
$affichage = isset($_POST["affichage"]) ? $_POST["affichage"] :(isset($_GET["affichage"]) ? $_GET["affichage"] : NULL);
$page= isset($_REQUEST['page'])?$_REQUEST['page']:0;
$traitement_csv_en_cours= isset($_REQUEST['traitement_csv_en_cours'])?$_REQUEST['traitement_csv_en_cours']:'non_defini';

if (isset($id_classe) && $id_classe != null) $_SESSION['id_classe_abs'] = $id_classe;
if (isset($date_absence_eleve_debut) && $date_absence_eleve_debut != null) $_SESSION['date_absence_eleve_debut'] = $date_absence_eleve_debut;
if (isset($date_absence_eleve_fin) && $date_absence_eleve_fin != null) $_SESSION['date_absence_eleve_fin'] = $date_absence_eleve_fin;

if (($date_absence_eleve_debut != null)&&(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $date_absence_eleve_debut))) {
    $dt_date_absence_eleve_debut = new DateTime(str_replace("/",".",$date_absence_eleve_debut));
} else {
    $dt_date_absence_eleve_debut = new DateTime('now');
    $dt_date_absence_eleve_debut->setDate($dt_date_absence_eleve_debut->format('Y'), $dt_date_absence_eleve_debut->format('m') - 1, $dt_date_absence_eleve_debut->format('d'));
}
if (($date_absence_eleve_fin != null)&&(preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}$#", $date_absence_eleve_fin))) {
    $dt_date_absence_eleve_fin = new DateTime(str_replace("/",".",$date_absence_eleve_fin));
} else {
    $dt_date_absence_eleve_fin = new DateTime('now');
}
$dt_date_absence_eleve_debut->setTime(0,0,0);
$dt_date_absence_eleve_fin->setTime(23,59,59);

$type_saisie=isset($_POST['type_saisie']) ? $_POST['type_saisie'] : "";
if (($type_saisie != "")&&($type_saisie != "SANS")) {
	$type_extrait=2;
}

$style_specifique[] = "edt_organisation/style_edt";
$style_specifique[] = "templates/DefaultEDT/css/small_edt";
$style_specifique[] = "mod_abs2/lib/abs_style";
//$javascript_specifique[] = "mod_abs2/lib/include";
$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
$dojo=true;
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Les absences";
if ($affichage != 'ods') {// on affiche pas de html
    require_once("../lib/header.inc.php");
//******************************************

    if ($traitement_csv_en_cours != 'true') {

	    include('menu_abs2.inc.php');
	    include('menu_bilans.inc.php');

		//debug_var();

		// Semaine précédente,...
		$ts_debut_semaine=time()-strftime("%u")*24*3600;
		$ts_fin_semaine=$ts_debut_semaine+7*24*3600;
		$ts_debut_semaine_prec=$ts_debut_semaine-7*24*3600;
		$ts_fin_semaine_prec=$ts_debut_semaine_prec+7*24*3600;

	    ?>
	    <div id="contain_div" class="css-panes">

		<form name='form_change_date' id='form_change_date' action="<?php $_SERVER['PHP_SELF']?>" method="post">
			<input type='hidden' name='date_absence_eleve_debut' id='date_absence_eleve_debut_2' value="" />
			<input type='hidden' name='date_absence_eleve_fin' id='date_absence_eleve_fin_2' value="" />
			<input type='hidden' name='nom_eleve' id='nom_eleve_2' value="" />
			<input type='hidden' name='id_classe' id='id_classe_2' value="" />
			<input type='hidden' name='type_extrait' id='type_extrait_2' value="" />
			<input type='hidden' name='type_saisie' id='type_saisie_2' value="" />
		</form>
		<script type='text/javascript'>
			function raffraichit_a_aujourdhui() {
				document.getElementById('date_absence_eleve_debut_2').value="<?php echo strftime('%d/%m/%Y');?>";
				document.getElementById('date_absence_eleve_fin_2').value="<?php echo strftime('%d/%m/%Y', time()+3600*24);?>";
				// Dojo ne semble pas permettre non plus de récupérer les champs qui suivent
				document.getElementById('nom_eleve_2').value=document.getElementById('nom_eleve').value;
				document.getElementById('id_classe_2').value=document.getElementById('id_classe').value;
				document.getElementById('type_extrait_2').value=document.getElementById('type_extrait').value;
				document.getElementById('type_saisie_2').value=document.getElementById('type_saisie').value;

				document.getElementById('form_change_date').submit();
			}
			/*
			function raffraichit_7j() {
				document.getElementById('date_absence_eleve_debut_2').value="<?php echo strftime('%d/%m/%Y', time()-7*3600*24);?>";
				document.getElementById('date_absence_eleve_fin_2').value="<?php echo strftime('%d/%m/%Y', time()+3600*24);?>";
				// Dojo ne semble pas permettre non plus de récupérer les champs qui suivent
				document.getElementById('nom_eleve_2').value=document.getElementById('nom_eleve').value;
				document.getElementById('id_classe_2').value=document.getElementById('id_classe').value;
				document.getElementById('type_extrait_2').value=document.getElementById('type_extrait').value;
				document.getElementById('type_saisie_2').value=document.getElementById('type_saisie').value;

				document.getElementById('form_change_date').submit();
			}
			*/
			function raffraichit_semaine_courante() {
				document.getElementById('date_absence_eleve_debut_2').value="<?php echo strftime('%d/%m/%Y', $ts_debut_semaine);?>";
				document.getElementById('date_absence_eleve_fin_2').value="<?php echo strftime('%d/%m/%Y', $ts_fin_semaine);?>";
				// Dojo ne semble pas permettre non plus de récupérer les champs qui suivent
				document.getElementById('nom_eleve_2').value=document.getElementById('nom_eleve').value;
				document.getElementById('id_classe_2').value=document.getElementById('id_classe').value;
				document.getElementById('type_extrait_2').value=document.getElementById('type_extrait').value;
				document.getElementById('type_saisie_2').value=document.getElementById('type_saisie').value;

				document.getElementById('form_change_date').submit();
			}
			function raffraichit_semaine_prec() {
				document.getElementById('date_absence_eleve_debut_2').value="<?php echo strftime('%d/%m/%Y', $ts_debut_semaine_prec);?>";
				document.getElementById('date_absence_eleve_fin_2').value="<?php echo strftime('%d/%m/%Y', $ts_fin_semaine_prec);?>";
				// Dojo ne semble pas permettre non plus de récupérer les champs qui suivent
				document.getElementById('nom_eleve_2').value=document.getElementById('nom_eleve').value;
				document.getElementById('id_classe_2').value=document.getElementById('id_classe').value;
				document.getElementById('type_extrait_2').value=document.getElementById('type_extrait').value;
				document.getElementById('type_saisie_2').value=document.getElementById('type_saisie').value;

				document.getElementById('form_change_date').submit();
			}
		</script>

	    <form dojoType="dijit.form.Form" id="choix_extraction" name="choix_extraction" action="<?php $_SERVER['PHP_SELF']?>" method="post">
	    <h2>Extraire les saisies du 		
	    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_debut" name="date_absence_eleve_debut" value="<?php echo $dt_date_absence_eleve_debut->format('Y-m-d')?>" />
	    au               
	    <input style="width : 8em;font-size:14px;" type="text" dojoType="dijit.form.DateTextBox" id="date_absence_eleve_fin" name="date_absence_eleve_fin" value="<?php echo $dt_date_absence_eleve_fin->format('Y-m-d')?>" />
	    <!--a href="#" onclick="document.getElementById('date_absence_eleve_debut').value='<?php echo strftime('%Y-%m-%d');?>'; document.getElementById('date_absence_eleve_fin').value='<?php echo strftime('%Y-%m-%d', time()+3600*24);?>'; return false;"> Aujourd'hui</a-->
	    <!-- 
	    
	    20140208
	    CA NE FONCTIONNE PAS NON PLUS: LA DATE CHANGE BIEN EN APPARENCE MAIS A LA VALIDATION LA DATE N'EST PAS PRISE EN COMPTE SI ON NE CLIQUE PAS DANS LES CHAMPS DE DATE
	    
	    &nbsp;<a href="#" onclick="document.getElementById('date_absence_eleve_debut').value='<?php echo strftime('%d/%m/%Y');?>'; document.getElementById('date_absence_eleve_fin').value='<?php echo strftime('%d/%m/%Y', time()+3600*24);?>'; return false;" title="Prendre la date du jour courant." style="font-size:x-small;"><img src='../images/icons/wizard.png' class='icone16' alt="Aujourd'hui" /></a>
	    -->
	    &nbsp;<a href="javascript:raffraichit_a_aujourdhui()" title="Prendre la date du jour courant." style="font-size:x-small;">J1<img src='../images/icons/wizard.png' class='icone16' alt="Aujourd'hui" /></a>
	<!--
	    &nbsp;-&nbsp;<a href="javascript:raffraichit_7j()" title="Les 7 derniers jours." style="font-size:x-small;">J-7<img src='../images/icons/wizard.png' class='icone16' alt="Les 7 derniers jours" /></a>
	-->
	    &nbsp;-&nbsp;<a href="javascript:raffraichit_semaine_courante()" title="Semaine courante." style="font-size:x-small;">Sem<img src='../images/icons/wizard.png' class='icone16' alt="Semaine courante" /></a>
	    &nbsp;-&nbsp;<a href="javascript:raffraichit_semaine_prec()" title="Semaine précédente." style="font-size:x-small;">S-1<img src='../images/icons/wizard.png' class='icone16' alt="Semaine précédente" /></a>

		</h2>
	    <?php 
	    if ($utilisateur->getStatut()!="administrateur" ) {
			echo '<p>Nom (facultatif) : <input dojoType="dijit.form.TextBox" type="text" style="width : 10em" name="nom_eleve" id="nom_eleve" size="10" value="'.$nom_eleve.'"/>';
		
		    //on affiche une boite de selection avec les classe
		    if ($affichage != 'ods')
		    if (getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
			$classe_col = ClasseQuery::create()->orderByNom()->orderByNomComplet()->find();
		    } else {
			$classe_col = $utilisateur->getClasses();
		    }
		    if (!$classe_col->isEmpty()) {
			    //echo ("Classe : <select dojoType=\"dijit.form.Select\" style=\"width :12em;font-size:12px;\" name=\"id_classe\" id=\"id_classe\">");
			    echo ("Classe : <select style=\"width :12em;font-size:12px;\" name=\"id_classe\" id=\"id_classe\">");
			    echo "<option value='-1'>Toutes les classes</option>\n";
			    foreach ($classe_col as $classe) {
				    echo "<option value='".$classe->getId()."'";
				    if ($id_classe == $classe->getId()) echo " selected='selected' ";
				    echo ">";
				    echo $classe->getNom();
				    echo "</option>\n";
			    }
			    echo "</select> ";
		    } else {
			echo 'Aucune classe avec élève affecté n\'a été trouvée';
		    }
	    ?>
		</p>

	    <p>
	    Type :
	    <!--select dojoType="dijit.form.Select" style="font-size:12px;" name="type_extrait" id="type_extrait"-->
	    <select style="font-size:12px;" name="type_extrait" id="type_extrait">
	    <option value='1' <?php if ($type_extrait == '1') {echo 'selected="selected"';}?>>Liste des saisies occasionnant un manquement aux obligations de présence</option>
	    <option value='2' <?php if ($type_extrait == '2') {echo 'selected="selected"';}?>>Liste de toutes les saisies</option>
	    </select>
	    </p>

	    <p>
	    Type de saisie :
	    <!-- Dojo transforme le SELECT en object HtmlTableElement 
	    On ne parvient pas à récupérer l'indice sélectionné.
	    -->
	    <!--select dojoType="dijit.form.Select" style="font-size:12px;" name="type_saisie" id="type_saisie"
	        onchange="
	        		alert(document.getElementById('type_saisie'));
	        		alert(document.getElementById('type_saisie').selectedIndex);
	        		if(document.getElementById('type_saisie').selectedIndex>1) {
	        			document.getElementById('type_extrait').selectedIndex=1;
        			}"-->
	    <!--select dojoType="dijit.form.Select" style="font-size:12px;" name="type_saisie" id="type_saisie"-->
	    <select style="font-size:12px;" name="type_saisie" id="type_saisie">
			<option value=''>---</option>
<?php
	echo "
			<option value='SANS'";
	if ($type_saisie == 'SANS') {
		echo " selected='selected' ";
	}
	echo ">SANS TYPE</option>";
	foreach (AbsenceEleveTypeQuery::create()->orderBySortableRank()->find() as $type) {
		echo "
			<option value='".$type->getId()."'";
		if ($type_saisie === (string) $type->getId()) {
			echo " selected='selected' ";
		}
		echo ">";
		echo $type->getNom();
		echo "</option>";
	}
?>
	    </select>

	    <p>
	    <button style="font-size:12px" dojoType="dijit.form.Button" type="submit" name="affichage" value="html">Afficher</button>
	    <button style="font-size:12px" dojoType="dijit.form.Button" type="submit" name="affichage" value="ods">Enregistrer au format ods</button>
	    <?php } else {
	    	?><button style="font-size:12px" dojoType="dijit.form.Button" type="submit" name="affichage" value="csv">Enregistrer au format csv</button>
	    	<?php
	    	if (isset($_REQUEST['retour']) && $traitement_csv_en_cours != 'true') {
				echo '<br/><br/><br/><input type="hidden" name="retour" value="'.$_REQUEST['retour'].'" />';
			}
	    }?>
		</form>
		 
	    <?php
	}
}
if ($affichage != null && $affichage != '') {
    $eleve_query = EleveQuery::create();
    if ($utilisateur->getStatut() == 'administrateur' || getSettingValue("GepiAccesAbsTouteClasseCpe")=='yes' && $utilisateur->getStatut() == "cpe") {
    } else {
		$eleve_query->filterByUtilisateurProfessionnel($utilisateur);
    }
    if ($id_classe !== null && $id_classe != -1) {
	$eleve_query->useJEleveClasseQuery()->filterByIdClasse($id_classe)->endUse();
    }
    if ($nom_eleve !== null && $nom_eleve != '') {
	$eleve_query->filterByNomOrPrenomLike($nom_eleve);
    }
    $eleve_col = $eleve_query->distinct()->find();

    $saisie_query = AbsenceEleveSaisieQuery::create()
	->filterByPlageTemps($dt_date_absence_eleve_debut, $dt_date_absence_eleve_fin)
	->filterByEleveId($eleve_col->toKeyValue('Id', 'Id'));

    if (($type_saisie != "")&&($type_saisie != "SANS")) {
        $saisie_query->useJTraitementSaisieEleveQuery()->useAbsenceEleveTraitementQuery()->filterByATypeId($type_saisie)->endUse()->endUse();
    }
    elseif ($type_extrait == '1') {
		$saisie_query->filterByManquementObligationPresence(true);
    }

    $saisie_query->useEleveQuery()->orderByNom()->orderByPrenom()->endUse();
    $saisie_query->orderByDebutAbs();
    $saisie_query->setFormatter('PropelOnDemandFormatter');
    
}

if ($affichage == 'html') {
	$alt=1;

	$cpt_eleve=0;
	$saisie_col = $saisie_query->find();
	echo '<table class="resizable sortable boireaus_alt2">
	<tr>
		<th class="text" title="Trier par nom d\'élève">Élèves</th>
		<th class="text" title="Trier par classe">Classes</th>
		<th class="text" title="Trier par date">Saisies</th>
	</tr>';
	$precedent_eleve_id = null;
	foreach ($saisie_col as $saisie) {
		if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
			continue;
		}
		if ($precedent_eleve_id != $saisie->getEleveId()) {
			if ($precedent_eleve_id != null) {
				//on finit la nouvelle ligne
				echo '
			</table>
		</td>
	</tr>';
			}
			$precedent_eleve_id = $saisie->getEleveId();
			//on affiche une nouvelle ligne
			$cpt_eleve++;
			echo '
	<tr style="border:1px solid" class="tr_alt">
		<td style="border:1px solid; vertical-align:top">
			<a href="../eleves/visu_eleve.php?ele_login='.$saisie->getEleve()->getLogin().'&amp;onglet=absences&amp;quitter_la_page=y" target="_blank" title="Voir la fiche élève dans un nouvel onglet.">
				'.$saisie->getEleve()->getNom().' '.$saisie->getEleve()->getPrenom().'
			</a>
		</td>
		<td style="border:1px solid; vertical-align:top">
			'.$saisie->getEleve()->getClasseNom().'
		</td>
		<td style="border:1px solid">
			<table cellspacing="0" width="100%">';
		}

		// Pour afficher d'une couleur différente les saisies des différents jours
		$tmp_tab=explode(" ", $saisie->getDateDescription());
		if((!isset($jour_precedent))||($tmp_tab[2]!=$jour_precedent)) {
			$alt=$alt*(-1);
			$jour_precedent=$tmp_tab[2];
		}
		echo '
				<tr class="lig'.$alt.'">
					<td>
						<span style="display:none" title="Non affichée. Juste là pour le tri">'.$saisie->getDebutAbs('Ymd_Hi').'</span>
						<a href="visu_saisie.php?id_saisie='.$saisie->getId().'" title="Voir la saisie dans un nouvel onglet." target="_blank">'.$saisie->getDateDescription().'</a>
					</td>
					<td>'.$saisie->getTypesDescription().'</td>
				</tr>';
	}
	echo '
			</table>
		</td>
	</tr>
</table>
<p>'.$cpt_eleve.' élève(s).</p>
<h5>Extraction faite le '.date("d/m/Y - H:i").'</h5>';

} else if ($affichage == 'ods') {
    // load the TinyButStrong libraries    
	include_once('../tbs/tbs_class.php'); // TinyButStrong template engine
    
    //include_once('../tbs/plugins/tbsdb_php.php');
    $TBS = new clsTinyButStrong; // new instance of TBS
    include_once('../tbs/plugins/tbs_plugin_opentbs.php');
    $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); // load OpenTBS plugin

    // Load the template
	$extraction_saisies=repertoire_modeles('absence_extraction_saisies.ods');
    $TBS->LoadTemplate($extraction_saisies, OPENTBS_ALREADY_UTF8);

    $titre = 'Extrait des absences du '.$dt_date_absence_eleve_debut->format('d/m/Y').' au '.$dt_date_absence_eleve_fin->format('d/m/Y');
    $classe = null;
    if ($id_classe != null && $id_classe != '') {
	$classe = ClasseQuery::create()->findOneById($id_classe);
	if ($classe != null) {
	    $titre .= ' pour la classe '.$classe->getNom();
	}
    }
    if ($nom_eleve != null && $nom_eleve != '' ) {
	$titre .= ' pour les élèves dont le nom ou le prénom contient '.$nom_eleve;
    }
    $TBS->MergeField('titre', $titre);
	$saisie_col = $saisie_query->find();
    $TBS->MergeBlock('saisie_col',$saisie_col);

    // Output as a download file (some automatic fields are merged here)
    $nom_fichier = 'extrait_saisies_';
    if ($classe != null) {
	$nom_fichier .= $classe->getNom().'_';
    }
    $nom_fichier .=  $dt_date_absence_eleve_fin->format("d_m_Y").'.ods';
    $TBS->Show(OPENTBS_DOWNLOAD+TBS_EXIT, $nom_fichier);
} else if ($affichage == 'csv' && $utilisateur->getStatut() == "administrateur") {
	if ($traitement_csv_en_cours == 'false') {
		//le traitement viens de se finir, on propose le fichier au téléchargement
		echo '<br/><a href="'.'../backup/'.getSettingValue("backup_directory").'/absences/'.$_REQUEST['filename'].'" target="_blank">Télécharger le résultat</a>';
		if ($page == 1) {
			//on s'est arreter à la page 0, il n'y a pas de résultat
			echo ' (extraction vide)';
		}
		echo '<br/>';

		echo "<p style='text-indent:-4em;margin-left:4em;margin-top:3em;'><em>NOTE&nbsp;:</em> Le fichier CSV est généré dans le sous-dossier 'absences' de votre dossier de sauvegarde (<em>sauvegardes de la base, des photos, des documents joints aux CDT, des archives de bulletins PDF,...</em>).<br />
		Si votre dossier de sauvegarde est protégé par un mot de passe, ce mot de passe va vous être réclamé lors du téléchargement de l'export des absences.<br />
		Si vous avez oublié ce mot de passe, vous pouvez le remplacer/mettre à jour en administrateur via la page de ";
		if ($utilisateur->getStatut()=="administrateur" ) {
			echo "<a href='../gestion/accueil_sauve.php' target='_blank'>Gestion des sauvegardes</a>.</p>";
		}
		else {
			echo "Gestion des sauvegardes.</p>";
		}
	} else {
		//print_r($page);die;
	    // titre des colonnes
    	$saisie_col = $saisie_query->paginate($page, 750);
		if ($page == 0) {
		    $output = '';
		    $date = new DateTime();
		    $output .= ('Extraction des saisies d\'absence '.getSettingValue('gepiSchoolName').' '.getSettingValue('gepiYear')."\n");
		    $output .= 'Extraction faite le '.date("d/m/Y - H:i")."\n";
		    $output .= ("Nom,Prenom,Classe,Debut absence,Fin absence, Type, Manquement a l'obligation de presence, Sous responsabilite etablissement\n");
		    $filename = 'extrait_saisies_'.date("d_m_Y_H_i").'.csv';
		    if (!file_exists('../backup/'.getSettingValue("backup_directory").'/absences')) {
		    	mkdir('../backup/'.getSettingValue("backup_directory").'/absences');
		    }
			$myFile = '../backup/'.getSettingValue("backup_directory").'/absences/'.$filename;
			$fh = fopen($myFile, 'w');
		    
		    fwrite($fh,$output);
			fclose($fh);
	    } else {
	    	$filename = $_REQUEST['filename'];
	
		
			echo '<br/>Veuillez patienter, étape '.$page.' sur '.$saisie_col->getLastPage();
			if (ob_get_contents()) {
				ob_flush();
			}
			flush();
			
	    	$output = '';
	    	foreach ($saisie_col as $saisie) {
				if ($type_extrait == '1' && !$saisie->getManquementObligationPresence()) {
				    continue;
				}
				$output .= $saisie->getEleve()->getNom().','.$saisie->getEleve()->getPrenom().','.$saisie->getEleve()->getClasseNom().',';
				$output .= $saisie->getDebutAbs('d/m/Y - H:i').','.$saisie->getFinAbs('d/m/Y - H:i').',';
				
				$traitement_col = $saisie->getAbsenceEleveTraitements();
				foreach ($traitement_col as $traitement) {
					if ($traitement->getAbsenceEleveType() != null) {
						$output .= $traitement->getAbsenceEleveType()->getNom().' - ';
					}
				}
				$output .= ',';
				if ($saisie->getManquementObligationPresence()) {
					$output .= 'oui,';
				} else {
					$output .= 'non,';
				}
				if ($saisie->getSousResponsabiliteEtablissement()) {
					$output .= 'oui';
				} else {
					$output .= 'non';
				}
				$output .= "\n";
		    }
			$myFile = '../backup/'.getSettingValue("backup_directory").'/absences/'.$filename;
			$fh = fopen($myFile, 'a');
		    
		    fwrite($fh,$output);
			fclose($fh);
	    }
		echo '<form action="extraction_saisies.php" method="post" name="form_table" id="form_table">';
		echo add_token_field();
		if (isset($filename)) {
			echo '<input type="hidden" name="filename" value="'.$filename.'" />';
		}
		echo '<input type="hidden" name="affichage" value="'.$affichage.'" />';
		if ($page == $saisie_col->getLastPage()) {
			echo '<input type="hidden" name="traitement_csv_en_cours" value="false" />';
		} else {
			echo '<input type="hidden" name="traitement_csv_en_cours" value="true" />';
		}
		$page++;
		echo '<input type="hidden" name="page" value="'.$page.'" />';
		if (isset($_REQUEST['retour'])) {
			echo '<input type="hidden" name="retour" value="'.$_REQUEST['retour'].'" />';
		}
		echo  "<script type='text/javascript'>
		                    document.form_table.submit();
		                </script>  
		                <noscript>
		                <input type='submit' name='Submit' value='Continuer' />
		                </noscript>
		            ";
		echo "</form><br/>";
	}
 }
 
if (isset($_REQUEST['retour']) && $traitement_csv_en_cours != 'true') {
	echo '<br/><br/><a href="'.$_REQUEST['retour'].'">Retour</a>';
}
?>
	</div>
<?php
$javascript_footer_texte_specifique = '<script type="text/javascript">
    dojo.require("dojo.parser");
    dojo.require("dijit.form.Button");    
    dojo.require("dijit.form.Form");  
    dojo.require("dijit.form.DateTextBox");  
    dojo.require("dijit.form.TextBox");
    dojo.require("dijit.form.Select");
    </script>';
require_once("../lib/footer.inc.php");
?>

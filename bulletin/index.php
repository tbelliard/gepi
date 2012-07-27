<?php
/**
 * Edition des bulletins
 * 
 * $Id$
 *
 * @copyright Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Bulletins
 * @subpackage Edition
 * @license GNU/GPL, 
 * @see COPYING.txt
 */
 /*
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

/**
 * Fichiers d'initialisation
 */
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

// Christian renvoye vers le fichier PDF bulletin
	if (empty($_GET['classe']) and empty($_POST['classe'])) {$classe="";}
	    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
	if (empty($_GET['eleve']) and empty($_POST['eleve'])) {$eleve="";}
	    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
	if (empty($_GET['periode']) and empty($_POST['periode'])) {$periode="";}
	    else { if (isset($_GET['periode'])) {$periode=$_GET['periode'];} if (isset($_POST['periode'])) {$periode=$_POST['periode'];} }
	if (empty($_GET['creer_pdf']) and empty($_POST['creer_pdf'])) {$creer_pdf="";}
	    else { if (isset($_GET['creer_pdf'])) {$creer_pdf=$_GET['creer_pdf'];} if (isset($_POST['creer_pdf'])) {$creer_pdf=$_POST['creer_pdf'];} }
	if (empty($_GET['type_bulletin']) and empty($_POST['type_bulletin'])) {$type_bulletin="";}
	    else { if (isset($_GET['type_bulletin'])) {$type_bulletin=$_GET['type_bulletin'];} if (isset($_POST['type_bulletin'])) {$type_bulletin=$_POST['type_bulletin'];} }

	if (empty($_GET['coefficients_a_1']) and empty($_POST['coefficients_a_1'])) {$coefficients_a_1 = '';}
	    else { if (isset($_GET['coefficients_a_1'])) {$coefficients_a_1=$_GET['coefficients_a_1'];} if (isset($_POST['coefficients_a_1'])) {$coefficients_a_1=$_POST['coefficients_a_1'];} }

	$bulletin_pass = 'non';

	//ajout Eric pour impression triée par Etab d'origine
	if (empty($_GET['tri_par_etab_origine']) and empty($_POST['tri_par_etab_origine'])) {$tri_par_etab_origine="non";}
	    else { if (isset($_GET['tri_par_etab_origine'])) {$tri_par_etab_origine=$_GET['tri_par_etab_origine'];} if (isset($_POST['tri_par_etab_origine'])) {$tri_par_etab_origine=$_POST['tri_par_etab_origine'];} }


	//=========================
	// AJOUT: boireaus 20080102
	$bull_pdf_debug=isset($_POST['bull_pdf_debug']) ? $_POST['bull_pdf_debug'] : NULL;
	//=========================


	// ERIC on n'imprime plus que les periodes fermées
	
	$periode_ferme = '1';
	if (empty($_GET['selection_eleve']) and empty($_POST['selection_eleve'])) { $selection_eleve = ''; }
	   else { if (isset($_GET['selection_eleve'])) { $selection_eleve = $_GET['selection_eleve']; } if (isset($_POST['selection_eleve'])) { $selection_eleve = $_POST['selection_eleve']; } }

	$message_erreur = '';
		if ( !empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les période(s) !'; }
		if ( empty($classe[0]) and !empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) !'; }
		if ( empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) et la ou les période(s) !'; }

	/*
	//debug_var();
	for($i=0;$i<count($classe);$i++){
		echo "\$classe[$i]=".$classe[$i]."<br />";
	}
	for($i=0;$i<count($periode);$i++){
		echo "\$periode[$i]=".$periode[$i]."<br />";
	}
	*/

	if ( !empty($classe[0]) and !empty($periode[0])) {
		for($i=0;$i<count($classe);$i++){
			for($j=0;$j<count($periode);$j++){
				
				$sql="SELECT 1=1 FROM periodes WHERE id_classe='".$classe[$i]."' AND nom_periode LIKE '".my_ereg_replace("[^.a-zA-Z0-9_-]+","%",html_entity_decode($periode[$j]))."' AND verouiller='N';";

				$test_per=mysql_query($sql);
				if(mysql_num_rows($test_per)>0){

					if($message_erreur!=''){$message_erreur.='<br />';}
					$message_erreur.="La période $periode[$j] n'est pas close pour ".get_class_from_id($classe[$i]);
				}
			}
		}
	}

	if($message_erreur=='') {
		$_SESSION['classe'] = $classe;
		$_SESSION['eleve'] = $eleve;
		$_SESSION['periode'] = $periode;
		$_SESSION['periode_ferme'] = $periode_ferme;
		$_SESSION['type_bulletin'] = $type_bulletin;
		$_SESSION['coefficients_a_1'] = $coefficients_a_1;


		$_SESSION['tri_par_etab_origine'] = $tri_par_etab_origine;


		//=========================
		// AJOUT: boireaus 20080102
		if(isset($bull_pdf_debug)) {
			$_SESSION['bull_pdf_debug']=$bull_pdf_debug;
		}
		else{
			unset($_SESSION['bull_pdf_debug']);
		}
		//=========================


	//ERIC
		if(!empty($creer_pdf) and !empty($periode[0]) and !empty($classe[0]) and !empty($type_bulletin) and empty($selection_eleve) ) {
			// le redirection se fait sur l'un ou l'autre des 2 fichiers de génération du bulletin en PDF
			$bulletin_pass = 'oui';
		}
	// FIN Christian renvoye vers le fichier PDF bulletin
	}

// Modif Christian pour les variable PDF
	$selection = isset($_POST["selection"]) ? $_POST["selection"] :NULL;
	$selection_eleve = isset($_POST["selection_eleve"]) ? $_POST["selection_eleve"] :NULL;
	$bt_select_periode = isset($_POST["bt_select_periode"]) ? $_POST["bt_select_periode"] :NULL;
	$valide_modif_model = isset($_POST["valide_modif_model"]) ? $_POST["valide_modif_model"] :NULL;


	if (empty($_GET['format']) and empty($_POST['format'])) {$format="";}
	    else { if (isset($_GET['format'])) {$format=$_GET['format'];} if (isset($_POST['format'])) {$format=$_POST['format'];} }
	if (empty($_GET['modele']) and empty($_POST['modele'])) {$modele="";}
	    else { if (isset($_GET['modele'])) {$modele=$_GET['modele'];} if (isset($_POST['modele'])) {$modele=$_POST['modele'];} }


//**************** EN-TETE *********************
$titre_page = "Edition des bulletins";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<script type='text/javascript' language='javascript'>

// fonction permettant d'afficher ou cacher un div
function affichercacher(a) {

	c = a.substr(4);
	var b = document.getElementById(a);

	var f = "img_"+c+"";

	if (b.style.display == "none" || b.style.display == "") {
		b.style.display = "block";
		document.images[f].src="../images/fleche_a.gif";
	}
	else
	{
		b.style.display = "none";
		document.images[f].src="../images/fleche_na.gif";
	}
}
</script>

<?php
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

//debug_var();

	//modification christian pour le choix des bulletins au format PDF
	if(!isset($id_classe)) {
		echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";

		if((empty($format))) {
			echo " | <a href='index.php?format=pdf'>Impression au format PDF </a>";
		}
		else {
			echo " | <a href='index.php?format='>Impression au format HTML </a>";
		}
	}
	
       //modification Christian CHAPEL
	if($format === 'pdf' and ( empty($bt_select_periode)) or !empty($creer_pdf) and $modele ==='' and !isset($classe[0]))
	{

		if ( $bulletin_pass === 'oui' )
		{

			?>
			<form method="post" action="bulletin_pdf_avec_modele_classe.php" name="imprime_pdf_ok" target="_blank">
		  		<fieldset style="width: 90%; margin: auto;"><legend>Votre sélection</legend>
		  		 	<input type="hidden" name="classe" value="<?php echo $classe; ?>" />
		  		 	<input type="hidden" name="eleve" value="<?php echo $eleve; ?>" />
		  		 	<input type="hidden" name="periode" value="<?php echo $periode; ?>" />
		  		 	<input type="hidden" name="periode_ferme" value="<?php echo $periode_ferme; ?>" />
		  		 	<input type="hidden" name="type_bulletin" value="<?php echo $type_bulletin; ?>" />
		  		 	<input type="hidden" name="tri_par_etab_origine" value="<?php echo $tri_par_etab_origine; ?>" />
		  		 	<input type="hidden" name="coefficients_a_1" value="<?php echo $coefficients_a_1; ?>" />
		  		 	<input type="hidden" name="bull_pdf_debug" value="<?php echo $bull_pdf_debug; ?>" />
		  		 	<input type="hidden" name="format" value="<?php echo $format; ?>" />
		  			<center><input type="submit" id="valider_pdf" name="creer_pdf" value="Télécharger le PDF" /></center>
				</fieldset>
			</form>
			<?php

		}
		?>

		<form method="post" action="index.php" name="imprime_pdf">
		  <fieldset style="width: 90%; margin: auto;"><legend>S&eacute;lection</legend>
		  <center>
			<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2" summary="Choix">
			  <tbody>
			    <tr>
			      <td align="right" nowrap="nowrap" valign="middle" colspan="1" rowspan="2" >
				<select name="classe[]" size="6" multiple="multiple" tabindex="3">
				  <optgroup label="----- Listes des classes -----">
				    <?php
					if( $_SESSION['statut'] === 'scolarite' ){ //n'affiche que les classes du profil scolarité
						$login_scolarite = $_SESSION['login'];
						$requete_classe = mysql_query("SELECT c.classe, c.nom_complet, c.id, jsc.login, jsc.id_classe, p.id_classe FROM ".$prefix_base."classes c, ".$prefix_base."j_scol_classes jsc, ".$prefix_base."periodes p WHERE ( jsc.login = '".$login_scolarite."' AND jsc.id_classe = c.id AND p.id_classe = c.id ) GROUP BY p.id_classe ORDER BY nom_complet ASC");
					} else {
					    if ($_SESSION["statut"] == "administrateur") {
							// on selectionne toutes les classes
							$sql_classe = "SELECT p.id_classe, c.* FROM classes c, periodes p WHERE (p.id_classe = c.id ) GROUP BY p.id_classe ORDER BY c.classe";
							$requete_classe = mysql_query($sql_classe);
						} else {
						    $requete_classe = mysql_query('SELECT c.* FROM '.$prefix_base.'classes c, '.$prefix_base.'j_eleves_professeurs jep, '.$prefix_base.'j_eleves_classes jec, '.$prefix_base.'periodes p WHERE ( jep.professeur = "'.$_SESSION['login'].'" AND jep.login = jec.login AND jec.id_classe = c.id AND p.id_classe = c.id ) GROUP BY p.id_classe ORDER BY c.classe');
						}
				    }

			  		while ($donner_classe = mysql_fetch_array($requete_classe))
				  	 {
						//=========================
						// AJOUT: boireaus 20071106 d'après Hugues MALHERBE
						// Pour régler le problème du champ id_classe non récupéré dans le cas d'un accès prof (on ne récupère que c.id, mais c.id=id_classe):
						$donner_classe['id_classe']=$donner_classe['id'];
						//=========================

						$sql_cpt_nb_eleve_1 = "SELECT count(eleves.login) FROM eleves, classes, j_eleves_classes WHERE classes.id = ".$donner_classe['id_classe']." AND j_eleves_classes.id_classe=classes.id AND j_eleves_classes.login=eleves.login GROUP BY eleves.login";
						$requete_cpt_nb_eleve_1 =  mysql_query($sql_cpt_nb_eleve_1);

						$requete_cpt_nb_eleve = mysql_num_rows($requete_cpt_nb_eleve_1);
					   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe) and in_array($donner_classe['id_classe'], $classe)) { ?>selected="selected"<?php } ?>><?php echo $donner_classe['nom_complet']." (".$donner_classe['classe'].") "; ?>&nbsp;;&nbsp; Eff : <?php echo $requete_cpt_nb_eleve; ?></option>
						<?php
					 }
					?>
				  </optgroup>
				  </select>
			      </td>
			      <td align="center" nowrap="nowrap" valign="middle">
				<select tabindex="5" name="periode[]" size="4" multiple="multiple">
				  <?php
					// sélection des période disponible
			            $requete_periode = mysql_query('SELECT nom_periode FROM '.$prefix_base.'periodes GROUP BY '.$prefix_base.'periodes.nom_periode ORDER BY '.$prefix_base.'periodes.nom_periode ASC');
				  		while($donner_periode = mysql_fetch_array($requete_periode))
					  	 {
						   ?><option value="<?php echo $donner_periode['nom_periode']; ?>" <?php if(!empty($periode) and in_array($donner_periode['nom_periode'], $periode)) { ?> selected="selected"<?php } ?>><?php echo ucfirst($donner_periode['nom_periode']); ?></option>
							<?php
						 }
				  ?>
				  </select>
				  </td>
			      <td align="left" nowrap="nowrap" valign="middle" colspan="1" rowspan="2" >
				<select name="eleve[]" size="6" multiple="multiple" tabindex="4">
				  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
				    <?php
					// sélection des id eleves sélectionné.
					if(!empty($classe[0]))
					{
						$cpt_classe_selec = 0; $selection_classe = "";
						while(!empty($classe[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
			                        $requete_eleve = mysql_query('SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
				  		while ($donner_eleve = mysql_fetch_array($requete_eleve))
					  	 {
						   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve) and in_array($donner_eleve['login'], $eleve)) { ?> selected="selected"<?php } ?>><?php echo my_strtoupper($donner_eleve['nom'])." ".casse_mot($donner_eleve['prenom'],'majf2'); ?></option>
							<?php
						 }
					}
					?>
				     <?php if(empty($classe[0]) and empty($eleve[0])) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
				  </optgroup>
				  </select>
				</td>
			    </tr>
			    <tr>
			      <td align="center" nowrap="nowrap" valign="middle"><input name="selection_eleve" id="selection_eleve" value="Liste élève >" onclick="this.form.submit();this.disabled=true;this.value='En cours'" type="submit" title="Transfère les élèves des classe sélectionné" alt="Transfère les élèves des classe sélectionné" /></td>
			    </tr>
			  </tbody>
		</table>
			<?php if ( $message_erreur != '' ) { ?><span style="color: #FF0000; font-weight: bold;"><?php echo $message_erreur; ?></span><?php } ?>


<?php
//ERIC
		$option_modele_bulletin=getSettingValue("option_modele_bulletin");

		if ($option_modele_bulletin!=1) {
		    echo "<br />Choisir le modèle de bulletin<br/>";
			echo "<select tabindex=\"5\" name=\"type_bulletin\">";
			// sélection des modèle des bulletins.
			$sql="SELECT id_model_bulletin, valeur FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY ".$prefix_base."modele_bulletin.valeur ASC";
			$requete_modele = mysql_query($sql);
			if ($option_modele_bulletin==2) { //Par défaut  le modèle défini pour les classes
				echo "<option value=\"-1\">Utiliser les modèles pré-sélectionnés par classe</option>";
			}
				while($donner_modele = mysql_fetch_array($requete_modele)) {
					echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
					echo ">".ucfirst($donner_modele['valeur'])."</option>\n";
				}
			echo "</select>\n";
		} else { // on utilise le modèle définie dans les paramètres de la classe.
		    echo "<input type=\"hidden\" name=\"type_bulletin\" value=\"-1\" />";
		}
?>

		<br />

		<div style="text-align: left;"><a href="#ao" onclick="affichercacher('div_1')" style="cursor: pointer;"><img style="border: 0px solid ; width: 13px; height: 13px; border: none; padding:2px; margin:2px; float: left;" name="img_1" alt="" title="Information" src="../images/fleche_na.gif" align="middle" />Autres options</a></div>
		<a name="ao"></a>
		<div style="text-align: left;">
			<div id="div_1" style="display: <?php
				//if( $coefficients_a_1 != '' or $bull_pdf_debug != '' or $active_entete_regroupement != '' ) {
				if( $coefficients_a_1 != '' or $bull_pdf_debug != '' or $tri_par_etab_origine != '' ) {
			?>block<?php } else { ?>none<?php } ?>; border-top: solid 1px; border-bottom: solid 1px; padding: 10px; background-color: #E0EEEF; font: normal 85% ;"><!--a name="ao"></a-->
			  <span>
				<input type="checkbox" name="tri_par_etab_origine" id="tri_par_etab_origine" value="oui" <?php if ( isset($tri_par_etab_origine) and $tri_par_etab_origine === 'oui' ) { ?>checked="checked"<?php } ?> />
				&nbsp;<label for="tri_par_etab_origine" style="cursor: pointer;">Impression triée par établissement d'origine des élèves.</label><br />
				<input type="checkbox" name="coefficients_a_1" id="coefficients_a_1" value="oui" <?php if ( isset($coefficients_a_1) and $coefficients_a_1 === 'oui' ) { ?>checked="checked"<?php } ?> />
				&nbsp;<label for="coefficients_a_1" style="cursor: pointer;">Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</label><br />
			  	<input type="checkbox" name="bull_pdf_debug" id="bull_pdf_debug" value="oui" <?php if ( isset($bull_pdf_debug) and $bull_pdf_debug === 'oui' ) { ?>checked="checked"<?php } ?> />
		  		&nbsp;<label for="bull_pdf_debug" style="cursor: pointer;">Activer le debug pour afficher les variables perturbant la génération de PDF.</label><br />
			  </span>
			</div>
			<br />
		</div>


	 	<input type="hidden" name="format" value="<?php echo $format; ?>" />
		<input type="submit" id="creer_pdf" name="creer_pdf" value="Créer le PDF" />
		</center>
		</fieldset>
	   </form>

<!-- TEXTE EXPLICATIF-->
<br />
<br />
<p>Vous allez effectuer l'impression de bulletin au format PDF. Voici quelques conseils pour utiliser l'interface.</p>
<ul>
<li>
	<p><b>Procédure à suivre pour imprimer des classes complètes :</b><br /></p>
	<ul>
		<li><p>Sélectionner la ou les classes à imprimer. Pour une sélection multiple, utiliser le CTRL-Clic et l'ascenseur.</p></li>
		<li><p>Sélectionner ensuite la ou les périodes pour lesquelles imprimer les bulletins. Remarque : Seules les périodes fermées seront imprimées.</p></li>
		<li><p>Choisir si nécessaire le modèle de bulletin à appliquer.</p></li>
		<li><p>Valider en cliquant sur le bouton "CREER LE PDF".</p><br/></li>
	</ul>
</li>
<li>
	<p><b>Procédure à suivre pour imprimer quelques élèves dans des classes :</b><br /></p>
	<ul>
		<li><p>Sélectionner la ou les classes à imprimer. Pour une sélection multiple, utiliser le CTRL-Clic et l'ascenseur.</p></li>
		<li><p>Cliquer sur le bouton "Liste élèves".</p></li>
		<li><p>Sélectionner la ou les élèves à imprimer. Pour une sélection multiple, utiliser le CTRL-Clic et l'ascenseur.</p></li>
		<li><p>Sélectionner ensuite la ou les périodes pour lesquelles imprimer les bulletins. Remarque : Seules les périodes fermées seront imprimées.</p></li>
		<li><p>Choisir si nécessaire le modèle de bulletin à appliquer.</p></li>
		<li><p>Valider en cliquant sur le bouton "CREER LE PDF".</p></li>
	</ul>
</li>
</ul>


		<?php
	}
	// fin de modification de la sélection pour le PDF Christian

	//modification christian gestion des modèles


if (!isset($id_classe) and $format != 'pdf' and $modele === '') {
    if ($_SESSION["statut"] == "scolarite") {
        $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe");
    } else {
	    if ($_SESSION["statut"] == "administrateur") {
		    // on selectionne toutes les classes
            $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c WHERE 1");
        } else {
		    $calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)");
		}
    }

    $nombreligne = mysql_num_rows($calldata);
    if ($nombreligne > "1") {
	  echo " | Total : $nombreligne ";
	  echo "classes";
	} else {
	  echo " | Total : $nombreligne ";
	  echo "classe";
    }
    echo "</p>\n";

	if($nombreligne==0){
		echo "<p>Aucune classe ne vous est attribuée.<br />Contactez l'administrateur pour qu'il effectue le paramétrage approprié dans la Gestion des classes.</p>\n";
	}
	else{
		echo "<p>Cliquez sur la classe pour laquelle vous souhaitez extraire les bulletins.<br />\n";

		unset($lien_classe);
		unset($txt_classe);
		$i = 0;
		while ($i < $nombreligne){
			$lien_classe[]="index.php?id_classe=".mysql_result($calldata, $i, "id");
			$txt_classe[]=mysql_result($calldata, $i, "classe");
			$i++;
		}

		tab_liste($txt_classe,$lien_classe,3);



		echo "<p>Ou <a href='bull_index.php'>accéder au nouveau dispositif des bulletins (<i>HTML et PDF</i>)</a></p>\n";
	}
}

if (isset($id_classe) and $format != 'pdf' and $modele === '') {
	
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";

	echo " | <a href='index.php?format=pdf'>Impression au format PDF</a>\n";

	if($_SESSION['statut']=='scolarite'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	if($_SESSION['statut']=='professeur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc,j_groupes_professeurs jgp WHERE jgp.login = '".$_SESSION['login']."' AND jgc.id_groupe=jgp.id_groupe AND jgc.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='cpe'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '".$_SESSION['login']."' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
	}
	if($_SESSION['statut']=='administrateur'){
		$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
	}

	$chaine_options_classes="";

	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
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
		}
	}
	// =================================

	if($id_class_prec!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
		echo "#graph'>Classe précédente</a>";
	}
	if($chaine_options_classes!="") {
		echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if($id_class_suiv!=0){
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
		echo "#graph'>Classe suivante</a>";
	}

	echo "</p>\n";

	echo "</form>\n";

	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";

	$i="1";
	echo "<form name='choix' action='edit.php' target='_blank' method='post'>\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' /> \n";
	echo "<table border='0' summary='Choix'>\n";
	$num_per_close=0;
	$nb_per_close=0;

	$periode_par_defaut_bulletin=isset($_SESSION['periode_par_defaut_bulletin']) ? $_SESSION['periode_par_defaut_bulletin'] : NULL;

	if(isset($periode_par_defaut_bulletin)) {
		if ($ver_periode[$periode_par_defaut_bulletin] == "N") {
			unset($periode_par_defaut_bulletin);
		}
	}

	while ($i < $nb_periode) {
		echo "<tr>\n";
		if ($ver_periode[$i] == "N") {
			echo "<td style='text-align:center; color:red;'>&nbsp;</td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b> : édition impossible ";
			echo " (<i>$gepiOpenPeriodLabel</i>)</td>\n";
		} else {
			echo "<td align='center'><input type='radio' name='periode_num' id='periode_num_$i' value='$i'";
			if(isset($periode_par_defaut_bulletin)) {
				if($periode_par_defaut_bulletin==$i) {echo " checked";}
			}
			elseif($nb_per_close==0){
				echo " checked";
			}
			echo " onchange='colore_checked();'";
			echo " /> </td>\n";
			echo "<td id='td_periode_$i'><label for='periode_num_$i' style='cursor: pointer;'><b>".ucfirst($nom_periode[$i])."</b>";
			if ($ver_periode[$i] == "P"){echo " (<i>Période partiellement close, seule la saisie des avis du conseil de classe est possible</i>)";}
			if ($ver_periode[$i] == "O"){echo " (<i>Période entièrement close, plus aucune saisie/modification n'est possible</i>)";}
			echo "</label></td>\n";
			$num_per_close=$i;
			$nb_per_close++;
		}
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";

	echo "<script type='text/javascript'>
	function colore_checked(){
		for(i=1;i<=$nb_periode;i++) {
			if(document.getElementById('periode_num_'+i)) {
				if(document.getElementById('periode_num_'+i).checked==true) {
					document.getElementById('td_periode_'+i).style.backgroundColor='white';
					document.getElementById('td_periode_'+i).style.border='1px solid black';
				}
				else {
					document.getElementById('td_periode_'+i).style.backgroundColor='';
					document.getElementById('td_periode_'+i).style.border='';
				}
			}
		}
	}
	colore_checked();
</script>\n";

	// AJOUTER LES AUTRES PARAMETRES
	echo "<p><b>Et les bulletins à imprimer: </b></p>\n";
	$sql="SELECT DISTINCT e.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e WHERE jec.login=e.login AND jec.id_classe='$id_classe' ORDER BY e.nom,e.prenom";
	$res_ele=mysql_query($sql);
	if(mysql_num_rows($res_ele)==0){
		echo "<p style='color:red;'>ERREUR: La classe choisie ne compterait aucun élève?</p>\n";
		echo "</form>\n";
		echo "</body>\n";
		echo "</html>\n";
		die();
	}
	else{

		echo "<table border='0' summary='Sélection'>\n";
		echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' id='selection_CLASSE_ENTIERE_' value='_CLASSE_ENTIERE_' onchange=\"affiche_nb_ele_select();\" checked /></td>\n";
		echo "<td valign='top'><label for='selection_CLASSE_ENTIERE_' style='cursor: pointer;'>Classe entière</label></td>\n";
		echo "<td valign='top'> ou </td>\n";
		//echo "</tr>\n";
		//echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' id='selection_ele' value='_SELECTION_' onchange=\"affiche_nb_ele_select();\" /></td>\n";
		echo "<td valign='top'><label for='selection_ele' style='cursor: pointer;'>Sélection</label><br />\n";
		echo "<select id='liste_login_ele' name='liste_login_ele[]' multiple='yes' size='5' onchange=\"document.getElementById('selection_ele').checked=true;affiche_nb_ele_select();\">\n";
		
		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".my_strtoupper($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "<td valign='bottom'>\n";
		echo "<div id='nb_ele_select'>\n";
		echo "&nbsp;\n";
		echo "</div>\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
	}

	echo "<script type='text/javascript'>
	function affiche_nb_ele_select(){
		if(document.getElementById('selection_ele').checked==true){
			num=0;
			//for(i=0;i<document.forms['choix'].selection.options.length;i++){
			//	if(document.forms['choix'].selection.options[i].selected){
			for(i=0;i<document.getElementById('liste_login_ele').options.length;i++){
				if(document.getElementById('liste_login_ele').options[i].selected){
					num++;
				}
			}
		}
		else{
			num=".mysql_num_rows($res_ele).";
		}

		if(num>=2){
			document.getElementById('nb_ele_select').innerHTML=num+' élèves sélectionnés.';
		}
		else{
			document.getElementById('nb_ele_select').innerHTML=num+' élève sélectionné.';
		}
	}
</script>\n";

	echo "<table border='0' summary='Nombre de bulletins'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' id='un_seul_bull_par_famille' value='oui' /></td><td><label for='un_seul_bull_par_famille' style='cursor: pointer;'>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</label></td></tr>\n";


    if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}

	// A FAIRE:
	// Tester et ne pas afficher:
	// - si tous les coeff sont à 1
	$test_coef=mysql_num_rows(mysql_query("SELECT coef FROM j_groupes_classes WHERE (id_classe='".$id_classe."' and coef!='1.0')"));
	if($test_coef>0){
		echo "<tr>\n";
		echo "<td colspan=\"2\"><br /><br /><b>Calcul des moyennes générales";
		// Ne pas afficher la mention de catégorie, si on n'affiche pas les catégories dans cette classe.
		$affiche_categories = sql_query1("SELECT display_mat_cat FROM classes WHERE id='".$id_classe."'");
		if ($affiche_categories == "y") {
			echo " et par catégorie";
		}
		echo ".<br /></b></td>\n";
		echo "</tr>\n";
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' id='coefficients_a_1' value='oui' /></td><td><label for='coefficients_a_1' style='cursor: pointer;'>Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</label></td></tr>\n";
	}
	echo "</table>\n";



	$b_adr_pg_defaut=isset($_SESSION['b_adr_pg']) ? $_SESSION['b_adr_pg'] : "xx";

	echo "<br />\n";
	echo "<p><b>Bloc adresse responsable et page de garde&nbsp;:</b></p>\n";
	echo "<blockquote>\n";
	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_xx' value='xx' ";
	if($b_adr_pg_defaut=="xx") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_xx' style='cursor:pointer'> D'après les paramètres du bulletin HTML</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_nn' value='nn' ";
	if($b_adr_pg_defaut=="nn") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_nn' style='cursor:pointer'> sans bloc adresse ni page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_yn' value='yn' ";
	if($b_adr_pg_defaut=="yn") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_yn' style='cursor:pointer'> avec bloc adresse sans page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_ny' value='ny' ";
	if($b_adr_pg_defaut=="ny") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_ny' style='cursor:pointer'> sans bloc adresse avec page de garde</label><br />\n";

	echo "<input type='radio' name='b_adr_pg' id='b_adr_pg_yy' value='yy' ";
	if($b_adr_pg_defaut=="yy") {
		echo "checked='checked' ";
	}
	echo "/><label for='b_adr_pg_yy' style='cursor:pointer'> avec bloc adresse et page de garde</label><br />\n";
	echo "</blockquote>\n";


	echo "<p style='text-align:center;'><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<br />\n<center><table border=\"1\" cellpadding=\"10\" width=\"80%\" summary='Avertissement'><tr><td>";
	echo "<center><b>Avertissement</b></center><br /><br />La mise en page des bulletins est très différente à l'écran et à l'impression.
	Avant d'imprimer les bulletins :
	<ul>
	<li>Veillez à utiliser la fonction \"aperçu avant impression\" disponible sur la plupart des navigateurs.</li>
	<li>Veillez à régler les paramètres de marges, d'en-tête et de pied de page.</li>
	</ul>
	</td></tr></table></center>\n";
}

/**
 * Pied de page
 */
require("../lib/footer.inc.php");
?>

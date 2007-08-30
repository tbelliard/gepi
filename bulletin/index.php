<?php
/*
 * $Id$
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
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
	// ERIC on n'imprime plus que les periodes fermées
	/*
	   if (empty($_GET['periode_ferme']) and empty($_POST['periode_ferme'])) { $periode_ferme = ''; }
	   else { if (isset($_GET['periode_ferme'])) { $periode_ferme = $_GET['periode_ferme']; } if (isset($_POST['periode_ferme'])) { $periode_ferme = $_POST['periode_ferme']; } }
	*/
	$periode_ferme = '1';
	if (empty($_GET['selection_eleve']) and empty($_POST['selection_eleve'])) { $selection_eleve = ''; }
	   else { if (isset($_GET['selection_eleve'])) { $selection_eleve = $_GET['selection_eleve']; } if (isset($_POST['selection_eleve'])) { $selection_eleve = $_POST['selection_eleve']; } }

	$message_erreur = '';
		if ( !empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les période(s) !'; }
		if ( empty($classe[0]) and !empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) !'; }
		if ( empty($classe[0]) and empty($periode[0]) and !empty($creer_pdf) and empty($selection_eleve) ) { $message_erreur = 'attention n\'oubliez pas de sélectionner la ou les classe(s) et la ou les période(s) !'; }

	$_SESSION['classe'] = $classe;
	$_SESSION['eleve'] = $eleve;
	$_SESSION['periode'] = $periode;
	$_SESSION['periode_ferme'] = $periode_ferme;
	$_SESSION['type_bulletin'] = $type_bulletin;

//ERIC
	if(!empty($creer_pdf) and !empty($periode[0]) and !empty($classe[0]) and !empty($type_bulletin) and empty($selection_eleve) ) {
	    // le redirection se fait sur l'un ou l'autre des 2 fichiers de génération du bulletin en PDF
  /*	    $option_modele_bulletin=getSettingValue("option_modele_bulletin");
		if ($option_modele_bulletin!=1) {
	      if ($type_bulletin == -1) {
		    //cas avec les modèles affectés aux classes.
	        header("Location: bulletin_pdf_avec_modele_classe.php");
		  } else {
		    //cas sans les modèles affectés à chaque classe .
			header("Location: buletin_pdf.php");
		  }
		} else { // on utilise le modèle définie dans les paramètres de la classe.
		    //cas avec les modèles affectés aux classes.
	        header("Location: bulletin_pdf_avec_modele_classe.php");
		}
		*/
	 header("Location: bulletin_pdf_avec_modele_classe.php");
	}
// FIN Christian renvoye vers le fichier PDF bulletin

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
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
   die("Droits insuffisants pour effectuer cette opération");
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour à l'accueil</a>";

//if (!isset($id_classe)) {

	//modification christian pour le choix des bulletins au format PDF
	?> | <?php if(empty($format) or $format != 'pdf') { ?><a href='index.php?format=pdf'>Impression au format PDF </a><?php } else { ?><a href='index.php?format='>Impression au format HTML </a><?php }
	//fin de modification

       //modification Christian CHAPEL
	if($format === 'pdf' and ( empty($bt_select_periode)) or !empty($creer_pdf) and $modele ==='' and !isset($classe[0]))
	{
		?>
		<form method="post" action="index.php" name="imprime_pdf">
		  <fieldset style="width: 90%; margin: auto;"><legend>S&eacute;lection</legend>
		  <center>
			<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
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
				  <!--<input type="checkbox" name="periode_ferme" id="periode_ferme" value="1" <?php if( isset($periode_ferme) and $periode_ferme === '1' ) { ?>checked="checked"<?php } ?> title="Imprimer seulement les période fermée" alt="Imprimer seulement les période fermée" />-->
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
						   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve) and in_array($donner_eleve['login'], $eleve)) { ?> selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option>
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


<?PHP
//ERIC
		$option_modele_bulletin=getSettingValue("option_modele_bulletin");

		if ($option_modele_bulletin!=1) {
		    echo "<br />Choisir le modèle de bulletin<br/>";
			echo "<select tabindex=\"5\" name=\"type_bulletin\">";
			// sélection des modèle des bulletins.
			$requete_modele = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
			if ($option_modele_bulletin==2) { //Par défaut  le modèle défini pour les classes
				echo "<option value=\"-1\">Utiliser les modèles pré-sélectionnés par classe</option>";
			}
				while($donner_modele = mysql_fetch_array($requete_modele)) {
					echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
					echo ">".ucfirst($donner_modele['nom_model_bulletin'])."</option>\n";
				}
			echo "</select>\n";
		} else { // on utilise le modèle définie dans les paramètres de la classe.
		    echo "<input type=\"hidden\" name=\"type_bulletin\" value=\"-1\" />";
		}
?>

		  <br /><br />
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
        //$calldata = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
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
	}
	/*
	$nb_class_par_colonne=round($nombreligne/3);
	//echo "<table width='100%' border='1'>\n";
	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$i = 0;

	echo "<td align='left'>\n";

	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			//echo "<td style='padding: 0 10px 0 10px'>\n";
			echo "<td align='left'>\n";
		}

		$ide_classe = mysql_result($calldata, $i, "id");
		$classe_liste = mysql_result($calldata, $i, "classe");
		echo "<br /><a href='index.php?id_classe=$ide_classe'>$classe_liste</a>\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	*/
}
if (isset($id_classe) and $format != 'pdf' and $modele === '') {
	echo " | <a href=\"index.php\">Choisir une autre classe</a>";
/*
	// On choisit le periode :
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p><b>".ucfirst($nom_periode[$i])."</b> : édition Impossible ";
			echo " ($gepiOpenPeriodLabel)";
		} else {
			echo "<p><a href='edit.php?id_classe=$id_classe&amp;periode_num=$i' target='bull'><b>".ucfirst($nom_periode[$i])."</b></a>";
			if ($ver_periode[$i] == "P")  echo " (Période partiellement close, seule la saisie des avis du conseil de classe est possible)";
			if ($ver_periode[$i] == "O")  echo " (Période entièrement close, plus aucune saisie/modification n'est possible)";
		}
		echo "</p>\n";
		$i++;
	}
*/
	echo "<p><b>Choisissez la période : </b></p>\n";
	include "../lib/periodes.inc.php";
	$i="1";
	//echo "<form name='choix' action='edit.php' target='bull' method='post'>\n";
	echo "<form name='choix' action='edit.php' target='_blank' method='post'>\n";
	//echo "<form name='choix' action='edit.php' target='bull' method='get' >\n";
	echo "<input type='hidden' name='id_classe' value='$id_classe' /> \n";
	echo "<table border='0'>\n";
	$num_per_close=0;
	$nb_per_close=0;
	while ($i < $nb_periode) {
		echo "<tr>\n";
		if ($ver_periode[$i] == "N") {
			//echo "<td style='text-align:center; color:red;'>X</td>\n";
			//echo "<td style='text-align:center; color:red;'><img src='../images/disabled.png' alt='impossible' /></td>\n";
			echo "<td style='text-align:center; color:red;'>&nbsp;</td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b> : édition impossible ";
			echo " (<i>$gepiOpenPeriodLabel</i>)</td>\n";
		} else {
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i' /> </td>\n";
			//echo "<td align='center'><input type='radio' name='periode_num' id='id_periode_num' value='$i'";
			echo "<td align='center'><input type='radio' name='periode_num' value='$i'";
			if($nb_per_close==0){
				echo " checked";
			}
			echo " /> </td>\n";
			echo "<td><b>".ucfirst($nom_periode[$i])."</b>";
			if ($ver_periode[$i] == "P"){echo " (<i>Période partiellement close, seule la saisie des avis du conseil de classe est possible</i>)";}
			if ($ver_periode[$i] == "O"){echo " (<i>Période entièrement close, plus aucune saisie/modification n'est possible</i>)";}
			echo "</td>\n";
			$num_per_close=$i;
			$nb_per_close++;
		}
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";

/*
// Je ne parviens pas à cocher la dernière période close (si elle existe)...
	if($nb_per_close>0){
		echo "<script type='text/javascript' language='javascript'>
//document.getElementById('id_periode_num').element[$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
//document.elements['choix'].elements['periode_num'][$num_per_close].checked=true;
//document.forms[0].periode_num[$num_per_close].checked=true;
radio=document.getElementById('id_periode_num');
for(i=0;i<$nb_per_close;i++){
	alert('radio['+i+']='+radio[i].value);
}
</script>\n";
	}
*/

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
		/*
		echo "<p>\n";
		echo "<select name='liste_login_ele'>\n";
		echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";
		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
		}
		echo "</select>\n";
		//echo "<br />\n";
		echo "</p>\n";
		*/

		echo "<table border='0'>\n";
		echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' value='_CLASSE_ENTIERE_' onchange=\"affiche_nb_ele_select();\" checked /></td>\n";
		echo "<td valign='top'>Classe entière</td>\n";
		echo "<td valign='top'> ou </td>\n";
		//echo "</tr>\n";
		//echo "<tr>\n";
		echo "<td valign='top'><input type='radio' name='selection' id='selection_ele' value='_SELECTION_' onchange=\"affiche_nb_ele_select();\" /></td>\n";
		echo "<td valign='top'>Sélection<br />\n";
		echo "<select id='liste_login_ele' name='liste_login_ele[]' multiple='yes' size='5' onchange=\"document.getElementById('selection_ele').checked=true;affiche_nb_ele_select();\">\n";
		//echo "<option value='_CLASSE_ENTIERE_' selected>Classe entière</option>\n";

		while($lig_ele=mysql_fetch_object($res_ele)){
			echo "<option value='$lig_ele->login'>".strtoupper($lig_ele->nom)." ".ucfirst(strtolower($lig_ele->prenom))."</option>\n";
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

	echo "<table border='0'>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='un_seul_bull_par_famille' value='oui' /></td><td>Ne pas imprimer de bulletin pour le deuxième parent<br />(<i>même dans le cas de parents séparés</i>).</td></tr>\n";


    if(!getSettingValue("bull_intitule_app")){
		$bull_intitule_app="Appréciations/Conseils";
	}
	else{
		$bull_intitule_app=getSettingValue("bull_intitule_app");
	}


/*
	echo "<tr><td valign='top'><input type='checkbox' name='ne_pas_afficher_moy_gen' value='oui' /><td><td>Ne pas afficher la moyenne générale (<i>même si l'affichage des coefficients de matières est activé</i>).<br /><i>Rappel:</i> La moyenne générale ne peut apparaitre que si un des coefficients de matière au moins est non nul.</td></tr>\n";
	echo "<tr><td valign='top'><input type='checkbox' name='min_max_moyclas' value='1' /></td><td>Afficher les moyennes minimale, classe et maximale dans une seule colonne pour gagner de la place pour l'appréciation.</td></tr>\n";
*/
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
		echo "<tr><td valign='top'><input type='checkbox' name='coefficients_a_1' value='oui' /></td><td>Forcer les coefficients des matières à 1, indépendamment des coefficients saisis dans les paramètres de la classe.</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p style='text-align:center;'><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<br />\n<center><table border=\"1\" cellpadding=\"10\" width=\"80%\"><tr><td>";
	echo "<center><b>Avertissement</b></center><br /><br />La mise en page des bulletins est très différente à l'écran et à l'impression.
	Avant d'imprimer les bulletins :
	<ul>
	<li>Veillez à utiliser la fonction \"aperçu avant impression\" disponible sur la plupart des navigateurs.</li>
	<li>Veillez à régler les paramètres de marges, d'en-tête et de pied de page.</li>
	</ul>
	</td></tr></table></center>\n";
}

require("../lib/footer.inc.php");
?>

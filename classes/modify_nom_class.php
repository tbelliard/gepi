<?php
/*
 * Last modification  : 04/11/2006
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

$msg = null;

if (isset($is_posted) and ($is_posted == '1')) {
	if (isset($display_rang)) $display_rang = 'y'; else $display_rang = 'n';
	if (isset($display_address)) $display_address = 'y'; else $display_address = 'n';
	if (isset($display_coef)) $display_coef = 'y'; else $display_coef = 'n';
	if (isset($display_mat_cat)) $display_mat_cat = 'y'; else $display_mat_cat = 'n';
	if (isset($display_nbdev)) $display_nbdev = 'y'; else $display_nbdev = 'n';
	if (isset($display_moy_gen)) $display_moy_gen = 'y'; else $display_moy_gen = 'n';

	if (!isset($modele_bulletin)) $$modele_bulletin = 1;

	// =========================
	// AJOUT: boireaus
	//rn_formule
	//rn_sign_nblig

	if(strlen(ereg_replace("[0-9]","",$rn_sign_nblig))!=0){$rn_sign_nblig=3;}

	if (isset($rn_nomdev)){$rn_nomdev='y';}else{$rn_nomdev='n';}
	if (isset($rn_toutcoefdev)){$rn_toutcoefdev='y';}else{$rn_toutcoefdev='n';}
	if (isset($rn_coefdev_si_diff)){$rn_coefdev_si_diff='y';}else{$rn_coefdev_si_diff='n';}
	if (isset($rn_datedev)){$rn_datedev='y';}else{$rn_datedev='n';}
	if (isset($rn_sign_chefetab)){$rn_sign_chefetab='y';}else{$rn_sign_chefetab='n';}
	if (isset($rn_sign_pp)){$rn_sign_pp='y';}else{$rn_sign_pp='n';}
	if (isset($rn_sign_resp)){$rn_sign_resp='y';}else{$rn_sign_resp='n';}
	// =========================


	if (isset($id_classe)) {
		if ($reg_class_name) {
			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat' WHERE id = '$id_classe'");
			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat', display_nbdev ='$display_nbdev' WHERE id = '$id_classe'");

			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat', display_nbdev ='$display_nbdev',display_moy_gen='$display_moy_gen' WHERE id = '$id_classe'");

			$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name',
													nom_complet='$reg_nom_complet',
													suivi_par='$reg_suivi_par',
													formule= '".html_entity_decode($reg_formule)."',
													format_nom='$reg_format',
													display_rang='$display_rang',
													display_address='$display_address',
													display_coef='$display_coef',
													display_mat_cat ='$display_mat_cat',
													display_nbdev ='$display_nbdev',
													display_moy_gen='$display_moy_gen',
													modele_bulletin_pdf='$modele_bulletin',
													rn_nomdev='$rn_nomdev',
													rn_toutcoefdev='$rn_toutcoefdev',
													rn_coefdev_si_diff='$rn_coefdev_si_diff',
													rn_datedev='$rn_datedev',
													rn_sign_chefetab='$rn_sign_chefetab',
													rn_sign_pp='$rn_sign_pp',
													rn_sign_resp='$rn_sign_resp',
													rn_sign_nblig='$rn_sign_nblig',
													rn_formule='$rn_formule'
												WHERE id = '$id_classe'");

			if (!$register_class) {
					$msg .= "Une erreur s'est produite lors de la modification de la classe.";
					} else {
					$msg .= "La classe a bien été modifiée.";
			}
			// On enregistre les infos relatives aux catégories de matières
			$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
			while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
				$reg_priority = $_POST['priority_'.$row["id"]];
				if (isset($_POST['moyenne_'.$row["id"]])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
				if (!is_numeric($reg_priority)) $reg_priority = 0;
				if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;
				$test = mysql_result(mysql_query("select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')"), 0);
				if ($test == 0) {
					// Pas d'entrée... on créé
					$res = mysql_query("INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");
				} else {
					// Entrée existante, on met à jour
					$res = mysql_query("UPDATE j_matieres_categories_classes SET priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "' WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $row["id"] . "')");
				}
				if (!$res) {
					$msg .= "<br/>Une erreur s'est produite lors de l'enregistrement des données de catégorie.";
				}
			}

		} else {
		$msg .= "Veuillez préciser le nom de la classe !";
		}
	} else {
		if ($reg_class_name) {
		//$register_class = mysql_query("INSERT INTO classes SET classe = '$reg_class_name', nom_complet = '$reg_nom_complet', suivi_par = '$reg_suivi_par', formule = '$reg_formule', format_nom = '$reg_format', display_rang = '$display_rang', display_address = '$display_address', display_coef = '$display_coef', display_mat_cat = '$display_mat_cat'");
		//$register_class = mysql_query("INSERT INTO classes SET classe = '$reg_class_name', nom_complet = '$reg_nom_complet', suivi_par = '$reg_suivi_par', formule = '$reg_formule', format_nom = '$reg_format', display_rang = '$display_rang', display_address = '$display_address', display_coef = '$display_coef', display_mat_cat = '$display_mat_cat', display_nbdev ='$display_nbdev'");
		$register_class = mysql_query("INSERT INTO classes SET classe = '$reg_class_name',
													nom_complet = '$reg_nom_complet',
													suivi_par = '$reg_suivi_par',
													formule = '$reg_formule',
													format_nom = '$reg_format',
													display_rang = '$display_rang',
													display_address = '$display_address',
													display_coef = '$display_coef',
													display_mat_cat = '$display_mat_cat',
													display_nbdev ='$display_nbdev',
													display_moy_gen='$display_moy_gen',
													modele_bulletin_pdf='$modele_bulletin',
													rn_nomdev='$rn_nomdev',
													rn_toutcoefdev='$rn_toutcoefdev',
													rn_coefdev_si_diff='$rn_coefdev_si_diff',
													rn_datedev='$rn_datedev',
													rn_sign_chefetab='$rn_sign_chefetab',
													rn_sign_pp='$rn_sign_pp',
													rn_sign_resp='$rn_sign_resp',
													rn_sign_nblig='$rn_sign_nblig',
													rn_formule='$rn_formule'
												");
		if (!$register_class) {
			$msg .= "Une erreur s'est produite lors de l'enregistrement de la nouvelle classe.";
		} else {
			$msg .= "La nouvelle classe a bien été enregistrée.";
			$id_classe = mysql_insert_id();

			// On enregistre les infos relatives aux catégories de matières
			$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
			while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
				$reg_priority = $_POST['priority_'.$row["id"]];
				if (isset($_POST['moyenne_'.$row["id"]])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
				if (!is_numeric($reg_priority)) $reg_priority = 0;
				if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;

				$res = mysql_query("INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");

				if (!$res) {
					$msg .= "<br/>Une erreur s'est produite lors de l'enregistrement des données de catégorie.";
				}
			}
		}

		} else {
		$msg .= "Veuillez préciser le nom de la classe !";
		}
	}
}


//**************** EN-TETE *******************************
$titre_page = "Gestion des classes | Modifier les paramètres";
require_once("../lib/header.inc");
//**************** FIN EN-TETE ***************************

$id_class_prec=0;
$id_class_suiv=0;

if (isset($id_classe)) {
	// =================================
	// AJOUT: boireaus
	$sql="SELECT id, classe FROM classes ORDER BY classe";
	$res_class_tmp=mysql_query($sql);
	if(mysql_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$temoin_tmp=1;
				if($lig_class_tmp=mysql_fetch_object($res_class_tmp)){
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
		}
	}
	// =================================
}

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec'>Classe précédente</a>";}
if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv'>Classe suivante</a>";}
?>

</p>
<p><b>Remarque : </b>Connectez vous avec un compte ayant le statut "scolarité" pour éditer les bulletins et avoir accès à d'autres paramètres d'affichage.</p>

<?php

if (isset($id_classe)) {

	$call_nom_class = mysql_query("SELECT * FROM classes WHERE id = '$id_classe'");
	$classe = mysql_result($call_nom_class, 0, 'classe');
	$nom_complet = mysql_result($call_nom_class, 0, 'nom_complet');
	$suivi_par = mysql_result($call_nom_class, 0, 'suivi_par');
	$formule = mysql_result($call_nom_class, 0, 'formule');
	$format_nom = mysql_result($call_nom_class, 0, 'format_nom');
	$display_rang = mysql_result($call_nom_class, 0, 'display_rang');
	$display_address = mysql_result($call_nom_class, 0, 'display_address');
	$display_coef = mysql_result($call_nom_class, 0, 'display_coef');
	$display_mat_cat = mysql_result($call_nom_class, 0, 'display_mat_cat');
	$display_nbdev = mysql_result($call_nom_class, 0, 'display_nbdev');
	$display_moy_gen = mysql_result($call_nom_class, 0, 'display_moy_gen');
	$modele_bulletin_pdf = mysql_result($call_nom_class, 0, 'modele_bulletin_pdf');

	// =========================
	// AJOUT: boireaus
	$rn_nomdev=mysql_result($call_nom_class, 0, 'rn_nomdev');
	$rn_toutcoefdev=mysql_result($call_nom_class, 0, 'rn_toutcoefdev');
	$rn_coefdev_si_diff=mysql_result($call_nom_class, 0, 'rn_coefdev_si_diff');
	$rn_datedev=mysql_result($call_nom_class, 0, 'rn_datedev');
	$rn_formule=mysql_result($call_nom_class, 0, 'rn_formule');
	$rn_sign_chefetab=mysql_result($call_nom_class, 0, 'rn_sign_chefetab');
	$rn_sign_pp=mysql_result($call_nom_class, 0, 'rn_sign_pp');
	$rn_sign_resp=mysql_result($call_nom_class, 0, 'rn_sign_resp');
	$rn_sign_nblig=mysql_result($call_nom_class, 0, 'rn_sign_nblig');
	// =========================
} else {
	$classe = '';
	$nom_complet = '';
	$suivi_par = '';
	$formule = '';
	$format_nom = 'np';
	$display_rang = 'n';
	$display_address = 'n';
	$display_coef = 'n';
	$display_mat_cat = 'n';
	$display_nbdev = 'n';
	$display_moy_gen = 'n';
	$modele_bulletin_pdf = NULL;

	// =========================
	// AJOUT: boireaus
	$rn_nomdev='n';
	$rn_toutcoefdev='n';
	$rn_coefdev_si_diff='n';
	$rn_datedev='n';
	$rn_formule='';
	$rn_sign_chefetab='n';
	$rn_sign_pp='n';
	$rn_sign_resp='n';
	$rn_sign_nblig=3;
	// =========================
}

?>
<form enctype="multipart/form-data" action="modify_nom_class.php" method=post>
<p>Nom court de la classe : <input type=text size=30 name=reg_class_name value = "<?php echo $classe; ?>" /></p>
<p>Nom complet de la classe : <input type=text size=50 name=reg_nom_complet value = "<?php echo $nom_complet; ?>" /></p>
<p>Prénom et nom du chef d'établissement ou de son représentant apparaissant en bas de chaque bulletin : <br /><input type=text size=30 name=reg_suivi_par value = "<?php echo $suivi_par; ?>" /></p>
<p>Formule à insérer sur les bulletins (cette formule sera suivie des nom et prénom de la personne désignée ci_dessus :<br /> <input type=text size=80 name=reg_formule value = "<?php echo $formule; ?>" /></p>

<p><b>Formatage de l'identité des professeurs pour les bulletins :</b>
<br /><br /><input type="radio" name="reg_format" value="<?php echo "np"; ?>" <?php if ($format_nom=="np") echo " checked "; ?>/>Nom Prénom (Durand Albert)
<br /><input type="radio" name="reg_format" value="<?php echo "pn"; ?>" <?php if ($format_nom=="pn") echo " checked "; ?>/>Prénom Nom (Albert Durand)
<br /><input type="radio" name="reg_format" value="<?php echo "in"; ?>" <?php   if ($format_nom=="in") echo " checked "; ?>/>Initiale-Prénom Nom (A. Durand)
<br /><input type="radio" name="reg_format" value="<?php echo "ni"; ?>" <?php   if ($format_nom=="ni") echo " checked "; ?>/>Initiale-Prénom Nom (Durand A.)
<br /><input type="radio" name="reg_format" value="<?php echo "cnp"; ?>" <?php   if ($format_nom=="cnp") echo " checked "; ?>/>Civilité Nom Prénom (M. Durand Albert)
<br /><input type="radio" name="reg_format" value="<?php echo "cpn"; ?>" <?php   if ($format_nom=="cpn") echo " checked "; ?>/>Civilité Prénom Nom (M. Albert Durand)
<br /><input type="radio" name="reg_format" value="<?php echo "cin"; ?>" <?php   if ($format_nom=="cin") echo " checked "; ?>/>Civ. initiale-Prénom Nom (M. A. Durand)
<br /><input type="radio" name="reg_format" value="<?php echo "cni"; ?>" <?php   if ($format_nom=="cni") echo " checked "; ?>/>Civ. Nom initiale-Prénom  (M. Durand A.)

<input type=hidden name=is_posted value=1 />
<?php if (isset($id_classe)) {echo "<input type=hidden name=id_classe value=$id_classe />";} ?>
<br />
<br />
<!-- ========================================= -->
<table style="border: 0;" cellpadding="5" cellspacing="5">
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres généraux : </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">
    Afficher les catégories de matières sur le bulletin (HTML), les relevés de notes (HTML), et les outils de visualisation :
    </td>
    <td><input type="checkbox" value="y" name="display_mat_cat"  <?php   if ($display_mat_cat=="y") echo " checked "; ?> />
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
	Paramétrage des catégories de matière pour cette classe (uniquement si case ci-dessus cochée)
	</td>
	<td>
		<table style='border: 1px solid black;'>
		<tr>
			<td style='width: auto;'>Catégorie</td><td style='width: 100px; text-align: center;'>Priorité d'affichage</td><td style='width: 100px; text-align: center;'>Afficher la moyenne sur le bulletin</td>
		</tr>
		<?php
		$get_cat = mysql_query("SELECT id, nom_court, priority FROM matieres_categories");
		while ($row = mysql_fetch_array($get_cat, MYSQL_ASSOC)) {
			// Pour la catégorie, on récupère les infos déjà enregistrées pour la classe
			if (isset($id_classe)) {
				$infos = mysql_fetch_object(mysql_query("SELECT priority, affiche_moyenne FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] ."' and classe_id = '" . $id_classe . "')"));
			} else {
				$infos = false;
			}
			if (!$infos) {
				$current_priority = $row["priority"];
				$current_affiche_moyenne = "0";
			} else {
				$current_priority = $infos->priority;
				$current_affiche_moyenne = $infos->affiche_moyenne;
			}

			echo "<tr>\n";
			echo "<td style='padding: 5px;'>".$row["nom_court"]."</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
					echo "<select name='priority_".$row["id"]."' size='1'>\n";
					for ($i=0;$i<11;$i++) {
						echo "<option value='$i'";
						if ($current_priority == $i) echo " SELECTED";
						echo ">$i</option>\n";
					}
					echo "</select>\n";
			echo "</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
				echo "<input type='checkbox' name='moyenne_".$row["id"]."'";
				if ($current_affiche_moyenne == '1') echo " CHECKED";
				echo " />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}
		?>
		</table>
</td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres bulletin HTML : </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps; width: 35%;">
    Afficher sur le bulletin le rang de chaque élève&nbsp;:
    </td>
    <td><input type="checkbox" value="y" name="display_rang"  <?php   if ($display_rang=="y") echo " checked "; ?> />
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">
    Afficher le bloc adresse du responsable de l'élève :
    </td>
    <td><input type="checkbox" value="y" name="display_address"  <?php   if ($display_address=="y") echo " checked "; ?> />
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">
    Afficher les coefficients des matières (uniquement si au moins un coef différent de 0) :
    </td>
    <td><input type="checkbox" value="y" name="display_coef"  <?php   if ($display_coef=="y") echo " checked "; ?> />
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">
    Afficher les moyennes générales sur les bulletins (uniquement si au moins un coef différent de 0) :
    </td>
    <td><input type="checkbox" value="y" name="display_moy_gen"  <?php   if ($display_moy_gen=="y") echo " checked "; ?> />
    </td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">
    Afficher le nombre de devoirs sur le bulletin :
    </td>
    <td><input type="checkbox" value="y" name="display_nbdev"  <?php   if ($display_nbdev=="y") echo " checked "; ?> />
    </td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres bulletin PDF : </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
	   Sélectionner le modèle de bulletin pour l'impression en PDF :
	</td>
	<td><?PHP
	    // Pour la classe, quel est le modèle de bulletin déja selectionné
	    $quel_modele=$modele_bulletin_pdf;

		//echo $quel_modele;
		echo "<select tabindex=\"5\" name=\"modele_bulletin\">";
		if ($quel_modele == NULL) {
		   echo "<option value=\"NULL\" selected=\"selected\" >Aucun modèle de sélectionné</option>";
		}
		// sélection des modèle des bulletins.
	    $requete_modele = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
		while($donner_modele = mysql_fetch_array($requete_modele)) {
		    echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
			if($quel_modele==$donner_modele['id_model_bulletin']) {
			    echo "selected=\"selected\"";
			}
			echo ">".ucfirst($donner_modele['nom_model_bulletin'])."</option>\n";
		}
		 echo "</select>\n";
		?>
	</td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
	  <h2><b>Paramètres des relevés de notes : </b></h2>
	</td>
</tr>
<!--
Afficher le nom des devoirs.
Afficher tous les coefficients des devoirs.
Afficher les coefficients des devoirs si des coefficients différents
> > sont présents.
Afficher les dates des devoirs.
> >
> >Et
Afficher un texte... (correspondant à ta demande)
> >Et encore
Afficher une case pour la signature des parents/responsables
Afficher une case pour la signature du prof principal
Afficher une case pour la signature du chef d'établissement
-->
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher le nom des devoirs :</td>
    <td><input type="checkbox" value="y" name="rn_nomdev"  <?php   if ($rn_nomdev=="y") echo " checked "; ?> /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher tous les coefficients des devoirs :</td>
    <td><input type="checkbox" value="y" name="rn_toutcoefdev"  <?php   if ($rn_toutcoefdev=="y") echo " checked "; ?> /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher les coefficients des devoirs si des coefficients différents sont présents :</td>
    <td><input type="checkbox" value="y" name="rn_coefdev_si_diff"  <?php   if ($rn_coefdev_si_diff=="y") echo " checked "; ?> /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher les dates des devoirs :</td>
    <td><input type="checkbox" value="y" name="rn_datedev"  <?php   if ($rn_datedev=="y") echo " checked "; ?> /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td>Formule/Message à insérer sous le relevé de notes :</td>
	<td><input type=text size=40 name="rn_formule" value="<?php echo $rn_formule; ?>" /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature du chef d'établissement :</td>
    <td><input type="checkbox" value="y" name="rn_sign_chefetab"  <?php   if ($rn_sign_chefetab=="y") echo " checked "; ?> /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature du prof principal :</td>
    <td><input type="checkbox" value="y" name="rn_sign_pp"  <?php   if ($rn_sign_pp=="y") echo " checked "; ?> /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Afficher une case pour la signature des parents/responsables :</td>
    <td><input type="checkbox" value="y" name="rn_sign_resp"  <?php   if ($rn_sign_resp=="y") echo " checked "; ?> /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
    <td style="font-variant: small-caps;">Nombre de lignes pour la signature :</td>
    <td><input type="text" name="rn_sign_nblig" value="<?php echo $rn_sign_nblig;?>" /></td>
</tr>

</table>
<center><input type=submit value="Enregistrer" style="margin-top: 30px; margin-bottom: 100px;" /></center>
</form>
<?php require("../lib/footer.inc.php");?>
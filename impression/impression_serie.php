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

//INSERT INTO droits VALUES ('/impression/impression_serie.php', 'V', 'V', 'V', 'V', 'V', 'V', 'Impression des listes (PDF)', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

$id_choix_periode=isset($_POST['id_choix_periode']) ? $_POST["id_choix_periode"] : 0;

//**************** EN-TETE **************************************
$titre_page = "Impression de listes au format PDF";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
//debug_var();
echo "<p class='bold'>";
echo "<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if ($id_choix_periode != 0) {
	echo " | <a href='./impression_serie.php'>Autre période</a>";
}
echo " | <a href='./impression.php'>Impression rapide à l'unité</a>";
echo " | <a href='./parametres_impression_pdf.php'>Régler les paramètres du PDF</a>";
if(check_droit_acces('/groupes/mes_listes.php',$_SESSION['statut'])) {
	echo " | <a href='../groupes/mes_listes.php'>Listes CSV</a>";
}
echo "</p>\n";

if ($id_choix_periode != 0) {
	$periode = "Période N°".$id_choix_periode;
	echo "<h3 align='center'>".$periode."</h3>\n";
	echo "<h3>Liste des classes : ";
	echo "</h3>\n";
} else {
	$periode="";
	echo "<h3>Liste des classes : ";
	echo "</h3>\n";
}

// sélection multiple avec choix de la période

if ($id_choix_periode == 0) {
	echo "<div style=\"text-align: center;\">\n";
	echo "<fieldset style='border: 1px solid grey;";
	echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "'>\n";
	echo "<legend style='border: 1px solid grey;";
	//echo "background-image: url(\"../images/background/opacite50.png\"); ";
	echo "background-color: white; ";
	echo "'>Sélectionnez la période pour laquelle vous souhaitez imprimer les listes.</legend>\n";
	echo "<form method=\"post\" action=\"impression_serie.php\" name=\"imprime_serie\">\n";
	$requete_periode = "SELECT DISTINCT `num_periode` FROM `periodes`";
	$resultat_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.mysqli_error($GLOBALS["mysqli"]));
	echo "<br />\n";
	While ( $data_periode = mysqli_fetch_array($resultat_periode)) {
		echo "<label for='id_choix_periode".$data_periode['num_periode']."' style='cursor: pointer;'>Période ".$data_periode['num_periode']." : </label><input type='radio' name='id_choix_periode' id='id_choix_periode".$data_periode['num_periode']."' value='".$data_periode['num_periode']."' /> <br />\n";
	}
	echo "<br /><br /> <input value=\"Valider la période\" name=\"Valider\" type=\"submit\" />\n
			<br />\n";

	echo "</form>\n";
	echo "</fieldset>\n";

	echo "</div>\n";
	echo "<br />";

}
else {
	$id_modele_defaut="";
	// Rechercher les modèles existants... proposer le modèle par défaut de l'utilisateur mais aussi le modèle par défaut de gepi
	$sql="SELECT * FROM modeles_grilles_pdf WHERE login='".$_SESSION['login']."' ORDER BY nom_modele;";
	//echo "$sql<br />";
	$res_modeles=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_modeles=mysqli_num_rows($res_modeles);
	$cpt=0;
	if($nb_modeles>0) {
		while($lig=mysqli_fetch_object($res_modeles)) {
			$tab_modele[$cpt]['id_modele']=$lig->id_modele;
			$tab_modele[$cpt]['nom_modele']=$lig->nom_modele;

			if($lig->par_defaut=='y') {
				$id_modele_defaut=$lig->id_modele;
			}

			$cpt++;
		}
	}

	if(isset($_SESSION['id_modele'])) {
		$id_modele_defaut=$_SESSION['id_modele'];
	}

	if(!isset($_POST['passer_au_choix_des_groupes'])) {
		echo "<div style=\"text-align: center;\">\n";
		echo "<fieldset style='border: 1px solid grey;";
		echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "'>\n";

		echo "<legend style='border: 1px solid grey;";
		//echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "background-color: white; ";
		echo "'>Sélectionnez la (ou les) classe(s) pour lesquelles vous souhaitez imprimer les listes.</legend>\n";

		echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf\">\n";
		if ($id_choix_periode != 0) {
			echo "<br />\n";

			echo "<select id='liste_classes' name='id_liste_classes[]' multiple='yes' size='5'>\n";
			if($_SESSION['statut']=='scolarite') { //n'affiche que les classes du profil scolarité
				$login_scolarite = $_SESSION['login'];
				$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jsc.login, jsc.id_classe
								FROM `periodes`, `classes` , `j_scol_classes` jsc
								WHERE (jsc.login='$login_scolarite'
								AND jsc.id_classe=classes.id
								AND `periodes`.`num_periode` = ".$id_choix_periode."
								AND `classes`.`id` = `periodes`.`id_classe`)
								ORDER BY `nom_complet` ASC";
			}
			else {
				$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` FROM `periodes`, `classes` WHERE `periodes`.`num_periode` = ".$id_choix_periode." AND `classes`.`id` = `periodes`.`id_classe` ORDER BY `nom_complet` ASC";
			}
			$resultat_classe = mysqli_query($GLOBALS["mysqli"], $requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
			echo "		<optgroup label=\"-- Les classes --\">\n";
			While ( $data_classe = mysqli_fetch_array($resultat_classe)) {
						echo "		<option value=\"";
						echo $data_classe['id_classe'];
						echo "\">";
						echo $data_classe['nom_complet']." (".$data_classe['classe'].")";
						echo "</option>\n";
			}
			echo "		</optgroup>\n";
			echo "	</select>\n";
			echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";

			// Rechercher les modèles existants... proposer le modèle par défaut de l'utilisateur mais aussi le modèle par défaut de gepi
			/*
			$sql="SELECT * FROM modeles_grilles_pdf WHERE login='".$_SESSION['login']."' ORDER BY nom_modele;";
			echo "$sql<br />";
			$res_modeles=mysql_query($sql);
			$nb_modeles=mysql_num_rows($res_modeles);
			$cpt=0;
			*/
			if($nb_modeles>0) {
				/*
				while($lig=mysql_fetch_object($res_modeles)) {
					$tab_modele[$cpt]['id_modele']=$lig->id_modele;
					$tab_modele[$cpt]['nom_modele']=$lig->nom_modele;
					$cpt++;
				}
				*/

				echo "<br />\n";
				echo "Modèle de réglages&nbsp;: <br /><select name='id_modele'>\n";
				echo "<option value=''>Réglages par défaut de Gepi</option>\n";
				for($loop=0;$loop<count($tab_modele);$loop++) {
					echo "<option value='".$tab_modele[$loop]['id_modele']."'";
					if($tab_modele[$loop]['id_modele']==$id_modele_defaut) {echo " selected='true'";}
					echo ">".$tab_modele[$loop]['nom_modele']."</option>\n";
				}
				echo "</select>\n";
			}
			else {
				// On utilisera les paramètres par défaut
			}

			echo "<br /><br /> <input value=\"Valider les classes\" name=\"Valider\" type=\"submit\" />\n";
			echo "<br />\n";
		}
		echo "</form>\n";
		echo "</fieldset>\n";

		if($_SESSION['statut']=='scolarite') {
			echo "<br />\n";
			echo "<p align='left'>Ou</p>\n";
	
			echo "<h3 align='left'>Liste des enseignements : </h3>\n";

			echo "<fieldset style='border: 1px solid grey;";
			echo "background-image: url(\"../images/background/opacite50.png\"); ";
			echo "'>\n";

			echo "<legend style='border: 1px solid grey;";
			//echo "background-image: url(\"../images/background/opacite50.png\"); ";
			echo "background-color: white; ";
			echo "'>Sélectionnez la (ou les) classe(s) dans laquelle/lesquelles rechercher des listes de groupes.</legend>\n";
			//echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf\">\n";
			echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name=\"choix_clas_grp\">\n";
			if ($id_choix_periode != 0) {
				echo "<br />\n";
	
				echo "<select id='liste_classes' name='id_liste_classes[]' multiple='yes' size='5'>\n";
				if($_SESSION['statut']=='scolarite') { //n'affiche que les classes du profil scolarité
					$login_scolarite = $_SESSION['login'];
					$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` , jsc.login, jsc.id_classe
									FROM `periodes`, `classes` , `j_scol_classes` jsc
									WHERE (jsc.login='$login_scolarite'
									AND jsc.id_classe=classes.id
									AND `periodes`.`num_periode` = ".$id_choix_periode."
									AND `classes`.`id` = `periodes`.`id_classe`)
									ORDER BY `nom_complet` ASC";
				}
				else {
					$requete_classe = "SELECT `periodes`.`id_classe`, `classes`.`classe`, `classes`.`nom_complet` FROM `periodes`, `classes` WHERE `periodes`.`num_periode` = ".$id_choix_periode." AND `classes`.`id` = `periodes`.`id_classe` ORDER BY `nom_complet` ASC";
				}
				$resultat_classe = mysqli_query($GLOBALS["mysqli"], $requete_classe) or die('Erreur SQL !'.$requete_classe.'<br />'.mysqli_error($GLOBALS["mysqli"]));
				echo "		<optgroup label=\"-- Les classes --\">\n";
				While ( $data_classe = mysqli_fetch_array($resultat_classe)) {
							echo "		<option value=\"";
							echo $data_classe['id_classe'];
							echo "\">";
							echo $data_classe['nom_complet']." (".$data_classe['classe'].")";
							echo "</option>\n";
				}
				echo "		</optgroup>\n";
				echo "	</select>\n";

				if($nb_modeles>0) {
					echo "<br />\n";
					echo "Modèle de réglages&nbsp;: <br /><select name='id_modele'>\n";
					echo "<option value=''>Réglages par défaut de Gepi</option>\n";
					for($loop=0;$loop<count($tab_modele);$loop++) {
						echo "<option value='".$tab_modele[$loop]['id_modele']."'";
						if($tab_modele[$loop]['id_modele']==$id_modele_defaut) {echo " selected='true'";}
						echo ">".$tab_modele[$loop]['nom_modele']."</option>\n";
					}
					echo "</select>\n";
				}
				else {
					// On utilisera les paramètres par défaut
				}

				//echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
				echo "<input value=\"".$id_choix_periode."\" name=\"id_choix_periode\" type=\"hidden\" />\n";
				echo "<input type=\"hidden\" name=\"passer_au_choix_des_groupes\" value='y' />\n";
				echo "<br /><br /> <input value=\"Valider les enseignements\" name=\"Valider\" type=\"submit\" />\n";
				echo "<br />\n";
			}
			echo "</form>\n";
			echo "</fieldset>\n";
		}
	
		echo "</div>\n";
		echo "<br />";
	}
	else {

		$id_liste_classes=isset($_POST['id_liste_classes']) ? $_POST["id_liste_classes"] : NULL;

		if(!isset($id_liste_classes)) {
			echo "<p>Il faut choisir au moins une classe.</p>\n";
			echo "<p><a href='".$_SERVER['PHP_SELF']."'>Retour</a></p>\n";
		}
		else {

			echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf\">\n";

			$cpt=0;
			echo "<p>Choisissez les groupes&nbsp;:</p>\n";

			for($i=0;$i<count($id_liste_classes);$i++) {
				echo "<div style='float:left; margin:1em; width:25em; border:1px solid black;'>\n";
				echo "<p class='bold'>Enseignements de ".get_class_from_id($id_liste_classes[$i])."</p>\n";
				$sql="SELECT DISTINCT jgc.id_groupe FROM j_groupes_classes jgc, groupes g WHERE g.id=jgc.id_groupe AND jgc.id_classe='".$id_liste_classes[$i]."' ORDER BY g.name;";
				//echo "$sql<br />";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					echo "<p style='color:red'>Aucun enseignement dans cette classe.</p>\n";
				}
				else {
					//$tab_champs_grp=array('classes','profs');
					$tab_champs_grp=array('classes');
					while($lig=mysqli_fetch_object($res)) {
						$current_group=get_group($lig->id_groupe,$tab_champs_grp);
						echo "<input type='checkbox' name='id_liste_groupes[]' id='id_groupe_$cpt' value='$lig->id_groupe' onchange='change_style_grp($cpt)' />\n";
						echo "<label for='id_groupe_".$cpt."' id='label_id_groupe_".$cpt."'>".$current_group['name']." (<span style='font-size:small;'>".$current_group['description'];
						if(strstr($current_group['classlist_string'],",")) {echo " en ".$current_group['classlist_string'];}
						echo "</span>)</label><br />\n";
						$cpt++;
					}
				}
				echo "</div>\n";
			}

			echo "<br />\n";
			echo "Option de tri&nbsp;:<br />\n";
			echo "<input type=\"radio\" name=\"tri\" id='tri_classe' value=\"classes\" /><label for='tri_classe''> Par classe puis alphabétique</label><br />\n";
			echo "<input type=\"radio\" name=\"tri\" id='tri_alpha' value=\"alpha\" checked /><label for='tri_alpha''> Alphabétique</label><br />\n";

			// Rechercher les modèles existants... proposer le modèle par défaut de l'utilisateur mais aussi le modèle par défaut de gepi
			if($nb_modeles>0) {
				echo "<br />\n";
				echo "Modèle de réglages&nbsp;: <br /><select name='id_modele'>\n";
				echo "<option value=''>Réglages par défaut de Gepi</option>\n";
				for($loop=0;$loop<count($tab_modele);$loop++) {
					echo "<option value='".$tab_modele[$loop]['id_modele']."'";
					if($tab_modele[$loop]['id_modele']==$id_modele_defaut) {echo " selected='true'";}
					echo ">".$tab_modele[$loop]['nom_modele']."</option>\n";
				}
				echo "</select>\n";
			}
			else {
				// On utilisera les paramètres par défaut
			}

			//echo "<input value=\"".$id_choix_periode."\" name=\"id_choix_periode\" type=\"hidden\" />\n";
			echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
			echo "<br /><br /> <input value=\"Valider les enseignements\" name=\"Valider\" type=\"submit\" />\n";
			echo "</form>\n";

			echo "<script type='text/javascript'>
	function change_style_grp(num) {
		if(document.getElementById('id_groupe_'+num)) {
			if(document.getElementById('id_groupe_'+num).checked) {
				document.getElementById('label_id_groupe_'+num).style.fontWeight='bold';
			}
			else {
				document.getElementById('label_id_groupe_'+num).style.fontWeight='normal';
			}
		}
	}
</script>\n";

		}
	}
}

if ($id_choix_periode != 0) {
	// Dans le cadre d'un professeur il peut choisir ses enseignements.
	if($_SESSION['statut']=='professeur') {
		echo "<h3>Liste des enseignements : </h3>\n";
		
		$groups=get_groups_for_prof($_SESSION["login"]);
		
		/*
		echo "<pre>";
			print_r($groups);
		echo "</pre>";
		*/
		
		// sélection multiple avec choix de la période
		echo "<div style=\"text-align: center;\">\n";
		echo "<fieldset style='border: 1px solid grey;";
		echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "'>\n";

		echo "<legend style='border: 1px solid grey;";
		//echo "background-image: url(\"../images/background/opacite50.png\"); ";
		echo "background-color: white; ";
		echo "'>Sélectionnez le (ou les) enseignement(s) pour lesquels vous souhaitez imprimer les listes.</legend>\n";
		
		//echo "<form method=\"post\" action=\"liste_pdf.php\" name=\"imprime_pdf\">\n";
		echo "<form method=\"post\" action=\"liste_pdf.php\" target='_blank' name=\"imprime_pdf2\">\n";
		echo "<br />\n";
		//echo "<select id='liste_classes' name='id_liste_groupes[]' multiple='yes' size='5'>\n";
		echo "<select id='liste_groupes' name='id_liste_groupes[]' multiple='yes' size='5'>\n";
		echo "		<optgroup label=\"-- Les enseignements --\">\n";
		
		for($i=0;$i<count($groups);$i++){				
			echo "		<option value=\"";
			echo $groups[$i]['id'];
			echo "\">";
			echo $groups[$i]['matiere']['nom_complet']." (".$groups[$i]['classlist_string'].")";
			echo "</option>\n";			
		}	
		echo "		</optgroup>\n";
		echo "	</select>\n";
		//echo "<br />Option de tri :<input type=\"radio\" name=\"tri\" value=\"classes\" />Par classe puis alphabétique<input type=\"radio\" name=\"tri\" value=\"alpha\" checked /> Alphabétique<br />\n";

		echo "<br />\n";
		echo "Option de tri&nbsp;:<br />\n";
		echo "<input type=\"radio\" name=\"tri\" id='tri_classe' value=\"classes\" /><label for='tri_classe''> Par classe puis alphabétique</label><br />\n";
		echo "<input type=\"radio\" name=\"tri\" id='tri_alpha' value=\"alpha\" checked /><label for='tri_alpha''> Alphabétique</label><br />\n";

		// Rechercher les modèles existants... proposer le modèle par défaut de l'utilisateur mais aussi le modèle par défaut de gepi
		if($nb_modeles>0) {
			echo "<br />\n";
			echo "Modèle de réglages&nbsp;: <br /><select name='id_modele'>\n";
			echo "<option value=''>Réglages par défaut de Gepi</option>\n";
			for($loop=0;$loop<count($tab_modele);$loop++) {
				echo "<option value='".$tab_modele[$loop]['id_modele']."'";
				if($tab_modele[$loop]['id_modele']==$id_modele_defaut) {echo " selected='true'";}
				echo ">".$tab_modele[$loop]['nom_modele']."</option>\n";
			}
			echo "</select>\n";
		}
		else {
			// On utilisera les paramètres par défaut
		}


		echo "<input value=\"".$id_choix_periode."\" name=\"id_periode\" type=\"hidden\" />\n";
		echo "<br /><br /> <input value=\"Valider les enseignements\" name=\"Valider\" type=\"submit\" />\n";
	}
		echo "<br />\n
		</form>\n
	</fieldset>\n
	</div>";
}

echo "<br />
<p style='text-indent:-4em; margin-left:4em;'><em>NOTE&nbsp;:</em> Vous pouvez définir plusieurs modèles de grille en suivant le lien <a href='./parametres_impression_pdf.php'>Régler les paramètres du PDF</a>.<br />
Un tableau entièrement quadrillé peut permettre de pointer des oublis, de noter des prises de parole,...<br />
Un tableau avec deux ou trois colonnes pour par exemple Nom, Moyenne et Avis (<em>avec une colonne large pour l'avis<em>) peut être pratique pour le ".getSettingValue('gepi_prof_suivi')." afin de demander à ses collègues un bref compte-rendu avant une rencontre parents/professeurs.</p>\n";

// Fin de sélection multiple avec choix de la période.
require("../lib/footer.inc.php");
?>

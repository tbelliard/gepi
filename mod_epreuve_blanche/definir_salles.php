<?php
/* $Id: definir_salles.php 6760 2011-04-09 17:54:37Z crob $ */
/*
* Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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



$sql="SELECT 1=1 FROM droits WHERE id='/mod_epreuve_blanche/definir_salles.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_epreuve_blanche/definir_salles.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Epreuve blanche: Définir les salles',
statut='';";
$insert=mysql_query($sql);
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

if(isset($definition_salles)) {
	check_token();

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
	}
	else {
		$lig=mysql_fetch_object($res);
		$etat=$lig->etat;
	
		if($etat!='clos') {

			$salles=isset($_POST['salles']) ? $_POST['salles'] : (isset($_GET['salles']) ? $_GET['salles'] : NULL);
			$salle=isset($_POST['salle']) ? $_POST['salle'] : (isset($_GET['salle']) ? $_GET['salle'] : array());
			$id_salle=isset($_POST['id_salle']) ? $_POST['id_salle'] : (isset($_GET['id_salle']) ? $_GET['id_salle'] : array());
			$suppr_salle=isset($_POST['suppr_salle']) ? $_POST['suppr_salle'] : (isset($_GET['suppr_salle']) ? $_GET['suppr_salle'] : array());

			$id_salle_existante=isset($_POST['id_salle_existante']) ? $_POST['id_salle_existante'] : NULL;
			$id_salle_cours_existante=isset($_POST['id_salle_cours_existante']) ? $_POST['id_salle_cours_existante'] : NULL;

			$msg="";

			// Modification des salles inscrites
			for($i=0;$i<count($id_salle);$i++) {
				if(in_array($id_salle[$i],$suppr_salle)) {
					$sql="UPDATE eb_copies SET id_salle='-1' WHERE id_salle='".$id_salle[$i]."' AND id_epreuve='$id_epreuve';";
					$nettoyage=mysql_query($sql);
					if(!$nettoyage) {
						$msg.="Erreur lors du nettoyage dans 'eb_copies' de la salle supprimée.<br />";
					}
					else {
						$sql="DELETE FROM eb_salles WHERE id='".$id_salle[$i]."' AND id_epreuve='$id_epreuve';";
						$suppr=mysql_query($sql);
						if(!$suppr) {
							$msg.="Erreur lors de la suppression de la salle $salle[$i].<br />";
						}
						else {
							$msg.="Suppression de la salle $salle[$i] effectuée.<br />";
						}
					}
				}
				else {
					$temp_salle=remplace_accents(trim($salle[$i]),'all_nospace');
					if($temp_salle=='') {
						$msg.="Le nom de la salle ne convient pas.<br />";
						// Ou alors on supprime la salle
					}
					else {
						// Ne pas renommer une salle au même nom qu'un salle existante pour l'épreuve
						$sql="SELECT id FROM eb_salles WHERE salle='".$temp_salle."' AND id_epreuve='$id_epreuve';";
						$res=mysql_query($sql);
						if(mysql_num_rows($res)==0) {
							$sql="UPDATE eb_salles SET salle='".$temp_salle."' WHERE id='".$id_salle[$i]."' AND id_epreuve='$id_epreuve';";
							$update=mysql_query($sql);
							if(!$update) {$msg.="Erreur lors de la mise à jour de la salle n°$id_salle[$i]<br />";}
						}
						else {
							// On n'affiche un avertissement que si ce n'est pas la salle courante que l'on renomme au même nom
							$lig=mysql_fetch_object($res);
							if($lig->id!=$id_salle[$i]) {$msg.="Une autre salle porte le même nom.<br />";}
						}
					}
				}
			}

			// Ajout de salles
			$tab_salles=array();
			$tab_id_salles=array();
			//echo "\$salles=$salles<br />";
			if((isset($salles))&&($salles!="")) {
				$tab=explode(",",$salles);
			
				//$tab_salles=array();
				//$tab_id_salles=array();
				$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {
						$tab_salles[]=$lig->salle;
						$tab_id_salles[]=$lig->id;
					}
				}
		
				for($i=0;$i<count($tab);$i++) {
					// A faire: virer les espaces en début et fin de chaine
					//$salle=remplace_accents(trim($tab[$i]),'all_nospace');
					//$salle=remplace_accents(ereg_replace("^[ ]","",trim($tab[$i])),'all_nospace');
					$salle=remplace_accents(trim($tab[$i]),'all_nospace');
					// Ne pas ajouter une salle de même nom qu'un salle existante pour l'épreuve
					//if(in_array($tab[$i],$tab_salles)) {
					//	$msg.="Une autre salle porte le même nom&nbsp;: '$tab[$i]'<br />";
					if(in_array($salle,$tab_salles)) {
						$msg.="Une autre salle porte le même nom&nbsp;: '$salle'<br />";
					}
					else {
						//$sql="INSERT INTO eb_salles SET salle='".$tab[$i]."', id_epreuve='$id_epreuve';";
						$sql="INSERT INTO eb_salles SET salle='".$salle."', id_epreuve='$id_epreuve';";
						$insert=mysql_query($sql);
						//if(!$insert) {$msg.="Erreur lors de l'ajout de la salle '$tab[$i]'<br />";}
						//else {$msg.="Salle '$tab[$i]' ajoutée.<br />";}
						if(!$insert) {$msg.="Erreur lors de l'ajout de la salle '$salle'<br />";}
						else {$msg.="Salle '$salle' ajoutée.<br />";}
					}
				}
			}

			$tab_salle_existante=array();
			if(isset($id_salle_existante)) {
				for($i=0;$i<count($id_salle_existante);$i++) {
					$sql="SELECT salle FROM eb_salles WHERE id='$id_salle_existante[$i]';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						// La salle existe

						$lig=mysql_fetch_object($res);
						//if((!in_array($lig->salle,$salle))&&(!in_array($lig->salle,$tab_salles))) {
						if(((!in_array($lig->salle,$tab_salles))&&(!is_array($salle)))||
						((is_array($salle))&&(!in_array($lig->salle,$salle))&&(!in_array($lig->salle,$tab_salles))))
						 {
							$sql="INSERT INTO eb_salles SET salle='".$lig->salle."', id_epreuve='$id_epreuve';";
							$insert=mysql_query($sql);
							if(!$insert) {$msg.="Erreur lors de l'ajout de la salle '$lig->salle'<br />";}
							else {$msg.="Salle '$lig->salle' ajoutée.<br />";}
							$tab_salle_existante[]=$lig->salle;
						}
					}
				}
			}

			if(isset($id_salle_cours_existante)) {
				for($i=0;$i<count($id_salle_cours_existante);$i++) {
					$sql="SELECT nom_salle FROM salle_cours WHERE id_salle='$id_salle_cours_existante[$i]';";
					$res=mysql_query($sql);
					if(mysql_num_rows($res)>0) {
						// La salle existe

						$lig=mysql_fetch_object($res);
						if((!in_array($lig->nom_salle,$salle))&&(!in_array($lig->nom_salle,$tab_salles))&&(!in_array($lig->nom_salle,$tab_salle_existante))) {
							$sql="INSERT INTO eb_salles SET salle='".$lig->nom_salle."', id_epreuve='$id_epreuve';";
							$insert=mysql_query($sql);
							if(!$insert) {$msg.="Erreur lors de l'ajout de la salle '$lig->nom_salle'<br />";}
							else {$msg.="Salle '$lig->nom_salle' ajoutée.<br />";}
						}
					}
				}
			}

		}
		else {
			$msg="L'épreuve choisie (<i>$id_epreuve</i>) est close.\n";
		}
	}
}
elseif(isset($_POST['valide_affect_eleves'])) {
	check_token();

	$login_ele=isset($_POST['login_ele']) ? $_POST['login_ele'] : (isset($_GET['login_ele']) ? $_GET['login_ele'] : array());
	$id_salle_ele=isset($_POST['id_salle_ele']) ? $_POST['id_salle_ele'] : (isset($_GET['id_salle_ele']) ? $_GET['id_salle_ele'] : array());

	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$msg="L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.\n";
	}
	else {
		$lig=mysql_fetch_object($res);
		$etat=$lig->etat;
	
		if($etat!='clos') {
			$msg="";
			for($i=0;$i<count($login_ele);$i++) {
				$sql="UPDATE eb_copies SET id_salle='$id_salle_ele[$i]' WHERE id_epreuve='$id_epreuve' AND login_ele='$login_ele[$i]'";
				$update=mysql_query($sql);
				if(!$update) {$msg.="Erreur lors de la mise à jour de la salle n°$id_salle_ele[$i] pour $login_ele[$i].<br />";}
			}
		
			if(($msg=="")&&(count($login_ele)>0)) {$msg="Enregistrement effectué.";}
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
$titre_page = "Epreuve blanche: Définition des salles";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

//echo "<div class='noprint'>\n";
//echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\" name='form1'>\n";
echo "<p class='bold'><a href='index.php?id_epreuve=$id_epreuve&amp;mode=modif_epreuve'";
echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
echo ">Retour</a>";
//echo "</p>\n";
//echo "</div>\n";

//if(!isset($definition_salles)) {
//==================================================================
if(!isset($mode)) {
	// Définition des salles
	echo "</p>\n";

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysql_fetch_object($res);
	$etat=$lig->etat;

	echo "<blockquote>\n";
	echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
	if($lig->description!='') {
		echo nl2br(trim($lig->description))."<br />\n";
	}
	else {
		echo "Pas de description saisie.<br />\n";
	}
	echo "</blockquote>\n";

	// Définir les salles
	if($etat!='clos') {
		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
		echo add_token_field();
	}

	$menu_a_droite="";

	$salles="";
	$sql="SELECT * FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
	$res=mysql_query($sql);
	$nb_salles_epreuve_courante=mysql_num_rows($res);
	$tab_salles=array();
	$tab_id_salles=array();
	if($nb_salles_epreuve_courante>0) {
		// Parcours des salles déjà définies pour cette épreuve:
		while($lig=mysql_fetch_object($res)) {
			$tab_salles[]=$lig->salle;
			$tab_id_salles[]=$lig->id;
		}
		//$nb_salles_epreuve_courante=count($tab_salles);

		/*
		echo "<div style='float:right; width:18em; text-align:center; border: 1px solid black;'>\n";
		echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=affect_eleves&amp;id_epreuve=$id_epreuve'";
		echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		echo ">Affecter les élèves dans les salles</a>.</p>";
		echo "</div>\n";
		*/
		$menu_a_droite.="<div style='float:right; width:18em;'>\n";

		$menu_a_droite.="<div style='text-align:center; border: 1px solid black;'>\n";
		$menu_a_droite.="<p><a href='".$_SERVER['PHP_SELF']."?mode=affect_eleves&amp;id_epreuve=$id_epreuve'";
		$menu_a_droite.=" onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		$menu_a_droite.=">Affecter les élèves dans les salles</a>.</p>";
		$menu_a_droite.="</div>\n";

	}

	// Choisir des salles parmi les salles auparavant définies... et qui ne sont pas déjà choisies
	$sql="SELECT * FROM eb_salles ORDER BY salle;";
	//echo "$sql<br />";
	$res2=mysql_query($sql);
	if(mysql_num_rows($res2)>0) {
		$chaine_salles_existantes="";
		$tab_salles_existantes=array();
		while($lig2=mysql_fetch_object($res2)) {
			if(!in_array($lig2->salle,$tab_salles_existantes)) {
				if(!in_array($lig2->salle,$tab_salles)) {
					$chaine_salles_existantes.="<input type='checkbox' name='id_salle_existante[]' id='id_salle_existante_$lig2->id' value='$lig2->id' /><label for='id_salle_existante_$lig2->id'> $lig2->salle</label><br />\n";
				}
				$tab_salles_existantes[]=$lig2->salle;
			}
		}

		if($chaine_salles_existantes!='') {
			/*
			echo "<div style='width:15em; float:right; border:1px solid black;'>\n";
			echo "<p>Sélectionner des salles parmi les salles définies antérieurement pour d'autres épreuves.<br />\n";
			echo $chaine_salles_existantes;
			echo "</p>\n";
			echo "</div>\n";
			*/

			if($menu_a_droite=='') {
				$menu_a_droite.="<div style='float:right; width:18em;'>\n";
			}

			$menu_a_droite.="<div style='border:1px solid black;margin-top:5px;'>\n";
			$menu_a_droite.="<p>Sélectionner des salles parmi les salles définies antérieurement pour d'autres épreuves.<br />\n";
			$menu_a_droite.=$chaine_salles_existantes;
			$menu_a_droite.="</p>\n";
			$menu_a_droite.="</div>\n";
		}
	}

	$sql="select * from salle_cours ORDER BY nom_salle;";
	$res_salle_cours=mysql_query($sql);
	if(mysql_num_rows($res_salle_cours)>0) {
		$chaine_salles_cours_existantes="";
		$tab_salles_cours_existantes=array();
		while($lig2=mysql_fetch_object($res_salle_cours)) {
			if(!in_array($lig2->nom_salle,$tab_salles_cours_existantes)) {
				if(!in_array($lig2->nom_salle,$tab_salles)) {
					$chaine_salles_cours_existantes.="<input type='checkbox' name='id_salle_cours_existante[]' id='id_salle_cours_existante_$lig2->id_salle' value='$lig2->id_salle' /><label for='id_salle_cours_existante_$lig2->id_salle'> $lig2->nom_salle</label><br />\n";
				}
				$tab_salles_cours_existantes[]=$lig2->nom_salle;
			}
		}

		if($chaine_salles_cours_existantes!='') {
			/*
			echo "<div style='width:15em; float:right; border:1px solid black;'>\n";
			echo "<p>Sélectionner des salles parmi les salles définies antérieurement pour d'autres épreuves.<br />\n";
			echo $chaine_salles_existantes;
			echo "</p>\n";
			echo "</div>\n";
			*/

			if($menu_a_droite=='') {
				$menu_a_droite.="<div style='float:right; width:18em;'>\n";
			}

			$menu_a_droite.="<div style='border:1px solid black;margin-top:5px;'>\n";
			$menu_a_droite.="<p>Sélectionner des salles parmi les salles de cours définies dans un autre module.<br />\n";
			$menu_a_droite.=$chaine_salles_cours_existantes;
			$menu_a_droite.="</p>\n";
			$menu_a_droite.="</div>\n";
		}
	}

	if($menu_a_droite!='') {
		$menu_a_droite.="</div>\n";
	}

	echo $menu_a_droite;

/*
		// Parcours des salles déjà définies pour cette épreuve:
		$tab_salles=array();
		$tab_id_salles=array();
		while($lig=mysql_fetch_object($res)) {
			$tab_salles[]=$lig->salle;
			$tab_id_salles[]=$lig->id;
		}
		$nb_salles_epreuve_courante=count($tab_salles);



		// Choisir des salles parmi les salles auparavant définies... et qui ne sont pas déjà choisies
		$sql="SELECT * FROM eb_salles ORDER BY salle;";
		echo "$sql<br />";
		$res2=mysql_query($sql);
		if(mysql_num_rows($res2)>0) {
			$chaine_salles_existantes="";
			$tab_salles_existantes=array();
			while($lig2=mysql_fetch_object($res2)) {
				if(!in_array($lig2->salle,$tab_salles_existantes)) {
					if(!in_array($lig2->salle,$tab_salles)) {
						$chaine_salles_existantes.="<input type='checkbox' name='id_salle_existante[]' id='id_salle_existante_$lig2->id' value='$lig2->id' /><label for='id_salle_existante_$lig2->id'> $lig2->salle</label><br />\n";
					}
					$tab_salles_existantes[]=$lig2->salle;
				}
			}

			if($chaine_salles_existantes!='') {
				echo "<div style='width:15em; float:right;'>\n";
				echo "<p>Sélectionner des salles parmi les salles définies antérieurement pour d'autres épreuves.<br />\n";
				echo $chaine_salles_existantes;
				echo "</p>\n";
				echo "</div>\n";
			}
		}
*/

	if($nb_salles_epreuve_courante>0) {

		echo "<p><b>Liste des salles déjà définies&nbsp;:</b>\n";
		//echo "<br />\n";
		echo "</p>\n";
		echo "<blockquote>\n";
		$alt=1;
		echo "<table class='boireaus' summary='Salles associées'>\n";
		echo "<tr>\n";
		echo "<th>Salles</th>\n";
		echo "<th>Effectif</th>\n";
		if($etat!='clos') {
			echo "<th>Supprimer</th>\n";
		}
		echo "</tr>\n";
		$cpt=0;
		$eff_aff=0;
		//while($lig=mysql_fetch_object($res)) {
		for($loop=0;$loop<$nb_salles_epreuve_courante;$loop++) {
			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";

			echo "<td>\n";
			if($salles!="") {$salles.=",";}
			//$salles.=$lig->salle;
			$salles.=$tab_salles[$loop];
			if($etat!='clos') {
				//echo "<input type='hidden' name='id_salle[]' value='$lig->id' />\n";
				//echo "<input type='text' name='salle[]' value='$lig->salle' onchange='changement();' /><br />\n";
				echo "<input type='hidden' name='id_salle[]' value='$tab_id_salles[$loop]' />\n";
				echo "<input type='text' name='salle[]' value='$tab_salles[$loop]' onchange='changement();' /><br />\n";
			}
			else {
				echo $lig->salle;
			}
			//echo "<br />\n";
			echo "</td>\n";

			echo "<td>\n";
			//$sql="SELECT 1=1 FROM eb_copies WHERE id_salle='$lig->id' AND id_epreuve='$id_epreuve';";
			$sql="SELECT 1=1 FROM eb_copies WHERE id_salle='$tab_id_salles[$loop]' AND id_epreuve='$id_epreuve';";
			$res_eff=mysql_query($sql);
			$eff[$cpt]=mysql_num_rows($res_eff);
			echo $eff[$cpt];
			$eff_aff+=$eff[$cpt];
			echo "</td>\n";

			if($etat!='clos') {
				echo "<td>\n";
				//echo "<input type='checkbox' name='suppr_salle[]' value='$lig->id' onchange='changement();' />\n";
				echo "<input type='checkbox' name='suppr_salle[]' value='$tab_id_salles[$loop]' onchange='changement();' />\n";
				echo "</td>\n";
			}

			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";

		if($eff_aff==0) {
			echo "<p>Aucun élève n'est encore affecté dans une salle.</p>\n";
		}
		else {
			$sql="SELECT login_ele FROM eb_copies WHERE id_salle='-1' AND id_epreuve='$id_epreuve';";
			$res_eff=mysql_query($sql);
			$eff[$cpt]=mysql_num_rows($res_eff);
			if($eff[$cpt]==0) {
				echo "<p>Tous les élèves sont affectés dans la/les salles.</p>\n";
			}
			elseif($eff[$cpt]==1) {
				$lig=mysql_fetch_object($res_eff);
				echo "<p>Un élève (".get_nom_prenom_eleve($lig->login_ele).") n'est pas affecté dans une salle.</p>\n";
			}
			else {
				echo "<p>".$eff[$cpt]." élèves ne sont pas affectés dans une salle.</p>\n";
			}
		}
		echo "</blockquote>\n";
	}
	else {
		echo "<p>Aucune salle n'est encore définie.</p>\n";
	}

	if($etat!='clos') {
		echo "<p>Ajouter une ou des salles&nbsp;:\n";
		//echo " <input type='text' name='salles' value='$salles' /><br />\n";
		echo " <input type='text' name='salles' value='' onchange='changement();' /><br />\n";
		echo "(<i>pour saisir plusieurs salles, mettre une virgule entre les salles<br />Exemple&nbsp;: '<b>Salle 1, Salle 2, Salle 3</b>'</i>)</p>\n";
		
		echo " <input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<p><input type='submit' name='definition_salles' value='Valider' /></p>\n";
		echo "</form>\n";
	}

	//echo "<p><a href='".$_SERVER['PHP_SELF']."?salles=$salles'>Affecter les élèves dans les salles</a>.</p>";
	/*
	echo "<p><a href='".$_SERVER['PHP_SELF']."?mode=affect_eleves&amp;id_epreuve=$id_epreuve'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Affecter les élèves dans les salles</a>.</p>";
	*/

	echo "<p style='color:red;'>Afficher le nombre d'élèves non affectés dans une salle.</p>\n";
	echo "<p style='color:red;'>Si on saisit une/des heures pour l'épreuve, on pourrait détecter qu'une même salle a été affectée pour plusieurs épreuves sur même créneau.</p>\n";
	echo "<p style='color:red;'>Pouvoir sélectionner des salles parmi celles déjà définies.</p>\n";
}
//==================================================================
else {
	// Affectation des élèves dans les salles
	echo " | <a href='".$_SERVER['PHP_SELF']."?id_epreuve=$id_epreuve'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">Définition des salles</a>";
	echo "</p>\n";

	echo "<p class='bold'>Epreuve n°$id_epreuve</p>\n";
	$sql="SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>L'épreuve choisie (<i>$id_epreuve</i>) n'existe pas.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	
	$lig=mysql_fetch_object($res);
	echo "<blockquote>\n";
	echo "<p><b>".$lig->intitule."</b> (<i>".formate_date($lig->date)."</i>)<br />\n";
	if($lig->description!='') {
		echo nl2br(trim($lig->description))."<br />\n";
	}
	else {
		echo "Pas de description saisie.<br />\n";
	}
	echo "</blockquote>\n";

	$sql="SELECT DISTINCT id,salle FROM eb_salles WHERE id_epreuve='$id_epreuve' ORDER BY salle;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<p>Aucune salle n'est encore définie.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$salles="";
	$salle=array();
	$id_salle=array();
	while($lig=mysql_fetch_object($res)) {
		if($salles!="") {$salles.=",";}
		$salles.=$lig->salle;
		$salle[]=$lig->salle;
		$id_salle[]=$lig->id;
	}

	//$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "groupe");
	$tri=isset($_POST['tri']) ? $_POST['tri'] : (isset($_GET['tri']) ? $_GET['tri'] : "alpha");

	echo "<p class='bold'>Trier les élèves par&nbsp;:</p>\n";
	echo "<ul>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=affect_eleves&amp;id_epreuve=$id_epreuve&amp;tri=alpha'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">ordre alphabétique</a></li>\n";
	echo "<li><a href='".$_SERVER['PHP_SELF']."?mode=affect_eleves&amp;id_epreuve=$id_epreuve&amp;tri=groupe'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo ">groupe/enseignement</a></li>\n";
	echo "</ul>\n";

	if($tri=='groupe') {

		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
		echo add_token_field();

		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves1' value='Valider' /></p>\n";
	
		$sql="SELECT DISTINCT g.* FROM eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name, g.description;";
		$res=mysql_query($sql);
		if(mysql_num_rows($res)==0) {
			echo "<p>Aucun groupe  n'est encore associé à l'épreuve.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	
		$compteur_groupe=-1;
		$tab_cpt_eleve=array();
		$tab_groupes=array();
		$cpt=0;
		while($lig=mysql_fetch_object($res)) {
			$tab_groupes[]=$lig->id;
	
			$compteur_groupe++;
	
			$tab_cpt_eleve[]=$cpt;
	
			$current_group=get_group($lig->id);
			echo "<p>"."<b>".$current_group['classlist_string']."</b> ".htmlentities($lig->name)." (<i>".htmlentities($lig->description)."</i>)"."</p>\n";
			echo "<blockquote>\n";
	
			//$sql="SELECT * FROM eb_copies ec, eb_groupes eg WHERE id_epreuve='$id_epreuve' AND...;";
	
			$sql="SELECT ec.login_ele,ec.id_salle FROM eb_copies ec, eb_groupes eg WHERE eg.id_epreuve='$id_epreuve' AND ec.id_epreuve=eg.id_epreuve AND eg.id_groupe='$lig->id';";
			//echo "$sql<br />";
			$res2=mysql_query($sql);
			if(mysql_num_rows($res2)==0) {
				echo "<p>Aucune élève n'est encore associé à l'épreuve.</p>\n";
				require("../lib/footer.inc.php");
				die();
			}
	
			$tab_ele_id_salle=array();
			while($lig2=mysql_fetch_object($res2)) {
				$tab_ele_id_salle[$lig2->login_ele]=$lig2->id_salle;
			}
	
			echo "<table class='boireaus' summary='Choix des élèves du groupe $lig->id'>\n";
			echo "<tr>\n";
			echo "<th>Elèves</th>\n";
			echo "<th>Classes</th>\n";
			for($i=0;$i<count($salle);$i++) {
				echo "<th>\n";
				echo "<a href='javascript:coche($i,$compteur_groupe,true)'>\n";
				echo "$salle[$i]\n";
				echo "</a>\n";
				//echo "<input type='hidden' name='salle[$i]' value='$salle[$i]' />\n";
				// A FAIRE: Afficher effectif
				// style='color:red;'
				//echo "<br />(<span id='eff_salle_".$lig->id."_$i'>Effectif</span>)";
				echo "</th>\n";
			}
			echo "<th>\n";
			echo "<a href='javascript:coche($i,$compteur_groupe,true)'>\n";
			echo "Non affecté";
			echo "</a>\n";
			echo "</th>\n";
			echo "</tr>\n";
	
			echo "<tr>\n";
			echo "<th>Effectifs</th>\n";
			echo "<th>&nbsp;</th>\n";
			for($i=0;$i<count($salle);$i++) {
				echo "<th>\n";
				echo "<span id='eff_salle_".$lig->id."_$i'>Effectif</span>";
				echo "</th>\n";
			}
			echo "<th>\n";
			//$i++;
			echo "<span id='eff_salle_".$lig->id."_$i'>Effectif</span>";
			echo "</th>\n";
			echo "</tr>\n";
	
			$alt=1;
			for($j=0;$j<count($current_group["eleves"]["all"]["list"]);$j++) {
				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
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
				for($i=0;$i<count($id_salle);$i++) {
					echo "<td>\n";
					echo "<input type='radio' name='id_salle_ele[$cpt]' id='id_salle_ele_".$i."_$cpt' value='$id_salle[$i]' ";
					echo "onchange='calcule_effectif();changement()' ";
					// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
					if($tab_ele_id_salle[$login_ele]==$id_salle[$i]) {echo "checked ";$affect="y";}
					echo "/>\n";
					echo "</td>\n";
				}
				echo "<td>\n";
				echo "<input type='radio' name='id_salle_ele[$cpt]' id='id_salle_ele_".$i."_$cpt' value='-1' ";
				echo "onchange='calcule_effectif();changement()' ";
				if($affect=="n") {echo "checked ";}
				echo "/>\n";
				echo "</td>\n";
				echo "</tr>\n";
				$cpt++;
			}
			echo "</table>\n";
	
			echo "</blockquote>\n";
			echo "<p><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		}
	
	
		echo "<input type='hidden' name='tri' value='$tri' />\n";
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		//echo "<p><input type='submit' name='bouton_valide_affect_eleves2' value='Valider' /></p>\n";
		echo "</form>\n";
	
	
		$chaine_groupes="";
		for($i=0;$i<count($tab_groupes);$i++) {
			if($i>0) {$chaine_groupes.=",";}
			$chaine_groupes.="'$tab_groupes[$i]'";
		}
	
	
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
		if($chaine_cpt1_eleves!='') {
			$chaine_cpt1_eleves.=",'$cpt'";
		}
		else {
			// Une seule salle a été définie.
			$chaine_cpt1_eleves.="'$cpt'";
		}	
	
		echo "<script type='text/javascript'>

function calcule_effectif() {
	var tab_groupes=new Array($chaine_groupes);
	var eff;

	for(i=0;i<".count($id_salle)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_salle_ele_'+i+'_'+j)) {
				if(document.getElementById('id_salle_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		//alert('Salle i='+i+' eff='+eff)
		for(j=0;j<tab_groupes.length;j++) {
			if(document.getElementById('eff_salle_'+tab_groupes[j]+'_'+i)) {
				document.getElementById('eff_salle_'+tab_groupes[j]+'_'+i).innerHTML=eff;
				//alert('eff_salle_'+tab_groupes[j]+'_'+i+' eff='+eff);
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
		if(document.getElementById('id_salle_ele_'+colonne+'_'+k)) {
			document.getElementById('id_salle_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}
</script>\n";
	}
	elseif($tri=='alpha') {

		echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n";
		echo add_token_field();

		echo "<p align='center'><input type='submit' name='bouton_valide_affect_eleves1' value='Valider' /></p>\n";
	
		$sql="SELECT DISTINCT g.* FROM eb_groupes eg, groupes g WHERE id_epreuve='$id_epreuve' AND eg.id_groupe=g.id ORDER BY g.name, g.description;";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			echo "<p>Aucun groupe n 'est encore associé à l'épreuve.</p>\n";
			require("../lib/footer.inc.php");
			die();
		}

		$sql="SELECT e.login,e.nom,e.prenom,ec.id_salle FROM eb_copies ec, eleves e WHERE ec.id_epreuve='$id_epreuve' AND ec.login_ele=e.login ORDER BY e.nom,e.prenom;";
		//echo "$sql<br />";
		$res=mysql_query($sql);

		$eff_par_salle=ceil(mysql_num_rows($res)/count($salle));

		$compteur_tranche=-1;
		$tab_cpt_eleve=array();
		//$tab_tranches=array();
		$cpt=0;
		$alt=1;
		while($lig=mysql_fetch_object($res)) {
			if($cpt/$eff_par_salle==floor($cpt/$eff_par_salle)) {
				if($cpt>0) {
					echo "</table>\n";
					echo "</blockquote>\n";

					echo "<p><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";

					echo "<p>Une autre tranche&nbsp;:</p>\n";
				}
				else {
					echo "<p>Une tranche&nbsp;:</p>\n";
				}
				$compteur_tranche++;

				$tab_cpt_eleve[]=$cpt;
				//echo "\$tab_cpt_eleve[]=$cpt<br />";

				echo "<blockquote>\n";

				echo "<table class='boireaus' summary='Une tranche'>\n";
				echo "<tr>\n";
				echo "<th>Elèves</th>\n";
				echo "<th>Classes</th>\n";
				for($i=0;$i<count($salle);$i++) {
					echo "<th>\n";
					echo "<a href='javascript:coche($i,$compteur_tranche,true)'>\n";
					echo "$salle[$i]\n";
					echo "</a>\n";
					echo "</th>\n";
				}
				echo "<th>\n";
				echo "<a href='javascript:coche($i,$compteur_tranche,true)'>\n";
				echo "Non affecté";
				echo "</a>\n";
				echo "</th>\n";
				echo "</tr>\n";
		
				echo "<tr>\n";
				echo "<th>Effectifs</th>\n";
				echo "<th>&nbsp;</th>\n";
				for($i=0;$i<count($salle);$i++) {
					echo "<th>\n";
					echo "<span id='eff_salle_".$compteur_tranche."_$i'>Effectif</span>";
					echo "</th>\n";
				}
				echo "<th>\n";
				//$i++;
				echo "<span id='eff_salle_".$compteur_tranche."_$i'>Effectif</span>";
				echo "</th>\n";
				echo "</tr>\n";
			}


			$alt=$alt*(-1);
			echo "<tr class='lig$alt'>\n";
			echo "<td style='text-align:left;'>\n";
			echo "<input type='hidden' name='login_ele[$cpt]' value='$lig->login' />\n";
			echo casse_mot($lig->nom)." ".casse_mot($lig->prenom,'majf2');
			echo "</td>\n";

			echo "<td>\n";
			$tmp_tab_classe=get_class_from_ele_login($lig->login);
			echo $tmp_tab_classe['liste'];
			echo "</td>\n";

			$affect="n";
			for($i=0;$i<count($id_salle);$i++) {
				echo "<td>\n";
				echo "<input type='radio' name='id_salle_ele[$cpt]' id='id_salle_ele_".$i."_$cpt' value='$id_salle[$i]' ";
				echo "onchange='calcule_effectif();changement()' ";
				// On risque une blague si pour une raison ou une autre, on n'a pas une copie dans eb_copies pour tous les élèves du groupe (toutes périodes confondues)... à améliorer
				if($lig->id_salle==$id_salle[$i]) {echo "checked ";$affect="y";}
				echo "/>\n";
				echo "</td>\n";
			}
			echo "<td>\n";
			echo "<input type='radio' name='id_salle_ele[$cpt]' id='id_salle_ele_".$i."_$cpt' value='-1' ";
			echo "onchange='calcule_effectif();changement()' ";
			if($affect=="n") {echo "checked ";}
			echo "/>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$cpt++;
		}
		echo "</table>\n";

		echo "</blockquote>\n";


		echo "<input type='hidden' name='tri' value='$tri' />\n";
		echo "<input type='hidden' name='id_epreuve' value='$id_epreuve' />\n";
		echo "<input type='hidden' name='mode' value='affect_eleves' />\n";
		echo "<input type='hidden' name='valide_affect_eleves' value='y' />\n";
		echo "<p><input type='submit' name='bouton_valide_affect_eleves$cpt' value='Valider' /></p>\n";
		echo "</form>\n";


		/*
		$chaine_groupes="";
		for($i=0;$i<count($tab_groupes);$i++) {
			if($i>0) {$chaine_groupes.=",";}
			$chaine_groupes.="'$tab_groupes[$i]'";
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
		//$chaine_cpt1_eleves.=",'$cpt'";
		if($chaine_cpt1_eleves!='') {
			$chaine_cpt1_eleves.=",'$cpt'";
		}
		else {
			// Une seule salle a été définie.
			$chaine_cpt1_eleves.="'$cpt'";
		}	

		//echo "\$chaine_cpt0_eleves=$chaine_cpt0_eleves<br />";
		//echo "\$chaine_cpt1_eleves=$chaine_cpt1_eleves<br />";
	
		echo "<script type='text/javascript'>

function calcule_effectif() {
	var eff;

	for(i=0;i<".count($id_salle)."+1;i++) {
		eff=0;

		for(j=0;j<$cpt;j++) {
			if(document.getElementById('id_salle_ele_'+i+'_'+j)) {
				if(document.getElementById('id_salle_ele_'+i+'_'+j).checked) {
					eff++;
				}
			}
		}

		for(j=0;j<=$compteur_tranche;j++) {
			if(document.getElementById('eff_salle_'+j+'_'+i)) {
				document.getElementById('eff_salle_'+j+'_'+i).innerHTML=eff;
			}
		}
	}
}

calcule_effectif();

function coche(colonne,rang_groupe,mode) {
	var tab_cpt0_ele=new Array($chaine_cpt0_eleves);
	var tab_cpt1_ele=new Array($chaine_cpt1_eleves);

	for(k=tab_cpt0_ele[rang_groupe];k<tab_cpt1_ele[rang_groupe];k++) {
		if(document.getElementById('id_salle_ele_'+colonne+'_'+k)) {
			document.getElementById('id_salle_ele_'+colonne+'_'+k).checked=mode;
		}
	}

	calcule_effectif();

	changement();
}
</script>\n";
	}
	else {
		echo "<p>Le mode de tri choisi ne convient pas.</p>\n";
	}
	//echo "<p style='color:red;'>Ajouter des confirm_abandon() sur les liens.</p>\n";
}

require("../lib/footer.inc.php");
?>

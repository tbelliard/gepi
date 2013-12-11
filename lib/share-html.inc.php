<?php
/**
 * Fonctions créant du html
 * 
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Initialisation
 * @subpackage html
 *
*/

/**
 * 
 * @global int $GLOBALS['num_debut_colonnes_matieres']
 * @name $num_debut_colonnes_matieres
 */
$GLOBALS['num_debut_colonnes_matieres'] = 0;

/**
 * 
 * @global int $GLOBALS['num_debut_lignes_eleves']
 * @name $num_debut_lignes_eleves
 */
$GLOBALS['num_debut_lignes_eleves'] = 0;

/**
 * 
 * @global text $GLOBALS['vtn_coloriser_resultats']
 * @name $vtn_coloriser_resultats
 */
$GLOBALS['vtn_coloriser_resultats'] = '';

/**
 * 
 * @global array $GLOBALS['vtn_borne_couleur']
 * @name $vtn_borne_couleur
 */
$GLOBALS['vtn_borne_couleur'] = array();

/**
 * 
 * @global array $GLOBALS['vtn_couleur_texte']
 * @name $vtn_couleur_texte
 */
$GLOBALS['vtn_couleur_texte'] = array();

/**
 * 
 * @global array $GLOBALS['vtn_couleur_cellule']
 * @name $vtn_couleur_cellule
 */
$GLOBALS['vtn_couleur_cellule'] = array();

/**
 * 
 * @global text $GLOBALS['selected_eleve']
 * @name $selected_eleve
 */
$GLOBALS['selected_eleve'] = '';

/**
 * Construit du html pour les cahiers de textes
 *
 * @deprecated La requête SQL n'est plus valide
 * @param string $link Le lien
 * @param <type> $current_classe
 * @param <type> $current_matiere
 * @param integer $year année
 * @param integer $month le mois
 * @param integer $day le jour
 * @return string echo résultat
 */
function make_area_list_html($link, $current_classe, $current_matiere, $year, $month, $day) {
	global $mysqli;
  echo "<strong><em>Cahier&nbsp;de&nbsp;texte&nbsp;de&nbsp;:</em></strong><br />";
  $sql_donnees = "SELECT * FROM classes ORDER BY classe";
  $appel_donnees = mysqli_query($mysqli, $sql_donnees);
  $lignes = $appel_donnees->num_rows;
  $i = 0;
 // while($i < $lignes){
  while($classe = $appel_donnees->fetch_object()){
    $id_classe = $classe->id;
	$sql_mat = "SELECT DISTINCT m.* FROM matieres m, j_classes_matieres_professeurs j 
		WHERE (j.id_classe='$id_classe' AND j.id_matiere=m.matiere) 
			ORDER BY m.nom_complet";
    $appel_mat = mysqli_query($mysqli, $sql_mat);
    $nb_mat = $appel_mat->num_rows;
    $j = 0;
    // while($j < $nb_mat){
    while($matiere = $appel_mat->fetch_object()){
      $flag2 = "no";
      $matiere_nom = $matiere->nom_complet;
      $matiere_nom_court = $matiere->matiere;
      $id_matiere = $matiere->matiere;
	  $sql_profs = "SELECT * FROM j_classes_matieres_professeurs 
		  WHERE ( id_classe='$id_classe' and id_matiere = '$id_matiere') 
			  ORDER BY ordre_prof";
      $call_profs = mysqli_query($mysqli, $sql_profs);
      $nombre_profs = $call_profs->num_rows;
      $k = 0;
      // while ($k < $nombre_profs) {
      while ($prof = $call_profs->fetch_object()){
        $temp = my_strtoupper($prof->id_professeur);
        if ($temp == $_SESSION['login']) {$flag2 = "yes";}
        $k++;
      }
      if ($flag2 == "yes") {
        echo "<strong>";
        $display_class = $classe->classe;
        if (($id_classe == $current_classe) and ($id_matiere == $current_matiere)) {
           echo ">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)&nbsp;";
        } else {
       echo "<a href=\"".$link."?id_classe=$id_classe&amp;id_matiere=$id_matiere&amp;year=$year&amp;month=$month&amp;day=$day\">$display_class&nbsp;-&nbsp;$matiere_nom&nbsp;($matiere_nom_court)</a>";

        }
        echo "</strong><br />";
      }
      $j++;
    }
    $i++;
  }
}



/**
 * Affichage de la liste des conteneurs
 *
 * @global array 
 * @global text 
 * @global int 
 * @global int 
 * @param int $id_conteneur Id du conteneur
 * @param int $periode_num Numéro de la période
 * @param text $empty existance de notes, no si le conteneur contient des notes (passage par référence) 
 * @param int $ver_periode Etat du vérouillage de la période
 * @return text no si le conteneur contient des notes, yes sinon
 * @see getSettingValue()
 * @see get_nom_prenom_eleve()
 * @see creer_div_infobulle()
 * @see add_token_in_url()
 * @see traitement_magic_quotes()
 */
function affiche_devoirs_conteneurs($id_conteneur,$periode_num, &$empty, $ver_periode) {
	global $tabdiv_infobulle, $gepiClosedPeriodLabel, $id_groupe, $eff_groupe, $acces_exceptionnel_saisie;
    
	if((isset($id_groupe))&&(!isset($eff_groupe))) {
		$sql="SELECT 1=1 FROM j_eleves_groupes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
		//echo "$sql<br />";
		$res_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
		$eff_groupe=mysqli_num_rows($res_ele_grp);
	}
	//
	// Cas particulier de la racine
	$gepi_denom_boite=getSettingValue("gepi_denom_boite");
	if(getSettingValue("gepi_denom_boite_genre")=='m'){
		$message_cont = "Etes-vous sûr de vouloir supprimer le ".getSettingValue("gepi_denom_boite")." ci-dessous ?";
		$message_cont_non_vide = "Le ".getSettingValue("gepi_denom_boite")." est non vide. Il ne peut pas être supprimé.";
	}
	else{
		$message_cont = "Etes-vous sûr de vouloir supprimer la ".getSettingValue("gepi_denom_boite")." ci-dessous ?";
		$message_cont_non_vide = "La ".getSettingValue("gepi_denom_boite")." est non vide. Elle ne peut pas être supprimée.";
	}
	$message_dev = "Etes-vous sûr de vouloir supprimer l\\'évaluation ci-dessous et les notes qu\\'elle contient ?";
	$sql="SELECT * FROM cn_conteneurs WHERE (parent='0' and id_racine='$id_conteneur')";
	$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cont = mysqli_num_rows($appel_conteneurs);
	if ($nb_cont != 0) {
		echo "<ul>\n";
		$id_cont = old_mysql_result($appel_conteneurs, 0, 'id');
		$id_parent = old_mysql_result($appel_conteneurs, 0, 'parent');
		$id_racine = old_mysql_result($appel_conteneurs, 0, 'id_racine');
		$nom_conteneur = old_mysql_result($appel_conteneurs, 0, 'nom_court');
		$modeBoiteMoy = old_mysql_result($appel_conteneurs, 0, 'mode');
		echo "<li>\n";
		echo htmlspecialchars($nom_conteneur);
		if ($ver_periode <= 1) {
			echo " (<strong>".$gepiClosedPeriodLabel."</strong>) ";
		}
		echo "- <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a> - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";

		$ponderation_cont=old_mysql_result($appel_conteneurs, 0, 'ponderation');
		if($ponderation_cont!='0.0') {
			$message_ponderation="La meilleure note de la ".getSettingValue("gepi_denom_boite")." est pondérée d'un coefficient +$ponderation_cont";
			echo " - <img src='../images/icons/flag.png' width='17' height='18' alt=\"$message_ponderation\" title=\"$message_ponderation\" />";
		}

		$sql="SELECT mode FROM cn_conteneurs WHERE id_racine='$id_conteneur';";
		$res_nb_conteneurs=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_nb_conteneurs)>1) {
			echo " - <a href='add_modif_conteneur.php?id_conteneur=$id_conteneur&mode_navig=retour_index' title=\"";
			if($modeBoiteMoy==1) {
				echo "la moyenne s'effectue sur toutes les notes contenues à la racine et dans les ".my_strtolower(getSettingValue("gepi_denom_boite"))."s sans tenir compte des options définies dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.";
			}
			else {
				echo "la moyenne s'effectue sur toutes les notes contenues à la racine et sur les moyennes des ".my_strtolower(getSettingValue("gepi_denom_boite"))."s en tenant compte des options dans ces ".my_strtolower(getSettingValue("gepi_denom_boite"))."s.";
			}
			echo "\">Mode Moy.: $modeBoiteMoy</a>";
		}

		$appel_dev = mysqli_query($GLOBALS["mysqli"], "select * from cn_devoirs where id_conteneur='$id_cont' order by date");
		$nb_dev  = mysqli_num_rows($appel_dev);
		if ($nb_dev != 0) {$empty = 'no';}
		if (($ver_periode >= 2)||($acces_exceptionnel_saisie)) {
			$j = 0;
			if($nb_dev>0){
				echo "<ul>\n";
				while ($j < $nb_dev) {
					if (getSettingValue("utiliser_sacoche") == 'yes') {
						echo '<form id="sacoche_form_'.$j.'" method="POST" action="'.getSettingValue("sacocheUrl").'/index.php?sso&page=evaluation_gestion&section=groupe">';
						echo '<input type="hidden" name="id" value="'.getSettingValue("sacoche_base").'"/>';
						echo '<input type="hidden" name="page" value="evaluation_gestion"/>';
						echo '<input type="hidden" name="section" value="groupe"/>';
						echo '<input type="hidden" name="source" value="distant-gepi-saml"/>';//source simplesaml pour préselectionner la source dans le module multiauth et éviter de choisir le webmestre
						//encodage du devoir
						mysqli_data_seek($appel_dev,  $j);
						$devoir_array = mysqli_fetch_array($appel_dev);
						$devoir_array = array_map_deep('ensure_utf8', $devoir_array);
						echo '<input type="hidden" name="period_num" value=\''.$periode_num.'\'/>';
						echo '<input type="hidden" name="gepi_cn_devoirs_array" value="'.htmlspecialchars(json_encode($devoir_array),ENT_COMPAT,'UTF-8').'"/>';
						$group_array = get_group($id_groupe);
						//on va purger un peut notre array
						unset($group_array['classes']);
						unset($group_array['matieres']);
						unset($group_array['eleves']['all']);
						for ($i=0;$i<5;$i++) {
							if ($i != $periode_num) {
								unset($group_array['eleves'][''.$i]);
							}
						}
						$current_group = array_map_deep('ensure_utf8', $group_array);
						echo '<input type="hidden" name="gepi_current_group" value="'.htmlspecialchars(json_encode($current_group),ENT_COMPAT,'UTF-8').'"/>';
						echo '</form>';
					}
					
					$nom_dev = old_mysql_result($appel_dev, $j, 'nom_court');
					$id_dev = old_mysql_result($appel_dev, $j, 'id');
					$date_dev = old_mysql_result($appel_dev, $j, 'date');
					$date_ele_resp_dev = old_mysql_result($appel_dev, $j, 'date_ele_resp');
					echo "<li>\n";
					echo "<span style='color:green;'>$nom_dev</span>";
					echo " - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";

					$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
					$res_eff_dev=mysqli_query($GLOBALS["mysqli"], $sql);
					$eff_dev=mysqli_num_rows($res_eff_dev);
					echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
					if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
					echo "'>($eff_dev";
					if(isset($eff_groupe)) {echo "/$eff_groupe";}
					echo ")</span>";

					// Pour détecter une anomalie:
					 $sql="SELECT * FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num' AND jec.login not in (select login from j_eleves_groupes where id_groupe='$id_groupe' and periode='$periode_num');";
					$test_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_anomalie)>0) {
						$titre_infobulle="Note pour un fantôme";
						$texte_infobulle="Une ou des notes existent pour un ou des élèves qui ne sont plus inscrits dans cet enseignement&nbsp;:<br />";
						$cpt_ele_anomalie=0;
						while($lig_anomalie=mysqli_fetch_object($test_anomalie)) {
							if($cpt_ele_anomalie>0) {$texte_infobulle.=", ";}
							$texte_infobulle.=get_nom_prenom_eleve($lig_anomalie->login,'avec_classe')."&nbsp;(<i title=\"Note enregistrée\">";
							if($lig_anomalie->statut=='') {$texte_infobulle.=$lig_anomalie->note;}
							elseif($lig_anomalie->statut=='v') {$texte_infobulle.="_";}
							else {$texte_infobulle.=$lig_anomalie->statut;}
							$texte_infobulle.="</i>)";
							$cpt_ele_anomalie++;
						}
						$texte_infobulle.="<br />";
						$texte_infobulle.="Cliquer <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;clean_anomalie_dev=$id_dev".add_token_in_url()."' class='bold'>ici</a> pour supprimer les notes associées?";
						$tabdiv_infobulle[]=creer_div_infobulle('anomalie_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

						echo " <a href=\"#\" onclick=\"afficher_div('anomalie_$id_dev','y',100,100);return false;\" title=\"Une ou des notes existent pour un ou des élèves qui ne sont plus inscrits dans cet enseignement.
Cliquez pour contrôler la liste.\"><img src='../images/icons/flag.png' width='17' height='18' alt='' /></a>";
					}

					if (getSettingValue("utiliser_sacoche") == 'yes') {
						echo " - <a href='#' onclick=\"document.getElementById('sacoche_form_".$j."').submit();\">Évaluer par compétence</a>";
					}

					echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a>";

					// Autre anomalie
					 $sql="SELECT * FROM cn_devoirs WHERE id='$id_dev' AND facultatif!='O' AND facultatif!='N' AND facultatif!='B';";
					$test_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($test_anomalie)>0) {
						$titre_infobulle="Devoir facultatif ou pas ?";
						$texte_infobulle="Le devoir n'est pas 'catégorisé' comme facultatif ou non.<br />Son mode de prise en compte ou non dans la moyenne n'est pas défini.<br />";
						$texte_infobulle.="Cliquer <a href='add_modif_dev.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev&amp;mode_navig=retour_index&amp;interface_simplifiee=n".add_token_in_url()."#statut_evaluation' class='bold'>ici</a> pour choisir un mode.";
						$tabdiv_infobulle[]=creer_div_infobulle('anomalie2_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

						echo " <a href=\"#\" onclick=\"afficher_div('anomalie2_$id_dev','y',100,100);return false;\" title=\"Mode de prise en compte ou non dans la moyenne non défini.\"><img src='../images/icons/flag.png' width='17' height='18' alt='' /></a>";
					}

					$note_sur=old_mysql_result($appel_dev, $j, 'note_sur');
					if($note_sur!='20') {
						echo " (<em><span title='Note sur $note_sur'>/$note_sur</span></em>)";
					}

					$display_parents=old_mysql_result($appel_dev, $j, 'display_parents');
					$coef=old_mysql_result($appel_dev, $j, 'coef');
					echo " (<i><span title='Coefficient $coef'>$coef</span> ";
					if($display_parents==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.' alt='Evaluation visible sur le relevé de notes' />";}
					else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";}
					echo "</i>)";

					$sql="SELECT * FROM cc_dev WHERE id_cn_dev='$id_dev';";
					$res_cc_dev=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_cc_dev)>0) {
						$lig_cc_dev=mysqli_fetch_object($res_cc_dev);
						echo " - <a href='index_cc.php?id_racine=".$id_racine."' title=\"Voir l'évaluation cumul associée $lig_cc_dev->nom_court ($lig_cc_dev->nom_complet)\">".$lig_cc_dev->nom_court."</a>";
					}

					echo " - <a href='copie_dev.php?id_devoir=".$id_dev."' title=\"Copier le devoir et les notes vers une autre période ou un autre enseignement (Les notes ne sont copiées que si les élèves sont les mêmes).\"><img src='../images/icons/copy-16.png' width='16' height='16' alt='' /></a>\n";

					echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
					echo "</li>\n";
					$j++;
				}
				echo "</ul>\n";
			}
		}
		echo "</li>\n";
		echo "</ul>\n";
	}
	if (($ver_periode >= 2)||($acces_exceptionnel_saisie)) {
		$appel_conteneurs = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE (parent='$id_conteneur') order by nom_court");
		$nb_cont = mysqli_num_rows($appel_conteneurs);
		if($nb_cont>0) {
			echo "<ul>\n";
			$i = 0;
			while ($i < $nb_cont) {
				$id_cont = old_mysql_result($appel_conteneurs, $i, 'id');
				$id_parent = old_mysql_result($appel_conteneurs, $i, 'parent');
				$id_racine = old_mysql_result($appel_conteneurs, $i, 'id_racine');
				$nom_conteneur = old_mysql_result($appel_conteneurs, $i, 'nom_court');
				if ($id_cont != $id_parent) {
					echo "<li>\n";
					echo "$nom_conteneur - <a href='saisie_notes.php?id_conteneur=$id_cont'>Visualisation</a>";
					echo " - <a href = 'add_modif_conteneur.php?id_conteneur=$id_cont&amp;mode_navig=retour_index'>Configuration</a>\n";

					$display_bulletin=old_mysql_result($appel_conteneurs, $i, 'display_bulletin');
					$coef=old_mysql_result($appel_conteneurs, $i, 'coef');
					echo " (<i><span title='Coefficient $coef'>$coef</span> ";
					if($display_bulletin==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='$gepi_denom_boite visible sur le bulletin' alt='$gepi_denom_boite visible sur le bulletin' />";}
					else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title=\"".ucfirst($gepi_denom_boite)." non visible sur le bulletin.
Cela ne signifie pas que les notes ne sont pas prises en compte dans le calcul de la moyenne.
En revanche, on n'affiche pas une case spécifique pour ce".((getSettingValue('gepi_denom_boite_genre')=='f') ? "tte" : "")." ".$gepi_denom_boite." dans le bulletin.\" alt='".ucfirst($gepi_denom_boite)." non visible sur le bulletin.' />\n";}
					echo "</i>)";

					$ponderation_cont=old_mysql_result($appel_conteneurs, $i, 'ponderation');
					if($ponderation_cont!='0.0') {
						$message_ponderation="La meilleure note de la ".getSettingValue("gepi_denom_boite")." est pondérée d'un coefficient +$ponderation_cont";
						echo " - <img src='../images/icons/flag.png' width='17' height='18' alt=\"$message_ponderation\" title=\"$message_ponderation\" />";
					}

					$appel_dev = mysqli_query($GLOBALS["mysqli"], "select * from cn_devoirs where id_conteneur='$id_cont' order by date");
					$nb_dev  = mysqli_num_rows($appel_dev);
					if ($nb_dev != 0) {$empty = 'no';}

					// Existe-t-il des sous-conteneurs?
					$sql="SELECT 1=1 FROM cn_conteneurs WHERE (parent='$id_cont')";
					$test_sous_cont=mysqli_query($GLOBALS["mysqli"], $sql);
					$nb_sous_cont=mysqli_num_rows($test_sous_cont);

					if(($nb_dev==0)&&($nb_sous_cont==0)) {
						echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_cont=$id_cont".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_conteneur)."', '".$message_cont."')\">Suppression</a>\n";
					}
					else {
						echo " - <a href = '#' onclick='alert(\"$message_cont_non_vide\")'><font color='gray'>Suppression</font></a>\n";
					}

					$j = 0;
					if($nb_dev>0) {
						echo "<ul>\n";
						while ($j < $nb_dev) {
							$nom_dev = old_mysql_result($appel_dev, $j, 'nom_court');
							$id_dev = old_mysql_result($appel_dev, $j, 'id');
							$date_dev = old_mysql_result($appel_dev, $j, 'date');
							$date_ele_resp_dev = old_mysql_result($appel_dev, $j, 'date_ele_resp');
							echo "<li>\n";
							echo "<font color='green'>$nom_dev</font> - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";

							//$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='-' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
							$sql="SELECT 1=1 FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num';";
							$res_eff_dev=mysqli_query($GLOBALS["mysqli"], $sql);
							$eff_dev=mysqli_num_rows($res_eff_dev);
							echo " <span title=\"Effectif des notes saisies/effectif total de l'enseignement\" style='font-size:small;";
							if(isset($eff_groupe)) {if($eff_dev==$eff_groupe) {echo "color:green;";} else {echo "color:red;";}}
							echo "'>($eff_dev";
							if(isset($eff_groupe)) {echo "/$eff_groupe";}
							echo ")</span>";

							// Pour détecter une anomalie:
							$sql="SELECT * FROM cn_notes_devoirs cnd, j_eleves_classes jec WHERE cnd.id_devoir='$id_dev' AND cnd.statut!='v' AND jec.login=cnd.login AND jec.periode='$periode_num' AND jec.login not in (select login from j_eleves_groupes where id_groupe='$id_groupe' and periode='$periode_num');";
							$test_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_anomalie)>0) {
								$titre_infobulle="Note pour un fantôme";
								$texte_infobulle="Une ou des notes existent pour un ou des élèves qui ne sont plus inscrits dans cet enseignement&nbsp;:<br />";
								$cpt_ele_anomalie=0;
								while($lig_anomalie=mysqli_fetch_object($test_anomalie)) {
									if($cpt_ele_anomalie>0) {$texte_infobulle.=", ";}
									$texte_infobulle.=get_nom_prenom_eleve($lig_anomalie->login,'avec_classe')."&nbsp;(<i title=\"Note enregistrée\">";
									if($lig_anomalie->statut=='') {$texte_infobulle.=$lig_anomalie->note;}
									elseif($lig_anomalie->statut=='v') {$texte_infobulle.="_";}
									else {$texte_infobulle.=$lig_anomalie->statut;}
									$texte_infobulle.="</i>)";
									$cpt_ele_anomalie++;
								}
								$texte_infobulle.="<br />";
								$texte_infobulle.="Cliquer <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;periode_num=$periode_num&amp;clean_anomalie_dev=$id_dev".add_token_in_url()."' class='bold'>ici</a> pour supprimer les notes associées?";
								$tabdiv_infobulle[]=creer_div_infobulle('anomalie_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		
								echo " <a href=\"#\" onclick=\"afficher_div('anomalie_$id_dev','y',100,100);return FALSE;\" title=\"Une ou des notes existent pour un ou des élèves qui ne sont plus inscrits dans cet enseignement.
Cliquez pour contrôler la liste.\"><img src='../images/icons/flag.png' width='17' height='18' alt='' /></a>";
							}

							echo " - <a href = 'add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index'>Configuration</a>";

							// Autre anomalie
							 $sql="SELECT * FROM cn_devoirs WHERE id='$id_dev' AND facultatif!='O' AND facultatif!='N' AND facultatif!='B';";
							$test_anomalie=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_anomalie)>0) {
								$titre_infobulle="Devoir facultatif ou pas ?";
								$texte_infobulle="Le devoir n'est pas 'catégorisé' comme facultatif ou non.<br />Son mode de prise en compte ou non dans la moyenne n'est pas défini.<br />";
								$texte_infobulle.="Cliquer <a href='add_modif_dev.php?id_conteneur=$id_conteneur&amp;id_devoir=$id_dev&amp;mode_navig=retour_index&amp;interface_simplifiee=n".add_token_in_url()."#statut_evaluation' class='bold'>ici</a> pour choisir un mode.";
								$tabdiv_infobulle[]=creer_div_infobulle('anomalie2_'.$id_dev,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

								echo " <a href=\"#\" onclick=\"afficher_div('anomalie2_$id_dev','y',100,100);return false;\" title=\"Mode de prise en compte ou non dans la moyenne non défini.\"><img src='../images/icons/flag.png' width='17' height='18' alt='' /></a>";
							}

							$note_sur=old_mysql_result($appel_dev, $j, 'note_sur');
							if($note_sur!='20') {
								echo " (<em><span title='Note sur $note_sur'>/$note_sur</span></em>)";
							}

							$display_parents=old_mysql_result($appel_dev, $j, 'display_parents');
							$coef=old_mysql_result($appel_dev, $j, 'coef');
							echo " (<i><span title='Coefficient $coef'>$coef</span> ";
							if($display_parents==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.' alt='Evaluation visible sur le relevé de notes' />";}
							else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";}
							echo "</i>)";

							$sql="SELECT * FROM cc_dev WHERE id_cn_dev='$id_dev';";
							$res_cc_dev=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_cc_dev)>0) {
								$lig_cc_dev=mysqli_fetch_object($res_cc_dev);
								echo " - <a href='index_cc.php?id_racine=".$id_racine."' title=\"Voir l'évaluation cumul associée $lig_cc_dev->nom_court ($lig_cc_dev->nom_complet)\">".$lig_cc_dev->nom_court."</a>";
							}

							echo " - <a href='copie_dev.php?id_devoir=".$id_dev."' title=\"Copier le devoir et les notes vers une autre période ou un autre enseignement (Les notes ne sont copiées que si les élèves sont les mêmes).\"><img src='../images/icons/copy-16.png' width='16' height='16' alt='' /></a>\n";

							echo " - <a href = 'index.php?id_racine=$id_racine&amp;del_dev=$id_dev".add_token_in_url()."' onclick=\"return confirmlink(this, 'suppression de ".traitement_magic_quotes($nom_dev)."', '".$message_dev."')\">Suppression</a>\n";
							echo "</li>\n";
							$j++;
						}
						echo "</ul>\n";
					}
				}
				if ($id_conteneur != $id_cont) {affiche_devoirs_conteneurs($id_cont,$periode_num, $empty,$ver_periode);}
				if ($id_cont != $id_parent) {
					echo "</li>\n";
				}
				$i++;
			}
			echo "</ul>\n";
		}
	}
	if ($empty != 'no') return 'yes';
}


/**
 * Affiche l'AID sur le bulletin
 *
 * @global type
 * @param type $affiche_graph
 * @param type $affiche_rang
 * @param type $affiche_coef
 * @param type $test_coef
 * @param type $affiche_nbdev
 * @param type $indice_aid
 * @param type $aid_id
 * @param type $current_eleve_login
 * @param type $periode_num
 * @param type $id_classe
 * @param type $style_bulletin 
 */
function affich_aid($affiche_graph, $affiche_rang, $affiche_coef, $test_coef,$affiche_nbdev,$indice_aid, $aid_id,$current_eleve_login,$periode_num,$id_classe,$style_bulletin) {
    //============================
    // AJOUT: boireaus
    global $min_max_moyclas;
    //============================

    $call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = $indice_aid");
    $AID_NOM_COMPLET = @old_mysql_result($call_data, 0, "nom_complet");
    $note_max = @old_mysql_result($call_data, 0, "note_max");
    $type_note = @old_mysql_result($call_data, 0, "type_note");
    $message = @old_mysql_result($call_data, 0, "message");
    $display_nom = @old_mysql_result($call_data, 0, "display_nom");
    $display_end = @old_mysql_result($call_data, 0, "display_end");


    $aid_nom_query = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid WHERE (id='$aid_id' and indice_aid='$indice_aid')");
    $aid_nom = @old_mysql_result($aid_nom_query, 0, "nom");
    //------
    // On regarde maintenant quelle sont les profs responsables de cette AID
    $aid_prof_resp_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE (id_aid='$aid_id'  and indice_aid='$indice_aid')");
    $nb_lig = mysqli_num_rows($aid_prof_resp_query);
    $n = '0';
    while ($n < $nb_lig) {
        $aid_prof_resp_login[$n] = old_mysql_result($aid_prof_resp_query, $n, "id_utilisateur");
        $n++;
    }
    //------
    // On appelle l'appréciation de l'élève, et sa note
    //------
    $current_eleve_aid_appreciation_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='$current_eleve_login' AND periode='$periode_num' and id_aid='$aid_id' and indice_aid='$indice_aid')");
    $current_eleve_aid_appreciation = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "appreciation");
    $periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
    $periode_max = mysqli_num_rows($periode_query);
    if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
        $place_eleve = "";
        $current_eleve_aid_note = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "note");
        $current_eleve_aid_statut = @old_mysql_result($current_eleve_aid_appreciation_query, 0, "statut");
        if (($current_eleve_aid_statut == '') and ($note_max != 20) ) {
            $current_eleve_aid_appreciation = "(note sur ".$note_max.") ".$current_eleve_aid_appreciation;
        }
        if ($current_eleve_aid_note == '') {
            $current_eleve_aid_note = '-';
        } else {
            if ($affiche_graph == 'y')  {
                if ($current_eleve_aid_note<5) { $place_eleve=6;}
                if (($current_eleve_aid_note>=5) and ($current_eleve_aid_note<8))  { $place_eleve=5;}
                if (($current_eleve_aid_note>=8) and ($current_eleve_aid_note<10)) { $place_eleve=4;}
                if (($current_eleve_aid_note>=10) and ($current_eleve_aid_note<12)) {$place_eleve=3;}
                if (($current_eleve_aid_note>=12) and ($current_eleve_aid_note<15)) { $place_eleve=2;}
                if ($current_eleve_aid_note>=15) { $place_eleve=1;}
            }
            $current_eleve_aid_note=number_format($current_eleve_aid_note,1, ',', ' ');
        }
        $aid_note_min_query = mysqli_query($GLOBALS["mysqli"], "SELECT MIN(note) note_min FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");

        $aid_note_min = @old_mysql_result($aid_note_min_query, 0, "note_min");
        if ($aid_note_min == '') {
            $aid_note_min = '-';
        } else {
            $aid_note_min=number_format($aid_note_min,1, ',', ' ');
        }
        $aid_note_max_query = mysqli_query($GLOBALS["mysqli"], "SELECT MAX(note) note_max FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_max = @old_mysql_result($aid_note_max_query, 0, "note_max");

        if ($aid_note_max == '') {
            $aid_note_max = '-';
        } else {
            $aid_note_max=number_format($aid_note_max,1, ',', ' ');
        }

        $aid_note_moyenne_query = mysqli_query($GLOBALS["mysqli"], "SELECT round(avg(note),1) moyenne FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid')");
        $aid_note_moyenne = @old_mysql_result($aid_note_moyenne_query, 0, "moyenne");
        if ($aid_note_moyenne == '') {
            $aid_note_moyenne = '-';
        } else {
            $aid_note_moyenne=number_format($aid_note_moyenne,1, ',', ' ');
        }

    }
    //------
    // On affiche l'appréciation aid :
    //------
    echo "<tr>\n<td style=\"height: ".getSettingValue("col_hauteur")."px; width: ".getSettingValue("col_matiere_largeur")."px;\"><span class='$style_bulletin'><strong>$AID_NOM_COMPLET</strong><br />";
    $chaine_prof="";
    $n = '0';
    while ($n < $nb_lig) {
        $chaine_prof.=affiche_utilisateur($aid_prof_resp_login[$n],$id_classe)."<br />";
        $n++;
    }
    if($n!=0){
	echo "<em>".$chaine_prof."</em>";
    }
    echo "</span></td>\n";
    if ($test_coef != 0 AND $affiche_coef == "y") echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";

    if ($affiche_nbdev=="y"){echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";}

    if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid))) {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_max</span></td>\n";

		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_moyenne</span></td>\n";
	}
	else{
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
		echo "<span class='$style_bulletin'>$aid_note_min<br />\n";
		echo "$aid_note_max<br />\n";
		echo "$aid_note_moyenne</span></td>\n";
	}
	//==========================

	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\">";
	echo "<span class='$style_bulletin'><strong>";
	if ($current_eleve_aid_statut == '') {
		echo $current_eleve_aid_note;
	} else if ($current_eleve_aid_statut == 'other') {
		echo "-";
	} else {
		echo $current_eleve_aid_statut;
	}
	echo "</strong></span></td>\n";
    } else {
	//==========================
	// MODIF: boireaus
	if($min_max_moyclas!=1){
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	else{
		// On ne met pas trois tirets.
		echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
	}
	//==========================
	echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='$style_bulletin'>-</span></td>\n";
    }
    if ($affiche_graph == 'y')  {
      if (($type_note == 'every') or (($type_note == 'last') and ($periode_num == $last_periode_aid)))  {
        $quartile1_classe = sql_query1("SELECT COUNT( a.note ) as quartile1 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=15)");
        $quartile2_classe = sql_query1("SELECT COUNT( a.note ) as quartile2 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=12 AND a.note<15)");
        $quartile3_classe = sql_query1("SELECT COUNT( a.note ) as quartile3 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=10 AND a.note<12)");
        $quartile4_classe = sql_query1("SELECT COUNT( a.note ) as quartile4 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=8 AND a.note<10)");
        $quartile5_classe = sql_query1("SELECT COUNT( a.note ) as quartile5 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note>=5 AND a.note<8)");
        $quartile6_classe = sql_query1("SELECT COUNT( a.note ) as quartile6 FROM aid_appreciations a, j_eleves_classes j WHERE (a.login = j.login and j.id_classe = '$id_classe' and a.statut='' and a.periode = '$periode_num' and j.periode='$periode_num' and a.indice_aid='$indice_aid' AND a.note<5)");
        echo "<td style=\"text-align: center; \"><img height=40 witdh=40 src='../visualisation/draw_artichow4.php?place_eleve=$place_eleve&temp1=$quartile1_classe&temp2=$quartile2_classe&temp3=$quartile3_classe&temp4=$quartile4_classe&temp5=$quartile5_classe&temp6=$quartile6_classe&nb_data=7' alt='' /></td>\n";
     } else
      echo "<td style=\"text-align: center; \"><span class='".$style_bulletin."'>-</span></td>\n";
    }
    if ($affiche_rang == 'y') echo "<td style=\"text-align: center; width: ".getSettingValue("col_note_largeur")."px;\"><span class='".$style_bulletin."'>-</span></td>\n";
    if (getSettingValue("bull_affiche_appreciations") == 'y') {
        echo "<td style=\"\" colspan=\"2\"><span class='$style_bulletin'>";
        if (($message != '') or ($display_nom == 'y')) {
            echo "$message ";
            if ($display_nom == 'y') {echo "<strong>$aid_nom</strong><br />";}
        }
    }
    echo "$current_eleve_aid_appreciation</span></td>\n</tr>\n";
    //------
}

/**
 * Affiche un tableau pour règler la taille d'un tableau
 * 
 * Utilisé uniquement dans prepa_conseil/index1.php
 *
 * @param int $larg_tab largeur en pixel
 * @param int $bord bords en pixel
 */
function parametres_tableau($larg_tab, $bord) {
    echo "<table border='1' width='680' cellspacing='1' cellpadding='1' summary=\"Tableau de paramètres\">\n";
    echo "<tr><td><span class=\"norme\">largeur en pixel : <input type=\"text\" name=\"larg_tab\" size=\"3\" value=\"".$larg_tab."\" />\n";
    echo "bords en pixel : <input type=\"text\" name=\"bord\" size=\"3\" value=\"".$bord."\" />\n";
    echo "<input type=\"submit\" value=\"Valider\" />\n";
    echo "</span></td></tr></table>\n";
}

/**
 * Affiche un tableau
 *
 * Utilisé uniquement dans prepa_conseil et visu_toutes_notes2.php
 * 
 * @global type 
 * @global type 
 * @global type 
 * @global type
 * @global type 
 * @global type 
 * @param type $nombre_lignes
 * @param type $nb_col
 * @param type $ligne1
 * @param type $col
 * @param type $larg_tab
 * @param type $bord
 * @param type $col1_centre
 * @param type $col_centre
 * @param type $couleur_alterne 
 */
function affiche_tableau($nombre_lignes, $nb_col, $ligne1, $col, $larg_tab, $bord, $col1_centre, $col_centre, $couleur_alterne) {
    global $num_debut_colonnes_matieres, $num_debut_lignes_eleves, $vtn_coloriser_resultats, $vtn_borne_couleur, $vtn_couleur_texte, $vtn_couleur_cellule;

	echo "<table border=\"$bord\" class='boireaus' cellspacing=\"0\" width=\"$larg_tab\" cellpadding=\"1\" summary=\"Tableau\">\n";
    echo "<tr>\n";
    $j = 1;
    while($j < $nb_col+1) {
        echo "<th class='small' id='td_ligne1_$j'>$ligne1[$j]</th>\n";
        $j++;
    }
    echo "</tr>\n";
    $i = "0";
    $bg_color = "";
    $flag = "1";
    $alt=1;
    while($i < $nombre_lignes) {
        if((isset($couleur_alterne))&&($couleur_alterne=='y')) {
            if ($flag==1) {$bg_color = "bgcolor=\"#C0C0C0\"";} else {$bg_color = "     ";}
        }

	    $alt=$alt*(-1);
        echo "<tr class='";
		if((isset($couleur_alterne))&&($couleur_alterne=='y')) {echo "lig$alt ";}
		echo "white_hover'>\n";
        $j = 1;
        while($j < $nb_col+1) {
            if ((($j == 1) and ($col1_centre == 0)) or (($j != 1) and ($col_centre == 0))) {

				echo "<td class='small' ";
				if(!preg_match("/Rang de l/",$ligne1[$j])) {
					if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
						if(mb_strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
							for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
								if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
									echo " style='";
									if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
									if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
									echo "'";
									break;
								}
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";

            } else {
				echo "<td align=\"center\" class='small' ";
				if(!preg_match("/Rang de l/",$ligne1[$j])) {
					if(($vtn_coloriser_resultats=='y')&&($j>=$num_debut_colonnes_matieres)&&($i>=$num_debut_lignes_eleves)) {
						if(mb_strlen(preg_replace('/[0-9.,]/','',$col[$j][$i]))==0) {
							for($loop=0;$loop<count($vtn_borne_couleur);$loop++) {
								if(preg_replace('/,/','.',$col[$j][$i])<=preg_replace('/,/','.',$vtn_borne_couleur[$loop])) {
									echo " style='";
									if($vtn_couleur_texte[$loop]!='') {echo "color:$vtn_couleur_texte[$loop]; ";}
									if($vtn_couleur_cellule[$loop]!='') {echo "background-color:$vtn_couleur_cellule[$loop]; ";}
									echo "'";
									break;
								}
							}
						}
					}
				}
				echo ">{$col[$j][$i]}</td>\n";
            }
            $j++;
        }
        echo "</tr>\n";
        if ($flag == "1") {$flag = "0";} else {$flag = "1";}
        $i++;
    }
    echo "</table>\n";
}



/**
 * Crée un formulaire avec une zone de sélection contenant les classes
 *
 * @param type $link lien vers la page à atteindre ensuite
 * @param type $current Id de la classe courante pour qu'elle soit par défaut
 * @param type $year l'année
 * @param type $month le mois
 * @param type $day le jour
 * @param type $domaine le domaine (cdt,...)
 * @return text la balise <form> complète
 */
function make_classes_select_html($link, $current, $year, $month, $day, $domaine='')
{
  // Pour le multisite, on doit récupérer le RNE de l'établissement
  $rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
  $aff_input_rne = $aff_get_rne = NULL;
  if ($rne != 'aucun') {
	$aff_input_rne = '<input type="hidden" name="rne" value="' . $rne . '" />' . "\n";
	$aff_get_rne = '&amp;rne=' . $rne;
  }
  $out_html = "<form name=\"classe\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\"><strong><em>Classe :</em></strong><br />
  " . $aff_input_rne . "
  <select name=\"classe\" onchange=\"classe_go()\">";

  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=-1\">(Choisissez une classe)";


  if (isset($_SESSION['statut']) && ($_SESSION['statut']=='scolarite'
		  && getSettingValue('GepiAccesCdtScolRestreint')=="yes")) {
  $sql = "SELECT DISTINCT c.id, c.classe
	FROM classes c, j_groupes_classes jgc, ct_entry ct, j_scol_classes jsc
	WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jsc.id_classe=jgc.id_classe
	  AND jsc.login='".$_SESSION ['login']."'
		)
	ORDER BY classe ;";
  } else if (isset($_SESSION['statut']) && ($_SESSION['statut']=='cpe'
		  && getSettingValue('GepiAccesCdtCpeRestreint')=="yes")) {
	$sql = "SELECT DISTINCT c.id, c.classe
	  FROM classes c, j_groupes_classes jgc, ct_entry ct, j_eleves_cpe jec,j_eleves_classes jecl
	  WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jec.cpe_login = '".$_SESSION ['login']."'
	  AND jec.e_login = jecl.login
	  AND jecl.id_classe = jgc.id_classe)
	  ORDER BY classe ;";
  } else {
	if(isset($_SESSION['statut']) && ($_SESSION['statut']=='professeur')&&(!getSettingAOui('GepiAccesCDTToutesClasses'))) {
		$sql = "SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_entry ct, j_groupes_professeurs jgp
		  WHERE (c.id = jgc.id_classe
		  AND jgp.id_groupe = ct.id_groupe
		  AND jgc.id_groupe = ct.id_groupe
		  AND jgp.login='".$_SESSION['login']."')
		  ORDER BY classe";
	}
	else {
		$sql = "SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_entry ct
		  WHERE (c.id = jgc.id_classe
		  AND jgc.id_groupe = ct.id_groupe)
		  ORDER BY classe";
	}
  }

  //GepiAccesCdtCpeRestreint

  $res = sql_query($sql);


  if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
  {
    $selected = ($row[0] == $current) ? "selected" : "";
    $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$row[0]" . $aff_get_rne;
    $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1]);
  }
  $out_html .= "</select>
  <script type=\"text/javascript\">
  <!--
  function classe_go()
  {
  box = document.forms[\"classe\"].classe;
  destination = box.options[box.selectedIndex].value;
  if (destination) location.href = destination;
  }
  // -->
  </script>
  <noscript>
  <input type=submit value=\"OK\" />
  </noscript>
  </form>";
  return $out_html;
}

/**
 * Crée un formulaire avec une zone de sélection contenant les groupes 
 * 
 * $id_ref peut être soit l'ID d'une classe, auquel cas on affiche tous les groupes pour la classe,
 * soit le login d'un élève, auquel cas on affiche tous les groupes pour l'élève en question
 * 
 * @param text $link lien vers la page à atteindre ensuite
 * @param int|text $id_ref Id d'une classe ou login d'un élève
 * @param int|text $current Id de la classe courante
 * @param int $year Année
 * @param type $month Mois
 * @param type $day Jour
 * @param text $special
 * @return text La balise <form>
 */
function make_matiere_select_html($link, $id_ref, $current, $year, $month, $day, $special='')
{
	// Pour le multisite, on doit récupérer le RNE de l'établissement
	$prof="";
	
	$rne = isset($_GET['rne']) ? $_GET['rne'] : (isset($_POST['rne']) ? $_POST['rne'] : 'aucun');
	$aff_input_rne = $aff_get_rne = NULL;
	if ($rne != 'aucun') {
		$aff_input_rne = '<input type="hidden" name="rne" value="' . $rne . '" />' . "\n";
		$aff_get_rne = '&amp;rne=' . $rne;
	}
		$out_html = "<form id=\"matiere\"  method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n" . $aff_input_rne . "\n
	<h2 class='h2_label'> \n<label for=\"enseignement\"><strong><em>Matière :<br /></em></strong></label>\n</h2>\n<p>\n<select id=\"enseignement\" name=\"matiere\" onchange=\"matiere_go()\">\n ";
	
	if (is_numeric($id_ref)) {
		$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_classe=$id_ref\">(Choisissez un enseignement)</option>\n";
	
		if($special!='') {
			$selected="";
			if($special=='Toutes_matieres') {$selected=" selected='TRUE'";}
			if (is_numeric($id_ref)) {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			} else {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			}
			$out_html .= "<option $selected value=\"$link2\"$selected>Toutes les matières</option>\n";
		}
	
		$sql = "select DISTINCT g.id, g.name, g.description from j_groupes_classes jgc, groupes g, ct_entry ct where (" .
				"jgc.id_classe='".$id_ref."' and " .
				"g.id = jgc.id_groupe and " .
				"jgc.id_groupe = ct.id_groupe" .
				") order by g.name";
	} else {
		$out_html .= "<option value=\"".$link."?&amp;year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;login_eleve=$id_ref\">(Choisissez un enseignement)</option>\n";

		if($special!='') {
			$selected="";
			if($special=='Toutes_matieres') {$selected=" selected='TRUE'";}
			if (is_numeric($id_ref)) {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			} else {
				$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=Toutes_matieres" . $aff_get_rne;
			}
			$out_html .= "<option $selected value=\"$link2\"$selected>Toutes les matières</option>\n";
		}

		$sql = "select DISTINCT g.id, g.name, g.description from j_eleves_groupes jec, groupes g, ct_entry ct where (" .
				"jec.login='".$id_ref."' and " .
				"g.id = jec.id_groupe and " .
				"jec.id_groupe = ct.id_groupe" .
				") order by g.name";
	}
	$res = sql_query($sql);
	if ($res) for ($i = 0; ($row = sql_row($res, $i)); $i++)
	{
		$test_prof = "SELECT nom, prenom FROM j_groupes_professeurs j, utilisateurs u WHERE (j.id_groupe='".$row[0]."' and u.login=j.login) ORDER BY nom, prenom";
		$res_prof = sql_query($test_prof);
		$chaine = "";
		for ($k=0;$prof=sql_row($res_prof,$k);$k++) {
			if ($k != 0) $chaine .= ", ";
			$chaine .= htmlspecialchars($prof[0])." ".mb_substr(htmlspecialchars($prof[1]),0,1).".";
		}

		$selected = ($row[0] == $current) ? "selected=\"selected\"" : "";
		if (is_numeric($id_ref)) {
			$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$id_ref&amp;id_groupe=$row[0]" . $aff_get_rne;
		} else {
			$link2 = "$link?&amp;year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=$id_ref&amp;id_groupe=$row[0]" . $aff_get_rne;
		}

		$out_html .= "<option $selected value=\"$link2\" title=\"".htmlspecialchars($row[2] . " - ")." ".$chaine;
		$tmp_tab=get_classes_from_id_groupe($row[0]);
		if(isset($tmp_tab['classlist_string'])) {
			$out_html .= " en ".$tmp_tab['classlist_string'];
		}
		$out_html .= "\">" . htmlspecialchars($row[2] . " - ")." ".$chaine."</option>";
	}
	$out_html .= "\n</select>\n</p>\n
	
	<script type=\"text/javascript\">
	<!--
	function matiere_go()
	{
		box = document.forms[\"matiere\"].matiere;
		destination = box.options[box.selectedIndex].value;
		if (destination) location.href = destination;
	}
	// -->
	</script>
	
	<noscript><p>\n";
		if (is_numeric($id_ref)) {
			$out_html .= "<input type=\"hidden\" name=\"id_classe\" value=\"$id_ref\" />\n";
		} else {
			$out_html .= "<input type=\"hidden\" name=\"login_eleve\" value=\"$id_ref\" />\n";
		}
		$out_html .= "<input type=\"hidden\" name=\"year\" value=\"$year\" />
		<input type=\"hidden\" name=\"month\" value=\"$month\" />
		<input type=\"hidden\" name=\"day\" value=\"$day\" />
		<input type=\"submit\" value=\"OK\" />
		</p>
	</noscript>
	</form>\n";
	
	return $out_html;
}

/**
 * Crée un formulaire avec une zone de sélection contenant les élèves 
 *
 * @global text 
 * @param text $link lien vers la page à atteindre ensuite
 * @param type $login_resp login du responsable
 * @param type $current login de l'élève actuellement sélectionné
 * @param type $year Année
 * @param type $month Mois
 * @param type $day Jour
 * @return type la balise <form...>
 */
function make_eleve_select_html($link, $login_resp, $current, $year, $month, $day)
{
	global $selected_eleve;
	// $current est le login de l'élève actuellement sélectionné
	$sql="(SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$login_resp."' AND (re.resp_legal='1' OR re.resp_legal='2')))";
	if(getSettingAOui('GepiMemesDroitsRespNonLegaux')) {
		$sql.=" UNION (SELECT e.login, e.nom, e.prenom FROM eleves e, resp_pers r, responsables2 re 
						WHERE (e.ele_id = re.ele_id AND 
							re.pers_id = r.pers_id AND 
							r.login = '".$login_resp."' AND 
							re.acces_sp='y' AND
							re.resp_legal='0'))";
	}
	$sql.=";";
	//echo "$sql<br />";
	$get_eleves = mysqli_query($GLOBALS["mysqli"], $sql);

	if (mysqli_num_rows($get_eleves) == 0) {
			// Aucun élève associé
		$out_html = "<p>Vous semblez n'être responsable d'aucun élève ! Contactez l'administrateur pour corriger cette erreur.</p>";
	} elseif (mysqli_num_rows($get_eleves) == 1) {
			// Un seul élève associé : pas de formulaire nécessaire
		$selected_eleve = mysqli_fetch_object($get_eleves);
		$out_html = "<p class='bold'>Elève : ".$selected_eleve->prenom." ".$selected_eleve->nom."</p>";
	} else {
		// Plusieurs élèves : on affiche un formulaire pour choisir l'élève
	  $out_html = "<form id=\"eleve\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">\n<h2 class='h2_label'>\n<label for=\"choix_eleve\"><strong><em>Elève :</em></strong></label>\n</h2>\n<p>\n<select id=\"choix_eleve\" name=\"eleve\" onchange=\"eleve_go()\">\n";
	  $out_html .= "<option value=\"".$link."?year=".$year."&amp;month=".$month."&amp;day=".$day."\">(Choisissez un élève)</option>\n";
		while ($current_eleve = mysqli_fetch_object($get_eleves)) {
		   if ($current) {
		   	$selected = ((is_object($current)&&($current_eleve->login == $current->login))||($current_eleve->login == $current)) ? "selected='selected'" : "";
		   } else {
		   	$selected = "";
		   }
		   $link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;login_eleve=".$current_eleve->login;
		   $out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($current_eleve->prenom . " - ".$current_eleve->nom)."</option>\n";
		}
        
	  $out_html .= "</select></p>
	  <script type=\"text/javascript\">
	  <!--
	  function eleve_go()
	  {
	    box = document.forms[\"eleve\"].eleve;
	    destination = box.options[box.selectedIndex].value;
	    if (destination) location.href = destination;
	  }
	  // -->
	  </script>

	  <noscript>
		<p>
		  <input type=\"hidden\" name=\"year\" value=\"$year\" />
		  <input type=\"hidden\" name=\"month\" value=\"$month\" />
		  <input type=\"hidden\" name=\"day\" value=\"$day\" />
		  <input type=\"submit\" value=\"OK\" />
		</p>
		</noscript>
	  </form>\n";
	}
	return $out_html;
}

/**
 * Construit une liste à puces des documents joints
 *
 * @param int $id_ct Id du cahier de texte
 * @param type $type_notice c pour ct_documents, t pour ct_devoirs_documents
 * @return string La liste à puces
 * @see getSettingValue()
 */
function affiche_docs_joints($id_ct,$type_notice) {
  global $contexte_affichage_docs_joints;

  // documents joints
  $html = '';
  $architecture="/documents/cl_dev";
  if ($type_notice == "t") {
      $sql = "SELECT titre, emplacement, visible_eleve_parent  FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
  } else if ($type_notice == "c") {
      $sql = "SELECT titre, emplacement, visible_eleve_parent FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
  }

  $res = sql_query($sql);
    if (($res) and (sql_count($res)!=0)) {
      $html .= "<span class='petit'>Document(s) joint(s):</span>";
      $html .= "<ul style=\"padding-left: 15px;\">";
      for ($i=0; ($row = sql_row($res,$i)); $i++) {
          if(((!isset($_SESSION['statut']))&&(getSettingValue('cdt_possibilite_masquer_pj')!='y'))||
              ((!isset($_SESSION['statut']))&&(getSettingValue('cdt_possibilite_masquer_pj')=='y')&&($row[2]==TRUE))||
              ((isset($_SESSION['statut']))&&($_SESSION['statut']!='eleve')&&($_SESSION['statut']!='responsable')&&($contexte_affichage_docs_joints!="visu_eleve"))||
              (($contexte_affichage_docs_joints=="visu_eleve")&&(getSettingValue('cdt_possibilite_masquer_pj')=='y')&&($row[2]==TRUE))||
              ((isset($_SESSION['statut']))&&(getSettingValue('cdt_possibilite_masquer_pj')!='y')&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))||
              ((isset($_SESSION['statut']))&&(getSettingValue('cdt_possibilite_masquer_pj')=='y')&&($row[2]==TRUE)&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))
          ) {
                $titre = $row[0];
                $emplacement = $row[1];
              // Ouverture dans une autre fenêtre conservée parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
              // alternative, utiliser un javascript
                //$html .= "<li style=\"padding: 0px; margin: 0px;font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return FALSE;\" href=\"$emplacement\">$titre</a></li>";
                $html .= "<li style=\"padding: 0px; margin: 0px;font-size: 80%;\"><a href=\"$emplacement\" target='_blank'>$titre</a></li>";
          }
      }
      $html .= "</ul>";
     }
   return $html;
 }

/**
 * Fonction destinée à présenter une liste de liens répartis en $nbcol colonnes
 * 
 *
 * @param type $tab_txt tableau des textes
 * @param type $tab_lien tableau des liens
 * @param int $nbcol Nombre de colonnes
 * @param type $extra_options Options supplémentaires
 */
function tab_liste($tab_txt,$tab_lien,$nbcol,$extra_options = NULL){

	// Nombre d'enregistrements à afficher
	$nombreligne=count($tab_txt);

	if(!is_int($nbcol)){
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=round($nombreligne/$nbcol);

	echo "<table width='100%' summary=\"Tableau de choix\">\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";

	$i = 0;
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		//echo "<br />\n";
		echo "<a href='".$tab_lien[$i]."'";
    if ($extra_options) echo ' '.$extra_options;
    echo ">".$tab_txt[$i]."</a>";
		echo "<br />\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
}

/**
 * Crée des liens html
 *
 * @param string $ele_login Login de l'élève
 * @return string 
 */
function liens_class_from_ele_login($ele_login){
	$chaine="";
	$tab_classe=get_class_from_ele_login($ele_login);
	if(isset($tab_classe)){
		if(count($tab_classe)>0){
			foreach ($tab_classe as $key => $value){
				if(mb_strlen(preg_replace("/[0-9]/","",$key))==0) {
					if($_SESSION['statut']=='administrateur') {
						$chaine.=", <a href='../classes/classes_const.php?id_classe=$key'>$value</a>";
					}
					else {
						$chaine.=", <a href='../eleves/index.php?id_classe=$key&amp;quelles_classes=certaines&amp;case_2=yes'>$value</a>";
					}
				}
			}
			$chaine="(".mb_substr($chaine,2).")";
		}
	}
	return $chaine;
}

/**
 * Teste et construit un tableau html des dossiers qui doivent être accessibles en écriture
 *
 * Par défaut les dossiers nécessaires à GEPI
 * 
 * Le chemin complet doit être passé
 * @global string
 * @param type $tab_restriction Le tableau des répertoires à tester
 */
function test_ecriture_dossier($tab_restriction=array()) {
    global $gepiPath, $multisite;

	if(count($tab_restriction)>0) {
		$tab_dossiers_rw=$tab_restriction;
	}
	else {
		$tab_dossiers_rw=array("artichow/cache","backup","documents","documents/archives","images","images/background","lib/standalone/HTMLPurifier/DefinitionCache/Serializer","mod_ooo/mes_modeles","mod_ooo/tmp","photos","temp");
    /**
     * Pour Debug : Décommenter les 2 lignes si pas en multisites
     */
    /* *
        $multisite='y';
        $_COOKIE['RNE']="essai";
     /* */
        if ((isset($multisite))&&($multisite=='y')&&(isset($_COOKIE['RNE']))) {
          $tab_dossiers_rw[] = 'photos/'.$_COOKIE['RNE'];
          $tab_dossiers_rw[] = 'photos/'.$_COOKIE['RNE'].'/eleves';
          $tab_dossiers_rw[] = 'photos/'.$_COOKIE['RNE'].'/personnels';
        }
	}

	$nom_fichier_test='test_acces_rw';

	echo "<table class='boireaus'>\n";
    echo "<caption style='display:none;'>dossiers devant être accessibles en écriture</caption>\n";
	echo "<tr>\n";
	echo "<th>Dossier</th>\n";
	echo "<th>Ecriture</th>\n";
	echo "</tr>\n";
	$alt=1;
	for($i=0;$i<count($tab_dossiers_rw);$i++) {
		$ok_rw="no";
		if ($f = @fopen("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test, "w")) {
			@fputs($f, '<'.'?php $ok_rw = "yes"; ?'.'>');
			@fclose($f);
			include("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test);
			$del = @unlink("../".$tab_dossiers_rw[$i]."/".$nom_fichier_test);
		}
		$alt=$alt*(-1);
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<td style='text-align:left;'>$gepiPath/$tab_dossiers_rw[$i]</td>\n";
		echo "<td>";
		if($ok_rw=='yes') {
			echo "<img src='../images/enabled.png' height='20' width='20' alt=\"Le dossier est accessible en écriture.\" />";
		}
		else {
			echo "<img src='../images/disabled.png' height='20' width='20' alt=\"Le dossier n'est pas accessible en écriture.\" />";
		}
		echo "</td>\n";
		echo "</tr>\n";

		if($tab_dossiers_rw[$i]=="documents/archives") {
			if($multisite=='y') {
				$dossier_temp='documents/archives/'.$_COOKIE['RNE'];
			}
			else {
				$dossier_temp='documents/archives/etablissement';
			}

			if(file_exists("../$dossier_temp")) {
				$ok_rw="no";
				if ($f = @fopen("../".$dossier_temp."/".$nom_fichier_test, "w")) {
					@fputs($f, '<'.'?php $ok_rw = "yes"; ?'.'>');
					@fclose($f);
					include("../".$dossier_temp."/".$nom_fichier_test);
					$del = @unlink("../".$dossier_temp."/".$nom_fichier_test);
				}
				$alt=$alt*(-1);
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td style='text-align:left;'>$gepiPath/$dossier_temp</td>\n";
				echo "<td>";
				if($ok_rw=='yes') {
					echo "<img src='../images/enabled.png' height='20' width='20' alt=\"Le dossier est accessible en écriture.\" />";
				}
				else {
					echo "<img src='../images/disabled.png' height='20' width='20' alt=\"Le dossier n'est pas accessible en écriture.\" />";
				}
				echo "</td>\n";
				echo "</tr>\n";

			}
		}
	}
	echo "</table>\n";
}

/**
 * Affiche un tableau html des connexions d'un utilisateur
 * 
 * $duree
 * -   7 -> "une semaine"
 * -  15 -> "quinze jours"
 * -  30 -> "un mois"
 * -  60 -> "deux mois"
 * - 183 -> "six mois"
 * - 365 -> "un an"
 * - all -> "le début"
 *
 * @param string $login Login de l'utilisateur
 * @param int|string $duree
 * @param string $page 'mon_compte' pour afficher ses propres connexions
 * @param string $pers_id Permet d'avoir une balise <input type='hidden' personnelle
 * @todo On pourrait utiliser $_SESSION['login'] plutôt que $page
 */
function journal_connexions($login,$duree,$page='mon_compte',$pers_id=NULL) {
	global $active_hostbyaddr;

	switch( $duree ) {
	case 7:
		$display_duree="une semaine";
		break;
	case 15:
		$display_duree="quinze jours";
		break;
	case 30:
		$display_duree="un mois";
		break;
	case 60:
		$display_duree="deux mois";
		break;
	case 183:
		$display_duree="six mois";
		break;
	case 365:
		$display_duree="un an";
		break;
	case 'all':
		$display_duree="le début";
		break;
	}

	if($page=='mon_compte') {
		echo "<h2>Journal de vos connexions depuis <strong>".$display_duree."</strong>**</h2>\n";
	}
	else {
		echo "<h2>Journal des connexions de ".civ_nom_prenom($login)." depuis <strong>".$display_duree."</strong>**</h2>\n";
	}
	$requete = '';
	if ($duree != 'all') {$requete = "and START > now() - interval " . $duree . " day";}

	$sql = "select START, SESSION_ID, REMOTE_ADDR, USER_AGENT, AUTOCLOSE, END from log where LOGIN = '".$login."' ".$requete." order by START desc";
	//echo "$sql<br />";
	$day_now   = date("d");
	$month_now = date("m");
	$year_now  = date("Y");
	$hour_now  = date("H");
	$minute_now = date("i");
	$seconde_now = date("s");
	$now = mktime($hour_now, $minute_now, $seconde_now, $month_now, $day_now, $year_now);

	echo "<ul>
<li>Les lignes en <span style='color:red; font-weight:bold;'>rouge</span> signalent une tentative de connexion avec un <span style='color:red; font-weight:bold;'>mot de passe erroné</span>.</li>
<li>Les lignes en <span style='color:orange; font-weight:bold;'>orange</span> signalent une session close pour laquelle vous ne vous êtes <span style='color:orange; font-weight:bold;'>pas déconnecté correctement</span>.</li>
<li>Les lignes en <span style='color:black; font-weight:bold;'>noir</span> signalent une <span style='color:black; font-weight:bold;'>session close normalement</span>.</li>
<li>Les lignes en <span style='color:green; font-weight:bold;'>vert</span> indiquent les <span style='color:green; font-weight:bold;'>sessions en cours</span> (<em>cela peut correspondre à une connexion actuellement close mais pour laquelle vous ne vous êtes pas déconnecté correctement</em>).</li>
</ul>
<table class='boireaus' style='width: 90%; margin-left: auto; margin-right: auto; margin-bottom: 32px;' cellpadding='5' cellspacing='0' summary='Connexions'>
	<tr>
		<th class='col'>Début session</th>
		<th class='col'>Fin session</th>
		<th class='col'>Adresse IP et nom de la machine cliente</th>
		<th class='col'>Navigateur</th>
	</tr>
";

	$tab_browser=array();
	$tab_host=array();
	$res = sql_query($sql);
	if ($res) {
		$alt=1;
		for ($i = 0; ($row = sql_row($res, $i)); $i++)
		{
			$annee_b = mb_substr($row[0],0,4);
			$mois_b =  mb_substr($row[0],5,2);
			$jour_b =  mb_substr($row[0],8,2);
			$heures_b = mb_substr($row[0],11,2);
			$minutes_b = mb_substr($row[0],14,2);
			$secondes_b = mb_substr($row[0],17,2);
			$date_debut = $jour_b."/".$mois_b."/".$annee_b." à ".$heures_b." h ".$minutes_b;

			$annee_f = mb_substr($row[5],0,4);
			$mois_f =  mb_substr($row[5],5,2);
			$jour_f =  mb_substr($row[5],8,2);
			$heures_f = mb_substr($row[5],11,2);
			$minutes_f = mb_substr($row[5],14,2);
			$secondes_f = mb_substr($row[5],17,2);
			$date_fin = $jour_f."/".$mois_f."/".$annee_f." à ".$heures_f." h ".$minutes_f;
			$end_time = mktime($heures_f, $minutes_f, $secondes_f, $mois_f, $jour_f, $annee_f);

			$temp1 = '';
			$temp2 = '';
			if ($end_time > $now) {
				$temp1 = "<font color='green'>";
				$temp2 = "</font>";
			} else if (($row[4] == 1) or ($row[4] == 2) or ($row[4] == 3) or ($row[4] == 10)) {
				//$temp1 = "<font color=orange>\n";
				$temp1 = "<font color='#FFA500'>";
				$temp2 = "</font>";
			} else if ($row[4] == 4) {
				$temp1 = "<strong><font color='red'>";
				$temp2 = "</font></strong>";

			}

			$alt=$alt*(-1);
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td class=\"col\">".$temp1.$date_debut.$temp2."</td>\n";
			if ($row[4] == 4) {
				echo "<td class=\"col\">".$temp1.$date_fin."<br />Tentative de connexion<br />avec mot de passe erroné.".$temp2."</td>\n";
			}
			else {
				echo "<td class=\"col\">".$temp1.$date_fin.$temp2."</td>\n";
			}
			if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
				//$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
				if(!in_array($row[2], $tab_host)) {
					$tab_host[$row[2]]=@gethostbyaddr($row[2]);
				}
				$result_hostbyaddr = " - ".$tab_host[$row[2]];
			}
			else if ($active_hostbyaddr == "no_local") {
				if ((mb_substr($row[2],0,3) == 127) or
					(mb_substr($row[2],0,3) == 10.) or
					(mb_substr($row[2],0,7) == 192.168)) {
					$result_hostbyaddr = "";
				}
				else {
					$tabip=explode(".",$row[2]);
					if(($tabip[0]==172)&&($tabip[1]>=16)&&($tabip[1]<=31)) {
						$result_hostbyaddr = "";
					}
					else {
						//$result_hostbyaddr = " - ".@gethostbyaddr($row[2]);
						if(!in_array($row[2], $tab_host)) {
							$tab_host[$row[2]]=@gethostbyaddr($row[2]);
						}
						$result_hostbyaddr = " - ".$tab_host[$row[2]];
					}
				}
			}
			else {
				$result_hostbyaddr = "";
			}

			echo "<td class=\"col\"><span class='small'>".$temp1.$row[2].$result_hostbyaddr.$temp2. "</span></td>\n";
			//echo "<td class=\"col\">".$temp1. detect_browser($row[3]) .$temp2. "</td>\n";
			if(!in_array($row[3], $tab_browser)) {
				$tab_browser[$row[3]]=detect_browser($row[3]);
			}
			echo "<td class=\"col\">".$temp1. $tab_browser[$row[3]] .$temp2. "</td>\n";
			echo "</tr>\n";
			flush();
		}
	}


	echo "</table>\n";

	echo "<form action=\"".$_SERVER['PHP_SELF']."#connexion\" name=\"form_affiche_log\" method=\"post\">\n";

	if($page=='modify_user') {
		echo "<input type='hidden' name='user_login' value='$login' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}
	elseif($page=='modify_eleve') {
		echo "<input type='hidden' name='eleve_login' value='$login' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}
	elseif($page=='modify_resp') {
		echo "<input type='hidden' name='pers_id' value='$pers_id' />\n";
		echo "<input type='hidden' name='journal_connexions' value='y' />\n";
	}

	echo "Afficher le journal des connexions depuis : <select name=\"duree\" size=\"1\">\n";
	echo "<option ";
	if ($duree == 7) echo "selected";
	echo " value=7>Une semaine</option>\n";
	echo "<option ";
	if ($duree == 15) echo "selected";
	echo " value=15 >Quinze jours</option>\n";
	echo "<option ";
	if ($duree == 30) echo "selected";
	echo " value=30>Un mois</option>\n";
	echo "<option ";
	if ($duree == 60) echo "selected";
	echo " value=60>Deux mois</option>\n";
	echo "<option ";
	if ($duree == 183) echo "selected";
	echo " value=183>Six mois</option>\n";
	echo "<option ";
	if ($duree == 365) echo "selected";
	echo " value=365>Un an</option>\n";
	echo "<option ";
	if ($duree == 'all') echo "selected";
	echo " value='all'>Le début</option>\n";
	echo "</select>\n";
	echo "<input type=\"submit\" name=\"Valider\" value=\"Valider\" />\n";

	echo "</form>\n";

	echo "<p class='small'>** Les renseignements ci-dessus peuvent vous permettre de vérifier qu'une connexion pirate n'a pas été effectuée sur votre compte.
	Dans le cas d'une connexion inexpliquée, vous devez immédiatement en avertir l'<a href=\"mailto:" . getSettingValue("gepiAdminAdress") . "\">administrateur</a>.</p>\n";
}

/**
 * Affiche une action à effectuer
 */
function affiche_infos_actions() {
    global $mysqli;
	$sql="SELECT ia.* FROM infos_actions ia, infos_actions_destinataires iad WHERE
	ia.id=iad.id_info AND
	((iad.nature='individu' AND iad.valeur='".$_SESSION['login']."') OR
	(iad.nature='statut' AND iad.valeur='".$_SESSION['statut']."')) ORDER BY date;";
    
        $res = mysqli_query($mysqli, $sql);
        $chaine_id="";
        if($res->num_rows > 0) {
?>
<div id='div_infos_actions' style='width: 60%; border: 2px solid red; padding:3px; margin-left: 20%;'>
    <div id='info_action_titre' style='font-weight: bold; min-height:16px; padding-right:8px;' class='infobulle_entete'>
        <div id='info_action_pliage' style='float:right; width: 1em;'>
            <a href="javascript:div_alterne_affichage('conteneur')" title="Plier/déplier le cadre des actions en attente"><span id='img_pliage_conteneur'><img src='images/icons/remove.png' width='16' height='16' alt='Réduire' /></span></a>
        </div>
<?php      
                if(acces("/gestion/gestion_infos_actions.php", $_SESSION['statut'])) {  
?> 
        <div style='float:right; width: 1em; margin-right:0.5em;'>
            <a href="gestion/gestion_infos_actions.php" title="Consulter, supprimer par lots les actions en attente"><span id='img_supprimer_conteneur'><img src='images/disabled.png' width='16' height='16' alt='Supprimer par lots' /></span></a>
        </div> 
<?php    
                }
?> 
        Actions en attente
    </div>
    <div id='info_action_corps_conteneur'>
<?php
            $cpt_id=0;
            while($lig=$res->fetch_object()) {
?>
        <div id='info_action_<?php echo $lig->id; ?>' style='border: 1px solid black; margin:2px; min-height:16px;'>
            <div id='info_action_titre_<?php echo $lig->id; ?>' style='font-weight: bold; min-height:16px; padding-right:8px;' class='infobulle_entete'>
                <div id='info_action_pliage_<?php echo $lig->id; ?>' style='float:right; width: 1em;'>
                    <a href="javascript:div_alterne_affichage('<?php echo $lig->id; ?>')" title="Plier/déplier l'action en attente"><span id='img_pliage_<?php echo $lig->id; ?>'><img src='images/icons/remove.png' style="width:16px; height:16px;" alt='Réduire' /></span></a>
                </div>
                <?php echo $lig->titre; ?>
            </div>
            <div id='info_action_corps_<?php echo $lig->id; ?>' style='padding:3px;' class='infobulle_corps'>
                <div style='float:right; width: 9em; text-align: right;'>
                    <?php echo "<a href=\"".$_SERVER['PHP_SELF']."?del_id_info=$lig->id".add_token_in_url()."\" onclick=\"return confirmlink(this, '".traitement_magic_quotes($lig->titre)."', 'Etes-vous sûr de vouloir supprimer ".traitement_magic_quotes($lig->titre)."')\" title=\"Supprimer cette notification d'action en attente\">Supprimer</a>"; ?>
                </div>
                <?php echo preg_replace("/\\\\n/","<br />",nl2br($lig->description)); ?>
            </div>
        </div>
<?php
                if($cpt_id>0) {$chaine_id.=", ";}
                $chaine_id.="'$lig->id'";
                $cpt_id++;
            }
?>
    </div>
</div>
<script type='text/javascript'>
    function div_alterne_affichage(id) {
        if(document.getElementById('info_action_corps_'+id)) {
            if(document.getElementById('info_action_corps_'+id).style.display=='none') {
                document.getElementById('info_action_corps_'+id).style.display='';
                document.getElementById('img_pliage_'+id).innerHTML='<img src=\'images/icons/remove.png\' width=\'16\' height=\'16\' alt=\'Réduire\' />'
            }
            else {
                document.getElementById('info_action_corps_'+id).style.display='none';
                document.getElementById('img_pliage_'+id).innerHTML='<img src=\'images/icons/add.png\' width=\'16\' height=\'16\' alt=\'Déplier\' />'
            }
        }
    }

    chaine_id_action=new Array(<?php echo $chaine_id; ?>);
    for(i=0;i<chaine_id_action.length;i++) {
        id_a=chaine_id_action[i];
        if(document.getElementById('info_action_corps_'+id_a)) {
            div_alterne_affichage(id_a);
        }
    }
</script>
<?php
        }
		$res->close();
}


/**
 * Affiche les accès aux cahiers de texte
 */
function affiche_acces_cdt() {
    global $mysqli;
	$retour="";

	$tab_statuts=array('professeur', 'administrateur', 'scolarite');
	if(in_array($_SESSION['statut'], $tab_statuts)) {
		$sql="SELECT a.* FROM acces_cdt a ORDER BY date2;";
        //echo "$sql<br />"; 
            $res = mysqli_query($mysqli, $sql);
            $chaine_id="";
            if($res->num_rows > 0) {
                $visible="y";
                if($_SESSION['statut']=='professeur') {
                    $visible = "n";
                    $sql = "SELECT ag.id_acces FROM acces_cdt_groupes ag, j_groupes_professeurs jgp WHERE jgp.id_groupe=ag.id_groupe AND jgp.login='".$_SESSION['login']."';";
                    $res2 = mysqli_query($mysqli, $sql);
                    if($res2->num_rows > 0) {
                        $visible="y";
                        $tab_id_acces=array();
                        while($lig2 = $res2->fetch_object()) {
                            $tab_id_acces[]=$lig2->id_acces;
                        }
                    }
                }
                if($visible=="y") {
                    $retour.="<div id='div_infos_acces_cdt' style='width: 60%; border: 2px solid red; padding:3px; margin-left: 20%; margin-top:3px;'>\n";
                    $retour.="<div id='info_acces_cdt_titre' style='font-weight: bold;' class='infobulle_entete'>\n";
                        $retour.="<div id='info_acces_cdt_pliage' style='float:right; width: 1em'>\n";
                        $retour.="<a href=\"javascript:div_alterne_affichage_acces_cdt('conteneur')\"><span id='img_pliage_acces_cdt_conteneur'><img src='images/icons/remove.png' width='16' height='16' alt='enlever' /></span></a>";
                        $retour.="</div>\n";
                        $retour.="Accès ouvert à des CDT";
                    $retour.="</div>\n";
                    $retour.="<div id='info_acces_cdt_corps_conteneur'>\n";
                    $cpt_id=0;
                    while($lig = $res->fetch_object()) {
                        $visible="y";
                        if(($_SESSION['statut']=='professeur')&&(!in_array($lig->id,$tab_id_acces))) {
                            $visible="n";
                        }
                        if($visible=="y") {
                            $retour.="<div id='info_acces_cdt_$lig->id' style='border: 1px solid black; margin:2px;'>\n";
                                $retour.="<div id='info_acces_cdt_titre_$lig->id' style='font-weight: bold;' class='infobulle_entete'>\n";
                                    $retour.="<div id='info_acces_cdt_pliage_$lig->id' style='float:right; width: 1em'>\n";
                                    $retour.="<a href=\"javascript:div_alterne_affichage_acces_cdt('$lig->id')\"><span id='img_pliage_acces_cdt_$lig->id'><img src='images/icons/remove.png' width='16' height='16' alt='enlever' /></span></a>";
                                    $retour.="</div>\n";
                                    $retour.="Accès CDT jusqu'au ".formate_date($lig->date2);
                                $retour.="</div>\n";

                                $retour.="<div id='info_acces_cdt_corps_$lig->id' style='padding:3px;' class='infobulle_corps'>\n";
                                    if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
                                        $retour.="<div style='float:right; width: 9em; text-align: right;'>\n";
                                        $retour.="<a href=\"".$_SERVER['PHP_SELF']."?del_id_acces_cdt=$lig->id".add_token_in_url()."\" onclick=\"return confirmlink(this, '".traitement_magic_quotes($lig->description)."', 'Etes-vous sûr de vouloir supprimer cet accès')\">Supprimer l'accès</span></a>";
                                        $retour.="</div>\n";
                                    }

                                    $retour.="<p><strong>L'accès a été ouvert pour le motif suivant&nbsp;:</strong><br />";
                                    $retour.=preg_replace("/\\\\r\\\\n/","<br />",$lig->description);
                                    $retour.="</p>\n";

                                    $chaine_enseignements="<ul>";
                                    $sql="SELECT id_groupe FROM acces_cdt_groupes WHERE id_acces='$lig->id';";
                                    $res3=mysqli_query($GLOBALS["mysqli"], $sql);
                                    if(mysqli_num_rows($res3)>0) {
                                        $tab_champs=array('classes', 'professeurs');
                                        while($lig3=mysqli_fetch_object($res3)) {
                                            $current_group=get_group($lig3->id_groupe);
                                            if((is_array($current_group))&&(count($current_group)>0)) {
                                                $chaine_profs="";
                                                if(isset($current_group['profs']['users'])) {
                                                    $cpt=0;
                                                    foreach($current_group['profs']['users'] as $login_prof => $current_prof) {
                                                        if($cpt>0) {$chaine_profs.=", ";}
                                                        $chaine_profs.=$current_prof['civilite']." ".$current_prof['nom']." ".$current_prof['prenom'];
                                                        $cpt++;
                                                    }
                                                }

                                                $chaine_enseignements.="<li>";
                                                $chaine_enseignements.=$current_group['name']." (<i>".$current_group['description']."</i>) en ".$current_group['classlist_string']." (<i>".$chaine_profs."</i>)";
                                                $chaine_enseignements.="</li>\n";
                                            }
                                        }
                                    }
                                    $chaine_enseignements.="</ul>";

                                    $retour.="<p>Les CDT accessibles à l'adresse <a href='$lig->chemin' target='_blank'>$lig->chemin</a> sont&nbsp;:<br />".$chaine_enseignements."</p>";
                                $retour.="</div>\n";
                            $retour.="</div>\n";
                            if($cpt_id>0) {$chaine_id.=", ";}
                            $chaine_id.="'$lig->id'";
                            $cpt_id++;
                        }                        
                    }                    
                    $retour.="</div>\n";
                    $retour.="</div>\n";

                    $retour.="<script type='text/javascript'>
                function div_alterne_affichage_acces_cdt(id) {
                    if(document.getElementById('info_acces_cdt_corps_'+id)) {
                        if(document.getElementById('info_acces_cdt_corps_'+id).style.display=='none') {
                            document.getElementById('info_acces_cdt_corps_'+id).style.display='';
                            document.getElementById('img_pliage_acces_cdt_'+id).innerHTML='<img src=\'images/icons/remove.png\' width=\'16\' height=\'16\' alt=\'enlever\' />'
                        }
                        else {
                            document.getElementById('info_acces_cdt_corps_'+id).style.display='none';
                            document.getElementById('img_pliage_acces_cdt_'+id).innerHTML='<img src=\'images/icons/add.png\' width=\'16\' height=\'16\' alt=\'ajouter\' />'
                        }
                    }
                }

                chaine_id_acces_cdt=new Array($chaine_id);
                for(i=0;i<chaine_id_acces_cdt.length;i++) {
                    id_a=chaine_id_acces_cdt[i];
                    if(document.getElementById('info_acces_cdt_corps_'+id_a)) {
                        div_alterne_affichage_acces_cdt(id_a);
                    }
                }
            </script>\n";
                    
                }
            }
            $res->close();
	}
	echo $retour;
}

/**
 * Crée une balise <p> avec les actions possibles sur un compte
 *
 * @global string 
 * @param string $login Id de l'utilisateur
 * @param string $target target pour ouvrir dans un autre onglet
 * @return string La balises
 * @see add_token_in_url()
 */
function affiche_actions_compte($login, $target="") {
	global $gepiPath;

	$retour="";

	$user=get_infos_from_login_utilisateur($login);

	$retour.="<p>\n";
	if ($user['etat'] == "actif") {
		$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=desactiver&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
		$retour.=add_token_in_url()."'";
		if($target!="") {
			$retour.=" target='$target'";
		}
		$retour.=">Désactiver le compte</a>";
	} else {
		$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=activer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
		$retour.=add_token_in_url()."'";
		if($target!="") {
			$retour.=" target='$target'";
		}
		$retour.=">Réactiver le compte</a>";
	}
	$retour.="<br />\n";
	if ($user['observation_securite'] == 0) {
		$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=observer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
		$retour.=add_token_in_url()."'";
		if($target!="") {
			$retour.=" target='$target'";
		}
		$retour.=">Placer en observation</a>";
	} else {
		$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=stop_observation&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
		$retour.=add_token_in_url()."'";
		if($target!="") {
			$retour.=" target='$target'";
		}
		$retour.=">Retirer l'observation</a>";
	}
	if($user['niveau_alerte']>0) {
		$retour.="<br />\n";
		$retour.="Score cumulé&nbsp;: ".$user['niveau_alerte'];
		$retour.="<br />\n";
		$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=reinit_cumul&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
		$retour.=add_token_in_url()."'";
		if($target!="") {
			$retour.=" target='$target'";
		}
		$retour.=">Réinitialiser cumul</a>";
	}
	$retour.="</p>\n";

	return $retour;
}

/**
 * Crée une balise <p> avec les liens de réinitialisation de mot de passe
 *
 * @global string 
 * @param string $login Id de l'utilisateur
 * @return string La balises
 * @see add_token_in_url()
 */
function affiche_reinit_password($login) {
	global $gepiPath;

	$retour="";

	$user=get_infos_from_login_utilisateur($login);

	$retour.="<p>\n";

	$retour.="<a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html";
	$retour.=add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank'>Réinitialiser le mot de passe</a><br />";

	if ($user['statut'] == "responsable") {
		$retour.="<a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html&amp;affiche_adresse_resp=y";
		$retour.=add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank'>Idem avec adresse</a>";
	}
	$retour.="</p>\n";

	return $retour;
}

/**
 * Insère une fonction javascript pour passer en gras/normal le label associé à un champ checkbox
 *
 * @param string $nom_js_func le nom de la fonction javascript (par défaut 'checkbox_change')
 * @param string $prefixe_texte le préfixe de l'id du label associé (par défaut 'texte_')
 *               Si l'id du checkbox est id_groupe_12, le label doit avoir l'id texte_id_groupe_12
 * @param string $avec_balise_script 'n': On ne renvoye que le texte de la fonction
 *                                   'y': On renvoye le texte entre balises <script>
 * Sur les checkbox, insérer onchange="checkbox_change(this.id)"
 * @return string Le texte de la fonction javascript
 */
function js_checkbox_change_style($nom_js_func='checkbox_change', $prefixe_texte='texte_', $avec_balise_script="n", $perc_opacity=1) {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}
	$retour.="
	function $nom_js_func(id) {
		if(document.getElementById(id)) {
			if(document.getElementById('$prefixe_texte'+id)) {
				if(document.getElementById(id).checked) {
					document.getElementById('$prefixe_texte'+id).style.fontWeight='bold';
					document.getElementById('$prefixe_texte'+id).style.opacity=1;
				}
				else {
					document.getElementById('$prefixe_texte'+id).style.fontWeight='normal';
					document.getElementById('$prefixe_texte'+id).style.opacity=$perc_opacity;
				}
			}
		}
	}\n";
	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

$tableau_type_login=array('name', 'name8', 'fname8', 'fname19', 'firstdotname', 'firstdotname19', 'namef8', 'lcs', 'name9_p', 'p_name9', 'name9-p', 'p-name9', 'name9.p', 'p.name9', 'name9_ppp', 'ppp_name9', 'name9-ppp', 'ppp-name9', 'name9.ppp', 'ppp.name9');
$tableau_type_login_description=array('nom', 
'nom (<em>tronqué à 8 caractères</em>)', 
'pnom (<em>tronqué à 8 caractères</em>)', 
'pnom (<em>tronqué à 19 caractères</em>)', 
'prenom.nom', 
'prenom.nom (<em>tronqué à 19 caractères</em>)', 
'nomp (<em>tronqué à 8 caractères</em>)', 
'pnom (<em>façon LCS</em>)', 
'nom tronqué à 9 caractères + _ + initiale du prénom', 
'initiale du prénom + _ + nom tronqué à 9 caractères', 
'nom tronqué à 9 caractères + - + initiale du prénom', 
'initiale du prénom + - + nom tronqué à 9 caractères', 
'nom tronqué à 9 caractères +  + initiale du prénom', 
'initiale du prénom + . + nom tronqué à 9 caractères', 
'nom tronqué à 9 caractères + _ + 3 premiers caractères du prénom', 
'3 premiers caractères du prénom + _ + nom tronqué à 9 caractères', 
'nom tronqué à 9 caractères + - + 3 premiers caractères du prénom', 
'3 premiers caractères du prénom + - + nom tronqué à 9 caractères', 
'nom tronqué à 9 caractères + . + 3 premiers caractères du prénom', 
'3 premiers caractères du prénom + . + nom tronqué à 9 caractères');

/**
 * Insère les champs radio de choix du format de login
 * @param string $nom_champ : le nom du champ de formulaire
 * @param string $default_login_gen_type : le format par défaut
 * @return string Les champs radio
 */
function champs_radio_choix_format_login($nom_champ, $default_login_gen_type="name") {
	global $tableau_type_login, $tableau_type_login_description;

	$retour="";

	for($i=0;$i<count($type_login);$i++) {
		$retour.="<input type='radio' name='".$nom_champ."' id='".$nom_champ."_".$tableau_type_login[$i]."' value='".$tableau_type_login[$i]."' ";
		if($default_login_gen_type==$tableau_type_login[$i]) {
			$retour.="checked='checked' ";
		}
		$retour.=" onchange='changement()'";
		$retour.="/> <label for='".$nom_champ."_".$tableau_type_login[$i]."'  style='cursor: pointer;'>".$tableau_type_login_description[$i]."</label>\n";
		$retour.="<br />\n";
	}

	if (getSettingValue("use_ent") == "y") {
		$retour.="<input type='radio' name='".$nom_champ."' id='".$nom_champ."_ent' value='ent' checked='checked' ";
		$retour.="onchange='changement()' ";
		$retour.="/>\n";
		$retour.="<label for='".$nom_champ."_ent'  style='cursor: pointer;'>
			Les logins sont produits par un ENT (<span title=\"Vous devez adapter le code du fichier ci-dessus vers la ligne 710.\">Attention !</span>)</label>\n";
		$retour.="<br />\n";
	}
	$retour.="<br />\n";

	return $retour;
}

/**
 * Insère le champ select de choix du format de login
 * @param string $nom_champ : le nom du champ de formulaire
 * @param string $default_login_gen_type : le format par défaut
 * @return string Le champ select avec ses options
 */
function champs_select_choix_format_login($nom_champ, $default_login_gen_type="name") {
	global $tableau_type_login, $tableau_type_login_description;

	$retour="<select name='".$nom_champ."' onchange='changement()'>\n";

	for($i=0;$i<count($tableau_type_login);$i++) {
		$retour.="<option value='".$tableau_type_login[$i]."'";
		if($default_login_gen_type==$tableau_type_login[$i]) {
			$retour.=" selected='true'";
		}
		$retour.=" onchange='changement()'";
		$retour.=">".$tableau_type_login_description[$i]."</option>\n";
	}

	$retour.="</select>\n";

	return $retour;
}


/**
 * Insère un champ input de choix du format de login
 * @param string $nom_champ : le nom du champ de formulaire
 * @param string $default_login_gen_type : le format par défaut
 * @return string Le champ input
*/
function champ_input_choix_format_login($nom_champ, $default_login_gen_type="nnnnnnnnnnnnnnnnnnnn", $longueur_champ=20, $longueur_max_champ=48, $avec_infobulle_explication='y') {
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;
	global $tabdiv_infobulle;

	$retour="<input type='text' name='$nom_champ' id='$nom_champ' value='$default_login_gen_type' size='$longueur_champ' maxlength='$longueur_max_champ' />\n";

	if($avec_infobulle_explication=='y') {
		$titre_infobulle="Formats de login";

		$texte_infobulle="<h4>Contraintes sur le format</h4>
<ul>
	<li>Au maximum 48 caractères.</li>
	<li>Au moins une lettre du prénom et une lettre du nom (<em>quel que soit l'ordre</em>).</li>
	<li>Un caractère entre le prénom et le nom parmi \"<strong>.-_</strong>\", ou aucun.</li>
</ul>
<hr />
<h4>Méthode employée</h4>
<p>
	Le modèle est indiqué à l'aide d'une suite de caractères.<br />
	Exemples pour un utilisateur se nommant <strong>Jean Aimarre</strong>.
</p>
<ul>
	<li>\"<strong>ppp.nnnnnnnn</strong>\" donnera \"<strong>jea.aimarre</strong>\"</li>

	<li>\"<strong>ppp-nnn</strong>\" donnera \"<strong>jea-aim</strong>\"</li>
	<li>\"<strong>p_nnnnnnnnnnn</strong>\" donnera \"<strong>j_aimarre</strong>\"</li>
	<li>\"<strong>pnnnnn</strong>\" donnera \"<strong>jaimar</strong>\"</li>

	<li>\"<strong>nnnnnnnnp</strong>\" donnera \"<strong>aimarrej</strong>\"</li>
	<li>\"<strong>n.ppp</strong>\" donnera \"<strong>a.jea</strong>\"</li>
</ul>\n";

		$tabdiv_infobulle[]=creer_div_infobulle('div_explication_formats_login_'.$nom_champ,$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');
		//$retour.=creer_div_infobulle('div_explication_formats_login',$titre_infobulle,"",$texte_infobulle,"",35,0,'y','y','n','n');

		$retour.=" <a href='#' onclick=\"afficher_div('div_explication_formats_login_$nom_champ','y',20,20); return false\" onmouseover=\"delais_afficher_div('div_explication_formats_login_$nom_champ','y',20,20,1000,20,20)\" onmouseout=\"cacher_div('div_explication_formats_login_.$nom_champ')\"><img src='../images/icons/ico_question_petit.png' width='15' height='15' alt='' title='aide' /></a>";
	}

	$retour.="<br />\n";
	$retour.="Casse du login&nbsp;: <label for='".$nom_champ."_casse_min'>minuscules</label><input type='radio' name='".$nom_champ."_casse' id='".$nom_champ."_casse_min' value='min' ";
	if((getSettingValue($nom_champ.'_casse')=='min')||(getSettingValue($nom_champ.'_casse')=='')) {$retour.="checked='checked' ";}
	$retour.="/> / \n";
	$retour.="<input type='radio' name='".$nom_champ."_casse' id='".$nom_champ."_casse_maj' value='maj' ";
	if(getSettingValue($nom_champ.'_casse')=='maj') {$retour.="checked='checked' ";}
	$retour.="/><label for='".$nom_champ."_casse_maj'> majuscules</label>\n";

	return $retour;
}

/**
 * Test du format de login proposé
 * @param string $format_login : le format de login proposé
 * @return boolean : true/false selon que le format est valide ou non
*/
function check_format_login($format_login) {
	if($format_login=="") {
		$test_profil = false;
	}
	elseif(mb_strlen($format_login)>48) {
		$test_profil = false;
	}
	else {
		$test_profil = (preg_match("#^p*[._-]?n*$#", $format_login)) ? true : false;
		$test_profil = (preg_match("#^n*[._-]?p*$#", $format_login)) ? true : $test_profil;
	}
	return $test_profil;
}

/**
 * Test JavaScript du format de login proposé pour vérification avant submit
 * @param string $nom_js_func : le nom de la fonction JS à insérer
 * @param string $avec_balise_script 'n': On ne renvoye que le texte de la fonction
 *                                   'y': On renvoye le texte entre balises <script>
 * @return boolean : true/false selon que le format est valide ou non
*/
function insere_js_check_format_login($nom_js_func, $avec_balise_script="n") {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}
	$retour.="
	function $nom_js_func(format) {
		if((format=='')||(format.length>48)) {
			test = false;
		}
		else {
			var reg1 = new RegExp(\"^p*[._-]?n*$\",\"g\");
			var reg2 = new RegExp(\"^n*[._-]?p*$\",\"g\");
			test = ( reg1.test(format) || reg2.test(format) ) ? true : false ;
		}
		return test;
	}\n";
	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

function check_param_bloc_adresse_html($a4_ou_a3="a4") {
	if($a4_ou_a3=="a4") {
		$largeur_page=210;
		$hauteur_page=297;
	}
	else {
		$largeur_page=297;
		$hauteur_page=420;
	}
	
	$retour="";
	
	$temoin_erreur="n";
	
	$addressblock_padding_right=getSettingValue('addressblock_padding_right');
	if(($addressblock_padding_right=='')||(!preg_match('/^[0-9]*$/',$addressblock_padding_right))) {
		$retour.="La valeur de l'espace à droite du bloc adresse n'est pas valide&nbsp;: '$addressblock_padding_right'<br />";
		$temoin_erreur="y";
	}
	
	$addressblock_length=getSettingValue('addressblock_length');
	if(($addressblock_length=='')||(!preg_match('/^[0-9]*$/',$addressblock_length))) {
		$retour.="La valeur de la longueur du bloc adresse n'est pas valide&nbsp;: '$addressblock_length'<br />";
		$temoin_erreur="y";
	}
	
	if($temoin_erreur!="y") {
		if($addressblock_padding_right+$addressblock_length>=$largeur_page) {
			$retour.="La somme des longueur du bloc adresse et de l'espace à droite du bloc adresse dépasse la largeur de la page&nbsp;: $addressblock_padding_right + $addressblock_length >= $largeur_page<br />";
		}
	}
	
	$temoin_erreur="n";
	
	$addressblock_padding_top=getSettingValue('addressblock_padding_top');
	$addressblock_padding_text=getSettingValue('addressblock_padding_text');
	if(($addressblock_padding_top=='')||(!preg_match('/^[0-9]*$/',$addressblock_padding_top))) {
		$retour.="La valeur de l'espace au-dessus du bloc adresse n'est pas valide&nbsp;: '$addressblock_padding_top'<br />";
		$temoin_erreur="y";
	}
	
	if(($addressblock_padding_text=='')||(!preg_match('/^[0-9]*$/',$addressblock_padding_text))) {
		$retour.="La valeur de l'espace au-dessous du bloc adresse n'est pas valide&nbsp;: '$addressblock_padding_text'<br />";
		$temoin_erreur="y";
	}
	
	if($temoin_erreur!="y") {
		if($addressblock_padding_top+$addressblock_padding_text>=$hauteur_page) {
			$retour.="La somme des espaces au-dessus et au-dessous du bloc adresse dépassent la hauteur de la page&nbsp;: $addressblock_padding_top + $addressblock_padding_text >= $hauteur_page<br />";
		}
	}
	
	// Pourcentages:
	$addressblock_logo_etab_prop=getSettingValue('addressblock_logo_etab_prop');
	if(($addressblock_logo_etab_prop=='')||(!preg_match('/^[0-9]*$/',$addressblock_logo_etab_prop))) {
		$retour.="La valeur de la proportion horizontale allouée au logo de l'établissement n'est pas valide&nbsp;: '$addressblock_logo_etab_prop'<br />";
	}
	elseif($addressblock_logo_etab_prop>100) {
		$retour.="La valeur de la proportion horizontale allouée au logo de l'établissement dépasse les 100%&nbsp;: '$addressblock_logo_etab_prop'<br />";
	}
	
	$addressblock_classe_annee=getSettingValue('addressblock_classe_annee');
	if(($addressblock_classe_annee=='')||(!preg_match('/^[0-9]*$/',$addressblock_classe_annee))) {
		$retour.="La valeur de la proportion horizontale allouée au bloc 'Classe, année, période' n'est pas valide&nbsp;: '$addressblock_classe_annee'<br />";
	}
	elseif($addressblock_classe_annee>100) {
		$retour.="La valeur de la proportion horizontale allouée au bloc 'Classe, année, période' n'est pas valide&nbsp;: '$addressblock_classe_annee'<br />";
	}
	
	return $retour;
}

/**
 * Insertion d'un lien destiné à provoquer l'insertion du code <img src...>
 * vers l'url de l'image passée en paramètre.
  *
 * @param string $url_img : Url de l'image
 *
 * @return string : Chaine HTML <div float:right...><a href...><img...></a></div>
*/
function insere_lien_insertion_image_dans_ckeditor($url_img) {
	$tmp_largeur='';
	$tmp_hauteur='';
	$tmp_size = getimagesize($url_img);
	if($tmp_size) {
		// Pour que l'image puisse être redimensionnée facilement dans la page, on fixe la largeur max à 400px
		$tmp_largeur_max=400;

		if(($tmp_size[0]>$tmp_largeur_max)&&($tmp_size[1]>20)) {
			$tmp_largeur=$tmp_largeur_max;
			$tmp_hauteur=round($tmp_size[1]*$tmp_largeur_max/$tmp_size[0]);
		}
		else {
			$tmp_largeur=$tmp_size[0];
			$tmp_hauteur=$tmp_size[1];
		}
	}
	return "<div style='float:right; width:18px;'><a href=\"javascript:insere_image_dans_ckeditor('".$url_img."','$tmp_largeur','$tmp_hauteur')\" title='Insérer cette image dans le texte'><img src='../images/up.png' width='18' height='18' alt='Insérer cette image dans le texte' /></a></div>";
}

/**
 * Insertion d'un lien destiné à provoquer l'insertion du code <a href...>
 * vers l'url de l'afficheur GeoGebra avec le fichier GGB passé en paramètre.
  *
 * @param string $url_ggb : Url du fichier GGB
 *
 * @return string : Chaine HTML <div float:right...><a href...><img...></a></div>
*/
function insere_lien_insertion_lien_geogebra_dans_ckeditor($titre_ggb, $url_ggb) {
	return "<div style='float:right; width:18px;'><a href=\"javascript:insere_lien_ggb_dans_ckeditor('".preg_replace("/'/", " ", $titre_ggb)."', '".$url_ggb."')\" title='Insérer un lien vers le visionneur GeoGebra pour ce fichier GGB'><img src='../images/up.png' width='18' height='18' alt='Insérer un lien vers le visionneur GeoGebra pour ce fichier GGB' /></a></div>";
}

/** fonction alertant sur la configuration de suhosin
 *
 * @return string Chaine de texte HTML 
 */
function alerte_config_suhosin() {
	$retour="<p class='bold' style='color:red'>Configuration suhosin</p>\n";

	$suhosin_post_max_totalname_length=ini_get('suhosin.post.max_totalname_length');
	if($suhosin_post_max_totalname_length!='') {
		$retour.="<p>Le module suhosin est activé.<br />\nUn paramétrage trop restrictif de ce module peut perturber le fonctionnement de Gepi, particulièrement dans les pages comportant de nombreux champs de formulaire (<i>comme par exemple dans la page de saisie des appréciations par les professeurs</i>)</p>\n";
		$retour.="<p>La page d'extraction des moyennes permettant de modifier/corriger des valeurs propose un très grand nombre de champs.<br />Le module suhosin risque de poser des problèmes.</p>";

		$tab_suhosin=array('suhosin.cookie.max_totalname_length', 
		'suhosin.get.max_vars', 
		'suhosin.get.max_totalname_length', 
		'suhosin.post.max_vars', 
		'suhosin.post.max_totalname_length', 
		'suhosin.post.max_value_length', 
		'suhosin.request.max_vars', 
		'suhosin.request.max_totalname_length', 
		'suhosin.request.max_value_length');

		$retour.="<ul>\n";
		for($i=0;$i<count($tab_suhosin);$i++) {
			$retour.="<li>".$tab_suhosin[$i]." = ".ini_get($tab_suhosin[$i])."</li>\n";
		}
		$retour.="</ul>\n";

		$retour.="En cas de problème, vous pouvez, soit désactiver le module, soit augmenter les valeurs.<br />\n";
		$retour.="C'est généralement la valeur de 'suhosin.post.max_vars' qui pose problème.<br />\n";
		$retour.="Le fichier de configuration de suhosin est habituellement en /etc/php5/conf.d/suhosin.ini<br />\nEn cas de modification de ce fichier, pensez à relancer le service apache ensuite pour prendre en compte la modification.<br />\n";
	}
	else {
		$retour.="<p>Le module suhosin n'est pas activé.<br />Il ne peut pas perturber Gepi.</p>\n";
	}
	return $retour;
}

/** Retourne un tableau HTML de la liste des élèves associés au groupe
 * @param integer $id_groupe : Identifiant du groupe
 * @param integer $nb_col : Nombre de colonnes du tableau
 *
 * @return string Tableau HTML de la liste des élèves associés au groupe
 */
function tableau_html_eleves_du_groupe($id_groupe, $nb_col) {
	$retour="<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"width: 500px;\">\n";
	$retour.="<thead>\n";
	$retour.="<tr style='background-color:white'>\n";
	$retour.="<th>Élèves</th>\n";
	for($i=1;$i<$nb_col;$i++) {
		$retour.="<th>Col".($i+1)."</th>\n";
	}
	$retour.="</tr>\n";
	$retour.="</thead>\n";
	$retour.="<tbody>\n";
	$sql="SELECT DISTINCT nom, prenom FROM eleves e, j_eleves_groupes jeg WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe';";
	$res_ele_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele_grp)>0) {
		$cpt=0;
		while($lig_ele=mysqli_fetch_object($res_ele_grp)) {
			$retour.="<tr style='background-color:";
			if($cpt%2==0) {$retour.="silver";} else {$retour.="white";}
			$retour.=";'>\n";
			$retour.="<td>".casse_mot($lig_ele->nom, 'maj')." ".casse_mot($lig_ele->prenom, 'majf2')."</td>\n";
			for($i=1;$i<$nb_col;$i++) {
				$retour.="<td></td>\n";
			}
			$retour.="</tr>\n";
			$cpt++;
		}
	}
	$retour.="</tbody>\n";
	$retour.="</table>\n";
	
	return $retour;
}

/** Retourne un tableau HTML des groupes d'une classe dans telle matière
 * @param integer $id_classe : Identifiant de classe
 * @param string $matiere : Nom de matière
 *
 * @return string Tableau HTML de la liste des enseignements d'une matière donnée dans une classe
 */
function tableau_html_groupe_matiere_telle_classe($id_classe, $matiere, $tab_grp_exclus=array()) {
	global $tab_domaines, $tab_domaines_sigle;
	global $avec_lien_edit_group, $themessage;

	$cpt_grp=0;

	$retour="<table class=\"boireaus\" border=\"1\" cellpadding=\"1\" cellspacing=\"1\">\n";
	$retour.="<thead>\n";
	$retour.="<tr>\n";
	$retour.="<th rowspan='2'>Enseignement</th>\n";
	$retour.="<th rowspan='2'>Classes</th>\n";
	$retour.="<th rowspan='2'>Professeurs</th>\n";
	$retour.="<th rowspan='2'>Coefficient</th>\n";
	$retour.="<th rowspan='2'>Catégorie</th>\n";
	$retour.="<th colspan='".count($tab_domaines_sigle)."'>Visibilité</th>\n";
	$retour.="</tr>\n";

	$retour.="<tr>\n";
	for($i=0;$i<count($tab_domaines_sigle);$i++) {
		$retour.="<th>".$tab_domaines_sigle[$i]."</th>\n";
	}
	$retour.="</tr>\n";
	$retour.="</thead>\n";
	$retour.="<tbody>\n";

	// Remarque: Le coef peut différer d'une classe à l'autre pour un groupe multiclasses,
	// mais la visibilité, elle est propre au groupe toutes classes confondues.
	$sql="select DISTINCT g.name, g.id, g.description, jgm.id_matiere, jgc.coef, jgc.categorie_id FROM groupes g, 
			j_groupes_classes jgc, 
			j_groupes_matieres jgm
		WHERE (
			jgm.id_matiere='".$matiere."' AND
			jgc.id_classe='".$id_classe."' AND
			jgm.id_groupe=jgc.id_groupe
			AND jgc.id_groupe=g.id
			)
		ORDER BY jgc.priorite,jgm.id_matiere, g.name;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$alt=1;
		while($lig=mysqli_fetch_object($res)) {
			if(!in_array($lig->id, $tab_grp_exclus)) {
				$alt=$alt*(-1);
				$retour.="<tr class='lig$alt white_hover'>\n";
				$retour.="<td>";
				if($avec_lien_edit_group=="y") {
					$retour.="<a href='edit_group.php?id_classe=$id_classe&amp;id_groupe=$lig->id' onclick=\"return confirm_abandon(this, change, '$themessage');\">".$lig->name."</a>";
				}
				else {
					$retour.=$lig->name;
				}
				$retour.="</td>\n";

				$retour.="<td>";
				$sql="SELECT id, classe FROM classes c, j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig->id' ORDER BY c.classe;";
				$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
				$cpt=0;
				while($lig_clas=mysqli_fetch_object($res_clas)) {
					if($cpt>0) {$retour.=", ";}
					if($lig_clas->id==$id_classe) {
						$retour.="<strong>".$lig_clas->classe."</strong>";
					}
					else {
						$retour.=$lig_clas->classe;
					}
					$cpt++;
				}
				$retour.="</td>\n";

				$retour.="<td>";
				$sql="SELECT u.nom, u.prenom, u.civilite FROM j_groupes_professeurs jgp, utilisateurs u WHERE jgp.id_groupe='$lig->id' AND jgp.login=u.login ORDER BY u.nom, u.prenom;";
				$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
				$cpt=0;
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					if($cpt>0) {$retour.=", ";}
					$retour.=$lig_prof->civilite." ".casse_mot($lig_prof->nom, "maj")." ".casse_mot(mb_substr($lig_prof->prenom,0,1), "maj");
					$cpt++;
				}
				$retour.="</td>\n";

				$retour.="<td title='Coefficient propre à la classe. Si plusieurs classes sont associées à ce groupe, leurs coefficients peuvent différer.'>".$lig->coef."</td>\n";

				$retour.="<td>";
				$sql="SELECT * FROM matieres_categories WHERE id='$lig->categorie_id';";
				$res_cat=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_cat)==0) {
					$retour.="<span style='color:red' title='Cet enseignement ne sera pas visible sur les bulletins si vous optez pour un affichage avec catégories de matières.'>Aucune</span>";
				}
				else {
					$lig_cat=mysqli_fetch_object($res_cat);
					$retour.=$lig_cat->nom_court;
				}
				$retour.="</td>\n";

				$tab_v=array();
				$sql="SELECT * FROM j_groupes_visibilite WHERE id_groupe='$lig->id';";
				$res_v=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_v=mysqli_fetch_object($res_v)) {
					$tab_v[$lig_v->domaine]=$lig_v->visible;
				}

				for($i=0;$i<count($tab_domaines);$i++) {
					$retour.="<td>";
					if((!isset($tab_v[$tab_domaines[$i]]))||($tab_v[$tab_domaines[$i]]!='n')) {
						$retour.="<img src='../images/enabled.png' width='20' height='20' title='Visible' alt='Visible' />";
					}
					else {
						$retour.="<img src='../images/disabled.png' width='20' height='20' title='Invisible' alt='Invisible' />";
					}
					$retour.="</td>\n";
				}
				$retour.="</tr>\n";

				$cpt_grp++;
			}
		}
	}
	$retour.="</tbody>\n";
	$retour.="</table>\n";

	if($cpt_grp>0) {
		return $retour;
	}
	else {
		return "";
	}
}

/** Retourne ce qui a été logué d'une mise à jour d'après Sconet
 * @param integer $id_maj_sconet : Identifiant de la mise à jour loguée.
 * @param integer $ts_maj_sconet : date de début de la mise à jour Sconet.
 *
 * @return string Retourne ce qui a été logué d'une mise à jour d'après Sconet
 */
function get_infos_maj_sconet($id_maj_sconet="", $ts_maj_sconet="") {
	$retour="";
	if($id_maj_sconet!="") {
		$sql="SELECT * FROM log_maj_sconet WHERE id='$id_maj_sconet';";
	}
	elseif($ts_maj_sconet!="") {
		$sql="SELECT * FROM log_maj_sconet WHERE date_debut='$ts_maj_sconet';";
	}

	if(isset($sql)) {
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$retour.="<p>Mise à jour d'après Sconet lancée par ".civ_nom_prenom($lig->login)." le ".formate_date($lig->date_debut, "y");
			if($lig->date_fin!="0000-00-00 00:00:00") {
				$retour.=" et achevée le ".formate_date($lig->date_fin, "y");
			}
			else {
				$retour.=" et <span style='color:red'>non achevée</span>\n";
			}
			$retour.=".</p>\n";

			$retour.=$lig->texte;
		}
	}
	return $retour;
}

/** Retourne la date et le login correspondant à la dernière màj sconet lancée
 *
 * @return string Retourne la date et le login correspondant à la dernière màj sconet lancée
 */
function get_infos_derniere_maj_sconet() {
	$retour="";

	$sql="SELECT * FROM log_maj_sconet ORDER BY date_debut DESC LIMIT 1;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$retour.="<p>La précédente mise à jour d'après Sconet a été lancée par ".civ_nom_prenom($lig->login)." le ".formate_date($lig->date_debut, "y");
		if($lig->date_fin!="0000-00-00 00:00:00") {
			$retour.=" et achevée le ".formate_date($lig->date_fin, "y");
		}
		else {
			$retour.=" et <span style='color:red'>non achevée</span>\n";
		}
		$retour.=".</p>\n";

		//$retour.=$lig->texte;
	}
	return $retour;
}


/** Retourne le lien HTML pour un histogramme des notes passées en paramètre
 *  et génère le DIV infobulle du graphe SVG.
 *
 * @param array $tab_graph_note : Le tableau des notes
 * @param string $titre : Titre de l'infobulle
 * @param string $id : Identifiant de l'infobulle
 *
 * @return string Retourne le lien
 */
function retourne_html_histogramme_svg($tab_graph_note, $titre, $id, $nb_tranches=5, $note_sur=20, $graphe_largeurTotale=200, $graphe_hauteurTotale=150, $graphe_taille_police=3, $graphe_epaisseur_traits=2) {
	global $tabdiv_infobulle;

	$retour="";

	$graphe_serie="";
	if(isset($tab_graph_note)) {
		for($l=0;$l<count($tab_graph_note);$l++) {
			if($l>0) {$graphe_serie.="|";}
			$graphe_serie.=$tab_graph_note[$l];
		}
	}

	$texte="<div align='center'><object data='../lib/graphe_svg.php?";
	$texte.="serie=$graphe_serie";
	$texte.="&amp;note_sur_serie=$note_sur";
	$texte.="&amp;nb_tranches=$nb_tranches";
	$texte.="&amp;titre=Repartition_des_notes";
	$texte.="&amp;v_legend1=Notes";
	$texte.="&amp;v_legend2=Effectif";
	$texte.="&amp;largeurTotale=$graphe_largeurTotale";
	$texte.="&amp;hauteurTotale=$graphe_hauteurTotale";
	$texte.="&amp;taille_police=$graphe_taille_police";
	$texte.="&amp;epaisseur_traits=$graphe_epaisseur_traits";
	$texte.="'";
	$texte.=" width='$graphe_largeurTotale' height='$graphe_hauteurTotale'";
	$texte.=" type=\"image/svg+xml\"></object></div>\n";

	$tabdiv_infobulle[]=creer_div_infobulle($id,$titre,"",$texte,"",14,0,'y','y','n','n');

	$retour.=" <a href='#' onmouseover=\"delais_afficher_div('$id','y',-100,20,1500,10,10);\"";
	$retour.=" onclick=\"afficher_div('$id','y',-100,20);return false;\"";
	$retour.=">";
	$retour.="<img src='../images/icons/histogramme.png' alt=\"$titre\" />";
	$retour.="</a>";

	return $retour;
}

function liste_checkbox_utilisateurs($tab_statuts, $tab_user_preselectionnes=array(), $nom_champ='login_user', $nom_func_js_tout_cocher_decocher='cocher_decocher') {
	$retour="";

	$sql="SELECT login, civilite, nom, prenom, statut FROM utilisateurs WHERE (";
	for($loop=0;$loop<count($tab_statuts);$loop++) {
		if($loop>0) {
			$sql.=" OR ";
		}
		$sql.="statut='".$tab_statuts[$loop]."'";
	}
	$sql.=") AND etat='actif' ORDER BY statut, login, nom, prenom;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table width='100%' summary=\"Tableau de choix des utilisateurs\">\n";
		$retour.="<tr valign='top' align='center'>\n";
		$retour.="<td align='left'>\n";

		$cpt=0;
		$statut_prec="";
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td align='left'>\n";
			}

			if($lig->statut!=$statut_prec) {
				$retour.="<p><b>".ucfirst($lig->statut)."</b><br />\n";
				$statut_prec=$lig->statut;
			}

			$retour.="<input type='checkbox' name='".$nom_champ."[]' id='".$nom_champ."_$cpt' value='$lig->login' ";
			$retour.="onchange=\"checkbox_change('".$nom_champ."_$cpt')\" ";
			if(in_array($lig->login, $tab_user_preselectionnes)) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->login\"><span id='texte_".$nom_champ."_$cpt'$temp_style>$lig->civilite $lig->nom $lig->prenom</span></label><br />\n";

			$cpt++;
		}
		$retour.="</td>\n";
		$retour.="</tr>\n";
		$retour.="</table>\n";

		$retour.="<script type='text/javascript'>
function $nom_func_js_tout_cocher_decocher(mode) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('".$nom_champ."_'+k)){
			document.getElementById('".$nom_champ."_'+k).checked=mode;
			checkbox_change('".$nom_champ."_'+k);
		}
	}
}
</script>\n";
	}

	return $retour;
}

function js_cdt_modif_etat_travail() {
	global $class_notice_dev_fait, $class_notice_dev_non_fait;

	$retour="<script type='text/javascript'>
	function cdt_modif_etat_travail(login_eleve, id_ct) {
		// Pour éviter trop de clics à la suite:
		if(document.getElementById('div_etat_travail_'+id_ct)) {
			document.getElementById('div_etat_travail_'+id_ct).innerHTML='<img src=\'../images/spinner.gif\' class=\'icone16\' />';
		}

		new Ajax.Updater($('div_etat_travail_'+id_ct),'../cahier_texte_2/ajax_cdt.php?login_eleve='+login_eleve+'&id_ct_devoir='+id_ct+'&mode=changer_etat".add_token_in_url(false)."',{method: 'get'});

		setTimeout('cdt_maj_class_div_dev('+id_ct+')', 2000);
	}

	function cdt_maj_class_div_dev(id_ct) {
		if(document.getElementById('div_etat_travail_'+id_ct)) {
			if(document.getElementById('div_travail_'+id_ct)) {
				chaine=document.getElementById('div_etat_travail_'+id_ct).innerHTML;
				//alert(chaine);
				if(chaine.indexOf('NON FAIT:',1)!='-1') {
					document.getElementById('div_travail_'+id_ct).className='$class_notice_dev_non_fait';
				}
				else {
					if(chaine.indexOf('FAIT:',1)!='-1') {
						document.getElementById('div_travail_'+id_ct).className='$class_notice_dev_fait';
					}
				}
			}
		}
	}
</script>\n";
	return $retour;
}

function js_dragresize($minWidth=50, $minHeight=50, $minLeft=0, $minTop=0, $maxLeft=10000, $maxTop=100000) {
	/*
	global $chaine_handles;
	if($chaine_handles=="") {
		$chaine_handles="'tl', 'tm', 'tr', 'ml', 'mr', 'bl', 'bm', 'br'";
	}

	// Reserve...
	var dragresize = new DragResize('dragresize',
	 { minWidth: $minWidth, minHeight: $minHeight, minLeft: $minLeft, minTop: $minTop, maxLeft: $maxLeft, maxTop: $maxTop },
	 \"$chaine_handles\");

	*/
	global $mode_handles;

	$retour="<script type='text/javascript'>
// Using DragResize is simple!
// You first declare a new DragResize() object, passing its own name and an object
// whose keys constitute optional parameters/settings:

var dragresize = new DragResize('dragresize',
 { minWidth: $minWidth, minHeight: $minHeight, minLeft: $minLeft, minTop: $minTop, maxLeft: $maxLeft, maxTop: $maxTop },
 \"$mode_handles\");

// Optional settings/properties of the DragResize object are:
//  enabled: Toggle whether the object is active.
//  handles[]: An array of drag handles to use (see the .JS file).
//  minWidth, minHeight: Minimum size to which elements are resized (in pixels).
//  minLeft, maxLeft, minTop, maxTop: Bounding box (in pixels).

// Next, you must define two functions, isElement and isHandle. These are passed
// a given DOM element, and must \"return true\" if the element in question is a
// draggable element or draggable handle. Here, I'm checking for the CSS classname
// of the elements, but you have have any combination of conditions you like:

dragresize.isElement = function(elm)
{
 if (elm.className && elm.className.indexOf('drsElement') > -1) return true;
};
dragresize.isHandle = function(elm)
{
 if (elm.className && elm.className.indexOf('drsMoveHandle') > -1) return true;
};

// You can define optional functions that are called as elements are dragged/resized.
// Some are passed true if the source event was a resize, or false if it's a drag.
// The focus/blur events are called as handles are added/removed from an object,
// and the others are called as users drag, move and release the object's handles.
// You might use these to examine the properties of the DragResize object to sync
// other page elements, etc.

dragresize.ondragfocus = function() { };
dragresize.ondragstart = function(isResize) { };
dragresize.ondragmove = function(isResize) { };
dragresize.ondragend = function(isResize) { };
dragresize.ondragblur = function() { };

// Finally, you must apply() your DragResize object to a DOM node; all children of this
// node will then be made draggable. Here, I'm applying to the entire document.
dragresize.apply(document);
</script>";

	return $retour;
}

function input_password_to_text($id_champ) {
	global $gepiPath;

	$retour="<span id='span_liens_js_".$id_champ."' style='display:none'>
	<a href='javascript:champ_text_".$id_champ."()' id='lien_text_".$id_champ."' title='Rendre le champ de saisie du mot de passe lisible (afficher en clair).'><img src='$gepiPath/images/icons/visible.png' width='19' height='16' alt='Mot de passe en clair' /></a>
	<a href='javascript:champ_password_".$id_champ."()' id='lien_password_".$id_champ."' style='display:none' title='Rendre le champ de saisie du mot de passe non lisible par ceux qui regardent par dessus votre épaule (masquer).'><img src='$gepiPath/images/icons/invisible.png' width='19' height='16' alt='Mot de passe masqué' /></a>
</span>

<script type='text/javascript'>
	isIE_input_password_to_text = (document.all);
	if(!isIE_input_password_to_text) {
		document.getElementById('span_liens_js_".$id_champ."').style.display='';
	}

	function champ_text_".$id_champ."() {
		if(confirm(\"Vous allez afficher votre saisie de mot de passe en clair.\\nSi quelqu'un regarde par dessus votre épaule où si vous vidéoprojetez au tableau votre écran, ce n'est pas souhaitable.\\nEtes-vous sûr de vouloir afficher le mot de passe en clair ?\")) {
			document.getElementById('".$id_champ."').setAttribute('type', 'text');
			//setTimeout(\"document.getElementById('".$id_champ."').setAttribute('type', 'text')\", 1000);

			document.getElementById('lien_text_".$id_champ."').style.display='none';
			document.getElementById('lien_password_".$id_champ."').style.display='';
		}
	}
	function champ_password_".$id_champ."() {
		document.getElementById('".$id_champ."').setAttribute('type','password');
		//setTimeout(\"document.getElementById('".$id_champ."').setAttribute('type', 'password')\", 1000);

			document.getElementById('lien_text_".$id_champ."').style.display='';
			document.getElementById('lien_password_".$id_champ."').style.display='none';
	}
</script>
<!--p>Cela fonctionne avec Firefox et Chrome, mais avec MSIE qui interdit les changements de type d'un input.</p-->";

	return $retour;
}

function html_ajout_suffixe_ou_renommer($id_nom_court, $id_nom_complet, $id_nom_matiere) {
	$retour="
	<p><strong>Ajouter un suffixe</strong> au nom court actuel de l'enseignement et au nom complet actuel&nbsp;:</p>
	<table class='boireaus'>
		<tr>
			<td class='lig1'>
				<a href=\"javascript:ajout_suffixe_nom_grp('_1', ' (groupe 1)')\">_1 et (groupe 1)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_2', ' (groupe 2)')\">_2 et (groupe 2)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_3', ' (groupe 3)')\">_3 et (groupe 3)</a></p>
			</td>
			<td class='lig-1'>
				<a href=\"javascript:ajout_suffixe_nom_grp('_g1', ' (groupe 1)')\">_g1 et (groupe 1)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_g2', ' (groupe 2)')\">_g2 et (groupe 2)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_g3', ' (groupe 3)')\">_g3 et (groupe 3)</a></p>
			</td>
			<td class='lig1'>
				<a href=\"javascript:ajout_suffixe_nom_grp('_A', ' (groupe A)')\">_A et (groupe A)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_B', ' (groupe B)')\">_B et (groupe B)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp('_C', ' (groupe C)')\">_C et (groupe C)</a>
			</td>
		</tr>
	</table>

	<br />
	<p>ou</p>

	<p><strong>Renommer</strong> l'enseignement en&nbsp;:</p>
	<table class='boireaus'>
		<tr>
			<td class='lig1'>
				<a href=\"javascript:modif_nom_grp('_1', ' (groupe 1)')\">_1 et (groupe 1)</a><br />
				<a href=\"javascript:modif_nom_grp('_2', ' (groupe 2)')\">_2 et (groupe 2)</a><br />
				<a href=\"javascript:modif_nom_grp('_3', ' (groupe 3)')\">_3 et (groupe 3)</a></p>
			</td>
			<td class='lig-1'>
				<a href=\"javascript:modif_nom_grp('_g1', ' (groupe 1)')\">_g1 et (groupe 1)</a><br />
				<a href=\"javascript:modif_nom_grp('_g2', ' (groupe 2)')\">_g2 et (groupe 2)</a><br />
				<a href=\"javascript:modif_nom_grp('_g3', ' (groupe 3)')\">_g3 et (groupe 3)</a></p>
			</td>
			<td class='lig1'>
				<a href=\"javascript:modif_nom_grp('_A', ' (groupe A)')\">_A et (groupe A)</a><br />
				<a href=\"javascript:modif_nom_grp('_B', ' (groupe B)')\">_B et (groupe B)</a><br />
				<a href=\"javascript:modif_nom_grp('_C', ' (groupe C)')\">_C et (groupe C)</a>
			</td>
		</tr>
	</table>

	<script type='text/javascript'>
		function ajout_suffixe_nom_grp(suffixe_nom_court, suffixe_nom_complet) {
			document.getElementById('$id_nom_court').value=document.getElementById('$id_nom_court').value+suffixe_nom_court;
			document.getElementById('$id_nom_complet').value=document.getElementById('$id_nom_complet').value+suffixe_nom_complet;
		}

		function modif_nom_grp(suffixe_nom_court, suffixe_nom_complet) {
			prefixe=document.getElementById('$id_nom_matiere').options[document.getElementById('$id_nom_matiere').selectedIndex].value;
			prefixe_nom_complet=document.getElementById('$id_nom_matiere').options[document.getElementById('$id_nom_matiere').selectedIndex].getAttribute('nom_matiere');
			document.getElementById('$id_nom_court').value=prefixe+suffixe_nom_court;
			document.getElementById('$id_nom_complet').value=prefixe_nom_complet+suffixe_nom_complet;
		}
	</script>\n";

	return $retour;
}

function img_calendrier_js($id_champ, $id_img) {
	global $gepiPath;
	return '<img id="'.$id_img.'" src="'.$gepiPath.'/images/icons/calendrier.gif" alt="" />
<script type="text/javascript">
	Calendar.setup({
		inputField     :    "'.$id_champ.'",     // id of the input field
		ifFormat       :    "%d/%m/%Y",      // format of the input field
		button         :    "'.$id_img.'",  // trigger for the calendar (button ID)
		align          :    "Tl",           // alignment (defaults to "Bl")
		singleClick    :    true,
		showsTime	:   false
	});
</script>';
}

/**
 * Fonction destinée à présenter une liste de liens répartis en $nbcol colonnes
 * 
 *
 * @param type $tab_txt tableau des textes
 * @param type $tab_nom_champ tableau des noms des champs checkbox
 * @param type $tab_id_champ tableau des id des champs checkbox
 * @param type $tab_valeur_champ tableau des valeurs des champs checkbox
 * @param int $nbcol Nombre de colonnes
 * @param type $extra_options Options supplémentaires
 */
function tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, $nom_js_func = "", $nom_func_tout_cocher="modif_coche", $nbcol=3) {

	// Nombre d'enregistrements à afficher
	$nombreligne=count($tab_txt);

	if(!is_int($nbcol)){
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=round($nombreligne/$nbcol);

	echo "<table width='100%' summary=\"Tableau de choix\">\n";
	echo "<tr valign='top' align='center'>\n";
	echo "<td align='left'>\n";

	$i = 0;
	$chaine_var_js="var tab_id_$nom_func_tout_cocher=new Array(";
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		//echo "<br />\n";
		//$chaine_var_js.="tab_id_".$nom_func_tout_cocher."[$i]='".$tab_id_champ[$i]."';\n";
		if($i>0) {
			$chaine_var_js.=", ";
		}
		$chaine_var_js.="'".$tab_id_champ[$i]."'";
		echo "<input type='checkbox' name='".$tab_nom_champ[$i]."' id='".$tab_id_champ[$i]."' value='".$tab_valeur_champ[$i]."' ";
		if($nom_js_func!="") {
			echo " onchange=\"$nom_js_func('$tab_id_champ[$i]')\"";
		}
		echo "/><label for='".$tab_id_champ[$i]."' id='label_".$tab_id_champ[$i]."'>".$tab_txt[$i]."</label>";
		echo "<br />\n";
		$i++;
	}
	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	if($nom_js_func!="") {
		echo js_checkbox_change_style($nom_js_func, 'label_', "y");
	}
	$chaine_var_js.=");\n";

	if($nom_func_tout_cocher!="") {
		echo "<p><a href=\"javascript:$nom_func_tout_cocher('true')\">Tout cocher</a> - <a href=\"javascript:$nom_func_tout_cocher('false')\">Tout décocher</a></p>
<script type='text/javascript'>
	$chaine_var_js
	function $nom_func_tout_cocher(mode) {
		for(i=0;i<$i;i++) {
			//if(i<5) {alert(tab_id_".$nom_func_tout_cocher."[i])}
			if(document.getElementById(tab_id_".$nom_func_tout_cocher."[i])) {
				if(mode=='true') {
					document.getElementById(tab_id_".$nom_func_tout_cocher."[i]).checked=true;";
		if($nom_js_func!="") {
			echo "
					$nom_js_func(tab_id_".$nom_func_tout_cocher."[i]);";
		}
		echo "
				}
				else {
					document.getElementById(tab_id_".$nom_func_tout_cocher."[i]).checked=false;";
		if($nom_js_func!="") {
			echo "
					$nom_js_func(tab_id_".$nom_func_tout_cocher."[i]);";
		}
		echo "
				}
			}
		}
	}
</script>";
	}
}


/**
 * Fonction destinée à présenter en tableau HTML un tableau d'élèves
 * 
 *
 * @param type $tab tableau des élèves au format
 *                  $tab[$cpt]['login']
 *                  $tab[$cpt]['nom']
 *                  $tab[$cpt]['prenom']
 *                  $tab[$cpt]['classe'][]
 */
function tableau_eleves($tab) {
	global $gepiPath;

	$retour="";
	if(count($tab)==0) {
		$retour.="<p style='color:red'>Aucun élève.</p>";
	}
	else {
		$acces_modify_eleve=acces('/eleves/modify_eleve.php', $_SESSION['statut']);

		$retour.="<table class='boireaus boireaus_alt' summary='Tableau des élèves'>
	<tr>
		<th>Login</th>
		<th>Nom</th>
		<th>Prénom</th>
		<th>Classe</th>
	</tr>";
		for($loop=0;$loop<count($tab);$loop++) {
			$retour.="
	<tr>";

			if($acces_modify_eleve) {
				$retour.="
		<th><a href='$gepiPath/eleves/modify_eleve.php?eleve_login=".$tab[$loop]['login']."' title=\"\" target='_blank'>".$tab[$loop]['login']."</a></th>";
			}
			else {
				$retour.="
		<th>".$tab[$loop]['login']."</th>";
			}

			$retour.="
		<th>".$tab[$loop]['nom']."</th>
		<th>".$tab[$loop]['prenom']."</th>
		<th>";
			if(isset($tab[$loop]['classe'])) {
				for($loop2=0;$loop2<count($tab[$loop]['classe']);$loop2++) {
					if($loop2>0) {$retour.=", ";}
					$retour.=$tab[$loop]['classe'][$loop2];
				}
			}
		$retour.="</th>
	</tr>";
		}
		$retour.="
</table>";
	}
	return $retour;
}

/**
 * Fonction destinée à retourner un champ SELECT de classe
 * 
 * @param string $login Login de l'utilisateur concerné
 * @param string $statut Statut de l'utilisateur concerné
 * @param string $nom_champ Attribut name du champ SELECT
 * @param string $id_champ Attribut id du champ SELECT
 * @param integer $id_classe_selected id de la classe pré-sélectionnée
 * @param string $onchange Chaine javascript à exécuter sur événement onchange
 */
function champ_select_classe($login, $statut, $nom_champ, $id_champ, $id_classe_selected='', $avec_option_vide="y", $onchange='') {
	$retour="";

	$tab=get_classes_from_user($login, $statut);

	if(count($tab)>0) {
		$retour.="<select";
		if($nom_champ!="") {
			$retour.=" name='$nom_champ'";
		}
		if($id_champ!="") {
			$retour.=" id='$id_champ'";
		}
		if($onchange!="") {
			$retour.=" onchange=\"$onchange\"";
		}
		$retour.=">\n";
		if($avec_option_vide=="y") {
			$retour.="<option value=''>---</option>";
		}
		foreach($tab as $id_classe => $classe) {
			$retour.="<option value='$id_classe'";
			if(($id_classe_selected!="")&&($id_classe==$id_classe_selected)) {
				$retour.=" selected='selected'";
			}
			$retour.=">".$classe."</option>\n";
		}
		$retour.="</select>\n";
	}

	return $retour;
}

/**
 * Fonction destinée à retourner un tableau de textes d'explication concernant le CDT2
 * On n'inscrit ainsi les textes d'explication qu'à un endroit... et on les appelle dans plusieurs pages
 */
function get_texte_CDT2() {
	$tab=array();

	$tab['attribut_title_CDT2_Voir_NP']="Voir toutes les Notices Privées attachées à cet enseignement.";
	$tab['attribut_title_CDT2_Banque']="Ouvrir en fenêtre popup une liste (personnalisable) de termes/formules que vous pourrez insérer d'un clic dans vos notices.
Vous pouvez par exemple vous préparer des formules comme:
- Exercice n°... page ...
- Correction de l'exercice n°... page ...
- Réviser pour un contrôle en classe portant sur...
C'est une banque dans laquelle vous ne trouverez que ce que vous y aurez mis, ni plus ni moins;).";
	$tab['attribut_title_CDT2_Archives']="Ouvrir une fenêtre popup pour consulter vos archives de cahiers de textes des années précédentes.";
	$tab['attribut_title_CDT2_Travaux_pour_ce_jour']="Voir les travaux à faire pour ce jour dans tous les enseignements associés à la classe.
Cela peut vous permettre d'éviter de placer un contrôle en classe alors qu'il y en a déjà trois de programmés pour ce jour.";

	return $tab;
}

function liste_des_prof_suivi_de_telle_classe($id_classe) {
	$retour="";

	$tab=get_tab_prof_suivi($id_classe);
	for($loop=0;$loop<count($tab);$loop++) {
		if($loop>0) {$retour.=", ";}
		$retour.=civ_nom_prenom($tab[$loop]);
	}
	return $retour;
}
?>

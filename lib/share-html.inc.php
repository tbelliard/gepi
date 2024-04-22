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

	$begin_bookings=getSettingValue('begin_bookings');
	$end_bookings=getSettingValue('end_bookings');

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

		if($ver_periode>=2) {
			echo " - <a href='add_modif_dev.php?id_conteneur=$id_racine&amp;mode_navig=retour_index' title=\"Créer une évaluation\"><img src='../images/icons/add.png' class='icone16' alt='Ajouter une évaluation' /></a>";
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
					$description_dev = preg_replace('/"/', "'", old_mysql_result($appel_dev, $j, 'description'));
					$id_dev = old_mysql_result($appel_dev, $j, 'id');
					$date_dev = old_mysql_result($appel_dev, $j, 'date');
					$date_ele_resp_dev = old_mysql_result($appel_dev, $j, 'date_ele_resp');
					echo "<li>\n";
					// 20210222
					echo "<span style='color:green;' title=\"".preg_replace('/"/', "'", $nom_dev)." (".formate_date($date_dev).")";
					if($description_dev!='') {
						echo "\n".$description_dev;
					}
					echo "\">$nom_dev</span>";
					if((isset($eff_groupe))&&($eff_groupe==0)) {
						echo " - <span title=\"Pas de saisie possible sans élève dans l'enseignement.\">Saisie</span>";
					}
					else {
						echo " - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";
					}

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

					$ts_date_dev=mysql_date_to_unix_timestamp($date_dev);
					if($ts_date_dev<$begin_bookings) {
						echo "<span style='background-color:yellow'> <img src='../images/icons/ico_attention.png' class='icone20' title=\"ATTENTION : La date de l'évaluation (".formate_date($date_dev).") est antérieure au début de l'année (".strftime("%d/%m/%Y", $begin_bookings)."). Cela peut perturber les saisies pour des élèves arrivés en début d'année.\" /> </span>";
					}
					if($ts_date_dev>$end_bookings) {
						echo "<span style='background-color:yellow'> <img src='../images/icons/ico_attention.png' class='icone20' title=\"ATTENTION : La date de l'évaluation (".formate_date($date_dev).") est postérieure à la fin de l'année (".strftime("%d/%m/%Y", $begin_bookings).").\" /> </span>";
					}

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
					echo "<span id='span_visibilite_$id_dev'>";
					if($display_parents==1) {
						echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=$id_dev&amp;mode=change_visibilite_dev&amp;visible=n".add_token_in_url()."' onclick=\"change_visibilite_dev($id_dev,'n');return false;\"><img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.

Cliquez pour ne pas faire apparaître cette note sur le relevé de notes.' alt='Evaluation visible sur le relevé de notes' /></a>";
					}
					else {
						echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=$id_dev&amp;mode=change_visibilite_dev&amp;visible=y".add_token_in_url()."' onclick=\"change_visibilite_dev($id_dev,'y');return false;\"><img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes.
					
Cliquez pour faire apparaître cette note sur le relevé de notes.' alt='Evaluation non visible sur le relevé de notes' /></a>\n";
					}
					echo "</span>";
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
							$description_dev = preg_replace('/"/', "'", old_mysql_result($appel_dev, $j, 'description'));
							$id_dev = old_mysql_result($appel_dev, $j, 'id');
							$date_dev = old_mysql_result($appel_dev, $j, 'date');
							$date_ele_resp_dev = old_mysql_result($appel_dev, $j, 'date_ele_resp');
							echo "<li>\n";
							// 20210222
							echo "<font color='green' title=\"".preg_replace('/"/', "'", $nom_dev)." (".formate_date($date_dev).")";
							if($description_dev!='') {
								echo "\n".$description_dev;
							}
							echo "\">$nom_dev</font> - <a href='saisie_notes.php?id_conteneur=$id_cont&amp;id_devoir=$id_dev'>Saisie</a>";

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
							/*
							if($display_parents==1) {echo "<img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.' alt='Evaluation visible sur le relevé de notes' />";}
							else {echo " <img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes' alt='Evaluation non visible sur le relevé de notes' />\n";}
							*/
							echo "<span id='span_visibilite_$id_dev'>";
							if($display_parents==1) {
								echo "<a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=$id_dev&amp;mode=change_visibilite_dev&amp;visible=n".add_token_in_url()."' onclick=\"change_visibilite_dev($id_dev,'n');return false;\"><img src='../images/icons/visible.png' width='19' height='16' title='Evaluation du ".formate_date($date_dev)." visible sur le relevé de notes.
Visible à compter du ".formate_date($date_ele_resp_dev)." pour les parents et élèves.

Cliquez pour ne pas faire apparaître cette note sur le relevé de notes.' alt='Evaluation visible sur le relevé de notes' /></a>";
							}
							else {
								echo " <a href='".$_SERVER['PHP_SELF']."?id_groupe=$id_groupe&amp;id_racine=$id_racine&amp;id_dev=$id_dev&amp;mode=change_visibilite_dev&amp;visible=y".add_token_in_url()."' onclick=\"change_visibilite_dev($id_dev,'y');return false;\"><img src='../images/icons/invisible.png' width='19' height='16' title='Evaluation non visible sur le relevé de notes.
					
Cliquez pour faire apparaître cette note sur le relevé de notes.' alt='Evaluation non visible sur le relevé de notes' /></a>\n";
							}
							echo "</span>";
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
  $sql = "(SELECT DISTINCT c.id, c.classe
	FROM classes c, j_groupes_classes jgc, ct_entry ct, j_scol_classes jsc
	WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jsc.id_classe=jgc.id_classe
	  AND jsc.login='".$_SESSION ['login']."') 
	UNION 
	(SELECT DISTINCT c.id, c.classe
	FROM classes c, j_groupes_classes jgc, ct_devoirs_entry ct, j_scol_classes jsc
	WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jsc.id_classe=jgc.id_classe
	  AND jsc.login='".$_SESSION ['login']."')) 
	ORDER BY classe ;";
  } else if (isset($_SESSION['statut']) && ($_SESSION['statut']=='cpe'
		  && getSettingValue('GepiAccesCdtCpeRestreint')=="yes")) {
	$sql = "(SELECT DISTINCT c.id, c.classe
	  FROM classes c, j_groupes_classes jgc, ct_entry ct, j_eleves_cpe jec,j_eleves_classes jecl
	  WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jec.cpe_login = '".$_SESSION ['login']."'
	  AND jec.e_login = jecl.login
	  AND jecl.id_classe = jgc.id_classe) 
	  UNION (SELECT DISTINCT c.id, c.classe
	  FROM classes c, j_groupes_classes jgc, ct_devoirs_entry ct, j_eleves_cpe jec,j_eleves_classes jecl
	  WHERE (c.id = jgc.id_classe
	  AND jgc.id_groupe = ct.id_groupe
	  AND jec.cpe_login = '".$_SESSION ['login']."'
	  AND jec.e_login = jecl.login
	  AND jecl.id_classe = jgc.id_classe))
	  ORDER BY classe ;";
  } else {
	if(isset($_SESSION['statut']) && ($_SESSION['statut']=='professeur')&&(!getSettingAOui('GepiAccesCDTToutesClasses'))) {
		$sql = "(SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_entry ct, j_groupes_professeurs jgp
		  WHERE (c.id = jgc.id_classe
		  AND jgp.id_groupe = ct.id_groupe
		  AND jgc.id_groupe = ct.id_groupe
		  AND jgp.login='".$_SESSION['login']."')
		  UNION 
		  (SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_devoirs_entry ct, j_groupes_professeurs jgp
		  WHERE (c.id = jgc.id_classe
		  AND jgp.id_groupe = ct.id_groupe
		  AND jgc.id_groupe = ct.id_groupe
		  AND jgp.login='".$_SESSION['login']."'))
		  ORDER BY classe";
	}
	else {
		$sql = "(SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_entry ct
		  WHERE (c.id = jgc.id_classe
		  AND jgc.id_groupe = ct.id_groupe))
		  UNION 
		  (SELECT DISTINCT c.id, c.classe
		  FROM classes c, j_groupes_classes jgc, ct_devoirs_entry ct
		  WHERE (c.id = jgc.id_classe
		  AND jgc.id_groupe = ct.id_groupe))
		  ORDER BY classe";
	}
  }

  //GepiAccesCdtCpeRestreint

	$res = sql_query($sql);

	if ($res) {
		$tab_deja=array();
		for ($i = 0; ($row = sql_row($res, $i)); $i++) {
			if(!in_array($row[0], $tab_deja)) {
				$selected = ($row[0] == $current) ? "selected" : "";
				$link2 = "$link?year=$year&amp;month=$month&amp;day=$day&amp;id_classe=$row[0]" . $aff_get_rne;
				$out_html .= "<option $selected value=\"$link2\">" . htmlspecialchars($row[1]);
				$tab_deja[]=$row[0];
			}
		}
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
		<input type=\"submit\" value=\"OK\" />
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

		$id_classe=get_id_classe_ele_d_apres_date($id_ref, time());
		//echo "id_classe=$id_classe<br />";
		if($id_classe=="") {
			$id_classe=get_id_classe_derniere_classe_ele($id_ref);
		}

		if($id_classe=="") {
			$sql = "select DISTINCT g.id, g.name, g.description from j_eleves_groupes jec, groupes g, ct_entry ct where (" .
				"jec.login='".$id_ref."' and " .
				"g.id = jec.id_groupe and " .
				"jec.id_groupe = ct.id_groupe" .
				") order by g.name";
		}
		else {
			$sql = "select DISTINCT g.id, g.name, g.description from j_eleves_classes jec, j_eleves_groupes jeg, groupes g, ct_entry ct where (" .
				"jec.id_classe='$id_classe' and ".
				"jec.login=jeg.login and ".
				"jec.periode=jeg.periode and ".
				"jeg.login='".$id_ref."' and " .
				"g.id = jeg.id_groupe and " .
				"jeg.id_groupe = ct.id_groupe" .
				") order by g.name";
		}
		// DEBUG 20160303
		//echo "$sql<br />";
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
  global $envoi_mail;
  global $gepiPath;

  // documents joints
  $html = '';
  $architecture="/documents/cl_dev";
  if ($type_notice == "t") {
      //$sql = "SELECT titre, emplacement, visible_eleve_parent  FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY 'titre'";
      $sql = "SELECT titre, emplacement, visible_eleve_parent  FROM ct_devoirs_documents WHERE id_ct_devoir='$id_ct' ORDER BY CONCAT('a', titre) ASC;";
  } else if ($type_notice == "c") {
      //$sql = "SELECT titre, emplacement, visible_eleve_parent FROM ct_documents WHERE id_ct='$id_ct' ORDER BY 'titre'";
      $sql = "SELECT titre, emplacement, visible_eleve_parent FROM ct_documents WHERE id_ct='$id_ct' ORDER BY CONCAT('a', titre) ASC;";
  }

  if(isset($_SESSION['statut'])) {
    $acces_voir_pj=acces('/cahier_texte_2/voir_pj.php', $_SESSION['statut']);
  }
  else {
    $acces_voir_pj=false;
  }

  // On n'a pas de docs joints sur les notices privées.
  if(isset($sql)) {
	  $res = sql_query($sql);
	    if (($res) and (sql_count($res)!=0)) {
		$html .= "<span class='petit'>Document(s) joint(s):</span>";
		if($acces_voir_pj) {
			$html .= "<a href='".$gepiPath."/cahier_texte_2/voir_pj.php?id_ct=".$id_ct."&type_notice=".$type_notice."' title='Visionner les documents joints' target='_blank'><img src='../images/icons/chercher.png' class='icone16' /></a>";
		}
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

		          if((isset($envoi_mail))&&($envoi_mail=="y")&&(getSettingValue("url_racine_gepi")!="")) {
		              $emplacement=preg_replace("#^\.\./#", getSettingValue("url_racine_gepi")."/", $emplacement);
		          }

		        // Ouverture dans une autre fenêtre conservée parce que si le fichier est un PDF, un TXT, un HTML ou tout autre document susceptible de s'ouvrir dans le navigateur, on risque de refermer sa session en croyant juste refermer le document.
		        // alternative, utiliser un javascript
		          //$html .= "<li style=\"padding: 0px; margin: 0px;font-size: 80%;\"><a onclick=\"window.open(this.href, '_blank'); return FALSE;\" href=\"$emplacement\">$titre</a></li>";
		          $html .= "<li style=\"padding: 0px; margin: 0px;font-size: 80%;\"><a href=\"$emplacement\" target='_blank'>$titre</a></li>";
		    }
		}
		$html .= "</ul>";
	     }
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
 * @param type $tab_extra Tableau supplémentaire pour par exemple des liens onclick
 */
function tab_liste($tab_txt,$tab_lien,$nbcol,$extra_options = NULL,$tab_extra=NULL) {

	// Nombre d'enregistrements à afficher
	$nombreligne=count($tab_txt);

	if((!is_int($nbcol))||($nbcol<1)) {
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=max(round($nombreligne/$nbcol),1);

	$percent=floor(100/$nbcol);

	echo "<table width='100%' summary=\"Tableau de choix\">\n";
	echo "<tr style='text-align:center; vertical-align: top;'>\n";
	echo "<td style='text-align:left' width='$percent%'>\n";

	$i = 0;
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td style='text-align:left' width='$percent%'>\n";
		}

		//echo "<br />\n";
		echo "<a href='".$tab_lien[$i]."'";
		if ($extra_options) echo ' '.$extra_options;
		if(isset($tab_extra[$i])) {
			echo $tab_extra[$i];
		}
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
	//echo "$sql<br />";
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
function affiche_actions_compte($login, $target="", $mode="") {
	global $gepiPath;

	$retour="";

	$user=get_infos_from_login_utilisateur($login);

	if(isset($user['etat'])) {
		$retour.="<p>\n";
		if ($user['etat'] == "actif") {
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=desactiver&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			if($mode=="ajax") {
				$retour.=" onclick=\"action_ajax_security_panel('desactiver');return false;\"";
			}
			$retour.=">Désactiver le compte</a>";
		} else {
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=activer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			if($mode=="ajax") {
				$retour.=" onclick=\"action_ajax_security_panel('activer');return false;\"";
			}
			$retour.=">Réactiver le compte</a>";
		}
		$retour.="<br />\n";
		//==================================================
		if ($user['observation_securite'] == 0) {
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=observer&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			if($mode=="ajax") {
				$retour.=" onclick=\"action_ajax_security_panel('observer');return false;\"";
			}
			$retour.=">Placer en observation</a>";
		} else {
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=stop_observation&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			if($mode=="ajax") {
				$retour.=" onclick=\"action_ajax_security_panel('stop_observation');return false;\"";
			}
			$retour.=">Retirer l'observation</a>";
		}
		//=================================================
		if($user['niveau_alerte']>0) {
			$retour.="<br />\n";
			$retour.="Score cumulé&nbsp;: ".$user['niveau_alerte'];
			$retour.="<br />\n";
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?action=reinit_cumul&amp;afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			if($mode=="ajax") {
				$retour.=" onclick=\"action_ajax_security_panel('reinit_cumul');return false;\"";
			}
			$retour.=">Réinitialiser cumul</a>";

			$retour.="<br />\n";
			$retour.="<a style='padding: 2px;' href='$gepiPath/gestion/security_panel.php?afficher_les_alertes_d_un_compte=y&amp;user_login=".$login;
			$retour.=add_token_in_url()."'";
			if($target!="") {
				$retour.=" target='$target'";
			}
			$retour.=">Voir les alertes</a>";
		}
		$retour.="</p>\n";

		if($mode=="ajax") {
			$retour.="
	<script type='text/javascript'>
		// <![CDATA[
		function action_ajax_security_panel(action) {
			if(action=='desactiver') {
				new Ajax.Updater($('div_affiche_actions_compte'),'$gepiPath/gestion/security_panel.php?mode=ajax&action=desactiver&afficher_les_alertes_d_un_compte=y&user_login=".$login.add_token_in_url(false)."',{method: 'get'});
			}
			else if(action=='activer') {
				new Ajax.Updater($('div_affiche_actions_compte'),'$gepiPath/gestion/security_panel.php?mode=ajax&action=activer&afficher_les_alertes_d_un_compte=y&user_login=".$login.add_token_in_url(false)."',{method: 'get'});
			}
			else if(action=='observer') {
				new Ajax.Updater($('div_affiche_actions_compte'),'$gepiPath/gestion/security_panel.php?mode=ajax&action=observer&afficher_les_alertes_d_un_compte=y&user_login=".$login.add_token_in_url(false)."',{method: 'get'});
			}
			else if(action=='stop_observation') {
				new Ajax.Updater($('div_affiche_actions_compte'),'$gepiPath/gestion/security_panel.php?mode=ajax&action=stop_observation&afficher_les_alertes_d_un_compte=y&user_login=".$login.add_token_in_url(false)."',{method: 'get'});
			}
			else if(action=='reinit_cumul') {
				new Ajax.Updater($('div_affiche_actions_compte'),'$gepiPath/gestion/security_panel.php?mode=ajax&action=reinit_cumul&afficher_les_alertes_d_un_compte=y&user_login=".$login.add_token_in_url(false)."',{method: 'get'});
			}
		}
		//]]>
	</script>\n";
		}

	}

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

	if(isset($user['etat'])) {
		$retour.="<p>\n";

		$retour.="<a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html";
		$retour.=add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank' title=\"Une fiche bienvenue sera générée avec les informations login/mot de passe.\">Réinitialiser le mot de passe</a><br />";

		if ($user['statut'] == "responsable") {
			$retour.="<a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html&amp;affiche_adresse_resp=y";
			$retour.=add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank' title=\"Une fiche bienvenue sera générée avec les informations login/mot de passe.\n\nLa fiche bienvenue comportera un cadre Adresse du responsable pour un envoi courrier papier.\">Idem avec adresse</a>";

			if((isset($user['mel']))&&(check_mail($user['mel']))) {
				$retour.="<br /><a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html";
				$retour.="&envoi_mail=y".add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank' title=\"Une fiche bienvenue sera générée avec les informations login/mot de passe.\nCette fiche sera affichée et envoyée par mail.\">Réinitialiser le mot de passe<br />et envoyer par mail à<br />".$user['mel']."</a><br />";
			}
		}
		else {
			if((isset($user['email']))&&(check_mail($user['email']))) {
				$retour.="<br /><a style='padding: 2px;' href='$gepiPath/utilisateurs/reset_passwords.php?user_login=".$login."&amp;user_status=".$user['statut']."&amp;mode=html";
				$retour.="&envoi_mail=y".add_token_in_url()."' onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir effectuer cette opération ?\\n Celle-ci est irréversible, et réinitialisera le mot de passe de l\'utilisateur avec un mot de passe alpha-numérique généré aléatoirement.\\n En cliquant sur OK, vous lancerez la procédure, qui génèrera une page contenant la fiche-bienvenue à imprimer immédiatement pour distribution à l\'utilisateur concerné.')\" target='_blank' title=\"Une fiche bienvenue sera générée avec les informations login/mot de passe.\nCette fiche sera affichée et envoyée par mail.\">Réinitialiser le mot de passe<br />et envoyer par mail à<br />".$user['email']."</a><br />";
			}
		}
		$retour.="</p>\n";
	}

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
function js_checkbox_change_style($nom_js_func='checkbox_change', $prefixe_texte='texte_', $avec_balise_script="n", $perc_opacity=1, $background_color='', $color='') {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}
	$retour.="
	function $nom_js_func(id) {
		//alert(id);
		if(document.getElementById(id)) {
			if(document.getElementById('$prefixe_texte'+id)) {
				//alert('$prefixe_texte'+id);
				if(document.getElementById(id).checked) {
					document.getElementById('$prefixe_texte'+id).style.fontWeight='bold';
					document.getElementById('$prefixe_texte'+id).style.opacity=1;
					document.getElementById('$prefixe_texte'+id).style.backgroundColor='$background_color';
					document.getElementById('$prefixe_texte'+id).style.color='$color';
				}
				else {
					document.getElementById('$prefixe_texte'+id).style.fontWeight='normal';
					document.getElementById('$prefixe_texte'+id).style.opacity=$perc_opacity;
					document.getElementById('$prefixe_texte'+id).style.backgroundColor='';
					document.getElementById('$prefixe_texte'+id).style.color='';
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

	for($i=0;$i<count($tableau_type_login);$i++) {
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
	if(file_exists($url_img)) {
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

// 20210928
function insere_lien_insertion_lien_document_dans_ckeditor($titre_doc, $url_doc) {
	return "<div style='float:right; width:18px;'><a href=\"javascript:insere_lien_document_dans_ckeditor('".preg_replace("/'/", " ", $titre_doc)."', '".$url_doc."')\" title='Insérer un lien vers le document'><img src='../images/lien_up.png' width='18' height='18' alt='Insérer un lien vers le document' /></a></div>";
}

/** fonction alertant sur la configuration de suhosin
 *
 * @return string Chaine de texte HTML 
 */
function alerte_config_suhosin() {
	$retour="<p class='bold' style='color:red'>Limitations à la transmission de variables</p>\n";

	$suhosin_post_max_totalname_length=ini_get('suhosin.post.max_totalname_length');
	if($suhosin_post_max_totalname_length!='') {
		$retour.="<p>Le module suhosin est activé.<br />\nUn paramétrage trop restrictif de ce module peut perturber le fonctionnement de Gepi, particulièrement dans les pages comportant de nombreux champs de formulaire (<i>comme par exemple dans la page de saisie des appréciations par les professeurs</i>)</p>\n";
		$retour.="<p>La page d'extraction des moyennes permettant de modifier/corriger des valeurs propose un très grand nombre de champs.<br />Le module suhosin risque de poser des problèmes.</p>";

		$retour="<p class='bold'>Configuration suhosin&nbsp;:</p>\n";
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

	$max_input_vars=ini_get('max_input_vars');
	if(($max_input_vars!="")&&($max_input_vars>0)) {
		$retour.="<p class='bold'>Limitation propre à PHP&nbsp;:</p><p>La variable PHP 'max_input_vars' est limitée à $max_input_vars.<br />Si le nombre de variables transmises dépasse cette valeur, Gepi peut être perturbé.<br />Tentez de soumettre moins de valeurs ou augmentez la valeur de 'max_input_vars' dans le fichier php.ini.</p>\n";
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
	$sql="SELECT DISTINCT nom, prenom FROM eleves e, j_eleves_groupes jeg WHERE jeg.login=e.login AND jeg.id_groupe='$id_groupe' ORDER BY nom, prenom;";
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
			//if($l>0) {$graphe_serie.="|";}
			if($l>0) {$graphe_serie.=";";}
			$graphe_serie.=$tab_graph_note[$l];
		}
	}

	$texte="<div class='center'><object data=\"../lib/graphe_svg.php?";
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
	$texte.="\"";
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

function liste_checkbox_utilisateurs($tab_statuts, $tab_user_preselectionnes=array(), $nom_champ='login_user', $nom_func_js_tout_cocher_decocher='cocher_decocher', $avec_titre_statut="y", $sql="", $nom_func_js_checkbox_change="checkbox_change", $avec_changement='n') {
	$retour="";

	if($sql=="") {
		$sql="SELECT login, civilite, nom, prenom, statut FROM utilisateurs WHERE (";
		for($loop=0;$loop<count($tab_statuts);$loop++) {
			if($loop>0) {
				$sql.=" OR ";
			}
			$sql.="statut='".$tab_statuts[$loop]."'";
		}
		$sql.=") AND etat='actif' ORDER BY statut, nom, prenom, login;";
	}
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table style ='width:100%;'>\n";
		$retour.="<caption class='invisible'>Tableau de choix des utilisateurs</caption>\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' ><p>\n";

		$cpt=0;
		$statut_prec="";
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</p></td>\n";
				$retour.="<td style='text-align:left' ><p>\n";
			}

			if($lig->statut!=$statut_prec) {
				if($avec_titre_statut=="y") {
					$retour.="<strong>".ucfirst($lig->statut)."</strong><br />\n";
				}
				$statut_prec=$lig->statut;
			}

			$retour.="<input type='checkbox' name='".$nom_champ."[]' id='".$nom_champ."_$cpt' value='$lig->login' ";
			$retour.="onchange=\"";
			if($avec_changement=='y') {
				$retour.="changement();";
			}
			$retour.=$nom_func_js_checkbox_change."('".$nom_champ."_$cpt')\" ";
			if(in_array_i($lig->login, $tab_user_preselectionnes)) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->login\"><span id='texte_".$nom_champ."_$cpt'$temp_style>$lig->civilite ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, 'majf2')."</span></label><br />\n";

			$cpt++;
		}
		$retour.="</p></td>\n";
		$retour.="</tr>\n";
		$retour.="</table>\n";

		if($nom_func_js_tout_cocher_decocher!='') {
			$retour.="<script type='text/javascript'>
function $nom_func_js_tout_cocher_decocher(mode) {
	for (var k=0;k<$cpt;k++) {
		if(document.getElementById('".$nom_champ."_'+k)){
			document.getElementById('".$nom_champ."_'+k).checked=mode;
			".$nom_func_js_checkbox_change."('".$nom_champ."_'+k);
		}
	}
}
</script>\n";
		}
	}

	return $retour;
}

function liste_checkbox_matieres($tab_matieres_preselectionnees=array(), $nom_champ='matiere', $nom_func_js_tout_cocher_decocher='cocher_decocher', $matieres_enseignees="y", $order_by="", $avec_changement='n') {

	$retour="";

	if($order_by=="") {
		$order_by="m.priority, m.matiere, m.nom_complet";
	}

	if($matieres_enseignees=="y") {
		$sql="SELECT DISTINCT m.* FROM matieres m, j_groupes_matieres jgm WHERE m.matiere=jgm.id_matiere ";
	}
	else {
		$sql="SELECT * FROM matieres m ";
	}
	$sql.=" ORDER BY $order_by;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table width='100%' summary=\"Tableau de choix des matieres\">\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' >\n";

		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td style='text-align:left' >\n";
			}

			$retour.="<input type='checkbox' name='".$nom_champ."[]' id='".$nom_champ."_$cpt' value='$lig->matiere' ";
			$retour.="onchange=\"";
			if($avec_changement=='y') {
				$retour.="changement();";
			}
			$retour.="checkbox_change('".$nom_champ."_$cpt')\" ";
			if(in_array($lig->matiere, $tab_matieres_preselectionnees)) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->matiere\"><span id='texte_".$nom_champ."_$cpt'$temp_style>".$lig->nom_complet." (<em>$lig->matiere</em>)</span></label><br />\n";

			$cpt++;
		}
		$retour.="</td>\n";
		$retour.="</tr>\n";
		$retour.="</table>\n";

		if($nom_func_js_tout_cocher_decocher!='') {
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

function html_ajout_suffixe_ou_renommer($id_nom_court, $id_nom_complet, $id_nom_matiere, $suffixe_per="") {
	$retour="
	<p><strong>Ajouter un suffixe</strong> au nom court actuel de l'enseignement et au nom complet actuel&nbsp;:</p>
	<table class='boireaus'>
		<tr>
			<td class='lig1'>
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_1', ' (groupe 1)')\">_1 et (groupe 1)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_2', ' (groupe 2)')\">_2 et (groupe 2)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_3', ' (groupe 3)')\">_3 et (groupe 3)</a></p>
			</td>
			<td class='lig-1'>
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_g1', ' (groupe 1)')\">_g1 et (groupe 1)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_g2', ' (groupe 2)')\">_g2 et (groupe 2)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_g3', ' (groupe 3)')\">_g3 et (groupe 3)</a></p>
			</td>
			<td class='lig1'>
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_A', ' (groupe A)')\">_A et (groupe A)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_B', ' (groupe B)')\">_B et (groupe B)</a><br />
				<a href=\"javascript:ajout_suffixe_nom_grp".$suffixe_per."('_C', ' (groupe C)')\">_C et (groupe C)</a>
			</td>
		</tr>
	</table>

	<br />
	<p>ou</p>

	<p><strong>Renommer</strong> l'enseignement en&nbsp;:</p>
	<table class='boireaus'>
		<tr>
			<td class='lig1'>
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_1', ' (groupe 1)')\">_1 et (groupe 1)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_2', ' (groupe 2)')\">_2 et (groupe 2)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_3', ' (groupe 3)')\">_3 et (groupe 3)</a></p>
			</td>
			<td class='lig-1'>
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_g1', ' (groupe 1)')\">_g1 et (groupe 1)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_g2', ' (groupe 2)')\">_g2 et (groupe 2)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_g3', ' (groupe 3)')\">_g3 et (groupe 3)</a></p>
			</td>
			<td class='lig1'>
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_A', ' (groupe A)')\">_A et (groupe A)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_B', ' (groupe B)')\">_B et (groupe B)</a><br />
				<a href=\"javascript:modif_nom_grp".$suffixe_per."('_C', ' (groupe C)')\">_C et (groupe C)</a>
			</td>
		</tr>
	</table>

	<script type='text/javascript'>
		function ajout_suffixe_nom_grp".$suffixe_per."(suffixe_nom_court, suffixe_nom_complet) {
			document.getElementById('$id_nom_court').value=document.getElementById('$id_nom_court').value+suffixe_nom_court;
			document.getElementById('$id_nom_complet').value=document.getElementById('$id_nom_complet').value+suffixe_nom_complet;
		}

		function modif_nom_grp".$suffixe_per."(suffixe_nom_court, suffixe_nom_complet) {
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
 * @param type $tab_valeurs_preselectionnees tableau des valeurs présélectionnées
 * @param type $extra_options Options supplémentaires
 */
function tab_liste_checkbox($tab_txt, $tab_nom_champ, $tab_id_champ, $tab_valeur_champ, $nom_js_func = "", $nom_func_tout_cocher="modif_coche", $nbcol=3, $tab_valeurs_preselectionnees=array()) {

	// Nombre d'enregistrements à afficher
	$nombreligne=count($tab_txt);

	if(!is_int($nbcol)){
		$nbcol=3;
	}

	// Nombre de lignes dans chaque colonne:
	$nb_class_par_colonne=max(round($nombreligne/$nbcol),1);

	echo "<table width='100%' summary=\"Tableau de choix\">\n";
	echo "<tr style='text-align:center; vertical-align: top;'>\n";
	echo "<td style='text-align:left' >\n";

	$i = 0;
	$chaine_var_js="var tab_id_$nom_func_tout_cocher=new Array(";
	while ($i < $nombreligne){

		if(($i>0)&&(round($i/$nb_class_par_colonne)==$i/$nb_class_par_colonne)){
			echo "</td>\n";
			echo "<td style='text-align:left' >\n";
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
		if(in_array($tab_valeur_champ[$i] , $tab_valeurs_preselectionnees)) {
			echo " checked";
		}
		echo " /><label for='".$tab_id_champ[$i]."' id='label_".$tab_id_champ[$i]."'";
		if(in_array($tab_valeur_champ[$i] , $tab_valeurs_preselectionnees)) {
			echo " style='font-weight:bold;'";
		}
		echo ">".$tab_txt[$i]."</label>";
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

	$ajout="";
	$cdt2_car_spec_liste=getPref($_SESSION['login'], 'cdt2_car_spec_liste', '');
	if($cdt2_car_spec_liste=="") {
		$ajout="\n\nSi le bouton est sans effet, il se peut qu'il faille forcer la mise à jour de la page.\nAvec Firefox ou Chrome, vous pouvez le faire en pressant CTRL+SHIFT+R.";
	}
	$tab['attribut_title_CDT2_CarSpec']="Ouvrir une fenêtre popup pour insérer des caractères spéciaux de votre choix.".$ajout;

	$tab['attribut_title_CDT2_PJ']="Voir les documents joints aux cahiers de textes.";

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

function choix_heure($champ_heure,$div_choix_heure, $mode_retour="echo") {
	global $tabdiv_infobulle, $gepiPath;

	$retour="";

	$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
	$res_abs_cren=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_abs_cren)>0) {
		if($mode_retour=="echo") {
			echo " <a href='#' onclick=\"afficher_div('$div_choix_heure','y',10,-40); return false;\" title=\"Choisir un créneau dans l'emploi du temps\"> <img src=\"$gepiPath/images/icons/clock.png\" class='icone16\"\" alt=\"Choix d'une heure\" /> </a>";
		}
		else {
			$retour=" <a href='#' onclick=\"afficher_div('$div_choix_heure','y',10,-40); return false;\" title=\"Choisir un créneau dans l'emploi du temps\"> <img src=\"$gepiPath/images/icons/clock.png\" class='icone16\"\" alt=\"Choix d'une heure\" /> </a>";
		}

		$texte="<table class='boireaus boireaus_alt' style='margin: auto;border:1px;'><caption class='invisible'>Choix d'une heure</caption>\n";
		while($lig_ac=mysqli_fetch_object($res_abs_cren)) {
			$td_style="";
			$tmp_bgcolor="";
			if($lig_ac->type_creneaux=='cours') {
				$td_style="";
				//$td_style=" style='background-color: lightgreen;'";
				//$tmp_bgcolor="lightgreen";
				$tmp_bgcolor="";
			}
			elseif($lig_ac->type_creneaux=='pause') {
				$td_style=" style='background-color: lightgrey;'";
				$tmp_bgcolor="lightgrey";
			}
			elseif($lig_ac->type_creneaux=='repas') {
				$td_style=" style='background-color: lightgrey;'";
				$tmp_bgcolor="lightgrey";
			}

			if(!is_array($champ_heure)) {
				$texte.="<tr class='white_hover'$td_style onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor='$tmp_bgcolor'\">
	<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\">".$lig_ac->nom_definie_periode."</a></td>
	<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\" style='text-decoration: none; color: black;'>".$lig_ac->heuredebut_definie_periode."</a></td>
	<td><a href='#' onclick=\"document.getElementById('$champ_heure').value='$lig_ac->nom_definie_periode';cacher_div('$div_choix_heure');changement();return false;\" style='text-decoration: none; color: black;'>".$lig_ac->heurefin_definie_periode."</a></td>
</tr>\n";
			}
			else {
				$h_deb=preg_replace("/:00$/", "", $lig_ac->heuredebut_definie_periode);
				$h_fin=preg_replace("/:00$/", "", $lig_ac->heurefin_definie_periode);
				$texte.="<tr class='white_hover'$td_style onmouseover=\"this.style.backgroundColor='white'\" onmouseout=\"this.style.backgroundColor='$tmp_bgcolor'\">
	<td><a href='#' onclick=\"if(document.getElementById('".$champ_heure[0]."')) {document.getElementById('".$champ_heure[0]."').value='".$h_deb."';};if(document.getElementById('".$champ_heure[1]."')) {document.getElementById('".$champ_heure[1]."').value='".$h_fin."';};cacher_div('$div_choix_heure');changement();return false;\">".$lig_ac->nom_definie_periode."</a></td>
	<td><a href='#' onclick=\"if(document.getElementById('".$champ_heure[0]."')) {document.getElementById('".$champ_heure[0]."').value='".$h_deb."';};if(document.getElementById('".$champ_heure[1]."')) {document.getElementById('".$champ_heure[1]."').value='".$h_fin."';};cacher_div('$div_choix_heure');changement();return false;\">".$lig_ac->heuredebut_definie_periode."</a></td>
	<td><a href='#' onclick=\"if(document.getElementById('".$champ_heure[0]."')) {document.getElementById('".$champ_heure[0]."').value='".$h_deb."';};if(document.getElementById('".$champ_heure[1]."')) {document.getElementById('".$champ_heure[1]."').value='".$h_fin."';};cacher_div('$div_choix_heure');changement();return false;\">".$lig_ac->heurefin_definie_periode."</a></td>
</tr>\n";
			}
		}
		$texte.="</table>\n";

		$tabdiv_infobulle[]=creer_div_infobulle("$div_choix_heure","Choix d'une heure","",$texte,"",13,0,'y','y','n','n');
	}

	if($mode_retour!="echo") {
		return $retour;
	}

}

function affiche_tableau_pp($tab_classe=array()) {
	if(count($tab_classe)==0) {
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

		if(($_SESSION['statut']=='scolarite')&&(getSettingValue("GepiAccesVisuToutesEquipScol") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}
		if(($_SESSION['statut']=='cpe')&&(getSettingValue("GepiAccesVisuToutesEquipCpe") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}
		if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesVisuToutesEquipProf") =="yes")){
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}

		if(($_SESSION['statut']=='autre')&&(acces('/groupes/visu_profs_class.php', 'autre'))) {
			$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
		}

		$result_classes=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_classes = mysqli_num_rows($result_classes);
		$tab_classe=array();
		if(mysqli_num_rows($result_classes)>0){
			$nb_classes=mysqli_num_rows($result_classes);
			while($lig_class=mysqli_fetch_object($result_classes)){
				$tab_classe[$lig_class->id]=$lig_class->classe;
			}
		}
	}

	$retour="
	<table class='boireaus boireaus_alt'>
		<tr>
			<th>Classe</th>
			<th>
				".ucfirst(getSettingValue('gepi_prof_suivi'))."
			</th>
		</tr>";
	$tab_pp=get_tab_prof_suivi();
	foreach($tab_classe as $current_id_classe => $current_classe) {
		$retour.="
		<tr>
			<td>$current_classe</td>
			<td>";
		if(isset($tab_pp[$current_id_classe])) {
			for($loop=0;$loop<count($tab_pp[$current_id_classe]);$loop++) {
				if($loop>0) {$retour.="<br />";}
				$designation_user=civ_nom_prenom($tab_pp[$current_id_classe][$loop]);
				$retour.="<div style='float:right; width:16px'>".affiche_lien_mailto_si_mail_valide($tab_pp[$current_id_classe][$loop], $designation_user)."</div>";
				$retour.=$designation_user;
			}
		}
			$retour.="</td>
		</tr>";
	}
	$retour.="
	</table>";

	return $retour;
}


function necessaire_bull_simple() {
	echo "<div id='div_bull_simp' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";
	
		echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
			echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
			echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
			echo "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />\n";
			echo "</a>\n";
			echo "</div>\n";
	
			echo "<div id='titre_entete_bull_simp'></div>\n";
		echo "</div>\n";
		
		echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
		echo "</div>\n";
	
	echo "</div>\n";

	echo "<script type='text/javascript'>
	// <![CDATA[
	function affiche_bull_simp(login_eleve,id_classe,num_per1,num_per2) {
		//alert(login_eleve);
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifié de '+login_eleve+' période '+num_per1+' à '+num_per2;
		new Ajax.Updater($('corps_bull_simp'),'../saisie/ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}
	//]]>
</script>\n";
}

function affiche_remplacements_en_attente_de_reponse($login_user) {
	global$gepiPath;

	$retour="";

	$tab=get_tab_propositions_remplacements($login_user, "en_attente");
	if(count($tab)>0) {
		$retour="<div class='postit' style='text-align:center;'>Vous avez ".count($tab)." remplacement(s) ponctuel(s) proposé(s) qui attendent une réponse de votre part.<br />Une réponse (<em>dans l'idéal, positive</em>) serait plus que bienvenue pour soulager la permanence et combler l'insatiable soif d'apprentissage des élèves;).<br /><a href='$gepiPath/mod_abs_prof/index.php'>Consulter les propositions pour les accepter ou les rejeter</a></div>";
	}

	return $retour;
}

function affiche_remplacements_confirmes($login_user) {
	global$gepiPath;

	$retour="";

	$tab=get_tab_propositions_remplacements($login_user, "futures_validees");
	for($loop=0;$loop<count($tab);$loop++) {
		$tab_creneau=get_infos_creneau($tab[$loop]['id_creneau']);
		$retour.="<div class='postit' style='text-align:center;'>Un remplacement vous est attribué&nbsp;:<br /><strong>".get_nom_classe($tab[$loop]['id_classe'])."&nbsp;:</strong> ".formate_date($tab[$loop]['date_debut_r'], 'n', 'court')." en ".$tab_creneau['info_html'];
		if($tab[$loop]['salle']!="") {
			$retour.=" (<em>salle ".$tab[$loop]['salle']."</em>)";
		}
		$retour.=".<br />(<em style='font-size:x-small'>remplacement de ".get_info_grp($tab[$loop]['id_groupe'])."</em>)";
		if($tab[$loop]['commentaire_validation']!="") {
			$retour.="<br />".$tab[$loop]['commentaire_validation'];
		}
		$retour.="</div>";
	}

	return $retour;
}

function test_reponses_favorables_propositions_remplacement() {
	global$gepiPath;

	$retour="";

	$test_champ=mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM abs_prof_remplacement LIKE 'id_aid';"));
	if ($test_champ==0) {
		$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE abs_prof_remplacement ADD id_aid INT(11) NOT NULL AFTER id_groupe;");
	}

	$nb=0;
	$sql="SELECT * FROM abs_prof_remplacement WHERE date_fin_r>='".strftime('%Y-%m-%d %H:%M:%S')."' AND reponse='oui';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(check_proposition_remplacement_validee($lig->id_absence, $lig->id_groupe, $lig->id_aid, $lig->id_classe, $lig->jour, $lig->id_creneau)=="") {
				$nb++;
			}
		}
	}

	if($nb>0) {
		$retour="<div class='postit' style='text-align:center;'>$nb proposition(s) de remplacement a(ont) reçu un accueil favorable.<br />Vous pouvez choisir à qui <a href='$gepiPath/mod_abs_prof/attribuer_remplacement.php'>attribuer le(s) remplacement(s)</a>.</div>";
	}

	return $retour;
}

function affiche_remplacements_eleve($login_eleve) {
	$retour="";

	$tab=get_tab_remplacements_eleve($login_eleve);
	/*
	echo "<pre>";
	print_r($tab);
	echo "</pre>";
	*/
	if(count($tab)>0) {
		$retour="<div class='postit' style='text-align:left;'><p><strong>Remplacement(s)&nbsp;:</strong></p>
	<ul>";
		for($loop=0;$loop<count($tab);$loop++) {
			$retour.="
		<li>".nl2br($tab[$loop]['texte_famille_traduit'])."</li>";
		}
		$retour.="
	</ul>
</div>";
	}

	return $retour;
}

function retourne_image_engagement($code_engagement, $nom_engagement) {
	global $gepiPath;

	$retour="";

	if(in_array($code_engagement, array("C", "V", "A", "E", "S"))) {
		$retour="<img src='$gepiPath/images/icons/engagement_".$code_engagement.".png' class='icone16' alt=\"Engagement : $nom_engagement\" />";
	}
	else {
		//$retour="<img src='$gepiPath/images/vert.png' class='icone16' alt=\"Engagement : $nom_engagement\" />";
		$retour="<img src='$gepiPath/images/icons/engagement_.png' class='icone16' alt=\"Engagement : $nom_engagement\" />";
	}

	return $retour;
}


function liste_checkbox_eleves_classe($id_classe, $num_periode="", $tab_eleves_preselectionnes=array(), $nom_champ='login_eleve', $nom_func_js_tout_cocher_decocher='cocher_decocher') {
	$retour="";

	$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe'";
	if($num_periode!="") {
		$sql.=" AND jec.periode='$num_periode'";
	}
	$sql.=" ORDER BY nom, prenom, login;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table width='100%' summary=\"Tableau de choix des élèves\">\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' >\n";

		$cpt=0;
		$statut_prec="";
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td style='text-align:left' >\n";
			}

			$retour.="<input type='checkbox' name='".$nom_champ."[]' id='".$nom_champ."_$cpt' value='$lig->login' ";
			$retour.="onchange=\"checkbox_change('".$nom_champ."_$cpt')\" ";
			if(in_array($lig->login, $tab_eleves_preselectionnes)) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->login\"><span id='texte_".$nom_champ."_$cpt'$temp_style>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, 'majf2')."</span></label><br />\n";

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

function liste_checkbox_eleves_classe2($id_classe, $num_periode="", $tab_eleves_preselectionnes=array(), $nom_champ='login_eleve', $id_champ='login_eleve', $nom_func_js_tout_cocher_decocher='cocher_decocher') {
	global $gepiPath;

	$retour="";

	$sql="SELECT DISTINCT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe'";
	if($num_periode!="") {
		$sql.=" AND jec.periode='$num_periode'";
	}
	$sql.=" ORDER BY nom, prenom, login;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$retour.="
	<table class='boireaus boireaus_alt'>
		<thead>
			<tr>
				<th>Élève</th>
				<th>
					<a href=\"#\" onclick=\"$nom_func_js_tout_cocher_decocher(true);return false;\" title='Tout cocher'><img src='$gepiPath/images/enabled.png' class='icone20' alt='Tout cocher' /></a> - <a href=\"#\" onclick=\"$nom_func_js_tout_cocher_decocher(false);return false;\" title='Tout décocher'><img src='$gepiPath/images/disabled.png' class='icone20' alt='Tout décocher' /></a>
				</th>
			</tr>
		</thead>
		<tbody>";

		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if(in_array($lig->login, $tab_eleves_preselectionnes)) {
				$checked="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$checked="";
				$temp_style="";
			}

			$retour.="
			<tr>
				<td>
					<label for='".$id_champ."_$cpt' title=\"$lig->login\"><span id='texte_".$id_champ."_$cpt'$temp_style>".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, 'majf2')."</span></label>
				</td>
				<td>
					<input type='checkbox' name='".$nom_champ."[]' id='".$id_champ."_$cpt' value='$lig->login' onchange=\"checkbox_change('".$nom_champ."_$cpt')\" $checked/>
				</td>
			</tr>";

			$cpt++;
		}
		$retour.="
		</tbody>
	</table>

	<script type='text/javascript'>
		function $nom_func_js_tout_cocher_decocher(mode) {
			for (var k=0;k<$cpt;k++) {
				if(document.getElementById('".$id_champ."_'+k)){
					document.getElementById('".$id_champ."_'+k).checked=mode;
					checkbox_change('".$id_champ."_'+k);
				}
			}
		}
		</script>\n";
	}

	return $retour;
}

function js_change_style_radio($nom_js_func="change_style_radio", $avec_balise_script="n", $avec_js_checkbox_change="n", $nom_js_func_checkbox_change='checkbox_change', $prefixe_texte_checkbox_change='texte_', $perc_opacity_checkbox_change=1) {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}

	if($avec_js_checkbox_change!="n") {
		$retour.=js_checkbox_change_style($nom_js_func_checkbox_change, $prefixe_texte_checkbox_change, "n", $perc_opacity_checkbox_change)."\n";
	}

	$retour.="
	function $nom_js_func() {
		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='radio') {
				".$nom_js_func_checkbox_change."(item[i].getAttribute('id'));
			}
		}
	}\n";
	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

function js_change_style_radio2($nom_js_func="change_style_radio", $avec_balise_script="n", $avec_js_checkbox_change="n", $nom_js_func_checkbox_change='checkbox_change', $prefixe_texte_checkbox_change='texte_', $perc_opacity_checkbox_change=1) {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}

	if($avec_js_checkbox_change!="n") {
		$retour.=js_checkbox_change_style($nom_js_func_checkbox_change, $prefixe_texte_checkbox_change, "n", $perc_opacity_checkbox_change)."\n";
	}

	$retour.="
	function $nom_js_func(nom_champ) {
		if(nom_champ=='') {
			item=document.getElementsByTagName('input');
			for(i=0;i<item.length;i++) {
				if(item[i].getAttribute('type')=='radio') {
					".$nom_js_func_checkbox_change."(item[i].getAttribute('id'));
				}
			}
		}
		else {
			var tab=document.getElementsByName(nom_champ);
			for(i=0;i<tab.length;i++) {
				current_id=tab[i].getAttribute('id');
				if(document.getElementById(current_id)) {
					if(document.getElementById('".$prefixe_texte_checkbox_change."'+current_id)) {
						".$nom_js_func_checkbox_change."(current_id);
					}
				}
			}
		}
	}

	function id_$nom_js_func(id_champ) {
		if(id_champ=='') {
			item=document.getElementsByTagName('input');
			for(i=0;i<item.length;i++) {
				if(item[i].getAttribute('type')=='radio') {
					".$nom_js_func_checkbox_change."(item[i].getAttribute('id'));
				}
			}
		}
		else {
			var nom_champ=document.getElementById(id_champ).getAttribute('name');
			var tab=document.getElementsByName(nom_champ);
			for(i=0;i<tab.length;i++) {
				current_id=tab[i].getAttribute('id');
				//alert('current_id='+current_id);
				if(document.getElementById(current_id)) {
					if(document.getElementById('".$prefixe_texte_checkbox_change."'+current_id)) {
						".$nom_js_func_checkbox_change."(current_id);
					}
					/*
					else {
						alert('document.getElementById(\'".$prefixe_texte_checkbox_change."\'+current_id) n existe pas.');
					}
					*/
				}
			}
		}
	}\n";
	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

function js_cocher_decocher_tous_checkbox($nom_js_func="cocher_decocher_tous_checkbox", $avec_balise_script="n", $avec_checkbox_change="n", $avec_js_checkbox_change="n", $nom_js_func_checkbox_change='checkbox_change', $prefixe_texte_checkbox_change='texte_', $perc_opacity_checkbox_change=1) {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}

	if($avec_js_checkbox_change!="n") {
		$retour.=js_checkbox_change_style($nom_js_func_checkbox_change, $prefixe_texte_checkbox_change, "n", $perc_opacity_checkbox_change)."\n";
	}

	$retour.="
	function $nom_js_func(mode) {
		item=document.getElementsByTagName('input');
		for(i=0;i<item.length;i++) {
			if(item[i].getAttribute('type')=='checkbox') {
				item[i].checked=mode;";
	if($avec_checkbox_change!="n") {
		$retour.="
				checkbox_change(item[i].getAttribute('id'));";
	}
	$retour.="
			}
		}
	}\n";

	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

function retourne_temoin_ou_lien_rss($ele_login) {
	$retour="";

	if((getSettingAOui('rss_cdt_eleve'))||(getSettingAOui('rss_cdt_responsable'))) {
		// Le témoin Lien RSS n'est utilisé actuellement que pour les CDT
		// Cela évoluera peut-être...
		if(!getSettingANon("active_cahiers_texte")) {
			$test_https = 'y';
			if (!isset($_SERVER['HTTPS'])
				OR (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) != "on")
				OR (isset($_SERVER['X-Forwaded-Proto']) AND $_SERVER['X-Forwaded-Proto'] != "https"))
			{
				$test_https = 'n';
			}

			if($_SESSION['statut']=='administrateur') {
				//echo "<div style='text-align:right;'>\n";
				$uri_el = retourneUri($ele_login, $test_https, 'cdt');
				if($uri_el['uri']!="#") {
					$retour="<a href='".$uri_el['uri']."' title='Flux RSS du cahier de textes de cet élève' target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
				}
				else {
					$retour="<a href='../cahier_texte_admin/rss_cdt_admin.php#rss_initialisation_cas_par_cas' target='_blank' title=\"Le flux RSS du cahier de textes de cet élève n'est pas initialisé. Cliquez pour accéder au paramétrage du module RSS et créer le flux de cet élève\"><img src='../images/icons/rss_non_initialise.png' width='16' height='16' /></a>";
				}
				//echo "</div>\n";
			}
			elseif((($_SESSION['statut']=='scolarite')&&(getSettingAOui('rss_cdt_scol')))||
			(($_SESSION['statut']=='cpe')&&(getSettingAOui('rss_cdt_cpe')))||
			(($_SESSION['statut']=='professeur')&&(getSettingAOui('rss_cdt_pp'))&&(is_pp($_SESSION['login'], "", $ele_login)))) {
				//echo "<div style='text-align:right;'>\n";
				$uri_el = retourneUri($ele_login, $test_https, 'cdt');
				if($uri_el['uri']!="#") {
					$retour="<a href='".$uri_el['uri']."' title='Flux RSS du cahier de textes de cet élève' target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
				}
				else {
					$retour="<img src='../images/icons/rss_non_initialise.png' width='16' height='16' title=\"Le flux RSS du cahier de textes de cet élève n'est pas initialisé. Contactez l'administrateur\" />";
				}
				//echo "</div>\n";
			}
			elseif(($_SESSION['statut']=='eleve')&&(getSettingAOui('rss_cdt_eleve'))&&(getSettingValue("rss_acces_ele") == 'direct')) {
				$uri_el = retourneUri($ele_login, $test_https, 'cdt');

				if($uri_el['uri']!="#") {
					$retour="<a href='".$uri_el['uri']."' title=\"Flux RSS de votre cahier de textes.

	Avec cette URL, vous pourrez consulter les travaux à faire sans devoir vous connecter dans Gepi.
	Firefox, Internet Explorer,... savent lire les flux RSS.
	Il existe également des lecteurs de flux RSS pour les SmartPhone,...\" target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
				}
				else {
					$retour="<img src='../images/icons/rss_non_initialise.png' width='16' height='16' title=\"Le flux RSS de votre cahier de textes n'est pas initialisé. Contactez l'administrateur\" />";
				}
			}
			elseif(($_SESSION['statut']=='responsable')&&(getSettingAOui('rss_cdt_responsable'))&&(getSettingValue("rss_acces_ele") == 'direct')) {
				$uri_el = retourneUri($ele_login, $test_https, 'cdt');

				if($uri_el['uri']!="#") {
					$retour="<a href='".$uri_el['uri']."' title=\"Flux RSS du cahier de textes de ".get_nom_prenom_eleve($ele_login).".

	Avec cette URL, vous pourrez consulter les travaux à faire sans devoir vous connecter dans Gepi.
	Firefox, Internet Explorer,... savent lire les flux RSS.
	Il existe également des lecteurs de flux RSS pour les SmartPhone,...\" target='_blank'><img src='../images/icons/rss.png' width='16' height='16' /></a>";
				}
				else {
					$retour="<img src='../images/icons/rss_non_initialise.png' width='16' height='16' title=\"Le flux RSS de votre cahier de textes n'est pas initialisé. Contactez l'administrateur\" />";
				}
			}
		}
	}

	return $retour;
}

function retourne_lien_edt_eleve($ele_login) {
	global $mysqli;
	global $tabdiv_infobulle, $tabid_infobulle;

	// Nécessite qu'avant le header.inc.php, il y ait
	/*
	$avec_js_et_css_edt="y";
	$style_specifique[] = "edt_organisation/style_edt";
	$style_specifique[] = "templates/DefaultEDT/css/small_edt";
	$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
	*/

	$retour="";

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))||
		((getSettingAOui('autorise_edt_eleve'))&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))
	) {

		$sql="SELECT * FROM eleves WHERE login='$ele_login';";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);

			$titre_infobulle="EDT de ".$lig->prenom." ".$lig->nom;
			$texte_infobulle="";
			$tabdiv_infobulle[]=creer_div_infobulle('edt_eleve',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

			$retour="<a href='../edt_organisation/index_edt.php?login_edt=".$ele_login."&amp;type_edt_2=eleve&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_en_infobulle();return false;\" title=\"Emploi du temps de ".$lig->prenom." ".$lig->nom."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>

<style type='text/css'>
.lecorps {
	margin-left:0px;
}
</style>

<script type='text/javascript'>
function affiche_edt_en_infobulle() {
	new Ajax.Updater($('edt_eleve_contenu_corps'),'../edt_organisation/index_edt.php?login_edt=".$ele_login."&type_edt_2=eleve&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
	afficher_div('edt_eleve','y',-20,20);
}
</script>\n";
		}
	}

	return $retour;
}


function liens_user($page_lien, $nom_var_login, $tab_statuts=array(), $autres_parametres_lien="") {
	$retour="";

	$sql="SELECT login, civilite, nom, prenom, statut FROM utilisateurs WHERE ";
	if(count($tab_statuts)>0) {
		$sql.="(";
		for($loop=0;$loop<count($tab_statuts);$loop++) {
			if($loop>0) {
				$sql.=" OR ";
			}
			$sql.="statut='".$tab_statuts[$loop]."'";
		}
		$sql.=") AND ";
	}
	$sql.="etat='actif' ORDER BY statut, nom, prenom, login;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table width='100%' summary=\"Tableau de choix des utilisateurs\">\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' >\n";

		$cpt=0;
		$statut_prec="";
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td style='text-align:left' >\n";
			}

			if($lig->statut!=$statut_prec) {
				$retour.="<p><strong>".ucfirst($lig->statut)."</strong><br />\n";
				$statut_prec=$lig->statut;
			}

			$retour.="<a href='".$page_lien."?".$nom_var_login."=".$lig->login.$autres_parametres_lien."'>$lig->civilite ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, 'majf2')."</a><br />\n";

			$cpt++;
		}
		$retour.="</td>\n";
		$retour.="</tr>\n";
		$retour.="</table>\n";
	}
	return $retour;
}

function liste_radio_utilisateurs($tab_statuts, $login_user_preselectionne="", $nom_champ='login_user', $nom_js_func_radio_change='radio_change_style') {
	$retour="";

	$sql="SELECT login, civilite, nom, prenom, statut FROM utilisateurs WHERE ";
	if(count($tab_statuts)>0) {
		$sql.="(";
		for($loop=0;$loop<count($tab_statuts);$loop++) {
			if($loop>0) {
				$sql.=" OR ";
			}
			$sql.="statut='".$tab_statuts[$loop]."'";
		}
		$sql.=") AND ";
	}
	$sql.="etat='actif' ORDER BY statut, nom, prenom, login;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table width='100%' summary=\"Tableau de choix de l'utilisateur\">\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' >\n";

		$cpt=0;
		$statut_prec="";
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td style='text-align:left' >\n";
			}

			if($lig->statut!=$statut_prec) {
				$retour.="<p><strong>".ucfirst($lig->statut)."</strong><br />\n";
				$statut_prec=$lig->statut;
			}

			$retour.="<input type='radio' name='".$nom_champ."' id='".$nom_champ."_$cpt' value='$lig->login' ";
			$retour.="onchange=\"".$nom_js_func_radio_change."()\" ";
			if($lig->login==$login_user_preselectionne) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->login\"><span id='texte_".$nom_champ."_$cpt'$temp_style>$lig->civilite ".casse_mot($lig->nom, "maj")." ".casse_mot($lig->prenom, 'majf2')."</span></label><br />\n";

			$cpt++;
		}
		$retour.="</td>\n";
		$retour.="</tr>\n";
		$retour.="</table>\n";

		$retour.="<script type='text/javascript'>
	function $nom_js_func_radio_change() {
		for (var k=0;k<$cpt;k++) {
			if(document.getElementById('".$nom_champ."_'+k)){
				if(document.getElementById('".$nom_champ."_'+k).checked==true) {
					document.getElementById('texte_".$nom_champ."_'+k).style.fontWeight='bold';
				}
				else {
					document.getElementById('texte_".$nom_champ."_'+k).style.fontWeight='normal';
				}
			}
		}
	}

	$nom_js_func_radio_change();
</script>\n";
	}

	return $retour;
}

function cdt2_affiche_car_spec_sous_textarea() {
	$cdt2_car_spec_liste=getPref($_SESSION['login'], "cdt2_car_spec_liste", "");

	$retour="";

	if($cdt2_car_spec_liste!="") {
		$tab=explode(';', $cdt2_car_spec_liste);

		$retour.="Insérer&nbsp;: ";

		// Tous les caractères mis bout à bout occupent 1628 caractères là où mediumtext permet  16777215 (2^24 − 1) caractères
		// https://dev.mysql.com/doc/refman/5.0/fr/string-type-overview.html#idm47771646626656
		for($loop=0;$loop<count($tab)-1;$loop++) {
			if($loop%2==0) {
				$bg="white";
			}
			else {
				$bg="silver";
			}
			$retour.="<input type='button' name='bouton_$loop' value=\"".$tab[$loop].";\" style='background-color:$bg;' onclick=\"insere_texte_dans_ckeditor('".$tab[$loop].";')\" /> ";
		}
	}
	return $retour;
}

function affiche_tableau_periodes_et_date_fin_classes() {
	$retour="";

	$sql="SELECT DISTINCT num_periode, nom_periode, date_fin FROM periodes ORDER BY num_periode ASC;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$retour.="<table class='boireaus boireaus_alt'>
	<tr>
		<th>Numéro</th>
		<th>Nom</th>
		<th>Date de fin</th>
		<th>Effectif</th>
		<th>Classes</th>
	</tr>";
		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			$date_fin_formatee=formate_date($lig->date_fin);
			$retour.="
	<tr class='white_hover'>
		<td>".$lig->num_periode."</td>
		<td id='modele_nom_periode_$cpt'>".$lig->nom_periode."</td>
		<td>".$date_fin_formatee."</td>
		<td>";
			//formate_date($lig->date_fin)
			$sql="SELECT COUNT(date_fin) AS eff_date_fin FROM periodes p WHERE p.num_periode='".$lig->num_periode."' AND p.nom_periode='".$lig->nom_periode."' AND p.date_fin='".$lig->date_fin."';";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$lig2=mysqli_fetch_object($res2);
				$retour.=$lig2->eff_date_fin;
			}
		$retour.="</td>
		<td>";

			$sql="SELECT c.id, c.classe FROM classes c, periodes p WHERE p.id_classe=c.id AND p.num_periode='".$lig->num_periode."' AND p.nom_periode='".$lig->nom_periode."' AND p.date_fin='".$lig->date_fin."' ORDER BY c.classe;";
			//$retour.="$sql<br />";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				$cpt2=0;
				while($lig2=mysqli_fetch_object($res2)) {
					if($cpt2>0) {$retour.=", ";}
					$retour.=$lig2->classe;
					$cpt2++;
				}
			}
			$retour.="
	</tr>";
			$cpt++;
		}
		$retour.="
</table>";
	}
	return $retour;
}

function retourne_tableau_lapsus_et_correction() {
	$tab_lapsus_et_correction=array();

	$sql="CREATE TABLE IF NOT EXISTS vocabulaire (id INT(11) NOT NULL auto_increment,
		terme VARCHAR(255) NOT NULL DEFAULT '',
		terme_corrige VARCHAR(255) NOT NULL DEFAULT '',
		PRIMARY KEY (id)
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$create_table=mysqli_query($GLOBALS["mysqli"], $sql);
	if($create_table) {
		$sql="SELECT * FROM vocabulaire;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig_voc=mysqli_fetch_object($res)) {
				//$tab_voc[]=$lig_voc->terme;
				//$tab_voc_corrige[]=$lig_voc->terme_corrige;
				$tab_lapsus_et_correction['lapsus'][]=$lig_voc->terme;
				$tab_lapsus_et_correction['correction'][]=$lig_voc->terme;
			}
		}
	}
	return $tab_lapsus_et_correction;
}

function teste_lapsus($chaine) {
	global $tab_lapsus_et_correction;

	$chaine_retour="";

	if((isset($tab_lapsus_et_correction))&&(is_array($tab_lapsus_et_correction))&&(count($tab_lapsus_et_correction)>0)) {
		$tab_voc=$tab_lapsus_et_correction['lapsus'];
		$tab_voc_corrige=$tab_lapsus_et_correction['correction'];

		$chaine_test=" ".preg_replace("/[',;\.]/"," ",casse_mot($chaine,'min'))." ";
		//echo "$appreciation_test<br />";
		for($loop=0;$loop<count($tab_voc);$loop++) {
			if(preg_match("/ ".$tab_voc[$loop]." /i",$chaine_test)) {
				if($chaine_retour=="") {$chaine_retour.="<span style='font-weight:bold'>Suspicion de faute de frappe&nbsp;: </span>";}
				$chaine_retour.=$tab_voc[$loop]." / ".$tab_voc_corrige[$loop]."<br />";
				//log_ajax_app("Suspicion de faute de frappe : ".$tab_voc[$loop]." / ".$tab_voc_corrige[$loop]);
			}
		}
	}
	return $chaine_retour;
}

function affiche_infos_adresse_et_tel($login_user, $tab_user=array()) {
	//global $acces_mail_resp, $acces_mail_ele, $acces_tel_ele, $acces_tel_responsable, $acces_adresse_responsable;

	$retour="";
	if($login_user!="") {
		$tab_user=get_info_user($login_user);
	}
	elseif(isset($tab_user['login'])) {
		$login_user=$tab_user['login'];
	}

	$afficher_colonne_mail=true;
	$afficher_colonne_tel=true;
	$afficher_colonne_adresse=true;
	if($tab_user['statut']=='eleve') {
		$afficher_colonne_tel=get_acces_tel_ele($login_user);
		$afficher_colonne_mail=get_acces_mail_ele($login_user);

		$afficher_colonne_adresse=false;
	}
	elseif($tab_user['statut']=='responsable') {
		$afficher_colonne_adresse=get_acces_adresse_resp('', '', $login_user);
		$afficher_colonne_tel=get_acces_tel_resp('', '', $login_user);
		$afficher_colonne_mail=get_acces_mail_resp('', '', $login_user);
	}

	if((!$afficher_colonne_mail)&&(!$afficher_colonne_tel)&&(!$afficher_colonne_adresse)) {
		$retour.="Aucune information ne peut être affichée.";
	}
	else {
		$retour.="<table class='boireaus boireaus_alt2'>
	<thead>
		<tr>".($afficher_colonne_tel ? "
			<th>Téléphone</th>":"").($afficher_colonne_mail ? "
			<th>Mail</th>":"").($afficher_colonne_adresse ? "
			<th>Adresse</th>":"")."
		</tr>
	</thead>
	<tbody>
		<tr>".($afficher_colonne_tel ? "
			<td>
				".((isset($tab_user['tel_pers'])&&($tab_user['tel_pers']!="")) ? "Tel.pers&nbsp;:".affiche_numero_tel_sous_forme_classique($tab_user['tel_pers'])."<br />" : "")."
				".((isset($tab_user['tel_port'])&&($tab_user['tel_port']!="")) ? "Tel.port&nbsp;:".affiche_numero_tel_sous_forme_classique($tab_user['tel_port'])."<br />" : "")."
				".((isset($tab_user['tel_pers'])&&($tab_user['tel_prof']!="")) ? "Tel.prof&nbsp;:".affiche_numero_tel_sous_forme_classique($tab_user['tel_prof']) : "")."
			</td>":"").($afficher_colonne_mail ? "
			<td>".(isset($tab_user['email']) ? "<a href='mailto:".$tab_user['email']."?".urlencode("subject=".getSettingValue('gepiPrefixeSujetMail')."[GEPI]")."' title='Envoyer un mail'>".$tab_user['email']."</a>" : "")."</td>":"").($afficher_colonne_adresse ? "
			<td>".(isset($tab_user['adresse']['en_ligne']) ? $tab_user['adresse']['en_ligne'] : "")."</td>":"")."
		</tr>
	</tbody>
</table>";
	}

	return $retour;
}

function insere_lien_recherche_ajax_ele($id_champ, $texte_lien_recherche="") {
	global $gepiPath;

	return "<a href=\"#\" onclick=\"recherche_classes_ajax_ele('$id_champ', ''); afficher_div('div_recherche_ajax_ele','y',10,10); return false;\" title=\"Rechercher des nom/prénoms élèves parmi les classes pour insérer dans le champ de formulaire.\"><img src='$gepiPath/images/icons/chercher_eleve.png' width='24' height='16' alt='Recherche' />$texte_lien_recherche</a>";
}

function insere_fonctions_js_recherche_ajax_ele() {
	global $gepiPath;

	return "<script type='text/javascript'>
	function recherche_ele_clas_ajax_ele(id_champ, id_classe) {
		new Ajax.Updater($('div_liste_ele_ajax_ele'),'$gepiPath/eleves/ajax_consultation.php?id_classe='+id_classe+'&id_champ='+id_champ,{method: 'get'});
	}

	function remplace_contenu_champ_ajax_ele(id_champ, texte) {
		if(document.getElementById(id_champ)) {
			document.getElementById(id_champ).value=texte;
			document.getElementById(id_champ).focus();
		}
		else {
			alert(\"Le champ '\"+id_champ+\"' n'existe pas.\");
		}
	}

	function complete_contenu_champ_ajax_ele(id_champ, texte) {
		if(document.getElementById(id_champ)) {
			document.getElementById(id_champ).value+=texte;
			document.getElementById(id_champ).focus();
		}
		else {
			alert(\"Le champ '\"+id_champ+\"' n'existe pas.\");
		}
	}

	function recherche_classes_ajax_ele(id_champ, mode2) {
		//alert('plop');

		$('span_titre_infobulle_recherche_ajax_ele').innerHTML=\"Recherche élève pour le champ '\"+id_champ+\"'\";

		// S'il y a plusieurs liens de recherche pour plusieurs champs,
		// il faut vider la liste des élèves précédente pour ne pas conserver
		// des liens élèves pointant vers le champ associé à la dernière recherche.
		$('div_liste_ele_ajax_ele').innerHTML='';

		if(mode2=='') {
			new Ajax.Updater($('p_div_recherche_ajax_ele'),'$gepiPath/eleves/ajax_consultation.php?mode=get_classes&id_champ='+id_champ,{method: 'get'});
		}
		else {
			new Ajax.Updater($('p_div_recherche_ajax_ele'),'$gepiPath/eleves/ajax_consultation.php?mode=get_classes&mode2='+mode2+'&id_champ='+id_champ,{method: 'get'});
		}
	}
</script>";
}

function insere_infobulle_recherche_ajax_ele() {
	global $tabdiv_infobulle;

	$chaine_classes="";
	$titre_infobulle="<span id='span_titre_infobulle_recherche_ajax_ele'>Recherche élève</span>";
	$texte_infobulle="<p>Recherche élève parmi les classes&nbsp;:</p>
	<p id='p_div_recherche_ajax_ele'>$chaine_classes</p>
	<div id='div_liste_ele_ajax_ele'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle("div_recherche_ajax_ele",$titre_infobulle, "", $texte_infobulle, "",35,0,'y','y','n','n',2);
}

function insere_tout_le_necessaire_recherche_ajax_ele($id_champ, $texte_lien_recherche="") {
	global $gepiPath, $tabdiv_infobulle;

	$retour=insere_lien_recherche_ajax_ele($id_champ, $texte_lien_recherche);
	$retour.=insere_fonctions_js_recherche_ajax_ele();
	$retour.=insere_infobulle_recherche_ajax_ele();
	return $retour;
}

function affiche_tableau_infos_resp($login_resp) {
	$retour="";

	$sql="SELECT rp.* FROM resp_pers rp WHERE login='".$login_resp."';";
	$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
	//echo "$sql<br />";
	if(mysqli_num_rows($res_resp)>0) {
		$lig=mysqli_fetch_object($res_resp);
		$retour.="
<div style='margin-left:2em;'>
	<table class='boireaus boireaus_alt boireaus_th_left' summary='Tableau de vos informations personnelles'>
		<tr>
			<th>Nom</th>
			<td>".$lig->nom."</td>
		</tr>
		<tr>
			<th>Prénom</th>
			<td>".$lig->prenom."</td>
		</tr>
		<tr>
			<th>Civilité</th>
			<td>".$lig->civilite."</td>
		</tr>
		<tr>
			<th>Tél.personnel</th>
			<td>".affiche_numero_tel_sous_forme_classique($lig->tel_pers)."</td>
		</tr>
		<tr>
			<th>Tél.portable</th>
			<td>".affiche_numero_tel_sous_forme_classique($lig->tel_port)."</td>
		</tr>
		<tr>
			<th>Tél.professionnel</th>
			<td>".affiche_numero_tel_sous_forme_classique($lig->tel_prof)."</td>
		</tr>
		<tr>
			<th>Email (*)</th>
			<td>".$lig->mel."</td>
		</tr>";

		$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig->adr_id."';";
		$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_adr)==0) {
			$retour.="
		<tr>
			<th>Adresse</th>
			<td style='color:red'>Aucune adresse n'est enregistrée.</td>
		</tr>";
		}
		else {
			$lig_adr=mysqli_fetch_object($res_adr);

			if($lig_adr->adr1!='') {
				$retour.="		<tr><th>Ligne 1 adresse:</th><td>".$lig_adr->adr1."</td></tr>\n";
			}
			if($lig_adr->adr2!='') {
				$retour.="		<tr><th>Ligne 2 adresse:</th><td>".$lig_adr->adr2."</td></tr>\n";
			}
			if($lig_adr->adr3!='') {
				$retour.="		<tr><th>Ligne 3 adresse:</th><td>".$lig_adr->adr3."</td></tr>\n";
			}
			if($lig_adr->adr4!='') {
				$retour.="		<tr><th>Ligne 4 adresse:</th><td>".$lig_adr->adr4."</td></tr>\n";
			}
			if($lig_adr->cp!='') {
				$retour.="		<tr><th>Code postal:</th><td>".$lig_adr->cp."</td></tr>\n";
			}
			if($lig_adr->commune!='') {
				$retour.="		<tr><th>Commune:</th><td>".$lig_adr->commune."</td></tr>\n";
			}
			if($lig_adr->pays!='') {
				$retour.="		<tr><th>Pays:</th><td>".$lig_adr->pays."</td></tr>\n";
			}
		}

		$retour.="
	</table>

	<p style='margin-top:2em;'>(*) L'adresse email définie dans la table 'resp_pers' peut différer de l'adresse mail définie dans 'Gérer mon compte'.<br />
	Cette éventuelle différence ne devrait être que temporaire (<em>le temps que le secrétariat de l'établissement effectue la synchronisation de ces adresses</em>).</p>
</div>";
	}

	return $retour;
}

function affiche_tableau_infos_eleves_associes_au_resp($pers_id, $login_resp="") {
	$retour="";

	if($pers_id!="") {
		$sql="(SELECT e.* FROM eleves e,
						responsables2 r
					WHERE e.ele_id=r.ele_id AND
						r.pers_id='".$pers_id."' AND
					(r.resp_legal='1' OR r.resp_legal='2') ORDER BY e.nom,e.prenom)";
	}
	else {
		$sql="(SELECT e.* FROM eleves e,
						responsables2 r, 
						resp_pers rp
					WHERE e.ele_id=r.ele_id AND 
						r.pers_id=rp.pers_id AND 
						rp.login='".$login_resp."' AND 
					(r.resp_legal='1' OR r.resp_legal='2') ORDER BY e.nom,e.prenom)";
	}
	//$retour.="$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)>0) {
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$tab_clas=get_class_from_ele_login($lig_ele->login);

			$ligne_login="";
			$sql="SELECT etat, auth_mode FROM utilisateurs WHERE statut='eleve' AND etat='actif' AND login='$lig_ele->login';";
			$test_compte=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test_compte)>0) {
				$lig_user=mysqli_fetch_object($test_compte);
				$ligne_login="
				<tr>
					<th>Login</th>
					<td>
						".$lig_ele->login."<br />
						(<em>compte <span style='color:".(($lig_user->etat=='actif') ? "green' title='Le compte peut se connecter" : "red' title='Le compte ne peut pas se connecter")."'>".$lig_user->etat."</span></em>)
					</td>
				</tr>";
			}

			$ligne_lieu_naissance="";
			if(getSettingAOui('ele_lieu_naissance')) {
				$ligne_lieu_naissance="
				<tr>
					<th>Lieu de naissance</th>
					<td>".get_commune($lig_ele->lieu_naissance,1)."</td>
				</tr>";
			}

			$ligne_tel_pers_ele="";
			if(getSettingAOui('ele_tel_pers')) {
				$ligne_tel_pers_ele="
					<tr>
						<th>Tél.personnel</th>
						<td>".affiche_numero_tel_sous_forme_classique($lig_ele->tel_pers)."</td>
					</tr>";
			}

			$ligne_tel_pers_port="";
			if(getSettingAOui('ele_tel_port')) {
				$ligne_tel_pers_port="
					<tr>
						<th>Tél.portable</th>
						<td>".affiche_numero_tel_sous_forme_classique($lig_ele->tel_port)."</td>
					</tr>";
			}

			$ligne_tel_pers_prof="";
			if(getSettingAOui('ele_tel_prof')) {
				$ligne_tel_pers_prof="
					<tr>
						<th>Tél.professionnel</th>
						<td>".affiche_numero_tel_sous_forme_classique($lig_ele->tel_prof)."</td>
					</tr>";
			}

			$ligne_regime="";
			$sql="SELECT * FROM j_eleves_regime WHERE login='$lig_ele->login';";
			$res_reg=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_reg)>0) {
				$lig_reg=mysqli_fetch_object($res_reg);
				$ligne_regime="
					<tr>
						<th>Régime</th>
						<td>";
				if($lig_reg->regime == "d/p") {$ligne_regime.="Demi-pensionnaire";}
				elseif ($lig_reg->regime == "ext.") {$ligne_regime.="Externe";}
				elseif ($lig_reg->regime == "int.") {$ligne_regime.="Interne";}
				elseif ($lig_reg->regime == "i-e") {
					$ligne_regime.="Interne&nbsp;externé";
					if (my_strtoupper($tab_ele['sexe'])!= "F") {$ligne_regime.="e";}
				}
				$ligne_regime.="</td>
					</tr>

					<tr>
						<th>Redoublant</th>
						<td>".(($lig_reg->doublant == "R") ? "Oui" : "Non")."</td>
					</tr>";
			}

			$ligne_sup1='';
			$sql="SELECT rp.*, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM resp_pers rp, 
						responsables2 r, 
						utilisateurs u 
					WHERE u.login=rp.login AND 
						r.pers_id=rp.pers_id AND 
						(r.acces_sp='y' OR r.resp_legal='1' OR r.resp_legal='2') AND 
						r.ele_id='".$lig_ele->ele_id."';";
			$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_u)>0) {
				$ligne_sup1="
					<tr>
						<th title=\"Parents/responsables pouvant consulter les données de ".$lig_ele->nom." ".$lig_ele->prenom."\">Accès familles</th>
						<td>";
				$GepiMemesDroitsRespNonLegaux=getSettingAOui('GepiMemesDroitsRespNonLegaux');
				while($lig_u=mysqli_fetch_object($res_u)) {
					if(($lig_u->resp_legal==0)&&($GepiMemesDroitsRespNonLegaux)) {
						$ligne_sup1.=$lig_u->civilite." ".$lig_u->nom." ".$lig_u->prenom."<br />";
					}
					else {
						$ligne_sup1.=$lig_u->civilite." ".$lig_u->nom." ".$lig_u->prenom."<br />";
					}
				}
				$ligne_sup1.="
						</td>
					</tr>";
			}

			$ligne_sup2='';
			$sql="SELECT rp.*, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM resp_pers rp, 
						responsables2 r, 
						utilisateurs u 
					WHERE u.login=rp.login AND 
						r.pers_id=rp.pers_id AND 
						(r.envoi_bulletin='y' OR r.resp_legal='1' OR r.resp_legal='2') AND 
						r.ele_id='".$lig_ele->ele_id."';";
			//echo "$sql<br />";
			$res_u=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_u)>0) {
				$ligne_sup2="
					<tr>
						<!--
						<th title=\"Destinataires des bulletins en plus des responsables légaux\">Destinataires<br />des bulletins</th>
						-->
						<th title=\"Destinataires des bulletins\">Destinataires<br />des bulletins</th>
						<td>";
				while($lig_u=mysqli_fetch_object($res_u)) {
					$ligne_sup2.=$lig_u->civilite." ".$lig_u->nom." ".$lig_u->prenom."<br />";
				}
				$ligne_sup2.="
						</td>
					</tr>";
			}

			$retour.="
			<div style='float:left; width:25em; margin-left:2em;'>
				<table class='boireaus boireaus_alt boireaus_th_left' summary='Tableau de vos informations personnelles'>
		".$ligne_login."
					<tr>
						<th>Nom</th>
						<td>".$lig_ele->nom."</td>
					</tr>
					<tr>
						<th>Prénom</th>
						<td>".$lig_ele->prenom."</td>
					</tr>
					<tr>
						<th>Genre</th>
						<td>".(($lig_ele->sexe=='F') ? "féminin" : "masculin")."</td>
					</tr>
					<tr>
						<th>Né(e) le</th>
						<td>".formate_date($lig_ele->naissance)."</td>
					</tr>".$ligne_lieu_naissance.$ligne_tel_pers_ele.$ligne_tel_pers_port.$ligne_tel_pers_prof."
					<tr>
						<th>Email (*)</th>
						<td>".$lig_ele->email."</td>
					</tr>
					<tr>
						<th>Classe</th>
						<td>".$tab_clas['liste_nbsp']."</td>
					</tr>".$ligne_regime.$ligne_sup1.$ligne_sup2."
				</table>
			</div>";
		}
	}

	return $retour;
}

function retourne_tab_html_pointages_disc($login_ele) {
	global $tab_type_pointage_discipline;
	global $mod_disc_terme_menus_incidents;

	if((!isset($tab_type_pointage_discipline))||(!is_array($tab_type_pointage_discipline))||(count($tab_type_pointage_discipline)==0)) {
		$tab_type_pointage_discipline=get_tab_type_pointage_discipline();
	}

	$retour="";

	// En cas de changement de classe avec des dates de périodes différentes pour les classes, on peut avoir un total annuel qui ne corresponde pas à la somme des totaux de périodes
	// Un même pointage pourra être pris en compte dans telle classe sur telle période et dans telle autre classe sur telle autre période.

	$tab_totaux=array();
	$tab_clas_ele=get_class_periode_from_ele_login($login_ele);
	foreach($tab_clas_ele['periode'] as $num_per => $classe_courante) {
		$sql="";

		$id_classe=$classe_courante['id_classe'];

		$sql="SELECT e.* FROM periodes p, edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND numero_periode='".$num_per."' AND p.num_periode=e.numero_periode AND p.id_classe='$id_classe';";
		//$retour.="$sql<br />";
		$res_edt=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_edt)>0) {
			while($lig_edt=mysqli_fetch_object($res_edt)) {
				$sql="SELECT DISTINCT sp.* FROM sp_saisies sp WHERE sp.login='$login_ele' AND date_sp>='".$lig_edt->jourdebut_calendrier." ".$lig_edt->heuredebut_calendrier."' AND date_sp<'".$lig_edt->jourfin_calendrier." ".$lig_edt->heurefin_calendrier."';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						if(!isset($tab_totaux[$num_per][$lig->id_type])) {
							$tab_totaux[$num_per][$lig->id_type]['total']=0;
							$tab_totaux[$num_per][$lig->id_type]['detail']="";
						}
						$tab_totaux[$num_per][$lig->id_type]['total']++;
						if(!isset($tab_u[$lig->created_by])) {
							$tab_u[$lig->created_by]=affiche_utilisateur($lig->created_by, $id_classe);
						}
						$chaine_detail="- ".formate_date($lig->date_sp, "n", "complet")." (".$tab_u[$lig->created_by].")\n";
						if($lig->commentaire!='') {
							$chaine_detail.=str_replace('"', "'", $lig->commentaire);
							/*
							$chaine_tmp=str_replace('"', "'", $lig->commentaire);
							$chaine_tmp=htmlentities($chaine_tmp);
							$chaine_detail.=$chaine_tmp;
							*/
						}
						$tab_totaux[$num_per][$lig->id_type]['detail'].=$chaine_detail;

						if(!isset($tab_totaux['all'][$lig->id_type])) {
							$tab_totaux['all'][$lig->id_type]['total']=0;
							$tab_totaux['all'][$lig->id_type]['detail']="";
						}
						$tab_totaux['all'][$lig->id_type]['total']++;
						$tab_totaux['all'][$lig->id_type]['detail'].=$chaine_detail;
					}
				}
			}
		}
	}

	if(count($tab_totaux)>0) {
		$ajout_lien='';
		if(acces('/mod_discipline/extraire_pointages.php', $_SESSION['statut'])) {
			$ajout_lien=" <a href='../mod_discipline/extraire_pointages.php?login_ele=".$login_ele."&id_classe=$id_classe' title=\"Extraire/afficher les $mod_disc_terme_menus_incidents pour cet élève.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>";
		}
		$retour.="<p class='bold'>Pointage des ".$mod_disc_terme_menus_incidents."&nbsp;:".$ajout_lien."</p>
<table class='boireaus boireaus_alt' style='margin-left:1em;'>
	<thead>
		<tr>
			<th>Type</th>";

		foreach($tab_clas_ele['periode'] as $num_per => $classe_courante) {
			$retour.="
			<th title=\"Inscrit en ".$classe_courante['classe']." en période $num_per\">P".$num_per."</th>";
		}

		$retour.="
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>";

		for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
			$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];

			$title_th="";
			if((!preg_match("/</", $tab_type_pointage_discipline['indice'][$loop2]['description']))&&(!preg_match("/>/", $tab_type_pointage_discipline['indice'][$loop2]['description']))) {
				$title_th=" title=\"".$tab_type_pointage_discipline['indice'][$loop2]['description']."\"";
			}

			$retour.="
		<tr>
			<th".$title_th.">".$tab_type_pointage_discipline['indice'][$loop2]['nom']."</th>";

			if(isset($tab_clas_ele['periode'])) {
				foreach($tab_clas_ele['periode'] as $num_per => $current_classe) {
					if(isset($tab_totaux[$num_per][$current_id_type])) {
						$retour.="
			<td title=\"".$tab_totaux[$num_per][$current_id_type]['detail']."\">".$tab_totaux[$num_per][$current_id_type]['total']."</td>";
					}
					else {
						$retour.="
			<td>-</td>";
					}
				}
			}

			if(isset($tab_totaux['all'][$current_id_type])) {
				$retour.="
			<td title=\"".$tab_totaux['all'][$current_id_type]['detail']."\">".$tab_totaux['all'][$current_id_type]['total']."</td>";
			}
			else {
				$retour.="
			<td>-</td>";
			}
			$retour.="
		</tr>";
		}
		$retour.="
	</tbody>
</table>
<br />";

	}

	/*
	$tab_totaux=array();
	$sql="SELECT DISTINCT sp.* FROM sp_saisies sp WHERE sp.login='$login_ele';";
	//$retour.="$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_object($res)) {
			if(!isset($tab_totaux[$lig->id_type])) {
				$tab_totaux[$lig->id_type]=0;
			}
			$tab_totaux[$lig->id_type]++;
		}

		$tab_clas_ele=get_class_periode_from_ele_login($login_ele);

		$retour.="<p class='bold'>Pointage des menus manquements&nbsp;:</p>
<table class='boireaus boireaus_alt' style='margin-left:1em;'>
	<thead>
		<tr>
			<th>Type</th>";

		if(isset($tab_clas_ele['periode'])) {
			foreach($tab_clas_ele['periode'] as $num_per => $current_classe) {
				$retour.="
			<th title=\"Inscrit en ".$current_classe['classe']." en période $num_per\">P".$num_per."</th>";
			}
		}

		$retour.="
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>";
		for($loop2=0;$loop2<count($tab_type_pointage_discipline['indice']);$loop2++) {
			$current_id_type=$tab_type_pointage_discipline['indice'][$loop2]['id_type'];

			$retour.="
		<tr>
			<th>".$tab_type_pointage_discipline['indice'][$loop2]['nom']."</th>";

			if(isset($tab_clas_ele['periode'])) {
				foreach($tab_clas_ele['periode'] as $num_per => $current_classe) {
					$retour.="
			<td title=\"Filtrage des pointages par période non encore implémenté.\"></td>";
				}
			}

			$retour.="
			<td>";
			if(isset($tab_totaux[$current_id_type])) {
				$retour.=$tab_totaux[$current_id_type];
			}
			$retour.="</td>
		</tr>";
		}
		$retour.="
	</tbody>
</table>
<br />";
	}
	*/

	return $retour;
}

function affiche_choix_action_conseil_de_classe($id_classe, $target="") {
	global $gepiPath, $mes_groupes;
	global $tab_id_classe_exclues_module_bulletins;

	if((!isset($tab_id_classe_exclues_module_bulletins))||(count($tab_id_classe_exclues_module_bulletins)==0)) {
		$tab_id_classe_exclues_module_bulletins=get_classes_exclues_tel_module('bulletins');
	}
	$classe_sans_bulletins_gepi=false;
	if(in_array($id_classe, $tab_id_classe_exclues_module_bulletins)) {
		$classe_sans_bulletins_gepi=true;
	}

	if($target!="") {
		$target=" target='$target'";
	}

	$sql="SELECT c.classe, p.* FROM periodes p, classes c WHERE p.id_classe='$id_classe' AND p.id_classe=c.id ORDER BY p.num_periode;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		$retour="<p class='bold'>Bulletins et conseil de classe&nbsp;: <span style='color:red'>Classe n+$id_classe inconnue</span></p>";
	}
	else {
		if ($_SESSION['statut'] == 'professeur') {
			$is_pp_classe=is_pp($_SESSION['login'], $id_classe);
		}

		$tab_per=array();
		while($lig=mysqli_fetch_object($res)) {
			$tab_per[$lig->num_periode]['nom_periode']=$lig->nom_periode;
			$tab_per[$lig->num_periode]['verouiller']=$lig->verouiller;
			$nom_classe=$lig->classe;
		}

		$lien_verif="";
		if((!$classe_sans_bulletins_gepi)&&(acces("/bulletin/verif_bulletins.php", $_SESSION['statut']))) {
			$lien_verif=" <em style='font-weight:normal;'>(<a href='".$gepiPath."/bulletin/verif_bulletins.php?id_classe=$id_classe' title=\"Vérifier le remplissage des notes, appréciations, avis, absences,...\"".$target."><img src='".$gepiPath."/images/icons/bulletin_verif_20.png' class='icone20' alt='Vérif' /> Vérification</a>)</em>";

			if ($_SESSION['statut'] == 'professeur') {
				if(getSettingValue("GepiProfImprBul")!='yes') {
					$lien_verif="";
				}
				elseif(!$is_pp_classe) {
					$lien_verif="";
				}
			}
			elseif (($_SESSION['statut'] == 'cpe') and getSettingValue("GepiCpeImprBul")!='yes') {
				$lien_verif="";
			}
		}

		$retour="<p class='bold'>Bulletins et conseil de classe&nbsp;: $nom_classe".$lien_verif."</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th>Action</th>";
		foreach($tab_per as $current_num_periode => $periode) {
			$retour.="
			<th>".$periode['nom_periode']."</th>";
		}
		$retour.="
		</tr>
	</thead>
	<tbody>";

		// 20171205: Compte scolarité: Ajouter une ligne Verrouiller en ajax avec rafraichissement du DIV contenant le tableau
		//           Et un lien vers vérifier (mais quoi: donc étape choix: https://127.0.0.1/steph/gepi_git_trunk/bulletin/verif_bulletins.php?id_classe=34)
		// Comme le lien vérifier les le même pour toutes les périodes, un seul lien quelque part... ou enregistrer la préférence.

	if(!$classe_sans_bulletins_gepi) {
		if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='secours')||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiRubConseilCpeTous')))||
		(($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiRubConseilCpe'))&&(is_cpe($_SESSION['login'], $id_classe)))||
		(($_SESSION['statut']=='professeur')&&($is_pp_classe)&&(getSettingAOui('GepiRubConseilProf')))) {

			// Saisie de l'avis du conseil
			$retour.="
		<tr>
			<td>Saisir l'avis du conseil de classe&nbsp;:</td>";
			foreach($tab_per as $current_num_periode => $periode) {
				if($periode['verouiller']!='O') {
					$retour.="
			<td><a href='$gepiPath/saisie/saisie_avis1.php?id_classe=$id_classe'$target title=\"Saisir l'avis du conseil de classe.\"><img src='$gepiPath/images/saisie_avis1.png' class='icone32' alt='Saisir' /></a></td>";
				}
				else {
					$retour.="
			<td style='background-color:gray' title=\"Période close\"><img src='$gepiPath/images/disabled.png' class='icone20' alt='Clos' /></td>";
				}
			}
			$retour.="
		</tr>";
		}


		if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='secours')||
		(($_SESSION['statut']=='cpe')&&(is_cpe($_SESSION['login'], $id_classe)))||
		(($_SESSION['statut']=='professeur')&&($is_pp_classe))||
		(($_SESSION['statut']=='autre')&&(acces('/saisie/impression_avis.php', $_SESSION['statut'])))) {
			// Impression avis du conseil
			$retour.="
		<tr>
			<td><a href='$gepiPath/saisie/impression_avis.php'$target>Imprimer les avis du conseil de classe&nbsp;:</a></td>";

			//$acces_saisie_avis2=acces("/saisie/saisie_avis2.php", $_SESSION['statut']);
			$acces_saisie_avis2=acces_saisie_avis2($id_classe);

			foreach($tab_per as $current_num_periode => $periode) {
				$sql="SELECT DISTINCT a.login FROM avis_conseil_classe a, j_eleves_classes jec WHERE jec.login=a.login AND jec.periode=a.periode AND jec.id_classe='$id_classe' AND a.periode='$current_num_periode' AND avis!='';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$retour.="
			<td title=\"Aucun avis n'est saisi pour cette période.\"></td>";
				}
				else {
					$retour.="
			<td>";
					$retour.="
				<a href='$gepiPath/impression/avis_pdf.php?id_classe=$id_classe&amp;periode_num=$current_num_periode'$target title=\"Imprimer l'avis du conseil de classe, ou générer un PDF des avis du conseil.\"><img src='$gepiPath/images/icons/pdf.png' class='icone32' alt='Saisir' /></a>";
					if($acces_saisie_avis2) {
						$retour.="
				 <a href='$gepiPath/saisie/saisie_avis2.php?id_classe=$id_classe&amp;periode_num=$current_num_periode'$target title=\"Afficher les avis, mentions, mises en garde,... du conseil.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone32' alt='Saisir' /></a>";
					}
					$retour.="</td>";
				}
			}
			$retour.="
		</tr>";

			// Accès à l'impression des bulletins en CPE ou PROFPRINCIPAL
			$acces_bull_index="y";
			if((($_SESSION['statut']=='professeur')&&(!getSettingAOui('GepiProfImprBul')))||
			(($_SESSION['statut']=='cpe')&&(!getSettingAOui('GepiCpeImprBul'))))
			 {
				$acces_bull_index="n";
			}

			// Affichage Appréciations sur le groupe classe
			$retour.="
		<tr>
			<td><a href='$gepiPath/prepa_conseil/index3.php?id_classe=$id_classe'$target title=\"Imprimer les appréciations des professeurs sur le groupe classe.\">Imprimer les appréciations des professeurs sur le groupe classe&nbsp;:</a></td>";
			foreach($tab_per as $current_num_periode => $periode) {
				$sql="SELECT DISTINCT mag.id_groupe FROM matieres_appreciations_grp mag, j_groupes_classes jgc WHERE jgc.id_groupe=mag.id_groupe AND jgc.id_classe='$id_classe' AND mag.periode='$current_num_periode' AND appreciation!='' AND appreciation!='-';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)==0) {
					$retour.="
			<td title=\"Aucune appréciation sur le groupe-classe n'est saisie pour cette période.\"></td>";
				}
				else {
					$retour.="
			<td>";
					$retour.="
				<a href='$gepiPath/prepa_conseil/edit_limite.php?choix_edit=4&id_classe=$id_classe&periode1=$current_num_periode&periode2=$current_num_periode&couleur_alterne=y' target='_blank' title=\"Imprimer le bulletin simplifié du groupe classe.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone32' alt='AppGrp' /></a>";

					if($acces_bull_index=="y") {
						$retour.="
				<a href='$gepiPath/bulletin/bull_index.php?mode_bulletin=pdf&intercaler_releve_notes=y&rn_param_auto=y&type_bulletin=-1&choix_periode_num=fait&valide_select_eleves=y&&tab_id_classe[0]=$id_classe&tab_periode_num[0]=$current_num_periode&intercaler_app_classe=y' target='_blank' title=\"Générer un PDF/imprimer un bulletin des appréciations sur le groupe classe.\"><img src='$gepiPath/images/icons/pdf32.png' class='icone32' alt='AppGrpPDF' /></a>";
					}

					$retour.="
			</td>";
				}
			}
			$retour.="
		</tr>";
		}
	}

			// Imprimer les documents de prise de notes à destination des élèves délégués pendant le conseil de classe
			if(getSettingAOui('active_mod_engagements')) {
				$retour.="
		<tr>
			<td><a href='$gepiPath/mod_engagements/imprimer_documents.php?id_classe[0]=$id_classe' title=\"Imprimer les documents pour le conseil de classe : Convocations, grilles,...\"$target>Imprimer les grilles/listes destinées à la prise de notes pendant le conseil de classe&nbsp;:</a></td>";
				foreach($tab_per as $current_num_periode => $periode) {
					$retour.="
			<td><a href='$gepiPath/mod_engagements/imprimer_documents.php?id_classe[0]=$id_classe&amp;periode=$current_num_periode&amp;imprimer_liste_eleve=y&destinataire=".add_token_in_url()."'$target title=\"Imprimer les grilles/listes destinées à la prise de notes pendant le conseil de classe.\"><img src='$gepiPath/images/icons/ods.png' class='icone32' alt='ODS' /></a></td>";
				}
				$retour.="
		</tr>";
			}

	if(!$classe_sans_bulletins_gepi) {
		// Bulletins,... 
		if(($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='autre')) {
			//Toutes les moyennes d'une classe
			// Bulletins simplifiés
			// Graphes

			if(acces('/prepa_conseil/index2.php', $_SESSION['statut'])) {
				$retour.="
		<tr>
			<td><a href='$gepiPath/prepa_conseil/index2.php?id_classe=$id_classe'$target title=\"Imprimer toutes les moyennes de la classe.\">Toutes les moyennes de la classe&nbsp;:</a></td>";
				if(acces('/prepa_conseil/visu_toutes_notes.php', $_SESSION['statut'])) {
					foreach($tab_per as $current_num_periode => $periode) {
						$retour.="
			<td><a href='$gepiPath/prepa_conseil/visu_toutes_notes.php?id_classe=$id_classe&amp;num_periode=$current_num_periode&amp;couleur_alterne=y' target='_blank' title=\"Imprimer toutes les moyennes de la classe en période $current_num_periode.\"><img src='$gepiPath/images/icons/releve.png' class='icone32' alt='Moyennes' /></a></td>";
					}
				}
				else {
					foreach($tab_per as $current_num_periode => $periode) {
						$retour.="
			<td></td>";
					}
				}
				$retour.="
		</tr>";
			}
			elseif(acces('/prepa_conseil/visu_toutes_notes.php', $_SESSION['statut'])) {
				$retour.="
		<tr>
			<td>Toutes les moyennes de la classe&nbsp;:</td>";
				foreach($tab_per as $current_num_periode => $periode) {
					$retour.="
			<td><a href='$gepiPath/prepa_conseil/visu_toutes_notes.php?id_classe=$id_classe&amp;num_periode=$current_num_periode&amp;couleur_alterne=y' target='_blank' title=\"Imprimer toutes les moyennes de la classe en période $current_num_periode.\"><img src='$gepiPath/images/icons/releve.png' class='icone32' alt='Moyennes' /></a></td>";
				}
				$retour.="
		</tr>";
			}

			if(acces('/prepa_conseil/edit_limite.php', $_SESSION['statut'])) {
				$retour.="
		<tr>
			<td><a href='$gepiPath/prepa_conseil/edit_limite.php?choix_edit=1&id_classe=$id_classe&periode1=1&periode2=".count($tab_per)."&couleur_alterne=y' target='_blank' title=\"Afficher les bulletins simplifiés de toutes les périodes.\">Bulletins simplifiés&nbsp;:</a></td>";
				foreach($tab_per as $current_num_periode => $periode) {
					//https://127.0.0.1/steph/gepi_git_trunk/prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=boivinj&id_classe=42&periode1=3&periode2=3
					$retour.="
			<td><a href='$gepiPath/prepa_conseil/edit_limite.php?choix_edit=1&id_classe=$id_classe&periode1=$current_num_periode&periode2=$current_num_periode&couleur_alterne=y' target='_blank' title=\"Afficher les bulletins simplifiés la période $current_num_periode.\"><img src='$gepiPath/images/icons/bulletin_simp.png' class='icone32' alt='BullSimp' /></a></td>";
				}
				$retour.="
		</tr>";
			}

			if(acces('/visualisation/affiche_eleve.php', $_SESSION['statut'])) {
				$retour.="
		<tr>
			<td><a href='$gepiPath/visualisation/affiche_eleve.php?id_classe=$id_classe&amp;choix_periode=toutes_periodes' title=\"Afficher les graphes en mode Évolution sur l'année\"$target>Graphes&nbsp;:</a></td>";
				foreach($tab_per as $current_num_periode => $periode) {
					$retour.="
			<td><a href='$gepiPath/visualisation/affiche_eleve.php?id_classe=$id_classe&amp;num_periode_choisie=$current_num_periode'$target title=\"Voir les graphes de la classe en période $current_num_periode\"><img src='$gepiPath/images/icons/graphes.png' class='icone32' alt='Graphes' /></a></td>";
			}
				$retour.="
		</tr>";
			}
		}

		if($_SESSION['statut']=='professeur') {
			if((!isset($mes_groupes))||(count($mes_groupes)==0)) {
				$mes_groupes=get_groups_for_prof($_SESSION['login'],NULL,array('classes', 'periodes', 'visibilite'));
			}

			$tab_mes_groupes_avec_bulletin_dans_cette_classe=array();
			foreach($mes_groupes as $tmp_group) {
				if((isset($tmp_group["classes"]["classes"][$id_classe]))&&
				((!isset($tmp_group['visibilite']['bulletins']))||($tmp_group['visibilite']['bulletins']!="n"))) {
					$tab_mes_groupes_avec_bulletin_dans_cette_classe[]=$tmp_group;
				}
			}

			$ajout_title_saisie_app="";
			if(getSettingAOui('autoriser_correction_bulletin')) {
				$ajout_title_saisie_app=", proposer des corrections de mes appréciations.";
			}

			// Saisie des notes
			// Saisie des appréciations
			for($loop=0;$loop<count($tab_mes_groupes_avec_bulletin_dans_cette_classe);$loop++) {
				$retour.="
		<tr>
			<td>".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['name']."&nbsp;:</td>";
				foreach($tab_per as $current_num_periode => $periode) {
					if($periode['verouiller']=='N') {
						$retour.="
			<td>
				<a href='$gepiPath/saisie/saisie_notes.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Saisir mes moyennes des bulletins en période $current_num_periode.\"><img src='$gepiPath/images/icons/bulletin_note_saisie.png' class='icone32' alt='Saisir note' /></a> 
				<a href='$gepiPath/saisie/saisie_appreciations.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Saisir mes appréciations des bulletins en période $current_num_periode.\"><img src='$gepiPath/images/icons/bulletin_app_saisie.png' class='icone32' alt='Saisir app' /></a> 
				<a href='$gepiPath/prepa_conseil/index1.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Voir/imprimer mes moyennes et appréciations en période $current_num_periode\"><img src='$gepiPath/images/icons/bulletin_visu.png' class='icone32' alt='Mes moy et app' /></a> 
			</td>";
					}
					else {
						$retour.="
			<td style='background-color:gray' title=\"Période close\">
				<a href='$gepiPath/saisie/saisie_notes.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Voir mes moyennes en période $current_num_periode\"><img src='$gepiPath/images/icons/bulletin_note_visu.png' class='icone32' alt='Visu note' /></a> 
				<a href='$gepiPath/saisie/saisie_appreciations.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Voir mes appréciations en période $current_num_periode".$ajout_title_saisie_app."\"><img src='$gepiPath/images/icons/bulletin_app_visu.png' class='icone32' alt='Visu app' /></a> 
				<a href='$gepiPath/prepa_conseil/index1.php?id_groupe=".$tab_mes_groupes_avec_bulletin_dans_cette_classe[$loop]['id']."'$target title=\"Voir/imprimer mes moyennes et appréciations en période $current_num_periode\"><img src='$gepiPath/images/icons/bulletin_visu.png' class='icone32' alt='Mes moy et app' /></a> 
			</td>";
					}
				}
			}
			$retour.="
		</tr>";

		}
		// Cas secours à traiter aussi
	}

		$retour.="
	</tbody>
</table>";
	}

	return $retour;
}

function affiche_tableau_notes_ele($login_ele, $id_groupe, $mode=1) {
	$retour="";

	$tab_clas_per=get_class_periode_from_ele_login($login_ele);
	if(!isset($tab_clas_per['periode'])) {
		$retour="<p style='color:red'>".get_nom_prenom_eleve($login_ele)." n'est inscrit dans aucune classe.</p>";
	}
	else {
		/*
		echo "<pre>";
		print_r($tab_clas_per);
		echo "</pre>";
		*/
		$tab_notes=array();
		foreach($tab_clas_per['periode'] as $current_num_periode => $current_clas) {
			$tab_notes[$current_num_periode]=get_tab_notes_ele($login_ele, $id_groupe, $current_num_periode);
		}
		/*
		echo "<pre>";
		print_r($tab_notes);
		echo "</pre>";
		*/
		if(count($tab_notes)==0) {
			$retour="<p style='color:red'>".get_nom_prenom_eleve($login_ele)." n'a aucune note quelle que soit la période.</p>";
		}
		else {
			if($mode==1) {
				$retour="<table class='boireaus boireaus_alt'>
	<thead>
		<tr>";
				foreach($tab_notes as $current_num_periode => $current_note_per) {
					$retour.="
			<th>".$tab_clas_per['periode'][$current_num_periode]['nom_periode']."</th>";
				}
				$retour.="
		</tr>
	</thead>
	<tbody>
		<tr>";
				foreach($tab_notes as $current_num_periode => $current_note_per) {
					$retour.="
			<td style='vertical-align:top;'>
				<table border='0' style='border-spacing:0;'>";

					foreach($current_note_per as $current_id_devoir => $current_devoir) {
						$detail_note="";
						if($current_devoir['statut']=="") {
							$detail_note.="\nNote : ".$current_devoir['note']."/".$current_devoir['note_sur'];
						}

						$complement_style='';
						if($current_devoir['coef']==0) {
							$complement_style='color:grey;';
						}

						$retour.="
					<tr title=\"".$current_devoir['nom_court']." ".($current_devoir['nom_court']!=$current_devoir['nom_complet'] ? "(".$current_devoir['nom_complet'].")" : "").($current_devoir['description']!='' ? "\n".str_replace('"', "'", $current_devoir['description']) : "").$detail_note."\n"."Coefficient : ".$current_devoir['coef']."\nDate : ".formate_date($current_devoir['date'])."\">
						<td style='text-align:left;border:0px solid black;".$complement_style."'>
							<strong>".$current_devoir['nom_court']."&nbsp;:</strong> 
						</td>
						<td style='text-align:right;border:0px solid black;'>";
						if($current_devoir['statut']=="") {
							$retour.=$current_devoir['note'];
							if($current_devoir['note_sur']!=20) {
								$retour.="<span style='font-size:x-small'>(*)</span>";
							}
						}
						elseif($current_devoir['statut']=="v") {
							$retour.="-";
						}
						else {
							$retour.=$current_devoir['statut'];
						}
						if($current_devoir['display_parents']==0) {
							$retour.="<img src='../images/icons/invisible.png' class='icone16' alt='Invisible' />";
						}
						$retour.="</td>
					</tr>";
					}
					$retour.="
				</table>
			</td>";
				}
				$retour.="
		</tr>
	</tbody>
</table>";
			}
			else {
			}
		}
	}

	return $retour;
}

function affiche_tab_avis_conseil($login_ele, $avec_js="y", $avec_lien="y") {
	global $gepiPath;

	$retour="";

	$sql="SELECT DISTINCT e.nom, e.prenom, a.*, jec.id_classe, c.classe, p.nom_periode 
			FROM avis_conseil_classe a, 
				eleves e, 
				j_eleves_classes jec, 
				periodes p, 
				classes c 
			WHERE a.login='".$login_ele."' AND 
				a.login=jec.login AND 
				e.login=jec.login AND 
				a.periode=jec.periode AND 
				a.periode=p.num_periode AND 
				jec.id_classe=p.id_classe AND 
				jec.id_classe=c.id
			ORDER BY a.periode;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$tab=array();
		$max_per=0;
		while($lig=mysqli_fetch_array($res)) {
			$tab[]=$lig;
			if($lig['periode']>$max_per) {
				$max_per=$lig['periode'];
			}
		}

		$retour="<p class='bold'>".$tab[0]['nom']." ".$tab[0]['prenom'];
		if($avec_lien=="y") {
			$retour.=" <a href='$gepiPath/prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$login_ele."&id_classe=".$tab[0]['id_classe']."&periode1=1&periode2=".$max_per."&couleur_alterne=y' target='_blank'";
			if($avec_js=="y") {
				$retour.=" onclick=\"affiche_bull_simp('$login_ele', '".$tab[0]['id_classe']."', 1, $max_per); return false;\"";
			}
			$retour.="><img src='$gepiPath/images/icons/bulletin.png' class='icone16' alt='BullSimp' /></a>";
		}
		$retour.="</p>
<table class='boireaus boireaus_alt'>";
		for($loop=0;$loop<count($tab);$loop++) {
			$retour.="
	<tr>
		<td>".preg_replace("/ /", "&nbsp;", $tab[$loop]['classe'])."</td>";
		if($avec_lien=="y") {
			$retour.="
		<td title=\"Voir le bulletin simplifié pour la période ".$tab[$loop]['periode'].".\"><a href='$gepiPath/prepa_conseil/edit_limite.php?choix_edit=2&login_eleve=".$login_ele."&id_classe=".$tab[$loop]['id_classe']."&periode1=".$tab[$loop]['periode']."&periode2=".$tab[$loop]['periode']."&couleur_alterne=y' target='_blank'";
			if($avec_js=="y") {
				$retour.=" onclick=\"affiche_bull_simp('$login_ele', '".$tab[0]['id_classe']."', ".$tab[$loop]['periode'].", ".$tab[$loop]['periode']."); return false;\"";
			}
			$retour.=">".preg_replace("/ /", "&nbsp;", $tab[$loop]['nom_periode'])."</a></td>";
		}
		else {
			$retour.="<td>".preg_replace("/ /", "&nbsp;", $tab[$loop]['nom_periode'])."</td>";
		}
		$retour.="
		<td>".$tab[$loop]['avis']."</td>
	</tr>";
		}
		$retour.="
</table>";
	}

	return $retour;
}

function affiche_date_prochain_conseil_de_classe_groupe($id_groupe, $current_group=NULL, $align="center", $type="") {
	if(!isset($current_group)) {
		$current_group=get_group($id_groupe, array('classes'));
	}

	$chaine_date_conseil_classe="";
	$date_courante_debut_journee_mysql=strftime("%Y-%m-%d 00:00:00");

	foreach($current_group["classes"]["list"] as $key => $current_id_classe) {
		$current_ev=get_tab_date_prochain_evenement_telle_classe($current_id_classe, 'conseil_de_classe', "y");

		if((isset($current_ev['id_ev']))&&(in_array($_SESSION["statut"], $current_ev['statuts']))) {

			if($current_ev['date_debut']<=$date_courante_debut_journee_mysql) {
				if($chaine_date_conseil_classe=="") {
					if($type=="span") {
						$chaine_date_conseil_classe="<span title=\"Date du prochain conseil de classe pour cette classe.\"><span style='color:red'>Conseil de classe&nbsp;:</span>";
					}
					else {
						$chaine_date_conseil_classe="<p align='$align' title=\"Date du prochain conseil de classe pour cette classe.\"><span style='color:red'>Conseil de classe&nbsp;:</span><br />";
					}
				}
				$lieu_conseil_de_classe="";
				if(isset($current_ev['lieu']['designation_complete'])) {
					$lieu_conseil_de_classe=" (".$current_ev['lieu']['designation_complete'].")";
				}
				$chaine_date_conseil_classe.=" ".$current_ev['classe']."&nbsp;<span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$current_ev['classe']." : ".$current_ev['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$current_ev['slashdate_ev'].")</span>";
			}
		}
	}

	if($chaine_date_conseil_classe!="") {
		if($type=="span") {
			$chaine_date_conseil_classe.="</span>";
		}
		else {
			$chaine_date_conseil_classe.="</p>";
		}
	}

	return $chaine_date_conseil_classe;
}


function affiche_date_prochain_conseil_de_classe_classe($id_classe, $align="center", $type="") {
	$chaine_date_conseil_classe="";

	$current_ev=get_tab_date_prochain_evenement_telle_classe($id_classe, 'conseil_de_classe');

	if(isset($current_ev['id_ev'])) {
		if($chaine_date_conseil_classe=="") {

			$lieu_conseil_de_classe="";
			if(isset($current_ev['lieu']['designation_complete'])) {
				$lieu_conseil_de_classe=" (".$current_ev['lieu']['designation_complete'].")";
			}

			if($type=="span") {
				$chaine_date_conseil_classe="<span title=\"Date du prochain conseil de classe pour cette classe.\">
					<span style='color:red'>Conseil de classe&nbsp;:</span> ".$current_ev['classe']."&nbsp;<span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$current_ev['classe']." : ".$current_ev['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$current_ev['slashdate_ev'].")</span>
				</span>";
			}
			elseif($type=="span_light") {
				$chaine_date_conseil_classe="<span title=\"Date du prochain conseil de classe pour la\n".$current_ev['classe']." : ".$current_ev['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$current_ev['slashdate_ev'].")</span>";
			}
			else {
				$chaine_date_conseil_classe="<p align='$align' title=\"Date du prochain conseil de classe pour cette classe.\"><span style='color:red'>Conseil de classe&nbsp;:</span><br />";

				$chaine_date_conseil_classe.=" ".$current_ev['classe']."&nbsp;<span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$current_ev['classe']." : ".$current_ev['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$current_ev['slashdate_ev'].")</span>";
				$chaine_date_conseil_classe.="</p>";
			}
		}
	}

	return $chaine_date_conseil_classe;
}

function affiche_date_dernier_conseil_de_classe_classe($id_classe) {
	$chaine_date_conseil_classe="";

	$current_ev=get_tab_date_dernier_evenement_telle_classe($id_classe, 'conseil_de_classe');

	if(isset($current_ev['id_ev'])) {
		if($chaine_date_conseil_classe=="") {
			$chaine_date_conseil_classe="<p align='center' title=\"Date du prochain conseil de classe pour cette classe.\"><span style='color:red'>Conseil de classe&nbsp;:</span><br />";
		}
		$lieu_conseil_de_classe="";
		if(isset($current_ev['lieu']['designation_complete'])) {
			$lieu_conseil_de_classe=" (".$current_ev['lieu']['designation_complete'].")";
		}
		$chaine_date_conseil_classe.=" ".$current_ev['classe']."&nbsp;<span style='font-size:small;' title=\"Date du prochain conseil de classe pour la\n".$current_ev['classe']." : ".$current_ev['slashdate_heure_ev'].$lieu_conseil_de_classe."\">(".$current_ev['slashdate_ev'].")</span>";
	}

	if($chaine_date_conseil_classe!="") {
		$chaine_date_conseil_classe.="</p>";
	}

	return $chaine_date_conseil_classe;
}

function affiche_tableau_vacances($id_classe="", $griser="n", $affiche_passe="y") {
	$retour="";

	$nb_lignes=0;

	// A FAIRE : Si $id_classe est non vide, relever le contenu de edt_calendrier pour la classe indiquée avec etabferme_calendrier='2', etabvacances_calendrier='1'

	if($id_classe=="") {
		$sql="SELECT * FROM calendrier_vacances ORDER BY debut_calendrier_ts;";
	}
	else {
		$sql="SELECT * FROM edt_calendrier WHERE (classe_concerne_calendrier LIKE '".$id_classe.";%' OR classe_concerne_calendrier LIKE '%;".$id_classe.";%') AND 
									etabferme_calendrier='2' AND 
									etabvacances_calendrier='1' ORDER BY debut_calendrier_ts;";
	}
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$retour.="<table class='boireaus boireaus_alt' summary='Vacances scolaires'>
	<tr>
		<th>Titre</th>
		<th>Du</th>
		<th>Au</th>
	</tr>";
		while($lig=mysqli_fetch_object($res)) {
			if(($griser=="y")&&($lig->fin_calendrier_ts<time())) {
				$passe=" style='background-color:grey'";
			}
			else {
				$passe="";
			}

			if(($affiche_passe=="y")||($passe=="")) {
				$retour.="
	<tr".$passe.">
		<th".$passe.">".$lig->nom_calendrier."</th>
		<td>".french_strftime("%a %d/%m/%Y", $lig->debut_calendrier_ts)."</td>
		<td>
			<!--
			".french_strftime("%a %d/%m/%Y", $lig->fin_calendrier_ts)."<br />
			-->
			".get_date_slash_from_mysql_date($lig->jourfin_calendrier." ".$lig->heurefin_calendrier, 'court')."
		</td>
	</tr>";
				$nb_lignes++;
			}
		}
		$retour.="</table>";
	}

	if($nb_lignes==0) {
		$retour="";
	}

	return $retour;
}

function affiche_calendrier_vacances() {
	$retour="";

	// Boucler sur les mois de septembre à juillet et griser les jours non ouvrés et vacances
	// Sur une option afficher l'indication de semaine A/B

	return $retour;
}

function affiche_tableau_periodes_ouvertes() {
	global $couleur_verrouillage_periode, $traduction_verrouillage_periode;

	$retour="";

	//SELECT c.classe, p.* FROM periodes p, classes c WHERE c.id=p.id_classe ORDER BY c.classe,p.;

	$sql="SELECT max(num_periode) AS maxper FROM periodes;";
	$res=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$maxper=$lig->maxper;

		$tab_clas=array();
		$sql=retourne_sql_mes_classes();
		$res_clas=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_clas)>0) {
			$retour="<table class='boireaus'>
	<tr>
		<th></th>";
			while($lig_clas=mysqli_fetch_object($res_clas)) {
				$tab_clas[$lig_clas->id_classe]=$lig_clas->classe;

				if(mb_strlen($lig_clas->classe)>3) {
					$aff_classe="<div class='texte_td_a_la_verticale'>".$lig_clas->classe."</div>";
				}
				else {
					//$aff_classe="<p>".$lig_clas->classe."</p>";
					$aff_classe=$lig_clas->classe;
				}

			$retour.="
		<th>".$aff_classe."</th>";

			}
			$retour.="
	</tr>";
		}

		$tab_per_clas=array();
		$sql="SELECT p.* FROM periodes p;";
		$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_per)>0) {
			while($lig_per=mysqli_fetch_object($res_per)) {
				$tab_per_clas[$lig_per->id_classe][$lig_per->num_periode]=$lig_per->verouiller;
			}
		}

		for($i=1;$i<=$maxper;$i++) {
			$retour.="
	<tr>
		<th title='Période $i'>P.".$i."</th>";

			foreach($tab_clas as $id_classe => $classe) {
				if(!isset($tab_per_clas[$id_classe][$i])) {
					$retour.="
		<td style='background-color:grey'></td>";
				}
				else {
					$retour.="
		<td style='background-color:".$couleur_verrouillage_periode[$tab_per_clas[$id_classe][$i]]."' title=\"Classe de ".$classe." : Période $i ".$traduction_verrouillage_periode[$tab_per_clas[$id_classe][$i]]."\"></td>";
				}

				$retour.="
		</td>";

			}

			$retour.="
	</tr>";
		}
			$retour.="
</table>";

	}

	return $retour;
}

function affiche_tableau_acces_ele_parents_appreciations_et_avis_bulletins($mode="") {
	global $couleur_verrouillage_periode, $traduction_verrouillage_periode;

	$retour="";

	//SELECT c.classe, p.* FROM periodes p, classes c WHERE c.id=p.id_classe ORDER BY c.classe,p.;

	if(($mode=="pp")&&($_SESSION['statut']=="professeur")&&(is_pp($_SESSION['login']))) {
		$tab_classe_pp=get_tab_ele_clas_pp($_SESSION['login']);
		$maxper=0;
		for($loop=0;$loop<count($tab_classe_pp['id_classe']);$loop++) {
			$sql="SELECT max(num_periode) AS maxper FROM periodes WHERE id_classe='".$tab_classe_pp['id_classe'][$loop]."';";
			$res=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res)>0) {
				$lig=mysqli_fetch_object($res);
				if($lig->maxper>$maxper) {
					$maxper=$lig->maxper;
				}
				$tab_classe_pp['maxper'][$loop]=$lig->maxper;

				if(getSettingValue("acces_app_ele_resp")=="manuel") {
					$sql="SELECT * FROM matieres_appreciations_acces WHERE id_classe='".$tab_classe_pp['id_classe'][$loop]."';";
					$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
					if(mysqli_num_rows($res_per)>0) {
						while($lig_per=mysqli_fetch_object($res_per)) {
							$tab_classe_pp['acces'][$loop][$lig_per->periode]=$lig_per->acces;
						}
					}
				}
				else {
					// getSettingValue("acces_app_ele_resp")=="manuel_individuel"
					$tab_eff_ele=array();

					$sql="SELECT * FROM j_eleves_classes WHERE id_classe='".$tab_classe_pp['id_classe'][$loop]."';";
					//echo "$sql<br />\n";
					$res_jec=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_jec=mysqli_fetch_object($res_jec)) {
						$tab_classe_pp['eleves'][$loop][$lig_jec->periode][]=$lig_jec->login;
					}

					$tab_acces=array();
					$sql="SELECT * FROM matieres_appreciations_acces_eleve maae, j_eleves_classes jec WHERE maae.login=jec.login AND 
													jec.periode=maae.periode AND 
													maae.acces='y' AND 
													jec.id_classe='".$tab_classe_pp['id_classe'][$loop]."';";
					//echo "$sql<br />\n";
					$res_maae=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_maae=mysqli_fetch_object($res_maae)) {
						$tab_classe_pp['acces'][$loop][$lig_maae->periode][]=$lig_maae->login;
					}
				}
			}
		}

		if($maxper>0) {

			$retour="<table class='boireaus'>
	<tr>
		<th>Classe</th>";
			for($loop=1;$loop<=$maxper;$loop++) {
				$retour.="
		<th title=\"Période $loop\">P".$loop."</th>";
			}
			$retour.="
	</tr>";

			for($loop=0;$loop<count($tab_classe_pp['id_classe']);$loop++) {
				$retour.="
	<tr>
		<th>".$tab_classe_pp['classe'][$loop]."</th>";
				for($i=1;$i<=$maxper;$i++) {
					if(getSettingValue("acces_app_ele_resp")=="manuel") {
						if($i>$tab_classe_pp['maxper'][$loop]) {
							$retour.="
		<td style='background-color:grey'></td>";
						}
						elseif((isset($tab_classe_pp['acces'][$loop][$i]))&&($tab_classe_pp['acces'][$loop][$i]=="y")) {
							$retour.="
		<td style='background-color:green' title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : Les appréciations et avis du conseil de classe sont visibles des parents et élèves.\"></td>";
						}
						elseif((isset($tab_classe_pp['acces'][$loop][$i]))&&($tab_classe_pp['acces'][$loop][$i]=="n")) {
							$retour.="
		<td style='background-color:red' title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : Les appréciations et avis du conseil de classe ne sont pas visibles des parents et élèves.\"></td>";
						}
						else {
							$retour.="
		<td title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : ???\">???</td>";
						}
					}
					else {
						// getSettingValue("acces_app_ele_resp")=="manuel_individuel"

						if($i>$tab_classe_pp['maxper'][$loop]) {
							$retour.="
			<td style='background-color:grey'></td>";
						}
						elseif(!isset($tab_classe_pp['acces'][$loop][$i])) {
							$retour.="
			<td style='background-color:red' title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : Les appréciations et avis du conseil de classe ne sont pas visibles des parents et élèves.\"></td>";
						}
						elseif(count($tab_classe_pp['acces'][$loop][$i])!=count($tab_classe_pp['eleves'][$loop][$i])) {
							$retour.="
			<td style='background-color:orange' title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : Les appréciations et avis du conseil de classe ne sont visibles que pour ".count($tab_classe_pp['acces'][$loop][$i])." élèves sur ".count($tab_classe_pp['eleves'][$loop][$i]).".\">".count($tab_classe_pp['acces'][$loop][$i])."</td>";
						}
						else {
							$retour.="
			<td style='background-color:green' title=\"Classe de ".$tab_classe_pp['classe'][$loop]." en période $i : Les appréciations et avis du conseil de classe sont visibles des parents et élèves.\"></td>";
						}

					}
				}
				$retour.="
	</tr>";
			}
			$retour.="
	</table>";
		}
	}
	else {
		$tab_pp=get_tab_prof_suivi();
		$gepi_prof_suivi=ucfirst(getSettingValue('gepi_prof_suivi'));

		$sql="SELECT max(num_periode) AS maxper FROM periodes;";
		$res=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$maxper=$lig->maxper;

			$tab_clas=array();
			$sql=retourne_sql_mes_classes();
			$res_clas=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_clas)>0) {
			$retour="<table class='boireaus'>
	<tr>
		<th></th>";
				while($lig_clas=mysqli_fetch_object($res_clas)) {
					$tab_clas[$lig_clas->id_classe]=$lig_clas->classe;

					$title="";
					if(isset($tab_pp[$lig_clas->id_classe])) {
						$title=" title=\"".$gepi_prof_suivi." : ";
						for($loop_pp=0;$loop_pp<count($tab_pp[$lig_clas->id_classe]);$loop_pp++) {
							if($loop_pp>0) {
								$title.=", ";
							}
							$title.=civ_nom_prenom($tab_pp[$lig_clas->id_classe][$loop_pp]);
						}
						$title.="\"";
					}

				if(mb_strlen($lig_clas->classe)>3) {
					$aff_classe="<div class='texte_td_a_la_verticale'>".$lig_clas->classe."</div>";
				}
				else {
					//$aff_classe="<p>".$lig_clas->classe."</p>";
					$aff_classe=$lig_clas->classe;
				}

				$retour.="
		<th".$title.">".$aff_classe."</th>";
				}
				$retour.="
	</tr>";
			}

			if(getSettingValue("acces_app_ele_resp")=="manuel") {
				$tab_per_clas=array();
				$sql="SELECT * FROM matieres_appreciations_acces;";
				$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res_per)>0) {
					while($lig_per=mysqli_fetch_object($res_per)) {
						$tab_per_clas[$lig_per->id_classe][$lig_per->periode]=$lig_per->acces;
					}
				}

				for($i=1;$i<=$maxper;$i++) {
					$retour.="
	<tr>
		<th title='Période $i'>P.".$i."</th>";

					foreach($tab_clas as $id_classe => $classe) {
						if(!isset($tab_per_clas[$id_classe][$i])) {
							$retour.="
		<td style='background-color:grey'></td>";
						}
						elseif($tab_per_clas[$id_classe][$i]=="y") {
							$retour.="
		<td style='background-color:green' title=\"Classe de ".$classe." en période $i : Les appréciations et avis du conseil de classe sont visibles des parents et élèves.\"></td>";
						}
						elseif($tab_per_clas[$id_classe][$i]=="n") {
							$retour.="
		<td style='background-color:red' title=\"Classe de ".$classe." en période $i : Les appréciations et avis du conseil de classe ne sont pas visibles des parents et élèves.\"></td>";
						}
						else {
							$retour.="
		<td title=\"Classe de ".$classe." en période $i : ???\">???</td>";
						}
						/*
						$retour.="
		</td>";
						*/
					}

					$retour.="
	</tr>";
				}
			}
			else {
				// getSettingValue("acces_app_ele_resp")=="manuel_individuel"
				$tab_eff_ele=array();

				$sql="SELECT * FROM j_eleves_classes;";
				//echo "$sql<br />\n";
				$res_jec=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_jec=mysqli_fetch_object($res_jec)) {
					$tab_eff_ele[$lig_jec->periode][$lig_jec->id_classe][]=$lig_jec->login;
				}

				$tab_acces=array();
				$sql="SELECT * FROM matieres_appreciations_acces_eleve maae, j_eleves_classes jec WHERE maae.login=jec.login AND 
												jec.periode=maae.periode AND 
												maae.acces='y';";
				//echo "$sql<br />\n";
				$res_maae=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_maae=mysqli_fetch_object($res_maae)) {
					$tab_acces[$lig_maae->periode][$lig_maae->id_classe][]=$lig_maae->login;
				}

				/*
				$tab_per_clas=array();
				$sql="SELECT * FROM matieres_appreciations_acces;";
				$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($res_per)>0) {
					while($lig_per=mysqli_fetch_object($res_per)) {
						$tab_per_clas[$lig_per->id_classe][$lig_per->periode]=$lig_per->acces;
					}
				}
				*/

				for($i=1;$i<=$maxper;$i++) {
					$retour.="
		<tr>
			<th title='Période $i'>P.".$i."</th>";

					foreach($tab_clas as $id_classe => $classe) {
						//if(!isset($tab_per_clas[$id_classe][$i])) {
						if(!isset($tab_eff_ele[$i][$id_classe])) {
							$retour.="
			<td style='background-color:grey'></td>";
						}
						elseif(!isset($tab_acces[$i][$id_classe])) {
							$retour.="
			<td style='background-color:red' title=\"Classe de ".$classe." en période $i : Les appréciations et avis du conseil de classe ne sont pas visibles des parents et élèves.\"></td>";
						}
						elseif(count($tab_acces[$i][$id_classe])!=count($tab_eff_ele[$i][$id_classe])) {
							$retour.="
			<td style='background-color:orange' title=\"Classe de ".$classe." en période $i : Les appréciations et avis du conseil de classe ne sont visibles que pour ".count($tab_acces[$i][$id_classe])." élèves sur ".count($tab_eff_ele[$i][$id_classe]).".\">".count($tab_acces[$i][$id_classe])."</td>";
						}
						else {
							$retour.="
			<td style='background-color:green' title=\"Classe de ".$classe." en période $i : Les appréciations et avis du conseil de classe sont visibles des parents et élèves.\"></td>";
						}
						/*
						$retour.="
			</td>";
						*/

					}

					$retour.="
		</tr>";
				}
			}
			$retour.="
	</table>";
		}
	}

	return $retour;
}

function chaine_title_explication_verrouillage_periodes() {
	global $couleur_verrouillage_periode, $traduction_verrouillage_periode, $explication_verrouillage_periode;

	$chaine="Le verrouillage/déverrouillage s'effectue en compte *scolarité*.";
	$chaine.="\n\n";
	$chaine.="Légende:\n";
	$chaine.="*".$couleur_verrouillage_periode['N']."*: Période ".$traduction_verrouillage_periode['N']."\n".preg_replace('/"/',"''",$explication_verrouillage_periode['N']);
	$chaine.="\n\n";
	$chaine.="*".$couleur_verrouillage_periode['P']."*: Période ".$traduction_verrouillage_periode['P']."\n".preg_replace('/"/',"''",$explication_verrouillage_periode['P']);
	$chaine.="\n\n";
	$chaine.="*".$couleur_verrouillage_periode['O']."*: Période ".$traduction_verrouillage_periode['O']."\n".preg_replace('/"/',"''",$explication_verrouillage_periode['O']);

	return $chaine;
}

function abs2_afficher_tab_alerte_nj($nb_nj="", $nj_delai="", $periode_courante_seulement="y", $avec_csv=false) {
	global $mysqli, $gepiPath;

	$chaine_lien_alerte_nj="";
	if(acces("/mod_abs2/alerte_nj.php", $_SESSION['statut'])) {
		$chaine_lien_alerte_nj="<div style='float:right; width:20px; margin:3px;' title=\"Afficher les absences non justifiées depuis un certain temps.\"><a href='$gepiPath/mod_abs2/alerte_nj.php'><img src='$gepiPath/images/icons/absences_flag.png' class='icone20' alt='Alerte' /></a></div>";
	}

	$lignes_alerte="";
	// Pour le debug
	$temoin_au_moins_une_alerte=0;

	if(($nb_nj=="")||(!preg_match("/^[0-9]{1,}$/", $nb_nj))||($nb_nj<1)) {
		$abs2_afficher_alerte_nb_nj=getSettingValue("abs2_afficher_alerte_nb_nj");
		if($abs2_afficher_alerte_nb_nj=="") {
			$abs2_afficher_alerte_nb_nj=4;
		}
	}
	else {
		$abs2_afficher_alerte_nb_nj=$nb_nj;
	}

	if(($nj_delai=="")||(!preg_match("/^[0-9]{1,}$/", $nj_delai))||($nj_delai<1)) {
		$abs2_afficher_alerte_nj_delai=getSettingValue("abs2_afficher_alerte_nj_delai");
		if($abs2_afficher_alerte_nj_delai=="") {
			$abs2_afficher_alerte_nj_delai=30;
		}
	}
	else {
		$abs2_afficher_alerte_nj_delai=$nj_delai;
	}

	// Autres paramètres: envoi de mail tous les tel jour de la semaine ou tel jour du mois (à la connexion du premier cpe, ou scolarite ou administrateur)
	// Renseigner un setting avec la date du dernier envoi... calculer la date de l'envoi suivant au cas où... ou renseigner une date d'envoi suivant... et si on dépasse, on envoie.

	$date_test=strftime("%Y-%m-%d", time()-$abs2_afficher_alerte_nj_delai*3600*24);

	$msg_switch_mode_periode="";
	// Période courante seulement?
	if($periode_courante_seulement=="y") {
		$afficher_alertes_periode_seulement="y";
		// Mettre "y" par défaut et avoir un lien... pour faire un autre choix: telle période ou année ou même d'autres seuils que les seuils par défaut.
		$sql="SELECT date_fin FROM periodes WHERE date_fin<NOW() ORDER BY date_fin DESC limit 1;";
		//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
		$res_date_fin=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_date_fin)>0) {
			$lig_date_fin=mysqli_fetch_object($res_date_fin);
			if($lig_date_fin->date_fin>$date_test." 00:00:00") {
				$afficher_alertes_periode_seulement="n";
				$msg_switch_mode_periode="<em>(à moins de $abs2_afficher_alerte_nj_delai jour(s) après la fin de période, on bascule vers l'affichage du décompte de toutes les absences non justifiées depuis le début de l'année)</em>";
			}
		}
	}
	else {
		$afficher_alertes_periode_seulement="n";
	}

	$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);
	$acces_bilan_individuel=acces("/mod_abs2/bilan_individuel.php", $_SESSION['statut']);

	$sql="SELECT e.login, aad.eleve_id, SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad, 
											eleves e 
										WHERE aad.eleve_id=e.id_eleve AND 
											(e.date_sortie IS NULL OR e.date_sortie LIKE '0000-00-00%' OR e.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."') AND 
											manquement_obligation_presence='1' AND 
											non_justifiee!='0' AND 
											date_demi_jounee<'".$date_test."' 
										GROUP BY eleve_id HAVING SUM(non_justifiee)>".$abs2_afficher_alerte_nb_nj." 
										ORDER BY SUM(non_justifiee) DESC;";
	//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
	$res_alerte=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_alerte)>0) {
		if($afficher_alertes_periode_seulement=="n") {
			$date_absence_eleve_debut=strftime("%Y-%m-%d", getSettingValue('begin_bookings'));
			$date_absence_eleve_fin=$date_test;

			$lignes_alerte.=$chaine_lien_alerte_nj;
			$lignes_alerte.="<p class='bold'>Liste des élèves dépassant le seuil des ".$abs2_afficher_alerte_nb_nj." demi-journées d'absence non justifiées depuis plus de ".$abs2_afficher_alerte_nj_delai." jour(s)".($avec_csv ? " ___MOTIF_EXPORT_CSV___" : "").".</p>".$msg_switch_mode_periode."
			<table class='boireaus boireaus_alt resizable sortable' border='1'>
				<thead>
					<tr>";
			if($acces_visu_eleve) {
				$lignes_alerte.="
						<th class='nosort'></th>";
			}
			$lignes_alerte.="
						<th class='text' title=\"Cliquez pour trier d'après le nom de l'élève\">Élève</th>
						<th class='text' title=\"Cliquez pour trier d'après la classe de l'élève\">Classe</th>
						<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">Nombre de demi-journées</th>
					</tr>
				</thead>
				<tbody>";

			if($avec_csv) {
				$csv="Élève;Classe;Nombre de demi-journées;\n";
			}

			while($lig=mysqli_fetch_object($res_alerte)) {
				$nom_prenom_ele=get_nom_prenom_eleve($lig->login);

				$classe="";
				$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' ORDER BY jec.periode DESC LIMIT 1;";
				//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
				//echo "$sql<br />";
				$res_clas=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_clas)>0) {
					$lig_clas=mysqli_fetch_object($res_clas);
					$classe=$lig_clas->classe;
				}

				$lignes_alerte.="
					<tr>";
				if($acces_visu_eleve) {
					$lignes_alerte.="
						<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$lig->login."&onglet=absences' title=\"Voir les absences de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a>".($avec_csv ? " ___MOTIF_EXPORT_CSV___" : "")."</td>";
				}
				$lignes_alerte.="
						<td>".$nom_prenom_ele."</td>
						<td>".$classe."</td>
						<td>";
				if($acces_bilan_individuel) {
					$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_nj."</a>";

				}
				else {
					$lignes_alerte.=$lig->nb_nj;
				}
				$lignes_alerte.="</td>
					</tr>";

				if($avec_csv) {
					$csv.=$nom_prenom_ele.";".$classe.";".$lig->nb_nj.";\n";
				}

				$temoin_au_moins_une_alerte++;
			}

			$lignes_alerte.="
				</tbody>
			</table>";
			//echo $lignes_alerte;
		}
		else {
			// Problème: Les $abs2_afficher_alerte_nj_delai premiers jours de la période courante, on n'a aucun affichage d'absence non justifiée.
			while($lig=mysqli_fetch_object($res_alerte)) {
				$num_periode_courante=get_num_periode_d_apres_date("", $lig->login);

				if($num_periode_courante>1) {
					$sql="SELECT p.* FROM periodes p, j_eleves_classes jec 
								WHERE p.id_classe=jec.id_classe AND 
									p.num_periode=jec.periode AND 
									p.num_periode='".($num_periode_courante-1)."';";
					//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
					$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_per)>0) {
						$lig_per=mysqli_fetch_object($res_per);

						$sql="SELECT SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad 
														WHERE eleve_id='".$lig->eleve_id."' AND 
															manquement_obligation_presence='1' AND 
															non_justifiee!='0' AND 
															date_demi_jounee>'".$lig_per->date_fin."' AND 
															date_demi_jounee<'".$date_test."' 
														GROUP BY eleve_id HAVING SUM(non_justifiee)>".$abs2_afficher_alerte_nb_nj.";";
						//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
						$date_absence_eleve_debut=$lig_per->date_fin;
						$date_absence_eleve_fin=$date_test;
					}
				}
				else {
					// On prend le début de l'année

						$sql="SELECT SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad
														WHERE eleve_id='".$lig->eleve_id."' AND 
															manquement_obligation_presence='1' AND 
															non_justifiee!='0' AND 
															date_demi_jounee<'".$date_test."' 
														GROUP BY eleve_id HAVING SUM(non_justifiee)>".$abs2_afficher_alerte_nb_nj.";";
						//$lignes_alerte.="<span style='color:red'>$sql</span><br />";

						$date_absence_eleve_debut=strftime("%Y-%m-%d", getSettingValue('begin_bookings'));
						$date_absence_eleve_fin=$date_test;
				}

				$res_alerte2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_alerte2)>0) {

					$nom_prenom_ele=get_nom_prenom_eleve($lig->login);

					$classe="";
					if($num_periode_courante>=1) {
						$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' ORDER BY jec.periode DESC LIMIT 1;";
					}
					else {
						$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' AND periode='".$num_periode_courante."';";
					}
					//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
					//echo "$sql<br />";
					$res_clas=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_clas)>0) {
						$lig_clas=mysqli_fetch_object($res_clas);
						$classe=$lig_clas->classe;
					}

					//if($lignes_alerte=="") {
					if($temoin_au_moins_une_alerte==0) {
						$lignes_alerte.=$chaine_lien_alerte_nj;
						$lignes_alerte.="<p class='bold'>Liste des élèves dépassant le seuil des ".$abs2_afficher_alerte_nb_nj." demi-journées d'absence non justifiées depuis plus de ".$abs2_afficher_alerte_nj_delai." jour(s) (<em>dans la période courante</em>)".($avec_csv ? " ___MOTIF_EXPORT_CSV___" : "").".</p>
				<table class='boireaus boireaus_alt resizable sortable' border='1'>
					<thead>
						<tr>";
						if($acces_visu_eleve) {
							$lignes_alerte.="
							<th class='nosort'>".($avec_csv ? "___MOTIF_EXPORT_CSV___" : "")."</th>";
						}
						$lignes_alerte.="
							<th class='text' title=\"Cliquez pour trier d'après le nom de l'élève\">Élève</th>
							<th class='text' title=\"Cliquez pour trier d'après la classe de l'élève\">Classe</th>
							<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">Nombre de demi-journées non justifiées</th>
						</tr>
					</thead>
					<tbody>";

						if($avec_csv) {
							$csv="Élève;Classe;Nombre de demi-journées NJ;\n";
						}
					}

					$lignes_alerte.="
						<tr>";
					if($acces_visu_eleve) {
						$lignes_alerte.="
							<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$lig->login."&onglet=absences' title=\"Voir les absences de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a></td>";
					}
					$lignes_alerte.="
							<td>".$nom_prenom_ele."</td>
							<td>".$classe."</td>
							<td>";
					if($acces_bilan_individuel) {
						$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_nj."</a>";

					}
					else {
						$lignes_alerte.=$lig->nb_nj;
					}
					$lignes_alerte.="</td>
						</tr>";

					if($avec_csv) {
						$csv.=$nom_prenom_ele.";".$classe.";".$lig->nb_nj.";\n";
					}

					$temoin_au_moins_une_alerte++;
				}
			}

			//if($lignes_alerte!="") {
			if($temoin_au_moins_une_alerte>0) {
				$lignes_alerte.="
				</tbody>
			</table>";

				//echo $lignes_alerte;
			}
		}
	}

	if($avec_csv) {
		$tmp_fich="../temp/".get_user_temp_directory()."/Absences_non_justifiees_".strftime("%Y%m%d_%H%M%S").".csv";
		$f=file_put_contents($tmp_fich, $csv);

		// Modifier le $lignes_alerte
		$lignes_alerte=str_replace('___MOTIF_EXPORT_CSV___', "<a href='".$tmp_fich."' target='_blank' title=\"Télécharger l'export CSV.\"><img src='".$gepiPath."/images/icons/csv.png' class='icone16' alt='CSV' /></a>", $lignes_alerte);
		//$lignes_alerte.="PLOP";
	}

	return $lignes_alerte;
}


function abs2_afficher_tab_alerte_abs($nb_abs="", $abs_delai="", $periode_courante_seulement="y", $avec_csv=false) {
	global $mysqli, $gepiPath;

	$chaine_lien_alerte_abs="";
	if(acces("/mod_abs2/alerte_nj.php", $_SESSION['statut'])) {
		$chaine_lien_alerte_abs="<div style='float:right; width:20px; margin:3px;' title=\"Afficher les absences justifiées ou non depuis un certain temps.\"><a href='$gepiPath/mod_abs2/alerte_nj.php?mode=abs'><img src='$gepiPath/images/icons/absences_flag.png' class='icone20' alt='Alerte' /></a></div>";
	}

	$lignes_alerte="";
	// Pour le debug
	$temoin_au_moins_une_alerte=0;

	if(($nb_abs=="")||(!preg_match("/^[0-9]{1,}$/", $nb_abs))||($nb_abs<1)) {
		$abs2_afficher_alerte_nb_abs=getSettingValue("abs2_afficher_alerte_nb_abs");
		if($abs2_afficher_alerte_nb_abs=="") {
			$abs2_afficher_alerte_nb_abs=4;
		}
	}
	else {
		$abs2_afficher_alerte_nb_abs=$nb_abs;
	}

	if(($abs_delai=="")||(!preg_match("/^[0-9]{1,}$/", $abs_delai))||($abs_delai<1)) {
		$abs2_afficher_alerte_abs_delai=getSettingValue("abs2_afficher_alerte_abs_delai");
		if($abs2_afficher_alerte_abs_delai=="") {
			$abs2_afficher_alerte_abs_delai=30;
		}
	}
	else {
		$abs2_afficher_alerte_abs_delai=$abs_delai;
	}

	// Autres paramètres: envoi de mail tous les tel jour de la semaine ou tel jour du mois (à la connexion du premier cpe, ou scolarite ou administrateur)
	// Renseigner un setting avec la date du dernier envoi... calculer la date de l'envoi suivant au cas où... ou renseigner une date d'envoi suivant... et si on dépasse, on envoie.

	$date_test=strftime("%Y-%m-%d", time()-$abs2_afficher_alerte_abs_delai*3600*24);

	$msg_switch_mode_periode="";
	// Période courante seulement?
	if($periode_courante_seulement=="y") {
		$afficher_alertes_periode_seulement="y";
		// Mettre "y" par défaut et avoir un lien... pour faire un autre choix: telle période ou année ou même d'autres seuils que les seuils par défaut.
		$sql="SELECT date_fin FROM periodes WHERE date_fin<NOW() ORDER BY date_fin DESC limit 1;";
		//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
		$res_date_fin=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_date_fin)>0) {
			$lig_date_fin=mysqli_fetch_object($res_date_fin);
			if($lig_date_fin->date_fin>$date_test." 00:00:00") {
				$afficher_alertes_periode_seulement="n";
				$msg_switch_mode_periode="<em>(à moins de $abs2_afficher_alerte_abs_delai jour(s) après la fin de période, on bascule vers l'affichage du décompte de toutes les absences non justifiées depuis le début de l'année)</em>";
			}
		}
	}
	else {
		$afficher_alertes_periode_seulement="n";
	}

	$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);
	$acces_bilan_individuel=acces("/mod_abs2/bilan_individuel.php", $_SESSION['statut']);

	$sql="SELECT e.login, aad.eleve_id, SUM(aad.manquement_obligation_presence) AS nb_abs , SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad, 
											eleves e 
										WHERE aad.eleve_id=e.id_eleve AND 
											(e.date_sortie IS NULL OR e.date_sortie LIKE '0000-00-00%' OR e.date_sortie>'".strftime("%Y-%m-%d %H:%M:%S")."') AND 
											manquement_obligation_presence='1' AND 
											date_demi_jounee<'".$date_test."' 
										GROUP BY eleve_id HAVING SUM(manquement_obligation_presence)>".$abs2_afficher_alerte_nb_abs." 
										ORDER BY SUM(manquement_obligation_presence) DESC;";
	//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
	$res_alerte=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_alerte)>0) {
		if($afficher_alertes_periode_seulement=="n") {
			$date_absence_eleve_debut=strftime("%Y-%m-%d", getSettingValue('begin_bookings'));
			$date_absence_eleve_fin=$date_test;

			$lignes_alerte.=$chaine_lien_alerte_abs;
			$lignes_alerte.="<p class='bold'>Liste des élèves dépassant le seuil des ".$abs2_afficher_alerte_nb_abs." demi-journées d'absence justifiées ou non depuis plus de ".$abs2_afficher_alerte_abs_delai." jour(s)".($avec_csv ? " ___MOTIF_EXPORT_CSV___" : "").".</p>".$msg_switch_mode_periode."
			<table class='boireaus boireaus_alt resizable sortable' border='1'>
				<thead>
					<tr>";
			if($acces_visu_eleve) {
				$lignes_alerte.="
						<th class='nosort'>".($avec_csv ? "___MOTIF_EXPORT_CSV___" : "")."</th>";
			}
			$lignes_alerte.="
						<th class='text' title=\"Cliquez pour trier d'après le nom de l'élève\">Élève</th>
						<th class='text' title=\"Cliquez pour trier d'après la classe de l'élève\">Classe</th>
						<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">Nombre de demi-journées</th>
						<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">NJ</th>
					</tr>
				</thead>
				<tbody>";

			if($avec_csv) {
				$csv="Élève;Classe;Nombre de demi-journées;NJ;\n";
			}

			while($lig=mysqli_fetch_object($res_alerte)) {
				$nom_prenom_ele=get_nom_prenom_eleve($lig->login);

				$classe="";
				$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' ORDER BY jec.periode DESC LIMIT 1;";
				//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
				//echo "$sql<br />";
				$res_clas=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_clas)>0) {
					$lig_clas=mysqli_fetch_object($res_clas);
					$classe=$lig_clas->classe;
				}

				$lignes_alerte.="
					<tr>";
				if($acces_visu_eleve) {
					$lignes_alerte.="
						<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$lig->login."&onglet=absences' title=\"Voir les absences de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a></td>";
				}
				$lignes_alerte.="
						<td>".$nom_prenom_ele."</td>
						<td>".$classe."</td>
						<td>";
				if($acces_bilan_individuel) {
					$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_abs."</a>";

				}
				else {
					$lignes_alerte.=$lig->nb_abs;
				}
				$lignes_alerte.="</td>
						<td>";
				if($acces_bilan_individuel) {
					$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_nj."</a>";

				}
				else {
					$lignes_alerte.=$lig->nb_nj;
				}
				$lignes_alerte.="</td>
					</tr>";

				if($avec_csv) {
					$csv.=$nom_prenom_ele.";".$classe.";".$lig->nb_abs.";".$lig->nb_nj.";\n";
				}

				$temoin_au_moins_une_alerte++;
			}

			$lignes_alerte.="
				</tbody>
			</table>";
			//echo $lignes_alerte;
		}
		else {
			// Problème: Les $abs2_afficher_alerte_abs_delai premiers jours de la période courante, on n'a aucun affichage d'absence non justifiée.
			while($lig=mysqli_fetch_object($res_alerte)) {
				$num_periode_courante=get_num_periode_d_apres_date("", $lig->login);

				if($num_periode_courante>1) {
					$sql="SELECT p.* FROM periodes p, j_eleves_classes jec 
								WHERE p.id_classe=jec.id_classe AND 
									p.num_periode=jec.periode AND 
									p.num_periode='".($num_periode_courante-1)."';";
					//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
					$res_per=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_per)>0) {
						$lig_per=mysqli_fetch_object($res_per);

						$sql="SELECT SUM(aad.manquement_obligation_presence) AS nb_abs,SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad 
														WHERE eleve_id='".$lig->eleve_id."' AND 
															manquement_obligation_presence='1' AND 
															date_demi_jounee>'".$lig_per->date_fin."' AND 
															date_demi_jounee<'".$date_test."' 
														GROUP BY eleve_id HAVING SUM(manquement_obligation_presence)>".$abs2_afficher_alerte_nb_abs.";";
						//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
						$date_absence_eleve_debut=$lig_per->date_fin;
						$date_absence_eleve_fin=$date_test;
					}
				}
				else {
					// On prend le début de l'année

						$sql="SELECT SUM(aad.manquement_obligation_presence) AS nb_abs,SUM(aad.non_justifiee) AS nb_nj FROM a_agregation_decompte aad
														WHERE eleve_id='".$lig->eleve_id."' AND 
															manquement_obligation_presence='1' AND 
															date_demi_jounee<'".$date_test."' 
														GROUP BY eleve_id HAVING SUM(manquement_obligation_presence)>".$abs2_afficher_alerte_nb_abs.";";
						//$lignes_alerte.="<span style='color:red'>$sql</span><br />";

						$date_absence_eleve_debut=strftime("%Y-%m-%d", getSettingValue('begin_bookings'));
						$date_absence_eleve_fin=$date_test;
				}

				$res_alerte2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_alerte2)>0) {

					$nom_prenom_ele=get_nom_prenom_eleve($lig->login);

					$classe="";
					if($num_periode_courante>=1) {
						$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' ORDER BY jec.periode DESC LIMIT 1;";
					}
					else {
						$sql="SELECT c.id, c.classe, c.nom_complet FROM j_eleves_classes jec, classes c WHERE c.id=jec.id_classe AND jec.login='".$lig->login."' AND periode='".$num_periode_courante."';";
					}
					//$lignes_alerte.="<span style='color:red'>$sql</span><br />";
					//echo "$sql<br />";
					$res_clas=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res_clas)>0) {
						$lig_clas=mysqli_fetch_object($res_clas);
						$classe=$lig_clas->classe;
					}

					//if($lignes_alerte=="") {
					if($temoin_au_moins_une_alerte==0) {	// BIZARRE A VERIFIER 20180403
						$lignes_alerte.=$chaine_lien_alerte_abs;
						$lignes_alerte.="<p class='bold'>Liste des élèves dépassant le seuil des ".$abs2_afficher_alerte_nb_abs." demi-journées d'absence justifiées ou non depuis plus de ".$abs2_afficher_alerte_abs_delai." jour(s) (<em>dans la période courante</em>).".($avec_csv ? " ___MOTIF_EXPORT_CSV___" : "")."</p>
				<table class='boireaus boireaus_alt resizable sortable' border='1'>
					<thead>
						<tr>";
						if($acces_visu_eleve) {
							$lignes_alerte.="
							<th class='nosort'>".($avec_csv ? "___MOTIF_EXPORT_CSV___" : "")."</th>";
						}
						$lignes_alerte.="
							<th class='text' title=\"Cliquez pour trier d'après le nom de l'élève\">Élève</th>
							<th class='text' title=\"Cliquez pour trier d'après la classe de l'élève\">Classe</th>
							<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">Nombre de demi-journées</th>
							<th class='number' title=\"Cliquez pour trier d'après le nombre de demi-journées\">NJ</th>
						</tr>
					</thead>
					<tbody>";

						if($avec_csv) {
							$csv="Élève;Classe;Nombre de demi-journées;NJ;\n";
						}
					}

					$lignes_alerte.="
						<tr>";
					if($acces_visu_eleve) {
						$lignes_alerte.="
							<td><a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$lig->login."&onglet=absences' title=\"Voir les absences de l'élève dans le classeur élève.\"><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a></td>";
					}
					$lignes_alerte.="
							<td>".$nom_prenom_ele."</td>
							<td>".$classe."</td>
							<td>";
					if($acces_bilan_individuel) {
						$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_abs."</a>";

					}
					else {
						$lignes_alerte.=$lig->nb_abs;
					}
					$lignes_alerte.="</td>
							<td>";
					if($acces_bilan_individuel) {
						$lignes_alerte.="<a href='$gepiPath/mod_abs2/bilan_individuel.php?date_absence_eleve_debut=".$date_absence_eleve_debut."&amp;date_absence_eleve_fin=".$date_absence_eleve_fin."&amp;id_eleve=".$lig->eleve_id."&amp;affichage=html&amp;id_classe=-1&amp;type_extrait=1' title=\"Voir le bilan des manquements de cet élève.\">".$lig->nb_nj."</a>";

					}
					else {
						$lignes_alerte.=$lig->nb_nj;
					}
					$lignes_alerte.="</td>
						</tr>";

					if($avec_csv) {
						$csv.=$nom_prenom_ele.";".$classe.";".$lig->nb_abs.";".$lig->nb_nj.";\n";
					}

					$temoin_au_moins_une_alerte++;
				}
			}

			//if($lignes_alerte!="") {
			if($temoin_au_moins_une_alerte>0) {
				$lignes_alerte.="
				</tbody>
			</table>";

				//echo $lignes_alerte;
			}
		}
	}

	if($avec_csv) {
		$tmp_fich="../temp/".get_user_temp_directory()."/Absences_non_justifiees_".strftime("%Y%m%d_%H%M%S").".csv";
		$f=file_put_contents($tmp_fich, $csv);

		// Modifier le $lignes_alerte
		$lignes_alerte=str_replace('___MOTIF_EXPORT_CSV___', "<a href='".$tmp_fich."' target='_blank' title=\"Télécharger l'export CSV.\"><img src='".$gepiPath."/images/icons/csv.png' class='icone16' alt='CSV' /></a>", $lignes_alerte);
	}

	return $lignes_alerte;
}

function get_liste_voeux_orientation($login_eleve, $mode="") {
	global $tab_orientation, $tab_orientation_classe_courante;

	$retour="";

	if((!isset($tab_orientation_classe_courante))||(!is_array($tab_orientation_classe_courante))||(!isset($tab_orientation_classe_courante['voeux'][$login_eleve]))) {
		if((!isset($tab_orientation))||(!is_array($tab_orientation))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}
		elseif((!isset($tab_orientation2))||(!is_array($tab_orientation2))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}

		$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');

		$tab=get_tab_voeux_orientations_ele($login_eleve);
		$tab_voeux_ele=$tab['voeux'];

		/*
		echo "<pre>";
		print_r($tab_voeux_ele);
		echo "</pre>";
		*/
	}
	else {
		$tab_voeux_ele=$tab_orientation_classe_courante['voeux'][$login_eleve];
	}

	for($loop_voeu=1;$loop_voeu<=count($tab_voeux_ele);$loop_voeu++) {
		$retour.="<b>".$loop_voeu.".</b> ".$tab_voeux_ele[$loop_voeu]['designation'];
		if(($tab_voeux_ele[$loop_voeu]['commentaire']!="")&&($tab_voeux_ele[$loop_voeu]['commentaire']!=$tab_voeux_ele[$loop_voeu]['designation'])) {
			if($mode=="pdf_cell_ajustee") {
				$retour.="<i> (".$tab_voeux_ele[$loop_voeu]['commentaire'].")</i>";
			}
			else {
				$retour.="<em style='font-size:x-small'> (".$tab_voeux_ele[$loop_voeu]['commentaire'].")</em>";
			}
		}
		$retour.="<br />\n";
	}

	return $retour;
}


function get_select_voeux_orientation($login_eleve) {
	global $tab_orientation;
	global $gepiPath;
	global $mysqli;

	$retour="";

	$lien_definition_type_orientation="";

	$sql="SELECT * FROM eleves WHERE login='$login_eleve';";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)==0) {
		$retour="Élève non trouvé.<br />";
	}
	else {
		//$retour.=$lien_definition_type_orientation;
		if((!isset($tab_orientation))||(!is_array($tab_orientation))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			//$tab_orientation2=get_tab_orientations_types();
		}
		/*
		elseif((!isset($tab_orientation2))||(!is_array($tab_orientation2))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}
		*/

		$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');

		$lig_ele=mysqli_fetch_object($res_ele);

		if((($_SESSION['statut']=="scolarite")&&(getSettingAOui("OrientationSaisieTypeScolarite")))||
		(($_SESSION['statut']=="cpe")&&(getSettingAOui("OrientationSaisieTypeCpe")))||
		(($_SESSION['statut']=="professeur")&&(getSettingAOui("OrientationSaisieTypePP"))&&(is_pp($_SESSION['login'],"",$login_eleve)))) {
			$tab_mef_ele=get_tab_mef_from_mef_code($lig_ele->mef_code);

			$lien_definition_type_orientation="<div style='float:right;width:16px' title=\"Éditer, modifier, compléter la liste des voeux d'orientation possibles pour un élève de ".(isset($tab_mef_ele["designation_courte"]) ? $tab_mef_ele["designation_courte"] : "MEF inconnu").".\"><a href='$gepiPath/mod_orientation/saisie_types_orientation.php' target='_blank'><img src='$gepiPath/images/edit16.png' class='icone16' alt='Saisie' /></a></div>";
		}

		$tab=get_tab_voeux_orientations_ele($login_eleve);
		$tab_voeux_ele=$tab['voeux'];

		for($loop=1;$loop<=$OrientationNbMaxVoeux;$loop++) {
			$commentaire="";
			$selected_aucun="";
			if(!isset($tab_voeux_ele[$loop])) {
				$selected_aucun=" selected";
			}
			else {
				$commentaire=preg_replace('/"/', " ", $tab_voeux_ele[$loop]['commentaire']);
			}
			$retour.="
							Voeu ".($loop)."
							<select name='voeu_".$lig_ele->id_eleve."[]' id='voeu_".$lig_ele->id_eleve."_".$loop."' onchange=\"changement();\">
								<option value='' title=\"Choisissez un voeu.\nSi l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected_aucun.">---</option>";
			if(isset($tab_orientation[$lig_ele->mef_code])) {
				for($loop2=0;$loop2<count($tab_orientation[$lig_ele->mef_code]['id_orientation']);$loop2++) {
					$selected="";
					if((isset($tab_voeux_ele[$loop]))&&($tab_voeux_ele[$loop]['id_orientation']==$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2])) {
						$selected=" selected";
					}
					$retour.="
								<option value='".$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2]."' title=\"".preg_replace('/"/', " ", $tab_orientation[$lig_ele->mef_code]['description'][$loop2])."\"".$selected.">".$tab_orientation[$lig_ele->mef_code]['titre'][$loop2]."</option>";
				}
			}
			$selected="";
			if((isset($tab_voeux_ele[$loop]))&&($tab_voeux_ele[$loop]['id_orientation']=="0")) {
				$selected=" selected";
			}
			$retour.="
								<option value='0' title=\"Si l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected.">Autre orientation</option>
							</select>
							<input type='text' name='commentaire_".$lig_ele->id_eleve."[]' id='commentaire_voeu_".$lig_ele->id_eleve."_".$loop."' value=\"".$commentaire."\" size='30' onchange=\"changement();\" /><br />";
		}
	}

	$retour.=$lien_definition_type_orientation;
	return $retour;
}


function get_liste_orientations_proposees($login_eleve, $mode="") {
	global $tab_orientation, $tab_orientation_classe_courante;

	$retour="";

	if((!isset($tab_orientation_classe_courante))||(!is_array($tab_orientation_classe_courante))||(!isset($tab_orientation_classe_courante['orientation_proposee'][$login_eleve]))) {
		if((!isset($tab_orientation))||(!is_array($tab_orientation))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}
		elseif((!isset($tab_orientation2))||(!is_array($tab_orientation2))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}

		$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');

		$tab=get_tab_voeux_orientations_ele($login_eleve);
		$tab_o_ele=$tab['orientation_proposee'];

		/*
		echo "<pre>";
		print_r($tab_voeux_ele);
		echo "</pre>";
		*/
	}
	else {
		$tab_o_ele=$tab_orientation_classe_courante['orientation_proposee'][$login_eleve];
	}

	for($loop_op=1;$loop_op<=count($tab_o_ele);$loop_op++) {
		$retour.="<b>".$loop_op.".</b> ".$tab_o_ele[$loop_op]['designation'];
		if(($tab_o_ele[$loop_op]['commentaire']!="")&&($tab_o_ele[$loop_op]['commentaire']!=$tab_o_ele[$loop_op]['designation'])) {
			if($mode=="pdf_cell_ajustee") {
				$retour.="<i> (".$tab_o_ele[$loop_op]['commentaire'].")</i>";
			}
			else {
				$retour.="<em style='font-size:x-small'> (".$tab_o_ele[$loop_op]['commentaire'].")</em>";
			}
		}
		$retour.="<br />\n";
	}

	return $retour;
}


function get_select_orientations_proposees($login_eleve) {
	global $tab_orientation;
	global $gepiPath;
	global $mysqli;

	$retour="";

	$lien_definition_type_orientation="";

	$sql="SELECT * FROM eleves WHERE login='$login_eleve';";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)==0) {
		$retour="Élève non trouvé.<br />";
	}
	else {

		if((!isset($tab_orientation))||(!is_array($tab_orientation))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			//$tab_orientation2=get_tab_orientations_types();
		}
		/*
		elseif((!isset($tab_orientation2))||(!is_array($tab_orientation2))) {
			$tab_orientation=get_tab_orientations_types_par_mef();
			$tab_orientation2=get_tab_orientations_types();
		}
		*/

		$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');

		$lig_ele=mysqli_fetch_object($res_ele);

		if((($_SESSION['statut']=="scolarite")&&(getSettingAOui("OrientationSaisieTypeScolarite")))||
		(($_SESSION['statut']=="cpe")&&(getSettingAOui("OrientationSaisieTypeCpe")))||
		(($_SESSION['statut']=="professeur")&&(getSettingAOui("OrientationSaisieTypePP"))&&(is_pp($_SESSION['login'],"",$login_eleve)))) {
			$tab_mef_ele=get_tab_mef_from_mef_code($lig_ele->mef_code);

			$lien_definition_type_orientation="<div style='float:right;width:16px' title=\"Éditer, modifier, compléter la liste des voeux d'orientation possibles pour un élève de ".(isset($tab_mef_ele["designation_courte"]) ? $tab_mef_ele["designation_courte"] : "MEF inconnu").".\"><a href='$gepiPath/mod_orientation/saisie_types_orientation.php' target='_blank'><img src='$gepiPath/images/edit16.png' class='icone16' alt='Saisie' /></a></div>";
		}

		$tab=get_tab_voeux_orientations_ele($login_eleve);
		$tab_o_ele=$tab['orientation_proposee'];

		for($loop=1;$loop<=$OrientationNbMaxOrientation;$loop++) {
			$commentaire="";
			$selected_aucun="";
			if(!isset($tab_o_ele[$loop])) {
				$selected_aucun=" selected";
			}
			else {
				$commentaire=preg_replace('/"/', " ", $tab_o_ele[$loop]['commentaire']);
			}
			$retour.="
							Orientation ".($loop)."
							<select name='orientation_".$lig_ele->id_eleve."[]' id='orientation_".$lig_ele->id_eleve."_".$loop."' onchange=\"changement();\">
								<option value='' title=\"Si l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected_aucun.">---</option>";
			if(isset($tab_orientation[$lig_ele->mef_code])) {
				for($loop2=0;$loop2<count($tab_orientation[$lig_ele->mef_code]['id_orientation']);$loop2++) {
					$selected="";
					if((isset($tab_o_ele[$loop]))&&($tab_o_ele[$loop]['id_orientation']==$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2])) {
						$selected=" selected";
					}
					$retour.="
								<option value='".$tab_orientation[$lig_ele->mef_code]['id_orientation'][$loop2]."' title=\"".preg_replace('/"/', " ", $tab_orientation[$lig_ele->mef_code]['description'][$loop2])."\"".$selected.">".$tab_orientation[$lig_ele->mef_code]['titre'][$loop2]."</option>";
				}
			}
			$selected="";
			if((isset($tab_o_ele[$loop]))&&($tab_o_ele[$loop]['id_orientation']=="0")) {
				$selected=" selected";
			}
			$retour.="
								<option value='0' title=\"Si l'orientation souhaitée n'est pas dans la liste proposée, choisissez 'Autre orientation' et précisez en commentaire l'orientation.\"".$selected.">Autre orientation</option>
							</select>
							<input type='text' name='commentaire_".$lig_ele->id_eleve."[]' id='commentaire_orientation_".$lig_ele->id_eleve."_".$loop."' value=\"".$commentaire."\" size='30' onchange=\"changement();\" /><br />";
		}
	}

	$retour.=$lien_definition_type_orientation;
	return $retour;
}



function get_avis_orientations_proposees($login_eleve, $mode="") {
	$retour="";

	$avis_o_ele="";
	$sql="SELECT DISTINCT * FROM o_avis oa WHERE oa.login='".$login_eleve."';";
	//echo "$sql<br />";
	$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_o)>0) {
		$cpt=1;
		$lig_o=mysqli_fetch_object($res_o);
		$avis_o_ele=$lig_o->avis;

		$retour="<b title=\"Avis sur l'orientation proposée\">Avis&nbsp;</b> ".$avis_o_ele;
	}

	//$retour="<b title=\"Avis sur l'orientation proposée\">Avis&nbsp;</b> ".$avis_o_ele;

	return $retour;
}

function get_champ_avis_orientations_proposees($login_eleve) {
	global $tab_orientation;

	$retour="";

	$sql="SELECT * FROM eleves WHERE login='$login_eleve';";
	//echo "$sql<br />";
	$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_ele)==0) {
		$retour="Élève non trouvé.<br />";
	}
	else {
		$lig_ele=mysqli_fetch_object($res_ele);

		$avis_o_ele="";
		$sql="SELECT DISTINCT * FROM o_avis oa WHERE oa.login='".$login_eleve."';";
		//echo "$sql<br />";
		$res_o=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_o)>0) {
			$cpt=1;
			$lig_o=mysqli_fetch_object($res_o);
			$avis_o_ele=$lig_o->avis;

			$avis_o_ele=preg_replace("#<br />#i", "", $lig_o->avis);
		}

		$retour.="<label for='avis_orientation_".$lig_ele->id_eleve."' style='vertical-align:top'><b title=\"Avis/commentaire sur l'orientation proposée\">Commentaire&nbsp;</b> </label><textarea name='avis_orientation_".$lig_ele->id_eleve."[]' id='avis_orientation_".$lig_ele->id_eleve."' cols='60' onchange=\"changement();\">".$avis_o_ele."</textarea></p>";
	}

	return $retour;
}

function js_change_style_all_checkbox($avec_balise_script="n", $avec_js_checkbox_change="n", $nom_js_func_checkbox_change='checkbox_change', $prefixe_texte_checkbox_change='texte_', $perc_opacity_checkbox_change=1) {
	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>\n";}

	if($avec_js_checkbox_change!="n") {
		$retour.=js_checkbox_change_style($nom_js_func_checkbox_change, $prefixe_texte_checkbox_change, "n", $perc_opacity_checkbox_change)."\n";
	}

	$retour.="
	item=document.getElementsByTagName('input');
	for(i=0;i<item.length;i++) {
		if(item[i].getAttribute('type')=='checkbox') {
			".$nom_js_func_checkbox_change."(item[i].getAttribute('id'));
		}
	}\n";
	if($avec_balise_script!="n") {$retour.="</script>\n";}
	return $retour;
}

function get_chaine_matieres_prof($login, $champ="matiere", $separateur=", ") {
	$retour="";

	$tab=get_tab_matieres_prof($login);
	for($loop=0;$loop<count($tab);$loop++) {
		if($loop>0) {
			$retour.=$separateur;
		}
		$retour.=$tab[$loop][$champ];
	}

	return $retour;
}

function insere_lien_calendrier_crob($float="") {
	global $gepiPath, $niveau_arbo, $tabdiv_infobulle;//, $tabid_infobulle;

	if(!acces("/lib/calendrier_crob.php", $_SESSION["statut"])) {
		$abandon="y";
	}
	else {
		//include("$gepiPath/lib/calendrier_crob.inc.php");
		if(isset($niveau_arbo)) {
			if($niveau_arbo==0) {
				include("./lib/calendrier_crob.inc.php");
			}
			elseif($niveau_arbo==1) {
				include("../lib/calendrier_crob.inc.php");
			}
			elseif($niveau_arbo==2) {
				include("../../lib/calendrier_crob.inc.php");
			}
			else {
				$abandon="y";
			}
		}
		else {
			include("../lib/calendrier_crob.inc.php");
		}
	}

	if(!isset($abandon)) {
		$titre_infobulle="Calendrier";
		$texte_infobulle="<div id='div_calendrier_popup_contenu_infobulle_crob'>".affiche_calendrier_crob(strftime("%m"), strftime("%Y"), "", "popup")."</div>";
		$tabdiv_infobulle[]=creer_div_infobulle('div_calendrier_crob_popup',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

		if($float!="") {
			echo "<div style='float:$float;width:16px;margin:3px;'>";
		}
		echo "<a href='$gepiPath/lib/calendrier_crob.php' onclick=\"afficher_div('div_calendrier_crob_popup', 'y', 10, 10); return false;\" target='_blank' title=\"Afficher le calendrier.\"><img src='$gepiPath/images/icons/date.png' class='icone16' alt='Calendrier' /></a>
<script type='text/javascript'>
	function affiche_calendrier_crob(mois, annee, id_classe) {
		//alert('plop');

		new Ajax.Updater($('div_calendrier_popup_contenu_infobulle_crob'),'$gepiPath/lib/calendrier_crob.php?mois='+mois+'&annee='+annee+'&mode=popup&id_classe='+id_classe+'".add_token_in_url(false)."',{method: 'get'});
	}
</script>";
		if($float!="") {
			echo "</div>";
		}

	}
}


function get_liste_tag_notice_cdt($id_ct, $type_ct, $float="") {
	global $mysqli, $tab_drapeaux_tag_cdt, $tab_tag_type, $gepiPath;

	$retour="";

	if((!isset($tab_tag_type))||(!is_array($tab_tag_type))||(count($tab_tag_type)==0)) {
		$tab_tag_type=get_tab_tag_cdt();
		// Pour contrôler que l'opération n'est bien effectuée qu'une fois par page (cf global)
		//$retour.="<span style='color:plum'>get_tab_tag_cdt</span>";
	}

	if(count($tab_tag_type)>0) {
		//echo "get_tab_tag_notice($id_ct, $type_ct)<br />";
		$tab_tag_notice=get_tab_tag_notice($id_ct, $type_ct);
		if(isset($tab_tag_notice["indice"])) {
			if($type_ct=="t") {
				$sql="SELECT date_ct FROM ct_devoirs_entry WHERE id_ct='".$id_ct."';";
			}
			elseif($type_ct=="c") {
				$sql="SELECT date_ct FROM ct_entry WHERE id_ct='".$id_ct."';";
			}
			elseif($type_ct=="p") {
				$sql="SELECT date_ct FROM ct_private_entry WHERE id_ct='".$id_ct."';";
			}
			$res_ct=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_ct)==0) {
				$chaine_complement_title="cette notice";
			}
			else {
				$lig_ct=mysqli_fetch_object($res_ct);
				$chaine_complement_title="le ".french_strftime("%A %d/%m/%Y", $lig_ct->date_ct);
			}
			for($loop_tag=0;$loop_tag<count($tab_tag_notice["indice"]);$loop_tag++) {
				if($float=="") {
					$retour.=" <img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"Un ".$tab_tag_notice["indice"][$loop_tag]['nom_tag']." est signalé pour ".$chaine_complement_title.".\" />";
				}
				elseif($float=="right") {
					$retour.="<div style='float:right; width:16px; margin-right:3px;'><img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"Un ".$tab_tag_notice["indice"][$loop_tag]['nom_tag']." est signalé pour ".$chaine_complement_title.".\" /></div>";
				}
				else {
					$retour.="<div style='float:left; width:16px; margin-left:3px;'><img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"Un ".$tab_tag_notice["indice"][$loop_tag]['nom_tag']." est signalé pour ".$chaine_complement_title.".\" /></div>";
				}
			}
		}
	}

	return $retour;
}

function get_infos_saisie_abs2($id_saisie, $tab=array()) {
	$retour="";

	if($id_saisie!="") {
		$tab=get_tab_saisie_abs2($id_saisie);
	}

	if((isset($tab['debut_abs']))&&(isset($tab['fin_abs']))) {
		if(mb_substr($tab['debut_abs'],0,10)==mb_substr($tab['fin_abs'],0,10)) {
			$retour.="le ".formate_date($tab['debut_abs'], "", "court")." de ".mb_substr($tab['debut_abs'], 11, 5)." à ".mb_substr($tab['fin_abs'], 11, 5);
		}
		else {
			$retour.="du ".formate_date($tab['debut_abs'], "y2", "court")." au ".formate_date($tab['fin_abs'], "y2", "court");
		}
	}

	if((isset($tab['commentaire']))&&($tab['commentaire']!="")) {
		if($retour!="") {
			$retour.=" ";
		}
		$retour.="(".$tab['commentaire'].")";
	}

	return $retour;
}

function get_infos_traitement_abs2($id_traitement) {
	$retour="";

	$tab=get_tab_traitement_abs2($id_traitement);

	if(isset($tab['traitement']['a_type_id'])) {
		$style="";
		if($tab['traitement']['a_type_id']['manquement_obligation_presence']=="1") {
			$style=" style='color:red'";
		}
		$retour.=" <span title=\"".$tab['traitement']['a_type_id']['nom']." : ".$tab['traitement']['a_type_id']['commentaire']."\"".$style.">".$tab['traitement']['a_type_id']['nom']."</span>";
	}

	if(isset($tab['traitement']['a_motif_id'])) {
		if($retour!="") {
			$retour.=" -";
		}
		$retour.=" <span title=\"".$tab['traitement']['a_motif_id']['nom']." : ".$tab['traitement']['a_motif_id']['commentaire'];
		if($tab['traitement']['a_motif_id']['valable']=="y") {
			$retour.="\n(motif valable)";
		}
		else {
			$retour.="\n(motif non valable)";
		}
		$retour.="\">".$tab['traitement']['a_motif_id']['nom']."</span>";
	}

	if(isset($tab['traitement']['a_justification_id'])) {
		if($retour!="") {
			$retour.=" -";
		}
		$retour.=" <span title=\"".$tab['traitement']['a_justification_id']['nom']." : ".$tab['traitement']['a_justification_id']['commentaire']."\">".$tab['traitement']['a_justification_id']['nom']."</span>";
	}

	if(isset($tab['traitement']['saisies'])) {
		$retour.=" (";
		for($loop=0;$loop<count($tab['traitement']['saisies']);$loop++) {
			if($loop>0) {$retour.=", ";}
			
			$retour.=get_infos_saisie_abs2("", $tab['traitement']['saisies'][$loop]);
		}
		$retour.=")";
	}

	return $retour;
}

function get_liste_classes_eleve($login_ele) {
	$retour="";
	$tmp_tab_clas=get_class_from_ele_login($login_ele);
	if((isset($tmp_tab_clas['liste']))&&($tmp_tab_clas['liste']!='')) {
		$retour=$tmp_tab_clas['liste'];
	}
	return $retour;
}


function liste_checkbox_classes($tab_classes_preselectionnees=array(), $nom_champ='id_classe', $nom_func_js_tout_cocher_decocher='cocher_decocher', $sql="", $nom_func_js_checkbox_change="checkbox_change", $avec_changement='n') {
	$retour="";

	if($sql=="") {
		$sql="SELECT DISTINCT id, classe FROM classes ORDER BY classe;";
	}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$nombreligne=mysqli_num_rows($res);
		$nbcol=3;
		$nb_par_colonne=round($nombreligne/$nbcol);

		$retour.="<table style ='width:100%;'>\n";
		$retour.="<caption class='invisible'>Tableau de choix des classes</caption>\n";
		$retour.="<tr style='text-align:center; vertical-align: top;'>\n";
		$retour.="<td style='text-align:left' >\n";

		$cpt=0;
		while($lig=mysqli_fetch_object($res)) {
			if(($cpt>0)&&(round($cpt/$nb_par_colonne)==$cpt/$nb_par_colonne)){
				$retour.="</td>\n";
				$retour.="<td style='text-align:left' >\n";
			}

			$retour.="<input type='checkbox' name='".$nom_champ."[]' id='".$nom_champ."_$cpt' value='$lig->id' ";
			$retour.="onchange=\"";
			if($avec_changement=='y') {
				$retour.="changement();";
			}
			$retour.=$nom_func_js_checkbox_change."('".$nom_champ."_$cpt')\" ";
			if(in_array($lig->id, $tab_classes_preselectionnees)) {
				$retour.="checked ";
				$temp_style=" style='font-weight: bold;'";
			}
			else {
				$temp_style="";
			}
			$retour.="/><label for='".$nom_champ."_$cpt' title=\"$lig->id\"><span id='texte_".$nom_champ."_$cpt'$temp_style>$lig->classe</span></label><br />\n";

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
			".$nom_func_js_checkbox_change."('".$nom_champ."_'+k);
		}
	}
}
</script>\n";
	}

	return $retour;
}

function affiche_numero_tel_sous_forme_classique($chaine) {
	if($chaine!="") {
		if(mb_substr($chaine,0,3)=="+33") {
			$chaine=preg_replace("/^\+33/", "0", $chaine);
		}
		if(preg_match("/^[0-9]{10}$/", $chaine)) {
			$chaine=mb_substr($chaine,0,2)." ".mb_substr($chaine,2,2)." ".mb_substr($chaine,4,2)." ".mb_substr($chaine,6,2)." ".mb_substr($chaine,8,2);
		}
	}
	return $chaine;
}

function get_info_categorie_aid($indice_aid, $aid_id="", $mode="tableau") {
	global $mysqli, $gepiPath;

	$retour="";
	if($indice_aid!="") {
		$sql="SELECT * FROM aid_config WHERE indice_aid='".$indice_aid."';";
	}
	else {
		$sql="SELECT ac.* FROM aid_config ac, aid a WHERE ac.indice_aid=a.indice_aid AND a.id='".$aid_id."';";
	}
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		if($mode=="tableau") {
			$nom_categorie_aid=$lig->nom;
			if($lig->nom_complet!=$lig->nom) {
				$nom_categorie_aid.=" <em>(".$lig->nom_complet.")</em>";
			}

			if($lig->display_begin==$lig->display_end) {
				$display_periodes="AID en période ".$lig->display_begin." seulement";
			}
			else {
				$display_periodes="Périodes ".$lig->display_begin." à ".$lig->display_end;
			}

			if($lig->type_note=="every") {
				$type_note="Une note par période";
			}
			elseif($lig->type_note=="no") {
				$type_note="Pas de note";
			}
			elseif($lig->type_note=="last") {
				$type_note="Une note en dernière période seulement";
			}
			else {
				$type_note="<span style='color:red'>Notes???</span>";
			}

			$info_bulletin="";
			if($lig->display_bulletin=="y") {
				$info_bulletin="Visible sur les bulletins";
			}

			if($lig->bull_simplifie=="y") {
				if($info_bulletin!="") {
					$info_bulletin.="<br />";
				}
				$info_bulletin.="Visible sur les bulletins simplifiés";
			}

			if($info_bulletin=="") {
				$info_bulletin="<img src='$gepiPath/images/icons/bulletin_16_no.png' class='icone16' alt='Pas sur bulletins' title=\"Cet AID n'apparait pas sur les bulletins.\" />";
			}
			elseif($lig->order_display1=="b") {
				$info_bulletin.=" <em title=\"Au début du bulletin, avant les enseignements.\">(début)</em>";
			}
			else {
				$info_bulletin.=" <em title=\"À la fin du bulletin, après les enseignements.\">(fin)</em>";
			}

			$retour="<table class='boireaus boireaus_alt'>
	<tr>
		<td>".$nom_categorie_aid."</td>
	</tr>
	<tr>
		<td>$display_periodes</td>
	</tr>
	<tr>
		<td>$type_note</td>
	</tr>
	<tr>
		<td>$info_bulletin</td>
	</tr>
</table>";
		}
		else {
			// En ligne?
		}
	}
	return $retour;
}

function liste_classes_pp_prof_suivi($login_user) {
	$retour="";

	$tab=get_tab_prof_suivi("", $login_user);
	for($loop=0;$loop<count($tab);$loop++) {
		if($loop>0) {$retour.=", ";}
		$retour.=get_nom_classe($tab[$loop]);
	}
	return $retour;
}

function ouvre_popup_visu_groupe_visu_aid($avec_balise_script="n") {
	global $gepiPath;

	$retour="";
	if($avec_balise_script!="n") {$retour.="<script type='text/javascript'>";}

	$retour.="
	var fen;
	function ouvre_popup_visu_groupe(id_groupe,id_classe,periode_num) {
		//alert('azerty');
		eval(\"fen=window.open('$gepiPath/groupes/popup.php?id_groupe=\"+id_groupe+\"&id_classe=\"+id_classe+\"&periode_num=\"+periode_num+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}

	function ouvre_popup_visu_aid(id_aid,periode_num) {
		//alert('azerty');
		eval(\"fen=window.open('$gepiPath/aid/popup.php?id_aid=\"+id_aid+\"&periode_num=\"+periode_num+\"','','width=400,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen.focus()',500);
	}";

	if($avec_balise_script!="n") {$retour.="
</script>";}

	return $retour;
}

function lien_valeur_unzipped_max_filesize() {
	global $gepiPath;

	$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;

	if(acces("/gestion/param_gen.php", $_SESSION['statut'])) {
		return "<a href='$gepiPath/gestion/param_gen.php#unzipped_max_filesize' target='_blank' title=\"Modifier la valeur maximale paramétrée.\">".$unzipped_max_filesize." octets <img src='$gepiPath/images/edit16.png' class='icone16' alt='Modifier' /></a>";
	}
	else {
		return $unzipped_max_filesize." octets <img src='$gepiPath/images/icons/ico_question_petit.png' class='icone16' alt='Info' title=\"Valeur qui peut être modifiée en administrateur dans Gestion générale/Configuration générale/\" />";
	}
}


function necessaire_modif_tel_resp_ele() {
	global $tabdiv_infobulle;
	global $gepiPath;

	$retour="";

	$texte_infobulle="<div id='div_modif_tel_resp_ou_ele'></div>";
	$tabdiv_infobulle[]=creer_div_infobulle('div_infobulle_modif_tel_resp_ou_ele', "Modifier/corriger","",$texte_infobulle,"",40,0,'y','y','n','n');
	$retour.="
<script type='text/javascript'>
	function affiche_corrige_tel_resp(pers_id) {
		//alert('plop');
		new Ajax.Updater($('div_modif_tel_resp_ou_ele'), '$gepiPath/gestion/saisie_contact.php?mode=js&pers_id='+pers_id,{method: 'get'});
		afficher_div('div_infobulle_modif_tel_resp_ou_ele', 'y', 10, 10);
	}
	function affiche_corrige_tel_ele(login_ele) {
		new Ajax.Updater($('div_modif_tel_resp_ou_ele'), '$gepiPath/gestion/saisie_contact.php?mode=js&login_ele='+login_ele,{method: 'get'});
		afficher_div('div_infobulle_modif_tel_resp_ou_ele', 'y', 10, 10);
	}

	function valider_correction_tel() {
		csrf_alea=document.getElementById('csrf_alea').value;
		pers_id='';
		if(document.getElementById('pers_id')) {
			pers_id=document.getElementById('pers_id').value;
		}
		login_ele='';
		if(document.getElementById('login_ele')) {
			login_ele=document.getElementById('login_ele').value;
		}

		tel_pers=document.getElementById('tel_pers').value;
		tel_port=document.getElementById('tel_port').value;
		tel_prof=document.getElementById('tel_prof').value;
		mel='';
		if(document.getElementById('mel')) {
			mel=document.getElementById('mel').value;
		}
		email='';
		if(document.getElementById('email')) {
			email=document.getElementById('email').value;
		}

		//new Ajax.Updater($('div_modif_tel_resp_ou_ele'), '$gepiPath/gestion/saisie_contact.php?mode=js&valide_correction_telephone&pers_id='+pers_id,{method: 'get'});
		//afficher_div('div_infobulle_modif_tel_resp_ou_ele', 'y', 10, 10);

		new Ajax.Updater($('div_modif_tel_resp_ou_ele'),'$gepiPath/gestion/saisie_contact.php',{method: 'post',
		parameters: {
			mode: 'js',
			valide_correction_telephone: 'y',
			pers_id: pers_id,
			login_ele: login_ele,
			tel_pers: tel_pers,
			tel_port: tel_port,
			tel_prof: tel_prof,
			mel: mel,
			email: email,
			csrf_alea: csrf_alea
		}});

	}
</script>";

	return $retour;
}

function choix_elements_programmes() {
	global $mysqli;
	global $id_groupe;
	global $periode_cn;

	// Récupérer la matière et le cycle du groupe (premier cycle trouvé pour un des élèves du groupe).
	$matiere_grp="";
	$sql="SELECT m.nom_complet FROM matieres m, j_groupes_matieres jgm WHERE m.matiere=jgm.id_matiere AND jgm.id_groupe='$id_groupe';";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$matiere_grp=mb_strtolower(remplace_accents($lig->nom_complet,"all"));
	}
	//echo "matiere_grp=$matiere_grp";

	$cycle_grp=4;
	$sql="select distinct mef_code, count(mef_code) from eleves e, j_eleves_groupes jeg where jeg.login=e.login and jeg.id_groupe='$id_groupe' group by mef_code order by count(mef_code) desc;";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);

		$tab_cycle=calcule_cycle_et_niveau($lig->mef_code, "4");

		$cycle_grp=$tab_cycle["mef_cycle"];
	}

	if($cycle_grp==3) {
		$checked_cycle_3=" checked";
		$checked_cycle_4="";
	}
	else {
		$checked_cycle_3="";
		$checked_cycle_4=" checked";
	}

	$retour="";

	$tab_matieres=array();
	$sql="SELECT DISTINCT matiere FROM elements_programmes ORDER BY matiere;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res)) {
		$tab_matieres[]=$lig->matiere;
	}

	$tab_item=array();
	$sql="SELECT * FROM elements_programmes ORDER BY matiere, cycle, rubrique, item, resume;";
	$res=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_item[]=$lig;
	}

	$retour.="<form action='".$_SERVER["PHP_SELF"]."' method='post'>
		<fieldset class='fieldset_opacite50'>
			<input type='hidden' name='champ_dans_lequel_inserer_texte_element_programme' id='champ_dans_lequel_inserer_texte_element_programme' value='' />
			<table>
				<tr>
					<td>
						<input type='radio' name='cycle' id='cycle_3' value='3'".$checked_cycle_3." onchange=\"maj_liste_elements_prog_affiches()\" /><label for='cycle_3' id='texte_cycle_3'>Cycle 3</label><br />
						<input type='radio' name='cycle' id='cycle_4' value='4'".$checked_cycle_4." onchange=\"maj_liste_elements_prog_affiches()\" /><label for='cycle_4' id='texte_cycle_4'>Cycle 4</label><br />
						<select name='matiere' id='matiere' onchange=\"maj_liste_elements_prog_affiches()\">";
	for($loop=0;$loop<count($tab_matieres);$loop++) {
		$selected="";
		if(($matiere_grp!="")&&(mb_strtolower(remplace_accents($tab_matieres[$loop],"all"))==$matiere_grp)) {
			$selected=" selected";
		}
		$retour.="
							<option value=\"".$tab_matieres[$loop]."\"".$selected.">".$tab_matieres[$loop]."</option>";
	}
	$retour.="
						</select>
					</td>
					<td style='vertical-align:top'>
						<p>
							<input type='button' id='button_inserer_texte_element_prog' value='Insérer' onclick=\"inserer_texte_element_prog()\" /> le texte suivant&nbsp;:<br />
							<span id='texte_a_inserer' class='fieldset_opacite50'> </span>
						</p>
					</td>
				</tr>
			</table>

			<p>Après avoir choisi ci-dessus, le cycle et la matière, cliquez sur le texte à insérer ci-dessous.</p>

			<table class='boireaus boireaus_alt'>
				<thead>
					<tr>
						<th style='display:none'>Matière</th>
						<th style='display:none'>Cycle</th>
						<th>Rubrique</th>
						<th>Item</th>
						<th>Résumé</th>
					</tr>
				</thead>
				<tbody>";
	for($loop=0;$loop<count($tab_item);$loop++) {
		$retour.="
					<tr id='tr_choix_element_prog_$loop'>
						<td style='display:none' id='td_choix_element_prog_matiere_$loop'>".$tab_item[$loop]["matiere"]."</td>
						<td style='display:none' id='td_choix_element_prog_cycle_$loop'>".$tab_item[$loop]["cycle"]."</td>
						<td onclick=\"document.getElementById('texte_a_inserer').innerHTML=this.innerHTML;document.getElementById('button_inserer_texte_element_prog').focus()\">".$tab_item[$loop]["rubrique"]."</td>
						<td onclick=\"document.getElementById('texte_a_inserer').innerHTML=this.innerHTML;document.getElementById('button_inserer_texte_element_prog').focus()\">".$tab_item[$loop]["item"]."</td>
						<td onclick=\"document.getElementById('texte_a_inserer').innerHTML=this.innerHTML;document.getElementById('button_inserer_texte_element_prog').focus()\">".$tab_item[$loop]["resume"]."</td>
					</tr>";
	}
	$retour.="
				</tbody>
			</table>

			<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTES&nbsp;:</em> Il est possible de cliquer dans n'importe quelle colonne <em>(rubrique, item ou résumé)</em>.<br />
			C'est le texte correspondant qui sera proposé à l'insertion.
			</p>
		</fieldset>
	</form>

	<script type='text/javascript'>
		function inserer_texte_element_prog() {
			id=document.getElementById('champ_dans_lequel_inserer_texte_element_programme').value;
			texte=document.getElementById('texte_a_inserer').innerHTML;
			document.getElementById(id).value=texte;
			cacher_div('choix_elements_programmes');
		}

		function maj_liste_elements_prog_affiches() {
			if(document.getElementById('cycle_3').checked==true) {
				cycle=3;
			}
			else {
				cycle=4;
			}
			matiere=document.getElementById('matiere').options[document.getElementById('matiere').selectedIndex].value;
			for(loop=0;loop<".count($tab_item).";loop++) {
				if((document.getElementById('td_choix_element_prog_cycle_'+loop).innerHTML==cycle)&&
				(document.getElementById('td_choix_element_prog_matiere_'+loop).innerHTML==matiere)) {
					document.getElementById('tr_choix_element_prog_'+loop).style.display='';
				}
				else {
					document.getElementById('tr_choix_element_prog_'+loop).style.display='none';
				}
			}
		}

		maj_liste_elements_prog_affiches();
	</script>";

	return $retour;
}

function affiche_resp_classe($id_classe, $login_ele="") {
	global $mysqli;

	$retour="";

	$tab=get_resp_classe($id_classe, $login_ele);

	//$tab["code"][$lig["code"]][$cpt]
	// Appeler la fonction qui par ajax, qui en direct dans les pages avec pas trop de requêtes



	return $retour;
}

// Créer fonction pour  Affichage des modalités pour un élève et l'utiliser dans modify_eleve.php
function liste_modalites_accompagnement_eleve($login_eleve, $mode="complet", $tab_modalites_accompagnement_eleve=NULL, $tab_restriction_code_accompagnement=NULL) {
	global $tab_modalites_accompagnement;
	$retour="";

	if((!isset($tab_modalites_accompagnement_eleve))||(!is_array($tab_modalites_accompagnement_eleve))) {
		$tab_modalites_accompagnement_eleve=get_tab_modalites_accompagnement_eleve($login_eleve);
	}
	//echo "<pre>";
	//print_r($tab_modalites_accompagnement_eleve);
	//echo "</pre>";
	
	if(isset($tab_modalites_accompagnement_eleve["code"])) {
		if((!is_array($tab_modalites_accompagnement))||(count($tab_modalites_accompagnement)==0)) {
			$tab_modalites_accompagnement=get_tab_modalites_accompagnement();
		}

		$tmp_tab_deja=array();
		foreach($tab_modalites_accompagnement_eleve["code"] as $current_code => $tmp_tab) {

			//echo "<pre>";
			//print_r($tmp_tab);
			//echo "</pre>";

			if(!in_array($current_code, $tmp_tab_deja)) {
				if((!isset($tab_restriction_code_accompagnement))||(!is_array($tab_restriction_code_accompagnement))||in_array($current_code,$tab_restriction_code_accompagnement)) {
					$retour.="<span title=\"".$tab_modalites_accompagnement["code"][$current_code]["libelle"];
					$tmp_tab_commentaires=array();
					for($loop=0;$loop<count($tmp_tab);$loop++) {
						if(isset($tmp_tab[$loop]["commentaire"])) {
							$tmp_commentaire=preg_replace('/"/', "''", trim($tmp_tab[$loop]["commentaire"]));
							if(!in_array($tmp_commentaire, $tmp_tab_commentaires)) {
								$tmp_tab_commentaires[]=$tmp_commentaire;
							}
						}
					}
					$liste_commentaires="";
					for($loop=0;$loop<count($tmp_tab_commentaires);$loop++) {
						$liste_commentaires.="\n- ".$tmp_tab_commentaires[$loop];
					}
					if($liste_commentaires!="") {
						$retour.=" :".$liste_commentaires;
					}
					$retour.="\">".$current_code."</span>";
				}
			}
			$tmp_tab_deja[]=$current_code;
		}
	}
	elseif($mode=="complet") {
		$retour.="Aucune modalité d'accompagnement n'est définie.";
	}

	return $retour;
}

function affiche_tableau_resp_classe($id_classe, $login_ele="") {
	$retour="";
	$tab=get_resp_classe($id_classe, $login_ele);

	if((count($tab['pp'])>0)||(count($tab['cpe'])>0)||(count($tab['suivi_par'])>0)) {
		if(($id_classe=='')&&($login_ele!='')) {
			$id_classe=get_id_classe_ele_d_apres_date($login_ele);
		}

		$retour="<table class='boireaus boireaus_alt' style='font-weight:normal;'>";

		if(count($tab['pp'])>0) {
			$retour.="
	<tr>
		<td>".ucfirst(retourne_denomination_pp($id_classe))."&nbsp;:</td>
		<td>";
			for($loop=0;$loop<count($tab['pp']);$loop++) {
				if($loop>0) {
					$retour.=', ';
				}
				$retour.=affiche_utilisateur($tab['pp'][$loop], $id_classe);
			}
			$retour.="</td>
	</tr>";
		}

		if(count($tab['cpe'])>0) {
			$retour.="
	<tr>
		<td>CPE&nbsp;:</td>
		<td>";
			for($loop=0;$loop<count($tab['cpe']);$loop++) {
				if($loop>0) {
					$retour.=', ';
				}
				$retour.=affiche_utilisateur($tab['cpe'][$loop], $id_classe);
			}
			$retour.="</td>
	</tr>";
		}

		if(count($tab['suivi_par'])>0) {
			$retour.="
	<tr>
		<td>Suivi&nbsp;:</td>
		<td>";
			for($loop=0;$loop<count($tab['suivi_par']);$loop++) {
				if($loop>0) {
					$retour.=', ';
				}
				$retour.=affiche_utilisateur($tab['suivi_par'][$loop], $id_classe);
			}
			$retour.="</td>
	</tr>";
		}

		$retour.="
</table>";
	}

	return $retour;
}

function get_designation_inspection_academique() {
	global $mysqli;

	$designation_inspection_academique=getSettingValue('gepiSchoolInspectionAcademique');
	if(trim($designation_inspection_academique)=='') {
		$designation_inspection_academique="l’Inspection Académique de ";
		if(getSettingValue("gepiSchoolAcademie")!='') {
			$designation_inspection_academique.=getSettingValue("gepiSchoolAcademie");
		}
	}

	return $designation_inspection_academique;
}

function affiche_tableau_annee_anterieure_ele_matiere($login_ele, $matiere) {
	global $gepiPath;

	$retour="";

	if(getSettingAOui('active_annees_anterieures')) {
		$tab=get_tab_annees_anterieures_ele_matiere($login_ele, $matiere);
		if(count($tab)==0) {
			$sql="SELECT annee, num_periode FROM archivage_disciplines ad, 
									eleves e 
								WHERE ad.INE=e.no_gep AND 
									e.login='".$login_ele."' 
								ORDER BY annee, num_periode 
								LIMIT 1;";
			//echo "$sql<br />";
			$test_aa=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test_aa)>0) {
				$retour.="<p>Aucune donnée d'année antérieure n'a été trouvée pour ".get_nom_prenom_eleve($login_ele)." en ".$matiere.",<br />
				mais il y a des données dans d'autres matières&nbsp;:<br /><a href='".$gepiPath."/mod_annees_anterieures/consultation_annee_anterieure.php?logineleve=".$login_ele."&mode=bull_simp' target='_blank'>Voir les archives de bulletins complets</a>.</p>";
			}
			else {
				$retour.="<p>Aucune donnée d'année antérieure n'a été trouvée pour ".get_nom_prenom_eleve($login_ele)." en ".$matiere.".</p>";
			}
		}
		else {
			$retour.="<p><a href='".$gepiPath."/mod_annees_anterieures/consultation_annee_anterieure.php?logineleve=".$login_ele."&mode=bull_simp' target='_blank'>Voir les archives de bulletins complets</a></p>
		<table class='boireaus boireaus_alt' style='font-size:small'>
			<thead>
				<tr>
					<th>Période</th>
					<th>Classe</th>
					<th>Enseignant</th>
					<th>Moy.clas.</th>
					<th>Moyenne</th>
					<th>Appréciation</th>
				</tr>
			</thead>
			<tbody>";
			$annee_precedente='';
			foreach($tab as $key => $current_aa) {
				if($annee_precedente!=$current_aa['annee']) {
					$retour.="
				<tr>
					<th colspan='6'>Année scolaire ".$current_aa['annee']."</th>
				</tr>";
				}
				$retour.="
				<tr>
					<td>".str_replace(' ', '&nbsp;', $current_aa['nom_periode'])."</td>
					<td>".$current_aa['classe']."</td>
					<td>".$current_aa['prof']."</td>
					<td>
						<span style='font-size:x-small'>".$current_aa['moymin']."</span><br />
						".$current_aa['moyclasse']."<br />
						<span style='font-size:x-small'>".$current_aa['moymax']."</span>
					</td>
					<td style='font-weight:bold;'>".$current_aa['note']."</td>
					<td>".nl2br($current_aa['appreciation'])."</td>
				</tr>";
				$annee_precedente=$current_aa['annee'];
			}
			$retour.="
			</tbody>
		</table>";
		}
	}

	return $retour;
}

function tableau_actions_eleve($mode='complet') {
	global $mysqli;

	$terme_mod_action=getSettingValue('terme_mod_action');
	$terme_mod_action_nettoye=str_replace("'", " ", str_replace('"', " ", $terme_mod_action));

	$retour='';
	$remplir_tableau=true;
	if((getSettingAOui('mod_actions_affichage_familles'))&&($_SESSION['statut']=='eleve')) {
		$sql="SELECT maa.*, 
			mai.presence, 
			mai.date_pointage, 
			mai.login_pointage 
		FROM mod_actions_action maa, 
			mod_actions_inscriptions mai 
		WHERE mai.id_action=maa.id AND 
			mai.login_ele='".$_SESSION['login']."' 
		ORDER BY date_action DESC;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			$retour="<p style='color:red'>Vous n'êtes inscrit(e) dans aucun(e) ".$terme_mod_action.".</p>";
			$remplir_tableau=false;
		}
	}
	elseif((getSettingAOui('mod_actions_affichage_familles'))&&($_SESSION['statut']=='responsable')) {
		$sql="SELECT maa.*, 
			mai.presence, 
			mai.date_pointage, 
			mai.login_pointage 
		FROM mod_actions_action maa, 
			mod_actions_inscriptions mai 
		WHERE mai.id_action=maa.id AND 
			mai.login_ele IN (SELECT e.login FROM eleves e, 
									responsables2 r, 
									resp_pers rp 
								WHERE e.ele_id=r.ele_id AND 
									r.pers_id=rp.pers_id AND 
									rp.login='".$_SESSION['login']."')
		ORDER BY date_action DESC;";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			$retour="<p style='color:red'>Aucun des élèves/enfants qui vous sont associés n'est inscrit(e) dans un(e) ".$terme_mod_action.".</p>";
			$remplir_tableau=false;
		}
	}
	elseif($mode=='complet') {
		$retour="<p style='color:red'>Tableau non implémenté pour le statut ".$_SESSION['statut']."</p>";
		$remplir_tableau=false;
	}

	if($remplir_tableau) {
		$tab_actions_categories=get_tab_actions_categories();

		$retour.="
		<table class='boireaus boireaus_alt sortable resizable'>
			<thead>
				<tr>
					<th>Catégorie</th>
					<th>Action</th>
					<th>Description</th>
					<th>Date</th>
					<th>Présence</th>
				</tr>
			</thead>
			<tbody>";
		while($lig=mysqli_fetch_object($res)) {
			$retour.="
				<tr>
					<td title=\"".str_replace('"', "'", $tab_actions_categories[$lig->id_categorie]['description'])."\">".$tab_actions_categories[$lig->id_categorie]['nom']."</td>
					<td>".$lig->nom."</td>
					<td>".nl2br($lig->description)."</td>
					<td>".formate_date($lig->date_action, 'y')."</td>
					<td>";
			if($lig->presence=='y') {
				$retour.="<img src='../images/enabled.png' class='icone20' title=\"Pointé(e) présent le ".formate_date($lig->date_pointage, 'y')." par ".civ_nom_prenom($lig->login_pointage)."\" />";
			}
			elseif($lig->presence=='n') {
				$retour.="<img src='../images/disabled.png' class='icone20' title=\"Pointé(e) absent le ".formate_date($lig->date_pointage, 'y')." par ".civ_nom_prenom($lig->login_pointage)."\" />";
			}
			else {
				$sql="SELECT MAX(date_pointage) AS max_date_pointage FROM mod_actions_inscriptions WHERE id_action='".$lig->id."' AND presence!='';";
				//echo "$sql<br />";
				$res2=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res2)>0) {
					$lig2=mysqli_fetch_object($res2);
					if((!is_null($lig2->max_date_pointage))&&($lig2->max_date_pointage!='1970-01-01 00:00:00')) {
						$retour.="<img src='../images/disabled.png' class='icone20' title=\"Non relevé présent le ".formate_date($lig2->max_date_pointage, 'y')."\" />";
					}
				}
			}
			$retour.="</td>
				</tr>";
		}
		$retour.="
			</tbody>
		</table>";
	}
	return $retour;
}

function insere_lien_insertion_lien_instrumenpoche_dans_ckeditor($titre_xml, $url_xml) {
	global $gepiPath;
	global $niveau_arbo;

	//$fichier_IEP="lib/iep.swf";
	$fichier_IEP="cahier_texte_2/iepjsmax.js";

	//if(!file_exists($gepiPath."/lib/iep.swf")) {
	if(((!isset($niveau_arbo))&&(!file_exists("../".$fichier_IEP)))||
	((isset($niveau_arbo))&&($niveau_arbo==0)&&(!file_exists("./".$fichier_IEP)))||
	((isset($niveau_arbo))&&($niveau_arbo==1)&&(!file_exists("../".$fichier_IEP)))||
	((isset($niveau_arbo))&&($niveau_arbo=='public')&&(!file_exists("../".$fichier_IEP)))||
	((isset($niveau_arbo))&&($niveau_arbo==2)&&(!file_exists("../../".$fichier_IEP)))) {
		return '';
	}
	else {

		// 20210928
		//insert into setting set name='cdt2_instrumenpoche_url_absolues', value='y';
		//insert into setting set name='cdt2_instrumenpoche_url_iep', value='https://serveur/chemin/iep_get_url.html';
		if((getSettingAOui('cdt2_instrumenpoche_url_absolues'))&&(getSettingValue('cdt2_instrumenpoche_url_iep')!='')) {
			if(preg_match('#^\.\./#', $url_xml)) {
				return "<div style='float:right; width:18px;'><a href=\"javascript:insere_lien_instrumenpoche_dans_ckeditor2('".preg_replace("/'/", " ", $titre_xml)."', '".getSettingValue('url_racine_gepi').preg_replace('#^\.\./#', '/', $url_xml)."', '".getSettingValue('cdt2_instrumenpoche_url_iep')."')\" title='Insérer un lien vers le visionneur Instrumenpoche pour ce fichier XML'><img src='../images/up.png' width='18' height='18' alt='Insérer' /></a></div>";
			}
			else {
				return "<div style='float:right; width:18px;'><a href=\"javascript:insere_lien_instrumenpoche_dans_ckeditor2('".preg_replace("/'/", " ", $titre_xml)."', '".$url_xml."', '".getSettingValue('cdt2_instrumenpoche_url_iep')."')\" title='Insérer un lien vers le visionneur Instrumenpoche pour ce fichier XML'><img src='../images/up.png' width='18' height='18' alt='Insérer' /></a></div>";
			}
		}
		else {
			return "<div style='float:right; width:18px;'><a href=\"javascript:insere_lien_instrumenpoche_dans_ckeditor('".preg_replace("/'/", " ", $titre_xml)."', '".$url_xml."')\" title='Insérer un lien vers le visionneur Instrumenpoche pour ce fichier XML'><img src='../images/up.png' width='18' height='18' alt='Insérer' /></a></div>";
		}
	}
}

function retourne_lien_edt2_eleve($ele_login, $ts='') {
	global $mysqli;
	global $tabdiv_infobulle, $tabid_infobulle;

	// Nécessite require("../edt/edt_ics_lib.php"); ?
	// Même pas... c'est index2.php lors de l'appel Ajax qui fait l'accès

	if($ts=='') {
		$ts=time();
	}

	$retour="";

	if((getSettingAOui('autorise_edt_tous'))||
		((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))||
		((getSettingAOui('autorise_edt_eleve'))&&(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')))
	) {

		$sql="SELECT * FROM eleves WHERE login='$ele_login';";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);

			$titre_infobulle="EDT de ".$lig->prenom." ".$lig->nom;
			$texte_infobulle="";
			$tabdiv_infobulle[]=creer_div_infobulle('edt_eleve',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

			$retour="<a href='../edt/index2.php?login_eleve=".$ele_login."&amp;type_affichage=eleve&amp;affichage=semaine&amp;&ts=$ts&mode_infobulle=y&mode=afficher_edt_js&x0=50&y0=40&largeur_edt=500&hauteur_une_heure=60&hauteur_jour=640&sans_semaine_suivante_precedente=y' onclick=\"affiche_edt2_eleve_en_infobulle('".$ele_login."', '".strtr(strtr($lig->prenom." ".$lig->nom, "'", " "), '"', " ")."', '$ts');return false;\" title=\"Emploi du temps de ".$lig->prenom." ".$lig->nom."\" target='_blank'><img src='../images/icons/edt2.png' class='icone16' alt='EDT' /></a>

<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt2_eleve_en_infobulle(login_ele, info_eleve, ts) {
		new Ajax.Updater($('edt_eleve_contenu_corps'),'../edt/index2.php?login_eleve=".$ele_login."&type_affichage=eleve&affichage=semaine&ts='+ts+'&mode_infobulle=y&mode=afficher_edt_js&x0=50&y0=40&largeur_edt=500&hauteur_une_heure=60&hauteur_jour=640&sans_semaine_suivante_precedente=y',{method: 'get'});
		afficher_div('edt_eleve','y',-20,20);
	}
</script>\n";
		}
	}

	return $retour;
}

function insere_lien_recherche_eleve($float="") {
	global $gepiPath, $themessage;

	// Mettre un test sur les droits
	$url_recherche='';
	if((acces("/eleves/recherche.php", $_SESSION['statut']))&&(in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe')))) {
		$url_recherche="/eleves/recherche.php";
		$title_recherche="Effectuer une recherche sur un élève, responsable, utilisateur, matière,...";
		$icon_recherche="chercher.png";
	}
	elseif(acces("/eleves/visu_eleve.php", $_SESSION['statut'])) {
		$url_recherche="/eleves/visu_eleve.php";
		$title_recherche="Effectuer une recherche sur une fiche élève.";
		$icon_recherche="chercher_eleve.png";
	}

	if($url_recherche!='') {
		if($float!="") {
			echo "<div style='float:$float;width:16px;margin:3px;'>";
		}

		echo "<a href='".$gepiPath.$url_recherche."' ";

		if((isset($themessage))&&($themessage!='')) {
			echo "onclick=\"return confirm_abandon (this, change, '$themessage')\"";
		}
		echo " target='_blank' title=\"".$title_recherche."\"><img src='$gepiPath/images/icons/".$icon_recherche."' class='icone16' alt='Recherche' /></a>";

		if($float!="") {
			echo "</div>";
		}
	}
}


/*
MariaDB [gepidev]> select id_definie_periode as id,nom_definie_periode as nom,heuredebut_definie_periode as debut,heurefin_definie_periode as fin, type_creneaux, jour_creneau from edt_creneaux order by heuredebut_definie_periode;
+----+-----+----------+----------+---------------+--------------+
| id | nom | debut    | fin      | type_creneaux | jour_creneau |
+----+-----+----------+----------+---------------+--------------+
|  1 | M1  | 08:00:00 | 08:55:00 | cours         | NULL         |
|  2 | M2  | 08:55:00 | 09:55:00 | cours         | NULL         |
| 31 | P1  | 09:55:00 | 10:10:00 | pause         | NULL         |
|  3 | M3  | 10:10:00 | 11:05:00 | cours         | NULL         |
|  4 | M4  | 11:05:00 | 12:05:00 | cours         | NULL         |
| 33 | R1  | 12:10:00 | 12:45:00 | repas         | NULL         |
| 34 | S0  | 12:45:00 | 13:35:00 | cours         | NULL         |
|  5 | S1  | 13:35:00 | 14:30:00 | cours         | NULL         |
| 39 | P2  | 14:30:00 | 14:45:00 | pause         | NULL         |
|  6 | S2  | 14:45:00 | 15:40:00 | cours         | NULL         |
|  7 | S3  | 15:45:00 | 16:35:00 | cours         | NULL         |
| 38 | S4  | 16:35:00 | 17:29:00 | pause         | NULL         |
+----+-----+----------+----------+---------------+--------------+
12 rows in set (0.001 sec)

MariaDB [gepidev]> 
*/
function avertissement_fin_cours_proche($niveau_arbo=0) {
	global $mysqli;

	$retour='';

	if(getPref($_SESSION['login'], 'avertir_fin_cours_proche', 'n')=='y') {
		$delai=getPref($_SESSION['login'], 'avertir_fin_cours_proche_delai', 120);
		$instant1=strftime('%H:%M:%S');
		$instant2=strftime('%H:%M:%S', time()+$delai);
		$sql="SELECT * FROM edt_creneaux WHERE heuredebut_definie_periode<'".$instant1."' AND heurefin_definie_periode>'".$instant2."'";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$ts=mysql_date_to_unix_timestamp($lig->heurefin_definie_periode);
			if(preg_match('/^[0-9]{1,}$/', $ts)) {

				// AJOUTER UN TEST SUR jour établissement ouvert

				if(time()<$ts-$delai) {

					$avertissement_fin_cours_proche_sound='libreoffice_apert.wav';
					if ($niveau_arbo == "0") {
						$chemin_sound="./sounds/".$avertissement_fin_cours_proche_sound;
					} elseif ($niveau_arbo == "1") {
						$chemin_sound="../sounds/".$avertissement_fin_cours_proche_sound;
					} elseif ($niveau_arbo == "2") {
						$chemin_sound="../../sounds/".$avertissement_fin_cours_proche_sound;
					} elseif ($niveau_arbo == "3") {
						$chemin_sound="../../../sounds/".$avertissement_fin_cours_proche_sound;
					}
					else {
						$chemin_sound="../sounds/".$avertissement_fin_cours_proche_sound;
					}

					if(file_exists($chemin_sound)) {

						$nb_millisecondes=($ts-time()-$delai)*1000;

						$retour ="<audio id='id_avertissement_fin_cours_proche_sound' preload='auto'>
				<source src='".$chemin_sound."' />
			</audio>
			<script type='text/javascript'>
			  function play_avertissement_fin_cours_proche_sound() {
				  if(document.getElementById('id_avertissement_fin_cours_proche_sound')) {
					  document.getElementById('id_avertissement_fin_cours_proche_sound').play();
				  }
			  }

			  setTimeout('play_avertissement_fin_cours_proche_sound()', $nb_millisecondes);
			  //alert('On va lancer une alerte de fin de cours proche dans ".$nb_millisecondes." millisecondes');
			</script>";

					return $retour;
					}
				}
			}
		}
	}
}


function avertissement_fin_cours($niveau_arbo=0) {
	global $mysqli;

	$retour='';

	//saveSetting('avertissement_fin_cours', 'y');

	// Pour tester sur un login particulier, remplacer le % par un login particulier.
	$restreindre_login_test=" AND login_prof LIKE '%'";

	if((getSettingAOui('avertissement_fin_cours'))&&
	($_SESSION['statut']=='professeur')) {
		// Rechercher le cours courant du professeur
		$jour=strftime('%A');
		// DEBUG : Pour tester un jour particulier
		//$jour='mardi';
		$sql="SELECT * FROM edt_cours ec, 
					edt_creneaux ecr 
				WHERE ec.login_prof='".$_SESSION['login']."' AND 
					ec.id_definie_periode=ecr.id_definie_periode AND 
					ec.jour_semaine='".$jour."' AND 
					ecr.heuredebut_definie_periode<'".strftime('%H:%M:%S')."' AND 
					ecr.heurefin_definie_periode>'".strftime('%H:%M:%S')."'".$restreindre_login_test.";";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			$type_semaine_courante='';
			$sql="SELECT * FROM edt_semaines WHERE num_edt_semaine='".strftime('%U')."';";
			//echo "$sql<br />";
			$res2=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res2)>0) {
				$lig2=mysqli_fetch_object($res2);
				$type_semaine_courante=$lig2->type_edt_semaine;
			}

			while($lig=mysqli_fetch_object($res)) {
				if(($lig->id_semaine!=0)&&($lig->id_semaine==$type_semaine_courante)) {
					if($lig->id_groupe!=0) {
						$id_groupe=$lig->id_groupe;
					}
					elseif($lig->id_aid!=0) {
						$id_aid=$lig->id_aid;
						// PB : On peut avoir des niveaux différents pour un AID
					}
					break;
				}
				else {
					if($lig->id_groupe!=0) {
						$id_groupe=$lig->id_groupe;
					}
					elseif($lig->id_aid!=0) {
						$id_aid=$lig->id_aid;
						// PB : On peut avoir des niveaux différents pour un AID
					}
					break;
				}
			}

			if(isset($id_groupe)) {
				if((strftime('%H:%M')>="08:00")&&(strftime('%H:%M')<="08:55")) {
					$ts=mktime(8, 55);
				}
				elseif((strftime('%H:%M')>="10:15")&&(strftime('%H:%M')<="11:05")) {
					$ts=mktime(11, 5);
				}
				elseif((strftime('%H:%M')>="12:40")&&(strftime('%H:%M')<="13:35")) {
					$ts=mktime(13, 35);
				}
				// A CORRIGER 14:36->14:50
				elseif((strftime('%H:%M')>="14:36")&&(strftime('%H:%M')<="15:35")) {
					$ts=mktime(15, 35);
				}
				else {
					$sql="SELECT jgc.id_classe FROM j_groupes_classes jgc, 
										j_eleves_classes jec 
									WHERE jgc.id_groupe='".$id_groupe."' AND 
										jgc.id_classe=jec.id_classe;";
					//echo "$sql<br />";
					$res=mysqli_query($mysqli, $sql);
					if(mysqli_num_rows($res)>0) {
						while($lig=mysqli_fetch_object($res)) {
							$tab=get_niveau_from_classe($lig->id_classe);
							if(count($tab)>0) {
								foreach($tab as $mef_code => $niveau) {
									if($niveau!='') {
										if(preg_match('/^6EME/', $niveau)) {
											if((strftime('%H:%M')>="09:00")&&(strftime('%H:%M')<="09:45")) {
												$ts=mktime(9, 45);
											}
											elseif((strftime('%H:%M')>="11:10")&&(strftime('%H:%M')<="12:05")) {
												$ts=mktime(12, 5);
											}
											elseif((strftime('%H:%M')>="13:35")&&(strftime('%H:%M')<="14:20")) {
												$ts=mktime(14, 20);
											}
											elseif((strftime('%H:%M')>="15:40")&&(strftime('%H:%M')<="16:25")) {
												$ts=mktime(16, 25);
											}
										}
										elseif(preg_match('/^5EME/', $niveau)) {
											if((strftime('%H:%M')>="09:00")&&(strftime('%H:%M')<="09:50")) {
												$ts=mktime(9, 50);
											}
											elseif((strftime('%H:%M')>="11:10")&&(strftime('%H:%M')<="12:00")) {
												$ts=mktime(12, 0);
											}
											elseif((strftime('%H:%M')>="13:35")&&(strftime('%H:%M')<="14:25")) {
												$ts=mktime(14, 25);
											}
											elseif((strftime('%H:%M')>="15:40")&&(strftime('%H:%M')<="16:25")) {
												$ts=mktime(16, 25);
											}
										}
										elseif(preg_match('/^4EME/', $niveau)) {
											if((strftime('%H:%M')>="09:00")&&(strftime('%H:%M')<="09:55")) {
												$ts=mktime(9, 55);
											}
											elseif((strftime('%H:%M')>="11:10")&&(strftime('%H:%M')<="12:05")) {
												$ts=mktime(12, 5);
											}
											elseif((strftime('%H:%M')>="13:35")&&(strftime('%H:%M')<="14:30")) {
												$ts=mktime(14, 30);
											}
											elseif((strftime('%H:%M')>="15:40")&&(strftime('%H:%M')<="16:30")) {
												$ts=mktime(16, 30);
											}
										}
										elseif(preg_match('/^3EME/', $niveau)) {
											if((strftime('%H:%M')>="09:00")&&(strftime('%H:%M')<="10:00")) {
												$ts=mktime(10, 0);
											}
											elseif((strftime('%H:%M')>="11:10")&&(strftime('%H:%M')<="12:00")) {
												$ts=mktime(12, 0);
											}
											elseif((strftime('%H:%M')>="13:35")&&(strftime('%H:%M')<="14:35")) {
												$ts=mktime(14, 35);
											}
											elseif((strftime('%H:%M')>="15:40")&&(strftime('%H:%M')<="16:35")) {
												$ts=mktime(16, 35);
											}
										}
									}
								}
							}
						}
					}
				}

				if(isset($ts)) {
					//echo "ts=$ts soit une fin de cours à ".strftime("%H:%M", $ts).".<br />";

					$nb_millisecondes=($ts-time())*1000;

					// DEBUG : Pour tester:
					//$nb_millisecondes=5000;

					if($nb_millisecondes>0) {

						$avertissement_fin_cours_sound='libreoffice_theetone.wav';
						if ($niveau_arbo == "0") {
							$chemin_sound="./sounds/".$avertissement_fin_cours_sound;
							$chemin_sound_php="./sounds/sound.php";
						} elseif ($niveau_arbo == "1") {
							$chemin_sound="../sounds/".$avertissement_fin_cours_sound;
							$chemin_sound_php="../sounds/sound.php";
						} elseif ($niveau_arbo == "2") {
							$chemin_sound="../../sounds/".$avertissement_fin_cours_sound;
							$chemin_sound_php="../../sounds/sound.php";
						} elseif ($niveau_arbo == "3") {
							$chemin_sound="../../../sounds/".$avertissement_fin_cours_sound;
							$chemin_sound_php="../../../sounds/sound.php";
						}
						else {
							$chemin_sound="../sounds/".$avertissement_fin_cours_sound;
							$chemin_sound_php="../sounds/sound.php";
						}

						if(file_exists($chemin_sound)) {

							//echo "Le fichier son est $chemin_sound<br />";

							$retour ="<audio id='id_avertissement_fin_cours_sound' preload='auto'>
					<source src='".$chemin_sound."' />
				</audio>

				<script type='text/javascript'>
				  function play_avertissement_fin_cours_sound() {

					  if(document.getElementById('id_avertissement_fin_cours_sound')) {
						  //alert('Lancement alerte de fin de cours.');
						  document.getElementById('id_avertissement_fin_cours_sound').play();
						  //alert('Après le lancement du son.');
						  //window.open('".$chemin_sound."', 'Fin de cours');
						  //window.open('".$chemin_sound_php."', 'Fin de cours');
					  }

					//var audio_fin_cours = new Audio('".$chemin_sound."');
					//audio_fin_cours.play();
				  }

				  setTimeout('play_avertissement_fin_cours_sound()', $nb_millisecondes);
				  //alert('On va lancer une alerte de fin de cours dans ".$nb_millisecondes." millisecondes');
				</script>";

						return $retour;
						}

					}
				}
			}

		}
	}

}

function insere_visu_eleve($login_eleve, $onglet='', $title='', $float='', $target='') {
	global $gepiPath;
	$retour='';
	$acces_visu_eleve=acces("/eleves/visu_eleve.php", $_SESSION['statut']);
	if($acces_visu_eleve) {
		if($float!='') {
			$retour.="<div style='float:".$float.", width:16px;'>";
		}
		$retour.="<a href='$gepiPath/eleves/visu_eleve.php?ele_login=".$login_eleve;
		if($onglet!='') {
			$retour.="&onglet=".$onglet;
		}
		$retour.="'";
		if($title!='') {
			$retour.=" title=\"".$title."\"";
		}
		$retour.="><img src='$gepiPath/images/icons/ele_onglets.png' class='icone16' alt='Visu' /></a>";
		if($float!='') {
			$retour.="</div>";
		}
	}
	return $retour;
}

// 20210124 A DEPLACER?
function retourne_tag_cdt($id_ct, $type_ct, $float='') {
	global $mysqli, $tab_tag_type, $gepiPath;

	if(!is_array($tab_tag_type)) {
		$tab_tag_type=get_tab_tag_cdt();
	}

	$retour="";

	$tab_tag_notice=get_tab_tag_notice($id_ct, $type_ct);
	if(isset($tab_tag_notice["indice"])) {
		for($loop_tag=0;$loop_tag<count($tab_tag_notice["indice"]);$loop_tag++) {
			if($float!='') {
				$retour.="<div style='float:right; width:16px;'>";
			}
			$retour.=" <img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" />";
			if($float!='') {
				$retour.="</div>";
			}
		}
	}

	return $retour;
}

?>

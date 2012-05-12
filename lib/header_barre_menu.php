<?php

/**
 * Fichier qui permet de construire la barre de menu
 *
 * @copyright 2008-2012
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see acces()
 * @see get_groups_for_prof()
 * @see getSettingValue()
 * @see insert_confirm_abandon()
 * @see menu_plugins()
 * @see nb_saisies_bulletin()
 * @see retourneCours()
 * @todo Proposer aussi les emplois du temps de ses classes
 * @todo Ajouter Paramètres des bulletins et Impression des bulletins (pour les PP)
 */
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

$utiliserMenuBarreLight=((getSettingValue("utiliserMenuBarre") == 'light') || (getPref($_SESSION["login"], "utiliserMenuBarre", "yes") == "light"))?"yes":"no";

//include('./barre_menu_css_js.php');
echo '<!--[if lt IE 7]>
<script type=text/javascript>
	// Fonction destinée à remplacer le "li:hover" pour IE 6
	sfHover = function() {
		var sfEls = document.getElementById("menu_barre").getElementsByTagName("li");
		for (var i=0; i<sfEls.length; i++) {
			sfEls[i].onmouseover = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
				this.className += " sfhover";
			}
			sfEls[i].onmouseout = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
			}
		}
	}
	if (window.attachEvent) window.attachEvent("onload", sfHover);
</script>

<style type="text/css">#menu_barre li {
	width: 164px;
}
</style>
<![endif]-->
';

	$mes_groupes=get_groups_for_prof($_SESSION['login'],NULL,array('classes', 'periodes','visibilite'));
	$tmp_mes_classes=array();
	$tmp_mes_classes_pp=array();
	foreach($mes_groupes as $tmp_group) {
		foreach($tmp_group["classes"]["classes"] as $key_id_classe => $value_tab_classe) {
			if(!in_array($value_tab_classe['classe'], $tmp_mes_classes)) {
				$tmp_mes_classes[$key_id_classe]=$value_tab_classe['classe'];

				$tmp_mes_classes_pp[$key_id_classe]="";
				$sql="SELECT DISTINCT u.nom,u.prenom,u.civilite FROM utilisateurs u, j_eleves_classes jec, j_eleves_professeurs jep WHERE u.login=jep.professeur AND jep.login=jec.login AND jec.id_classe='$key_id_classe' ORDER BY u.nom,u.prenom;";
				$res=mysql_query($sql);
				if(mysql_num_rows($res)>0) {
					while($lig=mysql_fetch_object($res)) {
						if($tmp_mes_classes_pp[$key_id_classe]!='') {
							$tmp_mes_classes_pp[$key_id_classe].=", ";
						}
						$tmp_mes_classes_pp[$key_id_classe].="<span title=\"$lig->civilite $lig->nom $lig->prenom\">".$lig->nom." ".mb_substr($lig->prenom,0,1)."</span>";
					}
				}
			}
		}
	}

	// Pour permettre d'utiliser le module EdT avec les autres modules
	$groupe_abs = $groupe_text = '';
	if (getSettingValue("autorise_edt_tous") == "y") {
		// Actuellement, ce professeur à ce cours (id_cours):
		$cours_actu = retourneCours($_SESSION["login"]);
		// Qui correspond à cet id_groupe :
		if ($cours_actu != "non") {
			$queryG = mysql_query("SELECT id_groupe, id_aid FROM edt_cours WHERE id_cours = '".$cours_actu."'");
			$groupe_actu = mysql_fetch_array($queryG);
			// Il faudrait vérifier si ce n'est pas une AID
			if ($groupe_actu["id_aid"] != NULL) {
				$groupe_abs = '?groupe=AID|'.$groupe_actu["id_aid"].'&amp;menuBar=ok';
				$groupe_text = '';
			}else{
				$groupe_text = '?id_groupe='.$groupe_actu["id_groupe"].'&amp;year='.date("Y").'&amp;month='.date("n").'&amp;day='.date("d").'&amp;edit_devoir=';
				$groupe_abs = '?groupe='.$groupe_actu["id_groupe"].'&amp;menuBar=ok';
			}
		}
	}

	//============================================================================
/* On fixe l'ensemble des modules qui sont ouverts pour faire la liste des <li> */
	// module absence
	if (getSettingValue("active_module_absence_professeur")=='y') {
	    if (getSettingValue("active_module_absence")=='2') {
		$barre_absence = '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs2/index.php'.$groupe_abs.'"'.insert_confirm_abandon().'>Absences</a></li>';
	    } else {
		$barre_absence = '<li class="li_inline"><a href="'.$gepiPath.'/mod_absences/professeurs/prof_ajout_abs.php'.$groupe_abs.'"'.insert_confirm_abandon().'>Absences</a></li>';
	    }
	}else{$barre_absence = '';}

	// Module Cahier de textes
	if (getSettingValue("active_cahiers_texte") == 'y') {
		$barre_textes = '<li class="li_inline">
	<a href="'.$gepiPath.'/cahier_texte/index.php'.$groupe_text.'"'.insert_confirm_abandon().'>C. de Textes</a>'."\n";
		$barre_textes .= '	<ul class="niveau2">'."\n";
			foreach($mes_groupes as $tmp_group) {
				$barre_textes .= '		<li><a href="'.$gepiPath.'/cahier_texte/index.php?id_groupe='.$tmp_group['id'].'&amp;year='.strftime("%Y").'&amp;month='.strftime("%m").'&amp;day='.strftime("%d").'&amp;edit_devoir="'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a></li>'."\n";
			}
			if(getSettingValue('GepiCahierTexteVersion')==2) {
				$barre_textes.= '		<li><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>Consultation des cahiers de textes</a></li>'."\n";
			}
			else {
				$barre_textes.= '		<li><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>Consultation des cahiers de textes</a></li>'."\n";
			}
			$barre_textes.= '		<li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Mes archives CDT</a></li>'."\n";
		$barre_textes .= '	</ul>'."\n";
		$barre_textes .= '</li>'."\n";
	}else{$barre_textes = '';}

	// Module carnet de notes
	if(getSettingValue("active_carnets_notes") == 'y'){
		// Cahiers de notes
		$barre_note = '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/index.php"'.insert_confirm_abandon().'>Notes</a>'."\n";
			$barre_note .= '	<ul class="niveau2">'."\n";
				foreach($mes_groupes as $tmp_group) {
					if((!isset($tmp_group["visibilite"]["cahier_notes"]))||($tmp_group["visibilite"]["cahier_notes"]=='y')) {
						//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3

						if($utiliserMenuBarreLight=="yes") {
							$barre_note.= '		<li><a href="'.$gepiPath.'/cahier_notes/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";
						}
						else {
							$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/cahier_notes/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";

							$barre_note.= '			<ul class="niveau3">'."\n";
							for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
								$barre_note.= '				<li><a href="'.$gepiPath.'/cahier_notes/index.php?id_groupe='.$tmp_group['id'].'&amp;periode_num='.$loop.'"'.insert_confirm_abandon().'>'.$tmp_group["periodes"][$loop]["nom_periode"];
								if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
									$barre_note.=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="Période non verrouillée: Saisie possible" title="Période non verrouillée: Saisie possible" />';
								}
								else {
									$barre_note.=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="Période verrouillée: Saisie impossible" title="Période verrouillée: Saisie impossible" />';
								}
								$barre_note.='</a>';
								$barre_note.='</li>'."\n";
							}
							$barre_note.= '			</ul>'."\n";
						}
						$barre_note.= '		</li>'."\n";
					}
				}

				if((getSettingValue("GepiAccesReleveProf") == "yes") OR
				(getSettingValue("GepiAccesReleveProfTousEleves") == "yes") OR
				(getSettingValue("GepiAccesReleveProfToutesClasses") == "yes")) {
					$barre_note.= '		<li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_bis.php"'.insert_confirm_abandon().'>Relevés de notes</a></li>'."\n";
				}

				if((getSettingValue("GepiAccesMoyennesProf") == "yes") OR
				(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
				(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")) {

					$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/cahier_notes/index2.php"'.insert_confirm_abandon().'>Moyennes des carnets de notes</a>'."\n";
						$barre_note .= '		<ul class="niveau3">'."\n";
						foreach($tmp_mes_classes as $key => $value) {
							$barre_note.= '		<li><a href="'.$gepiPath.'/cahier_notes/index2.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a></li>'."\n";
						}
						$barre_note.= '			</ul>'."\n";
					$barre_note.= '</li>'."\n";
				}

			$barre_note.= '	</ul>'."\n";
		$barre_note.= '</li>'."\n";

		// Bulletins
		$barre_note.='<li class="li_inline"><a href="'.$gepiPath.'/saisie/index.php"'.insert_confirm_abandon().'>Bulletins</a>'."\n";
		$barre_note .= '	<ul class="niveau2">'."\n";
			$barre_note .= '	<li class="plus"><a href="'.$gepiPath.'/saisie/index.php"'.insert_confirm_abandon().'>Notes</a>'."\n";
				$barre_note .= '		<ul class="niveau3">'."\n";
	
				foreach($mes_groupes as $tmp_group) {
					if((!isset($tmp_group["visibilite"]["bulletins"]))||($tmp_group["visibilite"]["bulletins"]=='y')) {
						//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3

						if($utiliserMenuBarreLight=="yes") {
							$barre_note.= '		<li><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";
						}
						else {
							$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";
				
							$barre_note.= '			<ul class="niveau4">'."\n";
							for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
								$barre_note.= '				<li><a href="'.$gepiPath.'/saisie/saisie_notes.php?id_groupe='.$tmp_group['id'].'&amp;periode_cn='.$loop.'"'.insert_confirm_abandon().'>'.$tmp_group["periodes"][$loop]["nom_periode"];
								$barre_note.=' '.nb_saisies_bulletin("notes", $tmp_group["id"], $loop, "couleur");
								if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
									$barre_note.=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="Période non verrouillée: Saisie possible" title="Période non verrouillée: Saisie possible" />';
								}
								else {
									$barre_note.=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="Période verrouillée: Saisie impossible" title="Période verrouillée: Saisie impossible" />';
								}
								$barre_note.='</a>';
								$barre_note.='</li>'."\n";
							}
							$barre_note.= '			</ul>'."\n";
						}
						$barre_note.= '		</li>'."\n";
					}
				}
				$barre_note.= '	</ul>'."\n";
			$barre_note.= '	</li>'."\n";


			$barre_note .= '	<li class="plus"><a href="'.$gepiPath.'/saisie/index.php"'.insert_confirm_abandon().'>Appréciations</a>'."\n";
				$barre_note .= '		<ul class="niveau3">'."\n";
	
				foreach($mes_groupes as $tmp_group) {
					if((!isset($tmp_group["visibilite"]["bulletins"]))||($tmp_group["visibilite"]["bulletins"]=='y')) {
						//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
						if($utiliserMenuBarreLight=="yes") {
							$barre_note.= '		<li><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";
						}
						else {
							$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a>'."\n";
				
							$barre_note.= '			<ul class="niveau4">'."\n";
							for($loop=1;$loop<=count($tmp_group["periodes"]);$loop++) {
								$barre_note.= '				<li><a href="'.$gepiPath.'/saisie/saisie_appreciations.php?id_groupe='.$tmp_group['id'].'&amp;periode_cn='.$loop.'"'.insert_confirm_abandon().'>'.$tmp_group["periodes"][$loop]["nom_periode"];
								$barre_note.=' '.nb_saisies_bulletin("appreciations", $tmp_group["id"], $loop, "couleur");
								if($tmp_group["classe"]["ver_periode"]["all"][$loop]>=2) {
									$barre_note.=' <img src="'.$gepiPath.'/images/edit16.png" width="16" height="16" alt="Période non verrouillée: Saisie possible" title="Période non verrouillée: Saisie possible" />';
								}
								else {
									$barre_note.=' <img src="'.$gepiPath.'/images/icons/securite.png" width="16" height="16" alt="Période verrouillée: Saisie impossible" title="Période verrouillée: Saisie impossible" />';
								}
								$barre_note.='</a>';
								$barre_note.='</li>'."\n";
							}
							$barre_note.= '			</ul>'."\n";
						}
						$barre_note.= '		</li>'."\n";
					}
				}
				$barre_note.= '	</ul>'."\n";
			$barre_note.= '	</li>'."\n";


			$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index1.php"'.insert_confirm_abandon().'>Mes moyennes et appréciations</a>';
				$barre_note .= '		<ul class="niveau3">'."\n";
				foreach($mes_groupes as $tmp_group) {
					if((!isset($tmp_group["visibilite"]["bulletins"]))||($tmp_group["visibilite"]["bulletins"]=='y')) {
						//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index1.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)'.'</a></li>'."\n";
					}
				}
				$barre_note.= '			</ul>'."\n";
			$barre_note.= '		</li>'."\n";


			if((getSettingValue("GepiAccesMoyennesProf") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")) {
				$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Visualiser toutes les moyennes d\'une classe</a>'."\n";
					$barre_note .= '		<ul class="niveau3">'."\n";
					foreach($tmp_mes_classes as $key => $value) {
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index2.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a></li>'."\n";
					}
					$barre_note.= '			</ul>'."\n";
				$barre_note.= '		</li>'."\n";
			}


			// Saisie des avis de conseil de classe
			if((getSettingValue("GepiRubConseilProf") == "yes")&&(is_pp($_SESSION['login']))) {
				$barre_note.= '		<li><a href="'.$gepiPath.'/saisie/saisie_avis.php"'.insert_confirm_abandon().'>Saisie des avis de conseils de classe</a></li>'."\n";
				//$barre_note.= '		</li>'."\n";
			}


			//$affiche_li_bull_simp="n";
			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				//$affiche_li_bull_simp="y";

				$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a>'."\n";
					$barre_note .= '		<ul class="niveau3">'."\n";
					foreach($tmp_mes_classes as $key => $value) {
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index3.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a></li>'."\n";
					}
					$barre_note.= '			</ul>'."\n";
				$barre_note.= '		</li>'."\n";
			}
			elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
				/*
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
											j_eleves_professeurs jep,
											j_eleves_classes jec
										WHERE jep.login=jeg.login AND
												jec.login=jeg.login AND
												jec.periode=jeg.periode AND
												jep.professeur='".$_SESSION['login']."';";
				*/
				$sql="SELECT 1=1 FROM j_eleves_professeurs jep
										WHERE jep.professeur='".$_SESSION['login']."';";
				$res_test_affiche_bull_simp=mysql_num_rows(mysql_query($sql));
				//echo "$sql";
				if($res_test_affiche_bull_simp>0) {

					$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a>'."\n";
						$barre_note .= '		<ul class="niveau3">'."\n";
						foreach($tmp_mes_classes as $key => $value) {
							$sql="SELECT 1=1 FROM j_eleves_professeurs jep,
											j_eleves_classes jec
										WHERE jep.login=jec.login AND
												jec.id_classe='$key' AND
												jep.professeur='".$_SESSION['login']."';";
							$res_test_affiche_bull_simp=mysql_num_rows(mysql_query($sql));
							//echo "$sql";
							if($res_test_affiche_bull_simp>0) {
								$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index3.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a></li>'."\n";
							}
						}
						$barre_note.= '			</ul>'."\n";
					$barre_note.= '		</li>'."\n";

					//$affiche_li_bull_simp="y";
				}
			}

			/*
			if($affiche_li_bull_simp=="y") {
				$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a>'."\n";
					$barre_note .= '		<ul class="niveau3">'."\n";
					foreach($tmp_mes_classes as $key => $value) {
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index3.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a>'."\n";
					}
					$barre_note.= '			</ul>'."\n";
				$barre_note.= '		</li>'."\n";
			}
			*/

			$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().'>Graphes</a>'."\n";
				$barre_note .= '		<ul class="niveau3">'."\n";
				foreach($tmp_mes_classes as $key => $value) {
					$barre_note.= '		<li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a></li>'."\n";
				}
				$barre_note.= '			</ul>'."\n";
			$barre_note.= '		</li>'."\n";


			if((getSettingAOui('AAProfTout'))||(getSettingAOui('AAProfClasses'))||(getSettingAOui('AAProfGroupes'))||
			((getSettingAOui('AAProfPrinc'))&&(is_pp($_SESSION['login'])))) {
				$barre_note .= '	<li><a href="'.$gepiPath.'/mod_annees_anterieures/consultation_annee_anterieure.php"'.insert_confirm_abandon().'>Années antérieures</a>'."</li>\n";
			}


			// Ajouter Paramètres des bulletins et Impression des bulletins (pour les PP)



		$barre_note.= '	</ul>'."\n";
		$barre_note.= '</li>'."\n";
	}else{$barre_note = '';}

	// Module emploi du temps
	if (getSettingValue("autorise_edt_tous") == "y") {

		$barre_edt = '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof"'.insert_confirm_abandon().'>Emploi du tps</a>'."\n";

		$barre_edt .= '   <ul class="niveau2">'."\n";
		$barre_edt .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=classe1"'.insert_confirm_abandon().'>EDT classe</a></li>'."\n";
		$barre_edt .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1"'.insert_confirm_abandon().'>EDT prof</a></li>'."\n";
		$barre_edt .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=salle1"'.insert_confirm_abandon().'>EDT salle</a></li>'."\n";
		$barre_edt .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=eleve1"'.insert_confirm_abandon().'>EDT élève</a></li>'."\n";
		$barre_edt .= '   </ul>'."\n";
		$barre_edt .= '</li>'."\n";

	}else{$barre_edt = '';}

	// Module discipline
	if (getSettingValue("active_mod_discipline")=='y') {
	    $barre_discipline = '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/index.php"'.insert_confirm_abandon().'>Discipline</a></li>'."\n";
	} else {$barre_discipline = '';}

	// Module notanet
	if (getSettingValue("active_notanet") == "y") {
		$barre_notanet = '<li class="li_inline"><a href="'.$gepiPath.'/mod_notanet/index.php"'.insert_confirm_abandon().'>Brevet</a></li>'."\n";
	}else{ $barre_notanet = '';}

	/*
	if (acces('/eleves/visu_eleve.php',$_SESSION['statut'])==1) {
		$barre_consult_eleve = '<li class="li_inline"><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consult.élève</a></li>'."\n";
	}
	else{ $barre_consult_eleve = '';}
	*/
	//=======================================================
	$barre_eleve = '<li class="li_inline"><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Élèves</a>';
	$barre_eleve.= '   <ul class="niveau2">'."\n";

	if (acces('/eleves/visu_eleve.php',$_SESSION['statut'])==1) {
		$barre_eleve.= '      <li><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consult.élève</a></li>'."\n";
	}

	if(getSettingValue('active_module_trombinoscopes')=='y') {
		$barre_eleve.= '      <li class="plus"><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscope</a>';
		$barre_eleve.= '         <ul class="niveau3">'."\n";
		foreach($mes_groupes as $tmp_group) {
			$barre_eleve.= '            <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombino_pdf.php?classe=&amp;groupe='.$tmp_group['id'].'&amp;equipepeda=&amp;discipline=&amp;statusgepi=&amp;affdiscipline="'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<em>'.$tmp_group['classlist_string'].'</em>)</a></li>'."\n";
		}
		$barre_eleve.= '            <li><a href="'.$gepiPath.'/mod_trombinoscopes/plan_de_classe.php"'.insert_confirm_abandon().'>Plan de classe</a></li>'."\n";
		$barre_eleve.= '         </ul>'."\n";
		$barre_eleve.= '      </li>'."\n";
	}

	$barre_eleve.= '      <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Mes listes</a></li>'."\n";
	$barre_eleve.= '      <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Mes listes CSV</a></li>'."\n";
	$barre_eleve.= '      <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Mes listes PDF</a></li>'."\n";

	$barre_eleve.= '		<li class="plus"><a href="'.$gepiPath.'/groupes/visu_profs_class.php"'.insert_confirm_abandon().'>Équipes pédagogiques</a>'."\n";
		$barre_eleve .= '		<ul class="niveau3">'."\n";
		foreach($tmp_mes_classes as $key => $value) {
			$barre_eleve.= '		<li><a href="'.$gepiPath.'/groupes/visu_profs_class.php?id_classe='.$key.'"'.insert_confirm_abandon().' onclick="ouvre_popup_visu_equip('.$key.');return false;">'.$value." <em style='font-size:x-small;'>(".$tmp_mes_classes_pp[$key].")</em>".'</a></li>'."\n";
		}
		$barre_eleve.= '			</ul>'."\n";
	$barre_eleve.= '</li>'."\n";

	$barre_eleve.= '   </ul>'."\n";
	$barre_eleve.= '</li>'."\n";
	//=======================================================

	
// plugins

/**
 * Inclusion des plugins dans la barre des menus
 */
include("menu_plugins.inc.php");
$barre_plugin=menu_plugins();
if ($barre_plugin!="")
	{
	$barre_plugin = "<li class='li_inline'><a href=\"\">Plugins</a>"."\n"
					."	<ul class='niveau2'>\n"
					.$barre_plugin
					."	</ul>\n"
					."</li>\n";
	}
// fin plugins

	echo '<div id="menu_barre">
	<ul class="niveau1">
		<li class="li_inline"><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$barre_absence.'
		'.$barre_textes.'
		'.$barre_note.'
		'.$barre_edt.'
		'.$barre_discipline.'
		'.$barre_notanet.'
		'.$barre_eleve.'
		'.$barre_plugin.'		  
		<li class="li_inline"><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ul>
</div>
';

echo "<script type='text/javascript'>
	var fen_visu_equip;
	function ouvre_popup_visu_equip(id_classe){
		eval(\"fen_visu_equip=window.open('$gepiPath/groupes/visu_profs_class.php?id_classe=\"+id_classe+\"&amp;no_header=y','','width=500,height=400,menubar=yes,scrollbars=yes')\");
		setTimeout('fen_visu_equip.focus()',500);
	}
</script>\n";

?>

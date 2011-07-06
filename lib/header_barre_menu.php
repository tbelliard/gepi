<?php

/**
 * Fichier qui permet de construire la barre de menu
 *
 * @version $Id$
 * @copyright 2008-2011
 */
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

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

	$mes_groupes=get_groups_for_prof($_SESSION['login']);
	$tmp_mes_classes=array();
	foreach($mes_groupes as $tmp_group) {
		foreach($tmp_group["classes"]["classes"] as $key_id_classe => $value_tab_classe) {
			if(!in_array($value_tab_classe['classe'], $tmp_mes_classes)) {
				$tmp_mes_classes[$key_id_classe]=$value_tab_classe['classe'];
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
				$barre_textes .= '		<li><a href="'.$gepiPath.'/cahier_texte/index.php?id_groupe='.$tmp_group['id'].'&amp;year='.strftime("%Y").'&amp;month='.strftime("%m").'&amp;day='.strftime("%d").'&amp;edit_devoir="'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<i>'.$tmp_group['classlist_string'].'</i>)'.'</a></li>'."\n";
			}
			if(getSettingValue('GepiCahierTexteVersion')==2) {
				$barre_textes.= '		<li><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>Consultation des cahiers de textes</a></li>'."\n";
			}
			else {
				$barre_textes.= '		<li><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>Consultation des cahiers de textes</a></li>'."\n";
			}
		$barre_textes .= '	</ul>'."\n";
		$barre_textes .= '</li>'."\n";
	}else{$barre_textes = '';}

	// Module carnet de notes
	if(getSettingValue("active_carnets_notes") == 'y'){
		// Cahiers de notes
		$barre_note = '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/index.php"'.insert_confirm_abandon().'>Notes</a>'."\n";
			$barre_note .= '	<ul class="niveau2">'."\n";
				foreach($mes_groupes as $tmp_group) {
					//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
					$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/cahier_notes/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<i>'.$tmp_group['classlist_string'].'</i>)'.'</a>'."\n";
		
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
					$barre_note.= '		</li>'."\n";
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
							$barre_note.= '		<li><a href="'.$gepiPath.'/cahier_notes/index2.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a>'."\n";
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
					//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
					$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<i>'.$tmp_group['classlist_string'].'</i>)'.'</a>'."\n";
		
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
					$barre_note.= '		</li>'."\n";
				}
				$barre_note.= '	</ul>'."\n";
			$barre_note.= '	</li>'."\n";


			$barre_note .= '	<li class="plus"><a href="'.$gepiPath.'/saisie/index.php"'.insert_confirm_abandon().'>Appréciations</a>'."\n";
				$barre_note .= '		<ul class="niveau3">'."\n";
	
				foreach($mes_groupes as $tmp_group) {
					//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
					$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/saisie/index.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<i>'.$tmp_group['classlist_string'].'</i>)'.'</a>'."\n";
		
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
					$barre_note.= '		</li>'."\n";
				}
				$barre_note.= '	</ul>'."\n";
			$barre_note.= '	</li>'."\n";


			$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index1.php"'.insert_confirm_abandon().'>Mes moyennes et appréciations</a>';
				$barre_note .= '		<ul class="niveau3">'."\n";
				foreach($mes_groupes as $tmp_group) {
					//https://127.0.0.1/steph/gepi-trunk/cahier_notes/index.php?id_groupe=1498&periode_num=3
					$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index1.php?id_groupe='.$tmp_group['id'].'"'.insert_confirm_abandon().'>'.$tmp_group['name'].' (<i>'.$tmp_group['classlist_string'].'</i>)'.'</a>'."\n";
				}
				$barre_note.= '			</ul>'."\n";
			$barre_note.= '		</li>'."\n";


			if((getSettingValue("GepiAccesMoyennesProf") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfTousEleves") == "yes") OR
			(getSettingValue("GepiAccesMoyennesProfToutesClasses") == "yes")) {
				$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Visualiser toutes les moyennes d\'une classe</a>'."\n";
					$barre_note .= '		<ul class="niveau3">'."\n";
					foreach($tmp_mes_classes as $key => $value) {
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index2.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a>'."\n";
					}
					$barre_note.= '			</ul>'."\n";
				$barre_note.= '		</li>'."\n";
			}

			$affiche_li_bull_simp="n";
			if ((getSettingValue("GepiAccesBulletinSimpleProf") == "yes")||(getSettingValue("GepiAccesBulletinSimpleProfTousEleves") == "yes")) {
				$affiche_li_bull_simp="y";
			}
			elseif(getSettingValue("GepiAccesBulletinSimplePP") == "yes") {
				$sql="SELECT 1=1 FROM j_eleves_groupes jeg,
											j_eleves_professeurs jep,
											j_eleves_classes jec
										WHERE jep.login=jeg.login AND
												jec.login=jeg.login AND
												jec.periode=jeg.periode AND
												jep.professeur='".$_SESSION['login']."';";
				$res_test_affiche_bull_simp=mysql_num_rows(mysql_query($sql));
				//echo "$sql";
				if($res_test_affiche_bull_simp>0) {$affiche_li_bull_simp="y";}
			}

			if($affiche_li_bull_simp=="y") {
				$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a>'."\n";
					$barre_note .= '		<ul class="niveau3">'."\n";
					foreach($tmp_mes_classes as $key => $value) {
						$barre_note.= '		<li><a href="'.$gepiPath.'/prepa_conseil/index3.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a>'."\n";
					}
					$barre_note.= '			</ul>'."\n";
				$barre_note.= '		</li>'."\n";
			}


			$barre_note.= '		<li class="plus"><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().'>Graphes</a>'."\n";
				$barre_note .= '		<ul class="niveau3">'."\n";
				foreach($tmp_mes_classes as $key => $value) {
					$barre_note.= '		<li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?id_classe='.$key.'"'.insert_confirm_abandon().'>'.$value.'</a>'."\n";
				}
				$barre_note.= '			</ul>'."\n";
			$barre_note.= '		</li>'."\n";



			// Ajouter Paramètres des bulletins et Impression des bulletins (pour les PP)


		$barre_note.= '	</ul>'."\n";
		$barre_note.= '</li>'."\n";
	}else{$barre_note = '';}

	// Module emploi du temps
	if (getSettingValue("autorise_edt_tous") == "y") {
		$barre_edt = '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof"'.insert_confirm_abandon().'>Emploi du tps</a></li>'."\n";

// edt_organisation/index_edt.php?visioedt=prof1&login_edt=BOIREAUS&type_edt_2=prof
// Proposer aussi les emplois du temps de ses classes

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
	function acces($id,$statut) {
		$tab_id = explode("?",$id);
		$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
		$droit = @mysql_result($query_droits, 0, $statut);
		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	}
	*/
	if (acces('/eleves/visu_eleve.php',$_SESSION['statut'])==1) {
		$barre_consult_eleve = '<li class="li_inline"><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consult.élève</a></li>'."\n";
	}
	else{ $barre_consult_eleve = '';}
	
	
// plugin

$req = "SELECT pl.id, pl.nom, au.fichier FROM plugins pl, plugins_autorisations au
          WHERE pl.id = au.plugin_id
	    AND au.user_statut = '".$_SESSION['statut']."'
	      AND au.auth = 'V'";
$rep = mysql_query($req);

if ($rep) {
  $tmp_sous_menu='	<ul class="niveau2">'."\n";
  $cpt_sous_menu=0;
  while ($item = mysql_fetch_array($rep)) {
    //$tmp_sous_menu[$cpt_sous_menu]=array("lien"=> "/".$item['fichier'] , "texte"=> $item['nom']);
    $cpt_sous_menu++;
    
    $tmp_sous_menu .= '	<li><a href="'.$gepiPath.'/'.$item['fichier'].'">'.$item['nom'].'</a>'."\n";
  }
  $tmp_sous_menu .= '	</ul>'."\n";
  
  if (count($cpt_sous_menu)) {
    // $tbs_menu_prof[$compteur_menu]=array("lien"=> '#' , "texte"=>"Plugin");
    // $tbs_menu_prof[$compteur_menu]['sous_menu']=$tmp_sous_menu;
    // $tbs_menu_prof[$compteur_menu]['niveau_sous_menu']=2;
    // $compteur_menu++;  
    
    $barre_plugin = '<li class="li_inline"><a href="">Plugins</a>'."\n";
    $barre_plugin .= $tmp_sous_menu;
    $barre_plugin .= '</li>'."\n";
    
  } else {
    $barre_plugin = "";
  }
  
}

	
	

	/*
	echo '
	<ol id="essaiMenu">
		<li><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$barre_absence.'
		'.$barre_textes.'
		'.$barre_note.'
		'.$barre_edt.'
		'.$barre_discipline.'
		'.$barre_notanet.'
		'.$barre_consult_eleve.'
		<li><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ol>
	';
	*/
	echo '<div id="menu_barre">
	<ul class="niveau1">
		<li class="li_inline"><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$barre_absence.'
		'.$barre_textes.'
		'.$barre_note.'
		'.$barre_edt.'
		'.$barre_discipline.'
		'.$barre_notanet.'
		'.$barre_consult_eleve.'
		'.$barre_plugin.'		  
		<li class="li_inline"><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ul>
</div>
';
?>
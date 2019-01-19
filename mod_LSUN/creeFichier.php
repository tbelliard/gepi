<?php

/*
*
* Copyright 2016-2018 Régis Bouguin
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 3 of the License, or
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

include_once 'lib/chargeXML.php';

// INSERT INTO setting SET name='LSU_export_historique_heure_deux_points', value='yes';
if(getSettingAOui('LSU_export_historique_heure_deux_points')) {
	$nomFichier = "LSU_".date("d-m-Y_H:i").".xml";
}
else {
	$nomFichier = "LSU_".date("d-m-Y_Hi").".xml";
}

$dirTemp = "../temp/";
$dirTemp .= get_user_temp_directory()."/";


$file = $dirTemp.$nomFichier;

$xml->save($file);

$LSUN_version_xsd=getSettingValue('LSUN_version_xsd');
if($LSUN_version_xsd=='') {
	$LSUN_version_xsd=20180427;
}

if($LSUN_version_xsd==20171009) {
	$schema = "xsd/import-bilan-complet_20171009.xsd";
}
elseif($LSUN_version_xsd==20190427) {
	$schema = "xsd/import-bilan-complet_20190427.xsd";
}
else {
	$schema = "xsd/import-bilan-complet.xsd";
}

// active la gestion d'erreur personnalisée
libxml_use_internal_errors(true);
	?>
<div class="lsun_cadre">
<?php
// Validation du document XML

// Affichage du résultat
if((isset($msg_erreur_remplissage))&&($msg_erreur_remplissage!="")) {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>".$msg_erreur_remplissage."</p>";
}

if((isset($msgErreur))&&($msgErreur!="")) {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>".$msgErreur."</p>";
}

$tmp_tab_periodes_extraites=array();
if((isset($tab_periodes_extraites))&&(count($tab_periodes_extraites)>0)) {
	echo "<p style='color:blue' title=\"L'absence de telle classe/période peut s'expliquer par l'absence d'avis du conseil de classe pour les élèves sur la période en question.\">Des données sont extraites pour les classes/périodes suivantes&nbsp;:<br />";
	foreach($tab_periodes_extraites as $current_classe => $current_tab_periode) {
		echo $current_classe." période(s) ";
		for($loop=0;$loop<count($current_tab_periode);$loop++) {
			if($loop>0) {
				echo ", ";
			}
			echo $current_tab_periode[$loop];
			$tmp_tab_periodes_extraites[]=$current_classe.'|'.$current_tab_periode[$loop];
		}
		echo ".<br />";
	}
	echo "</p>";
}

if((isset($tab_effectifs['parcours']))&&(count($tab_effectifs['parcours'])>0)) {
	echo "<p style='color:blue; margin-top:1em;' title=\"Parcours.\">Des données de <strong>Parcours</strong> sont extraites&nbsp;:<br />";
	//$tab_effectifs['parcours'][$parcoursClasse][$num_periode][$parcours->codeParcours]++;
	/*
	echo "<pre>";
	print_r($tab_effectifs['parcours']);
	echo "</pre>";
	*/
	foreach($tab_effectifs['parcours'] as $current_id_classe => $current_tab_periode) {
		echo "<div style='float:left; width:15em; margin:1em; color:blue;' title=\"Parcours.\">";
		foreach($current_tab_periode as $tmp_num_periode => $current_tab_parcours) {
			echo "<strong>".get_nom_classe($current_id_classe)." en période ".$tmp_num_periode."&nbsp;:</strong><br />";
			foreach($current_tab_parcours as $current_code_parcours => $current_eff) {
				/*
				echo "$current_eff<pre>";
				print_r($current_eff);
				echo "</pre>";
				*/
				echo "<span title=\"Nombre d'élèves avec commentaire de parcours.\">".$current_code_parcours."&nbsp;: ".$current_eff." élèves</span><br />";
			}
		}
		echo "<br />";
		echo "</div>";
	}
	//echo "</p>";
	echo "<div style='clear:both;'></div>";
}

if((isset($tab_effectifs['epi']))&&(count($tab_effectifs['epi'])>0)) {
	//$tab_effectifs['epi']["EPI_GROUPE_".$epiEleve->id_aid]['intitule']
	//$tab_effectifs['epi']["EPI_GROUPE_".$epiEleve->id_aid]['periodes'][$eleve->periode]['classes'][$tab_classe_ele[$eleve->login][$eleve->periode]]['effectif']
	//echo "<p style='color:blue; margin-top:1em;' title=\"Parcours.\">Des données d'<strong>EPI</strong> sont extraites&nbsp;:<br />";
	/*
	echo "<pre>";
	print_r($tab_effectifs['epi']);
	echo "</pre>";
	*/
	// Boucler sur les classes
	$chaine_rapport_EPI='';
	foreach($selectionClasse as $key => $current_id_classe) {
		foreach($tab_effectifs['epi'] as $current_id_epi => $current_EPI) {
			if(isset($current_EPI['periodes'])) {
				foreach($current_EPI['periodes'] as $tmp_num_periode => $current_tab_periode) {
					if(in_array(get_nom_classe($current_id_classe).'|'.$tmp_num_periode, $tmp_tab_periodes_extraites)) {
						if(isset($current_tab_periode['classes'][$current_id_classe]['effectif'])) {
							$chaine_rapport_EPI.="<strong>".get_nom_classe($current_id_classe)." en période ".$tmp_num_periode."&nbsp;:</strong> ".$current_EPI['intitule']."&nbsp;: 
							<span title=\"Nombre d'élèves.\">".$current_tab_periode['classes'][$current_id_classe]['effectif']." élèves</span><br />";
						}
					}
				}
			}
		}
	}
	if($chaine_rapport_EPI!='') {
		echo "<p style='color:blue; margin-top:1em;' title=\"EPI.\">Des données d'<strong>EPI</strong> sont extraites&nbsp;:<br />".$chaine_rapport_EPI."</p>";
	}
}

if((isset($tab_effectifs['ap']))&&(count($tab_effectifs['ap'])>0)) {
	/*
	echo "<pre>";
	print_r($tab_effectifs['ap']);
	echo "</pre>";
	*/
	// Boucler sur les classes
	$chaine_rapport_AP='';
	foreach($selectionClasse as $key => $current_id_classe) {
		foreach($tab_effectifs['ap'] as $current_id_ap => $current_AP) {
			if(isset($current_AP['periodes'])) {
				foreach($current_AP['periodes'] as $tmp_num_periode => $current_tab_periode) {
					if(in_array(get_nom_classe($current_id_classe).'|'.$tmp_num_periode, $tmp_tab_periodes_extraites)) {
						if(isset($current_tab_periode['classes'][$current_id_classe]['effectif'])) {
							$chaine_rapport_AP.="<strong>".get_nom_classe($current_id_classe)." en période ".$tmp_num_periode."&nbsp;:</strong> ".$current_AP['intitule']."&nbsp;: 
							<span title=\"Nombre d'élèves.\">".$current_tab_periode['classes'][$current_id_classe]['effectif']." élèves</span><br />";
						}
					}
				}
			}
		}
	}
	if($chaine_rapport_AP!='') {
		echo "<p style='color:blue; margin-top:1em;' title=\"AP.\">Des données d'<strong>AP</strong> sont extraites&nbsp;:<br />".$chaine_rapport_AP."</p>";
	}
}

if(isset($tab_effectifs['devoirs_faits'])) {
	echo "<p style='color:blue; margin-top:1em;' title=\"Devoirs faits.\">Des données de <strong>Devoirs faits</strong> sont extraites pour ".$tab_effectifs['devoirs_faits']." élève(s).</p>";
}

if(isset($tab_effectifs['accompagnement'])) {
	//$tab_effectifs['accompagnement'][$tab_modalites_accompagnement_eleve[$loop_modalite]["code"]][$eleve->periode]
	echo "<p style='color:blue; margin-top:1em; margin-bottom:1em;' title=\"Modalités d'accompagnement.\">Des données d'<strong>accompagnement</strong> sont extraites&nbsp;: <br />";
	foreach($tab_effectifs['accompagnement'] as $current_code_accompagnement => $tab_periodes) {
		echo "<strong>".$current_code_accompagnement."</strong>&nbsp;: ";
		foreach($tab_periodes as $current_periode => $tmp_effectif) {
			echo "<span title=\"Période ".$current_periode." ".$tmp_effectif." élève(s).\">P".$current_periode."&nbsp;: ".$tmp_effectif." élève(s)</span> - ";
		}
		echo "<br />";
		
	}
	echo "</p>";
}

// A désactiver peut-être pour ne pas faire peur inutilement?
$afficher_liste_absence_EP=false;
if (isset($absenceEP))  {
	echo "<p class='rouge center gras' style='margin-bottom:1em;'>Des élèves n'ont pas d'éléments de programme dans un <em>(ou plusieurs)</em> enseignement(s), vous devez vous assurer que c'est normal <em>(mais ce n'est pas bloquant)</em>.</p>";
	if($afficher_liste_absence_EP) {
		if(isset($liste_absenceEP)) {
			echo $liste_absenceEP;
		}
	}
}

// Activer "user error handling"
//libxml_use_internal_errors(true);

$dom = new DOMDocument("1.0");
$dom->Load($file);

$validate = $dom->schemaValidate($schema);
if ($validate) {
    echo "<p class='vert'>Le fichier $nomFichier semble valide !</p>\n";
} else {
	?>
<p class ="rouge">Le fichier <?php echo $nomFichier; ?> n'est pas valide, vous devez le vérifier et corriger les erreurs.</p>
<?php echo "<p class='rouge'>".libxml_display_errors()."</p>"; ?>
<p>Vous pouvez récupérer le schéma du fichier pour votre validateur en <a href="<?php echo $schema; ?>" target="_BLANK">cliquant ici</a></p>
<?php
}

unset($xml);

?>


<p>
	<a class="bold"  href='../temp/<?php echo $dirTemp ; ?><?php echo $nomFichier; ?>' target='_blank'>
		Récupérer le fichier XML
	</a>
	(<em>effectuer un clic-droit → enregistrer la cible </em>)
</p>
<p class='rouge'>Vous pouvez vérifier votre fichier sur <a href="http://www.xmlvalidation.com/index.php" target="_blank">xmlvalidation.com</a></p>

</div>


<?php
/**
 * Mise à jour de la base lors du passage en UTF-8
 * 
 * $Id:  $
 *
 * 
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Bouguin Régis
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @todo vérifier l'encodage de la base et des tables avant de les réencoder
 */

/* *
define('SET_ORIGINE', 'utf8');
define('SET_DEST', 'latin1');
 /* */
/* */
define('SET_ORIGINE', 'latin1');
define('SET_DEST', 'utf8 COLLATE utf8_general_ci');
 /* */

$result.="<br /><strong>Passage de la base en ".SET_DEST."</strong><br />";

$query = mysql_query("SHOW VARIABLES LIKE 'character_set_%'");
if ($query) {
  while ($row = mysql_fetch_object($query)) {
    $donneesBase[] = $row;
  }
} else {
  die ('Erreur de lecture de la base');
}

foreach ($donneesBase as $donnees) {
/*
 * non modifier
  if (($donnees->Variable_name == 'character_set_client')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_connection')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_results')&&($donnees->Value != SET_DEST)) {
    // non modifier character_set_results
  }
  if (($donnees->Variable_name == 'character_set_server')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
  if (($donnees->Variable_name == 'character_set_system')&&($donnees->Value != SET_DEST)) {
    // non modifier
  }
*/
  if (($donnees->Variable_name == 'character_set_database')&&($donnees->Value != SET_DEST)) {
      $result.=$donnees->Variable_name." est réglé à ".$donnees->Value."<br />";
      $result.="Passage de ".$donnees->Variable_name." à  ".SET_DEST."<br />";
    $queryBase = mysql_query("ALTER DATABASE  CHARACTER SET ".SET_DEST.";");
  }
}
unset ($donnees, $donneesBase);

//debug sur les variables
//$query = mysql_query("SHOW VARIABLES LIKE 'character\_set\_%'");
//if ($query) {
//  while ($row = mysql_fetch_object($query)) {
//    $donneesBase[] = $row;
//  }
//} else {
//  die ('Erreur de lecture de la base');
//}
//foreach ($donneesBase as $donnees) {
//    $result.=msj_present($donnees->Variable_name.' => '.$donnees->Value);
//}
//unset ($donnees);

/* on s'occupe des tables */
$result.="&nbsp;-> Passage des tables en ".SET_DEST."<br />";

//$donneesTable=array();
$query = mysql_query("SHOW table status");
if ($query) {
	while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
        if (mb_substr($row['Collation'],0,6) == 'latin1' ) {
            $donneesTable[] = $row['Name'];
        }
	}
} else {
	die ('Erreur de lecture de la base');
}
// On vérifie que les clés de archivage_ects et de gc_ele_arriv_red ne sont pas trop longues ce qui bloque la conversion
$queryToLong = mysql_query("SHOW COLUMNS FROM gc_ele_arriv_red");
if ($queryToLong) {
	while ($row = mysql_fetch_assoc($queryToLong)) {
		if (mb_substr($row['Field'],0,5) == 'login' ) {
			if (mb_substr($row['Type'],7,4) != '(50)' ) {
				// Le champ login de gc_ele_arriv_red est trop long
				$donneesTable[]='gc_ele_arriv_red';	
				$queryReduit= mysql_query("ALTER TABLE gc_ele_arriv_red MODIFY login VARCHAR(50)");
			}
		}	
	} 
}
$queryToLong = mysql_query("SHOW COLUMNS FROM archivage_ects");
if ($queryToLong) {
	while ($row = mysql_fetch_assoc($queryToLong)) {
		if (mb_substr($row['Field'],0,3) == 'ine' ) {
			if (mb_substr($row['Type'],7,4) != '(55)' ) {
				// Le champ ine de archivage_ects est trop long
				$donneesTable[]='archivage_ects';	
				$queryReduit= mysql_query("ALTER TABLE archivage_ects MODIFY ine VARCHAR(55)");
			}
		}		
	} 
}

if (empty($donneesTable) ){
    $result .= msj_present("Tables déjà encodées en ".SET_DEST);
} else {
    foreach ($donneesTable as $table) {
        $result.="Passage de $table en ".SET_DEST." en cours. ";
    	$querytable = mysql_query('ALTER TABLE '.$table.' CONVERT TO CHARACTER SET '.SET_DEST);
        $querytable = mysql_query('ALTER TABLE '.$table.' CHARACTER SET '.SET_DEST);
        $result.=" Terminé<br />";
    }
    unset ( $table);
    $result .= msj_ok("Migration terminée : Tables encodées en ".SET_DEST);
}

$sql="SELECT 1=1 FROM setting WHERE name='conv_html_mat_cat';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$tab = array_flip (get_html_translation_table(HTML_ENTITIES));
	$sql="SELECT * FROM matieres_categories;";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)>0) {
		$result .= "<br /><p><strong>Test de la présence d'accents HTML dans les noms de catégories de matières</strong><br />\n";
		$nb_corrections_html=0;
		while($lig=mysql_fetch_object($res)) {
			$correction=ensure_utf8(strtr($lig->nom_complet, $tab));
			if($lig->nom_complet!=$correction) {
				$nb_corrections_html++;
				$sql="UPDATE matieres_categories SET nom_complet='$correction' WHERE id='$lig->id';";
				//echo "$sql<br />";
				$update=mysql_query($sql);
				if($update) {
					$result .= msj_ok("Correction de l'encodage d'un nom de catégorie de matière en '$correction'");
				}
				else {
					$result .= msj_erreur("Erreur lors de la correction de l'encodage du nom de catégorie de matière '$lig->nom_complet' en '$correction'");
				}
			}
		}
		if($nb_corrections_html==0) {
			$result .= "Aucune correction de nom de catégorie de matière requise.<br />";
		}
	}

	saveSetting('conv_html_mat_cat','fait');
}

$sql="SELECT 1=1 FROM setting WHERE name='conv_html_fiche_bienvenue_personnels';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$correction=virer_accents_html_setting('Impression');
	if($correction==0) {
		$result .= msj_present("Pas d'accents HTML dans la Fiche Bienvenue des personnels.");
	}
	elseif($correction==1) {
		$result .= msj_ok("Conversion des accents HTML dans la Fiche Bienvenue des personnels effectuée.");
	}
	else {
		$result .= msj_erreur("Erreur lors de la conversion des accents HTML dans la Fiche Bienvenue des personnels.");
	}
	saveSetting('conv_html_fiche_bienvenue_personnels','fait');
}

$sql="SELECT 1=1 FROM setting WHERE name='conv_html_fiche_bienvenue_eleves';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$correction=virer_accents_html_setting('ImpressionFicheEleve');
	if($correction==0) {
		$result .= msj_present("Pas d'accents HTML dans la Fiche Bienvenue élève.");
	}
	elseif($correction==1) {
		$result .= msj_ok("Conversion des accents HTML dans la Fiche Bienvenue élève effectuée.");
	}
	else {
		$result .= msj_erreur("Erreur lors de la conversion des accents HTML dans la Fiche Bienvenue élève.");
	}
	saveSetting('conv_html_fiche_bienvenue_eleves','fait');
}

$sql="SELECT 1=1 FROM setting WHERE name='conv_html_fiche_bienvenue_responsables';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
	$correction=virer_accents_html_setting('ImpressionFicheParent');
	if($correction==0) {
		$result .= msj_present("Pas d'accents HTML dans la Fiche Bienvenue responsable.");
	}
	elseif($correction==1) {
		$result .= msj_ok("Conversion des accents HTML dans la Fiche Bienvenue responsable effectuée.");
	}
	else {
		$result .= msj_erreur("Erreur lors de la conversion des accents HTML dans la Fiche Bienvenue responsable.");
	}
	saveSetting('conv_html_fiche_bienvenue_responsables','fait');
}

?>

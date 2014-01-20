<?php

/**
 * Cette fonction renvoie la liste, éventuellement vide, des plugins obsolètes 
 * @param integer $niveau_arbo niveau du script appelant dans l'arborescence Gepi
 * @return string liste des plugins obsolètes
 */
function verif_version_plugins($niveau_arbo) {
	global $gepiVersion,$gepiPath;
	$dossier_plugins=str_repeat("../",$niveau_arbo)."mod_plugins";
	$liste_plugins_obsoletes="";
	$r_dossier_plugins=opendir($dossier_plugins);
	while($un_dossier=readdir($r_dossier_plugins)) {
		if (is_dir($dossier_plugins."/".$un_dossier) && $un_dossier<>"." && $un_dossier<>".." && file_exists($dossier_plugins."/".$un_dossier."/plugin.xml")) {
			$plugin_xml = simplexml_load_file($dossier_plugins."/".$un_dossier."/plugin.xml");
			if ($plugin_xml->versiongepi != $gepiVersion) $liste_plugins_obsoletes.=" ".$plugin_xml->nom;
			}
		}
	return $liste_plugins_obsoletes;
}

?>
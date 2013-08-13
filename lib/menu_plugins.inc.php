<?php
/**
 * Construction du menu des plugins
 * @version $Id:  $
 * @license GNU/GPL 
 * @package General
 * @subpackage Affichage
 */

/**
 * Construit l'arborescence du menu des Plugins
 * @global sting
 * @return string 
 */
function menu_plugins()
{
	global $gepiPath,$niveau_arbo;
	$menu_plugins="";
	// quels sont les plugins ouverts et autorisés au statut de l'utilisateur?
	$r_sql="SELECT DISTINCT `plugins`.* FROM `plugins`,`plugins_autorisations`
			WHERE (`plugins`.`ouvert`='y' AND `plugins`.`id`=`plugins_autorisations`.`plugin_id` AND `plugins_autorisations`.`user_statut`='".$_SESSION['statut']."')";
	$R_plugins=mysql_query($r_sql);
	if (mysql_num_rows($R_plugins)>0)
		{
		// abréviations statuts
		$t_abr_statuts=array('administrateur'=>'A', 'professeur'=>'P', 'cpe'=>'C', 'scolarite'=>'S', 'secours'=>'sec', 'eleve'=>'E', 'responsable'=>'R', 'autre'=>'autre');
		while ($plugin=mysql_fetch_assoc($R_plugins))
			{
			$plugins_path="";
			if (isset($niveau_arbo)) {
				for ($i=1;$i<=$niveau_arbo;$i++) {$plugins_path.="../";}
			}
			else {$plugins_path="../";}
			$plugins_path.="mod_plugins/";
			$plugin_xml=$plugins_path.$plugin['repertoire']."/plugin.xml";
			// on continue uniquement si le plugin est encore présent
			if (file_exists($plugin_xml))
				{
				$tmp_menu_plugins="";
				// on parcourt la section <administration><menu> de plugin.xml
				$plugin_xml = simplexml_load_file($plugin_xml);
				$nb_items=0;
				$tmp_sous_menu_plugins="";
				foreach($plugin_xml->administration->menu->item as $menu_script)
					{
					$t_autorisations=explode("-",$menu_script->attributes()->autorisation);
					if (in_array($t_abr_statuts[$_SESSION['statut']],$t_autorisations))
						{
						// si la fonction cacul_autorisation_... existe on vérifie si l'utilisateur est autorisé à accéder au script
						$autorise=true; // a priori l'utilisateur a acces à ce script
						$nom_fonction_autorisation = "calcul_autorisation_".$plugin['nom'];
						if (file_exists($plugins_path.$plugin['nom']."/functions_".$plugin['nom'].".php"))
							{
							// on évite de redéclarer la fonction $nom_fonction_autorisation
							if (!function_exists($nom_fonction_autorisation))
								include($plugins_path.$plugin['nom']."/functions_".$plugin['nom'].".php");
							if (function_exists($nom_fonction_autorisation))
								$autorise = $nom_fonction_autorisation($_SESSION['login'],$menu_script);
							}
						if ($autorise)
							{
							$nb_items++;
							$tmp_sous_menu_plugins.="						<li><a href=\"".$gepiPath."/mod_plugins/".$plugin['nom']."/".$menu_script."\" title=\"".$menu_script->attributes()->description."\"".insert_confirm_abandon().">".$menu_script->attributes()->titre."</a></li>\n";
							$tmp_sous_menu_plugins_solo="						<li><a href=\"".$gepiPath."/mod_plugins/".$plugin['nom']."/".$menu_script."\" title=\"".$menu_script->attributes()->description."\"".insert_confirm_abandon().">".$plugin['description']."&nbsp;"."</a></li>\n";
							}
						}
					}
					if ($nb_items>1)
						{
						$tmp_menu_plugins.="				<li class='plus'>\n";
						$tmp_menu_plugins.="					".$plugin['description']."\n";
						$tmp_menu_plugins.="					<ul class='niveau3'>\n";
						$tmp_menu_plugins.=$tmp_sous_menu_plugins."\n";
						$tmp_menu_plugins.="					</ul>\n";
						$tmp_menu_plugins.="				</li>\n";
						}
					else if ($nb_items==1)
						{
							$tmp_menu_plugins.=$tmp_sous_menu_plugins_solo."\n";
						}
					if ($tmp_menu_plugins!="") {$menu_plugins.=$tmp_menu_plugins;}
				}
			}
		}
return $menu_plugins;
}
?>

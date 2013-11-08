<?php
/**
 * Construction de la barre de menu des gabarits
 *
 * @version $Id: tbs_menu_plugins.inc.php 8001 2011-08-26 11:53:47Z jjocal $
 * @copyright 2008-2012
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * 
 */

/**
 * Construit le tableau pour afficher le menu des gabarits
 * 
 * @global string
 * @return array 
 */
function tbs_menu_plugins()
{
	global $gepiPath,$niveau_arbo;
    global $mysqli;
	$menu_plugins=array();
	// quels sont les plugins ouverts et autorisés au statut de l'utilisateur?
	$r_sql="SELECT DISTINCT `plugins`.* FROM `plugins`,`plugins_autorisations`
			WHERE (`plugins`.`ouvert`='y' AND `plugins`.`id`=`plugins_autorisations`.`plugin_id` AND `plugins_autorisations`.`user_statut`='".$_SESSION['statut']."')";
            
        $R_plugins = mysqli_query($mysqli, $r_sql); 
        if ( $R_plugins->num_rows > 0) {
            // abréviations statuts
            $t_abr_statuts=array('administrateur'=>'A', 'professeur'=>'P', 'cpe'=>'C', 'scolarite'=>'S', 'secours'=>'sec', 'eleve'=>'E', 'responsable'=>'R', 'autre'=>'autre');
            while ($plugin = $R_plugins->fetch_object()) {
                $plugins_path="";
                if (isset($niveau_arbo)) {
                    for ($i=1;$i<=$niveau_arbo;$i++) {$plugins_path.="../";}
                }
                else {$plugins_path="../";}
                $plugins_path.="mod_plugins/";
                $plugin_xml=$plugins_path.$plugin->repertoire."/plugin.xml";
                // on continue uniquement si le plugin est encore présent
                if (file_exists($plugin_xml)) {
                    $tmp_menu_plugins=array();
                    // on parcourt la section <administration><menu> de plugin.xml
                    $plugin_xml = simplexml_load_file($plugin_xml);
                    $nb_items=0;
                    $tmp_sous_menu_plugins=array();
                    foreach($plugin_xml->administration->menu->item as $menu_script) {
                        $t_autorisations=explode("-",$menu_script->attributes()->autorisation);
                        if (in_array($t_abr_statuts[$_SESSION['statut']],$t_autorisations)) {
                            // si la fonction cacul_autorisation_... existe on vérifie si l'utilisateur est autorisé à accéder au script
                            $autorise = TRUE; // a priori l'utilisateur a acces à ce script
                            $nom_fonction_autorisation = "calcul_autorisation_".$plugin->nom;
                            if (file_exists($plugins_path.$plugin->nom."/functions_".$plugin->nom.".php")) {
                                // on évite de redéclarer la fonction $nom_fonction_autorisation
                                if (!function_exists($nom_fonction_autorisation))
                                    include($plugins_path.$plugin->nom."/functions_".$plugin->nom.".php");
                                if (function_exists($nom_fonction_autorisation))
                                    $autorise = $nom_fonction_autorisation($_SESSION['login'],$menu_script);
                            }
                            if ($autorise) {
                                $nb_items++;
                                $tmp_sous_menu_plugins[]=array('lien'=>"/mod_plugins/".$plugin->nom."/".$menu_script,'title'=>$menu_script->attributes()->description,'texte'=>$menu_script->attributes()->titre);
                                $tmp_sous_menu_plugins_solo=array('lien'=>"/mod_plugins/".$plugin->nom."/".$menu_script,'title'=>$menu_script->attributes()->description,'texte'=>$plugin['description']);
                            }
                        }
                    }
					if ($nb_items > 1) {
						$tmp_menu_plugins = array('lien'=>"",'texte'=>$plugin->description,'sous_menu'=>$tmp_sous_menu_plugins,'niveau_sous_menu'=>3);
					}
					else if ($nb_items == 1) {
						$tmp_menu_plugins = $tmp_sous_menu_plugins_solo;
					}
					if (count($tmp_menu_plugins) > 0) {$menu_plugins[] = $tmp_menu_plugins;}
                    
                }
            }
        }
		$R_plugins->close();
        
return $menu_plugins;
}
?>

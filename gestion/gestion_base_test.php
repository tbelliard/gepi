<?php
/*
 *
 * Copyright 2001-2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// On précise de ne pas traiter les données avec la fonction anti_inject
if (isset($_POST["action"]) and ($_POST["action"] == 'protect'))  $traite_anti_inject = 'no';

// Initialisations files
require_once("../lib/initialisations.inc.php");

unset($action);
$action = isset($_POST["action"]) ? $_POST["action"] : (isset($_GET["action"]) ? $_GET["action"] : NULL);

$dossier_a_archiver=isset($_POST['dossier']) ? $_POST['dossier'] : (isset($_GET['dossier']) ? $_GET['dossier'] : '');

//debug_var();

// Resume session
$resultat_session = $session_gepi->security_check();
//Décommenter la ligne suivante pour le mode "manuel et bavard"
//$debug="yes";

if (!isset($action) or ($action != "restaure")) {
    if ($resultat_session == 'c') {
        header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
        die();
    } else if ($resultat_session == '0') {
        header("Location: ../logout.php?auto=1");
        die();
    }
}

if (!isset($action) or ($action != "restaure")) {
    if (!checkAccess()) {
        header("Location: ../logout.php?auto=1");
        die();
    }
} else {
	// On s'assure que l'utilisateur qui a initié la restauration était bien
	// un admin !
	if (!isset($_SESSION["tempstatut"])) {
		$_SESSION["tempstatut"] = $_SESSION['statut'];
	}
	if ($_SESSION["tempstatut"] != "administrateur") {
		die();
	}
}


// Initialisation du répertoire actuel de sauvegarde des donnes de test
$dirname = "donnees_test";

// Téléchargement d'un fichier vers backup
if (isset($action) and ($action == 'upload'))  {
	check_token();
    $sav_file = isset($_FILES["sav_file"]) ? $_FILES["sav_file"] : NULL;
    if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] =='')) {
        $msg = "Erreur de téléchargement.";
    } else if (!file_exists($sav_file['tmp_name'])) {
        $msg = "Erreur de téléchargement.";
    } else if (!preg_match('/sql$/',$sav_file['name']) AND !preg_match('/gz$/',$sav_file['name'])){
        $msg = "Erreur : seuls les fichiers ayant l'extension .sql ou .gz sont autorisés.";
    } else {
        $dest = "../backup/".$dirname."/";
        $n = 0;
        if (!deplacer_fichier_upload($sav_file['tmp_name'], "../backup/".$dirname."/data_test.sql")) {
            $msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire backup";
        } else {
            $msg = "Téléchargement réussi.";
        }
    }
}

// Protection du répertoire backup
if (isset($action) and ($action == 'protect'))  {
	check_token();
    include_once("../lib/class.htaccess.php");
    // Instance of the htaccess class
    //$ht = & new htaccess(TRUE);
    $ht = new htaccess(TRUE);
    $user = array();
    // Get the logins from the password file
    $user = $ht->get_htpasswd();
    // Add an Administrator
    if(empty($_POST['pwd1_backup']) || empty($_POST['pwd2_backup'])) {
        $msg = "Problème : les deux mots de passe ne sont pas identiques ou sont vides.";
        $error = 1;
    } elseif ($_POST['pwd1_backup'] != $_POST['pwd2_backup']) {
        $msg = "Problème : les deux mots de passe ne sont pas identiques.";
        $error = 1;
    } elseif (empty($_POST['login_backup'])) {
        $msg = "Problème : l'identifiant est vide.";
        $error = 1;
    } else {
        $_login = my_strtolower(unslashes($_POST['login_backup']));
        if(is_array($user)) {
            foreach($user as $key => $value) {
                if($_login == $key) {
                   $ht->delete_user($_login);
                }
            }
        }
    }
    if(!isset($error)) {
        $ht->set_user($_login, $_POST['pwd1_backup']);
        $ht->set_htpasswd();
        $user = array();
        $user = $ht->get_htpasswd();
        clearstatcache();
        if(!is_file('../backup/'.$dirname.'/.htaccess')) {
            $ht->option['AuthName'] = '"PROTECTION BACKUP"';
            $ht->set_htaccess();
        }
    }
}

// Suppression de la protection
if (isset($action) and ($action == 'del_protect'))  {
	check_token();
   if ((@unlink("../backup/".$dirname."/.htaccess")) and (@unlink("../backup/".$dirname."/.htpasswd"))) {
       $msg = "Les fichiers .htaccess et .htpasswd ont été supprimés. Le répertoire /backup n'est plus protégé\n";
   }
}

function deplacer_fichier_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = @move_uploaded_file($source, $dest);
    return $ok;
}


function test_ecriture_backup() {
    $ok = 'no';
    if ($f = @fopen("../backup/donnees_test/test", "w")) {
        @fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
        @fclose($f);
        include("../backup/donnees_test/test");
        $del = @unlink("../backup/donnees_test/test");
    }
    return $ok;
}

function mysql_version2() {
   $result = mysql_query('SELECT VERSION() AS version');
   if ($result != FALSE && @mysql_num_rows($result) > 0)
   {
      $row = mysql_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else
   {
      $result = @mysql_query('SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && @mysql_num_rows($result) > 0)
      {
         $row = mysql_fetch_row($result);
         $match = explode('.', $row[1]);
      }
   }

   if (!isset($match) || !isset($match[0])) $match[0] = 3;
   if (!isset($match[1])) $match[1] = 21;
   if (!isset($match[2])) $match[2] = 0;
   return $match[0] . "." . $match[1] . "." . $match[2];
}

function init_time() {
    global $TPSDEB,$TPSCOUR;
    list ($usec,$sec)=explode(" ",microtime());
    $TPSDEB=$sec;
    $TPSCOUR=0;
}

function current_time() {
    global $TPSDEB,$TPSCOUR;
    list ($usec,$sec)=explode(" ",microtime());
    $TPSFIN=$sec;
    if (round($TPSFIN-$TPSDEB,1)>=$TPSCOUR+1) //une seconde de plus
    {
    $TPSCOUR=round($TPSFIN-$TPSDEB,1);
    flush();
    }
}

function backupMySql($db,$dumpFile,$duree,$rowlimit) {
    echo $dumpFile;
    global $TPSCOUR,$offsettable,$offsetrow,$cpt,$debug;
    $fileHandle = fopen($dumpFile, "w");
    if(!$fileHandle) {
        echo "Ouverture de $dumpFile impossible<br />\n";
        return FALSE;
    }
    if ($offsettable==0&&$offsetrow==-1){
        $todump ="#**************** BASE DE DONNEES DE TEST ".$db." ****************"."\n"
        .date("\#\ \L\e\ \:\ d\ m\ Y\ \a\ H\h\ i")."\n";
        $todump.="# Serveur : ".$_SERVER['SERVER_NAME']."\n";
        $todump.="# Version PHP : " . phpversion()."\n";
        $todump.="# Version mySQL : " . mysql_version2()."\n";
        $todump.="# IP Client : ".$_SERVER['REMOTE_ADDR']."\n";
        $todump.="# Fichier SQL compatible PHPMyadmin\n#\n";
        $todump.="# ******* debut du fichier ********\n";
        fwrite ($fileHandle,$todump);
    }
    $result=mysql_list_tables($db);
    $numtab=0;
    while ($t = mysql_fetch_array($result)) {
	if ($t[0] == "log" ||
	    $t[0] == "tentatives_intrusion" ||
	    mb_substr($t[0], 0,4) == "temp" ||
	    mb_substr($t[0], 0,3) == "tmp" ||
	    mb_substr($t[0], 0,4) == "a_tm" ||
	    mb_substr($t[0], 0,15) == "modele_bulletin") {
	    
	    continue;
	}
        $tables[$numtab]=$t[0];
        $numtab++;
    }
    if (mysql_error()) {
       echo "<hr />\n<font color='red'>ERREUR lors de la sauvegarde du à un problème dans la la base.</font><br />".mysql_error()."<hr/>\n";
       return false;
       die();
    }

    for (;$offsettable<$numtab;$offsettable++){
        // Dump de la strucutre table
        if ($offsetrow==-1){
            $todump = get_def($db,$tables[$offsettable]);
            if (isset($debug)) echo "<b><br />Dump de la structure de la table ".$tables[$offsettable]."</b><br />\n";
            fwrite($fileHandle,$todump);
            $offsetrow++;
            $cpt++;
        }
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) //on atteint la fin du temps imparti
            return TRUE;
        if (isset($debug)) echo "<b><br />Dump des données de la table ".$tables[$offsettable]."<br /></b>\n";
        $fin=0;
        while (!$fin){
            $todump = get_content($db,$tables[$offsettable],$offsetrow,$rowlimit);
            $rowtodump=substr_count($todump, "INSERT INTO");
            if ($rowtodump>0){
                fwrite ($fileHandle,$todump);
                $cpt+=$rowtodump;
                $offsetrow+=$rowlimit;
                if ($rowtodump<$rowlimit) $fin=1;
                current_time();
                if ($duree>0 and $TPSCOUR>=$duree) {//on atteint la fin du temps imparti
                    if (isset($debug)) echo "<br /><br /><b>Nombre de lignes actuellement dans le fichier : ".$cpt."</b><br />\n";
                    return TRUE;
                }
            } else {
                $fin=1;$offsetrow=-1;
            }
        }
        if (isset($debug)) echo "Pour cette table, nombre de lignes sauvegardées : ".$offsetrow."<br />\n";
        if ($fin) $offsetrow=-1;
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) //on atteint la fin du temps imparti
            return TRUE;
    }
    $offsettable=-1;
    $todump ="#\n";
    $todump.="# ******* Fin du fichier - La sauvegarde s'est terminée normalement ********\n";
    fwrite ($fileHandle,$todump);
    fclose($fileHandle);
    return true;
}

//function restoreMySqlDump($dumpFile,$duree) {
function restoreMySqlDump($duree) {

    return TRUE;
}

function extractMySqlDump($dumpFile,$duree,$force) {
    $fd = fopen($dumpFile, "r");
    while (!feof($fd)) {
	    $query = fgets($fd, 10000);
	    $query = trim($query);
	    //=============================================
	    // MODIF: boireaus 20080218
	    //if (mb_substr($query,-1)==";") {
	    if((mb_substr($query,-1)==";")&&(mb_substr($query,0,3)!="-- ")) {
	    //=============================================
		    $query = "REPLACE" . mb_substr($query,6, mb_strlen($query));
		    $reg = mysql_query($query);
		    echo "<p>$query</p>\n";
		    if (!$reg) {
			echo "<p><font color=red>ERROR</font> : '$query' Erreur retournée : ".mysql_error()."</p>\n";
			$result_ok = 'no';
		    }
	    }
    }
    fclose($fd);
    return TRUE;
}


function debug_pb($ligne) {
	$debug=0;
	if($debug==1) {
		$fich=fopen("/tmp/rest.txt","a+");
		fwrite($fich,$ligne."\n");
		fclose($fich);
	}
}

function get_def($db, $table) {
    $def="\n\n#\n# table $table\n#\n";
    return $def;
}

function get_content($db, $table,$from,$limit) {
    $search       = array("\x00", "\x0a", "\x0d", "\x1a");
    $replace      = array('\0', '\n', '\r', '\Z');
    // les données de la table
    $def = '';
    $query = "SELECT DISTINCT * FROM $table LIMIT $from,$limit";
    $resData = @mysql_query($query);
    //peut survenir avec la corruption d'une table, on prévient
    if (!$resData) {
        $def .="Problème avec les données de $table, corruption possible !\n";
    } else {
        if (@mysql_num_rows($resData) > 0) {
             $sFieldnames = "";
             $num_fields = mysql_num_fields($resData);
              $sInsert = "INSERT INTO $table $sFieldnames values ";
              while($rowdata = mysql_fetch_row($resData)) {
		  if ($table == "utilisateurs" && $rowdata[0] == "ADMIN") {
		      continue;
		  }
                  $lesDonnees = "";
                  for ($mp = 0; $mp < $num_fields; $mp++) {
                  $lesDonnees .= "'" . str_replace($search, $replace, traitement_magic_quotes($rowdata[$mp])) . "'";
                  //on ajoute à la fin une virgule si nécessaire
                      if ($mp<$num_fields-1) $lesDonnees .= ", ";
                  }
                  $lesDonnees = "$sInsert($lesDonnees);\n";
                  $def .="$lesDonnees";
              }
        }
     }
     return $def;
}

// Type de fichier
$filetype = "sql";

// Chemin vers /backup
if (!isset($_GET["path"])) {
    $path="../backup/" . $dirname . "/" ;
}
else {
    $path=$_GET["path"];
}


// Durée d'une portion
if ((isset($_POST['duree'])) and ($_POST['duree'] > 0)) $_SESSION['defaulttimeout'] = $_POST['duree'];
if (getSettingValue("backup_duree_portion") > "4" and !isset($_POST['sauve_duree'])) $_SESSION['defaulttimeout'] = getSettingValue("backup_duree_portion");

if (!isset($_SESSION['defaulttimeout'])) {
    $max_time=min(get_cfg_var("max_execution_time"),get_cfg_var("max_input_time"));
    if ($max_time>20) {
        $_SESSION['defaulttimeout']=$max_time-2;
    } else {
        $_SESSION['defaulttimeout']=20;
    }
}

// Lors d'une sauvegarde, nombre de lignes traitées dans la base entre chaque vérification du temps restant
$defaultrowlimit=10;

//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Sauvegardes/Restauration";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

if (!function_exists("gzwrite")) {
    echo "<h3 class='gepi'>Problème de configuration :</h3>\n";
    echo "<p>Les fonctions de compression 'zlib' ne sont pas activées. Vous devez configurer PHP pour qu'il utilise 'zlib'.</p>\n";
    echo "<p>Vous ne pouvez donc pas accéder aux fonctions de sauvegarde/restauration de GEPI.
    Contactez l'administrateur technique afin de régler ce problème.</p>\n";
    require("../lib/footer.inc.php");
    die();
}

// Confirmation de la restauration
if (isset($action) and ($action == 'restaure_confirm'))  {
	check_token(false);

    echo "<h3>Confirmation de chargement des données de test. <span style='color:red; text-decoration: blink;'>Attention, ne pas faire sur une base de production</span></h3>\n";
    echo "Fichier sélectionné pour la restauration : <b>".$_GET['file']."</b><br/>";
    echo "Attention, les données vont être écrasées, et il y des entrées (<em>tables de jointures</em>) qui seront dupliquées si les contraintes de clés primaires ne sont pas bonnes.\n";
    echo "<p><b>Êtes-vous sûr de vouloir continuer ?</b></p>\n";

	echo "<blockquote>\n";

    echo "<table cellpadding=\"5\" cellspacing=\"5\" border=\"0\" summary='Confirmation'>\n";
    echo "<tr>\n";
    echo "<td>\n";
		echo "<form enctype=\"multipart/form-data\" action=\"gestion_base_test.php\" method=post name=formulaire_oui>\n";
		echo add_token_field();
		echo "<table summary='Oui'>\n";
		echo "<tr>\n";
		echo "<td valign='top'>\n";
		echo "<input type='submit' name='confirm' value = 'Oui' />\n";
		echo "</td>\n";
		echo "<td align='left'>\n";
		echo "<input type=\"hidden\" name=\"debug_restaure\" id=\"debug_restaure\" value=\"y\" />";

		echo "<input type=\"hidden\" name=\"ne_pas_restaurer_log\" id=\"ne_pas_restaurer_log\" value=\"y\" />";

		echo "<input type=\"hidden\" name=\"ne_pas_restaurer_tentatives_intrusion\" id=\"ne_pas_restaurer_tentatives_intrusion\" value=\"y\" />\n";

		echo "</td>\n";
		echo "</table>\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"restaure\" />\n";
		echo "<input type=\"hidden\" name=\"file\" value=\"".$_GET['file']."\" />\n";
		echo "</form>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "<tr>\n";
	echo "<td valign='top' align='left'>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"gestion_base_test.php\" method=post name=formulaire_non>\n";
    echo "<input type='submit' name='confirm' value = 'Non' />\n";
    echo "</form>\n";
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

	echo "</blockquote>\n";

    require("../lib/footer.inc.php");
    die();
}

$succes_etape='n';

// Restauration
if (isset($action) and ($action == 'restaure'))  {
	check_token();
    unset($file);
    $file = isset($_POST["file"]) ? $_POST["file"] : (isset($_GET["file"]) ? $_GET["file"] : NULL);

		$debug_restaure=isset($_POST["debug_restaure"]) ? $_POST["debug_restaure"] : (isset($_GET["debug_restaure"]) ? $_GET["debug_restaure"] : "n");

		$ne_pas_restaurer_log=isset($_POST["ne_pas_restaurer_log"]) ? $_POST["ne_pas_restaurer_log"] : (isset($_GET["ne_pas_restaurer_log"]) ? $_GET["ne_pas_restaurer_log"] : "n");

		$ne_pas_restaurer_tentatives_intrusion=isset($_POST["ne_pas_restaurer_tentatives_intrusion"]) ? $_POST["ne_pas_restaurer_tentatives_intrusion"] : (isset($_GET["ne_pas_restaurer_tentatives_intrusion"]) ? $_GET["ne_pas_restaurer_tentatives_intrusion"] : "n");

		init_time(); //initialise le temps

		//début de fichier
		// En fait d'offset, on compte maintenant des lignes
		if (!isset($_GET["offset"])) {$offset=0;}
		else {$offset=$_GET["offset"];}

		//timeout
		if (!isset($_GET["duree"])) {$duree=$_SESSION['defaulttimeout'];}
			else {$duree=$_GET["duree"];}

		echo "<div align='center'><b>Restauration en cours</b></div>\n";

		$suite_restauration=isset($_GET['suite_restauration']) ? $_GET['suite_restauration'] : NULL;

		if(!isset($suite_restauration)) {
			// EXTRAIRE -> SCINDER
			echo "<p>Extraction de l'archive...<br />";
			if(extractMySqlDump($path.$file,$duree,'y')) {
				$succes_etape="y";
			}

		}

		if($succes_etape!="y") {

			echo "<p style='color:red'>Une erreur s'est produite!<br />";

		} else {
		    echo "<p style='color:green'>Ok<br />";
		}

	require("../lib/footer.inc.php");
	die();
}

$quitter_la_page=isset($_POST['quitter_la_page']) ? $_POST['quitter_la_page'] : (isset($_GET['quitter_la_page']) ? $_GET['quitter_la_page'] : NULL);

// Sauvegarde
if (isset($action) and ($action == 'dump'))  {
	check_token(false);
	// On enregistre le paramètre pour s'en souvenir la prochaine fois
	saveSetting("mode_sauvegarde", "gepi");
	if (isset($_POST['sauve_duree'])) {
		if ($_POST['sauve_duree'] == "yes") {
			saveSetting("backup_duree_portion", $_SESSION['defaulttimeout']);
		}
	}
	// SAuvegarde de la base
    $nomsql = $dbDb."_le_".date("Y_m_d_\a_H\hi");
    $cur_time=date("Y-m-d H:i");
    $filename=$path."data_test.sql";

//    if (!isset($_GET["duree"])&&is_file($filename)){
//        echo "<font color=\"#FF0000\"><center><b>Le fichier existe déjà. Patientez une minute avant de retenter la sauvegarde.</b></center></font>\n<hr />\n";
//    } else {
        init_time(); //initialise le temps
        //début de fichier
        if (!isset($_GET["offsettable"])) $offsettable=0;
            else $offsettable=$_GET["offsettable"];
        //début de fichier
        if (!isset($_GET["offsetrow"])) $offsetrow=-1;
            else $offsetrow=$_GET["offsetrow"];
        //timeout de 30 secondes par défaut, -1 pour utiliser sans timeout
        $duree = 30;
        //Limite de lignes à dumper à chaque fois
        if (!isset($_GET["rowlimit"])) $rowlimit=$defaultrowlimit;
            else  $rowlimit=$_GET["rowlimit"];
         //si le nom du fichier n'est pas en paramètre le mettre ici
         if (!isset($_GET["fichier"])) {
             $fichier=$filename;
         } else $fichier=$_GET["fichier"];


        $tab=mysql_list_tables($dbDb);
        $tot=mysql_num_rows($tab);
        if(isset($offsettable)){
            if ($offsettable>=0)
                $percent=min(100,round(100*$offsettable/$tot,0));
            else $percent=100;
        }
        else $percent=0;

        if ($percent >= 0) {
            $percentwitdh=$percent*4;
            echo "<div align='center'>\n<table width=\"400\" border=\"0\">
            <tr><td width='400' align='center'><b>Sauvegarde en cours</b><br/>
            <br/>A la fin de la sauvegarde, Gepi vous proposera automatiquement de télécharger le fichier.
            <br/><br/>Progression ".$percent."%</td></tr>\n<tr><td>\n<table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table>\n</td></tr>\n</table>\n</div>\n";
        }
        flush();
        if ($offsettable>=0){
            if (backupMySql($dbDb,$fichier,$duree,$rowlimit)) {
                if (isset($debug)) {
					echo "<br />\n<b>Cliquez <a href=\"gestion_base_test.php?action=dump&amp;duree=$duree&amp;rowlimit=$rowlimit&amp;offsetrow=$offsetrow&amp;offsettable=$offsettable&amp;cpt=$cpt&amp;fichier=$fichier&amp;path=$path";
					if(isset($quitter_la_page)) {echo "&amp;quitter_la_page=y";}
					echo add_token_in_url();
					echo "\">ici</a> pour poursuivre la sauvegarde.</b>\n";
				}
                if (!isset($debug)) {
					echo "<br />\n<b>Redirection automatique sinon cliquez <a href=\"gestion_base_test.php?action=dump&amp;duree=$duree&amp;rowlimit=$rowlimit&amp;offsetrow=$offsetrow&amp;offsettable=$offsettable&amp;cpt=$cpt&amp;fichier=$fichier&amp;path=$path";
					if(isset($quitter_la_page)) {echo "&amp;quitter_la_page=y";}
					echo add_token_in_url();
					echo "\">ici</a></b>\n";
				}
                if (!isset($debug)) {
					echo "<script>window.location=\"gestion_base_test.php?action=dump&duree=$duree&rowlimit=$rowlimit&offsetrow=$offsetrow&offsettable=$offsettable&cpt=$cpt&fichier=$fichier&path=$path";
					if(isset($quitter_la_page)) {echo "&quitter_la_page=y";}
					echo add_token_in_url(false);
					echo "\";</script>\n";
				}
                flush();
                exit;
           }
        } else {
			// La sauvegarde est terminée. On compresse le fichier
			//$compress = gzip($fichier, 9);
			//if ($compress) {
			//	$filetype = ".sql.gz";
			//}
			//@unlink($fichier);

            echo "<div align='center'><p>Sauvegarde Terminée.<br />\n";

			//$nomsql.$filetype
			$handle=opendir($path);
			$tab_file = array();
			$n=0;
			while ($file = readdir($handle)) {
				if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
				//=================================
				// AJOUT: boireaus
				and ($file != 'csv')
				and ($file != 'notanet')  //le dossier notanet à ne pas afficher dans la liste
				//=================================
				and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
					$tab_file[] = $file;
					$n++;
				}
			}
			closedir($handle);
			//arsort($tab_file);
			rsort($tab_file);

			//$filepath = null;
			//$filename = null;
			//echo "\$nomsql.$filetype=$nomsql.$filetype<br />";

			$fileid=null;
			if ($n > 0) {
				for($m=0;$m<count($tab_file);$m++){
					//echo "\$tab_file[$m]=$tab_file[$m]<br />";
					if($tab_file[$m]=="$nomsql.$filetype"){
						$fileid=$m;
					}
				}
				clearstatcache();
			}

            //echo "<br/><p class=grand><a href='savebackup.php?filename=$fichier'>Télécharger le fichier généré par la sauvegarde</a></p>\n";
            echo "<br/><br/><a href=\"gestion_base_test.php";
			if(isset($quitter_la_page)) {echo "?quitter_la_page=y";}
			echo "\">Retour vers l'interface de sauvegarde/restauration</a><br /></div>\n";
			require("../lib/footer.inc.php");
            die();
        }

//    }
}

?>

<?php

if(!isset($quitter_la_page)){
	echo "<p class='bold'><a href='index.php#gestion_base_test'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "<p class='bold'><a href=\"javascript:window.self.close();\"";
	echo ">Refermer la page</a>";
}

$handle=opendir('../backup/' . $dirname);
$tab_file = array();
$n=0;
while ($file = readdir($handle)) {
    if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
    //=================================
    // AJOUT: boireaus
    and ($file != 'csv')
	and ($file != 'notanet') //ne pas afficher le dossier notanet
    //=================================
    and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
        $tab_file[] = $file;
        $n++;
    }
}
closedir($handle);
arsort($tab_file);

if ($n > 0) {
    echo "<h3>Fichiers de chargement des données de test</h3>\n";
    //echo "<center>\n<table border=\"1\" cellpadding=\"5\" cellspacing=\"1\">\n<tr><td><b>Nom du fichier de sauvegarde</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
    echo "<center>\n<table class='boireaus' cellpadding=\"5\" cellspacing=\"1\">\n<tr><th><b>Nom du fichier de sauvegarde</b></th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>\n";
    $m = 0;
	$alt=1;
    foreach($tab_file as $value) {
	if ($value == "doc.html") {
	    continue;
	}
        //echo "<tr><td><i>".$value."</i>&nbsp;&nbsp;(". round((filesize("../backup/".$dirname."/".$value)/1024),0)." Ko) </td>\n";
        $alt=$alt*(-1);
		echo "<tr class='lig$alt'><td><i>".$value."</i>&nbsp;&nbsp;(". round((filesize("../backup/".$dirname."/".$value)/1024),0)." Ko) </td>\n";
		if ((my_ereg('^_photos',$value)&&my_ereg('.zip$',$value))||(my_ereg('^_cdt',$value)&&my_ereg('.zip$',$value))){
		   echo "<td> </td>\n";
		} else {
            echo "<td><a href='gestion_base_test.php?action=restaure_confirm&amp;file=$value".add_token_in_url()."'>Charger les données</a></td>\n";
		}
        echo "<td><a href='savebackup.php?fileid=$m'>Télécharger</a></td>\n";
        echo "<td><a href='../backup/".$dirname."/".$value."'>Téléch. direct</a></td>\n";
        echo "</tr>\n";
        $m++;
    }
    clearstatcache();
    echo "</table>\n</center>\n<hr />\n";
}
?>

<H3>Créer un fichier de sauvegarde/restauration de la base de test <?php echo $dbDb; ?></H3>

<!--
<form enctype="multipart/form-data" action="gestion_base_test.php" method=post name=formulaire>
<center><input type="submit" value="Sauvegarder" />
<input type="hidden" name='action' value="dump"/>
</center>
<?php
echo add_token_field();
?>
</form>
-->
Pour activer la sauvegarde des données de tests, merci de décommenter les lignes 737 à 746 du fichier gestion/gestion_base_test.php

<?php
echo "<h3>Documentation de la base de test : </h3>\n";
include("../backup/$dirname/doc.html");

require("../lib/footer.inc.php");
?>

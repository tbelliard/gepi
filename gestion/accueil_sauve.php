<?php
/*
 *
 * Copyright 2001-2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// debug_var();

// Resume session
$resultat_session = $session_gepi->security_check();
//Décommenter la ligne suivante pour le mode "manuel et bavard"
//$debug="yes";

// Désactiver le mode deflate afin que les ob_flush() et flush() fonctionnent
if (function_exists('apache_setenv')) apache_setenv("no-gzip","1");
//apache_setenv("dont-vary","1");

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


// Initialisation du répertoire actuel de sauvegarde
$dirname = getSettingValue("backup_directory");

// Téléchargement d'un fichier vers backup
if (isset($action) and ($action == 'upload'))  {
	check_token();

    $sav_file = isset($_FILES["sav_file"]) ? $_FILES["sav_file"] : NULL;

//echo "\$sav_file['tmp_name']=".$sav_file['tmp_name']."<br />";

    if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] =='')) {
        $msg = "Erreur de téléchargement ".$sav_file['tmp_name'].' '.$dirname;
    } else if (!file_exists($sav_file['tmp_name'])) {
        $msg = "Erreur de téléchargement.".$dirname.' '.$sav_file['tmp_name'];
    } else if (!preg_match('/sql$/',$sav_file['name']) AND !preg_match('/gz$/',$sav_file['name'])){
        $msg = "Erreur : seuls les fichiers ayant l'extension .sql ou .gz sont autorisés.";
    } else {
        $dest = "../backup/".$dirname."/";
        $n = 0;
        $nom_corrige = preg_replace("/[^.a-zA-Z0-9_=-]+/", "_", $sav_file['name']);
        if (!deplacer_fichier_upload($sav_file['tmp_name'], "../backup/".$dirname."/".$nom_corrige)) {
            $msg = "Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire backup";
        } else {
            $msg = "Téléchargement réussi.";
        }
    }
}

// Suppression d'un fichier
if (isset($action) and ($action == 'sup'))  {
	check_token();

	if (isset($_GET['file']) && ($_GET['file']!='')) {
		if((isset($_GET['sous_dossier']))&&($_GET['sous_dossier']=='absences')) {
			if (@unlink("../backup/".$dirname."/absences/".$_GET['file'])) {
				$msg = "Le fichier <b>".$_GET['file']."</b> a été supprimé.<br />\n";
			} else {
				$msg = "Un problème est survenu lors de la tentative de suppression du fichier <b>".$_GET['file']."</b>.<br />
					Il s'agit peut-être un problème de droits sur le répertoire backup.<br />\n";
			}
		}
		else {
			if (@unlink("../backup/".$dirname."/".$_GET['file'])) {
				$msg = "Le fichier <b>".$_GET['file']."</b> a été supprimé.<br />\n";

				if(file_exists("../backup/".$dirname."/".$_GET['file'].".txt")) {
					@unlink("../backup/".$dirname."/".$_GET['file'].".txt");
				}
			} else {
				$msg = "Un problème est survenu lors de la tentative de suppression du fichier <b>".$_GET['file']."</b>.<br />
					Il s'agit peut-être un problème de droits sur le répertoire backup.<br />\n";
			}
		}
	}
}

// 20210419
if(isset($_POST['supprimer_fichiers_coches'])) {
	check_token();

	if(!isset($msg)) {
		$msg='';
	}

	$suppr_fich_svg=isset($_POST['suppr_fich_svg']) ? $_POST['suppr_fich_svg'] : array();

	$cpt_suppr=0;
	foreach($suppr_fich_svg as $key => $fichier) {

		$t=preg_replace('/[A-Za-z0-9_\.-]/', '', $fichier);
		if($t!='') {
			$msg.="Le fichier <strong>".$fichier."</strong> contient des caractères invalides&nbsp;: <strong>".$t."</strong><br />";
		}
		else {
			if (@unlink("../backup/".$dirname."/".$fichier)) {
				//$msg.="Le fichier <b>".$fichier."</b> a été supprimé.<br />\n";
				$cpt_suppr++;

				if(file_exists("../backup/".$dirname."/".$fichier.".txt")) {
					@unlink("../backup/".$dirname."/".$fichier.".txt");
				}
			} else {
				$msg.="Un problème est survenu lors de la tentative de suppression du fichier <b>".$fichier."</b>.<br />
					Il s'agit peut-être un problème de droits sur le répertoire backup.<br />\n";
			}
		}
	}

	if($cpt_suppr==1) {
		$msg.=$cpt_suppr." fichier a été supprimé.<br />";
	}
	elseif($cpt_suppr>1) {
		$msg.=$cpt_suppr." fichiers ont été supprimés.<br />";
	}
}

// Protection du répertoire backup
if (isset($action) and ($action == 'protect'))  {
	check_token();

    include_once("../lib/class.htaccess.php");
    // Instance of the htaccess class
    // $ht = & new htaccess(TRUE);
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

function gzip($src, $level = 5, $dst = false){
    // Pour compresser un fichier existant

    if($dst == false) {
        $dst = $src.".gz";
    }
    if(file_exists($src)){
        $filesize = filesize($src);
        $src_handle = fopen($src, "r");
        if(!file_exists($dst)){
            $dst_handle = gzopen($dst, "w$level");
            while(!feof($src_handle)){
                $chunk = fread($src_handle, 32768);
                gzwrite($dst_handle, $chunk);
            }
            fclose($src_handle);
            gzclose($dst_handle);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function deplacer_fichier_upload($source, $dest) {
    $ok = @copy($source, $dest);
    if (!$ok) $ok = @move_uploaded_file($source, $dest);
    return $ok;
}


function test_ecriture_backup() {
    $ok = 'no';
    if ($f = @fopen("../backup/test", "w")) {
        @fputs($f, '<'.'?php $ok = "yes"; ?'.'>');
        @fclose($f);
        include("../backup/test");
        $del = @unlink("../backup/test");
    }
    return $ok;
}

function mysql_version2() {
   $result = mysqli_query($GLOBALS["mysqli"], 'SELECT VERSION() AS version');
   if ($result != FALSE && @mysqli_num_rows($result) > 0)
   {
      $row = mysqli_fetch_array($result);
      $match = explode('.', $row['version']);
   }
   else
   {
      $result = @mysqli_query($GLOBALS["mysqli"], 'SHOW VARIABLES LIKE \'version\'');
      if ($result != FALSE && @mysqli_num_rows($result) > 0)
      {
         $row = mysqli_fetch_row($result);
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
    global $TPSCOUR,$offsettable,$offsetrow,$cpt,$debug;
    $fileHandle = fopen($dumpFile, "a");
    if(!$fileHandle) {
        echo "Ouverture de $dumpFile impossible<br />\n";
        return FALSE;
    }
    if ($offsettable==0&&$offsetrow==-1){
        $todump ="#**************** BASE DE DONNEES ".$db." ****************"."\n"
        .date("\#\ \L\e\ \:\ d\ m\ Y\ \a\ H\h\ i")."\n";
        $todump.="# Serveur : ".$_SERVER['SERVER_NAME']."\n";
        $todump.="# Version PHP : " . phpversion()."\n";
        $todump.="# Version mySQL : " . mysql_version2()."\n";
        $todump.="# IP Client : ".$_SERVER['REMOTE_ADDR']."\n";
        $todump.="# Fichier SQL compatible PHPMyadmin\n#\n";
        $todump.="# ******* debut du fichier ********\n";
        fwrite ($fileHandle,$todump);
    }
	$sql="SHOW TABLES;";
    $result=mysqli_query($GLOBALS["mysqli"], $sql);
    $numtab=0;
    while ($t = mysqli_fetch_array($result)) {
        $tables[$numtab]=$t[0];
        $numtab++;
    }
    if (mysqli_error($GLOBALS["mysqli"])) {
       echo "<hr />\n<font color='red'>ERREUR lors de la sauvegarde du à un problème dans la la base.</font><br />".mysqli_error($GLOBALS["mysqli"])."<hr/>\n";
       return false;
       die();
    }

    for (;$offsettable<$numtab;$offsettable++){
        // Dump de la strucutre table
        if ($offsetrow==-1){
            $todump = get_def($db,$tables[$offsettable]);
            if (isset($debug)&&$debug!='') echo "<b><br />Dump de la structure de la table ".$tables[$offsettable]."</b><br />\n";
            fwrite($fileHandle,$todump);
            $offsetrow++;
            $cpt++;
        }
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) //on atteint la fin du temps imparti
            return TRUE;
        if (isset($debug)&&$debug!='') echo "<b><br />Dump des données de la table ".$tables[$offsettable]."<br /></b>\n";
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
                    if (isset($debug)&&$debug!='') echo "<br /><br /><b>Nombre de lignes actuellement dans le fichier : ".$cpt."</b><br />\n";
                    return TRUE;
                }
            } else {
                $fin=1;$offsetrow=-1;
            }
        }
        if (isset($debug)&&$debug!='') echo "Pour cette table, nombre de lignes sauvegardées : ".$offsetrow."<br />\n";
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
    return TRUE;
}

//function restoreMySqlDump($dumpFile,$duree) {
function restoreMySqlDump($duree) {
    // $dumpFile, fichier source

	// ON TRANSMET EN FAIT LA VERSION EXTRAITE

    // $duree=timeout pour changement de page (-1 = aucun)

    global $TPSCOUR,$offset,$cpt,$erreur_mysql;
	//global $nom_table;
	//global $table_log_passee;
	global $dirname;
	global $debug_restaure;
	global $effectuer_unlock_tables;

	$sql="SELECT * FROM a_tmp_setting WHERE name LIKE 'table_%' AND value!='log' AND value!='setting' AND value!='utilisateurs' AND value!='a_tmp_setting' ORDER BY name LIMIT 1;";
	if($debug_restaure=='y') {echo "<span style='color:red; font-size: x-small;'>$sql</span><br />\n";}
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);

		$num_table=preg_replace('/^table_/','',$lig->name);
		$nom_table=$lig->value;

		$dumpFile="../backup/".$dirname."/base_extraite_table_".$num_table.".sql";
		if(!file_exists($dumpFile)) {
			echo "$dumpFile non trouvé<br />\n";
			return FALSE;
		}

		$sql="SELECT value FROM a_tmp_setting WHERE name='nb_tables';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$lig=mysqli_fetch_object($res);
		$nb_tables=$lig->value;

		$sql="SELECT 1=1 FROM a_tmp_setting WHERE name LIKE 'table_%';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		$nb_tables_passees=$nb_tables-mysqli_num_rows($res);
		// Ca ne correspond plus à un nombre de tables, mais à un nombre de fichiers

		echo "<p style='text-align:center;'>Fichier ".($nb_tables_passees+1)."/".$nb_tables."</p>\n";

		echo "<p>Traitement de la table <span style='color:green;'>$nom_table</span><br />";

		$fileHandle = gzopen($dumpFile, "rb");

		$cpt_insert=0;

		$formattedQuery = "";
		$old_offset = $offset;
		while(!gzeof($fileHandle)) {
			current_time();
			if ($duree>0 and $TPSCOUR>=$duree) {  //on atteint la fin du temps imparti
				if ($old_offset == $offset) {
					echo "<p class= 'rouge center'><strong>La procédure de restauration ne peut pas continuer.
					<br />Un problème est survenu lors du traitement d'une requête près de :.
					<br />".$debut_req."</strong></p><hr />\n";
					return FALSE;
				}
				$old_offset = $offset;
				return TRUE;
			}

			//echo $TPSCOUR."<br />";
			$buffer=gzgets($fileHandle);
			if (mb_substr($buffer,mb_strlen($buffer),1)==0) {
				$buffer=mb_substr($buffer,0,mb_strlen($buffer)-1);
			}
			//echo $buffer."<br />";

			if(mb_substr($buffer, 0, 1) != "#" AND mb_substr($buffer, 0, 1) != "/") {
				if (!isset($debut_req))  $debut_req = $buffer;
				$formattedQuery .= $buffer;
				//echo $formattedQuery."<hr />";
				if ($formattedQuery) {
					$sql = $formattedQuery;
					if (mysqli_query($GLOBALS["mysqli"], $sql)) {//réussie sinon continue à concaténer
						if(preg_match("/^DROP TABLE /",$sql)) {
							echo "Suppression de la table <span style='color:green;'>$nom_table</span> si elle existe.<br />";
						}
						elseif(preg_match("/^CREATE TABLE /",$sql)) {
							echo "Création de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde.<br />";
						}
						else {
							if($cpt_insert==0) {
								//echo "<div style='width:100%'>";
								echo "Restauration des enregistrements de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde: ";
							}
							else {
								echo "<span style='font-size: xx-small;'>. </span>";
							}
							$cpt_insert++;
						}
						flush();

						debug_pb($sql);

						$offset=gztell($fileHandle);
						//echo $offset;
						$formattedQuery = "";
						unset($debut_req);
						$cpt++;
						//echo $cpt;
					}
				}
			}
		}

		if($cpt_insert>0) {
			echo "<br />";
			echo "$cpt_insert enregistrement(s) restauré(s).<br />";
		}

		if (mysqli_error($GLOBALS["mysqli"])) {
			echo "<hr />\nERREUR à partir de ".nl2br($formattedQuery)." <br />".mysqli_error($GLOBALS["mysqli"])."<hr />\n";
			$erreur_mysql=TRUE;
		}
		gzclose($fileHandle);

		$sql="LOCK TABLES a_tmp_setting WRITE;";
		$lock=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="DELETE FROM a_tmp_setting WHERE name='table_".$num_table."';";
		if($debug_restaure=='y') {
			$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
			if($nettoyage) {
				echo "Succès de la suppression dans a_tmp_setting.<br />\n";
			}
			else {
				echo "<p style='color:red;'>Erreur lors de la suppression dans 'a_tmp_setting': $sql</p>\n";
				if (mysqli_error($GLOBALS["mysqli"])) {
					echo "ERREUR: ".mysqli_error($GLOBALS["mysqli"])."<hr />\n";
					$erreur_mysql=TRUE;
				}
			}

			if(unlink($dumpFile)) {
				echo "Succès de la suppression de $dumpFile.<br />";
			}
			else {
				echo "<p style='color:red;'>Erreur lors de la suppression de $dumpFile.</p>\n";
			}
		}
		else {
			$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);
			if(!$nettoyage) {
				echo "<p style='color:red;'>Erreur lors de la suppression dans 'a_tmp_setting'.</p>\n";
			}

			if(!unlink($dumpFile)) {
				echo "<p style='color:red;'>Erreur lors de la suppression de $dumpFile.</p>\n";
			}
		}

	}
	else {
		// Il ne reste que les tables log, setting et utilisateurs à restaurer

		$tab_tables=array("setting","utilisateurs","log");

		for($i=0;$i<count($tab_tables);$i++) {

			$sql="SELECT * FROM a_tmp_setting WHERE name LIKE 'table_%' AND value='".$tab_tables[$i]."';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				// On peut avoir plusieurs enregistrements pour une même table s'il y a plus de 1000 enregistrements dans la table
				// Ou alors, il ne faut pas scinder ces tables
				while($lig=mysqli_fetch_object($res)) {
					$num_table=preg_replace('/^table_/','',$lig->name);
					$nom_table=$lig->value;
	
					$dumpFile="../backup/".$dirname."/base_extraite_table_".$num_table.".sql";
					if(!file_exists($dumpFile)) {
						echo "$dumpFile non trouvé<br />\n";
						return FALSE;
					}
	
					echo "<p>Traitement de la table <span style='color:green;'>$nom_table</span><br />";

					$test_autre_mode_lecture="n";
					if($test_autre_mode_lecture=="y") {

						$fileHandle = fopen($dumpFile, "r");
	
						$cpt_insert=0;
	
						$formattedQuery = "";
						$old_offset = $offset;
						while(!feof($fileHandle)) {
							current_time();
							if ($duree>0 and $TPSCOUR>=$duree) {  //on atteint la fin du temps imparti
								if ($old_offset == $offset) {
									echo "<p class=\"rouge center\"><strong>La procédure de restauration ne peut pas continuer.
									<br />Un problème est survenu lors du traitement d'une requête près de :.
									<br />".$debut_req."</strong></p><hr />\n";
									return FALSE;
								}
								$old_offset = $offset;
								return TRUE;
							}
	
							//echo $TPSCOUR."<br />";
							//$buffer=gzgets($fileHandle);
							$buffer=fgets($fileHandle);
							if (mb_substr($buffer,mb_strlen($buffer),1)==0) {
								$buffer=mb_substr($buffer,0,mb_strlen($buffer)-1);
							}
							//echo $buffer."<br />";
	
							if(mb_substr($buffer, 0, 1) != "#" AND mb_substr($buffer, 0, 1) != "/") {
								if (!isset($debut_req))  $debut_req = $buffer;
								$formattedQuery .= $buffer;
								echo $formattedQuery."<hr />";
								if ($formattedQuery) {
									$sql = $formattedQuery;

									if($effectuer_unlock_tables=='y') {
										$sql_unlock="UNLOCK TABLES;";
										$lock=mysqli_query($GLOBALS["mysqli"], $sql_unlock);
									}

									if (mysqli_query($GLOBALS["mysqli"], $sql)) {//réussie sinon continue à concaténer
										if(preg_match("/^DROP TABLE /",$sql)) {
											echo "Suppression de la table <span style='color:green;'>$nom_table</span> si elle existe.<br />";
										}
										elseif(preg_match("/^CREATE TABLE /",$sql)) {
											echo "Création de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde.<br />";
										}
										else {
											if($cpt_insert==0) {
												//echo "<div style='width:100%'>";
												echo "Restauration des enregistrements de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde: ";
											}
											else {
												echo "<span style='font-size: xx-small;'>. </span>";
											}
											$cpt_insert++;
										}
										flush();
	
										debug_pb($sql);
	
										//$offset=gztell($fileHandle);
										//echo $offset;
										$formattedQuery = "";
										unset($debut_req);
										$cpt++;
										//echo $cpt;
									}
									else {
										//if(preg_match("/^INSERT INTO /",$sql)) {
										if($debug_restaure=='y') {
											echo "<p style='color:red'>$sql<br />\n".mysqli_error($GLOBALS["mysqli"])."</p>";
										}
									}
								}
							}
						}


					}
					else {
						$fileHandle = gzopen($dumpFile, "rb");
	
						$cpt_insert=0;
	
						$formattedQuery = "";
						$old_offset = $offset;
						while(!gzeof($fileHandle)) {
							current_time();
							if ($duree>0 and $TPSCOUR>=$duree) {  //on atteint la fin du temps imparti
								if ($old_offset == $offset) {
									echo "<p class=\"rouge center\"><strong>La procédure de restauration ne peut pas continuer.
									<br />Un problème est survenu lors du traitement d'une requête près de :.
									<br />".$debut_req."</strong></p><hr />\n";
									return FALSE;
								}
								$old_offset = $offset;
								return TRUE;
							}
	
							//echo $TPSCOUR."<br />";
							$buffer=gzgets($fileHandle);
							if (mb_substr($buffer,mb_strlen($buffer),1)==0) {
								$buffer=mb_substr($buffer,0,mb_strlen($buffer)-1);
							}
							//echo $buffer."<br />";
	
							if(mb_substr($buffer, 0, 1) != "#" AND mb_substr($buffer, 0, 1) != "/") {
								if (!isset($debut_req))  $debut_req = $buffer;
								$formattedQuery .= $buffer;
								//echo $formattedQuery."<hr />";
								if ($formattedQuery) {
									if($effectuer_unlock_tables=='y') {
										$sql_unlock="UNLOCK TABLES;";
										$lock=mysqli_query($GLOBALS["mysqli"], $sql_unlock);
									}

									$sql = $formattedQuery;
									if (mysqli_query($GLOBALS["mysqli"], $sql)) {//réussie sinon continue à concaténer
										if(preg_match("/^DROP TABLE /",$sql)) {
											echo "Suppression de la table <span style='color:green;'>$nom_table</span> si elle existe.<br />";
										}
										elseif(preg_match("/^CREATE TABLE /",$sql)) {
											echo "Création de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde.<br />";
										}
										else {
											if($cpt_insert==0) {
												//echo "<div style='width:100%'>";
												echo "Restauration des enregistrements de la table <span style='color:green;'>$nom_table</span> d'après la sauvegarde: ";
											}
											else {
												echo "<span style='font-size: xx-small;'>. </span>";
											}
											$cpt_insert++;
										}
										flush();
	
										debug_pb($sql);
	
										$offset=gztell($fileHandle);
										//echo $offset;
										$formattedQuery = "";
										unset($debut_req);
										$cpt++;
										//echo $cpt;
									}
									else {
										//if(preg_match("/^INSERT INTO /",$sql)) {
										if($debug_restaure=='y') {
											echo "<p style='color:red'>$sql<br />\n".mysqli_error($GLOBALS["mysqli"])."</p>";
										}
									}
								}
							}
						}
					}

					if($cpt_insert>0) {
						echo "<br />";
						echo "$cpt_insert enregistrement(s) restauré(s).<br />";
						//echo "</div>\n";
					}
	
					if (mysqli_error($GLOBALS["mysqli"])) {
						echo "<hr />\nERREUR à partir de <br />".nl2br($formattedQuery)."<br />".mysqli_error($GLOBALS["mysqli"])."<hr />\n";
						$erreur_mysql=TRUE;
					}
	
					gzclose($fileHandle);

					$sql="LOCK TABLES a_tmp_setting WRITE;";
					$lock=mysqli_query($GLOBALS["mysqli"], $sql);

					$sql="DELETE FROM a_tmp_setting WHERE name='table_".$num_table."';";
					if($debug_restaure=='y') {
						if($nettoyage=mysqli_query($GLOBALS["mysqli"], $sql)) {
							echo "Succès de la suppression dans a_tmp_setting.<br />\n";
						}
						else {
							echo "<p style='color:red;'>Erreur lors de la suppression dans 'a_tmp_setting'.</p>\n";
						}
	
						if(unlink($dumpFile)) {
							echo "Succès de la suppression de $dumpFile.<br />";
						}
						else {
							echo "<p style='color:red;'>Erreur lors de la suppression de $dumpFile.</p>\n";
						}
					}
					else {
						if(!$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql)) {
							echo "<p style='color:red;'>Erreur lors de la suppression dans 'a_tmp_setting'.</p>\n";
						}
	
						if(!unlink($dumpFile)) {
							echo "<p style='color:red;'>Erreur lors de la suppression de $dumpFile.</p>\n";
						}
					}
				}
			}
		}

	}

	return TRUE;
}

function extractMySqlDump($dumpFile,$duree) {
    // $dumpFile, fichier source
    // $duree=timeout pour changement de page (-1 = aucun)

    global $TPSCOUR,$offset,$cpt;
	//global $nom_table;
	global $dirname;
	//global $table_log_passee;

	global $ne_pas_restaurer_log;
	global $ne_pas_restaurer_tentatives_intrusion;
	global $effectuer_unlock_tables;

    if(!file_exists($dumpFile)) {
         echo "$dumpFile non trouvé<br />\n";
         return FALSE;
    }

    $fileHandle = gzopen($dumpFile, "rb");

    if(!$fileHandle) {
        echo "Ouverture de $dumpFile impossible.<br />\n";
        return FALSE;
    }

    if ($offset!=0) {
        if (gzseek($fileHandle,$offset,SEEK_SET)!=0) { //erreur
            echo "Impossible de trouver l'octet ".number_format($offset,0,""," ")."<br />\n";
            return FALSE;
        }
        flush();
    }

    $formattedQuery = "";
    $old_offset = $offset;
	$num_table=0;
    while(!gzeof($fileHandle)) {
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) {  //on atteint la fin du temps imparti
            if ($old_offset == $offset) {
                echo "<p  class=\"rouge center\"><strong>La procédure de restauration ne peut pas continuer.
                <br />Un problème est survenu lors du traitement d'une requête près de :.
                <br />".$buffer."</strong></p><hr />\n";
                return FALSE;
            }
            $old_offset = $offset;
            return TRUE;
        }
        //echo $TPSCOUR."<br />";
        $buffer=gzgets($fileHandle);

		// On ne met pas les lignes de commentaire, ni les lignes vides
		if(mb_substr($buffer, 0, 1) != "#" AND mb_substr($buffer, 0, 1) != "/" AND trim($buffer)!='') {
			if(preg_match("/^DROP TABLE /",$buffer)) {
				if(isset($fich)) {fclose($fich);}
				//$fich=fopen("../backup/".$dirname."/base_extraite_table_".$num_table.".sql","w+");
				$fich=fopen("../backup/".$dirname."/base_extraite_table_".sprintf("%08d",$num_table).".sql","w+");

				$nom_table=trim(preg_replace("/[ `;]/","",preg_replace("/^DROP TABLE /","",preg_replace("/^DROP TABLE IF EXISTS /","",$buffer))));

				$sql="INSERT INTO a_tmp_setting SET name='table_".sprintf("%08d",$num_table)."', value='$nom_table';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);

				$cpt_lignes_fichier=0;

				$num_table++;
			}
			if(isset($fich)) {
				if($nom_table=='log') {
					if(($ne_pas_restaurer_log!='y')||(!preg_match("/^INSERT INTO /i",$buffer))) {
						fwrite($fich,$buffer);
					}
				}
				elseif($nom_table=='tentatives_intrusion') {
					if(($ne_pas_restaurer_tentatives_intrusion!='y')||(!preg_match("/^INSERT INTO /i",$buffer)))  {
						fwrite($fich,$buffer);
					}
				}
				else {
					if($cpt_lignes_fichier>1000) {
						if(isset($fich)) {fclose($fich);}
						$fich=fopen("../backup/".$dirname."/base_extraite_table_".sprintf("%08d",$num_table).".sql","w+");
						// Le nom de table n'a pas changé:	
						$sql="INSERT INTO a_tmp_setting SET name='table_".sprintf("%08d",$num_table)."', value='$nom_table';";
						$res=mysqli_query($GLOBALS["mysqli"], $sql);

						$cpt_lignes_fichier=0;
						$num_table++; // Du coup, la variable ne correspond plus au nombre de tables, mais au nombre de morceaux.
					}
					fwrite($fich,$buffer);
					$cpt_lignes_fichier++;
				}
			}
			else {
				echo "Non enregistré: \$buffer=$buffer<br />";
			}
		}
	}
	if(isset($fich)) {fclose($fich);}

    gzclose($fileHandle);

	$sql="INSERT INTO a_tmp_setting SET name='nb_tables', value='$num_table';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);

    return TRUE;
}

function restoreMySqlDump_old($dumpFile,$duree) {
    // $dumpFile, fichier source
    // $duree=timeout pour changement de page (-1 = aucun)

    global $TPSCOUR,$offset,$cpt,$erreur_mysql;

    if(!file_exists($dumpFile)) {
         echo "$dumpFile non trouvé<br />\n";
         return FALSE;
    }
    $fileHandle = gzopen($dumpFile, "rb");

    if(!$fileHandle) {
        echo "Ouverture de $dumpFile impossible.<br />\n";
        return FALSE;
    }

    if ($offset!=0) {
        if (gzseek($fileHandle,$offset,SEEK_SET)!=0) { //erreur
            echo "Impossible de trouver l'octet ".number_format($offset,0,""," ")."<br />\n";
            return FALSE;
        }
        flush();
    }
    $formattedQuery = "";
    $old_offset = $offset;
    while(!gzeof($fileHandle)) {
        current_time();
        if ($duree>0 and $TPSCOUR>=$duree) {  //on atteint la fin du temps imparti
            if ($old_offset == $offset) {
                echo "<p  class=\"rouge center\"><strong>La procédure de restauration ne peut pas continuer.
                <br />Un problème est survenu lors du traitement d'une requête près de :.
                <br />".$debut_req."</strong></p><hr />\n";
                return FALSE;
            }
            $old_offset = $offset;
            return TRUE;
        }
        //echo $TPSCOUR."<br />";
        $buffer=gzgets($fileHandle);
        if (mb_substr($buffer,mb_strlen($buffer),1)==0) {
            $buffer=mb_substr($buffer,0,mb_strlen($buffer)-1);
        }
        //echo $buffer."<br />";

        if(mb_substr($buffer, 0, 1) != "#" AND mb_substr($buffer, 0, 1) != "/") {
            if (!isset($debut_req)) {$debut_req = $buffer;}
            $formattedQuery .= $buffer;
              //echo $formattedQuery."<hr />";
            if (trim($formattedQuery)!="") {
                $sql = $formattedQuery;
                if (mysqli_query($GLOBALS["mysqli"], $sql)) {//réussie sinon continue à concaténer
                    $offset=gztell($fileHandle);
                    //echo $offset;
                    $formattedQuery = "";
                    unset($debut_req);
                    $cpt++;
                    //echo "$cpt requêtes exécutées avec succès jusque là.<br />";
                }
            }
        }
    }

    if (mysqli_error($GLOBALS["mysqli"])) {
        echo "<hr />\nERREUR à partir de ".nl2br($formattedQuery)."<br />".mysqli_error($GLOBALS["mysqli"])."<hr />\n";
		$erreur_mysql=TRUE;
    }

    gzclose($fileHandle);
    $offset=-1;
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
    $def="#\n# Structure de la table $table\n#\n";
    $def .="DROP TABLE IF EXISTS `$table`;\n";
    // requete de creation de la table
    $query = "SHOW CREATE TABLE $table";
    $resCreate = mysqli_query($GLOBALS["mysqli"], $query);
    $row = mysqli_fetch_array($resCreate);
    $schema = $row[1].";";
    $def .="$schema\n";
    return $def;
}

function get_content($db, $table,$from,$limit) {
    $search       = array("\x00", "\x0a", "\x0d", "\x1a");
    $replace      = array('\0', '\n', '\r', '\Z');
    // les données de la table
    $def = '';
    $query = "SELECT * FROM $table LIMIT $from,$limit";
    $resData = @mysqli_query($GLOBALS["mysqli"], $query);
    //peut survenir avec la corruption d'une table, on prévient
    if (!$resData) {
        $def .="Problème avec les données de $table, corruption possible !\n";
    } else {
        if (@mysqli_num_rows($resData) > 0) {
             $sFieldnames = "";
             $num_fields = (($___mysqli_tmp = mysqli_num_fields($resData)) ? $___mysqli_tmp : false);
              $sInsert = "INSERT INTO $table $sFieldnames values ";
              while($rowdata = mysqli_fetch_row($resData)) {
                  $lesDonnees = "";
                  for ($mp = 0; $mp < $num_fields; $mp++) {
                      if (is_null($rowdata[$mp])) {
                          $lesDonnees .= "NULL";
                          } else {
                              $lesDonnees .= "'" . str_replace($search, $replace, traitement_magic_quotes($rowdata[$mp])) . "'";
                          }
                  //on ajoute à la fin une virgule si nécessaire
                      if ($mp<$num_fields-1) $lesDonnees .= ",";
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
if (!isset($_GET["path"]))
    $path="../backup/" . $dirname . "/" ;
else
    $path=$_GET["path"];



// Durée d'une portion
if (!isset($_SESSION['defaulttimeout'])) {
    $_SESSION['defaulttimeout']=max(get_cfg_var("max_execution_time")-2,5);
}

// Lors d'une sauvegarde, nombre de lignes traitées dans la base entre chaque vérification du temps restant
$defaultrowlimit=10;

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
//**************** EN-TETE *****************
$titre_page = "Outil de gestion | Sauvegardes/Restauration";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

// Test d'écriture dans /backup
$test_write = test_ecriture_backup();
if ($test_write == 'no') {
    echo "<h3 class='gepi'>Problème de droits d'accès :</h3>\n";
    echo "<p>Le répertoire \"/backup\" n'est pas accessible en écriture.</p>\n";
    echo "<p>Vous ne pouvez donc pas accéder aux fonctions de sauvegarde/restauration de GEPI.
    Contactez l'administrateur technique afin de régler ce problème.</p>\n";
    require("../lib/footer.inc.php");
    die();
}

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
	check_token();

    echo "<h3>Confirmation de la restauration de la base</h3>\n";
    echo "<p>Fichier sélectionné pour la restauration : <b>".$_GET['file']."</b></p>\n";

	// 20180831 : Tester s'il existe une fichier de description et si oui l'afficher
	if(file_exists('../backup/'.$dirname.'/'.$_GET['file'].'.txt')) {
		$handle = fopen('../backup/'.$dirname.'/'.$_GET['file'].'.txt', "r");
		$contents = fread($handle, filesize('../backup/'.$dirname.'/'.$_GET['file'].'.txt'));
		fclose($handle);
		$contents=preg_replace('/"/', "", $contents);

		echo "<div class='fieldset_opacite50' style='margin:0.5em; padding:0.5em;'><strong>Description&nbsp;:</strong> ".nl2br($contents)."</div>";
	}

    echo "<p><b>ATTENTION :</b> La procédure de restauration de la base est <b>irréversible</b>. Le fichier de restauration doit être valide. Selon le contenu de ce fichier, tout ou partie de la structure actuelle de la base ainsi que des données existantes peuvent être supprimées et remplacées par la structure et les données présentes dans le fichier.
    <br /><br />\n<b>AVERTISSEMENT :</b> Cette procédure peut être très longue selon la quantité de données à restaurer.</p>\n";
	echo "<br />Options de restauration :\n";
	echo "<blockquote>\n";



		echo "<form enctype=\"multipart/form-data\" action=\"accueil_sauve.php\" method='post' id='formulaire_oui'>\n";
		echo "<p>".add_token_field()."</p>";

		echo "<div style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); margin-bottom:1em;'>";
		echo "--Restauration par tables (option par défaut)--<br />";
		echo "<blockquote>\n";

        echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"debug_restaure\" id=\"debug_restaure\" value=\"y\" onchange='document.getElementById(\"restauration_old_way\").checked=false;' /><label for='debug_restaure' style='cursor:pointer;'> Activer le mode debug</label>\n";
        echo "</p>\n";

        echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"ne_pas_restaurer_log\" id=\"ne_pas_restaurer_log\" value=\"y\"  onchange='document.getElementById(\"restauration_mysql\").checked=false;document.getElementById(\"restauration_old_way\").checked=false;' /><label for='ne_pas_restaurer_log' style='cursor:pointer;'> Ne pas restaurer les enregistrements de la table 'log'.</label>\n";
		echo "</p>\n";

        echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"ne_pas_restaurer_tentatives_intrusion\" id=\"ne_pas_restaurer_tentatives_intrusion\" value=\"y\"  onchange='document.getElementById(\"restauration_mysql\").checked=false;document.getElementById(\"restauration_old_way\").checked=false;' /><label for='ne_pas_restaurer_tentatives_intrusion' style='cursor:pointer;'> Ne pas restaurer les enregistrements de la table 'tentatives_intrusion'.</label>\n";
		echo "</p>\n";

        echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"effectuer_unlock_tables\" id=\"effectuer_unlock_tables\" value=\"y\" /><label for='effectuer_unlock_tables' style='cursor:pointer;'> Effectuer des 'unlock tables' dans le cas où des erreurs se produiraient <em style='color:red'>(expérimental)</em>.</label>\n";
		echo "</p>\n";
        echo "</blockquote>\n";
		echo "</div>";

		echo "<div style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\"); margin-bottom:1em;'>";
		echo "--Restauration d'un bloc--<br />";
		echo "<blockquote>\n";
		echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"restauration_old_way\" id=\"restauration_old_way\" value=\"y\" onchange='document.getElementById(\"restauration_mysql\").checked=false;document.getElementById(\"ne_pas_restaurer_tentatives_intrusion\").checked=false;document.getElementById(\"ne_pas_restaurer_log\").checked=false;document.getElementById(\"debug_restaure\").checked=false;' /><label for='restauration_old_way' style='cursor:pointer;'> Restaurer la sauvegarde d'un bloc<br />(<i>utile par exemple pour restaurer un fichier SQL ne correspondant pas à une sauvegarde classique</i>)</label>\n";
		echo "</p>\n";
        echo "</blockquote>\n";
		echo "</div>";

		echo "<div style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>";
		echo "--Restauration par MySQL--<br />";
		echo "<blockquote>\n";
		echo "<p>\n";
		echo "<input type=\"checkbox\" name=\"restauration_mysql\" id=\"restauration_mysql\" value=\"y\"";
		if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN') && (!file_exists("mysql.exe"))) echo " disabled";
		echo " onchange='document.getElementById(\"restauration_old_way\").checked=false;document.getElementById(\"ne_pas_restaurer_tentatives_intrusion\").checked=false;document.getElementById(\"ne_pas_restaurer_log\").checked=false;'";
		echo "/><label for='restauration_mysql' style='cursor:pointer;'> Restaurer la sauvegarde par un appel à la commande système mysql<br />(<i>plus rapide mais il n'y a aucune indication de progression durant le processus</i>)</label><br /><br />\n";
		echo "</p>\n";
        
		//echo "<span style='color:red; text-decoration:blink; font-weight:bolder;'> -> </span>préciser si le fichier à restaurer est codé en UTF8 (sauvegarde GEPI >=1.6.0) <input type='radio' name='char_set' value='utf8'  checked='checked'> ou en ISO (sauvegarde GEPI <=1.5.5)<input type='radio' name='char_set' value='latin1'>\n";

		echo '<p>
		<span style="color:red; text-decoration:blink; font-weight:bolder;"> → </span>
		préciser si le fichier à restaurer est
	</p>
	<ul style="list-style-type:none;" >
		<li>
			<input id ="char_set_utf8" name="char_set" value="utf8" checked="checked" type="radio" />
			<label for="char_set_utf8">
				codé en UTF8 (sauvegarde GEPI &gt;=1.6.0)
			</label>
		</li>
		<li>
			<input id ="char_set_latin1" name="char_set" value="latin1" type="radio" />
			<label for="char_set_latin1">
				ou en ISO (sauvegarde GEPI &lt;=1.5.5)
			</label>
		</li>
	</ul>';

		if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN') && (!file_exists("mysql.exe"))) {
		echo "<p><b><font color=\"#FF0000\">Attention : </font></b>pour utiliser la commande système mysql lorsque Gepi est hébergé sous Windows il faut au préalable copier le fichier \"mysql.exe\" dans le dossier \"gestion\" de Gepi. Ce fichier \"mysql.exe\" se trouve généralement dans le sous-dossier \"bin\" du dossier d'installation de MySQL.</p>";
		}
		echo "</blockquote>\n";
		echo "</div>";

		echo "<p><br /><br />";
		echo "<input type='submit' id='confirm' name='confirm' value = 'Lancer la restauration' />\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"restaure\" />\n";
		echo "<input type=\"hidden\" name=\"file\" value=\"".$_GET['file']."\" />\n";
		echo "</p>\n";
		echo "</form>\n";

	echo "<p>ou</p>\n";
    echo "<form enctype=\"multipart/form-data\" action=\"accueil_sauve.php\" method=\"post\" id=\"formulaire_non\">\n";
    echo "<p><input type='submit' name='confirm' value = 'Abandonner la restauration' /></p>\n";
    echo "</form>\n";


	echo "</blockquote>\n";

    require("../lib/footer.inc.php");
    die();
}


// Restauration
if (isset($action) and ($action == 'restaure'))  {
	check_token();
	
	$restauration_mysql=isset($_POST["restauration_mysql"]) ? $_POST["restauration_mysql"] : (isset($_GET["restauration_mysql"]) ? $_GET["restauration_mysql"] : "n");

    unset($file);
    $file = isset($_POST["file"]) ? $_POST["file"] : (isset($_GET["file"]) ? $_GET["file"] : NULL);

	$restauration_old_way=isset($_POST["restauration_old_way"]) ? $_POST["restauration_old_way"] : (isset($_GET["restauration_old_way"]) ? $_GET["restauration_old_way"] : "n");

	$cpt=isset($_POST["cpt"]) ? $_POST["cpt"] : (isset($_GET["cpt"]) ? $_GET["cpt"] : 0);
	
	$t_debut=isset($_POST["t_debut"]) ? $_POST["t_debut"] : (isset($_GET["t_debut"]) ? $_GET["t_debut"] : time());

	if($restauration_old_way=='y') {
		//===============================================
		init_time(); //initialise le temps
		//début de fichier
		if (!isset($_GET["offset"])) $offset=0;
		else  $offset=$_GET["offset"];

		//timeout
		if (!isset($_GET["duree"])) $duree=$_SESSION['defaulttimeout'];
			else $duree=$_GET["duree"];
		$fsize=filesize($path.$file);
		if(isset($offset)) {
			if ($offset==-1) $percent=100;
			else $percent=min(100,round(100*$offset/$fsize,0));
		}
		else $percent=0;

		if ($percent >= 0) {
			$percentwitdh=$percent*4;
			echo "<div class=\"center\"><table class='tab_cadre' width='400'><tr><td width='400'  class=\"center\"><strong>Restauration en cours</strong><br /><br />Progression ".$percent."%</td></tr><tr><td><table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table></td></tr></table></div>\n";
		}
		if (ob_get_contents()) {
			ob_flush();
		}
		flush();
		if ($offset!=-1) {
			$erreur_mysql=FALSE;
			if (restoreMySqlDump_old($path.$file,$duree)) {
				echo "$cpt requête(s) exécutée(s) avec succès jusque là.<br />";

				if (isset($debug)&&$debug!='') {
					echo "<br />\n<b>Cliquez <a href=\"accueil_sauve.php?action=restaure&file=".$file."&duree=$duree&offset=$offset&cpt=$cpt&path=$path&restauration_old_way=$restauration_old_way&t_debut=$t_debut".add_token_in_url()."\">ici</a> pour poursuivre la restauration</b>\n";
				}

				if (!isset($debug)||$debug=='') {
					if (!$erreur_mysql) echo "<br />\n<b>Redirection automatique sinon";
						else echo "<br />\n<b>Pour continuer";
					echo " cliquez <a href=\"accueil_sauve.php?action=restaure&file=".$file."&duree=$duree&offset=$offset&cpt=$cpt&path=$path&restauration_old_way=$restauration_old_way&t_debut=$t_debut".add_token_in_url()."\">ici</a></b>\n";
				}

				if (!$erreur_mysql && (!isset($debug)||$debug=='')) {
					echo "<script>window.location=\"accueil_sauve.php?action=restaure&file=".$file."&duree=$duree&offset=$offset&cpt=$cpt&path=$path&restauration_old_way=$restauration_old_way&t_debut=$t_debut".add_token_in_url(false)."\";</script>\n";
				}
				flush();
				exit;
			} else die("<br />Erreur restoreMySqlDump_old");
		} else {
			echo "<p style='text-align:center'>$cpt requête(s) exécutée(s) avec succès en tout.</p>";
			
			// durée de la restauration
			$t_duree=time()-$t_debut;
			$s=$t_duree%60;
			$t_duree=floor($t_duree/60);
			$m=$t_duree%60;
			$h=floor($t_duree/60);

			echo "<div class=\"center\"><p>Restauration terminée en ".$h." h ".$m." min ".$s." s.<br /><br />Votre session GEPI n'est plus valide, vous devez vous reconnecter<br /><a href = \"../login.php\">Se connecter</a></p></div>\n";

			$sql="UNLOCK TABLES;";
			$lock=mysqli_query($GLOBALS["mysqli"], $sql);

			require("../lib/footer.inc.php");
			die();
		}
		//===============================================
	}
	if($restauration_mysql=='y') {
	function shutdown() {
		global $retour,$t_retour,$t_debut,$creation_fichier_sql,$gepiPath,$dirname,$file;
		
		// durée de la restauration
		$t_duree=time()-$t_debut;
		$s=$t_duree%60;
		$t_duree=floor($t_duree/60);
		$m=$t_duree%60;
		$h=floor($t_duree/60);
		echo "<script>document.getElementById('restau_en_cours').innerHTML='<p>Restauration effectuée en ".$h." h ".$m." min ".$s." s</p>'</script>";

		// bilan de la restauration
		if ($retour==0) {
			echo "<p style='padding-left: 1em;'>La restauration a été correctement effectuée.";
			// on ne peut pas utliser unlink car dans la fonction shutdown() la arcine
			// devient le dossier d'installation de PHP (echo getcwd();)
			//unlink($gepiPath."/backup/".$dirname."/bilan_restauration_".$file.".txt");
			echo "<br />Un fichier texte nommé 'bilan_restauration_".$file.".txt' a été créé dans le dossier des sauvegardes, vous pouvez le supprimer.";
		}
		else {
			echo "<p style='padding-left: 1em;'><span style='color:red; font-weight:bolder;'>ATTENTION : la restauration a échoué.</span>";
			echo "<br />Un fichier texte nommé <a href='../backup/".$dirname."/bilan_restauration_".$file.".txt' target='_blank'>'bilan_restauration_".$file.".txt'</a> a été créé dans le dossier des sauvegardes,<br />la requête qui a fait échouer la restauration se trouve à la fin de ce fichier.";
		}
		if ($creation_fichier_sql) echo "<br />Un fichier nommé '".$file."' a été créé dans le dossier des sauvegardes, vous pouvez le supprimer.";
		echo "</p>";

		// dernière erreur fatale ou warning enregistrée
		$error = error_get_last();
		//if(($error!==NULL) && ($error['type'] & ( E_ERROR | E_WARNING))) {
		if(isset($_POST['debug_restaure']) && $_POST['debug_restaure']=="y") {
			echo "<p style='padding-left: 1em;'>Dernière erreur PHP : ".$error['message']." dans le fichier ".$error['file']." en ligne ".$error['line']."</p>";
		}

		echo "<br /><p style='padding-left: 1em;'><a href='../login.php'>Votre session Gepi n'est plus valide, vous devez vous reconnecter.</a></p>";

		// On détruit la session
		//session_destroy();
	}

	// on fait patienter
	echo "<br />";
	echo "<span id='restau_en_cours'><p>Restauration de ".$file." en cours...</p></span>";
	if (ob_get_contents()) ob_flush(); flush();


	// on teste l'accès à mysql
	if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN') && file_exists("mysqldump.exe")) @exec("mysql.exe --help",$t_retour,$retour);
	else @exec("mysql --help",$t_retour,$retour);
	if ($retour!=0) {
		echo "<script>document.getElementById('restau_en_cours').innerHTML='<a href=\"accueil_sauve.php\"><img src=\"../images/icons/back.png\" alt=\"Retour\">Retour</a>'</script>";
		echo "<br />";
		echo "<br /><p>La commande mysql n'est pas accessible, vérifiez votre configuration (sans doute un problème de PATH).</p>";
		exit();
	}

	// Quel est le char set à utiliser ?
	$char_set=(isset($_POST['char_set']))?$_POST['char_set']:"utf8";
	
	// Tests sur le type du fichier à restaurer
	if (!is_readable("../backup/".$dirname."/".$file) || !is_writable("../backup/".$dirname."/".$file)){
		echo "<script>document.getElementById('restau_en_cours').innerHTML='<a href=\"accueil_sauve.php\"><img src=\"../images/icons/back.png\" alt=\"Retour\">Retour</a>'</script>";
		echo "<br />";
		echo "<br /><p>Le fichier ".$file." n'est pas accessible en lecture et/ou en écriture, vérifiez les droits.</p>";
		exit();
	}
	$file_info=pathinfo("../backup/".$dirname."/".$file);
	if(!isset($file_info['extension']) || (strtolower($file_info['extension']!="gz") && strtolower($file_info['extension']!="sql"))) {
		echo "<script>document.getElementById('restau_en_cours').innerHTML='<a href=\"accueil_sauve.php\"><img src=\"../images/icons/back.png\" alt=\"Retour\">Retour</a>'</script>";
		echo "<br />";
		echo "<br /><p>Le fichier à restaurer doit avoir pour extension '.sql' ou '.gz'.</p>";
		exit();
	}
	

	// il faut éventuellement décompresser le fichier, car le serveur peut être sous Windows
	// (sinon un pipe et gunzip suffiraient)
	$creation_fichier_sql=false;
	if (strtolower($file_info['extension']=="gz")) {
		// on décompresse l'archive
		$d_file=$file_info['filename'];
		if (!file_exists("../backup/".$dirname."/".$d_file)) {
			$h=gzopen("../backup/".$dirname."/".$file,"rb");
			$d_h=fopen("../backup/".$dirname."/".$d_file,"wb");
			// ajout de SET NAMES...
			while($buffer=gzread($h,10240)) {
				fwrite($d_h,$buffer,strlen($buffer));
			}
			gzclose($h);
			fclose($d_h);
			$file=$d_file;
			$creation_fichier_sql=true;
		} else {
			echo "<script>document.getElementById('restau_en_cours').innerHTML='<a href=\"accueil_sauve.php\"><img src=\"../images/icons/back.png\" alt=\"Retour\">Retour</a>'</script>";
			echo "<br />";
			echo "<br /><p>La restauration ne peut se faire avec la commande système mysql car un fichier ".$d_file." est déjà présent dans le dossier backup.</p>";
			exit();
			}
		}

	// C'est parti pour la restauration
	register_shutdown_function('shutdown');
	if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN') && file_exists("mysql.exe")) {
		$cmd="mysql.exe -v --default_character_set ".$char_set." -p".$dbPass." -u ".$dbUser." ".$dbDb." --host=".$dbHost;
		if (isset($dbPort)) {$cmd.=" --port=".$dbPort;}
		$cmd.=" < ../backup/".$dirname."/".$file ." > ../backup/".$dirname."/bilan_restauration_".$file.".txt";
	}
	else {
		$cmd="mysql -v --default_character_set ".$char_set." -p".$dbPass." -u ".$dbUser." ".$dbDb." --host=".$dbHost;
		if (isset($dbPort)) {$cmd.=" --port=".$dbPort;}
		$cmd.=" < ../backup/".$dirname."/".$file ." > ../backup/".$dirname."/bilan_restauration_".$file.".txt";
	}
	@exec($cmd,$t_retour,$retour);
	// ici le script est terminé, et donc la fonction 'shutdown' est appelée
	

	}
	else {
		$debug_restaure=isset($_POST["debug_restaure"]) ? $_POST["debug_restaure"] : (isset($_GET["debug_restaure"]) ? $_GET["debug_restaure"] : "n");

		$ne_pas_restaurer_log=isset($_POST["ne_pas_restaurer_log"]) ? $_POST["ne_pas_restaurer_log"] : (isset($_GET["ne_pas_restaurer_log"]) ? $_GET["ne_pas_restaurer_log"] : "n");

		$ne_pas_restaurer_tentatives_intrusion=isset($_POST["ne_pas_restaurer_tentatives_intrusion"]) ? $_POST["ne_pas_restaurer_tentatives_intrusion"] : (isset($_GET["ne_pas_restaurer_tentatives_intrusion"]) ? $_GET["ne_pas_restaurer_tentatives_intrusion"] : "n");

		$effectuer_unlock_tables=isset($_POST["effectuer_unlock_tables"]) ? $_POST["effectuer_unlock_tables"] : (isset($_GET["effectuer_unlock_tables"]) ? $_GET["effectuer_unlock_tables"] : "n");

		init_time(); //initialise le temps

		//début de fichier
		// En fait d'offset, on compte maintenant des lignes
		if (!isset($_GET["offset"])) {$offset=0;}
		else {$offset=$_GET["offset"];}

		//timeout
		if (!isset($_GET["duree"])) {$duree=$_SESSION['defaulttimeout'];}
			else {$duree=$_GET["duree"];}

		echo "<div  class=\"center\"><strong>Restauration en cours</strong></div>\n";

		$suite_restauration=isset($_GET['suite_restauration']) ? $_GET['suite_restauration'] : NULL;
		if(!isset($suite_restauration)) {
			// EXTRAIRE -> SCINDER

			$sql="SHOW TABLES LIKE 'a_tmp_setting';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				// Nettoyage au cas où la restauration précédente aurait échoué
				$sql="SELECT * FROM a_tmp_setting WHERE name LIKE 'table_%';";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res)>0) {
					while($lig=mysqli_fetch_object($res)) {
						$num_table=preg_replace('/^table_/','',$lig->name);
						if(file_exists("../backup/".$dirname."/base_extraite_table_".$num_table.".sql")) {
							unlink("../backup/".$dirname."/base_extraite_table_".$num_table.".sql");
						}
					}
				}
			}

			// On achève le ménage:
			$sql="DROP TABLE a_tmp_setting;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="CREATE TABLE a_tmp_setting (
name VARCHAR(255) NOT NULL,
value VARCHAR(255) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="INSERT INTO a_tmp_setting SET name='offset', value='0';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);

			$sql="INSERT INTO a_tmp_setting SET name='nom_table', value='';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);

			echo "<p>Extraction de l'archive...<br />";
			$succes_etape=extractMySqlDump($path.$file,$duree)?'y':'n';

		}
		else {
			// TESTER S'IL RESTE DES table_%
			$sql="SELECT 1=1 FROM a_tmp_setting WHERE name LIKE 'table_%';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)>0) {
				$erreur_mysql=FALSE;
				// Il reste des tables à restaurer
				//if (restoreMySqlDump($path."/base_extraite.sql",$duree)) {
				$succes_etape=restoreMySqlDump($duree)?'y':'n';

			}
			else {
				// La restauration est achevée

				// On ne devrait pas arriver là.

				echo "<div  class=\"center\"><strong><p>Restauration terminée.<br /><br />Votre session GEPI n'est plus valide, vous devez vous reconnecter<br /><a href = \"../login.php\">Se connecter</a></p></div>\n";

				$sql="UNLOCK TABLES;";
				$lock=mysqli_query($GLOBALS["mysqli"], $sql);

				require("../lib/footer.inc.php");
				die();
			}
		}

		if($succes_etape!="y") {

			echo "<p style='color:red'>Une erreur s'est produite!<br />";

		}
		else {
			// durée de la sauvegarde
			$t_duree=time()-$t_debut;
			$s=$t_duree%60;
			$t_duree=floor($t_duree/60);
			$m=$t_duree%60;
			$h=floor($t_duree/60);

			//$sql="SELECT * FROM a_tmp_setting WHERE name LIKE 'table_%';";
			// Pour nettoyer aussi une trace d'une sauvegarde consécutive à une restauration ratée... pas sûr que ce soit prudent...
			$sql="SELECT * FROM a_tmp_setting WHERE name LIKE 'table_%' AND value!='a_tmp_setting';";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				echo "<div id='div_fin_restauration' class='infobulle_corps' style='position:absolute; top: 200px; left:100px; border:1px solid black; width: 30em;'>\n";

					echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; width: 30em;'";
					// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
					echo " onmousedown=\"dragStart(event, 'div_fin_restauration')\">";
					echo "Restauration terminée";
					echo "</div>\n";

					echo "<div  class=\"center\">\n";
					echo "<p>Restauration terminée en ".$h." h ".$m." min ".$s." s.<br /><br />Votre session GEPI n'est plus valide, vous devez vous reconnecter<br /><a href=\"../login.php\">Se connecter</a></p>\n";
					//echo "<p><em>NOTE:</em> J'ai un problème bizarre! Alors que le lien pointe bien vers ../login.php, je me retrouve un dossier plus haut sur un logout.php hors du dossier de Gepi si bien que j'obtiens un 404 Not Found???</p>\n";
					echo "</div>\n";

				echo "</div>\n";

				// Il peut rester un fichier base_extraite_table_XXX.sql correspondant à a_tmp_setting si on a restauré une sauvegarde faite après une restauration ratée/incomplète... inquiètant.

				$sql="DROP TABLE a_tmp_setting;";
				$res=mysqli_query($GLOBALS["mysqli"], $sql);

				// Il ne faut pas recharger la page après restauration des tables log, setting, utilisateurs.

				$sql="UNLOCK TABLES;";
				$lock=mysqli_query($GLOBALS["mysqli"], $sql);

				require("../lib/footer.inc.php");
				die();
			}

			// RESOUMETTRE
			echo "<form action='".$_SERVER['PHP_SELF']."' method='get' id='form_suite'>\n";
			echo "<p>\n";
			echo "<input type='hidden' name='suite_restauration' value='y' />\n";
			echo "<input type='hidden' name='action' value='restaure' />\n";
			echo "<input type='hidden' name='debug_restaure' value='$debug_restaure' />\n";
			echo add_token_field();
			echo "<input type='hidden' name='ne_pas_restaurer_log' value='$ne_pas_restaurer_log' />\n";
			echo "<input type='hidden' name='ne_pas_restaurer_tentatives_intrusion' value='$ne_pas_restaurer_tentatives_intrusion' />\n";
			echo "<input type='hidden' name='effectuer_unlock_tables' value='$effectuer_unlock_tables' />\n";
			echo "<input type='hidden' name='t_debut' value='$t_debut' />\n";
			echo "</p>\n";
			echo "</form>\n";
			if (((isset($erreur_mysql) && !$erreur_mysql)) || !isset($erreur_mysql)) echo "<script type='text/javascript'>
	setTimeout(\"document.forms['form_suite'].submit();\",500);
</script>\n";

			echo "<br />\n";
			echo "<a name='suite'></a>\n";
			echo "<b>Cliquez <a href=\"accueil_sauve.php?action=restaure";
			echo add_token_in_url();
			echo "&amp;suite_restauration=y";
			echo "&amp;debug_restaure=$debug_restaure";
			echo "&amp;ne_pas_restaurer_log=$ne_pas_restaurer_log";
			echo "&amp;ne_pas_restaurer_tentatives_intrusion=$ne_pas_restaurer_tentatives_intrusion";
			echo "&amp;effectuer_unlock_tables=$effectuer_unlock_tables";
			echo "&amp;t_debut=$t_debut";
			echo "#suite\">ici</a> pour poursuivre la restauration</b>\n";
		}
	}

	$sql="UNLOCK TABLES;";
	$lock=mysqli_query($GLOBALS["mysqli"], $sql);

	require("../lib/footer.inc.php");
	die();
}

$quitter_la_page=isset($_POST['quitter_la_page']) ? $_POST['quitter_la_page'] : (isset($_GET['quitter_la_page']) ? $_GET['quitter_la_page'] : NULL);

// Sauvegarde
if (isset($action) and ($action == 'dump'))  {
	$t_debut=isset($_POST["t_debut"]) ? $_POST["t_debut"] : (isset($_GET["t_debut"]) ? $_GET["t_debut"] : time());
	// On enregistre le paramètre pour s'en souvenir la prochaine fois
	saveSetting("mode_sauvegarde", "gepi");
	// Sauvegarde de la base
    $nomsql = $dbDb."_le_".date("Y_m_d_\a_H\hi");
    $cur_time=date("Y-m-d H:i");
    $filename=$path.$nomsql.".".$filetype;

	if((isset($_POST['description_sauvegarde']))&&($_POST['description_sauvegarde']!='')) {
		$f_desc=fopen($filename.".txt", "a+");
		$description_sauvegarde=suppression_sauts_de_lignes_surnumeraires($_POST['description_sauvegarde']);
		fwrite($f_desc, $description_sauvegarde);
		fclose($f_desc);
	}

	// Ce nom est modifié à chaque passage dans action=dump, mais pour les passages suivant le premier, on reçoit $fichier en $_GET donc on n'utilise pas $filename

    if (!isset($_GET["duree"])&&is_file($filename)){
        echo "<span class='rouge centre'><strong>Le fichier existe déjà. Patientez une minute avant de retenter la sauvegarde.</strong></span>\n<hr />\n";
    } else {
        init_time(); //initialise le temps
        //début de fichier
        if (!isset($_GET["offsettable"])) $offsettable=0;
            else $offsettable=$_GET["offsettable"];
        //début de fichier
        if (!isset($_GET["offsetrow"])) $offsetrow=-1;
            else $offsetrow=$_GET["offsetrow"];
        //timeout de 5 secondes par défaut, -1 pour utiliser sans timeout
        if (!isset($_GET["duree"])) $duree=$_SESSION['defaulttimeout'];
            else $duree=$_GET["duree"];
        //Limite de lignes à dumper à chaque fois
        if (!isset($_GET["rowlimit"])) $rowlimit=$defaultrowlimit;
            else  $rowlimit=$_GET["rowlimit"];
         //si le nom du fichier n'est pas en paramètre le mettre ici
         if (!isset($_GET["fichier"])) {
             $fichier=$filename;
         } else {
			check_token();
			$fichier=$_GET["fichier"];
		}


		$sql="SHOW TABLES;";
		$tab=mysqli_query($GLOBALS["mysqli"], $sql);
        $tot=mysqli_num_rows($tab);
        if(isset($offsettable)){
            if ($offsettable>=0)
                $percent=min(100,round(100*$offsettable/$tot,0));
            else $percent=100;
        }
        else $percent=0;

        if ($percent >= 0) {
            $percentwitdh=$percent*4;
            echo "<div class=\"center\">\n<table width=\"400\" border=\"0\">
            <tr><td width='400' class=\"center\"><strong>Sauvegarde en cours</strong><br/>
            <br/>A la fin de la sauvegarde, Gepi vous proposera automatiquement de télécharger le fichier.
            <br/><br/>Progression ".$percent."%</td></tr>\n<tr><td>\n<table><tr><td bgcolor='red'  width='$percentwitdh' height='20'>&nbsp;</td></tr></table>\n</td></tr>\n</table>\n</div>\n";
        }
        if ($percent != 100) {
            if (ob_get_contents()) {
                ob_flush();
            }
            flush();
        }
        if ($offsettable>=0){
            if (backupMySql($dbDb,$fichier,$duree,$rowlimit)) {
                if (isset($debug)&&$debug!='') {
					echo "<br />\n<b>Cliquez <a href=\"accueil_sauve.php?action=dump&amp;duree=$duree&amp;rowlimit=$rowlimit&amp;offsetrow=$offsetrow&amp;offsettable=$offsettable&amp;cpt=$cpt&amp;fichier=$fichier&amp;path=$path&amp;t_debut=$t_debut";
					if(isset($quitter_la_page)) {echo "&amp;quitter_la_page=y";}
					echo add_token_in_url();
					echo "\">ici</a> pour poursuivre la sauvegarde.</b>\n";
				}
                if (!isset($debug)||$debug=='') {
					echo "<br />\n<b>Redirection automatique sinon cliquez <a href=\"accueil_sauve.php?action=dump&amp;duree=$duree&amp;rowlimit=$rowlimit&amp;offsetrow=$offsetrow&amp;offsettable=$offsettable&amp;cpt=$cpt&amp;fichier=$fichier&amp;path=$path&amp;t_debut=$t_debut";
					if(isset($quitter_la_page)) {echo "&amp;quitter_la_page=y";}
					echo add_token_in_url();
					echo "\">ici</a></b>\n";
				}
                if (!isset($debug)||$debug=='') {
					echo "<script>window.location=\"accueil_sauve.php?action=dump&duree=$duree&rowlimit=$rowlimit&offsetrow=$offsetrow&offsettable=$offsettable&cpt=$cpt&fichier=$fichier&path=$path&t_debut=$t_debut";
					if(isset($quitter_la_page)) {echo "&quitter_la_page=y";}
					echo add_token_in_url(false);
					echo "\";</script>\n";
				}
                flush();
                exit;
           }
        } else {
			// La sauvegarde est terminée. On compresse le fichier
			$compress = gzip($fichier, 9);
			if ($compress) {
				$filetype = "sql.gz";
			}
			@unlink($fichier);

			// durée de la sauvegarde
			$t_duree=time()-$t_debut;
			$s=$t_duree%60;
			$t_duree=floor($t_duree/60);
			$m=$t_duree%60;
			$h=floor($t_duree/60);

			echo "<div class=\"center\"><p>Sauvegarde terminée en ".$h." h ".$m." min ".$s." s.<br />\n";

			//$nomsql.$filetype
			/*
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
			rsort($tab_file);
			*/
			$tab_file=get_tab_fichiers_du_dossier_de_sauvegarde($path);
			$n=count($tab_file);
			//echo "<p style='color:red'>Il y a $n fichiers dans le dossier de sauvegarde $path</p>";

			$nom_fichier=str_replace($path,'',$fichier);
			$nom_fichier=str_replace('.sql','',$nom_fichier);
			$fileid=null;
			if ($n > 0) {
				//echo "<p style='color:red'>On cherche ".$nom_fichier.'.'.$filetype."<br />";
				for($m=0;$m<count($tab_file);$m++){
					//echo "\$tab_file[$m]=".$tab_file[$m]."<br />";
					if($tab_file[$m]==$nom_fichier.'.'.$filetype){
						$fileid=$m;
						//echo "On a trouvé l'indice $m<br />";
						break;
					}
				}
				clearstatcache();
				//echo "</p>";
			}

			echo "<br/><p class=grand><a href='savebackup.php?fileid=$fileid'>Télécharger le fichier généré par la sauvegarde<br />($nom_fichier)</a></p>\n";
			echo "<br/><br/><a href=\"accueil_sauve.php";
			if(isset($quitter_la_page)) {echo "?quitter_la_page=y";}
			echo "\">Retour vers l'interface de sauvegarde/restauration</a><br /></div>\n";

			//$sql="UNLOCK TABLES;";
			//$lock=mysqli_query($GLOBALS["mysqli"], $sql);

			require("../lib/footer.inc.php");
			die();
		}

	}
}

if (isset($action) and ($action == 'system_dump'))  {
	check_token();

	// On enregistre le paramètre pour s'en souvenir la prochaine fois
	saveSetting("mode_sauvegarde", "mysqldump");

	// Sauvegarde de la base en utilisant l'utilitaire système mysqldump
    $nomsql = $dbDb."_le_".date("Y_m_d_\a_H\hi");
    $cur_time=date("Y-m-d H:i");
    $filetype = "sql.gz";
    $filename=$path.$nomsql.".".$filetype;
    // Juste pour être sûr :
	$no_escapeshellarg=getSettingValue('no_escapeshellarg');
	if($no_escapeshellarg=='y') {
		$dbHost = preg_replace("/[^A-Za-z0-9_-.]/","",$dbHost);
		$dbUser = preg_replace("/[^A-Za-z0-9_-.]/","",$dbUser);
		$dbPass = preg_replace("/[^A-Za-z0-9_-.]/","",$dbPass);
		$dbDb = preg_replace("/[^A-Za-z0-9_-.]/","",$dbDb);
	}
	else {
		$dbHost = escapeshellarg($dbHost);
		$dbUser = escapeshellarg($dbUser);
		$dbPass = escapeshellarg($dbPass);
		$dbDb = escapeshellarg($dbDb);
	}

	$req_version = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT version();"), 0);
	$ver_mysql = explode(".", $req_version);
	if (!is_numeric(mb_substr($ver_mysql[2], 1, 1))) {
		$ver_mysql[2] = mb_substr($ver_mysql[2], 0, 1);
	} else {
		$ver_mysql[2] = mb_substr($ver_mysql[2], 0, 2);
	}

	// on fait patienter
	echo "<br />";
	echo "<span id='sauvegarde_en_cours'><p>Sauvegarde en cours...</p></span>";
	if (ob_get_contents()) ob_flush(); flush();

	$t_debut=time();
	if (strtoupper(substr(PHP_OS,0,3)) == 'WIN') {
		if(file_exists("mysqldump.exe")) {
			// on est sous Window$ et on a $filename : "xxxx.sql.gz"
			$filename=substr($filename,0,-3); // $filename : "xxxx.sql"
			$command = "mysqldump.exe --skip-opt --add-drop-table --skip-disable-keys --quick -Q --create-options --set-charset --skip-comments -h $dbHost -u $dbUser --password=$dbPass";
			if (isset($dbPort)) {$command.=" --port=".$dbPort;}
			$command .= " $dbDb > $filename";
			$exec = exec($command);
			gzip($filename); // on compresse et on obtient un fichier xxxx.sql.gz
			unlink($filename); // on supprime le fichier xxxx.sql
			$filename=$filename.".gz"; // // $filename : xxxx.sql.gz
		}
		else {
			echo "<p class='centre' style='color: red; font-weight: bold;'>Erreur lors de la sauvegarde&nbsp;: le fichier mysqldump.exe est absent dans le dossier 'gestion'.</p>\n";
		}
	}
	else {
			if ($ver_mysql[0] == "5" OR ($ver_mysql[0] == "4" AND $ver_mysql[1] >= "1")) {
				$command = "mysqldump --skip-opt --add-drop-table --skip-disable-keys --quick -Q --create-options --set-charset --skip-comments -h $dbHost -u $dbUser --password=$dbPass";
				if (isset($dbPort)) {$command.=" --port=".$dbPort;}
				$command .= " $dbDb | gzip > $filename";
			} elseif ($ver_mysql[0] == "4" AND $ver_mysql[1] == "0" AND $ver_mysql[2] >= "17") {
				// Si on est là, c'est que le serveur mysql est d'une version 4.0.17 ou supérieure
				$command = "mysqldump --add-drop-table --quick --quote-names --skip-comments -h $dbHost -u $dbUser --password=$dbPass";
				if (isset($dbPort)) {$command.=" --port=".$dbPort;}
				$command .= " $dbDb | gzip > $filename";
			} else {
				// Et là c'est qu'on a une version inférieure à 4.0.17
				$command = "mysqldump --add-drop-table --quick --quote-names -h $dbHost -u $dbUser --password=$dbPass";
				if (isset($dbPort)) {$command.=" --port=".$dbPort;}
				$command .= " $dbDb | gzip > $filename";
			}
		$exec = exec($command);
	}

	// durée de la sauvegarde
	$t_duree=time()-$t_debut;
	$s=$t_duree%60;
	$t_duree=floor($t_duree/60);
	$m=$t_duree%60;
	$h=floor($t_duree/60);

	if (file_exists($filename)) {
		echo "<script>document.getElementById('sauvegarde_en_cours').innerHTML=''</script>";
		echo "<p class='centre' style='color: red; font-weight: bold;'>La sauvegarde a été réalisée avec succès en ".$h." h ".$m." min ".$s." s.</p>\n";
		if((isset($_POST['description_sauvegarde']))&&($_POST['description_sauvegarde']!='')) {
			$f_desc=fopen($filename.".txt", "a+");
			$description_sauvegarde=suppression_sauts_de_lignes_surnumeraires($_POST['description_sauvegarde']);
			fwrite($f_desc, $description_sauvegarde);
			fclose($f_desc);
		}
	} else {
		echo "<p class='centre' style='color: red; font-weight: bold;'>Erreur lors de la sauvegarde.</p>\n";
	}
}


//Ajout Eric
if (isset($action) and ($action == 'zip'))  {
	check_token();

  define( 'PCLZIP_TEMPORARY_DIR', '../backup/' );
  require_once('../lib/pclzip.lib.php');

  if (isset($dossier_a_archiver)) {

		$suffixe_zip="_le_".date("Y_m_d_\a_H\hi");

        switch ($dossier_a_archiver) {
        case "cdt":
			$chemin_stockage = $path."/_cdt".$suffixe_zip.".zip"; //l'endroit où sera stockée l'archive
			if((isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y')) {
				if((isset($_COOKIE['RNE']))&&($_COOKIE['RNE']!='')) {
					if(!preg_match('/^[A-Za-z0-9]*$/', $_COOKIE['RNE'])) {
						echo "<p style='color:red; text-align:center'>RNE invalide&nbsp;: ".$_COOKIE['RNE']."</p>\n";
						$chemin_stockage="";
					}
					else {
						if (!is_dir('../documents/'.$_COOKIE['RNE'])){
							@mkdir('../documents/'.$_COOKIE['RNE']);
						}
						$dossier_a_traiter = '../documents/'.$_COOKIE['RNE'].'/'; //le dossier à traiter
						$dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive créée
					}
				}
				else {
					echo "<p style='color:red; text-align:center'>RNE invalide.</p>\n";
					$chemin_stockage="";
				}
			}
			else {
				$dossier_a_traiter = '../documents/'; //le dossier à traiter
				$dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive créée
			}

			if ($chemin_stockage !='') {

				$handle=opendir($dossier_a_traiter);
				$tab_file = array();
				$n=0;
				$zip_error=0;
				$zip_debug="n";
				while ($file = readdir($handle)) {
					if(preg_match("#^cl#", $file)) {
						if($zip_debug=='y') {echo "<span style='color:green'>";}
						$enregistrer="y";
					}
					else {
						if($zip_debug=='y') {echo "<span style='color:red'>";}
						$enregistrer="n";
					}
					if($zip_debug=='y') {echo "$file</span><br />";}

					if($enregistrer=="y") {
						if($n==0) {
							$archive = new PclZip($chemin_stockage);
							$v_list = $archive->create("$dossier_a_traiter/$file",
								PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
								PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
							if($v_list==0) {$zip_error++;}
						}
						else {
							$v_list = $archive->add("$dossier_a_traiter/$file",
								PCLZIP_OPT_REMOVE_PATH,$dossier_a_traiter,
								PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
							if($v_list==0) {$zip_error++;}
						}
						$n++;
					}
				}
				closedir($handle);
			
				if ($zip_error != 0) {
					die("<p style='color:red; text-align:center'>Error : ".$archive->errorInfo(true)."</p>");
				}
				elseif($n>0) {
					echo "<p style='color:red; text-align:center;'>Le Zip a été créé (<em>$n dossier(s) archivé(s)</em>).</p>";
				}
				else {
					echo "<p style='color:red; text-align:center;'>Aucun dossier de documents joints à une notice n'a été trouvé.</p>";
				}

			}

			break;
		case "photos":
			$retour=cree_zip_archive_avec_msg_erreur("photos",1);
			if ($retour!="") die("<p style='color:red; text-align:center'>".$retour."</p>\n");

			break;
		default:
			$chemin_stockage = '';
			echo "<p style='color:red; text-align:center;'>La nature de l'archivage à effectuer est inconnue.</p>";
		}
	}
}

if(!isset($quitter_la_page)){
	if(isset($_GET['chgt_annee'])) {$_SESSION['chgt_annee']="y";}

	echo "<p class='bold'><a href='";
	if(isset($_SESSION['chgt_annee'])) {
		echo "changement_d_annee.php";
	}
	else {
		echo "index.php#accueil_sauve";
	}
	echo "'";
	echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
	echo "><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
else {
	echo "<p class='bold'><a href=\"javascript:window.self.close();\"";
	echo ">Refermer la page</a>";
}


// Test présence de fichiers htaccess
if (!(file_exists("../backup/".$dirname."/.htaccess")) or !(file_exists("../backup/".$dirname."/.htpasswd"))) {
    echo "</p >\n";
    echo "<h3 class='gepi'>Répertoire backup non protégé :</h3>\n";
    echo "<p class='rouge'><strong>Le répertoire \"/backup\" n'est actuellement pas protégé</strong>.
    Si vous stockez des fichiers dans ce répertoire, ils seront accessibles de l'extérieur à l'aide d'un simple navigateur.</p>\n";
    echo "<form action=\"accueil_sauve.php\" id=\"protect\" method=\"post\">\n";
    echo "<p >\n";
	echo add_token_field();
    echo "</p >\n";
    echo "<table><tr><td><label for='login_backup'>Nouvel identifiant : </label></td><td><input type=\"text\" id=\"login_backup\" name=\"login_backup\" value=\"\" size=\"20\" /></td></tr>\n";
    echo "<tr><td><label for='pwd1_backup'>Nouveau mot de passe : </label></td><td><input type=\"password\" id=\"pwd1_backup\" name=\"pwd1_backup\" value=\"\" size=\"20\" /></td></tr>\n";
    echo "<tr><td><label for='pwd2_backup'>Confirmation du mot de passe : </label></td><td><input type=\"password\" id=\"pwd2_backup\" name=\"pwd2_backup\" value=\"\" size=\"20\" /></td></tr></table>\n";

    echo "<p class=\"center\"><input type=\"submit\" value=\"Envoyer\" /></p>\n";
    echo "<p >\n";
    echo "<input type=\"hidden\" name=\"action\" value=\"protect\" />\n";
    echo "</p >\n";
    echo "</form>\n";
    echo "<hr />\n";
} else {
    echo " | <a href='#' onClick=\"clicMenu('2')\" style=\"cursor: hand\"><b>Protection du répertoire backup</b></a>\n";
    echo "<div style=\"display:none\" id=\"menu2\">\n";

    echo "<table border=\"1\" cellpadding=\"5\" bgcolor=\"#C0C0C0\">
	<tr>
		<td>
			<h3 class='gepi'>Protection du répertoire backup :</h3>\n";
    echo "
			<p>Le répertoire \"/backup\" est actuellement protégé par un identifiant et un mot de passe.
			Pour accéder aux fichiers stockés dans ce répertoire à partir d'un navigateur web, il est nécessaire de s'authentifier.
			<br /><br />Cliquez sur le bouton ci-dessous pour <b>supprimer la protection</b>
			ou bien pour définir un nouvel <b>identifiant et un mot de passe</b></p>\n";
    echo "
			<form action=\"accueil_sauve.php\" id=\"del_protect\" method=\"post\">\n";
    echo "<p>\n";
	echo add_token_field();
    echo "</p >\n";
    echo "
			<p class=\"center\"><input type=\"submit\" value=\"Modifier/supprimer la protection du répertoire\" /></p>\n";
    echo "
			<p><input type=\"hidden\" name=\"action\" value=\"del_protect\" /></p>\n";
    echo "
			</form>
		</td>
	</tr>
</table>\n";
    echo "<hr /></div>\n";
}

?>

<h3>Créer un fichier de sauvegarde/restauration de la base <?php echo $dbDb; ?></h3>
<p>Deux méthodes de sauvegarde sont disponibles : l'utilisation de la commande système mysqldump ou bien le système intégré à Gepi.<br/>
La première méthode (mysqldump) est vigoureusement recommandée car beaucoup moins lourde en ressources, mais ne fonctionnera que sur certaines configurations serveurs.<br />
La seconde méthode est lourde en ressources mais passera sur toutes les configurations.</p>
<?php
if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN') && !file_exists("mysqldump.exe"))
	{
?>
<p>
    <b>
        <span class="rouge">Attention : </span>
    </b>pour utiliser la commande système mysqldump lorsque Gepi est hébergé sous Windows il faut au préalable copier le fichier "mysqldump.exe" dans le dossier "gestion" de Gepi. Ce fichier "mysqldump.exe" se trouve généralement dans le sous-dossier "bin" du dossier d'installation de MySQL.
</p>
<?php
	}
?>

<form enctype="multipart/form-data" action="accueil_sauve.php" method="post" id="formulaire">
    <p>
<?php
	echo add_token_field();
?>
    </p>
    <div class="center">
        <p><input type="submit" value="Sauvegarder" /></p>
        <label for='action' class='invisible'>type de sauvegarde</label>
        <select id='action' name='action' size='1'>
            <option value='dump'<?php if (getSettingValue("mode_sauvegarde") == "gepi") echo " selected='selected'";?>>sans mysqldump</option>
<?php
if ((strtoupper(substr(PHP_OS,0,3)) == 'WIN' && file_exists("mysqldump.exe"))||
	(strtoupper(substr(PHP_OS,0,3)) != 'WIN'))
	{
?>
            <option value='system_dump' 
			<?php if (strtoupper(substr(PHP_OS,0,3)) == 'WIN' && !file_exists("mysql.exe")) echo " disabled";
			 else if (getSettingValue("mode_sauvegarde") == "mysqldump") echo " selected='selected'";?>
			>avec mysqldump</option>
<?php
	}
?>
        </select>
        <p>
            <label for='description_sauvegarde'>Description (<em>facultative</em>) de la sauvegarde&nbsp;:</label><br />
            <textarea id='description_sauvegarde' name='description_sauvegarde' cols='30' rows='2'></textarea>
        </p>
    </div>
</form>

<p>
    <span class='small'>
        <b>Remarque</b> : les répertoires 'documents' (contenant les documents joints aux cahiers de textes) et 'photos' (contenant les photos du trombinoscope) ne seront pas sauvegardés. Un outil de sauvegarde spécifique se trouve en bas de <a href='#zip'>cette page</a>.
    </span>
<hr />


<?php

$tab_file=get_tab_fichiers_du_dossier_de_sauvegarde('../backup/' . $dirname);
$n=count($tab_file);

if ($n > 0) {
	/*
	echo "<div style='float:left; width:50em;' class='fieldset_opacite50'>";
	echo "<pre>";
	print_r($tab_file);
	echo "</pre>";
	echo "</div>";

	echo "<div style='float:left; width:50em;' class='fieldset_opacite50'>";
	echo "<pre>";
	for($m=0;$m<count($tab_file);$m++) {
	echo "\$tab_file[$m]=".$tab_file[$m]."\n";
	}
	echo "</pre>";
	echo "</div>";
	*/

	echo "<h3>Fichiers de restauration</h3>\n";

	// 20210419
	echo "<form enctype=\"multipart/form-data\" action=\"accueil_sauve.php\" method=\"post\" id=\"form_tableau_svg\">
	<input type='hidden' name='supprimer_fichiers_coches' value='y' />\n";
	echo add_token_field();

	echo "<p>Le tableau ci-dessous indique la liste des fichiers de restauration actuellement stockés dans le répertoire \"backup\" à la racine de GEPI.</p>\n";
	// echo "<table class='boireaus centre' cellpadding=\"5\" cellspacing=\"1\">\n<tr><th><strong>Nom du fichier de sauvegarde</strong></th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr>\n";
	echo "<table class='boireaus centre resizable sortable' style='margin:auto;' >
	<tr>
		<th class='text'>
			<strong>Nom du fichier de sauvegarde</strong>
		</th>
		<th class='number'>Taille</th>
		<th class='number'>Date</th>
		<th class='nosort'>
			<a href='#' onclick=\"accueil_sauve_cocher_decocher_tous_checkbox('suppr_fich_svg_', true)\"><img src='../images/enabled.png' class='icone20' /></a> / 
			<a href='#' onclick=\"accueil_sauve_cocher_decocher_tous_checkbox('suppr_fich_svg_', false)\"><img src='../images/disabled.png' class='icone20' /></a><br />
			<input type='submit' value='Supprimer' title=\"Supprimer tous les fichiers cochés\" />
		</th>
		<th class='nosort'>&nbsp;</th>
		<th class='nosort'>&nbsp;</th>
		<th class='nosort'>&nbsp;</th>
	</tr>\n";
	$m = 0;
	$alt=1;
	//foreach($tab_file as $value) {
	for($m=0;$m<count($tab_file);$m++) {
		$value=$tab_file[$m];
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td style='padding:5px;'>\n";
		echo "<em>";
		if(file_exists('../backup/'.$dirname.'/'.$value.'.txt')) {
			$handle = fopen('../backup/'.$dirname.'/'.$value.'.txt', "r");
			$contents = fread($handle, filesize('../backup/'.$dirname.'/'.$value.'.txt'));
			fclose($handle);
			$contents=preg_replace('/"/', "", $contents);

			$titre_infobulle=$value;
			$texte_infobulle=nl2br($contents);
			$tabdiv_infobulle[]=creer_div_infobulle('div_description_svg_'.$m,$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');

			echo "<a href='#' onmouseover=\"delais_afficher_div('div_description_svg_$m','y',-20,20,1000,20,20);\" onclick=\"afficher_div('div_description_svg_$m','y',-20,20); return false\" onmouseout=\"cacher_div('div_description_svg_$m')\">";
			echo $value;
			echo "</a>";
			//$m++;
		}
		else {
			echo $value;
		}
		$tmp_taille=filesize("../backup/".$dirname."/".$value);
		$tmp_taille_ko=round(($tmp_taille/1024),0);
		echo "</em>";
		//echo "&nbsp;&nbsp;(".$tmp_taille_ko." Ko)\n";
		echo "</td>\n";
		echo "<td><span style='display:none'>$tmp_taille.</span>".$tmp_taille_ko." Ko</td>\n";
		echo "<td>";
		$tmp_date=filemtime("../backup/".$dirname."/".$value);
		echo "<span style='display:none'>".$tmp_date."</span>".strftime("%d/%m/%Y %H:%M:%S", $tmp_date);
		echo "</td>\n";
		// 20210419
		echo "<td style='padding:5px;'>
			<a href='accueil_sauve.php?action=sup&amp;file=$value".add_token_in_url()."'>Supprimer</a>
			<input type='checkbox' name='suppr_fich_svg[]' id='suppr_fich_svg_".$m."' value=\"".$value."\" />
		</td>\n";
		$type_sauvegarde="";
		if (preg_match('/^_photos/i',$value)&& preg_match('/.zip$/i',$value))$type_sauvegarde="photos";
		if (preg_match('/^_cdt/i',$value)&& preg_match('/.zip$/i',$value)) $type_sauvegarde="cdt";
		if ((preg_match('/.sql.gz$/i',$value) || preg_match('/.sql$/i',$value))) $type_sauvegarde="base";
		switch ($type_sauvegarde) {
			case "photos" :
				echo "<td style='padding:5px;'><a href='../mod_trombinoscopes/trombinoscopes_admin.php?action=restaurer_photos&amp;file=$value".add_token_in_url()."'>Restaurer</a></td>\n</td>\n";
				break;
			case "base" :
				echo "<td style='padding:5px;'><a href='accueil_sauve.php?action=restaure_confirm&amp;file=$value".add_token_in_url()."#restaurer'>Restaurer</a></td>\n";
				break;
			default :
				echo "<td></td>\n";
				break;
		}
		echo "<td style='padding:5px;'><a href='savebackup.php?fileid=$m'>Télécharger</a></td>\n";
		echo "<td style='padding:5px;'><a href='../backup/".$dirname."/".$value."'>Téléch. direct</a></td>\n";
		echo "</tr>\n";
		//$m++;
	}
	clearstatcache();
	// 20210419
	echo "</table>
	</form>
	<hr />
	<script type='text/javascript'>
		function accueil_sauve_cocher_decocher_tous_checkbox(prefixe, mode) {
			for(i=0;i<$m;i++) {
				if(document.getElementById(prefixe+i)) {
					document.getElementById(prefixe+i).checked=mode;
				}
			}
		}
	</script>";
}

if($temoin_dossier_backup_absences=="y") {
	$tab_file=get_tab_fichiers_du_dossier_de_sauvegarde('../backup/' . $dirname."/absences", "y");
	$n=count($tab_file);

	if($n>0) {
		echo "<h3>Fichiers export des absences</h3>\n";
		echo "<p>Les fichiers d'export des absences en fin d'année sont générés dans le sous-dossier 'absences' du dossier de stockage des sauvegardes.</p>";
		echo "<table class='boireaus boireaus_alt centre sortable resizable' style='margin:auto;' >
	<tr>
		<th class='text'>Nom du fichier d'export</th>
		<th class='nosort'>&nbsp;</th>
		<th class='nosort'>&nbsp;</th>
		<th class='nosort'>&nbsp;</th>
	</tr>";
		$m = 0;
		//foreach($tab_file as $value) {
		for($m=0;$m<count($tab_file);$m++) {
			$value=$tab_file[$m];
			$alt=$alt*(-1);
			echo "
	<tr>
		<td style='padding:5px;'>
			<em>".$value."</em>&nbsp;&nbsp;(". round((filesize("../backup/".$dirname."/absences/".$value)/1024),0)." Ko)
		</td>
		<td style='padding:5px;'>
			<a href='accueil_sauve.php?action=sup&amp;sous_dossier=absences&amp;file=$value".add_token_in_url()."'>Supprimer</a>
		</td>
		<td style='padding:5px;'>
			<a href='savebackup.php?fileid=$m&amp;sous_dossier=absences'>Télécharger</a>
		</td>
		<td style='padding:5px;'>
			<a href='../backup/".$dirname."/absences/".$value."'>Téléch. direct</a>
		</td>
	</tr>\n";
			//$m++;
		}
		clearstatcache();
		echo "
</table>
<hr />\n";
	}
}

echo "<h3>Uploader un fichier (de restauration) vers le répertoire backup</h3>\n";
echo "<form enctype=\"multipart/form-data\" action=\"accueil_sauve.php\" method=\"post\" id=\"formulaire2\">\n";

echo "<p >\n";
echo add_token_field();
echo "</p >\n";
$sav_file="";
echo "<p >\n";
echo "Les fichiers de sauvegarde sont sauvegardés dans un sous-répertoire du répertoire \"/backup\", dont le nom change de manière aléatoire régulièrement.
Si vous le souhaitez, vous pouvez uploader un fichier de sauvegarde directement dans ce répertoire.
Une fois cela fait, vous pourrez le sélectionner dans la liste des fichiers de restauration, sur cette page.\n";
echo "</p >\n";
echo "<p >\n";
echo "Vous pouvez également directement télécharger le fichier par ftp dans le répertoire \"/backup\".\n";
echo "</p >\n";
echo "<p >\n";
echo "<br />
    <label for='sav_file'><strong>Fichier à \"uploader\" </strong>: </label>
    <input type=\"file\" id=\"sav_file\" name=\"sav_file\" />
    <input type=\"hidden\" name=\"action\" value=\"upload\" />
    <input type=\"submit\" value=\"Valider\" name=\"bouton1\" />
    </p >
</form>\n";

$post_max_size=ini_get('post_max_size');
$upload_max_filesize=ini_get('upload_max_filesize');
echo "<p><b>Attention:</b></p>\n";
echo "<p style='margin-left: 20px;'>Selon la configuration du serveur et la taille du fichier, l'opération de téléchargement vers le répertoire \"/backup\" peut échouer
(<i>par exemple si la taille du fichier dépasse la <b>taille maximale autorisée lors des téléchargements</b></i>).
<br />Si c'est le cas, signalez le problème à l'administrateur du serveur.</p>\n";

echo "<table class='boireaus center'>\n";
echo "<tr><th style='font-weight: bold; text-align: center;'>Variable</th><th style='font-weight: bold; text-align: center;'>Valeur</th></tr>\n";
echo "<tr class='lig1'><td style='font-weight: bold; text-align: center;'>post_max_size</td><td style='text-align: center;'>$post_max_size</td></tr>\n";
echo "<tr class='lig-1'><td style='font-weight: bold; text-align: center;'>upload_max_filesize</td><td style='text-align: center;'>$upload_max_filesize</td></tr>\n";
echo "</table>\n";

echo "<br /><hr />";
echo "<h3 id=\"zip\">Créer une archive (Zip) de dossiers de Gepi</h3>\n";
echo "Une fois créée, pour télécharger l'archive, rendez-vous à la section \"Fichiers de restauration\" de cette page. <br />";
echo "<p style=\"color: red;\">ATTENTION : veillez à supprimer le fichier créé une fois l'archive téléchargée.</p>";
echo "<form enctype=\"multipart/form-data\" action=\"accueil_sauve.php\" method=\"post\" id=\"formulaire3\">\n";
echo "<p >\n";
echo add_token_field();
echo "<br />Dossier à sauvegarder :<br />";
echo "</p >\n";

$dossier_photos = '../photos/'; //le dossier photos
$dossier_documents = '../documents/'; //le dossier documents
$dossiers_OK = true;


if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	//$dossier_photos .=$_COOKIE['RNE']."/";
	if((isset($_COOKIE['RNE']))&&($_COOKIE['RNE']!='')) {
		if(!preg_match('/^[A-Za-z0-9]*$/', $_COOKIE['RNE'])) {
			echo "<p style='color:red; text-align:center'>RNE invalide&nbsp;: ".$_COOKIE['RNE']."</p>\n";
			$dossiers_OK = false;
		}
		else {
			$dossier_photos = '../photos/'.$_COOKIE['RNE'].'/'; //le dossier photos
			$dossier_documents = '../documents/'.$_COOKIE['RNE'].'/'; //le dossier documents
		}
	}
	else {
		echo "<p style='color:red; text-align:center'>RNE invalide.</p>\n";
		$dossiers_OK = false;
	}
}
echo "<p><input type=\"radio\" name=\"dossier\" id=\"dossier_photos\" value=\"photos\" checked='checked' /><label for='dossier_photos'> Dossier Photos (<em>_photos_le_DATE_a_HEURE.zip</em>)</label>";
if ($dossiers_OK) {
	echo "<br />&nbsp;&nbsp;(<em>volume du dossier 'photos'&nbsp;: ".volume_dir_human($dossier_photos)."</em>)";
}
echo "<br />\n";

if(!getSettingAOui('active_module_trombinoscopes')) {echo "<span style='color:red; margin-left:2em;'>Le module Trombinoscopes est <a href='../mod_trombinoscopes/trombinoscopes_admin.php'>inactif</a>, il ne devrait pas y avoir de photos à archiver.</span><br />";}
echo "<input type=\"radio\" name=\"dossier\" id=\"dossier_cdt\" value=\"cdt\" /><label for='dossier_cdt'> Dossier documents du cahier de textes (<em>_cdt_le_DATE_a_HEURE.zip</em>)</label>\n";
if ($dossiers_OK) {
	echo "<br />&nbsp;&nbsp;(<em>volume du dossier 'documents'&nbsp;: ".volume_dir_human($dossier_documents).", dont  ".volume_dir_human($dossier_documents."/archives")." dans le sous-dossier 'archives'";
	if (is_dir($dossier_documents."/discipline")) {
		echo " et ".volume_dir_human($dossier_documents."/discipline")." dans le sous-dossier 'discipline'";
		echo " qui ne seront";
	} else echo " qui ne sera";
	echo " pas inclus dans l'archive</em>)";
}
echo "<br />\n";
if((!getSettingAOui('active_cahiers_texte'))&&(!getSettingAOui('acces_cdt_prof'))) {echo "<span style='color:red; margin-left:2em;'>Le module Cahiers de textes est <a href='../cahier_texte_admin/index.php'>inactif</a>, il ne devrait pas y avoir de documents à archiver</span><br />";}
echo "<br />\n";
echo "<input type=\"hidden\" name=\"action\" value=\"zip\" />\n
	  <input type=\"submit\" value=\"Créer l'archive\" name=\"bouton3\" />\n
      </p>
	  </form>\n";

echo "<br /><hr />";

echo "<h3 id=\"rw\">Contrôle des dossiers devant être accessibles en écriture pour la sauvegarde/restauration</h3>\n";

$tab_restriction=array("backup");
test_ecriture_dossier($tab_restriction);
echo "<br />\n";
echo "<p>Les autres fichiers et dossiers devant être accessibles en écriture peuvent être contrôlés dans la page <a href='../mod_serveur/test_serveur.php'>Configuration serveur</a>";
echo "<br />\n";
echo "&nbsp;\n";
echo "</p>\n";

require("../lib/footer.inc.php");
?>

<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) {$traite_anti_inject = "yes";}

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec ckeditor
if (isset($_POST['notes'])) {$traite_anti_inject = 'no';}

$filtrage_extensions_fichiers_table_ct_types_documents='y';

// Initialisations files
require_once("../lib/initialisations.inc.php");
require_once("../public/lib/functions.inc");
include("../ckeditor/ckeditor.php") ;

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

$message_avertissement_navigateur = "";
////on regarde si les preferences pour le cdt ont change
if (getSettingValue("GepiCahierTexteVersion") == '2') {
    //on regarde les preferences de l'utilisateur
    if (getPref($_SESSION['login'],'cdt_version',"non renseigne") != "1" ) {
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6' ) !== FALSE) {
			//on reste sur le cdt1, le navigateur n'etant pas compatible avec le cdt2
			$message_avertissement_navigateur = "Votre navigateur n'est pas compatible avec le cahier de texte 2, mais vous pouvez utiliser la version 1.";
		} else {
			$temp_header = "Location: ../cahier_texte_2/index.php";

			$ajout_temp_header="";
			$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
			if ($id_groupe != NULL) {
				$ajout_temp_header .= "?id_groupe=" . $id_groupe;
			}

			$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
			if ($id_ct != NULL) {
				if($ajout_temp_header=="") {$ajout_temp_header.="?";}
				else {$ajout_temp_header.="&";}
				$ajout_temp_header .= "id_ct=" . $id_ct;
			}

			$type_notice = isset($_POST["type_notice"]) ? $_POST["type_notice"] :(isset($_GET["type_notice"]) ? $_GET["type_notice"] :NULL);
			if ($type_notice != NULL) {
				if($ajout_temp_header=="") {$ajout_temp_header.="?";}
				else {$ajout_temp_header.="&";}
				$ajout_temp_header .= "type_notice=" . $type_notice;
			}
			
			$temp_header.=$ajout_temp_header;

			header($temp_header);
			die();
		}
    }
}


//Ajout Eric traitement Visa
$visa_cdt_inter_modif_notices_visees=getSettingValue("visa_cdt_inter_modif_notices_visees");

include "../lib/mincals.inc";

// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
if(!isset($_SESSION['uid_prime'])) {
    $_SESSION['uid_prime']='';
}

$uid_post = isset($_POST["uid_post"]) ? $_POST["uid_post"] :(isset($_GET["uid_post"]) ? $_GET["uid_post"] :NULL);
$uid = md5(uniqid(microtime(), 1));
if ($uid_post==$_SESSION['uid_prime']) {
    $valide_form = 'yes';
}
else {
    $valide_form = 'no';
}

$_SESSION['uid_prime'] = $uid;

// initialisation des variables
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
if ($id_ct  == '') {$id_ct =NULL;}

$edit_devoir = isset($_POST["edit_devoir"]) ? $_POST["edit_devoir"] :(isset($_GET["edit_devoir"]) ? $_GET["edit_devoir"] :NULL);
if ($edit_devoir  == '') {$edit_devoir =NULL;}

$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
$id_matiere = isset($_POST["id_matiere"]) ? $_POST["id_matiere"] : (isset($_GET["id_matiere"]) ? $_GET["id_matiere"] : -1);
$day = isset($_POST["day"]) ? $_POST["day"] :(isset($_GET["day"]) ? $_GET["day"] :date("d"));
$month = isset($_POST["month"]) ? $_POST["month"] :(isset($_GET["month"]) ? $_GET["month"] :date("m"));
$year = isset($_POST["year"]) ? $_POST["year"] :(isset($_GET["year"]) ? $_GET["year"] :date("Y"));
$heure_entry = isset($_POST["heure_entry"]) ? $_POST["heure_entry"] :(isset($_GET["heure_entry"]) ? $_GET[""] :NULL);
$ajout = isset($_POST["ajout"]) ? $_POST["ajout"] :(isset($_GET["ajout"]) ? $_GET["ajout"] :'');
$info = isset($_POST["info"]) ? $_POST["info"] :(isset($_GET["info"]) ? $_GET["info"] :NULL);
$doc_name = isset($_POST["doc_name"]) ? $_POST["doc_name"] :(isset($_GET["doc_name"]) ? $_GET["doc_name"] :NULL);
$doc_name_modif = isset($_POST["doc_name_modif"]) ? $_POST["doc_name_modif"] :(isset($_GET["doc_name_modif"]) ? $_GET["doc_name_modif"] :NULL);
$id_document = isset($_POST["id_document"]) ? $_POST["id_document"] :(isset($_GET["id_document"]) ? $_GET["id_document"] :NULL);
if (!isset($_SESSION['type_display_notices'])) {$_SESSION['type_display_notices'] = "all";}
if (isset($_GET["type_display_notices"])) {$_SESSION['type_display_notices'] = $_GET["type_display_notices"];}
if (empty($_FILES['doc_file'])) { $doc_file=''; } else { $doc_file=$_FILES['doc_file'];}

$heure_visibilite=isset($_POST['heure_visibilite']) ? $_POST['heure_visibilite'] : NULL;
$jour_visibilite=isset($_POST['jour_visibilite']) ? $_POST['jour_visibilite'] : NULL;

//debug_var();

// Initialisation de la valeur delai
$delai = getSettingValue("delai_devoirs");

//on met le groupe dans la session, pour naviguer entre absence, cahier de texte et autres
if ($id_groupe != "") {
    $_SESSION['id_groupe_session'] = $id_groupe;
} else if (isset($_SESSION['id_groupe_session']) and  $_SESSION['id_groupe_session'] != "") {
     $id_groupe = $_SESSION['id_groupe_session'];
}


// $id_ct : identifiant de la notice
// $edit_devoir : si $edit_devoir est défini, la notice est de type "devoir à faire", sinon, elle est de type "compte-rendu"
// $id_groupe : identifiant du groupe
// $id_matiere : identifiant de la matière
// $day : le jour courant
// $month : le mois courant
// $year : l'année courante
// $heure_entry : heure de création d'une notice
// $ajout :  prend la valeur "yes" ou bien n'est pas défini. $ajout='yes' si on ajoute une notice à une journée lorqu'une notice existe déjà
// $info  : si $info est défini, la notice en cours de modification est la notice d'information générale
// $doc_name : tableau contenant les noms des documents joints
// $doc_name_modif : nouveau nom d'un document
// $id_document : tableau des identifiants des documents joints

if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

// Vérification : est-ce que l'utilisateur a le droit d'être ici ?
if (($current_group["id"] != "") ) {
    if (!check_prof_groupe($_SESSION['login'],$current_group["id"])) {
        header("Location: ../logout.php?auto=1");
        die();
    }
}

// Modification d'un enregistrement
// on récupère la date
if (isset($id_ct))
 {
     if (isset($edit_devoir)) {
         $sql = "SELECT date_ct FROM ct_devoirs_entry WHERE id_ct='$id_ct'";
     } else {
         $sql = "SELECT date_ct FROM ct_entry WHERE id_ct='$id_ct'";
    }
    //echo "$sql<br />";
    // On récupère la date dans la table
    $date_ct = sql_query1($sql);

    if ($date_ct != 0) {
        // Il ne s'agit pas de la notice d'info générale : la date courante devient celle de la notice
        $day = strftime("%d", $date_ct);
        $month = strftime("%m", $date_ct);
        $year = strftime("%Y", $date_ct);
    } else {
        // Il s'agit de la notice d'info générale, on ne change pas date courante
        $day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
        $month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
        $year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
    }

	if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
		// On vérifie si l'utilisateur est proprio
		if (isset($edit_devoir)) {
			$sql = "SELECT date_ct FROM ct_devoirs_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
		}
		else {
			$sql = "SELECT date_ct FROM ct_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
		}
		//echo "$sql<br />";
		$test_proprio=mysql_query($sql);
		if(mysql_num_rows($test_proprio)==0) {
			unset($id_ct);
		}
	}
}

// Vérification
settype($month,"integer");
settype($day,"integer");
settype($year,"integer");
$minyear = strftime("%Y", getSettingValue("begin_bookings"));
$maxyear = strftime("%Y", getSettingValue("end_bookings"));
if ($day < 1) {$day = 1;}
if ($day > 31) {$day = 31;}
if ($month < 1) {$month = 1;}
if ($month > 12) {$month = 12;}
if ($year < $minyear) {$year = $minyear;}
if ($year > $maxyear) {$year = $maxyear;}

$sday=$day;
$smonth=$month;
$syear=$year;
# Make the date valid if day is more then number of days in month
while (!checkdate($month, $day, $year)) {$day--;}
$message_suppression = "Confirmation de suppression";

// $today : date courante
$today = mktime(0,0,0,$month,$day,$year);
$aujourdhui = mktime(0,0,0,date("m"),date("d"),date("Y"));
// On donne toutes les informations pour le jour de demain
if (isset($today)) {
	$lendemain = $today + 86400;
} else {
	$lendemain = $aujourdhui + 86400;
}
$jour_lendemain = date("d", $lendemain);
$mois_lendemain = date("m", $lendemain);
$annee_lendemain = date("Y", $lendemain);

// Suppression de plusieurs notices
if ((isset($_POST['action'])) and ($_POST['action'] == 'sup_serie') and $valide_form=='yes') {
   check_token();

   $error = 'no';
   $sup_date = mktime(0,0,0,$_POST['sup_month'],$_POST['sup_day'],$_POST['sup_year']);
   if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
      $sql="SELECT id_ct  FROM ct_entry WHERE (id_groupe='".$current_group["id"]."' and date_ct != '' and date_ct < '".$sup_date."' AND id_login='".$_SESSION['login']."')";
   }
   else {
      $sql="SELECT id_ct  FROM ct_entry WHERE (id_groupe='".$current_group["id"]."' and date_ct != '' and date_ct < '".$sup_date."')";
   }
   $appel_ct = sql_query($sql);
   if (($appel_ct) and (sql_count($appel_ct)!=0)) {
     for ($i=0; ($row = sql_row($appel_ct,$i)); $i++) {
       $id_ctexte = $row[0];
       $appel_doc = sql_query("select emplacement from ct_documents where id_ct='".$id_ctexte."'");
       for ($j=0; ($row2 = sql_row($appel_doc,$j)); $j++) {
          $empl = $row2[0];
          if ($empl != -1) $del = @unlink($empl);
       }
       $del_doc = sql_query("delete from ct_documents where id_ct='".$id_ctexte."'");
       if (!($del_doc)) $error = 'yes';
	   //Modif Eric ==> ne pas supprimer les visas et les notices visées
       //$del_ct = sql_query("delete from ct_entry where id_ct='".$id_ctexte."'");
	   $del_ct = sql_query("delete from ct_entry where (id_ct='".$id_ctexte."' and vise != 'y')");
       if (!($del_ct)) $error = 'yes';
     }
     if ($error == 'no') {
        $msg = "Suppression réussie";
     } else {
        $msg = "Il y a eu un problème lors de la suppression.";
     }
   } else {
     $msg = "Rien a supprimer.";
   }
}

//
// Suppression d'une notice
//
if ((isset($_GET['action'])) and ($_GET['action'] == 'sup_entry') and $valide_form=='yes') {
	check_token();

	$suppression_possible="y";
	if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
		$sql="SELECT 1=1 FROM ct_entry WHERE (id_ct='".$_GET['id_ct_del']."' AND id_login='".$_SESSION['login']."')";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)==0) {
			$suppression_possible="n";
			$msg="Vous n'êtes pas l'auteur de la notice que vous souhaitez supprimer.<br />";
		}
	}

	if($suppression_possible=='y') {
		$architecture= "/documents/cl_dev";
		$sql = "select id from ct_documents where id_ct='".$_GET['id_ct_del']."'";
		$res = sql_query($sql);
		if (($res) and (sql_count($res)!=0)) {
			$msg = "Impossible de supprimer cette notice : Vous devez d'abord supprimer les documents joints";
		} else {
			//modif Eric interdire la suppression de notice visée
			$res = sql_query("delete from ct_entry where (id_ct = '".$_GET['id_ct_del']."' and vise != 'y')");
			if ($res) $msg = "Suppression réussie";
		}
	}
}
//
// Suppression d'un devoir
//
if ((isset($_GET['action'])) and ($_GET['action'] == 'sup_devoirs') and $valide_form=='yes') {
   check_token();

	$suppression_possible="y";
	if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
		$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE (id_ct='".$_GET['id_ct_del']."' AND id_login='".$_SESSION['login']."')";
		$res_test=mysql_query($sql);
		if(mysql_num_rows($res_test)==0) {
			$suppression_possible="n";
			$msg="Vous n'êtes pas l'auteur de la notice que vous souhaitez supprimer.<br />";
		}
	}

	if($suppression_possible=='y') {
	    $architecture= "/documents/cl_dev";
	    $sql = "select id from ct_devoirs_documents where id_ct_devoir='".$_GET['id_ct_del']."' AND emplacement LIKE '%".$architecture."%'";
	    $res = sql_query($sql);
	    if (($res) and (sql_count($res)!=0)) {
		  $msg = "Impossible de supprimer cette notice : Vous devez d'abord supprimer les documents joints";
	    } else {
		//modif Eric interdire la suppression de notice visée
	    $res = mysql_query("delete from ct_devoirs_entry where (id_ct = '".$_GET['id_ct_del']."' and vise != 'y')");
		  if ($res) $msg = "Suppression réussie";
	    }
	}

}
//
// Insertion ou modification d'une notice
//
if (isset($_POST['notes']) and $valide_form=='yes') {
   check_token();

    // Cas des devoirs
	if (isset($edit_devoir)) {
		$msg="";
		//==========================================================
		$date_visibilite_mal_formatee="n";
		if((isset($jour_visibilite))&&(isset($heure_visibilite))) {
			//echo "$heure_visibilite<br />\n";
			if(!preg_match("/^[0-9]{1,2}:[0-9]{1,2}$/",$heure_visibilite)) {
				$heure_courante=strftime("%H:%M");
				//echo "Heure de visibilité mal formatée : $heure_visibilite<br />";
				//die();
				if (isset($id_ct))  {
					$msg.="Heure de visibilité mal formatée : $heure_visibilite.<br />L'heure de visibilité ne sera pas modifiée.<br />";
				}
				else {
					$msg.="Heure de visibilité mal formatée : $heure_visibilite.<br />L'heure courante sera utilisée : $heure_courante<br />";
				}
				$heure_visibilite=$heure_courante;

				$date_visibilite_mal_formatee="y";
			}
			$tab_tmp=explode(":",$heure_visibilite);
			$heure_v=$tab_tmp[0];
			$min_v=$tab_tmp[1];
			
			//if(!preg_match("#^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$#",$jour_visibilite)) {
			if(!preg_match( '`^\d{1,2}/\d{1,2}/\d{4}$`', $jour_visibilite)) {
				$jour_courant=strftime("%d/%m/%Y");
				//echo "Le jour de visibilité est mal formaté : $jour_visibilite<br />";
				//die();
				if (isset($id_ct))  {
					$msg.="Le jour de visibilité est mal formaté : $jour_visibilite.<br />Le jour de visibilité ne sera pas modifié.<br />";
				}
				else {
					$msg.="Le jour de visibilité est mal formaté : $jour_visibilite.<br />Le jour courant sera utilisé : $jour_courant<br />";
				}
				//echo "alert('Le jour de visibilité est mal formaté : $jour_visibilite. Le jour courant sera utilisé : $jour_courant')";
				$jour_visibilite=$jour_courant;

				$date_visibilite_mal_formatee="y";
			}
			$tab_tmp=explode("/",$jour_visibilite);
			$jour_v=$tab_tmp[0];
			$mois_v=$tab_tmp[1];
			$annee_v=$tab_tmp[2];

			//$date_visibilite_eleve=mktime($heure_v,$min_v,0,$mois_v,$jour_v,$annee_v);
			//echo "\$date_visibilite_eleve=mktime($heure_v,$min_v,0,$mois_v,$jour_v,$annee_v)=$date_visibilite_eleve<br />";

			$date_visibilite_eleve="$annee_v-$mois_v-$jour_v $heure_v:$min_v";
			//echo "\$date_visibilite_eleve=$date_visibilite_eleve<br />";
		}
		//==========================================================

        // Il s'agit d'un devoir à faire : on récupère la date à l'aide de $_POST['display_date']
        if (preg_match("#([0-9]{2})/([0-9]{2})/([0-9]{4})#", $_POST['display_date'])) {
            $_year = mb_substr($_POST['display_date'],6,4);
            $_month = mb_substr($_POST['display_date'],3,2);
            $_day = mb_substr($_POST['display_date'],0,2);
            $date_travail_a_faire=mktime(0,0,0,$_month,$_day,$_year);
        } else {
            $msg_error_date = "La date choisie pour le travail à faire n'est pas conforme";
		}
        $contenu_cor = traitement_magic_quotes(corriger_caracteres($_POST['notes']),'');
        if ($contenu_cor == '') {$contenu_cor="...";}

        if (!isset($msg_error_date)) {
          if (isset($id_ct)) {
			// 20130727:
			$contenu_precedent="";
			$sql="SELECT * FROM ct_devoirs_entry WHERE id_ct='$id_ct';";
			//echo "$sql<br />";
			$req = mysql_query($sql);
			if(mysql_num_rows($req)>0) {
				$contenu_precedent=mysql_result($req, 0, 'contenu');
				if($contenu_precedent!=$contenu_cor) {
					$date_modif=strftime("%Y-%m-%d %H:%M:%S");
					$sql="UPDATE ct_devoirs_faits SET etat='', commentaire='Le professeur a modifié la notice de travail à faire ($date_modif).', date_modif='".$date_modif."' WHERE id_ct='$id_ct';";
					//echo "$sql<br />";
					$update=mysql_query($sql);
				}
			}

            // Modification d'un devoir
            $sql="UPDATE ct_devoirs_entry SET contenu = '$contenu_cor', id_login='".$_SESSION['login']."', date_ct='$date_travail_a_faire'";
			if((isset($date_visibilite_eleve))&&($date_visibilite_mal_formatee=="n")) {$sql.=", date_visibilite_eleve='$date_visibilite_eleve'";}
			$sql.=" WHERE id_ct='$id_ct';";
			//echo "$sql<br />";
            $req = mysql_query($sql);
          } else {
            // On insère la notice dans ct_devoirs_entry
            $sql="INSERT INTO ct_devoirs_entry SET id_ct='0', contenu = '$contenu_cor', id_login='".$_SESSION['login']."', id_groupe='".$id_groupe."', date_ct='$date_travail_a_faire'";
			if(isset($date_visibilite_eleve)) {$sql.=", date_visibilite_eleve='$date_visibilite_eleve'";}
			$sql.=";";
			//echo "$sql<br />";
            $req = mysql_query($sql);
            $id_ct = mysql_insert_id();
          }
          if ($req) {$msg.= "Enregistrement réussi.";} else {$msg .= "Problème lors de l'enregistrement !";}
        } else {
          $msg = $msg_error_date;
		}
    } else {
        // Cas d'une notice
        isset($_POST['info']) ? $temp = '' : $temp = $today;
        //$contenu_cor = traitement_magic_quotes(corriger_caracteres($_POST['notes']),'');
        $contenu_cor = traitement_magic_quotes(($_POST['notes']),'');
        if ($contenu_cor == '') $contenu_cor="...";
        if (isset($id_ct)) {
            $req = mysql_query("UPDATE ct_entry SET contenu = '$contenu_cor', id_login='".$_SESSION['login']."' WHERE id_ct='$id_ct' AND id_groupe='".$current_group["id"]."'");
        } else {
            $req = mysql_query("INSERT INTO ct_entry SET id_ct='0', contenu = '$contenu_cor', heure_entry='$heure_entry', id_login='".$_SESSION['login']."', id_groupe='".$id_groupe."', date_ct='$temp'");
            $id_ct = mysql_insert_id();
//            $today = $temp;
        }
        if ($req) $msg = "Enregistrement réussi."; else $msg = "Problème lors de l'enregistrement !";

    }
}
//
// Traitement du téléchargement de fichier
//
// Ajout d'un document
if (isset($doc_file['tmp_name']) AND (!empty($doc_file['tmp_name'][0]) and $valide_form=='yes') or
// Changement de nom d'un document
(isset($doc_name_modif) and isset($id_document) and ($id_document !=-1) and $valide_form=='yes')) {
	check_token();
	include "traite_doc.php";
}

//echo "id_ct=$id_ct<br />";

// Suppression d'un document
if ((isset($_GET['action'])) and ($_GET['action'] == 'del') and $valide_form=='yes') {
	check_token();
	include "traite_doc.php";
}

// si aucune notice n'existe dans ct_entry et qu'il existe des notices dans ct_devoirs_entry
// on crée une notice "info générales" vide
$test_ct_vide = sql_count(sql_query("SELECT id_ct FROM ct_entry WHERE (id_groupe='" . $current_group["id"]."')"));
$test_ct_devoirs_vide = sql_count(sql_query("SELECT id_ct FROM ct_devoirs_entry WHERE (id_groupe='" . $current_group["id"] ."')"));
if (($test_ct_vide == 0) and ($test_ct_devoirs_vide != 0)) {$req = mysql_query("INSERT INTO ct_entry SET id_ct='0', contenu = '', id_login='".$_SESSION['login']."', id_groupe='" . $current_group["id"]. "', date_ct=''");}


// Détermination de $id_ct
if($ajout=='oui') {
    // Compte-rendu supplémentaire : on ne va pas chercher une notice existante
    $test_cahier_texte = 0;
}
else {
	$ajout_req="";
	if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
		$ajout_req=" AND id_login='".$_SESSION['login']."'";
	}

    if (isset($_GET['info']) or isset($_POST['info'])) {
      $sql="SELECT heure_entry, contenu, id_ct,vise,visa  FROM ct_entry WHERE (id_groupe='" . $current_group["id"] . "' AND date_ct=''";
      $sql.=$ajout_req;
      $sql.=")";
      $infoyes = "&amp;info=yes";
    } elseif (isset($edit_devoir)) {
      $sql="SELECT contenu, id_ct,vise  FROM ct_devoirs_entry WHERE (id_groupe='" . $current_group["id"] . "' AND date_ct = '$today'";
      $sql.=$ajout_req;
      $sql.=")";
      $infoyes = "";
    } elseif (isset($id_ct)) {
      $sql="SELECT heure_entry, contenu, id_ct,vise,visa  FROM ct_entry WHERE (id_groupe='" . $current_group["id"] . "' AND date_ct = '$today' AND id_ct='$id_ct'";
      $sql.=$ajout_req;
      $sql.=")";
      $infoyes = "";
    } else {
      $sql="SELECT heure_entry, contenu, id_ct,vise,visa  FROM ct_entry WHERE (id_groupe='" . $current_group["id"] . "' AND date_ct='$today'";
      $sql.=$ajout_req;
      $sql.=") ORDER BY heure_entry ASC LIMIT 1";
      $infoyes = "";
    }
    $appel_cahier_texte = mysql_query($sql);
    $test_cahier_texte = mysql_num_rows($appel_cahier_texte);
}

if ($test_cahier_texte != 0) {
    // Il y a une notice à modifier
    if (!isset($edit_devoir))
        $heure_entry = mysql_result($appel_cahier_texte, 0,'heure_entry');
    // on initialise heure_entry si nouveau = heure actuelle si modification on prend celui de la base de donéne
    $contenu = mysql_result($appel_cahier_texte, 0,'contenu');

    $id_ct = mysql_result($appel_cahier_texte, 0,'id_ct');
} else {
    // Il s'agit d'une nouvelle notice
    $contenu = '';
}

// PB: Cela fait sauter le mini-calendrier...
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//echo "id_ct=$id_ct<br />";

// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//echo "id_ct=$id_ct<br />";

echo "<script type=\"text/javascript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>";
//-----------------------------------------------------------------------------------

echo "<table width=\"98%\" cellspacing=0 align=\"center\" summary=\"Tableau d'entête\">\n";

// Première ligne du tableau
echo "<tr>\n";

// Première cellule de la première ligne du tableau
echo "<td valign='top'>\n";

echo $message_avertissement_navigateur;
echo "<p>Nous sommes le :&nbsp;<br />\n";
echo "<script type=\"text/javascript\">\n";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->\n";
echo "</script>\n";
echo "</p>\n";


// **********************************************
// Affichage des différents groupes du professeur
// Récupération de toutes les infos sur le groupe
//$groups = get_groups_for_prof($_SESSION["login"]);
$groups = get_groups_for_prof($_SESSION["login"],"classe puis matière");
if (empty($groups)) {
    echo "<br /><br />\n";
    echo "<b>Aucun cahier de textes n'est disponible.</b>";
    echo "<br /><br />\n";
}
	$a = 1;
foreach($groups as $group) {
	$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$group['id']."' AND domaine='cahier_texte' AND visible='n';";
	//echo "$sql<br />\n";
	$test_grp_visib=mysql_query($sql);
	if(mysql_num_rows($test_grp_visib)==0) {
		//echo "<b>";
		if ($group["id"] == $current_group["id"]) {
		   echo "<p style=\"background-color: silver; padding: 2px; border: 1px solid black; font-weight: bold;\">" . $group["description"] . "&nbsp;-&nbsp;(";
			$str = null;
			foreach ($group["classes"]["classes"] as $classe) {
				$str .= $classe["classe"] . ", ";
			}
			$str = mb_substr($str, 0, -2);
			echo $str . ")&nbsp;</p>\n";
		} else {
			echo "<span style=\"font-weight: bold;\">";
		   echo "<a href=\"index.php?id_groupe=". $group["id"] ."&amp;year=$year&amp;month=$month&amp;day=$day&amp;edit_devoir=$edit_devoir\">";
		   echo $group["name"] . "&nbsp;-&nbsp;(";
			$str = null;
			foreach ($group["classes"]["classes"] as $classe) {
				$str .= $classe["classe"] . ", ";
			}
			$str = mb_substr($str, 0, -2);
			echo $str . ")</a>&nbsp;</span>\n";
		}
		//echo "</b>\n";
		if ($a == 2) {
			echo "<br />\n";
			$a = 1;
		} else {
			$a = 2;
		}
	}
}
// Fin Affichage des différents groupes du professeur
// **********************************************

// Fin première cellule de la première ligne du tableau
echo "</td>\n";

// Deuxième cellule de la première ligne du tableau
echo "<td style=\"text-align: center; vertical-align: top;\">\n";
//echo "id_ct=$id_ct<br />";
echo "<p><span class='grand'>Cahier de textes</span><br />";
if (getSettingValue("GepiCahierTexteVersion") == '2') {
echo "<a href=\"../cahier_texte_2/index.php?cdt_version_pref=2\">\n";
echo "<img src='../images/icons/cdt1_2.png' alt='Utiliser la version 2 du cahier de textes' class='link' title='Utiliser la version 2 du cahier de textes'/> </a>";
//echo "&nbsp;&nbsp;<button style='width: 200px;' onclick=\"javascript:window.location.replace('../cahier_texte_2/index.php?cdt_version_pref=2')
//				\">Utiliser la version 2 du cahier de textes</button>\n";
}
if ($id_groupe != null) {

	if(getSettingValue('cahier_texte_acces_public')!='no'){
	    echo "<a href='../public/index.php?id_groupe=" . $current_group["id"] ."' target='_blank'>Visualiser le cahier de textes en accès public</a>\n";
	} else {
		echo "<a href='./see_all.php'>Visualiser les cahiers de textes (accès restreint)</a>\n";
	}

    if ((getSettingValue("cahiers_texte_login_pub") != '') and (getSettingValue("cahiers_texte_passwd_pub") != '')) {
       echo "<br />(Identifiant : ".getSettingValue("cahiers_texte_login_pub")." - Mot de passe : ".getSettingValue("cahiers_texte_passwd_pub").")\n";
    }

	echo "<p class='grand'>".strftime("%A %d %B %Y", $today)."</p>\n";
	if ($delai > 0) {
		$cr_cours = "<p style=\"border: 1px solid grey; background-color: ".$color_fond_notices["c"]."; font-weight: bold;\">
			<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=" . $current_group["id"] ."\" title=\"Cr&eacute;er/modifier les comptes rendus de s&eacute;ance de cours\">
			Passer à la saisie des<br />Comptes rendus de séance</a></p>\n";
		$travaux_perso = "<p style=\"border: 1px solid grey; background-color: ".$color_fond_notices["t"]."; font-weight: bold;\">
			<a href=\"index.php?edit_devoir=yes&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=". $current_group["id"] ."\" title=\"Cr&eacute;er/modifier les notifications de travaux personnels &agrave; faire\">
			Passer à la saisie des<br />Travaux personnels à effectuer</a></p>\n";
		// Si la notice d'info est en modification, on affiche les deux liens
		if (isset($info)) {
			echo $cr_cours.$travaux_perso;
		} elseif (isset($edit_devoir)) {
			echo $cr_cours;
		} else {
			echo $travaux_perso;
		}
	}
	echo "<br />\n";
	// Ajout des différentes notices
	$nb_total_notices = sql_query1("select count(id_ct) from ct_entry where contenu != '' and id_groupe = '" . $current_group["id"] ."'");
	$nb_total_notices += sql_query1("select count(id_ct) from ct_devoirs_entry where contenu != '' and id_groupe = '" . $current_group["id"] ."'");
	if ($nb_total_notices > 1) {
		$legend = "Actuellement : ".$nb_total_notices." notices.<br />\n";
	}
	elseif ($nb_total_notices == 1) {
		$legend = "Actuellement : 1 notice.<br />\n";
	}
	else {
		$legend = "";
	}
	if ($nb_total_notices > 15) {
		echo "<fieldset style=\"border: 1px solid grey; font-size: 0.8em; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
		echo "<legend style=\"font-variant: small-caps; border: 1px solid grey;\">".$legend."</legend>\n";
		if ($_SESSION['type_display_notices'] == "all")  {
			echo "<b>>>&nbsp;&nbsp;Afficher&nbsp;toutes&nbsp;les&nbsp;notices<<</b><br />\n";
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=".$current_group["id"]."&amp;type_display_notices=15\">Afficher&nbsp;15&nbsp;notices&nbsp;max.</a>\n";
		} else {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=".$current_group["id"]."&amp;type_display_notices=all\">Afficher&nbsp;toutes&nbsp;les&nbsp;notices</a><br />\n";
			echo "<b>>>&nbsp;Afficher&nbsp;15&nbsp;notices&nbsp;max.<<</b>\n";
		}
		echo "</fieldset>\n";
	} else {
		$_SESSION['type_display_notices'] = "all";
		echo $legend;
	}

	//echo "</center>\n";
	echo "</td>\n";

    // Troisième cellule de la première ligne du tableau
    echo "<td align=\"right\">\n";
    echo "<form action=\"./index.php\" method=\"post\" style=\"width: 100%;\">\n";
    genDateSelector("", $day, $month, $year,'');
    echo "<input type=\"hidden\" name=\"id_groupe\" value=\"".$current_group["id"]."\"/>\n";
    echo "<input type=\"hidden\" name=\"uid_post\" value=\"".$uid."\"/>\n";
    echo "<input type=\"submit\" value=\"OK\"/>\n</form>\n";
    //Affiche le calendrier
    if (isset($edit_devoir)) {
        minicals($year, $month, $day, $current_group["id"],'index.php?edit_devoir=yes&amp;');
    } else {
        minicals($year, $month, $day, $current_group["id"],'index.php?');
    }
} else {
    echo "<span class='grand'> - Sélectionnez un groupe.</span>\n";
}
// Fin deuxième ou troixième cellule de la première ligne du tableau
echo "</td>\n";
echo "</tr>\n</table>\n<hr />\n";

// Si le choix du groupe n'a pas été fait, on affiche un texte d'explication et de mise en garde
if (($id_groupe == null)) {
    if ((getSettingValue("cahiers_texte_login_pub") != '') and (getSettingValue("cahiers_texte_passwd_pub") != '')) {
       echo " <b>AVERTISSEMENT</b> : En raison du caractère personnel du contenu, l'accès au <a href=\"../public\">site de consultation publique du cahier de textes</a> est restreint.
       Pour accéder aux cahiers de textes, le visiteur (élève, parent, ...) doit être en possession d'un nom d'utilisateur et d'un mot de passe valides.\n";
    } elseif(getSettingValue('cahier_texte_acces_public') == 'no'){
		echo '<p style="font-weight: bold;">L\'accès aux cahiers de textes est protégé.</p>';
	} else {
       echo " <b><span style='font-weight:bold;'>AVERTISSEMENT</span> : l'accès à l'interface de consultation publique du cahier de textes est entièrement libre et n'est soumise à aucune restriction.</b>\n";
    }
    echo "<br /><br />En utilisant le cahier de textes électronique de GEPI :
    <ul>\n";

	if(getSettingValue('cahier_texte_acces_public')!='no'){
		echo "<li>vous acceptez que vos nom, initiale de prénom, classes et matières enseignées apparaissent sur le <a href=\"../public\">site de consultation publique du cahier de textes</a>,</li>\n";
	}
	else {
		echo "<li>l'accès au cahier de textes est limité aux utilisateurs disposant d'un compte (<i>ce peuvent être les élèves, les parents d'élèves si des comptes ont été créés pour eux, mais dans ce cas, les élèves n'ont accès qu'aux cahiers de textes des enseignements qu'ils suivent et les parents n'ont accès qu'aux cahiers de textes de leurs enfants</i>),</li>\n";
	}

    echo "<li>vous acceptez que toutes les informations que vous fournissez dans ce module soient diffusées sur ce même site.</li>
    <li>vous vous engagez à respecter les règles fixées concernant les cahiers de textes (Circulaire du 3 mai 1961 adressée aux recteurs - RLR, 550-1 b)</li>
    <li>vous vous engagez à ne pas faire figurer d'informations nominatives concernant les élèves</li>
    </ul>\n";
    echo "<b>RAPPEL</b> : le cahier de textes constitue un outil de communication pour l'élève, les équipes disciplinaires
    et pluridisciplinaires, l'administration, le chef d'établissement, les corps d'inspection et les familles.
    Il relate le travail réalisé en classe :
    <ul>
    <li>projet de l'équipe pédagogique,</li>
    <li>contenu pédagogique de chaque séance, chronologie, objectif visé, travail à faire ...</li>
    <li>documents divers,</li>
    <li>évaluations, ...</li>
    </ul>\n";
    //echo "</body></html>\n";
	require("../lib/footer.inc.php");
    die();
}

/*/ Deuxième tableau
echo "<table width=\"98%\" cellspacing=0 align=\"center\">\n";
echo "<tr>\n";
// Première colonne du tableau
echo "<td valign=\"top\" width=\"20%\">\n";
// Nombre total de notices :
$nb_total_notices = sql_query1("select count(id_ct) from ct_entry where contenu != '' and id_groupe = '" . $current_group["id"] ."'");
$nb_total_notices += sql_query1("select count(id_ct) from ct_devoirs_entry where contenu != '' and id_groupe = '" . $current_group["id"] ."'");
if ($nb_total_notices > 1)
    $legend = "Actuellement : ".$nb_total_notices." notices.<br />";
else if ($nb_total_notices == 1)
    $legend = "Actuellement : 1 notice.<br />";
else
    $legend = "";
if ($nb_total_notices > 15) {
  echo "<fieldset style=\"padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">";
  echo "<legend style=\"font-variant: small-caps;\">".$legend."</legend>";
  if ($_SESSION['type_display_notices'] == "all")  {
    echo "<b>>>&nbsp;&nbsp;Afficher&nbsp;toutes&nbsp;les&nbsp;notices<<</b><br />\n";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=".$current_group["id"]."&amp;type_display_notices=15\">Afficher&nbsp;15&nbsp;notices&nbsp;max.</a>\n";
  } else {
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=".$current_group["id"]."&amp;type_display_notices=all\">Afficher&nbsp;toutes&nbsp;les&nbsp;notices</a><br />\n";
    echo "<b>>>&nbsp;Afficher&nbsp;15&nbsp;notices&nbsp;max.<<</b>\n";
  }
 echo "</fieldset>";
} else {
  $_SESSION['type_display_notices'] = "all";
  echo $legend;
}

echo "</td>\n";
// Deuxième colonne
echo "<td valign=\"top\" width=\"60%\">\n";
echo "<center>\n";
echo "<p class='grand'>".strftime("%A %d %B %Y", $today)."</p>";
if ($delai > 0) {
    if (isset($edit_devoir)) {
    	//echo "<a href=\"index.php?edit_devoir=yes&amp;year=".$annee_lendemain."&amp;month=".$mois_lendemain."&amp;day=".$jour_lendemain."&amp;id_groupe=". $current_group["id"] ."\" title=\"Saisir un nouveau travail personnel &agrave; faire\">Nouveaux travaux personnels à effectuer</a> - \n";
        echo "<b>>> Travaux personnels à effectuer<<</b> - \n";
        echo "<a href=\"index.php?year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=" . $current_group["id"] ."\" title=\"Cr&eacute;er/modifier les comptes rendus de s&eacute;ance de cours\">Comptes rendus de séance</a>\n";
    } else {
        echo "<a href=\"index.php?edit_devoir=yes&amp;year=$year&amp;month=$month&amp;day=$day&amp;id_groupe=". $current_group["id"] ."\" title=\"Cr&eacute;er/modifier les notifications de travaux personnels &agrave; faire\">Travaux personnels à effectuer</a> - \n";
        echo "<b>>> Comptes rendus de séance <<</b>\n";
    }
}
echo "</center>\n";
echo "</td>\n";
// Troisième colonne
echo "<td valign=\"top\" width=\"20%\">\n";
echo "</td>\n";
echo "</tr></table>\n";

echo "<hr />";
*/ // ============================== fin modif
// Début tableau d'affichage des notices
echo "<table width=\"100%\" border = 0 align=\"center\" cellpadding=\"10\" summary=\"Tableau d'affichage des notices\">\n";
echo "<tr>\n";

// Début colonne de gauche
echo "<td width = \"30%\" valign=\"top\">\n";

// recherche des "travaux à faire" futurs, toutes matières confondues
$debutCdt = getSettingValue("begin_bookings");
foreach ($current_group["classes"]["list"] as $_id_classe) {
    $total[$_id_classe] = null;
    $date[$_id_classe] = null;
    $groups = get_groups_for_class($_id_classe,"","n");
    foreach ($groups as $group) {
       $req_total =
            "select count(id_ct) total, max(date_ct) date
            from ct_devoirs_entry
            where (id_groupe = '" . $group["id"] . "'
            and date_ct > $aujourdhui)";
        $res_total = mysql_query($req_total);
        $sum = mysql_fetch_object($res_total);
        $total[$_id_classe] += $sum->total;
        if ($sum->date > $date[$_id_classe]) $date[$_id_classe] = $sum->date;
    }
}

// Affichage des travaux à faire futurs, toutes matières confondues
foreach ($current_group["classes"]["list"] as $_id_classe) {
    if ($total[$_id_classe] > 0) {
        echo "<p>La classe " . $current_group["classes"]["classes"][$_id_classe]["classe"] . " a  <a href=\"javascript:centrerpopup('liste_tous_devoirs.php?classe=$_id_classe&amp;debut=$aujourdhui',260,320,'scrollbars=yes,statusbar=no,resizable=yes');\"><strong>" . $total[$_id_classe] . "</strong> ";
        echo (($total[$_id_classe] == 1) ? "travail personnel" : "travaux personnels");
        echo "</a> jusqu'au <strong>" . strftime("%a %d %b %y", $date[$_id_classe]) . "</strong>.</p>\n";
    }
}

//================================================
/*
$sql="select * FROM ct_entry WHERE id_ct='5';";
$res_test=mysql_query($sql);
if(mysql_num_rows($res_test)>0) {
	$lig=mysql_fetch_object($res_test);
	if(strstr($lig->contenu,"<![endif]-->")) {
		echo "<div style='background-color:white; border: 1px dashed black;'>\n";
		// Pour dépolluer les copier/coller depuis M$Office
		echo ereg_replace('.*<\!\[endif\]-->',"",$lig->contenu);
		echo "</div>\n";
	}
}
*/
//================================================

//Modif vise ==> ERIC ajout champs vise visa dans les requetes
// recherche et affichage des prochains travaux futurs pour la matière en cours
$req_devoirs_arendre =
    "select 't' type, contenu, date_ct, id_ct, vise, id_login
    from ct_devoirs_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] ."'
    and date_ct > $today
    order by date_ct desc ";
//echo "$req_devoirs_arendre<br />";
if ($_SESSION['type_display_notices'] != "all")
    $req_devoirs_arendre .= " limit 5";
$res_devoirs_arendre = mysql_query($req_devoirs_arendre);
$dev_arendre = mysql_fetch_object($res_devoirs_arendre);

$req_notices =
    "select 'c' type, contenu, date_ct, id_ct, vise, visa, heure_entry, id_login
    from ct_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] . "'";
if ($_SESSION['type_display_notices'] != "all")
    $req_notices .= " and date_ct <= $today";
$req_notices .= " and date_ct >= $debutCdt
    order by date_ct desc, heure_entry";
if ($_SESSION['type_display_notices'] != "all")
    $req_notices .= " limit 10";
$res_notices = mysql_query($req_notices);
$notice = mysql_fetch_object($res_notices);

$req_devoirs =
    "select 't' type, contenu, date_ct, id_ct, vise, id_login
    from ct_devoirs_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] ."'";
//if ($_SESSION['type_display_notices'] != "all")
    $req_devoirs .= " and date_ct <= $today";
$req_devoirs .= " and date_ct >= $debutCdt
    order by date_ct desc ";
if ($_SESSION['type_display_notices'] != "all")
    $req_devoirs .= " limit 10";

$res_devoirs = mysql_query($req_devoirs);
$devoir = mysql_fetch_object($res_devoirs);

if((isset($id_ct))&&(is_numeric($id_ct))&&(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
	//echo "id_ct=$id_ct<br />";

	// On vérifie si l'utilisateur est proprio
	if (isset($edit_devoir)) {
		$sql = "SELECT date_ct FROM ct_devoirs_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
	}
	else {
		$sql = "SELECT date_ct FROM ct_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
	}
	//echo "$sql<br />";
	$test_proprio=mysql_query($sql);
	if(mysql_num_rows($test_proprio)==0) {
		unset($id_ct);
		$contenu ='';
	}
}

// Boucle d'affichage des notices dans la colonne de gauche
$date_ct_old = -1;
while (true) {
    if ($dev_arendre) {
        // Il reste des "travaux à faire"
        // On le copie dans $not_dev et on récupère le suivant
        $not_dev = $dev_arendre;
        $dev_arendre = mysql_fetch_object($res_devoirs_arendre);
    } else {
        // On a épuisé les devoirs à rendre
        // On met les notices du jour avant les devoirs à rendre aujourd'hui
        if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
            // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
            $not_dev = $notice;
            $notice = mysql_fetch_object($res_notices);
        } elseif($devoir) {
            // Plus de notices et toujours un devoir, ou devoir plus récent
            $not_dev = $devoir;
            $devoir = mysql_fetch_object($res_devoirs);
        } else {
            // Plus rien à afficher, on sort de la boucle
            break;
        }
    }
	/*
	echo "<pre>";
	print_r($not_dev);
	echo "</pre>";
	*/
	$liens_edition_suppression="y";
	if((my_strtoupper($not_dev->id_login)!=my_strtoupper($_SESSION['login']))&&(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
		$liens_edition_suppression="n";
	}

    // dans le cas ou il y a plusieurs notices pour une journée, il faut les numéroter.

    // Passage en HTML
	// INSERT INTO setting SET name='depolluer_MSOffice', value='y';
	if(getSettingValue('depolluer_MSOffice')=='y') {
		$content = &my_ereg_replace('.*<\!\[endif\]-->',"",$not_dev->contenu);
	}
	else {
		$content = &$not_dev->contenu;
	}

    // Documents joints
    $content .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);

    if (($not_dev->date_ct > $today) and ($not_dev->type == "t")) {
        echo("<strong>A faire pour le :</strong><br/>\n");
    }
    echo("<b>" . strftime("%a %d %b %y", $not_dev->date_ct) . "</b>\n");

    // Numérotation des notices si plusieurs notice sur la même journée
    if ($not_dev->type == "c") {
    if ($date_ct_old == $not_dev->date_ct) {
        $num_notice++;
        echo " <b><i>(notice N° ".$num_notice.")</i></b>";
    } else {
        // on affiche "(notice N° 1)" uniquement s'il y a plusieurs notices dans la même journée
        $nb_notices = sql_query1("SELECT count(id_ct) FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='".$not_dev->date_ct."')");
        if ($nb_notices > 1)
            echo " <b><i>(notice N° 1)</i></b>";
        // On réinitialise le compteur
        $num_notice = 1;
    }
    }

	//Eric
	if (isset($not_dev->visa)) { //notice
	    if ($not_dev->visa != 'y') {
	      if ((isset($id_ct))&&($not_dev->id_ct == $id_ct)) {echo " - <strong><span  class=\"red\">en&nbsp;modification</span></strong>";}
          echo("&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	} else { //devoir
	      if ((isset($id_ct))&&($not_dev->id_ct == $id_ct)) {echo " - <strong><span  class=\"red\">en&nbsp;modification</span></strong>";}
          echo("&nbsp;&nbsp;&nbsp;&nbsp;");
	}

	//Modif  Eric visa des notices et interdiction de modifier suite à un visa des notices
	$content_balise = '<div style="margin: 0px; float: left;">'."\n";
	//$content_balise.=" $not_dev->id_ct ";
	if ($not_dev->type == "c") {
		if (($not_dev->vise != 'y') or ($visa_cdt_inter_modif_notices_visees == 'no')){

			if($liens_edition_suppression=="y") {
				$content_balise .=("<a href=\"index.php?id_ct=$not_dev->id_ct&amp;id_groupe=" . $current_group["id"] . "\"><img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
				$content_balise .=(" ");
				$content_balise .=(
				"<a href=\"index.php?id_ct_del=$not_dev->id_ct&amp;edit_devoir=$edit_devoir&amp;action=sup_entry&amp;uid_post=$uid&amp;id_groupe=".$current_group["id"].add_token_in_url()."\" onclick=\"return confirmlink(this,'suppression de la notice du " . strftime("%a %d %b %y", $not_dev->date_ct) . " ?','" . $message_suppression . "')\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n"
				);
			}

			// cas d'un visa, on n'affiche rien
			if ($not_dev->visa == 'y') {
				$content_balise = " ";
			} else {
				if ($not_dev->vise == 'y') {
					$content_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
				}
			}
		} else {
			// cas d'un visa, on n'affiche rien
			if ($not_dev->visa == 'y') {
				$content_balise .= " ";
			} else {
				$content_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
			}
		}
	} else {
		if (($not_dev->vise != 'y') or ($visa_cdt_inter_modif_notices_visees == 'no')) {
			if($liens_edition_suppression=="y") {
				$content_balise .=("<a href=\"index.php?id_ct=$not_dev->id_ct&amp;id_groupe=" . $current_group["id"] . "&amp;edit_devoir=yes\"><img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a>\n");
				$content_balise .=(" ");
				$content_balise .=(
				"<a href=\"index.php?id_ct_del=$not_dev->id_ct&amp;edit_devoir=$edit_devoir&amp;action=sup_devoirs&amp;uid_post=$uid&amp;id_groupe=".$current_group["id"].add_token_in_url()."\" onclick=\"return confirmlink(this,'suppression du devoir du " . strftime("%a %d %b %y", $not_dev->date_ct) . " ?','" . $message_suppression . "')\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>\n"
				);
			}

			if ($not_dev->vise == 'y') {
				$content_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
			}

		} else {
			$content_balise .= "<i><span  class=\"red\">Notice signée</span></i>";
		}
	}
	$content_balise .= "</div>\n";

	echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices[$not_dev->type]."\" summary=\"Tableau de...\">\n<tr>\n<td>\n$content_balise$content</td>\n</tr>\n</table>\n<br/>\n");
	if ($not_dev->type == "c") {$date_ct_old = $not_dev->date_ct;}
}

mysql_free_result($res_devoirs_arendre);
mysql_free_result($res_devoirs);
mysql_free_result($res_notices);

// Affichage des info générales
$appel_info_cahier_texte = mysql_query("SELECT heure_entry, contenu, id_ct  FROM ct_entry WHERE (id_groupe='" . $current_group["id"] . "' and date_ct='') ORDER BY heure_entry");
$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
$content = @mysql_result($appel_info_cahier_texte, 0,'contenu');
$id_ctexte = @mysql_result($appel_info_cahier_texte, 0,'id_ct');
  $architecture= "/documents/cl".$current_group["id"];
  $sql = "SELECT titre, emplacement FROM ct_documents WHERE id_ct='".$id_ctexte."' ORDER BY titre";
  $res = sql_query($sql);
  if (($res) and (sql_count($res)!=0)) {
     $content .= "<small style=\"font-weight: bold;\">Document(s) joint(s):</small>\n";
     $content .= "<ul type=\"disc\" style=\"padding-left: 15px; margin: 0px; padding-top: 0px; \">\n";
     for ($i=0; ($row = sql_row($res,$i)); $i++) {
        $titre = $row[0];
        $emplacement = $row[1];
        $content .=  "<li style=\"padding: 1px; margin: 1px; \"><a href='".$emplacement."' target=\"_blank\">".$titre."</a></li>\n";
   }
   $content .= "</ul>\n";
  }
echo "<b>Informations Générales</b>\n";
if ((isset($id_ct))&&($id_ctexte == $id_ct)) {echo "<b><font color=\"red\"> - en&nbsp;modification</font></b>";}

$content_balise = "<div style=\"margin: 0px; float: left;\"><a href='index.php?info=yes&amp;id_groupe=" . $current_group["id"] . "'><img style=\"border: 0px;\" src=\"../images/edit16.png\" alt=\"modifier\" title=\"modifier\" /></a> <a href='index.php?info=yes&amp;id_ct_del=$id_ctexte&amp;action=sup_entry&amp;uid_post=$uid&amp;id_groupe=".$current_group["id"].add_token_in_url()."' onclick=\"return confirmlink(this,'suppression de la notice Informations générales ?','".$message_suppression."')\"><img style=\"border: 0px;\" src=\"../images/delete16.png\" alt=\"supprimer\" title=\"supprimer\" /></a>";
//$content_balise.="Export au <a href='../cahier_texte_2/exportcsv.php?id_groupe=".$current_group["id"]."'>format csv</a> / <a href='../cahier_texte_2/export_cdt.php?id_groupe=".$current_group["id"]."'>format html</a><br/>";
$content_balise.="</div>\n";

echo "<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$color_fond_notices["i"] ."; padding: 2px; margin: 2px;\" width=\"100%\" cellpadding=\"2\" summary=\"Tableau de...\">\n<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: ".$couleur_cellule["i"]."; padding: 0px; margin: 0px;\">\n<td>\n".$content_balise.$content."</td>\n</tr>\n</table>\n<br />";

echo "Export au <a href='../cahier_texte_2/exportcsv.php?id_groupe=".$current_group["id"]."'>format csv</a> / <a href='../cahier_texte_2/export_cdt.php?id_groupe=".$current_group["id"]."'>format html</a><br/>";

//===============================
// B.O.
echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; margin-top: 3px;\">\n";
echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\">B.O.</legend>\n";
echo "<div style='height: 10em; overflow: auto;'>\n";

require("../lib/textes.inc.php");
echo $cdt_texte_bo;

echo "</div>\n";
echo "</fieldset>\n";
//===============================

// Fin de la colonne de gauche
echo "</td>\n";

// Début de la colonne de droite
echo "<td valign=\"top\">\n";
$test_ct_vide = sql_count(sql_query("SELECT id_ct FROM ct_entry WHERE (id_groupe='" . $current_group["id"] . "')"));
if ($test_ct_vide == 0) {echo "<b><font color='red'>Actuellement ce cahier de textes est vide. Il n'est donc pas visible dans l'espace public.</font></b>\n";}

//
// Affichage de la notice en modification
//

// Initialisation du type de couleur (voir global.inc.php)
if (isset($edit_devoir)) {
    $type_couleur = "t";
}
else {
    if (isset($info)) {$type_couleur = "i";} else {$type_couleur = "c";}
}

// Nombre de notices pour ce jour :
$num_notice = NULL;
/*
if((isset($id_ct))&&(is_numeric($id_ct))&&(getSettingValue("cdt_autoriser_modif_multiprof")!="yes")) {
	//echo "id_ct=$id_ct<br />";

	// On vérifie si l'utilisateur est proprio
	if (isset($edit_devoir)) {
		$sql = "SELECT date_ct FROM ct_devoirs_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
	}
	else {
		$sql = "SELECT date_ct FROM ct_entry WHERE id_ct='$id_ct' AND id_login='".$_SESSION['login']."';";
	}
	//echo "$sql<br />";
	$test_proprio=mysql_query($sql);
	if(mysql_num_rows($test_proprio)==0) {
		unset($id_ct);
		$contenu ='';
	}
}
*/
//if(isset($id_ct)) {echo "000 id_ct=$id_ct<br />";} else {echo "Pas de id_ct<br />";}

$sql="SELECT * FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='$today') ORDER BY heure_entry ASC";
//echo "$sql<br />";
$appel_cahier_texte_liste = mysql_query($sql);
// Si plusieurs notices pour ce jour, on numérote la notice en cours
//if (mysql_num_rows($appel_cahier__liste) > 1) {
if (mysql_num_rows($appel_cahier_texte_liste) > 1) {
    $cpt_compte_rendu_liste = "1";
    while ( $appel_cahier_texte_donne = mysql_fetch_array ($appel_cahier_texte_liste)) {
        if ((isset($id_ct))&&($appel_cahier_texte_donne['id_ct'] == $id_ct)) {$num_notice = $cpt_compte_rendu_liste;}
        $cpt_compte_rendu_liste++;
    }
} else {
  // ajout Eric ==> interdire la modification d'un visa par le prof
  // si c'est un visa
  $appel_cahier_texte_donne = mysql_fetch_array ($appel_cahier_texte_liste);
  if ($appel_cahier_texte_donne['visa']=='y') {;
	  unset ($edit_devoir);
	  unset ($id_ct);
	  $contenu ='';
  }
}

echo "<div style=\"position:absolute;top:310px;left:30%;border:2px solid black;background-color:".$color_fond_notices[$type_couleur].";width:610px;height:20px;text-align:center;\">
	<span style='font-weight:bold'>Saisie ".(($type_couleur == "t") ? "des Travaux personnels à effectuer" : "du compte-rendu de séance")."</span>
</div>\n";

// ======================= Correctif Pascal Fautrero : permet d'afficher la fenêtre de saisie dans une fenêtre flottante

$reduce = isset($_POST["reduce"]) ? $_POST["reduce"] :(isset($_GET["reduce"]) ? $_GET["reduce"] :'off');
if ($reduce == "off") {
    echo "<div style=\"position:absolute;top:350px;left:30%;border:2px solid black;background-color:white;width:610px;height:20px;text-align:center;\">\n";
    echo "<a href=\"./index.php?reduce=on\">cacher la fenêtre de saisie</a>";
}
else {
    echo "<div style=\"position:absolute;top:350px;left:30%;border:2px solid black;background-color:white;width:610px;height:20px;text-align:center;\">\n";
    echo "<a href=\"./index.php?reduce=off\">montrer la fenêtre de saisie</a>";
    echo "</div>\n";
    echo "<div style=\"display:none;\">\n";
}
// ===============================


echo "<fieldset style=\"width:100%;border: 5px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto; background: ".$color_fond_notices[$type_couleur].";\">\n";
if (isset($edit_devoir)) {
    echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Travaux personnels";
    $test_appel_cahier_texte = mysql_query("SELECT contenu, id_ct  FROM ct_devoirs_entry WHERE (id_groupe='" . $current_group["id"] . "' AND date_ct = '$today')");
    if (isset($id_ct)) {
		echo " - <b><font color=\"red\">Modification de la notice</font></b>";
		// Pour permettre d'ajouter directement une nouvelle notice sur le travail à effectuer, on ajoute un jour à la date précédente ($today)
		echo " - <a href=\"index.php?edit_devoir=yes&amp;year=".$annee_lendemain."&amp;month=".$mois_lendemain."&amp;day=".$jour_lendemain."&amp;id_groupe=". $current_group["id"] ."\" title=\"Saisir un nouveau travail personnel &agrave; faire\">Nouveau travail</a>";
	} else {
		echo " - <b><font color=\"red\">Nouvelle notice</font></b>\n";
	}
    echo "</legend>\n";
} else {
    if (isset($info))
        echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Informations générales ";
    else
        echo "<legend style=\"border: 1px solid grey; background: ".$color_fond_notices[$type_couleur]."; font-variant: small-caps;\"> Compte rendu ";
	if (isset($num_notice)) echo " <b>N° ".$num_notice."</b> ";
//    echo "de la séance du " . strftime("%A %d %B %Y", $today);
    if (isset($id_ct)) {
        echo " - <b><font color=\"red\">Modification de la notice</font></b>";
        if (!isset($info))
        echo " - <a href=\"index.php?year=".$year."&amp;month=".$month."&amp;day=".$day."&amp;id_groupe=".$current_group["id"]."&amp;ajout=oui\" title=\"Cliquer pour ajouter un compte rendu pour ce jour\">Ajouter une notice</a>\n";
    } else
        echo " - <b><font color=\"red\">Nouvelle notice</font></b>\n";
    echo "</legend>\n";
}

echo "<form enctype=\"multipart/form-data\" name=\"mef\" id=\"mef\" action=\"./index.php\" method=\"post\" style=\"width: 100%;\">\n";
echo add_token_field();
if (!isset($edit_devoir) and $info !='yes') {
    echo "<input type=\"hidden\" name=\"heure_entry\" value=\"";
    if (!isset($heure_entry)) {
        echo date('G:i');
    }
    else {
        echo $heure_entry;
    }
    echo "\" />\n";
}

if (isset($_GET['info']) or isset($_POST['info']))
    $temp = "Informations Générales : ";
else if (isset($edit_devoir)) {
    //Configuration du calendrier
    include("../lib/calendrier/calendrier.class.php");
    $cal = new Calendrier("mef", "display_date");
    $temp = "A faire pour le : ";
    $temp .= "<input type='text' name = 'display_date' id= 'display_date' size='10' value = \"".date("d",$today)."/".date("m",$today)."/".date("Y",$today)."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />\n";
    $temp .=  "<a href=\"#calend\" onClick=\"".$cal->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"calendrier\"/></a>\n";
    //$temp .= img_calendrier_js("display_date", "img_bouton_display_date");;
} else {
    $temp = strftime("%A %d %B %Y", $today);
} ?>
<table border="0" width="100%" summary="Tableau de saisie de notice">
<tr>
<td style="width:100%"><b><?php echo $temp; ?></b>&nbsp;
<input type="submit" value="Enregistrer la notice" style="font-variant: small-caps;" />
<?php
$i= mktime(0,0,0,$month,$day-1,$year);
$yy = date("Y",$i);
$ym = date("m",$i);
$yd = date("d",$i);
$i= mktime(0,0,0,$month,$day+1,$year);
$ty = date("Y",$i);
$tm = date("m",$i);
$td = date("d",$i);


// Si c'est une notice de devoir
if (isset($edit_devoir)) {
	// Date de visibilité
	$heure_courante=strftime("%H:%M");
	$jour_courant=strftime("%d/%m/%Y");
	if((isset($id_ct))&&($id_ct!='')) {
		$sql="SELECT date_visibilite_eleve FROM ct_devoirs_entry WHERE id_ct='$id_ct';";
		$res_visibilite=mysql_query($sql);
		if(mysql_num_rows($res_visibilite)>0) {
			$lig_visibilite=mysql_fetch_object($res_visibilite);
			$heure_courante=get_heure_2pt_minute_from_mysql_date($lig_visibilite->date_visibilite_eleve);
			$jour_courant=get_date_slash_from_mysql_date($lig_visibilite->date_visibilite_eleve);
		}
	}

	echo "<br />\n";
	echo "<span title='Vous pouvez modifier les dates et heure de visibilité avec les flèches Haut/Bas, PageUp/PageDown du clavier.' style='font-weight: bold;'>Date de visibilité</span>&nbsp;:\n";
	echo " <input type='text' name='jour_visibilite' id='jour_visibilite' value='$jour_courant' size='7' onkeydown='clavier_date(this.id,event)' 
	onblur=\"date_v=document.getElementById('jour_visibilite').value;
		tab=date_v.split('/');
		jour_v=tab[0];
		mois_v=tab[1];
		annee_v=tab[2];
		if(!checkdate(mois_v, jour_v, annee_v)) {
			alert('La date de visibilité saisie n est pas valide.');
		}
	\" />\n";
// onblur='verif_date_visibilite()' />\n";
	echo " à <input type='text' name='heure_visibilite' id='heure_visibilite' value='$heure_courante' size='3' onkeydown='clavier_heure(this.id,event)' 
	onblur=\"instant_v=document.getElementById('heure_visibilite').value;
		var exp=new RegExp('^[0-9]{1,2}:[0-9]{0,2}$','g');
		erreur='n';
		if (exp.test(instant_v)) {
			tab=instant_v.split(':');
			heure_v=eval(tab[0]);
			min_v=eval(tab[1]);

			if((heure_v<0)||(heure_v>=24)||(min_v<0)||(min_v>=60)) {erreur='y';}
		}
		else {
			erreur='y';
		}

		if(erreur=='y') {
			alert('L heure de visibilité saisie n est pas valide.');
		}
	\" />\n";
}


echo "</td>\n";
echo "<td>\n";
if (isset($edit_devoir)) {
	echo "<a title=\"Aller au jour précédent\" href=\"index.php?edit_devoir=yes&amp;year=$yy&amp;month=$ym&amp;day=$yd&amp;id_groupe=" . $current_group["id"] . "\">&lt;&lt;</a></td><td align=center><a href=\"index.php?edit_devoir=yes&amp;id_groupe=" . $current_group["id"] ."&amp;id_matiere=$id_matiere\">Aujourd'hui</a></td><td align=right><a title=\"Aller au jour suivant\" href=\"index.php?edit_devoir=yes&amp;year=$ty&amp;month=$tm&amp;day=$td&amp;id_groupe=" . $current_group["id"]."&amp;id_matiere=$id_matiere\">&gt;&gt;</a>\n";
} else {
	echo "<a title=\"Aller au jour précédent\" href=\"index.php?year=$yy&amp;month=$ym&amp;day=$yd&amp;id_groupe=" . $current_group["id"] . "\">&lt;&lt;</a></td><td align=center><a href=\"index.php?id_groupe=" . $current_group["id"] . "\">Aujourd'hui</a></td><td align=right><a title=\"Aller au jour suivant\" href=\"index.php?year=$ty&amp;month=$tm&amp;day=$td&amp;id_groupe=" . $current_group["id"]."&amp;id_matiere=$id_matiere\">&gt;&gt;</a>\n";
}
echo "</td>\n";
echo "</tr>\n";
echo "\n";
?>
<tr><td colspan="4">
<?php
// lancement de CKeditor
$oCKeditor = new CKeditor('../ckeditor/');
$oCKeditor->editor('notes',$contenu) ;

//echo "<a href=\"#\" onclick=\"javascript: document.getElementById('notes').value='TRUC'; return false;\">CLIC</a>";
//echo "<a href=\"#\" onclick=\"javascript: alert(document.getElementById('notes').value); return false;\">CLOC</a>";

// gestion des fichiers attachés
echo '<div style="border-style:solid; border-width:1px; border-color: '.$couleur_bord_tableau_notice.'; background-color: '.$couleur_cellule[$type_couleur].';  padding: 2px; margin: 2px;">
<b>Fichier(s) attaché(s) : </b><br />'."\n";
echo '<div id="div_fichier">'."\n";
if (isset($edit_devoir)) {
    $architecture= "/documents/cl_dev".$current_group["id"];
}
else {
    $architecture= "/documents/cl".$current_group["id"];
}

if (isset($id_ct)) {
	//echo "AAA id_ct=$id_ct<br />";

    // Recherche de documents joints
    if (isset($edit_devoir)) {
		$sql = "SELECT id, titre, taille, emplacement FROM ct_devoirs_documents WHERE id_ct_devoir='".$id_ct."' ORDER BY titre";
    } else {
		$sql = "SELECT id, titre, taille, emplacement FROM ct_documents WHERE id_ct='".$id_ct."' ORDER BY titre";
    }
    $res = sql_query($sql);
    if (($res) and (sql_count($res)!=0)) {
        // Affichage des documents joints
        echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; background-color: #000000; width: 100%\" cellspacing=\"1\" summary=\"Tableau des documents joints\">\n";
        echo "<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: $couleur_entete_fond[$type_couleur];\"><td style=\"text-align: center;\"><b>Titre</b></td><td style=\"text-align: center; width: 100px\"><b>Taille en Ko</b></td><td style=\"text-align: center; width: 100px\"></td></tr>\n";
        $nb_doc = 0;
        $id_document = array();
        $ic='1';
        for ($i=0; ($row = sql_row($res,$i)); $i++) {
            if ($ic=='1') { $ic='2'; $couleur_cellule_=$couleur_cellule[$type_couleur]; } else { $couleur_cellule_=$couleur_cellule_alt[$type_couleur]; $ic='1'; }
            $id_document[$i] = $row[0];
            $titre_[$i] = $row[1];
            $taille = round($row[2]/1024,1);
            $emplacement = $row[3];
            echo "<tr style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice."; background-color: $couleur_cellule_;\"><td><a href='".$emplacement."' target=\"_blank\">".$titre_[$i]."</a></td><td style=\"text-align: center;\">".$taille."</td><td style=\"text-align: center;\"><a href='index.php?action=del&amp;uid_post=$uid&amp;id_del=".$id_document[$i]."&amp;edit_devoir=".$edit_devoir."&amp;id_ct=$id_ct&amp;id_groupe=".$current_group["id"].$infoyes.add_token_in_url()."' onclick=\"return confirmlink(this,'suppression du document joint ".basename($row[3])." ?','".$message_suppression."');document.mef.submit();\">Supprimer</a></td></tr>\n";
            $nb_doc++;
        }
        echo "</table>\n";
        //gestion de modification du nom d'un documents
        echo "Nouveau nom <input type=\"text\" name=\"doc_name_modif\" size=\"25\" /> pour\n";
        echo "<select name=\"id_document\">\n";
        echo "<option value='-1'>(choisissez)</option>\n";
        for ($i=0; $i<$nb_doc; $i++)
            echo "<option value='".$id_document[$i]."'>".$titre_[$i]."</option>\n";
        echo "</select>\n<br /><br />\n";
    }
}
if (isset($id_ct)) {
    echo "<input type=\"hidden\" name=\"id_ct\" value=\"".$id_ct."\" />\n";
}
if (isset($edit_devoir)) {
     echo "<input type=\"hidden\" name=\"edit_devoir\" value=\"yes\" />\n";
}
if (isset($_GET['info']) or isset($_POST['info'])) {
    echo "<input type=\"hidden\" name=\"info\" value=\"yes\" />";
}
?>
<input type="hidden" name="day" value="<?php echo $day; ?>" />
<input type="hidden" name="month" value="<?php echo $month; ?>" />
<input type="hidden" name="year" value="<?php echo $year; ?>" />
<input type="hidden" name="id_groupe" value="<?php echo $current_group['id']; ?>" />
<input type="hidden" name="uid_post" value="<?php echo $uid; ?>" />


<table style="border-style:solid; border-width:0px; border-color: <?php echo $couleur_bord_tableau_notice;?> ; background-color: #000000; width: 100%" cellspacing="1" summary="Tableau de...">
<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
<td style="font-weight: bold; text-align: center; width: 20%">Titre (facultatif)</td>
<td style="font-weight: bold; text-align: center; width: 60%">Emplacement</td>
</tr>
<?php
$nb_doc_choisi='3';
$nb_doc_choisi_compte='0';
while($nb_doc_choisi_compte<$nb_doc_choisi) { ?>
    <tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
    <td style="text-align: center;"><input type="text" name="doc_name[]" size="20" /></td>
    <td style="text-align: center;"><input type="file" name="doc_file[]" size="20" /></td>
    </tr>
    <?php $nb_doc_choisi_compte++;
} ?>
<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice;?>; background-color: <?php echo $couleur_cellule[$type_couleur]; ?>;">
<td colspan="2" style="text-align: center;">
<input type="submit" value="Enregistrer la notice" />
</td>
</tr>
<tr style="border-style:solid; border-width:1px; border-color: <?php echo $couleur_bord_tableau_notice; ?>; background-color: <?php echo $couleur_entete_fond[$type_couleur]; ?>;">
<td colspan="2" style="text-align: center;"><?php  echo "Tous les documents ne sont pas acceptés, voir <a href='javascript:centrerpopup(\"limites_telechargement.php?id_groupe=" . $current_group["id"] . "\",600,480,\"scrollbars=yes,statusbar=no,resizable=yes\")'>les limites et restrictions</a>\n"; ?>
</td>
</tr>
</table>
</div>
</div>
</td>
</tr>
</table>
</form>
</fieldset>



<?php
//
// Suppression du cahier de textes jusqu'à une date choisie
//
$last_date1 = sql_query1("SELECT date_ct FROM ct_entry WHERE (contenu != '' and id_groupe='" . $current_group["id"] . "' and date_ct != '') order by date_ct  LIMIT 1 ");
$last_date2 = sql_query1("SELECT date_ct FROM ct_devoirs_entry WHERE (contenu != '' and id_groupe='" . $current_group["id"] . "' and date_ct != '') order by date_ct  LIMIT 1 ");
$last_date = max($last_date1,$last_date2);
if ($last_date != "-1") {
    $sday = strftime("%d", $last_date);
    $smonth = strftime("%m", $last_date);
    $syear = strftime("%Y", $last_date);

	echo "<br />\n";
    echo "<div style=\"width:100%;\">\n";
    echo "<fieldset style=\"border: 1px solid grey; padding-top: 8px; padding-bottom: 8px;  margin-left: auto; margin-right: auto;\">\n";
    echo "<legend style=\"border: 1px solid grey; font-variant: small-caps;\">Suppression de notices</legend>\n";
    echo "<table border='0' width='100%' summary=\"Tableau de...\">\n";
    echo "<tr>\n<td>\n";
    echo "<form action=\"./index.php\" method=\"post\" style=\"width: 100%;\">\n";
    echo add_token_field();

    echo "Date de la notice la plus ancienne : ".strftime("%A %d %B %Y", $last_date)."<br /><br />";

    echo "<b>Effacer toutes les données</b> (textes et documents joints) du cahier de textes avant la date ci-dessous :<br />\n";
    genDateSelector("sup_", $sday, $smonth, $syear,"more_years");
    echo "<input type='hidden' name='action' value='sup_serie' />\n";
    echo "<input type='hidden' name='id_groupe' value='".$current_group["id"]."' />\n";
    ?>
    <input type="hidden" name="uid_post" value="<?php echo $uid; ?>" />
    <?php
    echo "<input type='submit' value='Valider' onclick=\"return confirmlink(this,'Etes-vous sûr de vouloir supprimer les notices et les documents joints jusqu\'à la date selectionnée ?','Confirmation de suppression')\" />\n";
    echo "</form>\n";
    echo "</td>\n</tr>\n</table>\n</fieldset>\n";
    echo "</div>\n";
    echo "</div>\n";
}
$_SESSION['cacher_header'] = "n";
// Fin de la colonne de droite
echo "</td>\n</tr>\n</table>\n";
require("../lib/footer.inc.php");
?>

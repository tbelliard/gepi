<?php
/*
 *
 * Copyright 2009-2012 Josselin Jacquard
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

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
echo   ("Resume session") ;
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
echo   ("checkAccess") ;
    header("Location: ../logout.php?auto=1&amp;pb_checkAccess=y");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

// Vérification : est-ce que l'utilisateur a le droit d'être ici ?
if (isset($current_group["id"]) AND ($current_group["id"] != "") ) {
    if (!check_prof_groupe($_SESSION['login'],$current_group["id"])) {
        header("Location: ../logout.php?auto=1");
        die();
    }
}

$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
    die();
}

// Liste les données des tables ct_entry et ct_devoirs_entry
// -------------------------------------------
$req_notices =
    "select 'Compte rendu' type, date_ct, contenu
    from ct_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] . "'";
$req_devoirs = 
    "select 'Travail a faire' type, date_ct, contenu
    from ct_devoirs_entry
    where contenu != ''
    and id_groupe = '" . $current_group["id"] ."'";
$req_union = "select * from (" . $req_notices . ") as notices UNION (" . $req_devoirs . ") order by date_ct desc";
$sql_union = mysql_query($req_union);

//nom du fichier à telecharger
$nom_fic = mb_substr($current_group["description"],0 , 4);
foreach ($current_group["classes"]["classes"] as $classe) {
    $nom_fic .= $classe["classe"];
}

$nom_fic.="_".date("dmY") . ".csv";

$csv="";
if (mysql_num_rows($sql_union) == 0) {
	$csv.="aucune donnée";
} else {
	// titre des colonnes
	$csv.="Date,Type,Contenu";
	$csv.="\n";
	
	// données de la table
	while ($arrSelect = mysql_fetch_array($sql_union, MYSQL_ASSOC)) {
		if ($arrSelect["date_ct"] != 0) {
			$csv.=strftime("%d/%m/%y", $arrSelect["date_ct"]).",";
		} else {
			$csv.="info generale ,";
		}
		$csv.=($arrSelect["type"].",");
		$csv.="\"".strip_tags(html_entity_decode($arrSelect["contenu"], ENT_NOQUOTES, 'UTF-8'))."\"";
		$csv.="\n";
	}
}

send_file_download_headers('text/x-csv',$nom_fic);
//echo $csv;
echo echo_csv_encoded($csv);

?>

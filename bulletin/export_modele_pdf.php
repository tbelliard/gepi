<?php
/*
* $Id$
*
* Copyright 2001-2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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


// Initialisations files
require_once("../lib/initialisations.inc.php");

//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
};

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
/*
if (empty($_GET['selection']) and empty($_POST['selection'])) {
	$selection = '';
}
else {
	if (isset($_GET['selection'])) {
		$selection = $_GET['selection'];
	}
	if (isset($_POST['selection'])) {
		$selection = $_POST['selection'];
	}
}
if (empty($_GET['id_model_bulletin']) and empty($_POST['id_model_bulletin'])) {
	$modele = '';
}
else {
	if (isset($_GET['id_model_bulletin'])) {
		$modele = $_GET['id_model_bulletin'];
	}
	if (isset($_POST['id_model_bulletin'])) {
		$modele = $_POST['id_model_bulletin'];
	}
}
*/
//$id_model_bulletin=isset($_POST['id_model_bulletin']) ? $_POST['id_model_bulletin'] : NULL;
$selection=isset($_POST['selection']) ? $_POST['selection'] : NULL;

//debug_var();

$action = 'export';

if ( $action === 'export' ) {

	header("Content-Type: application/csv-tab-delimited-table");
	header("Content-disposition: filename=modelebulletinpdf.csv");

	//==============================
	// Initialisation d'un tableau des champs de model_bulletin
	include('bulletin_pdf.inc.php');
	//==============================

	$fd="id_model_bulletin";
	for($i=0;$i<count($champ_bull_pdf);$i++) {
		$fd.=";".$champ_bull_pdf[$i];
	}
	$fd.="\r\n";

	$sql="SELECT DISTINCT id_model_bulletin FROM modele_bulletin ORDER BY id_model_bulletin;";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	while($lig=mysql_fetch_object($res)) {
		$id_model_bulletin=$lig->id_model_bulletin;

		$exporter_ce_modele='y';
		if(isset($selection)) {
			//echo "Test $id_model_bulletin dans \$selection<br />";
			if(!in_array($id_model_bulletin,$selection)) {
				$exporter_ce_modele='n';
			}
		}
		//echo "\$exporter_ce_modele=$exporter_ce_modele<br />";

		if($exporter_ce_modele=='y') {
			$fd.=$id_model_bulletin;
			for($i=0;$i<count($champ_bull_pdf);$i++) {
				$fd.=";";
				$sql="SELECT valeur FROM modele_bulletin WHERE nom='".$champ_bull_pdf[$i]."' AND id_model_bulletin='".$id_model_bulletin."';";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0) {
					$lig2=mysql_fetch_object($res2);
					$fd.=$lig2->valeur;
				}
			}
			$fd.="\r\n";
		}
	}

	echo $fd;
	/*
	//requête des modèle sélectionné
	if ( !empty($modele[0]) ) {
		$o=0;
		$prepa_requete = "";
		$passage = 'non';
		while( !empty($modele[$o]) )
		{
			if ( !empty($selection[$o]) and $selection[$o] === '1' ) {
				if($passage === 'non') { $prepa_requete = 'id_model_bulletin = "'.$modele[$o].'"'; $passage = 'oui'; }
				if($passage != 'non') { $prepa_requete = $prepa_requete.' OR id_model_bulletin = "'.$modele[$o].'" '; }
			}
			$o = $o + 1;
		}
	}


	if ($modele != '') {
		$requete = 'SELECT * FROM '.$prefix_base.'model_bulletin WHERE ('.$prepa_requete.') ORDER BY nom_model_bulletin ASC';
		//echo "$requete<br />";
	}

	// liste le champs de la table model_bulletin
	$result = mysql_query("SHOW COLUMNS FROM ".$prefix_base."model_bulletin");
	if (!$result) {
	echo 'Impossible d\'exécuter la requête : ' . mysql_error();
	exit;
	}
	if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		echo $row['Field'].';';
		$var[] = $row['Field'];
	}
	}
	echo "\r\n";

$executer = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br>'.mysql_error());
while ( $donner = mysql_fetch_array( $executer ) )
{
	$o = '0';
	while ( !empty($var[$o]) ) {
	$var_select = $var[$o];
		if ( $var_select === 'id_model_bulletin' ) {
			echo ';';
		} else {
				echo $donner[$var_select].';';
			}
	$o = $o + 1;
	}
	echo "\r\n";
}
	*/
}
?>

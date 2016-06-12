<?php
/*
 *
 * Copyright 2009-2015 Josselin Jacquard
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

header('Content-Type: text/html; charset=utf-8');

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec l'échappement mysql déjà géré par propel
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_duplication = isset($_POST["date_duplication"]) ? $_POST["date_duplication"] :(isset($_GET["date_duplication"]) ? $_GET["date_duplication"] :NULL);
$id_groupe = isset($_POST["id_groupe_duplication"]) ? $_POST["id_groupe_duplication"] :(isset($_GET["id_groupe_duplication"]) ? $_GET["id_groupe_duplication"] :NULL);
$type = isset($_POST["type"]) ? $_POST["type"] :(isset($_GET["type"]) ? $_GET["type"] :NULL);

$ctCompteRendu = null;
if ($type == 'CahierTexteTravailAFaire') {
	$ctCompteRendu = CahierTexteTravailAFairePeer::retrieveByPK($id_ct);
} elseif ($type == 'CahierTexteCompteRendu') {
	$ctCompteRendu = CahierTexteCompteRenduPeer::retrieveByPK($id_ct);
} elseif ($type == 'CahierTexteNoticePrivee') {
	$ctCompteRendu = CahierTexteNoticePriveePeer::retrieveByPK($id_ct);
}

if ($ctCompteRendu == null) {
	echo ("Erreur duplication de notice : pas de notice trouvée.");
	die();
}
$groupe = GroupePeer::retrieveByPK($id_groupe);
if ($groupe == null) {
	echo("Pas de groupe spécifié");
	die;
}

$deepcopy = 1;
$nouveauCompteRendu = $ctCompteRendu->copy($deepcopy);
$nouveauCompteRendu->setGroupe($groupe);
$nouveauCompteRendu->setDateCt($date_duplication);

$nouveauCompteRendu->save();
/*
$f=fopen("/tmp/gepi_debug_ct_dev.txt", "a+");
fwrite($f, "Copie de la notice $id_ct de type $type : Id de la nouvelle notice : ".$nouveauCompteRendu->getIdCt()."\n");
fclose($f);
if ($type == 'CahierTexteTravailAFaire') {
	$sql="SELECT 1=1 FROM ct_devoirs_entry WHERE special='controle' AND id_ct='$id_ct';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="UPDATE ct_devoirs_entry SET special='controle' WHERE id_ct='".$nouveauCompteRendu->getIdCt()."';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}
*/

if ($type=='CahierTexteTravailAFaire') {
	$type_ct="t";
}
elseif($type=='CahierTexteCompteRendu') {
	$type_ct="c";
}
else {
	$type_ct="p";
}
$tab_tag_notice_source=get_tab_tag_notice($id_ct, $type_ct);
if(isset($tab_tag_notice_source["id"])) {
	$id_ct_nouvelle_notice=$nouveauCompteRendu->getIdCt();

	for($loop=0;$loop<count($tab_tag_notice_source["id"]);$loop++) {
		$sql="INSERT INTO ct_tag SET id_ct='".$id_ct_nouvelle_notice."', type_ct='".$type_ct."', id_tag='".$tab_tag_notice_source["id"][$loop]."';";
		//echo "$sql<br />";
		/*
		$f=fopen("/tmp/gepi_debug_ct_dev.txt", "a+");
		fwrite($f, $sql."\n");
		fclose($f);
		*/
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}

$utilisateur->clearAllReferences();
echo("Duplication effectuée");
?>

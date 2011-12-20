<?php
/*
 *
 * Copyright 2009-2011 Josselin Jacquard
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

//Attention, la sortie standard de ce script (echo), doit etre soit une erreur soit l'id de la noice. La sortie est utilisée dans un javascript
//
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec l'échappement mysql déjà géré par propel
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/traitement_data.inc.php");

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_ct = isset($_POST["date_ct"]) ? $_POST["date_ct"] :(isset($_GET["date_ct"]) ? $_GET["date_ct"] :NULL);
$contenu = isset($_POST["contenu"]) ? $_POST["contenu"] :NULL;
$heure_entry = isset($_POST["heure_entry"]) ? $_POST["heure_entry"] :(isset($_GET["heure_entry"]) ? $_GET["heure_entry"] :NULL);
$uid_post = isset($_POST["uid_post"]) ? $_POST["uid_post"] :(isset($_GET["uid_post"]) ? $_GET["uid_post"] :0);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

// uid de pour ne pas refaire renvoyer plusieurs fois le meme formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid_prime = isset($_SESSION['uid_prime']) ? $_SESSION['uid_prime'] : 1;
if ($uid_post==$uid_prime) {
	echo("Erreur enregistrement de notice privee : formulaire dejà posté précédemment.");
	die();
}
$_SESSION['uid_prime'] = $uid_post;

//récupération du compte rendu
//$ctNoticePrivee = new CahierTexteNoticePrivee();
if ($id_ct != null) {
	$criteria = new Criteria();
	$criteria->add(CahierTexteNoticePriveePeer::ID_CT, $id_ct, "=");
	$ctNoticePrivees = $utilisateur->getCahierTexteNoticePrivees($criteria);
	$ctNoticePrivee = $ctNoticePrivees[0];
	if ($ctNoticePrivee == null) {
		echo "Erreur enregistrement de notice privee : notice non trouvée";
		die();
	}
	$groupe = $ctNoticePrivee->getGroupe();
} else {
	//si pas  du compte rendu précisé, récupération du groupe dans la requete et création d'un nouvel objet CahierTexteNoticePrivee
	foreach ($utilisateur->getGroupes() as $group) {
		if ($id_groupe == $group->getId()) {
			$groupe = $group;
			break;
		}
	}// cela economise un acces db par rapport à  $current_group = GroupePeer::retrieveByPK($id_groupe), et permet de ne pas avoir a nettoyer les reference de utilisateurs.
	if ($groupe == null) {
		echo("Erreur enregistrement de notice privee : pas de groupe ou mauvais groupe spécifié");
		die;
	}
	//pas de notices, on lance une création de notice
	$ctNoticePrivee = new CahierTexteNoticePrivee();
	$ctNoticePrivee->setIdGroupe($groupe->getId());
	$ctNoticePrivee->setIdLogin($utilisateur->getLogin());
}

if((isset($_POST['id_ct_a_importer']))&&($_POST['id_ct_a_importer']!='')) {
	if(preg_match("/^devoir_/", $_POST['id_ct_a_importer'])) {
		$id_ct_import=preg_replace("/^devoir_/", "", $_POST['id_ct_a_importer']);
		if(preg_match("/^[0-9]*$/", $id_ct_import)) {
			//$sql="SELECT id_ct, contenu FROM ct_devoirs_entry WHERE id_groupe='$id_groupe' AND id_ct='".$id_ct_import."';";
			$sql="SELECT DISTINCT id_ct, contenu FROM ct_devoirs_entry cde, j_groupes_professeurs jgp WHERE jgp.id_groupe=cde.id_groupe AND jgp.login='".$_SESSION['login']."' AND cde.id_ct='".$id_ct_import."';";
			$res_ct=mysql_query($sql);
			if(mysql_num_rows($res_ct)==1) {
				$lig_ct=mysql_fetch_object($res_ct);
				$contenu.=$lig_ct->contenu;
			}
		}
	}
	elseif(preg_match("/^compte_rendu_/", $_POST['id_ct_a_importer'])) {
		$id_ct_import=preg_replace("/^compte_rendu_/", "", $_POST['id_ct_a_importer']);
		if(preg_match("/^[0-9]*$/", $id_ct_import)) {
			//$sql="SELECT id_ct, contenu FROM ct_entry WHERE id_groupe='$id_groupe' AND id_ct='".$id_ct_import."';";
			$sql="SELECT DISTINCT id_ct, contenu FROM ct_entry ce, j_groupes_professeurs jgp WHERE jgp.id_groupe=ce.id_groupe AND jgp.login='".$_SESSION['login']."' AND ce.id_ct='".$id_ct_import."';";
			$res_ct=mysql_query($sql);
			if(mysql_num_rows($res_ct)==1) {
				$lig_ct=mysql_fetch_object($res_ct);
				$contenu.=$lig_ct->contenu;
			}
		}
	}
	if(preg_match("/^notice_privee_/", $_POST['id_ct_a_importer'])) {
		$id_ct_import=preg_replace("/^notice_privee_/", "", $_POST['id_ct_a_importer']);
		if(preg_match("/^[0-9]*$/", $id_ct_import)) {
			//$sql="SELECT id_ct, contenu FROM ct_private_entry WHERE id_groupe='$id_groupe' AND id_ct='".$id_ct_import."';";
			$sql="SELECT DISTINCT id_ct, contenu FROM ct_private_entry cpe, j_groupes_professeurs jgp WHERE jgp.id_groupe=cpe.id_groupe AND jgp.login='".$_SESSION['login']."' AND cpe.id_ct='".$id_ct_import."';";
			$res_ct=mysql_query($sql);
			if(mysql_num_rows($res_ct)==1) {
				$lig_ct=mysql_fetch_object($res_ct);
				$contenu.=$lig_ct->contenu;
			}
		}
	}
}

//affectation des parametres de la requete à l'objet ctNoticePrivee
$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
$contenu_cor = str_replace("\\r","",$contenu_cor);
$contenu_cor = str_replace("\\n","",$contenu_cor);
$contenu_cor = stripslashes($contenu_cor);
if ($contenu_cor == "" or $contenu_cor == "<br>") $contenu_cor = "...";
$ctNoticePrivee->setContenu($contenu_cor);
$ctNoticePrivee->setDateCt($date_ct);
$ctNoticePrivee->setGroupe($groupe);
$ctNoticePrivee->setHeureEntry($heure_entry);

//enregistrement de l'objet
$ctNoticePrivee->save();

echo ($ctNoticePrivee->getIdCt());
$utilisateur->clearAllReferences();
?>

<?php
/*
 * $Id$
 *
 * Copyright 2009 Josselin Jacquard
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

//Attention, la sortie standard de ce script (echo), doit etre soit une erreur soit l'id de la noice. La sortie est utilisée dans un javascript

header('Content-Type: text/html; charset=ISO-8859-1');
// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste une notice ou un devoir, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
$traite_anti_inject = 'no';

require_once("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");
require_once("../lib/traitement_data.inc.php");

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

//récupération des paramètres de la requète
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
$date_ct = isset($_POST["date_ct"]) ? $_POST["date_ct"] :(isset($_GET["date_ct"]) ? $_GET["date_ct"] :NULL);
$contenu = isset($_POST["contenu"]) ? $_POST["contenu"] :NULL;
$heure_entry = isset($_POST["heure_entry"]) ? $_POST["heure_entry"] :(isset($_GET["heure_entry"]) ? $_GET["heure_entry"] :NULL);
$uid_post = isset($_POST["uid_post"]) ? $_POST["uid_post"] :(isset($_GET["uid_post"]) ? $_GET["uid_post"] :0);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

//parametre d'enregistrement de fichiers joints
if (empty($_FILES['doc_file'])) { $doc_file=''; } else { $doc_file=$_FILES['doc_file'];}
$doc_name = isset($_POST["doc_name"]) ? $_POST["doc_name"] :(isset($_GET["doc_name"]) ? $_GET["doc_name"] :NULL);

//parametre de changement de titre de fichier joint.
$doc_name_modif = isset($_POST["doc_name_modif"]) ? $_POST["doc_name_modif"] :(isset($_GET["doc_name_modif"]) ? $_GET["doc_name_modif"] :NULL);
$id_document = isset($_POST["id_document"]) ? $_POST["id_document"] :(isset($_GET["id_document"]) ? $_GET["id_document"] :NULL);

// uid de pour ne pas refaire renvoyer plusieurs fois le mÃªme formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid_prime = isset($_SESSION['uid_prime']) ? $_SESSION['uid_prime'] : 1;
if ($uid_post==$uid_prime) {
	echo("Erreur enregistrement de compte rendu : formulaire dejà posté précédemment.");
	die();
}
$_SESSION['uid_prime'] = $uid_post;

//récupération du compte rendu
//$ctCompteRendu = new CahierTexteCompteRendu();
if ($id_ct != null) {
	$criteria = new Criteria();
	$criteria->add(CahierTexteCompteRenduPeer::ID_CT, $id_ct, "=");
	$ctCompteRendus = $utilisateur->getCahierTexteCompteRendus($criteria);
	$ctCompteRendu = $ctCompteRendus[0];
	if ($ctCompteRendu == null) {
		echo "Erreur enregistrement de compte rendu : Compte rendu non trouvé";
		die();
	}
	$groupe = $ctCompteRendu->getGroupe();
} else {
	//si pas  du compte rendu précisé, récupération du groupe dans la requete et création d'un nouvel objet CahierTexteCompteRendu
	foreach ($utilisateur->getGroupes() as $group) {
		if ($id_groupe == $group->getId()) {
			$groupe = $group;
			break;
		}
	}// cela economise un acces db par rapport à  $current_group = GroupePeer::retrieveByPK($id_groupe), et permet de ne pas avoir a nettoyer les reference de utilisateurs.
	if ($groupe == null) {
		echo("Erreur enregistrement de compte rendu : pas de groupe ou mauvais groupe spécifié");
		die;
	}
	//pas de notices, on lance une création de notice
	$ctCompteRendu = new CahierTexteCompteRendu();
	$ctCompteRendu->setIdGroupe($groupe->getId());
	$ctCompteRendu->setIdLogin($utilisateur->getLogin());
}

// interdire la modification d'un visa par le prof si c'est un visa
if ($ctCompteRendu->getVise() == 'y') {
	echo("Erreur enregistrement de compte rendu : Notice signée, edition impossible/");
	die();
}

//affectation des parametres de la requete à l'objet ctCompteRendu
$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
$contenu_cor = str_replace("\\r","",$contenu_cor);
$contenu_cor = str_replace("\\n","",$contenu_cor);
$contenu_cor = stripslashes($contenu_cor);
if ($contenu_cor == "" or $contenu_cor == "<br>") $contenu_cor = "...";
$ctCompteRendu->setContenu($contenu_cor);
$ctCompteRendu->setDateCt($date_ct);
$ctCompteRendu->setGroupe($groupe);
$ctCompteRendu->setHeureEntry($heure_entry);

//enregistrement de l'objet
$ctCompteRendu->save();

//traitement de telechargement de documents joints
if (!empty($doc_file['name'][0])) {
	require_once("traite_doc.php");
	$total_max_size = getSettingValue("total_max_size");
	$max_size = getSettingValue("max_size");
	$dest_dir = "../documents/cl".$ctCompteRendu->getIdCt();

	//il y a au plus trois documents joints dans l'interface de saisie
	for ($index_doc=0; $index_doc < 3; $index_doc++) {
		if(!empty($doc_file['tmp_name'][$index_doc])) {
			$file_path = ajout_fichier($doc_file, $dest_dir, $index_doc, $id_groupe);
			if ($file_path != null) {
				//création de l'objet ctDocument
				$ctDocument = new CahierTexteCompteRenduFichierJoint();
				$ctDocument->setIdCt($ctCompteRendu->getIdCt());
				$ctDocument->setTaille($doc_file['size'][$index_doc]);
				$ctDocument->setEmplacement($file_path);
				if ($doc_name[$index_doc] != null) {
					$ctDocument->setTitre(corriger_caracteres($doc_name[$index_doc]));
				} else {
					$ctDocument->setTitre(basename($file_path));
				}
				$ctDocument->save();
				$ctCompteRendu->addCahierTexteCompteRenduFichierJoint($ctDocument);
				$ctCompteRendu->save();
			}
		}
	}
}

//traitement de changement de nom de fichiers joint
// Changement de nom
if (!empty($doc_name_modif) && (trim($doc_name_modif)) != '' && !empty($id_document)) {
	$titre = corriger_caracteres($doc_name_modif);
	$criteria = new Criteria(CahierTexteCompteRenduFichierJointPeer::DATABASE_NAME);
	$criteria->add(CahierTexteCompteRenduFichierJointPeer::ID, $id_document, '=');
	$documents = $ctCompteRendu->getCahierTexteCompteRenduFichierJoints($criteria);

	if (empty($documents)) {
		echo "Erreur enregistrement de compte rendu : document non trouvé.";
		die();
	}
	$document = $documents[0];
	if ($document == null) {
		echo "Erreur enregistrement de compte rendu : document non trouvé.";
		die();
	}
	$document->setTitre(corriger_caracteres($doc_name_modif));
	$document->save();
}

echo ($ctCompteRendu->getIdCt());
$utilisateur->clearAllReferences();
?>

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
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] :(isset($_GET["id_devoir"]) ? $_GET["id_devoir"] :NULL);
$date_devoir = isset($_POST["date_devoir"]) ? $_POST["date_devoir"] :(isset($_GET["date_devoir"]) ? $_GET["date_devoir"] :NULL);
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
	echo("Erreur enregistrement de devoir : formulaire dejà  posté précédemment.");
	die();
}
$_SESSION['uid_prime'] = $uid_post;

//récupération du compte rendu
$ctTravailAFaire = CahierTexteTravailAFairePeer::retrieveByPK($id_devoir);
if ($ctTravailAFaire != null) {
	$groupe = $ctTravailAFaire->getGroupe();
}

//si pas  du compte rendu trouvé, récupération du groupe dans la requete et création d'un nouvel objet CahierTexteCompteRendu
if ($ctTravailAFaire == null) {
	$groupe = GroupePeer::retrieveByPK($id_groupe);
	if ($groupe == null) {
		echo("Erreur enregistrement de devoir : pas de groupe ou mauvais groupe spécifié");
		die;
	}

	// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce groupe ?
	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur enregistrement de devoir : le groupe n'appartient pas au professeur";
		die();
	}

	//pas de notices, on lance une création de notice
	$ctTravailAFaire = new CahierTexteTravailAFaire();
	$ctTravailAFaire->setIdGroupe($groupe->getId());
	$ctTravailAFaire->setIdLogin($utilisateur->getLogin());
}

// Vérification : est-ce que l'utilisateur a le droit de travailler sur ce devoir ?
if ($ctTravailAFaire->getIdLogin() != $utilisateur->getLogin()) {
	echo("Erreur enregistrement de devoir : vous n'avez pas le droit de modifier cette notice.");
	die();
}


// interdire la modification d'un visa par le prof si c'est un visa
if ($ctTravailAFaire->getVise() == 'y') {
	echo("Erreur enregistrement de devoir : Notice signée, edition impossible/");
	die();
}

//affectation des parametres de la requete à l'objet ctCompteRendu
$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
$contenu_cor = str_replace("\\r","",$contenu_cor);
$contenu_cor = str_replace("\\n","",$contenu_cor);
$contenu_cor = stripslashes($contenu_cor);
if ($contenu_cor == "" or $contenu_cor == "<br>") $contenu_cor = "...";
$ctTravailAFaire->setContenu($contenu_cor);
$ctTravailAFaire->setDateCt($date_devoir);
$ctTravailAFaire->setGroupe($groupe);
//$ctTravailAFaire->setHeureEntry($heure_entry);

//enregistrement de l'objet
$ctTravailAFaire->save();

//traitement de telechargement de documents joints
if (!empty($doc_file['name'][0])) {
	require_once("traite_doc.php");
	$total_max_size = getSettingValue("total_max_size");
	$max_size = getSettingValue("max_size");
	$dest_dir = "../documents/cl_dev".$ctTravailAFaire->getIdCt();

	//il y a au plus trois documents joints dans l'interface de saisie
	for ($index_doc=0; $index_doc < 3; $index_doc++) {
		if(!empty($doc_file['tmp_name'][$index_doc])) {
			$file_path = ajout_fichier($doc_file, $dest_dir, $index_doc, $id_groupe);
			if ($file_path != null) {
				//création de l'objet ctDocument
				$ctDocument = new CahierTexteTravailAFaireFichierJoint();
				$ctDocument->setIdCtDevoir($ctTravailAFaire->getIdCt());
				$ctDocument->setTaille($doc_file['size'][$index_doc]);
				$ctDocument->setEmplacement($file_path);
				if ($doc_name[$index_doc] != null) {
					$ctDocument->setTitre(corriger_caracteres($doc_name[$index_doc]));
				} else {
					$ctDocument->setTitre(basename($file_path));
				}
				$ctDocument->save();
				$ctTravailAFaire->addCahierTexteTravailAFaireFichierJoint($ctDocument);
				$ctTravailAFaire->save();
			}
		}
	}
}

//traitement de changement de nom de fichiers joint
// Changement de nom
if (!empty($doc_name_modif) && (trim($doc_name_modif)) != '' && !empty($id_document)) {
	$titre = corriger_caracteres($doc_name_modif);
	$criteria = new Criteria(CahierTexteTravailAFaireFichierJointPeer::DATABASE_NAME);
	$criteria->add(CahierTexteTravailAFaireFichierJointPeer::ID, $id_document, '=');
	$documents = $ctTravailAFaire->getCahierTexteTravailAFaireFichierJoints($criteria);

	if (empty($documents)) {
		echo "Erreur enregistrement de devoir : document non trouvé.";
		die();
	}
	$document = $documents[0];
	if ($document == null) {
		echo "Erreur enregistrement de devoir :  document non trouvé.";
		die();
	}
	$document->setTitre(corriger_caracteres($doc_name_modif));
	$document->save();
}

echo($ctTravailAFaire->getIdCt());
$utilisateur->clearAllReferences();
?>

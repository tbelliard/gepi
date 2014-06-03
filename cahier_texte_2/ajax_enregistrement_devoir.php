<?php
/*
 *
 * Copyright 2009-2013 Josselin Jacquard
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

//Attention, la sortie standard de ce script (echo), doit etre soit une erreur soit l'id de la notice. La sortie est utilisée dans un javascript

header('Content-Type: text/html; charset=utf-8');

$filtrage_extensions_fichiers_table_ct_types_documents='y';

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

//debug_var();

//récupération des paramètres de la requète
$id_devoir = isset($_POST["id_devoir"]) ? $_POST["id_devoir"] :(isset($_GET["id_devoir"]) ? $_GET["id_devoir"] :NULL);
$date_devoir = isset($_POST["date_devoir"]) ? $_POST["date_devoir"] :(isset($_GET["date_devoir"]) ? $_GET["date_devoir"] :NULL);
$contenu = isset($_POST["contenu"]) ? $_POST["contenu"] :NULL;
$heure_entry = isset($_POST["heure_entry"]) ? $_POST["heure_entry"] :(isset($_GET["heure_entry"]) ? $_GET["heure_entry"] :NULL);
$uid_post = isset($_POST["uid_post"]) ? $_POST["uid_post"] :(isset($_GET["uid_post"]) ? $_GET["uid_post"] :0);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

$jour_visibilite=isset($_POST["jour_visibilite"]) ? $_POST["jour_visibilite"] :(isset($_GET["jour_visibilite"]) ? $_GET["jour_visibilite"] :NULL);
$heure_visibilite=isset($_POST["heure_visibilite"]) ? $_POST["heure_visibilite"] :(isset($_GET["heure_visibilite"]) ? $_GET["heure_visibilite"] :NULL);

//parametre d'enregistrement de fichiers joints
if (empty($_FILES['doc_file'])) { $doc_file=''; } else { $doc_file=$_FILES['doc_file'];}
$doc_name = isset($_POST["doc_name"]) ? $_POST["doc_name"] :(isset($_GET["doc_name"]) ? $_GET["doc_name"] :NULL);
$doc_masque = isset($_POST["doc_masque"]) ? $_POST["doc_masque"] :(isset($_GET["doc_masque"]) ? $_GET["doc_masque"] :NULL);

//parametre de changement de titre de fichier joint.
$doc_name_modif = isset($_POST["doc_name_modif"]) ? $_POST["doc_name_modif"] :(isset($_GET["doc_name_modif"]) ? $_GET["doc_name_modif"] :NULL);
$id_document = isset($_POST["id_document"]) ? $_POST["id_document"] :(isset($_GET["id_document"]) ? $_GET["id_document"] :NULL);

// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post==$_SESSION['uid_prime']
$uid_prime = isset($_SESSION['uid_prime']) ? $_SESSION['uid_prime'] : 1;
// Pour tester la mise en place d'une copie de sauvegarde, décommenter la ligne ci-dessous:
//$uid_post=$uid_prime;
if ($uid_post==$uid_prime) {
	if(getSettingValue('cdt2_desactiver_copie_svg')!='y') {
		$contenu_cor = traitement_magic_quotes(corriger_caracteres($contenu),'');
		$contenu_cor = str_replace("\\r","",$contenu_cor);
		$contenu_cor = str_replace("\\n","",$contenu_cor);
		//$contenu_cor = stripslashes($contenu_cor);
		if ($contenu_cor == "" or $contenu_cor == "<br>") {$contenu_cor = "...";}
	
		$sql="INSERT INTO ct_private_entry SET date_ct='$date_devoir', heure_entry='".strftime("%H:%M:%S")."', id_login='".$_SESSION['login']."', id_groupe='$id_groupe', contenu='<b>COPIE DE SAUVEGARDE</b><br />$contenu_cor';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);

		echo("Erreur enregistrement de devoir : formulaire dejà posté précédemment.\nUne copie de sauvegarde a été créée en notice privée.");
	}
	else {
		echo("Erreur enregistrement de devoir : formulaire dejà posté précédemment.");
	}

	die();
}
$_SESSION['uid_prime'] = $uid_post;

//récupération du compte rendu
$ctTravailAFaire = CahierTexteTravailAFairePeer::retrieveByPK($id_devoir);
if ($ctTravailAFaire != null) {
	$groupe = $ctTravailAFaire->getGroupe();

	if ($groupe == null) {
		echo("Erreur enregistrement de devoir : Pas de groupe associé au devoir");
		die;
	}

	if (!$groupe->belongsTo($utilisateur)) {
		echo "Erreur edition de compte rendu : le groupe n'appartient pas au professeur";
		die();
	}
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
	if(getSettingValue("cdt_autoriser_modif_multiprof")!="yes") {
		echo("Erreur enregistrement de devoir : vous n'avez pas le droit de modifier cette notice.");
		die();
	}
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

// Recuperation des images de formules mathematiques:
//if(getSettingValue('get_img_formules_math')=='y') {
	$contenu_cor=get_img_formules_math($contenu_cor, $id_groupe, "t");
//}

//=============================
// Corriger en chemins relatifs les chemins absolus débutant par getSettingValue('url_racine_gepi')...
// pas seulement: on peut avoir le nom DNS et l'IP dans le cas d'un gepi en DMZ ou plus généralement atteint en IP ou en nom DNS.
$url_absolues_gepi=getSettingValue("url_absolues_gepi");
if($url_absolues_gepi!="") {
	$contenu_cor=cdt_changer_chemin_absolu_en_relatif($contenu_cor);
}
//=============================

// 20130727
if($ctTravailAFaire->getContenu()!=$contenu_cor) {
	$date_modif=strftime("%Y-%m-%d %H:%M:%S");
	$sql="UPDATE ct_devoirs_faits SET etat='', commentaire='Le professeur a modifié la notice de travail à faire ($date_modif).', date_modif='".$date_modif."' WHERE id_ct='$id_devoir';";
	$update=mysqli_query($GLOBALS["mysqli"], $sql);
}

$ctTravailAFaire->setContenu($contenu_cor);
$ctTravailAFaire->setDateCt($date_devoir);
$ctTravailAFaire->setGroupe($groupe);
//$ctTravailAFaire->setHeureEntry($heure_entry);

/*
if(isset($id_devoir)) {
	$f=fopen("/tmp/gepi_test.txt","a+");
	fwrite($f, strftime("%d/%m/%Y %H:%M:%S").": id_devoir=$id_devoir\n");
	fclose($f);
}
*/

$date_visibilite_mal_formatee="n";
//echo "$heure_visibilite<br />\n";
if(!preg_match("/^[0-9]{1,2}:[0-9]{1,2}$/",$heure_visibilite)) {
	$heure_courante=strftime("%H:%M");
	if((!isset($id_devoir))||($id_devoir=="")) {
		echo "Erreur: Heure de visibilité mal formatée : $heure_visibilite.\nL'heure courante sera utilisée : $heure_courante";
	}
	else {
		echo "Erreur: Heure de visibilité mal formatée : $heure_visibilite.\nLa date de visibilité ne sera pas modifiée (maintenue à ".get_date_heure_from_mysql_date($ctTravailAFaire->getDateVisibiliteEleve()).").";
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

	/*
	$f=fopen("/tmp/gepi_test.txt","a+");
	fwrite($f, "Date mal formatee: $jour_visibilite\n");
	fclose($f);
	*/

	if((!isset($id_devoir))||($id_devoir=="")) {
		echo "Erreur: Le jour de visibilité est mal formaté : $jour_visibilite.\nLe jour courant sera utilisé : $jour_courant";
	}
	else {
		echo "Erreur: Le jour de visibilité est mal formaté : $jour_visibilite.\nLa date de visibilité ne sera pas modifiée (maintenue à ".get_date_heure_from_mysql_date($ctTravailAFaire->getDateVisibiliteEleve()).").\n";
	}

	$jour_visibilite=$jour_courant;
	$date_visibilite_mal_formatee="y";
}
$tab_tmp=explode("/",$jour_visibilite);
$jour_v=$tab_tmp[0];
$mois_v=$tab_tmp[1];
$annee_v=$tab_tmp[2];

if((!isset($id_devoir))||($id_devoir=="")||($date_visibilite_mal_formatee=="n")) {
	$date_visibilite_eleve=mktime($heure_v,$min_v,0,$mois_v,$jour_v,$annee_v);
}
else {
	$date_visibilite_eleve=$ctTravailAFaire->getDateVisibiliteEleve();
}
$ctTravailAFaire->setDateVisibiliteEleve($date_visibilite_eleve);

//enregistrement de l'objet
$ctTravailAFaire->save();

//traitement de telechargement de documents joints
if (!empty($doc_file['name'][0])) {
	require_once("traite_doc.php");
	$total_max_size = getSettingValue("total_max_size");
	$max_size = getSettingValue("max_size");
        $multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'].'/' : NULL;
        if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/'.$multi) === false){
            mkdir('../documents/'.$multi);
        }
	$dest_dir = '../documents/'.$multi.'cl_dev'.$ctTravailAFaire->getIdCt();

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
				if(isset($doc_masque[$index_doc])) {
					$ctDocument->setVisibleEleveParent(false);
				}
				else {
					$ctDocument->setVisibleEleveParent(true);
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

//traitement de la copie de fichier joint
if (isset($_REQUEST['ct_a_importer_class']) && isset($_REQUEST['id_ct_a_importer'])) {
        $classname = $_REQUEST["ct_a_importer_class"].'Query';
        if (class_exists($classname)) {
            $notice = call_user_func($classname .'::create')->findOneByPrimaryKey($_REQUEST["id_ct_a_importer"]);
            if ($notice != null && $ctTravailAFaire!= null 
                    && $notice != $ctTravailAFaire) {//pour la dernière condition, on évite de copier les fichiers joints d'une notice sur elle même
                $method = 'get'.$_REQUEST["ct_a_importer_class"].'FichierJoints';
                foreach($notice->$method() as $fichier_joint_modele) {
                    $fj = new CahierTexteTravailAFaireFichierJoint();
                    $fj->setEmplacement($fichier_joint_modele->getEmplacement());
                    $fj->setTitre($fichier_joint_modele->getTitre());
                    $fj->setTaille($fichier_joint_modele->getTaille());
                    $fj->save();
                    $ctTravailAFaire->addCahierTexteTravailAFaireFichierJoint($fj);
                }
                $ctTravailAFaire->save();
            }
        } 
}

echo($ctTravailAFaire->getIdCt());
$utilisateur->clearAllReferences();
?>

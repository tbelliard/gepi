<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_msj")!='y') {
    die("Le module n'est pas activé.");
}

	include('../lib/fonction_dossier.php');
// uid de pour ne pas refaire renvoyer plusieurs fois le même formulaire
// autoriser la validation de formulaire $uid_post===$_SESSION['uid_prime']
if(empty($_SESSION['uid_prime'])) {
	$_SESSION['uid_prime']='';
}
if (empty($_GET['uid_post']) and empty($_POST['uid_post'])) {
	$uid_post='';
}else {
	$uid_post = isset($_GET['uid_post']) ? $_GET['uid_post'] : (isset($_POST['uid_post']) ? $_POST['uid_post'] : NULL);
}

$uid = md5(uniqid(microtime(), 1));
$_SESSION['uid_prime'] = $uid;

// on remplace les %20 par des espaces
$uid_post = preg_replace('/%20/',' ',$uid_post);
// et on vérifie si les deux infos sont identiques ou pas avant de savoir si le formulaire a été validé ou pas
if($uid_post===$_SESSION['uid_prime']) {
	$valide_form = 'yes';
} else {
	$valide_form = 'no';
}

// =========== variable à connaître et à initialiser ========
$site_de_miseajour = getSettingValue('site_msj_gepi');
//information FTP
$dossier_ftp_gepi = getSettingValue("dossier_ftp_gepi");
$affiche_info_rc='';
$affiche_info_beta='';
$ligne='';
$ligne2='';
$version_stable='';
$version_rc='';
$version_beta='';

$maj_logiciel = isset($_GET["maj_logiciel"]) ? $_GET["maj_logiciel"] : (isset($_POST["maj_logiciel"]) ? $_POST["maj_logiciel"] : NULL);
$maj_fichier = isset($_GET["maj_fichier"]) ? $_GET["maj_fichier"] : (isset($_POST["maj_fichier"]) ? $_POST["maj_fichier"] : NULL);
$maj_type = isset($_GET["maj_type"]) ? $_GET["maj_type"] : (isset($_POST["maj_type"]) ? $_POST["maj_type"] : NULL);
$tableau_select = isset($_GET["tableau_select"]) ? $_GET["tableau_select"] : (isset($_POST["tableau_select"]) ? $_POST["tableau_select"] : NULL);
$source_fichier_select = isset($_POST["source_fichier_select"]) ? $_POST["source_fichier_select"] : NULL;
$nom_fichier_select = isset($_POST["nom_fichier_select"]) ? $_POST["nom_fichier_select"] : NULL;
$emplacement_fichier_select = isset($_POST["emplacement_fichier_select"]) ? $_POST["emplacement_fichier_select"] : NULL;
$date_fichier_select = isset($_POST["date_fichier_select"]) ? $_POST["date_fichier_select"] : NULL;
$heure_fichier_select = isset($_POST["heure_fichier_select"]) ? $_POST["heure_fichier_select"] : NULL;
$md5_fichier_select = isset($_POST["md5_fichier_select"]) ? $_POST["md5_fichier_select"] : NULL;
$message_traitement = isset($_SESSION["message_traitement"]) ? $_SESSION["message_traitement"] : NULL;
$message_erreur = isset($_SESSION["message_erreur"]) ? $_SESSION["message_erreur"] : NULL;

//$ = isset($_GET[""]) ? $_GET[""] : (isset($_POST[""]) ? $_POST[""] : NULL);
//if(empty($_POST['source_fichier_select'])) { $source_fichier_select=''; } else {$source_fichier_select=$_POST['source_fichier_select']; }

// =============== header ========
$style_specifique = "mod_miseajour/lib/style_maj";
$javascript_specifique = "mod_miseajour/lib/javascript_maj";

require_once("../../lib/header.inc.php");

//on recherche le fichier de mise à jour sur le site du principal
if(url_exists($site_de_miseajour."version.msj")) {

	if (!$fp = fopen($site_de_miseajour."version.msj","r")) {
		// impossible d'ouvrire le fichier de mise à jour
		$_SESSION['message_erreur'] = "Impossible d'ouvrir le fichier d'information de mise à jour";
		header("Location: fenetre.php?maj_type=".$maj_type);
	} else {
		while (!feof($fp)) {
			//on parcourt toutes les lignes
			$ligne .= fgets($fp, 4096); // lecture du contenu de la ligne
		}
		fclose($fp);

		if(function_exists("mb_eregi")) {
			$ereg = mb_eregi("<info>(.*)</info>",$ligne,$stable_serveur);
			$erega = mb_eregi("<changelog>(.*)</changelog>",$stable_serveur[1],$changelog);
	
			$ereg1 = mb_eregi("<stable>(.*)</stable>",$ligne,$stable_serveur);
			$ereg1a = mb_eregi("<version_stable>(.*)</version_stable>",$stable_serveur[1],$version_stable_serveur);
			$ereg2a = mb_eregi("<site_stable>(.*)</site_stable>",$stable_serveur[1],$version_stable_site);
			$ereg3a= mb_eregi("<fichier_stable>(.*)</fichier_stable>",$stable_serveur[1],$version_stable_fichier);
			$ereg4a= mb_eregi("<md5_stable>(.*)</md5_stable>",$stable_serveur[1],$version_stable_md5);
	
			if (getSettingValue("rc_module_msj")==='y') {
				$ereg2 = mb_eregi("<rc>(.*)</rc>",$ligne,$rc_serveur);
				$ereg1b = mb_eregi("<version_rc>(.*)</version_rc>",$rc_serveur[1],$version_rc_serveur);
				$ereg2b = mb_eregi("<site_rc>(.*)</site_rc>",$rc_serveur[1],$version_rc_site);
				$ereg3b = mb_eregi("<fichier_rc>(.*)</fichier_rc>",$rc_serveur[1],$version_rc_fichier);
				$ereg4b = mb_eregi("<md5_rc>(.*)</md5_rc>",$rc_serveur[1],$version_rc_md5);
				$affiche_info_rc="oui";
			}
	
			if (getSettingValue("beta_module_msj")==='y') {
				$ereg3 = mb_eregi("<beta>(.*)</beta>",$ligne,$beta_serveur);
				$ereg1c = mb_eregi("<version_beta>(.*)</version_beta>",$beta_serveur[1],$version_beta_serveur);
				$ereg2c = mb_eregi("<site_beta>(.*)</site_beta>",$beta_serveur[1],$version_beta_site);
				$ereg3c = mb_eregi("<fichier_beta>(.*)</fichier_beta>",$beta_serveur[1],$version_beta_fichier);
				$ereg4c = mb_eregi("<md5_beta>(.*)</md5_beta",$beta_serveur[1],$version_beta_md5);
				$affiche_info_beta="oui";
			}
		}
		else {
			$ereg = eregi("<info>(.*)</info>",$ligne,$stable_serveur);
			$erega = eregi("<changelog>(.*)</changelog>",$stable_serveur[1],$changelog);
	
			$ereg1 = eregi("<stable>(.*)</stable>",$ligne,$stable_serveur);
			$ereg1a = eregi("<version_stable>(.*)</version_stable>",$stable_serveur[1],$version_stable_serveur);
			$ereg2a = eregi("<site_stable>(.*)</site_stable>",$stable_serveur[1],$version_stable_site);
			$ereg3a= eregi("<fichier_stable>(.*)</fichier_stable>",$stable_serveur[1],$version_stable_fichier);
			$ereg4a= eregi("<md5_stable>(.*)</md5_stable>",$stable_serveur[1],$version_stable_md5);
	
			if (getSettingValue("rc_module_msj")==='y') {
				$ereg2 = eregi("<rc>(.*)</rc>",$ligne,$rc_serveur);
				$ereg1b = eregi("<version_rc>(.*)</version_rc>",$rc_serveur[1],$version_rc_serveur);
				$ereg2b = eregi("<site_rc>(.*)</site_rc>",$rc_serveur[1],$version_rc_site);
				$ereg3b = eregi("<fichier_rc>(.*)</fichier_rc>",$rc_serveur[1],$version_rc_fichier);
				$ereg4b = eregi("<md5_rc>(.*)</md5_rc>",$rc_serveur[1],$version_rc_md5);
				$affiche_info_rc="oui";
			}
	
			if (getSettingValue("beta_module_msj")==='y') {
				$ereg3 = eregi("<beta>(.*)</beta>",$ligne,$beta_serveur);
				$ereg1c = eregi("<version_beta>(.*)</version_beta>",$beta_serveur[1],$version_beta_serveur);
				$ereg2c = eregi("<site_beta>(.*)</site_beta>",$beta_serveur[1],$version_beta_site);
				$ereg3c = eregi("<fichier_beta>(.*)</fichier_beta>",$beta_serveur[1],$version_beta_fichier);
				$ereg4c = eregi("<md5_beta>(.*)</md5_beta",$beta_serveur[1],$version_beta_md5);
				$affiche_info_beta="oui";
			}
		}
	}

//on recherche la version du client
$version_stable_client[1]=getSettingValue('version');
if($affiche_info_rc==='oui') { $version_rc_client[1]=getSettingValue('versionRc'); }
if($affiche_info_beta==='oui') { $version_beta_client[1]=getSettingValue('versionBeta'); }

// version stable
$nouvelle_stable = 'non';
$texte_stable='pas de version stable disponible actuellement';
if($version_stable_serveur[1]>$version_stable_client[1]) {
	$texte_stable = 'une nouvelle version stable est disponible';
	$nouvelle_stable = 'oui';
} elseif($version_stable_serveur[1]===$version_stable_client[1]) {
	$texte_stable = 'votre version est à jour';
} elseif($version_stable_serveur[1]<$version_stable_client[1]) {
	$texte_stable = 'vous avez une version supérieur à celle disponible';
}

// version rc
if($affiche_info_rc==='oui')
{
	$nouvelle_rc = 'non';
	$texte_rc='pas de version RC disponible actuellement';
	$rc_version = explode('/', $version_rc_serveur[1]);
	if($rc_version[0]>$version_stable_client[1] or $rc_version[0]===$version_stable_client[1])
	{
		if($rc_version[1]>$version_rc_client[1] and $rc_version[1]!='0') {
			$texte_rc = 'une nouvelle version RC est disponible';
			$nouvelle_rc = 'oui';
		} elseif($rc_version[1]===$version_rc_client[1]) {
			$texte_rc = 'votre version RC est à jour';
		} elseif($rc_version[1]<$version_rc_client[1]) {
			$texte_rc = 'vous avez une version supérieur à celle disponible';
		}
	} else { $texte_rc = 'aucune version RC disponible actuellement'; }
}

// version beta
if($affiche_info_beta==='oui')
{
$nouvelle_beta = 'non';
$texte_beta='pas de version BETA disponible actuellement';
$beta_version = explode('/', $version_beta_serveur[1]);
if($beta_version[0]>$version_stable_client[1] or $beta_version[0]===$version_stable_client[1])
 {
	if($beta_version[1]>$version_beta_client[1] and $beta_version[1]!='0') {
		$texte_beta = 'une nouvelle version BETA est disponible';
		$nouvelle_beta = 'oui';
	} elseif($beta_version[1]===$version_beta_client[1]) {
		$texte_beta = 'votre version BETA à jour';
	} elseif($beta_version[1]<$version_beta_client[1]) {
		$texte_beta = 'vous avez une version supérieur à celle disponible';
	}
 } else { $texte_beta = 'aucune version BETA disponible actuellement'; }
 if( $rc_version[0]!='' ) { $nouvelle_beta='non'; }
}

// mettre à jour un fichier du logiciel
if($maj_fichier==='oui' and $valide_form==='yes' and $maj_type==='fichier')
 {
	check_token(false);

	// répertoire temporaire
	$rep_temp     = '../../documents/msj_temp/';

	$tableau_select['source_fichier']['1'] = $source_fichier_select;
	$tableau_select['nom_fichier']['1'] = $nom_fichier_select;
	$tableau_select['emplacement_fichier']['1'] = $emplacement_fichier_select;
	$tableau_select['date_fichier']['1'] = $date_fichier_select;
	$tableau_select['heure_fichier']['1'] = $heure_fichier_select;
	$tableau_select['md5_fichier']['1'] = $md5_fichier_select;

	// on copie le fichier dans le dossier temporaire
	$copie_fichier=copie_fichier_temp($tableau_select, $rep_temp);
	// on transfert le fichier via FTP
	$transfert_fichier=envoi_ftp($copie_fichier, $dossier_ftp_gepi);

	     //mise à jour ok on l'insère dans la base ou on le met à jour
	     // on regarde s'il existe déjas un enregistrement identitique
             $compte_msj = old_mysql_result(mysqli_query($GLOBALS["mysqli"], 'SELECT count(*) FROM '.$prefix_base.'miseajour WHERE fichier_miseajour="'.$tableau_select['nom_fichier']['1'].'" AND emplacement_miseajour="'.$tableau_select['emplacement_fichier']['1'].'"'),0);
	     // si oui
	     if( $compte_msj === "0" ) { $requete='INSERT INTO '.$prefix_base.'miseajour (fichier_miseajour, emplacement_miseajour, date_miseajour, heure_miseajour) values ("'.$tableau_select['nom_fichier']['1'].'","'.$tableau_select['emplacement_fichier']['1'].'","'.date_sql($tableau_select['date_fichier']['1']).'","'.$tableau_select['heure_fichier']['1'].'")'; }
	     // si non
             if( $compte_msj != "0" ) { $requete='UPDATE '.$prefix_base.'miseajour SET date_miseajour = "'.date_sql($tableau_select['date_fichier']['1']).'", heure_miseajour  = "'.$tableau_select['heure_fichier']['1'].'" WHERE fichier_miseajour="'.$tableau_select['nom_fichier']['1'].'" AND emplacement_miseajour="'.$tableau_select['emplacement_fichier']['1'].'"'; }
             $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	// on supprime le fichier du dossier temporaire
        unlink($rep_temp.$tableau_select['nom_fichier']['1']);
 }

if($maj_logiciel==='oui' and $valide_form==='yes')
{
	check_token(false);

	if($maj_type==='stable') { $site = $version_stable_site[1]; $fichier=$version_stable_fichier[1]; $md5_de_verif = $version_stable_md5[1]; }
	if($maj_type==='rc') { $site = $version_rc_site[1]; $fichier=$version_rc_fichier[1]; $md5_de_verif = $version_rc_md5[1]; }
	if($maj_type==='beta') { $site = $version_beta_site[1]; $fichier=$version_beta_fichier[1]; $md5_de_verif = $version_beta_md5[1]; }

 	//connaitre l'extension et le nom du fichier complet sans l'extension
	$nom_fichier_sans_ext=preg_replace("/\.zip/i",'',$fichier);
	if($nom_fichier_sans_ext!=$fichier) { $ext='.zip'; }
	else { $nom_fichier_sans_ext=preg_replace("/\.tar.gz/i",'',$fichier);
		if($nom_fichier_sans_ext!=$fichier) { $ext='.tar.gz'; }
	}

	// emplacement du fichier à jour
	$file = $site.$fichier;
	// emplacement de la copie sur le serveur à mettre à jour
	$newfile = '../../documents/msj_temp/'.$fichier;

	// si le dossier de téléchargement de mise à jour n'existe pas on le créer
	$rep_de_miseajour='../../documents/msj_temp/';
	if (!is_dir($rep_de_miseajour))
	 {
		$old = umask(0000);
		mkdir($rep_de_miseajour, 0777);
		chmod($rep_de_miseajour, 0777);
		umask($old);
	 }


	// on copie l'archive du logiciel à jour dans le dossier des mise à jour
	$old = umask(0000);
	if(!url_exists($file))
         {
	   // le téléchargement n'a pas réussi car le fichier de mise à jour n'est pas présent
	   $_SESSION['message_erreur'] = "Le fichier $file est inexistant";
           header("Location: fenetre.php?maj_type=".$maj_type);
	 } else {
		  // le fichier existe on le copie
		  if (!copy($file, $newfile))
                  {
		    // le téléchargement n'a pas réussi echec de connection au serveur de mise à jour
		    $_SESSION['message_erreur'] = "La copie du fichier $file n'a pas réussi...";
	            header("Location: fenetre.php?maj_type=".$maj_type);
		  } else {
			    umask($old);
			    // le fichier à été copier
			    // on vérifie le md5
			    $md5_du_fichier_telecharge = md5_file($rep_de_miseajour.$fichier);
			    if($md5_de_verif!=$md5_du_fichier_telecharge)
			    {
			      // si le md5 n'est pas bon message d'erreur
			      $_SESSION['message_erreur'] = "le fichier téléchargé est corrompu, mise à jour impossible";
			      header("Location: fenetre.php?maj_type=".$maj_type);
			     } else {
				      // si le md5 est bon on continue la mise à jour
				      // on décompresse le fichier
				      if(!class_exists("PclZip"))
   				      {
					// si il manque la bilbliothèque on donne un message d'erreur
					$_SESSION['message_erreur'] = "il manque la bibliothèque de décompression des fichiers";
			      	        header("Location: fenetre.php?maj_type=".$maj_type);
				      } else {
						   // source du fichier compressé
					  	   $source = $rep_de_miseajour.$fichier;
						   // destinations de la décompression du fichier
						   $destination = '../../documents/msj_temp';
					           if ($ext === ".zip") {
						      $old = umask(0000);
						      $archive = new PclZip($source);
						      if (@$archive -> extract(PCLZIP_OPT_PATH, $destination, PCLZIP_OPT_SET_CHMOD, 0777, PCLZIP_OPT_REMOVE_PATH, $nom_fichier_sans_ext) == TRUE) { unlink($source); } else { die("Error : ".$archive->errorInfo(true)); }
					              umask($old);
						      // on liste le dossier
						      $copie_fichier = listage_dossier($destination, $destination);
						      // on transfert via FTP
						      $transfert_fichier=envoi_ftp($copie_fichier, $dossier_ftp_gepi);
						      umask($old);
						      // on supprime le dossier msj_temp
						      $old = umask(0000);
						      $dossier_destination[0]=$destination;
						      supprimer_rep($dossier_destination);
						      // puis on le recrée
						      mkdir($destination, 0777);
					              umask($old);

							//mise à jour ok on l'insère dans la base
						     // puisque que c'est une nouvelle version on efface les données de la base mise à jour
						     $requete='TRUNCATE TABLE '.$prefix_base.'miseajour';
						     $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
						     // puis on informe la base de la version actuelle de la mise à jour
						     $requete='INSERT INTO '.$prefix_base.'miseajour (fichier_miseajour, emplacement_miseajour, date_miseajour, heure_miseajour) values ("'.$beta_version[0].'","","'.date('Y-m-d').'","'.date('H:i:s').'")';
					             $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
						   }
				           	   if ($ext === ".tar.gz") {
						      $old = umask(0000);
						      @$archive = PclTarExtract($source, $destination, 'gepi');
						      unlink($source);
//debug
// echo $archive[5][status];
						      // on liste le dossier
						      $copie_fichier = listage_dossier($destination, $destination);
						      // on transfert via FTP
						      $transfert_fichier=envoi_ftp($copie_fichier, $dossier_ftp_gepi);
						      umask($old);
						      // on supprime le dossier msj_temp
						      $old = umask(0000);
						      $dossier_destination[0]=$destination;
						      supprimer_rep($dossier_destination);
						      // puis on le recret
						      mkdir($destination, 0777);
					              umask($old);

							//mise à jour ok on l'insère dans la base
						     // puisque que c'est une nouvelle version on efface les données de la base mise à jour
						     $requete='TRUNCATE TABLE '.$prefix_base.'miseajour';
						     $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
						     // puis on informe la base de la version actuelle de la mise à jour
						     $requete='INSERT INTO '.$prefix_base.'miseajour (fichier_miseajour, emplacement_miseajour, date_miseajour, heure_miseajour) values ("'.$beta_version[0].'","","'.date('Y-m-d').'","'.date('H:i:s').'")';
					             $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
//debug
//echo '<pre>';
//print_r($copie_fichier);
//echo '<pre>';
						   }
						   $_SESSION['message_traitement'] = 'mise à jour terminée ! Pensez à vous déconnecter et vous reconnecter en administrateur après cette mise à jour';
						   $_SESSION['message_erreur'] = '';
				 		   header("Location: fenetre.php?maj_type=".$maj_type);
					       }
		                     }
			  }
	         }
}
?>

<center>
<h2>Syst&egrave;me de mise &agrave; jour du logiciel</h2>
<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin-left: 2px; margin-right: 2px; margin-top: 2px; margin-bottom: 0px;  text-align: left;">
   <div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin: 2px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">VERSION STABLE</div>
   <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 6px; margin: 2px;">
	<?php if($nouvelle_stable==='oui') { ?><div style="margin: 0px; padding: 0px; float: right; border-style:solid; border-width:1px; border-color: #6F6968; height: 25px;"><form action="fenetre.php?maj_logiciel=oui&amp;maj_type=stable" method="post" onSubmit="showWait('Mise à jour en cours...')">
	<?php
		echo add_token_field();
	?>
	<input type="hidden" name="donnee[]" value="" /><input type="hidden" name="uid_post" value="<?php echo preg_replace('/ /','%20',$uid); ?>" /><input type="submit" value="Mettre à jour vers là <?php echo $version_stable_serveur[1]; ?>" style="height: 25px; wight: auto;" onclick="return confirmlink(this,'Voulez vous le mettre à jour')" /></form></div><?php } ?>
	<span style=" font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #000000;">Votre version actuelle : <?php echo $version_stable_client[1]; ?></span><br />
	<span style="font-family: Helvetica,Arial,sans-serif; color: rgb(255, 0, 0); margin-left: 20px;"><?php echo $texte_stable; ?></span><br />
	<div style="font-family: Helvetica,Arial,sans-serif; color: rgb(0, 0, 0); margin-left: 20px; text-align: justify;">descriptif: <a href="<?php echo $changelog[1]; ?>" target="_blank">voir le changelog</a></div>
	<?php if(($message_traitement!='' or $message_erreur!='') and $maj_type==='stable') { ?>
	    <br />
            <table class="<?php if($message_erreur!='') { ?>table_erreur<?php } else { ?>table_info<?php } ?>" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td style="width: 28px;"><img src="<?php if($message_erreur!='') { ?>../../images/attention.png<?php } else { ?>../../images/info.png<?php } ?>" width="28" height="28" alt="" /></td>
                <td class="<?php if($message_erreur!='') { ?>erreur<?php } else { ?>info<?php } ?>"><strong><?php if($message_erreur!='') { echo $message_erreur; } else { echo $message_traitement; } ?></strong></td>
              </tr>
            </table>
	<?php } ?>
   </div>
</div>
<div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: #5A7ACF;  padding: 2px; margin-left: 20px; margin-right: 2px; margin-top: 0px; margin-bottom: 2px; text-align: left;">
   <div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: #5A7ACF;  padding: 0px; margin: 0px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">Fichier pouvant être mis à jour pour cet version.</div>
   <?php
	$donne_fichier = info_miseajour_fichier($site_de_miseajour, $version_stable_client[1]);
	$info_fichier_base = info_miseajour_base();
	$nb_a = '1';
while(!empty($donne_fichier['nom_fichier'][$nb_a])) { ?>
           <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 6px; margin: 2px;">
	   <?php
	   $identitification = $donne_fichier['emplacement_fichier'][$nb_a].''.$donne_fichier['nom_fichier'][$nb_a];
	   $amettreajour='non';
	   if(!empty($info_fichier_base[$identitification]['date']))
	   {
	       if($info_fichier_base[$identitification]['date']===$donne_fichier['date_fichier'][$nb_a])
	        {
	  	    if($info_fichier_base[$identitification]['heure']===$donne_fichier['heure_fichier'][$nb_a]) { $amettreajour='non'; $info_etat='version du fichier à jour'; }
  		    if($info_fichier_base[$identitification]['heure'] < $donne_fichier['heure_fichier'][$nb_a]) { $amettreajour='oui'; $info_etat='une mise à jour du fichier est disponible'; }
		    if($info_fichier_base[$identitification]['heure'] > $donne_fichier['heure_fichier'][$nb_a]) { $amettreajour='non'; $info_etat='version du fichier à jour'; }
 	        }
	       if($info_fichier_base[$identitification]['date'] < $donne_fichier['date_fichier'][$nb_a]) { $amettreajour='oui'; $info_etat='une mise à jour du fichier est disponible'; }
	       if($info_fichier_base[$identitification]['date'] > $donne_fichier['date_fichier'][$nb_a]) { $amettreajour='non'; $info_etat='version du fichier à jour'; }
	   }
	   if(empty($info_fichier_base[$identitification]['date']))
	   {
	     $amettreajour='oui';
		 $info_etat='une mise à jour du fichier est disponible';
	   }
	?>
	<?php if($amettreajour==='oui') { ?><div style="margin: 0px; padding: 0px; float: right; border-style:solid; border-width:1px; border-color: #6F6968; height: 25px;"><form action="fenetre.php?maj_fichier=oui&amp;maj_type=fichier" method="post" onSubmit="showWait('Mise à jour en cours...')">
	<?php
		echo add_token_field();
	?>
	<input type="hidden" name="uid_post" value="<?php echo preg_replace('/ /','%20',$uid); ?>" /><?php $tableau_select['source_fichier']['1'] = $donne_fichier['source_fichier'][$nb_a]; $tableau_select['nom_fichier']['1'] = $donne_fichier['nom_fichier'][$nb_a]; $tableau_select['emplacement_fichier']['1'] = $donne_fichier['emplacement_fichier'][$nb_a]; $tableau_select['date_fichier']['1'] = $donne_fichier['date_fichier'][$nb_a]; $tableau_select['heure_fichier']['1'] = $donne_fichier['heure_fichier'][$nb_a]; $tableau_select['md5_fichier']['1'] = $donne_fichier['md5_fichier'][$nb_a]; ?>
		<input type="hidden" name="source_fichier_select" value="<?php echo $donne_fichier['source_fichier'][$nb_a]; ?>" />
		<input type="hidden" name="nom_fichier_select" value="<?php echo $donne_fichier['nom_fichier'][$nb_a]; ?>" />
		<input type="hidden" name="emplacement_fichier_select" value="<?php echo $donne_fichier['emplacement_fichier'][$nb_a]; ?>" />
		<input type="hidden" name="date_fichier_select" value="<?php echo $donne_fichier['date_fichier'][$nb_a]; ?>" />
		<input type="hidden" name="heure_fichier_select" value="<?php echo $donne_fichier['heure_fichier'][$nb_a]; ?>" />
		<input type="hidden" name="md5_fichier_select" value="<?php echo $donne_fichier['md5_fichier'][$nb_a]; ?>" />
		<input type="submit" value="Mettre à jour le fichier" style="height: 25px; wight: auto;" /></form></div><?php } ?>
	<span style=" font-family: Helvetica,Arial,sans-serif; color: #000000;">Fichier <strong><?php echo $donne_fichier['nom_fichier'][$nb_a]; ?></strong> mise à jour du: <strong><?php echo $donne_fichier['date_fichier'][$nb_a].' '.$donne_fichier['heure_fichier'][$nb_a]; ?></strong></span><br />
	<span style="font-family: Helvetica,Arial,sans-serif; color: rgb(255, 0, 0); margin-left: 20px;"><?php echo $info_etat; ?></span><br />
	<div style="font-family: Helvetica,Arial,sans-serif; color: rgb(0, 0, 0); margin-left: 20px; text-align: justify;">descriptif: <?php echo $donne_fichier['descriptif_fichier'][$nb_a]; ?></div>
   </div>
<?php $amettreajour='non'; $info_etat=''; $nb_a++; } ?>
</div>

<?php
if($affiche_info_rc==='oui')
{ ?>
<br />
<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: rgb(255, 0, 0);  padding: 2px; margin-left: 2px; margin-right: 2px; margin-top: 2px; margin-bottom: 0px;  text-align: left;">
   <div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: rgb(255, 0, 0);  padding: 2px; margin: 2px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">VERSION RC</div>
   <div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 6px; margin: 2px;">
	<?php if($nouvelle_rc==='oui') { ?><div style="margin: 0px; padding: 0px; float: right; border-style:solid; border-width:1px; border-color: #6F6968; height: 25px;"><form action="fenetre.php?maj_logiciel=oui&amp;maj_type=stable" method="post" onSubmit="showWait('Mise à jour en cours...')">
	<?php
		echo add_token_field();
	?>
	<input type="hidden" name="donnee[]" value="" /><input type="hidden" name="uid_post" value="<?php echo preg_replace('/ /','%20',$uid); ?>" /><input type="submit" value="Passer en RC <?php echo $version_rc_serveur[1]; ?>" style="height: 25px; wight: auto;" onclick="return confirmlink(this,'Voulez vous le mettre à jour')" /></form></div><?php } ?>
	<span style=" font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #000000;">Version RC de GEPI a installer: <?php if($version_rc_client[1]!='') { echo 'RC '.$version_rc_client[1]; } else { echo 'aucune RC installé'; } ?></span><br />
	<span style="font-family: Helvetica,Arial,sans-serif; color: rgb(255, 0, 0); margin-left: 20px;"><?php echo $texte_rc; ?></span><br />
	<div style="font-family: Helvetica,Arial,sans-serif; color: rgb(0, 0, 0); margin-left: 20px; text-align: justify;">descriptif: <a href="<?php echo $changelog[1]; ?>" target="_blank">voir le changelog</a></div>
	<?php if(($message_traitement!='' or $message_erreur!='') and $maj_type==='rc') { ?>
	    <br />
            <table class="<?php if($message_erreur!='') { ?>table_erreur<?php } else { ?>table_info<?php } ?>" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td style="width: 28px;"><img src="<?php if($message_erreur!='') { ?>../../images/attention.png<?php } else { ?>../../images/info.png<?php } ?>" width="28" height="28" alt="" /></td>
                <td class="<?php if($message_erreur!='') { ?>erreur<?php } else { ?>info<?php } ?>"><strong><?php if($message_erreur!='') { echo $message_erreur; } else { echo $message_traitement; } ?></strong></td>
              </tr>
            </table>
	<?php } ?>
   </div>
</div>
<?php } ?>

<?php
if($affiche_info_beta==='oui')
{ ?>
<br />
	<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: rgb(255, 0, 0);  padding: 2px; margin-left: 2px; margin-right: 2px; margin-top: 2px; margin-bottom: 0px;  text-align: left;">
	<div style="border-style:solid; border-width:0px; border-color: #6F6968; background-color: rgb(255, 0, 0);  padding: 2px; margin: 2px; font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #FFFFFF;">VERSION BETA</div>
	<div style="border-style:solid; border-width:1px; border-color: #6F6968; background-color: #FFFFFF;  padding: 6px; margin: 2px;">
	<?php if($nouvelle_beta==='oui') { ?><div style="margin: 0px; padding: 0px; float: right; border-style:solid; border-width:1px; border-color: #6F6968; height: 25px;"><form action="fenetre.php?maj_logiciel=oui&amp;maj_type=stable" method="post" onSubmit="showWait('Mise à jour en cours...')">
	<?php
		echo add_token_field();
	?>
	<input type="hidden" name="donnee[]" value="" /><input type="hidden" name="uid_post" value="<?php echo preg_replace('/ /','%20',$uid); ?>" /><input type="submit" value="Passer en BETA <?php echo $version_beta_serveur[1]; ?>" style="height: 25px; wight: auto;" onclick="return confirmlink(this,'Voulez vous le mettre à jour')" /></form></div><?php } ?>
	<span style=" font-family: Helvetica,Arial,sans-serif; font-weight: bold; color: #000000;">Version BETA de GEPI a installer: <?php if($version_beta_client[1]!='') { echo 'BETA '.$version_beta_client[1]; } else { echo 'aucune'; } ?></span><br />
	<span style="font-family: Helvetica,Arial,sans-serif; color: rgb(255, 0, 0); margin-left: 20px;"><?php echo $texte_beta; ?></span><br />
	<div style="font-family: Helvetica,Arial,sans-serif; color: rgb(0, 0, 0); margin-left: 20px; text-align: justify;">descriptif: <a href="<?php echo $changelog[1]; ?>" target="_blank">voir le changelog</a></div>
	<?php if(($message_traitement!='' or $message_erreur!='') and $maj_type==='beta') { ?>
	    <br />
            <table class="<?php if($message_erreur!='') { ?>table_erreur<?php } else { ?>table_info<?php } ?>" border="0" cellpadding="4" cellspacing="2">
              <tr>
                <td style="width: 28px;"><img src="<?php if($message_erreur!='') { ?>../../images/attention.png<?php } else { ?>../../images/info.png<?php } ?>" width="28" height="28" alt="" /></td>
                <td class="<?php if($message_erreur!='') { ?>erreur<?php } else { ?>info<?php } ?>"><strong><?php if($message_erreur!='') { echo $message_erreur; } else { echo $message_traitement; } ?></strong></td>
              </tr>
            </table>
	<?php } ?>
   </div>
</div>
<?php } ?>
<br /><br /><a href="javascript:window.close();">Fermer la fenêtre</a>
</center>
</body>
</html>
<?php }
else {
	echo '

			<br /><br /><h3 class="center">Mise à jour impossible</h3>
			<p class="center">le serveur de mise à jour n\'est pas disponible</p>
		';
}
((is_null($___mysqli_res = mysqli_close($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

// Inclusion du footer
require_once("../../lib/footer.inc.php");
?>

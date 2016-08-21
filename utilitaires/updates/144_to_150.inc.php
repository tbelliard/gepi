<?php
/**
 * Mise à jour des bases vers la version 1.5.0
 * 
 * $Id: maj.php 7839 2011-08-20 08:17:28Z dblanqui $
 *
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */
		$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.5.0" . " :</h3>";
		$result .= "<p>";
		$result .= "&nbsp;->Extension de la taille du champ NAME de la table 'setting'<br />";
		$query28 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE setting CHANGE NAME NAME VARCHAR( 255 ) NOT NULL");
		if ($query28) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
		}

		$result .= "&nbsp;->Ajout du champ responsable à la table droits<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM droits LIKE 'responsable'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `droits` ADD `responsable` varchar(1) NOT NULL DEFAULT 'F' AFTER `eleve`");
			if ($query5) {
				$result .= msj_ok();

				foreach ($droits_requests as $key => $value) {
					$exec = traite_requete($value);
				}
			} else {
				$result .= msj_erreur('(le champ existe déjà ?)');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}


		$result .= "&nbsp;->Ajout du champ 'email' à la table 'eleves'<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'email'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `eleves` ADD `email` varchar(255) NOT NULL");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur('!');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les accès élèves et parents<br/>";
		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GepiAccesReleveEleve : ";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveEleve';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('GepiAccesReleveEleve', 'yes');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}


		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GepiAccesCahierTexteEleve : ";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'GepiAccesCahierTexteEleve';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('GepiAccesCahierTexteEleve', 'yes');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}

		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GepiAccesReleveParent : ";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'GepiAccesReleveParent';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('GepiAccesReleveParent', 'yes');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}

		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GepiAccesCahierTexteParent : ";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'GepiAccesCahierTexteParent';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('GepiAccesCahierTexteParent', 'yes');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}

		$result .= "&nbsp;->Ajout (si besoin) du paramètre autorisant l'utilisation de l'outil de récupération de mot de passe<br/>";
		$req_test = mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'enable_password_recovery'");
		$res_test = mysqli_num_rows($req_test);
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('enable_password_recovery', 'no');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}


		$result .= "&nbsp;->Ajout du champ password_ticket à la table utilisateurs<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'password_ticket'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `password_ticket` varchar(255) NOT NULL AFTER `date_verrouillage`");
			if ($query5) {
				$result .= msj_ok("Ok !");
			} else {
				$result .= msj_erreur('!');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout du champ ticket_expiration à la table utilisateurs<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'ticket_expiration'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `ticket_expiration` datetime NOT NULL AFTER `password_ticket`");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les droits d'accès à la fonction de réinitialisation du mot de passe perdu<br/>";
		$tmp_tab1=array("GepiPasswordReinitProf", "GepiPasswordReinitScolarite", "GepiPasswordReinitCpe", "GepiPasswordReinitAdmin", "GepiPasswordReinitEleve", "GepiPasswordReinitParent");
		$tmp_tab2=array("no", "no", "no", "no", "yes", "yes");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}


		$result .= "&nbsp;->Ajout (si besoin) du paramètre autorisant l'accès public aux cahiers de texte<br/>";
		$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cahier_texte_acces_public : ";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'cahier_texte_acces_public';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('cahier_texte_acces_public', 'no');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les droits d'accès à l'équipe pédagogique d'un élève<br/>";
		$tmp_tab1=array("GepiAccesEquipePedaEleve", "GepiAccesEquipePedaEmailEleve", "GepiAccesCpePPEmailEleve", "GepiAccesEquipePedaParent", "GepiAccesEquipePedaEmailParent", "GepiAccesCpePPEmailParent");
		$tmp_tab2=array("yes", "no", "no", "yes", "no", "no");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les droits d'accès aux bulletins simplifiés et relevés de notes<br/>";
		$tmp_tab1=array("GepiAccesBulletinSimpleEleve", "GepiAccesBulletinSimpleParent", "GepiAccesBulletinSimpleProf", "GepiAccesBulletinSimpleProfTousEleves", "GepiAccesBulletinSimpleProfToutesClasses", "GepiAccesReleveProfTousEleves");
		$tmp_tab2=array("yes", "yes", "yes", "no", "no", "yes");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les droits d'accès aux moyennes par les professeurs<br/>";
		$tmp_tab1=array("GepiAccesMoyennesProf", "GepiAccesMoyennesProfTousEleves", "GepiAccesMoyennesProfToutesClasses");
		$tmp_tab2=array("yes", "yes", "yes");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les droits d'accès aux graphiques de visualisation (eleves et responsables)<br/>";
		$tmp_tab1=array("GepiAccesGraphEleve", "GepiAccesGraphParent");
		$tmp_tab2=array("yes", "yes");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour les fiches d'information destinée aux nouveaux utilisateurs<br/>";
		$tmp_tab1=array("ImpressionParent", "ImpressionEleve", "ImpressionNombre", "ImpressionNombreParent", "ImpressionNombreEleve");
		$tmp_tab2=array("", "", "1", "1", "1");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		$result .= "&nbsp;->Ajout du champ show_email à la table utilisateurs<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'show_email'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `show_email` varchar(3) NOT NULL DEFAULT 'no' AFTER `email`");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout du champ ele_id à la table eleves<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM eleves LIKE 'ele_id'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `eleves` ADD `ele_id` varchar(10) NOT NULL DEFAULT '' AFTER `ereno`");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'bull_categ_font_size_avis'");
		$res_test = mysqli_num_rows($req_test);
		if ($res_test == 0){
			$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO setting VALUES ('bull_categ_font_size_avis', '10');");
			$result .= "Initialisation du paramètre bull_categ_font_size_avis à '10': ";
			if($query){
				$result .= msj_ok();
			}
			else{
				$result .= msj_erreur('!');
			}
		}

		$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'bull_police_avis'");
		$res_test = mysqli_num_rows($req_test);
		if ($res_test == 0){
			$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO setting VALUES ('bull_police_avis', 'Times New Roman');");
			$result .= "Initialisation du paramètre bull_police_avis à 'Times New Roman': ";
			if($query){
				$result .= msj_ok();
			}
			else{
				$result .= msj_erreur('!');
			}
		}

		$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'bull_font_style_avis'");
		$res_test = mysqli_num_rows($req_test);
		if ($res_test == 0){
			$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO setting VALUES ('bull_font_style_avis', 'Normal');");
			$result .= "Initialisation du paramètre bull_font_style_avis à Normal: ";
			if($query){
				$result .= msj_ok();
			}
			else{
				$result .= msj_erreur('!');
			}
		}


		$result .= "&nbsp;->Création de la table responsables2<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'responsables2'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE IF NOT EXISTS `responsables2` (
`ele_id` varchar(10) NOT NULL,
`pers_id` varchar(10) NOT NULL,
`resp_legal` varchar(1) NOT NULL,
`pers_contact` varchar(1) NOT NULL
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}

		$result .= "&nbsp;->Création de la table resp_pers<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'resp_pers'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE IF NOT EXISTS `resp_pers` (
`pers_id` varchar(10) NOT NULL,
`login` varchar(50) NOT NULL,
`nom` varchar(30) NOT NULL,
`prenom` varchar(30) NOT NULL,
`civilite` varchar(5) NOT NULL,
`tel_pers` varchar(255) NOT NULL,
`tel_port` varchar(255) NOT NULL,
`tel_prof` varchar(255) NOT NULL,
`mel` varchar(100) NOT NULL,
`adr_id` varchar(10) NOT NULL,
PRIMARY KEY  (`pers_id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}

		$result .= "&nbsp;->Création de la table resp_adr<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'resp_adr'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE IF NOT EXISTS `resp_adr` (
`adr_id` varchar(10) NOT NULL,
`adr1` varchar(100) NOT NULL,
`adr2` varchar(100) NOT NULL,
`adr3` varchar(100) NOT NULL,
`adr4` varchar(100) NOT NULL,
`cp` varchar(6) NOT NULL,
`pays` varchar(50) NOT NULL,
`commune` varchar(50) NOT NULL,
PRIMARY KEY  (`adr_id`)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}


		$result .= "&nbsp;->Passage de 10 caractères à 255 caractères des champs tel_pers, tel_port et tel_prof de la table resp_pers.<br />";
		$alter1 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `resp_pers` CHANGE `tel_pers` `tel_pers` VARCHAR( 255 )");
		$result .= "tel_pers: ";
		if ($alter1) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
		}
		$alter2 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `resp_pers` CHANGE `tel_port` `tel_port` VARCHAR( 255 )");
		$result .= "tel_port: ";
		if ($alter2) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
		}
		$alter3 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `resp_pers` CHANGE `tel_prof` `tel_prof` VARCHAR( 255 )");
		$result .= "tel_prof: ";
		if ($alter3) {
			$result .= msj_ok();
		} else {
			$result .= msj_erreur();
		}


		// affectation des modèles de bulletin  PDF aux classes
		$result .= "&nbsp;->Ajout du champs `modele_bulletin_pdf` à la table `classes`.<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM classes LIKE 'modele_bulletin_pdf'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `classes` ADD `modele_bulletin_pdf` VARCHAR( 255 ) NULL AFTER `display_moy_gen`");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur('!');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}


		$req_test= mysqli_query($GLOBALS["mysqli"], "SELECT VALUE FROM setting WHERE NAME = 'option_modele_bulletin'");
		$res_test = mysqli_num_rows($req_test);
		if ($res_test == 0){
			$query = mysqli_query($GLOBALS["mysqli"], "INSERT INTO `setting` VALUES ('option_modele_bulletin', '2');;");
			$result .= "Initialisation du paramètre option_modele_bulletin à '2': ";
			if($query){
				$result .= msj_ok();
			}
			else{
				$result .= msj_erreur('!');
			}
		}

		$result .= "&nbsp;->Création de la table tentatives_intrusion<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'tentatives_intrusion'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE `tentatives_intrusion` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `login` VARCHAR( 255 ) NULL , `adresse_ip` VARCHAR( 255 ) NOT NULL , `date` DATETIME NOT NULL , `niveau` SMALLINT NOT NULL , `fichier` VARCHAR( 255 ) NOT NULL , `description` TEXT NOT NULL , `statut` VARCHAR( 255 ) NOT NULL , PRIMARY KEY ( `id`, `login` )) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}

		$result .= "&nbsp;->Ajout du champs `niveau_alerte` à la table `utilisateurs`.<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'niveau_alerte'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `niveau_alerte` SMALLINT NOT NULL DEFAULT '0'");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur('!');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout du champs `observation_securite` à la table `utilisateurs`.<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'observation_securite'"));
		if ($test1 == 0) {
			$query5 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `observation_securite` TINYINT NOT NULL DEFAULT '0'");
			if ($query5) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur('!');
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}

		$result .= "&nbsp;->Ajout (si besoin) de paramètres par défaut pour la définition de la politique de sécurité<br/>";
		$tmp_tab1=array("security_alert_email_admin", "security_alert_email_min_level", "security_alert1_normal_cumulated_level", "security_alert1_normal_email_admin", "security_alert1_normal_block_user", "security_alert1_probation_cumulated_level", "security_alert1_probation_email_admin", "security_alert1_probation_block_user", "security_alert2_normal_cumulated_level", "security_alert2_normal_email_admin", "security_alert2_normal_block_user", "security_alert2_probation_cumulated_level", "security_alert2_probation_email_admin", "security_alert2_probation_block_user", "deverouillage_auto_periode_suivante");
		$tmp_tab2=array("yes", "1", "3", "yes", "no", "2", "yes", "no", "7", "yes", "yes", "5", "yes", "yes", "n");
		for($loop=0;$loop<count($tmp_tab1);$loop++) {
			$result .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$tmp_tab1[$loop]." (".$tmp_tab2[$loop].") : ";
			$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = '".$tmp_tab1[$loop]."';"));
			if ($test_champ==0) {
				$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('".$tmp_tab1[$loop]."', '".$tmp_tab2[$loop]."');");
				if ($query) {
						$result .= msj_ok("Ok !");
				} else {
						$result .= msj_erreur();
				}
			} else {
				$result .= msj_present("Paramètre déjà présent.");
			}
		}

		// Ajout Mod_absences
		$result .= "&nbsp;->Création de la table vs_alerts_eleves<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'vs_alerts_eleves'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE `vs_alerts_eleves` (
				  `id_alert_eleve` int(11) NOT NULL auto_increment,
				  `eleve_alert_eleve` varchar(100) NOT NULL,
				  `date_alert_eleve` date NOT NULL,
				  `groupe_alert_eleve` int(11) NOT NULL,
				  `type_alert_eleve` int(11) NOT NULL,
				  `nb_trouve` int(11) NOT NULL,
				  `temp_insert` varchar(100) NOT NULL,
				  `etat_alert_eleve` tinyint(4) NOT NULL,
				  `etatpar_alert_eleve` varchar(100) NOT NULL,
				  PRIMARY KEY  (`id_alert_eleve`)
				  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}

		$result .= "&nbsp;->Création de la table vs_alerts_groupes<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'vs_alerts_groupes'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE `vs_alerts_groupes` (
				  `id_alert_groupe` int(11) NOT NULL auto_increment,
				  `nom_alert_groupe` varchar(150) NOT NULL,
				  `creerpar_alert_groupe` varchar(100) NOT NULL,
				  PRIMARY KEY  (`id_alert_groupe`)
				  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}

		$result .= "&nbsp;->Création de la table vs_alerts_types<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW TABLES LIKE 'vs_alerts_types'"));
		if ($test1 == 0) {
			$query1 = mysqli_query($GLOBALS["mysqli"], "CREATE TABLE `vs_alerts_types` (
				  `id_alert_type` int(11) NOT NULL auto_increment,
				  `groupe_alert_type` int(11) NOT NULL,
				  `type_alert_type` varchar(10) NOT NULL,
				  `specifisite_alert_type` varchar(25) NOT NULL,
				  `eleve_concerne` text NOT NULL,
				  `date_debut_comptage` date NOT NULL,
				  `nb_comptage_limit` varchar(200) NOT NULL,
				  PRIMARY KEY  (`id_alert_type`)
				  ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
			if ($query1) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('La table existe déjà');
		}
		// Fin Ajout Mod_absences

		$result .= "&nbsp;->Ajout (si besoin) du paramètre sélectionnant la feuille de style à utiliser<br/>";
		$test_champ=mysqli_num_rows(mysqli_query($mysqli, "SELECT VALUE FROM setting WHERE NAME = 'gepi_stylesheet';"));
		if ($test_champ==0) {
			$query = mysqli_query($mysqli, "INSERT INTO setting VALUES ('gepi_stylesheet', 'style');");
			if ($query) {
					$result .= msj_ok("Ok !");
			} else {
					$result .= msj_erreur();
			}
		} else {
			$result .= msj_present("Paramètre déjà présent.");
		}


		$result .= "&nbsp;->Ajout du champ temp_dir à la table utilisateurs<br />";
		$test1 = mysqli_num_rows(mysqli_query($GLOBALS["mysqli"], "SHOW COLUMNS FROM utilisateurs LIKE 'temp_dir'"));
		if ($test1 == 0) {
			$query3 = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE `utilisateurs` ADD `temp_dir` VARCHAR( 255 ) NOT NULL AFTER `observation_securite`");
			if ($query3) {
				$result .= msj_ok();
			} else {
				$result .= msj_erreur();
			}
		} else {
			$result .= msj_present('Le champ existe déjà');
		}
        
        $result .= "</p>";
		
?>

<?php
/** Fonctions accessibles dans toutes les pages
 *
 *
 * @copyright Copyright 2001, 2019, 2021 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau, Romain Neil
 *
 * @package Initialisation
 * @subpackage general
 *
 */


/**
 * Fonctions de manipulation du gepi_alea contre les attaques CRSF
 *
 * @see share-csrf.inc.php
 */
include_once dirname(__FILE__) . '/share-csrf.inc.php';
/**
 * Fonctions qui produisent du code html
 *
 * @see share-html.inc.php
 */
include_once dirname(__FILE__) . '/share-html.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 *
 * @see share-notes.inc.php
 */
include_once dirname(__FILE__) . '/share-notes.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 *
 * @see share-aid.inc.php
 */
include_once dirname(__FILE__) . '/share-aid.inc.php';
/**
 * Fonctions de manipulation des conteneurs et des notes
 *
 * @see share-pdf.inc.php
 */
include_once dirname(__FILE__) . '/share-pdf.inc.php';


/**
 * Envoi d'un courriel
 *
 * @param string $sujet Le sujet du message
 * @param string $message Le message
 * @param string $destinataire Le destinataire
 * @param string $ajout_headers Text à ajouter dans le header
 * @param string $plain_ou_html format du mail (plain pour texte brut, html sinon)
 * @param array $tab_param_mail tableau des paramètres mail pour phpMailer
 * @throws \PHPMailer\PHPMailer\Exception
 */
function envoi_mail($sujet, $message, $destinataire, $ajout_headers = '', $plain_ou_html = "plain", $tab_param_mail = array(), $piece_jointe = "") {
	global $gepiPath, $niveau_arbo;

	$gepiPrefixeSujetMail = getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";

	if ($gepiPrefixeSujetMail != '') {
		$gepiPrefixeSujetMail .= " ";
	}

	$subject = $gepiPrefixeSujetMail . "GEPI : $sujet";
	//$subject = "=?UTF-8?B?".base64_encode($subject)."?=\r\n";

	if ((getSettingAOui('utiliser_phpmailer')) && (getSettingValue('phpmailer_smtp_host') != "") && (getSettingValue('phpmailer_smtp_port') != "") && (getSettingValue('phpmailer_from') != "")) {

		/*
		echo "<p>niveau_arbo=$niveau_arbo<br />";
		echo "tab_param_mail:<pre>";
		print_r($tab_param_mail);
		echo "</pre>";
		*/

		if (PHP_VERSION < '5.3') {
			if ((!isset($niveau_arbo)) || ("$niveau_arbo" == "") || ($niveau_arbo == 1)) {
				require_once("../lib/PHPMailer-5/PHPMailerAutoload.php");
			} elseif ("$niveau_arbo" == 'public') {
				require_once("../lib/PHPMailer/PHPMailerAutoload.php");
			} elseif ($niveau_arbo == 0) {
				require_once("lib/PHPMailer-5/PHPMailerAutoload.php");
			} elseif ($niveau_arbo == 2) {
				require_once("../../lib/PHPMailer-5/PHPMailerAutoload.php");
			}

			$mail = new PHPMailer;
		} else {
			if ((!isset($niveau_arbo)) || ("$niveau_arbo" == "") || ($niveau_arbo == 1)) {
				//require_once("../lib/PHPMailer/vendor/autoload.php");
				require_once("../lib/PHPMailer/src/PHPMailer.php");
				require_once("../lib/PHPMailer/src/SMTP.php");
				require_once("../lib/PHPMailer/src/Exception.php");
			} elseif ("$niveau_arbo" == 'public') {
				require_once("../lib/PHPMailer/src/PHPMailer.php");
				require_once("../lib/PHPMailer/src/SMTP.php");
				require_once("../lib/PHPMailer/src/Exception.php");
			} elseif ($niveau_arbo == 0) {
				require_once("lib/PHPMailer/src/PHPMailer.php");
				require_once("lib/PHPMailer/src/SMTP.php");
				require_once("lib/PHPMailer/src/Exception.php");
			} elseif ($niveau_arbo == 2) {
				require_once("../../lib/PHPMailer/src/PHPMailer.php");
				require_once("../../lib/PHPMailer/src/SMTP.php");
				require_once("../../lib/PHPMailer/src/Exception.php");
			}
			//$mail=new PHPMailer;
			$mail = new PHPMailer\PHPMailer\PHPMailer;
		}
		$mail->isSMTP();

		if (getSettingAOui('phpmailer_debug')) {
			$mail->SMTPDebug = 3;                               // Enable verbose debug output
		}

		$mail->Host = getSettingValue('phpmailer_smtp_host');
		$mail->Port = getSettingValue('phpmailer_smtp_port');

		if (getSettingAOui('phpmailer_smtp_auth')) {
			$mail->SMTPAuth = true;
			$mail->Username = getSettingValue('phpmailer_smtp_username');
			$mail->Password = getSettingValue('phpmailer_smtp_password');
		}

		$mail->SMTPSecure = getSettingValue('phpmailer_securite');

		$mail->CharSet = 'UTF-8';
		$message = ensure_utf8($message);

		/*
		$mail = new PHPMailer;
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'user@example.com';                 // SMTP username
		$mail->Password = 'secret';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		$mail->addAddress('ellen@example.com');               // Name is optional
		$mail->addReplyTo('info@example.com', 'Information');
		$mail->addCC('cc@example.com');
		$mail->addBCC('bcc@example.com');

		$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		*/

		if ($piece_jointe != "") {
			$path = $_SERVER['DOCUMENT_ROOT'] . $gepiPath . "/";
			$mail->addAttachment($path . $piece_jointe);
		}

		// From
		// INSERT INTO setting SET name='phpmailer_forcer_from', value='y';
		if (getSettingAOui('phpmailer_forcer_from')) {
			if ((isset($tab_param_mail['from'])) && (check_mail($tab_param_mail['from']))) {
				$mail->From = getSettingValue('phpmailer_from');
				$mail->FromName = 'Mail automatique Gepi';

				if (isset($tab_param_mail['from_name'])) {
					$mail->addReplyTo($tab_param_mail['from'], $tab_param_mail['from_name']);
				} else {
					$mail->addReplyTo($tab_param_mail['from']);
				}
			} else {
				//$mail->From = "ne-pas-repondre@".$_SERVER['SERVER_NAME'];
				//2015-04-08 15:35:54	CLIENT -> SERVER: MAIL FROM:<ne-pas-repondre@127.0.0.1>
				//2015-04-08 15:35:54	SERVER -> CLIENT: 501 5.1.7 Bad sender address syntax
				$mail->From = getSettingValue('phpmailer_from');
				$mail->FromName = 'Mail automatique Gepi';
			}
		} else {
			if ((isset($tab_param_mail['from'])) && (check_mail($tab_param_mail['from']))) {
				$mail->From = $tab_param_mail['from'];
				if (isset($tab_param_mail['from_name'])) {
					$mail->FromName = $tab_param_mail['from_name'];
				}
			} else {
				//$mail->From = "ne-pas-repondre@".$_SERVER['SERVER_NAME'];
				//2015-04-08 15:35:54	CLIENT -> SERVER: MAIL FROM:<ne-pas-repondre@127.0.0.1>
				//2015-04-08 15:35:54	SERVER -> CLIENT: 501 5.1.7 Bad sender address syntax
				$mail->From = getSettingValue('phpmailer_from');
				$mail->FromName = 'Mail automatique Gepi';
			}
		}

		// Destinataires
		if (isset($tab_param_mail['destinataire'])) {
			if (is_array($tab_param_mail['destinataire'])) {
				for ($loop = 0; $loop < count($tab_param_mail['destinataire']); $loop++) {
					if (isset($tab_param_mail['destinataire_name'][$loop])) {
						$mail->addAddress($tab_param_mail['destinataire'][$loop], $tab_param_mail['destinataire_name'][$loop]);
					} else {
						$mail->addAddress($tab_param_mail['destinataire'][$loop]);
					}
				}
			} else {
				if (isset($tab_param_mail['destinataire_name'])) {
					$mail->addAddress($tab_param_mail['destinataire'], $tab_param_mail['destinataire_name']);
				} else {
					$mail->addAddress($tab_param_mail['destinataire']);
				}
			}
		} else {
			$tmp_tab_mail = explode(",", $destinataire);
			for ($loop = 0; $loop < count($tmp_tab_mail); $loop++) {
				$dest_clean = preg_replace("#^.*<#", "", preg_replace("#>.*$#", "", $tmp_tab_mail[$loop]));
				$mail->addAddress($dest_clean);
			}
		}

		// CC
		if (isset($tab_param_mail['cc'])) {
			if (is_array($tab_param_mail['cc'])) {
				for ($loop = 0; $loop < count($tab_param_mail['cc']); $loop++) {
					if (isset($tab_param_mail['cc_name'][$loop])) {
						$mail->addCC($tab_param_mail['cc'][$loop], $tab_param_mail['cc_name'][$loop]);
					} else {
						$mail->addCC($tab_param_mail['cc'][$loop]);
					}
				}
			} else {
				if (isset($tab_param_mail['cc_name'])) {
					$mail->addCC($tab_param_mail['cc'], $tab_param_mail['cc_name']);
				} else {
					$mail->addCC($tab_param_mail['cc']);
				}
			}
		}

		// BCC
		if (isset($tab_param_mail['bcc'])) {
			if (is_array($tab_param_mail['bcc'])) {
				for ($loop = 0; $loop < count($tab_param_mail['bcc']); $loop++) {
					if (isset($tab_param_mail['bcc_name'][$loop])) {
						$mail->addBCC($tab_param_mail['bcc'][$loop], $tab_param_mail['bcc_name'][$loop]);
					} else {
						$mail->addBCC($tab_param_mail['bcc'][$loop]);
					}
				}
			} else {
				if (isset($tab_param_mail['bcc_name'])) {
					$mail->addBCC($tab_param_mail['bcc'], $tab_param_mail['bcc_name']);
				} else {
					$mail->addBCC($tab_param_mail['bcc']);
				}
			}
		}

		// ReplyTo
		if (isset($tab_param_mail['replyto'])) {
			if (is_array($tab_param_mail['replyto'])) {
				for ($loop = 0; $loop < count($tab_param_mail['replyto']); $loop++) {
					if (isset($tab_param_mail['replyto_name'][$loop])) {
						$mail->addReplyTo($tab_param_mail['replyto'][$loop], $tab_param_mail['replyto_name'][$loop]);
					} else {
						$mail->addReplyTo($tab_param_mail['replyto'][$loop]);
					}
				}
			} else {
				if (isset($tab_param_mail['replyto_name'])) {
					$mail->addReplyTo($tab_param_mail['replyto'], $tab_param_mail['replyto_name']);
				} else {
					$mail->addReplyTo($tab_param_mail['replyto']);
				}
			}
		}

		if ($plain_ou_html == "plain") {
			$mail->isHTML(false);
		} else {
			$mail->isHTML(true);
		}

		/*
		// A REVOIR:
		// Ca ne fonctionne pas... probable problème de syntaxe... s'inscrire sur la liste phpmailer pour prendre en compte ces paramètres
		if(isset($tab_param_mail['message_id'])) {
			$mail->AddCustomHeaders(array('Message-id', $tab_param_mail['message_id']));
		}

		if(isset($tab_param_mail['references'])) {
			$mail->AddCustomHeaders(array('References', $tab_param_mail['references']));
		}
		*/

		// Debug
		//$message.="\n\nEnvoi avec PHPMailer";

		$mail->Subject = $subject;
		$mail->Body = $message;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if (!$mail->send()) {
			$envoi = false;
		} else {
			$envoi = true;
		}

	} else {
		$subject = "=?UTF-8?B?" . base64_encode($subject) . "?=\r\n";

		$headers = "X-Mailer: PHP/" . phpversion() . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/$plain_ou_html; charset=UTF-8\r\n";
		if (strpos($ajout_headers, 'From:') === false) $headers .= "From: Mail automatique Gepi <ne-pas-repondre@" . $_SERVER['SERVER_NAME'] . ">\r\n";
		$headers .= $ajout_headers;

		if ($piece_jointe == "") {
			$headers = "X-Mailer: PHP/" . phpversion() . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/$plain_ou_html; charset=UTF-8\r\n";
			if (strpos($ajout_headers, 'From:') === false) $headers .= "From: Mail automatique Gepi <ne-pas-repondre@" . $_SERVER['SERVER_NAME'] . ">\r\n";
			$headers .= $ajout_headers;
		} else {
			$path = $_SERVER['DOCUMENT_ROOT'] . $gepiPath . "/";

			/*
			$f=fopen("/tmp/debug_PJ_".strftime("%Y%m%d").".txt", "a+");
			fwrite($f, "===============================\n");
			fwrite($f, "Date: ".strftime("%Y-%m-%d %H:%M:%S")."\n");
			fwrite($f, "\$path=".$path."\n");
			fwrite($f, "\$path.\$piece_jointe=".$path.$piece_jointe."\n");
			fclose($f);
			*/
			$type_piece_jointe = filetype($path . $piece_jointe);
			$data = chunk_split(base64_encode(file_get_contents($path . $piece_jointe)));

			$boundary = md5(uniqid(time()));

			$headers = "X-Mailer: PHP/" . phpversion() . "\r\n";
			$headers .= "X-Priority: 1 \r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\" \r\n";
			//$headers .= "Content-type: text/$plain_ou_html; charset=UTF-8\r\n";
			if (strpos($ajout_headers, 'From:') === false) $headers .= "From: Mail automatique Gepi <ne-pas-repondre@" . $_SERVER['SERVER_NAME'] . ">\r\n";
			$headers .= $ajout_headers;

			$tmp_message = "--$boundary \r\n";
			//$tmp_message .= "Content-Type: text/html; charset=UTF-8\r\n";
			$tmp_message .= "Content-type: text/$plain_ou_html; charset=UTF-8\r\n";
			$tmp_message .= "Content-Transfer-Encoding:8bit\r\n";
			$tmp_message .= "\r\n";
			$tmp_message .= $message;
			$tmp_message .= "\r\n";
			$tmp_message .= "--$boundary \r\n";
			$tmp_message .= "Content-Type: $type_piece_jointe; name=\"" . basename($piece_jointe) . "\" \r\n";
			$tmp_message .= "Content-Transfer-Encoding: base64 \r\n";
			$tmp_message .= "Content-Disposition: attachment; filename=\"" . basename($piece_jointe) . "\" \r\n";
			$tmp_message .= "\r\n";
			$tmp_message .= $data . "\r\n";
			$tmp_message .= "\r\n";
			$tmp_message .= "--" . $boundary . "--";

			$message = $tmp_message;

		}

		// On envoie le mail
		$envoi = mail($destinataire,
			$subject,
			$message,
			$headers);
	}

	if (getSettingAOui('log_envoi_mail')) {
		$dirname = "$gepiPath/backup/" . getSettingValue("backup_directory");
		if (isset($niveau_arbo)) {
			if ($niveau_arbo == 0) {
				$dirname = "./backup/" . getSettingValue("backup_directory");
			} elseif (($niveau_arbo == 1) || ("$niveau_arbo" == 'public')) {
				$dirname = "../backup/" . getSettingValue("backup_directory");
			} elseif ($niveau_arbo == 2) {
				$dirname = "../../backup/" . getSettingValue("backup_directory");
			}

			$ts = french_strftime("%a %d/%m/%Y %H:%M:%S");
			$f = fopen($dirname . "/debug_envoi_mail_.log", "a+");
			fwrite($f, "==========================\n" . $ts . " :\r\n");
			if (!$envoi) {
				fwrite($f, "ECHEC de l'envoi de mail\r\n");
			} else {
				fwrite($f, "SUCCES de l'envoi de mail\r\n");
			}
			fwrite($f, "HTTP_REFERER : " . $_SERVER['HTTP_REFERER'] . "\r\n");
			if (isset($_SESSION['login'])) {
				fwrite($f, "\$_SESSION['login'] : " . $_SESSION['login'] . "\r\n");
			}
			fwrite($f, "Sujet : $sujet\r\n");
			fwrite($f, "Destinataire : $destinataire\r\n");

			if (isset($tab_param_mail['destinataire'])) {
				if (is_array($tab_param_mail['destinataire'])) {
					for ($loop = 0; $loop < count($tab_param_mail['destinataire']); $loop++) {
						fwrite($f, "\$tab_param_mail['destinataire'][$loop] : " . $tab_param_mail['destinataire'][$loop] . "\r\n");
					}
				} else {
					fwrite($f, "\$tab_param_mail['destinataire'] : " . $tab_param_mail['destinataire'] . "\r\n");
				}
			}

			if (isset($tab_param_mail['cc'])) {
				if (is_array($tab_param_mail['cc'])) {
					for ($loop = 0; $loop < count($tab_param_mail['cc']); $loop++) {
						fwrite($f, "\$tab_param_mail['cc'][$loop] : " . $tab_param_mail['cc'][$loop] . "\r\n");
					}
				} else {
					fwrite($f, "\$tab_param_mail['cc'] : " . $tab_param_mail['cc'] . "\r\n");
				}
			}

			if (isset($tab_param_mail['bcc'])) {
				if (is_array($tab_param_mail['bcc'])) {
					for ($loop = 0; $loop < count($tab_param_mail['bcc']); $loop++) {
						fwrite($f, "\$tab_param_mail['bcc'][$loop] : " . $tab_param_mail['bcc'][$loop] . "\r\n");
					}
				} else {
					fwrite($f, "\$tab_param_mail['bcc'] : " . $tab_param_mail['bcc'] . "\r\n");
				}
			}

			fwrite($f, "Headers suppl : $ajout_headers\r\n");
			fclose($f);
		}
	}

	return $envoi;
}

/**
 * Verification de la validité d'un mot de passe
 *
 * longueur : getSettingValue("longmin_pwd") minimum
 *
 * composé de lettres et d'au moins un chiffre
 *
 * @param string $password Mot de passe
 * @param boolean $flag Si $flag = 1, il faut également au moins un caractères spécial
 * @return boolean TRUE si le mot de passe est valable
 * @see getSettingValue()
 */
function verif_mot_de_passe($password, $flag) {
	global $info_verif_mot_de_passe;

	if ($flag == 1) {
		if (preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe = "Le mot de passe ne doit pas être uniquement numérique ou uniquement alphabétique.";
			return FALSE;
		} elseif (preg_match("/^[[:alnum:]\W]{" . getSettingValue("longmin_pwd") . ",}$/", $password) and preg_match("/[\W]+/", $password) and preg_match("/[0-9]+/", $password)) {
			$info_verif_mot_de_passe = "";
			return TRUE;
		} else {
			if (preg_match("/^[A-Za-z0-9]*$/", $password)) {
				$info_verif_mot_de_passe = "Le mot de passe doit comporter au moins un caractère spécial (#, *,...).";
			} elseif (mb_strlen($password) < getSettingValue("longmin_pwd")) {
				$info_verif_mot_de_passe = "La longueur du mot de passe doit être supérieure ou égale à " . getSettingValue("longmin_pwd") . ".";
				return FALSE;
			} else {
				// Euh... qu'est-ce qui a été saisi?
				$info_verif_mot_de_passe = "";
			}
			return FALSE;
		}
	} else {
		if (preg_match("/(^[a-zA-Z]*$)|(^[0-9]*$)/", $password)) {
			$info_verif_mot_de_passe = "Le mot de passe ne doit pas être uniquement numérique ou uniquement alphabétique.";
			return FALSE;
		} elseif (mb_strlen($password) < getSettingValue("longmin_pwd")) {
			$info_verif_mot_de_passe = "La longueur du mot de passe doit être supérieure ou égale à " . getSettingValue("longmin_pwd") . ".";
			return FALSE;
		} else {
			$info_verif_mot_de_passe = "";
			return TRUE;
		}
	}
}

/**
 * Teste si le login existe déjà dans la base
 *
 * @param string $s le login testé
 * @return string yes si le login existe, no sinon
 */
function test_unique_login($s, $sauf_tempo_utilisateurs = "n") {
	global $mysqli;
	// On vérifie que le login ne figure pas déjà dans la base utilisateurs
	$sql1 = "SELECT login FROM utilisateurs WHERE (login='$s' OR login='" . my_strtoupper($s) . "')";
	$resultat = mysqli_query($mysqli, $sql1);
	$test1 = $resultat->num_rows;
	$resultat->close();

	if ($test1 != "0") {
		return 'no';
	} else {
		$sql2 = "SELECT login FROM eleves WHERE (login='$s' OR login = '" . my_strtoupper($s) . "')";
		$resultat = mysqli_query($mysqli, $sql2);
		$test2 = $resultat->num_rows;
		$resultat->close();
		if ($test2 != "0") {
			return 'no';
		} else {
			$sql3 = "SELECT login FROM resp_pers WHERE (login='$s' OR login='" . my_strtoupper($s) . "')";
			$resultat = mysqli_query($mysqli, $sql2);
			$test3 = $resultat->num_rows;
			$resultat->close();
			if ($test3 != "0") {
				return 'no';
			} elseif ($sauf_tempo_utilisateurs == "n") {
				$sql4 = "SELECT login FROM tempo_utilisateurs WHERE (login='$s' OR login='" . my_strtoupper($s) . "')";
				$resultat = mysqli_query($mysqli, $sql4);
				$test4 = $resultat->num_rows;
				$resultat->close();
				if ($test4 != "0") {
					return 'no';
				} else {
					return 'yes';
				}
			} else {
				return 'yes';
			}
		}
	}
}

/**
 * Vérifie l'unicité du login
 *
 * On vérifie que le login ne figure pas déjà dans une des bases élève des années passées
 *
 * @param string $s le login à vérifier
 * @param <type> $indice ??
 * @return string yes si le login existe, no sinon
 */
function test_unique_e_login($s, $indice, $sauf_tempo_utilisateurs = "n") {
	global $mysqli;
	// On vérifie que le login ne figure pas déjà dans la base utilisateurs

	$test7 = test_unique_login($s, $sauf_tempo_utilisateurs);
	if ($test7 == "no") {
		// Si le login figure déjà dans une des bases élève des années passées ou bien
		// dans la base utilisateurs, on retourne 'no' !
		return 'no';
	} else {
		// Si le login ne figure pas dans une des bases élève des années passées ni dans la base
		// utilisateurs, on vérifie qu'un même login ne vient pas d'être attribué !
		$sql_tempo2 = "SELECT col2 FROM tempo2 WHERE (col2='$s' or col2='" . my_strtoupper($s) . "')";

		$resultat = mysqli_query($mysqli, $sql_tempo2);
		$test_tempo2 = $resultat->num_rows;
		$resultat->close();
		if ($test_tempo2 != "0") {
			return 'no';
		} else {
			$reg = mysqli_query($mysqli, "INSERT INTO tempo2 VALUES ('$indice', '$s')");
			return 'yes';
		}
	}
}

/**
 * Génére le login à partir du nom et du prénom
 *
 * Génère puis nettoie un login pour qu'il soit valide et unique
 *
 * Le mode de génération doit être passé en argument
 *
 * cf. http://sacoche.sesamath.net/appel_doc.php?fichier=support_administrateur__gestion_format_logins
 *
 * @param string $_nom nom de l'utilisateur
 * @param string $_prenom prénom de l'utilisateur
 * @param string $_mode Le mode de génération ou NULL
 * @param string $_casse La casse du login ('maj', 'min') est par défaut en minuscules
 * @return string|boolean Le login généré ou false si on obtient un login vide
 * @see test_unique_login()
 */
function generate_unique_login($_nom, $_prenom, $_mode, $_casse = 'min') {

	if (($_mode == NULL) || (!check_format_login($_mode))) {
		$_mode = "nnnnnnnnnnnnnnnnnnnn";
	}

	//==========================
	// Nettoyage des caractères du nom et du prénom

	$_prenom = remplace_accents(preg_replace("/Æ/", "AE", preg_replace("/æ/", "ae", preg_replace("/Œ/", "OE", preg_replace("/œ/", "oe", $_prenom)))));

	$prenoms = explode(" ", $_prenom);
	$premier_prenom = $prenoms[0];
	$prenom_compose = '';
	if (isset($prenoms[1])) {
		$prenom_compose = $prenoms[0] . "-" . $prenoms[1];
	}

	$_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);

	$_nom = remplace_accents(preg_replace("/Æ/", "AE", preg_replace("/æ/", "ae", preg_replace("/Œ/", "OE", preg_replace("/œ/", "oe", $_nom)))));
	$_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	//==========================
	// Nettoyage historique... éventuellement à revoir

	$_nom = preg_replace("/[ ']/", "", $_nom);
	$_nom = preg_replace("/-/", "_", $_nom);

	$_prenom = preg_replace("/[ ']/", "", $_prenom);
	$_prenom = preg_replace("/-/", "_", $_prenom);

	//==========================
	if (getSettingAOui("FiltrageStrictAlphaNomPrenomPourLogin")) {
		$_nom = preg_replace("/[^A-Za-z]/", "", $_nom);
		$_prenom = preg_replace("/[^A-Za-z]/", "", $_prenom);
	}
	//==========================
	// On génère le login

	if ((preg_match('/n/', $_mode)) && ($_nom == "")) {
		return false;
	} elseif ((preg_match('/p/', $_mode)) && ($_prenom == "")) {
		return false;
	} else {
		$nb_n = mb_strlen(preg_replace('/[^n]/', '', $_mode));
		$nb_p = mb_strlen(preg_replace('/[^p]/', '', $_mode));
		$separateur = preg_replace('/[^._-]/', '', $_mode);
		//echo "<br />";
		//echo "\$_prenom=$_prenom<br />";
		//echo "\$_nom=$_nom<br />";

		$part_prenom = mb_substr($_prenom, 0, min($nb_p, mb_strlen($_prenom)));
		$part_nom = mb_substr($_nom, 0, min($nb_n, mb_strlen($_nom)));

		//echo "\$part_prenom=$part_prenom<br />";
		//echo "\$part_nom=$part_nom<br />";

		if (preg_match('/^p/', $_mode)) {
			// C'est un mode commençant par une portion de prénom
			$temp1 = $part_prenom . $separateur . $part_nom;
		} else {
			// C'est un mode commençant par une portion de nom
			$temp1 = $part_nom . $separateur . $part_prenom;
		}
		//echo "\$temp1=$temp1<br />";

		// Révision de la casse
		if ($_casse == 'maj') {
			$temp1 = my_strtoupper($temp1);
		} //elseif($_casse=='min') {
		else {
			$temp1 = my_strtolower($temp1);
		}

		// Suppression des _,-,. multiples
		$temp1 = preg_replace("/_{2,}/", "_", $temp1);
		$temp1 = preg_replace("/\.{2,}/", ".", $temp1);
		$temp1 = preg_replace("/\-{2,}/", "-", $temp1);

		$login_user = $temp1;

		//==========================
		// Nettoyage final
		$login_user = mb_substr($login_user, 0, 50);
		$login_user = preg_replace("/[^A-Za-z0-9._\-]/", "", trim($login_user));

		//$test1 = $login_user{0};
		$test1 = mb_substr($login_user,0,1);
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = mb_substr($login_user, 1);
			//$test1 = $login_user{0};
			$test1 = mb_substr($login_user,0,1);
		}
	
		//$test1 = $login_user{mb_strlen($login_user)-1};
		$test1 = mb_substr($login_user, mb_strlen($login_user) - 1, 1);
		while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
			$login_user = mb_substr($login_user, 0, mb_strlen($login_user)-1);
			//$test1 = $login_user{mb_strlen($login_user)-1};
			$test1 = mb_substr($login_user, mb_strlen($login_user) - 1, 1);
		}

		//==========================
		// On teste l'unicité du login que l'on vient de créer
		$m = '';
		$test_unicite = 'no';
		while ($test_unicite != 'yes') {
			if (($m != '') && ($m > 99)) {
				$login_user = false;
				break;
			} else {
				$test_unicite = test_unique_login($login_user . $m);
				if ($test_unicite != 'yes') {
					if ($m == '') {
						$m = 2;
					} else {
						$m++;
					}
				} else {
					$login_user = $login_user . $m;
				}
			}
		}

		//echo "\$login_user=$login_user<br />";

		return $login_user;
	}
}

/**
 * Génére le login à partir du nom et du prénom
 *
 * Génère puis nettoie un login pour qu'il soit valide et unique
 *
 * Le mode de génération doit être passé en argument
 *
 * name             à partir du nom
 *
 * name8            à partir du nom, réduit à 8 caractères
 *
 * name9_p          à partir du nom, réduit à 9 caractères + _ + première lettre du prénom (format historique du login élève dans Gepi)
 *
 * fname8           première lettre du prénom + nom, réduit à 8 caractères
 *
 * fname19          première lettre du prénom + nom, réduit à 19 caractères
 *
 * firstdotname     prénom.nom
 *
 * firstdotname19   prénom.nom réduit à 19 caractères
 *
 * namef8           nom réduit à 7 caractères + première lettre du prénom
 *
 * lcs              première lettre du prénom + premier nom (+ _ + deuxième nom si le 1er nom fait moins de 4 caractères)
 *
 * si $_mode est NULL, fname8 est utilisé
 *
 * @param string $_nom nom de l'utilisateur
 * @param string $_prenom prénom de l'utilisateur
 * @param string $_mode Le mode de génération ou NULL
 * @param string $_casse La casse du login ('maj', 'min', '') par défaut la casse n'est pas modifiée
 * @return string|booleanLe login généré ou FALSE si on obtient un login vide
 * @see test_unique_login()
 */
function generate_unique_login_old($_nom, $_prenom, $_mode, $_casse = '') {

	if ($_mode == NULL) {
		$_mode = "fname8";
	}
	// On génère le login
	$_prenom = remplace_accents($_prenom);

	$prenoms = explode(" ", $_prenom);
	$premier_prenom = $prenoms[0];
	$prenom_compose = '';
	if (isset($prenoms[1])) {
		$prenom_compose = $prenoms[0] . "-" . $prenoms[1];
	}

	$_prenom = preg_replace("/[^a-zA-Z.\-]/", "", $_prenom);

	$_nom = remplace_accents($_nom);
	$_nom = preg_replace("/[^a-zA-Z.\-]/", "", $_nom);

	// 20220206
	$_initiale_prenom=substring($_prenom, 0, 1);

	if($_nom=='') {return FALSE;}

	if ($_mode == "name") {
		$temp1 = $_nom;
		$temp1 = preg_replace("/ /", "", $temp1);
		$temp1 = preg_replace("/-/", "_", $temp1);
		$temp1 = preg_replace("/'/", "", $temp1);
	} elseif ($_mode == "name8") {
		$temp1 = $_nom;
		$temp1 = preg_replace("/ /", "", $temp1);
		$temp1 = preg_replace("/-/", "_", $temp1);
		$temp1 = preg_replace("/'/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 8);
	} elseif ($_mode == "name9_p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '_' . mb_substr($temp2, 0, 1);
			}
		}
	} elseif ($_mode == "name9-p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '-' . mb_substr($temp2, 0, 1);
			}
		}
	} elseif ($_mode == "name9.p") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '.' . mb_substr($temp2, 0, 1);
			}
		}
	} elseif ($_mode == "p_name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 1) . "_" . $temp1;
			}
		}
	} elseif ($_mode == "p-name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 1) . "-" . $temp1;
			}
		}
	} elseif ($_mode == "p.name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 1) . "." . $temp1;
			}
		}
	} elseif ($_mode == "name9_ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '_' . mb_substr($temp2, 0, 3);
			}
		}
	} elseif ($_mode == "name9-ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '-' . mb_substr($temp2, 0, 3);
			}
		}
	} elseif ($_mode == "name9.ppp") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 .= '.' . mb_substr($temp2, 0, 3);
			}
		}
	} elseif ($_mode == "ppp_name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 3) . "_" . $temp1;
			}
		}
	} elseif ($_mode == "ppp-name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 3) . "-" . $temp1;
			}
		}
	} elseif ($_mode == "ppp.name9") {
		// Format d'origine des comptes élèves dans Gepi
		$temp1 = $_nom;
		$temp1 = preg_replace("/[ '-]/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 9);
		if ($_prenom != '') {
			$temp2 = preg_replace("/ /", "", $_prenom);
			$temp2 = preg_replace("/-/", "_", $temp2);
			$temp2 = preg_replace("/'/", "", $temp2);
			if ($temp2 != '') {
				$temp1 = mb_substr($temp2, 0, 3) . "." . $temp1;
			}
		}
	} elseif ($_mode == "fname8") {
		if($_prenom=='') {return FALSE;}
		//$temp1 = $_prenom{0} . $_nom;
		$temp1 = $_initiale_prenom . $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,8);
	} elseif ($_mode == "fname19") {
		if($_prenom=='') {return FALSE;}
		//$temp1 = $_prenom{0} . $_nom;
		$temp1 = $_initiale_prenom . $_nom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
		$temp1 = mb_substr($temp1,0,19);
	} elseif ($_mode == "firstdotname") {
		if ($_prenom == '') {
			return FALSE;
		}

		if ($prenom_compose != '') {
			$firstname = $prenom_compose;
		} else {
			$firstname = $premier_prenom;
		}

		//$temp1 = $_prenom . "." . $_nom;
		$temp1 = $firstname . "." . $_nom;

		$temp1 = preg_replace("/ /", "", $temp1);
		$temp1 = preg_replace("/-/", "_", $temp1);
		$temp1 = preg_replace("/'/", "", $temp1);
	} elseif ($_mode == "firstdotname19") {
		if ($_prenom == '') {
			return FALSE;
		}

		if ($prenom_compose != '') {
			$firstname = $prenom_compose;
		} else {
			$firstname = $premier_prenom;
		}

		//$temp1 = $_prenom . "." . $_nom;
		$temp1 = $firstname . "." . $_nom;

		$temp1 = preg_replace("/ /", "", $temp1);
		$temp1 = preg_replace("/'/", "", $temp1);
		$temp1 = mb_substr($temp1, 0, 19);
	} elseif ($_mode == "namef8") {
		if($_prenom=='') {return FALSE;}
		//$temp1 =  mb_substr($_nom,0,7) . $_prenom{0};
		$temp1 =  mb_substr($_nom,0,7) . $_initiale_prenom;
		$temp1 = preg_replace("/ /","", $temp1);
		$temp1 = preg_replace("/-/","_", $temp1);
		$temp1 = preg_replace("/'/","", $temp1);
	} elseif ($_mode == "lcs") {
		$temp1 = my_strtolower($_nom);
		if (preg_match("/\s/", $temp1)) {
			$noms = preg_split("/\s/", $temp1);
			$temp1 = $noms[0];
			if (mb_strlen($noms[0]) < 4) {
				$temp1 .= "_" . $noms[1];
			}
		}
		$temp1 = my_strtolower(mb_substr($_prenom, 0, 1)) . $temp1;
	} else {
		return FALSE;
	}

	if ($_casse == 'maj') {
		$temp1 = my_strtoupper($temp1);
	} elseif ($_casse == 'min') {
		$temp1 = my_strtolower($temp1);
	}

	$login_user = $temp1;

	// Nettoyage final
	$login_user = mb_substr($login_user, 0, 50);
	$login_user = preg_replace("/[^A-Za-z0-9._\-]/", "", trim($login_user));

	//$test1 = $login_user{0};
	$test1 = mb_substr($login_user, 0, 1);
	while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
		$login_user = mb_substr($login_user, 1);
		//$test1 = $login_user{0};
		$test1 = mb_substr($login_user, 0, 1);
	}

	//$test1 = $login_user{mb_strlen($login_user)-1};
	$test1 = mb_substr($login_user, mb_strlen($login_user) - 1, 1);
	while ($test1 == "_" OR $test1 == "-" OR $test1 == ".") {
		$login_user = mb_substr($login_user, 0, mb_strlen($login_user)-1);
		//$test1 = $login_user{mb_strlen($login_user)-1};
		$test1 = mb_substr($login_user, mb_strlen($login_user) - 1, 1);
	}

	// On teste l'unicité du login que l'on vient de créer
	$m = '';
	$test_unicite = 'no';
	while ($test_unicite != 'yes') {
		$test_unicite = test_unique_login($login_user . $m);
		if ($test_unicite != 'yes') {
			if ($m == '') {
				$m = 2;
			} else {
				$m++;
			}
		} else {
			$login_user = $login_user . $m;
		}
	}

	return $login_user;
}

/**
 * Fonction qui propose l'ordre d'affichage du nom, prénom et de la civilité en fonction des réglages de la classe de l'élève
 *
 * @param string $login login de l'utilisateur
 * @param integer $id_classe Id de la classe
 * @param string $format Format d'affichage (par défaut, c'est le paramétrage lié à la classe)
 * @return string nom, prénom, civilité formaté
 */
function affiche_utilisateur($login, $id_classe, $format = "") {
	global $mysqli;
	$sql = "select nom, prenom, civilite from utilisateurs where login = '" . $login . "';";
	//echo "$sql<br />";

	$resultat = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($resultat) == 0) {
		$result = $login;
	} else {
		$obj = $resultat->fetch_object();
		$nom = $obj->nom;
		$prenom = $obj->prenom;
		$civilite = $obj->civilite;
		$resultat->close();

		if (($format == "") || (!in_array($format, array('np', 'pn', 'in', 'ni', 'cnp', 'cpn', 'cin', 'cni', 'cn')))) {
			$sql_format = "select format_nom from classes where id = '" . $id_classe . "'";
			$resultat_format = mysqli_query($mysqli, $sql_format);
			if ($resultat_format->num_rows > 0) {
				$obj_format = $resultat_format->fetch_object();
				$format = $obj_format->format_nom;
				//$result = "";
				$resultat_format->close();
			} else {
				$format = "";
			}
		}

		$i = '';
		if ((($format == 'ni') or ($format == 'in') or ($format == 'cni') or ($format == 'cin'))
			and ($prenom != '')) {
			$temp = explode("-", $prenom);
			$i = mb_substr($temp[0], 0, 1);
			if (isset($temp[1]) and ($temp[1] != '')) $i .= "-" . mb_substr($temp[1], 0, 1);
			$i .= ". ";
		}

		$result = "";
		switch ($format) {
			case 'np':
				$result = $nom . " " . $prenom;
				break;
			case 'pn':
				$result = $prenom . " " . $nom;
				break;
			case 'in':
				$result = $i . $nom;
				break;
			case 'ni':
				$result = $nom . " " . $i;
				break;
			case 'cnp':
				if ($civilite != '') $result = $civilite . " ";
				$result .= $nom . " " . $prenom;
				break;
			case 'cpn':
				if ($civilite != '') $result = $civilite . " ";
				$result .= $prenom . " " . $nom;
				break;
			case 'cin':
				if ($civilite != '') $result = $civilite . " ";
				$result .= $i . $nom;
				break;
			case 'cni':
				if ($civilite != '') $result = $civilite . " ";
				$result .= $nom . " " . $i;
				break;
			case 'cn':
				if ($civilite != '') $result = $civilite . " ";
				$result .= $nom;
				break;
			default:
				$result = $nom . " " . $prenom;
		}
	}

	return $result;
}

/**
 * Fonction qui propose l'ordre d'affichage du nom, prénom de l'élève en fonction des réglages de la classe de l'élève
 *
 * @param string $nom nom de l'élève
 * @param string $prenom prénom de l'élève
 * @param integer $id_classe Id de la classe
 * @return string nom, prénom formaté
 */
function affiche_eleve($nom, $prenom, $id_classe) {
	global $mysqli;
	$sql_format = "select format_nom_eleve from classes where id = '" . $id_classe . "'";
	$resultat_format = mysqli_query($mysqli, $sql_format);
	// Dans le cas d'une mise à jour oubliée, le champ format_nom_eleve n'existe pas.
	if ((is_object($resultat_format)) && ($resultat_format->num_rows > 0)) {
		$obj_format = $resultat_format->fetch_object();
		$format = $obj_format->format_nom_eleve;
		$resultat_format->close();
	} else {
		$format = "";
	}

	switch ($format) {
		case 'np':
			$result = $nom . " " . $prenom;
			break;
		case 'pn':
			$result = $prenom . " " . $nom;
			break;
		default:
			$result = $nom . " " . $prenom;
	}
	return $result;
}

/**
 * Verifie si l'extension d_base est active
 *
 * Affiche une page d'avertissement si le module dbase n'est pas actif
 *
 */
function verif_active_dbase() {
	if (!function_exists("dbase_open")) {
		echo "<center><p class=grand>ATTENTION : PHP n'est pas configuré pour gérer les fichiers GEP (dbf).
        <br />L'extension d_base n'est pas active. Adressez-vous à l'administrateur du serveur pour corriger le problème.</p></center></body></html>";
		die();
	}
}

/**
 * Ecrit une balise <select> de date jour mois année
 * correction W3C : ajout de la balise de fin </option> à la fin de $out_html
 * Création d'un label pour passer les tests WAI
 *
 * @param string $prefix l'attribut name sera de la forme $prefixday, $prefixMois,...
 * @param integer $day
 * @param integer $month
 * @param integer $year
 * @param string $option Si = more_years, on ajoute +5 et -5 années aux années possibles
 * @see getSettingValue()
 */
function genDateSelector($prefix, $day, $month, $year, $option) {
	if ($day == 0) $day = date("d");
	if ($month == 0) $month = date("m");
	if ($year == 0) $year = date("Y");

	echo "\n<label for=\"${prefix}jour\"><span style='display:none;'>Jour</span></label>\n";
	echo "<select id=\"${prefix}jour\" name=\"${prefix}day\">\n";

	for ($i = 1; $i <= 31; $i++)
		echo "<option value = \"$i\"" . ($i == $day ? " selected=\"selected\"" : "") . ">$i</option>\n";

	echo "</select>\n";

	echo "\n<label for=\"${prefix}mois\"><span style='display:none;'>Mois</span></label>\n";
	echo "<select id=\"${prefix}mois\" name=\"${prefix}month\">\n";

	for ($i = 1; $i <= 12; $i++) {
		$m = french_strftime("%b", mktime(0, 0, 0, $i, 1, $year));

		echo "<option value=\"$i\"" . ($i == $month ? " selected=\"selected\"" : "") . ">$m</option>\n";
	}

	echo "</select>\n";

	echo "\n<label for=\"${prefix}annee\"><span style='display:none;'>Année</span></label>\n";
	echo "<select id=\"${prefix}annee\" name=\"${prefix}year\">\n";

	$min = strftime("%Y", getSettingValue("begin_bookings"));
	if ($option == "more_years") $min = date("Y") - 5;

	$max = strftime("%Y", getSettingValue("end_bookings"));
	if ($option == "more_years") $max = date("Y") + 5;

	for ($i = $min; $i <= $max; $i++)
		print "<option" . ($i == $year ? " selected=\"selected\"" : "") . ">$i</option>\n";

	echo "</select>\n";
}

/**
 * Vérifie que la page est bien accessible par l'utilisateur
 *
 * @return boolean TRUE si la page est accessible, FALSE sinon
 * @global string
 * @see tentative_intrusion()
 */
function checkAccess() {
	global $gepiPath;
	global $mysqli;

	if (!preg_match("/mon_compte.php/", $_SERVER['SCRIPT_NAME'])) {
		if ((isset($_SESSION['statut'])) && ($_SESSION['statut'] != "administrateur") && (getSettingAOui('MailValideRequis' . ucfirst($_SESSION['statut'])))) {

			$debug_test_mail = "n";
			if ($debug_test_mail == "y") {
				$f = fopen("/tmp/debug_check_mail.txt", "a+");
				fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " checkAccess(): depuis " . $_SERVER['SCRIPT_NAME'] . "\n");
				fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " checkAccess(): Avant le test check_mail().\n");
				fclose($f);
			}

			$ping_host = getSettingValue('ping_host');
			if ($ping_host == "") {
				//$ping_host="www.google.fr";
				$ping_host = "173.194.40.183";
			}

			$redir_saisie_mail_requise = "n";
			//if((!isset($_SESSION['email']))||(!check_mail($_SESSION['email']))) {
			if (!isset($_SESSION['email'])) {
				$redir_saisie_mail_requise = "y";
				if ($debug_test_mail == "y") {
					$f = fopen("/tmp/debug_check_mail.txt", "a+");
					fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " \$_SESSION['email'] est vide.\n");
					fclose($f);
				}
			} elseif ((getSettingAOui('MailValideRequisCheckDNS')) && (ping($ping_host, 80, 3) != "down")) {
				if ($debug_test_mail == "y") {
					$f = fopen("/tmp/debug_check_mail.txt", "a+");
					fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " Avant le test checkdnsrr...\n");
					fclose($f);
				}
				if (!check_mail($_SESSION['email'], 'checkdnsrr', 'y')) {
					$redir_saisie_mail_requise = "y";
					if ($debug_test_mail == "y") {
						$f = fopen("/tmp/debug_check_mail.txt", "a+");
						fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " Le test checkdnsrr a échoué.\n");
						fclose($f);
					}
				}
			} elseif (!check_mail($_SESSION['email'])) {
				if ($debug_test_mail == "y") {
					$f = fopen("/tmp/debug_check_mail.txt", "a+");
					fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " Le check_mail() a échoué.\n");
					fclose($f);
				}

				$redir_saisie_mail_requise = "y";
			}

			if ($redir_saisie_mail_requise == "y") {
				if ($debug_test_mail == "y") {
					$f = fopen("/tmp/debug_check_mail.txt", "a+");
					if (!isset($_SESSION['email'])) {
						fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " checkAccess(): Après le test check_mail() qui n'a pas été effectué : \$_SESSION['email'] n'est pas initialisé.\n");
					} else {
						fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " checkAccess(): Après le test check_mail() qui a échoué sur '" . $_SESSION['email'] . "'.\n");
					}
					fclose($f);
				}

				header("Location: $gepiPath/utilisateurs/mon_compte.php?saisie_mail_requise=yes");
				//getSettingValue('sso_url_portail')
				die();
			}
		}
	}

	$url = parse_url($_SERVER['SCRIPT_NAME']);

	if (mb_substr($url['path'], 0, mb_strlen($gepiPath)) != $gepiPath) {
		tentative_intrusion(2, "Tentative d'accès avec modification sauvage de gepiPath");
		return (FALSE);
	} else {
		if ($_SESSION["statut"] == 'autre') {
			$sql = "SELECT autorisation
					FROM droits_speciaux
					WHERE nom_fichier = '" . mb_substr($url['path'], mb_strlen($gepiPath)) . "'
					AND id_statut = '" . $_SESSION['statut_special_id'] . "'
					AND autorisation='V'";
		} else {
			$sql = "SELECT " . $_SESSION['statut'] . " AS autorisation
					FROM droits
					WHERE id = '" . mb_substr($url['path'], mb_strlen($gepiPath)) . "'
					AND " . $_SESSION['statut'] . "='V';";
		}

		$resultat = mysqli_query($mysqli, $sql);
		$nb_lignes = $resultat->num_rows;
		$resultat->close();

		if ($nb_lignes > 0) {
			return (TRUE);
		} else {
			tentative_intrusion(1, "Tentative d'accès à un fichier sans avoir les droits nécessaires");
			return (FALSE);
		}
	}
}

/**
 * Recherche dans la base l'adresse courriel d'un utilisateur
 *
 * @param string $login_u Login de l'utilisateur
 * @return string adresse courriel de l'utilisateur
 */
function retourne_email($login_u) {
	global $mysqli;
	$sql_call = "SELECT email FROM utilisateurs WHERE login = '$login_u'";

	$resultat = mysqli_query($mysqli, $sql_call);
	$obj_call = $resultat->fetch_object();
	$email = $obj_call->email;
	$resultat->close();

	return $email;
}

/**
 * Renvoie une chaine débarassée de l'encodage ASCII
 *
 * @param string $s le texte à convertir
 * @return string le texte avec les lettres accentuées
 */
function dbase_filter($s) {
	for ($i = 0; $i < mb_strlen($s); $i++) {
		$code = ord($s[$i]);
		switch ($code) {
			case 129:
				$s[$i] = "ü";
				break;
			case 130:
				$s[$i] = "é";
				break;
			case 131:
				$s[$i] = "â";
				break;
			case 132:
				$s[$i] = "ä";
				break;
			case 133:
				$s[$i] = "à";
				break;
			case 135:
				$s[$i] = "ç";
				break;
			case 136:
				$s[$i] = "ê";
				break;
			case 137:
				$s[$i] = "ë";
				break;
			case 138:
				$s[$i] = "è";
				break;
			case 139:
				$s[$i] = "ï";
				break;
			case 140:
				$s[$i] = "î";
				break;
			case 147:
				$s[$i] = "ô";
				break;
			case 148:
				$s[$i] = "ö";
				break;
			case 150:
				$s[$i] = "û";
				break;
			case 151:
				$s[$i] = "ù";
				break;
		}
	}

	return $s;
}

/**
 * Renvoie le navigateur et sa version
 *
 * @param string $HTTP_USER_AGENT
 * @return string navigateur - version
 */
function detect_browser($HTTP_USER_AGENT) {
	// D'après le fichier db_details_common.php de phpmyadmin
	/*
	$f=fopen("/tmp/detect_browser.txt","a+");
	fwrite($f,date("d/m/Y His").": $HTTP_USER_AGENT\n");
	fclose($f);
	*/
	include_once(dirname(__FILE__) . '/HTMLPurifier.standalone.php');
	$config = HTMLPurifier_Config::createDefault();
	$config->set('Core.Encoding', 'utf-8'); // replace with your encoding
	$config->set('HTML.Doctype', 'XHTML 1.0 Strict'); // replace with your doctype
	$purifier = new HTMLPurifier($config);


	if (function_exists('preg_match')) {
		if (preg_match('/Opera(\/| )([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif (preg_match('/OmniWeb\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif (preg_match('/(Konqueror\/)(.*)(;)/', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif (preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version)) {
			if (preg_match('/Chrome\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'GoogleChrome';
			} elseif (preg_match('/Safari\/([0-9]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
				$BROWSER_AGENT = 'SAFARI';
			} elseif (preg_match('/Firefox\/([0-9.]*)/', $HTTP_USER_AGENT, $log_version2)) {
				$BROWSER_VER = $log_version2[1];
				$BROWSER_AGENT = 'Firefox';
			} else {
				$BROWSER_VER = $log_version[1];
				$BROWSER_AGENT = 'MOZILLA';
			}
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	} elseif (function_exists('mb_ereg')) {
		if (mb_ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif (mb_ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif (mb_ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif (mb_ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif ((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (mb_ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif ((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (mb_ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif ((mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (mb_ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} elseif (mb_ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	} elseif (function_exists('ereg')) {
		if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'OPERA';
		} elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'Internet Explorer';
		} elseif (ereg('OmniWeb/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'OMNIWEB';
		} elseif (ereg('(Konqueror/)(.*)(;)', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[2];
			$BROWSER_AGENT = 'KONQUEROR';
		} elseif ((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (ereg('GoogleChrome/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'GoogleChrome';
		} elseif ((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (ereg('Safari/([0-9]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version[1] . '.' . $log_version2[1];
			$BROWSER_AGENT = 'SAFARI';
		} elseif (ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) {
			$BROWSER_VER = $log_version[1];
			$BROWSER_AGENT = 'MOZILLA';
		} elseif ((ereg('Mozilla/([0-9].[0-9]{1,2})', $HTTP_USER_AGENT, $log_version)) && (ereg('Firefox/([0-9.]*)', $HTTP_USER_AGENT, $log_version2))) {
			$BROWSER_VER = $log_version2[1];
			$BROWSER_AGENT = 'Firefox';
		} else {
			$BROWSER_VER = '';
			$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
		}
	} else {
		$BROWSER_VER = '';
		$BROWSER_AGENT = $purifier->purify($HTTP_USER_AGENT);
	}
	return $BROWSER_AGENT . " - " . $BROWSER_VER;
}

/**
 * Formate une date en jour/mois/année
 *
 * Accepte les dates aux formats YYYY-MM-DD ou YYYYMMDD ou YYYY-MM-DD xx:xx:xx
 *
 * Retourne la date passée en argument si le format n'est pas bon
 *
 * @param date $date La date à formater
 * @return string la date formatée
 */
function affiche_date_naissance($date) {
	if (mb_strlen($date) == 10) {
		// YYYY-MM-DD
		$annee = mb_substr($date, 0, 4);
		$mois = mb_substr($date, 5, 2);
		$jour = mb_substr($date, 8, 2);
	} elseif (mb_strlen($date) == 8) {
		// YYYYMMDD
		$annee = mb_substr($date, 0, 4);
		$mois = mb_substr($date, 4, 2);
		$jour = mb_substr($date, 6, 2);
	} elseif (mb_strlen($date) == 19) {
		// YYYY-MM-DD xx:xx:xx
		$annee = mb_substr($date, 0, 4);
		$mois = mb_substr($date, 5, 2);
		$jour = mb_substr($date, 8, 2);
	} else {
		// Format inconnu
		return ($date);
	}
	return $jour . "/" . $mois . "/" . $annee;
}

/**
 *
 * @return boolean TRUE si on a une nouvelle version
 * @global mixed
 * @global mixed
 * @global mixed
 */
function test_maj() {
	global $gepiVersion;
	$version_old = getSettingValue("version");

	if ($version_old == '') {
		return TRUE;
	}
	if ($gepiVersion > $version_old) {
		// On a une nouvelle version stable
		return TRUE;
	}
	return FALSE;
}

/**
 * Recherche si la mise à jour est à faire
 *
 * @param mixed $num le numéro de version
 * @return bool TRUE s'il faut faire la mise à jour
 */
function quelle_maj($num) {
	return (getSettingValue("version") < $num);
}

/**
 *
 * @return bool TRUE si tout c'est bien passé
 * @global text
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_backup_directory() {
	global $multisite;

	$pref_multi = "";
	if (($multisite == 'y') && (isset($_COOKIE['RNE']))) {
		$pref_multi = $_COOKIE['RNE'] . "_";
	}

	$current_backup_dir = getSettingValue("backup_directory");
	if ($current_backup_dir == NULL) {
		$current_backup_dir = "no_folder";
	}
	if (!file_exists("./backup/" . $current_backup_dir)) {
		$backupDirName = NULL;
		if ($multisite != 'y') {
			// On regarde d'abord si le répertoire de backup n'existerait pas déjà...
			$handle = opendir('./backup');

			while ($file = readdir($handle)) {
				if (mb_strlen($file) > 34 and is_dir('./backup/' . $file)) $backupDirName = $file;
			}

			closedir($handle);
		}

		if ($backupDirName != NULL) {
			// Il existe : on met simplement à jour le nom du répertoire...
			$update = saveSetting("backup_directory", $backupDirName);
		} else {
			// Il n'existe pas
			// On crée le répertoire de backup
			$length = rand(35, 45);
			for ($len = $length, $r = ''; mb_strlen($r) < $len; $r .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122)))) ;
			$dirname = $pref_multi . $r;
			$create = mkdir("./backup/" . $dirname, 0700);
			//copy("./backup/index.html","./backup/".$dirname."/index.html");
			if ($create) {
				$f = fopen("./backup/" . $dirname . "/index.html", "w+");
				fwrite($f, '<script type="text/javascript">document.location.replace("../../login.php");</script>');
				fclose($f);

				saveSetting("backup_directory", $dirname);
				saveSetting("backupdir_lastchange", time());
			} else {
				return FALSE;
			}

			// On déplace les éventuels fichiers .sql dans ce nouveau répertoire

			$handle = opendir('./backup');
			$tab_file = array();
			$n = 0;
			while ($file = readdir($handle)) {
				if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
					and (preg_match('/sql$/', $file)) and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html')) {
					$tab_file[] = $file;
					$n++;
				}
			}
			closedir($handle);
			foreach ($tab_file as $filename) {
				rename("backup/" . $filename, "backup/" . $dirname . "/" . $filename);
			}
		}
	}

	// On vérifie la date du dernier changement, et on change le nom
	// du répertoire si le dernier changement a eu lieu il y a plus de 48h
	$lastchange = getSettingValue("backupdir_lastchange");
	$current_time = time();

	// Si le dernier changement a eu lieu il y a plus de 48h, on change le nom du répertoire
	if ($current_time - $lastchange > 172800) {
		$dirname = getSettingValue("backup_directory");
		$length = rand(35, 45);
		for ($len = $length, $r = ''; mb_strlen($r) < $len; $r .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122)))) ;
		$newdirname = $pref_multi . $r;
		if (rename("./backup/" . $dirname, "./backup/" . $newdirname)) {
			// Correction du contenu de l'index.html (bug sur le chemin relatif de la redir à une époque)
			$f = fopen("./backup/" . $newdirname . "/index.html", "w+");
			fwrite($f, '<script type="text/javascript">document.location.replace("../../login.php");</script>');
			fclose($f);

			saveSetting("backup_directory", $newdirname);
			saveSetting("backupdir_lastchange", time());
			return TRUE;
		} else {
			echo "Erreur lors du renommage du dossier de sauvegarde.<br />";
			return FALSE;
		}
	}

	return TRUE;
}

/**
 * Fonction qui retourne le nombre de périodes pour une classe
 *
 * @param int identifiant numérique de la classe
 * @return int Nombre de periodes définies pour cette classe
 */
function get_period_number($_id_classe) {
	global $mysqli;

	//$sql_periode = "SELECT count(*) FROM periodes WHERE id_classe = '" . $_id_classe . "'";
	$sql_periode = "SELECT MAX(num_periode) AS max_per FROM periodes WHERE id_classe = '" . $_id_classe . "'";
	//echo "$sql_periode<br />";
	$resultat = mysqli_query($mysqli, $sql_periode);
	if (mysqli_num_rows($resultat) == 0) {
		$nb_periode = 0;
	} else {
		//$nb_periode = $resultat->num_rows;
		$lig_per = mysqli_fetch_object($resultat);
		$nb_periode = $lig_per->max_per;
		$resultat->close();
	}

	return $nb_periode;
}

/**
 * Renvoie le numéro et le nom de la première période active (non verrouillée en saisie) pour une classe
 *
 * @param int $_id_classe identifiant unique de la classe
 * @return array numéro de la période 'num' et son nom 'nom'
 */
function get_periode_active($_id_classe) {
	global $mysqli;
	$sql_periode = "SELECT num_periode, nom_periode FROM periodes WHERE id_classe = '" . $_id_classe . "' AND verouiller = 'N'";

	$periode_query = mysqli_query($mysqli, $sql_periode);
	if (mysqli_num_rows($periode_query) > 0) {
		$reponse = $periode_query->fetch_array();
		$retour = array('num_periode' => $reponse["num_periode"], 'nom' => $reponse["nom_periode"]);
	} else {
		$retour = array('num_periode' => "?", 'nom' => "Période non trouvée");
	}

	return $retour;
}

/**
 * Cette fonction est à appeler dans tous les cas où une tentative
 * d'utilisation illégale de Gepi est manifestement avérée.
 * Elle est à appeler notamment dans tous les tests de sécurité lorsqu'un test est négatif.
 * Possibilité d'envoyer un mail à l'administrateur et de bloquer l'utilisateur
 *
 * @param integer $_niveau Niveau d'intrusion enregistré
 * @param string $_description Message enregistré pour cette tentative
 * @param string $_login_a_enregistrer Login à enregistrer bien que la session ne soit pas ouverte
 * (cas du verrouillage de compte pour erreur de mot de passe)
 * Il convient de vérifier avant de passer ce paramètre, que le compte existe.
 * @global string
 * @see getSettingValue()
 * @see mail()
 */
function tentative_intrusion($_niveau, $_description, $_login_a_enregistrer = "") {
	global $mysqli;
	global $gepiPath;

	// On commence par enregistrer la tentative en question
	if (isset($_SESSION['login'])) {
		$user_login = $_SESSION['login'];
	} elseif ($_login_a_enregistrer != "") {
		$user_login = $_login_a_enregistrer;
	} else {
		// Ici, ça veut dire que l'attaque est extérieure. Il n'y a pas d'utilisateur logué.
		$user_login = "-";
	}
	$adresse_ip = $_SERVER['REMOTE_ADDR'];
	$date = strftime("%Y-%m-%d %H:%M:%S");
	$url = parse_url($_SERVER['REQUEST_URI']);
	$fichier = mb_substr($url['path'], mb_strlen($gepiPath));
	$sql = "INSERT INTO tentatives_intrusion SET 
                login = '" . $user_login . "', 
                adresse_ip = '" . $adresse_ip . "', 
                date = '" . $date . "', 
                niveau = '" . (int)$_niveau . "', 
                fichier = '" . $fichier . "', 
                description = '" . addslashes($_description) . "', 
                statut = 'new'";

	$res = mysqli_query($mysqli, $sql);


	// On a enregistré.

	// On initialise des marqueurs pour les deux actions possibles : envoie d'un email à l'admin
	// et blocage du compte de l'utilisateur

	$send_email = FALSE;
	$block_user = FALSE;

	// Est-ce qu'on envoie un mail quoi qu'il arrive ?
	if (getSettingValue("security_alert_email_admin") == "yes" and $_niveau >= getSettingValue("security_alert_email_min_level")) {
		$send_email = TRUE;
	}

	// Si la tentative d'intrusion a été effectuée par un utilisateur connecté à Gepi,
	// on regarde si des seuils ont été dépassés et si certaines actions doivent être
	// effectuées.

	if ($user_login != "-") {
		// On récupère quelques infos
		$sql = "SELECT nom, prenom, statut, niveau_alerte, observation_securite FROM utilisateurs WHERE (login = '" . $user_login . "')";

		$result = mysqli_query($mysqli, $sql);
		$user = $result->fetch_object();
		$result->close();

		// On va utiliser ça pour générer automatiquement les noms de settings, ça fait du code en moins...
		if ($user->observation_securite == "1") {
			$obs = "probation";
		} else {
			$obs = "normal";
		}

		// D'abord, on met à jour le niveau cumulé
		$nouveau_cumul = (int)$user->niveau_alerte + (int)$_niveau;

		$sql = "UPDATE utilisateurs SET niveau_alerte = '" . $nouveau_cumul . "' WHERE (login = '" . $user_login . "')";

		$res = mysqli_query($mysqli, $sql);

		$seuil1 = FALSE;
		$seuil2 = FALSE;
		// Maintenant on regarde les seuils.
		if ($nouveau_cumul >= getSettingValue("security_alert1_" . $obs . "_cumulated_level")
			and $nouveau_cumul < getSettingValue("security_alert2_" . $obs . "_cumulated_level")) {
			// Seuil 1
			if (getSettingValue("security_alert1_" . $obs . "_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert1_" . $obs . "_block_user") == "yes") $block_user = TRUE;
			$seuil1 = TRUE;

		} elseif ($nouveau_cumul >= getSettingValue("security_alert2_" . $obs . "_cumulated_level")) {
			// Seuil 2
			if (getSettingValue("security_alert2_" . $obs . "_email_admin") == "yes") $send_email = TRUE;
			if (getSettingValue("security_alert2_" . $obs . "_block_user") == "yes") $block_user = TRUE;
			$seuil2 = TRUE;
		}

		// On désactive le compte de l'utilisateur si nécessaire :
		if ($block_user) {
			$sql = "UPDATE utilisateurs SET etat = 'inactif' WHERE (login = '" . $user_login . "')";
			$res = mysqli_query($mysqli, $sql);
		}
	} // Fin : if ($user_login != "-")

	// On envoie un email à l'administrateur si nécessaire
	if ($send_email) {
		$message = "** Alerte automatique sécurité Gepi **\n\n";
		$message .= "Une nouvelle tentative d'intrusion a été détectée par Gepi. Les détails suivants ont été enregistrés dans la base de données :\n\n";
		$message .= "Date : " . $date . "\n";
		$message .= "Fichier visé : " . $fichier . "\n";
		if (isset($_SERVER['HTTP_REFERER'])) {
			$message .= "Url d'origine : " . $_SERVER['HTTP_REFERER'] . "\n";
		}
		$message .= "Niveau de gravité : " . $_niveau . "\n";
		$message .= "Description : " . $_description . "\n\n";
		if ($user_login == "-") {
			$message .= "La tentative d'intrusion a été effectuée par un utilisateur non connecté à Gepi.\n";
			$message .= "Adresse IP : " . $adresse_ip . "\n";
		} else {
			$message .= "Informations sur l'utilisateur :\n";
			$message .= "Login : " . $user_login . "\n";
			$message .= "Nom : " . $user->prenom . " " . $user->nom . "\n";
			$message .= "Statut : " . $user->statut . "\n";
			$message .= "Score cumulé : " . $nouveau_cumul . "\n\n";
			if ($seuil1) $message .= "L'utilisateur a dépassé le seuil d'alerte 1.\n\n";
			if ($seuil2) $message .= "L'utilisateur a dépassé le seuil d'alerte 2.\n\n";
			if ($block_user) $message .= "Le compte de l'utilisateur a été désactivé.\n";
		}

		$gepiPrefixeSujetMail = getSettingValue("gepiPrefixeSujetMail") ? getSettingValue("gepiPrefixeSujetMail") : "";
		if ($gepiPrefixeSujetMail != '') {
			$gepiPrefixeSujetMail .= " ";
		}

		$subject = $gepiPrefixeSujetMail . "GEPI : Alerte sécurité -- Tentative d'intrusion";
		$subject = "=?UTF-8?B?" . base64_encode($subject) . "?=\r\n";

		$headers = "X-Mailer: PHP/" . phpversion() . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
		$headers .= "From: Mail automatique Gepi <ne-pas-repondre@" . $_SERVER['SERVER_NAME'] . ">\r\n";

		// On envoie le mail
		$envoi = mail(getSettingValue("gepiAdminAdress"),
			$subject,
			$message,
			$headers);
	}
}

/**
 * Fonction destinée à créer un dossier temporaire aléatoire /temp/<alea>
 *
 * Test le dossier en écriture et le crée au besoin
 *
 * @return bool TRUE si tout c'est bien passé
 * @see getSettingValue()
 * @see saveSetting()
 */
function check_temp_directory() {
	$dirname = getSettingValue("temp_directory");
	if (($dirname == '') || (!file_exists("./temp/$dirname"))) {
		// Il n'existe pas
		// On créé le répertoire temp
		$length = rand(35, 45);
		for ($len = $length, $r = ''; mb_strlen($r) < $len; $r .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122)))) ;
		$dirname = $r;
		$create = mkdir("./temp/" . $dirname, 0700);

		if ($create) {
			$fich = fopen("./temp/" . $dirname . "/index.html", "w+");
			fwrite($fich, '<html><head><script type="text/javascript">
    document.location.replace("../../login.php")
</script></head></html>
');
			fclose($fich);

			saveSetting("temp_directory", $dirname);
			return TRUE;
		} else {
			return FALSE;
			die();
		}
	} else {
		return TRUE;
	}
}

/**
 * Fonction destinée à créer un dossier /temp/<alea> propre au professeur
 *
 * Test le dossier en écriture et le crée au besoin
 * La fonction est appelée depuis la racine de l'arborescence GEPI (sinon ça peut bugger)
 *
 * @param string $login_user Le login de l'utilisateur (si vide, on utilise $_SESSION['login'])
 *
 * @return bool TRUE si tout c'est bien passé
 */
function check_user_temp_directory($login_user = "", $_niveau_arbo = 0) {
	global $multisite;
	global $mysqli;

	$pref_arbo = ".";
	if ($_niveau_arbo == 1) {
		$pref_arbo = "..";
	} elseif ($_niveau_arbo == 2) {
		$pref_arbo = "../..";
	}

	if ($login_user == "") {
		$login_user = $_SESSION['login'];
	}

	$pref_multi = "";
	if (($multisite == 'y') && (isset($_COOKIE['RNE']))) {
		$pref_multi = $_COOKIE['RNE'] . "_";
	}

	$sql = "SELECT temp_dir FROM utilisateurs WHERE login='" . $login_user . "'";
	$res_temp_dir = mysqli_query($mysqli, $sql);
	if ($res_temp_dir->num_rows == 0) {
		// Cela revient à dire que l'utilisateur n'est pas dans la table utilisateurs???
		return FALSE;
	} else {
		$lig_temp_dir = $res_temp_dir->fetch_object();
		$dirname = $lig_temp_dir->temp_dir;

		if ($dirname == "") {
			// Le dossier n'existe pas
			// On créé le répertoire temp
			$length = rand(35, 45);
			for ($len = $length, $r = ''; mb_strlen($r) < $len; $r .= chr(!mt_rand(0, 2) ? mt_rand(48, 57) : (!mt_rand(0, 1) ? mt_rand(65, 90) : mt_rand(97, 122)))) ;
			$dirname = $pref_multi . $login_user . "_" . $r;
			$create = mkdir($pref_arbo . "/temp/" . $dirname, 0700);

			if ($create) {
				$fich = fopen($pref_arbo . "/temp/" . $dirname . "/index.html", "w+");
				fwrite($fich, '<html><head><script type="text/javascript">
	document.location.replace("' . $pref_arbo . '/login.php")
</script></head></html>
');
				fclose($fich);

				$sql = "UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='" . $login_user . "'";
				$res_update = mysqli_query($mysqli, $sql);
				if ($res_update) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		} else {
			if (($pref_multi != '') && (!preg_match("/^$pref_multi/", $dirname)) && (file_exists("$pref_arbo/temp/" . $dirname))) {
				// Il faut renommer le dossier
				if (!rename("$pref_arbo/temp/" . $dirname, "$pref_arbo/temp/" . $pref_multi . $dirname)) {
					return FALSE;
				} else {
					$dirname = $pref_multi . $dirname;
					$sql = "UPDATE utilisateurs SET temp_dir='$dirname' WHERE login='" . $login_user . "'";
					$res_update = mysqli_query($mysqli, $sql);
					if (!$res_update) {
						return FALSE;
					}
				}
			}

			if (!file_exists("$pref_arbo/temp/" . $dirname)) {
				// Le dossier n'existe pas
				// On créé le répertoire temp
				$create = mkdir("$pref_arbo/temp/" . $dirname, 0700);

				if ($create) {
					$fich = fopen("$pref_arbo/temp/" . $dirname . "/index.html", "w+");
					fwrite($fich, '<html><head><script type="text/javascript">
	document.location.replace("' . $pref_arbo . '/login.php")
</script></head></html>
');
					fclose($fich);
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				$fich = fopen("$pref_arbo/temp/" . $dirname . "/test_ecriture.tmp", "w+");
				$ecriture = fwrite($fich, 'Test d écriture.');
				$fermeture = fclose($fich);
				if (file_exists("$pref_arbo/temp/" . $dirname . "/test_ecriture.tmp")) {
					unlink("$pref_arbo/temp/" . $dirname . "/test_ecriture.tmp");
				}

				if (($fich) && ($ecriture) && ($fermeture)) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		}
	}
	$res_temp_dir->close(); //28-05-2021 - Romain Neil: mon IDE m'indique que ce bout de code ne peut pas être atteint ; doit-on le supprimer ? ou alors mieux réfléchir où le placer
}

/**
 * Renvoie le nom du répertoire temporaire de l'utilisateur
 *
 * @param string $login_user Le login de l'utilisateur (si vide, on utilise $_SESSION['login'])
 *
 * @return bool|string retourne FALSE s'il n'existe pas et le nom du répertoire s'il existe, sans le chemin
 */
function get_user_temp_directory($login_user = "") {
	global $mysqli;
	if ($login_user == "") {
		$login_user = $_SESSION['login'];
	}
	$sql = "SELECT temp_dir FROM utilisateurs WHERE login='" . $login_user . "'";

	$resultat = mysqli_query($mysqli, $sql);
	$nb_lignes = $resultat->num_rows;

	if ($nb_lignes > 0) {
		$lig_temp_dir = $resultat->fetch_object();

		$dirname = $lig_temp_dir->temp_dir;
		$resultat->close();

		if (($dirname != "") && (mb_strlen(preg_replace("/[A-Za-z0-9_.]/", "", $dirname)) == 0)) {
			if (file_exists("temp/" . $dirname)) {
				return $dirname;
			} elseif (file_exists("../temp/" . $dirname)) {
				return $dirname;
			} else if (file_exists("../../temp/" . $dirname)) {
				return $dirname;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}

/**
 * Retourne un nombre formaté en Mo, ko ou o suivant ça taille
 *
 * @param int $volume le nombre à formater
 * @return string le nombre formaté
 */
function volume_human($volume) {
	if ($volume >= 1048576) {
		$volume = round(10 * $volume / 1048576) / 10;
		return $volume . " Mo";
	} elseif ($volume >= 1024) {
		$volume = round(10 * $volume / 1024) / 10;
		return $volume . " ko";
	} else {
		return $volume . " o";
	}
}

/**
 * Renvoie la taille d'un répertoire
 *
 * @param string $dir Le répertoire à tester
 * @return string la taille formatée
 * @global int
 * @see volume_dir()
 * @see volume_human()
 */
function volume_dir_human($dir) {
	$volume = volume_dir($dir);
	return volume_human($volume);
}

/**
 * Additionne la taille des répertoires et sous-répertoires
 *
 * @param string $dir répertoire à parser
 * @return int la taille totale du répertoire
 * @global int
 */
function volume_dir($dir) {
	//global $totalsize;
	$totalsize = 0;

	$handle = @opendir($dir);
	while ($file = @readdir($handle)) {
		if (preg_match("/^\.{1,2}$/i", $file))
			continue;
		if (is_dir("$dir/$file")) {
			$totalsize += volume_dir("$dir/$file");
		} else {
			$tabtmpsize = stat("$dir/$file");
			$size = $tabtmpsize[7];

			$totalsize += $size;
		}
	}
	@closedir($handle);

	return ($totalsize);
}

/**
 * Supprime les fichiers d'un dossier
 *
 * @param string $dir le répertoire à vider
 * @param array $tab_exclusion tableau de fichiers ou dossiers à exclure de la suppression
 *
 * @return bool|array
 *                 TRUE si tout c'est bien passé
 *                 FALSE si un dossier a été trouvé ou si une erreur s'est produite
 *         array   si un des fichiers ou dossiers exclus de la suppression a été trouvé
 *
 * @todo En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple)
 */
function vider_dir($dir, $tab_exclusion = array()) {
	$statut = TRUE;
	$handle = @opendir($dir);
	while ($file = @readdir($handle)) {
		if (preg_match("/^\.{1,2}$/i", $file)) {
			continue;
		}

		if (in_array($file, $tab_exclusion)) {
			$fichiers_exclus_trouves[] = $file;
			continue;
		}

		if (is_dir("$dir/$file")) {
			// On ne cherche pas à vider récursivement.
			$statut = FALSE;

			echo "<!-- DOSSIER: $dir/$file -->\n";
			// En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
		} else {
			if (!unlink($dir . "/" . $file)) {
				$statut = FALSE;
				echo "<!-- Echec suppression: $dir/$file -->\n";
				break;
			}
		}
	}
	@closedir($handle);

	if (isset($fichiers_exclus_trouves)) {
		return $fichiers_exclus_trouves;
	} else {
		return $statut;
	}
}


/**
 * Additionne la taille des documents joints dans le CDT d'un groupe
 *
 * @param int $id_groupe Identifiant du groupe
 * @return int la taille totale des documents joints
 */
function volume_docs_joints($id_groupe, $mode = "all") {
	global $mysqli;
	$volume_cdt_groupe = 0;

	if ($mode == "devoirs") {
		$sql = "SELECT DISTINCT cdd.emplacement FROM ct_devoirs_documents cdd, ct_devoirs_entry cde WHERE cdd.id_ct_devoir=cde.id_ct AND cde.id_groupe='" . $id_groupe . "';";
	} elseif ($mode == "compte_rendus") {
		$sql = "SELECT DISTINCT cd.emplacement FROM ct_documents cd, ct_entry ce WHERE cd.id_ct=ce.id_ct AND ce.id_groupe='" . $id_groupe . "';";
	} else {
		$sql = "(SELECT DISTINCT cd.emplacement FROM ct_documents cd, ct_entry ce WHERE cd.id_ct=ce.id_ct AND ce.id_groupe='" . $id_groupe . "') UNION (SELECT DISTINCT cdd.emplacement FROM ct_devoirs_documents cdd, ct_devoirs_entry cde WHERE cdd.id_ct_devoir=cde.id_ct AND cde.id_groupe='" . $id_groupe . "');";
	}
	//echo "$sql<br />";

	$res_doc = mysqli_query($mysqli, $sql);
	if ($res_doc->num_rows > 0) {
		while ($lig_doc = $res_doc->fetch_object()) {
			if (file_exists($lig_doc->emplacement)) {
				$tabtmpsize = stat($lig_doc->emplacement);
				if (isset($tabtmpsize[7])) {
					$size = $tabtmpsize[7];
					$volume_cdt_groupe += $size;
				}
			}
		}
	}

	return ($volume_cdt_groupe);
}


/**
 * Cette fonction supprime le BOM éventuel d'un fichier encodé en UTF-8
 * A appeler immédiatement après ouverture du fichier
 * Exemple :
 * $handle=fopen("....");
 * skip_bom_utf8($handle)
 *
 * @param handle $h_file : Le pointeur de fichier à tester
 * @return boolean : true si pas de BOM ou si BOM sauté, false dans les autres cas
 */
function skip_bom_utf8($h_file) {
	if (ftell($h_file) != 0) return false;
	$bytes = fread($h_file, 3);
	if ($bytes === false) return false;
	if ($bytes != "\xEF\xBB\xBF") return rewind($h_file);
	return true;
}

/**
 * Cette méthode prend une chaîne de caractères et s'assure qu'elle est bien retournée en UTF-8
 * Attention, certain encodages sont très similaire et ne peuve pas être théoriquement distingué sur une chaine de caractere.
 * Si vous connaissez déjà l'encodage de votre chaine de départ, il est préférable de le préciser
 *
 * @param string $str La chaine à encoder
 * @param string $from_encoding L'encodage de départ
 * @throws Exception si la chaine n'a pas pu être encodée correctement
 * @return string La chaine en utf8
 */
function ensure_utf8($str, $from_encoding = null) {
	if ($str === null || $str === '') {
		return $str;
	} else if ($from_encoding == null && detect_utf8($str)) {
		return $str;
	}

	if ($from_encoding != null) {
		$encoding = $from_encoding;
	} else {
		$encoding = detect_encoding($str);
	}
	$result = null;
	if ($encoding !== false && $encoding != null) {
		if (function_exists('mb_convert_encoding')) {
			$result = mb_convert_encoding($str, 'UTF-8', $encoding);
		}
	}
	if ($result === null || !detect_utf8($result)) {
		throw new Exception('Impossible de convertir la chaine vers l\'utf8');
	}

	return $result;
}


/**
 * Cette méthode prend une chaîne de caractères et teste si elle ne contient que
 * de l'ASCII 7 bits ou si elle contient au moins une suite d'octets codant un
 * caractère en UTF8
 * @param string $str La chaine à tester
 * @return boolean
 */
function detect_utf8($str) {
	// Inspiré de http://w3.org/International/questions/qa-forms-utf-8.html
	//
	// on s'assure de bien opérer sur une chaîne de caractère
	$str = (string)$str;
	// La chaîne ne comporte que des octets <= 7F ?
	$full_ascii = true;
	$i = 0;
	while ($full_ascii && $i < strlen($str)) {
		$full_ascii = $full_ascii && (ord($str[$i]) <= 0x7F);
		$i++;
	}
	// Si oui c'est de l'utf8 sinon on cherche si la chaîne contient
	// au moins une suite d'octets valide en UTF8
	if ($full_ascii) return true;
	else return preg_match('#[\xC2-\xDF][\x80-\xBF]#', $str) || // non-overlong 2-byte
		preg_match('#\xE0[\xA0-\xBF][\x80-\xBF]#', $str) || // excluding overlongs
		preg_match('#[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}#', $str) || // straight 3-byte
		preg_match('#\xED[\x80-\x9F][\x80-\xBF]#', $str) | // excluding surrogates
		preg_match('#\xF0[\x90-\xBF][\x80-\xBF]{2}#', $str) || // planes 1-3
		preg_match('#[\xF1-\xF3][\x80-\xBF]{3}#', $str) || // planes 4-15
		preg_match('# \xF4[\x80-\x8F][\x80-\xBF]{2}#', $str); // plane 16
}

/**
 * Cette méthode prend une chaîne de caractères et teste si elle est bien encodée en UTF-8
 *
 * @param string $str La chaine à tester
 * @return boolean
 */
function check_utf8($str) {
	// Longueur maximale de la chaîne pour éviter un stack overflow
	// dans le test à base d'expression régulière
	$long_max = 1000;
	if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') $long_max = 300; // dans le cas de Window$
	if (mb_strlen($str) < $long_max) {
		// From http://w3.org/International/questions/qa-forms-utf-8.html
		$preg_match_result = 1 == preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $str);
	} else {
		$preg_match_result = FALSE;
	}
	if ($preg_match_result) {
		return true;
	} else {
		//le test preg renvoie faux, et on va vérifier avec d'autres fonctions
		$result = true;
		$test_done = false;
		if (function_exists('mb_check_encoding')) {
			$test_done = true;
			$result = $result && @mb_check_encoding($str, 'UTF-8');
		}

		if (function_exists('mb_detect_encoding')) {
			$test_done = true;
			$result = $result && @mb_detect_encoding($str, 'UTF-8', true);
		}
		if (function_exists('iconv')) {
			$test_done = true;
			$result = $result && ($str === (@iconv('UTF-8', 'UTF-8//IGNORE', $str)));
		}
		if (function_exists('mb_convert_encoding') && !$test_done) {
			$test_done = true;
			$result = $result && ($str === @mb_convert_encoding(@mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'));
		}
		return ($test_done && $result);
	}
}


/**
 * Cette méthode prend une chaîne de caractères et détecte son encodage
 *
 * @param string $str La chaine à tester
 * @return mixed l'encodage ou false si indétectable
 */
function detect_encoding($str) {
	//on commence par vérifier si c'est de l'utf8
	if (detect_utf8($str)) {
		return 'UTF-8';
	}

	//on va commencer par tester ces encodages
	static $encoding_list = array('UTF-8', 'ISO-8859-15', 'windows-1251');
	foreach ($encoding_list as $item) {
		if (function_exists('iconv')) {
			$sample = @iconv($item, $item, $str);
			if (md5($sample) == md5($str)) {
				return $item;
			}
		} else if (function_exists('mb_detect_encoding')) {
			if (@mb_detect_encoding($str, $item, true)) {
				return $item;
			}
		}
	}

	//la méthode précédente n'a rien donnée
	if (function_exists('mb_detect_encoding')) {
		return mb_detect_encoding($str);
	} else {
		return false;
	}
}

/**
 * Correspondances de caractères accentués/désaccentués
 *
 * @global string $GLOBALS ['liste_caracteres_accentues']
 * @name $liste_caracteres_accentues
 */
//$GLOBALS['liste_caracteres_accentues']="ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØ¦ÛÜÙÚÝ¾´áàâäãåçéèêëîïìíñôöðòóõø¨ûüùúýÿ¸";
$GLOBALS['liste_caracteres_accentues'] = "ÂÄÀÁÃÅÇÊËÈÉÎÏÌÍÑÔÖÒÓÕØÛÜÙÚÝ¾áàâäãåçéèêëîïìíñôöðòóõøûüùúýÿ";

/**
 * Correspondances de caractères accentués/désaccentués
 *
 * @global string $GLOBALS ['liste_caracteres_desaccentues']
 * @name $liste_caracteres_desaccentues
 */
//$GLOBALS['liste_caracteres_desaccentues']="AAAAAACEEEEIIIINOOOOOOSUUUUYYZaaaaaaceeeeiiiinooooooosuuuuyyz";
$GLOBALS['liste_caracteres_desaccentues'] = "AAAAAACEEEEIIIINOOOOOOUUUUYYaaaaaaceeeeiiiinooooooouuuuyy";

/**
 * Cette méthode prend une chaîne de caractères et s'assure qu'elle est bien retournée en ASCII
 * Attention, certain encodages sont très similaire et ne peuve pas être théoriquement distingué sur une chaine de caractere.
 * Si vous connaissez déjà l'encodage de votre chaine de départ, il est préférable de le préciser
 *
 * @param string $chaine La chaine à encoder
 * @param string $encoding L'encodage de départ
 * @throws \Exception
 * @return string La chaine en ascii
 */
function ensure_ascii($chaine, $encoding = '') {
	if ($chaine == null || $chaine == '') {
		return $chaine;
	}

	$chaine = ensure_utf8($chaine, $encoding);
	$str = null;
	if (function_exists('iconv')) {
		//test : est-ce que iconv est bien implémenté sur ce système ?
		$test = 'c\'est un bel ete' === iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", 'c\'est un bel été');
		if ($test) {
			//on utilise iconv pour la conversion
			$str = @iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $chaine);
		}
	}
	if ($str === null) {
		//on utilise pas iconv pour la conversion
		$translit = array('Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ç' => 'C', 'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Í' => 'I', 'Ï' => 'I', 'Î' => 'I', 'Ì' => 'I', 'Ñ' => 'N', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ä' => 'a', 'ã' => 'a', 'å' => 'a', 'ç' => 'c', 'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'õ' => 'o', 'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y');
		$str = strtr($chaine, $translit);
	}
	if (function_exists('mb_convert_encoding')) {
		$str = @mb_convert_encoding($str, 'ASCII', 'UTF-8');
	}

	return $str;
}

/**
 * Remplace les accents dans une chaine, et en fonction du paramètre 'mode' remplace les caractères non alphabétiques par des _ (underscore)
 *
 * $mode = 'all' ou mode = '' On remplace espaces et apostrophes par des '_' et les caractères accentués par leurs équivalents non accentués.
 *
 * $mode = 'all_nospace' On remplace apostrophes par des '_' et les caractères accentués par leurs équivalents non accentués.
 *
 *  Sinon, on remplace les caractères accentués par leurs équivalents non accentués.
 *
 * @param string $chaine La chaine à tester
 * @param string $mode Mode de conversion
 * @throws \Exception
 * @return array|null|string|string[]
 * @global string
 * @global string
 */
function remplace_accents($chaine, $mode = '') {
	$str = ensure_ascii($chaine);

	if ($mode == 'all') {
		return preg_replace('#[^a-zA-Z0-9\-\_]#', '_', $str); // Pour des noms de fichiers par exemple
	} elseif ($mode == 'all_nospace') {
		return preg_replace('#[^a-zA-Z0-9\-\._ ]#', '_', $str);
	} else {
		return preg_replace('#[^a-zA-Z0-9\-\._"\' ;]#', '_', $str);
	}
}

/**
 * @throws \Exception
 * @see remplace_accent($chaine,$mode='')
 **/
function enleve_accents($chaine, $mode = '') {
	return remplace_accents($chaine, $mode = '');
}

/**
 * @see remplace_accent($chaine,$mode='')
 **/
function accents_enleve($chaine, $mode = '') {
	return remplace_accents($chaine, $mode = '');
}

/**
 * Nettoyage des caractères d'un nom ou prénom
 * On ne conserve que les lettres (accentuées incluses), l'espace et le tiret
 *
 * @param string $chaine La chaine à traiter
 * @throws \Exception
 * @throws \Exception
 * @return string La chaine corrigée
 * @global string
 */
function nettoyer_caracteres_nom($chaine, $mode = "a", $chaine_autres_caracteres_acceptes = "", $caractere_remplacement = "", $remplacer_oe_ae = "n") {
	global $liste_caracteres_accentues;

	// Pour que le tiret soit à la fin si on le met dans $chaine_autres_caracteres_acceptes
	$chaine_autres_caracteres_acceptes = "ÆæŒœ" . $chaine_autres_caracteres_acceptes;

	if (is_numeric(trim($chaine))) {
		$retour = trim($chaine);
	} else {
		$retour = trim(ensure_utf8($chaine));
	}

	if ($remplacer_oe_ae == "y") {
		$retour = preg_replace("#Æ#u", "AE", preg_replace("#æ#u", "ae", preg_replace("#Œ#u", "OE", preg_replace("#œ#u", "oe", $retour))));
	}

	// Le /u sur les preg_replace permet de traiter correctement des chaines utf8
	if ($mode == 'a') {

		$retour = preg_replace("#[^A-Za-z" . $liste_caracteres_accentues . $chaine_autres_caracteres_acceptes . "]#u", "$caractere_remplacement", $retour);

	} elseif ($mode == 'an') {
		$retour = preg_replace("#[^A-Za-z0-9" . $liste_caracteres_accentues . $chaine_autres_caracteres_acceptes . "]#u", "$caractere_remplacement", $retour);

	}

	return $retour;
}


/**
 * fonction qui renvoie la liste des classes d'un élève par période
 *
 * @param string $ele_login login de l'élève
 * @return array Tableau des classes en fonction des périodes
 */
function get_class_periode_from_ele_login($ele_login) {
	global $mysqli;

	$sql = "SELECT DISTINCT jec.id_classe, c.classe, jec.periode, p.nom_periode FROM j_eleves_classes jec, classes c, periodes p WHERE p.id_classe=c.id AND jec.periode=p.num_periode AND jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class = mysqli_query($mysqli, $sql);
	$tab_classe = array();
	if ($res_class->num_rows > 0) {
		while ($lig_tmp = $res_class->fetch_object()) {
			$tab_classe['periode'][$lig_tmp->periode]['id_classe'] = $lig_tmp->id_classe;
			$tab_classe['periode'][$lig_tmp->periode]['classe'] = $lig_tmp->classe;
			$tab_classe['periode'][$lig_tmp->periode]['nom_periode'] = $lig_tmp->nom_periode;

			$tab_classe['classe'][$lig_tmp->id_classe]['periode'][] = $lig_tmp->periode;
			$tab_classe['classe'][$lig_tmp->id_classe]['nom_periode'][] = $lig_tmp->nom_periode;
			$tab_classe['classe'][$lig_tmp->id_classe]['classe'] = $lig_tmp->classe;
		}
		$res_class->close();
	}

	return $tab_classe;
}

/**
 * fonction qui renvoie le nom de la classe d'un élève pour chaque période
 *
 * @param string $ele_login login de l'élève
 * @return array Tableau des classes en fonction des périodes
 */
function get_class_from_ele_login($ele_login) {
	global $mysqli;
	$sql = "SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";

	$res_class = mysqli_query($mysqli, $sql);
	$a = 0;
	$tab_classe = array();
	if ($res_class->num_rows > 0) {
		$tab_classe['liste'] = "";
		$tab_classe['liste_nbsp'] = "";
		while ($lig_tmp = $res_class->fetch_object()) {

			$tab_classe[$lig_tmp->id_classe] = $lig_tmp->classe;

			if ($a > 0) {
				$tab_classe['liste'] .= ", ";
			}
			$tab_classe['liste'] .= $lig_tmp->classe;

			if ($a > 0) {
				$tab_classe['liste_nbsp'] .= ", ";
			}
			$tab_classe['liste_nbsp'] .= preg_replace("/ /", "&nbsp;", $lig_tmp->classe);

			$tab_classe['id' . $a] = $lig_tmp->id_classe;
			//$a = $a++;
			$a++;

		}
		$res_class->close();
	}

	return $tab_classe;
}

/**
 * Retourne les classes d'un élève ordonnées par périodes puis classes
 *
 * @param string $ele_login Login de l'élève
 * @return array
 */
function get_noms_classes_from_ele_login($ele_login) {
	global $mysqli;
	$sql = "SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";

	$res_class = mysqli_query($mysqli, $sql);
	$tab_classe = array();
	if ($res_class->num_rows > 0) {
		while ($lig_tmp = $res_class->fetch_object()) {
			$tab_classe[] = $lig_tmp->classe;
		}
		$res_class->close();
	}

	return $tab_classe;
}

/**
 * Retourne la chaine des classes d'un élève ordonnées par périodes puis classes
 *
 * @param string $ele_login Login de l'élève
 * @return string
 */
function get_chaine_liste_noms_classes_from_ele_login($ele_login) {
	global $mysqli;
	$sql = "SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_classes jec, classes c WHERE jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY periode,classe;";
	$res_class = mysqli_query($mysqli, $sql);
	$chaine = "";
	if ($res_class->num_rows > 0) {
		while ($lig_tmp = $res_class->fetch_object()) {
			if ($chaine != "") {
				$chaine .= ", ";
			}
			$chaine = $lig_tmp->classe;
		}
		$res_class->close();
	}

	return $chaine;
}

/**
 * Renvoie les élèves liés à un responsable
 *
 * @param string $resp_login Login du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 * @return array
 * @see get_class_from_ele_login()
 */
function get_enfants_from_resp_login($resp_login, $mode = 'simple', $meme_en_resp_legal_0 = "n", $tab_infos_a_afficher = array()) {
	global $mysqli;
	$sql = "(SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
				WHERE e.ele_id=r.ele_id AND
				rp.pers_id=r.pers_id AND
				rp.login='$resp_login' AND
				(r.resp_legal='1' OR r.resp_legal='2') ORDER BY e.nom,e.prenom)";
	if ($meme_en_resp_legal_0 == "y") {
		$sql .= " UNION (SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
			WHERE e.ele_id=r.ele_id AND
			rp.pers_id=r.pers_id AND
			rp.login='$resp_login' AND
			r.resp_legal='0' ORDER BY e.nom,e.prenom)";
	} elseif ($meme_en_resp_legal_0 == "yy") {
		$sql .= " UNION (SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
			WHERE e.ele_id=r.ele_id AND
			rp.pers_id=r.pers_id AND
			rp.login='$resp_login' AND
			r.acces_sp='y' AND
			r.resp_legal='0' ORDER BY e.nom,e.prenom)";
	}
	//echo "$sql<br />";

	$res_ele = mysqli_query($mysqli, $sql);
	$tab_ele = array();
	if ($res_ele->num_rows > 0) {
		while ($lig_tmp = $res_ele->fetch_object()) {
			$infos_supplementaires = "";
			if (in_array("resp_legal", $tab_infos_a_afficher)) {
				$infos_supplementaires .= " <span title=\"";
				if ($lig_tmp->resp_legal == 0) {
					$infos_supplementaires .= "Responsable non légal";
				} else {
					$infos_supplementaires .= "Responsable légal n°" . $lig_tmp->resp_legal;
				}
				$infos_supplementaires .= "\">(<em>$lig_tmp->resp_legal</em>)</span>";

			}
			if (in_array("envoi_bulletin", $tab_infos_a_afficher)) {
				if ($lig_tmp->resp_legal == 0) {
					if ($lig_tmp->envoi_bulletin == "y") {
						$infos_supplementaires .= " <span title=\"Les bulletins de cet élève sont générés à destination de ce responsable non légal.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone16' /></span>";
					} else {
						$infos_supplementaires .= " <span title=\"Les bulletins de cet élève ne sont pas générés à destination de ce responsable non légal.\"><img src='$gepiPath/images/icons/bulletin_barre.png' class='icone16' /></span>";
					}
				} else {
					$infos_supplementaires .= " <span title=\"Les bulletins sont toujours générés à destination des responsables légaux.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone16' /></span>";
				}
			}
			// A faire: indiquer s'il y a un acces_sp dans le cas resp_legal=0

			$tab_ele[] = $lig_tmp->login;
			if ($mode == 'avec_classe') {
				$tmp_chaine_classes = "";

				$tmp_tab_clas = get_class_from_ele_login($lig_tmp->login);
				if (isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes = " (" . $tmp_tab_clas['liste'] . ")";
				}

				$tab_ele[] = ucfirst(mb_strtolower($lig_tmp->prenom)) . " " . mb_strtoupper($lig_tmp->nom) . $tmp_chaine_classes . $infos_supplementaires;
			} else {
				$tab_ele[] = ucfirst(mb_strtolower($lig_tmp->prenom)) . " " . mb_strtoupper($lig_tmp->nom) . $infos_supplementaires;
			}
		}
		$res_ele->close();
	}

	return $tab_ele;
}

/**
 * Renvoie les élèves liés à un responsable
 *
 * @param string $pers_id identifiant sconet du responsable
 * @param string $mode Si avec_classe renvoie aussi la classe
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 * @throws \Exception
 * @return array
 * @see get_class_from_ele_login()
 */
function get_enfants_from_pers_id($pers_id, $mode = 'simple', $meme_en_resp_legal_0 = "n", $tab_infos_a_afficher = array()) {
	global $mysqli, $gepiPath;
	$sql = "(SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
		WHERE e.ele_id=r.ele_id AND
		rp.pers_id=r.pers_id AND
		rp.pers_id='$pers_id' AND
		(r.resp_legal='1' OR r.resp_legal='2')
		ORDER BY e.nom,e.prenom)";
	if ($meme_en_resp_legal_0 == "y") {
		$sql .= " UNION (SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
			WHERE e.ele_id=r.ele_id AND
			rp.pers_id=r.pers_id AND
			rp.pers_id='$pers_id' AND
			r.resp_legal='0'
			ORDER BY e.nom,e.prenom)";
	} elseif ($meme_en_resp_legal_0 == "yy") {
		$sql .= " UNION (SELECT e.nom,e.prenom,e.login, r.resp_legal, r.acces_sp, r.envoi_bulletin FROM eleves e, responsables2 r, resp_pers rp
			WHERE e.ele_id=r.ele_id AND
			rp.pers_id=r.pers_id AND
			rp.pers_id='$pers_id' AND
			r.acces_sp='y' AND
			r.resp_legal='0'
			ORDER BY e.nom,e.prenom)";
	}

	$res_ele = mysqli_query($mysqli, $sql);
	$tab_ele = array();
	if ($res_ele->num_rows > 0) {
		while ($lig_tmp = $res_ele->fetch_object()) {
			if ($mode == 'csv') {
				$tab_ele[] = $lig_tmp->login;

				$tmp_chaine_classes = "";
				$tmp_chaine_classes2 = "";
				$tmp_tab_clas = get_class_from_ele_login($lig_tmp->login);
				if (isset($tmp_tab_clas['liste'])) {
					$tmp_chaine_classes = " (" . $tmp_tab_clas['liste'] . ")";
					$tmp_chaine_classes2 = $tmp_tab_clas['liste'];
				}

				$chaine_prenom_nom = casse_mot($lig_tmp->prenom, 'majf2') . " " . casse_mot($lig_tmp->nom, 'maj');
				$tab_ele[] = $chaine_prenom_nom . $tmp_chaine_classes . ";" .
					$lig_tmp->login . ";" .
					$chaine_prenom_nom . ";" .
					$tmp_chaine_classes2;
			} else {
				$infos_supplementaires = "";
				if (in_array("resp_legal", $tab_infos_a_afficher)) {
					$infos_supplementaires .= " <span title=\"";
					if ($lig_tmp->resp_legal == 0) {
						$infos_supplementaires .= "Responsable non légal";
					} else {
						$infos_supplementaires .= "Responsable légal n°" . $lig_tmp->resp_legal;
					}
					$infos_supplementaires .= "\">(<em>$lig_tmp->resp_legal</em>)</span>";

				}
				if (in_array("envoi_bulletin", $tab_infos_a_afficher)) {
					if ($lig_tmp->resp_legal == 0) {
						if ($lig_tmp->envoi_bulletin == "y") {
							$infos_supplementaires .= " <span title=\"Les bulletins de cet élève sont générés à destination de ce responsable non légal.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone16' /></span>";
						} else {
							$infos_supplementaires .= " <span title=\"Les bulletins de cet élève ne sont pas générés à destination de ce responsable non légal.\"><img src='$gepiPath/images/icons/bulletin_barre.png' class='icone16' /></span>";
						}
					} else {
						$infos_supplementaires .= " <span title=\"Les bulletins sont toujours générés à destination des responsables légaux.\"><img src='$gepiPath/images/icons/bulletin.png' class='icone16' /></span>";
					}
				}
				// A faire: indiquer s'il y a un acces_sp dans le cas resp_legal=0

				$tab_ele[] = $lig_tmp->login;
				if ($mode == 'avec_classe') {
					$tmp_chaine_classes = "";

					$tmp_tab_clas = get_class_from_ele_login($lig_tmp->login);
					if (isset($tmp_tab_clas['liste'])) {
						$tmp_chaine_classes = " (" . $tmp_tab_clas['liste'] . ")";
					}

					$tab_ele[] = ucfirst(mb_strtolower($lig_tmp->prenom)) . " " . mb_strtoupper($lig_tmp->nom) . $tmp_chaine_classes . $infos_supplementaires;
				} else {
					$tab_ele[] = ucfirst(mb_strtolower($lig_tmp->prenom)) . " " . mb_strtoupper($lig_tmp->nom) . $infos_supplementaires;
				}
			}
		}
		$res_ele->close();
	}

	return $tab_ele;
}

/**
 * Renvoie le statut avec des accents
 *
 * @param string $user_statut Statut à corriger
 * @return string Le statut corrigé
 */
function statut_accentue($user_statut) {
	switch ($user_statut) {
		case "administrateur":
			$chaine = "administrateur";
			break;
		case "scolarite":
			$chaine = "scolarité";
			break;
		case "professeur":
			$chaine = "professeur";
			break;
		case "secours":
			$chaine = "secours";
			break;
		case "cpe":
			$chaine = "cpe";
			break;
		case "eleve":
			$chaine = "élève";
			break;
		case "responsable":
			$chaine = "responsable";
			break;
		default:
			$chaine = "statut inconnu";
			break;
	}

	return $chaine;
}

/**
 * Renvoie le nom d'une classe à partir de son Id
 *
 * Renvoie classes.classe
 *
 * @param mixed $id_classe Id de la classe
 * @return string|bool Le nom d'une classe ou FALSE
 */
function get_nom_classe($id_classe) {
	global $mysqli;
	$sql = "SELECT classe FROM classes WHERE id='$id_classe';";
	$resultat = mysqli_query($mysqli, $sql);
	if ($resultat->num_rows > 0) {
		$lig_tmp = $resultat->fetch_object();
		$classe = $lig_tmp->classe;
		$resultat->close();
		return $classe;
	} else {
		return FALSE;
	}
}

/**
 * Formate une date au format jj/mm/aa
 *
 * @param string $date
 * @return string La date formatée
 */
function formate_date($date, $avec_heure = "n", $avec_nom_jour = "") {
	$tmp_date = explode(" ", $date);
	$tab_date = explode("-", $tmp_date[0]);

	$retour = "";

	if (isset($tab_date[2])) {
		if ($avec_nom_jour == "court") {
			$instant = mktime(12, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]);
			$jour = french_strftime("%a", $instant) . " ";
			$retour .= $jour;
		} elseif ($avec_nom_jour == "complet") {
			$instant = mktime(12, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]);
			$jour = french_strftime("%A", $instant) . " ";
			$retour .= $jour;
		}
		$retour .= sprintf("%02d", $tab_date[2]) . "/" . sprintf("%02d", $tab_date[1]) . "/" . $tab_date[0];
	} elseif (isset($tab_date[0])) {
		$retour .= $tab_date[0];
	} else {
		$retour .= $date;
	}

	if (($avec_heure == "y") && (isset($tmp_date[1])) && (preg_match("/[0-9]{2}:[0-9]{2}:[0-9]{2}/", $tmp_date[1]))) {
		$retour .= " à " . $tmp_date[1];
	} elseif (($avec_heure == "y2") && (isset($tmp_date[1])) && (preg_match("/[0-9]{2}:[0-9]{2}:[0-9]{2}/", $tmp_date[1]))) {
		// Virer les secondes à 00
		$retour .= " à " . preg_replace("/:00$/", "", $tmp_date[1]);
	}

	return $retour;
}

/**
 * Convertit les codes régimes de Sconet
 *
 * @param int $code_regime Le code Sconet
 * @return string Le régime dans Gépi
 */
function traite_regime_sconet($code_regime) {
	$premier_caractere_code_regime = mb_substr($code_regime, 0, 1);
	switch ($premier_caractere_code_regime) {
		case "0":
			// 0       EXTERN  EXTERNE LIBRE
			return "ext.";
			break;
		case "1":
			// 1       EX.SUR  EXTERNE SURVEILLE
			return "ext.";
			break;
		case "2":
			/*
			2       DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT
			21      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 1
			22      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 2
			23      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 3
			24      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 4
			25      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 5
			26      DP DAN  DEMI-PENSIONNAIRE DANS L'ETABLISSEMENT 6
			29      AU TIC  DEMI-PENSIONNAIRE AU TICKET
			*/
			return "d/p";
			break;
		case "3":
			/*
			3       INTERN  INTERNE DANS L'ETABLISSEMENT
			31      INT 1J  INTERNE 1 JOUR
			32      INT 2J  INTERNE 2 JOURS
			33      INT 3J  INTERNE 3 JOURS
			34      INT 4J  INTERNE 4 JOURS
			35      INT 5J  INTERNE 5 JOURS
			36      INT 6J  INTERNE 6 JOURS
			38      1/2 IN  DEMI INTERNE
			39      INT WE  INTERNE WEEK END
			*/
			return "int.";
		case "4":
			// 4       IN.EX.  INTERNE EXTERNE
			return "i-e";
		case "5":
			// 5       IN.HEB  INTERNE HEBERGE
			return "int.";
		case "6":
			// 6       DP HOR  DEMI-PENSIONNAIRE HORS L'ETABLISSEMENT
			return "d/p";
		default:
			return "ERR";
		//return "d/p";
	}
}

/**
 * Renvoie les préférences d'un utilisateur pour un item en interrogeant la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherché
 * @param string $default Valeur par défaut
 * @return string La valeur de l'item
 */
function getPref($login, $item, $default) {
	global $mysqli;
	$sql = "SELECT value FROM preferences WHERE login='$login' AND name='$item'";
	//echo "$sql<br />";
	$res_prefs = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res_prefs) > 0) {
		$ligne = $res_prefs->fetch_object();
		$res_prefs->close();
		return $ligne->value;
	} else {
		return $default;
	}
}

/**
 * Enregistre les préférences d'un utilisateur pour un item dans la table preferences
 *
 * @param string $login Login de l'utilisateur
 * @param string $item Item recherché
 * @param string $valeur Valeur à enregistrer
 * @return boolean TRUE si tout c'est bien passé
 */
function savePref($login, $item, $valeur) {
	global $mysqli;
	$sql = "SELECT value FROM preferences WHERE login='$login' AND name='$item'";

	$res_prefs = mysqli_query($mysqli, $sql);
	$nb_lignes = $res_prefs->num_rows;
	$res_prefs->close();

	if ($nb_lignes > 0) {
		$sql = "UPDATE preferences SET value='$valeur' WHERE login='$login' AND name='$item';";
	} else {
		$sql = "INSERT INTO preferences SET login='$login', name='$item', value='$valeur';";
	}
	$res = mysqli_query($mysqli, $sql);

	if ($res) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Renvoie l'ensemle des paramètres d'une classe en interrogeant la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @return array Tableau associatif des paramètres name=>value
 */
function getAllParamClasse($id_classe) {
	global $mysqli;
	$sql = "SELECT * FROM classes_param WHERE id_classe='$id_classe' ORDER BY name;";

	$res_param = mysqli_query($mysqli, $sql);
	$tab_param = array();
	if ($res_param->num_rows > 0) {
		while ($ligne = $res_param->fetch_object()) {
			$tab_param[$ligne->name] = $ligne->value;
		}
		$res_param->close();
	}

	return $tab_param;
}

/**
 * Renvoie les paramètres d'une classe pour un item en interrogeant la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @param string $item Item recherché
 * @param string $default Valeur par défaut
 * @return string La valeur de l'item
 */
function getParamClasse($id_classe, $item, $default) {
	global $mysqli;
	$sql = "SELECT value FROM classes_param WHERE id_classe='$id_classe' AND name='$item'";

	$res_param = mysqli_query($mysqli, $sql);
	if ($res_param->num_rows > 0) {
		$ligne = $res_param->fetch_object();
		$res_param->close();
		return $ligne->value;
	} else {
		return $default;
	}
}

/**
 * Enregistre les paramètres d'une classe pour un item dans la table classes_param
 *
 * @param string $id_classe Identifiant de la classe
 * @param string $item Item recherché
 * @param string $valeur Valeur à enregistrer
 * @return boolean TRUE si tout c'est bien passé
 */
function saveParamClasse($id_classe, $item, $valeur) {
	global $mysqli;
	$sql = "SELECT value FROM classes_param WHERE id_classe='$id_classe' AND name='$item'";

	$resultat = mysqli_query($mysqli, $sql);
	$nb_lignes = $resultat->num_rows;
	$resultat->close();

	if ($nb_lignes > 0) {
		$sql = "UPDATE classes_param SET value='$valeur' WHERE id_classe='$id_classe' AND name='$item';";
	} else {
		$sql = "INSERT INTO classes_param SET id_classe='$id_classe', name='$item', value='$valeur';";
	}

	$res = mysqli_query($mysqli, $sql);

	if ($res) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Position horizontale initiale pour permettre un affichage sans superposition
 *
 * @global int $GLOBALS ['$posDiv_infobulle']
 * @name $posDiv_infobulle
 */
$GLOBALS['posDiv_infobulle'] = 0;

/**
 *
 * @global array $GLOBALS ['tabid_infobulle']
 * @name $tabid_infobulle
 */
$GLOBALS['tabid_infobulle'] = array();

/**
 *
 * @global string $GLOBALS ['unite_div_infobulle']
 * @name $unite_div_infobulle
 */
$GLOBALS['unite_div_infobulle'] = '';

/**
 * Les infobulles ne sont pas décallées si à oui
 *
 * @global string $GLOBALS ['pas_de_decalage_infobulle']
 * @name $pas_de_decalage_infobulle
 */
$GLOBALS['pas_de_decalage_infobulle'] = '';

/**
 * Ajoute un argument aux classes du div
 *
 * @global string $GLOBALS ['class_special_infobulle']
 * @name $class_special_infobulle
 */
$GLOBALS['class_special_infobulle'] = '';

/**
 * $bg_titre: Si $bg_titre est vide, on utilise la couleur par défaut correspondant à .infobulle_entete (défini dans style.css et éventuellement modifié dans style_screen_ajout.css)
 *
 * $bg_texte: Si $bg_texte est vide, on utilise la couleur par défaut correspondant à .infobulle_corps (défini dans style.css et éventuellement modifié dans style_screen_ajout.css)
 *
 * $hauteur: En mettant 0, on laisse le DIV s'adapter au contenu (se réduire/s'ajuster)
 *
 * $bouton_close: S'il est affiché, c'est dans la barre de titre. Si la barre de titre n'est pas affichée, ce bouton ne peut pas être affiché.
 *
 * @param string $id Identifiant du DIV conteneur
 * @param string $titre Texte du titre du DIV
 * @param string $bg_titre Couleur de fond de la barre de titre.
 * @param string $texte Texte du contenu du DIV
 * @param string $bg_texte Couleur de fond du DIV contenant le texte
 * @param int $largeur Largeur du DIV conteneur
 * @param int $hauteur Hauteur (minimale) du DIV conteneur
 * @param string $drag 'y' ou 'n' pour rendre le DIV draggable ('yy' rend même le corps de l'infobulle poignée de drag)
 * @param string $bouton_close 'y' ou 'n' pour afficher le bouton Close
 * @param string $survol_close 'y' ou 'n' pour refermer le DIV automatiquement lorsque le survol quitte le DIV
 * @param string $overflow 'y' ou 'n' activer l'overflow automatique sur la partie Texte. Il faut que $hauteur soit non NULLe
 * @param int $zindex_infobulle
 * @return string
 * @global type
 * @global array
 * @global type
 * @global type
 * @global type
 * @global type
 */
function creer_div_infobulle($id, $titre, $bg_titre, $texte, $bg_texte, $largeur, $hauteur, $drag, $bouton_close, $survol_close, $overflow, $zindex_infobulle = 1) {
	/*
		$overflow:
	*/
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;

	$style_box = "color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:$zindex_infobulle;";

	$style_bar = "color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	$style_close = "color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";

	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[] = $id;

	// Conteneur:
	if ($bg_texte == '') {
		$div = "<div id='$id' class='infobulle_corps div_conteneur_infobulle";
		if ((isset($class_special_infobulle)) && ($class_special_infobulle != '')) {
			$div .= " " . $class_special_infobulle;
		}
		$div .= "' style='$style_box width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; ";
	} else {
		$div = "<div id='$id' ";
		if ((isset($class_special_infobulle)) && ($class_special_infobulle != '')) {
			$div .= "class='" . $class_special_infobulle . " div_conteneur_infobulle' ";
		} else {
			$div .= "class='div_conteneur_infobulle' ";
		}
		$div .= "style='$style_box background-color: $bg_texte; width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; ";
	}
	if (($hauteur != 0) || ($hauteur != "")) {
		$div .= "height: " . $hauteur;
		if (is_numeric($hauteur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= $unite_div_infobulle . "; ";
	}
	// Position horizontale initiale pour permettre un affichage sans superposition si Javascript est désactivé:
	$div .= "left:" . $posDiv_infobulle . $unite_div_infobulle . ";";
	$div .= "'>\n";


	// Barre de titre:
	// Elle n'est affichée que si le titre est non vide
	if ($titre != "") {
		if ($bg_titre == '') {
			$div .= "<div class='infobulle_entete' style='$style_bar width: " . $largeur;
			if (is_numeric($largeur)) {
				$div .= $unite_div_infobulle;
			}
			$div .= ";'";
		} else {
			$div .= "<div style='$style_bar background-color: $bg_titre; width: " . $largeur;
			if (is_numeric($largeur)) {
				$div .= $unite_div_infobulle;
			}
			$div .= ";'";
		}
		if (($drag == "y") || ($drag == "yy")) {
			// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
			$div .= " onmousedown=\"dragStart(event, '$id')\"";
		}
		$div .= ">\n";

		if ($bouton_close == "y") {
			//$div.="<div style='$style_close'><a href='#' onclick=\"cacher_div('$id'); return false;\">";
			$div .= "<div style='$style_close'><a href=\"javascript:cacher_div('$id')\">";
			if (isset($niveau_arbo) && $niveau_arbo == 0) {
				$div .= "<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else if (isset($niveau_arbo) && $niveau_arbo == 1) {
				$div .= "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else if (isset($niveau_arbo) && $niveau_arbo == 2) {
				$div .= "<img src='../../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else {
				$div .= "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			$div .= "</a></div>\n";
		}
		$div .= "<span style='padding-left: 1px;'>\n";
		$div .= $titre . "\n";
		$div .= "</span>\n";
		$div .= "</div>\n";
	}


	// Partie texte:
	//==================
	// 20110113
	$div .= "<div id='" . $id . "_contenu_corps'";

	if ($drag == "yy") {
		// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
		$div .= " onmousedown=\"dragStart(event, '$id')\"";
	}

	//==================
	if ($survol_close == "y") {
		// On referme le DIV lorsque la souris quitte la zone de texte.
		$div .= " onmouseout=\"cacher_div('$id');\"";
	}
	$div .= ">\n";
	if (($overflow == 'y') && (($hauteur != 0) || ($hauteur != ""))) {
		$hauteur_hors_titre = $hauteur - 1; // Le calcul n'est correct que dans le cas où l'unité est 'em'
		$div .= "<div style='width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; height: " . $hauteur_hors_titre;
		if (is_numeric($hauteur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; overflow: auto;'>\n";
		$div .= "<div style='padding-left: 1px;'>\n";
		$div .= $texte;
		$div .= "</div>\n";
		$div .= "</div>\n";
	} else {
		$div .= "<div style='padding-left: 1px;'>\n";
		$div .= $texte;
		$div .= "</div>\n";
	}
	$div .= "</div>\n";

	$div .= "</div>\n";

	// Les div vont s'afficher côte à côte sans superposition en bas de page si JavaScript est désactivé:
	if (isset($pas_de_decalage_infobulle) and $pas_de_decalage_infobulle == "oui") {
		// on ne décale pas les div des infobulles
	} else {
		$largeur = str_replace('px', '', $largeur);
		$largeur = str_replace('em', '', $largeur);
		//echo "largeur='$largeur'<br />\$posDiv_infobulle=$posDiv_infobulle<br />";

		$posDiv_infobulle = $posDiv_infobulle + $largeur;
	}

	return $div;
}

// Fonction de création d'infobulle redimensionnable (http://www.twinhelix.com/javascript/dragresize/)
function creer_div_infobulle2($id, $titre, $bg_titre, $texte, $bg_texte, $largeur, $hauteur, $drag, $bouton_close, $survol_close, $overflow, $zindex_infobulle = 1) {
	/*
		$overflow:
	*/
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;

	$style_box = "color: #000000; border: 1px solid #000000; padding: 0px; position: absolute; z-index:$zindex_infobulle;";

	$style_bar = "color: #ffffff; cursor: move; font-weight: bold; padding: 0px;";
	$style_close = "color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;";

	// On fait la liste des identifiants de DIV pour cacher les Div avec javascript en fin de chargement de la page (dans /lib/footer.inc.php).
	$tabid_infobulle[] = $id;

	// Conteneur:
	if ($bg_texte == '') {
		$div = "<div id='$id' class='drsElement infobulle_corps";
		if ((isset($class_special_infobulle)) && ($class_special_infobulle != '')) {
			$div .= " " . $class_special_infobulle;
		}
		$div .= "' style='$style_box width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; ";
	} else {
		$div = "<div id='$id' ";
		if ((isset($class_special_infobulle)) && ($class_special_infobulle != '')) {
			$div .= "class='drsElement " . $class_special_infobulle . "' ";
		} else {
			$div .= "class='drsElement' ";
		}
		$div .= "style='$style_box background-color: $bg_texte; width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= "; ";
	}
	if (($hauteur != 0) || ($hauteur != "")) {
		$div .= "height: " . $hauteur;
		if (is_numeric($hauteur)) {
			$div .= $unite_div_infobulle;
		}
		$div .= $unite_div_infobulle . "; ";
	}
	// Position horizontale initiale pour permettre un affichage sans superposition si Javascript est désactivé:
	$div .= "left:" . $posDiv_infobulle . $unite_div_infobulle . ";";
	$div .= "'>\n";


	// Barre de titre:
	// Elle n'est affichée que si le titre est non vide
	if ($titre != "") {
		if ($bg_titre == '') {
			/*
			$div.="<div class='drsMoveHandle infobulle_entete' style='$style_bar width: ".$largeur;
			if(is_numeric($largeur)) {
				$div.=$unite_div_infobulle;
			}
			*/
			$div .= "<div class='drsMoveHandle infobulle_entete' style='$style_bar";
			$div .= ";'";
		} else {
			/*
			$div.="<div class='drsMoveHandle' style='$style_bar background-color: $bg_titre; width: ".$largeur;
			if(is_numeric($largeur)) {
				$div.=$unite_div_infobulle;
			}
			*/
			$div .= "<div class='drsMoveHandle' style='$style_bar background-color: $bg_titre; ";
			$div .= ";'";
		}

		/*
		if(($drag=="y")||($drag=="yy")){
			// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
			$div.=" onmousedown=\"dragStart(event, '$id')\"";
		}
		*/
		$div .= ">\n";

		if ($bouton_close == "y") {
			//$div.="<div style='$style_close'><a href='#' onclick=\"cacher_div('$id'); return false;\">";
			$div .= "<div style='$style_close'><a href=\"javascript:cacher_div('$id')\">";
			if (isset($niveau_arbo) && $niveau_arbo == 0) {
				$div .= "<img src='./images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else if (isset($niveau_arbo) && $niveau_arbo == 1) {
				$div .= "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else if (isset($niveau_arbo) && $niveau_arbo == 2) {
				$div .= "<img src='../../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			} else {
				$div .= "<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />";
			}
			$div .= "</a></div>\n";
		}
		$div .= "<span style='padding-left: 1px;'>\n";
		$div .= $titre . "\n";
		$div .= "</span>\n";
		$div .= "</div>\n";
	}


	// Partie texte:
	//==================
	// 20110113
	$div .= "<div id='" . $id . "_contenu_corps'";

	if ($drag == "yy") {
		// Là on utilise les fonctions de http://www.brainjar.com stockées dans brainjar_drag.js
		//$div.=" onmousedown=\"dragStart(event, '$id')\"";
		$div .= " class=\"drsMoveHandle\"";
	}

	//==================
	if ($survol_close == "y") {
		// On referme le DIV lorsque la souris quitte la zone de texte.
		$div .= " onmouseout=\"cacher_div('$id');\"";
	}
	$div .= ">\n";
	if (($overflow == 'y') && (($hauteur != 0) || ($hauteur != ""))) {
		$hauteur_hors_titre = $hauteur - 1; // Le calcul n'est correct que dans le cas où l'unité est 'em'
		$div .= "<div style='width: " . $largeur;
		if (is_numeric($largeur)) {
			$div .= $unite_div_infobulle;
		}
		// Je n'arrive pas à ce que l'overflow s'adapte au redimensionnement du div via dragresize
		$div .= "; height: " . $hauteur_hors_titre;
		if (is_numeric($hauteur)) {
			$div .= $unite_div_infobulle;
		}

		$div .= "; overflow: auto;'>\n";
		$div .= "<div style='padding-left: 1px;'>\n";
		$div .= $texte;
		$div .= "</div>\n";
		$div .= "</div>\n";
	} else {
		$div .= "<div style='padding-left: 1px;'>\n";
		$div .= $texte;
		$div .= "</div>\n";
	}
	$div .= "</div>\n";

	$div .= "</div>\n";

	// Les div vont s'afficher côte à côte sans superposition en bas de page si JavaScript est désactivé:
	if (isset($pas_de_decalage_infobulle) and $pas_de_decalage_infobulle == "oui") {
		// on ne décale pas les div des infobulles
	} else {
		$largeur = str_replace('px', '', $largeur);
		$posDiv_infobulle = $posDiv_infobulle + $largeur;
	}

	return $div;
}


/**
 * tableau des variables transmises d'une page à l'autre
 *
 * @global array $GLOBALS ['debug_var_count']
 * @name $debug_var_count
 */
$GLOBALS['debug_var_count'] = array();

/**
 * indice de la variable transmise
 *
 * @global int $GLOBALS ['cpt_debug_debug_var']
 * @name $cpt_debug_debug_var
 */
$GLOBALS['cpt_debug_debug_var'] = 0;

/**
 * Affiche les variables transmises d'une page à l'autre: GET, POST, SERVER et SESSION
 *
 * @global array
 * @global int
 */
$debug_var_count = array();
$cpt_debug_debug_var = 0;
function debug_var() {
	global $debug_var_count;
	global $cpt_debug_debug_var;
	global $NON_PROTECT;

	$debug_var_count['NON_PROTECT'] = 0;
	$debug_var_count['POST'] = 0;
	$debug_var_count['GET'] = 0;
	$debug_var_count['SESSION'] = 0;
	$debug_var_count['SERVER'] = 0;

	$debug_var_count['COOKIE'] = 0;

	$debug_var_count['FILES'] = 0;

	// Fonction destinée à afficher les variables transmises d'une page à l'autre: GET, POST et SESSION
	echo "<div style='border: 1px solid black; background-color: white; color: black;'>\n";

	$cpt_debug_debug_var = 0;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p><strong>Variables transmises en POST, GET, SESSION,...</strong> (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)</p>\n";

	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en POST: ";
	if ((!isset($_POST)) || (count($_POST) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;

	echo "<script type='text/javascript'>
	tab_etat_debug_var = [];
	function affiche_debug_var(id,mode) {
		if(document.getElementById(id)) {
			if(mode === 1) {
				document.getElementById(id).style.display='';
			}
			else {
				document.getElementById(id).style.display='none';
			}
		}
	}
</script>\n";


	/**
	 * Affiche un tableau des valeurs de GET, POST, SERVER ou SESSION
	 *
	 * @param string $chaine_tab_niv1
	 * @param array $tableau
	 * @param string $pref_chaine
	 * @global array
	 * @global int
	 */
	function tab_debug_var($chaine_tab_niv1, $tableau, $pref_chaine) {
		global $cpt_debug_debug_var;
		global $debug_var_count;

		echo " (<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)\n";

		echo "<table id='container_debug_var_$cpt_debug_debug_var' summary=\"Tableau de debug\">\n";
		foreach ($tableau as $post => $val) {
			echo "<tr><td valign='top'>" . $pref_chaine . "['" . $post . "']=</td><td>";

			if (is_array($val)) {
				$cpt_debug_debug_var++;

				echo "Array";
				tab_debug_var($chaine_tab_niv1, $val, $pref_chaine . '[' . $post . ']');

				$cpt_debug_debug_var++;
			} elseif (isset($debug_var_count[$chaine_tab_niv1])) {
				/*
				echo "<pre>";
				print_r($val);
				echo "</pre>";
				*/
				if (is_string($val)) {
					echo $val;
				} elseif (is_numeric($val)) {
					echo $val;
				} else {
					echo "<span style='color:red'>Ce n'est ni un tableau, ni une chaine... est-ce un résultat de requête SQL?</span>";
				}
				$debug_var_count[$chaine_tab_niv1]++;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";
	}


	echo "<table summary=\"Tableau de debug\">\n";
	foreach ($_POST as $post => $val) {
		echo "<tr><td valign='top'>\$_POST['" . $post . "']=</td><td>";

		if (is_array($val)) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('POST', $val, '$_POST[' . $post . ']');

			$cpt_debug_debug_var++;
		} else {
			echo $val;
			$debug_var_count['POST']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en POST: <b>" . $debug_var_count['POST'] . "</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en GET: ";
	if ((!isset($_GET)) || (count($_GET) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur GET\">";
	foreach ($_GET as $get => $val) {

		echo "<tr><td valign='top'>\$_GET['" . $get . "']=</td><td>";

		if (is_array($_GET[$get])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('GET', $val, '$_GET[' . $get . ']');

			$cpt_debug_debug_var++;
		} else {
			echo $val;
			$debug_var_count['GET']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en GET: <b>" . $debug_var_count['GET'] . "</b></p>\n";

	echo "</div>\n";
	echo "</blockquote>\n";


	if (isset($NON_PROTECT)) {
		echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
		echo "<p>Variables envoyées en NON_PROTECT: ";
		if (count($NON_PROTECT) == 0) {
			echo "aucune";
		} else {
			echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
		}
		echo "</p>\n";
		echo "<blockquote>\n";
		echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
		$cpt_debug_debug_var++;
		echo "<table summary=\"Tableau de debug\">\n";
		foreach ($NON_PROTECT as $post => $val) {
			echo "<tr><td valign='top'>\$NON_PROTECT['" . $post . "']=</td><td>";

			if (is_array($val)) {
				echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
				tab_debug_var('NON_PROTECT', $val, '$NON_PROTECT[' . $post . ']');

				$cpt_debug_debug_var++;
			} else {
				echo $val;
				$debug_var_count['NON_PROTECT']++;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";

		echo "<p>Nombre de valeurs en NON_PROTECT: <b>" . $debug_var_count['NON_PROTECT'] . "</b></p>\n";
		echo "</div>\n";
		echo "</blockquote>\n";
	}


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en SESSION: ";
	if ((!isset($_SESSION)) || (count($_SESSION) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur SESSION\">";
	foreach ($_SESSION as $variable => $val) {

		echo "<tr><td valign='top'>\$_SESSION['" . $variable . "']=</td><td>";
		if (is_array($_SESSION[$variable])) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('SESSION', $_SESSION[$variable], '$_SESSION[' . $variable . ']');

			$cpt_debug_debug_var++;
		} else {
			echo $val;
			$debug_var_count['SESSION']++;
		}
		echo "</td></tr>\n";

	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en SESSION: <b>" . $debug_var_count['SESSION'] . "</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en SERVER: ";
	if ((!isset($_SERVER)) || (count($_SERVER) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur SERVER\">";
	foreach ($_SERVER as $variable => $valeur) {
		echo "<tr><td>\$_SERVER['" . $variable . "']=</td><td>" . $valeur . "</td></tr>\n";
	}
	echo "</table>\n";

	echo "<p>Nombre de valeurs en SERVER: <b>" . $debug_var_count['SERVER'] . "</b></p>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables envoyées en FILES: ";
	if ((!isset($_FILES)) || (count($_FILES) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	if ((isset($_FILES)) && (count($_FILES) > 0)) {
		echo "<blockquote>\n";
		echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
		$cpt_debug_debug_var++;

		echo "<table summary=\"Tableau de debug\">\n";
		foreach ($_FILES as $key => $val) {
			echo "<tr><td valign='top'>\$_FILES['" . $key . "']=</td><td>";

			if (is_array($_FILES[$key])) {
				echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
				tab_debug_var('FILES', $_FILES[$key], '$_FILES[' . $key . ']');

				$cpt_debug_debug_var++;
			} else {
				echo $val;
			}

			echo "</td></tr>\n";
		}
		echo "</table>\n";

		echo "<p>Nombre de valeurs en FILES: <b>" . $debug_var_count['FILES'] . "</b></p>\n";
		echo "</div>\n";
		echo "</blockquote>\n";
	}

	echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>\n";
	echo "<p>Variables COOKIES: ";
	if ((!isset($_COOKIE)) || (count($_COOKIE) == 0)) {
		echo "aucune";
	} else {
		echo "(<a href='#ancre_debug_var_$cpt_debug_debug_var' onclick=\"tab_etat_debug_var[$cpt_debug_debug_var]=tab_etat_debug_var[$cpt_debug_debug_var]*(-1);affiche_debug_var('container_debug_var_$cpt_debug_debug_var',tab_etat_debug_var[$cpt_debug_debug_var]);return FALSE;\">*</a>)";
	}
	echo "</p>\n";
	echo "<blockquote>\n";
	echo "<div id='container_debug_var_$cpt_debug_debug_var'>\n";
	$cpt_debug_debug_var++;
	echo "<table summary=\"Tableau de debug sur COOKIE\">";
	foreach ($_COOKIE as $variable => $val) {

		echo "<tr><td valign='top'>\$_COOKIE['" . $variable . "']=</td><td>";

		if (is_array($val)) {
			echo "<a name='ancre_debug_var_$cpt_debug_debug_var'></a>Array\n";
			tab_debug_var('COOKIE', $val, '$_COOKIE[' . $variable . ']');

			$cpt_debug_debug_var++;
		} else {
			echo $val;
			$debug_var_count['COOKIE']++;
		}

		echo "</td></tr>\n";
	}
	echo "</table>\n";
	echo "</div>\n";
	echo "</blockquote>\n";


	echo "<script type='text/javascript'>
	// On masque le cadre de debug au chargement:
	//affiche_debug_var('container_debug_var',var_debug_var_etat);

	//for(i=0;i<tab_etat_debug_var.length;i++) {
	for(let i = 0; i < $cpt_debug_debug_var; i++) {
		if(document.getElementById('container_debug_var_'+i)) {
			affiche_debug_var('container_debug_var_'+i,-1);
		}
		// Variable destinée à alterner affichage/masquage
		tab_etat_debug_var[i]=-1;
	}
</script>\n";

	echo "</div>\n";
	echo "</div>\n";
}

/**
 *permet de vérifier si tel statut peut avoir accès à l'EdT en fonction des settings de l'admin
 *
 * @param string $statut Statut testé
 * @return string yes si peut avoir accès à l'EdT, no sinon
 * @see getSettingValue()
 */
function param_edt($statut) {
	$verif = "";
	if ($statut == "administrateur") {
		$verif = getSettingValue("autorise_edt_admin");
	} elseif ($statut == "professeur" or $statut == "scolarite" or $statut == "cpe" or $statut == "secours" or $statut == "autre") {
		$verif = getSettingValue("autorise_edt_tous");
	} elseif ($statut = "eleve" or $statut = "responsable") {
		$verif = getSettingValue("autorise_edt_eleve");
	} else {
		$verif = "";
	}
	// On vérifie $verif et on renvoie le return
	if ($verif == "y" or $verif == "yes") {
		return "yes";
	} else {
		return "no";
	}
}

/**
 * Le message à afficher
 *
 * @global string $GLOBALS ['themessage']
 * @name $themessage
 */
$GLOBALS['themessage'] = '';

/**
 * Affiche un fenêtre de confirmation via javascript
 *
 * Ajoute un attribut onclick à une balise pour appeler une fonction javascript contenant le message
 *
 * @return  string l'attribut onclick ou vide
 * @global string
 */
function insert_confirm_abandon() {
	global $themessage;

	if (isset($themessage)) {
		if ($themessage != "") {
			return " onclick=\"return confirm_abandon(this, change, '$themessage')\" ";
		} else {
			return "";
		}
	} else {
		return "";
	}
}

/**
 * Largeur maximum désirée
 *
 * @global int $GLOBALS ['photo_largeur_max']
 * @name $photo_largeur_max
 */
$GLOBALS['photo_largeur_max'] = 0;

/**
 * Hauteur maximum désirée;
 *
 * @global int $GLOBALS ['photo_hauteur_max']
 * @name $photo_hauteur_max
 */
$GLOBALS['photo_hauteur_max'] = 0;

/**
 * Renvoie le nom d'une classe à partir de son Id
 *
 * @param int $id_classe Id de la classe recherchée
 * @return string|bool nom de la classe (classe.classes)
 */
function get_class_from_id($id_classe) {
	global $mysqli;
	$sql = "SELECT classe FROM classes c WHERE id='$id_classe';";

	$res_class = mysqli_query($mysqli, $sql);
	if ($res_class->num_rows > 0) {
		$lig_tmp = $res_class->fetch_object();
		$classe = $lig_tmp->classe;
		$res_class->close();
		return $classe;
	} else {
		return FALSE;
	}
}


/**
 *
 * @throws \PHPMailer\PHPMailer\Exception
 * @global string
 */
function mail_connexion() {
	global $mysqli;
	global $active_hostbyaddr;

	$test_envoi_mail = getSettingValue("envoi_mail_connexion");

	//$date = strftime("%Y-%m-%d %H:%M:%S");
	//$date = ucfirst(french_strftime("%A %d-%m-%Y à %H:%M:%S"));
	//fdebug_mail_connexion("\$_SESSION['login']=".$_SESSION['login']."\n\$test_envoi_mail=$test_envoi_mail\n\$date=$date\n====================\n");

	if ($test_envoi_mail == "y") {
		$user_login = $_SESSION['login'];

		$sql = "SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";
		$res_user = mysqli_query($mysqli, $sql);
		if ($res_user->num_rows > 0) {
			$lig_user = $res_user->fetch_object();
			if (check_mail($lig_user->email)) {
				$adresse_ip = $_SERVER['REMOTE_ADDR'];
				$date = ucfirst(french_strftime("%A %d-%m-%Y à %H:%M:%S"));

				if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
					$result_hostbyaddr = " - " . @gethostbyaddr($adresse_ip);
				} else if ($active_hostbyaddr == "no_local") {
					if ((mb_substr($adresse_ip, 0, 3) == 127) or (mb_substr($adresse_ip, 0, 3) == 10.) or (mb_substr($adresse_ip, 0, 7) == 192.168)) {
						$result_hostbyaddr = "";
					} else {
						$tabip = explode(".", $adresse_ip);
						if (($tabip[0] == 172) && ($tabip[1] >= 16) && ($tabip[1] <= 31)) {
							$result_hostbyaddr = "";
						} else {
							$result_hostbyaddr = " - " . @gethostbyaddr($adresse_ip);
						}
					}
				} else {
					$result_hostbyaddr = "";
				}

				$message = "** Mail connexion Gepi **\n\n";
				$message .= "\n";
				$message .= "Vous (*) vous êtes connecté à GEPI :\n\n";
				$message .= "Identité                : " . mb_strtoupper($lig_user->nom) . " " . ucfirst(mb_strtolower($lig_user->prenom)) . "\n";
				$message .= "Login                   : " . $user_login . "\n";
				$message .= "Date                    : " . $date . "\n";
				$message .= "Origine de la connexion : " . $adresse_ip . "\n";
				if ($result_hostbyaddr != "") {
					$message .= "Adresse IP résolue en   : " . $result_hostbyaddr . "\n";
				}
				$message .= "\n";
				$message .= "Ce message, s'il vous parvient alors que vous ne vous êtes pas connecté à la date/heure indiquée, est susceptible d'indiquer que votre identité a pu être usurpée.\nVous devriez contrôler vos données, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'établissement) pour qu'il puisse prendre les mesures appropriées.\n";
				$message .= "\n";
				$message .= "(*) Vous ou une personne tentant d'usurper votre identité.\n";

				// On envoie le mail
				//fdebug_mail_connexion("\$message=$message\n====================\n");
				$destinataire = $lig_user->email;
				$tab_param_mail['destinataire'] = $lig_user->email;
				$sujet = "GEPI : Connexion $date";
				envoi_mail($sujet, $message, $destinataire, "", "plain", $tab_param_mail);
			}
			$res_user->close();
		}
	}
}

/**
 * Envoi un courriel à un utilisateur en cas de connexion avec son compte
 *
 * @param string $sujet Sujet du message
 * @param string $texte Texte du message
 * @param string $informer_admin Envoi aussi un courriel à l'administrateur si pas à 'n'
 * @throws \PHPMailer\PHPMailer\Exception
 * @see envoi_mail()
 * @see getSettingValue()
 * @global string
 */
function mail_alerte($sujet, $texte, $informer_admin = 'n') {
	global $mysqli;
	global $active_hostbyaddr;

	$user_login = $_SESSION['login'];

	$sql = "SELECT nom,prenom,email FROM utilisateurs WHERE login='$user_login';";

	$res_user = mysqli_query($mysqli, $sql);
	if ($res_user->num_rows > 0) {
		$lig_user = $res_user->fetch_object();

		$adresse_ip = $_SERVER['REMOTE_ADDR'];
		//$date = strftime("%Y-%m-%d %H:%M:%S");
		$date = ucfirst(french_strftime("%A %d-%m-%Y à %H:%M:%S"));
		//$url = parse_url($_SERVER['REQUEST_URI']);

		if (!(isset($active_hostbyaddr)) or ($active_hostbyaddr == "all")) {
			$result_hostbyaddr = " - " . @gethostbyaddr($adresse_ip);
		} else if ($active_hostbyaddr == "no_local") {
			if ((mb_substr($adresse_ip, 0, 3) == 127) or (mb_substr($adresse_ip, 0, 3) == 10.) or (mb_substr($adresse_ip, 0, 7) == 192.168)) {
				$result_hostbyaddr = "";
			} else {
				$tabip = explode(".", $adresse_ip);
				if (($tabip[0] == 172) && ($tabip[1] >= 16) && ($tabip[1] <= 31)) {
					$result_hostbyaddr = "";
				} else {
					$result_hostbyaddr = " - " . @gethostbyaddr($adresse_ip);
				}
			}
		} else {
			$result_hostbyaddr = "";
		}

		//$message = "** Mail connexion Gepi **\n\n";
		$message = $texte;
		$message .= "\n";
		$message .= "Vous (*) vous êtes connecté à GEPI :\n\n";
		$message .= "Identité                : " . mb_strtoupper($lig_user->nom) . " " . ucfirst(mb_strtolower($lig_user->prenom)) . "\n";
		$message .= "Login                   : " . $user_login . "\n";
		$message .= "Date                    : " . $date . "\n";
		$message .= "Origine de la connexion : " . $adresse_ip . "\n";
		if ($result_hostbyaddr != "") {
			$message .= "Adresse IP résolue en   : " . $result_hostbyaddr . "\n";
		}
		$message .= "\n";
		$message .= "Ce message, s'il vous parvient alors que vous ne vous êtes pas connecté à la date/heure indiquée, est susceptible d'indiquer que votre identité a pu être usurpée.\nVous devriez contrôler vos données, changer votre mot de passe et avertir l'administrateur (et/ou l'administration de l'établissement) pour qu'il puisse prendre les mesures appropriées.\n";
		$message .= "\n";
		$message .= "(*) Vous ou une personne tentant d'usurper votre identité.\n";

		$ajout = "";
		if (($informer_admin != 'n') && (check_mail(getSettingValue("gepiAdminAdress")))) {
			$ajout = "Bcc: " . getSettingValue("gepiAdminAdress") . "\r\n";
			$tab_param_mail['bcc'] = getSettingValue("gepiAdminAdress");
		}

		// On envoie le mail
		//fdebug_mail_connexion("\$message=$message\n====================\n");

		$destinataire = $lig_user->email;
		$tab_param_mail['destinataire'] = $lig_user->email;
		$sujet = "GEPI : $sujet $date";
		envoi_mail($sujet, $message, $destinataire, $ajout, "plain", $tab_param_mail);

		$res_user->close();
	}

}

/**
 * Formate un texte
 *
 * - Si le texte contient des < et >, on affiche tel quel
 * - Sinon, on transforme les retours à la ligne en <br />
 *
 * @param string $texte Le texte à formater
 * @return string Le texte formaté
 */
function texte_html_ou_pas($texte) {
	if ((strstr($texte, ">")) || (strstr($texte, "<"))) {
		$retour = $texte;
	} else {
		$retour = nl2br($texte);
	}
	return $retour;
}

/**
 * Activer le mode debug, "y" pour oui
 *
 * @global string $GLOBALS ['debug']
 * @name $debug
 */
$GLOBALS['debug'] = '';

/**
 *
 *
 * @global array $GLOBALS ['tab_instant']
 * @name $tab_instant
 */
$GLOBALS['tab_instant'] = array();


/**
 * Met une date en français
 *
 * @return text La date formatée
 */
function get_date_php() {
	$eng_words = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	$french_words = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	$date_str = date('l') . ' ' . date('d') . ' ' . date('F') . ' ' . date('Y');
	$date_str = str_replace($eng_words, $french_words, $date_str);
	return $date_str;
}

/**
 * Met en forme un prénom
 *
 * @param type $prenom Le prénom à traiter
 * @return type Le prénom traité
 */
function casse_prenom($prenom) {
	$tab = explode("-", $prenom);

	$retour = "";
	for ($i = 0; $i < count($tab); $i++) {
		if ($i > 0) {
			$retour .= "-";
		}
		$tab[$i] = casse_mot($tab[$i], 'majf2');
		$retour .= $tab[$i];
	}

	return $retour;
}

/**
 * Arrondi un nombre avec un certain nombre de chiffres après la virgule
 *
 * @param type $nombre Le nombre à convertir
 * @param type $nb_chiffre_apres_virgule
 * @return decimal Le nombre arrondi
 */
function nf($nombre, $nb_chiffre_apres_virgule = 1) {
	// Formatage des nombres
	// Precision:
	// Pour être sûr d'avoir un entier
	$nb_chiffre_apres_virgule = floor($nb_chiffre_apres_virgule);
	if ($nb_chiffre_apres_virgule < 1) {
		$precision = 0.1;
		$nb_chiffre_apres_virgule = 0;
	} else {
		$precision = pow(10, -1 * $nb_chiffre_apres_virgule);
	}

	if (($nombre == '') || ($nombre == '-')) {
		$valeur = $nombre;
	} else {
		$nombre = strtr($nombre, ",", ".");
		$valeur = number_format(round($nombre / $precision) * $precision, $nb_chiffre_apres_virgule, ',', '');
	}
	return $valeur;
}


/**
 * Envoit les informations de debug dans un fichier si à 'fichier', vers l'écran sinon
 *
 * @global string $GLOBALS ['mode_my_echo_debug']
 * @name $mode_my_echo_debug
 */
$GLOBALS['mode_my_echo_debug'] = '';

/**
 * Écrit les informations de debug si à 1
 *
 * @global int $GLOBALS ['my_echo_debug']
 * @name $my_echo_debug
 */
$GLOBALS['my_echo_debug'] = NULL;

/**
 * Ecrit des informations de debug dans un fichier ou à l'écran
 *
 * $dossier est à "/tmp" pour simplifier en debug sur une machine perso sous *nix,
 * Commenter la ligne au besoin
 *
 * @param string $texte
 * @global int
 * @global int
 * @global string
 * @see get_user_temp_directory()
 */
function my_echo_debug($texte) {
	global $mode_my_echo_debug, $my_echo_debug, $niveau_arbo;

	if ($my_echo_debug == 1) {
		if ($mode_my_echo_debug != 'fichier') {
			echo $texte;
		} else {
			$tempdir = get_user_temp_directory();
			if (isset($niveau_arbo) and ($niveau_arbo == "0")) {
				$points = ".";
			} elseif (isset($niveau_arbo) and ($niveau_arbo == "2")) {
				$points = "../..";
			} else {
				$points = "..";
			}
			$dossier = $points . "/temp/" . $tempdir;

			// Pour simplifier en debug sur une machine perso sous *nix:
			//$dossier="/tmp";

			$fichier = $dossier . "/my_echo_debug_" . date("Ymd_Hi") . ".txt";

			$f = fopen($fichier, "a+");
			fwrite($f, $texte);
			fclose($f);
		}
	}
}

/**
 * Retourne une chaine utf-8 avec la bonne casse
 *
 * $mode
 * - 'maj'   -> tout en majuscules
 * - 'min'   -> tout en minuscules
 * - 'majf'  -> Première lettre en majuscule, le reste en minuscule
 * - 'majf2' -> Première lettre de tous les mots en majuscule, le reste en minuscule
 *
 * @param string $mot chaine à modifier
 * @param string $mode Mode de conversion
 * @throws \Exception
 * @return string chaine mise en forme
 */
function casse_mot($mot, $mode = 'maj') {
	if (function_exists('mb_convert_case')) {
		if ($mode == 'maj') {
			return mb_convert_case(($mot), MB_CASE_UPPER);
		} elseif ($mode == 'min') {
			return mb_convert_case(($mot), MB_CASE_LOWER);
		} elseif ($mode == 'majf') {
			$temp = mb_convert_case(($mot), MB_CASE_LOWER);
			return mb_convert_case(mb_substr($temp, 0, 1), MB_CASE_UPPER) . mb_substr($temp, 1);
		} elseif ($mode == 'majf2') {
			$temp = mb_convert_case(($mot), MB_CASE_LOWER);
			return mb_convert_case(($temp), MB_CASE_TITLE);
		}
	} else {
		$str = ensure_ascii($mot);
		if ($mode == 'maj') {
			return strtoupper($str);
		} elseif ($mode == 'min') {
			return strtolower($str);
		} elseif ($mode == 'majf') {
			if (mb_strlen($str) > 1) {
				return strtoupper(mb_substr($str, 0, 1)) . strtolower(substr($str, 1));
			} else {
				return strtoupper($str);
			}
		} elseif ($mode == 'majf2') {
			$chaine = "";
			$tab = explode(" ", $str);
			for ($i = 0; $i < count($tab); $i++) {
				if ($i > 0) {
					$chaine .= " ";
				}
				$tab2 = explode("-", $tab[$i]);
				for ($j = 0; $j < count($tab2); $j++) {
					if ($j > 0) {
						$chaine .= "-";
					}
					if (mb_strlen($tab2[$j]) > 1) {
						$chaine .= mb_strtoupper(mb_substr($tab2[$j], 0, 1)) . strtolower(mb_substr($tab2[$j], 1));
					} else {
						$chaine .= mb_strtoupper($tab2[$j]);
					}
				}
			}
			return $chaine;
		}
	}
	throw new Exception('Parametre ' . $mode . ' non reconnu');
}

/**
 * Retourne une chaine utf-8 passée en minuscules avec casse_mot()
 * Fonction destinée à remplacer rapidement les appels strtolower() dans les pages
 *
 * @param string $mot chaine à modifier
 * @throws \Exception
 * @return string chaine mise en forme
 */
function my_strtolower($mot) {
	return casse_mot($mot, 'min');
}

/**
 * Retourne une chaine utf-8 passée en majuscules avec casse_mot()
 * Fonction destinée à remplacer rapidement les appels strtoupper() dans les pages
 *
 * @param string $mot chaine à modifier
 * @throws \Exception
 * @return string chaine mise en forme
 */
function my_strtoupper($mot) {
	return casse_mot($mot);
}

/**
 * Renvoie le nom et le prénom d'un élève
 *
 * ou civilité nom prénom (non-élève) si ce n'est pas un élève
 *
 * @param string $login_ele Login de l'élève
 * @param string $mode si 'avec_classe' on retourne aussi la(les) classe(s)
 * @return string
 * @see civ_nom_prenom()
 * @see get_class_from_ele_login()
 * @see casse_mot()
 * @throws \Exception
 */
function get_nom_prenom_eleve($login_ele, $mode = 'simple') {
	global $mysqli;
	$sql = "SELECT nom,prenom FROM eleves WHERE login='$login_ele';";

	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		// Si ce n'est pas un élève, c'est peut-être un utilisateur prof, cpe, responsable,...
		$sql_ = "SELECT 1=1 FROM utilisateurs WHERE login='$login_ele';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$res->close();
			return civ_nom_prenom($login_ele) . " (non-élève)";
		} else {
			return "Elève inconnu ($login_ele)";
		}
	} else {
		$lig = $res->fetch_object();
		$ajout = "";
		if ($mode == 'avec_classe') {
			$tmp_tab_clas = get_class_from_ele_login($login_ele);
			if ((isset($tmp_tab_clas['liste'])) && ($tmp_tab_clas['liste'] != '')) {
				$ajout = " (" . $tmp_tab_clas['liste'] . ")";
			}
		}
		$res->close();
		return casse_mot($lig->nom) . " " . casse_mot($lig->prenom, 'majf2') . $ajout;
	}

}

/**
 * Renvoie le nom et le prénom d'un élève
 *
 * @param string $ele_id ele_id de l'élève
 * @param string $mode si 'avec_classe' on retourne aussi la(les) classe(s)
 * @return string
 * @see civ_nom_prenom()
 * @see get_class_from_ele_login()
 * @see casse_mot()
 * @throws \Exception
 */
function get_nom_prenom_eleve_from_ele_id($ele_id, $mode = 'simple') {
	global $mysqli;
	$sql = "SELECT login, nom,prenom FROM eleves WHERE ele_id='$ele_id';";

	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		return "Elève inconnu ($ele_id)";
	} else {
		$lig = $res->fetch_object();
		$ajout = "";
		if ($mode == 'avec_classe') {
			$tmp_tab_clas = get_class_from_ele_login($lig->login);
			if ((isset($tmp_tab_clas['liste'])) && ($tmp_tab_clas['liste'] != '')) {
				$ajout = " (" . $tmp_tab_clas['liste'] . ")";
			}
		}
		$res->close();
		return casse_mot($lig->nom) . " " . casse_mot($lig->prenom, 'majf2') . $ajout;
	}
}

/**
 * Retourne une commune à partir de son code insee
 *
 * $mode :
 * - 0 -> la commune
 * - 1 -> la commune (<em>le département</em>)
 * - 2 -> la commune (le département)
 *
 * @param string $code_commune_insee
 * @param int $mode
 * @return string La commune
 */
function get_commune($code_commune_insee, $mode = 0) {
	global $mysqli;
	$retour = "";

	if (strstr($code_commune_insee, '@')) {
		// On a affaire à une commune étrangère
		$tmp_tab = explode('@', $code_commune_insee);
		$sql = "SELECT * FROM pays WHERE code_pays='$tmp_tab[0]';";
		//echo "$sql<br />";

		$res_pays = mysqli_query($mysqli, $sql);
		if ($res_pays->num_rows == 0) {
			$retour = stripslashes($tmp_tab[1]) . " ($tmp_tab[0])";
		} else {
			$lig_pays = $res_pays->fetch_object();
			$res_pays->close();
			$retour = stripslashes($tmp_tab[1]) . " (" . $lig_pays->nom_pays . ")";
		}
	} else {
		$sql = "SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$lig = $res->fetch_object();
			if ($mode == 0) {
				$retour = $lig->commune;
			} elseif ($mode == 1) {
				$retour = $lig->commune . " (<em>" . $lig->departement . "</em>)";
			} elseif ($mode == 2) {
				$retour = $lig->commune . " (" . $lig->departement . ")";
			}
			$res->close();
		}
	}
	return $retour;
}

/**
 * Renvoie civilite nom prénom d'un utilisateur avec compte (table 'utilisateurs')
 *
 * @param string $login Login de l'utilisateur recherché
 * @param string $mode si 'prenom' inverse le nom et le prénom
 * @param string $avec_statut avec affichage ou non du statut entre parenthèses
 *
 * @return string civilite nom prénom de l'utilisateur
 * @throws \Exception
 */
function civ_nom_prenom($login, $mode = 'prenom', $avec_statut = "n", $retourner_login_si_non_trouve = "n") {
	global $mysqli;
	$retour = "";
	if ($login != "") {
		$sql = "SELECT nom,prenom,civilite,statut FROM utilisateurs WHERE login='$login';";
		$res_user = mysqli_query($mysqli, $sql);
		if ($res_user->num_rows > 0) {
			$lig_user = $res_user->fetch_object();
			if ($lig_user->civilite != "") {
				$retour .= $lig_user->civilite . " ";
			}
			if ($mode == 'prenom') {
				$retour .= my_strtoupper($lig_user->nom) . " " . casse_mot($lig_user->prenom, 'majf2');
			} else {
				// Initiale
				$retour .= my_strtoupper($lig_user->nom) . " " . my_strtoupper(mb_substr($lig_user->prenom, 0, 1));
			}
			if ($avec_statut == 'y') {
				if ($lig_user->statut == 'autre') {
					$sql = "SELECT ds.id, ds.nom_statut FROM droits_statut ds, droits_utilisateurs du
						WHERE du.login_user = '" . $login . "'
							AND du.id_statut = ds.id;";
					$res_statut = mysqli_query($mysqli, $sql);
					if ($res_statut->num_rows > 0) {
						$lig_statut = $res_statut->fetch_object();
						$retour .= " ($lig_statut->nom_statut)";
						$res_statut->close();
					}
				} else {
					$retour .= " ($lig_user->statut)";
				}
			}
			$res_user->close();
		} else {
			// Chercher si c'est un élève, ou un parent avec login dont le compte aurait été supprimé?
			$sql = "SELECT nom,prenom,sexe FROM eleves WHERE login='$login';";
			$res_user = mysqli_query($mysqli, $sql);
			if ($res_user->num_rows > 0) {
				$lig_user = $res_user->fetch_object();
				if ($mode == 'prenom') {
					$retour .= my_strtoupper($lig_user->nom) . " " . casse_mot($lig_user->prenom, 'majf2');
				} else {
					// Initiale
					$retour .= my_strtoupper($lig_user->nom) . " " . my_strtoupper(mb_substr($lig_user->prenom, 0, 1));
				}
				if ($avec_statut == 'y') {
					$retour .= " (eleve)";
				}
				$res_user->close();
			} else {
				$sql = "SELECT nom,prenom,civilite FROM resp_pers WHERE login='$login';";
				$res_user = mysqli_query($mysqli, $sql);
				if ($res_user->num_rows > 0) {
					$lig_user = $res_user->fetch_object();
					if ($lig_user->civilite != "") {
						$retour .= $lig_user->civilite . " ";
					}
					if ($mode == 'prenom') {
						$retour .= my_strtoupper($lig_user->nom) . " " . casse_mot($lig_user->prenom, 'majf2');
					} else {
						// Initiale
						$retour .= my_strtoupper($lig_user->nom) . " " . my_strtoupper(mb_substr($lig_user->prenom, 0, 1));
					}
					if ($avec_statut == 'y') {
						$retour .= " (responsable)";
					}
					$res_user->close();
				}
			}
		}
		if (($retour == "") && ($retourner_login_si_non_trouve == "y")) {
			$retour = $login;
		}
	}
	return $retour;
}

/**
 * Renvoie civilite nom prénom d'un responsable
 *
 * @param string $pers_id pers_id de l'utilisateur recherché
 * @param string $mode si 'prenom' inverse le nom et le prénom
 *
 * @return string civilite nom prénom de l'utilisateur
 */
function civ_nom_prenom_from_pers_id($pers_id, $mode = 'prenom') {
	global $mysqli;
	$retour = "";
	$sql = "SELECT nom,prenom,civilite FROM resp_pers WHERE pers_id='$pers_id';";
	$res_user = mysqli_query($mysqli, $sql);
	if ($res_user->num_rows > 0) {
		$lig_user = $res_user->fetch_object();
		if ($lig_user->civilite != "") {
			$retour .= $lig_user->civilite . " ";
		}
		if ($mode == 'prenom') {
			$retour .= my_strtoupper($lig_user->nom) . " " . casse_mot($lig_user->prenom, 'majf2');
		} else {
			// Initiale
			$retour .= my_strtoupper($lig_user->nom) . " " . my_strtoupper(mb_substr($lig_user->prenom, 0, 1));
		}
		$res_user->close();
	}
	return $retour;
}

/**
 *Enleve le numéro des titres numérotés ("1. Titre" -> "Titre")
 *
 * Exemple :  "12. Titre"  donne "Titre"
 * @param string $texte Le titre de départ
 * @return string  Le titre formaté
 */
function supprimer_numero($texte) {
	return preg_replace(",^[[:space:]]*([0-9]+)([.)])[[:space:]]+,S", "", $texte);
}


/**
 * Teste si style_screen_ajout.css existe et est accessible en écriture
 *
 * @return boolean TRUE si on peut écrire dans le fichier
 */
function test_ecriture_style_screen_ajout() {
	$nom_fichier = 'style_screen_ajout.css';
	$f = @fopen("../" . $nom_fichier, "a+");
	if ($f) {
		$ecriture = fwrite($f, "/* Test d'ecriture dans $nom_fichier */\n");
		fclose($f);
		if ($ecriture) {
			return TRUE;
		} else {
			return FALSE;
		}
	} else {
		return FALSE;
	}
}

/**********************************************************************************************
 *                                  Fonctions Trombinoscope
 **********************************************************************************************/


/**
 * Ajoute au début d'un nom de fichier une chaîne de 'encodage_photos_eleves_longueur' caractères pseudo alétaoires
 * le but étant d'empêcher l'accès aux photos élèves.
 *
 * Renvoie le nom de fichier modifié si les valeurs 'encodage_photos_eleves_alea'
 * et 'encodage_photos_eleves_longueur' sont définies
 * dans la table 'setting', sinon renvoie le nom de fichier inchangé.
 *
 * @param string $nom_photo le nom du fichier SANS extension
 * @return string le nom du fichier modifié
 *
 */
function encode_nom_photo($nom_photo) {
	if (getSettingValue('encodage_photos_eleves_alea') === null || getSettingValue('encodage_photos_eleves_longueur') === null) return $nom_photo;
	else return substr(md5(getSettingValue('encodage_photos_eleves_alea') . $nom_photo), 0, getSettingValue('encodage_photos_eleves_longueur')) . $nom_photo;
}

/**
 * Supprime 'encodage_photos_eleves_longueur' au début d'un nom de fichier
 *
 * Renvoie le nom de fichier modifié si
 * et 'encodage_photos_eleves_longueur' est définie
 * dans la table 'setting', sinon renvoie le nom de fichier inchangé.
 *
 * @param string $nom_photo le nom du fichier
 * @return string le nom du fichier modifié
 *
 */
function des_encode_nom_photo($nom_photo) {
	if (getSettingValue('encodage_photos_eleves_longueur') === null) return $nom_photo;
	else return substr($nom_photo, getSettingValue('encodage_photos_eleves_longueur'));
}

/**
 * Renvoie le nom de la photo de l'élève ou du prof
 *
 * Renvoie NULL si :
 *
 * - le module trombinoscope n'est pas activé
 * - la photo n'existe pas.
 *
 * @param string $_elenoet_ou_login selon les cas, soit l'elenoet de l'élève soit le login du professeur
 * @param string $repertoire "eleves" ou "personnels"
 * @param int $arbo niveau d'aborescence (1 ou 2).
 * @return string Le chemin vers la photo ou NULL
 * @see getSettingValue()
 */
function nom_photo($_elenoet_ou_login, $repertoire = "eleves", $arbo = 1) {
	global $mysqli;

	if ($arbo == 2) {
		$chemin = "../";
	} else {
		$chemin = "";
	}
	if (($repertoire != "eleves") and ($repertoire != "personnels")) {
		return NULL;
		die();
	}
	if (getSettingValue("active_module_trombinoscopes") != 'y') {
		return NULL;
		die();
	}

	$photo = NULL;

	// En multisite, on ajoute le répertoire RNE
	if (isset($GLOBALS['multisite']) and $GLOBALS['multisite'] == 'y') {
		// On récupère le RNE de l'établissement
		$repertoire2 = $_COOKIE['RNE'] . "/";
	} else {
		$repertoire2 = "";
	}


	// Cas des élèves
	if ($repertoire == "eleves") {

		if ($_elenoet_ou_login != '') {
			//echo "\$_elenoet_ou_login=$_elenoet_ou_login<br />";
			// on vérifie si la photo existe

			// En multisite, on recherche aussi avec les logins
			if (isset($GLOBALS['multisite']) and $GLOBALS['multisite'] == 'y') {
				// On récupère le login de l'élève
				$sql = 'SELECT login FROM eleves WHERE elenoet = "' . $_elenoet_ou_login . '"';
				$query = mysqli_query($mysqli, $sql);
				$obj = $query->fetch_object();
				$_elenoet_ou_login = $obj->login;
			}
			//echo "\$_elenoet_ou_login=$_elenoet_ou_login<br />";

			if (file_exists($chemin . "../photos/" . $repertoire2 . "eleves/" . encode_nom_photo($_elenoet_ou_login) . ".jpg")) {
				$photo = $chemin . "../photos/" . $repertoire2 . "eleves/" . encode_nom_photo($_elenoet_ou_login) . ".jpg";
			} else {
				if (file_exists($chemin . "../photos/" . $repertoire2 . "eleves/" . sprintf("%05d", encode_nom_photo($_elenoet_ou_login)) . ".jpg")) {
					$photo = $chemin . "../photos/" . $repertoire2 . "eleves/" . sprintf("%05d", encode_nom_photo($_elenoet_ou_login)) . ".jpg";
				} else {
					for ($i = 0; $i < 5; $i++) {
						if (mb_substr(encode_nom_photo($_elenoet_ou_login), $i, 1) == "0") {
							$test_photo = mb_substr($_elenoet_ou_login, $i + 1);
							if (($test_photo != '') && (file_exists($chemin . "../photos/" . $repertoire2 . "eleves/" . $test_photo . ".jpg"))) {
								$photo = $chemin . "../photos/" . $repertoire2 . "eleves/" . $test_photo . ".jpg";
								break;
							}
						}
					}
				}
			}

		}
		// DEBUG
		/*
		if(isset($photo)) {
			echo "\$photo=$photo<br />";
		}
		*/
	} // Cas des non-élèves
	else {

		$_elenoet_ou_login = md5(mb_strtolower($_elenoet_ou_login));
		if (file_exists($chemin . "../photos/" . $repertoire2 . "personnels/$_elenoet_ou_login.jpg")) {
			$photo = $chemin . "../photos/" . $repertoire2 . "personnels/$_elenoet_ou_login.jpg";
		} else {
			$photo = NULL;
		}
	}
	return $photo;
}


// 20240411
/**
 * Retourne le type de l'image
 *
 * @param string $file_source fichier à identifier
 * @return type du fichier s'il est identifié, false sinon
 */
function get_image_type($file_source) {
	if (function_exists('exif_imagetype')) {
		return exif_imagetype($file_source);
	}
	else {
		if ( ( list($width, $height, $type, $attr) = getimagesize( $file_source ) ) !== false ) {
			return $type;
		}
		else {
			return false;
		}
	}
}


/**
 * Redimensionne un fichier photo JPG en conservant son ratio d'origine
 * Si les dimensions du fichier source sont plus petites que celles du
 * fichier destination alors le fichier source est inclus dans le fichier
 * destination afin de ne pas perdre en qualité suite à un agrandissement
 * de la photo
 *
 * @param string $file_source fichier à redimensionner
 * @param integer $largeur_destination largeur à obtenir
 * @param integer $$hauteur_destination hauteur à obtenir
 * @param integer $angle_rotation rotation à appliquer à l'image (facultatif)
 * @return true si redimensionnement OK, false sinon ou si inutile de redimenssionner
 */
function redim_photo($file_source, $largeur_destination, $hauteur_destination, $angle_rotation = 0) {
	if (!is_file($file_source)) return false;

	// 20240411
	//echo "<p>".get_image_type($file_source)."</p>";
	if(!$img_type=get_image_type($file_source)) return false;
	if(($img_type=='IMAGETYPE_JPEG')||($img_type=='2')) {
		$source = imagecreatefromjpeg($file_source);
	}
	elseif(($img_type=='IMAGETYPE_PNG')||($img_type=='3')) {
		$source = imagecreatefrompng($file_source);
	}
	/*
	// Pb dans modify_eleve.php avec les images webp
	// La copie de tmp vers dest ne se fait pas ???
	elseif(($img_type=='IMAGETYPE_WEBP')||($img_type=='18')) {
		$source = imagecreatefromwebp($file_source);
	}
	*/
	else {
		return false;
	}

	if ($source===false) return false;

	if ($angle_rotation != 0) $source = imagerotate($source, -$angle_rotation, 0xFFFFFF);
	if ($source === false) return false;

	$destination = imagecreatetruecolor($largeur_destination, $hauteur_destination);
	if ($destination === false) return false;
	$blanc = imagecolorallocate($destination, 0xFF, 0xFF, 0xFF);
	if ($blanc === false) return false;

	if (!imagefill($destination, 0, 0, $blanc)) return false;

	$largeur_source = imagesx($source);
	if ($largeur_source === false) return false;
	$hauteur_source = imagesy($source);
	if ($hauteur_source === false) return false;

	if ($largeur_source == 0 || $hauteur_source == 0) return false;
	if ($largeur_source == $largeur_destination && $hauteur_source == $hauteur_destination) return true;

	$ratio_lh_source = $largeur_source / $hauteur_source;
	$ratio_lh_destination = $largeur_destination / $hauteur_destination;

	if ($ratio_lh_source < $ratio_lh_destination) {
		$dest_l = (int)($hauteur_destination * $ratio_lh_source);
		if ($dest_l > $largeur_source) $dest_l = $largeur_source;
		$dest_x = (int)(($largeur_destination - $dest_l) / 2);
		$dest_h = $hauteur_destination;
		if ($dest_h > $hauteur_source) $dest_h = $hauteur_source;
		$dest_y = (int)(($hauteur_destination - $dest_h) / 2);
	} else {
		$dest_h = (int)($largeur_destination / $ratio_lh_source);
		if ($dest_h > $hauteur_source) $dest_h = $hauteur_source;
		$dest_y = (int)(($hauteur_destination - $dest_h) / 2);
		$dest_l = $largeur_destination;
		if ($dest_l > $largeur_source) $dest_l = $largeur_source;
		$dest_x = (int)(($largeur_destination - $dest_l) / 2);
	}

	if (!imagecopyresampled($destination, $source, $dest_x, $dest_y, 0, 0, $dest_l, $dest_h, $largeur_source, $hauteur_source)) return false;

	if (!imagejpeg($destination, $file_source, 100)) return false;
	imagedestroy($destination);
	return true;
}

/**********************************************************************************************
 *                               Fin Fonctions Trombinoscope
 **********************************************************************************************/

/**********************************************************************************************
 *                                   Fil d'Ariane
 **********************************************************************************************/
/**
 * gestion du fil d'ariane en remplissant le tableau $_SESSION['ariane']
 * @param string $lien page atteinte par le lien
 * @param string $texte texte à afficher dans le fil d'ariane
 * @return boolean True si tout s'est bien passé, False sinon
 */
function suivi_ariane($lien, $texte) {
	if (!isset($_SESSION['ariane'])) {
		$_SESSION['ariane']['lien'][] = $lien;
		$_SESSION['ariane']['texte'][] = $texte;
		return TRUE;
	} else {
		$trouve = FALSE;
		foreach ($_SESSION['ariane']['lien'] as $index => $lienActuel) {
			if ($trouve) {
				unset ($_SESSION['ariane']['lien'][$index]);
				unset ($_SESSION['ariane']['texte'][$index]);
			} else {
				if ($lienActuel == $lien)
					$trouve = TRUE;
			}
		}
		unset ($index, $lienActuel);
		if (!$trouve) {
			$_SESSION['ariane']['lien'][] = $lien;
			$_SESSION['ariane']['texte'][] = $texte;
		}
		return TRUE;
	}
}

/**
 * Affiche le fil d'Ariane
 *
 * une validation sera demandée en cas de modification de la page si validation est à TRUE
 * et si le javascript est activé
 * @param <boolean> $validation validation si TRUE,
 * @param <texte> $themessage message à afficher lors de la confirmation
 */
//function affiche_ariane($validation= FALSE,$themessage="" ){
function affiche_ariane($validation = FALSE) {
	global $themessage;
	if ($themessage != "") {
		$validation = TRUE;
	}
	if (isset($_SESSION['ariane'])) {
		echo "<p class='ariane'>";
		foreach ($_SESSION['ariane']['lien'] as $index => $lienActuel) {
			if ($index != "0") {
				echo " &gt;&gt; ";
			}
			if ($validation) {
				echo "<a class='bold' href='" . $lienActuel . "' onclick='return confirm_abandon (this, change, \"" . $themessage . "\")' >";
			} else {
				echo "<a class='bold' href='" . $lienActuel . "' >";
			}
			echo $_SESSION['ariane']['texte'][$index];
			echo " </a>";
		}
		unset ($index, $lienActuel);
		echo "</p>";
	}
}

/**********************************************************************************************
 *                               Fin Fil d'Ariane
 **********************************************************************************************/
/**********************************************************************************************
 *                               Manipulation de fichiers
 **********************************************************************************************/

/**
 * Renvoie le chemin relatif pour remonter à la racine du site
 * @param int $niveau niveau dans l'arborescence
 * @return string chemin relatif vers la racine
 */
function path_niveau($niveau = 1) {
	switch ($niveau) {
		case 0:
			$path = "./";
			break;
		case 1:
			$path = "../";
			break;
		case 2:
			$path = "../../";
		default:
			$path = "../";
	}
	return $path;
}

/**
 * Fonction call back pour dés-encoder les noms de fichier photo élève
 * avant archivage avec PCLZIP
 * Voir : http://www.phpconcept.net/pclzip/user-guide/50
 */
function des_encode_4_PCLZIP($p_event, &$p_header) {
	$info = pathinfo($p_header['stored_filename']);
	if (isset($info['dirname']) && isset($info['extension']) && (strtolower($info['extension']) == 'jpg') && substr(strrchr($info['dirname'], '/'), 1) == 'eleves') {
		$p_header['stored_filename'] = $info['dirname'] . '/' . des_encode_nom_photo($info['basename']);
		return 1;
	} elseif (isset($info['dirname']) && isset($info['extension']) && (strtolower($info['extension']) == 'jpg') && substr(strrchr($info['dirname'], '/'), 1) == 'personnels') {
		return 1;
	} else return 0;
}

/**
 * Crée une archive Zip des dossiers documents ou photos
 *
 * @param string $dossier_a_archiver limité à documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return string message d'erreur, vide si aucune erreur
 * @see cree_zip_archive_msg()
 */
function cree_zip_archive_avec_msg_erreur($dossier_a_archiver, $niveau = 1) {
	$path = path_niveau();
	$dirname = "backup/" . getSettingValue("backup_directory") . "/";
	if (!defined('PCLZIP_TEMPORARY_DIR') || constant('PCLZIP_TEMPORARY_DIR') != $path . $dirname) {
		@define('PCLZIP_TEMPORARY_DIR', $path . $dirname);
	}

	require_once($path . 'lib/pclzip.lib.php');

	if (isset($dossier_a_archiver)) {
		$suffixe_zip = "_le_" . date("Y_m_d_\a_H\hi_s");
		switch ($dossier_a_archiver) {
			case "documents":
				$chemin_stockage = $path . $dirname . "_cdt" . $suffixe_zip . ".zip"; //l'endroit où sera stockée l'archive
				$dossier_a_traiter = $path . 'documents/'; //le dossier à traiter
				$dossier_dans_archive = 'documents'; //le nom du dossier dans l'archive créée
				break;
			case "photos":
				$chemin_stockage = $path . $dirname . "_photos" . $suffixe_zip . ".zip";
				$dossier_a_traiter = $path . 'photos/'; //le dossier à traiter
				if (isset($GLOBALS['multisite']) and $GLOBALS['multisite'] == 'y') {
					if ((isset($_COOKIE['RNE'])) && ($_COOKIE['RNE'] != '')) $dossier_a_traiter .= $_COOKIE['RNE'] . "/";
					else return "RNE invalide&nbsp;:&nbsp;" . $_COOKIE['RNE'];
				}
				$dossier_dans_archive = 'photos'; //le nom du dossier dans l'archive créée
				break;
			default:
				$chemin_stockage = '';
		}

		if ($chemin_stockage != '') {
			$archive = new PclZip($chemin_stockage);
			switch ($dossier_a_archiver) {
				case "photos":
					$v_list = $archive->create($dossier_a_traiter,
						PCLZIP_OPT_REMOVE_PATH, $dossier_a_traiter,
						PCLZIP_OPT_ADD_PATH, $dossier_dans_archive,
						PCLZIP_CB_PRE_ADD, 'des_encode_4_PCLZIP',
						PCLZIP_OPT_NO_COMPRESSION);
					break;
				default:
					$v_list = $archive->create($dossier_a_traiter,
						PCLZIP_OPT_REMOVE_PATH, $dossier_a_traiter,
						PCLZIP_OPT_ADD_PATH, $dossier_dans_archive);
			}
			if ($v_list == 0) {
				return "Erreur : " . $archive->errorInfo(TRUE);
			} else {
				return "";
			}
		}
	}
}


/**
 * Crée une archive Zip des dossiers documents ou photos
 *
 * @param string $dossier_a_archiver limité à documents ou photos
 * @param int $niveau niveau dans l'arborescence de la page appelante, racine = 0
 * @return boolean
 * @see cree_zip_archive_msg()
 */
function cree_zip_archive($dossier_a_archiver, $niveau = 1) {
	return (cree_zip_archive_avec_msg_erreur($dossier_a_archiver, $niveau) == "") ? TRUE : FALSE;
}

/**
 * Déplace un fichier de $source vers $dest
 * @param string $source : emplacement du fichier à déplacer
 * @param string $dest : Nouvel emplacement du fichier
 * @return bool
 */
function deplacer_upload($source, $dest) {
	$ok = @copy($source, $dest);
	if (!$ok) $ok = (@move_uploaded_file($source, $dest));
	return $ok;
}

/**
 * Télécharge un fichier dans $dirname après avoir nettoyer son nom
 *
 * si tout se passe bien :
 * $sav_file['name']=my_ereg_replace("[^.a-zA-Z0-9_=-]+", "_", $sav_file['name'])
 * @param array $sav_file tableau de type $_FILE["nom_du_fichier"]
 * @param string $dirname
 * @return string ok ou message d'erreur
 * @see deplacer_upload()
 */
function telecharge_fichier($sav_file, $dirname, $ext = "", $type = "") {
	if (!isset($sav_file['tmp_name']) or ($sav_file['tmp_name'] == '')) {
		return ("Erreur de téléchargement.");
	} else if (!file_exists($sav_file['tmp_name'])) {
		return ("Erreur de téléchargement 2.");
	} else if (($ext != "") && (!preg_match('/' . $ext . '$/i', $sav_file['name']))) {
		return ("Erreur : seuls les fichiers ayant l'extension ." . $ext . " sont autorisés.");
		//} else if ($sav_file['type']!=$type ){
	} else if (($type != "") && (strripos($type, $sav_file['type']) === false)) {
		return ("Erreur : seuls les fichiers de type '" . $type . "' sont autorisés<br />Votre fichier est de type " . $sav_file['type']);
	} else {
		$nom_corrige = preg_replace("/[^.a-zA-Z0-9_=-]+/", "_", $sav_file['name']);
		if (!deplacer_upload($sav_file['tmp_name'], $dirname . "/" . $nom_corrige)) {
			return ("Problème de transfert : le fichier n'a pas pu être transféré sur le répertoire " . $dirname);
		} else {
			$sav_file['name'] = $nom_corrige;
			return ("ok");
		}
	}
}

/**
 * Extrait une archive Zip
 * @param string $fichier le nom du fichier à dézipper
 * @param string $repertoire le répertoire de destination
 * @param int $niveau niveau dans l'arborescence de la page appelante
 * @return string ok ou message d'erreur
 */
function dezip_PclZip_fichier($fichier, $repertoire, $niveau = 1) {
	$path = path_niveau();
	require_once($path . 'lib/pclzip.lib.php');
	$archive = new PclZip($fichier);
	//if ($archive->extract() == 0) {
	if ($archive->extract(PCLZIP_OPT_PATH, $repertoire) == 0) {
		return "Une erreur a été rencontrée lors de l'extraction du fichier zip";
	} else {
		return "ok";
	}
}

/**********************************************************************************************
 *                              Fin Manipulation de fichiers
 **********************************************************************************************/
/**
 * Vérifie qu'un statut à les droits sur une page
 *
 * @param string $id le lien vers la page à tester
 * @param string $statut Le statut à tester
 * @return int
 */
function check_droit_acces($id, $statut) {
	global $mysqli;
	$tab_id = explode("?", $id);
	$sql = "SELECT " . $statut . " as droit FROM droits WHERE id='$tab_id[0]'";
	$query_droits = mysqli_query($mysqli, $sql);
	$obj = $query_droits->fetch_object();
	$droit = $obj->droit;
	$query_droits->close();
	if ($droit == "V") {
		return "1";
	} else {
		return "0";
	}
}

/**
 * Renvoie des balises option contenant les élèves
 *
 * Renvoie une chaine contenant une balise option par élève à insérer dans un select
 *
 * @param int $id_classe Id de la classe
 * @param string $login_eleve_courant Login de l'élève qui sera sélectionné par défaut
 * @param request $sql_ele requête à utiliser
 * @return string Les balises options
 */
function lignes_options_select_eleve($id_classe, $login_eleve_courant, $sql_ele = "") {
	global $mysqli;
	global $indice_ele_courant_lignes_options_select_eleve;
	global $login_ele_prec_lignes_options_select_eleve;
	global $login_ele_suiv_lignes_options_select_eleve;

	if ($sql_ele != "") {
		$sql = $sql_ele;
	} else {
		$sql = "SELECT DISTINCT jec.login,e.nom,e.prenom FROM j_eleves_classes jec, eleves e
			WHERE jec.login=e.login AND
			jec.id_classe='$id_classe'
				ORDER BY e.nom,e.prenom";
	}
	//echo "$sql<br />";
	//echo "\$login_eleve=$login_eleve<br />";
	$res_ele_tmp = mysqli_query($mysqli, $sql);
	$chaine_options_login_eleves = "";
	$cpt_eleve = 0;
	$num_eleve = -1;
	if ($res_ele_tmp->num_rows > 0) {
		$login_eleve_prec = "";
		$login_eleve_suiv = "";
		$temoin_tmp = 0;
		while ($lig_ele_tmp = $res_ele_tmp->fetch_object()) {
			if ($lig_ele_tmp->login == $login_eleve_courant) {
				$chaine_options_login_eleves .= "<option value='$lig_ele_tmp->login' selected='selected'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";

				$num_eleve = $cpt_eleve;

				$temoin_tmp = 1;
				if ($lig_ele_tmp = $res_ele_tmp->fetch_object()) {
					$login_eleve_suiv = $lig_ele_tmp->login;
					$chaine_options_login_eleves .= "<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
				} else {
					$login_eleve_suiv = "";
				}
			} else {
				$chaine_options_login_eleves .= "<option value='$lig_ele_tmp->login'>$lig_ele_tmp->nom $lig_ele_tmp->prenom</option>\n";
			}

			if ($temoin_tmp == 0) {
				$login_eleve_prec = $lig_ele_tmp->login;
			}
			$cpt_eleve++;
		}
		$res_ele_tmp->close();
	}

	$indice_ele_courant_lignes_options_select_eleve = $num_eleve;
	$login_ele_prec_lignes_options_select_eleve = $login_eleve_prec;
	$login_ele_suiv_lignes_options_select_eleve = $login_eleve_suiv;

	return $chaine_options_login_eleves;
}

/**
 *Vérifie si un utilisateur est prof principal (gepi_prof_suivi)
 *
 * $id_classe : identifiant de la classe (si vide, on teste juste si le prof est PP
 * (éventuellement pour un élève particulier si login_eleve est non vide))
 *
 * $login_eleve : login de l'élève à tester (si vide, on teste juste si le prof est PP
 * (éventuellement pour la classe si id_classe est non vide))
 *
 * @param type string $login_prof login de l'utilisateur à tester
 * @param type integer $id_classe identifiant de la classe
 * @param type string $login_eleve login de l'élève
 * @param type integer $num_periode numéro de la période
 * @param type string $login_resp login d'un responsable de l'élève
 * @return boolean
 */
function is_pp($login_prof, $id_classe = "", $login_eleve = "", $num_periode = "", $login_resp = "") {
	global $mysqli;
	$retour = FALSE;

	if ($login_eleve == '') {
		$sql = "SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if ($id_classe != "") {
			$sql .= "id_classe='$id_classe' AND ";
		}
		$sql .= "professeur='$login_prof';";
	} elseif ($login_resp != "") {
		$sql = "SELECT 1=1 FROM j_eleves_professeurs jep, 
							eleves e, 
							responsables2 r, 
							resp_pers rp 
						WHERE jep.professeur='" . $login_prof . "' AND 
							jep.login=e.login AND 
							e.ele_id=r.ele_id AND 
							r.pers_id=rp.pers_id AND 
							rp.login='$login_resp'";
	} else {
		$sql = "SELECT 1=1 FROM j_eleves_professeurs WHERE ";
		if ($id_classe != "") {
			$sql .= "id_classe='$id_classe' AND ";
		}
		$sql .= "professeur='$login_prof' AND login='$login_eleve';";
	}
	//echo "$sql<br />";
	$resultat = mysqli_query($mysqli, $sql);
	if ($resultat->num_rows > 0) {
		$resultat->close();
		$retour = TRUE;
	}

	return $retour;
}

/**
 * Récupère le tableau des classes/élèves dont un utilisateur est prof principal (gepi_prof_suivi)
 *
 * @param type string $login_prof login de l'utilisateur à tester
 *
 * @return array Tableau d'indices ['login'][] et ['id_classe'][] et ['classe'][]
 */
function get_tab_ele_clas_pp($login_prof) {
	global $mysqli;
	$tab = array();
	$tab['login'] = array();
	$tab['id_classe'] = array();

	$sql = "SELECT DISTINCT jep.login FROM j_eleves_professeurs jep, eleves e, j_eleves_classes jec, classes c WHERE jep.professeur='$login_prof' AND jep.login=e.login AND jec.login=e.login AND jec.id_classe=c.id AND jep.id_classe=jec.id_classe ORDER BY c.classe, e.nom, e.prenom;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab['login'][] = $lig->login;
		}
		$res->close();
	}

	$sql = "SELECT DISTINCT jec.id_classe, c.classe FROM j_eleves_professeurs jep, j_eleves_classes jec, classes c WHERE jep.professeur='$login_prof' AND jep.login=jec.login AND jec.id_classe=c.id AND jep.id_classe=jec.id_classe ORDER BY c.classe;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab['id_classe'][] = $lig->id_classe;
			$tab['classe'][] = $lig->classe;
		}
		$res->close();
	}

	return $tab;
}

/**
 *Vérifie si un utilisateur est cpe de l'élève choisi ou de la classe choisie
 *
 * $login_eleve : login de l'élève à tester (si vide, on teste juste si le cpe
 *                est responsable d'au moins un élève de la classe $id_classe
 *
 * $id_classe : identifiant de la classe
 *              (pris en compte seulement si le login_eleve est vide)
 *
 * @param type $login_cpe login de l'utilisateur à tester
 * @param type $id_classe identifiant de la classe
 * @param type $login_eleve login de l'élève
 * @return boolean
 */
function is_cpe($login_cpe, $id_classe = "", $login_eleve = "") {
	global $mysqli;
	$retour = FALSE;
	if ($login_eleve != '') {
		$sql = "SELECT 1=1 FROM j_eleves_cpe WHERE cpe_login='$login_cpe' AND e_login='$login_eleve';";
	} elseif ($id_classe != '') {
		$sql = "SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=jecpe.e_login AND jecpe.cpe_login='$login_cpe';";
	} else {
		$sql = "SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='$login_cpe';";
	}
	if (isset($sql)) {
		$test = mysqli_query($mysqli, $sql);
		if ($test->num_rows > 0) {
			$test->close();
			$retour = TRUE;
		}
	}
	return $retour;
}


/**
 * Récupère le tableau des login CPE associés à une classe
 *
 * $id_classe : identifiant de la classe
 *              (si vide, on récupère tous les CPE de l'établissement)
 *
 * @param type $id_classe identifiant de la classe
 * @return array
 */
function tab_cpe($id_classe = '') {
	global $mysqli;
	$tab = array();
	if ((is_numeric($id_classe)) && ($id_classe > 0)) {
		$sql = "SELECT DISTINCT u.login FROM utilisateurs u, j_eleves_cpe jecpe, j_eleves_classes jec WHERE u.statut='cpe' AND u.etat='actif' AND u.login=jecpe.cpe_login AND jec.login=jecpe.e_login AND jec.id_classe='$id_classe' ORDER BY u.nom, u.prenom;";
	} else {
		$sql = "SELECT DISTINCT u.login FROM utilisateurs WHERE statut='cpe' AND etat='actif' ORDER BY nom, prenom;";
	}
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[] = $lig->login;
		}
		$res->close();
	}
	return $tab;
}

/**
 * Vérifie qu'un utilisateur a le droit de voir la page en lien
 *
 * @param string $id l'adresse de la page telle qu'enregistrée dans la table droits
 * @param string $statut le statut de l'utilisateur
 * @return entier 1 si l'utilisateur a le droit de voir la page 0 sinon
 * @TODO Je l'ai déjà vu au-dessus dans le fichier → function check_droit_acces($id,$statut) si $_SESSION['statut']!='autre'
 */
function acces($id, $statut) {
	global $mysqli;
	if ($_SESSION['statut'] != 'autre') {
		$tab_id = explode("?", $id);
		$sql = "SELECT " . $statut . " as droit FROM droits WHERE id='$tab_id[0]'";
		$query_droits = mysqli_query($mysqli, $sql);

		if (mysqli_num_rows($query_droits) == 0) {
			$droit = "F";
		} else {
			$obj = $query_droits->fetch_object();
			$droit = $obj->droit;
			$query_droits->close();
		}

		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	} else {
		// On teste avec WHERE ds.autorisation='V' parce que pour une même page on peut avoir plusieurs enregistrements dans les droits spéciaux:
		// Cas des cases EDT
		$sql = "SELECT ds.autorisation FROM `droits_speciaux` ds,  `droits_utilisateurs` du
					WHERE (ds.nom_fichier='" . $id . "'
						AND ds.id_statut=du.id_statut
						AND ds.autorisation='V'
						AND du.login_user='" . $_SESSION['login'] . "');";
		//echo "$sql<br />";
		$result = mysqli_query($mysqli, $sql);
		if (!$result) {
			return FALSE;
		} else {
			$row = $result->fetch_row();
			$result->close();
			if ($row[0] == 'V' || $row[0] == 'v') {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}

// Méthode pour envoyer les en-têtes HTTP nécessaires au téléchargement de fichier.
// Le content-type est obligatoire, ainsi que le nom du fichier.
/**
 * Méthode pour envoyer les en-têtes HTTP nécessaires au téléchargement de fichier.
 *
 * Le content-type est obligatoire, ainsi que le nom du fichier.
 * @param string $content_type type Mime
 * @param string $filename Nom du fichier
 * @param type $content_disposition Content-Disposition 'attachment' par défaut
 */
function send_file_download_headers($content_type, $filename, $content_disposition = 'attachment', $encodeSortie = 'utf-8') {

	header('Content-Encoding: ' . $encodeSortie);

	header('Content-Type: ' . $content_type);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	header('Content-Disposition: ' . $content_disposition . '; filename="' . $filename . '"');

	// Contournement d'un bug IE lors d'un téléchargement en HTTPS...
	if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)) {
		header('Pragma: private');
		header('Cache-Control: private, must-revalidate');
	} else {
		header('Pragma: no-cache');
	}
}

/**
 * Enregistrer une action à effectuer pour qu'elle soit par la suite affichée en page d'accueil pour tels ou tels utilisateurs
 *
 * @param string $titre titre de l'action/info
 * @param string $description le détail de l'action à effectuer avec autant que possible un lien vers la page et paramètres utiles pour l'action
 * @param string $destinataire le tableau des login ou statuts des utilisateurs pour lesquels l'affichage sera réalisé
 * @param string $mode vaut 'individu' si $destinataire désigne des logins et 'statut' si ce sont des statuts
 * @return int|boolean Id de l'enregistrement s'est bien effectué FALSE sinon
 *
 *
 */
function enregistre_infos_actions($titre, $texte, $destinataire, $mode) {
	global $mysqli;
	if (is_array($destinataire)) {
		$tab_dest = $destinataire;
	} else {
		$tab_dest = array($destinataire);
	}

	$sql = "INSERT INTO infos_actions SET titre='" . $mysqli->real_escape_string($titre) . "', description='" . $mysqli->real_escape_string($texte) . "', date=NOW();";
	$insert = mysqli_query($mysqli, $sql);
	if (!$insert) {
		return FALSE;
	} else {
		$id_info = $mysqli->insert_id;
		$return = $id_info;
		for ($loop = 0; $loop < count($tab_dest); $loop++) {
			$sql = "INSERT INTO infos_actions_destinataires SET id_info='$id_info', nature='$mode', valeur='$tab_dest[$loop]';";
			$insert = mysqli_query($mysqli, $sql);
			if (!$insert) {
				$return = FALSE;
			}
		}
		return $return;
	}
}

/**
 * Supprime une action à effectuer de la base
 *
 * @param type $id_info Id de l'action à effacer de la base
 * @param type $_login Login concerné par l'action à effacer de la base
 * @param type $_statut Statut concerné par l'action à effacer de la base
 * (on peut fournir login ou statut)
 *
 * @return boolean TRUE si l'action a été effacée de la base
 */
function del_info_action($id_info, $_login = "", $_statut = "") {
	global $mysqli;
	// Dans le cas des infos destinées à un statut... c'est le premier qui supprime qui vire pour tout le monde?
	// S'il s'agit bien de loguer des actions à effectuer... elle ne doit être effectuée qu'une fois.
	// Ou alors il faudrait ajouter des champs pour marquer les actions comme effectuées et n'afficher par défaut que les actions non effectuées

	if ($_SESSION['statut'] == "administrateur") {
		$sql = "SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info';";
	} else {
		$_login = $_SESSION['login'];
		$_statut = $_SESSION['statut'];

		$sql = "SELECT 1=1 FROM infos_actions_destinataires WHERE id_info='$id_info' AND ((nature='statut' AND valeur='" . $_statut . "') OR (nature='individu' AND valeur='" . $_login . "'));";
	}
	//echo "$sql<br />";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 0) {
		$sql = "DELETE FROM infos_actions_destinataires WHERE id_info='$id_info';";
		//echo "$sql<br />";
		$del = mysqli_query($mysqli, $sql);
		$test->close();
		if (!$del) {
			return FALSE;
		} else {
			$sql = "DELETE FROM infos_actions WHERE id='$id_info';";
			//echo "$sql<br />";
			$del = mysqli_query($mysqli, $sql);
			if (!$del) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}
}

/**
 * affiche sous la forme JJ/MM/AAAA la date de sortie d'un élève
 * présente dans la base comme un timestamp
 *
 * @param date $date_sortie date (timestamp)
 * @return string La date formatée
 */
function affiche_date_sortie($date_sortie, $heure = FALSE) {
	//
	$eleve_date_de_sortie_time = strtotime($date_sortie);
	//récupération du jour, du mois et de l'année
	$eleve_date_sortie_jour = date('j', $eleve_date_de_sortie_time);
	$eleve_date_sortie_mois = date('m', $eleve_date_de_sortie_time);
	$eleve_date_sortie_annee = date('Y', $eleve_date_de_sortie_time);
	$eleve_date_sortie_heure = date('H', $eleve_date_de_sortie_time);
	$eleve_date_sortie_minute = date('i', $eleve_date_de_sortie_time);
	if ($heure) {
		return sprintf("%02d", $eleve_date_sortie_jour) . "/" . sprintf("%02d", $eleve_date_sortie_mois) . "/" . $eleve_date_sortie_annee . " " . $eleve_date_sortie_heure . ":" . $eleve_date_sortie_minute;
	} else {
		return sprintf("%02d", $eleve_date_sortie_jour) . "/" . sprintf("%02d", $eleve_date_sortie_mois) . "/" . $eleve_date_sortie_annee;
	}
}

/**
 * Traite une chaine de caractères JJ/MM/AAAA vers un timestamp AAAA-MM-JJ 00:00:00
 *
 * @param string $date_sortie date (JJ/MM/AAAA)
 * @return date date (timestamp)
 */
function traite_date_sortie_to_timestamp($date_sortie) {
	//
	$date = explode("/", $date_sortie);
	$jour = $date[0];
	$mois = $date[1];
	$annee = $date[2];

	return $annee . "-" . $mois . "-" . $jour . " 00:00:00";
}

/**
 * Supprime les accès au cahier de textes
 *
 * @param int $id_acces Id du cahier de texte
 * @return boolean TRUE si tout c'est bien passé
 */
function del_acces_cdt($id_acces) {
	global $mysqli;

	$sql = "SELECT * FROM acces_cdt WHERE id='$id_acces';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$lig = $res->fetch_object();
		$res->close();
		$chemin = preg_replace("#/index.(html|php)#", "", $lig->chemin);
		if ((!preg_match("#^documents/acces_cdt_#", $chemin)) || (strstr($chemin, ".."))) {
			echo "<p><span style='color:red'>Chemin $chemin invalide</span></p>";
			return FALSE;
		} else {
			if ((isset($GLOBALS['multisite'])) && ($GLOBALS['multisite'] == 'y')) {
				$test = explode("?", $chemin);
				$chemin = count($test) > 1 ? $test[0] : $chemin;
			}

			$nettoyer_acces = "y";
			if (file_exists($chemin)) {
				$suppr = deltree($chemin, TRUE);
				if (!$suppr) {
					echo "<p><span style='color:red'>Erreur lors de la suppression de $chemin</span></p>";
					return FALSE;
					$nettoyer_acces = "n";
				}
			}

			if ($nettoyer_acces == "y") {
				$sql = "DELETE FROM acces_cdt_groupes WHERE id_acces='$id_acces';";
				$del = mysqli_query($mysqli, $sql);
				if (!$del) {
					echo "<p><span style='color:red'>Erreur lors de la suppression des groupes associés à l'accès n°$id_acces</span></p>";
					return FALSE;
				} else {
					$sql = "DELETE FROM acces_cdt WHERE id='$id_acces';";
					$del = mysqli_query($mysqli, $sql);
					if (!$del) {
						echo "<p><span style='color:red'>Erreur lors de la suppression de l'accès n°$id_acces</span></p>";
						return FALSE;
					} else {
						return TRUE;
					}
				}
			}
		}
	}
}

//=======================================================
// Fonction récupérée dans /mod_ooo/lib/lib_mod_ooo.php

/**
 * Supprime une arborescence
 *
 * Retourne TRUE si tout s'est bien passé,
 * FALSE si un fichier est resté (problème de permission ou attribut lecture sous Win.
 * Dans tous les cas, le maximum possible est supprimé.
 * @staticvar int $niv niveau dans l'arborescence
 * @param string $rep Le répertoire de départ
 * @param boolean $repaussi TRUE ~> efface aussi $rep
 * @return boolean TRUE si tout s'est bien passé
 */
function deltree($rep, $repaussi = TRUE) {
	static $niv = 0;
	$niv++;
	if (!is_dir($rep)) {
		return FALSE;
	}
	$handle = opendir($rep);
	if (!$handle) {
		return FALSE;
	}
	while ($entree = readdir($handle)) {
		if (is_dir($rep . '/' . $entree)) {
			if ($entree != '.' && $entree != '..') {
				$ok = deltree($rep . '/' . $entree);
			} else {
				$ok = TRUE;
			}
		} else {
			$ok = @unlink($rep . '/' . $entree);
		}
	}
	closedir($handle);
	$niv--;
	if ($niv || $repaussi) $ok &= @rmdir($rep);
	return $ok;
}

//=======================================================


/**
 *
 * @param string $email
 * @param string $mode
 * @return boolean
 */
function check_mail($email, $mode = 'simple', $test_mail = "n") {
	$debug_test_mail = "n";

	if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
		if ($debug_test_mail == "y") {
			$f = fopen("/tmp/debug_check_mail.txt", "a+");
			fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " check_mail(): Le format de la chaine '$email' est invalide.\n");
			fclose($f);
		}
		return FALSE;
	} else {
		if (($mode == 'simple') || (!function_exists('checkdnsrr'))) {
			if ($debug_test_mail == "y") {
				$f = fopen("/tmp/debug_check_mail.txt", "a+");
				fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " check_mail(): Le format de la chaine '$email' est valide.\n");
				fclose($f);
			}
			return TRUE;
		} else {
			if ($debug_test_mail == "y") {
				$f = fopen("/tmp/debug_check_mail.txt", "a+");
				fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " check_mail(): Le format de la chaine '$email' est valide; On teste avec checkdnsrr().\n");
				fclose($f);
			}

			$tab = explode('@', $email);
			if (checkdnsrr($tab[1], 'MX')) {
				return TRUE;
			} elseif ($test_mail == "n") {
				if (checkdnsrr($tab[1], 'A')) {
					return TRUE;
				} else {
					return FALSE;
				}
			} else {
				return FALSE;
			}
		}
	}
}


/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS
 * et à retourner une date au format jj/mm/aaaa
 *
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @param string $avec_nom_jour court, complet ou vide
 * @return string  date (jj/mm/aaaa)
 * @todo on a déjà cette fonction
 */
function get_date_slash_from_mysql_date($mysql_date, $avec_nom_jour = "") {
	$tmp_tab = explode(" ", $mysql_date);
	if (isset($tmp_tab[0])) {
		$tmp_tab2 = explode("-", $tmp_tab[0]);
		if (isset($tmp_tab2[2])) {
			$jour = "";
			if ($avec_nom_jour != "") {
				$instant = mktime(12, 0, 0, $tmp_tab2[1], $tmp_tab2[2], $tmp_tab2[0]);
				if ($avec_nom_jour == "court") {
					$jour = french_strftime("%a", $instant) . " ";
				} else {
					$jour = french_strftime("%A", $instant) . " ";
				}
			}

			return $jour . $tmp_tab2[2] . "/" . $tmp_tab2[1] . "/" . $tmp_tab2[0];
		} else {
			return "Date '" . $tmp_tab[0] . "' mal formatée?";
		}
	} else {
		return "Date '$mysql_date' mal formatée?";
	}
}

/**
 * Fonction destinée à prendre une date au format jj/mm/aaaa
 * et à retourner une date mysql aaaa-mm-jj HH:MM:SS
 *
 * @param string $slash_date (jj/mm/aaaa)
 * @return date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @todo on a déjà cette fonction
 */
function get_mysql_date_from_slash_date($slash_date, $avec_HHMMSS = "y") {
	$tmp_tab = explode("/", $slash_date);
	if (isset($tmp_tab[2])) {
		if ($avec_HHMMSS == "y") {
			return $tmp_tab[2] . "-" . $tmp_tab[1] . "-" . $tmp_tab[0] . " 00:00:00";
		} else {
			return $tmp_tab[2] . "-" . $tmp_tab[1] . "-" . $tmp_tab[0];
		}
	} else {
		return "Date '$slash_date' mal formatée?";
	}
}

// Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS et à retourner une heure au format HH:MM

/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS
 * et à retourner une heure au format HH:MM
 *
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (HH:MM)
 */
function get_heure_2pt_minute_from_mysql_date($mysql_date) {
	$tmp_tab = explode(" ", $mysql_date);
	if (isset($tmp_tab[1])) {
		$tmp_tab2 = explode(":", $tmp_tab[1]);
		if (isset($tmp_tab2[1])) {
			return $tmp_tab2[0] . ":" . $tmp_tab2[1];
		} else {
			return "Heure '" . $tmp_tab[1] . "' mal formatée?";
		}
	} else {
		return "Date '$mysql_date' mal formatée?";
	}
}

/**
 * Fonction destinée à prendre une date mysql aaaa-mm-jj HH:MM:SS
 * et à retourner une date  au format jj/mm/aaaa HH:MM
 *
 * @param date $mysql_date date (aaaa-mm-jj HH:MM:SS)
 * @return string  heure (jj/mm/aaaa HH:MM)
 */
function get_date_heure_from_mysql_date($mysql_date) {
	return get_date_slash_from_mysql_date($mysql_date) . " " . get_heure_2pt_minute_from_mysql_date($mysql_date);
}

/**
 *
 * @param type $mysql_date
 * @return type
 */
function mysql_date_to_unix_timestamp($mysql_date) {
	$tmp_tab = explode(" ", $mysql_date);
	$tmp_tab2 = explode("-", $tmp_tab[0]);
	if ((!isset($tmp_tab[1])) || (!isset($tmp_tab2[2]))) {
		// Ces retours ne sont pas adaptés... on fait généralement une comparaison sur le retour de cette fonction
		return "Date '$mysql_date' mal formatée?";
	} else {
		//$tmp_tab3=explode(":", $tmp_tab[1]);
		$tmp_tab3 = explode(":", str_ireplace('m', ':', str_ireplace('h', ':', $tmp_tab[1])));

		if (!isset($tmp_tab3[2])) {
			// Ces retours ne sont pas adaptés... on fait généralement une comparaison sur le retour de cette fonction
			return "Date '$mysql_date' mal formatée?";
		} else {
			$jour = $tmp_tab2[2];
			$mois = $tmp_tab2[1];
			$annee = $tmp_tab2[0];

			$heure = $tmp_tab3[0];
			$min = $tmp_tab3[1];
			$sec = $tmp_tab3[2];

			return mktime($heure, $min, $sec, $mois, $jour, $annee);
		}
	}
}

/**
 * Recherche les profs principaux d'une classe ou des classes dont un prof est PP
 *
 * @param string $id_classe id de la classe
 * @param string $login_user si l'id_classe est vide, on recherche les classes dont $login_user est PP
 * @return array Tableau des logins des profs principaux
 */
function get_tab_prof_suivi($id_classe = "", $login_user = "") {
	global $mysqli;
	$tab = array();

	if ($id_classe != "") {
		$sql = "SELECT DISTINCT jep.professeur 
			FROM j_eleves_professeurs jep, j_eleves_classes jec 
			WHERE jec.id_classe='$id_classe' 
			AND jec.login=jep.login
			AND jec.id_classe=jep.id_classe
			ORDER BY professeur;";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$tab[] = $lig->professeur;
			}
			$res->close();
		}
	} elseif ($login_user != "") {
		$sql = "SELECT DISTINCT jep.id_classe 
			FROM j_eleves_professeurs jep, classes c 
			WHERE jep.professeur='$login_user'
			AND jep.id_classe=c.id
			ORDER BY c.classe;";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$tab[] = $lig->id_classe;
			}
			$res->close();
		}
	} else {
		$sql = "SELECT DISTINCT jep.professeur, jec.id_classe 
			FROM j_eleves_professeurs jep, j_eleves_classes jec 
			WHERE jec.login=jep.login
			AND jec.id_classe=jep.id_classe
			ORDER BY professeur;";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$tab[$lig->id_classe][] = $lig->professeur;
			}
			$res->close();
		}
	}

	return $tab;
}


/**
 * Retourne la liste des profs principaux d'une classe sous la forme d'une chaine séparée par des virgules
 *
 * @param string $id_classe id de la classe
 * @return string Liste des profs principaux d'une classe sous la forme d'une chaine séparée par des virgules
 */
function liste_prof_suivi($id_classe, $pour_qui = "", $avec_lien_mail = "") {
	global $mysqli;
	$retour = "";

	$tab = get_tab_prof_suivi($id_classe);
	//$retour=implode(", ", $tab);
	for ($loop = 0; $loop < count($tab); $loop++) {
		if ($loop > 0) {
			$retour .= ", ";
		}

		$mail_user = "";
		if ($avec_lien_mail == "y") {
			$mail_user = get_mail_user($tab[$loop]);
		}

		if ($mail_user != "") {
			if ($pour_qui == "profs") {
				$retour .= "<a href='mailto:$mail_user?" . urlencode("subject=" . getSettingValue('gepiPrefixeSujetMail')) . "[GEPI]: ...' title=\"Envoyer un mail.\">" . civ_nom_prenom($tab[$loop]) . "</a>";
			} else {
				$retour .= "<a href='mailto:$mail_user?" . urlencode("subject=" . getSettingValue('gepiPrefixeSujetMail')) . "[GEPI]: ...' title=\"Envoyer un mail.\">" . affiche_utilisateur($tab[$loop], $id_classe) . "</a>";
			}
		} else {
			if ($pour_qui == "profs") {
				$retour .= civ_nom_prenom($tab[$loop]);
			} else {
				$retour .= affiche_utilisateur($tab[$loop], $id_classe);
			}
		}
	}

	return $retour;
}

/**
 * Enregistre pour Affichage un message sur la page d'accueil du destinataire (ML 5/2011)
 *
 * Les appels possibles
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel") : affiche le message "Bonjour Untel" sur la page d'accueil du destinataire de login "UNTEL" dès l'appel de la fonction, pour une durée de 7 jours, avec décompte sur le 7ième jour
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, pour une durée de 7 jours, avec décompte sur le 7ième jour
 *  - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130684567
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567,130690844) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130690844
 * - message_accueil_utilisateur("UNTEL","Bonjour Untel",130674844,130684567,130690844,true) : affiche le message "Bonjour Untel" sur la page du destinataire de login "UNTEL" à partir de la date 130674844, jusqu'à la date 130684567, avec décompte sur la date 130690844, et autorise l'utilisateur à supprimer ce message
 *
 * @param type string $login_destinataire login du destinataire (obligatoire)
 * @param type string $texte texte du message contenant éventuellement des balises HTML et encodé en iso-8859-1 (obligatoire)
 * @param type timestamp $date_debut date à partir de laquelle est affiché le message (optionnel)
 * @param type timestamp $date_fin date à laquelle le message n'est plus affiché (optionnel)
 * @param type timestamp $date_decompte date butoir du décompte, la chaîne _DECOMPTE_ dans $texte est remplacée par un décompte (optionnel)
 * @param type bolean $bouton_supprimer détermine s'il faut ajouter au message le bouton "Supprimer ce message"
 * @return type TRUE ou FALSE selon que le message a été enregistré ou pas
 */
function message_accueil_utilisateur($login_destinataire, $texte, $date_debut = 0, $date_fin = 0, $date_decompte = 0, $bouton_supprimer = false) {
	global $mysqli;
	global $gepiPath;
	// On arrondit le timestamp d'appel à l'heure (pas nécessaire mais pour l'esthétique)
	$t_appel = time() - (time() % 3600);
	// suivant le nombre de paramètres passés :
	switch (func_num_args()) {
		case 3:
			$date_fin = $date_debut + 3600 * 24 * 7;
			$date_decompte = $date_fin;
			break;
		case 4:
			$date_decompte = $date_fin;
			break;
		case 5:
		case 6:
			break;
		default :
			// valeurs par défaut
			$date_debut = $t_appel;
			$date_fin = $t_appel + 3600 * 24 * 7;
			$date_decompte = $date_fin;
	}
	$r_sql = "INSERT INTO `messages` values('','" . addslashes($texte) . "','" . $date_debut . "','" . $date_fin . "','" . $_SESSION['login'] . "','_','" . $login_destinataire . "','" . $date_decompte . "')";
	$retour = mysqli_query($mysqli, $r_sql) ? TRUE : FALSE;
	if ($retour && $bouton_supprimer) {
		$id_message = $mysqli->insert_id;
		$contenu = '
		<form method="POST" action="#" name="f_suppression_message">
		<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">
		<input type="hidden" name="supprimer_message" value="' . $id_message . '">
		<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="' . $gepiPath . '/images/icons/delete.png" alt="" /></button>
		</form>' . addslashes($texte);
		$r_sql = "UPDATE `messages` SET `texte`='" . $contenu . "' WHERE `id`='" . $id_message . "'";
		$retour = mysqli_query($mysqli, $r_sql) ? TRUE : FALSE;
	}
	return $retour;
}

/**
 * Transforme un tableau en chaine, les lignes sont séparées par une ,
 *
 * @param array $tableau Le tableau à parser
 * @return string La chaine produite
 */
function array_to_chaine($tableau) {
	$chaine = "";
	$cpt = 0;
	foreach ($tableau as $key => $value) {
		if ($cpt > 0) {
			$chaine .= ", ";
		}
		$chaine .= "'$value'";
		$cpt++;
	}
	unset ($key);
	unset ($value);
	return $chaine;
}

/**
 * Supprime les sauts de lignes dupliqués
 *
 * @param string $chaine La chaine  à parser
 * @return string La chaine produite
 */
function suppression_sauts_de_lignes_surnumeraires($chaine) {
	$retour = preg_replace('/(\\\r\\\n)+/', "\r\n", $chaine);
	$retour = preg_replace('/(\\\r)+/', "\r", $retour);
	$retour = preg_replace('/(\\\n)+/', "\n", $retour);
	return $retour;
}

/**
 * Affiche le nombre de notes ou commentaires saisis pour les bulletins
 *
 * @param string $type "notes" pour voir les notes sinon commentaires
 * @param int $id_groupe Id du groupe
 * @param int $periode_num numéro de la période
 * @param string $mode Si "couleur" le texte est sur fond orange si tous les élèves ne sont pas notés
 * @return string le nombre de notes ou commentaires saisis
 */
function nb_saisies_bulletin($type, $id_groupe, $periode_num, $mode = "") {
	global $mysqli;
	$retour = "";

	if ($type == "notes") {
		$sql = "SELECT 1=1 FROM matieres_notes WHERE id_groupe='" . $id_groupe . "' AND periode='" . $periode_num . "';";
	} else {
		$sql = "SELECT 1=1 FROM matieres_appreciations WHERE id_groupe='" . $id_groupe . "' AND periode='" . $periode_num . "';";
	}
	$test = mysqli_query($mysqli, $sql);
	$nb_saisies_bulletin = $test->num_rows;
	$test->close();
	$tab_champs = array('eleves');
	$current_group = get_group($id_groupe, $tab_champs);
	$effectif_groupe = count($current_group["eleves"][$periode_num]["users"]);
	if ($mode == "couleur") {
		if ($nb_saisies_bulletin == $effectif_groupe) {
			$retour = "<span style='font-size: x-small;' title='Saisies complètes'>";
			$retour .= "($nb_saisies_bulletin/$effectif_groupe)";
			$retour .= "</span>";
		} else {
			$retour = "<span style='font-size: x-small; background-color: orangered;' title='Saisies incomplètes ou non encore effectuées'>";
			$retour .= "($nb_saisies_bulletin/$effectif_groupe)";
			$retour .= "</span>";
		}
	} else {
		$retour = "($nb_saisies_bulletin/$effectif_groupe)";
	}

	return $retour;
}

/**
 * Crée un fichier index.html de redirection vers login.php
 *
 * @param string $chemin_relatif Le répertoire à protéger
 * @param int $niveau_arbo Niveau dans l'arborescence GEPI
 * @return boolean TRUE si le fichier est créé
 */
function creation_index_redir_login($chemin_relatif, $niveau_arbo = 1) {
	$retour = TRUE;

	if ($niveau_arbo == 0) {
		$pref = ".";
	} else {
		$pref = "";
		for ($i = 0; $i < $niveau_arbo; $i++) {
			if ($i > 0) {
				$pref .= "/";
			}
			$pref .= "..";
		}
	}

	$fich = fopen($chemin_relatif . "/index.html", "w+");
	if (!$fich) {
		$retour = FALSE;
	} else {
		$res = fwrite($fich, '<html><head><script type="text/javascript">
    document.location.replace("' . $pref . '/login.php")
</script></head></html>
');
		if (!$res) {
			$retour = FALSE;
		}
		fclose($fich);
	}

	return $retour;
}

/**
 * Renvoie un tableau des fichiers contenus dans le dossier
 *
 * @param string $path Le dossier à parser
 * @param array $tab_exclusion Fichiers à ne pas prendre en compte
 * @return array Tableau des fichiers
 */
function get_tab_file($path, $tab_exclusion = array(".", "..", "remove.txt", ".htaccess", ".htpasswd", "index.html")) {
	$tab_file = array();

	$handle = opendir($path);
	$n = 0;
	while ($file = readdir($handle)) {
		if (!in_array(mb_strtolower($file), $tab_exclusion)) {
			$tab_file[] = $file;
			$n++;
		}
	}
	closedir($handle);
	//arsort($tab_file);
	rsort($tab_file);

	return $tab_file;
}


/**
 * Tableau des mentions pour les bulletins
 *
 * @global array $GLOBALS ['tableau_des_mentions_sur_le_bulletin']
 * @name $tableau_des_mentions_sur_le_bulletin
 */
$GLOBALS['tableau_des_mentions_sur_le_bulletin'] = array();

/**
 * Retourne une mention pour les bulletins à partir de son Id
 *
 * @param int $code Id de la mention recherchée
 * @return string
 * @global array
 * @see get_mentions()
 */
function traduction_mention($code) {
	global $tableau_des_mentions_sur_le_bulletin;

	if ((!is_array($tableau_des_mentions_sur_le_bulletin)) || (count($tableau_des_mentions_sur_le_bulletin) == 0)) {
		$tableau_des_mentions_sur_le_bulletin = get_mentions();
	}

	$retour = "";
	if (!isset($tableau_des_mentions_sur_le_bulletin[$code])) {
		$retour = "-";
	} else {
		$retour = $tableau_des_mentions_sur_le_bulletin[$code];
	}

	return $retour;
}

/**
 * Retourne un tableau des mentions pour les bulletins
 *
 * tableau[index de la mention] = texte de la mention;
 *
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_mentions($id_classe = NULL) {
	global $mysqli;
	$tab = array();
	if (!isset($id_classe)) {
		$sql = "SELECT * FROM mentions ORDER BY id;";
	} else {
		$sql = "SELECT m.* FROM mentions m, j_mentions_classes j WHERE j.id_mention=m.id AND j.id_classe='$id_classe' ORDER BY j.ordre, m.mention, m.id;";
	}
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[$lig->id] = $lig->mention;
		}
		$res->close();
	}
	return $tab;
}

/**
 * Retourne un tableau des mentions déjà utilisées dans les bulletins
 *
 * Pour interdire la suppression d'une mention saisie pour un élève
 *
 * @param int $id_classe Id de la classe
 * @return array Le tableau des mentions
 */
function get_tab_mentions_affectees($id_classe = NULL) {
	global $mysqli;
	$tab = array();
	if (!isset($id_classe)) {
		$sql = "SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a WHERE a.id_mention=j.id_mention;";
	} else {
		$sql = "SELECT DISTINCT j.id_mention FROM j_mentions_classes j, avis_conseil_classe a, j_eleves_classes jec WHERE a.id_mention=j.id_mention AND j.id_classe=jec.id_classe AND jec.periode=a.periode AND jec.login=a.login AND j.id_classe='$id_classe';";
	}
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[] = $lig->id_mention;
		}
		$res->close();
	}
	return $tab;
}

/**
 * Renvoie une balise <select> avec les mentions de bulletin
 *
 * @param string $nom_champ_select valeur des attribut name et id du select
 * @param int $id_classe Id de la classe
 * @param string $id_mention_selected Id de la mention à sélectionner par défaut
 * @return string La balise
 */
function champ_select_mention($nom_champ_select, $id_classe, $id_mention_selected = '') {

	$tab_mentions = get_mentions($id_classe);
	$retour = "<select name='$nom_champ_select' id='$nom_champ_select'>\n";
	$retour .= "<option value=''";
	if (($id_mention_selected == "") || (!array_key_exists($id_mention_selected, $tab_mentions))) {
		$retour .= " selected='selected'";
	}
	$retour .= " title=\"Aucune mention\"> --- </option>\n";
	foreach ($tab_mentions as $key => $value) {
		$retour .= "<option value='$key'";
		if ($id_mention_selected == $key) {
			$retour .= " selected='selected'";
		}
		//$retour.=">".$value." ".$key."</option>\n";
		$retour .= ">" . $value . "</option>\n";
	}
	$retour .= "</select>\n";

	return $retour;
}

/**
 * Teste s'il y a des mentions de bulletin définies pour une classe
 *
 * @param type $id_classe Id de la classe
 * @return boolean TRUE si il y a des mentions
 */
function test_existence_mentions_classe($id_classe) {
	global $mysqli;
	$sql = "SELECT 1=1 FROM j_mentions_classes WHERE id_classe='$id_classe';";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 0) {
		$test->close();
		return TRUE;
	} else {
		return FALSE;
	}

}

/**
 * Teste si un compte est actif
 *
 * - 0 si l'utilisateur n'est pas trouvé
 * - 1 compte actif
 * - 2 compte non-actif
 *
 * @param type $login Login de l'utilisateur
 * @return int
 */
function check_compte_actif($login) {
	global $mysqli;
	$sql = "SELECT etat FROM utilisateurs WHERE login='$login';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		return 0;
	} else {
		$lig = $res->fetch_object();
		$res->close();
		if ($lig->etat == 'actif') {
			return 1;
		} else {
			return 2;
		}
	}
}

/**
 * Crée un lien derrière une image pour modifier les données d'un utilisateur
 *
 * @param string $login id de l'utilisateur cherché
 * @param string $statut statut de l'utilisateur (si '', il sera cherché avec get_statut_from_login())
 * @param string $target pour ouvrir dans une autre fenêtre
 * @param string $avec_lien 'y' ou absent pour créer un lien
 * @return string Le code html
 * @global string
 * @see check_compte_actif()
 * @see get_statut_from_login()
 * @see get_infos_from_login_utilisateur()
 * @todo si $target='_blank' il faudrait ajouter un argument title pour prévenir
 */
function lien_image_compte_utilisateur($login, $statut = '', $target = '', $avec_lien = 'y', $avec_span_invisible = 'n') {
	global $gepiPath;

	$retour = "";

	if ($target != "") {
		/*
		// Cela masque le title Compte actif/inactif
		if($target=='_blank') {
			$target=" target='$target' title='Ouverture dans un nouvel onglet.'";
		}
		else {
		*/
		$target = " target='$target'";
		//}
	}

	$test = check_compte_actif($login);
	if ($test != 0) {
		if ($statut == "") {
			$statut = get_statut_from_login($login);
		} else {
			$tmp_statut = get_statut_from_login($login);
			if ($tmp_statut != $statut) {
				if ($avec_span_invisible == "y") {
					$retour .= "<span style='display:none'>Anomalie</span>";
				}
				$retour .= "<img src='../images/icons/flag2.gif' width='17' height='18' alt='' title=\"ANOMALIE : Le statut du compte ne coïncide pas avec le statut attendu.
                    Le compte est '$tmp_statut' alors que vous avez fait
                    une recherche pour un compte '$statut'.\" /> ";
			}
		}

		if ($statut != "") {
			$refermer_lien = "y";

			if ($avec_span_invisible == "y") {
				$retour .= "<span style='display:none'>Compte " . (($test == 1) ? "actif" : "inactif") . "</span>";
			}

			if ($avec_lien == "y") {
				if ($statut == 'eleve') {
					$retour .= "<a href='" . $gepiPath . "/eleves/modify_eleve.php?eleve_login=$login'$target>";
				} elseif ($statut == 'responsable') {
					$infos = get_infos_from_login_utilisateur($login);
					if (isset($infos['pers_id'])) {
						$retour .= "<a href='" . $gepiPath . "/responsables/modify_resp.php?pers_id=" . $infos['pers_id'] . "'$target>";
					} else {
						$refermer_lien = "n";
					}
				} elseif ($statut == 'autre') {
					$retour .= "<a href='" . $gepiPath . "/utilisateurs/creer_statut.php'$target>";
				} else {
					$retour .= "<a href='" . $gepiPath . "/utilisateurs/modify_user.php?user_login=$login'$target>";
				}
			}

			if ($test == 1) {
				$retour .= "<img src='" . $gepiPath . "/images/icons/buddy.png' width='16' height='16' alt='Compte $login actif' title='Compte $login actif' />";
			} else {
				$retour .= "<img src='" . $gepiPath . "/images/icons/buddy_no.png' width='16' height='16' alt='Compte $login inactif' title='Compte $login inactif' />";
			}

			if ($avec_lien == "y") {
				if ($refermer_lien == "y") {
					$retour .= "</a>";
				}
			}
		}
	}

	return $retour;
}

/**
 * Renvoie le statut d'un utilisateur à partir de son login
 *
 * @param string $login Login de l'utilisateur
 * @return string Le statut
 */
function get_statut_from_login($login) {
	global $mysqli;
	$sql = "SELECT statut FROM utilisateurs WHERE login='$login';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		return "";
	} else {
		$lig = $res->fetch_object();
		$res->close();
		return $lig->statut;
	}
}

/**
 * Renvoie dans un tableau les informations d'un utilisateur à partir de son login
 *
 * Champs disponibles dans le tableau
 * - tout utilisateur ->  'nom', 'prenom', 'civilite', 'email','show_email','statut','etat','change_mdp','date_verrouillage','ticket_expiration','niveau_alerte','observation_securite','temp_dir','numind','auth_mode'
 * - responsable -> pers_id
 * - eleve -> 'no_gep','sexe','naissance','lieu_naissance','elenoet','ereno','ele_id','id_eleve','id_mef','date_sortie'
 *
 * @param string $login Login de l'utilisateur
 * @param string $tab_champs Tableau non utilisé
 * @return array Le tableau des informations
 * @todo $tab_champs n'est pas utilisé pour l'instant
 * @todo Déterminer les champs supplémentaires pour le statut autre
 */
function get_infos_from_login_utilisateur($login, $tab_champs = array()) {
	global $mysqli;
	$tab = array();

	$tab_champs_utilisateur = array('nom', 'prenom', 'civilite', 'email', 'show_email', 'statut', 'etat', 'change_mdp', 'date_verrouillage', 'ticket_expiration', 'niveau_alerte', 'observation_securite', 'temp_dir', 'numind', 'auth_mode');
	$sql = "SELECT * FROM utilisateurs WHERE login='$login';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$lig = $res->fetch_object();
		foreach ($tab_champs_utilisateur as $key => $value) {
			$tab[$value] = $lig->$value;
		}
		unset ($key, $value);
		$res->close();

		if ($tab['statut'] == 'responsable') {
			$sql = "SELECT pers_id, mel FROM resp_pers WHERE login='$login';";
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$lig = $res->fetch_object();
				$tab['pers_id'] = $lig->pers_id;
				$tab['mel'] = $lig->mel;

				if (in_array('enfants', $tab_champs)) {
					// A compléter
				}
				$res->close();
			}
		} elseif ($tab['statut'] == 'eleve') {
			$sql = "SELECT * FROM eleves WHERE login='$login';";
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$lig = $res->fetch_object();

				$tab_champs_eleve = array('no_gep', 'sexe', 'naissance', 'lieu_naissance', 'elenoet', 'ereno', 'ele_id', 'id_eleve', 'mef_code', 'date_sortie');
				foreach ($tab_champs_eleve as $key => $value) {
					$tab[$value] = $lig->$value;
				}
				unset ($key, $value);

				if (in_array('parents', $tab_champs)) {
					// A compléter
				}
				$res->close();
			}

		} elseif ($tab['statut'] == 'autre') {
			// A compléter
			$tab['statut_autre'] = "A EXTRAIRE";
		}
	}
	return $tab;
}

/**
 * Vérifie qu'un responsable a accès au module discipline
 *
 * @param string $login_resp Login du responsable
 * @return boolean TRUE si le responsable a accès
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_resp_disc($login_resp) {
	if ((check_compte_actif($login_resp) != 0) &&
		((getSettingAOui('visuRespDisc')) || (getSettingAOui('visuRespDiscNature')))) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Vérifie qu'un élève a accès au module discipline
 *
 * @param string $login_ele Login de l'élève
 * @return boolean TRUE si l'élève a accès
 * @see check_compte_actif()
 * @see getSettingValue()
 */
function acces_ele_disc($login_ele) {
	if ((check_compte_actif($login_ele) != 0) &&
		((getSettingAOui('visuEleDisc')) || getSettingAOui('visuEleDiscNature'))) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/**
 * Renvoie un tableau des responsables d'un élève
 *
 * $tab[indice] = array('login','nom','prenom','civilite','designation'=>civilite nom prenom, 'pers_id')
 *
 * @param string $ele_login Login de l'élève
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 *
 * @return array Le tableau
 */
function get_resp_from_ele_login($ele_login, $meme_en_resp_legal_0 = "n", $envoi_bulletin = "") {
	global $mysqli;
	$tab = array();

	$sql = "(SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND (r.resp_legal='1' OR r.resp_legal='2'))";
	if ($meme_en_resp_legal_0 == "y") {
		$sql .= " UNION (SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.resp_legal='0')";
	} elseif ($meme_en_resp_legal_0 == "yy") {
		$sql .= " UNION (SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.resp_legal='0' AND r.acces_sp='y')";
	}

	if ($envoi_bulletin == "y") {
		$sql .= " UNION (SELECT rp.*, r.resp_legal FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND rp.pers_id=r.pers_id AND r.ele_id=e.ele_id AND r.envoi_bulletin='y')";
	}
	$sql .= ";";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$cpt = 0;
		$tab_deja = array();
		while ($lig = $res->fetch_object()) {
			if (!in_array($lig->login, $tab_deja)) {
				$tab[$cpt] = array();

				$tab[$cpt]['login'] = $lig->login;
				$tab[$cpt]['nom'] = $lig->nom;
				$tab[$cpt]['prenom'] = $lig->prenom;
				$tab[$cpt]['civilite'] = $lig->civilite;
				$tab[$cpt]['mel'] = $lig->mel;

				$sql = "SELECT u.email FROM utilisateurs u WHERE u.login='" . $lig->login . "' AND u.statut='responsable' AND u.email LIKE '%@%' AND u.email!='" . $lig->mel . "';";
				//echo "$sql<br />";
				$res_u = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($res_u) > 0) {
					$lig_u = mysqli_fetch_object($res_u);
					$tab[$cpt]['email'] = $lig_u->email;
				}

				$tab[$cpt]['designation'] = $lig->civilite . " " . $lig->nom . " " . $lig->prenom;

				$tab[$cpt]['pers_id'] = $lig->pers_id;

				$tab[$cpt]['resp_legal'] = $lig->resp_legal;

				$tab_deja[] = $lig->login;
				$cpt++;
			}
		}
		$res->close();
	}

	//print_r($tab);

	return $tab;
}

/**
 *
 * @param type $callback
 * @param ArrayAccess $array
 * @return type
 */
function array_map_deep($callback, $array) {
	$new = array();
	if (is_array($array) || $array instanceof ArrayAccess) {
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$new[$key] = array_map_deep($callback, $val);
			} else {
				$new[$key] = call_user_func($callback, $val);
			}
		}
	} else $new = call_user_func($callback, $array);
	return $new;
}

/**
 * Création de la balise audio pour l'alarme sonore de fin de session
 *
 * @return string Balises audio
 */
function joueAlarme($niveau_arbo = "0") {
	$retour = "";
	$footer_sound = isset ($_SESSION['login']) ? getPref($_SESSION['login'], 'footer_sound', "") : "";

	if ($footer_sound == '') {
		$footer_sound = getSettingValue('footer_sound');
		if ($footer_sound == '') {
			$footer_sound = "KDE_Beep_Pop.wav";
		}
	}

	if ($niveau_arbo == "0") {
		$chemin_sound = "./sounds/" . $footer_sound;
	} elseif ($niveau_arbo == "1") {
		$chemin_sound = "../sounds/" . $footer_sound;
	} elseif ($niveau_arbo == "2") {
		$chemin_sound = "../../sounds/" . $footer_sound;
	} elseif ($niveau_arbo == "3") {
		$chemin_sound = "../../../sounds/" . $footer_sound;
	} else {
		$chemin_sound = "../sounds/" . $footer_sound;
	}

	if (file_exists($chemin_sound)) {
		$retour = "<audio id='id_footer_sound' preload='auto'>
	<source src='" . $chemin_sound . "' />
</audio>
<script type='text/javascript'>
  function play_footer_sound() {
	  if(document.getElementById('id_footer_sound')) {
		  document.getElementById('id_footer_sound').play();
	  }
  }
  </script>";
	}
	//}
	return $retour;
}

/**
 * Recupere le timestamp unix du jour ouvert precedent
 *
 * @param int $timestamp du jour courant
 * @return int $timestamp du jour precedent
 */
function get_timestamp_jour_precedent($timestamp_today) {
	global $mysqli;
	$hier = false;

	$tab_nom_jour = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
	$sql = "select * from horaires_etablissement WHERE ouverture_horaire_etablissement!=fermeture_horaire_etablissement AND ouvert_horaire_etablissement!='0' ORDER BY id_horaire_etablissement;";
	$res_jours_ouverts = mysqli_query($mysqli, $sql);
	if ($res_jours_ouverts->num_rows > 0) {
		$tab_jours_ouverture = array();
		while ($lig_j = $res_jours_ouverts->fetch_object()) {
			$tab_jours_ouverture[] = $lig_j->jour_horaire_etablissement;
			//echo "\$tab_jours_ouverture[]=".$lig_j->jour_horaire_etablissement."<br />";
		}

		$compteur = 0;
		$j_prec = $timestamp_today - 3600 * 24;
		while ((isset($tab_nom_jour[strftime("%w", $j_prec)])) && (!in_array($tab_nom_jour[strftime("%w", $j_prec)], $tab_jours_ouverture)) && ($compteur < 8)) {
			$j_prec -= 3600 * 24;
			$compteur++;
		}
		if ($compteur < 7) {
			$hier = $j_prec;
		}
		$res_jours_ouverts->close();
	}

	return $hier;
}

/**
 * Recupere le timestamp unix du jour ouvert suivant
 *
 * @param int $timestamp du jour courant
 * @return int $timestamp du jour suivant
 */
function get_timestamp_jour_suivant($timestamp_today) {
	global $mysqli;
	$demain = false;

	$tab_nom_jour = array('dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi');
	$sql = "SELECT * from horaires_etablissement WHERE ouverture_horaire_etablissement!=fermeture_horaire_etablissement AND ouvert_horaire_etablissement!='0' ORDER BY id_horaire_etablissement;";
	$res_jours_ouverts = mysqli_query($mysqli, $sql);
	if ($res_jours_ouverts->num_rows > 0) {
		$tab_jours_ouverture = array();
		while ($lig_j = $res_jours_ouverts->fetch_object()) {
			$tab_jours_ouverture[] = $lig_j->jour_horaire_etablissement;
			//echo "\$tab_jours_ouverture[]=".$lig_j->jour_horaire_etablissement."<br />";
		}
		$res_jours_ouverts->close();

		$compteur = 0;
		$j_prec = $timestamp_today - 3600 * 24;
		while ((isset($tab_nom_jour[strftime("%w", $j_prec)])) && (!in_array($tab_nom_jour[strftime("%w", $j_prec)], $tab_jours_ouverture)) && ($compteur < 8)) {
			$j_prec -= 3600 * 24;
			$compteur++;
		}
		if ($compteur < 7) {
			$hier = $j_prec;
		}

		$compteur = 0;
		$j_suiv = $timestamp_today + 3600 * 24;
		while ((isset($tab_nom_jour[strftime("%w", $j_suiv)])) && (!in_array($tab_nom_jour[strftime("%w", $j_suiv)], $tab_jours_ouverture)) && ($compteur < 8)) {
			$j_suiv += 3600 * 24;
			$compteur++;
		}
		if ($compteur < 7) {
			$demain = $j_suiv;
		}
	}

	return $demain;
}

/**
 * Retourne la chaine nettoyee des retours à la ligne en trop
 *
 * @param string $texte Texte à nettoyer
 * @return string Texte nettoyé
 */
function nettoyage_retours_ligne_surnumeraires($texte) {
	$retour = preg_replace('/(\\\r\\\n)+/', "\r\n", $texte);
	$retour = preg_replace('/(\\\r)+/', "\r", $retour);
	$retour = preg_replace('/(\\\n)+/', "\n", $retour);

	return $retour;
}

/** fonction de formatage des dates de debut et de fin de saisie d'absence
 *
 * @param date $date_debut
 * @param date $date_fin
 * @return string Les dates formatées
 */
function getDateDescription($date_debut, $date_fin, $avec_temoin_pb = "n") {
	global $mysqli;

	$flag = "";
	if ($avec_temoin_pb == "y") {
		global $tab_heure_ouverture;

		$num_jour = id_j_semaine($date_debut);
		//echo "num_jour=$num_jour<br />";
		if (!isset($tab_heure_ouverture_etablissement[$num_jour])) {

			$tab_sem[1] = 'lundi';
			$tab_sem[2] = 'mardi';
			$tab_sem[3] = 'mercredi';
			$tab_sem[4] = 'jeudi';
			$tab_sem[5] = 'vendredi';
			$tab_sem[6] = 'samedi';
			$tab_sem[7] = 'dimanche';

			$sql = "SELECT ouverture_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='" . $tab_sem[$num_jour] . "';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$lig = mysqli_fetch_object($res);
				$tab_heure_ouverture_etablissement[id_j_semaine($date_debut)] = $lig->ouverture_horaire_etablissement;
			}
		}

		if (isset($tab_heure_ouverture_etablissement[$num_jour])) {
			//echo "strftime('%I:%M:%S', $date_debut)=".strftime("%I:%M:%S", $date_debut)."<br />";
			//echo "\$tab_heure_ouverture_etablissement[$num_jour]=".$tab_heure_ouverture_etablissement[$num_jour]."<br />";
			if (strftime("%H:%M:%S", $date_debut) < $tab_heure_ouverture_etablissement[$num_jour]) {
				$flag = " <img src='../images/icons/flag.png' class='icone16' alt='Anomalie' title=\"L'heure de début est antérieure à l'heure d'ouverture de l'établissement.
Dans le cas d'une absence ou d'un retard, il se peut qu'il ne soit pas pris en compte dans le décompte.\" />";
			}
		}
	}

	$message = '';
	if (french_strftime("%a %d/%m/%Y", $date_debut) == french_strftime("%a %d/%m/%Y", $date_fin)) {
		$message .= 'le ';
		$message .= (french_strftime("%a %d/%m/%Y", $date_debut));
		$message .= ' entre  ';
		$message .= (french_strftime("%H:%M", $date_debut)) . $flag;
		$message .= ' et ';
		$message .= (french_strftime("%H:%M", $date_fin));

	} else {
		$message .= ' entre le ';
		$message .= (french_strftime("%a %d/%m/%Y %H:%M", $date_debut)) . $flag;
		$message .= ' et le ';
		$message .= (french_strftime("%a %d/%m/%Y %H:%M", $date_fin));
	}
	return $message;
}

/** fonction retournant une chaine encodée pour le download d'un CSV
 *
 * @param string $texte_csv
 * @return string La chaine encodée
 */
function echo_csv_encoded($texte_csv) {
	// D'après http://www.oxeron.com/2008/09/15/probleme-daccent-dans-un-export-csv-en-php
	//$retour=$texte_csv;
	//$retour=chr(255).chr(254).mb_convert_encoding($texte_csv, 'UTF-16LE', 'UTF-8');

	$choix_encodage_csv = getPref($_SESSION['login'], "choix_encodage_csv", "");
	if (!in_array($choix_encodage_csv, array("", "ascii", "utf-8", "windows-1252"))) {
		$choix_encodage_csv = "ascii";
	}

	if ($choix_encodage_csv == "") {
		if ($_SESSION['statut'] == 'administrateur') {
			$retour = $texte_csv;
		} else {
			//$retour=mb_convert_encoding($texte_csv, 'ASCII', 'utf-8');
			//$retour=remplace_accents($texte_csv,'csv');
			// Les autres utilisateurs preferont sans doute ca:
			$retour = mb_convert_encoding($texte_csv, "windows-1252", 'utf-8');
		}
	} else {
		if ($choix_encodage_csv == "ascii") {
			//echo "=======================================<br />\n";
			//echo $texte_csv;
			$retour = ensure_ascii($texte_csv);
			//echo "=======================================<br />\n";
			//echo $retour;
			//echo "=======================================<br />\n";
		} else {
			$retour = mb_convert_encoding($texte_csv, $choix_encodage_csv, 'utf-8');
		}
	}

	return $retour;
}

/** fonction retournant le jour traduit en français
 *
 * @param string $jour_en Le jour en anglais (Mon, Tue, Wed,...)
 * @return string La date en français
 */
function jour_fr($jour_en, $mode = "") {
	$tab['mon'] = "lun";
	$tab['tue'] = "mar";
	$tab['wed'] = "mer";
	$tab['thu'] = "jeu";
	$tab['fri'] = "ven";
	$tab['sat'] = "sam";
	$tab['sun'] = "dim";

	if (isset($tab[mb_strtolower($jour_en)])) {
		if ($mode == 'majf2') {
			return casse_mot($tab[mb_strtolower($jour_en)], 'majf2');
		} else {
			return $tab[mb_strtolower($jour_en)];
		}
	} else {
		return $jour_en;
	}
}

/** fonction creant le fichier temp/info_jours.js pris en compte dans le CDT2 pour passer au jours suivant.
 *
 */
function creer_info_jours_js() {
	global $mysqli;
	global $prefix_base, $niveau_arbo;

	//echo "\$niveau_arbo=$niveau_arbo<br />";

	if (!isset($prefix_base)) {
		$prefix_base = "";
	}

	// tableau semaine
	$tab_sem[0] = 'lundi';
	$tab_sem[1] = 'mardi';
	$tab_sem[2] = 'mercredi';
	$tab_sem[3] = 'jeudi';
	$tab_sem[4] = 'vendredi';
	$tab_sem[5] = 'samedi';
	$tab_sem[6] = 'dimanche';

	$chaine_jours_ouverts = "";

	$i = 0;
	for ($i = 0; $i < count($tab_sem); $i++) {
		$sql = "SELECT 1=1 FROM " . $prefix_base . "horaires_etablissement
				WHERE jour_horaire_etablissement = '" . $tab_sem[$i] . "' AND
						date_horaire_etablissement = '0000-00-00' AND ouvert_horaire_etablissement='1'";
		$res_j_o = mysqli_query($mysqli, $sql);
		if ($res_j_o->num_rows) {
			if ($chaine_jours_ouverts != "") {
				$chaine_jours_ouverts .= ",";
			}
			$num_jour = $i + 1;
			$chaine_jours_ouverts .= "'$num_jour'";
			$res_j_o->close();
		}
	}

	if ($chaine_jours_ouverts == '') {
		$chaine_jours_ouverts = "'0','1','2','3','4','5','6'";
	}

	if (!isset($niveau_arbo)) {
		$pref_arbo = "..";
	} elseif ("$niveau_arbo" == 'public') {
		$pref_arbo = "..";
	} elseif ("$niveau_arbo" == "0") {
		$pref_arbo = ".";
	} elseif ("$niveau_arbo" == "1") {
		$pref_arbo = "..";
	} elseif ("$niveau_arbo" == "2") {
		$pref_arbo = "../..";
	} elseif ("$niveau_arbo" == "3") {
		$pref_arbo = "../../..";
	}

	//echo "\$pref_arbo=$pref_arbo<br />";

	$f = fopen("$pref_arbo/temp/info_jours.js", "w+");
	fwrite($f, "// Tableau des jours ouverts
	// 0 pour dimanche,
	// 1 pour lundi,...
	var tab_jours_ouverture=new Array($chaine_jours_ouverts);");
	fclose($f);
}

/** Fonction destinée à récupérer les images de formules mathématiques générées
 * sur http://latex.codecogs.com/
 * Cela évite de faire une requête vers le site http://latex.codecogs.com/ pour
 * chaque image et assure que lors de l'archivage, les images resteront
 * disponibles même si le site http://latex.codecogs.com/ cesse de fonctionner.
 *
 * @param string $texte Le texte à traiter
 * @param integer $id_groupe L'identifiant du groupe
 * @param string $type_notice Le type de notice
 *               ('c' pour compte-rendu et 't' pour travail à faire)
 * @return string La chaine corrigée après téléchargement des images vers
 * ../documents/cl$idgroupe ou ../documents/cl_dev$idgroupe
 */
function get_img_formules_math($texte, $id_groupe, $type_notice = "c") {
	global $multisite;

	$contenu_cor = $texte;

	if ((preg_match('|src="http://latex.codecogs.com/|', $contenu_cor)) ||
		(preg_match('|src="https://latex.codecogs.com/|', $contenu_cor))||
		(preg_match('|src="https://latex2image-output.s3.amazonaws.com/|', $contenu_cor))) {

		//https://latex2image-output.s3.amazonaws.com/img-VEPJ9xcGhepQ.png

		$niv_arbo_tmp = 2;
		$dest_documents = '../documents/';
		$dossier = '';
		$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'] . '/' : NULL;
		if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/' . $multi) === false) {
			@mkdir('../documents/' . $multi);
			$dest_documents .= $multi;
			$niv_arbo_tmp++;
		} elseif ((isset($multisite) && $multisite == 'y')) {
			$dest_documents .= $multi;
			$niv_arbo_tmp++;
		}

		//$type_notice="c";
		if ($type_notice == 'c') {
			$dest_documents .= "/cl" . $id_groupe;
		} else {
			$dest_documents .= "/cl_dev" . $id_groupe;
		}

		if (!file_exists($dest_documents)) {
			mkdir($dest_documents);
			creation_index_redir_login($dest_documents, $niv_arbo_tmp);
		}

		$stream_context = get_stream_context();

		$chaine = "";
		$tab_tmp = preg_split('/"/', $contenu_cor);
		for ($loop = 0; $loop < count($tab_tmp); $loop++) {
			if ((preg_match("|^http://latex.codecogs.com/|", $tab_tmp[$loop])) ||
				(preg_match("|^https://latex.codecogs.com/|", $tab_tmp[$loop]))||
				(preg_match("|^https://latex2image-output.s3.amazonaws.com/|", $tab_tmp[$loop]))) {
				$erreur = "n";
				$extension_fichier_formule = "gif";
				if ((preg_match("|^http://latex.codecogs.com/gif.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/gif.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "gif";
				} elseif ((preg_match("|^http://latex.codecogs.com/png.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/png.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "png";
				} elseif ((preg_match("|^http://latex.codecogs.com/swf.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/swf.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "swf";
				} elseif ((preg_match("|^http://latex.codecogs.com/emf.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/emf.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "emf";
				} elseif ((preg_match("|^http://latex.codecogs.com/pdf.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/pdf.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "pdf";
				} elseif ((preg_match("|^http://latex.codecogs.com/svg.latex|", $tab_tmp[$loop])) ||
					(preg_match("|^https://latex.codecogs.com/svg.latex|", $tab_tmp[$loop]))) {
					$extension_fichier_formule = "svg";
				}
				elseif (preg_match("|^https://latex2image-output.s3.amazonaws.com/|", $tab_tmp[$loop])) {
					$extension_fichier_formule = "png";
				}

				// Eviter les doublons:
				$nom_tmp = strftime("%Y%m%d_%H%M%S");
				$nom_tmp0 = $nom_tmp;
				$cpt = 1;
				while (file_exists($dest_documents . "/" . $nom_tmp . "." . $extension_fichier_formule)) {
					$nom_tmp = $nom_tmp0 . "_" . $cpt;
					if ($cpt > 100) {
						$erreur = "y";
					}
					$cpt++;
				}

				// Telechargement du fichier:
				if ($erreur == "n") {
					$morceau_courant = $dest_documents . "/" . $nom_tmp . "." . $extension_fichier_formule;
					// On a tendance à récupérer des chemins du type ../documents//cl2675/20131101_142603.gif
					// Le // n'est pas très propre...
					$morceau_courant = preg_replace("|/{2,}|", "/", $morceau_courant);
					/*
					$f=fopen("/tmp/formule.txt", "a+");
					fwrite($f, strftime('%Y%m%d %H%M%S')." : ".$morceau_courant."\n");
					fclose($f);
					*/
					if ($stream_context == "") {
						if (!copy($tab_tmp[$loop], $morceau_courant)) {
							$morceau_courant = $tab_tmp[$loop];
						}
					} else {
						if (!copy($tab_tmp[$loop], $morceau_courant, $stream_context)) {
							$morceau_courant = $tab_tmp[$loop];
						}
					}
				} else {
					$morceau_courant = $tab_tmp[$loop];
				}
			} else {
				$morceau_courant = $tab_tmp[$loop];
			}

			// On complète la chaine du contenu de la notice:
			if ($chaine != "") {
				$chaine .= "\"";
			}
			$chaine .= $morceau_courant;
		}
		$contenu_cor = $chaine;
	}

	return $contenu_cor;
}

/** Fonction destinée à contacter le serveur toutes les $intervalle_temps secondes
 *  et afficher si le serveur répond.
 *  Pour contrôler que le serveur est OK avant de valider une page avec beaucoup de saisies
 *
 * @param string $id_div_retour nom du div inséré
 * @param string $nom_js_func nom de la fonction js insérée
 * @param string $nom_var nom de la variable js compteur de secondes
 * @param string $taille la taille du div carré inséré
 * @param integer $intervalle_temps l'intervalle de temps en secondes entre deux contacts du serveur
 *
 * @return string le code HTML et JS
 */

function temoin_check_srv($id_div_retour = "retour_ping", $nom_js_func = "check_srv", $nom_var = "cpt_ping", $taille = 10, $intervalle_temps = 10) {
	global $gepiPath;

	echo "<div id='retour_ping' class='noprint' style='width:" . $taille . "px; height:" . $taille . "px; background-color:red; border:1px solid black; float:left; margin:1px; display:none;' title=\"Témoin de réponse du serveur: Un test est effectué toutes les $intervalle_temps secondes.
Si le témoin se maintient au rouge, c'est que le serveur n'est pas joignable.
Vous devriez dans ce cas (pour vous prémunir d'une perte de ce qui a été saisi et pas encore enregistré), copier dans un Bloc-notes tout ce qui n'a pas encore été enregistré.
Provoquer l'enregistrement/validation des données vers le serveur risque de se solder par un échec (si le serveur est indisponible, il ne recevra pas ce que vous enverrez et tout sera perdu).\"></div>\n";

	echo "<script type='text/javascript'>
	var $nom_var=0;

	document.getElementById('retour_ping').style.display='';

	function $nom_js_func() {

		if(($nom_var==$intervalle_temps)||($nom_var==0)) {
			$nom_var=0;

			$('$id_div_retour').style.opacity=1;
			$('$id_div_retour').innerHTML='';
	
			new Ajax.Updater($('$id_div_retour'),'$gepiPath/lib/echo.php?var=valeur',{method: 'get'});

		}
		else {
			$('$id_div_retour').style.opacity=1-$nom_var/$intervalle_temps;
		}
		$nom_var+=1;
		setTimeout('$nom_js_func()', 1000);
	}

	$nom_js_func();
</script>\n";
}

/** Fonction destinée à télécharger les images générées sur http://latex.codecogs.com/
 *  et à corriger les notices en conséquence pour pointer sur uneURL locale
 *
 * @param integer $eff_parcours
 *
 * @return string le code HTML relatant le nombre de notices corrigées.
 */

function correction_notices_cdt_formules_maths($eff_parcours) {
	global $mysqli;
	$tab_grp = array();

	$nb_corr = 0;
	$sql = "SELECT * FROM ct_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%' LIMIT $eff_parcours;";
	$res = mysqli_query($mysqli, $sql);
	while ($lig = $res->fetch_object()) {
		$id_ct = $lig->id_ct;
		$id_groupe = $lig->id_groupe;
		$contenu = $lig->contenu;
		$type_notice = "c";

		if (!isset($tab_grp[$id_groupe])) {
			$tab_grp[$id_groupe] = get_group($id_groupe);
		}

		$contenu_corrige = get_img_formules_math($contenu, $id_groupe, $type_notice);
		$sql = "UPDATE ct_entry SET contenu='" . $mysqli->real_escape_string($contenu_corrige) . "' WHERE id_ct='$id_ct';";
		$res_ct = mysqli_query($mysqli, $sql);
		if (!$res_ct) {
			echo "<div style='border:1px solid red; margin:3px;'>";
			echo "<p style='color:red;'>ERREUR sur<br />$sql";
			echo "</div>\n";
		} else {
			echo "<p>Correction sur une notice de <strong>compte-rendu</strong> en " . $tab_grp[$id_groupe]['name'] . " en " . $tab_grp[$id_groupe]['classlist_string'] . " : " . strftime("%d/%m/%Y", $lig->date_ct) . "<br />\n";
			$nb_corr++;
		}
		flush();
	}
	$res->close();
	echo "<p>$nb_corr corrections effectuées sur 'ct_entry'.</p>";

	$nb_corr = 0;
	$sql = "SELECT * FROM ct_devoirs_entry WHERE contenu LIKE '%src=\"http://latex.codecogs.com/%' OR contenu LIKE '%src=\"https://latex.codecogs.com/%' LIMIT $eff_parcours;";
	$res = mysqli_query($mysqli, $sql);
	while ($lig = $res->fetch_object()) {
		$id_ct = $lig->id_ct;
		$id_groupe = $lig->id_groupe;
		$contenu = $lig->contenu;
		$type_notice = "t";

		if (!isset($tab_grp[$id_groupe])) {
			$tab_grp[$id_groupe] = get_group($id_groupe);
		}

		$contenu_corrige = get_img_formules_math($contenu, $id_groupe, $type_notice);
		$sql = "UPDATE ct_devoirs_entry SET contenu='" . $mysqli->real_escape_string($contenu_corrige) . "' WHERE id_ct='$id_ct';";
		$res_ct = mysqli_query($mysqli, $sql);
		if (!$res_ct) {
			echo "<div style='border:1px solid red; margin:3px;'>";
			echo "<p style='color:red;'>ERREUR sur<br />$sql";
			echo "</div>\n";
		} else {
			echo "<p>Correction sur une notice de <strong>devoir</strong> en " . $tab_grp[$id_groupe]['name'] . " en " . $tab_grp[$id_groupe]['classlist_string'] . " : " . strftime("%d/%m/%Y", $lig->date_ct) . "<br />\n";
			$nb_corr++;
		}
		flush();
	}
	$res->close();
	echo "<p>$nb_corr corrections effectuées sur 'ct_devoirs_entry'.</p>";
}


/** Fonction destinée à retourner un tableau PHP des numéros de téléphone responsable (et élève)
 *
 * @param string $ele_login Login de l'élève
 *
 * @return array Tableau PHP des numéros de tel.
 */
function get_tel_resp_ele($ele_login) {
	global $mysqli;
	$tab_tel = array();

	$cpt_resp = 0;

	$sql = "SELECT rp.*, r.resp_legal, e.tel_pers AS ele_tel_pers, e.tel_port AS ele_tel_port, e.tel_prof AS ele_tel_prof FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND (r.resp_legal='1' OR r.resp_legal='2') ORDER BY r.resp_legal;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab_tel['responsable'][$cpt_resp] = array();
			$tab_tel['responsable'][$cpt_resp]['pers_id'] = $lig->pers_id;
			$tab_tel['responsable'][$cpt_resp]['resp_legal'] = $lig->resp_legal;
			$tab_tel['responsable'][$cpt_resp]['civ_nom_prenom'] = $lig->civilite . " " . casse_mot($lig->nom, 'maj') . " " . casse_mot($lig->prenom, 'majf2');
			if ($lig->tel_pers != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_pers'] = $lig->tel_pers;
			}
			if ($lig->tel_port != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_port'] = $lig->tel_port;
			}
			if ($lig->tel_prof != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_prof'] = $lig->tel_prof;
			}

			// On va remplir plusieurs fois les champs suivants (mais avec les mêmes valeurs) s'il y a plusieurs responsables
			if ((getSettingAOui('ele_tel_pers')) && ($lig->ele_tel_pers != '')) {
				$tab_tel['eleve']['tel_pers'] = $lig->ele_tel_pers;
			}
			if ((getSettingAOui('ele_tel_port')) && ($lig->ele_tel_port != '')) {
				$tab_tel['eleve']['tel_port'] = $lig->ele_tel_port;
			}
			if ((getSettingAOui('ele_tel_prof')) && ($lig->ele_tel_prof != '')) {
				$tab_tel['eleve']['tel_prof'] = $lig->ele_tel_prof;
			}
			$cpt_resp++;
		}
		$res->close();
	}

	$sql = "SELECT rp.*, r.resp_legal, e.tel_pers AS ele_tel_pers, e.tel_port AS ele_tel_port, e.tel_prof AS ele_tel_prof FROM resp_pers rp, responsables2 r, eleves e WHERE e.login='$ele_login' AND e.ele_id=r.ele_id AND r.pers_id=rp.pers_id AND resp_legal='0' ORDER BY rp.civilite, rp.nom, rp.prenom;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab_tel['responsable'][$cpt_resp] = array();
			$tab_tel['responsable'][$cpt_resp]['pers_id'] = $lig->pers_id;
			$tab_tel['responsable'][$cpt_resp]['resp_legal'] = $lig->resp_legal;
			$tab_tel['responsable'][$cpt_resp]['civ_nom_prenom'] = $lig->civilite . " " . casse_mot($lig->nom, 'maj') . " " . casse_mot($lig->prenom, 'majf2');
			if ($lig->tel_pers != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_pers'] = $lig->tel_pers;
			}
			if ($lig->tel_port != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_port'] = $lig->tel_port;
			}
			if ($lig->tel_prof != '') {
				$tab_tel['responsable'][$cpt_resp]['tel_prof'] = $lig->tel_prof;
			}

			// On va remplir plusieurs fois les champs suivants (mais avec les mêmes valeurs) s'il y a plusieurs responsables
			if ((getSettingAOui('ele_tel_pers')) && ($lig->ele_tel_pers != '')) {
				$tab_tel['eleve']['tel_pers'] = $lig->ele_tel_pers;
			}
			if ((getSettingAOui('ele_tel_port')) && ($lig->ele_tel_port != '')) {
				$tab_tel['eleve']['tel_port'] = $lig->ele_tel_port;
			}
			if ((getSettingAOui('ele_tel_prof')) && ($lig->ele_tel_prof != '')) {
				$tab_tel['eleve']['tel_prof'] = $lig->ele_tel_prof;
			}
			$cpt_resp++;
		}
		$res->close();
	}

	return $tab_tel;
}

/** Fonction destinée à retourner un tableau HTML des numéros de téléphone responsable (et élève)
 *
 * @param string $ele_login Login de l'élève
 *
 * @return array Tableau HTML des numéros de tel.
 */
function tableau_tel_resp_ele($ele_login) {
	global $tabdiv_infobulle;
	global $mysqli;
	global $gepiPath;

	// Appeler, avant la présente fonction necessaire_modif_tel_resp_ele() parce qu'on peut appeler plusieurs fois tableau_tel_resp_ele() dans la même page.

	$retour = "";
	$tab_tel = get_tel_resp_ele($ele_login);

	$tab_style[1] = "impair";
	$tab_style[-1] = "pair";

	if (((isset($tab_tel['responsable'])) && (count($tab_tel['responsable']) > 0)) || ((isset($tab_tel['eleve'])) && (count($tab_tel['eleve']) > 0))) {
		// 20170724 : Ajouter le script javascript et l'infobulle, d'où des tab_div_infobulle en global,...
		$acces_saisie_tel_resp = acces_saisie_telephone("responsable");
		$acces_saisie_tel_ele = acces_saisie_telephone("eleve");
		/*
		if($acces_saisie_tel_resp||$acces_saisie_tel_ele) {
			$retour.="";
		}
		*/

		$retour .= "<table class='boireaus' summary='Tableau des numéros de téléphone'>\n";
		//$retour.="<table class='tb_absences' summary='Tableau des numéros de telephone'>\n";
		$retour .= "<tr>\n";
		$retour .= "<th></th>\n";
		$retour .= "<th>Identité</th>\n";
		$retour .= "<th>Personnel</th>\n";
		$retour .= "<th>Portable</th>\n";
		$retour .= "<th>Professionnel</th>\n";
		// 20170724 : Ajouter une colonne si l'utilisateur a le droit de saisir/modifier les numéros
		if ($acces_saisie_tel_resp || $acces_saisie_tel_ele) {
			$retour .= "<th title=\"Modifier/corriger les numéros de téléphone, email\"><img src='" . $gepiPath . "/images/edit16.png' class='icone16' alt='Éditer' /></th>\n";
		}
		$retour .= "</tr>\n";

		$alt = 1;
		//foreach($tab_tel['responsable'] as $resp_legal => $tab_resp_legal) {
		for ($i = 0; $i < count($tab_tel['responsable']); $i++) {
			$alt = $alt * (-1);
			$retour .= "<tr class='lig$alt white_hover'>\n";
			//$retour.="<tr class='".$tab_style[$alt]." white_hover'>\n";
			$retour .= "<td title='Numéro de responsable légal'>" . $tab_tel['responsable'][$i]['resp_legal'] . "</td>\n";
			$retour .= "<td>" . $tab_tel['responsable'][$i]['civ_nom_prenom'] . "</td>\n";
			$retour .= "<td>";
			if (isset($tab_tel['responsable'][$i]['tel_pers'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['responsable'][$i]['tel_pers']);
			}
			$retour .= "</td>\n";
			$retour .= "<td>";
			if (isset($tab_tel['responsable'][$i]['tel_port'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['responsable'][$i]['tel_port']);
			}
			$retour .= "</td>\n";
			$retour .= "<td>";
			if (isset($tab_tel['responsable'][$i]['tel_prof'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['responsable'][$i]['tel_prof']);
			}
			$retour .= "</td>\n";
			// 20170724 : Ajouter une colonne si l'utilisateur a le droit de saisir/modifier les numéros
			if ($acces_saisie_tel_resp || $acces_saisie_tel_ele) {
				$retour .= "<td title=\"Modifier/corriger les numéros de téléphone, email\">";
				if ($acces_saisie_tel_resp) {
					$retour .= "<a href='$gepiPath/gestion/saisie_contact.php?pers_id=" . $tab_tel['responsable'][$i]['pers_id'] . "' onclick=\"affiche_corrige_tel_resp(" . $tab_tel['responsable'][$i]['pers_id'] . ");return false;\" target='_blank'><img src='" . $gepiPath . "/images/edit16.png' class='icone16' alt='Éditer' /></a>";
				}
				$retour .= "</td>\n";
			}
			$retour .= "</tr>\n";
		}

		if (isset($tab_tel['eleve'])) {
			$alt = $alt * (-1);
			$retour .= "<tr class='lig$alt white_hover'>\n";
			$retour .= "<td colspan='2'>Élève</td>\n";

			$retour .= "<td>";
			if (isset($tab_tel['eleve']['tel_pers'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['eleve']['tel_pers']);
			}
			$retour .= "</td>\n";

			$retour .= "<td>";
			if (isset($tab_tel['eleve']['tel_port'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['eleve']['tel_port']);
			}
			$retour .= "</td>\n";

			$retour .= "<td>";
			if (isset($tab_tel['eleve']['tel_prof'])) {
				$retour .= affiche_numero_tel_sous_forme_classique($tab_tel['eleve']['tel_prof']);
			}
			$retour .= "</td>\n";

			// 20170724 : Ajouter une colonne si l'utilisateur a le droit de saisir/modifier les numéros
			if ($acces_saisie_tel_resp || $acces_saisie_tel_ele) {
				$retour .= "<td title=\"Modifier/corriger les numéros de téléphone, email\">";
				if ($acces_saisie_tel_ele) {
					$retour .= "<a href='$gepiPath/gestion/saisie_contact.php?login_ele=" . $ele_login . "' onclick=\"affiche_corrige_tel_ele('" . $ele_login . "');return false;\" target='_blank'><img src='" . $gepiPath . "/images/edit16.png' class='icone16' alt='Éditer' /></a>";
				}
				$retour .= "</td>\n";
			}

			$retour .= "</tr>\n";
		}
		$retour .= "</table>\n";
	} else {
		$retour .= "<p style='color:red'>Aucun numéro de téléphone n'a été trouvé.</p>\n";
	}
	return $retour;
}

/** Fonction destinée à retourner un tableau des dimensions et type d'une image après redimensionnement
 *
 * @param string $image chemin de l'image
 * @param integer $dim_max_largeur la largeur maximale de l'image
 * @param integer $dim_max_hauteur la hauteur maximale de l'image
 * @param string $mode le type de redimensionnement:
 *                      <vide> la hauteur ne doit pas dépasser $dim_max_hauteur, et la largeur retournées ne doit pas dépasser la $dim_max_largeur
 *                      'largeur' on redimensionne en forçant la largeur à $dim_max_largeur
 *                      'hauteur' on redimensionne en forçant la hauteur à $dim_max_hauteur
 *
 * @return array Tableau des dimensions et type
 */
function redim_img($image, $dim_max_largeur, $dim_max_hauteur, $mode = "") {
	$info_image = getimagesize($image);

	$largeur = $info_image[0];
	$hauteur = $info_image[1];

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $dim_max_largeur;
	$ratio_h = $hauteur / $dim_max_hauteur;
	if ($mode == "") {
		$ratio = ($ratio_l > $ratio_h) ? $ratio_l : $ratio_h;
	} elseif ($mode == "largeur") {
		$ratio = $ratio_l;
	} else {
		$ratio = $ratio_h;
	}

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = round($largeur / $ratio);
	$nouvelle_hauteur = round($hauteur / $ratio);

	$type_img = "";
	if (isset($info_image[2])) {
		// 1 = GIF, 2 = JPG, 3 = PNG, 4 = SWF, 5 = PSD, 6 = BMP, 7 = TIFF(orden de bytes intel), 8 = TIFF(orden de bytes motorola), 9 = JPC, 10 = JP2, 11 = JPX, 12 = JB2, 13 = SWC, 14 = IFF, 15 = WBMP, 16 = XBM.
		$type_img = $info_image[2];
	}

	return array($nouvelle_largeur, $nouvelle_hauteur, $type_img);
}

/** Fonction destinée à supprimer les accents HTML dans des enregistrements de la table setting
 *
 * @param string $name champ name dans la table setting
 *
 * @return integer 0 Correction inutile, 1 Correction réussie, 2 Echec de la correction.
 */

function virer_accents_html_setting($name) {
	global $mysqli;
	$tab = array_flip(get_html_translation_table(HTML_ENTITIES));
	$valeur = getSettingValue($name);
	$correction = ensure_utf8(strtr($valeur, $tab));
	/*
	$f=fopen("/tmp/correction_fb.txt", "a+");
	fwrite($f, "=========================================================================\n");
	fwrite($f, "name=$name\n");
	fwrite($f, "value=$valeur\n");
	fwrite($f, "correction=$correction\n");
	fclose($f);
	*/
	if ($valeur != $correction) {
		if (saveSetting($name, $mysqli->real_escape_string($correction))) {
			return 1;
		} else {
			return 2;
		}
	} else {
		return 0;
	}
}

/** Fonction destinée à enregistrer des détails sur la mise à jour Sconet en cours
 *
 * @param string $texte Texte à ajouter au log en cours
 * @param string $fin 'y' ou 'n' pour mettre à jour la date de fin
 *
 * @return boolean Succès ou échec de l'enregistrement.
 */

function enregistre_log_maj_sconet($texte, $fin = "n") {
	global $mysqli;
	$ts_maj_sconet = getSettingValue('ts_maj_sconet');
	if ($ts_maj_sconet == '') {
		return false;
	} else {
		$sql = "SELECT * FROM log_maj_sconet WHERE date_debut='$ts_maj_sconet';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$lig = $res->fetch_object();

			$sql = "UPDATE log_maj_sconet SET texte='" . $mysqli->real_escape_string($lig->texte . $texte) . "'";
			if ($fin != "n") {
				$sql .= ", date_fin='" . strftime("%Y-%m-%d %H:%M:%S") . "'";
			}
			$sql .= " WHERE date_debut='$ts_maj_sconet';";
			$res->close();
		} else {
			$sql = "INSERT INTO log_maj_sconet SET date_debut='$ts_maj_sconet', login='" . $_SESSION['login'] . "', date_fin='0000-00-00 00:00:00', texte='" . $mysqli->real_escape_string($texte) . "';";
		}
		$res = mysqli_query($mysqli, $sql);
		if ($res) {
			return true;
		} else {
			return false;
		}
	}
}

/** Fonction destinée à faire un test in_array() insensible à la casse
 *
 * @param string $chaine chaine
 * @param array $tableau tableau dans lequel on cherche la chaine
 *
 * @return boolean true/false
 */

function in_array_i($chaine, $tableau) {
	$retour = false;
	$chaine = mb_strtolower($chaine);
	foreach ($tableau as $key => $value) {
		if ($chaine == mb_strtolower($value)) {
			$retour = true;
			break;
		}
	}
	return $retour;
}

/** Fonction destinée à tester si un parent est responsable d'un élève
 *
 * @param string $login_eleve Login de l'élève
 * @param string $login_resp Login du responsable
 * @param string $pers_id Identifiant pers_id du responsable
 * @param string $meme_en_resp_legal_0 'y'
 *               (on récupère même les enfants dont $resp_login est resp_legal=0)
 *               'yy' (on récupère aussi les resp_legal=0 mais seulement s'ils ont l'accès aux données en tant qu'utilisateur
 *               ou 'n' (on ne récupère que les enfants dont $resp_login est resp_legal=1 ou 2)
 *
 *
 * @return boolean true/false
 */

function is_responsable($login_eleve, $login_resp = "", $pers_id = "", $meme_en_resp_legal_0 = "n") {
	global $mysqli;
	$retour = false;
	if ($login_resp != "") {
		$sql = "(SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2'))";
		if ($meme_en_resp_legal_0 == "y") {
			$sql .= " UNION (SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0')";
		} elseif ($meme_en_resp_legal_0 == "yy") {
			$sql .= " UNION (SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e WHERE r.pers_id=rp.pers_id AND rp.login='$login_resp' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0' AND r.acces_sp='y')";
		}
		$sql .= ";";
		$test = mysqli_query($mysqli, $sql);
		if ($test->num_rows > 0) {
			$test->close();
			$retour = true;
		}
	} elseif ($pers_id != "") {
		$sql = "(SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2'))";
		if ($meme_en_resp_legal_0 == "y") {
			$sql .= " UNION (SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0')";
		} elseif ($meme_en_resp_legal_0 == "yy") {
			$sql .= " UNION (SELECT 1=1 FROM responsables2 r, eleves e WHERE r.pers_id='$pers_id' AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND r.resp_legal='0' AND r.acces_sp='y')";
		}
		$sql .= ";";
		$test = mysqli_query($mysqli, $sql);
		if ($test->num_rows > 0) {
			$test->close();
			$retour = true;
		}
	}
	return $retour;
}

// http://www.siteduzero.com/tutoriel-3-56199-les-captchas-textuels.html
function captchaMath() {
	$n1 = mt_rand(0, 10);
	$n2 = mt_rand(0, 10);
	$nbrFr = array('zero', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix');
	$resultat = $n1 + $n2;
	$phrase = $nbrFr[$n1] . ' plus ' . $nbrFr[$n2];

	return array($resultat, $phrase);
}

function captcha() {
	list($resultat, $phrase) = captchaMath();
	$_SESSION['captcha'] = $resultat;
	return $phrase;
}

/** Fonction destinée à retourner un tableau des ouvertures par période en
 *  consultation parent/élève pour telle classe sur telle à telle période
 *
 * @param integer $periode1 La première période à tester
 * @param integer $periode2 La dernière période à tester
 * @param integer $id_classe L'identifiant de la classe à tester
 * @param string $statut Le statut 'responsable' ou 'eleve'
 *                           Si le statut est vide, on prend le statut de
 *                           l'utilisateur connecté.
 * @param string $login_ele Le login de l'élève, utilisé dans le cas d'une ouverture manuelle par élève
 *
 * @return array Tableau avec les numéros de période en indice et 'y' ou 'n'
 *               selon que les appréciations sont ou non accessibles
 */
function acces_appreciations($periode1, $periode2, $id_classe, $statut = '', $login_ele = "") {
	global $mysqli;
	global $delais_apres_cloture;
	global $date_ouverture_acces_app_classe;

	if ($delais_apres_cloture === "") {
		$delais_apres_cloture = getSettingValue('delais_apres_cloture');
	}

	$tab_acces_app = array();

	if ($statut == "") {
		$statut = $_SESSION['statut'];
	}

	if (($statut == 'eleve') || ($statut == 'responsable')) {
		if (getSettingValue('acces_app_ele_resp') == 'periode_close') {
			$timestamp_limite = time() - $delais_apres_cloture * 24 * 3600;
			for ($i = $periode1; $i <= $periode2; $i++) {
				$sql = "SELECT 1=1 FROM periodes WHERE UNIX_TIMESTAMP(date_verrouillage)<='" . $timestamp_limite . "' AND id_classe='$id_classe' AND num_periode='$i' AND verouiller='O';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if ($res->num_rows > 0) {
					$res->close();
					$tab_acces_app[$i] = "y";
				} else {
					$tab_acces_app[$i] = "n";
				}
			}
		} elseif (getSettingValue('acces_app_ele_resp') == 'date') {
			for ($i = $periode1; $i <= $periode2; $i++) {
				$sql = "SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND
													statut='" . $statut . "' AND
													periode='$i';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if ($res) {
					if ($res->num_rows > 0) {
						$lig = $res->fetch_object();
						//echo "\$lig->acces=$lig->acces<br />";
						if ($lig->acces == "date") {
							//echo "<p>Période $i: Date limite: $lig->date<br />";
							$tab_date = explode("-", $lig->date);
							$timestamp_limite = mktime(0, 0, 0, $tab_date[1], $tab_date[2], $tab_date[0]);
							//echo "$timestamp_limite<br />";
							$timestamp_courant = time();
							//echo "$timestamp_courant<br />";

							$date_ouverture_acces_app_classe[$i] = $lig->date;

							if ($timestamp_courant > $timestamp_limite) {
								$tab_acces_app[$i] = "y";
							} else {
								$tab_acces_app[$i] = "n";
							}
						} else {
							$tab_acces_app[$i] = "n";
						}
						$res->close();
					} else {
						$tab_acces_app[$i] = "n";
					}
				} else {
					$tab_acces_app[$i] = "n";
				}
			}
		} elseif (getSettingValue('acces_app_ele_resp') == 'manuel_individuel') {
			// Ouverture manuelle élève par élève
			for ($i = $periode1; $i <= $periode2; $i++) {
				if ($login_ele != "") {
					$sql = "SELECT * FROM matieres_appreciations_acces_eleve WHERE login='" . $login_ele . "' AND
													periode='$i';";
					//echo "$sql<br />";
					$res = mysqli_query($mysqli, $sql);
					if ($res) {
						if ($res->num_rows > 0) {
							$lig = $res->fetch_object();
							//echo "\$lig->acces=$lig->acces<br />";
							if ($lig->acces == "y") {
								$tab_acces_app[$i] = "y";
							} else {
								$tab_acces_app[$i] = "n";
							}
						} else {
							$tab_acces_app[$i] = "n";
						}
						$res->close();
					} else {
						$tab_acces_app[$i] = "n";
					}
				} else {
					$sql = "SELECT maae.* FROM matieres_appreciations_acces_eleve maae, j_eleves_classes jec WHERE maae.login=jec.login AND 
													jec.id_classe='" . $id_classe . "' AND 
													jec.periode=maae.periode AND 
													maae.periode='$i';";
					//echo "$sql<br />";
					$res = mysqli_query($mysqli, $sql);
					if ($res) {
						if ($res->num_rows > 0) {
							$tmp_tab_acces = array();
							while ($lig = $res->fetch_object()) {
								$tmp_tab_acces[$lig->acces][] = $lig->login;
							}

							if ((isset($tmp_tab_acces['y'])) && (isset($tmp_tab_acces['n']))) {
								$tab_acces_app[$i] = count($tmp_tab_acces['y']);
							} elseif (isset($tmp_tab_acces['y'])) {
								$tab_acces_app[$i] = "y";
							} else {
								$tab_acces_app[$i] = "n";
							}
						} else {
							$tab_acces_app[$i] = "n";
						}
						$res->close();
					} else {
						$tab_acces_app[$i] = "n";
					}
				}
			}
		} else {
			// Ouverture manuelle
			for ($i = $periode1; $i <= $periode2; $i++) {
				$sql = "SELECT * FROM matieres_appreciations_acces WHERE id_classe='$id_classe' AND
													statut='" . $statut . "' AND
													periode='$i';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if ($res) {
					if ($res->num_rows > 0) {
						$lig = $res->fetch_object();
						//echo "\$lig->acces=$lig->acces<br />";
						if ($lig->acces == "y") {
							$tab_acces_app[$i] = "y";
						} else {
							$tab_acces_app[$i] = "n";
						}
					} else {
						$tab_acces_app[$i] = "n";
					}
					$res->close();
				} else {
					$tab_acces_app[$i] = "n";
				}
			}
		}
	} else {
		// Pas de limitations d'accès pour les autres statuts.
		for ($i = $periode1; $i <= $periode2; $i++) {
			$tab_acces_app[$i] = "y";
		}
	}
	return $tab_acces_app;
}


/** Fonction destinée à tester si les responsables légaux habitent à des adresses distinctes
 *
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function responsables_adresses_separees($login_eleve) {
	global $mysqli;
	$retour = false;

	$sql = "SELECT DISTINCT adr1, adr2, adr3, adr4, cp, commune, pays FROM resp_adr ra, resp_pers rp, responsables2 r, eleves e WHERE ra.adr_id=rp.adr_id AND r.pers_id=rp.pers_id AND e.ele_id=r.ele_id AND e.login='$login_eleve' AND (r.resp_legal='1' OR r.resp_legal='2');";
	//echo "$sql<br />";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 1) {
		$test->close();
		$retour = true;
	}

	return $retour;
}

/** Fonction destinée à récupérer le rang de l'élève
 *
 * @param string $login_eleve Login de l'élève
 * @param integer $id_classe Identifiant de la classe
 * @param integer $periode_num Numéro de la période
 * @param string $forcer_recalcul "y" ou "n" Forcer le recalcul du rang avant extraction du rang
 * @param string $recalcul_si_rang_nul "y" ou "n"
 *
 * @return integer rang de l'élève
 */
function get_rang_eleve($login_eleve, $id_classe, $periode_num, $forcer_recalcul = "n", $recalcul_si_rang_nul = "n") {
	global $mysqli;
	global $affiche_categories, $test_coef;
	$retour = 0;

	$recalcul_rang = "";
	for ($loop = 1; $loop <= $periode_num; $loop++) {
		$recalcul_rang .= "y";
	}

	if ($forcer_recalcul == "y") {
		$sql_coef = "SELECT coef FROM j_groupes_classes WHERE (id_classe='" . $id_classe . "' and coef > 0)";
		$query_coef = mysqli_query($mysqli, $sql_coef);
		$test_coef = $query_coef->num_rows;

		$sql = "UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

		include("../lib/calcul_rang.inc.php");
		$test_coef->close();
	}

	$sql = "SELECT rang FROM j_eleves_classes WHERE periode='" . $periode_num . "' AND id_classe='" . $id_classe . "' AND login = '" . $login_eleve . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$obj = $res->fetch_object();
		$res->close();
		$retour = $obj->rang;
		if (($retour == 0) && ($recalcul_si_rang_nul == 'y')) {
			$sql_coef = "SELECT coef FROM j_groupes_classes WHERE (id_classe='" . $id_classe . "' and coef > 0)";
			$res_coef = mysqli_query($mysqli, $sql_coef);
			$test_coef = $res_coef->num_rows;
			$res_coef->close();
			$sql = "UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

			include("../lib/calcul_rang.inc.php");

			$sql = "SELECT rang FROM j_eleves_classes WHERE periode='" . $periode_num . "' AND id_classe='" . $id_classe . "' AND login = '" . $login_eleve . "';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$obj = $res->fetch_object();
				$retour = $obj->rang;
				$res->close();
			}
		}
	} elseif ($recalcul_si_rang_nul == 'y') {
		$sql_coef = "SELECT coef FROM j_groupes_classes WHERE (id_classe='" . $id_classe . "' and coef > 0)";
		$query_coef = mysqli_query($mysqli, $sql_coef);
		$test_coef = $query_coef->num_rows;
		$query_coef->close();

		$sql = "UPDATE groupes SET recalcul_rang='$recalcul_rang' WHERE id in (SELECT id_groupe FROM j_groupes_classes WHERE id_classe='$id_classe');";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		// Les rangs seront recalculés lors de l'appel à calcul_rang.inc.php

		include("../lib/calcul_rang.inc.php");

		$sql = "SELECT rang FROM j_eleves_classes WHERE periode='" . $periode_num . "' AND id_classe='" . $id_classe . "' AND login = '" . $login_eleve . "';";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$obj = $res->fetch_object();
			$retour = $obj->rang;
			$res->close();
		}
	}

	return $retour;
}

/** Fonction destinée à retourner pour un élève, un tableau des classes et dates de périodes en fonction du numéro de période
 *
 * @param string $login_eleve Login de l'élève
 *
 * @return array Tableau d'indice num_periode
 */

function get_class_dates_from_ele_login($login_eleve) {
	global $mysqli;
	$tab = array();

	$sql = "SELECT p.*, c.classe, c.nom_complet FROM periodes p, j_eleves_classes jec, classes c WHERE jec.id_classe=p.id_classe AND jec.periode=p.num_periode AND c.id=jec.id_classe AND jec.login='" . $login_eleve . "' ORDER BY p.num_periode;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[$lig->num_periode]['id_classe'] = $lig->id_classe;
			$tab[$lig->num_periode]['classe'] = $lig->classe;
			$tab[$lig->num_periode]['nom_complet'] = $lig->nom_complet;
			$tab[$lig->num_periode]['date_fin'] = $lig->date_fin;
		}
		$res->close();
	}

	return $tab;
}

/** Fonction destinée à passer outre le paramètre PHP session.gc_maxlifetime
 *  s'il est inférieur au paramétrage sessionMaxLength de la table setting
 */

function maintien_de_la_session() {
	global $gepiPath;

	$session_gc_maxlifetime = ini_get("session.gc_maxlifetime");
	// On fait réagir 3min avant la fin de session PHP
	$nb_sec = max(60, $session_gc_maxlifetime - 60 * 3);

	echo "<p>
	<span id='span_maintien_session'></span>
	</p>

<script type='text/javascript'>
	var nb_millisec_maintien_session=$nb_sec*1000;

	function function_maintien_session() {
		new Ajax.Updater($('span_maintien_session'),'$gepiPath/lib/echo.php?var=maintien_session',{method: 'get'});
		setTimeout('function_maintien_session()', nb_millisec_maintien_session);
	}

	setTimeout('function_maintien_session()', nb_millisec_maintien_session);
</script>\n";
}

/** Fonction destinée à récupérer la liste des enseignants associés à une matière
 */
function get_profs_for_matiere($matiere) {
	global $mysqli;
	$tab = array();

	$sql = "SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_professeurs_matieres jpm WHERE jpm.id_professeur=u.login AND jpm.id_matiere='" . $matiere . "' ORDER BY u.nom, u.prenom;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$cpt = 0;
		while ($lig = $res->fetch_object()) {
			$tab[$cpt]['login'] = $lig->login;
			$tab[$cpt]['nom'] = $lig->nom;
			$tab[$cpt]['prenom'] = $lig->prenom;
			$tab[$cpt]['civilite'] = $lig->civilite;
			$tab[$cpt]['civ_nom_prenom'] = $lig->civilite . " " . casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
			$cpt++;
		}
		$res->close();
	}

	return $tab;
}

/** Fonction destinée à récupérer la liste des enseignants associés à une classe
 */
function get_profs_for_classe($id_classe) {
	global $mysqli;
	$tab = array();

	$sql = "SELECT DISTINCT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe=jgc.id_groupe AND jgc.id_classe='" . $id_classe . "' ORDER BY u.nom, u.prenom;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$cpt = 0;
		while ($lig = $res->fetch_object()) {
			$tab[$cpt]['login'] = $lig->login;
			$tab[$cpt]['nom'] = $lig->nom;
			$tab[$cpt]['prenom'] = $lig->prenom;
			$tab[$cpt]['civilite'] = $lig->civilite;
			$tab[$cpt]['civ_nom_prenom'] = $lig->civilite . " " . casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
			$cpt++;
		}
		$res->close();
	}

	return $tab;
}

/** Fonction destinée à générer des fichiers de debug
 *  Nécessite de modifier la valeur de $debug
 *
 * @param string $fichier Le chemin/fichier
 * @param string $mode Le mode (a+, w+,...)
 * @param string $texte Le texte à écrire
 */
function fwrite_debug($fichier, $mode, $texte) {
	$debug = "n";

	if ($debug == "y") {
		$f = fopen($fichier, $mode);
		fwrite($f, $texte);
		fclose($f);
	}
}

/** Fonction destinée à retourner la période courante associée à une classe
 *
 * @param integer $id_classe identifiant de la classe
 * @param integer $ts Timestamp unix (par défaut, si vide "", on prend le timestamp courant)
 * @param integer $valeur_par_defaut La valeur par défaut à prendre si aucun retour n'est trouvé dans les tables.
 * @param string $pour bulletins 'y' ou 'n' Si on cherche une période avec bulletins remplis, on se base sur la période suivante ouverte en saisie.
 *
 * @return integer Numéro de la période
 */
function cherche_periode_courante($id_classe, $ts = "", $valeur_par_defaut = "", $pour_bulletins = "n") {
	global $mysqli;
	//echo "<pre>\$ts=$ts</pre>";
	$retour = $valeur_par_defaut;

	if ($ts == "") {
		$ts = time();
	}

	$fich_debug = "/tmp/cherche_periode_courante.txt";
	fwrite_debug($fich_debug, "a+", "=================================================\n");

	$periode_trouvee = "n";
	if ($pour_bulletins == "y") {
		$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode ASC;";
		fwrite_debug($fich_debug, "a+", $sql . "\n");
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$num_per_temp = $lig->num_periode;
				if ($lig->verouiller == 'N') {
					if ($num_per_temp > 1) {
						$retour = $num_per_temp - 1;
					} else {
						// Si la première période est ouverte en saisie, on est en début d'année,
						// pas la peine d'espérer que les bulletins soient remplis
						$retour = 1;
					}
					$periode_trouvee = "y";
					break;
				}
			}
			$res->close();
		}

		fwrite_debug($fich_debug, "a+", "\$periode_trouvee=" . $periode_trouvee . "\n");

		if ($periode_trouvee == "n") {
			$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' ORDER BY num_periode DESC;";
			fwrite_debug($fich_debug, "a+", $sql . "\n");
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				while ($lig = $res->fetch_object()) {
					$num_per_temp = $lig->num_periode;
					if ($lig->verouiller == 'P') {
						$retour = $num_per_temp;
						$periode_trouvee = "y";
						break;
					}
				}
				$res->close();
			}
		}

		if ($periode_trouvee == "n") {
			$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND date_fin>FROM_UNIXTIME($ts) ORDER BY num_periode ASC LIMIT 1;";
			fwrite_debug($fich_debug, "a+", $sql . "\n");
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$obj = $res->fetch_object();
				$retour = $obj->num_periode;
				$periode_trouvee = "y";
				$res->close();
			}
		}

		if ($periode_trouvee == "n") {
			//$sql="select * from periodes where id_classe='$id_classe' and date_fin>CURRENT_TIMESTAMP order by num_periode ASC LIMIT 1;";
			//$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND date_fin<FROM_UNIXTIME($ts) ORDER BY num_periode DESC LIMIT 1;";
			$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND date_fin<FROM_UNIXTIME($ts) ORDER BY num_periode DESC LIMIT 1;";
			fwrite_debug($fich_debug, "a+", $sql . "\n");
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$obj = $res->fetch_object();
				$retour = $obj->num_periode;
				$res->close();
			}
		}
	} else {
		//$sql="select * from periodes where id_classe='$id_classe' and date_fin>CURRENT_TIMESTAMP order by num_periode ASC LIMIT 1;";
		$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND date_fin>FROM_UNIXTIME($ts) ORDER BY num_periode ASC LIMIT 1;";
		fwrite_debug($fich_debug, "a+", $sql . "\n");
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$obj = $res->fetch_object();
			$retour = $obj->num_periode;
			$res->close();
		} else {
			$sql = "SELECT p.num_periode FROM periodes p, edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND $ts<fin_calendrier_ts AND $ts>debut_calendrier_ts AND p.nom_periode=e.nom_calendrier AND p.id_classe='$id_classe';";
			//echo "$sql<br />";
			fwrite_debug($fich_debug, "a+", $sql . "\n");
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$obj = $res->fetch_object();
				$retour = $obj->num_periode;
				$res->close();
			}
		}
	}

	if (!is_numeric($retour)) {
		$retour = $valeur_par_defaut;
	}

	return $retour;
}

/** Fonction destinée à retourner la période courante associée à un élève
 *
 * @param string $login_eleve Le login de l'élève
 * @param integer $ts Timestamp unix (par défaut, si vide "", on prend le timestamp courant)
 * @param integer $valeur_par_defaut La valeur par défaut à prendre si aucun retour n'est trouvé dans les tables.
 * @param string $pour bulletins 'y' ou 'n' Si on cherche une période avec bulletins remplis, on se base sur la période suivante ouverte en saisie.
 *
 * @return integer Numéro de la période
 */
function cherche_periode_courante_eleve($login_eleve, $ts, $valeur_par_defaut = "", $pour_bulletins = "n") {
	global $mysqli;
	//echo "<pre>\$ts=$ts</pre>";
	$retour = $valeur_par_defaut;

	if ($ts == "") {
		$ts = time();
	}

	$fich_debug = "/tmp/cherche_periode_courante_eleve.txt";
	fwrite_debug($fich_debug, "a+", "=================================================\n");

	$periode_trouvee = "n";
	if ($pour_bulletins == "y") {
		$sql = "SELECT * FROM periodes p, j_eleves_classes jec WHERE p.id_classe=jec.id_classe AND jec.login='$login_eleve' ORDER BY num_periode ASC;";
		fwrite_debug($fich_debug, "a+", $sql . "\n");
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$num_per_temp = $lig->num_periode;
				if ($lig->verouiller == 'N') {
					if ($num_per_temp > 1) {
						$retour = $num_per_temp - 1;
					} else {
						// Si la première période est ouverte en saisie, on est en début d'année,
						// pas la peine d'espérer que les bulletins soient remplis
						$retour = 1;
					}
					$periode_trouvee = "y";
					break;
				}
			}
			$res->close();
		}

		fwrite_debug($fich_debug, "a+", "\$periode_trouvee=" . $periode_trouvee . "\n");

		if ($periode_trouvee == "n") {
			//$sql="select * from periodes where id_classe='$id_classe' and date_fin>CURRENT_TIMESTAMP order by num_periode ASC LIMIT 1;";
			$sql = "SELECT * FROM periodes p, j_eleves_classes jec WHERE p.id_classe=jec.id_classe AND jec.login='$login_eleve' AND date_fin<FROM_UNIXTIME($ts) ORDER BY num_periode DESC LIMIT 1;";
			fwrite_debug($fich_debug, "a+", $sql . "\n");
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$lig = $res->fetch_object();
				$retour = $lig->num_periode;
				$res->close();
			}
		}
	} else {
		//$sql="select * from periodes where id_classe='$id_classe' and date_fin>CURRENT_TIMESTAMP order by num_periode ASC LIMIT 1;";
		$sql = "SELECT * from periodes p, j_eleves_classes jec WHERE p.id_classe=jec.id_classe AND jec.login='$login_eleve' AND date_fin>FROM_UNIXTIME($ts) ORDER BY num_periode ASC LIMIT 1;";
		fwrite_debug($fich_debug, "a+", $sql . "\n");
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$lig = $res->fetch_object();
			$retour = $lig->num_periode;
			$res->close();
		}
	}

	if (!is_numeric($retour)) {
		$retour = $valeur_par_defaut;
	}

	return $retour;
}


/*
CREATE TABLE IF NOT EXISTS messagerie (
  id int(11) NOT NULL AUTO_INCREMENT,
  in_reply_to int(11) NOT NULL,
  login_src varchar(50) NOT NULL,
  login_dest varchar(50) NOT NULL,
  sujet varchar(100) NOT NULL,
  message text NOT NULL,
  date_msg timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  vu tinyint(4) NOT NULL,
  date_vu timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
*/

function enregistre_message($sujet, $message, $login_src, $login_dest, $date_visibilite = "", $in_reply_to = -1) {
	global $mysqli;
	$retour = "";

	$date_courante = strftime("%Y-%m-%d %H:%M:%S");
	if (($date_visibilite == "") || ($date_visibilite < $date_courante)) {
		$date_visibilite = $date_courante;
	}

	$sql = "INSERT INTO messagerie SET sujet='" . $mysqli->real_escape_string($sujet) . "',
									message='" . $mysqli->real_escape_string($message) . "',
									login_src='" . $login_src . "',
									login_dest='" . $login_dest . "',
									in_reply_to='" . $in_reply_to . "',
									date_msg='" . $date_courante . "',
									date_visibilite='" . $date_visibilite . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res) {
		$retour = $mysqli->insert_id;
	}
	return $retour;
}

/*
function form_saisie_message($in_reply_to=-1) {
	$chaine="<form action='../mod_alerte/form_message.php' method='post'>
		<fieldset style='border:1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>

		</fieldset>
	</form>\n";
}
*/

function affiche_historique_messages($login_src, $mode = "tous", $tri = "date") {
	global $mysqli;
	global $gepiPath;

	$retour = "";
	if ($mode == 'tous') {
		$sql = "SELECT * FROM messagerie WHERE login_src='$login_src'";
	} else {
		// Pour le moment aucun autre cas que Tous n'est géré
		$sql = "SELECT * FROM messagerie WHERE login_src='$login_src' ORDER BY date_msg DESC, login_dest ASC, sujet;";
	}

	if ($tri == "sujet") {
		$sql .= " ORDER BY sujet, date_msg DESC, login_dest ASC;";
	} elseif ($tri == "dest") {
		$sql .= " ORDER BY login_dest, date_msg DESC, login_dest ASC, sujet;";
	} elseif ($tri == "vu") {
		$sql .= " ORDER BY vu, date_msg DESC, login_dest ASC, sujet;";
	} else {
		$sql .= " ORDER BY date_msg DESC, login_dest ASC, sujet;";
	}
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		$retour = "<p>Aucun message.</p>";
	} else {
		$retour .= "<a name='tableau_historique_messages_envoyes'></a>
<table class='boireaus boireaus_alt'>
	<tr>
		<th><a href=\"javascript:trie_affiche_historique_messages('date')\" title='Trier par date'>Date</a></th>
		<th><a href=\"javascript:trie_affiche_historique_messages('dest')\" title='Trier par date'>Destinataire</a></th>
		<th><a href=\"javascript:trie_affiche_historique_messages('sujet')\" title='Trier par date'>Sujet</a></th>
		";
		if (peut_poster_message($_SESSION['statut'])) {
			$retour .= "<th title=\"En cliquant sur le texte du message souhaité, vous pouvez compléter le champ Message d'un message que vous êtes en train de rédiger.\">Message <img src='../images/icons/ico_ampoule.png' width='9' height='15' alt='' /></th>";
		} else {
			$retour .= "<th>Message</th>";
		}
		$retour .= "
		<th title='Témoin indiquant si votre message a été vu/lu'><a href=\"javascript:trie_affiche_historique_messages('vu')\" title='Trier selon que le message est lu ou non'>Lu/vu</a></th>
		<th>Relancer</th>
		<th title=\"Marquer le message comme clos/traité.
Cela permet d'indiquer au destinataire que le message peut ne pas être pris en compte.
Exemple: Si vous avez demandé à plusieurs destinataires à ce que tel élève vous soit envoyé,
         une fois l'élève vu, la lecture du message n'est plus nécessaire.\">Clore</th>
	</tr>";
		$cpt_ahm = 0;
		while ($lig = $res->fetch_object()) {
			$precision_visibilite = "";
			if ($lig->date_visibilite > $lig->date_msg) {
				$precision_visibilite = " title='Message du " . formate_date($lig->date_msg, 'y') . " visible à compter de " . formate_date($lig->date_visibilite, 'y') . "'";
			}
			$temoin_visibilite = "";
			if ($lig->date_visibilite > strftime("%Y-%m-%d %H:%M:%S")) {
				$temoin_visibilite = "<img src='../images/icons/flag.png' width='17' height='18' alt='Visibilité décalée' />";
			}
			$retour .= "
	<tr>
		<td$precision_visibilite>" . formate_date($lig->date_msg, 'y') . "$temoin_visibilite</td>
		<td>" . civ_nom_prenom($lig->login_dest) . "</td>
		<td>$lig->sujet</td>
		<td id='td_ahm_" . $cpt_ahm . "' onclick=\"copie_ahm($cpt_ahm)\">" . stripslashes(nl2br(preg_replace("/\\\\n/", "\n", $lig->message))) . "</td>
		<td id='td_lu_message_envoye_" . $lig->id . "'>";
			if ($lig->vu == 1) {
				$retour .= "<img src='../images/enabled.png' width='20' height='20' alt='Lu' title='Votre message a été lu/vu le " . formate_date($lig->date_vu, 'y') . "' /></td>
		<td id='td_relance_message_envoye_" . $lig->id . "'>
            <a href='$gepiPath/mod_alerte/form_message.php?mode=relancer&amp;mode_no_js=y&amp;id_msg=" . $lig->id . add_token_in_url() . "' onclick=\"relancer_message(" . $lig->id . ");return false;\" title=\"Relancer le message au même destinataire.
Concrètement, le témoin est juste remis à non lu.\" target='_blank'>
            <img src='../images/icons/forward.png' width='16' height='16' alt='Relancer' />
            </a>";
			} elseif ($lig->vu == 2) {
				$retour .= "<img src='../images/icons/securite.png' width='16' height='16' alt='Non lu/vu' title='Non lu/vu' /></td>
		<td>";
			} else {
				$retour .= "<img src='../images/disabled.png' width='20' height='20' alt='Non lu/vu' title='Non lu/vu' /></td>
		<td>";
			}
			$retour .= "</td>
		<td>
			<a href='$gepiPath/mod_alerte/form_message.php?mode=clore&amp;mode_no_js=y&amp;id_msg=" . $lig->id . add_token_in_url() . "' onclick=\"clore_message(" . $lig->id . ");return false;\"><img src='../images/icons/wizard.png' width='16' height='16' alt='Clore' /></a>
		</td>
	</tr>";
			$cpt_ahm++;
		}
		$res->close();
		$retour .= "</table>
<script type='text/javascript'>
	function copie_ahm(num) {
		if(document.getElementById('message_messagerie')) {
			document.getElementById('message_messagerie').innerHTML+=ahm_nettoyage(document.getElementById('td_ahm_'+num).innerHTML);
		}
	}

	function ahm_nettoyage(str) {
		//return str.replace(/<br\s*\/?>/mg,\"\\n\");
		chaine=str.replace(/<br\s*\/?>/mg,\"\\n\")
		chaine=chaine.replace(/\\r\\n\\r\\n/g, \"\\r\\n\");
		chaine=chaine.replace(/\\n\\n/g, \"\\n\");
		return chaine;
	}

	function relancer_message(id_msg) {
		csrf_alea=document.getElementById('csrf_alea').value;
		new Ajax.Updater($('td_lu_message_envoye_'+id_msg),'$gepiPath/mod_alerte/form_message.php?mode=relancer&id_msg='+id_msg+'&csrf_alea='+csrf_alea,{method: 'get'});
		document.getElementById('td_relance_message_envoye_'+id_msg).innerHTML='';
	}

	function clore_message(id_msg) {
		csrf_alea=document.getElementById('csrf_alea').value;
		new Ajax.Updater($('td_lu_message_envoye_'+id_msg),'$gepiPath/mod_alerte/form_message.php?mode=clore&id_msg='+id_msg+'&csrf_alea='+csrf_alea,{method: 'get'});
	}

	function trie_affiche_historique_messages(tri) {
		new Ajax.Updater($('div_messages_envoyes'),'$gepiPath/mod_alerte/form_message.php?mode=affiche_messages&tri='+tri+'&mode_affiche_historique_messages=$mode',{method: 'get'});
	}
</script>
";
	}
	return $retour;
}

function affiche_historique_messages_recus($login_dest, $mode = "tous", $tri = "date") {
	global $mysqli;
	global $gepiPath;
	global $themessage;

	$retour = "";
	if ($mode == 'tous') {
		$sql = "SELECT * FROM messagerie WHERE login_dest='$login_dest' AND date_visibilite<='" . strftime("%Y-%m-%d %H:%M:%S") . "'";
	} elseif ($mode == 'non_lus') {
		$sql = "SELECT * FROM messagerie WHERE login_dest='$login_dest' AND date_visibilite<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND vu='0'";
	}
	if ($tri == "sujet") {
		$sql .= " ORDER BY sujet, date_msg DESC;";
	} elseif ($tri == "source") {
		$sql .= " ORDER BY login_src, date_msg DESC, sujet;";
	} elseif ($tri == "vu") {
		$sql .= " ORDER BY vu, date_msg DESC, sujet;";
	} else {
		$sql .= " ORDER BY date_msg DESC, sujet;";
	}

	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		$retour = "<p>Aucun message.</p>";
	} else {
		$peut_poster_message = peut_poster_message($_SESSION['statut']);
		$retour .= add_token_field(true) . "<table class='boireaus boireaus_alt'>
	<tr>
		<th><a href=\"javascript:trie_affiche_historique_messages_recus('date')\" title='Trier par date'>Date</a></th>
		<th><a href=\"javascript:trie_affiche_historique_messages_recus('source')\" title='Trier par expéditeur'>Source</a></th>
		<th><a href=\"javascript:trie_affiche_historique_messages_recus('sujet')\" title='Trier par sujet'>Sujet</a></th>
		";
		if ($peut_poster_message) {
			$retour .= "<th title=\"En cliquant sur le texte du message souhaité, vous pouvez compléter le champ Message d'un message que vous êtes en train de rédiger.\">Message <img src='../images/icons/ico_ampoule.png' width='9' height='15' alt='Astuce' /></th>";
		} else {
			$retour .= "<th>Message</th>";
		}
		$retour .= "
		<th><a href=\"javascript:trie_affiche_historique_messages_recus('vu')\" title='Trier selon que le message est lu ou non'>Lu/vu</a><br /><a href='$gepiPath/mod_alerte/form_message.php?mode=marquer_tous_lus&amp;mode_no_js=y" . add_token_in_url() . "' onclick=\"return confirm_abandon (this, change, '$themessage')\"/><img src='$gepiPath/images/enabled_wizard.png' class='icone20' alt='Magic' title=\"Marquer tous les messages comme lus (pour éviter de devoir les cocher un par un).\"/></a></th>
		<!-- A FAIRE : Ajouter une colonne pour Répondre si on en a le droit -->";

		if ($peut_poster_message) {
			$retour .= "
		<th>Répondre</th>";
		}

		$retour .= "
	</tr>";


		$cpt_ahmr = 0;
		while ($lig = $res->fetch_object()) {
			$retour .= "
	<tr>
		<td>" . formate_date($lig->date_msg, 'y') . "</td>
		<td>" . civ_nom_prenom($lig->login_src) . "</td>
		<td>$lig->sujet</td>
		<td id='td_ahmr_" . $cpt_ahmr . "' onclick=\"copie_ahmr($cpt_ahmr)\">" . stripslashes(nl2br(preg_replace("/\\\\n/", "\n", $lig->message))) . "</td>
		<td>";
			if ($lig->vu == 1) {
				$retour .= "<img src='../images/enabled.png' width='20' height='20' alt='Lu' title='Vous avez marqué/lu/vu ce message le " . formate_date($lig->date_vu, 'y') . "' />";
			} elseif ($lig->vu == 2) {
				$retour .= "<img src='../images/icons/securite.png' width='16' height='16' alt='' title=\"Ce message a été marqué comme clos/traité par l'expéditeur le " . formate_date($lig->date_vu, 'y') . "\" />";
			} else {
				$retour .= "<span id='span_message_$lig->id'><a href='$gepiPath/mod_alerte/form_message.php?mode=marquer_lu&amp;id_msg=$lig->id&amp;mode_no_js=y" . add_token_in_url() . "' onclick=\"marquer_message_lu($lig->id);return false;\" target='_blank'><img src='../images/disabled.png' width='20' height='20' alt='Non lu/vu' title='Non lu/vu. Cliquez pour marquer ce message comme lu.' /></a></span>";
			}
			$retour .= "</td>";

			if ($peut_poster_message) {
				/*
				$retour.="
		<td><a href='$gepiPath/mod_alerte/form_message.php?mode=repondre&amp;id_msg=$lig->id".add_token_in_url()."' onclick=\"repondre_message($lig->id);return false;\" target='_blank' title='Répondre'><img src='../images/icons/back.png' width='16' height='16' /></a></td>";
				*/
				$retour .= "
		<td><a href='$gepiPath/mod_alerte/form_message.php?mode=repondre&amp;id_msg=$lig->id" . add_token_in_url() . "' title='Répondre'><img src='../images/icons/back.png' width='16' height='16' alt='Répondre' /></a></td>";
			}

			$retour .= "
	</tr>";
			$cpt_ahmr++;
		}
		$res->close();
		$retour .= "</table>

<script type='text/javascript'>
	function marquer_message_lu(id_msg) {
		csrf_alea=document.getElementById('csrf_alea').value;

		new Ajax.Updater($('span_message_'+id_msg),'$gepiPath/mod_alerte/form_message.php?mode=marquer_lu&id_msg='+id_msg+'&csrf_alea='+csrf_alea,{method: 'get'});
		new Ajax.Updater($('temoin_messagerie_non_vide'),'$gepiPath/mod_alerte/form_message.php?mode=check&sound=no&csrf_alea='+csrf_alea,{method: 'get'});
		new Ajax.Updater($('temoin_messagerie_non_vide'),'$gepiPath/mod_alerte/form_message.php?mode=check2&csrf_alea='+csrf_alea,{method: 'get'});
	}

	function copie_ahmr(num) {
		if(document.getElementById('message_messagerie')) {
			document.getElementById('message_messagerie').innerHTML+=ahmr_nettoyage(document.getElementById('td_ahmr_'+num).innerHTML);
		}
	}

	function ahmr_nettoyage(str) {
		//return str.replace(/<br\s*\/?>/mg,\"\\n\");
		chaine=str.replace(/<br\s*\/?>/mg,\"\\n\")
		chaine=chaine.replace(/\\r\\n\\r\\n/g, \"\\r\\n\");
		chaine=chaine.replace(/\\n\\n/g, \"\\n\");
		return chaine;
	}

	function trie_affiche_historique_messages_recus(tri) {
		new Ajax.Updater($('div_messages_recus'),'$gepiPath/mod_alerte/form_message.php?mode=affiche_messages_recus&tri='+tri+'&mode_affiche_historique_messages_recus=$mode',{method: 'get'});
	}
</script>";
	}
	return $retour;
}

function check_messages_recus($login_dest) {
	global $mysqli;
	$retour = "";
	//$sql="SELECT 1=1 FROM messagerie WHERE login_dest='$login_dest' AND vu='0';";
	$sql = "SELECT 1=1 FROM messagerie WHERE login_dest='$login_dest' AND vu='0' AND date_visibilite<=CURRENT_TIMESTAMP;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 1) {
		$retour = "1 message non lu.";
		$res->close();
	} elseif ($res->num_rows > 1) {
		$retour = $res->num_rows . " messages non lus.";
		$res->close();
	}
	return $retour;
}

// A faire: check_mes_messages_lus() pour signaler qu'un de ses messages a été lu?

function marquer_message_lu($id_msg, $etat = true) {
	global $mysqli;
	$retour = "";

	if ($etat) {
		$sql = "UPDATE messagerie SET vu='1', date_vu=CURRENT_TIMESTAMP WHERE id='$id_msg' AND login_dest='" . $_SESSION['login'] . "';";
	} else {
		$sql = "UPDATE messagerie SET vu='0', date_vu=CURRENT_TIMESTAMP WHERE id='$id_msg';";
	}
	$update = mysqli_query($mysqli, $sql);
	if ($update) {
		$retour = "Succès";
	} else {
		$retour = "Erreur";
	}

	return $retour;
}

function clore_declore_message($id_msg) {
	global $mysqli;
	$retour = "";

	$sql = "SELECT 1=1 FROM messagerie WHERE id='$id_msg' AND login_dest='" . $_SESSION['login'] . "' AND vu='2';";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows == 0) {
		$sql = "UPDATE messagerie SET vu='2', date_vu=CURRENT_TIMESTAMP WHERE id='$id_msg' AND login_dest='" . $_SESSION['login'] . "';";
		$update = mysqli_query($mysqli, $sql);
		if ($update) {
			$retour = 2;
		} else {
			$retour = "Erreur";
		}
	} else {
		$test->close();
		$sql = "UPDATE messagerie SET vu='0', date_vu=CURRENT_TIMESTAMP WHERE id='$id_msg';";
		$update = mysqli_query($mysqli, $sql);
		if ($update) {
			$retour = 0;
		} else {
			$retour = "Erreur";
		}
	}

	return $retour;
}

function peut_poster_message($statut) {
	// A FAIRE: Gérer le statut Autre...
	if (getSettingAOui('active_mod_alerte')) {
		if (($_SESSION['statut'] != 'autre') && (!acces('/mod_alerte/form_message.php', $statut))) {
			return false;
		} else {
			if (getSettingAOui('PeutPosterMessage' . ucfirst(mb_strtolower($statut)))) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}

function affichage_temoin_messages_recus($portee = "header_et_fixe") {
	global $gepiPath;
	global $mysqli;

	$MessagerieDelaisTest = getSettingValue('MessagerieDelaisTest');
	if (($MessagerieDelaisTest == '') || (!preg_match('/^[0-9]$/', $MessagerieDelaisTest)) || ($MessagerieDelaisTest == 0)) {
		$MessagerieDelaisTest = 1;
	}

	// On teste la présence de messages toutes les 1min, 2min,...
	$nb_sec = 60 * $MessagerieDelaisTest;

	$retour = "";

	// Mieux vaut l'enveloppe seule pour le témoin
	$image_no_mail = "no_mail.png";
	//$image_no_mail="module_alerte32.png";
	if (peut_poster_message($_SESSION['statut'])) {
		$retour .= "<span id='span_messages_recus'><a href='$gepiPath/mod_alerte/form_message.php' target='_blank'><img src='$gepiPath/images/icons/$image_no_mail' width='16' height='16' title='Aucun message' alt='Aucun message' /></a></span>";
	} else {
		$sql = "SELECT 1=1 FROM messagerie WHERE login_dest='" . $_SESSION['login'] . "' OR login_src='" . $_SESSION['login'] . "';";

		$resultat = mysqli_query($mysqli, $sql);
		$nb_lignes = $resultat->num_rows;

		if ($nb_lignes > 0) {
			$retour .= "<span id='span_messages_recus'><a href='$gepiPath/mod_alerte/form_message.php' target='_blank'><img src='$gepiPath/images/icons/$image_no_mail' width='16' height='16' title='Aucun message' alt='Aucun message' /></a></span>";
			$resultat->close();
		} else {
			$retour .= "<span id='span_messages_recus'><img src='$gepiPath/images/icons/$image_no_mail' width='16' height='16' title='Aucun message' alt='Aucun message' /></span>";
		}
	}

	$retour .= "
<script type='text/javascript'>
	var nb_millisec_check_message=$nb_sec*1000;

	function function_check_message() {
		new Ajax.Updater($('span_messages_recus'),'$gepiPath/mod_alerte/form_message.php?mode_js=y&mode=check',{method: 'get'});";
	if ($portee != "header_seul") {
		$retour .= "
		new Ajax.Updater($('temoin_messagerie_non_vide'),'$gepiPath/mod_alerte/form_message.php?mode_js=y&mode=check2',{method: 'get'});";
	}
	$retour .= "
		setTimeout('function_check_message()', nb_millisec_check_message);
	}

	//alert('plop')
	//setTimeout('function_check_message()', nb_millisec_check_message);
	// On lance le premier test 10s après l'affichage de la page
	setTimeout('function_check_message()', 10000);
	// En fait, en cas de changement de page, on va tester la présence de message dans la seconde qui suit.
</script>\n";

	return $retour;
}


function joueSon($sound, $id_son = "") {
	global $gepiPath, $niveau_arbo;

	$retour = "";
	if (!in_array($sound, array("KDE_Beep_Pop.wav", "libreoffice_gong.wav", "libreoffice_nature1.wav", "pluck.wav", "verre_brise.wav", "default_alarm.wav"))) {
		$sound = "KDE_Beep_Pop.wav";
	}

	if ($niveau_arbo == "0") {
		//$chemin_sound="./sounds/".$sound;
		$chemin_sound = "sounds/" . $sound;
	} elseif ($niveau_arbo == "1") {
		$chemin_sound = "../sounds/" . $sound;
	} elseif ($niveau_arbo == "2") {
		$chemin_sound = "../../sounds/" . $sound;
	} elseif ($niveau_arbo == "3") {
		$chemin_sound = "../../../sounds/" . $sound;
	} else {
		$chemin_sound = "../sounds/" . $sound;
	}

	//$chemin_sound=$gepiPath."/sounds/".$sound;

	if ((isset($_SERVER['HTTP_REFERER'])) && ((preg_match("#/accueil.php#", $_SERVER['HTTP_REFERER'])) || (preg_match("#/accueil_simpl_prof.php#", $_SERVER['HTTP_REFERER'])))) {
		//$chemin_sound="./sounds/".$sound;
		$chemin_sound = "sounds/" . $sound;
	}

	$debug = "n";
	if ($debug == "y") {
		$f = fopen("/tmp/debug_gepi_sound.txt", "a+");
		fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " : ================================\n");
		if (isset($_SERVER['HTTP_REFERER'])) {
			fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " : \$_SERVER['HTTP_REFERER']=" . $_SERVER['HTTP_REFERER'] . "\n");
		}
		fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " : gepiPath=$gepiPath\n");
		fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " : niveau_arbo=$niveau_arbo\n");
		fwrite($f, strftime("%Y-%m-%d %H:%M:%S") . " : chemin_sound=$chemin_sound\n");
		fclose($f);
	}

	if ($id_son == "") {
		$id_son = "id_son_" . preg_replace("/[^0-9]/", "_", microtime());
	}
	if (file_exists($chemin_sound)) {
		$retour = "<audio id='$id_son' preload='auto' autobuffer autoplay>
	<source src='" . $chemin_sound . "' />
</audio>
";
	} else {
		$retour = "";
	}
	//$retour.="Bip";
	return $retour;
}

function acces_exceptionnel_saisie_bull_app_groupe_periode($id_groupe, $num_periode) {
	global $mysqli;
	$sql = "SELECT 1=1 FROM matieres_app_delais WHERE id_groupe='$id_groupe' AND periode='$num_periode' AND date_limite>'" . strftime("%Y-%m-%d %H:%M:%S") . "' AND mode='acces_complet';";
	//echo "$sql<br />";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 0) {
		$test->close();
		return true;
	} else {
		return false;
	}
}

function acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $num_periode) {
	global $mysqli;
	$sql = "SELECT 1=1 FROM acces_exceptionnel_matieres_notes WHERE id_groupe='$id_groupe' AND periode='$num_periode' AND date_limite>'" . strftime("%Y-%m-%d %H:%M:%S") . "';";
	//echo "$sql<br />";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 0) {
		$test->close();
		return true;
	} else {
		return false;
	}
}

function log_modifs_acces_exceptionnel_saisie_bull_note_groupe_periode($id_groupe, $num_periode, $texte_ajoute) {
	global $mysqli;
	$sql = "SELECT * FROM acces_exceptionnel_matieres_notes WHERE id_groupe='$id_groupe' AND periode='$num_periode';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		// Il n'y a au plus qu'un enregistrement par (id_groupe;periode) dans acces_cn
		$lig = $res->fetch_object();
		$texte = $lig->commentaires . "\n" . $texte_ajoute;
		$res->close();
		$sql = "UPDATE acces_exceptionnel_matieres_notes SET commentaires='" . $mysqli->real_escape_string($texte) . "' WHERE id='$lig->id';";
		$update = mysqli_query($mysqli, $sql);
		if ($update) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}


/**
 * Retourne l'URI des élèves pour les flux rss
 *
 * @param string $eleve Login de l'élève
 * @param string $https La page est-elle sécurisée ? en https si 'y'
 * @param string $type 'cdt' ou ''
 * @return string
 * @global string
 * @see getSettingValue()
 */
function retourneUri($eleve, $https, $type) {
	global $mysqli;
	global $gepiPath;
	$rep = array();

	// on vérifie que la table en question existe déjà
	$sql_table = "SHOW TABLES LIKE 'rss_users'";
	$query_table = mysqli_query($mysqli, $sql_table);
	$test_table = $query_table->num_rows;
	if ($test_table >= 1) {
		$sql = "SELECT user_uri FROM rss_users WHERE user_login = '" . $eleve . "' LIMIT 1";
		$query = mysqli_query($mysqli, $sql);
		$nbre = $query->num_rows;
		if ($nbre == 1) {
			//$uri = $mysqli->fetch_array($mysqli, $query);
			$uri = $query->fetch_array();
			if ($https == 'y') {
				$web = 'https://';
			} else {
				$web = 'http://';
			}
			if ($type == 'cdt') {
				$rep["uri"] = $web . $_SERVER["SERVER_NAME"] . $gepiPath . '/class_php/syndication.php?rne=' . getSettingValue("gepiSchoolRne") . '&amp;ele_l=' . $eleve . '&amp;type=cdt&amp;uri=' . $uri["user_uri"];
				$rep["text"] = $web . $_SERVER["SERVER_NAME"] . $gepiPath . '/class_php/syndication.php?rne=' . getSettingValue("gepiSchoolRne") . '&amp;ele_l=' . $eleve . '&amp;type=cdt&amp;uri=' . $uri["user_uri"];
			}
			$query->close();
		} else {
			$rep["text"] = 'Erreur : Votre URI n\'a peut-être pas encore été générée. Contactez votre administrateur.';
			$rep["uri"] = '#';
		}
		$query_table->close();
	} else {

		$rep["text"] = 'Demandez à votre administrateur de générer les URI.';
		$rep["uri"] = '#';

	}

	return $rep;
}

function prendre_en_compte_js_et_css_edt() {
	global $javascript_specifique, $style_specifique;

	// CSS et js particulier à l'EdT

	if ((is_array($javascript_specifique))) {
		$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
	} else {
		$tmp_js = $javascript_specifique;
		$javascript_specifique = array();
		if ($tmp_js != "") {
			$javascript_specifique[] = $tmp_js;
		}
		$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
	}

	if ((is_array($style_specifique))) {
		$ua = getenv("HTTP_USER_AGENT");
		if (strstr($ua, "MSIE 6.0")) {
			//$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt_ie6";
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt_ie6_infobulle";
		} else {
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt";
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt_infobulle";
		}
	} else {
		$tmp_css = $style_specifique;
		$style_specifique = array();
		if ($tmp_css != "") {
			$style_specifique[] = $tmp_css;
		}

		$ua = getenv("HTTP_USER_AGENT");
		if (strstr($ua, "MSIE 6.0")) {
			//$style_specifique[] = "templates/".NameTemplateEDT()."/css/style_edt_ie6";
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt_ie6_infobulle";
		} else {
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt";
			$style_specifique[] = "templates/" . NameTemplateEDT() . "/css/style_edt_infobulle";
		}
	}
}

function get_next_tel_jour($jour, $decalage_aujourdhui = 0) {
	$retour = "";

	$debug = "n";

	if ($debug == "y") {
		$tab = array();
		$tab[] = "Mardi";
		$tab[] = "Mercredi";
		$tab[] = "Jeudi";
		$tab[] = "Vendredi";
		$tab[] = "Samedi";
		$tab[] = "Dimanche";
		$tab[] = "Lundi";

		$f = fopen("/tmp/debug_get_next_tel_jour.txt", "a+");
		fwrite($f, "============================================\n");
		fwrite($f, "get_next_tel_jour($jour, $decalage_aujourdhui)\n\n");
		fwrite($f, "Recherche du prochain: " . $tab[$jour] . "\n");
		fwrite($f, "Aujourd'hui: " . french_strftime("%a %d/%m/%Y") . "\n");
	}

	$indice_courant = id_j_semaine();

	for ($i = $decalage_aujourdhui; $i < 9; $i++) {
		if ($debug == "y") {
			fwrite($f, "\n\$i=$i\n");
		}
		$jour_suivant = id_j_semaine(time() + 24 * 3600 * $i);
		if ($debug == "y") {
			fwrite($f, "\$jour_suivant=$jour_suivant\n");
			fwrite($f, "soit " . french_strftime("%a %d/%m/%Y", time() + 24 * 3600 * $i) . "\n");
		}
		if ($jour_suivant == $jour) {
			if ($debug == "y") {
				fwrite($f, "Jour trouvé \$i=$i\n");
			}
			$retour = $i;
			break;
		}
	}

	if ($debug == "y") {
		fclose($f);
	}
	return $retour;
}

function get_output_mode_pdf() {
	/*
		I : envoyer en inline au navigateur. Le visualiseur PDF est utilisé.
		D : envoyer au navigateur en forçant le téléchargement, avec le nom indiqué dans name.
		F : sauver dans un fichier local, avec le nom indiqué dans name (peut inclure un répertoire).
		S : renvoyer le document sous forme de chaîne.
	*/
	$output_mode_pdf = getSettingValue("output_mode_pdf");
	if (!in_array($output_mode_pdf, array("D", "I"))) {
		$output_mode_pdf = 'D';
	}
	return getPref($_SESSION['login'], "output_mode_pdf", $output_mode_pdf);
}

function get_tab_mef($mode = "indice_mef_code", $restreindre = "n") {
	global $mysqli;
	$tab_mef = array();
	$cpt = 0;
	if ($restreindre == "n") {
		$sql = "SELECT * FROM mef ORDER BY libelle_edition, libelle_long, libelle_court;";
	} else {
		// On restreint aux MEFs assocoés à des élèves
		$sql = "SELECT DISTINCT m.* FROM mef m, eleves e WHERE m.mef_code=e.mef_code ORDER BY libelle_edition, libelle_long, libelle_court;";
	}
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			if ($mode == "indice_mef_code") {
				$tab_mef[$lig->mef_code]['mef_code'] = $lig->mef_code;
				$tab_mef[$lig->mef_code]['libelle_court'] = $lig->libelle_court;
				$tab_mef[$lig->mef_code]['libelle_long'] = $lig->libelle_long;
				$tab_mef[$lig->mef_code]['libelle_edition'] = $lig->libelle_edition;
				$tab_mef[$lig->mef_code]['mef_rattachement'] = $lig->mef_rattachement;
				if ($lig->libelle_edition != "") {
					$tab_mef[$lig->mef_code]['designation_courte'] = $lig->libelle_edition;
				} elseif ($lig->libelle_long != "") {
					$tab_mef[$lig->mef_code]['designation_courte'] = $lig->libelle_long;
				} elseif ($lig->libelle_court != "") {
					$tab_mef[$lig->mef_code]['designation_courte'] = $lig->libelle_court;
				} elseif ($lig->mef_code != "") {
					$tab_mef[$lig->mef_code]['designation_courte'] = $lig->mef_code;
				}
			} else {
				$tab_mef[$cpt]['mef_code'] = $lig->mef_code;
				$tab_mef[$cpt]['libelle_court'] = $lig->libelle_court;
				$tab_mef[$cpt]['libelle_long'] = $lig->libelle_long;
				$tab_mef[$cpt]['libelle_edition'] = $lig->libelle_edition;
				$tab_mef[$cpt]['mef_rattachement'] = $lig->mef_rattachement;
				if ($lig->libelle_edition != "") {
					$tab_mef[$cpt]['designation_courte'] = $lig->libelle_edition;
				} elseif ($lig->libelle_long != "") {
					$tab_mef[$cpt]['designation_courte'] = $lig->libelle_long;
				} elseif ($lig->libelle_court != "") {
					$tab_mef[$cpt]['designation_courte'] = $lig->libelle_court;
				} elseif ($lig->mef_code != "") {
					$tab_mef[$cpt]['designation_courte'] = $lig->mef_code;
				}
				$cpt++;
			}
		}
		$res->close();
	}
	return $tab_mef;
}

/**
 * Fonction destinée à extraire les informations associées à un MEF_CODE
 *
 * @param string $mef_code Le MEF_CODE à rechercher
 *
 * @return array Tableau des champs du MEF extrait
 * @global string
 */
function get_tab_mef_from_mef_code($mef_code) {
	global $mysqli;
	$tab_mef = array();

	$sql = "SELECT * FROM mef WHERE mef_code='$mef_code';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$lig = $res->fetch_object();
		$tab_mef['mef_code'] = $lig->mef_code;
		$tab_mef['libelle_court'] = $lig->libelle_court;
		$tab_mef['libelle_long'] = $lig->libelle_long;
		$tab_mef['libelle_edition'] = $lig->libelle_edition;
		$tab_mef['mef_rattachement'] = $lig->mef_rattachement;
		if ($lig->libelle_edition != "") {
			$tab_mef['designation_courte'] = $lig->libelle_edition;
		} elseif ($lig->libelle_long != "") {
			$tab_mef['designation_courte'] = $lig->libelle_long;
		} elseif ($lig->libelle_court != "") {
			$tab_mef['designation_courte'] = $lig->libelle_court;
		} elseif ($lig->mef_code != "") {
			$tab_mef['designation_courte'] = $lig->mef_code;
		}
		$res->close();
	}
	return $tab_mef;
}

function clean_temp_tables() {
	global $mysqli;
	$retour = "";
	$tab_table = array("temp_abs_import",
		"temp_ele_classe",
		"temp_etab_import",
		"temp_gep_import",
		"temp_gep_import2",
		"temp_grp",
		"temp_matieres_import",
		"temp_resp_adr_import",
		"temp_resp_pers_import",
		"temp_responsables2_import",
		"tempo",
		"tempo2",
		"tempo3",
		"tempo3_cdt",
		"tempo4",
		"tempo_utilisateurs");
	$nb_tables_videes = 0;
	for ($i = 0; $i < count($tab_table); $i++) {
		$sql = "SHOW TABLES LIKE '$tab_table[$i]';";
		//echo "$sql<br />\n";
		$res_test = mysqli_query($mysqli, $sql);
		if ($res_test->num_rows > 0) {
			if ($i > 0) {
				$retour .= ", ";
			}
			$retour .= $tab_table[$i];

			$sql = "SELECT 1=1 FROM $tab_table[$i];";
			//echo "$sql<br />\n";
			$res_nb = mysqli_query($mysqli, $sql);
			$nb_reg = $res_nb->num_rows;
			$retour .= " (<em title=\"Nombre d'enregistrement avant vidage\">" . $nb_reg . "</em>)";

			if ($nb_reg > 0) {
				$res_nb->close();
				$sql = "TRUNCATE TABLE $tab_table[$i];";
				//echo "$sql<br />\n";
				$suppr = mysqli_query($mysqli, $sql);
				if (!$suppr) {
					$retour .= " <span style='color:red'>ERREUR</span>";
				} else {
					$nb_tables_videes++;
				}
			}
		}
		$res_test->close();
	}
	$retour .= "<br />$nb_tables_videes table(s) vidée(s).";
	return $retour;
}

function get_tab_signature_bull($login_user = "") {
	global $mysqli;
	global $niveau_arbo;
	$tab = array();

	if ($login_user == "") {
		$login_user = $_SESSION['login'];
	}

	if ($niveau_arbo == "") {
		$niveau_arbo = 1;
	}

	$pref_arbo = "..";
	if ($niveau_arbo == 0) {
		$pref_arbo = ".";
	}
	if ($niveau_arbo == 1) {
		$pref_arbo = "..";
	}
	if ($niveau_arbo == 2) {
		$pref_arbo = "../..";
	}

	$user_temp_directory = get_user_temp_directory();

	$sql = "SELECT 1=1 FROM signature_droits WHERE login='$login_user';";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows > 0) {
		$sql = "SELECT * FROM signature_fichiers WHERE login='$login_user';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$tab['fichier'][$lig->id_fichier]['fichier'] = $lig->fichier;
				$tab['fichier'][$lig->id_fichier]['chemin'] = $pref_arbo . "/temp/" . $user_temp_directory . "/signature/" . $lig->fichier;
			}
			$res->close();
		}

		$sql = "SELECT * FROM signature_classes WHERE login='$login_user';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			while ($lig = $res->fetch_object()) {
				$tab['classe'][$lig->id_classe]['id_fichier'] = $lig->id_fichier;
				if ($lig->id_fichier != -1) {
					$tab['fichier'][$lig->id_fichier]['id_classe'][] = $lig->id_classe;
				}
			}
			$res->close();
		}
		$test->close();
	}

	return $tab;
}

function get_tab_signature_bull_archivage($id_classe) {
	global $mysqli;
	global $niveau_arbo;
	$tab = array();

	if ($niveau_arbo == "") {
		$niveau_arbo = 1;
	}

	$pref_arbo = "..";
	if ($niveau_arbo == 0) {
		$pref_arbo = ".";
	}
	if ($niveau_arbo == 1) {
		$pref_arbo = "..";
	}
	if ($niveau_arbo == 2) {
		$pref_arbo = "../..";
	}

	/*
mysql> SELECT * FROM signature_droits;
+----+----------+
| id | login    |
+----+----------+

mysql> SELECT * FROM signature_fichiers;
+------------+-------------------------+----------+------+
| id_fichier | fichier                 | login    | type |
+------------+-------------------------+----------+------+

mysql> SELECT * FROM signature_classes;
+----+----------+-----------+------------+
| id | login    | id_classe | id_fichier |
+----+----------+-----------+------------+

*/

	$sql = "SELECT sf.fichier, sc.* FROM signature_droits sd, 
			signature_fichiers sf, 
			signature_classes sc 
		WHERE sd.login=sf.login AND 
			sf.id_fichier=sc.id_fichier AND 
			sd.login=sc.login AND 
			sc.id_classe='" . $id_classe . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			if (!isset($user_temp_directory[$lig->login])) {
				$user_temp_directory[$lig->login] = get_user_temp_directory($lig->login);
			}

			$tab['fichier'][$cpt]['fichier'] = $lig->fichier;
			$tab['fichier'][$cpt]['chemin'] = $pref_arbo . "/temp/" . $user_temp_directory[$lig->login] . "/signature/" . $lig->fichier;

			$cpt++;
		}
	}
	/*
	echo "<pre>";
	print_r($tab);
	echo "</pre>";
	*/

	return $tab;
}

/*
# 1 : session ouverte, mais pas refermée (encore ouverte, ou fermée en fermant le navigateur)
# 0 : logout normal
# 2 : logout renvoyé par la fonction checkAccess (problème gepiPath ou accès interdit)
# 3 : logout lié à un timeout
# 10 : logout lié à une nouvelle connexion sous un nouveau profil

# 4 : Erreur MDP
*/
function get_last_connexion($login, $reussie = "y") {
	global $mysqli;
	$tab = array();

	if ($reussie == "y") {
		$sql = "SELECT * FROM log WHERE LOGIN='$login' AND (AUTOCLOSE='0' OR AUTOCLOSE='1' OR AUTOCLOSE='2' OR AUTOCLOSE='3' OR AUTOCLOSE='10') ORDER BY START DESC LIMIT 1;";
	} else {
		$sql = "SELECT * FROM log WHERE LOGIN='$login' AND AUTOCLOSE='4' ORDER BY START DESC LIMIT 1;";
	}
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$lig = $res->fetch_object();

		$tab['START'] = $lig->START;
		$tab['REMOTE_ADDR'] = $lig->REMOTE_ADDR;
		$tab['USER_AGENT'] = $lig->USER_AGENT;
		$tab['REFERER'] = $lig->REFERER;
		$tab['AUTOCLOSE'] = $lig->AUTOCLOSE;
		$tab['END'] = $lig->END;
	}
	$res->close();
	return $tab;
}

function get_ele_clas_connexions($id_classe, $timestamp1, $timestamp2, $tab_autoclose = array()) {
	global $mysqli;
	$tab = array();

	$chaine_autoclose = "";
	if (count($tab_autoclose) > 0) {
		$chaine_autoclose .= " AND (";
		for ($loop = 0; $loop < count($tab_autoclose); $loop++) {
			if ($loop > 0) {
				$chaine_autoclose .= " OR ";
			}
			$chaine_autoclose .= "AUTOCLOSE='" . $tab_autoclose[$loop] . "'";
		}
		$chaine_autoclose .= ")";
	}

	$sql = "SELECT DISTINCT jec.login FROM log l, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=l.LOGIN AND l.START>='$timestamp1' AND l.START<='$timestamp2'" . $chaine_autoclose . ";";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[] = $lig->login;
		}
		$res->close();
	}
	return $tab;
}

function get_tab_etat_travail_fait($eleve_login) {
	global $mysqli;
	$tab_etat_travail_fait = array();
	$sql = "SELECT * FROM ct_devoirs_faits WHERE login='$eleve_login';";
	$res_cdf = mysqli_query($mysqli, $sql);
	if ($res_cdf->num_rows > 0) {
		while ($lig_cdf = $res_cdf->fetch_object()) {
			$tab_etat_travail_fait[$lig_cdf->id_ct]['etat'] = $lig_cdf->etat;
			$tab_etat_travail_fait[$lig_cdf->id_ct]['date_initiale'] = $lig_cdf->date_initiale;
			$tab_etat_travail_fait[$lig_cdf->id_ct]['date_modif'] = $lig_cdf->date_modif;
			$tab_etat_travail_fait[$lig_cdf->id_ct]['commentaire'] = $lig_cdf->commentaire;
		}
		$res_cdf->close();
	}
	return $tab_etat_travail_fait;
}

function get_etat_et_img_cdt_travail_fait($id_ct) {
	global $tab_etat_travail_fait,
		   $image_etat,
		   $texte_etat_travail,
		   $class_color_fond_notice;

	if (array_key_exists($id_ct, $tab_etat_travail_fait)) {
		if ($tab_etat_travail_fait[$id_ct]['etat'] == 'fait') {
			$image_etat = "../images/edit16b.png";
			$texte_etat_travail = "FAIT: Le travail est actuellement pointé comme fait.\n";
			if ($tab_etat_travail_fait[$id_ct]['date_modif'] != $tab_etat_travail_fait[$id_ct]['date_initiale']) {
				$texte_etat_travail .= "Le travail a été pointé comme fait la première fois le " . formate_date($tab_etat_travail_fait[$id_ct]['date_initiale'], "y") . "\net modifié pour la dernière fois par la suite le " . formate_date($tab_etat_travail_fait[$id_ct]['date_modif'], "y") . "\n";
			} else {
				$texte_etat_travail .= "Le travail a été pointé comme fait le " . formate_date($tab_etat_travail_fait[$id_ct]['date_initiale'], "y") . "\n";
			}
			$texte_etat_travail .= "Cliquer pour corriger si le travail n'est pas encore fait.";
			$class_color_fond_notice = "color_fond_notices_t_fait";
		} else {
			$image_etat = "../images/edit16.png";
			$texte_etat_travail = "NON FAIT: Le travail n'est actuellement pas fait.\n";
			if ($tab_etat_travail_fait[$id_ct]['commentaire'] != '') {
				$texte_etat_travail .= $tab_etat_travail_fait[$id_ct]['commentaire'] . "\n";
			}
			$texte_etat_travail .= "Cliquer pour pointer le travail comme fait.";
		}
	} else {
		$image_etat = "../images/edit16.png";
		$texte_etat_travail = "NON FAIT: Le travail n'est actuellement pas fait.\nCliquer pour pointer le travail comme fait.";
	}
}

function is_prof_ele($login_prof, $login_ele = "", $login_resp = "", $id_classe = "") {
	global $mysqli;
	$is_prof_ele = false;

	if ($login_ele != "") {
		$sql = "SELECT 1=1 FROM j_eleves_groupes jeg, 
							j_groupes_professeurs jgp
						WHERE jgp.login='" . $login_prof . "' AND 
							jgp.id_groupe=jeg.id_groupe AND 
							jeg.login='$login_ele' LIMIT 1;";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$is_prof_ele = true;
			$res->close();
		}
	} elseif ($login_resp != "") {
		$sql = "SELECT 1=1 FROM j_eleves_groupes jeg, 
							j_groupes_professeurs jgp, 
							eleves e, 
							responsables2 r, 
							resp_pers rp 
						WHERE jgp.login='" . $login_prof . "' AND 
							jgp.id_groupe=jeg.id_groupe AND 
							jeg.login=e.login AND 
							e.ele_id=r.ele_id AND 
							r.pers_id=rp.pers_id AND 
							rp.login='$login_resp' LIMIT 1";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$is_prof_ele = true;
			$res->close();
		}
	} elseif ($id_classe != "") {
		$is_prof_ele = is_prof_classe($login_prof, $id_classe);
	} else {
		// Est-ce un prof avec des élèves?
		$sql = "SELECT 1=1 FROM j_eleves_groupes jeg, 
							j_groupes_professeurs jgp
						WHERE jgp.login='" . $login_prof . "' AND 
							jgp.id_groupe=jeg.id_groupe LIMIT 1;";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$is_prof_ele = true;
			$res->close();
		}
	}

	return $is_prof_ele;
}

function is_prof_classe_ele($login_prof, $login_ele) {
	global $mysqli;
	$is_prof_ele = false;

	$sql = "SELECT 1=1 FROM j_groupes_classes jgc, 
						j_groupes_professeurs jgp,
						j_eleves_classes jec
					WHERE jgp.login='" . $login_prof . "' AND 
						jgp.id_groupe=jgc.id_groupe AND 
						jec.id_classe=jgc.id_classe AND 
						jec.login='$login_ele' LIMIT 1;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$is_prof_ele = true;
		$res->close();
	}

	return $is_prof_ele;
}

function is_prof_classe($login_prof, $id_classe) {
	global $mysqli;

	$is_prof_classe = false;

	$sql = "SELECT 1=1 FROM j_groupes_classes jgc, 
						j_groupes_professeurs jgp
					WHERE jgp.login='" . $login_prof . "' AND 
						jgp.id_groupe=jgc.id_groupe AND 
						jgc.id_classe='$id_classe' LIMIT 1;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$is_prof_classe = true;
		$res->close();
	}

	return $is_prof_classe;
}

function AccesDerniereConnexionEle($login_ele) {
	$AccesDerniereConnexionEle = false;
	if (($_SESSION['statut'] == 'administrateur') ||
		(($_SESSION['statut'] == 'scolarite') && (getSettingAOui('AccesDerniereConnexionEleScolarite'))) ||
		(($_SESSION['statut'] == 'cpe') && (getSettingAOui('AccesDerniereConnexionEleCpe'))) ||
		(($_SESSION['statut'] == 'professeur') && (is_prof_ele($_SESSION['login'], $login_ele)) && ((getSettingAOui('AccesDerniereConnexionEleProfesseur')) || (getSettingAOui('AccesDetailConnexionEleProfesseur')))) ||
		(($_SESSION['statut'] == 'professeur') && (is_pp($_SESSION['login'], "", $login_ele)) && ((getSettingAOui('AccesDerniereConnexionEleProfP')) || (getSettingAOui('AccesDetailConnexionEleProfP'))))) {
		$AccesDerniereConnexionEle = true;
	}
	return $AccesDerniereConnexionEle;
}

function AccesDerniereConnexionResp($login_resp, $login_ele = "") {
	$is_pp = false;
	$is_prof_ele = false;
	if ($login_ele == "") {
		if (($_SESSION['statut'] == 'professeur') && ((getSettingAOui('AccesDerniereConnexionRespProfesseur')) || (getSettingAOui('AccesDetailConnexionRespProfesseur')))) {
			$is_prof_ele = is_prof_ele($_SESSION['login'], "", $login_resp);
		}

		if (($_SESSION['statut'] == 'professeur') && ((getSettingAOui('AccesDerniereConnexionRespProfP')) || (getSettingAOui('AccesDetailConnexionRespProfP')))) {
			$is_pp = is_pp($_SESSION['login'], "", "", "", $login_resp);
		}
	} else {
		if (($_SESSION['statut'] == 'professeur') && ((getSettingAOui('AccesDerniereConnexionRespProfesseur')) || (getSettingAOui('AccesDetailConnexionRespProfesseur')))) {
			$is_prof_ele = is_prof_ele($_SESSION['login'], $login_ele);
		}

		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui('AccesDerniereConnexionRespProfP'))) {
			$is_pp = is_pp($_SESSION['login'], "", $login_ele);
		}
	}

	$AccesDerniereConnexionResp = false;
	if (($_SESSION['statut'] == 'administrateur') ||
		(($_SESSION['statut'] == 'scolarite') && (getSettingAOui('AccesDerniereConnexionRespScolarite'))) ||
		(($_SESSION['statut'] == 'cpe') && (getSettingAOui('AccesDerniereConnexionRespCpe'))) ||
		(($_SESSION['statut'] == 'professeur') && ($is_prof_ele) && ((getSettingAOui('AccesDerniereConnexionRespProfesseur')) || (getSettingAOui('AccesDetailConnexionRespProfesseur')))) ||
		(($_SESSION['statut'] == 'professeur') && ($is_pp) && ((getSettingAOui('AccesDerniereConnexionRespProfP')) || (getSettingAOui('AccesDetailConnexionRespProfP'))))) {
		$AccesDerniereConnexionResp = true;
	}
	return $AccesDerniereConnexionResp;
}

function AccesInfoEle($mode, $login_ele = "", $id_classe = "") {
	$AccesInfoEle = false;
	if (($_SESSION['statut'] == 'administrateur') ||
		(($_SESSION['statut'] == 'scolarite') && (getSettingAOui($mode . 'Scolarite'))) ||
		(($_SESSION['statut'] == 'cpe') && (getSettingAOui($mode . 'Cpe'))) ||
		(($_SESSION['statut'] == 'professeur') && (is_prof_ele($_SESSION['login'], $login_ele, "", $id_classe)) && (getSettingAOui($mode . 'Professeur'))) ||
		(($_SESSION['statut'] == 'professeur') && (is_pp($_SESSION['login'], $id_classe, $login_ele)) && (getSettingAOui($mode . 'ProfP')))) {
		$AccesInfoEle = true;
	}
	return $AccesInfoEle;
}

function AccesInfoResp($mode, $login_resp = "", $login_ele = "", $id_classe = "") {
	$is_pp = false;
	$is_prof_ele = false;
	if ($login_resp != "") {
		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'Professeur'))) {
			$is_prof_ele = is_prof_ele($_SESSION['login'], "", $login_resp);
		}

		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'ProfP'))) {
			$is_pp = is_pp($_SESSION['login'], "", "", "", $login_resp);
		}
	} elseif ($id_classe != "") {
		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'Professeur'))) {
			$is_prof_ele = is_prof_ele($_SESSION['login'], $login_ele, "", $id_classe);
		}

		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'ProfP'))) {
			$is_pp = is_pp($_SESSION['login'], $id_classe, $login_ele);
		}
	} else {
		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'Professeur'))) {
			$is_prof_ele = is_prof_ele($_SESSION['login'], $login_ele);
		}

		if (($_SESSION['statut'] == 'professeur') && (getSettingAOui($mode . 'ProfP'))) {
			$is_pp = is_pp($_SESSION['login'], "", $login_ele);
		}
	}

	$AccesInfoResp = false;
	if (($_SESSION['statut'] == 'administrateur') ||
		(($_SESSION['statut'] == 'scolarite') && (getSettingAOui($mode . 'Scolarite'))) ||
		(($_SESSION['statut'] == 'cpe') && (getSettingAOui($mode . 'Cpe'))) ||
		(($_SESSION['statut'] == 'professeur') && ($is_prof_ele) && (getSettingAOui($mode . 'Professeur'))) ||
		(($_SESSION['statut'] == 'professeur') && ($is_pp) && (getSettingAOui($mode . 'ProfP')))) {
		$AccesInfoResp = true;
	}
	return $AccesInfoResp;
}

function get_date_debut_log() {
	global $mysqli;
	$retour = "";

	$sql = "SELECT START FROM log ORDER BY START LIMIT 1;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$obj = $res->fetch_object();
		$retour = formate_date($obj->START, "y");
		$res->close();
	}

	return $retour;
}

function get_info_grp($id_groupe, $tab_infos = array('description', 'matieres', 'classes', 'profs'), $mode = "html") {
	$group = get_group($id_groupe, $tab_infos);

	$retour = "";
	if (isset($group['name'])) {
		$retour = $group['name'];
		if ($mode == "html") {
			if (in_array('description', $tab_infos)) {
				$retour .= " (<em>" . $group['description'] . "</em>)";
			}
			if (in_array('matieres', $tab_infos)) {
				$retour .= " " . $group['matiere']['matiere'];
			}
			if (in_array('classes', $tab_infos)) {
				$retour .= " en " . $group['classlist_string'];
			}
			if (in_array('profs', $tab_infos)) {
				$retour .= " (<em>" . $group['profs']['proflist_string'] . "</em>)";
			}
		} else {
			if (in_array('description', $tab_infos)) {
				$retour .= " (" . $group['description'] . ")";
			}
			if (in_array('matieres', $tab_infos)) {
				$retour .= " " . $group['matiere']['matiere'];
			}
			if (in_array('classes', $tab_infos)) {
				$retour .= " en " . $group['classlist_string'];
			}
			if (in_array('profs', $tab_infos)) {
				$retour .= " (" . $group['profs']['proflist_string'] . ")";
			}
		}
	}

	return $retour;
}

function get_adresse_responsable($pers_id, $login_resp = "") {
	global $mysqli;
	$tab_adresse = array();

	$tab_adresse['adr_id'] = "";
	$tab_adresse['adr1'] = "";
	$tab_adresse['adr2'] = "";
	$tab_adresse['adr3'] = "";
	$tab_adresse['cp'] = "";
	$tab_adresse['commune'] = "";
	$tab_adresse['pays'] = "";
	$tab_adresse['en_ligne'] = "";
	$tab_adresse['adresse_sans_cp_commune'] = "";

	if (($pers_id != "") || ($login_resp != "")) {
		if ($pers_id != "") {
			$sql = "SELECT * FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.pers_id='$pers_id';";
		} elseif ($login_resp != "") {
			$sql = "SELECT * FROM resp_adr ra, resp_pers rp WHERE rp.adr_id=ra.adr_id AND rp.login='$login_resp';";
		}
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$lig = $res->fetch_object();
			$tab_adresse['adr_id'] = $lig->adr_id;
			$tab_adresse['adr1'] = $lig->adr1;
			$tab_adresse['adr2'] = $lig->adr2;
			$tab_adresse['adr3'] = $lig->adr3;
			$tab_adresse['cp'] = $lig->cp;
			$tab_adresse['commune'] = $lig->commune;
			$tab_adresse['pays'] = $lig->pays;

			$tab_adresse['en_ligne'] = $lig->adr1;

			if ($lig->adr2 != "") {
				if ($tab_adresse['en_ligne'] != "") {
					$tab_adresse['en_ligne'] .= ", ";
				}
				$tab_adresse['en_ligne'] .= $lig->adr2;
			}

			if ($lig->adr3 != "") {
				if ($tab_adresse['en_ligne'] != "") {
					$tab_adresse['en_ligne'] .= ", ";
				}
				$tab_adresse['en_ligne'] .= $lig->adr3;
			}
			$tab_adresse['adresse_sans_cp_commune'] = $tab_adresse['en_ligne'];

			if ($lig->cp != "") {
				if ($tab_adresse['en_ligne'] != "") {
					$tab_adresse['en_ligne'] .= ", ";
				}
				$tab_adresse['en_ligne'] .= $lig->cp;
			}

			if ($lig->commune != "") {
				if ($tab_adresse['en_ligne'] != "") {
					$tab_adresse['en_ligne'] .= ", ";
				}
				$tab_adresse['en_ligne'] .= $lig->commune;
			}

			if (($tab_adresse['pays'] != '') && ($tab_adresse['pays'] != getSettingValue('gepiSchoolPays'))) {
				if ($tab_adresse['en_ligne'] != "") {
					$tab_adresse['en_ligne'] .= ", ";
				}
				$tab_adresse['en_ligne'] .= $tab_adresse['pays'];
			}
			$res->close();
		}
	}

	return $tab_adresse;
}

function enregistrer_udt_corresp($champ, $nom_udt, $nom_gepi) {
	global $mysqli;
	$sql = "SELECT * FROM udt_corresp WHERE champ='$champ' AND nom_udt='" . $mysqli->real_escape_string($nom_udt) . "' AND nom_gepi='$nom_gepi';";
	$test = mysqli_query($mysqli, $sql);
	if ($test->num_rows == 0) {
		$test->close();
		$sql = "INSERT INTO udt_corresp SET champ='$champ', nom_udt='" . $mysqli->real_escape_string($nom_udt) . "', nom_gepi='$nom_gepi';";
		$insert = mysqli_query($mysqli, $sql);
		return $insert;
	} else {
		$test->close();
		return true;
	}
}

/**
 * Affichage si le compte est auth_mode=sso et si on utilise la table de correspondance,
 * si l'association est faite.
 *
 * @param string $login id de l'utilisateur cherché
 * @return string Le code html
 * @global string
 */
function temoin_compte_sso($login_user) {
	global $mysqli;
	global $gepiPath;

	$retour = "";

	if (getSettingAOui('sso_cas_table')) {
		$sql = "SELECT auth_mode FROM utilisateurs WHERE login='$login_user' AND auth_mode='sso';";
		$test = mysqli_query($mysqli, $sql);
		if ($test->num_rows > 0) {
			$sql2 = "SELECT login_sso FROM sso_table_correspondance WHERE login_gepi='$login_user';";
			$test2 = mysqli_query($mysqli, $sql2);
			if ($test2->num_rows == 0) {
				$retour .= "<img src='" . $gepiPath . "/images/icons/sens_interdit.png' width='16' height='16' alt=\"Correspondance SSO absente\" title=\"La correspondance SSO n'est pas enregistrée dans la table 'sso_table_correspondance' pour ce compte.\" />";
			} else {
				$lig = mysqli_fetch_object($test2);
				if ($lig->login_sso == "") {
					$retour .= "<img src='" . $gepiPath . "/images/icons/sens_interdit.png' width='16' height='16' alt=\"Correspondance SSO vide\" title=\"La correspondance SSO est vide dans la table 'sso_table_correspondance' pour ce compte Gepi.\" />";
				} else {
					$retour .= "<img src='" . $gepiPath . "/images/icons/buddy_sso.png' width='16' height='16' alt=\"Correspondance SSO présente\" title=\"La correspondance SSO est enregistrée";
					if ($_SESSION['statut'] == 'administrateur') {
						$retour .= " (" . $lig->login_sso . ")";
					}
					$retour .= " dans la table 'sso_table_correspondance' pour ce compte.\" />";
				}
			}
			$test->close();
		}
	}

	return $retour;
}

function check_mae($login_user) {
	global $mysqli;

	$test = sql_query1("SHOW TABLES LIKE 'mod_alerte_divers'");
	if ($test == -1) {
		return true;
	} else {
		$sql = "SELECT 1=1 FROM mod_alerte_divers WHERE name='login_exclus' AND value='" . $login_user . "';";

		$resultat = mysqli_query($mysqli, $sql);
		$nb_lignes = $resultat->num_rows;

		if ($nb_lignes == 0) {
			return true;
		} else {
			$resultat->close();
			return false;
		}
	}
}

function clean_table_log($jusque_telle_date) {
	global $mysqli;

	if (($jusque_telle_date == "") || (!preg_match("#[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}#", $jusque_telle_date))) {
		return "<span style='color:red'>Date '$jusque_telle_date' invalide.</span>";
	} else {
		$tmp_tab = explode("/", $jusque_telle_date);
		$log_day = $tmp_tab[0];
		$log_month = $tmp_tab[1];
		$log_year = $tmp_tab[2];
		if (!checkdate($log_month, $log_day, $log_year)) {
			return "<span style='color:red'>Date '$jusque_telle_date' invalide.</span>";
		} else {

			// Pour éviter de flinguer la session en cours
			$hier_day = date('d', time() - 24 * 3600);
			$hier_month = date('m', time() - 24 * 3600);
			$hier_year = date('Y', time() - 24 * 3600);

			//$sql="SELECT * FROM log WHERE start<'$log_year-$log_month-$log_day 00:00:00' AND start<'".date('Y')."-".date('m')."-".$hier." 00:00:00';";
			$sql = "DELETE FROM log WHERE start<'$log_year-$log_month-$log_day 00:00:00' AND start<'" . $hier_year . "-" . $hier_month . "-" . $hier_day . " 00:00:00';";
			//echo "$sql<br />\n";
			$del = mysqli_query($mysqli, $sql);
			if (!$del) {
				return "<span style='color:red'>Echec du nettoyage.</span>\n";
			} else {
				return "<span style='color:green'>Nettoyage effectué.</span>\n";
			}
		}
	}
}

function clean_table_tentative_intrusion($jusque_telle_date) {
	global $mysqli;

	if (($jusque_telle_date == "") || (!preg_match("#[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}#", $jusque_telle_date))) {
		return "<span style='color:red'>Date '$jusque_telle_date' invalide.</span>";
	} else {
		$tmp_tab = explode("/", $jusque_telle_date);
		$log_day = $tmp_tab[0];
		$log_month = $tmp_tab[1];
		$log_year = $tmp_tab[2];
		if (!checkdate($log_month, $log_day, $log_year)) {
			return "<span style='color:red'>Date '$jusque_telle_date' invalide.</span>";
		} else {

			// Pour éviter de flinguer la session en cours
			$hier_day = date('d', time() - 24 * 3600);
			$hier_month = date('m', time() - 24 * 3600);
			$hier_year = date('Y', time() - 24 * 3600);

			//$sql="SELECT * FROM log WHERE start<'$log_year-$log_month-$log_day 00:00:00' AND start<'".date('Y')."-".date('m')."-".$hier." 00:00:00';";
			$sql = "DELETE FROM tentatives_intrusion WHERE date<'$log_year-$log_month-$log_day 00:00:00' AND date<'" . $hier_year . "-" . $hier_month . "-" . $hier_day . " 00:00:00';";
			//echo "$sql<br />\n";
			$del = mysqli_query($mysqli, $sql);
			if (!$del) {
				return "<span style='color:red'>Echec du nettoyage.</span>\n";
			} else {
				return "<span style='color:green'>Nettoyage effectué.</span>\n";
			}
		}
	}
}

function is_eleve_du_groupe($login_ele, $id_groupe) {
	global $mysqli;
	$sql = "SELECT 1=1 FROM j_eleves_groupes WHERE login='$login_ele' AND id_groupe='$id_groupe';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		return false;
	} else {
		$res->close();
		return true;
	}
}

function chercher_homonyme($nom, $prenom, $statut = "eleve") {
	global $mysqli;
	$tab = array();

	$tmp_nom = preg_replace("/[^A-Za-z]/", "%", $nom);
	$tmp_prenom = preg_replace("/[^A-Za-z]/", "%", $prenom);

	if ($statut == "eleve") {
		$sql = "SELECT * FROM eleves WHERE nom LIKE '$tmp_nom' AND prenom LIKE '$tmp_prenom';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$cpt = 0;
			while ($lig = $res->fetch_object()) {
				$tab[$cpt]['login'] = $lig->login;
				$tab[$cpt]['nom'] = $lig->nom;
				$tab[$cpt]['prenom'] = $lig->prenom;

				$sql = "SELECT DISTINCT c.classe FROM classes c, j_eleves_classes jec WHERE jec.id_classe=c.id AND jec.login='$lig->login' ORDER BY periode;";
				$res2 = mysqli_query($mysqli, $sql);
				if ($res2->num_rows > 0) {
					while ($lig2 = $res2->fetch_object()) {
						$tab[$cpt]['classe'][] = $lig2->classe;
					}
					$res2->close();
				}
				$cpt++;
			}
			$res->close();
		}
	} elseif ($statut == "responsable") {
		$sql = "SELECT * FROM resp_pers WHERE nom LIKE '$tmp_nom' AND prenom LIKE '$tmp_prenom';";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$cpt = 0;
			while ($lig = $res->fetch_object()) {
				$tab[$cpt]['login'] = $lig->login;
				$tab[$cpt]['nom'] = $lig->nom;
				$tab[$cpt]['prenom'] = $lig->prenom;

				// Chercher les enfants associés
				$tab[$cpt]['responsable_de'] = get_enfants_from_resp_login($lig->login);
				$cpt++;
			}
			$res->close();
		}
	}

	return $tab;
}

function get_classes_from_prof($login) {
	global $mysqli;
	$tab = array();

	$sql = "SELECT DISTINCT id, classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp 
		WHERE c.id=jgc.id_classe 
		AND jgc.id_groupe=jgp.id_groupe 
		AND jgp.login='$login'
			ORDER BY c.classe;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[$lig->id] = $lig->classe;
		}
	}
	$res->close();
	return $tab;
}

function get_matieres_from_prof($login) {
	global $mysqli;
	$tab = array();

	$tmp_tab = array();
	$sql = "SELECT DISTINCT id_matiere FROM j_groupes_matieres jgm, j_groupes_professeurs jgp
		WHERE jgm.id_groupe=jgp.id_groupe AND jgp.login='$login';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tmp_tab[] = $lig->id_matiere;
		}
		$res->close();
	}

	$sql = "SELECT DISTINCT matiere, nom_complet FROM matieres m, j_professeurs_matieres jpm
		WHERE m.matiere=jpm.id_matiere AND jpm.id_professeur='$login'
			ORDER BY jpm.ordre_matieres, m.matiere;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$cpt = 0;
		while ($lig = $res->fetch_object()) {
			$tab[$cpt]['matiere'] = $lig->matiere;
			$tab[$cpt]['nom_complet'] = $lig->nom_complet;
			if (in_array($lig->matiere, $tmp_tab)) {
				$tab[$cpt]['enseignee'] = "y";
			} else {
				$tab[$cpt]['enseignee'] = "n";
			}
			$cpt++;
		}
		$res->close();
	}

	return $tab;
}

/**
 * @throws \Exception
 */
function get_profs_from_matiere($matiere) {
	global $mysqli;
	$tab = array();

	$tmp_tab = array();
	$sql = "SELECT DISTINCT login FROM j_groupes_matieres jgm, j_groupes_professeurs jgp
		WHERE jgm.id_groupe=jgp.id_groupe AND jgm.id_matiere='$matiere';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tmp_tab[] = $lig->login;
		}
		$res->close();
	}

	$sql = "SELECT DISTINCT u.login, u.nom, u.prenom, u.civilite, u.etat 
		FROM utilisateurs u, j_professeurs_matieres jpm
		WHERE u.login=jpm.id_professeur AND jpm.id_matiere='$matiere'
			ORDER BY u.login;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$cpt = 0;
		while ($lig = $res->fetch_object()) {
			$tab[$cpt]['login'] = $lig->login;
			$tab[$cpt]['nom'] = casse_mot($lig->nom, 'maj');
			$tab[$cpt]['prenom'] = casse_mot($lig->prenom, 'majf2');
			$tab[$cpt]['civilite'] = $lig->civilite;
			$tab[$cpt]['designation'] = $lig->civilite . " " . $tab[$cpt]['nom'] . " " . $tab[$cpt]['prenom'];
			if (in_array($lig->login, $tmp_tab)) {
				$tab[$cpt]['enseignee'] = "y";
			} else {
				$tab[$cpt]['enseignee'] = "n";
			}
			$cpt++;
		}
		$res->close();
	}

	return $tab;
}

/** Fonction destinée à tester si un utilisateur a le droit d'accéder au CDT de tel élève
 *
 * @param string $login_user Login de l'utilisateur
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function acces_cdt_eleve($login_user, $login_eleve) {
	global $mysqli;
	$retour = false;

	$sql = "SELECT statut FROM utilisateurs WHERE login='$login_user';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$obj = $res->fetch_object();
		$statut = $obj->statut;
		$res->close();

		if (($statut == "eleve") && ($login_user == $login_eleve) && (getSettingAOui('GepiAccesCahierTexteEleve'))) {
			$retour = true;
		} elseif (($statut == "responsable") && (getSettingAOui('GepiAccesCahierTexteParent')) && (getSettingAOui('GepiMemesDroitsRespNonLegaux')) && (is_responsable($login_eleve, $login_user, "", "yy"))) {
			$retour = true;
		} elseif (($statut == "responsable") && (getSettingAOui('GepiAccesCahierTexteParent')) && (is_responsable($login_eleve, $login_user))) {
			$retour = true;
		} elseif (($statut == "professeur") && (getSettingAOui('GepiAccesCDTToutesClasses'))) {
			$retour = true;
		} elseif ($statut == "professeur") {
			$sql_prof = "SELECT 1=1 FROM j_groupes_professeurs jgp, j_eleves_groupes jeg WHERE jgp.id_groupe=jeg.id_groupe AND jeg.login='$login_eleve' AND jgp.login='$login_user';";
			$res_prof = mysqli_query($mysqli, $sql_prof);
			if ($res_prof->num_rows > 0) {
				$res_prof->close();
				$retour = true;
			}
			// Donner le droit au PP?
		} elseif (($statut == "cpe") && (getSettingAOui('GepiAccesCdtCpe'))) {
			$retour = true;
		} elseif (($statut == "cpe") && (getSettingAOui('GepiAccesCdtCpeRestreint'))) {
			$sql_cpe = "SELECT 1=1 FROM j_eleves_cpe WHERE e_login='$login_eleve' AND cpe_login='$login_user';";
			$res_cpe = mysqli_query($mysqli, $sql_cpe);
			if ($res_cpe->num_rows > 0) {
				$res_cpe->close();
				$retour = true;
			}
		} elseif (($statut == "scolarite") && (getSettingAOui('GepiAccesCdtScol'))) {
			$retour = true;
		} elseif (($statut == "scolarite") && (getSettingAOui('GepiAccesCdtScolRestreint'))) {
			$sql_scol = "SELECT 1=1 FROM j_scol_classes jsc, j_eleves_classes jec WHERE jsc.id_classe=jec.id_classe AND jsc.login='$login_user' AND jec.login='$login_eleve';";
			$res_scol = mysqli_query($mysqli, $sql_scol);
			if ($res_scol->num_rows > 0) {
				$res_scol->close();
				$retour = true;
			}
		} elseif ($statut == "autre") {
			if (acces("/cahier_texte/see_all.php", $statut)) {
				// Dans le cas statut autre, le test est fait sur $_SESSION['login'] dans acces()
				$retour = true;
			}
		} elseif ($statut == "administrateur") {
			$retour = true;
		}
	}

	return $retour;
}

/** fonction testant la présence de limitations à la transmission de variables (par suhosin ou autre)
 *
 * @return boolean Limitation ou pas
 */
function test_alerte_config_suhosin() {
	$suhosin_post_max_totalname_length = ini_get('suhosin.post.max_totalname_length');
	$max_input_vars = ini_get('max_input_vars');
	if ($suhosin_post_max_totalname_length != '') {
		return true;
	} elseif (($max_input_vars != "") && ($max_input_vars > 0)) {
		return true;
	} else {
		return false;
	}
}

/** Fonction destinée à récupérer dans la table 'utilisateurs' le mail d'un utilisateur
 *
 * @param string $login_user Login de l'utilisateur
 *
 * @return string Le mail de l'utilisateur
 */
function get_mail_user($login_user) {
	global $mysqli;
	$retour = "";

	$email_user = "";
	$statut_user = "";
	$sql = "SELECT statut,email FROM utilisateurs WHERE login='$login_user';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$obj = $res->fetch_object();
		$statut_user = $obj->statut;
		$email_user = $obj->email;
		$res->close();

		if ($statut_user == "responsable") {
			if ((getSettingValue('mode_email_resp') == 'mon_compte') && (check_mail($email_user))) {
				// Email trouvé.
				$retour = $email_user;
			} else {
				$sql = "SELECT mel FROM resp_pers WHERE login='$login_user';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if ($res->num_rows > 0) {
					$obj = $res->fetch_object();
					$mel_user = $obj->mel;
					$res->close();

					if (check_mail($mel_user)) {
						// Email trouvé.
						$retour = $mel_user;
					} else {
						// Choix faute de mieux
						$retour = $email_user;
					}
				}
			}
		} elseif ($statut_user == "eleve") {
			if ((getSettingValue('mode_email_ele') == 'mon_compte') && (check_mail($email_user))) {
				// Email trouvé.
				$retour = $email_user;
			} else {
				$sql = "SELECT email FROM eleves WHERE login='$login_user';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if ($res->num_rows > 0) {
					$obj = $res->fetch_object();
					$email_ele_user = $obj->email;
					$res->close();

					if (check_mail($email_ele_user)) {
						// Email trouvé.
						$retour = $email_ele_user;
					} else {
						// Choix faute de mieux
						$retour = $email_user;
					}
				}
			}
		} else {
			// Personnel de l'établissement
			$retour = $email_user;
		}
	} else {
		// Cas d'un parent dont le compte utilisateur aurait été supprimé
		$sql = "SELECT mel FROM resp_pers WHERE login='$login_user';";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if ($res->num_rows > 0) {
			$obj = $res->fetch_object();
			$retour = $obj->mel;
			$res->close();
		} else {
			$sql = "SELECT email FROM eleves WHERE login='$login_user';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if ($res->num_rows > 0) {
				$obj = $res->fetch_object();
				$retour = $obj->email;
				$res->close();
			}
		}
	}

	return $retour;
}

/** Fonction destinée à récupérer un tableau associatif des classes concernant un utilisateur
 *
 * @param string $login_user Login de l'utilisateur
 * TODO: Ajouter un paramètre pour spécifier le contexte dans lequel on veut faire l'extraction
 *       Il n'est pas toujours judicieux de restreindre la liste si par exemple tous les CPE ont les mêmes droits sur tous les élèves.
 *
 * @return array Le tableau des classes avec l'id_classe pour indice et classe pour valeur
 */
function get_classes_from_user($login_user, $statut) {
	global $mysqli;
	$tab = array();

	if ($statut == 'professeur') {
		$sql = "SELECT DISTINCT id, classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp 
			WHERE c.id=jgc.id_classe 
			AND jgc.id_groupe=jgp.id_groupe 
			AND jgp.login='$login_user'
				ORDER BY c.classe;";
	} elseif ($statut == 'administrateur') {
		$sql = "SELECT DISTINCT id, classe FROM classes c
				ORDER BY c.classe;";
	} elseif ($statut == 'secours') {
		$sql = "SELECT DISTINCT id, classe FROM classes c
				ORDER BY c.classe;";
	} elseif ($statut == 'autre') {
		$sql = "SELECT DISTINCT id, classe FROM classes c
				ORDER BY c.classe;";
	} elseif ($statut == 'scolarite') {
		$sql = "SELECT DISTINCT c.id, c.classe FROM classes c, j_scol_classes jsc
				WHERE jsc.id_classe=c.id
				AND jsc.login='$login_user'
				ORDER BY c.classe;";
	} elseif ($statut == 'cpe') {
		$sql = "SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec, j_eleves_cpe jecpe
				WHERE jec.id_classe=c.id
				AND jec.login=jecpe.e_login
				AND jecpe.cpe_login='$login_user'
				ORDER BY c.classe;";
	}

	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab[$lig->id] = $lig->classe;
		}
	}
	$res->close();
	return $tab;
}

/** Fonction destinée à tester si un utilisateur est professeur du groupe
 *
 * @param string $login Login de l'utilisateur
 * @param integer $id_groupe Identifiant du groupe
 *
 * @return boolean True/False selon que l'utilisateur est ou non prof du groupe
 */
function verif_prof_groupe($login, $id_groupe) {
	global $mysqli;
	if (empty($login) || empty($id_groupe)) {
		return FALSE;
		die();
	}

	$sql = "SELECT login FROM j_groupes_professeurs WHERE (id_groupe='" . $id_groupe . "' and login='" . $login . "')";
	//echo "$sql<br />";
	$call_prof = mysqli_query($mysqli, $sql);
	$nb = mysqli_num_rows($call_prof);

	if ($nb != 0) {
		return TRUE;
	} else {
		return FALSE;
	}
}

/** Fonction destinée à envoyer un mail suite à une proposition de correction d'appréciation
 *
 * @param string $corriger_app_login_eleve Login de l'élève
 * @param integer $corriger_app_id_groupe Identifiant du groupe
 * @param integer $corriger_app_num_periode Numéro de période
 * $texte_mail string l'explication à envoyer
 *
 * @return $string Chaine pour $msg
 */
function envoi_mail_proposition_correction($corriger_app_login_eleve, $corriger_app_id_groupe, $corriger_app_num_periode, $texte_mail) {
	global $mysqli;
	global $prefixe_debug;
	if ($prefixe_debug == "") {
		$prefixe_debug = strftime("%Y%m%d %H%M%S") . " : " . $_SESSION['login'];
	}

	$msg = "";

	if ($texte_mail != "") {

		$envoi_mail_actif = getSettingValue('envoi_mail_actif');
		if (($envoi_mail_actif != 'n') && ($envoi_mail_actif != 'y')) {
			$envoi_mail_actif = 'y'; // Passer à 'n' pour faire des tests hors ligne... la phase d'envoi de mail peut sinon ensabler.
		}

		if ($envoi_mail_actif == 'y') {
			$email_destinataires = "";

			$sql = "SELECT id_classe FROM j_eleves_classes WHERE (login='$corriger_app_login_eleve' AND periode='$corriger_app_num_periode');";
			$req = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($req) > 0) {
				//$correction_id_classe=mysql_result($req,0,"id_classe");
				$obj_classe = $req->fetch_object();
				$correction_id_classe = $obj_classe->id_classe;
				$sql = "(SELECT DISTINCT email FROM utilisateurs WHERE statut='secours' AND email!='')
				UNION (SELECT DISTINCT email FROM utilisateurs u, j_scol_classes jsc WHERE u.login=jsc.login AND id_classe='$correction_id_classe');";
			} else {
				//$sql="select email from utilisateurs where statut='secours' AND email!='';";
				$sql = "select email from utilisateurs where (statut='secours' OR statut='scolarite') AND email!='';";
			}
			//echo "$sql<br />";
			fich_debug_proposition_correction_app($prefixe_debug . " : $sql\n");
			$req = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($req) > 0) {
				$lig_u = mysqli_fetch_object($req);
				$email_destinataires = $lig_u->email;
				while ($lig_u = mysqli_fetch_object($req)) {
					$email_destinataires .= ", " . $lig_u->email;
					$tab_param_mail['destinataire'][] = $lig_u->email;
				}

				$email_declarant = "";
				$nom_declarant = "";
				$sql = "select nom, prenom, civilite, email from utilisateurs where login = '" . $_SESSION['login'] . "';";
				fich_debug_proposition_correction_app($prefixe_debug . " : $sql\n");
				$req = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($req) > 0) {
					$lig_u = mysqli_fetch_object($req);
					$nom_declarant = $lig_u->civilite . " " . casse_mot($lig_u->nom, 'maj') . " " . casse_mot($lig_u->prenom, 'majf');
					$email_declarant = $lig_u->email;
					$tab_param_mail['from'] = $lig_u->email;
					$tab_param_mail['from_name'] = $nom_declarant;
				}

				$email_autres_profs_grp = "";
				// Recherche des autres profs du groupe
				$sql = "SELECT DISTINCT u.email FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.id_groupe='$corriger_app_id_groupe' AND jgp.login=u.login AND u.login!='" . $_SESSION['login'] . "' AND u.email!='';";
				fich_debug_proposition_correction_app($prefixe_debug . " : $sql\n");
				//echo "$sql<br />";
				$req = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($req) > 0) {
					$lig_u = mysqli_fetch_object($req);
					$email_autres_profs_grp .= $lig_u->email;

					$tab_param_mail['cc'][] = $lig_u->email;
					while ($lig_u = mysqli_fetch_object($req)) {
						$email_autres_profs_grp .= "," . $lig_u->email;
						$tab_param_mail['cc'][] = $lig_u->email;
					}
				}

				$sujet_mail = "Demande de validation de correction d'appréciation";

				$ajout_header = "";
				if ($email_declarant != "") {
					$ajout_header .= "Cc: $nom_declarant <" . $email_declarant . ">";
					$tab_param_mail['cc'][] = $email_declarant;
					if ($email_autres_profs_grp != '') {
						$ajout_header .= ", $email_autres_profs_grp";
					}
					$ajout_header .= "\r\n";
					$ajout_header .= "Reply-to: $nom_declarant <" . $email_declarant . ">\r\n";
					$tab_param_mail['replyto'] = $email_declarant;
					$tab_param_mail['replyto_name'] = $nom_declarant;

				} elseif ($email_autres_profs_grp != '') {
					$ajout_header .= "Cc: $email_autres_profs_grp\r\n";
				}

				$salutation = (date("H") >= 18 or date("H") <= 5) ? "Bonsoir" : "Bonjour";
				$texte_mail = $salutation . ",\n\n" . $texte_mail . "\nCordialement.\n-- \n" . $nom_declarant;

				fich_debug_proposition_correction_app($prefixe_debug . " : envoi_mail($sujet_mail, \n$texte_mail, \n$email_destinataires, \n$ajout_header)\n");
				$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header, "plain", $tab_param_mail);
				if (!$envoi) {
					fich_debug_proposition_correction_app($prefixe_debug . " : Erreur lors de l envoi du mail.\n\n");
				}
			} else {
				$msg .= "Aucun compte scolarité avec adresse mail n'est associé à cet(te) élève.<br />Pas de compte secours avec adresse mail non plus.<br />La correction a été soumise, mais elle n'a pas fait l'objet d'un envoi de mail.<br />";
				fich_debug_proposition_correction_app($prefixe_debug . " : Aucun compte secours ni scol associé à la classe.\n");
			}
		}
	}
	return $msg;
}

function fich_debug_proposition_correction_app($texte) {
	$debug = "n";
	if ($debug == "y") {
		$dirname = getSettingValue('backup_directory');
		$chaine_jour = strftime("%Y%m%d");
		$f = fopen("../backup/" . $dirname . "/debug_proposition_correction_app_" . $chaine_jour . ".txt", "a+");
		fwrite($f, $texte);
		fclose($f);
	}
}

/** Fonction destinée à renvoyer le nom du statut personnalisé d'après l'id du statut ou à défaut d'après le login de l'utilisateur
 *
 * @param integer $id_statut identifiant du statut personnalisé
 * @param string $login_user login de l'utilisateur
 *
 * @return $string le nom du statut
 */
function get_nom_statut_autre($id_statut, $login_user = "") {
	global $mysqli;
	if ($login_user != "") {
		$sql = "SELECT nom_statut FROM droits_statut ds, droits_utilisateurs du WHERE du.login_user = '" . $login_user . "' AND du.id_statut = ds.id;";
	} else {
		$sql = "SELECT nom_statut FROM droits_statut ds WHERE ds.id = '" . $id_statut . "';";
	}
	//echo "$sql<br />";
	$query = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($query) > 0) {
		$rep = mysqli_fetch_array($query);
		return $rep["nom_statut"];
	} else {
		return "";
	}
}

/** Fonction destinée à tester si le serveur accède à internet.
 *  Cas d'un serveur en DMZ: On peut accéder au serveur en interne même si l'accès internet est coupé.
 *  Dans ce cas, les tests DNS (par exemple) échoueront.
 *  Un test ping peut rendre des services pour désactiver certains tests nécessitant un accès internet.
 *
 * @param string $host IP ou nom DNS
 * @param integer $port le port à atteindre
 * @param integer $timeout le temps max d'attente
 *
 * @return $string 'down' s'il n'y a pas d'accès et une durée en ms sinon.
 */
function ping($host, $port, $timeout) {
	$tB = microtime(true);
	$fP = @fSockOpen($host, $port, $errno, $errstr, $timeout);
	if (!$fP) {
		return "down";
	}
	$tA = microtime(true);
	return round((($tA - $tB) * 1000), 0) . " ms";
}

function afficher_les_evenements($afficher_obsolete = "n") {
	global $gepiPath;
	$retour = "";

	if ($afficher_obsolete == "y") {
		if ($_SESSION['statut'] == 'professeur') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='professeur' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'cpe') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='cpe' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'scolarite') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='scolarite' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'responsable') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, 
									d_dates_evenements_classes ddec, 
									d_dates_evenements_utilisateurs ddeu 
								WHERE ddeu.statut='responsable' AND 
									ddeu.id_ev=dde.id_ev AND 
									dde.id_ev=ddec.id_ev AND 
									dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND 
									id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y'));";
		} elseif ($_SESSION['statut'] == 'eleve') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, 
									d_dates_evenements_classes ddec, 
									d_dates_evenements_utilisateurs ddeu,
									j_eleves_classes jec 
								WHERE ddeu.statut='eleve' AND 
									ddeu.id_ev=dde.id_ev AND 
									dde.id_ev=ddec.id_ev AND 
									dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND 
									ddec.id_classe=jec.id_classe AND 
									jec.login='" . $_SESSION['login'] . "';";
		}
	} else {
		if ($_SESSION['statut'] == 'professeur') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='professeur' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND ddec.date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND dde.id_ev=ddec.id_ev AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'cpe') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='cpe' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND ddec.date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'scolarite') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, d_dates_evenements_classes ddec, d_dates_evenements_utilisateurs ddeu WHERE ddeu.statut='scolarite' AND ddeu.id_ev=dde.id_ev AND dde.id_ev=ddec.id_ev AND ddec.date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "');";
		} elseif ($_SESSION['statut'] == 'responsable') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, 
									d_dates_evenements_classes ddec, 
									d_dates_evenements_utilisateurs ddeu 
								WHERE ddeu.statut='responsable' AND 
									ddeu.id_ev=dde.id_ev AND 
									dde.id_ev=ddec.id_ev AND 
									ddec.date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND 
									dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND 
									id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y'));";
		} elseif ($_SESSION['statut'] == 'eleve') {
			$sql = "SELECT DISTINCT ddec.id_ev FROM d_dates_evenements dde, 
									d_dates_evenements_classes ddec, 
									d_dates_evenements_utilisateurs ddeu,
									j_eleves_classes jec 
								WHERE ddeu.statut='eleve' AND 
									ddeu.id_ev=dde.id_ev AND 
									dde.id_ev=ddec.id_ev AND 
									ddec.date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND 
									dde.date_debut<='" . strftime("%Y-%m-%d %H:%M:%S") . "' AND 
									ddec.id_classe=jec.id_classe AND 
									jec.login='" . $_SESSION['login'] . "';";
		}
	}

	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$retour .= "<div style='border: 1px solid grey; background-image: url(\"$gepiPath/images/background/opacite50.png\");padding: 3px;margin: 3px;'>";

			if (in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				$retour .= "<div style='float:right; width:16px;'><a href='$gepiPath/classes/dates_classes.php?id_ev=" . $lig->id_ev . "' title=\"Modifier cet événement.\"><img src='$gepiPath/images/edit16.png' class='icone16' alt='Editer' /></a></div>";
			}

			//$retour.="$sql<br />";
			$retour .= affiche_evenement($lig->id_ev, $afficher_obsolete);
			$retour .= "</div>";
		}
	}
	/*
	else {
		$retour.="<div style='border: 1px solid grey; background-image: url(\"$gepiPath/images/background/opacite50.png\");padding: 3px;margin: 3px;'>";
		//$retour.="$sql<br />";
		$retour.="<span style='color:red'>Aucune classe n'est associée à l'événement.</span>";
		$retour.="</div>";
	}
	*/
	return $retour;
}

function affiche_evenement($id_ev, $afficher_obsolete = "n") {
	global $gepiPath;
	global $tab_salle;
	global $evenement_sans_lien_mail;
	global $evenement_sans_lien_ics;
	global $mes_groupes;

	/*
	global $posDiv_infobulle;
	global $tabid_infobulle;
	global $unite_div_infobulle;
	global $niveau_arbo;
	global $pas_de_decalage_infobulle;
	global $class_special_infobulle;
	*/
	global $tabdiv_infobulle;

	$retour = "";

	if ((!isset($tab_salle)) || (!is_array($tab_salle)) || (count($tab_salle) == 0)) {
		$tab_salle = get_tab_salle_cours();
	}

	$sql = "SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$tab_u = array();
		$sql = "SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev';";
		//echo "$sql<br />";
		$res_u = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_u) > 0) {
			while ($lig_u = mysqli_fetch_object($res_u)) {
				$tab_u[] = $lig_u->statut;
			}
		}

		if ((!isset($evenement_sans_lien_mail)) || ($evenement_sans_lien_mail != "y")) {
			if (acces_info_dates_evenements()) {
				$retour .= "<div style='float:right; width:16px;margin-right:3px;' title=\"Informer les/des destinataires par mail.\"><a href='$gepiPath/classes/info_dates_classes.php?id_ev=" . $id_ev . "' target='_blank'><img src='$gepiPath/images/icons/mail.png' class='icone16' alt='Mail' /></a></div>";
			}
		}

		if ((!isset($evenement_sans_lien_ics)) || ($evenement_sans_lien_ics != "y")) {
			$retour .= "<div style='float:right; width:16px;margin-right:3px;' title=\"Exporter au format ical/ics l'événement.\nVous pourrez l'importer dans un agenda type Google, WebCalendar,...\"><a href='$gepiPath/lib/ical.php?id_ev=" . $id_ev . "' target='_blank'><img src='$gepiPath/images/icons/ical.png' class='icone16' alt='ical' /></a></div>";
		}

		if ($lig->type == 'autre') {
			//$retour.=nl2br($lig->description)."<br />";
			$retour .= $lig->texte_avant;
			//$retour.="<br />";

			$liste_dest = "";
			if (in_array("professeur", $tab_u)) {
				$liste_dest .= " <img src='$gepiPath/images/icons/prof.png' class='icone16' alt='Prof' title=\"Professeurs de la classe.\" />";
			}
			if (in_array("cpe", $tab_u)) {
				$liste_dest .= " <img src='$gepiPath/images/icons/cpe.png' class='icone16' alt='Cpe' title=\"CPE de la classe.\" />";
			}
			if (in_array("scolarite", $tab_u)) {
				$liste_dest .= " <img src='$gepiPath/images/icons/scolarite.png' class='icone16' alt='Scol' title=\"Comptes scolarité associés à la classe.\" />";
			}
			if (in_array("responsable", $tab_u)) {
				$liste_dest .= " <img src='$gepiPath/images/icons/responsable.png' class='icone16' alt='Resp' title=\"Comptes responsables associés à la classe.\" />";
			}
			if (in_array("eleve", $tab_u)) {
				$liste_dest .= " <img src='$gepiPath/images/icons/eleve.png' class='icone16' alt='Resp' title=\"Élèves associés à la classe.\" />";
			}
			//$retour.="<br />";

			if ($afficher_obsolete == "y") {
				if ($_SESSION['statut'] == 'professeur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'cpe') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'scolarite') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'administrateur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'responsable') {
					$sql = "SELECT DISTINCT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
																) ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'eleve') {
					$sql = "SELECT DISTINCT d.*, c.* FROM d_dates_evenements_classes d, classes c, j_eleves_classes jec WHERE id_ev='$id_ev' AND d.id_classe=c.id AND d.id_classe=jec.id_classe AND jec.login='" . $_SESSION['login'] . "' ORDER BY date_evenement, classe;";
				}
			} else {
				// 12h après
				if ($_SESSION['statut'] == 'professeur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'cpe') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'scolarite') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'administrateur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'responsable') {
					$sql = "SELECT DISTINCT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
																) ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'eleve') {
					$sql = "SELECT DISTINCT d.*, c.* FROM d_dates_evenements_classes d, classes c, j_eleves_classes jec WHERE id_ev='$id_ev' AND d.id_classe=c.id AND d.id_classe=jec.id_classe AND jec.login='" . $_SESSION['login'] . "' AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' ORDER BY date_evenement, classe;";
				}
			}
			//$retour.="$sql<br />";
			$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res2) > 0) {

				$tab_tableau = array();
				$tab_liste_salles = array();
				$tab_liste_dates = array();
				while ($lig2 = mysqli_fetch_object($res2)) {
					if (!in_array($lig2->date_evenement, $tab_liste_dates)) {
						$tab_liste_dates[] = $lig2->date_evenement;
					}

					if (!in_array($lig2->id_salle, $tab_liste_salles)) {
						$tab_liste_salles[] = $lig2->id_salle;
					}

					if (isset($tab_tableau[$lig2->date_evenement][$lig2->id_salle])) {
						$tab_tableau[$lig2->date_evenement][$lig2->id_salle] .= ", ";
					} else {
						$tab_tableau[$lig2->date_evenement][$lig2->id_salle] = "";
					}

					if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
						//$tab_tableau[$lig2->date_evenement][$lig2->id_salle].="<span style='color:red'>".$lig2->classe."&nbsp;: ".formate_date($lig2->date_evenement, "y", "court")."</span>";
						$tab_tableau[$lig2->date_evenement][$lig2->id_salle] .= "<span style='color:red'>" . $lig2->classe . "</span>";
					} else {
						//$tab_tableau[$lig2->date_evenement][$lig2->id_salle].=$lig2->classe."&nbsp;: ".formate_date($lig2->date_evenement, "y", "court");
						$tab_tableau[$lig2->date_evenement][$lig2->id_salle] .= $lig2->classe;
					}

					/*
					if(($lig2->id_salle>0)&&(isset($tab_salle['indice'][$lig2->id_salle]))) {
						$tab_tableau[$lig2->date_evenement][$lig2->id_salle].=" (<em>salle ".$tab_salle['indice'][$lig2->id_salle]['designation_complete']."</em>)";
					}
					*/
					//$retour.="<br />";
				}

				$retour .= "<table class='boireaus boireaus_alt'>
	<tr>
		<th>$liste_dest</th>";
				for ($loop = 0; $loop < count($tab_liste_dates); $loop++) {
					$retour .= "
		<th>" . formate_date($tab_liste_dates[$loop], "y2", "court") . "</th>";
				}
				$retour .= "
	</tr>";

				for ($loop0 = 0; $loop0 < count($tab_liste_salles); $loop0++) {
					if (($tab_liste_salles[$loop0] > 0) && (isset($tab_salle['indice'][$tab_liste_salles[$loop0]]))) {
						$salle_courante = $tab_salle['indice'][$tab_liste_salles[$loop0]]['designation_complete'];
					} else {
						$salle_courante = "";
					}
					$retour .= "
	<tr>
		<th>$salle_courante</th>";
					for ($loop = 0; $loop < count($tab_liste_dates); $loop++) {
						$retour .= "
		<td>";
						if (isset($tab_tableau[$tab_liste_dates[$loop]][$tab_liste_salles[$loop0]])) {
							$retour .= $tab_tableau[$tab_liste_dates[$loop]][$tab_liste_salles[$loop0]];
						}
						$retour .= "</td>";
					}
					$retour .= "
	</tr>";
				}
				$retour .= "
</table>";

				/*
				while($lig2=mysqli_fetch_object($res2)) {
					if($lig2->date_evenement<strftime("%Y-%m-%d %H:%M:%S")) {
						$retour.="<span style='color:red'>".$lig2->classe."&nbsp;: ".formate_date($lig2->date_evenement, "y", "court")."</span>";
					}
					else {
						$retour.=$lig2->classe."&nbsp;: ".formate_date($lig2->date_evenement, "y", "court");
					}
					if(($lig2->id_salle>0)&&(isset($tab_salle['indice'][$lig2->id_salle]))) {
						$retour.=" (<em>salle ".$tab_salle['indice'][$lig2->id_salle]['designation_complete']."</em>)";
					}
					$retour.="<br />";
				}
				*/
			}
			if (($_SESSION['statut'] != 'eleve') && ($_SESSION['statut'] != 'responsable')) {
				$retour .= $lig->texte_apres;
			} else {
				$retour .= $lig->texte_apres_ele_resp;
			}
		} elseif ($lig->type == 'conseil_de_classe') {

			$texte_infobulle = "<div id='div_action_conseil_de_classe_$id_ev'></div>";
			$tabdiv_infobulle[] = creer_div_infobulle('div_infobulle_action_conseil_de_classe_' . $id_ev, "Bulletins et conseils de classe", "", $texte_infobulle, "", 40, 0, 'y', 'y', 'n', 'n');
			$retour .= "
<script type='text/javascript'>
	function afficher_action_classe_$id_ev(id_classe) {
		new Ajax.Updater($('div_action_conseil_de_classe_$id_ev'), '$gepiPath/lib/ajax_action.php?mode=actions_conseil_classe&id_classe='+id_classe,{method: 'get'});
		afficher_div('div_infobulle_action_conseil_de_classe_$id_ev', 'y', 10, 10);
		//alert('id_classe='+id_classe);
	}
</script>";

			$tab_classe_pp = array("id_classe");
			if ($_SESSION['statut'] == "professeur") {
				$tab_classe_pp = get_tab_ele_clas_pp($_SESSION['login']);
			}

			if (getSettingAOui('active_mod_engagements')) {
				if (($_SESSION['statut'] == "scolarite") ||
					(($_SESSION['statut'] == "cpe") && (getSettingAOui('imprimerConvocationConseilClasseCpe')))) {
					$retour .= "<div style='float:right;width:16px;margin-right:3px;'><a href=\"$gepiPath/mod_engagements/imprimer_documents.php\" title=\"Imprimer les documents pour les délégués\"><img src='$gepiPath/images/icons/odt.png' class='icone16' alt='Document' /></a></div>";
				}
			}

			$retour .= $lig->texte_avant;
			//$retour.="<br />";

			if ($afficher_obsolete == "y") {
				if ($_SESSION['statut'] == 'professeur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'cpe') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'scolarite') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'administrateur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'responsable') {
					$sql = "SELECT DISTINCT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
																) ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'eleve') {
					$sql = "SELECT DISTINCT d.*, c.* FROM d_dates_evenements_classes d, classes c, j_eleves_classes jec WHERE id_ev='$id_ev' AND d.id_classe=c.id AND d.id_classe=jec.id_classe AND jec.login='" . $_SESSION['login'] . "' ORDER BY date_evenement, classe;";
				}
			} else {
				// 12h après
				if ($_SESSION['statut'] == 'professeur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jgc.id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'cpe') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec, j_eleves_cpe jecpe WHERE jec.login=jecpe.e_login AND jecpe.cpe_login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'scolarite') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jsc.id_classe FROM j_scol_classes jsc WHERE jsc.login='" . $_SESSION['login'] . "') ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'administrateur') {
					$sql = "SELECT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' ORDER BY date_evenement, classe;";
				} elseif ($_SESSION['statut'] == 'responsable') {
					$sql = "(SELECT DISTINCT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
																)) ";

					if (getSettingAOui('active_mod_engagements')) {
						$sql_test = "SELECT eu.valeur AS id_classe FROM engagements e, 
											engagements_user eu
										WHERE e.id=eu.id_engagement AND 
											e.conseil_de_classe='yes' AND 
											eu.login='" . $_SESSION['login'] . "' AND 
											e.type='id_classe' AND 
											eu.id_type='id_classe' AND 
											eu.valeur NOT IN (SELECT DISTINCT jec.id_classe FROM resp_pers rp, 
																	responsables2 r, 
																	eleves e, 
																	j_eleves_classes jec 
																WHERE rp.login='" . $_SESSION['login'] . "' AND 
																	rp.pers_id=r.pers_id AND 
																	r.ele_id=e.ele_id AND 
																	e.login=jec.login AND 
																	(r.resp_legal='1' OR r.resp_legal='2' OR r.acces_sp='y')
																);";
						//echo "$sql_test<br />";
						$res_test = mysqli_query($GLOBALS["mysqli"], $sql_test);
						if (mysqli_num_rows($res_test) > 0) {
							while ($lig_test = mysqli_fetch_object($res_test)) {
								$sql .= " UNION (SELECT DISTINCT * FROM d_dates_evenements_classes d, classes c WHERE id_ev='$id_ev' AND d.id_classe=c.id AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' AND id_classe='" . $lig_test->id_classe . "')";
							}
						}
					}
					$sql .= " ORDER BY date_evenement, classe;";

				} elseif ($_SESSION['statut'] == 'eleve') {
					$sql = "SELECT DISTINCT d.*, c.* FROM d_dates_evenements_classes d, classes c, j_eleves_classes jec WHERE id_ev='$id_ev' AND d.id_classe=c.id AND d.id_classe=jec.id_classe AND jec.login='" . $_SESSION['login'] . "' AND date_evenement>='" . strftime("%Y-%m-%d %H:%M:%S", time() - 12 * 3600) . "' ORDER BY date_evenement, classe;";
				}
			}
			// DEBUG:
			//$retour.="$sql<br />";
			$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res2) > 0) {
				// On va remplir un tableau et repérer les jours et heures.
				$tab_jours = array();
				$tab_heures = array();
				$tab_cellules = array();
				while ($lig2 = mysqli_fetch_object($res2)) {
					$tmp_jour = get_date_slash_from_mysql_date($lig2->date_evenement, "court");
					if (!in_array($tmp_jour, $tab_jours)) {
						$tab_jours[] = $tmp_jour;
					}
					//sort($tab_jours);

					$tmp_tab_pp = get_tab_prof_suivi($lig2->id_classe);
					//$liste_pp=implode(", ", $tmp_tab_pp);
					$liste_pp = "";
					for ($loop = 0; $loop < count($tmp_tab_pp); $loop++) {
						if ($loop > 0) {
							$liste_pp .= "";
						}
						$liste_pp .= affiche_utilisateur($tmp_tab_pp[$loop], $lig2->id_classe);
					}

					$tmp_heure = get_heure_2pt_minute_from_mysql_date($lig2->date_evenement);
					if (!in_array($tmp_heure, $tab_heures)) {
						$tab_heures[] = $tmp_heure;
					}
					sort($tab_heures);

					$indication_salle = "";
					if (($lig2->id_salle > 0) && (isset($tab_salle['indice'][$lig2->id_salle]))) {
						$indication_salle = "\nSalle: " . $tab_salle['indice'][$lig2->id_salle]['designation_complete'] . "";
					}

					/*
					if($lig2->date_evenement<strftime("%Y-%m-%d %H:%M:%S")) {
						if(!isset($tab_cellules[$tmp_jour][$tmp_heure])) {
							$tab_cellules[$tmp_jour][$tmp_heure]="";
						}
						else {
							$tab_cellules[$tmp_jour][$tmp_heure].=" - ";
						}
						$tab_cellules[$tmp_jour][$tmp_heure].="<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : ".formate_date($lig2->date_evenement, "y")."
".ucfirst(getSettingValue('gepi_prof_suivi'))." : $liste_pp\">".$lig2->classe."</span>";
					}
					else {
					*/
					if (!isset($tab_cellules[$tmp_jour][$tmp_heure])) {
						$tab_cellules[$tmp_jour][$tmp_heure] = "";
					} else {
						$tab_cellules[$tmp_jour][$tmp_heure] .= " - ";
					}

					if ($_SESSION["statut"] == "professeur") {
						if (in_array($lig2->id_classe, $tab_classe_pp['id_classe'])) {
							if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour saisir/consulter l'avis du conseil de classe,\npour saisir vos notes et appréciations,\npour consulter les graphes, les bulletins,...\">";
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<a href='$gepiPath/saisie/saisie_avis1.php?id_classe=$lig2->id_classe' style='color:red' onclick=\"afficher_action_classe_$id_ev($lig2->id_classe);return false;\">";
							} else {
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour saisir l'avis du conseil de classe,\npour saisir vos notes et appréciations,\npour consulter les graphes, les bulletins,...\">";
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<a href='$gepiPath/saisie/saisie_avis1.php?id_classe=$lig2->id_classe' style='color:black' onclick=\"afficher_action_classe_$id_ev($lig2->id_classe);return false;\">";
							}
							$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
							$tab_cellules[$tmp_jour][$tmp_heure] .= "</a>";
							$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";

							if (getSettingAOui('active_mod_engagements')) {
								$tab_cellules[$tmp_jour][$tmp_heure] .= " <a href=\"$gepiPath/mod_engagements/imprimer_documents.php\" title=\"Imprimer les documents pour les délégués\"><img src='$gepiPath/images/icons/odt.png' class='icone16' alt='Document' /></a>";
							}

						} else {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<a href='#' style='color:black' onclick=\"afficher_action_classe_$id_ev($lig2->id_classe);return false;\">";
							if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour consulter vos notes et appréciations, les graphes, les bulletins,...\">";
							} else {
								$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour saisir vos notes et appréciations, consulter les graphes, les bulletins,...\">";
							}
							// Problème: Un prof peut avoir plusieurs groupes dans une classe
							//$tab_cellules[$tmp_jour][$tmp_heure].="<a href='$gepiPath/saisie/saisie_appreciations.php?id_groupe=' style='color:black'>";
							$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
							$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";
							$tab_cellules[$tmp_jour][$tmp_heure] .= "</a>";
						}
					} elseif ($_SESSION["statut"] == "scolarite") {
						if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour saisir/consulter l'avis du conseil de classe,\n pour accéder aux bulletins, aux graphes,...\">";
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<a href='$gepiPath/saisie/saisie_avis1.php?id_classe=$lig2->id_classe' style='color:red' onclick=\"afficher_action_classe_$id_ev($lig2->id_classe);return false;\">";
						} else {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "

Cliquer pour saisir l'avis du conseil de classe,\n pour accéder aux bulletins, aux graphes,...\">";
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<a href='$gepiPath/saisie/saisie_avis1.php?id_classe=$lig2->id_classe' style='color:black' onclick=\"afficher_action_classe_$id_ev($lig2->id_classe);return false;\">";
						}
						$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
						$tab_cellules[$tmp_jour][$tmp_heure] .= "</a>";
						$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";
					} elseif ($_SESSION["statut"] == "cpe") {
						if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						} else {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						}
						$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
						$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";
					} elseif ($_SESSION["statut"] == "administrateur") {
						if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						} else {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						}
						$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
						$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";
					} elseif (($_SESSION["statut"] == "responsable") || ($_SESSION["statut"] == "eleve")) {
						if ($lig2->date_evenement < strftime("%Y-%m-%d %H:%M:%S")) {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span style='color:red' title=\"La date du conseil de classe de $lig2->classe est passée : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						} else {
							$tab_cellules[$tmp_jour][$tmp_heure] .= "<span title=\"Date du conseil de classe de $lig2->classe : " . formate_date($lig2->date_evenement, "y") . "
" . ucfirst(retourne_denomination_pp($lig2->id_classe)) . " : " . $liste_pp . $indication_salle . "\">";
						}
						$tab_cellules[$tmp_jour][$tmp_heure] .= $lig2->classe;
						$tab_cellules[$tmp_jour][$tmp_heure] .= "</span>";

						if ((getSettingAOui('active_mod_engagements')) && (is_delegue_conseil_classe($_SESSION['login'], $lig2->id_classe))) {
							$tab_cellules[$tmp_jour][$tmp_heure] .= " <a href=\"$gepiPath/mod_engagements/imprimer_documents.php?id_classe=" . $lig2->id_classe . "&imprimer=liste_eleves\" title=\"Imprimer la liste des élèves pour prendre des notes pendant le conseil de classe\" target='_blank'><img src='$gepiPath/images/icons/tableau.png' class='icone16' alt='Document' /></a>";

							$tab_cellules[$tmp_jour][$tmp_heure] .= " <a href=\"$gepiPath/mod_engagements/imprimer_documents.php?id_classe=" . $lig2->id_classe . "&imprimer=convocation\" title=\"Imprimer la convocation pour le conseil de classe\" target='_blank'><img src='$gepiPath/images/icons/saisie.png' class='icone16' alt='Document' /></a>";
						}

					}
					//}
				}

				$retour .= "<table class='boireaus boireaus_alt' summary='Dates de conseils de classe'>
	<thead>
		<tr>
			<th>";


				if (in_array("professeur", $tab_u)) {
					$retour .= " <img src='$gepiPath/images/icons/prof.png' class='icone16' alt='Prof' title=\"Professeurs de la classe.\" />";
				}
				if (in_array("cpe", $tab_u)) {
					$retour .= " <img src='$gepiPath/images/icons/cpe.png' class='icone16' alt='Cpe' title=\"CPE de la classe.\" />";
				}
				if (in_array("scolarite", $tab_u)) {
					$retour .= " <img src='$gepiPath/images/icons/scolarite.png' class='icone16' alt='Scol' title=\"Comptes scolarité associés à la classe.\" />";
				}
				if (in_array("responsable", $tab_u)) {
					$retour .= " <img src='$gepiPath/images/icons/responsable.png' class='icone16' alt='Resp' title=\"Comptes responsables associés à la classe.\" />";
				}
				if (in_array("eleve", $tab_u)) {
					$retour .= " <img src='$gepiPath/images/icons/eleve.png' class='icone16' alt='Resp' title=\"Élèves de la classe.\" />";
				}
				//$retour.="<br />";

				$retour .= "</th>";
				for ($j = 0; $j < count($tab_jours); $j++) {
					$retour .= "
			<th>" . $tab_jours[$j] . "</th>";
				}
				$retour .= "
	</thead>
	<tbody>";

				for ($i = 0; $i < count($tab_heures); $i++) {
					$retour .= "
		<tr>
			<th>" . $tab_heures[$i] . "</th>";

					for ($j = 0; $j < count($tab_jours); $j++) {
						$retour .= "
			<td>";
						if (isset($tab_cellules[$tab_jours[$j]][$tab_heures[$i]])) {
							$retour .= $tab_cellules[$tab_jours[$j]][$tab_heures[$i]];
						}
						$retour .= "</td>";
					}
					$retour .= "
		</tr>";

				}
				$retour .= "
	</tbody>
</table>";

			}
			if (($_SESSION['statut'] != 'eleve') && ($_SESSION['statut'] != 'responsable')) {
				$retour .= $lig->texte_apres;
			} else {
				$retour .= $lig->texte_apres_ele_resp;
			}

		}
	}

	return $retour;
}

function affiche_details_evenement($id_ev, $afficher_obsolete = "n") {
	global $gepiPath;
	global $tab_salle;
	global $evenement_sans_lien_mail;
	global $evenement_sans_lien_ics;
	$retour = "";

	$sql = "SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) == 0) {
		$retour = "<p style='color:red'>L'événement n°$id_ev est inconnu.</p>";
	} else {
		$tab_u = array();
		$sql = "SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$id_ev';";
		//echo "$sql<br />";
		$res_u = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_u) > 0) {
			while ($lig_u = mysqli_fetch_object($res_u)) {
				$tab_u[] = $lig_u->statut;
			}
		}

		$liste_dest = "";
		if (in_array("professeur", $tab_u)) {
			$liste_dest .= " <img src='$gepiPath/images/icons/prof.png' class='icone16' alt='Prof' title=\"Professeurs de la classe.\" />";
		}
		if (in_array("cpe", $tab_u)) {
			$liste_dest .= " <img src='$gepiPath/images/icons/cpe.png' class='icone16' alt='Cpe' title=\"CPE de la classe.\" />";
		}
		if (in_array("scolarite", $tab_u)) {
			$liste_dest .= " <img src='$gepiPath/images/icons/scolarite.png' class='icone16' alt='Scol' title=\"Comptes scolarité associés à la classe.\" />";
		}
		if (in_array("responsable", $tab_u)) {
			$liste_dest .= " <img src='$gepiPath/images/icons/responsable.png' class='icone16' alt='Resp' title=\"Comptes responsables associés à la classe.\" />";
		}
		if (in_array("eleve", $tab_u)) {
			$liste_dest .= " <img src='$gepiPath/images/icons/eleve.png' class='icone16' alt='Resp' title=\"Élèves associés à la classe.\" />";
		}

		$lig = mysqli_fetch_object($res);

		$retour = "<p class='bold'>Événement de type " . $lig->type . " n°$id_ev visible à compter du " . formate_date($lig->date_debut) . "<br />
Statuts concernés&nbsp;: $liste_dest</p>
<div class='fieldset_opacite50' style='padding:0.5em; margin:0.5em;'>
	" . affiche_evenement($id_ev, $afficher_obsolete) . "
</div>";
	}
	return $retour;
}

function get_tab_infos_evenement($id_ev) {
	$tab = array();

	$sql = "SELECT * FROM d_dates_evenements WHERE id_ev='$id_ev';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		// Modification des valeurs
		$titre_mess = "Modification de l'événement n°" . $id_ev;
		$obj_ev = mysqli_fetch_object($res);

		$tab['type'] = $obj_ev->type;
		$tab['periode'] = $obj_ev->periode;
		$tab['date_debut'] = $obj_ev->date_debut;
		$tab['texte_avant'] = $obj_ev->texte_avant;
		$tab['texte_apres'] = $obj_ev->texte_apres;
		$tab['texte_apres_ele_resp'] = $obj_ev->texte_apres_ele_resp;

		//$tab_u=array();
		$sql = "SELECT * FROM d_dates_evenements_utilisateurs WHERE id_ev='$obj_ev->id_ev';";
		$res_u = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_u) > 0) {
			while ($lig_u = mysqli_fetch_object($res_u)) {
				//$tab_u[]=$lig_u->statut;

				$tab['statuts'][] = $lig_u->statut;
			}
		}

		$sql = "SELECT * FROM d_dates_evenements_classes WHERE id_ev='$obj_ev->id_ev' ORDER BY date_evenement;";
		$res_c = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_c) > 0) {
			$tab['classes'] = array();
			$tab['evenement'] = array();
			$tab['date_evenement'] = array();
			$tab['id_salle'] = array();
			while ($lig_c = mysqli_fetch_object($res_c)) {
				if (!in_array($lig_c->id_classe, $tab['classes'])) {
					$tab['classes'][] = $lig_c->id_classe;
				}

				if (!in_array($lig_c->date_evenement, $tab['date_evenement'])) {
					$tab['date_evenement'][] = $lig_c->date_evenement;
				}

				if (!in_array($lig_c->id_salle, $tab['id_salle'])) {
					$tab['id_salle'][] = $lig_c->id_salle;
				}

				//$tab['evenement'][]=$lig_c->;
				$tab['evenement'][$lig_c->id_ev_classe]['id_classe'] = $lig_c->id_classe;
				$tab['evenement'][$lig_c->id_ev_classe]['id_salle'] = $lig_c->id_salle;
				$tab['evenement'][$lig_c->id_ev_classe]['date_evenement'] = $lig_c->date_evenement;
			}
		}

	}

	return $tab;
}

//function get_tab_date_prochain_evenement_telle_classe($id_classe, $type, $tab_visible_par=array("all")) {
function get_tab_date_prochain_evenement_telle_classe($id_classe, $type, $avec_visible_par_statut = "n", $mes_classes_seulement = "n") {
	$tab = array();

	// $mes_classes_seulement implémenté seulement pour les profs pour le moment
	//                        Sans intérêt si $id_classe!=""

	/*
	$sql_ajout="";
	if(!in_array("all", $tab_visible_par)) {

	}
	*/

	if ($id_classe != "") {
		$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND ddec.id_classe='" . $id_classe . "' AND dde.type='" . $type . "' AND c.id=ddec.id_classe ORDER BY date_evenement LIMIT 1;";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab = $lig;
				$tab['slashdate_ev'] = formate_date($lig['date_evenement']);
				$tab['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
				$tab['lieu'] = get_infos_salle_cours($lig['id_salle']);
				$tab['periode'] = get_infos_salle_cours($lig['periode']);
				if ($avec_visible_par_statut == "y") {
					$tab['statuts'] = array();
					$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
					$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($res2) > 0) {
						while ($lig2 = mysqli_fetch_assoc($res2)) {
							$tab['statuts'][] = $lig2['statut'];
						}
					}
				}
			}
		}
	} else {
		$sql_ajout_mes_classes = "";
		if ($mes_classes_seulement == "y") {
			$sql_ajout_mes_classes = " AND ddec.id_classe IN (SELECT DISTINCT id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ";
		}

		$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND dde.type='" . $type . "' AND c.id=ddec.id_classe " . $sql_ajout_mes_classes . " ORDER BY date_evenement;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_assoc($res)) {
				// Pour ne récupérer que le prochain conseil de classe de chaque classe.
				if (!isset($tab[$lig['id_classe']])) {
					$tab[$lig['id_classe']] = $lig;
					$tab[$lig['id_classe']]['slashdate_ev'] = formate_date($lig['date_evenement']);
					$tab[$lig['id_classe']]['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
					$tab[$lig['id_classe']]['lieu'] = get_infos_salle_cours($lig['id_salle']);
					$tab[$lig['id_classe']]['periode'] = get_infos_salle_cours($lig['periode']);
					if ($avec_visible_par_statut == "y") {
						$tab[$lig['id_classe']]['statuts'] = array();
						$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
						$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
						if (mysqli_num_rows($res2) > 0) {
							while ($lig2 = mysqli_fetch_assoc($res2)) {
								$tab[$lig['id_classe']]['statuts'][] = $lig2['statut'];
							}
						}
					}
				}
			}
		}
	}
	return $tab;
}

function get_tab_dates_evenements_classes($id_classe, $type, $avec_visible_par_statut = "n", $mes_classes_seulement = "n", $indice = "") {
	$tab = array();

	// $mes_classes_seulement implémenté seulement pour les profs pour le moment
	//                        Sans intérêt si $id_classe!=""

	/*
	$sql_ajout="";
	if(!in_array("all", $tab_visible_par)) {

	}
	*/

	if ($indice == "date_ev") {
		if ($id_classe != "") {
			$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND ddec.id_classe='" . $id_classe . "' AND dde.type='" . $type . "' AND c.id=ddec.id_classe ORDER BY date_evenement LIMIT 1;";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$date_ev = formate_date($lig['date_evenement']);
					$tab[$date_ev] = $lig;
					$tab[$date_ev]['slashdate_ev'] = formate_date($lig['date_evenement']);
					$tab[$date_ev]['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
					$tab[$date_ev]['lieu'] = get_infos_salle_cours($lig['id_salle']);
					$tab[$date_ev]['periode'] = get_infos_salle_cours($lig['periode']);
					if ($avec_visible_par_statut == "y") {
						$tab[$date_ev]['statuts'] = array();
						$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
						$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
						if (mysqli_num_rows($res2) > 0) {
							while ($lig2 = mysqli_fetch_assoc($res2)) {
								$tab[$date_ev]['statuts'][] = $lig2['statut'];
							}
						}
					}
				}
			}
		} else {
			$sql_ajout_mes_classes = "";
			if ($mes_classes_seulement == "y") {
				// 20171122
				if ($_SESSION["statut"] == "eleve") {
					$sql_ajout_mes_classes = " AND ddec.id_classe IN (SELECT DISTINCT id_classe FROM j_eleves_classes jec WHERE jec.login='" . $_SESSION['login'] . "') ";
				} elseif ($_SESSION["statut"] == "responsable") {
					$sql_ajout_mes_classes = " AND ddec.id_classe IN (SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec,
																	eleves e, 
																	responsables2 r, 
																	resp_pers rp 
																WHERE jec.login=e.login AND 
																	e.ele_id=r.ele_id AND 
																	r.pers_id=rp.pers_id AND 
																	rp.login='" . $_SESSION['login'] . "') ";
				} else {
					$sql_ajout_mes_classes = " AND ddec.id_classe IN (SELECT DISTINCT id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ";
				}
			}

			$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND dde.type='" . $type . "' AND c.id=ddec.id_classe " . $sql_ajout_mes_classes . " ORDER BY date_evenement;";
			//echo "$sql<br />";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					// Pour ne récupérer que le prochain conseil de classe de chaque classe.
					if (!isset($tab[$lig['id_classe']])) {
						$date_ev = formate_date($lig['date_evenement']);
						$tab[$date_ev][$lig['id_classe']] = $lig;
						$tab[$date_ev][$lig['id_classe']]['slashdate_ev'] = formate_date($lig['date_evenement']);
						$tab[$date_ev][$lig['id_classe']]['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
						$tab[$date_ev][$lig['id_classe']]['lieu'] = get_infos_salle_cours($lig['id_salle']);
						$tab[$date_ev][$lig['id_classe']]['periode'] = get_infos_salle_cours($lig['periode']);
						if ($avec_visible_par_statut == "y") {
							$tab[$date_ev][$lig['id_classe']]['statuts'] = array();
							$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
							$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
							if (mysqli_num_rows($res2) > 0) {
								while ($lig2 = mysqli_fetch_assoc($res2)) {
									$tab[$date_ev][$lig['id_classe']]['statuts'][] = $lig2['statut'];
								}
							}
						}
					}
				}
			}
		}
	} else {
		if ($id_classe != "") {
			$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND ddec.id_classe='" . $id_classe . "' AND dde.type='" . $type . "' AND c.id=ddec.id_classe ORDER BY date_evenement LIMIT 1;";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab = $lig;
					$tab['slashdate_ev'] = formate_date($lig['date_evenement']);
					$tab['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
					$tab['lieu'] = get_infos_salle_cours($lig['id_salle']);
					$tab['periode'] = get_infos_salle_cours($lig['periode']);
					if ($avec_visible_par_statut == "y") {
						$tab['statuts'] = array();
						$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
						$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
						if (mysqli_num_rows($res2) > 0) {
							while ($lig2 = mysqli_fetch_assoc($res2)) {
								$tab['statuts'][] = $lig2['statut'];
							}
						}
					}
				}
			}
		} else {
			$sql_ajout_mes_classes = "";
			if ($mes_classes_seulement == "y") {
				$sql_ajout_mes_classes = " AND ddec.id_classe IN (SELECT DISTINCT id_classe FROM j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "') ";
			}

			$sql = "SELECT DISTINCT dde.id_ev, dde.date_debut, dde.periode, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement>=NOW() AND dde.type='" . $type . "' AND c.id=ddec.id_classe " . $sql_ajout_mes_classes . " ORDER BY date_evenement;";
			//echo "$sql<br />";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					// Pour ne récupérer que le prochain conseil de classe de chaque classe.
					if (!isset($tab[$lig['id_classe']])) {
						$tab[$lig['id_classe']] = $lig;
						$tab[$lig['id_classe']]['slashdate_ev'] = formate_date($lig['date_evenement']);
						$tab[$lig['id_classe']]['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
						$tab[$lig['id_classe']]['lieu'] = get_infos_salle_cours($lig['id_salle']);
						$tab[$lig['id_classe']]['periode'] = get_infos_salle_cours($lig['periode']);
						if ($avec_visible_par_statut == "y") {
							$tab[$lig['id_classe']]['statuts'] = array();
							$sql = "SELECT DISTINCT statut FROM d_dates_evenements_utilisateurs WHERE id_ev='" . $lig['id_ev'] . "';";
							$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
							if (mysqli_num_rows($res2) > 0) {
								while ($lig2 = mysqli_fetch_assoc($res2)) {
									$tab[$lig['id_classe']]['statuts'][] = $lig2['statut'];
								}
							}
						}
					}
				}
			}
		}
	}
	return $tab;
}

function get_infos_salle_cours($id_salle) {
	$tab = array();

	$sql = "SELECT * FROM salle_cours WHERE id_salle='$id_salle';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab['id_salle'] = $lig->id_salle;
		$tab['numero_salle'] = $lig->numero_salle;
		$tab['nom_salle'] = $lig->nom_salle;

		$designation_courte = '';
		$designation_complete = '';
		if ($lig->numero_salle != "") {
			$designation_courte = $lig->numero_salle;
			$designation_complete = $lig->numero_salle;
		} else {
			$designation_courte = $lig->nom_salle;
		}

		if ($lig->nom_salle != "") {
			if (($lig->numero_salle != "") && (strpos($lig->nom_salle, $lig->numero_salle) === false)) {
				$designation_complete .= " (" . $lig->nom_salle . ")";
			} else {
				$designation_complete = $lig->nom_salle;
			}
		}

		$tab['designation_courte'] = $designation_courte;
		$tab['designation_complete'] = $designation_complete;
	}

	return $tab;
}

function get_info_id_definie_periode($id_definie_periode) {
	$tab = array();

	$sql = "SELECT * FROM edt_creneaux WHERE id_definie_periode='$id_definie_periode';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab['id_definie_periode'] = $lig->id_definie_periode;
		$tab['nom_definie_periode'] = $lig->nom_definie_periode;

		$tab['heuredebut_definie_periode'] = $lig->heuredebut_definie_periode;
		$tab['heurefin_definie_periode'] = $lig->heurefin_definie_periode;

		$tab['type_creneaux'] = $lig->type_creneaux;
	}

	return $tab;
}

function get_info_id_cours($id_cours) {
	$retour = "";
	$sql = "SELECT * FROM edt_cours WHERE id_cours='$id_cours';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$tab_h = get_info_id_definie_periode($lig->id_definie_periode);
		$retour = $lig->jour_semaine . " en " . $tab_h['nom_definie_periode'];
		if ($lig->id_semaine != "0") {
			$retour .= " (semaine $lig->id_semaine)";
		}
		$retour .= " ";
		$retour .= get_info_grp($lig->id_groupe);
	}
	return $retour;
}

function get_tab_id_cours($id_cours) {
	$tab = array();

	$sql = "SELECT * FROM edt_cours WHERE id_cours='$id_cours';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$tab_h = get_info_id_definie_periode($lig->id_definie_periode);

		$tab['jour'] = $lig->jour_semaine;
		$tab['id_definie_periode'] = $lig->id_definie_periode;
		$tab['nom_definie_periode'] = $tab_h['nom_definie_periode'];
		$tab['id_semaine'] = $lig->id_semaine;
		$tab['id_groupe'] = $lig->id_groupe;
		$tab['id_aid'] = $lig->id_aid;
	}
	return $tab;
}

function get_type_semaine($num_semaine) {
	$sql = "SELECT type_edt_semaine FROM edt_semaines WHERE num_edt_semaine='$num_semaine';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		return $lig->type_edt_semaine;
	} else {
		return "";
	}
}

function formate_info_id_definie_periode($id_definie_periode) {
	$retour = "";

	$tab = get_info_id_definie_periode($id_definie_periode);
	if (isset($tab['nom_definie_periode'])) {
		$retour .= $tab['nom_definie_periode'];
		$retour .= " (" . $tab['heuredebut_definie_periode'];
		$retour .= " - " . $tab['heurefin_definie_periode'] . ")";
	}
	return $retour;
}

function acces_edt_prof() {
	if (in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe'))) {
		return true;
	} elseif (($_SESSION['statut'] == "professeur") && (getSettingAOui('AccesProf_EdtProfs'))) {
		return true;
	} else {
		return false;
	}
}

function acces_edt_classe() {
	if (in_array($_SESSION['statut'], array('administrateur', 'scolarite', 'cpe', 'professeur'))) {
		return true;
	} else {
		return false;
	}
}

function get_tab_type_avertissement() {
	$tab_type_avertissement_fin_periode = array();

	$sql = "SELECT * FROM s_types_avertissements ORDER BY nom_court, nom_complet;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			$tab_type_avertissement_fin_periode['cpt'][$cpt]['id_type_avertissement'] = $lig->id_type_avertissement;
			$tab_type_avertissement_fin_periode['cpt'][$cpt]['nom_court'] = $lig->nom_court;
			$tab_type_avertissement_fin_periode['cpt'][$cpt]['nom_complet'] = $lig->nom_complet;
			$tab_type_avertissement_fin_periode['cpt'][$cpt]['description'] = $lig->description;

			$tab_type_avertissement_fin_periode['id_type_avertissement'][$lig->id_type_avertissement]['nom_court'] = $lig->nom_court;
			$tab_type_avertissement_fin_periode['id_type_avertissement'][$lig->id_type_avertissement]['nom_complet'] = $lig->nom_complet;
			$tab_type_avertissement_fin_periode['id_type_avertissement'][$lig->id_type_avertissement]['description'] = $lig->description;

			$sql = "SELECT 1=1 FROM s_avertissements WHERE id_type_avertissement='" . $lig->id_type_avertissement . "';";
			$test = mysqli_query($GLOBALS["mysqli"], $sql);
			$tab_type_avertissement_fin_periode['cpt'][$cpt]['effectif'] = mysqli_num_rows($test);
			$tab_type_avertissement_fin_periode['id_type_avertissement'][$lig->id_type_avertissement]['effectif'] = mysqli_num_rows($test);

			$cpt++;
		}
	}

	return $tab_type_avertissement_fin_periode;
}

function affiche_tab_type_avertissement() {
	global $mod_disc_terme_avertissement_fin_periode;

	$retour = "";

	$tab_type_avertissement_fin_periode = get_tab_type_avertissement();
	if (!isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {
		$retour .= "
<p style='color:red'>Aucun type d'$mod_disc_terme_avertissement_fin_periode n'est encore défini.</p>";
	} else {
		$retour .= "
<p>Liste des " . $mod_disc_terme_avertissement_fin_periode . "s définis&nbsp;:</p>
<table class='boireaus boireaus_alt' summary='Tableau des " . $mod_disc_terme_avertissement_fin_periode . "s définis'>
	<tr>
		<th>Identifiant</th>
		<th>Nom court</th>
		<th>" . ucfirst($mod_disc_terme_avertissement_fin_periode) . "</th>
	</tr>";

		foreach ($tab_type_avertissement_fin_periode['id_type_avertissement'] as $key => $value) {
			$retour .= "
	<tr>
		<td><label for='suppr_$key'>" . $key . "</label></td>
		<td><label for='suppr_$key'>" . $value['nom_court'] . "</label></td>
		<td><label for='suppr_$key'>" . $value['nom_complet'] . "</label></td>
	</tr>";
		}
		$retour .= "
</table>";
	}

	return $retour;
}

function get_tab_avertissement($login_ele, $periode = "", $s_periode = "") {
	$tab = array();

	$sql = "SELECT * FROM s_avertissements WHERE login_ele='$login_ele' ";
	if (($periode != "") && (preg_match("/^[0-9]{1,}$/", $periode))) {
		$sql .= "AND periode='$periode' ";
	}
	if (($s_periode == "y") || ($s_periode == "n")) {
		$sql .= "AND s_periode='$s_periode' ";
	}
	$sql .= "ORDER BY date_avertissement;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			$tab['periode'][$lig->periode][$lig->s_periode][$cpt]['id_avertissement'] = $lig->id_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode][$cpt]['id_type_avertissement'] = $lig->id_type_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode][$cpt]['declarant'] = $lig->declarant;
			$tab['periode'][$lig->periode][$lig->s_periode][$cpt]['date_avertissement'] = $lig->date_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode][$cpt]['commentaire'] = $lig->commentaire;
			$tab['id_type_avertissement'][$lig->periode][$lig->s_periode][] = $lig->id_type_avertissement;
			$cpt++;
		}
	}

	return $tab;
}

function get_tab_avertissement_classe($id_classe, $periode = "", $s_periode = "") {
	$tab = array();

	$sql = "SELECT jec.login, sa.* FROM s_avertissements sa, j_eleves_classes jec WHERE jec.id_classe='$id_classe' AND jec.login=sa.login_ele ";
	if (($periode != "") && (preg_match("/^[0-9]{1,}$/", $periode))) {
		$sql .= "AND jec.periode='$periode' AND sa.periode=jec.periode ";
	}
	if (($s_periode == "y") || ($s_periode == "n")) {
		$sql .= "AND s_periode='$s_periode' ";
	}
	$sql .= "ORDER BY date_avertissement;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		//$cpt=0;
		while ($lig = mysqli_fetch_object($res)) {
			$cpt = 0;
			if (isset($tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login])) {
				$cpt = count($tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login]);
			}
			$tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login][$cpt]['id_avertissement'] = $lig->id_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login][$cpt]['id_type_avertissement'] = $lig->id_type_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login][$cpt]['declarant'] = $lig->declarant;
			$tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login][$cpt]['date_avertissement'] = $lig->date_avertissement;
			$tab['periode'][$lig->periode][$lig->s_periode]['login'][$lig->login][$cpt]['commentaire'] = $lig->commentaire;

			$cpt = 0;
			if (isset($tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode])) {
				$cpt = count($tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode]);
			}
			$tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode][$cpt]['id_avertissement'] = $lig->id_avertissement;
			$tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode][$cpt]['id_type_avertissement'] = $lig->id_type_avertissement;
			$tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode][$cpt]['declarant'] = $lig->declarant;
			$tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode][$cpt]['date_avertissement'] = $lig->date_avertissement;
			$tab['login'][$lig->login]['periode'][$lig->periode][$lig->s_periode][$cpt]['commentaire'] = $lig->commentaire;

			if ((!isset($tab['id_type_avertissement'][$lig->periode][$lig->s_periode])) || (!in_array($lig->id_type_avertissement, $tab['id_type_avertissement'][$lig->periode][$lig->s_periode]))) {
				$tab['id_type_avertissement'][$lig->periode][$lig->s_periode][] = $lig->id_type_avertissement;
			}
			//$cpt++;
		}
	}

	return $tab;
}

function liste_avertissements_fin_periode($login_ele, $periode, $mode = "nom_complet", $html = "y", $s_periode = "n") {
	global $tab_type_avertissement_fin_periode;
	global $mod_disc_terme_avertissement_fin_periode;
	global $tab_totaux_avertissement_fin_periode;

	$tab = get_tab_avertissement($login_ele, $periode, $s_periode);
	/*
	echo "<p>get_tab_avertissement($login_ele, $periode)<pre>";
	print_r($tab);
	echo "</pre>";
	*/

	if (!is_array($tab_type_avertissement_fin_periode)) {
		$tab_type_avertissement_fin_periode = get_tab_type_avertissement();
	}

	$retour = "";

	if (isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {
		if (isset($tab['id_type_avertissement'][$periode][$s_periode])) {
			for ($loop = 0; $loop < count($tab['id_type_avertissement'][$periode][$s_periode]); $loop++) {
				if ($loop > 0) {
					$retour .= ", ";
				}
				if ($mode == "nom_court") {
					if ($html == "y") {
						$retour .= "<span title=\"" . $tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]['nom_complet'] . "\">" . $tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]['nom_court'] . "</span>";
					} else {
						$retour .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]['nom_court'];
					}
				} else {
					//$retour.="<span style='color:red'>\$tab['id_type_avertissement'][$periode][$loop]=".$tab['id_type_avertissement'][$periode][$loop]."</span>";
					$retour .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]['nom_complet'];
				}

				if (!isset($tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]])) {
					$tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]] = 1;
				} else {
					$tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]++;
				}

				if (!isset($tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$s_periode][$loop]])) {
					$tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$s_periode][$loop]] = 1;
				} else {
					$tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$s_periode][$loop]]++;
				}
			}
		}
	}

	return $retour;
}

/*
function liste_avertissements_fin_periode($login_ele, $periode, $mode="nom_complet", $html="y", $s_periode="") {
	global $tab_type_avertissement_fin_periode;
	global $mod_disc_terme_avertissement_fin_periode;
	global $tab_totaux_avertissement_fin_periode;

	$tab=get_tab_avertissement($login_ele, $periode, $s_periode);

	if(!is_array($tab_type_avertissement_fin_periode)) {
		$tab_type_avertissement_fin_periode=get_tab_type_avertissement();
	}

	$retour="";

	if(isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {

		if(isset($tab['id_type_avertissement'][$periode])) {
			$tab_s_periode=array();
			if($s_periode!="") {
				$tab_s_periode[]=$s_periode;
			}
			else {
				// On va parcourir les demi-périodes avant les fins de périodes
				$tab_s_periode[]="y";
				$tab_s_periode[]="n";
			}

			for($loop_s=0;$loop_s<count($tab_s_periode);$loop_s++) {
				if(isset($tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]])) {
					if($retour!="") {$retour.="<br />";}
					if($tab_s_periode[$loop_s]]=="y") {
						$retour.="<strong>Mi-période&nbsp;:</strong> ";
					}
					else {
						$retour.="<strong>Fin de période&nbsp;:</strong> ";
					}
					for($loop=0;$loop<count($tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]]);$loop++) {
						if($loop>0) {$retour.=", ";}
						if($mode=="nom_court") {
							if($html=="y") {
								$retour.="<span title=\"".$tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]['nom_complet']."\">".$tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]['nom_court']."</span>";
							}
							else {
								$retour.=$tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]['nom_court'];
							}
						}
						else {
							//$retour.="<span style='color:red'>\$tab['id_type_avertissement'][$periode][$loop]=".$tab['id_type_avertissement'][$periode][$loop]."</span>";
							$retour.=$tab_type_avertissement_fin_periode['id_type_avertissement'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]['nom_complet'];
						}

						if(!isset($tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]])) {
							$tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]=1;
						}
						else {
							$tab_totaux_avertissement_fin_periode['periodes']['toutes'][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]++;
						}

						if(!isset($tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]])) {
							$tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]=1;
						}
						else {
							$tab_totaux_avertissement_fin_periode['periodes'][$periode][$tab['id_type_avertissement'][$periode][$tab_s_periode[$loop_s]][$loop]]++;
						}
					}
				}
			}
		}
	}

	return $retour;
}
*/
function liste_avertissements_fin_periode_classe($id_classe, $periode, $mode = "nom_complet", $html = "y", $s_periode = "n") {
	global $tab_type_avertissement_fin_periode;
	global $mod_disc_terme_avertissement_fin_periode;

	$tab_retour = array();

	$tab = get_tab_avertissement_classe($id_classe, $periode, $s_periode);

	if (!is_array($tab_type_avertissement_fin_periode)) {
		$tab_type_avertissement_fin_periode = get_tab_type_avertissement();
	}

	//$tab_ele=array();

	if (isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {
		if (isset($tab['id_type_avertissement'][$periode][$s_periode])) {

			foreach ($tab['periode'][$periode][$s_periode]['login'] as $current_login => $tab_avt_ele) {
				//echo "\$current_login=".$current_login."<br />";
				for ($loop = 0; $loop < count($tab_avt_ele); $loop++) {
					if (isset($tab_retour[$current_login])) {
						$tab_retour[$current_login] .= ", ";
					} else {
						$tab_retour[$current_login] = "";
					}

					$id_type_avertissement_courant = $tab_avt_ele[$loop]['id_type_avertissement'];
					//echo "\$tab_avt_ele[$loop]=".$id_type_avertissement_courant."<br />";

					if ($mode == "nom_court") {
						if ($html == "y") {
							$tab_retour[$current_login] .= "<span title=\"" . $tab_type_avertissement_fin_periode['id_type_avertissement'][$id_type_avertissement_courant]['nom_complet'] . "\">" . $tab_type_avertissement_fin_periode['id_type_avertissement'][$id_type_avertissement_courant]['nom_court'] . "</span>";
						} else {
							$tab_retour[$current_login] .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$id_type_avertissement_courant]['nom_court'];
						}
					} else {
						$tab_retour[$current_login] .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$id_type_avertissement_courant]['nom_complet'];
					}
				}
			}
		}
	}

	return $tab_retour;
}

function champs_checkbox_avertissements_fin_periode($login_ele, $periode, $s_periode = "n") {
	global $tab_type_avertissement_fin_periode;
	global $mod_disc_terme_avertissement_fin_periode;

	if ($login_ele != "") {
		$tab = get_tab_avertissement($login_ele, $periode, $s_periode);
	} else {
		$tab = array();
	}

	if (!is_array($tab_type_avertissement_fin_periode)) {
		$tab_type_avertissement_fin_periode = get_tab_type_avertissement();
	}

	if (!isset($tab_type_avertissement_fin_periode['id_type_avertissement'])) {
		$retour = "<span style='color:red'>Aucun type d'$mod_disc_terme_avertissement_fin_periode n'est encore défini.</span>";
	} else {
		$retour = "<table class='boireaus boireaus_alt' summary=\"Tableau des $mod_disc_terme_avertissement_fin_periode\">";
		foreach ($tab_type_avertissement_fin_periode['id_type_avertissement'] as $key => $value) {
			$retour .= "
	<tr>
		<td><input type='checkbox' id='id_type_avertissement_$key' name='id_type_avertissement[]' value='$key' onchange=\"checkbox_change('id_type_avertissement_$key'); changement();\" ";
			if ((isset($tab['id_type_avertissement'][$periode][$s_periode])) && (in_array($key, $tab['id_type_avertissement'][$periode][$s_periode]))) {
				$retour .= "checked ";
			}
			$retour .= "/></td>
		<td><label for='id_type_avertissement_$key' id='texte_id_type_avertissement_$key'";
			if ((isset($tab['id_type_avertissement'][$periode][$s_periode])) && (in_array($key, $tab['id_type_avertissement'][$periode][$s_periode]))) {
				$retour .= " style='font-weight:bold;'";
			}
			$retour .= ">" . $value['nom_complet'] . "</label></td>
	</tr>";
		}
		$retour .= "
</table>";
	}

	return $retour;
}

/** Fonction destinée tester si l'utilisateur courant est autorisé à accéder à la saisie d'avertissement de fin de période pour l'élève choisi
 *
 * @param string $login_ele identifiant de l'élève
 *
 * @return boolean Accès ou non
 */
function acces_saisie_avertissement_fin_periode($login_ele) {

	if (getSettingValue('mod_disc_acces_avertissements') == "n") {
		return false;
	} else {
		if ($_SESSION['statut'] == 'professeur') {
			if ((getSettingAOui('saisieDiscProfPAvt')) && (is_pp($_SESSION['login'], "", $login_ele))) {
				return true;
			}
		} elseif ($_SESSION['statut'] == 'scolarite') {
			if (getSettingAOui('GepiRubConseilScol')) {
				return true;
			}
		} elseif ($_SESSION['statut'] == 'cpe') {
			if (getSettingAOui('saisieDiscCpeAvtTous')) {
				return true;
			} elseif ((!getSettingAOui('saisieDiscCpeAvt')) && (is_cpe($_SESSION['login'], "", $login_ele))) {
				return true;
			} else {
				return false;
			}
		} elseif ($_SESSION['statut'] == 'secours') {
			return true;
		} elseif ($_SESSION['statut'] == 'administrateur') {
			return true;
		}
	}

	return false;
}

/** Fonction destinée tester si l'utilisateur courant est autorisé à imprimer les avertissements de fin de période pour l'élève choisi
 *
 * @param string $login_ele identifiant de l'élève
 * @param string $id_classe identifiant de la classe
 *
 * @return boolean Accès ou non
 */
function acces_impression_avertissement_fin_periode($login_ele = "", $id_classe = "") {
	if (getSettingValue('mod_disc_acces_avertissements') == "n") {
		return false;
	} else {
		if (acces('/mod_discipline/imprimer_bilan_periode.php', $_SESSION['statut'])) {
			if ($_SESSION['statut'] == 'professeur') {
				if (getSettingAOui('imprDiscProfAvtOOo')) {
					return true;
				} else {
					if ($login_ele != "") {
						if ((getSettingAOui('imprDiscProfPAvtOOo')) && (is_pp($_SESSION['login'], "", $login_ele))) {
							return true;
						}
					} else {
						if ((getSettingAOui('imprDiscProfPAvtOOo')) && (is_pp($_SESSION['login'], $id_classe, ""))) {
							return true;
						}
					}
				}
			} elseif ($_SESSION['statut'] == 'scolarite') {
				if (getSettingAOui('GepiRubConseilScol')) {
					return true;
				}
			} elseif ($_SESSION['statut'] == 'cpe') {
				if (getSettingAOui('imprDiscCpeAvtOOo')) {
					return true;
				} /*
				elseif((!getSettingAOui('GepiRubConseilCpe'))&&(is_cpe($_SESSION['login'], "", $login_ele))) {
					return true;
				}
				*/
				else {
					return false;
				}
			} elseif ($_SESSION['statut'] == 'secours') {
				return true;
			} elseif ($_SESSION['statut'] == 'administrateur') {
				return true;
			}
		}
	}

	return false;
}


// Attention à ne pas coller un appel à cette fonction dans une
// section <form></form> parce qu'on insère aussi un formulaire.
function necessaire_saisie_avertissement_fin_periode() {
	global $mod_disc_terme_avertissement_fin_periode;

	$largeur_infobulle = "400px";

	$sql = "SELECT id_type_avertissement FROM s_types_avertissements;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$chaine_js = "var tab_id_type_avertissement=new Array(";
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			if ($cpt > 0) {
				$chaine_js .= ", ";
			}
			$chaine_js .= $lig->id_type_avertissement;
			$cpt++;
		}
		$chaine_js .= ");";
	}

	if (!isset($chaine_js)) {
		$chaine_js = "var tab_id_type_avertissement=new Array();";
	}

	$retour = "
<script type='text/javascript'>

	function valider_saisie_avertissement_fin_periode() {

		saisie_avertissement_fin_periode_login_ele=document.getElementById('saisie_avertissement_fin_periode_login_ele').value;
		saisie_avertissement_fin_periode_periode=document.getElementById('saisie_avertissement_fin_periode_periode').value;
		saisie_avertissement_fin_periode_s_periode=document.getElementById('saisie_avertissement_fin_periode_s_periode').value;
		saisie_avertissement_fin_periode_id_retour_ajax=document.getElementById('saisie_avertissement_fin_periode_id_retour_ajax').value;

		$chaine_js

		id_type_avertissement='';
		j=0;
		for(i=0;i<tab_id_type_avertissement.length;i++) {
			if(document.getElementById('id_type_avertissement_'+tab_id_type_avertissement[i]).checked==true) {
				if(j>0) {
					id_type_avertissement=id_type_avertissement+'|';
				}

				id_type_avertissement=id_type_avertissement+tab_id_type_avertissement[i];
				j++;
			}
		}

		//alert(id_retour_ajax);

		if(saisie_avertissement_fin_periode_id_retour_ajax=='') {
			alert('Erreur');
		}
		else {

			// Problème avec l'appel depuis une infobulle de saisie d'avis du conseil dans affiche_eleve.php
			//if($(saisie_avertissement_fin_periode_id_retour_ajax)) {
				//alert('Le champ '+saisie_avertissement_fin_periode_id_retour_ajax+' existe/est atteint.');

				new Ajax.Updater($(saisie_avertissement_fin_periode_id_retour_ajax),'../mod_discipline/saisie_avertissement_fin_periode.php?a=a&" . add_token_in_url(false) . "',{method: 'post',
				parameters: {
					login_ele: saisie_avertissement_fin_periode_login_ele,
					periode: saisie_avertissement_fin_periode_periode,
					s_periode: saisie_avertissement_fin_periode_s_periode,
					saisie_avertissement_fin_periode: 'y',
					mode_js: 'y',
					lien_refermer: 'y',
					id_type_avertissement: id_type_avertissement,
				}});
			/*
			}
			else {
				//alert('Le champ '+saisie_avertissement_fin_periode_id_retour_ajax+' n existe pas ou ne peut pas etre atteint.');
				document.getElementById('form_saisie_avertissement_fin_periode').submit();
			}
			*/

			cacher_div('div_saisie_avertissement_fin_periode');
		}
	}

	function afficher_saisie_avertissement_fin_periode(login_ele, periode, s_periode, id_retour_ajax) {
		document.getElementById('saisie_avertissement_fin_periode_id_retour_ajax').value=id_retour_ajax;
		document.getElementById('saisie_avertissement_fin_periode_login_ele').value=login_ele;
		document.getElementById('saisie_avertissement_fin_periode_periode').value=periode;
		document.getElementById('saisie_avertissement_fin_periode_s_periode').value=s_periode;

		precision_s_periode='';
		if(s_periode=='y') {
			precision_s_periode=' (mi-période)';
		}
		document.getElementById('titre_entete_saisie_avertissement_fin_periode').innerHTML='Saisie pour '+login_ele+' en période '+periode+precision_s_periode;

		// 20140616
		new Ajax.Updater($('div_champs_checkbox_avertissements_fin_periode'),'../mod_discipline/saisie_avertissement_fin_periode.php?a=a&" . add_token_in_url(false) . "',{method: 'post',
		parameters: {
			login_ele: login_ele,
			periode: periode,
			s_periode: s_periode,
			get_avertissement_fin_periode: 'y',
			mode_js: 'y',
			lien_refermer: 'y',
		}});

		afficher_div('div_saisie_avertissement_fin_periode','y',100,100);
	}

	" . js_checkbox_change_style('checkbox_change', 'texte_', 'n') . "

</script>

<div id='div_saisie_avertissement_fin_periode' style='position: absolute; top: 220px; right: 20px; width: $largeur_infobulle; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>

	<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: $largeur_infobulle; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_saisie_avertissement_fin_periode')\">
		<div style='color: #ffffff; cursor: move; float:right; width: 16px; margin-right: 1px;'>
			<a href='#' onClick=\"cacher_div('div_saisie_avertissement_fin_periode');return false;\">
				<img src='../images/icons/close16.png' width='16' height='16' alt='Fermer' />
			</a>
		</div>

		<div id='titre_entete_saisie_avertissement_fin_periode'></div>
	</div>

	<div id='corps_saisie_avertissement_fin_periode' class='infobulle_corps' style='color: black; cursor: auto; padding: 0px; height: 15em; width: $largeur_infobulle; overflow: auto;'>
		<form name='form_saisie_avertissement_fin_periode' id='form_saisie_avertissement_fin_periode' action ='../mod_discipline/saisie_avertissement_fin_periode.php' method='post' target='_blank'>
			<fieldset style='border: 1px solid grey; background-image: url(\"../images/background/opacite50.png\");'>
				<input type='hidden' name='saisie_avertissement_fin_periode_login_ele' id='saisie_avertissement_fin_periode_login_ele' value='' />
				<input type='hidden' name='saisie_avertissement_fin_periode_periode' id='saisie_avertissement_fin_periode_periode' value='' />
				<input type='hidden' name='saisie_avertissement_fin_periode_s_periode' id='saisie_avertissement_fin_periode_s_periode' value='' />
				<input type='hidden' name='saisie_avertissement_fin_periode_id_retour_ajax' id='saisie_avertissement_fin_periode_id_retour_ajax' value='' />
				<!--
				Problème avec l'appel depuis une infobulle de saisie d'avis du conseil dans affiche_eleve.php
				<input type='hidden' name='login_ele' id='saisie_avertissement_fin_periode_login_ele' value='' />
				<input type='hidden' name='periode' id='saisie_avertissement_fin_periode_periode' value='' />
				<input type='hidden' name='saisie_avertissement_fin_periode_id_retour_ajax' id='saisie_avertissement_fin_periode_id_retour_ajax' value='' />
				-->

				<p class='bold'>Saisie d'$mod_disc_terme_avertissement_fin_periode</p>
				<div id='div_champs_checkbox_avertissements_fin_periode'>
					" . champs_checkbox_avertissements_fin_periode("", 1, "") . "
				</div>

				<input type='button' onclick='valider_saisie_avertissement_fin_periode()' name='Valider' value='Valider' />
				" . add_token_field() . "

				<p><br /></p>
				<p><em>NOTE&nbsp;:</em> Les cases cochées dans cette infobulle ne correspondent pas nécessairement à l'état actuel des saisies sur la période choisie pour l'élève choisi.</p>
			</fieldset>
		</form>
	</div>
</div>\n";

	return $retour;
}

function insere_avertissement_fin_periode_par_defaut() {
	global $mod_disc_terme_avertissement_fin_periode;

	$cpt_erreur = 0;
	$cpt_reg = 0;
	$retour = "";

	$tab_avertissement_fin_periode_nom_court = array('Av.T', 'Av.C');
	$tab_avertissement_fin_periode = array('Avertissement travail', 'Avertissement conduite');
	for ($i = 0; $i < count($tab_avertissement_fin_periode); $i++) {
		$sql = "SELECT 1=1 FROM s_types_avertissements WHERE nom_complet='$tab_avertissement_fin_periode[$i]';";
		$test = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($test) > 0) {
			$retour .= "L'$mod_disc_terme_avertissement_fin_periode '$tab_avertissement_fin_periode[$i]' est déjà enregistré.<br />\n";
		} else {
			$sql = "INSERT INTO s_types_avertissements SET nom_court='$tab_avertissement_fin_periode_nom_court[$i]',
											nom_complet='$tab_avertissement_fin_periode[$i]'
											;";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$res) {
				$cpt_erreur++;
			} else {
				$cpt_reg++;
			}
		}
	}

	if ($cpt_erreur > 0) {
		$retour .= "$cpt_erreur erreur(s) lors de l'insertion des " . $mod_disc_terme_avertissement_fin_periode . "s par défaut.<br />\n";
	}

	if ($cpt_reg > 0) {
		$retour .= "$cpt_reg $mod_disc_terme_avertissement_fin_periode(s) enregistré(s).<br />\n";
	}

	return $retour;
}


function tableau_des_avertissements_de_fin_de_periode_eleve($login_ele) {
	global $tab_type_avertissement_fin_periode;
	global $mod_disc_terme_avertissement_fin_periode;

	if ((!isset($tab_type_avertissement_fin_periode)) || (!is_array($tab_type_avertissement_fin_periode)) || (count($tab_type_avertissement_fin_periode) == 0)) {
		$tab_type_avertissement_fin_periode = get_tab_type_avertissement();
	}

	if ($mod_disc_terme_avertissement_fin_periode == "") {
		$mod_disc_terme_avertissement_fin_periode = getSettingValue('mod_disc_terme_avertissement_fin_periode');
	}

	$retour = "";

	$tab_avt_ele = get_tab_avertissement($login_ele);
	if (count($tab_avt_ele) > 0) {
		if (getSettingANon("mod_disc_avertissements_mi_periode")) {

			$retour = "<table class='boireaus boireaus_alt boireaus_white_hover'>
	<tr>
		<th title='Période'>Période</th>
		<th>" . ucfirst($mod_disc_terme_avertissement_fin_periode) . "</th>";

			$acces_imprimer_bilan_periode = "n";
			//if(acces('/mod_discipline/imprimer_bilan_periode.php', $_SESSION['statut'])) {
			if (acces_impression_avertissement_fin_periode($login_ele)) {
				$acces_imprimer_bilan_periode = "y";
				$retour .= "
		<th title=\"Imprimer.\">Impr.</th>";
			}

			$tab_classes_ele = get_class_periode_from_ele_login($login_ele);

			$retour .= "
	</tr>";
			foreach ($tab_avt_ele['id_type_avertissement'] as $current_num_periode => $current_tab_avt) {
				$retour .= "
	<tr>
		<td>" . $current_num_periode . "</td>
		<td>";
				if (isset($current_tab_avt["n"])) {
					for ($loop = 0; $loop < count($current_tab_avt["n"]); $loop++) {
						if ($loop > 0) {
							$retour .= "<br />";
						}
						//$retour.=$current_tab_avt[$loop];
						$retour .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$current_tab_avt["n"][$loop]]['nom_complet'];
					}
					$retour .= "</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$current_id_classe = $tab_classes_ele['periode'][$current_num_periode]['id_classe'];
						$retour .= "
		<td><a href='../mod_discipline/imprimer_bilan_periode.php?id_classe[0]=$current_id_classe&s_periode=n&periode[0]=$current_num_periode&eleve[0]=$current_id_classe|$current_num_periode|$login_ele' title=\"Imprimer l'" . $mod_disc_terme_avertissement_fin_periode . "\"><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a></td>";
					}
				} else {
					$retour .= "
		</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$retour .= "
		<td></td>";
					}
				}
			}
			$retour .= "
	</tr>
</table>";
		} else {
			$retour = "<table class='boireaus boireaus_alt boireaus_white_hover'>
	<tr>
		<th title='Période'>Période</th>
		<th title='Moitié ou Fin de période'>&frac12; ou fin</th>
		<th>" . ucfirst($mod_disc_terme_avertissement_fin_periode) . "</th>";

			$acces_imprimer_bilan_periode = "n";
			//if(acces('/mod_discipline/imprimer_bilan_periode.php', $_SESSION['statut'])) {
			if (acces_impression_avertissement_fin_periode($login_ele)) {
				$acces_imprimer_bilan_periode = "y";
				$retour .= "
		<th title=\"Imprimer.\">Impr.</th>";
			}

			$tab_classes_ele = get_class_periode_from_ele_login($login_ele);

			$retour .= "
	</tr>";
			foreach ($tab_avt_ele['id_type_avertissement'] as $current_num_periode => $current_tab_avt) {
				$retour .= "
	<tr>
		<td rowspan='2'>" . $current_num_periode . "</td>
		<td title=\"Mi-période\">&frac12;</td>
		<td>";
				if (isset($current_tab_avt["y"])) {
					for ($loop = 0; $loop < count($current_tab_avt["y"]); $loop++) {
						if ($loop > 0) {
							$retour .= "<br />";
						}
						//$retour.=$current_tab_avt[$loop];
						$retour .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$current_tab_avt["y"][$loop]]['nom_complet'];
					}
					$retour .= "</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$current_id_classe = $tab_classes_ele['periode'][$current_num_periode]['id_classe'];
						$retour .= "
		<td><a href='../mod_discipline/imprimer_bilan_periode.php?id_classe[0]=$current_id_classe&s_periode=y&periode[0]=$current_num_periode&eleve[0]=$current_id_classe|$current_num_periode|$login_ele' title=\"Imprimer l'" . $mod_disc_terme_avertissement_fin_periode . " (mi-période)\"><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a></td>";
					}
				} else {
					$retour .= "
		</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$retour .= "
		<td></td>";
					}
				}
				$retour .= "
	</tr>
	<tr>
		<td title=\"Fin de période\">Fin</td>
		<td>";
				if (isset($current_tab_avt["n"])) {
					for ($loop = 0; $loop < count($current_tab_avt["n"]); $loop++) {
						if ($loop > 0) {
							$retour .= "<br />";
						}
						//$retour.=$current_tab_avt[$loop];
						$retour .= $tab_type_avertissement_fin_periode['id_type_avertissement'][$current_tab_avt["n"][$loop]]['nom_complet'];
					}
					$retour .= "</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$current_id_classe = $tab_classes_ele['periode'][$current_num_periode]['id_classe'];
						$retour .= "
		<td><a href='../mod_discipline/imprimer_bilan_periode.php?id_classe[0]=$current_id_classe&s_periode=n&periode[0]=$current_num_periode&eleve[0]=$current_id_classe|$current_num_periode|$login_ele' title=\"Imprimer l'" . $mod_disc_terme_avertissement_fin_periode . "\"><img src='../images/icons/print.png' class='icone16' alt='Imprimer' /></a></td>";
					}
				} else {
					$retour .= "
		</td>";
					if ($acces_imprimer_bilan_periode == "y") {
						$retour .= "
		<td></td>";
					}
				}
			}
			$retour .= "
	</tr>
</table>";
		}
	}

	/*
	$tab_avertissement_fin_periode=get_tab_avertissement($current_eleve_login, $periode_num);

			echo "<div>
		<img src='../images/icons/balance_justice.png' class='icone20' title=\"Saisir un ou des ".ucfirst($mod_disc_terme_avertissement_fin_periode)."\" style='float:left;' />
		<input type='hidden' name='saisie_avertissement_fin_periode' value='y' />
		<div>
			".champs_checkbox_avertissements_fin_periode($current_eleve_login, $periode_num)."
		</div>
</div>";
*/
	return $retour;
}


function get_info_eleve($login_ele, $periode = 1) {
	$tab = array();

	$sql = "SELECT * FROM eleves WHERE login='$login_ele';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab['login'] = $login_ele;
		$tab['nom'] = $lig->nom;
		$tab['prenom'] = $lig->prenom;
		$tab['denomination'] = casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
		$tab['naissance'] = $lig->naissance;
		$tab['sexe'] = $lig->sexe;
		if ($lig->sexe == "F") {
			$tab['civilite'] = "Mlle";
		} else {
			$tab['civilite'] = "M.";
		}
		$tab['civ_denomination'] = $tab['civilite'] . " " . $tab['denomination'];
		$tab['no_gep'] = $lig->no_gep;
		$tab['elenoet'] = $lig->elenoet;
		$tab['ele_id'] = $lig->ele_id;
		$tab['email'] = $lig->email;
		$tab['tel_pers'] = $lig->tel_pers;
		$tab['tel_prof'] = $lig->tel_prof;
		$tab['tel_port'] = $lig->tel_port;
		$tab['date_entree'] = $lig->date_entree;
		$tab['date_sortie'] = $lig->date_sortie;
		$tab['id_eleve'] = $lig->id_eleve;
		$tab['mef_code'] = $lig->mef_code;

		$sql = "SELECT * FROM j_eleves_regime WHERE login='$login_ele';";
		//echo "$sql<br />";
		$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res2) > 0) {
			$lig2 = mysqli_fetch_object($res2);

			$tab['doublant'] = $lig2->doublant;
			$tab['regime'] = $lig2->regime;
		}

		// 20171124: get_tab_modalites_accompagnement_eleve
		$tab['modalites_accompagnement'] = get_tab_modalites_accompagnement_eleve($login_ele);

		$sql = "SELECT c.id, c.classe FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe AND jec.login='$login_ele' AND jec.periode='$periode';";
		//echo "$sql<br />";
		$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res2) > 0) {
			$lig2 = mysqli_fetch_object($res2);

			$tab['id_classe'] = $lig2->id;
			$tab['classe'] = $lig2->classe;
		}

		$tab['id_classes'] = array();
		$tab['classes'] = "";
		$sql = "SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec WHERE c.id=jec.id_classe AND jec.login='$login_ele' ORDER BY jec.periode;";
		//echo "$sql<br />";
		$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res2) > 0) {
			$cpt = 0;
			while ($lig2 = mysqli_fetch_object($res2)) {
				if ($cpt > 0) {
					$tab['classes'] .= ", ";
				}
				$tab['classes'] .= $lig2->classe;
				if (!in_array($lig2->id, $tab['id_classes'])) {
					$tab['id_classes'][] = $lig2->id;
				}
				$cpt++;
			}
		}

		$tab['statut'] = "eleve";
	}

	return $tab;
}

function get_info_responsable($login_resp, $pers_id = "") {
	$tab = array();

	if (($login_resp != "") || ($pers_id != "")) {
		if ($login_resp != "") {
			$sql = "SELECT * FROM resp_pers WHERE login='$login_resp';";
		} elseif ($pers_id != "") {
			$sql = "SELECT * FROM resp_pers WHERE pers_id='$pers_id';";
		}
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$tab['login'] = $lig->login;
			$tab['pers_id'] = $lig->pers_id;

			$tab['civilite'] = $lig->civilite;
			$tab['nom'] = $lig->nom;
			$tab['prenom'] = $lig->prenom;
			$tab['denomination'] = casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
			$tab['civ_denomination'] = $tab['civilite'] . " " . $tab['denomination'];

			$tab['tel_pers'] = $lig->tel_pers;
			$tab['tel_port'] = $lig->tel_port;
			$tab['tel_prof'] = $lig->tel_prof;
			$tab['mel'] = $lig->mel;
			$tab['email'] = $lig->mel;

			$tab['adr_id'] = $lig->adr_id;

			$tab['adresse'] = get_adresse_responsable($lig->pers_id);
			$tab['enfants'] = get_enfants_from_pers_id($lig->pers_id);

			$tab['statut'] = "responsable";
		}
	}

	return $tab;
}

function get_info_user($login_user, $tab_champs = array()) {
	$tab = array();

	$sql = "SELECT * FROM utilisateurs WHERE login='$login_user';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		if ($lig->statut == 'eleve') {
			$tab = get_info_eleve($login_user, 1);
		} elseif ($lig->statut == 'responsable') {
			$tab = get_info_responsable($login_user);
		} elseif ($lig->statut == 'professeur') {
			$tab['login'] = $lig->login;
			$tab['civilite'] = $lig->civilite;
			$tab['nom'] = $lig->nom;
			$tab['prenom'] = $lig->prenom;
			$tab['statut'] = $lig->statut;
			$tab['email'] = $lig->email;
			$tab['etat'] = $lig->etat;
			$tab['auth_mode'] = $lig->auth_mode;
			$tab['denomination'] = casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
			$tab['civ_denomination'] = $tab['civilite'] . " " . $tab['denomination'];

			$tab['classes'] = get_classes_from_prof($login_user);
			$tab['matieres'] = get_matieres_from_prof($login_user);
			$tab['groupes'] = get_groups_for_prof($login_user);
		} else {
			$tab['login'] = $lig->login;
			$tab['civilite'] = $lig->civilite;
			$tab['nom'] = $lig->nom;
			$tab['prenom'] = $lig->prenom;
			$tab['statut'] = $lig->statut;
			$tab['email'] = $lig->email;
			$tab['etat'] = $lig->etat;
			$tab['auth_mode'] = $lig->auth_mode;
			$tab['denomination'] = casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
			$tab['civ_denomination'] = $tab['civilite'] . " " . $tab['denomination'];
		}
	}

	return $tab;
}

/** Fonction destinée tester si la période est est ouverte/partiellement/close pour un élève sans devoir préciser la classe de l'élève sur la période en question
 *
 * @param string $login_ele identifiant de l'élève
 * @param integer $periode numéro de la période
 *
 * @return string Etat du champ periodes.verouiller
 */
function etat_verrouillage_eleve_periode($login_ele, $periode) {
	$retour = "";

	$sql = "SELECT verouiller FROM periodes p, j_eleves_classes jec WHERE jec.id_classe=p.id_classe AND jec.periode=p.num_periode AND jec.login='$login_ele' AND jec.periode='$periode';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$retour = $lig->verouiller;
	}

	return $retour;
}

/** Fonction destinée tester si la période est est ouverte/partiellement/close pour une classe
 *
 * @param string $id_classe identifiant de la classe
 * @param integer $periode numéro de la période
 *
 * @return string Etat du champ periodes.verouiller
 */
function etat_verrouillage_classe_periode($id_classe, $periode) {
	$retour = "";

	$sql = "SELECT verouiller FROM periodes p WHERE p.id_classe='$id_classe' AND p.num_periode='$periode';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$retour = $lig->verouiller;
	}

	return $retour;
}

function get_infos_classe_periode($id_classe) {
	$tab = array();

	$sql = "SELECT * FROM periodes p WHERE id_classe='$id_classe';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$lig->num_periode]['nom_periode'] = $lig->nom_periode;
			$tab[$lig->num_periode]['verouiller'] = $lig->verouiller;
			$tab[$lig->num_periode]['date_verrouillage'] = $lig->date_verrouillage;
			$tab[$lig->num_periode]['date_fin'] = $lig->date_fin;
		}
	}

	return $tab;
}

function html_etat_verrouillage_periode_classe($id_classe) {
	$retour = "";

	$couleur_ver['O'] = "red";
	$couleur_ver['P'] = "darkorange";
	$couleur_ver['N'] = "green";

	$texte_ver['O'] = "close";
	$texte_ver['P'] = "partiellement close";
	$texte_ver['N'] = "ouverte";

	$sql = "SELECT * FROM periodes p WHERE id_classe='$id_classe' ORDER BY num_periode;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			if ($retour != "") {
				$retour .= "-";
			}
			$retour .= "<span style='color:" . $couleur_ver[$lig->verouiller] . "' title=\"$lig->nom_periode : Période " . $texte_ver[$lig->verouiller] . " \">$lig->num_periode</span>";
		}
	}

	return $retour;
}

function get_verrouillage_classes_periodes() {
	$tab = array();

	$sql = "SELECT * FROM periodes;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$lig->id_classe][$lig->num_periode] = $lig->verouiller;
		}
	}

	return $tab;
}

function check_heure($heure, $format = "") {
	if (preg_match("/[0-9]{1,2}:[0-9]{2}:[0-9]{2}/", $heure)) {
		$tmp_tab = explode(":", $heure);
		$h = $tmp_tab[0];
		$m = $tmp_tab[1];
		$s = $tmp_tab[2];

		if (($h >= 0) && ($h <= 23) &&
			($m >= 0) && ($m <= 59) &&
			($s >= 0) && ($s <= 59)) {
			return true;
		} else {
			return false;
		}
	} elseif (preg_match("/[0-9]{1,2}:[0-9]{2}/", $heure)) {
		$tmp_tab = explode(":", $heure);
		$h = $tmp_tab[0];
		$m = $tmp_tab[1];

		if (($h >= 0) && ($h <= 23) &&
			($m >= 0) && ($m <= 59)) {
			return true;
		} else {
			return false;
		}
	} else {
		// Tester les créneaux M1, M2,...
		$sql = "SELECT * FROM edt_creneaux WHERE nom_definie_periode='$heure';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			return true;
		} else {
			return false;
		}
	}
}

function get_mysql_heure($heure, $debut_ou_fin = "debut") {
	if (preg_match("/[0-9]{1,2}:[0-9]{2}:[0-9]{2}/", $heure)) {
		$tmp_tab = explode(":", $heure);
		$h = $tmp_tab[0];
		$m = $tmp_tab[1];
		$s = $tmp_tab[2];

		if (($h >= 0) && ($h <= 23) &&
			($m >= 0) && ($m <= 59) &&
			($s >= 0) && ($s <= 59)) {
			return $h . ":" . $m . ":" . $s;
		} else {
			return false;
		}
	} elseif (preg_match("/[0-9]{1,2}:[0-9]{2}/", $heure)) {
		$tmp_tab = explode(":", $heure);
		$h = $tmp_tab[0];
		$m = $tmp_tab[1];

		if (($h >= 0) && ($h <= 23) &&
			($m >= 0) && ($m <= 59)) {
			return $h . ":" . $m . ":00";
		} else {
			return false;
		}
	} else {
		// Tester les créneaux M1, M2,...
		$sql = "SELECT * FROM edt_creneaux WHERE nom_definie_periode='$heure';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);

			if ($debut_ou_fin == "debut") {
				return $lig->heuredebut_definie_periode;
			} else {
				return $lig->heurefin_definie_periode;
			}
		} else {
			return false;
		}
	}
}

function get_premiere_et_derniere_date_cn_devoirs_classe_periode($id_classe, $periode) {
	$tab_date = array();

	$sql = "select cd.date from classes c,j_groupes_classes jgc,cn_cahier_notes ccn,cn_conteneurs cc, cn_devoirs cd where c.id=jgc.id_classe and jgc.id_groupe=ccn.id_groupe and ccn.id_cahier_notes=cc.id and cd.id_racine=cc.id and jgc.id_classe='$id_classe' and periode='$periode' order by date asc limit 1;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab_date[0] = $lig->date;
	}

	$sql = "select cd.date from classes c,j_groupes_classes jgc,cn_cahier_notes ccn,cn_conteneurs cc, cn_devoirs cd where c.id=jgc.id_classe and jgc.id_groupe=ccn.id_groupe and ccn.id_cahier_notes=cc.id and cd.id_racine=cc.id and jgc.id_classe='$id_classe' and periode='$periode' order by date desc limit 1;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab_date[1] = $lig->date;
	}

	return $tab_date;
}

function affiche_lien_mailto($mail_user, $info_user) {
	$retour = " <a href='mailto:" . $mail_user . "?subject=" . getSettingValue('gepiPrefixeSujetMail') . "GEPI&amp;body=";
	$tmp_date = getdate();
	if ($tmp_date['hours'] >= 18) {
		$retour .= "Bonsoir";
	} else {
		$retour .= "Bonjour";
	}
	$retour .= ",%0d%0aCordialement.' title=\"Envoyer un mail à $info_user\">";
	$retour .= "<img src='../images/icons/mail.png' class='icone16' alt='mail' />";
	$retour .= "</a>";
	return $retour;
}

function affiche_lien_mailto_si_mail_valide($login_user, $designation_user = "", $mail_user = "") {
	$retour = "";

	if ($designation_user == "") {
		$designation_user = civ_nom_prenom($designation_user);
	}

	if ($mail_user == "") {
		$mail_user = get_mail_user($login_user);
	}

	if (check_mail($mail_user)) {
		$retour = affiche_lien_mailto($mail_user, $designation_user);
	}

	return $retour;
}

function acces_impression_bulletin($login_eleve, $id_classe = "") {

	$retour = false;

	if (($_SESSION['statut'] == 'professeur') && (getSettingAOui('GepiProfImprBul')) && (is_pp($_SESSION['login']))) {
		if ($login_eleve != "") {
			// PP: Le test est fait sur l'association avec la classe (même si l'élève a changé de classe en cours d'année)
			//     On ne se contente pas de is_pp(is_pp($_SESSION['login'], '', $login_ele)
			$sql = "SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec WHERE jec.login='$login_eleve';";
			//echo "$sql<br />";
			$res = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_object($res)) {
					if (is_pp($_SESSION['login'], $lig->id_classe)) {
						$retour = true;
						break;
					}
				}
			}
		} elseif (($id_classe != "") && (is_pp($_SESSION['login'], $id_classe))) {
			$retour = true;
		}
	} elseif (($_SESSION['statut'] == 'cpe') && (getSettingAOui('GepiCpeImprBul'))) {
		if (is_cpe($_SESSION['login'], "", $login_eleve)) {
			$retour = true;
		}
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'secours') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	}

	return $retour;
}

function acces_impression_releve_notes($login_eleve, $id_classe = "") {
	$retour = false;

	if (($_SESSION['statut'] == 'professeur') && (getSettingAOui('GepiAccesReleveProfToutesClasses'))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'professeur') && ($login_eleve != "") && (getSettingAOui('GepiAccesReleveProf')) && (is_prof_ele($_SESSION['login'], $login_eleve, "", $id_classe))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'professeur') && ($login_eleve != "") && (getSettingAOui('GepiAccesReleveProfTousEleves')) && (is_prof_classe_ele($_SESSION['login'], $login_eleve))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'professeur') && ($id_classe != "") && (getSettingAOui('GepiAccesReleveProfTousEleves')) && (is_prof_classe($_SESSION['login'], $id_classe))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'professeur') && ($id_classe != "") && (getSettingAOui('GepiAccesReleveProfP')) && (is_pp($_SESSION['login'], $id_classe))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'professeur') && ($login_eleve != "") && (getSettingAOui('GepiAccesReleveProfP')) && (is_pp($_SESSION['login']))) {
		// PP: Le test est fait sur l'association avec la classe (même si l'élève a changé de classe en cours d'année)
		//     On ne se contente pas de is_pp(is_pp($_SESSION['login'], '', $login_ele)
		$sql = "SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec WHERE jec.login='$login_eleve';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				if (is_pp($_SESSION['login'], $lig->id_classe)) {
					$retour = true;
					break;
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'cpe') && (getSettingAOui('GepiAccesReleveCpeTousEleves'))) {
		$retour = true;
	} elseif (($_SESSION['statut'] == 'cpe') && (getSettingAOui('GepiAccesReleveCpe'))) {
		if (is_cpe($_SESSION['login'], "", $login_eleve)) {
			$retour = true;
		}
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'secours') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'administrateur') {
		$retour = false;
	}

	return $retour;
}

function renseigner_tab_rn($tab_id_classe) {
	global $tab_rn_nomdev,
		   $tab_rn_toutcoefdev,
		   $tab_rn_coefdev_si_diff,
		   $tab_rn_col_moy,
		   $tab_rn_datedev,
		   $tab_rn_app,
		   $tab_rn_sign_chefetab,
		   $tab_rn_sign_pp,
		   $tab_rn_sign_resp,
		   $tab_rn_sign_nblig,
		   $tab_rn_formule,
		   $tab_rn_adr_resp,
		   $tab_rn_bloc_obs,
		   $tab_rn_bloc_abs2,
		   $tab_rn_aff_classe_nom,
		   $tab_rn_moy_min_max_classe,
		   $tab_rn_moy_classe,
		   $tab_rn_retour_ligne,
		   $tab_rn_rapport_standard_min_font;

	$tab_param_table_classes_param = array('rn_aff_classe_nom', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy');

	//$tab_item=array();
	$tab_item = $tab_param_table_classes_param;
	$tab_item[] = 'rn_nomdev';
	$tab_item[] = 'rn_toutcoefdev';
	$tab_item[] = 'rn_coefdev_si_diff';
	$tab_item[] = 'rn_datedev';

	// SELON LE STATUT: Accès ou pas
	if ((($_SESSION['statut'] != 'eleve') && ($_SESSION['statut'] != 'responsable')) ||
		(($_SESSION['statut'] == 'eleve') && (getSettingAOui('GepiAccesColMoyReleveEleve'))) ||
		(($_SESSION['statut'] == 'responsable') && (getSettingAOui('GepiAccesColMoyReleveParent')))
	) {
		$tab_item[] = 'rn_col_moy';
	}

	$tab_item[] = 'rn_sign_chefetab';
	$tab_item[] = 'rn_sign_pp';
	$tab_item[] = 'rn_sign_resp';

	if (getSettingValue("active_module_absence") == '2') {
		$tab_item[] = 'rn_abs_2';
	}

	$chaine_coef = "coef.: ";

	for ($k = 0; $k < count($tab_item); $k++) {
		$champ_courant = $tab_item[$k];
		$chaine_tab = "tab_" . $champ_courant;
		//echo "<p style='text-indent:-3em;margin-left:3em;'>Récupération de la valeur de ".$tab_item[$k]."<br />";
		for ($i = 0; $i < count($tab_id_classe); $i++) {
			if (!in_array($tab_item[$k], $tab_param_table_classes_param)) {
				$sql = "SELECT * FROM classes WHERE id='" . $tab_id_classe[$i] . "';";
				//echo "$sql<br />";
				$res_class_tmp = mysqli_query($GLOBALS["mysqli"], $sql);
				if (mysqli_num_rows($res_class_tmp) > 0) {
					$lig_class_tmp = mysqli_fetch_object($res_class_tmp);

					$current_item = $tab_item[$k];
					if ($lig_class_tmp->$current_item == "y") {
						${$chaine_tab}[$i] = "y";

						//echo "\${".$chaine_tab."}[$i]=".${$chaine_tab}[$i]."<br />";

					}
					/*
					else {
						${$chaine_tab}[$i]="n";
					}
					*/
				}
			} elseif (getParamClasse($tab_id_classe[$i], $tab_item[$k], '') == "y") {
				${$chaine_tab}[$i] = "y";

				//echo "\${".$chaine_tab."}[$i]=".${$chaine_tab}[$i]."<br />";
			} else {
				//echo "getParamClasse(".$tab_id_classe[$i].",".$tab_item[$k].",'')=".getParamClasse($tab_id_classe[$i],$tab_item[$k],'')."<br />";
			}
		}
	}
}

function get_tab_infos_cn_devoir($id_dev) {
	$tab = array();

	$sql = "SELECT * FROM cn_devoirs WHERE id='$id_dev';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab = mysqli_fetch_assoc($res);
	}

	return $tab;
}

function get_tab_infos_epreuve_blanche($id_epreuve) {
	$tab = array();

	$sql = "SELECT * FROM eb_epreuves WHERE id='$id_epreuve';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab = mysqli_fetch_assoc($res);
	}

	return $tab;
}


function get_tab_grp_groupes($id_grp_groupe, $champs_groupes = array('classes', 'matieres')) {
	$tab = array();

	$sql = "SELECT * FROM grp_groupes WHERE id='$id_grp_groupe';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab = mysqli_fetch_assoc($res);
		$tab['id_grp_groupe'] = $tab['id'];

		// Récupérer les groupes associés
		$tab['groupes'] = array();
		$sql = "SELECT * FROM grp_groupes_groupes WHERE id_grp_groupe='$id_grp_groupe';";
		$res_groupes = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_groupes) > 0) {
			$cpt = 0;

			while ($lig = mysqli_fetch_object($res_groupes)) {
				$tab['groupes'][$cpt] = get_group($lig->id_groupe, $champs_groupes);
				$tab['groupes'][$cpt]['id_groupe'] = $lig->id_groupe;
				$cpt++;
			}
		}

		// Récupérer les utilisateurs associés
		$tab['admin'] = array();
		$sql = "SELECT u.login, u.civilite, u.nom, u.prenom, u.statut FROM grp_groupes_admin gga, utilisateurs u WHERE id_grp_groupe='$id_grp_groupe' AND u.login=gga.login ORDER BY statut, nom, prenom;";
		$res_u = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_u) > 0) {
			$cpt = 0;

			while ($lig = mysqli_fetch_object($res_u)) {
				$tab['admin'][$cpt]['login'] = $lig->login;
				$tab['admin'][$cpt]['nom'] = casse_mot($lig->nom, 'maj');
				$tab['admin'][$cpt]['prenom'] = casse_mot($lig->prenom, 'majf2');
				$tab['admin'][$cpt]['civilite'] = $lig->civilite;
				$tab['admin'][$cpt]['statut'] = $lig->statut;

				$tab['admin'][$cpt]['denomination'] = $lig->civilite . " " . $tab['admin'][$cpt]['nom'] . " " . $tab['admin'][$cpt]['prenom'];

				$cpt++;
			}
		}

	}
	return $tab;
}

function acces_modif_liste_eleves_grp_groupes($id_groupe = "", $id_grp_groupe = "") {
	if (($id_groupe == "") && ($id_grp_groupe == "")) {
		$sql = "SELECT 1=1 FROM grp_groupes_admin WHERE login='" . $_SESSION['login'] . "';";
	} elseif ($id_groupe != "") {
		$sql = "SELECT 1=1 FROM grp_groupes_admin gga, grp_groupes_groupes ggg WHERE gga.login='" . $_SESSION['login'] . "' AND gga.id_grp_groupe=ggg.id_grp_groupe AND ggg.id_groupe='$id_groupe';";
	} elseif ($id_grp_groupe != "") {
		$sql = "SELECT 1=1 FROM grp_groupes_admin gga WHERE gga.login='" . $_SESSION['login'] . "' AND gga.id_grp_groupe='$id_grp_groupe';";
	}
	//echo "$sql<br />";
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($test) == 0) {
		return false;
	} else {
		return true;
	}
}

function is_groupe_du_grp_groupes($id_groupe, $id_grp_groupe) {
	$sql = "SELECT 1=1 FROM grp_groupes_groupes WHERE id_groupe='" . $id_groupe . "' AND id_grp_groupe='" . $id_grp_groupe . "';";
	//echo "$sql<br />";
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($test) == 0) {
		return false;
	} else {
		return true;
	}
}

//fonction redimensionne les photos petit format
function redimensionne_image_petit($photo) {
	global $photo_redim_taille_max_largeur, $photo_redim_taille_max_hauteur;

	if ((!preg_match("/^[0-9]{1,}$/", $photo_redim_taille_max_largeur)) || ($photo_redim_taille_max_largeur <= 0)) {
		$photo_redim_taille_max_largeur = 35;
	}

	if ((!preg_match("/^[0-9]{1,}$/", $photo_redim_taille_max_hauteur)) || ($photo_redim_taille_max_hauteur <= 0)) {
		$photo_redim_taille_max_hauteur = 35;
	}

	// prendre les informations sur l'image
	$info_image = getimagesize($photo);
	// largeur et hauteur de l'image d'origine
	$largeur = $info_image[0];
	$hauteur = $info_image[1];
	// largeur et/ou hauteur maximum à afficher
	//$taille_max_largeur = 35;
	//$taille_max_hauteur = 35;

	// calcule le ratio de redimensionnement
	$ratio_l = $largeur / $photo_redim_taille_max_largeur;
	$ratio_h = $hauteur / $photo_redim_taille_max_hauteur;
	$ratio = ($ratio_l > $ratio_h) ? $ratio_l : $ratio_h;

	// définit largeur et hauteur pour la nouvelle image
	$nouvelle_largeur = $largeur / $ratio;
	$nouvelle_hauteur = $hauteur / $ratio;

	// on renvoit la largeur et la hauteur
	return array($nouvelle_largeur, $nouvelle_hauteur);
}

// Le prof de $id_groupe a-t-il autorisé le PP à corriger ses appréciations
// On teste aussi getSettingAOui('PeutAutoriserPPaCorrigerSesApp')
function acces_correction_app_pp($id_groupe) {
	$sql = "SELECT 1=1 FROM groupes_param WHERE id_groupe='$id_groupe' AND name='AutoriserCorrectionAppreciationParPP' AND value='y';";
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($test) == 0) {
		return false;
	} else {
		return true;
	}
}


/** Fonction destinée à corriger des chemins du type https://NOM_SERVEUR/CHEMIN_GEPI/documents/cl1234/XXX en ../documents/cl1234/XXX
 *
 * @param string $texte Le texte à traiter
 * @return string La chaine corrigée
 */
function cdt_changer_chemin_absolu_en_relatif($texte) {
	global $multisite;

	$contenu_cor = $texte;

	$url_absolues_gepi = getSettingValue("url_absolues_gepi");
	if ($url_absolues_gepi != "") {
		$tab_url = explode("|", $url_absolues_gepi);
		for ($loop = 0; $loop < count($tab_url); $loop++) {
			if (preg_match('| src="' . $tab_url[$loop] . '/documents/|', $contenu_cor)) {
				$contenu_cor = preg_replace('| src="' . $tab_url[$loop] . '/documents/|', ' src="../documents/', $contenu_cor);
			}
			if (preg_match('| href="' . $tab_url[$loop] . '/documents/|', $contenu_cor)) {
				$contenu_cor = preg_replace('| href="' . $tab_url[$loop] . '/documents/|', ' href="../documents/', $contenu_cor);
			}

			if (preg_match('| href="' . $tab_url[$loop] . '/cahier_texte_2/visionneur_geogebra.php|', $contenu_cor)) {
				$contenu_cor = preg_replace('| href="' . $tab_url[$loop] . '/cahier_texte_2/visionneur_geogebra.php|', ' href="../cahier_texte_2/visionneur_geogebra.php', $contenu_cor);
			}
		}
	}

	return $contenu_cor;
}

/** Fonction destinée à retourner un tableau des fichiers présents dans le dossier de sauvegarde de Gepi (backup\$backup_directory)
 *
 * @param string $path Le chemin (optionnel): s'il n'est pas fourni, on cherche dans backup\$backup_directory
 * @return array Le tableau des fichiers de sauvegarde (de la base, des bulletins PDF, des photos,...)
 */
function get_tab_fichiers_du_dossier_de_sauvegarde($path = "", $sous_dossier = "n") {
	global $temoin_dossier_backup_absences;

	if ($path == "") {
		$dirname = getSettingValue("backup_directory");
		$path = '../backup/' . $dirname;
	}
	$handle = opendir($path);
	$tab_file = array();
	$n = 0;
	while ($file = readdir($handle)) {
		if ($sous_dossier == "n") {
			if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
				//=================================
				and ($file != 'csv')
				and ($file != 'bulletins')
				and ($file != 'absences') //ne pas afficher le dossier export des absences en fin d'année
				and ($file != 'notanet') //ne pas afficher le dossier notanet
				//=================================
				and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html') and ($file != '.test')
				and (!preg_match('/sql.gz.txt$/i', $file))) {
				$tab_file[] = $file;
				$n++;
			}

			if ($file == 'absences') {
				$temoin_dossier_backup_absences = "y";
			}
		} else {
			if (($file != '.') and ($file != '..') and ($file != 'remove.txt')
				and ($file != '.htaccess') and ($file != '.htpasswd') and ($file != 'index.html') and ($file != '.test')) {
				$tab_file[] = $file;
				$n++;
			}
		}
	}
	closedir($handle);
	arsort($tab_file);

	return $tab_file;
}

function get_tab_jour_ouverture_etab($mode = "") {
	$tab_jour = array();
	if ($mode == "indice") {
		// id_j_semaine() : 	ISO-8601 numeric representation of the day of the week 	1 (for Monday) through 7 (for Sunday)
		$tmp_tab_jour = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
		$tmp_tab_jour_US = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
		for ($loop = 0; $loop < count($tmp_tab_jour); $loop++) {
			$sql = "SELECT DISTINCT jour_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='" . $tmp_tab_jour[$loop] . "' AND ouvert_horaire_etablissement='1';";
			$test_jour = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test_jour) > 0) {
				$tab_jour[$loop + 1]["fr"] = $tmp_tab_jour[$loop];
				$tab_jour[$loop + 1]["us"] = $tmp_tab_jour_US[$loop];
			}
		}
	} else {
		$tmp_tab_jour = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
		for ($loop = 0; $loop < count($tmp_tab_jour); $loop++) {
			$sql = "SELECT DISTINCT jour_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='" . $tmp_tab_jour[$loop] . "' AND ouvert_horaire_etablissement='1';";
			$test_jour = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test_jour) > 0) {
				$tab_jour[] = $tmp_tab_jour[$loop];
			}
		}
	}
	return $tab_jour;
}

function get_tab_jour_ouverture_etab_US() {
	$tab_jour = array();
	$tmp_tab_jour = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
	$tmp_tab_jour_US = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");
	for ($loop = 0; $loop < count($tmp_tab_jour); $loop++) {
		$sql = "SELECT DISTINCT jour_horaire_etablissement FROM horaires_etablissement WHERE jour_horaire_etablissement='" . $tmp_tab_jour[$loop] . "' AND ouvert_horaire_etablissement='1';";
		$test_jour = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($test_jour) > 0) {
			$tab_jour[] = $tmp_tab_jour_US[$loop];
		}
	}
	return $tab_jour;
}

function nombre_de_dossiers_docs_joints_a_des_sanctions() {
	global $multisite;

	$dossier_documents_discipline = "../documents/discipline";
	if (((isset($multisite)) && ($multisite == 'y')) || (getSettingValue('multisite') == 'y')) {
		if (isset($_COOKIE['RNE'])) {
			$dossier_documents_discipline .= "_" . $_COOKIE['RNE'];
		}
	}

	if (!file_exists($dossier_documents_discipline)) {
		@mkdir($dossier_documents_discipline, 0770);
	}

	$handle = opendir($dossier_documents_discipline);
	$nombre_de_dossiers_de_documents_discipline = 0;
	while ($file = readdir($handle)) {
		if (preg_match("/^incident_[0-9]*$/", $file)) {
			$nombre_de_dossiers_de_documents_discipline++;
		}
	}
	closedir($handle);

	return $nombre_de_dossiers_de_documents_discipline;
}

function get_tab_propositions_remplacements($login_user, $mode = "", $info_famille = "") {
	$tab = array();

	$sql_ajout = "";
	if ($login_user != "") {
		$sql_ajout .= " AND login_user='$login_user'";
	}

	if ($info_famille == "oui") {
		$sql_ajout .= " AND info_famille='$info_famille'";
	} elseif ($info_famille == "non") {
		$sql_ajout .= " AND (info_famille='$info_famille' OR info_famille='')";
	}

	if ($mode == "") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE date_fin_r>='" . strftime('%Y-%m-%d %H:%M:%S') . "' $sql_ajout ORDER BY date_debut_r;";
	} elseif ($mode == "en_attente") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE date_fin_r>='" . strftime('%Y-%m-%d %H:%M:%S') . "' $sql_ajout AND reponse='' ORDER BY date_debut_r;";
	} elseif ($mode == "futures_avec_reponse") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE date_fin_r>='" . strftime('%Y-%m-%d %H:%M:%S') . "' $sql_ajout AND reponse!='' ORDER BY date_debut_r;";
	} elseif ($mode == "futures_validees") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE date_fin_r>='" . strftime('%Y-%m-%d %H:%M:%S') . "' $sql_ajout AND validation_remplacement='oui' ORDER BY date_debut_r;";
	} elseif ($mode == "validees_passees") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE date_fin_r<='" . strftime('%Y-%m-%d %H:%M:%S') . "' $sql_ajout AND validation_remplacement='oui' ORDER BY date_debut_r;";
	} elseif ($mode == "validees") {
		$sql = "SELECT * FROM abs_prof_remplacement WHERE validation_remplacement='oui' $sql_ajout ORDER BY date_debut_r;";
	}
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		$tab_infos_absence = array();
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$cpt]['id'] = $lig->id;
			$tab[$cpt]['id_absence'] = $lig->id_absence;

			if (!isset($tab_infos_absence[$lig->id_absence])) {
				$sql = "SELECT * FROM abs_prof WHERE id='$lig->id_absence';";
				$res_abs = mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_abs = mysqli_fetch_object($res_abs);
				$tab_infos_absence[$lig->id_absence]['login_prof_abs'] = $lig_abs->login_user;
			}
			$tab[$cpt]['login_prof_abs'] = $tab_infos_absence[$lig->id_absence]['login_prof_abs'];

			$tab[$cpt]['id_groupe'] = $lig->id_groupe;
			$tab[$cpt]['id_aid'] = $lig->id_aid;
			$tab[$cpt]['id_classe'] = $lig->id_classe;
			$tab[$cpt]['jour'] = $lig->jour;
			$tab[$cpt]['id_creneau'] = $lig->id_creneau;
			$tab[$cpt]['date_debut_r'] = $lig->date_debut_r;
			$tab[$cpt]['date_fin_r'] = $lig->date_fin_r;
			$tab[$cpt]['login_user'] = $lig->login_user;
			$tab[$cpt]['commentaire_prof'] = $lig->commentaire_prof;
			$tab[$cpt]['reponse'] = $lig->reponse;
			$tab[$cpt]['date_reponse'] = $lig->date_reponse;
			$tab[$cpt]['validation_remplacement'] = $lig->validation_remplacement;
			$tab[$cpt]['commentaire_validation'] = $lig->commentaire_validation;
			$tab[$cpt]['salle'] = $lig->salle;
			$tab[$cpt]['info_famille'] = $lig->info_famille;
			$tab[$cpt]['texte_famille'] = $lig->texte_famille;
			$cpt++;
		}
	}

	return $tab;
}

function check_proposition_remplacement_validee($id_absence, $id_groupe, $id_aid, $id_classe, $jour, $id_creneau) {
	$retour = "";

	$sql = "SELECT * FROM abs_prof_remplacement WHERE id_absence='$id_absence' AND id_groupe='$id_groupe' AND id_aid='$id_aid' AND id_classe='$id_classe' AND jour='$jour' AND id_creneau='$id_creneau' AND validation_remplacement='oui';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$retour = civ_nom_prenom($lig->login_user);
	}

	return $retour;
}

function check_proposition_remplacement_validee2($id_proposition) {
	$retour = "";

	$sql = "SELECT * FROM abs_prof_remplacement WHERE id='$id_proposition' AND validation_remplacement='oui';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$retour = civ_nom_prenom($lig->login_user);
	}

	return $retour;
}

function get_heures_debut_fin_creneaux() {
	$tab = array();

	$sql = "SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$lig->id_definie_periode]['nom_creneau'] = $lig->nom_definie_periode;
			$tab[$lig->id_definie_periode]['debut'] = $lig->heuredebut_definie_periode;
			$tab[$lig->id_definie_periode]['fin'] = $lig->heurefin_definie_periode;
			$tab[$lig->id_definie_periode]['debut_court'] = substr($lig->heuredebut_definie_periode, 0, 5);
			$tab[$lig->id_definie_periode]['fin_court'] = substr($lig->heurefin_definie_periode, 0, 5);
		}
	}

	return $tab;
}

function get_infos_creneau($id_creneau) {
	$tab = array();

	$sql = "SELECT * FROM edt_creneaux WHERE id_definie_periode='$id_creneau';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab['nom_creneau'] = $lig->nom_definie_periode;
			$tab['type_creneaux'] = $lig->type_creneaux;
			$tab['debut'] = $lig->heuredebut_definie_periode;
			$tab['fin'] = $lig->heurefin_definie_periode;
			$tab['debut_court'] = substr($lig->heuredebut_definie_periode, 0, 5);
			$tab['fin_court'] = substr($lig->heurefin_definie_periode, 0, 5);
			$tab['info'] = $lig->nom_definie_periode . " (" . $tab['debut_court'] . " - " . $tab['fin_court'] . ")";
			$tab['info_html'] = $lig->nom_definie_periode . " (<em>" . $tab['debut_court'] . " - " . $tab['fin_court'] . "</em>)";

			$tmp_tab1 = explode(':', $tab['debut']);
			$tmp_tab2 = explode(':', $tab['fin']);
			$tab['duree_minutes'] = ($tmp_tab2[0] * 60 + $tmp_tab2[1]) - ($tmp_tab1[0] * 60 + $tmp_tab1[1]);
		}
	}

	return $tab;
}

function get_tab_profs_exclus_des_propositions_de_remplacement() {
	$tab = array();

	$sql = "SELECT value FROM abs_prof_divers WHERE name='login_exclus';";
	$res_mae = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_mae) > 0) {
		while ($lig_mae = mysqli_fetch_object($res_mae)) {
			$tab[] = $lig_mae->value;
		}
	}

	return $tab;
}

function get_tab_matieres_exclues_des_propositions_de_remplacement() {
	$tab = array();

	$sql = "SELECT value FROM abs_prof_divers WHERE name='matiere_exclue';";
	$res_mae = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_mae) > 0) {
		while ($lig_mae = mysqli_fetch_object($res_mae)) {
			$tab[] = $lig_mae->value;
		}
	}

	return $tab;
}

function get_tab_profs_refusant_toute_proposition_de_remplacement() {
	$tab = array();

	if ((getSettingAOui('active_mod_abs_prof')) && (getSettingAOui('AbsProfAutoriserProfPasApparaitre'))) {
		$sql = "SELECT login FROM preferences WHERE name='AbsProf_jamais_remplacer';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab[] = $lig->login;
			}
		}
	}

	return $tab;
}

function get_tab_jours_vacances($id_classe = '') {
	$tab = array();

	$sql_ajout = "";
	if ($id_classe != "") {
		$sql_ajout = " AND (classe_concerne_calendrier LIKE '$id_classe;%' OR classe_concerne_calendrier LIKE '%;$id_classe;%')";
	}

	$sql = "SELECT * FROM edt_calendrier WHERE etabvacances_calendrier='1'" . $sql_ajout . ";";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$ts1 = $lig->debut_calendrier_ts;
			$ts2 = $lig->fin_calendrier_ts;

			if ($ts2 > $ts1) {
				$current_ts = $ts1;
				while ($current_ts < $ts2) {

					$tab[] = strftime("%Y%m%d", $current_ts);

					$current_ts += 3600 * 24;
				}
			}

		}
	}

	return $tab;
}

function get_tab_jours_vacances2($id_classe = '') {
	$tab = array();
	$tab['jour'] = array();
	$tab['nom_ferie'] = array();

	$sql_ajout = "";
	if ($id_classe != "") {
		$sql_ajout = " AND (classe_concerne_calendrier LIKE '$id_classe;%' OR classe_concerne_calendrier LIKE '%;$id_classe;%')";
	}

	$sql = "SELECT * FROM edt_calendrier WHERE etabvacances_calendrier='1'" . $sql_ajout . ";";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$ts1 = $lig->debut_calendrier_ts;
			$ts2 = $lig->fin_calendrier_ts;

			if ($ts2 > $ts1) {
				$current_ts = $ts1;
				while ($current_ts < $ts2) {

					$tab['jour'][] = strftime("%Y%m%d", $current_ts);
					$tab['nom_ferie'][] = $lig->nom_calendrier;

					$current_ts += 3600 * 24;
				}
			}

		}
	}

	return $tab;
}

function get_tab_remplacements_eleve($login_eleve, $mode = "") {
	global $gepiPath;

	$tab = array();

	$sql_ajout = "";
	if ($mode == "") {
		$sql_ajout = " AND date_fin_r>='" . strftime('%Y-%m-%d %H:%M:%S') . "'";
	}

	$sql = "SELECT DISTINCT apr.* FROM abs_prof_remplacement apr, 
					j_eleves_groupes jeg, 
					j_eleves_classes jec 
				WHERE jeg.login='$login_eleve' AND 
					jeg.login=jec.login AND 
					jec.id_classe=apr.id_classe AND 
					jeg.id_groupe=apr.id_groupe AND 
					apr.validation_remplacement='oui' AND 
					apr.info_famille='oui'" . $sql_ajout . ";";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		$tab_infos_absence = array();
		$nom_prof = array();
		while ($lig = mysqli_fetch_object($res)) {
			// Il faudrait tester plus finement les dates dans le cas d'élèves qui changent de classe en cours d'année.

			$tab[$cpt]['id'] = $lig->id;
			$tab[$cpt]['id_absence'] = $lig->id_absence;

			if (!isset($tab_infos_absence[$lig->id_absence])) {
				$sql = "SELECT * FROM abs_prof WHERE id='$lig->id_absence';";
				$res_abs = mysqli_query($GLOBALS["mysqli"], $sql);
				$lig_abs = mysqli_fetch_object($res_abs);
				$tab_infos_absence[$lig->id_absence]['login_prof_abs'] = $lig_abs->login_user;
			}
			$tab[$cpt]['login_prof_abs'] = $tab_infos_absence[$lig->id_absence]['login_prof_abs'];

			if (!isset($nom_prof[$tab[$cpt]['login_prof_abs']])) {
				$nom_prof[$tab[$cpt]['login_prof_abs']] = affiche_utilisateur($tab[$cpt]['login_prof_abs'], $lig->id_classe);
			}

			$tab[$cpt]['id_groupe'] = $lig->id_groupe;
			$tab[$cpt]['id_classe'] = $lig->id_classe;
			$tab[$cpt]['jour'] = $lig->jour;
			$tab[$cpt]['id_creneau'] = $lig->id_creneau;
			$tab[$cpt]['date_debut_r'] = $lig->date_debut_r;
			$tab[$cpt]['date_fin_r'] = $lig->date_fin_r;
			$tab[$cpt]['login_user'] = $lig->login_user;

			if (!isset($nom_prof[$tab[$cpt]['login_user']])) {
				$nom_prof[$tab[$cpt]['login_user']] = affiche_utilisateur($tab[$cpt]['login_user'], $lig->id_classe);
			}

			$tab[$cpt]['commentaire_prof'] = $lig->commentaire_prof;
			$tab[$cpt]['reponse'] = $lig->reponse;
			$tab[$cpt]['date_reponse'] = $lig->date_reponse;
			$tab[$cpt]['validation_remplacement'] = $lig->validation_remplacement;
			$tab[$cpt]['commentaire_validation'] = $lig->commentaire_validation;
			$tab[$cpt]['salle'] = $lig->salle;
			$tab[$cpt]['info_famille'] = $lig->info_famille;
			$tab[$cpt]['texte_famille'] = $lig->texte_famille;

			// Effectuer des preg_replace() sur des chaines
			//__PROF_ABSENT__, __COURS__, __DATE_HEURE__, __PROF_REMPLACANT__ et __SALLE__
			$chaine_a_traduire = $lig->texte_famille;
			$chaine_a_traduire = preg_replace("/__SALLE__/", $lig->salle, $chaine_a_traduire);
			$chaine_a_traduire = preg_replace("/__PROF_ABSENT__/", $nom_prof[$tab[$cpt]['login_prof_abs']], $chaine_a_traduire);
			$chaine_a_traduire = preg_replace("/__PROF_REMPLACANT__/", $nom_prof[$tab[$cpt]['login_user']], $chaine_a_traduire);

			$ts1 = mysql_date_to_unix_timestamp($tab[$cpt]['date_debut_r']);
			$date_heure = french_strftime("%A %d/%m/%Y de %H:%M", $ts1);
			$ts2 = mysql_date_to_unix_timestamp($tab[$cpt]['date_fin_r']);
			$date_heure .= strftime(" à %H:%M", $ts2);
			$chaine_a_traduire = preg_replace("/__DATE_HEURE__/", $date_heure, $chaine_a_traduire);

			$info_grp = get_info_grp($lig->id_groupe, array('description', 'matieres'));
			$chaine_a_traduire = preg_replace("/__COURS__/", $info_grp, $chaine_a_traduire);

			// A FAIRE : PRENDRE EN COMPTE AUSSI UNE CHAINE __LIEN_EDT_ICAL__
			if ((getSettingAOui('active_edt_ical')) && ((getSettingAOui('EdtIcalEleve')) || (getSettingAOui('EdtIcalResponsable')))) {
				if (preg_match("/__LIEN_EDT_ICAL__/", $chaine_a_traduire)) {
					//$num_semaine_annee=sprintf("%02d", strftime("%V", $ts1))."|".strftime("%Y", $ts1);
					$num_semaine_annee = sprintf("%02d", id_num_semaine($ts1)) . "|" . strftime("%Y", $ts1);
					//$num_semaine_annee=strftime("%V", $ts1)."|".strftime("%Y", $ts1);
					$chaine_a_traduire = preg_replace("/__LIEN_EDT_ICAL__/", "<a href='$gepiPath/edt/index.php?mode=afficher_edt&type_edt=classe&id_classe=" . $lig->id_classe . "&num_semaine_annee=" . $num_semaine_annee . "'>Emploi du temps</a>", $chaine_a_traduire);

				}
			}

			$tab[$cpt]['texte_famille_traduit'] = $chaine_a_traduire;

			$cpt++;
		}
	}

	return $tab;
}

function get_tab_engagements($statut_concerne = "", $statut_saisie = "") {
	$tab_engagements = array();
	$tab_engagements['indice'] = array();
	$tab_engagements['id_engagement'] = array();

	$sql = "SELECT * FROM engagements";
	if (($statut_concerne != "") || ($statut_saisie != "")) {
		$sql_ajout = "";
		if ($statut_saisie == "scolarite") {
			if ($sql_ajout != "") {
				$sql_ajout .= " AND ";
			}
			$sql_ajout .= "SaisieScol='yes'";
		}
		if ($statut_saisie == "cpe") {
			if ($sql_ajout != "") {
				$sql_ajout .= " AND ";
			}
			$sql_ajout .= "SaisieCpe='yes'";
		}
		if ($statut_concerne == "eleve") {
			if ($sql_ajout != "") {
				$sql_ajout .= " AND ";
			}
			$sql_ajout .= "ConcerneEleve='yes'";
		}
		if ($statut_concerne == "responsable") {
			if ($sql_ajout != "") {
				$sql_ajout .= " AND ";
			}
			$sql_ajout .= "ConcerneResponsable='yes'";
		}

		if ($sql_ajout != "") {
			$sql .= " WHERE " . $sql_ajout;
		}
	}
	$sql .= " ORDER BY nom;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab_engagements['indice'][$cpt] = $lig;

			$tab_engagements['indice'][$cpt]['effectif'] = 0;
			//$sql="SELECT 1=1 FROM engagements_user WHERE id_engagement='".$lig['id']."';";
			$sql = "SELECT 1=1 FROM engagements_user WHERE id_engagement='" . $lig['id'] . "' AND id_type='" . $lig['type'] . "';";
			$res_eff = mysqli_query($GLOBALS["mysqli"], $sql);
			$tab_engagements['indice'][$cpt]['effectif'] = mysqli_num_rows($res_eff);

			$tab_engagements['id_engagement'][$lig['id']] = $lig;
			$tab_engagements['id_engagement'][$lig['id']]['effectif'] = mysqli_num_rows($res_eff);

			$tab_engagements['indice'][$cpt]['droit_user'] = array();
			$tab_engagements['id_engagement'][$lig['id']]['droit_user'] = array();

			$sql = "SELECT * FROM engagements_droit_saisie WHERE id_engagement='" . $lig['id'] . "';";
			$res_droit = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res_droit)) {
				while ($lig_droit = mysqli_fetch_object($res_droit)) {
					$tab_engagements['indice'][$cpt]['droit_user'][] = $lig_droit->login;

					$tab_engagements['id_engagement'][$lig['id']]['droit_user'][] = $lig_droit->login;
				}
			}

			$sql = "SELECT * FROM engagements_droit_saisie WHERE id_engagement='" . $lig['id'] . "';";
			//echo "$sql<br />";
			$res2 = mysqli_query($GLOBALS["mysqli"], $sql);
			while ($lig2 = mysqli_fetch_object($res2)) {
				$tab_engagements['indice'][$cpt]["droit_special"][] = $lig2->login;
			}

			$cpt++;
		}
		/*
		echo "<pre>";
		print_r($tab_engagements);
		echo "</pre>";
		*/
	}
	return $tab_engagements;
}

function get_tab_engagements_user($login_user = "", $id_classe = '', $statut_concerne = "", $id_groupe = '') {
	/*
	global $tab_engagements;

	if((!is_array($tab_engagements))||(count($tab_engagements)==0)) {
		$tab_engagements=get_tab_engagements();
	}
	*/

	$tab_engagements_user = array();
	$tab_engagements_user['indice'] = array();
	$tab_engagements_user['login_user'] = array();
	$tab_engagements_user['id_engagement'] = array();
	$tab_engagements_user['id_engagement_user'] = array();
	//$sql="SELECT eu.*, e.nom AS nom_engagement, e.description AS engagement_description, e.type, e.conseil_de_classe, e.code AS code_engagement FROM engagements_user eu, engagements e WHERE eu.id_engagement=e.id";
	$sql = "SELECT eu.*, 
			e.nom AS nom_engagement, 
			e.description AS engagement_description, 
			e.type, 
			e.conseil_de_classe, 
			e.code AS code_engagement 
		FROM engagements_user eu, 
			engagements e 
		WHERE eu.id_engagement=e.id AND 
			eu.id_type=e.type";

	if ($login_user != "") {
		$sql .= " AND eu.login='" . $login_user . "'";
	}

	if ($id_classe != "") {
		$sql .= " AND eu.id_type='id_classe' AND valeur='" . $id_classe . "'";
	}

	if ($statut_concerne == "eleve") {
		$sql .= " AND e.ConcerneEleve='yes'";
	} elseif ($statut_concerne == "responsable") {
		$sql .= " AND e.ConcerneResponsable='yes'";
	}

	if ($id_groupe != '') {
		$sql .= " AND eu.id_type='id_classe' AND valeur IN (SELECT DISTINCT id_classe FROM j_groupes_classes WHERE id_groupe='" . $id_groupe . "')";
	}
	$sql .= ";";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt = 0;
	while ($lig = mysqli_fetch_assoc($res)) {
		$tab_engagements_user['indice'][$cpt] = $lig;
		$tab_engagements_user['login_user'][$lig['login']][] = $cpt;
		$tab_engagements_user['id_engagement'][$lig['id_engagement']][] = $cpt;
		$tab_engagements_user['id_engagement_user'][$lig['id_engagement']][] = $lig['login'];
		$cpt++;
	}

	return $tab_engagements_user;
}

function get_tab_login_tel_engagement($id_engagement, $id_classe = "", $statut = "") {
	/*
	global $tab_engagements;

	if((!is_array($tab_engagements))||(count($tab_engagements)==0)) {
		$tab_engagements=get_tab_engagements();
	}
	*/

	$tab_user = array();
	if ($id_classe == "") {
		if ($statut == "eleve") {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu, eleves e WHERE eu.id_engagement='$id_engagement' AND eu.login=e.login;";
		} elseif ($statut == "responsable") {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu, resp_pers rp WHERE eu.id_engagement='$id_engagement' AND rp.login=eu.login;";
		} else {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu WHERE eu.id_engagement='$id_engagement';";
		}
	} else {
		if ($statut == "eleve") {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu, j_eleves_classes jec WHERE eu.id_engagement='$id_engagement' AND eu.login=jec.login AND jec.id_classe='$id_classe';";
		} elseif ($statut == "responsable") {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu, 
									j_eleves_classes jec, 
									eleves e, 
									responsables2 r, 
									resp_pers rp 
								WHERE eu.id_engagement='$id_engagement' AND 
									eu.login=rp.login AND 
									rp.pers_id=r.pers_id AND 
									r.ele_id=e.ele_id AND 
									e.login=jec.login AND 
									jec.id_classe='$id_classe';";
		} else {
			$sql = "SELECT DISTINCT eu.login FROM engagements_user eu, j_eleves_classes jec WHERE eu.id_engagement='$id_engagement' AND eu.login=jec.login AND jec.id_classe='$id_classe';";
		}
	}
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt = 0;
	while ($lig = mysqli_fetch_assoc($res)) {
		$tab_user[$cpt] = $lig['login'];
		$cpt++;
	}

	return $tab_user;
}

function get_tab_engagements_pages() {

	$tab_engagements_pages = array();
	$tab_engagements_pages['indice'] = array();
	$tab_engagements_pages['id_type'] = array();
	$tab_engagements_pages['page'] = array();
	$sql = "SELECT * FROM engagements_pages ORDER BY id_type;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	$cpt = 0;
	while ($lig = mysqli_fetch_assoc($res)) {
		$tab_engagements_pages['indice'][$cpt] = $lig;
		$tab_engagements_pages['id_type'][$lig['id_type']]["cpt"][] = $cpt;
		$tab_engagements_pages['id_type'][$lig['id_type']]["pages"][] = $lig['page'];
		$tab_engagements_pages['page'][$lig['page']]["cpt"][] = $cpt;
		$tab_engagements_pages['page'][$lig['page']]["types"][] = $lig['id_type'];
		$cpt++;
	}
	/*
	echo "<pre>";
	print_r($tab_engagements_pages);
	echo "</pre>";
	*/
	return $tab_engagements_pages;
}

function get_tab_engagements_telle_page($page) {

	$tab_engagements_pages = array();
	$sql = "SELECT * FROM engagements_pages WHERE page='" . $page . "' ORDER BY id_type;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	while ($lig = mysqli_fetch_object($res)) {
		$tab_engagements_pages[] = $lig->id_type;
	}

	return $tab_engagements_pages;
}

function get_tab_engagements_pages_tel_id_type($id_type) {

	$tab_engagements_pages = array();
	$sql = "SELECT * FROM engagements_pages WHERE id_type='" . $id_type . "' ORDER BY page;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	while ($lig = mysqli_fetch_object($res)) {
		$tab_engagements_pages[] = $lig->page;
	}

	return $tab_engagements_pages;
}

function is_delegue_conseil_classe($login_user, $id_classe = "") {
	$sql = "SELECT 1=1 FROM engagements e, 
					engagements_user eu 
				WHERE e.id=eu.id_engagement AND 
					e.conseil_de_classe='yes' AND 
					eu.login='" . $login_user . "' AND 
					e.type='id_classe' AND 
					eu.id_type='id_classe'";
	if ($id_classe != "") {
		$sql .= " AND eu.valeur='" . $id_classe . "'";
	}
	$sql .= ";";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		return true;
	} else {
		return false;
	}
}


function get_tab_engagements_droit_saisie_tel_user($login_user) {

	$tab_engagements_user = array();
	$tab_engagements_user['indice'] = array();
	$tab_engagements_user['login_user'] = array();
	$tab_engagements_user['id_engagement'] = array();
	$tab_engagements_user['id_engagement_user'] = array();

	$statut = get_valeur_champ("utilisateurs", "login='" . $login_user . "'", "statut");

	$cpt = 0;
	if ($statut == "administrateur") {
		$sql = "SELECT * FROM engagements ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab_engagements_user['indice'][$cpt] = $lig;
			$tab_engagements_user['id_engagement'][$lig['id']] = $cpt;
			$cpt++;
		}
	} elseif ($statut == "scolarite") {
		$sql = "SELECT * FROM engagements WHERE SaisieScol='yes' ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab_engagements_user['indice'][$cpt] = $lig;
			$tab_engagements_user['id_engagement'][$lig['id']] = $cpt;
			$cpt++;
		}
	} elseif ($statut == "cpe") {
		$sql = "SELECT * FROM engagements WHERE SaisieCpe='yes' ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab_engagements_user['indice'][$cpt] = $lig;
			$tab_engagements_user['id_engagement'][$lig['id']] = $cpt;
			$cpt++;
		}
	} elseif (($statut == "professeur") && (is_pp($login_user))) {
		$sql = "SELECT * FROM engagements WHERE SaisiePP='yes' ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab_engagements_user['indice'][$cpt] = $lig;
			$tab_engagements_user['id_engagement'][$lig['id']] = $cpt;

			// Accès limité aux classes dont le prof est PP:
			// Récupérer la liste des classes dont le prof est PP
			$tmp_tab = get_tab_prof_suivi("", $login_user);
			for ($loop = 0; $loop < count($tmp_tab); $loop++) {
				$tab_engagements_user['indice'][$cpt]["id_classe"][] = $tmp_tab[$loop];
			}

			$cpt++;
		}
	}

	$sql = "SELECT e.* FROM engagements_droit_saisie eds, engagements e WHERE eds.id_engagement=e.id AND eds.login='" . $login_user . "' ORDER BY e.nom, e.description;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	while ($lig = mysqli_fetch_assoc($res)) {
		if (!array_key_exists($lig['id'], $tab_engagements_user['id_engagement'])) {
			$tab_engagements_user['indice'][$cpt] = $lig;
			$tab_engagements_user['id_engagement'][$lig['id']] = $cpt;
			// Droit quelle que soit la classe:
			$tab_engagements_user['indice'][$cpt]["droit_special"] = "y";
			// Au moins un droit sur toutes les classes
			$tab_engagements_user["droit_special"] = "y";
		}
		$cpt++;
	}

	return $tab_engagements_user;
}

/*
function acces_saisie_engagement($id_engagement, $id_classe) {
	global $tab_engagements_avec_droit_saisie;

// A MODIFIER : Il faut voir si l'utilisateur a un engagement particulier non lié à la classe
// issset($tab_engagements_user['indice'][$cpt]["droit_special"])









	if(count($tab_engagements_avec_droit_saisie)==0) {
		$tab_engagements_avec_droit_saisie=get_tab_engagements_droit_saisie_tel_user($_SESSION['login']);
	}

	if(array_key_exists($id_engagement, $tab_engagements_avec_droit_saisie["id_engagement"])) {
		return true;
	}
	else {
		return false;
	}
}
*/

/** Fonction destinée à copier les images de ../documents/archives/etablissement/cl1234/XXX vers le CDT courant quand des copier/coller sont faits depuis des archives
 *  Sans cela, les chemins des images sont incorrects quand on archive les CDT l'année suivante, et les images risquent de disparaitre du CDT courant si l'archive dont elles viennent est supprimée.
 *
 * @param string $texte Le texte à traiter
 * @return string La chaine corrigée
 */
function cdt_copie_fichiers_archive_vers_cdt_courant($texte, $type_notice, $id_groupe) {
	global $multisite;

	$contenu_cor = $texte;

	$url_racine_gepi = trim(preg_replace("#/$#", '', getSettingValue('url_racine_gepi')));
	$url_racine_gepi_interne = trim(preg_replace("#/$#", '', getSettingValue('url_racine_gepi_interne')));

	preg_match_all('# src="\.\./documents/archives/[^"]*"| src="' . $url_racine_gepi_interne . '/documents/archives/[^"]*"| src="' . $url_racine_gepi . '/documents/archives/[^"]*"#', $contenu_cor, $tab_match);

	/*
	$f=fopen("../backup/contenu_cdt.txt", "a+");
	foreach($tab_match as $key => $value) {
		if(is_array($value)) {
			foreach($value as $key2 => $value2) {
				fwrite($f, "\$tab_match[$key][$key2]=$value2\n");
			}
		}
		else {
			fwrite($f, "\$tab_match[$key]=$value\n");
		}
	}
	fclose($f);
	*/

	if (count($tab_match) > 0) {
		// Dans le cas où une même image est plusieurs fois dans la même notice:
		$tab_deja = array();
		for ($loop = 0; $loop < count($tab_match[0]); $loop++) {
			// Récupérer le nom du fichier
			//echo "\$tab_match[0][$loop]=".$tab_match[0][$loop]."<br />\n";
			$tmp_tab = explode('"', $tab_match[0][$loop]);
			$chemin_fichier_src = $tmp_tab[1];
			$fichier_src = basename($chemin_fichier_src);

			if (!in_array($chemin_fichier_src, $tab_deja)) {
				// Créer le dossier documents du CDT courant s'il n'existe pas
				$dossier_dest = "../documents/";

				$multi = (isset($multisite) && $multisite == 'y') ? $_COOKIE['RNE'] . '/' : NULL;
				if ((isset($multisite) && $multisite == 'y') && is_dir('../documents/' . $multi) === false) {
					@mkdir('../documents/' . $multi);
					$dossier_dest .= $multi;
				} elseif ((isset($multisite) && $multisite == 'y')) {
					$dossier_dest .= $multi;
				}

				if ($type_notice == "devoir") {
					$dossier_dest .= "cl_dev" . $id_groupe;
				} else {
					$dossier_dest .= "cl" . $id_groupe;
				}

				if (!is_dir($dossier_dest)) {
					@mkdir($dossier_dest);
				}

				// Recherche d'un nom de fichier non utilisé
				$fichier_dest = $fichier_src;
				$cpt = 0;
				$limite = 100;
				while (($cpt < $limite) && (file_exists($dossier_dest . "/" . $fichier_dest))) {
					$fichier_dest = time() . $fichier_src;
					$cpt++;
				}
				$chemin_fichier_dest = $dossier_dest . "/" . $fichier_dest;

				/*
				$f=fopen("../backup/contenu_cdt.txt", "a+");
				fwrite($f, "\n");
				fwrite($f, "\$chemin_fichier_src=$chemin_fichier_src\n");
				$chemin_fichier_src_chemin_copie=preg_replace("#^$url_racine_gepi_interne#", "..", $chemin_fichier_src);
				fwrite($f, "\$chemin_fichier_src_chemin_copie=$chemin_fichier_src_chemin_copie\n");
				$chemin_fichier_src_chemin_copie=preg_replace("#^$url_racine_gepi#", "..", $chemin_fichier_src_chemin_copie);
				fwrite($f, "\$chemin_fichier_src_chemin_copie=$chemin_fichier_src_chemin_copie\n\n");
				fclose($f);
				*/
				$chemin_fichier_src_chemin_copie = preg_replace("#^$url_racine_gepi_interne#", "..", $chemin_fichier_src);
				$chemin_fichier_src_chemin_copie = preg_replace("#^$url_racine_gepi#", "..", $chemin_fichier_src_chemin_copie);

				// Copier le fichier
				if ((file_exists($chemin_fichier_src_chemin_copie)) && (copy($chemin_fichier_src_chemin_copie, $chemin_fichier_dest))) {
					// Corriger la notice pour pointer sur le document du CDT courant
					$contenu_cor = preg_replace('| src="' . $chemin_fichier_src . '"|', ' src="' . $chemin_fichier_dest . '"', $contenu_cor);
				}

				$tab_deja[] = $chemin_fichier_src;

			}
		}
	}

	return $contenu_cor;
}

/** Fonction destinée à générer un tableau du contenu de la table 'salle_cours'
 *
 * @return array Tableau des salles avec indices primaires 'indice' et 'list'
 */
function get_tab_salle_cours() {
	$tab = array();

	$sql = "SELECT * FROM salle_cours ORDER BY numero_salle, nom_salle;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			$tab['indice'][$lig->id_salle]['id_salle'] = $lig->id_salle;
			$tab['indice'][$lig->id_salle]['numero_salle'] = $lig->numero_salle;
			$tab['indice'][$lig->id_salle]['nom_salle'] = $lig->nom_salle;

			if ($lig->numero_salle != "") {
				$designation_courte = $lig->numero_salle;
				$designation_complete = $lig->numero_salle;
			} else {
				$designation_courte = $lig->nom_salle;
			}

			if ($lig->nom_salle != "") {
				$designation_complete .= " (" . $lig->nom_salle . ")";
			}

			$tab['indice'][$lig->id_salle]['designation_courte'] = $designation_courte;
			$tab['indice'][$lig->id_salle]['designation_complete'] = $designation_complete;

			$tab['list'][$cpt]['id_salle'] = $lig->id_salle;
			$tab['list'][$cpt]['numero_salle'] = $lig->numero_salle;
			$tab['list'][$cpt]['nom_salle'] = $lig->nom_salle;
			$tab['list'][$cpt]['designation_courte'] = $designation_courte;
			$tab['list'][$cpt]['designation_complete'] = $designation_complete;
			$cpt++;
		}
	}

	return $tab;
}

/** Fonction destinée à générer un tableau du cotnenu de la table edt_creneaux
 *
 * @return array Tableau des créneaux avec indices primaires 'indice' et 'list'
 */
function get_tab_creneaux() {
	$tab = array();

	$sql = "SELECT * FROM edt_creneaux ORDER BY jour_creneau, heuredebut_definie_periode;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			$tab['indice'][$lig->id_definie_periode]['id_definie_periode'] = $lig->id_definie_periode;
			$tab['indice'][$lig->id_definie_periode]['nom_definie_periode'] = $lig->nom_definie_periode;
			$tab['indice'][$lig->id_definie_periode]['heuredebut_definie_periode'] = $lig->heuredebut_definie_periode;
			$tab['indice'][$lig->id_definie_periode]['heurefin_definie_periode'] = $lig->heurefin_definie_periode;
			$tab['indice'][$lig->id_definie_periode]['debut_court'] = substr($lig->heuredebut_definie_periode, 0, 5);
			$tab['indice'][$lig->id_definie_periode]['fin_court'] = substr($lig->heurefin_definie_periode, 0, 5);
			$tab['indice'][$lig->id_definie_periode]['type_creneaux'] = $lig->type_creneaux;
			$tab['indice'][$lig->id_definie_periode]['jour_creneau'] = $lig->jour_creneau;

			$tab['list'][$cpt]['id_definie_periode'] = $lig->id_definie_periode;
			$tab['list'][$cpt]['nom_definie_periode'] = $lig->nom_definie_periode;
			$tab['list'][$cpt]['heuredebut_definie_periode'] = $lig->heuredebut_definie_periode;
			$tab['list'][$cpt]['heurefin_definie_periode'] = $lig->heurefin_definie_periode;
			$tab['list'][$cpt]['debut_court'] = substr($lig->heuredebut_definie_periode, 0, 5);
			$tab['list'][$cpt]['fin_court'] = substr($lig->heurefin_definie_periode, 0, 5);
			$tab['list'][$cpt]['type_creneaux'] = $lig->type_creneaux;
			$tab['list'][$cpt]['jour_creneau'] = $lig->jour_creneau;

			$cpt++;
		}
	}

	return $tab;
}

function get_login_from_pers_id($pers_id) {
	global $mysqli;

	$retour = "";
	$sql = "SELECT login FROM resp_pers WHERE pers_id='$pers_id';";
	$res_user = mysqli_query($mysqli, $sql);
	if ($res_user->num_rows > 0) {
		$lig_user = $res_user->fetch_object();
		$retour = $lig_user->login;
		$res_user->close();
	}
	return $retour;
}

function get_pers_id_from_login($login_resp) {
	global $mysqli;

	$retour = "";
	$sql = "SELECT pers_id FROM resp_pers WHERE login='$login_resp';";
	$res_user = mysqli_query($mysqli, $sql);
	if ($res_user->num_rows > 0) {
		$lig_user = $res_user->fetch_object();
		$retour = $lig_user->pers_id;
		$res_user->close();
	}
	return $retour;
}

function retourne_sql_mes_classes() {
	if ($_SESSION['statut'] == 'cpe') {
		if (getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
			$sql = "SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c ORDER BY classe";
		} else {
			$sql = "SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c,j_eleves_cpe jec,j_eleves_classes jecl WHERE jec.cpe_login = '" . $_SESSION['login'] . "' AND jec.e_login=jecl.login AND jecl.id_classe=c.id ORDER BY c.classe";
		}
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$sql = "SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c, j_scol_classes jsc WHERE jsc.id_classe=c.id AND jsc.login='" . $_SESSION['login'] . "' ORDER BY classe";
	} elseif ($_SESSION['statut'] == 'professeur') {
		$sql = "SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c, j_groupes_classes jgc, j_groupes_professeurs jgp WHERE jgc.id_classe=c.id AND jgc.id_groupe=jgp.id_groupe AND jgp.login='" . $_SESSION['login'] . "' ORDER BY classe";
	} else {
		$sql = "SELECT DISTINCT c.id, c.id as id_classe, c.classe FROM classes c ORDER BY classe";
	}
	//echo "$sql<br />";
	return $sql;
}

function get_temoin_discipline_ele($ele_login, $date_depuis) {
	global $mysqli;

	$cpt = 0;

	$sql = "SELECT * FROM s_incidents si, s_protagonistes sp WHERE si.id_incident=sp.id_incident AND sp.login='$ele_login' AND date>'$date_depuis';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt += mysqli_num_rows($res);
	} else {
		$sql = "SELECT * FROM s_retenues sr, s_sanctions ss WHERE ss.id_sanction=sr.id_sanction AND login='$ele_login' AND sr.date>'$date_depuis';";
		//echo "$sql<br />";
		if (mysqli_num_rows($res) > 0) {
			$cpt += mysqli_num_rows($res);
		} else {
			$sql = "SELECT * FROM s_exclusions se, s_sanctions ss WHERE ss.id_sanction=se.id_sanction AND login='$ele_login' AND se.date_fin>='$date_depuis';;";
			//echo "$sql<br />";
			if (mysqli_num_rows($res) > 0) {
				$cpt += mysqli_num_rows($res);
			} else {
				$sql = "SELECT * FROM s_travail st, s_sanctions ss WHERE ss.id_sanction=st.id_sanction AND login='$ele_login' AND st.date_retour>='$date_depuis';";
				//echo "$sql<br />";
				if (mysqli_num_rows($res) > 0) {
					$cpt += mysqli_num_rows($res);
				}
			}
		}
	}

	return $cpt;
}

function get_temoin_discipline($date_depuis = "") {
	global $mysqli;

	$cpt = 0;

	if ($date_depuis == "") {
		$sql = "SELECT * FROM log WHERE LOGIN='" . $_SESSION['login'] . "' AND (AUTOCLOSE='0' OR AUTOCLOSE='1' OR AUTOCLOSE='2' OR AUTOCLOSE='3' OR AUTOCLOSE='10') AND START<'" . strftime("%Y-%m-%d %H:%M:%S", (time() - 3600 * 24)) . "' ORDER BY START DESC LIMIT 1;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$tmp_tab = explode(" ", $lig->START);
			$date_depuis = $tmp_tab[0];
		} else {
			$date_depuis = "1970-01-01";
		}
	}

	$tab_ele = array();
	if ($_SESSION['statut'] == "responsable") {
		$tmp_tab_ele = get_enfants_from_resp_login($_SESSION['login']);

		for ($loop = 0; $loop < count($tmp_tab_ele); $loop += 2) {
			$tab_ele[] = $tmp_tab_ele[$loop];
		}
	} else {
		$tab_ele[] = $_SESSION['login'];
	}

	for ($loop = 0; $loop < count($tab_ele); $loop++) {
		$cpt += get_temoin_discipline_ele($tab_ele[$loop], $date_depuis);
	}

	return $cpt;
}

function get_temoin_discipline_personnel($date_depuis = "") {
	global $mysqli;

	$cpt = 0;

	if ($date_depuis == "") {
		$sql = "SELECT * FROM log WHERE LOGIN='" . $_SESSION['login'] . "' AND (AUTOCLOSE='0' OR AUTOCLOSE='1' OR AUTOCLOSE='2' OR AUTOCLOSE='3' OR AUTOCLOSE='10') AND START<'" . strftime("%Y-%m-%d %H:%M:%S", (time() - 3600 * 24)) . "' ORDER BY START DESC LIMIT 1;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$tmp_tab = explode(" ", $lig->START);
			$date_depuis = $tmp_tab[0];
		} else {
			$date_depuis = "1970-01-01";
		}
	}

	if ($_SESSION['statut'] == 'professeur') {
		if (((getSettingAOui('visuDiscProfClasses')) || (getSettingAOui('visuDiscProfGroupes'))) &&
			(getPref($_SESSION['login'], 'DiscTemoinIncidentProf', "n") == "y")) {

			if (getSettingAOui('visuDiscProfClasses')) {
				$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
									s_protagonistes sp, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									j_eleves_classes jec
								WHERE si.id_incident=sp.id_incident AND 
									si.date>'$date_depuis' AND 
									sp.login=jec.login AND 
									jec.id_classe=jgc.id_classe AND 
									jgc.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
			} else {
				$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
									s_protagonistes sp, 
									j_eleves_groupes jeg, 
									j_groupes_professeurs jgp
								WHERE si.id_incident=sp.id_incident AND 
									si.date>'$date_depuis' AND 
									sp.login=jeg.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
			}
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		} elseif (getPref($_SESSION['login'], 'DiscTemoinIncidentPP', "n") == "y") {
			$tab_pp = get_tab_ele_clas_pp($_SESSION['login']);
			for ($loop = 0; $loop < count($tab_pp['id_classe']); $loop++) {
				$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
									s_protagonistes sp, 
									j_eleves_classes jec
								WHERE si.id_incident=sp.id_incident AND 
									si.date>'$date_depuis' AND 
									sp.login=jec.login AND 
									jec.id_classe='" . $tab_pp['id_classe'][$loop] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				$cpt += mysqli_num_rows($res);
			}
		}
	} elseif ($_SESSION['statut'] == 'cpe') {
		if (getPref($_SESSION['login'], 'DiscTemoinIncidentCpeTous', "n") == "y") {
			$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
								s_protagonistes sp
							WHERE si.id_incident=sp.id_incident AND 
								si.date>'$date_depuis';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		} elseif (getPref($_SESSION['login'], 'DiscTemoinIncidentCpe', "n") == "y") {
			$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
								s_protagonistes sp, 
								j_eleves_cpe jecpe
							WHERE si.id_incident=sp.id_incident AND 
								si.date>'$date_depuis' AND 
								sp.login=jecpe.e_login AND 
								jecpe.cpe_login='" . $_SESSION['login'] . "';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		}
	} elseif ($_SESSION['statut'] == 'scolarite') {
		if (getPref($_SESSION['login'], 'DiscTemoinIncidentScolTous', "n") == "y") {
			$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
								s_protagonistes sp
							WHERE si.id_incident=sp.id_incident AND 
								si.date>'$date_depuis';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		} elseif (getPref($_SESSION['login'], 'DiscTemoinIncidentScol', "n") == "y") {
			$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
								s_protagonistes sp, 
								j_eleves_classes jec,
								j_scol_classes jsc
							WHERE si.id_incident=sp.id_incident AND 
								si.date>'$date_depuis' AND 
								sp.login=jec.login AND 
								jec.id_classe=jsc.id_classe AND 
								jsc.login='" . $_SESSION['login'] . "';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		}
	} elseif ($_SESSION['statut'] == 'administrateur') {
		if (getPref($_SESSION['login'], 'DiscTemoinIncidentAdmin', "n") == "y") {
			$sql = "SELECT DISTINCT si.id_incident FROM s_incidents si, 
								s_protagonistes sp
							WHERE si.id_incident=sp.id_incident AND 
								si.date>'$date_depuis';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			$cpt = mysqli_num_rows($res);
		}
	}

	return $cpt;
}

function acces_messagerie($statut_user) {
	if (!acces("/messagerie/index.php", $statut_user)) {
		return false;
	} else {
		// Traiter ici les statuts pour lesquels, il peut y avoir restriction du droit
		if (($_SESSION['statut'] == 'cpe') && (!getSettingAOui('GepiAccesPanneauAffichageCpe'))) {
			return false;
		} else {
			return true;
		}
	}
}

function acces_carnet_notes($statut_user) {
	if (!getSettingAOui('active_carnets_notes')) {
		return false;
	} elseif ($_SESSION['statut'] == 'administrateur') {
		return false;
	} elseif ($_SESSION['statut'] == 'responsable') {
		if (getSettingAOui('GepiAccesReleveParent')) {
			return true;
		} else {
			return false;
		}
	} elseif ($_SESSION['statut'] == 'eleve') {
		if (getSettingAOui('GepiAccesReleveEleve')) {
			return true;
		} else {
			return false;
		}
	} elseif ($_SESSION['statut'] == 'professeur') {
		return true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		return true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		return true;
	} else {
		// A vérifier: 'secours', cas particulier du staut 'autre'
		return false;
	}
}

function acces_moyenne_chaque_devoir_carnet_notes($statut_user) {
	if ($_SESSION['statut'] == 'responsable') {
		if (getSettingAOui('GepiAccesMoyClasseReleveParent')) {
			return true;
		} else {
			return false;
		}
	} elseif ($_SESSION['statut'] == 'eleve') {
		if (getSettingAOui('GepiAccesMoyClasseReleveEleve')) {
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function acces_moyenne_min_max_chaque_devoir_carnet_notes($statut_user) {
	if ($_SESSION['statut'] == 'responsable') {
		if (getSettingAOui('GepiAccesMoyMinClasseMaxReleveParent')) {
			return true;
		} else {
			return false;
		}
	} elseif ($_SESSION['statut'] == 'eleve') {
		if (getSettingAOui('GepiAccesMoyMinClasseMaxReleveEleve')) {
			return true;
		} else {
			return false;
		}
	} else {
		return true;
	}
}

function get_id_classe_ele_d_apres_date($login_eleve, $timestamp = "") {
	global $mysqli;
	$id_classe = "";

	if ($timestamp == "") {
		$timestamp = time();
	}

	$date_mysql = strftime("%Y-%m-%d %H:%M:%S", $timestamp);

	$sql = "SELECT p.id_classe FROM periodes p, 
						j_eleves_classes jec 
					WHERE p.id_classe=jec.id_classe AND 
						p.num_periode=jec.periode AND 
						jec.login='" . $login_eleve . "' AND 
						p.date_fin>='" . $date_mysql . "'
					ORDER BY date_fin ASC LIMIT 1;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$id_classe = $lig->id_classe;
	}

	return $id_classe;
}

function get_id_classe_derniere_classe_ele($login_eleve) {
	global $mysqli;
	$id_classe = "";

	$sql = "SELECT p.id_classe FROM periodes p, 
						j_eleves_classes jec 
					WHERE p.id_classe=jec.id_classe AND 
						jec.login='" . $login_eleve . "'
					ORDER BY date_fin DESC LIMIT 1;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$id_classe = $lig->id_classe;
	}

	return $id_classe;
}

/** Fonction destinée à tester si un utilisateur a le droit d'accéder aux incidents et sanctions de tel élève
 *
 * @param string $login_user Login de l'utilisateur
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function acces_incidents_disc_eleve($login_user, $statut_user, $login_eleve) {
	if ($statut_user == 'responsable') {
		if ((is_responsable($login_eleve, $login_user, "", "yy")) &&
			((getSettingAOui('visuRespDisc')) || (getSettingAOui('visuRespDiscNature')))) {
			return true;
		} else {
			return false;
		}
	} elseif ($statut_user == 'eleve') {
		if ((getSettingAOui('visuEleDisc')) || (getSettingAOui('visuEleDiscNature'))) {
			return true;
		} else {
			return false;
		}
	} elseif ($statut_user == 'administrateur') {
		return true;
	} elseif ($statut_user == 'scolarite') {
		return true;
	} elseif ($statut_user == 'cpe') {
		return true;
	} elseif ($statut_user == 'professeur') {
		if (((is_prof_classe_ele($login_user, $login_eleve)) && (getSettingAOui('visuDiscProfClasses'))) ||
			((is_prof_ele($login_user, $login_eleve)) && (getSettingAOui('visuDiscProfGroupes')))) {
			return true;
		} else {
			return false;
		}
	} else {
		// Cas 'autre' à voir
		return false;
	}
}


/** Fonction destinée à tester si un utilisateur a le droit d'accéder aux absences de tel élève
 *
 * @param string $login_user Login de l'utilisateur
 * @param string $login_eleve Login de l'élève
 *
 * @return boolean true/false
 */
function acces_abs_eleve($login_user, $statut_user, $login_eleve) {
	if ($statut_user == 'responsable') {
		if ((is_responsable($login_eleve, $login_user, "", "yy")) && (getSettingAOui('active_absences_parents'))) {
			return true;
		} else {
			return false;
		}
	} elseif ($statut_user == 'eleve') {
		return false;
	} elseif ($statut_user == 'administrateur') {
		return true;
	} elseif ($statut_user == 'scolarite') {
		return true;
	} elseif ($statut_user == 'cpe') {
		return true;
	} elseif ($statut_user == 'professeur') {
		return true;
	} else {
		// Cas 'autre' à voir
		return false;
	}
}

function is_eleve_classe($login_ele, $id_classe) {
	global $mysqli;
	$sql = "SELECT 1=1 FROM j_eleves_classes WHERE login='$login_ele' AND id_classe='$id_classe';";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows == 0) {
		return false;
	} else {
		$res->close();
		return true;
	}
}

/** Fonction destinée à retourner un message informant l'utilisateur que la version de Gepi a été mise à jour
 *
 * @return string Le message d'information/explication
 */
function afficher_message_nouvelle_version_gepi() {
	global $gepiVersion, $gepiPath;

	$retour = "";

	if ($_SESSION['statut'] == "administrateur") {
		$retour = "<div style='float:right; width:16px; margin:5px;' title='Supprimer ce message'><a href='$gepiPath/accueil.php?suppr_msg_chgt_version_gepi=y" . add_token_in_url() . "'><img src='$gepiPath/images/delete16.png' class='icone16' alt='Supprimer' /></a></div>
<p>Gepi a été mis à jour en version <strong>" . $gepiVersion . "</strong></p>

<p>De nouvelles fonctionnalités sont probablement proposées.<br />
Vous pouvez en consulter la liste dans le <a href='$gepiPath/a_lire.php?fichier=changelog.txt#affichage_fichier'>changelog</a></p>

<p>De nouveaux droits ont pu être ajoutés avec cette nouvelle version.<br />
Consultez la liste des droits dans <a href='$gepiPath/gestion/droits_acces.php'>Droits d'accès</a> pour adapter le comportement de Gepi aux préférences de l'établissement.</p>

<p>Vous pouvez définir certains choix personnels dans <a href='$gepiPath/utilisateurs/mon_compte.php'><img src='$gepiPath/images/icons/buddy.png' class='icone16' alt='Mon compte' /> Gérer mon compte</a></p>";
	} elseif (in_array($_SESSION['statut'], array("professeur", "scolarite", "cpe"))) {
		$retour = "<div style='float:right; width:16px; margin:5px;' title='Supprimer ce message'><a href='$gepiPath/accueil.php?suppr_msg_chgt_version_gepi=y" . add_token_in_url() . "'><img src='$gepiPath/images/delete16.png' class='icone16' alt='Supprimer' /></a></div>
<p>Gepi a été mis à jour en version <strong>" . $gepiVersion . "</strong></p>
<p>De nouvelles fonctionnalités sont probablement proposées.<br />
Votre administrateur a peut-être aussi activé de nouveaux modules.<br />
Vous pouvez définir certains choix personnels dans <a href='$gepiPath/utilisateurs/mon_compte.php'><img src='$gepiPath/images/icons/buddy.png' class='icone16' alt='Mon compte' /> Gérer mon compte</a></p>";
	}

	return $retour;
}

/** Fonction destinée à retourner la dénomination gepi_prof_suivi adaptée à la classe
 *
 * @param integer $id_classe Identifiant de classe
 *
 * @return string La dénomination gepi_prof_suivi
 */
function retourne_denomination_pp($id_classe) {
	$gepi_prof_suivi = getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));
	if ($gepi_prof_suivi == "") {
		$gepi_prof_suivi = "professeur principal";
	}
	return $gepi_prof_suivi;
}

//=========================================
function ajout_bouton_supprimer_message($contenu_cor, $id_message) {
	global $gepiPath;

	$contenu_cor = '
	<form method="POST" action="' . $gepiPath . '/accueil.php" name="f_suppression_message">
	<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">
	<input type="hidden" name="supprimer_message" value="' . $id_message . '">
	<button type="submit" title=" Supprimer ce message " style="border: none; background: none; float: right;"><img style="vertical-align: bottom;" src="' . $gepiPath . '/images/icons/delete.png"></button>
	</form>' . $contenu_cor;
	$r_sql = "UPDATE messages SET texte='" . $contenu_cor . "' WHERE id='" . $id_message . "'";
	//echo htmlentities($r_sql)."<br />";
	return mysqli_query($GLOBALS["mysqli"], $r_sql) ? true : false;
}

function update_message($contenu_cor, $date_debut, $date_fin, $date_decompte, $statuts_destinataires, $login_destinataire) {
	$r_sql = "UPDATE messages
	SET texte = '" . $contenu_cor . "',
	date_debut = '" . $date_debut . "',
	date_fin = '" . $date_fin . "',
	date_decompte = '" . $date_decompte . "',
	auteur='" . $_SESSION['login'] . "',
	statuts_destinataires = '" . $statuts_destinataires . "',
	login_destinataire='" . $login_destinataire . "'
	WHERE id ='" . $_POST['id_mess'] . "'";
	//", matiere_destinataire='".$matiere_destinataire."'";
	return mysqli_query($GLOBALS["mysqli"], $r_sql) ? true : false;
}

function set_message($contenu_cor, $date_debut, $date_fin, $date_decompte, $statuts_destinataires, $login_destinataire) {
	$r_sql = "INSERT INTO messages
	SET texte = '" . $contenu_cor . "',
	date_debut = '" . $date_debut . "',
	date_fin = '" . $date_fin . "',
	date_decompte = '" . $date_decompte . "',
	auteur='" . $_SESSION['login'] . "',
	statuts_destinataires = '" . $statuts_destinataires . "',
	login_destinataire='" . $login_destinataire . "'";
	//$r_sql.=", matiere_destinataire='".$matiere_destinataire."'";
	//echo "$r_sql<br />";
	$retour = mysqli_query($GLOBALS["mysqli"], $r_sql) ? true : false;
	if ($retour) {
		$id_message = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		if (isset($_POST['suppression_possible']) && $_POST['suppression_possible'] == "oui" && $statuts_destinataires == "_")
			$retour = ajout_bouton_supprimer_message($contenu_cor, $id_message);
	}
	return $retour;
}

function set_message2($contenu_cor, $date_debut, $date_fin, $date_decompte, $statuts_destinataires, $login_destinataire) {
	$r_sql = "INSERT INTO messages
	SET texte = '" . $contenu_cor . "',
	date_debut = '" . $date_debut . "',
	date_fin = '" . $date_fin . "',
	date_decompte = '" . $date_decompte . "',
	auteur='" . $_SESSION['login'] . "',
	statuts_destinataires = '" . $statuts_destinataires . "',
	login_destinataire='" . $login_destinataire . "'";
	//$r_sql.=", matiere_destinataire='".$matiere_destinataire."'";
	//echo "$r_sql<br />";
	$retour = mysqli_query($GLOBALS["mysqli"], $r_sql) ? true : false;
	if ($retour) {
		$id_message = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
		$retour = $id_message;
	}
	return $retour;
}

//=========================================

function acces_info_dates_evenements() {
	if (!acces('/classes/info_dates_classes.php', $_SESSION['statut'])) {
		return false;
	} elseif ($_SESSION['statut'] == 'administrateur') {
		return true;
	} elseif (getSettingAOui('droit_informer_evenement_' . $_SESSION['statut'])) {
		return true;
	} else {
		return false;
	}
}

function get_tab_eleves_classe($id_classe) {
	$temp = array();
	$tab_per = array();
	$temp["eleves"]["all"]["list"] = array();
	$sql = "SELECT e.*, jec.id_classe, jec.periode, c.classe FROM eleves e, j_eleves_classes jec, classes c WHERE jec.id_classe='$id_classe' AND jec.id_classe=c.id AND jec.login=e.login ORDER BY periode, e.nom, e.prenom;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$temp["eleves"][$lig->periode]["list"][] = $lig->login;
			$temp["eleves"][$lig->periode]["users"][$lig->login] = array("login" => $lig->login, "nom" => $lig->nom, "prenom" => $lig->prenom, "id_classe" => $lig->id_classe, "classe" => $lig->classe, "sconet_id" => $lig->ele_id, "elenoet" => $lig->elenoet, "sexe" => $lig->sexe);
			if (!in_array($lig->periode, $tab_per)) {
				$tab_per[] = $lig->periode;
			}
		}

		for ($loop = 0; $loop < count($tab_per); $loop++) {
			foreach ($temp["eleves"][$tab_per[$loop]]["users"] as $current_login => $current_ele) {
				if (!in_array($current_login, $temp["eleves"]["all"]["list"])) {
					$temp["eleves"]["all"]["list"][] = $current_login;

					$temp["eleves"]["all"]["users"][$current_login] = $current_ele;
				}
			}
		}
	}
	return $temp;
}

function get_tab_type_pointage_discipline() {
	$tab = array();

	$sql = "SELECT * FROM sp_types_saisies ORDER BY rang, nom;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_object($res)) {
			$tab['id_type'][$lig->id_type]['id_type'] = $lig->id_type;
			$tab['id_type'][$lig->id_type]['nom'] = $lig->nom;
			$tab['id_type'][$lig->id_type]['description'] = $lig->description;
			$tab['id_type'][$lig->id_type]['rang'] = $lig->rang;

			$tab['indice'][$cpt]['id_type'] = $lig->id_type;
			$tab['indice'][$cpt]['nom'] = $lig->nom;
			$tab['indice'][$cpt]['description'] = $lig->description;
			$tab['indice'][$cpt]['rang'] = $lig->rang;
			$cpt++;
		}
	}

	return $tab;
}

function get_clas_ele_telle_date($login_ele, $mysql_date, $slash_date = "") {
	$tab = array();

	if ($mysql_date == "") {
		$mysqldate = get_mysql_date_from_slash_date($slash_date);
	}

	$sql = "SELECT c.id,c.classe FROM periodes p, 
						classes c, 
						j_eleves_classes jec 
					WHERE p.id_classe=c.id AND 
						jec.id_classe=c.id AND 
						jec.login='$login_ele' AND 
						p.date_fin>='$mysql_date' 
						ORDER BY date_fin DESC LIMIT 1";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$tab['id_classe'] = $lig->id;
		$tab['classe'] = $lig->classe;
	} else {
		// Chercher la dernière classe de l'élève?
		$sql = "SELECT c.id,c.classe FROM periodes p, 
						classes c, 
						j_eleves_classes jec 
					WHERE p.id_classe=c.id AND 
						jec.id_classe=c.id AND 
						jec.login='$login_ele' 
						ORDER BY date_fin DESC LIMIT 1";
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$tab['id_classe'] = $lig->id;
			$tab['classe'] = $lig->classe;
		}
	}

	return $tab;
}

function get_horaires_jour($jour = "") {
	$tab = array();

	if ($jour == "") {
		$sql = "SELECT * FROM horaires_etablissement WHERE jour_horaire_etablissement='lundi';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab[$lig['jour_horaire_etablissement']] = $lig;
			}
		}
	} else {
		$sql = "SELECT * FROM horaires_etablissement WHERE jour_horaire_etablissement='" . $jour . "';";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res) > 0) {
			$tab = mysqli_fetch_assoc($res);
		}
	}
	//$tmp_tab_jour=array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");

	return $tab;
}


function is_scol_classe_ele($login_scol, $login_ele, $num_periode = "") {
	global $mysqli;
	$is_scol_ele = false;

	$ajout_sql = "";
	if ($num_periode != "") {
		$ajout_sql = " AND periode='$num_periode'";
	}
	$sql = "SELECT 1=1 FROM j_eleves_classes jec, 
						j_scol_classes jsc
					WHERE jsc.login='" . $login_scol . "' AND 
						jec.id_classe=jsc.id_classe AND 
						jec.login='$login_ele'" . $ajout_sql . " LIMIT 1;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$is_scol_ele = true;
		$res->close();
	}

	return $is_scol_ele;
}

function is_scol_classe($login_scol, $id_classe) {
	global $mysqli;

	$is_scol_classe = false;

	$sql = "SELECT 1=1 FROM j_scol_classes jsc
					WHERE jsc.login='" . $login_scol . "' AND 
						jsc.id_classe='$id_classe' LIMIT 1;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$is_scol_classe = true;
		$res->close();
	}

	return $is_scol_classe;
}

function get_valeur_champ($table, $critere, $champ) {
	global $mysqli;

	$retour = "";
	$sql = "SELECT " . $champ . " FROM " . $table . " WHERE " . $critere;
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_array($res);
		$retour = $lig[0];
	}

	return $retour;
}

function my_file_get_contents($url) {
	global $gepiPath;

	if (file_exists($gepiPath . "/secure/proxy.php")) {
		require($gepiPath . "/secure/proxy.php");
		if ((isset($ProxyServer)) && (isset($ProxyPort))) {
			$tmp_context_proxy = array(
				'http' => array(
					'proxy' => "tcp:$ProxyServer:$ProxyPort",
					'request_fulluri' => true,
				),
				'https' => array(
					'proxy' => "tcp:$ProxyServer:$ProxyPort",
					'request_fulluri' => true,
				)
			);
			$context_proxy = stream_context_create($tmp_context_proxy);
		}
	}


	if (isset($context_proxy)) {
		$content = file_get_contents($url, false, $context_proxy);
	} else {
		$content = file_get_contents($url);
	}

	return $content;
}

/** Fonction destinée à insérer un target="_blank" dans les <a href>
 * pour passer outre les filtrages HTMLPurifier.
 *
 * @param string $texte Le texte à traiter
 * @param string $target La cible du lien (_blank par défaut)
 * @return string La chaine corrigée
 */
function a_href_target_blank($texte, $target = "_blank") {
	$contenu_cor = $texte;
	if (preg_match('|<a href=|i', $contenu_cor)) {
		$contenu_cor = preg_replace('|<a href=|i', '<a target="' . $target . '" href=', $contenu_cor);
	}
	return $contenu_cor;
}

/** Fonction destinée à indiquer si un utilisateur a le droit ou non d'uploader ses propres modèles
 *
 * @param string $login Le login de l'utilisateur
 * @param string $statut Statut de l'utilisateur
 * @return boolean Droit ou non d'uploader ses propres modèles
 */
function acces_upload_modele_ooo($login, $statut = "") {
	$retour = false;

	if ($statut == "") {
		$statut = get_valeur_champ("utilisateurs", "login='" . $login . "'", "statut");
	}

	if ($statut == 'administrateur') {
		$retour = true;
	} elseif (($statut == 'scolarite') && (getSettingAOui('OOoUploadScol'))) {
		$retour = true;
	} elseif (($statut == 'cpe') && (getSettingAOui('OOoUploadCpe'))) {
		$retour = true;
	} elseif (($statut == 'professeur') && (getSettingAOui('OOoUploadProf'))) {
		$retour = true;
	}

	if ((!$retour) && ($login != "")) {
		// On teste si un droit spécifique a été donné à cet utilisateur en particulier
		if (getPref($login, 'AccesOOoUpload', false)) {
			$retour = true;
		}
	}

	return $retour;
}

function get_tab_date_dernier_evenement_telle_classe($id_classe, $type) {
	$tab = array();

	$sql = "SELECT DISTINCT dde.id_ev, ddec.date_evenement, ddec.id_classe, ddec.id_salle, c.classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE ddec.id_ev=dde.id_ev AND ddec.date_evenement<=NOW() AND ddec.id_classe='" . $id_classe . "' AND dde.type='" . $type . "' AND c.id=ddec.id_classe ORDER BY date_evenement DESC LIMIT 1;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab = $lig;
			$tab['slashdate_ev'] = formate_date($lig['date_evenement']);
			$tab['slashdate_heure_ev'] = formate_date($lig['date_evenement'], 'y');
			$tab['lieu'] = get_infos_salle_cours($lig['id_salle']);
		}
	}
	return $tab;
}

function acces_extract_disc($id_classe = "", $login_ele = "") {
	if ((!getSettingAOui('active_mod_ooo')) || (!acces("/mod_discipline/mod_discipline_extraction_ooo.php", $_SESSION['statut']))) {
		return false;
	} else {
		if (($_SESSION['statut'] == 'administrateur') || ($_SESSION['statut'] == 'scolarite') || ($_SESSION['statut'] == 'cpe')) {
			return true;
		} elseif ($_SESSION['statut'] == 'professeur') {
			if (getSettingAOui('extractDiscProf')) {
				if ($login_ele != "") {
					if (is_prof_ele($_SESSION['login'], $login_ele)) {
						return true;
					} else {
						return false;
					}
				} elseif ($id_classe != "") {
					if (is_prof_classe($_SESSION['login'], $id_classe)) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			} elseif (getSettingAOui('extractDiscProfP')) {
				if ($login_ele != "") {
					if (is_pp($_SESSION['login'], "", $login_ele)) {
						return true;
					} else {
						return false;
					}
				} elseif ($id_classe != "") {
					if (is_pp($_SESSION['login'], $id_classe)) {
						return true;
					} else {
						return false;
					}
				} else {
					return true;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

function acces_validation_correction_app() {

	if ($_SESSION['statut'] == 'professeur') {
		if (!getSettingAOui('autoriser_valider_correction_app_pp')) {
			return false;
		} elseif (!is_pp($_SESSION['login'])) {
			return false;
		} else {
			return true;
		}
	} elseif (acces("/saisie/validation_corrections.php", $_SESSION['statut'])) {
		return true;
	} else {
		return false;
	}
}

/**
 * Retourne le tableau des informations sur l'établissement d'origine de l'élève
 *
 * @param string Login de l'élève
 * @return array Tableau des infos établissement
 */
function get_tab_etab_orig($login) {
	$tab = array();

	$tab["id"] = "";
	$tab["rne"] = "";
	$tab["nom"] = "";
	$tab["niveau"] = "";
	$tab["type"] = "";
	$tab["cp"] = "";
	$tab["ville"] = "";

	$sql = "SELECT et.* FROM etablissements et, j_eleves_etablissements jee, eleves e WHERE et.id=jee.id_etablissement AND jee.id_eleve=e.elenoet AND e.login='" . $login . "';";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab = mysqli_fetch_assoc($res);
		$tab["rne"] = $tab['id'];
	}

	$chaine_csv = $tab["rne"] . ";" . $tab["nom"] . ";" . $tab["niveau"] . ";" . $tab["type"] . ";" . $tab["cp"] . ";" . $tab["ville"];
	$chaine_csv2 = '"' . $tab["rne"] . '";"' . $tab["nom"] . '";"' . $tab["niveau"] . '";"' . $tab["type"] . '";"' . $tab["cp"] . '";"' . $tab["ville"] . '"';

	$tab["chaine_csv"] = $chaine_csv;
	$tab["chaine_csv2"] = $chaine_csv2;

	return $tab;
}

// renvoie la priorite d'affichage : 1:Retard Justifie ; 2 Absence Justifiee ; 3 Retard Non justifé ; 4 Absence non justifiée
function get_priorite($abs) {
	if ($abs->getJustifiee()) {
		if ($abs->getRetard()) {
			$priorite = 1;
		} else {
			$priorite = 2;
		}
	} else {
		if ($abs->getRetard()) {
			$priorite = 3;
		} else {
			$priorite = 4;
		}
	}
	return ($priorite);
}

// Si $id_classe est vide, il faut que $login_eleve soit non vide
function get_num_periode_d_apres_date($id_classe, $login_eleve = "", $timestamp = "") {
	global $mysqli;

	$num_periode = "";

	if ($timestamp == "") {
		$timestamp = time();
	}

	$date_mysql = strftime("%Y-%m-%d %H:%M:%S", $timestamp);

	if ($id_classe != "") {
		$sql = "SELECT p.num_periode FROM periodes p 
					WHERE p.id_classe='$id_classe' AND 
						p.date_fin>='" . $date_mysql . "'
					ORDER BY date_fin ASC LIMIT 1;";
	} else {
		$sql = "SELECT p.num_periode FROM periodes p, 
						j_eleves_classes jec 
					WHERE p.id_classe=jec.id_classe AND 
						p.num_periode=jec.periode AND 
						jec.login='" . $login_eleve . "' AND 
						p.date_fin>='" . $date_mysql . "'
					ORDER BY date_fin ASC LIMIT 1;";
	}
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$num_periode = $lig->num_periode;
	}

	return $num_periode;
}

function acces_saisie_voeux_orientation($id_classe = "") {
	$acces = "n";

	if (getSettingAOui("active_mod_orientation")) {
		if ($id_classe == "") {
			if (($_SESSION['statut'] == 'administrateur') ||
				(($_SESSION['statut'] == 'scolarite') && (getSettingAOui('OrientationSaisieVoeuxScolarite'))) ||
				(($_SESSION['statut'] == 'cpe') && (getSettingAOui('OrientationSaisieVoeuxCpe'))) ||
				(($_SESSION['statut'] == 'professeur') && (getSettingAOui('OrientationSaisieVoeuxPP')) && (is_pp($_SESSION['login'])))) {
				$acces = "y";
			}
		} else {
			if ($_SESSION['statut'] == "administrateur") {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "scolarite") && (getSettingAOui('OrientationSaisieVoeuxScolarite')) && (is_scol_classe($_SESSION['login'], $id_classe))) {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "cpe") && (getSettingAOui('OrientationSaisieVoeuxCpe')) && (is_cpe($_SESSION['login'], $id_classe))) {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "professeur") && (getSettingAOui('OrientationSaisieVoeuxPP')) && (is_pp($_SESSION['login'], $id_classe))) {
				$acces = "y";
			}
		}
	}
	if ($acces == "n") {
		return false;
	} else {
		return true;
	}
}

function acces_saisie_orientation($id_classe = "") {
	$acces = "n";

	if (getSettingAOui("active_mod_orientation")) {
		if ($id_classe == "") {
			if (($_SESSION['statut'] == 'administrateur') ||
				(($_SESSION['statut'] == 'scolarite') && (getSettingAOui('OrientationSaisieOrientationScolarite'))) ||
				(($_SESSION['statut'] == 'cpe') && (getSettingAOui('OrientationSaisieOrientationCpe'))) ||
				(($_SESSION['statut'] == 'professeur') && (getSettingAOui('OrientationSaisieOrientationPP')) && (is_pp($_SESSION['login'])))) {
				$acces = "y";
			}
		} else {
			if ($_SESSION['statut'] == "administrateur") {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "scolarite") && (getSettingAOui('OrientationSaisieOrientationScolarite')) && (is_scol_classe($_SESSION['login'], $id_classe))) {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "cpe") && (getSettingAOui('OrientationSaisieOrientationCpe')) && (is_cpe($_SESSION['login'], $id_classe))) {
				$acces = "y";
			} elseif (($_SESSION['statut'] == "professeur") && (getSettingAOui('OrientationSaisieOrientationPP')) && (is_pp($_SESSION['login'], $id_classe))) {
				$acces = "y";
			}
		}
	}

	if ($acces == "n") {
		return false;
	} else {
		return true;
	}
}

function acces_saisie_type_orientation() {
	$acces = "n";

	if (getSettingAOui("active_mod_orientation")) {
		if (($_SESSION['statut'] == 'administrateur') ||
			(($_SESSION['statut'] == 'scolarite') && (getSettingAOui('OrientationSaisieTypeScolarite'))) ||
			(($_SESSION['statut'] == 'cpe') && (getSettingAOui('OrientationSaisieTypeCpe'))) ||
			(($_SESSION['statut'] == 'professeur') && (getSettingAOui('OrientationSaisieTypePP')) && (is_pp($_SESSION['login'])))) {
			$acces = "y";
		}
	}

	if ($acces == "n") {
		return false;
	} else {
		return true;
	}

}

function get_tab_orientations_types_par_mef() {
	global $mysqli;
	// Orientations type saisies dans la bases
	$tab_orientation = array();
	//$tab_orientation2=array();

	if (getSettingAOui('active_mod_orientation')) {
		$sql = "SELECT oob.*, oom.mef_code FROM o_orientations_base oob, o_orientations_mefs oom WHERE oob.id=oom.id_orientation ORDER BY titre;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 0;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				$tab_orientation[$lig_o->mef_code]['id_orientation'][] = $lig_o->id;
				$tab_orientation[$lig_o->mef_code]['titre'][] = $lig_o->titre;
				$tab_orientation[$lig_o->mef_code]['description'][] = $lig_o->description;
				/*
				$tab_orientation2[$lig_o->id]['id_orientation']=$lig_o->id;
				$tab_orientation2[$lig_o->id]['titre']=$lig_o->titre;
				$tab_orientation2[$lig_o->id]['description']=$lig_o->description;
				*/
				$cpt++;
			}
		}
	}

	return $tab_orientation;
}

function get_tab_orientations_types() {
	global $mysqli;
	// Orientations type saisies dans la bases
	//$tab_orientation=array();
	$tab_orientation2 = array();

	if (getSettingAOui('active_mod_orientation')) {
		$sql = "SELECT oob.*, oom.mef_code FROM o_orientations_base oob, o_orientations_mefs oom WHERE oob.id=oom.id_orientation ORDER BY titre;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 0;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				/*
				$tab_orientation[$lig_o->mef_code]['id_orientation'][]=$lig_o->id;
				$tab_orientation[$lig_o->mef_code]['titre'][]=$lig_o->titre;
				$tab_orientation[$lig_o->mef_code]['description'][]=$lig_o->description;
				*/

				$tab_orientation2[$lig_o->id]['id_orientation'] = $lig_o->id;
				$tab_orientation2[$lig_o->id]['titre'] = $lig_o->titre;
				$tab_orientation2[$lig_o->id]['description'] = $lig_o->description;

				$cpt++;
			}
		}
	}

	return $tab_orientation2;
}

function get_tab_voeux_orientations_classe($id_classe) {
	global $mysqli;
	global $tab_orientation, $tab_orientation2;

	if (getSettingAOui('active_mod_orientation')) {

		if ((!isset($tab_orientation)) || (!is_array($tab_orientation))) {
			$tab_orientation = get_tab_orientations_types_par_mef();
			$tab_orientation2 = get_tab_orientations_types();
		} elseif ((!isset($tab_orientation2)) || (!is_array($tab_orientation2))) {
			$tab_orientation = get_tab_orientations_types_par_mef();
			$tab_orientation2 = get_tab_orientations_types();
		}

		// Extraire les voeux et orientations pour la classe courante
		$tab_orientation_classe_courante = array();

		$tab_voeux_ele = array();
		$sql = "SELECT DISTINCT ov.* FROM o_voeux ov, j_eleves_classes jec WHERE ov.login=jec.login AND jec.id_classe='" . $id_classe . "' ORDER BY ov.login, ov.rang;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$login_prec = "";
			$cpt = 1;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				if ($lig_o->login != $login_prec) {
					$cpt = 1;
					$login_prec = $lig_o->login;
				}
				$tab_voeux_ele[$lig_o->login][$cpt]['id_orientation'] = $lig_o->id_orientation;

				// Si le titre existe, le récupérer de $tab_orientation ou $tab_orientation2
				// Sinon, juste mettre le commentaire?
				if (isset($tab_orientation2[$lig_o->id_orientation])) {
					$tab_voeux_ele[$lig_o->login][$cpt]['designation'] = $tab_orientation2[$lig_o->id_orientation]['titre'];
					$tab_voeux_ele[$lig_o->login][$cpt]['description'] = $tab_orientation2[$lig_o->id_orientation]['description'];
				} else {
					// Proposer de ne pas faire apparaitre les voeux non listés dans la base si jamais on ouvre la saisie aux parents/élèves ou si on conserve des trucs à titre informatif, mais ne devant pas figurer sur le bulletin.
					$tab_voeux_ele[$lig_o->login][$cpt]['designation'] = $lig_o->commentaire;
					// Ou mettre "Autre orientation"
					$tab_voeux_ele[$lig_o->login][$cpt]['description'] = "";
				}

				$tab_voeux_ele[$lig_o->login][$cpt]['commentaire'] = $lig_o->commentaire;
				$tab_voeux_ele[$lig_o->login][$cpt]['rang'] = $lig_o->rang;
				$tab_voeux_ele[$lig_o->login][$cpt]['saisi_par'] = $lig_o->saisi_par;
				$tab_voeux_ele[$lig_o->login][$cpt]['saisi_par_cnp'] = civ_nom_prenom($lig_o->saisi_par);
				$tab_voeux_ele[$lig_o->login][$cpt]['date_voeu'] = formate_date($lig_o->date_voeu, "y");
				$cpt++;
			}
		}

		$tab_o_ele = array();
		$sql = "SELECT DISTINCT oo.* FROM o_orientations oo, j_eleves_classes jec WHERE oo.login=jec.login AND jec.id_classe='" . $id_classe . "' ORDER BY oo.login, oo.rang;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$login_prec = "";
			$cpt = 1;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				if ($lig_o->login != $login_prec) {
					$cpt = 1;
					$login_prec = $lig_o->login;
				}
				$tab_o_ele[$lig_o->login][$cpt]['id_orientation'] = $lig_o->id_orientation;

				// Si le titre existe, le récupérer de $tab_orientation ou $tab_orientation2
				// Sinon, juste mettre le commentaire?
				if (isset($tab_orientation2[$lig_o->id_orientation])) {
					$tab_o_ele[$lig_o->login][$cpt]['designation'] = $tab_orientation2[$lig_o->id_orientation]['titre'];
					$tab_o_ele[$lig_o->login][$cpt]['description'] = $tab_orientation2[$lig_o->id_orientation]['description'];
				} else {
					// Proposer de ne pas faire apparaitre les voeux non listés dans la base si jamais on ouvre la saisie aux parents/élèves ou si on conserve des trucs à titre informatif, mais ne devant pas figurer sur le bulletin.
					$tab_o_ele[$lig_o->login][$cpt]['designation'] = $lig_o->commentaire;
					// Ou mettre "Autre orientation"
					$tab_o_ele[$lig_o->login][$cpt]['description'] = "";
				}

				$tab_o_ele[$lig_o->login][$cpt]['commentaire'] = $lig_o->commentaire;
				$tab_o_ele[$lig_o->login][$cpt]['rang'] = $lig_o->rang;
				$tab_o_ele[$lig_o->login][$cpt]['saisi_par'] = $lig_o->saisi_par;
				$tab_o_ele[$lig_o->login][$cpt]['saisi_par_cnp'] = civ_nom_prenom($lig_o->saisi_par);
				$tab_o_ele[$lig_o->login][$cpt]['date_orientation'] = formate_date($lig_o->date_orientation, "y");
				$cpt++;
			}
		}

		$tab_avis_o_ele = array();
		$sql = "SELECT DISTINCT oa.* FROM o_avis oa, j_eleves_classes jec WHERE oa.login=jec.login AND jec.id_classe='" . $id_classe . "' ORDER BY oa.login;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 1;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				$tab_avis_o_ele[$lig_o->login] = $lig_o->avis;
			}
		}

		$tab_orientation_classe_courante['voeux'] = $tab_voeux_ele;
		$tab_orientation_classe_courante['orientation_proposee'] = $tab_o_ele;
		$tab_orientation_classe_courante['avis'] = $tab_avis_o_ele;
	}

	return $tab_orientation_classe_courante;
}

function get_tab_voeux_orientations_ele($login_ele) {
	global $mysqli;
	global $tab_orientation, $tab_orientation2;

	if (getSettingAOui('active_mod_orientation')) {

		if ((!isset($tab_orientation)) || (!is_array($tab_orientation))) {
			$tab_orientation = get_tab_orientations_types_par_mef();
			$tab_orientation2 = get_tab_orientations_types();
		} elseif ((!isset($tab_orientation2)) || (!is_array($tab_orientation2))) {
			$tab_orientation = get_tab_orientations_types_par_mef();
			$tab_orientation2 = get_tab_orientations_types();
		}

		// Extraire les voeux et orientations pour la classe courante
		$tab_orientation_eleve = array();

		$tab_voeux_ele = array();
		$sql = "SELECT DISTINCT ov.* FROM o_voeux ov WHERE ov.login='" . $login_ele . "' ORDER BY ov.rang;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 1;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				$tab_voeux_ele[$cpt]['id_orientation'] = $lig_o->id_orientation;

				// Si le titre existe, le récupérer de $tab_orientation ou $tab_orientation2
				// Sinon, juste mettre le commentaire?
				if (isset($tab_orientation2[$lig_o->id_orientation])) {
					$tab_voeux_ele[$cpt]['designation'] = $tab_orientation2[$lig_o->id_orientation]['titre'];
					$tab_voeux_ele[$cpt]['description'] = $tab_orientation2[$lig_o->id_orientation]['description'];
				} else {
					// Proposer de ne pas faire apparaitre les voeux non listés dans la base si jamais on ouvre la saisie aux parents/élèves ou si on conserve des trucs à titre informatif, mais ne devant pas figurer sur le bulletin.
					$tab_voeux_ele[$cpt]['designation'] = $lig_o->commentaire;
					// Ou mettre "Autre orientation"
					$tab_voeux_ele[$cpt]['description'] = "";
				}

				$tab_voeux_ele[$cpt]['commentaire'] = $lig_o->commentaire;
				$tab_voeux_ele[$cpt]['rang'] = $lig_o->rang;
				$tab_voeux_ele[$cpt]['saisi_par'] = $lig_o->saisi_par;
				$tab_voeux_ele[$cpt]['saisi_par_cnp'] = civ_nom_prenom($lig_o->saisi_par);
				$tab_voeux_ele[$cpt]['date_voeu'] = formate_date($lig_o->date_voeu, "y");
				$cpt++;
			}
		}

		$tab_o_ele = array();
		$sql = "SELECT DISTINCT oo.* FROM o_orientations oo WHERE oo.login='" . $login_ele . "' ORDER BY oo.rang;";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 1;
			while ($lig_o = mysqli_fetch_object($res_o)) {
				$tab_o_ele[$cpt]['id_orientation'] = $lig_o->id_orientation;

				// Si le titre existe, le récupérer de $tab_orientation ou $tab_orientation2
				// Sinon, juste mettre le commentaire?
				if (isset($tab_orientation2[$lig_o->id_orientation])) {
					$tab_o_ele[$cpt]['designation'] = $tab_orientation2[$lig_o->id_orientation]['titre'];
					$tab_o_ele[$cpt]['description'] = $tab_orientation2[$lig_o->id_orientation]['description'];
				} else {
					// Proposer de ne pas faire apparaitre les voeux non listés dans la base si jamais on ouvre la saisie aux parents/élèves ou si on conserve des trucs à titre informatif, mais ne devant pas figurer sur le bulletin.
					$tab_o_ele[$cpt]['designation'] = $lig_o->commentaire;
					// Ou mettre "Autre orientation"
					$tab_o_ele[$cpt]['description'] = "";
				}

				$tab_o_ele[$cpt]['commentaire'] = $lig_o->commentaire;
				$tab_o_ele[$cpt]['rang'] = $lig_o->rang;
				$tab_o_ele[$cpt]['saisi_par'] = $lig_o->saisi_par;
				$tab_o_ele[$cpt]['saisi_par_cnp'] = civ_nom_prenom($lig_o->saisi_par);
				$tab_o_ele[$cpt]['date_orientation'] = formate_date($lig_o->date_orientation, "y");
				$cpt++;
			}
		}

		$avis_o_ele = "";
		$sql = "SELECT DISTINCT * FROM o_avis oa WHERE oa.login='" . $login_ele . "';";
		//echo "$sql<br />";
		$res_o = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($res_o) > 0) {
			$cpt = 1;
			$lig_o = mysqli_fetch_object($res_o);
			$avis_o_ele = $lig_o->avis;
		}

		$tab_orientation_eleve['voeux'] = $tab_voeux_ele;
		$tab_orientation_eleve['orientation_proposee'] = $tab_o_ele;
		$tab_orientation_eleve['avis'] = $avis_o_ele;
	}

	return $tab_orientation_eleve;
}

function get_tab_mef_avec_proposition_orientation() {
	global $mysqli;

	$tab_mef = array();

	$sql = "SELECT m.* FROM o_mef om, mef m WHERE om.mef_code=m.mef_code AND om.affichage='y' ORDER BY libelle_edition, libelle_long, libelle_court;";
	//echo "$sql<br />";
	$res_mef = mysqli_query($mysqli, $sql);
	while ($lig_mef = mysqli_fetch_object($res_mef)) {
		$tab_mef[] = $lig_mef->mef_code;
	}

	return $tab_mef;
}

function mef_avec_proposition_orientation($id_classe = "", $mef_code = "", $login_eleve = "") {
	global $mysqli;

	$retour = false;

	if ($id_classe != "") {
		$sql = "SELECT 1=1 FROM o_mef om, j_eleves_classes jec, eleves e WHERE om.affichage='y' AND e.mef_code=om.mef_code AND e.login=jec.login AND jec.id_classe='" . $id_classe . "';";
		//echo "$sql<br />";
		$res_mef = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res_mef) > 0) {
			$retour = true;
		}
	} elseif ($mef_code != "") {
		$sql = "SELECT 1=1 FROM o_mef om WHERE om.mef_code='$mef_code' AND om.affichage='y';";
		//echo "$sql<br />";
		$res_mef = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res_mef) > 0) {
			$retour = true;
		}
	} elseif ($login_eleve != "") {
		$sql = "SELECT 1=1 FROM o_mef om, eleves e WHERE om.affichage='y' AND e.mef_code=om.mef_code AND e.login='" . $login_eleve . "';";
		//echo "$sql<br />";
		$res_mef = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res_mef) > 0) {
			$retour = true;
		}
	}

	/*
	$sql="SELECT m.* FROM o_mef om, mef m WHERE om.mef_code=m.mef_code AND om.affichage='y' ORDER BY libelle_edition, libelle_long, libelle_court;";
	$res_mef=mysqli_query($mysqli, $sql);
	while($lig_mef=mysqli_fetch_object($res_mef)) {
		$tab_mef[]=$lig_mef->mef_code;
	}
	*/

	return $retour;
}

function get_tab_acces_appreciations_ele($periode1, $periode2, $id_classe, $statut = '') {
	global $mysqli;
	global $delais_apres_cloture;
	global $date_ouverture_acces_app_classe;

	$tab_acces_app = array();

	$tab_ele = array();
	$sql = "SELECT * FROM j_eleves_classes WHERE id_classe='$id_classe';";
	$res_ele = mysqli_query($mysqli, $sql);
	while ($lig_ele = mysqli_fetch_object($res_ele)) {
		$tab_ele[$lig_ele->periode][] = $lig_ele->login;
	}

	if ($statut == "") {
		$statut = $_SESSION['statut'];
	}

	if (($statut == 'eleve') || ($statut == 'responsable')) {
		if (getSettingValue('acces_app_ele_resp') == 'periode_close') {
			$tmp_tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe, $statut);
			for ($i = $periode1; $i <= $periode2; $i++) {
				for ($loop = 0; $loop < count($tab_ele[$i]); $loop++) {
					$tab_acces_app[$i][$tab_ele[$i][$loop]] = $tmp_tab_acces_app[$i];
				}
			}
		} elseif (getSettingValue('acces_app_ele_resp') == 'date') {
			$tmp_tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe, $statut);
			for ($i = $periode1; $i <= $periode2; $i++) {
				for ($loop = 0; $loop < count($tab_ele[$i]); $loop++) {
					$tab_acces_app[$i][$tab_ele[$i][$loop]] = $tmp_tab_acces_app[$i];
				}
			}
		} elseif (getSettingValue('acces_app_ele_resp') == 'manuel_individuel') {
			// Ouverture manuelle élève par élève
			for ($i = $periode1; $i <= $periode2; $i++) {
				for ($loop = 0; $loop < count($tab_ele[$i]); $loop++) {
					$sql = "SELECT * FROM matieres_appreciations_acces_eleve WHERE login='" . $tab_ele[$i][$loop] . "' AND periode='" . $i . "';";
					//echo "$sql<br />";
					$res = mysqli_query($mysqli, $sql);
					if ($res) {
						if ($res->num_rows > 0) {
							$lig = $res->fetch_object();
							//echo "\$lig->acces=$lig->acces<br />";
							if ($lig->acces == "y") {
								$tab_acces_app[$i][$tab_ele[$i][$loop]] = "y";
							} else {
								$tab_acces_app[$i][$tab_ele[$i][$loop]] = "n";
							}
						} else {
							$tab_acces_app[$i][$tab_ele[$i][$loop]] = "n";
						}
						$res->close();
					} else {
						$tab_acces_app[$i][$tab_ele[$i][$loop]] = "n";
					}
				}
			}
		} else {
			// Ouverture manuelle
			$tmp_tab_acces_app = acces_appreciations($periode1, $periode2, $id_classe, $statut);
			for ($i = $periode1; $i <= $periode2; $i++) {
				if (isset($tab_ele[$i])) {
					for ($loop = 0; $loop < count($tab_ele[$i]); $loop++) {
						$tab_acces_app[$i][$tab_ele[$i][$loop]] = $tmp_tab_acces_app[$i];
					}
				}
			}
		}
	} else {
		// Pas de limitations d'accès pour les autres statuts.
		for ($i = $periode1; $i <= $periode2; $i++) {
			for ($loop = 0; $loop < count($tab_ele[$i]); $loop++) {
				$tab_acces_app[$i][$tab_ele[$i][$loop]] = "y";
			}
		}
	}
	return $tab_acces_app;
}

function get_tab_matieres_prof($login, $mode = "associees_a_un_groupe") {
	global $mysqli;
	$tab = array();

	if ($mode != "associees_a_un_groupe") {
		$sql = "SELECT DISTINCT m.matiere, m.nom_complet FROM matieres m, j_professeurs_matieres jpm WHERE id_professeur='" . $login . "' AND jpm.id_matiere=m.matiere ORDER BY jpm.ordre_matieres, m.matiere;";
	} else {
		$sql = "SELECT DISTINCT m.matiere, m.nom_complet FROM matieres m, j_professeurs_matieres jpm, j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE id_professeur='" . $login . "' AND jpm.id_professeur=jgp.login AND jpm.id_matiere=m.matiere AND jgm.id_matiere=jpm.id_matiere AND jgm.id_groupe=jgp.id_groupe ORDER BY jpm.ordre_matieres, m.matiere;";
	}
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab[$cpt] = $lig;
			$cpt++;
		}
	}

	return $tab;
}

function get_tab_modalites_election($mode = "indice") {
	$tab = array();

	$sql = "SELECT * FROM nomenclature_modalites_election;";
	//echo "$sql<br />";
	$res_nme = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_nme) > 0) {
		if ($mode == "indice") {
			$cpt = 0;
			while ($lig_nme = mysqli_fetch_assoc($res_nme)) {
				$tab[$cpt] = $lig_nme;
				$cpt++;
			}
		} else {
			while ($lig_nme = mysqli_fetch_assoc($res_nme)) {
				$tab[$lig_nme["code_modalite_elect"]] = $lig_nme;
			}
		}
	}

	return $tab;
}

function get_dates_debut_fin_classe_periode($id_classe, $num_periode, $mode = 1) {
	$tab = array();

	if ($id_classe == "") {
		$sql = "SELECT e.* FROM edt_calendrier e WHERE etabferme_calendrier='1' AND numero_periode='" . $num_periode . "';";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS['mysqli'], $sql);
		if (mysqli_num_rows($res) > 0) {
			// On s'aligne sur le premier enregistrement
			$lig = mysqli_fetch_object($res);
			$tab['debut']['ts'] = $lig->debut_calendrier_ts;
			$tab['debut']['mysql_date'] = $lig->jourdebut_calendrier;
			$tab['fin']['ts'] = $lig->fin_calendrier_ts;
			$tab['fin']['mysql_date'] = $lig->jourfin_calendrier;
		} else {
			// A améliorer parce que là, on évite juste le plantage
			$ts_debut = getSettingValue('begin_bookings');
			$tab['debut']['ts'] = $ts_debut;
			$tab['debut']['mysql_date'] = strftime("%Y-%m-%d", $ts_debut);

			$ts_fin = getSettingValue('end_bookings');
			$tab['fin']['ts'] = $ts_fin;
			$tab['fin']['mysql_date'] = strftime("%Y-%m-%d", $ts_fin);
		}
	} else {
		$sql = "SELECT e.* FROM periodes p, edt_calendrier e WHERE (classe_concerne_calendrier LIKE '%;$id_classe;%' OR classe_concerne_calendrier LIKE '$id_classe;%') AND etabferme_calendrier='1' AND numero_periode='" . $num_periode . "' AND p.nom_periode=e.nom_calendrier AND p.id_classe='$id_classe';";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS['mysqli'], $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$tab['debut']['ts'] = $lig->debut_calendrier_ts;
			$tab['debut']['mysql_date'] = $lig->jourdebut_calendrier;
			$tab['fin']['ts'] = $lig->fin_calendrier_ts;
			$tab['fin']['mysql_date'] = $lig->jourfin_calendrier;
		} else {
			if ($num_periode == 1) {
				$ts_debut = getSettingValue('begin_bookings');
			} else {
				$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='" . ($num_periode - 1) . "';";
				$res = mysqli_query($GLOBALS['mysqli'], $sql);
				if (mysqli_num_rows($res) > 0) {
					$lig = mysqli_fetch_object($res);
					$ts_debut = mysql_date_to_unix_timestamp($lig->date_fin);
				} else {
					// On ne devrait pas passer là
					$ts_debut = getSettingValue('begin_bookings');
				}
			}

			$tab['debut']['ts'] = $ts_debut;
			$tab['debut']['mysql_date'] = strftime("%Y-%m-%d", $ts_debut);

			if ($mode == 2) {
				$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='" . ($num_periode + 1) . "';";
				$res = mysqli_query($GLOBALS['mysqli'], $sql);
				if (mysqli_num_rows($res) == 0) {
					$ts_fin = getSettingValue('end_bookings');
				} else {
					$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='" . $num_periode . "';";
					$res = mysqli_query($GLOBALS['mysqli'], $sql);
					if (mysqli_num_rows($res) > 0) {
						$lig = mysqli_fetch_object($res);
						$ts_fin = mysql_date_to_unix_timestamp($lig->date_fin);
					} else {
						// Ca ne devrait pas arriver
						$ts_fin = getSettingValue('end_bookings');
					}
				}
			} else {
				$sql = "SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='" . $num_periode . "';";
				$res = mysqli_query($GLOBALS['mysqli'], $sql);
				if (mysqli_num_rows($res) > 0) {
					$lig = mysqli_fetch_object($res);
					$ts_fin = mysql_date_to_unix_timestamp($lig->date_fin);
				} else {
					// Ca ne devrait pas arriver
					$ts_fin = getSettingValue('end_bookings');
				}
			}

			$tab['fin']['ts'] = $ts_fin;
			$tab['fin']['mysql_date'] = strftime("%Y-%m-%d", $ts_fin);
		}
	}

	return $tab;
}

function get_date_debut_classe_periode($id_classe, $num_periode, $mode = 1) {
	$tab = get_dates_debut_fin_classe_periode($id_classe, $num_periode, $mode);
	$tab2 = $tab["debut"];
	return $tab2;
}

function get_date_fin_classe_periode($id_classe, $num_periode, $mode = 1) {
	$tab = get_dates_debut_fin_classe_periode($id_classe, $num_periode, $mode);
	$tab2 = $tab["fin"];
	return $tab2;
}

function get_tab_tag_cdt() {
	global $mysqli, $tab_drapeaux_tag_cdt;

	$tab_tag = array();
	$sql = "SELECT * FROM ct_tag_type ORDER BY nom_tag;";
	$res_tag = mysqli_query($mysqli, $sql);
	$loop = 0;
	//$loop_modif="";
	while ($lig_tag = mysqli_fetch_object($res_tag)) {
		$tab_tag["indice"][$loop]['id'] = $lig_tag->id;
		$tab_tag["indice"][$loop]['nom_tag'] = $lig_tag->nom_tag;
		$tab_tag["indice"][$loop]['drapeau'] = $lig_tag->drapeau;
		$tab_tag["indice"][$loop]['tag_compte_rendu'] = $lig_tag->tag_compte_rendu;
		$tab_tag["indice"][$loop]['tag_devoir'] = $lig_tag->tag_devoir;
		$tab_tag["indice"][$loop]['tag_notice_privee'] = $lig_tag->tag_notice_privee;

		$tab_tag["id"][$lig_tag->id]['id'] = $lig_tag->id;
		$tab_tag["id"][$lig_tag->id]['nom_tag'] = $lig_tag->nom_tag;
		$tab_tag["id"][$lig_tag->id]['drapeau'] = $lig_tag->drapeau;
		$tab_tag["id"][$lig_tag->id]['tag_compte_rendu'] = $lig_tag->tag_compte_rendu;
		$tab_tag["id"][$lig_tag->id]['tag_devoir'] = $lig_tag->tag_devoir;
		$tab_tag["id"][$lig_tag->id]['tag_notice_privee'] = $lig_tag->tag_notice_privee;

		$tab_tag["nom_tag"][$lig_tag->nom_tag]["id"] = $lig_tag->id;
		$tab_tag["nom_tag"][$lig_tag->nom_tag]["iindice"] = $loop;

		if ($lig_tag->tag_compte_rendu == "y") {
			$tab_tag["tag_compte_rendu"][$lig_tag->id]['id'] = $lig_tag->id;
			$tab_tag["tag_compte_rendu"][$lig_tag->id]['nom_tag'] = $lig_tag->nom_tag;
			$tab_tag["tag_compte_rendu"][$lig_tag->id]['drapeau'] = $lig_tag->drapeau;
		}

		if ($lig_tag->tag_devoir == "y") {
			$tab_tag["tag_devoir"][$lig_tag->id]['id'] = $lig_tag->id;
			$tab_tag["tag_devoir"][$lig_tag->id]['nom_tag'] = $lig_tag->nom_tag;
			$tab_tag["tag_devoir"][$lig_tag->id]['drapeau'] = $lig_tag->drapeau;
		}

		if ($lig_tag->tag_notice_privee == "y") {
			$tab_tag["tag_notice_privee"][$lig_tag->id]['id'] = $lig_tag->id;
			$tab_tag["tag_notice_privee"][$lig_tag->id]['nom_tag'] = $lig_tag->nom_tag;
			$tab_tag["tag_notice_privee"][$lig_tag->id]['drapeau'] = $lig_tag->drapeau;
		}

		$loop++;
	}

	return $tab_tag;
}

function get_tab_tag_notice($id_ct, $type_ct) {
	global $mysqli, $tab_drapeaux_tag_cdt, $tab_tag_type;

	if ((!isset($tab_tag_type)) || (!is_array($tab_tag_type)) || (count($tab_tag_type) == 0)) {
		$tab_tag_type = get_tab_tag_cdt();
	}

	$tab_tag_notice = array();
	$sql = "SELECT * FROM ct_tag WHERE id_ct='$id_ct' AND type_ct='$type_ct';";
	//echo "$sql<br />";
	$res_tag = mysqli_query($mysqli, $sql);
	$loop = 0;
	//$loop_modif="";
	while ($lig_tag = mysqli_fetch_object($res_tag)) {
		if (isset($tab_tag_type["id"][$lig_tag->id_tag])) {
			$tab_tag_notice['indice'][] = $tab_tag_type["id"][$lig_tag->id_tag];
			$tab_tag_notice['id'][] = $lig_tag->id_tag;
			// 20240111
			$tab_tag_notice['commentaire'][$lig_tag->id_tag] = $lig_tag->commentaire;
		} else {
			// Anomalie
			//echo "\$tab_tag_type[\"id\"][$lig_tag->id_tag] non défini.<br />";
		}
	}

	return $tab_tag_notice;
}

function get_stream_context() {
	$retour = "";

	$ip_proxy = getSettingValue('ip_proxy');
	$port_proxy = getSettingValue('port_proxy');
	if (($ip_proxy != "") && ($port_proxy != "")) {
		$opts = array(
			'http' => array('proxy' => $ip_proxy . ':' . $port_proxy, 'request_fulluri' => true),
			'ssl' => array('SNI_enabled' => false)
		);
		$retour = stream_context_create($opts);
	}

	return $retour;
}

function get_elements_programmes_ele($login_ele, $id_groupe, $periode) {
	global $mysqli;

	$tab = array();

	/*
	$sql="SELECT * FROM matiere_element_programme mep,
				j_mep_groupe jmg,
				j_mep_eleve jme,
				j_eleves_groupes jeg
			WHERE mep.id=jmg.idEP AND
				mep.id=jmp.idEP AND
				mep.id=jme.idEP AND
				jmg.idGroupe=jme.idGroupe AND
				jmg.idGroupe='".$id_groupe."' AND
				jme.periode='".$periode."' AND
				jmg.idGroupe=jeg.id_groupe AND
				jme.periode=jeg.periode AND
				jme.idEleve=jeg.login AND
				jme.idEleve='".$login_ele."'
			ORDER BY mep.libelle;";
	*/
	$sql = "SELECT * FROM matiere_element_programme mep, 
				j_mep_eleve jme
			WHERE mep.id=jme.idEP AND 
				jme.idGroupe='" . $id_groupe . "' AND 
				jme.periode='" . $periode . "' AND 
				jme.idEleve='" . $login_ele . "' 
			ORDER BY mep.libelle;";
	//echo "$sql<br />";
	// Faut-il trier par libelle ou par ordre de saisie? ajouter un paramètre à la fonction?
	$res = mysqli_query($mysqli, $sql);
	while ($lig = mysqli_fetch_object($res)) {
		$tab[] = $lig->libelle;
	}

	return $tab;
}

function get_elements_programmes_grp($id_groupe, $periode) {
	global $mysqli;

	$tab = array();

	/*
	$sql="SELECT * FROM matiere_element_programme mep,
				j_mep_groupe jmg,
				j_mep_eleve jme,
				j_eleves_groupes jeg
			WHERE mep.id=jmg.idEP AND
				mep.id=jmp.idEP AND
				mep.id=jme.idEP AND
				jeg.id_groupe=jme.idGroupe AND
				jeg.login=jme.idEleve AND
				jeg.id_groupe=jmg.idGroupe AND
				jeg.periode=jme.periode AND
				jmg.idGroupe='".$id_groupe."' AND
				jme.periode='".$periode."' AND
			ORDER BY mep.libelle;";
	*/
	$sql = "SELECT * FROM matiere_element_programme mep, 
				j_mep_eleve jme, 
				j_eleves_groupes jeg 
			WHERE mep.id=jme.idEP AND 
				jeg.id_groupe=jme.idGroupe AND 
				jeg.login=jme.idEleve AND 
				jeg.periode=jme.periode AND 
				jme.idGroupe='" . $id_groupe . "' AND 
				jme.periode='" . $periode . "' AND 
			ORDER BY mep.libelle;";
	//echo "$sql<br />";
	// Faut-il trier par libelle ou par ordre de saisie? ajouter un paramètre à la fonction?
	$res = mysqli_query($mysqli, $sql);
	while ($lig = mysqli_fetch_object($res)) {
		$tab['ele'][$lig->login][] = $lig->libelle;
		$tab['mep'][$lig->idEP][] = $lig->login;
	}

	return $tab;
}

function get_elements_programmes_classe($id_classe, $periode, $ordre = "mep.libelle") {
	global $mysqli;

	$tab = array();

	/*
	$sql="SELECT * FROM matiere_element_programme mep,
				j_mep_groupe jmg,
				j_mep_eleve jme,
				j_eleves_groupes jeg,
				j_groupes_classes jgc
			WHERE mep.id=jmg.idEP AND
				mep.id=jmg.idEP AND
				mep.id=jme.idEP AND
				jeg.id_groupe=jme.idGroupe AND
				jeg.id_groupe=jmg.idGroupe AND
				jeg.periode=jme.periode AND
				jgc.id_groupe=jmg.idGroupe AND
				jeg.login=jme.idEleve AND
				jgc.id_classe='".$id_classe."' AND
				jme.periode='".$periode."'
			ORDER BY mep.libelle;";
	*/
	$sql = "SELECT * FROM matiere_element_programme mep, 
				j_mep_eleve jme, 
				j_eleves_groupes jeg, 
				j_groupes_classes jgc 
			WHERE mep.id=jme.idEP AND 
				jeg.id_groupe=jme.idGroupe AND 
				jeg.periode=jme.periode AND 
				jgc.id_groupe=jme.idGroupe AND 
				jeg.login=jme.idEleve AND 
				jgc.id_classe='" . $id_classe . "' AND 
				jme.periode='" . $periode . "' 
			ORDER BY $ordre;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	while ($lig = mysqli_fetch_object($res)) {
		$tab['mep'][$lig->idEP][] = $lig->login;
		$tab['ele'][$lig->login][$lig->id_groupe][] = $lig->libelle;
	}

	return $tab;
}

function get_tab_saisie_abs2($id_saisie) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT * FROM a_saisies WHERE id='" . $id_saisie . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab = mysqli_fetch_assoc($res);
	}

	return $tab;
}

function get_tab_traitement_abs2($id_traitement) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT * FROM a_traitements WHERE id='" . $id_traitement . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$tab['traitement'] = mysqli_fetch_assoc($res);
		if (!is_null($tab['traitement']['a_type_id'])) {
			$sql = "SELECT * FROM a_types WHERE id='" . $tab['traitement']['a_type_id'] . "';";
			//echo "$sql<br />";
			$res2 = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res2) > 0) {
				$tab['traitement']['a_type_id'] = mysqli_fetch_assoc($res2);
			}
		}

		if (!is_null($tab['traitement']['a_motif_id'])) {
			$sql = "SELECT * FROM a_motifs WHERE id='" . $tab['traitement']['a_motif_id'] . "';";
			//echo "$sql<br />";
			$res2 = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res2) > 0) {
				$tab['traitement']['a_motif_id'] = mysqli_fetch_assoc($res2);
			}
		}

		if (!is_null($tab['traitement']['a_justification_id'])) {
			$sql = "SELECT * FROM a_justifications WHERE id='" . $tab['traitement']['a_justification_id'] . "';";
			//echo "$sql<br />";
			$res2 = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res2) > 0) {
				$tab['traitement']['a_justification_id'] = mysqli_fetch_assoc($res2);
			}
		}

		$sql = "SELECT a.* FROM j_traitements_saisies jts, a_saisies a WHERE jts.a_traitement_id='" . $id_traitement . "' AND a.id=jts.a_saisie_id ORDER BY a.debut_abs;";
		$res2 = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res2) > 0) {
			while ($lig = mysqli_fetch_assoc($res2)) {
				$tab['traitement']['saisies'][] = $lig;
			}
		}
	}

	return $tab;
}

function get_tab_types_groupe() {
	global $mysqli;

	$tab = array();

	if (!getSettingANon('AutoriserTypesEnseignements')) {
		$sql = "SELECT * FROM groupes_types;";
	} else {
		$sql = "SELECT * FROM groupes_types WHERE nom_court='local';";
	}
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab[] = $lig;
		}
	}
	return $tab;
}

function check_tables_modifiees() {
	global $mysqli;

	if (getSettingValue('version') == "1.6.9") {
		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM j_mep_eleve LIKE 'idGroupe';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE j_mep_eleve ADD idGroupe INT(11) NOT NULL default '0' AFTER idEleve;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);
		}

		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM matiere_element_programme LIKE 'id_user';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE matiere_element_programme ADD id_user VARCHAR(50) NOT NULL default '' COMMENT 'Auteur/proprio de l élément de programme' AFTER libelle;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);
		}

		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM j_mep_eleve LIKE 'date_insert';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE j_mep_eleve ADD date_insert DATETIME NOT NULL default '0000-00-00 00:00:00' AFTER periode;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);
		}
	}

	if ((getSettingValue('version') == "1.7.1") || (getSettingValue('version') == "master")) {
		// Ajouter le champ annee sur les tables socle_*

		$gepiYear = getSettingValue("gepiYear");
		$gepiYear_debut = mb_substr($gepiYear, 0, 4);
		if (!preg_match("/^20[0-9]{2}/", $gepiYear_debut)) {
			$gepiYear_debut = "";
		}

		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM socle_eleves_composantes LIKE 'annee';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE socle_eleves_composantes ADD annee varchar(10) NOT NULL default '' AFTER cycle;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);

			$sql = "SHOW INDEX FROM socle_eleves_composantes WHERE Key_name='ine';";
			$test = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test) > 0) {
				$sql = "ALTER TABLE socle_eleves_composantes DROP INDEX ine;";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			}

			$sql = "SHOW INDEX FROM socle_eleves_composantes WHERE Key_name='ine_cycle_id_composante_periode';";
			$test = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test) == 0) {
				$sql = "ALTER TABLE socle_eleves_composantes ADD INDEX ine_cycle_id_composante_periode(ine,cycle,code_composante,annee);";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			} else {
				$sql = "ALTER TABLE socle_eleves_composantes DROP INDEX ine_cycle_id_composante_periode, ADD INDEX ine_cycle_id_composante_periode(ine,cycle,code_composante,annee);";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			}

			if ($gepiYear_debut != "") {
				$sql = "UPDATE socle_eleves_composantes SET annee='" . $gepiYear_debut . "';";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			}
		}

		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM socle_eleves_syntheses LIKE 'annee';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE socle_eleves_syntheses ADD annee varchar(10) NOT NULL default '' AFTER cycle;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);

			$sql = "SHOW INDEX FROM socle_eleves_syntheses WHERE Key_name='ine_cycle';";
			$test = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($test) == 0) {
				$sql = "ALTER TABLE socle_eleves_syntheses ADD INDEX ine_cycle_annee(ine,cycle,annee);";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			} else {
				$sql = "ALTER TABLE socle_eleves_syntheses DROP INDEX ine_cycle, ADD INDEX ine_cycle_annee(ine,cycle,annee);";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			}

			if ($gepiYear_debut != "") {
				$sql = "UPDATE socle_eleves_syntheses SET annee='" . $gepiYear_debut . "';";
				//echo "$sql<br />";
				$query = mysqli_query($mysqli, $sql);
			}
		}

		// 20170917
		$test_champ = mysqli_num_rows(mysqli_query($mysqli, "SHOW COLUMNS FROM horaires_etablissement LIKE 'num_jour_table_horaires_etablissement';"));
		if ($test_champ == 0) {
			$sql = "ALTER TABLE horaires_etablissement ADD num_jour_table_horaires_etablissement TINYINT(1) NOT NULL default '0' AFTER ouvert_horaire_etablissement;";
			//echo "$sql<br />";
			$query = mysqli_query($mysqli, $sql);
			if ($query) {
				$sql = "SELECT * FROM horaires_etablissement;";
				//echo "$sql<br />";
				$res = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if ($lig->jour_horaire_etablissement == "lundi") {
							$valeur = 0;
						} elseif ($lig->jour_horaire_etablissement == "mardi") {
							$valeur = 1;
						} elseif ($lig->jour_horaire_etablissement == "mercredi") {
							$valeur = 2;
						} elseif ($lig->jour_horaire_etablissement == "jeudi") {
							$valeur = 3;
						} elseif ($lig->jour_horaire_etablissement == "vendredi") {
							$valeur = 4;
						} elseif ($lig->jour_horaire_etablissement == "samedi") {
							$valeur = 5;
						} elseif ($lig->jour_horaire_etablissement == "dimanche") {
							$valeur = 6;
						} else {
							// Bizarre
							$valeur = 8;
						}
						$sql = "UPDATE horaires_etablissement SET num_jour_table_horaires_etablissement='" . $valeur . "' WHERE id_horaire_etablissement='" . $lig->id_horaire_etablissement . "';";
						//echo "$sql<br />";
						$update = mysqli_query($mysqli, $sql);
					}
				}
			}
		}
	}

	// 20200219
	//echo "getSettingValue('version')=".getSettingValue('version')."<br />";
	if (getSettingValue('version') == "1.7.4") {
		$sql = "CREATE TABLE IF NOT EXISTS socle_eleves_competences_numeriques (id int(11) NOT NULL auto_increment, 
	ine varchar(50) NOT NULL DEFAULT '', 
	cycle tinyint(2) NOT NULL DEFAULT '0', 
	annee varchar(10) NOT NULL DEFAULT '',
	code_competence varchar(10) NOT NULL DEFAULT '', 
	niveau_maitrise varchar(10) NOT NULL DEFAULT '', 
	periode INT(11) NOT NULL default '1', 
	login_saisie varchar(50) NOT NULL DEFAULT '', 
	date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
	PRIMARY KEY (id), INDEX ine_cycle_id_competence_periode (ine, cycle, code_competence, periode), UNIQUE(ine, cycle, code_competence, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		//echo "$sql<br />";
		$create = mysqli_query($mysqli, $sql);

		$sql = "CREATE TABLE IF NOT EXISTS socle_eleves_syntheses_numeriques (id int(11) NOT NULL auto_increment, 
		ine varchar(50) NOT NULL, 
		cycle tinyint(2) NOT NULL, 
		annee varchar(10) NOT NULL DEFAULT '',
		periode INT(11) NOT NULL default '1', 
		synthese TEXT, 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), UNIQUE(ine, cycle, annee, periode)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		//echo "$sql<br />";
		$create = mysqli_query($mysqli, $sql);

		$sql = "CREATE TABLE IF NOT EXISTS socle_classes_syntheses_numeriques (id int(11) NOT NULL auto_increment, 
		id_classe int(11) NOT NULL, 
		classe varchar(50) NOT NULL, 
		annee varchar(10) NOT NULL DEFAULT '',
		synthese TEXT, 
		login_saisie varchar(50) NOT NULL DEFAULT '', 
		date_saisie DATETIME DEFAULT '1970-01-01 00:00:01', 
		PRIMARY KEY (id), UNIQUE(id_classe, annee)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		//echo "$sql<br />";
		$create = mysqli_query($mysqli, $sql);
	}
}

function nettoyage_evenements_classes() {
	$sql = "DELETE FROM d_dates_evenements_classes WHERE id_classe NOT IN (SELECT id FROM classes);";
	//echo "$sql<br />";
	$menage = mysqli_query($GLOBALS['mysqli'], $sql);
}

function acces_impression_bulletins_simplifies($login_eleve, $id_classe = "") {

	$retour = false;

	if ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesBulletinSimpleProfToutesClasses')) {
			$retour = true;
		} else {
			if ((getSettingAOui('GepiAccesBulletinSimplePP')) && (is_pp($_SESSION['login']))) {
				if ($login_eleve != "") {
					// PP: Le test est fait sur l'association avec la classe (même si l'élève a changé de classe en cours d'année)
					//     On ne se contente pas de is_pp(is_pp($_SESSION['login'], '', $login_ele)
					$sql = "SELECT DISTINCT jec.id_classe FROM j_eleves_classes jec WHERE jec.login='$login_eleve';";
					//echo "$sql<br />";
					$res = mysqli_query($GLOBALS["mysqli"], $sql);
					if (mysqli_num_rows($res) > 0) {
						while ($lig = mysqli_fetch_object($res)) {
							if (is_pp($_SESSION['login'], $lig->id_classe)) {
								$retour = true;
								break;
							}
						}
					}
				} elseif (($id_classe != "") && (is_pp($_SESSION['login'], $id_classe))) {
					$retour = true;
				}
			}

			if (!$retour) {
				// On teste aussi: Tous les élèves des classes dans lesquels le prof enseigne, même s'il n'a pas l'élève dans un de ses groupes
				if (getSettingAOui('GepiAccesBulletinSimpleProfTousEleves')) {
					if ($login_eleve != "") {
						$sql = "SELECT DISTINCT 1=1 FROM j_groupes_professeurs jgp, 
												j_groupes_classes jgc, 
												j_eleves_classes jec 
											WHERE jec.login='" . $login_eleve . "' AND 
												jec.id_classe=jgc.id_classe AND 
												jgc.id_groupe=jgp.id_groupe AND 
												jgp.login='" . $_SESSION['login'] . "';";
						//echo "$sql<br />";
						$res = mysqli_query($GLOBALS["mysqli"], $sql);
						if (mysqli_num_rows($res) > 0) {
							$retour = true;
						}
					} elseif ($id_classe != "") {
						$sql = "SELECT DISTINCT 1=1 FROM j_groupes_professeurs jgp, 
												j_groupes_classes jgc 
											WHERE jgc.id_classe='" . $id_classe . "' AND 
												jgc.id_groupe=jgp.id_groupe AND 
												jgp.login='" . $_SESSION['login'] . "';";
						//echo "$sql<br />";
						$res = mysqli_query($GLOBALS["mysqli"], $sql);
						if (mysqli_num_rows($res) > 0) {
							$retour = true;
						}
					}
				}

				if (!$retour) {
					// On teste aussi:
					if (getSettingAOui('GepiAccesBulletinSimpleProf')) {
						if ($login_eleve != "") {
							$sql = "SELECT DISTINCT 1=1 FROM j_groupes_professeurs jgp, 
													j_eleves_groupes jeg 
												WHERE jeg.login='$login_eleve' AND 
													jeg.id_groupe=jgp.id_groupe AND 
													jgp.login='" . $_SESSION['login'] . "';";
							//echo "$sql<br />";
							$res = mysqli_query($GLOBALS["mysqli"], $sql);
							if (mysqli_num_rows($res) > 0) {
								$retour = true;
							}
						} elseif ($id_classe != "") {
							$sql = "SELECT DISTINCT 1=1 FROM j_groupes_professeurs jgp, 
													j_groupes_classes jgc 
												WHERE jgc.id_classe='" . $id_classe . "' AND 
													jgc.id_groupe=jgp.id_groupe AND 
													jgp.login='" . $_SESSION['login'] . "';";
							//echo "$sql<br />";
							$res = mysqli_query($GLOBALS["mysqli"], $sql);
							if (mysqli_num_rows($res) > 0) {
								$retour = true;
							}
						}
					}
				}
			}
		}
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'secours') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'autre') {
		// A DETAILLER
		$retour = false;
	}

	return $retour;
}

function estEpiApParcours($indice_aid) {
	global $mysqli;

	$sqlEstEAP = "SELECT type_aid FROM aid_config WHERE indice_aid = $indice_aid ";
	//echo "$sql<br />";
	$retour = $mysqli->query($sqlEstEAP)->fetch_object()->type_aid;
	return $retour;
}

function saveResumeSurBulletin($resumeSurBulletin, $idAid) {
	global $mysqli;
	$sqlUpdateResumeSurBulletin = "UPDATE `aid` SET `resumeBulletin` = '$resumeSurBulletin' WHERE `aid`.`id` = '$idAid';";
	//echo "$sqlUpdateResumeSurBulletin<br />";
	$mysqli->query($sqlUpdateResumeSurBulletin);
}

function aidEstAfficheBulletin($aid_id) {
	global $mysqli;
	$retour = FALSE;
	$sqlEstAffiche = "SELECT 1=1 FROM aid WHERE `aid`.`id` = '$aid_id' AND resumeBulletin = 'y' ;";
	//echo $sqlEstAffiche."<br />";
	$res = $mysqli->query($sqlEstAffiche);
	if (($res) && (mysqli_num_rows($res) > 0)) {
		$retour = TRUE;
	}
	return $retour;
}

function afficheResumeAid($aid_id) {
	global $mysqli;
	$retour = FALSE;
	$sqlAffiche = "SELECT 1=1 FROM aid WHERE id = $aid_id AND resumeBulletin = 'Y' ";
	if ($mysqli->query($sqlAffiche)->num_rows) {
		$retour = TRUE;
	}
	return $retour;
}

function getResume($aid_id) {
	global $mysqli;
	$retour = "";
	$sqlAffiche = "SELECT resume FROM aid WHERE id = $aid_id AND resumeBulletin = 'Y' ";
	$retourAffiche = $mysqli->query($sqlAffiche);
	if ($retourAffiche->num_rows) {
		$retour = $retourAffiche->fetch_object()->resume . " : ";
	}
	return $retour;
}

function acces_trombinoscope() {
	$retour = "false";
	if (getSettingAOui("active_module_trombinoscopes")) {
		if (acces("/mod_trombinoscopes/trombinoscope.php", $_SESSION['statut'])) {
			$retour = "true";
		}
	}
	return $retour;
}

function calcule_cycle_et_niveau($mef_code_ele, $valeur_par_defaut_cycle = "", $valeur_par_defaut_niveau = "") {
	global $tab_mef;

	if ((!isset($tab_mef)) || (!is_array($tab_mef)) || (count($tab_mef) == 0)) {
		$tab_mef = get_tab_mef();
		/*
		echo "<pre>";
		print_r($tab_mef);
		echo "</pre>";
		*/
	}

	$tab = array();

	if ((isset($tab_mef[$mef_code_ele]["mef_rattachement"])) && ($tab_mef[$mef_code_ele]["mef_rattachement"] != "")) {
		if ($tab_mef[$mef_code_ele]["mef_rattachement"] == "10010012110") {
			// C'est une classe de 6ème
			$cycle = 3;
			$niveau = 6;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "10110001110") {
			// C'est une classe de 5ème
			$cycle = 4;
			$niveau = 5;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "10210001110") {
			// C'est une classe de 4ème
			$cycle = 4;
			$niveau = 4;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "10310019110") {
			// C'est une classe de 3ème
			$cycle = 4;
			$niveau = 3;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "16410002110") {
			// C'est une classe de 6ème SEGPA
			$cycle = 2;
			$niveau = 6;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "16510002110") {
			// C'est une classe de 5ème SEGPA
			$cycle = 3;
			$niveau = 5;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "16610002110") {
			// C'est une classe de 4ème SEGPA
			$cycle = 3;
			$niveau = 4;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "16710002110") {
			// C'est une classe de 3ème SEGPA
			$cycle = 3;
			$niveau = 3;
		} elseif ($tab_mef[$mef_code_ele]["mef_rattachement"] == "10310026110") {
			// C'est une classe de 3ème prepa pro
			$cycle = 4;
			$niveau = 3;
		} else {
			// Pour le moment, on suppose que c'est un cycle 4 et même un élève de 3ème
			// On verra plus tard le cas d'un Gepi en Lycée
			//$cycle=4;
			//$niveau=3;

			// Il vaut mieux ne rien mettre en couleur pour repérer que les cycle et niveau n'ont pas été identifiés
			$cycle = $valeur_par_defaut_cycle;
			$niveau = $valeur_par_defaut_niveau;
		}
	} elseif ($mef_code_ele == "10010012110") {
		// C'est une classe de 6ème
		$cycle = 3;
		$niveau = 6;
	} elseif ($mef_code_ele == "10110001110") {
		// C'est une classe de 5ème
		$cycle = 4;
		$niveau = 5;
	} elseif ($mef_code_ele == "10210001110") {
		// C'est une classe de 4ème
		$cycle = 4;
		$niveau = 4;
	} elseif ($mef_code_ele == "10310019110") {
		// C'est une classe de 3ème
		$cycle = 4;
		$niveau = 3;
	} elseif ($mef_code_ele == "16410002110") {
		// C'est une classe de 6ème SEGPA
		$cycle = 2;
		$niveau = 6;
	} elseif ($mef_code_ele == "16510002110") {
		// C'est une classe de 5ème SEGPA
		$cycle = 3;
		$niveau = 5;
	} elseif ($mef_code_ele == "16610002110") {
		// C'est une classe de 4ème SEGPA
		$cycle = 3;
		$niveau = 4;
	} elseif ($mef_code_ele == "16710002110") {
		// C'est une classe de 3ème SEGPA
		$cycle = 3;
		$niveau = 3;
	} else {
		// Pour le moment, on suppose que c'est un cycle 4 et même un élève de 3ème
		// On verra plus tard le cas d'un Gepi en Lycée
		//$cycle=4;
		//$niveau=3;

		// Il vaut mieux ne rien mettre en couleur pour repérer que les cycle et niveau n'ont pas été identifiés
		$cycle = $valeur_par_defaut_cycle;
		$niveau = $valeur_par_defaut_niveau;
	}

	$tab['mef_cycle'] = $cycle;
	$tab['mef_niveau'] = $niveau;

	return $tab;
}

function acces_consultation_admin_abs2($page) {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == "administrateur") {
		$retour = true;
	} elseif (acces($page, $_SESSION['statut'])) {
		// Tester si le droit a été donné.
		$sql = "SELECT 1=1 FROM a_droits WHERE login='" . $_SESSION['login'] . "' AND page='" . $page . "' AND consultation='y'";
		$test = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($test) > 0) {
			$retour = true;
		}
	}
	return $retour;
}

function acces_saisie_admin_abs2($page) {
	global $mysqli;

	$retour = false;
	if ($_SESSION['statut'] == "administrateur") {
		$retour = true;
	} elseif (acces($page, $_SESSION['statut'])) {
		// Tester si le droit a été donné.
		$sql = "SELECT 1=1 FROM a_droits WHERE login='" . $_SESSION['login'] . "' AND page='" . $page . "' AND saisie='y'";
		$test = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($test) > 0) {
			$retour = true;
		}
	}
	return $retour;
}

// ABS2: Retourner le nombre de traitements avec tel Type d'absence attribué.
function abs2_nombre_de_saisies_de_tel_type($type_id) {
	global $mysqli;

	$retour = 0;

	$sql = "SELECT 1=1 FROM a_traitements WHERE a_type_id='" . $type_id . "';";
	$test = mysqli_query($mysqli, $sql);
	return mysqli_num_rows($test);
}

// ABS2: Retourner le nombre de traitements avec tel Motif d'absence attribué.
function abs2_nombre_de_saisies_avec_tel_motif($motif_id) {
	global $mysqli;

	$retour = 0;

	$sql = "SELECT 1=1 FROM a_traitements WHERE a_motif_id='" . $motif_id . "';";
	$test = mysqli_query($mysqli, $sql);
	return mysqli_num_rows($test);
}

// ABS2: Retourner le nombre de traitements avec telle justification d'absence attribuée.
function abs2_nombre_de_saisies_avec_cette_justification($justification_id) {
	global $mysqli;

	$retour = 0;

	$sql = "SELECT 1=1 FROM a_traitements WHERE a_justification_id='" . $justification_id . "';";
	$test = mysqli_query($mysqli, $sql);
	return mysqli_num_rows($test);
}

// ABS2: Retourner le nombre de saisies avec tel Lieu d'absence attribué.
function abs2_nombre_de_saisies_avec_tel_lieu($lieu_id) {
	global $mysqli;

	$retour = 0;

	$sql = "SELECT 1=1 FROM a_saisies WHERE id_lieu='" . $lieu_id . "';";
	$test = mysqli_query($mysqli, $sql);
	return mysqli_num_rows($test);
}

function abs2_acces_au_moins_une_pages_admin() {
	global $mysqli;

	$tab_url = array();
	$tab_url[] = "/mod_abs2/admin/admin_types_absences.php";
	$tab_url[] = "/mod_abs2/admin/admin_motifs_absences.php";
	$tab_url[] = "/mod_abs2/admin/admin_lieux_absences.php";
	$tab_url[] = "/mod_abs2/admin/admin_justifications_absences.php";

	$tab_pages = array();
	$tab_pages["/mod_abs2/admin/admin_types_absences.php"] = "Types d'absence";
	$tab_pages["/mod_abs2/admin/admin_motifs_absences.php"] = "Motifs d'absence";
	$tab_pages["/mod_abs2/admin/admin_lieux_absences.php"] = "Lieux d'absence";
	$tab_pages["/mod_abs2/admin/admin_justifications_absences.php"] = "Justifications d'absence";

	$retour = false;
	foreach ($tab_pages as $url => $intitule) {
		if ((acces_consultation_admin_abs2($url)) || (acces_saisie_admin_abs2($url))) {
			$retour = true;
			break;
		}
	}
	return $retour;
}

function get_nom_prenom_from_INE($ine) {
	global $mysqli;

	$retour = "";

	$sql = "SELECT nom, prenom FROM eleves WHERE no_gep='" . $ine . "';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		$lig = mysqli_fetch_object($test);
		$retour = casse_mot($lig->nom, "maj") . " " . casse_mot($lig->prenom, "majf2");
	}

	return $retour;
}

function get_tab_dates_periodes() {
	global $mysqli;

	$tab = array();

	if ($_SESSION["statut"] == "eleve") {
		$sql = "SELECT p.*, c.classe FROM periodes p, 
							classes c, 
							j_eleves_classes jec 
						WHERE c.id=p.id_classe AND 
							c.id=jec.id_classe AND 
							jec.periode=p.num_periode AND 
							jec.login='" . $_SESSION['login'] . "' 
						ORDER BY c.classe, p.num_periode, p.date_fin;";
	} elseif ($_SESSION["statut"] == "responsable") {
		$sql = "SELECT p.*, c.classe FROM periodes p, 
							classes c, 
							j_eleves_classes jec 
						WHERE c.id=p.id_classe AND 
							c.id=jec.id_classe AND 
							jec.periode=p.num_periode AND 
							jec.login IN (SELECT DISTINCT e.login FROM eleves e, 
														responsables2 r, 
														resp_pers rp 
													WHERE e.ele_id=r.ele_id AND 
														r.pers_id=rp.pers_id AND 
														rp.login='" . $_SESSION['login'] . "') 
						ORDER BY c.classe, p.num_periode, p.date_fin;";
	} else {
		$sql = "SELECT p.*, c.classe FROM periodes p, classes c WHERE c.id=p.id_classe ORDER BY c.classe, p.num_periode, p.date_fin;";
	}
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab["id_classe"][$lig["id_classe"]][$lig["num_periode"]] = $lig;
			$tab["periode"][$lig["num_periode"]][$lig["id_classe"]] = $lig;
			$tab["date_fin"][$lig["date_fin"]][] = $lig;
		}
	}

	return $tab;
}

function get_tab_types_enseignements_complement() {
	global $mysqli;

	$tab = array();

	$tab["indice"] = array();
	$tab["code"] = array();

	$sql = "SELECT * FROM nomenclatures_valeurs WHERE type='enseignement_complement' ORDER BY code;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab["indice"][$cpt] = $lig;
			$tab["code"][$lig["code"]] = $lig;
			$cpt++;
		}
	}
	return $tab;
}

function nettoye_texte_vers_chaine($texte, $remplacement_retour_ligne = " - ") {

	$chaine = preg_replace('/\r/', '\n', $texte);
	$chaine = nettoyage_retours_ligne_surnumeraires($chaine);
	// On remplace les retours à la ligne par des " - "
	$chaine = preg_replace('/\n/', $remplacement_retour_ligne, trim($chaine));
	$chaine = preg_replace('/(' . $remplacement_retour_ligne . '){2,}/', $remplacement_retour_ligne, trim($chaine));
	$chaine = preg_replace('/[ ]{2,}/', " ", trim($chaine));
	$chaine = trim($chaine);

	return $chaine;
}

function get_tab_modalites_accompagnement() {
	global $mysqli;

	$tab = array();

	$tab["indice"] = array();
	$tab["code"] = array();

	$sql = "SELECT * FROM modalites_accompagnement ORDER BY code;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$cpt = 0;
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab["indice"][$cpt] = $lig;
			$tab["code"][$lig["code"]]["libelle"] = $lig["libelle"];
			$tab["code"][$lig["code"]]["avec_commentaire"] = $lig["avec_commentaire"];
			$cpt++;
		}
	}
	return $tab;
}

function get_tab_modalites_accompagnement_eleve($login_eleve, $periode = "") {
	global $mysqli;

	$tab = array();

	if ($periode == "") {
		$sql = "SELECT jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login='" . $login_eleve . "' ORDER BY code;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			//$temoin_commentaire_non_vide=0;
			$cpt = 0;
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab["periode"][$lig["periode"]][$cpt] = $lig;
				$tab["code"][$lig["code"]][$cpt] = $lig;
				$cpt++;
			}
		}
	} else {
		$sql = "SELECT jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login='" . $login_eleve . "' AND jmae.periode='" . $periode . "' ORDER BY code;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			$cpt = 0;
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab[] = $lig;
			}
		}
	}
	return $tab;
}

function nb_pts_DNB($num) {
	$retour = 0;
	if ($num == 1) {
		$retour = 10;
	} elseif ($num == 2) {
		$retour = 25;
	} elseif ($num == 3) {
		$retour = 40;
	} elseif ($num == 4) {
		$retour = 50;
	}
	return $retour;
}

function calcule_points_DNB_enseignement_complement($ine) {
	global $mysqli;

	/*
	http://eduscol.education.fr/cid98239/dnb-2017.html
	10 points si les objectifs d'apprentissage du cycle 4 sont atteints ;
	20 points si ces objectifs sont dépassés.
	*/

	$retour = 0;
	// Un seul enseignement de complément doit être retenu.
	//$sql="SELECT * FROM socle_eleves_enseignements_complements WHERE ine='".$ine."';";
	$sql = "SELECT seec.* FROM socle_eleves_enseignements_complements seec, j_groupes_enseignements_complement jgec WHERE seec.ine='" . $ine . "' AND jgec.id_groupe=seec.id_groupe ORDER BY positionnement DESC LIMIT 1;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			if ($lig->positionnement == 1) {
				$retour += 10;
			} elseif ($lig->positionnement == 2) {
				$retour += 20;
			}
		}
	}

	return $retour;
}

// Faire une fonction remplissant $tab_resp_adr pour un ele_id donné et éventuellement un pers_id particulier.
// On a get_adresse_responsable($pers_id, $login_resp="")
function adresse_postale_resp($tab_resp_adr, $mode = "pdf") {
	global $un_seul_bull_par_famille;

	// $tab_resp_adr indice 0 resp_legal=1
	// $tab_resp_adr indice 1 resp_legal=2

	//echo "\$un_seul_bull_par_famille=$un_seul_bull_par_famille<br />";

	// Retour:
	$tab = array();
	$tab["nb_adr"] = 0;

	// Traitement des adresses des responsables légaux (ou de ce qu'on a mis/rempli dans $tab_resp_adr)
	if (isset($tab_resp_adr[1])) {
		if ((isset($tab_resp_adr[1]['adr1'])) &&
			(isset($tab_resp_adr[1]['adr2'])) &&
			(isset($tab_resp_adr[1]['adr3'])) &&
			(isset($tab_resp_adr[1]['adr4'])) &&
			(isset($tab_resp_adr[1]['cp'])) &&
			(isset($tab_resp_adr[1]['commune']))
		) {
			// Le deuxième responsable existe et est renseigné
			// On va comparer les adresses
			if (($tab_resp_adr[0]['adr_id'] == $tab_resp_adr[1]['adr_id']) or
				(
					(mb_strtolower($tab_resp_adr[0]['adr1']) == mb_strtolower($tab_resp_adr[1]['adr1'])) &&
					(mb_strtolower($tab_resp_adr[0]['adr2']) == mb_strtolower($tab_resp_adr[1]['adr2'])) &&
					(mb_strtolower($tab_resp_adr[0]['adr3']) == mb_strtolower($tab_resp_adr[1]['adr3'])) &&
					(mb_strtolower($tab_resp_adr[0]['adr4']) == mb_strtolower($tab_resp_adr[1]['adr4'])) &&
					($tab_resp_adr[0]['cp'] == $tab_resp_adr[1]['cp']) &&
					(mb_strtolower($tab_resp_adr[0]['commune']) == mb_strtolower($tab_resp_adr[1]['commune']))
				)
			) {
				// Les adresses sont identiques
				$tab["nb_adr"] = 1;

				// Les lignes d'adresse après le civilite/nom/prénom pour le resp legal 1 (indice 0 de $tab_resp_adr)
				$tab["adresse"][1] = lignes_adresse_postale($tab_resp_adr[0], $mode);

				// Fabriquer la ligne Civilite Nom Prénom
				$tab["adresse"][1][1] = "";
				if (($tab_resp_adr[0]['nom'] != $tab_resp_adr[1]['nom']) &&
					($tab_resp_adr[1]['nom'] != "")) {
					// Les noms des responsables sont différents
					$tab["adresse"][1][1] = $tab_resp_adr[0]['civilite'] . " " . $tab_resp_adr[0]['nom'] . " " . $tab_resp_adr[0]['prenom'] . " et " . $tab_resp_adr[1]['civilite'] . " " . $tab_resp_adr[1]['nom'] . " " . $tab_resp_adr[1]['prenom'];
				} else {
					if (($tab_resp_adr[0]['civilite'] != "") && ($tab_resp_adr[1]['civilite'] != "")) {
						$tab["adresse"][1][1] = $tab_resp_adr[0]['civilite'] . " et " . $tab_resp_adr[1]['civilite'] . " " . $tab_resp_adr[0]['nom'] . " " . $tab_resp_adr[0]['prenom'];
					} else {
						$tab["adresse"][1][1] = "M. et Mme " . $tab_resp_adr[0]['nom'] . " " . $tab_resp_adr[0]['prenom'];
					}
				}

				// Le bloc complet des lignes adresse:
				$tab["adresse"][1]["adresse"] = "<b>" . $tab["adresse"][1][1] . "</b>\n" . $tab["adresse"][1]["adresse"];


				if (isset($tab_resp_adr[0]["pers_id"])) {
					$tab["adresse"][1]["pers_id"] = $tab_resp_adr[0]["pers_id"];
				}

				if (isset($tab_resp_adr[0]["resp_legal"])) {
					$tab["adresse"][1]["resp_legal"] = $tab_resp_adr[0]["resp_legal"];
				}

				if (isset($tab_resp_adr[0]["mel"])) {
					$tab["adresse"][1]["email"][] = $tab_resp_adr[0]["mel"];
				}
				if (isset($tab_resp_adr[0]["email"])) {
					if ((!isset($tab["adresse"][1]["email"])) || (!in_array($tab_resp_adr[0]["email"], $tab["adresse"][1]["email"]))) {
						$tab["adresse"][1]["email"][] = $tab_resp_adr[0]["email"];
					}
				}

				if (isset($tab_resp_adr[1]["mel"])) {
					if ((!isset($tab["adresse"][1]["email"])) || (!in_array($tab_resp_adr[1]["mel"], $tab["adresse"][1]["email"]))) {
						$tab["adresse"][1]["email"][] = $tab_resp_adr[1]["mel"];
					}
				}
				if (isset($tab_resp_adr[1]["email"])) {
					if ((!isset($tab["adresse"][1]["email"])) || (!in_array($tab_resp_adr[1]["email"], $tab["adresse"][1]["email"]))) {
						$tab["adresse"][1]["email"][] = $tab_resp_adr[1]["email"];
					}
				}
			} else {
				// Les adresses sont différentes
				// On teste en plus si la deuxième adresse est valide
				if (($un_seul_bull_par_famille != "oui") &&
					(($tab_resp_adr[1]['adr1'] != "") || ($tab_resp_adr[1]['adr2'] != "") || ($tab_resp_adr[1]['adr3'] != "") || ($tab_resp_adr[1]['adr4'] != "")) &&
					($tab_resp_adr[1]['commune'] != "")
				) {
					$tab["nb_adr"] = 2;
				} else {
					$tab["nb_adr"] = 1;
				}

				for ($cpt = 0; $cpt < $tab["nb_adr"]; $cpt++) {

					$num_resp = $cpt + 1;

					// Les lignes d'adresse après le civilite/nom/prénom
					$tab["adresse"][$num_resp] = lignes_adresse_postale($tab_resp_adr[$cpt], $mode);

					// Fabriquer la ligne Civilite Nom Prénom
					if ($tab_resp_adr[$cpt]['civilite'] != "") {
						$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['civilite'] . " " . $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
					} else {
						$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
					}

					// Le bloc complet des lignes adresse:
					$tab["adresse"][$num_resp]["adresse"] = "<b>" . $tab["adresse"][$num_resp][1] . "</b>\n" . $tab["adresse"][$num_resp]["adresse"];

					if (isset($tab_resp_adr[$cpt]["pers_id"])) {
						$tab["adresse"][$num_resp]["pers_id"] = $tab_resp_adr[$cpt]["pers_id"];
					}

					if (isset($tab_resp_adr[$cpt]["resp_legal"])) {
						$tab["adresse"][$num_resp]["resp_legal"] = $tab_resp_adr[$cpt]["resp_legal"];
					}

					if (isset($tab_resp_adr[$cpt]["mel"])) {
						$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["mel"];
					}
					if (isset($tab_resp_adr[$cpt]["email"])) {
						if ((!isset($tab["adresse"][$num_resp]["email"])) || (!in_array($tab_resp_adr[$cpt]["email"], $tab["adresse"][$num_resp]["email"]))) {
							$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["email"];
						}
					}

				}
			}
		} else {
			// Il n'y a pas de deuxième adresse, mais il y aurait un deuxième responsable???
			// On mettra l'adresse en blanc...
			// CA NE DEVRAIT PAS ARRIVER ETANT DONNé LA REQUETE EFFECTUEE QUI JOINT resp_pers ET resp_adr...
			if ($un_seul_bull_par_famille != "oui") {
				$tab["nb_adr"] = 2;

				if (!isset($tab_resp_adr[1]['adr1'])) {
					$tab_resp_adr[1]['adr1'] = "";
				}
				if (!isset($tab_resp_adr[1]['adr2'])) {
					$tab_resp_adr[1]['adr2'] = "";
				}
				if (!isset($tab_resp_adr[1]['adr3'])) {
					$tab_resp_adr[1]['adr3'] = "";
				}
				if (!isset($tab_resp_adr[1]['adr4'])) {
					$tab_resp_adr[1]['adr4'] = "";
				}
				if (!isset($tab_resp_adr[1]['cp'])) {
					$tab_resp_adr[1]['cp'] = "";
				}
				if (!isset($tab_resp_adr[1]['commune'])) {
					$tab_resp_adr[1]['commune'] = "";
				}
			} else {
				$tab["nb_adr"] = 1;
			}

			for ($cpt = 0; $cpt < $tab["nb_adr"]; $cpt++) {

				$num_resp = $cpt + 1;

				// Les lignes d'adresse après le civilite/nom/prénom
				$tab["adresse"][$num_resp] = lignes_adresse_postale($tab_resp_adr[$cpt], $mode);

				// Fabriquer la ligne Civilite Nom Prénom
				if ($tab_resp_adr[$cpt]['civilite'] != "") {
					$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['civilite'] . " " . $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
				} else {
					$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
				}

				// Le bloc complet des lignes adresse:
				$tab["adresse"][$num_resp]["adresse"] = "<b>" . $tab["adresse"][$num_resp][1] . "</b>\n" . $tab["adresse"][$num_resp]["adresse"];

				if (isset($tab_resp_adr[$cpt]["pers_id"])) {
					$tab["adresse"][$num_resp]["pers_id"] = $tab_resp_adr[$cpt]["pers_id"];
				}

				if (isset($tab_resp_adr[$cpt]["resp_legal"])) {
					$tab["adresse"][$num_resp]["resp_legal"] = $tab_resp_adr[$cpt]["resp_legal"];
				}

				if (isset($tab_resp_adr[$cpt]["mel"])) {
					$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["mel"];
				}
				if (isset($tab_resp_adr[$cpt]["email"])) {
					if ((!isset($tab["adresse"][$num_resp]["email"])) || (!in_array($tab_resp_adr[$cpt]["email"], $tab["adresse"][$num_resp]["email"]))) {
						$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["email"];
					}
				}
			}

		}
	} else {
		// Il n'y a pas de deuxième responsable
		$tab["nb_adr"] = 1;

		// Les lignes d'adresse après le civilite/nom/prénom
		$tab["adresse"][1] = lignes_adresse_postale($tab_resp_adr[0], $mode);
		// Là, on a rempli les lignes $tab["adresse"][1][2]
		//                            $tab["adresse"][1][3]
		//                            ...
		//                            $tab["adresse"][1][7]
		// des lignes individuelles de l'adresse parent
		// et                         $tab["adresse"][1]["adresse"] qui contient le bloc adresse complet sans le civilité nom prénom du parent

		// Fabriquer la ligne Civilite Nom Prénom
		$tab["adresse"][1][1] = "";

		if ($tab_resp_adr[0]['civilite'] != "") {
			$tab["adresse"][1][1] = $tab_resp_adr[0]['civilite'] . " " . $tab_resp_adr[0]['nom'] . " " . $tab_resp_adr[0]['prenom'];
		} else {
			$tab["adresse"][1][1] = $tab_resp_adr[0]['nom'] . " " . $tab_resp_adr[0]['prenom'];
		}

		// Le bloc complet des lignes adresse avec la ligne civilité nom prénom:
		$tab["adresse"][1]["adresse"] = "<b>" . $tab["adresse"][1][1] . "</b>\n" . $tab["adresse"][1]["adresse"];

		if (isset($tab_resp_adr[0]["pers_id"])) {
			$tab["adresse"][1]["pers_id"] = $tab_resp_adr[0]["pers_id"];
		}

		if (isset($tab_resp_adr[0]["resp_legal"])) {
			$tab["adresse"][1]["resp_legal"] = $tab_resp_adr[0]["resp_legal"];
		}

		if (isset($tab_resp_adr[0]["mel"])) {
			$tab["adresse"][1]["email"][] = $tab_resp_adr[0]["mel"];
		}
		if (isset($tab_resp_adr[0]["email"])) {
			if ((!isset($tab["adresse"][1]["email"])) || (!in_array($tab_resp_adr[0]["email"], $tab["adresse"][1]["email"]))) {
				$tab["adresse"][1]["email"][] = $tab_resp_adr[0]["email"];
			}
		}
	}


	// 20170713
	// Passer en revue les indices suivants pour des resp non légaux néanmoins destinataires de bulletins,...
	// Associer une adresse mail en indice quelque part...

	// Faut-il générer ces lignes si on a $un_seul_bull_par_famille=="oui"

	if ($un_seul_bull_par_famille != "oui") {
		for ($cpt = 2; $cpt < count($tab_resp_adr); $cpt++) {
			// Vérifier si les indices sont renseignés?
			if (isset($tab_resp_adr[$cpt])) {
				$tab["nb_adr"]++;

				$num_resp = $cpt + 1;

				// Les lignes d'adresse après le civilite/nom/prénom
				$tab["adresse"][$num_resp] = lignes_adresse_postale($tab_resp_adr[$cpt], $mode);

				// Fabriquer la ligne Civilite Nom Prénom
				if ($tab_resp_adr[$cpt]['civilite'] != "") {
					$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['civilite'] . " " . $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
				} else {
					$tab["adresse"][$num_resp][1] = $tab_resp_adr[$cpt]['nom'] . " " . $tab_resp_adr[$cpt]['prenom'];
				}

				// Le bloc complet des lignes adresse:
				$tab["adresse"][$num_resp]["adresse"] = "<b>" . $tab["adresse"][$num_resp][1] . "</b>\n" . $tab["adresse"][$num_resp]["adresse"];

				if (isset($tab_resp_adr[$cpt]["pers_id"])) {
					$tab["adresse"][$num_resp]["pers_id"] = $tab_resp_adr[$cpt]["pers_id"];
				}

				if (isset($tab_resp_adr[$cpt]["resp_legal"])) {
					$tab["adresse"][$num_resp]["resp_legal"] = $tab_resp_adr[$cpt]["resp_legal"];
				}

				if (isset($tab_resp_adr[$cpt]["mel"])) {
					$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["mel"];
				}
				if (isset($tab_resp_adr[$cpt]["email"])) {
					if ((!isset($tab["adresse"][$num_resp]["email"])) || (!in_array($tab_resp_adr[$cpt]["email"], $tab["adresse"][$num_resp]["email"]))) {
						$tab["adresse"][$num_resp]["email"][] = $tab_resp_adr[$cpt]["email"];
					}
				}
			}
		}
	}

	return $tab;
}

// Lignes adresse suivant la ligne civilité nom prénom
function lignes_adresse_postale($tab_adr, $mode = "pdf") {
	// Pour le moment, seul le mode PDF est implémenté

	global $gepiSchoolPays;
	if ($gepiSchoolPays == "") {
		$gepiSchoolPays = getSettingValue("gepiSchoolPays");
	}

	$retour = array();
	//$retour["ligne"]=array();
	//$retour[1]="ADRESSE MANQUANTE";
	$retour[2] = "";
	$retour[3] = "";
	$retour[4] = "";
	$retour[5] = "";
	$retour[6] = "";
	$retour[7] = "";

	$retour["adresse"] = "";
	$retour["adresse_valide"] = false;

	if ((isset($tab_adr['adr1'])) &&
		(isset($tab_adr['adr2'])) &&
		(isset($tab_adr['adr3'])) &&
		(isset($tab_adr['adr4'])) &&
		(isset($tab_adr['cp'])) &&
		(isset($tab_adr['commune']))
	) {

		$retour[2] = "";
		if ($tab_adr['adr1'] != '') {
			$retour[2] = $tab_adr['adr1'];
			$retour["adresse"] .= "\n";
			$retour["adresse"] .= $retour[2];

			$retour["adresse_valide"] = true;
		}

		if ($tab_adr['adr2'] != "") {
			$retour[3] = $tab_adr['adr2'];

			$retour["adresse"] .= "\n";
			$retour["adresse"] .= $retour[3];

			$retour["adresse_valide"] = true;
		}

		if ($tab_adr['adr3'] != "") {
			$retour[4] = $tab_adr['adr3'];

			$retour["adresse"] .= "\n";
			$retour["adresse"] .= $retour[4];

			$retour["adresse_valide"] = true;
		}

		if ($tab_adr['adr4'] != "") {
			$retour[5] = $tab_adr['adr4'];

			$retour["adresse"] .= "\n";
			$retour["adresse"] .= $retour[5];

			$retour["adresse_valide"] = true;
		}

		$retour[6] = $tab_adr['cp'] . " " . $tab_adr['commune'];
		$retour["adresse"] .= "\n";
		$retour["adresse"] .= $retour[6];


		if ((isset($tab_adr['pays'])) && ($tab_adr['pays'] != "") && (mb_strtolower($tab_adr['pays']) != mb_strtolower($gepiSchoolPays))) {
			$retour[7] = $tab_adr['pays'];

			$retour["adresse"] .= "\n";
			$retour["adresse"] .= $retour[7];
		}
	}

	return $retour;
}

function get_max_per($id_classe) {
	global $mysqli;
	$maxper = 0;

	$sql = "SELECT MAX(num_periode) AS maxper FROM periodes WHERE id_classe='" . $id_classe . "';";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$maxper = $lig->maxper;
	}
	return $maxper;
}

function get_date_conseil_classe($id_classe, $periode) {
	global $mysqli;
	$tab = array();
	$tab["date_conseil_classe_valide"] = false;

	$sql = "SELECT date_conseil_classe FROM periodes WHERE id_classe='" . $id_classe . "' AND num_periode='" . $periode . "';";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);

		$tab["date_conseil_classe_valide"] = true;

		$tab['date_conseil_classe'] = $lig->date_conseil_classe;

		$tab['date_conseil_classe_valide'] = true;
		if ($lig->date_conseil_classe == null) {
			$tab['date_conseil_classe_valide'] = false;
		} elseif ($lig->date_conseil_classe == "0000-00-00 00:00:00") {
			$tab['date_conseil_classe_valide'] = false;
		} else {
			$tmp_tab = explode(" ", $lig->date_conseil_classe);
			$tmp_tab2 = explode("-", $tmp_tab[0]);
			$tmp_day = $tmp_tab2[2];
			$tmp_month = $tmp_tab2[1];
			$tmp_year = $tmp_tab2[0];
			//echo "\$tmp_month=$tmp_month, \$tmp_day=$tmp_day, \$tmp_year=$tmp_year<br />";
			if (!checkdate($tmp_month, $tmp_day, $tmp_year)) {
				//echo "plop<br />";
				$tab['date_conseil_classe_valide'] = false;
			} else {
				//$tab['date_conseil_classe_DateTime']=DateTime::createFromFormat('Y-m-d H:M:S', $tab['date_conseil_classe']);
				$tab['date_conseil_classe_DateTime'] = new DateTime(str_replace("/", ".", formate_date($tab['date_conseil_classe'])));
			}
		}
	}
	return $tab;
}

function get_tab_mef_avec_affichage_orientation() {
	global $mysqli;

	$tab_mef_af = array();
	$sql = "SELECT m.* FROM o_mef om, mef m WHERE om.mef_code=m.mef_code AND om.affichage='y' ORDER BY libelle_edition, libelle_long, libelle_court;";
	$res_mef = mysqli_query($mysqli, $sql);
	while ($lig_mef = mysqli_fetch_object($res_mef)) {
		$tab_mef_af[] = $lig_mef->mef_code;
	}
	return $tab_mef_af;
}

function liste_designations_courtes_mef_avec_affichage_orientation() {
	global $mysqli;
	global $tab_mef_af;
	if ((!isset($tab_mef_af)) || (!is_array($tab_mef_af)) || (count($tab_mef_af) == 0)) {
		$tab_mef_af = get_tab_mef_avec_affichage_orientation();
	}

	$chaine = "";
	$tab_mef = get_tab_mef();
	foreach ($tab_mef as $mef_code => $current_mef) {
		if (in_array($mef_code, $tab_mef_af)) {
			if ($chaine != "") {
				$chaine .= ", ";
			}
			$chaine .= $current_mef['designation_courte'];
		}
	}
	return $chaine;
}

function get_derniere_classe_from_ele_login($ele_login) {
	global $mysqli;

	$retour = "";
	$sql = "SELECT jec.id_classe FROM j_eleves_classes jec, classes c, periodes p WHERE p.id_classe=c.id AND jec.periode=p.num_periode AND jec.id_classe=c.id AND jec.login='$ele_login' ORDER BY jec.periode DESC LIMIT 1;";
	//echo "$sql<br />";
	$res_class = mysqli_query($mysqli, $sql);
	if ($res_class->num_rows > 0) {
		$lig_tmp = $res_class->fetch_object();
		$retour = $lig_tmp->id_classe;
		$res_class->close();
	}
	return $retour;
}

function acces_cdt() {
	if (getSettingAOui("active_cahiers_texte")) {
		return true;
	} elseif (($_SESSION["statut"] == "professeur") && (getSettingAOui("acces_cdt_prof"))) {
		return true;
	} else {
		return false;
	}
}

function acces_saisie_modalites_accompagnement() {
	if ($_SESSION["statut"] == "administrateur") {
		return true;
	} elseif (!acces("/gestion/saisie_modalites_accompagnement.php", $_SESSION["statut"])) {
		return false;
	} else {
		if (($_SESSION["statut"] == "scolarite") && (getSettingAOui("saisieModalitesAccompagnementScol"))) {
			return true;
		} else {
			return false;
		}
	}
}

function acces_saisie_telephone($statut) {
	if ($_SESSION["statut"] == "administrateur") {
		return true;
	} elseif ($_SESSION["statut"] == "scolarite") {
		return true;
	} elseif (!acces("/gestion/saisie_contact.php", $_SESSION["statut"])) {
		return false;
	} else {
		if (($_SESSION["statut"] == "cpe") && (getSettingAOui("GepiAccesSaisieTelephone" . ucfirst(strtolower($statut)) . "Cpe"))) {
			return true;
		} else {
			return false;
		}
	}
}

function affiche_lien_cdt() {
	if (getSettingAOui("active_cahiers_texte")) {
		//echo 1;
		return true;
	} elseif (($_SESSION["statut"] == "professeur") && (getSettingAOui("acces_cdt_prof"))) {
		$pref = getPref($_SESSION["login"], "acces_cdt_prof", "");
		if ($pref == "y") {
			//echo 2;
			return true;
		} elseif ($pref == "y") {
			//echo 3;
			return false;
		} else {
			if (getSettingAOui("acces_cdt_prof_afficher_lien")) {
				//echo 4;
				return true;
			} else {
				//echo 5;
				return false;
			}
		}
	} else {
		//echo 6;
		return false;
	}
}

function get_eleves_from_classe($id_classe, $periode = "") {
	global $mysqli;

	$tab = array();

	$sql = "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='" . $id_classe . "'";
	if ($periode != "") {
		$sql .= " AND jec.periode='$periode'";
	}
	$sql .= " ORDER BY e.nom, e.prenom;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab[] = $lig;
			$tab["list"][] = $lig;
			$tab["users"][$lig["login"]] = $lig;
		}
	}
	return $tab;
}

function acces_saisie_abs_prof($login_user, $statut_user = "") {
	$retour = false;

	if ((getSettingAOui("active_mod_abs_prof")) &&
		(acces("/mod_abs_prof/saisir_absence.php", $_SESSION['statut']))) {
		if ($statut_user == "") {
			$statut_user = get_valeur_champ("utilisateurs", "login='" . $login_user . "'", "statut");
		}

		if (
			($statut_user == "administrateur") ||
			(($statut_user == "scolarite") && (getSettingAOui("AbsProfSaisieAbsScol"))) ||
			(($statut_user == "cpe") && (getSettingAOui("AbsProfSaisieAbsCpe")))
		) {
			$retour = true;
		}
	}

	return $retour;
}

function id_j_semaine($time = 0) {
	/**
	 * Renvoie l'indice du jour de la semaine
	 * comme le fait strftime('%u'... mais le
	 * paramètre %u n'est pas pris en compte
	 * dans certaines versions Window$ de strftime
	 *
	 * @param timestamp $time : par défaut le timestamp courant
	 *
	 * @return 1 pour lundi à 7 pour dimanche
	 */
	if ($time == 0) $time = time();
	$id = (int)strftime('%w', $time);
	if ($id == 0) $id = 7;
	return $id;
}

// Traduction US-FR des dates strftime() sous windows quand des locales manquent.
// Fonction à utiliser en lieu et place de strftime() si le motif contient %A, %a, %B ou %b
/*
function french_strftime($motif="%A", $ts="") {
	global $temoin_strftime_us;

	if($ts=="") {
		$ts=time();
	}

	if($temoin_strftime_us) {
		$eng_words = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', );
		$french_words = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.', 'dim.', 'janv.', 'févr.', 'mars', 'avril', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.');

		$traduction = array('Monday'=>'Lundi',
		'Tuesday'=>'Mardi',
		'Wednesday'=>'Mercredi',
		'Thursday'=>'Jeudi',
		'Friday'=>'Vendredi',
		'Saturday'=>'Samedi',
		'Sunday'=>'Dimanche',
		'January'=>'Janvier',
		'February'=>'Février',
		'March'=>'Mars',
		'April'=>'Avril',
		'May'=>'Mai',
		'June'=>'Juin',
		'July'=>'Juillet',
		'August'=>'Août',
		'September'=>'Septembre',
		'October'=>'Octobre',
		'November'=>'Novembre',
		'December'=>'Décembre',
		'Mon'=>'lun.',
		'Tue'=>'mar.',
		'Wed'=>'mer.',
		'Thu'=>'jeu.',
		'Fri'=>'ven.',
		'Sat'=>'sam.',
		'Sun'=>'dim.',
		'Jan'=>'janv.',
		'Feb'=>'févr.',
		'Mar'=>'mars',
		'Apr'=>'avril',
		//'May'=>'mai',
		'Jun'=>'juin',
		'Jul'=>'juil.',
		'Aug'=>'août',
		'Sep'=>'sept.',
		'Oct'=>'oct.',
		'Nov'=>'nov.',
		'Dec'=>'déc.');

		// Avec str_replace(), on a des remplacements successifs Tuesday->Mardi->Marsdi (le Mar de March est transformé en mars dans la version FR)
		//$retour = str_replace($eng_words, $french_words, strftime($motif, $ts));
		// Avec strtr(), le texte remplacé n'est pas réutilisé par la suite
		$retour = strtr(strftime($motif, $ts), $traduction);
	}
	else {
		$retour=strftime($motif, $ts);
	}
	return $retour;
}
*/
function french_strftime($motif, $timestamp = 0) {
	global $strftime_utf8;
	if ($timestamp == 0) {
		$timestamp = time();
	}
	return $strftime_utf8 ? strftime($motif, $timestamp) : utf8_encode(strftime($motif, $timestamp));
}

/*
// Sous Window$, certains paramètres de strftime() ne sont pas implémentés (notamment le %V)
// Cf. https://msdn.microsoft.com/en-us/library/fe06s4ak.aspx
function id_num_semaine($ts="") {
	global $temoin_strftime_V_vide;

	if($ts=="") {
		$ts=time();
	}

	if($temoin_strftime_V_vide) {
		return id_s_annee($ts);
	}
	else {
		return strftime('%V', $ts);
	}
}
*/

function id_num_semaine($ts_date = 0) {
	/**
	 * Renvoie le numéro ISO-8601:1988 de la semaine
	 * comme le fait strftime('%V'... mais le
	 * paramètre %V n'est pas pris en compte
	 * dans certaines versions Window$ de strftime
	 * Source de l'algorithme : https://fr.wikipedia.org/wiki/ISO_8601#Num.C3.A9ro_de_semaine
	 * A voir: https://fr.wikipedia.org/wiki/Bug_de_l%27an_2038
	 *
	 * @param timestamp $ts_date : par défaut le timestamp courant
	 *
	 * @return numéro ISO-8601:1988 de la semaine
	 */
	if ($ts_date == 0) $ts_date = time();
	// numéro du jour de $ts_date (0:dimanche...7:samedi)
	// on est obligé de s'appuyer sur %w, car %u n'est pas pris en compte sous Window$
	$id_j_ts_date = strftime('%w', $ts_date);
	// calcul de la date du jeudi de la même semaine que $ts_date
	$ts_jeudi_de_la_semaine = ($id_j_ts_date == 0) ? ($ts_date - 3 * 24 * 3600) : ($ts_date - ($id_j_ts_date - 4) * 24 * 3600);
	// numéro du jeudi dans l'année
	$n_ts_jeudi_de_la_semaine = strftime('%j', $ts_jeudi_de_la_semaine);
	// finalement
	$r_id_s_annee = 1 + (int)(($n_ts_jeudi_de_la_semaine - 1) / 7);
	return ($r_id_s_annee < 10) ? ('0' . $r_id_s_annee) : ((string)$r_id_s_annee);
}

// $login_ele: Login de l'élève
function get_resp_classe($id_classe = '', $login_ele = '') {
	global $mysqli;

	$tab = array();
	$tab['pp'] = array();
	$tab['cpe'] = array();
	$tab['suivi_par'] = array();
	// Engagement: Représentants parents, élèves
	//$tab['']=array();

	// Professeur principal
	if ($id_classe != "") {
		if ($login_ele != "") {
			$sql = "SELECT DISTINCT jep.professeur 
				FROM j_eleves_professeurs jep, j_eleves_classes jec 
				WHERE jec.id_classe='$id_classe' 
				AND jec.login=jep.login 
				AND jec.login='" . $login_ele . "' 
				AND jec.id_classe=jep.id_classe 
				ORDER BY professeur;";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_object($res)) {
					$tab['pp'][] = $lig->professeur;
				}
			}
		} else {
			$tab['pp'] = get_tab_prof_suivi($id_classe);
		}
	} elseif ($login_ele != "") {
		$sql = "SELECT DISTINCT jep.professeur 
			FROM j_eleves_professeurs jep, 
				j_eleves_classes jec, 
				periodes p 
			WHERE jep.login=jec.login AND 
				jec.id_classe=p.id_classe AND 
				jep.login='" . $login_ele . "' 
			ORDER BY p.num_periode, jep.professeur;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab['pp'][] = $lig->professeur;
			}
		}
	} else {
		$sql = "SELECT DISTINCT id FROM classes ORDER BY classe;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab[$lig->id]['pp'] = get_tab_prof_suivi($lig->id);
			}
		}
	}

	// CPE
	if ($login_ele != "") {
		$sql = "SELECT DISTINCT u.login FROM utilisateurs u, 
								j_eleves_cpe jecpe 
							WHERE u.login=jecpe.cpe_login AND 
								u.statut='cpe' AND 
								jecpe.e_login='" . $login_ele . "' 
							ORDER BY u.nom,u.prenom;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab['cpe'][] = $lig->login;
			}
		}
	} elseif ($id_classe != "") {
		$sql = "SELECT DISTINCT u.login FROM utilisateurs u, 
								j_eleves_cpe jecpe, 
								j_eleves_classes jec 
							WHERE u.login=jecpe.cpe_login AND 
								u.statut='cpe' AND 
								jecpe.e_login=jec.login AND 
								jec.id_classe='" . $id_classe . "' 
							ORDER BY u.nom,u.prenom;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab['cpe'][] = $lig->login;
			}
		}
	} else {
		/*
		$sql="SELECT DISTINCT u.login FROM utilisateurs u,
								j_eleves_cpe jecpe
							WHERE u.login=jecpe.cpe_login AND
								u.statut='cpe'
							ORDER BY u.nom,u.prenom;";
		$res = mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$tab['cpe'][]=$lig->login;
			}
		}
		*/
		$sql = "SELECT DISTINCT u.login, jec.id_classe FROM utilisateurs u, 
								j_eleves_cpe jecpe, 
								j_eleves_classes jec
							WHERE u.login=jecpe.cpe_login AND 
								u.statut='cpe' AND 
								jec.login=jecpe.e_login 
							ORDER BY u.nom,u.prenom;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab[$lig->id_classe]['cpe'][] = $lig->login;
			}
		}
	}

	// Suivi (chef ou adjoint)
	if ($id_classe != "") {
		$sql = "SELECT suivi_par FROM classes WHERE id='" . $id_classe . "';";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab['suivi_par'][] = $lig->suivi_par;
			}
		}
	} elseif ($login_ele != "") {
		$sql = "SELECT DISTINCT c.suivi_par FROM c.classes, j_eleves_classes jec WHERE jec.login='" . $login_ele . "' AND c.id=jec.id_classe ORDER BY jec.periode;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab['suivi_par'][] = $lig->suivi_par;
			}
		}
	} else {
		$sql = "SELECT DISTINCT id, suivi_par FROM classes ORDER BY classe;";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_object($res)) {
				$tab[$lig->id]['suivi_par'] = $lig->suivi_par;
			}
		}
	}

	return $tab;
}


function get_periode_from_classe_d_apres_date($id_classe, $timestamp = "") {
	global $mysqli;
	$num_periode = "";

	if ($timestamp == "") {
		$timestamp = time();
	}

	$sql = "SELECT * FROM edt_calendrier WHERE (classe_concerne_calendrier like '" . $id_classe . ";%' OR 
								classe_concerne_calendrier like '%;" . $id_classe . ";%') AND 
								numero_periode!='0' AND 
								debut_calendrier_ts<='" . $timestamp . "' AND 
								fin_calendrier_ts>='" . $timestamp . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$num_periode = $lig->numero_periode;
	} else {
		// On essaye avec la table periodes

		$date_mysql = strftime("%Y-%m-%d %H:%M:%S", $timestamp);

		$sql = "SELECT p.num_periode FROM periodes p 
						WHERE p.id_classe='" . $id_classe . "' AND 
							p.date_fin>='" . $date_mysql . "'
						ORDER BY date_fin ASC LIMIT 1;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			$lig = mysqli_fetch_object($res);
			$num_periode = $lig->num_periode;
		}
	}

	return $num_periode;
}

function get_tab_engagements_conseil_classe($id_classe, $id_groupe = '') {
	global $mysqli;

	$tab = array();

	if ($id_groupe != "") {
		$sql = "SELECT DISTINCT eu.login, nom FROM engagements_user eu, 
									engagements e, 
									j_eleves_groupes jeg 
								WHERE e.id=eu.id_engagement AND 
									e.conseil_de_classe='yes' AND 
									e.ConcerneEleve='yes' AND 
									jeg.login=eu.login AND 
									jeg.id_groupe='" . $id_groupe . "';";
	} else {
		$sql = "SELECT DISTINCT eu.login, nom FROM engagements_user eu, 
									engagements e, 
									j_eleves_classes jec 
								WHERE e.id=eu.id_engagement AND 
									e.conseil_de_classe='yes' AND 
									e.ConcerneEleve='yes' AND 
									jec.login=eu.login AND 
									jec.id_classe='" . $id_classe . "';";
	}
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$lig->login][] = $lig->nom;
		}
	}

	return $tab;
}

function get_tab_modalites_accompagnement_classe_ou_groupe($id_classe, $id_groupe = "", $periode = "") {
	global $mysqli;

	$tab = array();

	if ($id_groupe != "") {
		if ($periode == "") {
			$sql = "SELECT DISTINCT e.login, jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e, j_eleves_groupes jeg WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login=jeg.login AND jeg.id_groupe='" . $id_groupe . "' AND jeg.periode=jmae.periode ORDER BY e.login, ma.code, jmae.periode;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				//$temoin_commentaire_non_vide=0;
				$cpt = 0;
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab["login"][$lig["login"]]["periode"][$lig["periode"]][$cpt] = $lig;
					$tab["login"][$lig["login"]]["code"][$lig["code"]][$cpt] = $lig;

					$tab["periode"][$lig["periode"]]["login"][$lig["login"]][$cpt] = $lig;
					$tab["code"][$lig["code"]]["login"][$lig["login"]][$cpt] = $lig;

					$cpt++;
				}
			}
		} else {
			$sql = "SELECT DISTINCT e.login, jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e, j_eleves_groupes jeg WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login=jeg.login AND jeg.id_groupe='" . $id_groupe . "' AND jeg.periode=jmae.periode AND jmae.periode='" . $periode . "' ORDER BY e.login, ma.code, jmae.periode;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				$cpt = 0;
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab["login"][$lig["login"]][] = $lig;
					$tab["code"][$lig["code"]][] = $lig;
				}
			}
		}
	} else {
		if ($periode == "") {
			$sql = "SELECT DISTINCT e.login, jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e, j_eleves_classes jec WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login=jec.login AND jec.id_classe='" . $id_classe . "' AND jec.periode=jmae.periode ORDER BY e.login, ma.code, jmae.periode;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				//$temoin_commentaire_non_vide=0;
				$cpt = 0;
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab["login"][$lig["login"]]["periode"][$lig["periode"]][$cpt] = $lig;
					$tab["login"][$lig["login"]]["code"][$lig["code"]][$cpt] = $lig;

					$tab["periode"][$lig["periode"]]["login"][$lig["login"]][$cpt] = $lig;
					$tab["code"][$lig["code"]]["login"][$lig["login"]][$cpt] = $lig;

					$cpt++;
				}
			}
		} else {
			$sql = "SELECT DISTINCT e.login, jmae.*, ma.libelle FROM modalites_accompagnement ma, j_modalite_accompagnement_eleve jmae, eleves e, j_eleves_classes jec WHERE ma.code=jmae.code AND jmae.id_eleve=e.id_eleve AND e.login=jeg.login AND jec.id_classe='" . $id_classe . "' AND jec.periode=jmae.periode AND jmae.periode='" . $periode . "' ORDER BY e.login, ma.code, jmae.periode;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				$cpt = 0;
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab["login"][$lig["login"]][] = $lig;
					$tab["code"][$lig["code"]][] = $lig;
				}
			}
		}
	}

	return $tab;
}


function get_tab_engagements_abs2() {
	global $mysqli;

	// Engagements
	$tab_engagements = get_tab_engagements("eleve");
	$tab_engagements_abs2 = array();

	if ((isset($tab_engagements['indice'])) && (count($tab_engagements['indice']) > 0)) {
		foreach ($tab_engagements['id_engagement'] as $current_id => $engagement) {
			if (getSettingAOui('abs2_grp_engagement_' . $current_id)) {
				$tab_engagements_abs2[] = $current_id;
			}
		}
	}

	return $tab_engagements_abs2;
}

function get_tab_modalites_accompagnement_abs2() {
	global $mysqli;

	// Modalités d'accompagnement
	$tab_modalite_accompagnement = get_tab_modalites_accompagnement();
	$tab_modalite_accompagnement_abs2 = array();

	if ((isset($tab_modalite_accompagnement["code"])) && (count($tab_modalite_accompagnement["code"]) > 0)) {
		foreach ($tab_modalite_accompagnement["code"] as $current_code => $accompagnement) {
			if (getSettingAOui('abs2_grp_accompagnement_' . $current_code)) {
				$tab_modalite_accompagnement_abs2[] = $current_code;
			}
		}
	}

	return $tab_modalite_accompagnement_abs2;
}

function acces_param_pointage_discipline() {
	global $mysqli;
	if ($_SESSION['statut'] == 'administrateur') {
		return true;
	} elseif (acces("/mod_discipline/param_pointages.php", $_SESSION['statut'])) {
		$sql = "CREATE TABLE IF NOT EXISTS b_droits_divers (login varchar(50) NOT NULL default '', nom_droit varchar(50) NOT NULL default '', valeur_droit varchar(50) NOT NULL default '') ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create = mysqli_query($GLOBALS["mysqli"], $sql);

		$sql = "SELECT 1=1 FROM b_droits_divers WHERE login='" . $_SESSION['login'] . "' AND nom_droit='mod_discipline_param_pointages' AND valeur_droit='y';";
		$test = mysqli_query($GLOBALS["mysqli"], $sql);
		if (mysqli_num_rows($test) > 0) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function acces_extraire_pointage_discipline() {
	global $mysqli;
	// Ajouter des paramétrages supplémentaires de restrictions d'accès par la suite?
	return acces("/mod_discipline/saisie_pointages.php", $_SESSION['statut']);
}

function get_tab_groupes_grp_groupes() {
	global $mysqli;

	$tab = array();
	$sql = "SELECT DISTINCT ggg.id_groupe FROM grp_groupes_admin gga, grp_groupes_groupes ggg WHERE gga.login='" . $_SESSION['login'] . "' AND gga.id_grp_groupe=ggg.id_grp_groupe;";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[] = $lig->id_groupe;
		}
	}
	return $tab;
}

/**
 * Retourne un pays à partir de son code_pays
 *
 * @param string $code_pays
 * @return string Le pays
 */
function get_pays($code_pays) {
	global $mysqli;
	$retour = "PAYS INCONNU";

	$sql = "SELECT * FROM pays WHERE code_pays='$code_pays';";
	//echo "$sql<br />";
	$res_pays = mysqli_query($mysqli, $sql);
	if ($res_pays->num_rows > 0) {
		$lig_pays = $res_pays->fetch_object();
		$res_pays->close();
		$retour = $lig_pays->nom_pays;
	}
	return $retour;
}

function get_last_class_ele($ele_login, $champs = "all") {
	global $mysqli;

	$sql = "SELECT c.classe, j.id_classe FROM classes c, j_eleves_classes j WHERE (j.login = '$ele_login' and j.id_classe = c.id) order by j.periode DESC LIMIT 1;";
	$res_class = mysqli_query($mysqli, $sql);
	if ($res_class->num_rows > 0) {
		$lig_tmp = $res_class->fetch_object();
		if ($champs == "all") {
			$tab_classe = array();
			$tab_classe['id_classe'] = $lig_tmp->id_classe;
			$tab_classe['classe'] = $lig_tmp->classe;
			$res_class->close();
			return $tab_classe;
		} elseif ($champs == "id_classe") {
			return $lig_tmp->id_classe;
		} else {
			return $lig_tmp->classe;
		}
	} else {
		if ($champs == "all") {
			return $tab_classe;
		} else {
			return "";
		}
	}
}

function get_tab_types_LVR() {
	global $mysqli;

	/*
	<!-- Liste des langues régionales possibles pour le niveau A2 -->
	<langues-culture-regionale>
		<langue-culture-regionale code="BAS" libelle="Basque"/>
		<langue-culture-regionale code="BRE" libelle="Breton"/>
		<langue-culture-regionale code="COR" libelle="Corse"/>
		<langue-culture-regionale code="OCC" libelle="Occitan langue d'oc"/>
		<langue-culture-regionale code="ALS" libelle="Langue régionale d'Alsace"/>
		<langue-culture-regionale code="MOS" libelle="Langue régionale des pays mosellans"/>
	</langues-culture-regionale>

	Devenu

	<langues-culture-regionale>
		<langue-culture-regionale code="AUC" libelle="Aucun"/>
		<langue-culture-regionale code="BAQ" libelle="Basque"/>
		<langue-culture-regionale code="BRE" libelle="Breton"/>
		<langue-culture-regionale code="CAT" libelle="Catalan"/>
		<langue-culture-regionale code="COS" libelle="Corse"/>
		<langue-culture-regionale code="GSW" libelle="Langue régionale d'Alsace"/>
		<langue-culture-regionale code="OCI" libelle="Occitan langue d'oc"/>
	</langues-culture-regionale>

	// A partir de fin avril 2019
	<langues-culture-regionale>
		<langue-culture-regionale code="AUC" libelle="Aucun"/>
		<langue-culture-regionale code="BAQ" libelle="Basque"/>
		<langue-culture-regionale code="BRE" libelle="Breton"/>
		<langue-culture-regionale code="CAT" libelle="Catalan"/>
		<langue-culture-regionale code="COS" libelle="Corse"/>
		<langue-culture-regionale code="CPF" libelle="Créole" />
		<langue-culture-regionale code="FUD" libelle="Futunien" />
		<langue-culture-regionale code="GAL" libelle="Gallo" />
		<langue-culture-regionale code="GSW" libelle="Langue régionale d'Alsace"/>
		<langue-culture-regionale code="MEL" libelle="Langues mélanésiennes" />
		<langue-culture-regionale code="MOL" libelle="Langues régionales des pays mosellans" />
		<langue-culture-regionale code="OCI" libelle="Occitan langue d'oc"/>
		<langue-culture-regionale code="TAH" libelle="Tahitien" />
		<langue-culture-regionale code="WLS" libelle="Wallisien" />
	</langues-culture-regionale>
	*/

	$tab = array();
	$tab["indice"] = array();
	$tab["code"] = array();

	$tab["indice"][0]["code"] = 'BAQ';
	$tab["indice"][0]["libelle"] = 'Basque';
	$tab["code"]['BAQ'] = 'Basque';

	$tab["indice"][1]["code"] = 'BRE';
	$tab["indice"][1]["libelle"] = 'Breton';
	$tab["code"]['BRE'] = 'Breton';

	$tab["indice"][2]["code"] = 'CAT';
	$tab["indice"][2]["libelle"] = 'Catalan';
	$tab["code"]['CAT'] = 'Catalan';

	$tab["indice"][3]["code"] = 'COS';
	$tab["indice"][3]["libelle"] = 'Corse';
	$tab["code"]['COS'] = 'Corse';

	$tab["indice"][4]["code"] = 'CPF';
	$tab["indice"][4]["libelle"] = "Créole";
	$tab["code"]['CPF'] = "Créole";

	$tab["indice"][5]["code"] = 'FUD';
	$tab["indice"][5]["libelle"] = "Futunien";
	$tab["code"]['FUD'] = "Futunien";

	$tab["indice"][6]["code"] = 'GAL';
	$tab["indice"][6]["libelle"] = "Gallo";
	$tab["code"]['GAL'] = "Gallo";

	$tab["indice"][7]["code"] = 'GSW';
	$tab["indice"][7]["libelle"] = "Langue régionale d'Alsace";
	$tab["code"]['GSW'] = "Langue régionale d'Alsace";

	$tab["indice"][8]["code"] = 'MEL';
	$tab["indice"][8]["libelle"] = "Langues mélanésiennes";
	$tab["code"]['MEL'] = "Langues mélanésiennes";

	$tab["indice"][9]["code"] = 'MOL';
	$tab["indice"][9]["libelle"] = "Langues régionales des pays mosellans";
	$tab["code"]['MOL'] = "Langues régionales des pays mosellans";

	$tab["indice"][10]["code"] = 'OCI';
	$tab["indice"][10]["libelle"] = "Occitan langue d'oc";
	$tab["code"]['OCI'] = "Occitan langue d'oc";

	$tab["indice"][11]["code"] = 'TAH';
	$tab["indice"][11]["libelle"] = "Tahitien";
	$tab["code"]['TAH'] = "Tahitien";

	$tab["indice"][12]["code"] = 'WLS';
	$tab["indice"][12]["libelle"] = "Wallisien";
	$tab["code"]['WLS'] = "Wallisien";

	/*
	$sql="SELECT * FROM nomenclatures_valeurs WHERE type='langue_vivante_regionale' ORDER BY code;";
	//echo "$sql<br />";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		$cpt=0;
		while($lig=mysqli_fetch_assoc($res)) {
			$tab["indice"][$cpt]=$lig;
			$tab["code"][$lig["code"]]=$lig;
			$cpt++;
		}
	}
	*/
	return $tab;
}

function check_b_droit($login, $droit) {
	global $mysqli;

	$sql = "SELECT 1=1 FROM b_droits_divers WHERE login='" . $login . "' AND nom_droit='" . $droit . "' AND (valeur_droit='y' OR valeur_droit='yes');";
	$res_droit = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_droit) > 0) {
		return true;
	} else {
		return false;
	}
}

function get_tab_app_d_apres_moy($login) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT * FROM commentaires_types_d_apres_moy WHERE login='" . $login . "' ORDER BY note_min, note_max;";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab[$lig['note_min']] = $lig;
		}
	}

	return $tab;
}

/**
 * Retourne le tableau des moyennes et appréciations dans le module années antérieures pour un élève donné dans une matière donnée
 *
 * @param string $login_ele login de l'élève
 * @param string $matiere nom complet de matière
 *
 * @return array
 */
function get_tab_annees_anterieures_ele_matiere($login_ele, $matiere) {
	$tab = array();

	$sql = "SELECT ad.* FROM archivage_disciplines ad, eleves e WHERE ad.matiere LIKE '" . $matiere . "' AND ad.INE=e.no_gep AND e.login='" . $login_ele . "' ORDER BY annee, num_periode;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_assoc($res)) {
			$tab[] = $lig;
		}
	}

	return $tab;
}

function get_tab_actions_categories($id_categorie = '') {
	global $mysqli;

	$tab_categories_actions = array();

	if ($_SESSION['statut'] == 'administrateur') {
		if ($id_categorie == '') {
			$sql = "SELECT * FROM mod_actions_categories ORDER BY nom, description;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab_categories_actions[$lig['id']] = $lig;
					$tab_categories_actions[$lig['id']]['gestionnaire'] = array();
					$sql = "SELECT * FROM mod_actions_gestionnaires WHERE id_categorie='" . $lig['id'] . "';";
					//echo "$sql<br />";
					$res2 = mysqli_query($mysqli, $sql);
					if (mysqli_num_rows($res2) > 0) {
						while ($lig2 = mysqli_fetch_assoc($res2)) {
							$tab_categories_actions[$lig['id']]['gestionnaire'][] = $lig2['login_user'];
						}
					}
				}
			}
		} else {
			$sql = "SELECT * FROM mod_actions_categories WHERE id='" . $id_categorie . "';";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab_categories_actions = $lig;
					$tab_categories_actions['gestionnaire'] = array();
					$sql = "SELECT * FROM mod_actions_gestionnaires WHERE id_categorie='" . $lig['id'] . "';";
					//echo "$sql<br />";
					$res2 = mysqli_query($mysqli, $sql);
					if (mysqli_num_rows($res2) > 0) {
						while ($lig2 = mysqli_fetch_assoc($res2)) {
							$tab_categories_actions['gestionnaire'][] = $lig2['login_user'];
						}
					}
				}
			}
		}
	} elseif ($_SESSION['statut'] == 'eleve') {
		$sql = "SELECT mac.* FROM mod_actions_categories mac, 
						mod_actions_action maa, 
						mod_actions_inscriptions mai
					WHERE mai.id_action=maa.id AND 
						maa.id_categorie=mac.id AND 
						mai.login_ele='" . $_SESSION['login'] . "' 
					ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab_categories_actions[$lig['id']] = $lig;
			}
		}
	} elseif ($_SESSION['statut'] == 'responsable') {
		$sql = "SELECT mac.* FROM mod_actions_categories mac, 
						mod_actions_action maa, 
						mod_actions_inscriptions mai
					WHERE mai.id_action=maa.id AND 
						maa.id_categorie=mac.id AND 
						mai.login_ele IN (SELECT e.login FROM eleves e, 
												responsables2 r, 
												resp_pers rp 
											WHERE e.ele_id=r.ele_id AND 
												r.pers_id=rp.pers_id AND 
												rp.login='" . $_SESSION['login'] . "') 
					ORDER BY nom, description;";
		//echo "$sql<br />";
		$res = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res) > 0) {
			while ($lig = mysqli_fetch_assoc($res)) {
				$tab_categories_actions[$lig['id']] = $lig;
			}
		}
	} else {
		if ($id_categorie == '') {
			$sql = "SELECT mac.* FROM mod_actions_categories mac, 
							mod_actions_gestionnaires mag 
						WHERE mac.id=mag.id_categorie AND 
							mag.login_user='" . $_SESSION['login'] . "' 
						ORDER BY nom, description;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab_categories_actions[$lig['id']] = $lig;
					$tab_categories_actions[$lig['id']]['gestionnaire'] = array();
					$sql = "SELECT * FROM mod_actions_gestionnaires WHERE id_categorie='" . $lig['id'] . "';";
					//echo "$sql<br />";
					$res2 = mysqli_query($mysqli, $sql);
					if (mysqli_num_rows($res2) > 0) {
						while ($lig2 = mysqli_fetch_assoc($res2)) {
							$tab_categories_actions[$lig['id']]['gestionnaire'][] = $lig2['login_user'];
						}
					}
				}
			}
		} else {
			$sql = "SELECT mac.* FROM mod_actions_categories mac, 
							mod_actions_gestionnaires mag 
						WHERE mac.id='" . $id_categorie . "' AND 
							mac.id=mag.id_categorie AND 
							mag.login_user='" . $_SESSION['login'] . "' 
						ORDER BY nom, description;";
			//echo "$sql<br />";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_assoc($res)) {
					$tab_categories_actions = $lig;
					$tab_categories_actions['gestionnaire'] = array();
					$sql = "SELECT * FROM mod_actions_gestionnaires WHERE id_categorie='" . $lig['id'] . "';";
					//echo "$sql<br />";
					$res2 = mysqli_query($mysqli, $sql);
					if (mysqli_num_rows($res2) > 0) {
						while ($lig2 = mysqli_fetch_assoc($res2)) {
							$tab_categories_actions['gestionnaire'][] = $lig2['login_user'];
						}
					}
				}
			}
		}
	}

	return $tab_categories_actions;
}

function acces_mod_action($id_categorie = '') {
	global $mysqli;

	if ($_SESSION['statut'] == 'administrateur') {
		return true;
	} else {
		if (getSettingAOui('active_mod_actions')) {
			if ($id_categorie == '') {
				$sql = "SELECT 1=1 FROM mod_actions_gestionnaires WHERE login_user='" . $_SESSION['login'] . "';";
				$test = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($test) > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				$sql = "SELECT 1=1 FROM mod_actions_gestionnaires WHERE id_categorie='" . $id_categorie . "' AND login_user='" . $_SESSION['login'] . "';";
				$test = mysqli_query($mysqli, $sql);
				if (mysqli_num_rows($test) > 0) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}
}

function check_date($date) {
	if (!preg_match('|^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$|', $date)) {
		return false;
	} else {
		$tmp_tab = explode("/", $date);
		$day = $tmp_tab[0];
		$month = $tmp_tab[1];
		$year = $tmp_tab[2];
		if ($year < 100) {
			$year = "20" . $year;
		}
		if (!checkdate($month, $day, $year)) {
			return false;
		} else {
			return true;
		}
	}
}


function get_action($id_action) {
	global $mysqli;

	$action = array();

	$sql = "SELECT * FROM mod_actions_action WHERE id='" . $id_action . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_assoc($res);

		$action = $lig;
		$action['eleves'] = array();
		$action['eleves_list'] = array();
		$action['presents'] = array();

		// Récupérer les inscrits?
		$sql = "SELECT e.nom, e.prenom, e.elenoet, mai.* FROM mod_actions_inscriptions mai, 
									eleves e 
								WHERE mai.id_action='" . $lig['id'] . "' AND 
									mai.login_ele=e.login 
								ORDER BY e.nom, e.prenom;";
		$res2 = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($res2) > 0) {
			while ($lig2 = mysqli_fetch_assoc($res2)) {
				$action['eleves'][] = $lig2;
				$action['eleves_list'][] = $lig2['login_ele'];
				if ($lig2['presence'] == 'y') {
					$action['presents'][$lig2['login_ele']] = $lig2;
				}
			}
		}
	}

	return $action;
}

// 20190101
function get_acces_adresse_resp($login_ele, $id_classe = '', $login_resp = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesAdresseTousParentsProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;

			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			} elseif ($login_resp != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_classes jec, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jec.id_classe=jgc.id_classe AND 
									jgc.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			}

			if (($eleve_classe_prof) || ($eleve_groupe_prof)) {
				/*
				if(getSettingAOui('GepiAccesGestElevesProf')) {
					$retour=true;
				}
				*/

				if (getSettingAOui('GepiAccesAdresseParentsRespProf')) {
					$retour = true;
				}
			}

			if (!$retour) {
				//echo "is_pp(".$_SESSION['login'].", '', '', '', $login_resp)<br />";
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe))) ||
					(($login_resp != '') && (is_pp($_SESSION['login'], '', '', '', $login_resp)))) {
					if (getSettingAOui('GepiAccesGestElevesProfP')) {
						$acces_adresse_responsable = true;
					}

					if (getSettingAOui('GepiAccesAdresseParentsRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesAdresseParents', $_SESSION['statut']))) {
		$retour = true;
	}

	return $retour;
}

function get_acces_tel_resp($login_ele, $id_classe = '', $login_resp = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesTelTousParentsProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;


			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			} elseif ($login_resp != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_classes jec, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jec.id_classe=jgc.id_classe AND 
									jgc.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			}

			if (($eleve_classe_prof) || ($eleve_groupe_prof)) {
				/*
				if(getSettingAOui('GepiAccesGestElevesProf')) {
					$retour=true;
				}
				*/

				if (!$retour) {
					if (getSettingAOui('GepiAccesTelParentsRespProf')) {
						$retour = true;
					}
				}
			}

			if (!$retour) {
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe))) ||
					(($login_resp != '') && (is_pp($_SESSION['login'], '', '', '', $login_resp)))) {
					if (getSettingAOui('GepiAccesTelParentsRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesTelParents', $_SESSION['statut']))) {
		$retour = true;
	}

	return $retour;
}

function get_acces_mail_resp($login_ele, $id_classe = '', $login_resp = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesMailTousParentsProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;


			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			} elseif ($login_resp != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_classes jec, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jec.id_classe=jgc.id_classe AND 
									jgc.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp, 
									responsables2 r, 
									resp_pers rp 
								WHERE rp.login='" . $login_resp . "' AND 
									r.pers_id=rp.pers_id AND 
									(r.resp_legal='1' OR r.resp_legal='2') AND 
									e.ele_id=r.ele_id AND 
									jec.login=e.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			}


			if (($eleve_classe_prof == "y") || ($eleve_groupe_prof == "y")) {
				if (getSettingAOui('GepiAccesMailParentsRespProf')) {
					$retour = true;
				}
			}

			if (!$retour) {
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe))) ||
					(($login_resp != '') && (is_pp($_SESSION['login'], '', '', '', $login_resp)))) {
					if (getSettingAOui('GepiAccesMailParentsRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesMailParents', $_SESSION['statut']))) {
		$retour = true;
	}

	return $retour;
}

// Les adresses élèves ne sont pas enregistrées dans Gepi pour le moment
// Les droits associés ne sont pas activés dans gestion/droits_acces.php
function get_acces_adresse_ele($login_ele, $id_classe = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesAdresseTousElevesProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;

			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			}

			if (($eleve_classe_prof) || ($eleve_groupe_prof)) {
				/*
				if(getSettingAOui('GepiAccesGestElevesProf')) {
					$retour=true;
				}
				*/

				if (getSettingAOui('GepiAccesAdresseElevesRespProf')) {
					$retour = true;
				}
			}

			if (!$retour) {
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe)))) {
					/*
					if(getSettingAOui('GepiAccesGestElevesProfP')) {
						$acces_adresse_responsable=true;
					}
					*/

					if (getSettingAOui('GepiAccesAdresseElevesRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesAdresseEleves', $_SESSION['statut']))) {
		$retour = true;
		//Le droit AccesAdresseEleves dans droits_speciaux n'existe pas actuellement
	}

	return $retour;
}

function get_acces_tel_ele($login_ele, $id_classe = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesTelTousElevesProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;


			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			}

			if (($eleve_classe_prof) || ($eleve_groupe_prof)) {
				/*
				if(getSettingAOui('GepiAccesGestElevesProf')) {
					$retour=true;
				}
				*/

				if (!$retour) {
					if (getSettingAOui('GepiAccesTelElevesRespProf')) {
						$retour = true;
					}
				}
			}

			if (!$retour) {
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe)))) {
					if (getSettingAOui('GepiAccesTelElevesRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesTelEleves', $_SESSION['statut']))) {
		$retour = true;
	}

	return $retour;
}

function get_acces_mail_ele($login_ele, $id_classe = '') {
	global $mysqli;

	$retour = false;

	if ($_SESSION['statut'] == 'administrateur') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesMailTousElevesProf')) {
			$retour = true;
		}

		if (!$retour) {
			$eleve_classe_prof = false;
			$eleve_groupe_prof = false;


			if ($login_ele != '') {
				//=====================================
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.login='" . $login_ele . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
				//=====================================
				if (!$eleve_classe_prof) {
					$sql = "SELECT 1=1 FROM j_eleves_groupes jeg,
										j_groupes_professeurs jgp
									WHERE jeg.login='" . $login_ele . "' AND
											jeg.id_groupe=jgp.id_groupe AND
											jgp.login='" . $_SESSION['login'] . "';";
					//echo "$sql<br />";
					$test_eleve_groupe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

					if (mysqli_num_rows($test_eleve_groupe_prof) > 0) {
						$eleve_groupe_prof = true;
					}
				}
				//=====================================
			} elseif ($id_classe != '') {
				$sql = "SELECT 1=1 FROM j_eleves_classes jec,
									j_groupes_classes jgc,
									j_groupes_professeurs jgp
								WHERE jec.id_classe='" . $id_classe . "' AND
										jec.id_classe=jgc.id_classe AND
										jgc.id_groupe=jgp.id_groupe AND
										jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$test_eleve_classe_prof = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($test_eleve_classe_prof) > 0) {
					$eleve_classe_prof = true;
				}
			}

			if (($eleve_classe_prof == "y") || ($eleve_groupe_prof == "y")) {
				if (getSettingAOui('GepiAccesMailElevesRespProf')) {
					$retour = true;
				}
			}

			if (!$retour) {
				if ((($login_ele != '') && (is_pp($_SESSION['login'], '', $login_ele))) ||
					(($id_classe != '') && (is_pp($_SESSION['login'], $id_classe)))) {
					if (getSettingAOui('GepiAccesMailElevesRespPP')) {
						$retour = true;
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesMailEleves', $_SESSION['statut']))) {
		$retour = true;
	}

	return $retour;
}

// Tableau des élèves pour lesquels l'utilisateur a accès au numéro de téléphone élève
function get_tab_acces_tel_ele() {
	global $mysqli;

	$tab = array();
	$tab['acces_global'] = false;
	$tab['login_ele'] = array();

	if ($_SESSION['statut'] == 'administrateur') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesTelTousElevesProf')) {
			$tab['acces_global'] = true;
		} else {
			if (getSettingAOui('GepiAccesTelElevesRespProf')) {
				$sql = "SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg,
									j_groupes_professeurs jgp
								WHERE jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						$tab['login_ele'][] = $lig->login;
					}
				}
			}

			if (getSettingAOui('GepiAccesTelElevesRespPP')) {
				$sql = "SELECT DISTINCT login FROM j_eleves_professeurs
								WHERE professeur='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if (!in_array($lig->login, $tab['login_ele'])) {
							$tab['login_ele'][] = $lig->login;
						}
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesTelEleves', $_SESSION['statut']))) {
		$tab['acces_global'] = true;
	}

	return $tab;
}

function get_tab_acces_mail_ele() {
	global $mysqli;

	$tab = array();
	$tab['acces_global'] = false;
	$tab['login_ele'] = array();

	if ($_SESSION['statut'] == 'administrateur') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesMailTousElevesProf')) {
			$tab['acces_global'] = true;
		} else {
			if (getSettingAOui('GepiAccesMailElevesRespProf')) {
				$sql = "SELECT DISTINCT jeg.login FROM j_eleves_groupes jeg,
									j_groupes_professeurs jgp
								WHERE jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						$tab['login_ele'][] = $lig->login;
					}
				}
			}

			if (getSettingAOui('GepiAccesMailElevesRespPP')) {
				$sql = "SELECT DISTINCT login FROM j_eleves_professeurs
								WHERE professeur='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if (!in_array($lig->login, $tab['login_ele'])) {
							$tab['login_ele'][] = $lig->login;
						}
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesMailEleves', $_SESSION['statut']))) {
		$tab['acces_global'] = true;
	}

	return $tab;
}

function get_tab_acces_adresse_resp() {
	global $mysqli;

	$tab = array();
	$tab['acces_global'] = false;
	$tab['pers_id'] = array();

	if ($_SESSION['statut'] == 'administrateur') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesAdresseTousParentsProf')) {
			$tab['acces_global'] = true;
		} else {
			if (getSettingAOui('GepiAccesAdresseParentsRespProf')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp
								WHERE r.ele_id=e.ele_id AND 
									e.login=jeg.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						$tab['pers_id'][] = $lig->pers_id;
					}
				}
			}

			if (getSettingAOui('GepiAccesAdresseParentsRespPP')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_professeurs jep 
								WHERE r.ele_id=e.ele_id AND 
									e.login=jep.login AND 
									jep.professeur='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if (!in_array($lig->pers_id, $tab['pers_id'])) {
							$tab['pers_id'][] = $lig->pers_id;
						}
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesAdresseParents', $_SESSION['statut']))) {
		$tab['acces_global'] = true;
	}

	return $tab;
}

function get_tab_acces_tel_resp() {
	global $mysqli;

	$tab = array();
	$tab['acces_global'] = false;
	$tab['pers_id'] = array();

	if ($_SESSION['statut'] == 'administrateur') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesTelTousParentsProf')) {
			$tab['acces_global'] = true;
		} else {
			if (getSettingAOui('GepiAccesTelTousParentsProf')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp
								WHERE r.ele_id=e.ele_id AND 
									e.login=jeg.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						$tab['pers_id'][] = $lig->pers_id;
					}
				}
			}

			if (getSettingAOui('GepiAccesTelParentsRespPP')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_professeurs jep 
								WHERE r.ele_id=e.ele_id AND 
									e.login=jep.login AND 
									jep.professeur='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if (!in_array($lig->pers_id, $tab['pers_id'])) {
							$tab['pers_id'][] = $lig->pers_id;
						}
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesTelParents', $_SESSION['statut']))) {
		$tab['acces_global'] = true;
	}

	return $tab;
}

function get_tab_acces_mail_resp() {
	global $mysqli;

	$tab = array();
	$tab['acces_global'] = false;
	$tab['pers_id'] = array();

	if ($_SESSION['statut'] == 'administrateur') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'scolarite') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		$tab['acces_global'] = true;
	} elseif ($_SESSION['statut'] == 'professeur') {

		if (getSettingAOui('GepiAccesMailTousParentsProf')) {
			$tab['acces_global'] = true;
		} else {
			if (getSettingAOui('GepiAccesMailParentsRespProf')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_groupes jeg,
									j_groupes_professeurs jgp
								WHERE r.ele_id=e.ele_id AND 
									e.login=jeg.login AND 
									jeg.id_groupe=jgp.id_groupe AND 
									jgp.login='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						$tab['pers_id'][] = $lig->pers_id;
					}
				}
			}

			if (getSettingAOui('GepiAccesMailParentsRespPP')) {
				$sql = "SELECT DISTINCT r.pers_id FROM responsables2 r,
									eleves e,
									j_eleves_professeurs jep 
								WHERE r.ele_id=e.ele_id AND 
									e.login=jep.login AND 
									jep.professeur='" . $_SESSION['login'] . "';";
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);

				if (mysqli_num_rows($res) > 0) {
					while ($lig = mysqli_fetch_object($res)) {
						if (!in_array($lig->pers_id, $tab['pers_id'])) {
							$tab['pers_id'][] = $lig->pers_id;
						}
					}
				}
			}
		}
	} elseif (($_SESSION['statut'] == 'autre') && (acces('/AccesMailParents', $_SESSION['statut']))) {
		$tab['acces_global'] = true;
	}

	return $tab;
}

function etat_verrouillage_groupe_periode($id_groupe, $periode, $tab_id_classe_exclu = array()) {
	global $mysqli;

	$tab = array();
	$tab['P'] = 0;
	$tab['O'] = 0;
	$tab['N'] = 0;
	$tab['classes'] = array();
	$tab['classes']['P'] = '';
	$tab['classes']['O'] = '';
	$tab['classes']['N'] = '';

	$sql = "SELECT p.id_classe, verouiller FROM periodes p, j_groupes_classes jgc WHERE p.id_classe=jgc.id_classe AND jgc.id_groupe='" . $id_groupe . "' AND num_periode='" . $periode . "';";
	$res = mysqli_query($mysqli, $sql);
	while ($lig = mysqli_fetch_object($res)) {
		if (!in_array($lig->id_classe, $tab_id_classe_exclu)) {
			$tab[$lig->verouiller]++;
			if ($tab['classes'][$lig->verouiller] != '') {
				$tab['classes'][$lig->verouiller] .= ', ';
			}
			$tab['classes'][$lig->verouiller] .= get_nom_classe($lig->id_classe);
		}
	}

	return $tab;
}

/*
function is_saisie_possible_groupe_periode($id_groupe, $periode) {
	global $mysqli;

	$sql="SELECT 1=1 FROM periodes p, j_groupes_classes jgc WHERE p.id_classe=jgc.id_classe AND jgc.id_groupe='".$id_groupe."' AND p.num_periode='".$periode."' AND p.verouiller!='N';";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		// Saisie peut-être possible pour une des classes du groupe
		return false;
	}
	else {
		return true;
	}
}
*/

function get_tab_options_sconet() {
	global $mysqli;

	// Recherche des options renseignées dans Sconet:
	$tab_options_sconet = array();
	$tab_options_sconet['code'] = array();
	$tab_options_sconet['matiere'] = array();
	$sql = "SELECT * FROM nomenclatures_valeurs WHERE nom='option_sconet_saisie' AND valeur='y';";
	//echo "$sql<br />";
	$res_options_sconet = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res_options_sconet) > 0) {
		while ($lig_options_sconet = mysqli_fetch_object($res_options_sconet)) {
			//$sql="SELECT * FROM nomenclatures WHERE code='$lig_options_sconet->code' AND type='matiere';";
			$sql = "SELECT * FROM matieres WHERE code_matiere='" . $lig_options_sconet->code . "';";
			$res_opt_courante = mysqli_query($GLOBALS["mysqli"], $sql);
			if (mysqli_num_rows($res_opt_courante) > 0) {
				//$tab_options_sconet['code'][]=$lig_options_sconet->code;

				while ($lig_opt_courante = mysqli_fetch_object($res_opt_courante)) {
					$tab_options_sconet['matiere'][$lig_opt_courante->matiere]['code'] = $lig_options_sconet->code;
					$tab_options_sconet['code'][$lig_options_sconet->code]['matieres'][] = $lig_opt_courante->matiere;
				}
			}
		}
	}
	return $tab_options_sconet;
}

function acces_saisie_avis2($id_classe) {
	global $mysqli;

	$retour = false;
	if (($_SESSION['statut'] == 'scolarite') && (getSettingAOui("GepiRubConseilScol"))) {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'cpe') {
		if (getSettingAOui("GepiRubConseilCpeTous")) {
			$retour = true;
		} elseif (getSettingAOui("GepiRubConseilCpe")) {
			$sql = "SELECT 1=1 FROM j_eleves_cpe jecpe, j_eleves_classes jec 
			WHERE jec.id_classe='" . $id_classe . "' AND 
			jec.login=jecpe.e_login AND 
			jecpe.cpe_login = '" . $_SESSION['login'] . "';";
			$test_cpe_suivi = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($test_cpe_suivi) > 0) {
				$retour = true;
			}
		}
	} elseif ($_SESSION['statut'] == 'secours') {
		$retour = true;
	} elseif ($_SESSION['statut'] == 'professeur') {
		if (getSettingAOui("GepiRubConseilProf")) {
			if (is_pp($_SESSION['login'], $id_classe)) {
				$retour = true;
			}
		}
	}

	return $retour;
}

function is_prof_groupe($login_prof, $id_groupe) {
	global $mysqli;

	$is_prof_groupe = false;

	$sql = "SELECT 1=1 FROM j_groupes_professeurs jgp
					WHERE jgp.login='" . $login_prof . "' AND 
						jgp.id_groupe='$id_groupe' LIMIT 1;";
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		$is_prof_groupe = true;
		$res->close();
	}

	return $is_prof_groupe;
}

function etat_verrouillage_aid_periode($id_aid, $periode, $tab_id_classe_exclu = array()) {
	global $mysqli;

	$tab = array();
	$tab['P'] = 0;
	$tab['O'] = 0;
	$tab['N'] = 0;
	$tab['classes'] = array();
	$tab['classes']['P'] = '';
	$tab['classes']['O'] = '';
	$tab['classes']['N'] = '';

	$sql = "SELECT DISTINCT p.id_classe, p.verouiller FROM periodes p, 
						j_eleves_classes jec, 
						j_aid_eleves jae
					WHERE p.id_classe=jec.id_classe AND 
						jae.login=jec.login AND 
						jae.id_aid='" . $id_aid . "' AND 
						jec.periode=p.num_periode AND 
						p.num_periode='" . $periode . "';";
	//echo "$sql<br />";
	$res = mysqli_query($mysqli, $sql);
	while ($lig = mysqli_fetch_object($res)) {
		if (!in_array($lig->id_classe, $tab_id_classe_exclu)) {
			$tab[$lig->verouiller]++;
			if ($tab['classes'][$lig->verouiller] != '') {
				$tab['classes'][$lig->verouiller] .= ', ';
			}
			$tab['classes'][$lig->verouiller] .= get_nom_classe($lig->id_classe);
		}
	}

	return $tab;
}

function traduction_nom_creneau_edt($nom_definie_periode) {
	global $mysqli;

	$retour = $nom_definie_periode;

	$sql = "SELECT * FROM edt_creneaux WHERE nom_definie_periode='$nom_definie_periode';";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		$lig = mysqli_fetch_object($res);
		$retour = "<span title=\"de " . preg_replace('/-00$/', '', $lig->heuredebut_definie_periode) . " à " . preg_replace('/-00$/', '', $lig->heurefin_definie_periode) . "\">" . preg_replace('/-00$/', '', $lig->heuredebut_definie_periode) . "-&gt;" . preg_replace('/-00$/', '', $lig->heurefin_definie_periode) . "</span>";
	}

	return $retour;
}

function liste_incidents_eleve_jours($ele_login, $n, $p) {
	global $mysqli;

	$retour = '';

	$date_debut = strftime("%Y-%m-%d", time() - $n * 24 * 3600);
	$date_fin = strftime("%Y-%m-%d", time() - $p * 24 * 3600);
	$sql = "SELECT * FROM s_incidents si, s_protagonistes sp WHERE si.id_incident=sp.id_incident AND sp.login='$ele_login' AND (si.date>='$date_debut' AND si.date<='$date_fin') ORDER BY si.date DESC;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$retour .= "<div class='fieldset_opacite50' style='padding:0.2em;margin-bottom:0.2em'>" . formate_date($lig->date) . " <span style='font-size:small'>(" . traduction_nom_creneau_edt($lig->heure) . ")</span> <strong>" . $lig->nature . "</strong> <em title=\"en qualité de " . $lig->qualite . "\">(" . $lig->qualite . ")</em></div>";
		}
	}

	return $retour;
}

function liste_sanctions_a_venir_eleve($ele_login, $n = 365) {
	global $mysqli;

	$retour = '';

	$date_debut = strftime("%Y-%m-%d");
	$date_fin = strftime("%Y-%m-%d", time() + $n * 24 * 3600);

	$sql = "SELECT ss.nature AS nature_sanction, se.* FROM s_sanctions ss, 
			s_exclusions se 
		WHERE ss.id_sanction=se.id_sanction AND 
			ss.login='$ele_login' AND 
			(se.date_debut>='$date_debut' AND se.date_fin<='$date_fin') 
		ORDER BY se.date_debut DESC;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$retour .= "<div class='fieldset_opacite50' style='padding:0.2em;margin-bottom:0.2em'><strong>" . ucfirst($lig->nature_sanction) . "</strong> du " . formate_date($lig->date_debut) . " <em style='font-size:small'>(" . $lig->heure_debut . ")</em> au " . formate_date($lig->date_fin) . " <em style='font-size:small'>(" . $lig->heure_fin . ")</em></div>";
		}
	}

	$sql = "SELECT ss.nature AS nature_sanction, sr.* FROM s_sanctions ss, 
			s_retenues sr 
		WHERE ss.id_sanction=sr.id_sanction AND 
			ss.login='$ele_login' AND 
			(sr.date>='$date_debut' AND sr.date<='$date_fin') 
		ORDER BY sr.date DESC;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$retour .= "<div class='fieldset_opacite50' style='padding:0.2em;margin-bottom:0.2em'><strong>" . ucfirst($lig->nature_sanction) . "</strong> le " . formate_date($lig->date) . " " . $lig->heure_debut . " <em title='Durée'>(" . $lig->duree . "h)</em>";
			if (trim($lig->lieu) != '') {
				$retour .= " <em style='font-size:small' title=\"Lieu\">(" . $lig->lieu . ")</em>";
			}
			if (trim($lig->materiel) != '') {
				$retour .= " <em style='font-size:small' title=\"Matériel à apporter\">(" . $lig->materiel . ")</em>";
			}
			$retour .= "</div>";
		}
	}

	$sql = "SELECT ss.nature AS nature_sanction, st.* FROM s_sanctions ss, 
			s_travail st 
		WHERE ss.id_sanction=st.id_sanction AND 
			ss.login='$ele_login' AND 
			(st.date_retour>='$date_debut' AND st.date_retour<='$date_fin') 
		ORDER BY st.date_retour DESC;";
	//echo "$sql<br />";
	$res = mysqli_query($GLOBALS["mysqli"], $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$retour .= "<div class='fieldset_opacite50' style='padding:0.2em;margin-bottom:0.2em'><strong>" . ucfirst($lig->nature_sanction) . "</strong> pour le " . formate_date($lig->date_retour) . " " . $lig->heure_retour . " <em title='Travail' style='font-size:small'>(" . $lig->travail . ")</em>";
			$retour .= "</div>";
		}
	}

	return $retour;
}

function menage_engagements_user() {
	global $mysqli;

	$retour = '';

	$sql = "SELECT eu.*, e.nom AS nom_engagement, e.description AS engagement_description, e.type, e.conseil_de_classe, e.code AS code_engagement FROM engagements_user eu, engagements e WHERE eu.id_engagement=e.id AND eu.id_type!=e.type;";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$sql = "DELETE FROM engagements_user WHERE id='" . $lig->id . "';";
			$del = mysqli_query($mysqli, $sql);
			if ($del) {
				$retour .= "<span style='color:green'>Engagement n°" . $lig->id_engagement . " <em title=\"Liaison à une classe ou non au niveau utilisateur, non conforme à ce qui est déclaré globalement pour l'engagement.\">(de type erroné)</em> supprimé pour " . civ_nom_prenom($lig->login) . "</span><br />";
			} else {
				$retour .= "<span style='color:red'>Erreur lors de la suppression de l'engagement n°" . $lig->id_engagement . " <em title=\"Liaison à une classe ou non au niveau utilisateur, non conforme à ce qui est déclaré globalement pour l'engagement.\">(de type erroné)</em> pour " . civ_nom_prenom($lig->login) . "</span><br />";
			}
		}
	}

	$sql = "SELECT * FROM engagements_user eu WHERE id_engagement NOT IN (SELECT id FROM engagements);";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$sql = "DELETE FROM engagements_user eu WHERE id_engagement='" . $lig->id_engagement . "';";
			$del = mysqli_query($mysqli, $sql);
			if ($del) {
				$retour .= "<span style='color:green'>Suppression d'une scorie d'engagement n°" . $lig->id_engagement . " pour " . civ_nom_prenom($lig->login) . " <em>(cet engagement n'existe plus)</em>.</span><br />";
			} else {
				$retour .= "<span style='color:red'>Erreur lors de la suppression d'une scorie d'engagement n°" . $lig->id_engagement . " pour " . civ_nom_prenom($lig->login) . " <em>(cet engagement n'existe plus)</em>.</span><br />";
			}
		}
	}

	return $retour;
}

function cdt_corrige_chemin_archive($texte) {
	$contenu_cor = $texte;
	if (preg_match('|/documents/archives/etablissement/[[:alnum:] _-]*/documents/archives/etablissement/|', $contenu_cor)) {
		//$contenu_cor=preg_replace('|<a href=|i', '<a target="'.$target.'" href=', $contenu_cor);
		$contenu_cor = preg_replace('|/documents/archives/etablissement/[[:alnum:] _-]*/documents/archives/etablissement/|', '/documents/archives/etablissement/', $contenu_cor);
	}
	return $contenu_cor;
}


// Le prof de $id_aid a-t-il autorisé le PP à corriger ses appréciations
// On teste aussi getSettingAOui('PeutAutoriserPPaCorrigerSesApp')
function acces_correction_app_aid_pp($id_aid) {
	$retour = false;

	/*
	// Question non tranchée: Faut-il créer une table aid_param?
	$sql="SELECT 1=1 FROM aid_param WHERE id_groupe='$id_groupe' AND name='AutoriserCorrectionAppreciationParPP' AND value='y';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)==0) {
		return false;
	}
	else {
		return true;
	}
	*/
	return $retour;
}

function get_sql_classes_tel_module($module, $statut, $login) {
	global $mysqli;

	$sql = "SHOW TABLES LIKE 'modules_restrictions';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		if ($module == 'bulletins') {
			if ($statut == "scolarite") {
				// On sélectionne les classes associées au compte scolarité
				$sql = "SELECT DISTINCT c.*, c.id AS id_classe FROM classes c, j_scol_classes jsc, j_eleves_classes jec WHERE (jec.id_classe=c.id AND jsc.id_classe=c.id AND jsc.login='" . $login . "') AND 
				c.id NOT IN (SELECT value FROM modules_restrictions WHERE module='" . $module . "' AND name='id_classe') 
				ORDER BY c.classe;";
			} elseif (($statut == "administrateur") ||
				($statut == "secours") ||
				($statut == "autre")) {
				// On selectionne toutes les classes
				$sql = "SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) AND 
				c.id NOT IN (SELECT value FROM modules_restrictions WHERE module='" . $module . "' AND name='id_classe') 
				ORDER BY c.classe;";
			} elseif ($_SESSION["statut"] == "professeur") {
				/*
				$sql="SELECT DISTINCT c.*, c.id AS id_classe FROM classes c, j_eleves_professeurs jep, j_eleves_classes jec WHERE (jep.professeur='".$login."' AND jep.login = jec.login AND jec.id_classe = c.id) AND
				c.id NOT IN (SELECT value FROM modules_restrictions WHERE module='".$module."' AND name='id_classe')
				ORDER BY c.classe;";
				*/

				$sql = "SELECT DISTINCT c.*, c.id AS id_classe FROM classes c, 
									periodes p, 
									j_groupes_classes jgc, 
									j_groupes_professeurs jgp, 
									j_eleves_groupes jeg 
								WHERE jeg.id_groupe=jgc.id_groupe AND 
									p.id_classe = c.id AND 
									jgc.id_classe=c.id AND 
									jgp.id_groupe=jgc.id_groupe AND 
									jgp.login='" . $login . "' AND 
									c.id NOT IN (SELECT value FROM modules_restrictions 
													WHERE module='" . $module . "' AND name='id_classe') 
								ORDER BY c.classe";

			} elseif ($_SESSION["statut"] == "cpe") {
				$sql = "SELECT DISTINCT c.*, c.id AS id_classe FROM classes c, j_eleves_cpe jecpe, j_eleves_classes jec WHERE (jecpe.cpe_login='" . $login . "' AND jecpe.e_login = jec.login AND jec.id_classe = c.id) AND 
				c.id NOT IN (SELECT value FROM modules_restrictions WHERE module='" . $module . "' AND name='id_classe') 
				ORDER BY c.classe;";
			} else {
				// On retourne une requête sans enregistrement associé
				$sql = "SELECT c.*, c.id AS id_classe FROM classes c WHERE c.id='-1000' ORDER BY c.classe;";
			}
		} else {
			$sql = "SELECT DISTINCT c.*, c.id AS id_classe FROM classes c ORDER BY c.classe;";
		}
	} else {
		$sql = "SELECT DISTINCT c.*, c.id AS id_classe FROM classes c ORDER BY c.classe;";
	}

	return $sql;
}

function get_classes_exclues_tel_module($module) {
	global $mysqli;

	$tab = array();

	$sql = "SHOW TABLES LIKE 'modules_restrictions';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		if (($module == 'bulletins') || ($module == 'cahier_notes')) {
			$sql = "SELECT DISTINCT value FROM modules_restrictions WHERE module='" . $module . "' AND name='id_classe'";
			$res = mysqli_query($mysqli, $sql);
			if (mysqli_num_rows($res) > 0) {
				while ($lig = mysqli_fetch_object($res)) {
					$tab[] = $lig->value;
				}
			}
		}
	}

	return $tab;
}

function is_groupe_exclu_tel_module($id_groupe, $module) {
	global $mysqli;

	$retour = false;

	//if(($_SESSION['statut']!='professeur')||
	//(($_SESSION['statut']=='professeur')&&(!getSettingAOui('acces_cn_prof')))) {
	$sql = "SHOW TABLES LIKE 'modules_restrictions';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		$sql = "SELECT * FROM modules_restrictions mr, 
					j_groupes_classes jgc 
				WHERE mr.module='" . $module . "' AND 
					mr.name='id_classe' AND 
					mr.value=jgc.id_classe AND 
					jgc.id_groupe='" . $id_groupe . "';";
		//echo "$sql<br />";
		$test = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($test) > 0) {
			$retour = true;
		}
	}
	//}

	return $retour;
}

function is_classe_exclue_tel_module($id_classe, $module) {
	global $mysqli;

	$retour = false;

	$sql = "SHOW TABLES LIKE 'modules_restrictions';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		$sql = "SELECT * FROM modules_restrictions mr, 
				j_groupes_classes jgc 
			WHERE mr.module='" . $module . "' AND 
				mr.name='id_classe' AND 
				mr.value='" . $id_classe . "';";
		//echo "$sql<br />";
		$test = mysqli_query($mysqli, $sql);
		if (mysqli_num_rows($test) > 0) {
			$retour = true;
		}
	}

	return $retour;
}

function get_classes_eleve_avec_carnet_notes($login) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT DISTINCT id_classe FROM j_eleves_classes jec 
		WHERE jec.login='" . $login . "' AND 
			jec.id_classe NOT IN (SELECT value FROM modules_restrictions mr WHERE
			mr.module='cahier_notes' AND 
			mr.name='id_classe');";
	//echo "$sql<br />";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		while ($lig = mysqli_fetch_object($test)) {
			$tab[] = $lig->id_classe;
		}
	}

	return $tab;
}

function is_eleve_avec_carnet_notes($login) {
	global $mysqli;

	$tab = get_classes_eleve_avec_carnet_notes($login);
	if (count($tab) > 0) {
		return true;
	} else {
		return false;
	}
}

function is_responsable_avec_eleve_avec_carnet_notes($login_resp) {
	global $mysqli;

	$retour = false;

	$tab_tmp_ele = get_enfants_from_resp_login($_SESSION['login'], '', 'y');
	for ($loop = 0; $loop < count($tab_tmp_ele); $loop += 2) {
		$tab = get_classes_eleve_avec_carnet_notes($tab_tmp_ele[$loop]);
		if (count($tab) > 0) {
			$retour = true;
			break;
		}
	}

	return $retour;
}

function set_affichage_cn_perso($login) {
	global $mysqli;

	if (getSettingAOui('acces_cn_prof')) {
		$pref_acces_cn_prof_afficher_lien = getPref($login, 'acces_cn_prof_afficher_lien', '');
		if ($pref_acces_cn_prof_afficher_lien == '') {

			$acces_cn_prof_afficher_lien = getSettingAOui('acces_cn_prof_afficher_lien');
			if ($acces_cn_prof_afficher_lien) {
				savePref($login, 'acces_cn_prof_afficher_lien', 'y');
			} else {
				savePref($login, 'acces_cn_prof_afficher_lien', 'n');
			}
		}
	}
}

function is_prof_avec_groupes_sans_cn($login) {
	global $mysqli;

	$retour = false;

	$sql = "SELECT 1=1 FROM j_groupes_classes jgc, 
				j_groupes_professeurs jgp, 
				modules_restrictions mr 
			WHERE jgp.login='" . $_SESSION['login'] . "' AND 
				jgc.id_groupe=jgp.id_groupe AND 
				jgc.id_classe=mr.value AND 
				mr.module='cahier_notes';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		$retour = true;
	}

	return $retour;
}

function get_groups_sans_cn_prof($login_prof) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT distinct jgc.id_groupe FROM j_groupes_classes jgc, 
				j_groupes_professeurs jgp, 
				modules_restrictions mr 
			WHERE jgp.login='" . $_SESSION['login'] . "' AND 
				jgc.id_groupe=jgp.id_groupe AND 
				jgc.id_classe=mr.value AND 
				mr.module='cahier_notes';";
	$test = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($test) > 0) {
		while ($lig = mysqli_fetch_object($test)) {
			$tab[] = $lig->id_groupe;
		}
	}

	return $tab;
}

function get_tab_competences_numeriques_LSU() {
	global $mysqli;

	/*
	// Liste des compétences numériques pour LSUN en fin de cycle 3 (6è) au 20200211
	<!-- Liste des compétences numériques possibles, organisées en domaines, pour les 6èmes -->
	<competences-numeriques>
		<domaine-competences-numeriques libelle="Information et données">
			<competence-numerique code="CN_INF_MEN" libelle="Mener une recherche et une veille d'information"/>
			<competence-numerique code="CN_INF_GER" libelle="Gérer des données"/>
			<competence-numerique code="CN_INF_TRA" libelle="Traiter des données"/>
		</domaine-competences-numeriques>
		<domaine-competences-numeriques libelle="Communication et collaboration">
			<competence-numerique code="CN_COM_INT" libelle="Interagir"/>
			<competence-numerique code="CN_COM_PAR" libelle="Partager et publier"/>
			<competence-numerique code="CN_COM_COL" libelle="Collaborer"/>
			<competence-numerique code="CN_COM_SIN" libelle="S'insérer dans le monde numérique"/>
		</domaine-competences-numeriques>
		<domaine-competences-numeriques libelle="Création de contenus">
			<competence-numerique code="CN_CRE_TEX" libelle="Développer des documents textuels"/>
			<competence-numerique code="CN_CRE_MUL" libelle="Développer des documents multimédia"/>
			<competence-numerique code="CN_CRE_ADA" libelle="Adapter les documents à leur finalité"/>
			<competence-numerique code="CN_CRE_PRO" libelle="Programmer"/>
		</domaine-competences-numeriques>
		<domaine-competences-numeriques libelle="Protection et sécurité">
			<competence-numerique code="CN_PRO_SEC" libelle="Sécuriser l'environnement numérique"/>
			<competence-numerique code="CN_PRO_DON" libelle="Protéger les données personnelles et la vie privée"/>
			<competence-numerique code="CN_PRO_SAN" libelle="Protéger la santé, le bien-être et l'environnement"/>
		</domaine-competences-numeriques>
		<domaine-competences-numeriques libelle="Environnement numérique">
			<competence-numerique code="CN_ENV_RES" libelle="Résoudre des problèmes techniques"/>
			<competence-numerique code="CN_ENV_EVO" libelle="Évoluer dans un environnement numérique"/>
		</domaine-competences-numeriques>
	</competences-numeriques>
	*/

	$tab = array();
	$tab["domaine"] = array();
	$tab["code"] = array();

	$tab["domaine"][0]["libelle"] = 'Information et données';
	$tab["code"]['CN_INF_MEN']['libelle'] = "Mener une recherche et une veille d'information";
	$tab["code"]['CN_INF_MEN']['domaine'] = $tab["domaine"][0]["libelle"];
	$tab["code"]['CN_INF_GER']['libelle'] = 'Gérer des données';
	$tab["code"]['CN_INF_GER']['domaine'] = $tab["domaine"][0]["libelle"];
	$tab["code"]['CN_INF_TRA']['libelle'] = 'Traiter des données';
	$tab["code"]['CN_INF_TRA']['domaine'] = $tab["domaine"][0]["libelle"];

	$tab["domaine"][1]["libelle"] = 'Communication et collaboration';
	$tab["code"]['CN_COM_INT']['libelle'] = "Interagir";
	$tab["code"]['CN_COM_INT']['domaine'] = $tab["domaine"][1]["libelle"];
	$tab["code"]['CN_COM_PAR']['libelle'] = "Partager et publier";
	$tab["code"]['CN_COM_PAR']['domaine'] = $tab["domaine"][1]["libelle"];
	$tab["code"]['CN_COM_COL']['libelle'] = "Collaborer";
	$tab["code"]['CN_COM_COL']['domaine'] = $tab["domaine"][1]["libelle"];
	$tab["code"]['CN_COM_SIN']['libelle'] = "S'insérer dans le monde numérique";
	$tab["code"]['CN_COM_SIN']['domaine'] = $tab["domaine"][1]["libelle"];

	$tab["domaine"][2]["libelle"] = 'Création de contenus';
	$tab["code"]['CN_CRE_TEX']['libelle'] = "Développer des documents textuels";
	$tab["code"]['CN_CRE_TEX']['domaine'] = $tab["domaine"][2]["libelle"];
	$tab["code"]['CN_CRE_MUL']['libelle'] = "Développer des documents multimédia";
	$tab["code"]['CN_CRE_MUL']['domaine'] = $tab["domaine"][2]["libelle"];
	$tab["code"]['CN_CRE_ADA']['libelle'] = "Adapter les documents à leur finalité";
	$tab["code"]['CN_CRE_ADA']['domaine'] = $tab["domaine"][2]["libelle"];
	$tab["code"]['CN_CRE_PRO']['libelle'] = "Programmer";
	$tab["code"]['CN_CRE_PRO']['domaine'] = $tab["domaine"][2]["libelle"];

	$tab["domaine"][3]["libelle"] = 'Protection et sécurité';
	$tab["code"]['CN_PRO_SEC']['libelle'] = "Sécuriser l'environnement numérique";
	$tab["code"]['CN_PRO_SEC']['domaine'] = $tab["domaine"][3]["libelle"];
	$tab["code"]['CN_PRO_DON']['libelle'] = "Protéger les données personnelles et la vie privée";
	$tab["code"]['CN_PRO_DON']['domaine'] = $tab["domaine"][3]["libelle"];
	$tab["code"]['CN_PRO_SAN']['libelle'] = "Protéger la santé, le bien-être et l'environnement";
	$tab["code"]['CN_PRO_SAN']['domaine'] = $tab["domaine"][3]["libelle"];

	$tab["domaine"][4]["libelle"] = 'Environnement numérique';
	$tab["code"]['CN_ENV_RES']['libelle'] = "Résoudre des problèmes techniques";
	$tab["code"]['CN_ENV_RES']['domaine'] = $tab["domaine"][4]["libelle"];
	$tab["code"]['CN_ENV_EVO']['libelle'] = "Évoluer dans un environnement numérique";
	$tab["code"]['CN_ENV_EVO']['domaine'] = $tab["domaine"][4]["libelle"];

	return $tab;
}

function get_tab_id_jours_ouvres() {
	global $mysqli;
	global $prefix_base;

	$tab = array();

	// tableau semaine
	$tab_sem[0] = 'dimanche';
	$tab_sem[1] = 'lundi';
	$tab_sem[2] = 'mardi';
	$tab_sem[3] = 'mercredi';
	$tab_sem[4] = 'jeudi';
	$tab_sem[5] = 'vendredi';
	$tab_sem[6] = 'samedi';

	for ($i = 0; $i < count($tab_sem); $i++) {
		$sql = "SELECT 1=1 FROM " . $prefix_base . "horaires_etablissement
				WHERE jour_horaire_etablissement = '" . $tab_sem[$i] . "' AND
						date_horaire_etablissement = '0000-00-00' AND ouvert_horaire_etablissement='1';";
		//echo "$sql<br />";
		$res_j_o = mysqli_query($mysqli, $sql);
		if ($res_j_o->num_rows) {
			$tab[] = $i;
			$res_j_o->close();
		}
	}

	/*
	echo "<pre>";
	print_r($tab);
	echo "</pre>";
	*/

	// Tester if(!in_array(strftime('%w', $ts, get_tab_id_jours_ouvres())))
	// %w commence avec 0->dimanche, 1->lundi,...

	return $tab;
}


function retourne_sql_classes_pp($login_prof) {
	$sql = "SELECT DISTINCT jec.id_classe, 
				c.* 
			FROM j_eleves_professeurs jep, 
				j_eleves_classes jec, 
				classes c 
			WHERE jep.professeur='$login_prof' AND 
				jep.login=jec.login AND 
				jec.id_classe=c.id AND 
				jep.id_classe=jec.id_classe 
			ORDER BY c.classe;";
	return $sql;
}

function get_tab_clas_pp($login_prof) {
	global $mysqli;
	$tab = array();
	$tab['login'] = array();
	$tab['id_classe'] = array();

	$sql = retourne_sql_classes_pp($login_prof);
	$res = mysqli_query($mysqli, $sql);
	if ($res->num_rows > 0) {
		while ($lig = $res->fetch_object()) {
			$tab['id_classe'][] = $lig->id_classe;
			$tab['classe'][] = $lig->classe;
		}
		$res->close();
	}

	return $tab;
}

function check_edt_cours2() {
	global $mysqli;
	//INSERT INTO setting SET name='use_edt_cours2', value='y';
	$use_edt_cours2 = getSettingAOui('use_edt_cours2');
	if ($use_edt_cours2) {
		$sql = "CREATE TABLE IF NOT EXISTS `edt_cours2` (`id_cours` int(11) NOT NULL auto_increment, `id_groupe` varchar(10) NOT NULL, `id_aid` varchar(10) NOT NULL, `id_salle` varchar(3) NOT NULL, `jour_semaine` varchar(10) NOT NULL, `h_debut` varchar(10) NOT NULL, `duree_minutes` INT(11) NOT NULL default '60', `duree` varchar(10) NOT NULL default '2', `heuredeb_dec` varchar(3) NOT NULL default '0', `id_semaine` varchar(10) NOT NULL default '0', `id_calendrier` varchar(3) NOT NULL default '0', `modif_edt` varchar(3) NOT NULL default '0', `login_prof` varchar(50) NOT NULL, `id_cours1` int(11) NOT NULL DEFAULT '0', PRIMARY KEY  (`id_cours`)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
		$create_table = mysqli_query($mysqli, $sql);
		if ($create_table) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function get_niveau_from_classe($id_classe) {
	global $mysqli;

	$tab = array();

	$sql = "SELECT DISTINCT m.mef_code, m.libelle_long FROM mef m, 
					eleves e, 
					j_eleves_classes jec 
				WHERE m.mef_code=e.mef_code AND 
					e.login=jec.login AND 
					jec.id_classe='" . $id_classe . "';";
	$res = mysqli_query($mysqli, $sql);
	if (mysqli_num_rows($res) > 0) {
		while ($lig = mysqli_fetch_object($res)) {
			$tab[$lig->mef_code] = $lig->libelle_long;
		}
	}

	return $tab;
}

?>

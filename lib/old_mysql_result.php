<?php
# http://mysql.dotpointer.com/
# mysql to mysqli migration library
# by dotpointer
	function old_mysql_result ($result , $row , $field = 0) {
		if (!(strpos($field,".")===false)) {
			if(getSettingValue('debug_old_mysql_result')!="no") {
				$ajout_header="";
				$email_destinataires=getSettingValue('gepiAdminAdress');
				if(check_mail($email_destinataires)) {
					$sujet_mail="[Gepi]: Probleme old_mysql_result";
					$texte_mail="Bonjour,

Un problème a été détecté avec le champ '$field' dans ".$_SERVER['PHP_SELF']." avec la fonction old_mysql_result().
Merci d'en informer la liste de diffusion officielle Gepi pour aider à corriger ce problème.

Cordialement.
-- 
Equipe de developpement Gepi.";
					$envoi = envoi_mail($sujet_mail, $texte_mail, $email_destinataires, $ajout_header);
				}
			}
			die ("<br /><h1>Pb avec le champ '$field' old_mysql_result dans ".$_SERVER['PHP_SELF']."</h1>");
		}

		if (mysqli_data_seek($result, $row) === false) return false;
		if (is_int($field)) $line=mysqli_fetch_array($result); else $line=mysqli_fetch_assoc($result);
		if (!isset($line[$field])) return false;
		return $line[$field];
	}
?>

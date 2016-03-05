<?php

//************************
// Copyleft Marc Leygnac
//************************

// tableau des prestataires pris en compte
$tab_prestataires_SMS=array("pluriware.fr","tm4b.com","123-SMS.net");

function filtrage_numero($numero,$international=false) {
	// supprime les caractères indésirables et ajoute éventuellement l'indicatif 33
	$numero=ereg_replace("[^0-9]","",$numero);
	if ($international) $numero='33'.substr($numero, 1);
}

function envoi_requete_http($url,$script,$t_parametres,$methode="POST") {
	/*
	$methode : GET ou par défaut POST
	$url : truc.com
	$script : machin.php
	$t_parametres : array("param1" => "val1","param2" => "val2",...)
	retour : chaîne de caractères contenant la réponse du serveur sans l'en-tête
	*/

	/*$parametres='';
	foreach($t_parametres as $clef => $valeur)  {
		if ($parametres!='') $parametres.='&';
	    $parametres.=$clef.'='.urlencode($valeur);
		} */
	$parametres=http_build_query($t_parametres);

	if (in_array('curl',get_loaded_extensions())) {

	    // avec cURL
		$ch=curl_init();
		if ($methode=="GET") {
			if ($parametres!='') $script=$script."?".$parametres;  // Méthode GET
			curl_setopt($ch,CURLOPT_URL,$url.$script); // Méthode GET
			curl_setopt($ch,CURLOPT_HTTPGET,true); // Méthode GET
		} else {
			curl_setopt($ch,CURLOPT_URL,$url.$script);
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$parametres);
		}

		//curl_setopt($ch,CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$r_exec=curl_exec($ch); $error=curl_error($ch);
		if ($r_exec===false) return "Erreur : ".$error; else return $r_exec;
		curl_close($ch);

	} else {
	
		// sans cURL
		if ($methode=="GET") {
			$entete="GET ".$script."?".$parametres." HTTP/1.1\r\n";  // Méthode GET
		} else {
			$entete="POST ".$script." HTTP/1.1\r\n";
		}
		$entete.="Host: ".$url."\r\n";
		$entete.="Content-Type: application/x-www-form-urlencoded\r\n";
		if ($methode=="POST") $entete.="Content-Length: ".strlen($parametres)."\r\n";
		$entete.="Connection: Close\r\n\r\n";
		$socket=@fsockopen($url,80,$errno,$errstr);
		if($socket) {
			fputs($socket,$entete); // envoi de l'en-tête
			if ($methode=="POST") fputs($socket,$parametres);
			// on saute l'en-tête de la réponse HTTP
			$line="";
			// on saute les lignes vides
			while (!feof($socket) && $line=="") {
				$line=trim(fgets($socket));
			}
			// on saute l'en-tête
			while (!feof($socket) && $line!="") {
				$line=trim(fgets($socket));
			}
			// on saute les lignes vides
			while (!feof($socket) && $line=="") {
				$line=trim(fgets($socket));
			}
			// ici $line contient la première ligne après l'en-tête de la réponse HTTP
			$retour=$line;
			while (!feof($socket)) {
				$retour.=trim(fgets($socket));
			}
			return $retour;
			fclose($socket);
			}
			else  return 'Erreur : no socket available.';
	}
}

function envoi_SMS($tab_to,$sms) {
	// $tab_to : tableau des numéros de téléphone auxquels envoyer le SMS
	// attention : certains prestataires n'autorisent qu'un seul sms par requête
	// $sms : le texte du SMS
	// retourne "OK" si envoi réussi, un message d'erreur sinon
	
	/* Pour déboguer décommenter le code suivant
	echo "Envoi de ".$sms." à ".$tab_to[0].".<br />";
	return "OK"; // ou return "Erreur d'envoi SMS";
	*/
	
	$sms_prestataire=getSettingValue("sms_prestataire");
	switch ($sms_prestataire) {
		case "pluriware.fr" :
			$url="sms.pluriware.fr";
			$script="/httpapi.php";
			$parametres['cmd']='sendsms';            
			$parametres['txt']=$sms; // message a envoyer
			$parametres['user']=getSettingValue("sms_username"); // identifiant Pluriware
			$parametres['pass']=getSettingValue("sms_password"); // mot de passe Pluriware
		
			foreach($tab_to as $key => $to) $tab_to[$key]=filtrage_numero($to,true);
			$to=$tab_to[0]; // ! un seul numéro
			$parametres['to']=$to; // numéro de téléphone auxquel on envoie le message (! un seul numéro)
			$parametres['from']=getSettingValue("sms_identite"); // expéditeur du message (facultatif)
			
			/*
				Les  parametres suivants sont pour le moment facultatifs (janv/2011) 
			mais peuvent êtres utiles pour une évolution future ou en cas de debug
			*/
			$parametres['gepi_school'] = getSettingValue("sms_identite");
			$parametres['gepi_version'] = getSettingValue("version"); // pour debug au cas ou
			$parametres['gepi_mail'] = getSettingValue("gepiSchoolEmail"); // remontée éventuelle des réponses par mail
			$parametres['gepi_rne'] = getSettingValue("gepiSchoolRne"); // identification supplémentaire
			$parametres['gepi_pays'] = getSettingValue("gepiSchoolPays"); // peux servir pour corriger ou insérer l'indicatif international du num tel

			$reponse=envoi_requete_http($url,$script,$parametres);
			if (substr($reponse,0,3)=='ERR' || substr($reponse, 0, 6)=='Erreur') {
				return 'SMS non enoyé(s) : '.$reponse;
				} 
			else return "OK";

			break;

		case "123-SMS.net" :
			$url="www.123-SMS.net";
			$script="/http.php";
			$hote="123-SMS.net";
			$script="/http.php";
			$parametres['email']=getSettingValue("sms_username"); // identifiant 123-SMS.net
			$parametres['pass']=getSettingValue("sms_password"); // mot de passe 123-SMS.net
			$parametres['message']=$sms; // message que l'on désire envoyer
			
			foreach($tab_to as $key => $to) $tab_to[$key]=filtrage_numero($to);
			$to=implode("-",$tab_to);
			$parametres['numero']=$to; // numéros de téléphones auxquels on envoie le message séparés par des tirets
			$t_erreurs=array(80 => "Le message a été envoyé", 81 => "Le message est enregistré pour un envoi en différé", 82 => "Le login et/ou mot de passe n’est pas valide",  83 => "vous devez créditer le compte", 84 => "le numéro de gsm n’est pas valide", 85 => "le format d’envoi en différé n’est pas valide", 86 => "le groupe de contacts est vide", 87 => "la valeur email est vide", 88 => "la valeur pass est vide",  89 => "la valeur numero est vide", 90 => "la valeur message est vide", 91 => "le message a déjà été envoyé à ce numéro dans les 24 dernières heures");
			$reponse=envoi_requete_http($url,$script,$parametres);
			if ($reponse!='80') {
				return 'SMS non enoyé(s) : '.$reponse.' '.$t_erreurs[$reponse];
				} 
			else return "OK";
			
			break;

		case "tm4b.com" :
			$url="www.tm4b.com";
			$script="/client/api/http.php";
			$hote="tm4b.com";
			$script="/client/api/http.php";
			$parametres['username']=getSettingValue("sms_username"); // identifiant  TM4B
			$parametres['password']=getSettingValue("sms_password"); // mot de passe  TM4B
			$parametres['type']='broadcast'; // envoi de sms
			$parametres['msg']=$sms; // message a envoyer
			
			foreach($tab_to as $key => $to) $tab_to[$key]=filtrage_numero($to,true);
			$to=implode("%7C",$tab_to);
			$parametres['to']=$to; // numéros de téléphones auxquels on envoie le message séparés par des 'pipe' %7C

			$parametres['from']=getSettingValue("sms_identite"); // expéditeur du message (first class uniquement)
			$parametres['route']='business'; // type de route (pour la france, business class uniquement)
			$parametres['version']='2.1';
			// $parametres['sim']='yes'; // on active le mode simulation, pour tester notre script
			
			$reponse=envoi_requete_http($url,$script,$parametres);
			if (mb_substr($reponse, 0, 5)=='error' || substr($reponse, 0, 6)=='Erreur') {
				return 'SMS non enoyé(s) : '.$reponse;
				} 
			else return "OK";

			break;

		default :
			return "SMS non enoyé(s) : prestataire SMS non défini.";
		}
	
	return $reponse;
}

/* Tests prestataires

echo "tm4b<br>";
$url="www.tm4b.com";
$script="/client/api/http.php";
$parametres=array("username"=>"toto");
echo envoi_requete_http($url,$script,$parametres);

echo "<hr>Pluriware<br>";
$url="sms.pluriware.fr";
$script="/httpapi.php";
$parametres=array("cmd"=>"sendsms");
echo envoi_requete_http($url,$script,$parametres);

echo "<hr>123-SMS<br>";			
$url="www.123-SMS.net";
$script="/http.php";
$parametres=array("email"=>"toto","message"=>"test");	
$r=envoi_requete_http($url,$script,$parametres);
$t_erreurs=array(80 => "Le message a été envoyé", 81 => "Le message est enregistré pour un envoi en différé", 82 => "Le login et/ou mot de passe n’est pas valide",  83 => "vous devez créditer le compte", 84 => "le numéro de gsm n’est pas valide", 85 => "le format d’envoi en différé n’est pas valide", 86 => "le groupe de contacts est vide", 87 => "la valeur email est vide", 88 => "la valeur pass est vide",  89 => "la valeur numero est vide", 90 => "la valeur message est vide", 91 => "le message a déjà été envoyé à ce numéro dans les 24 dernières heures");
echo $r." : ".$t_erreurs[$r];


echo "<hr>Pluriware-SMS XML<br>";			
$url="sms.pluriware.fr";
$script="/xmlapi.php";
$parametres=array("data"=>'<pluriAPI><login></login><password></password><sendMsg><to>330628000000</to><txt>Test msg 1</txt><climsgid></climsgid><status></status></sendMsg></pluriAPI>');	
echo envoi_requete_http($url,$script,$parametres,"GET");
			
$url="www.ac-poitiers.fr";
$script="/";
$parametres=array();
echo envoi_requete_http($url,$script,$parametres,"GET");
*/

?>

<?php

//************************
// Copyleft Marc Leygnac
//************************

// tableau des prestataires pris en compte
$tab_prestataires_SMS=array('PLURIWARE','TM4B','123-SMS','AllMySMS');

function json_echapp($ch) {
	// Echappe dans la chaîne $ch les caractères de contrôle
	// selon http://www.json.org/
	$cars_spec=array('/"/','/\\\/','/\//',"/\n/","/\r/","/\t/");
	$cars_echapp=array('\"','\\\\\\','\/','\\n','\\r','\\t');
	return preg_replace($cars_spec, $cars_echapp, $ch);
}

function filtrage_numero($numero,$prefixe_france=false) {
	// supprime les caractères indésirables et ajoute éventuellement le préfixe +33
	$numero=preg_replace('#[^0-9,+]#','',$numero);
	if ($prefixe_france && (substr($numero,0,1)=='0')) $numero='+33'.substr($numero, 1);
	return $numero;
}

function liste_parametres($parametres) {
	$liste='';
	foreach($parametres as $cle => $valeur) {
		$liste.=$cle.' : '."\n";
		$liste.=$valeur."\n";
	}
	return $liste;
}

function envoi_requete_http($url,$script,$t_parametres,$methode='POST',$port=80) {
	/*
	$methode : GET ou par défaut POST
	$url : truc.com
	$script : machin.php
	$t_url_encode_parametres : array("param1" => "val1","param2" => "val2",...)
	retour : chaîne de caractères contenant la réponse du serveur sans l'en-tête
	*/

	/*$url_encode_parametres='';
	foreach($t_parametres as $clef => $valeur)  {
		if ($url_encode_parametres!='') $url_encode_parametres.='&';
	    $url_encode_parametres.=$clef.'='.urlencode($valeur);
		} */
	$url_encode_parametres=http_build_query($t_parametres);

	if (in_array('curl',get_loaded_extensions())) {
	    // avec cURL
		$ch=curl_init();
		if ($methode=='GET') {
			if ($url_encode_parametres!='') $script=$script."?".$url_encode_parametres; 
			curl_setopt($ch,CURLOPT_URL,$url.$script);
			curl_setopt($ch,CURLOPT_HTTPGET,true);
		} else {
			curl_setopt($ch,CURLOPT_URL,$url.$script);
			curl_setopt($ch,CURLOPT_POST,true);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$t_parametres);
		}

		//curl_setopt($ch,CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$r_exec=curl_exec($ch); $error=curl_error($ch);
		if ($r_exec===false) return "Erreur : ".$error; else return $r_exec;
		curl_close($ch);

	} else {
			// sans cURL
			if ($methode=='GET') {
				$envoi='GET '.$script.'?'.$url_encode_parametres.' HTTP/1.1'."\n";
				$envoi.='Host: '.$url."\n";
				$envoi.='Connection: Close'."\n";
				$envoi.="\n";
			} else {
				$boundary='-----------------------------9051914041544843365972754266';
				$boundary='---------------------'.time();
				$data='';
				foreach($t_parametres as $key => $val){
					$data.='--'.$boundary."\n";
					$data.='Content-Disposition: form-data; name="'.$key.'"'."\n\n";
					$data.=$val."\n";
				}
				$data.='--'.$boundary.'--'."\n";

				$envoi='POST '.$script.' HTTP/1.1'."\n";
				$envoi.='Host: '.$url."\n";
				$envoi.='Connection: Close'."\n";
				$envoi.='Content-Type: multipart/form-data; charset=UTF-8; boundary='.$boundary."\n";
				$envoi.='Content-Length: '.strlen($data)."\n";
				$envoi.="\n";
				$envoi.=$data;
			}
			if (!$socket=@fsockopen($url,$port,$errno,$errstr,120)) return 'Erreur fsckopen : '.$errstr;
			fputs($socket,$envoi);

			// En-tête
			$en_tete='';
			$line='';
			while (!feof($socket) && $line=='') {
				$line=trim(fgets($socket));
			}
			while (!feof($socket) && $line!='') {
				$en_tete.=$line."\n";
				$line=trim(fgets($socket));
			}

			//Retour
			$retour=$line;
			$line='';
			while (!feof($socket) && $line=='') {
				$line=trim(fgets($socket));
			}
			while (!feof($socket)) {
				$retour.=$line."\n";
				$line=trim(fgets($socket));
			}
			$retour.=$line."\n";
			return $retour;
		}
		
}

function envoi_SMS($tab_to,$sms,$log=false) {
	// $tab_to : tableau des numéros de téléphone auxquels envoyer le SMS
	// attention : certains prestataires n'autorisent qu'un seul sms par requête
	// $sms : le texte du SMS
	// retour
	// si $log==false : "OK" si envoi réussi, un message d'erreur sinon
	// si $log==true : retourne un tableau $t_log avec
	//					$t_log['retour'] = "OK" si envoi réussi, un message d'erreur sinon
	//					$t_log['envoi'] = ce qui est envoyé au prestataire sous forme de chaîne de caractères (si besoin appel à function liste_parametres)
	//					$t_log['reponse'] = réponse du prestataire sous forme de chaîne de caractères (si besoin appel à function liste_parametres)

	// on ajoute l'identité de l'émetteur
	if(getSettingValue('sms_identite')!=='') $sms=getSettingValue('sms_identite').' '.$sms;
	
	$sms_prestataire=getSettingValue("sms_prestataire");
	switch ($sms_prestataire) {
		case 'PLURIWARE' :
			$url='sms.pluriware.fr';
			$script='/xmlapi.php';

			$parametres['data']='<?xml version="1.0" encoding="UTF-8" ?>'."\n";
			$parametres['data'].='<pluriAPI>'."\n";
			$parametres['data'].='	<login>'.getSettingValue('sms_username').'</login>'."\n";
			$parametres['data'].='	<password>'.getSettingValue('sms_password').'</password>'."\n";
			foreach($tab_to as $to) {
				$parametres['data'].='		<sendMsg>'."\n";;
				$parametres['data'].='			<to>'.filtrage_numero($to,true).'</to>'."\n";
				$parametres['data'].='			<txt><![CDATA['.$sms.']]></txt>'."\n";
				//$parametres['data'].='			<from>".substr(getSettingValue("sms_identite"),0,11)."</from>'."\n";
				$parametres['data'].='		</sendMsg>'."\n";
			}
			$parametres['data'].='</pluriAPI>'."\n";

			$t_log['envoi']=liste_parametres($parametres);
			$reponse=envoi_requete_http($url,$script,$parametres);
			if ($reponse=='Erreur fsckopen'){ 
				$retour='SMS non envoyé(s) : '.$reponse;
				$t_log['envoi']='';
				$t_log['retour']=$retour;
				$t_log['reponse']='';
			} 
			else {
				$xml = new DOMDocument();
				$xml->loadXML($reponse);
				$err=$xml->getElementsByTagName('err');
				if ($err->length!=0) {
					$erreur='';
					$descs=$xml->getElementsByTagName('desc');
					foreach($descs as $desc) $erreur.=$desc->nodeValue;
					$retour='SMS non envoyé(s) : '.$erreur;
				} else $retour='OK';
				$t_log['retour']=$retour;
				$t_log['reponse']=$xml->saveXML();
			};

			break;

		case '123-SMS' :
			$url='www.123-SMS.net';
			$script='/http.php';
			$parametres['email']=getSettingValue('sms_username'); // identifiant 123-SMS.net
			$parametres['pass']=getSettingValue('sms_password'); // mot de passe 123-SMS.net
			$parametres['message']=urlencode($sms); // message que l'on désire envoyer
			//$parametres['from']=urlencode(getSettingValue('sms_identite')); // expéditeur
			
			foreach($tab_to as $key => $to) $tab_to[$key]=filtrage_numero($to);
			$to=implode('-',$tab_to);
			$parametres['numero']=$to; // numéros de téléphones auxquels on envoie le message séparés par des tirets
			$t_erreurs=array(80 => 'Le message a été envoyé', 81 => 'Le message est enregistré pour un envoi en différé', 82 => 'Le login et/ou mot de passe n’est pas valide',  83 => 'Vous devez créditer le compte', 84 => 'Le numéro de gsm n’est pas valide', 85 => 'Le format d’envoi en différé n’est pas valide', 86 => 'Le groupe de contacts est vide', 87 => 'La valeur email est vide', 88 => 'La valeur pass est vide',  89 => 'La valeur numero est vide', 90 => 'La valeur message est vide', 91 => 'Le message a déjà été envoyé à ce numéro dans les 24 dernières heures');

			$t_log['envoi']=liste_parametres($parametres);
			$reponse=envoi_requete_http($url,$script,$parametres,'GET');
			if ($reponse=='Erreur fsckopen'){ 
				$retour='SMS non envoyé(s) : '.$reponse;
				$t_log['envoi']='';
				$t_log['retour']=$retour;
				$t_log['reponse']='';
			}
			else {
				if ($reponse!='80') $retour='SMS non envoyé(s) : '.$reponse.' '.$t_erreurs[$reponse];
					else $retour='OK';
				$t_log['retour']=$retour;
				$t_log['reponse']=$reponse.' '.$t_erreurs[$reponse];
			};

			break;

		case "TM4B" :
			$url='www.tm4b.com';
			$script='/client/api/http.php';
			$parametres['username']=getSettingValue('sms_username'); // identifiant  TM4B
			$parametres['password']=getSettingValue('sms_password'); // mot de passe  TM4B
			$parametres['type']='broadcast'; // envoi de sms
			$parametres['msg']=urlencode($sms); // message a envoyer
			
			foreach($tab_to as $key => $to) $tab_to[$key]=filtrage_numero($to,true);
			$to=implode('%7C',$tab_to);
			$parametres['to']=$to; // numéros de téléphones auxquels on envoie le message séparés par des 'pipe' %7C

			//$parametres['from']=getSettingValue('sms_identite'); // expéditeur du message (first class uniquement)
			$parametres['route']='business'; // type de route (pour la france, business class uniquement)
			$parametres['version']='2.1';
			// $parametres['sim']='yes'; // on active le mode simulation, pour tester notre script

			$t_log['envoi']=liste_parametres($parametres);			
			$reponse=envoi_requete_http($url,$script,$parametres,'GET');
			if ($reponse=='Erreur fsckopen'){ 
				$retour='SMS non envoyé(s) : '.$reponse;
				$t_log['envoi']='';
				$t_log['retour']=$retour;
				$t_log['reponse']='';
			}
			else {
				if (substr($reponse, 0, 5)=='error' || substr($reponse, 0, 6)=='Erreur') $retour='SMS non envoyé(s) : '.$reponse;
					else $retour='OK';
				$t_log['retour']=$retour;
				$t_log['reponse']=$reponse;
			};

			break;

		 case 'AllMySMS' :
			//URL Simul : https://api.allmysms.com/http/9.0/simulateCampaign/
			//URL envoi : https://api.allmysms.com/http/9.0/sendSms/

			$url='api.allmysms.com';
			$script='/http/9.0/sendSms/';
			$parametres['login']=getSettingValue('sms_username');    //votre identifant allmysms
			$parametres['apiKey']=getSettingValue('sms_password');    //votre clé d'API allmysms
			
			//$sender=substr(getSettingValue('sms_identite'),0,11);  //l'expediteur, attention pas plus de 11 caractères alphanumériques
																	//Doit commencer par une lettre
																	//Ne peut contenir que des caractères alphanumériques (a-z0-9) et majuscules, ou un espace
																	//Pas de caractères accentués ou de caractères spéciaux

			$message=substr($sms,0,160);    //le message SMS, attention pas plus de 160 caractères

			$parametres['smsData']='{'."\n";
			$parametres['smsData'].='"DATA": {'."\n";
			$parametres['smsData'].='	"MESSAGE": "'.json_echapp($message).'",'."\n";
			//$parametres['smsData'].='	"TPOA": "'.json_echapp($sender).'",'."\n";
			$parametres['smsData'].='	"DYNAMIC": "0",'."\n";
			$parametres['smsData'].='	"SMS": 	['."\n";
			$mobiles="";
			foreach($tab_to as $to) {
				if ($mobiles!="") $mobiles.='				},'."\n";
				$mobiles.='				{'."\n";
				$mobiles.='				"MOBILEPHONE": "'.filtrage_numero($to).'"'."\n";
			};
			
			$mobiles=rtrim($mobiles,',');
			$parametres['smsData'].=$mobiles;
			$parametres['smsData'].='				}'."\n";
			$parametres['smsData'].='			]'."\n";
			$parametres['smsData'].='	}'."\n";
			$parametres['smsData'].='}'."\n";
			
			$t_log['envoi']=liste_parametres($parametres);			
			$reponse=envoi_requete_http($url,$script,$parametres);
			if ($reponse=='Erreur fsckopen'){ 
				$retour='SMS non envoyé(s) : '.$reponse;
				$t_log['envoi']='';
				$t_log['retour']=$retour;
				$t_log['reponse']=''; 
			} 
			else {
				$t_reponse=json_decode($reponse,true);
				if ($t_reponse['status']==100) $retour='OK';
					else $retour='SMS non envoyé(s) : '.$t_reponse['statusText'];
				$t_log['retour']=$retour;
				$t_log['reponse']=liste_parametres($t_reponse);
			};
		
			break;

		default :
			$retour="SMS non envoyé(s) : prestataire SMS non défini.";
			$t_log['envoi']="sms envoyé : \n".$sms;
			$t_log['retour']=$retour;
			$t_log['reponse']='';
		}

	if ($log==true) return $t_log; else return $retour;
}

?>

<?php

// fonction permettant le listage d'un dossier et de mettre les informations dans un tableau
// ex: listage_dossier("./gepi144/documents","./gepi144/documents/") -> tab[1]='dossier/test.txt
// appel de la fonction: listage_dossier(emplacement, emplacement);
// le deuxième emplacement et le même que le premier mais il sert de dossier racine pour la suite
function listage_dossier($dossier, $dossier_racine)
 {
	//on initialiste le compteur de fichier
	if(empty($GLOBALS['cpt_fichier'])) { $cpt_fichier='1'; }
	else { $cpt_fichier=$GLOBALS['cpt_fichier']; }

	//on initialiste le tableau de fichier
	if(empty($GLOBALS['tab_fichier'][0])) { $tab_fichier=''; }
	else { $tab_fichier=$GLOBALS['tab_fichier'][0]; }
	
	//on vérifie si c'est un dossier
	if(is_dir($dossier))
	{
		//on ouvre le dossier
		if($dh=opendir($dossier))
		{
			// on liste les fichiers et dossier
			while(($nom=readdir($dh))!=false)
			{
				// s'il sont différent de . et .. on continue le listage
			        if($nom!='.' and $nom!='..') {
				   $emplacement="$dossier/$nom";
				   // fonction récursive si c'est un dossier on appel à nouveau la fonction dans laquelle on se trouve actuellement
				   if(is_dir($emplacement)&&($nom!=".")&&($nom!=".."))
			 	   {
					listage_dossier("$dossier/$nom", $dossier_racine);
				   } else { 
						// si ce n'est pas un dossier alors un met l'emplacement du fichier et le fichier dans le tableau
						$tab_fichier_select=$dossier.'/'.$nom;
						//on enlève la partie non utils
						$GLOBALS['tab_fichier'][$cpt_fichier] = my_eregi_replace($dossier_racine,'',$tab_fichier_select);
						$cpt_fichier++;
						$GLOBALS['cpt_fichier']=$cpt_fichier;
					  }
 				}
			}
		}
	}
 return ($GLOBALS['tab_fichier']);
 }

// fonction permettant l'envoie d'un tableau contenant l'emplacement des fichier vers un FTP
// ex: envoi_ftp($mon_tab)
// appel de la fonction: envoi_ftp(la variable du tableau);

function envoi_ftp($tableau, $source, $destination)
 {
	// on compte le nombre d'enregistrement du tableau
	$nb_valeur=count($tableau);

	// on essaye de ce connecter en ftp sécurisé
	$conn_id = ftp_ssl_connect($ftp_server);
	if($conn_id===FALSE)
         {
	       // si la connection sécurisé n'est pas possible alors on se connect en non sécurisé
	       $conn_id = ftp_connect("$ftp_server");
	       if($conn_id===FALSE) { $message_ftp['connection']='Impossible de se connecter au serveur FTP: '.$ftp_server; exit(); } else { $message_ftp['connection']='Connecté au serveur FTP en transfert non sécurisé'; }
	 } else { $message_ftp['connection']='Connecté au serveur FTP en transfert sécurisé'; }

	// Identification avec un nom d'utilisateur et un mot de passe
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if($login_result===FALSE)
	 {
		// si le nom d'utilisateur ou le mot de passe n'est pas correct
		$message_ftp['authentification']='Le nom d\'utilisateur ou le mot de passe sont erroné pour l\'utilisateur: '.$ftp_user_name;
		exit();
	 }

	// choix du mode passive si cela est possible
	$ftp_mode = ftp_pasv($conn_id, true);
	if($ftp_mode===FALSE) 
         {
		// si le mode pasive n'est pas possible alors on ne l'active pas
		$ftp_mode = ftp_pasv($conn_id, false);
		if($ftp_mode===FALSE) 
	         {
			//si aucun mode ne passe
			$message_ftp['mode']='Une erreur est intervenue dans le choix du mode';
			exit();
		 } else { $message_ftp['mode']='mode non passive'; }
	 } else { $message_ftp['mode']='mode passive'; }

	// début de la gestion du téléchargement des fichiers du tableau
	$i_tab_ftp='1'; $dossier_du_fichier_precedent=''; $nb_ancien_enfant='0';
	while(!empty($tableau[$i_tab_ftp]))
	 {
	     $dossier_du_fichier = '';
	     //nom du fichier
	     $nom_du_fichier     = basename($tableau[$i_tab_ftp]);
	     //emplacement complet du fichier - on enléve le nom du fichier au chemin complet
	     $dossier_du_fichier = my_eregi_replace($nom_du_fichier,'',$tableau[$i_tab_ftp]);
	     //on donne met dans une variable le chemin sans le nom du fichier pour une comparaisont par la suite
	     $dossier_du_fichier_verif = $dossier_du_fichier;
	     //on explose le chemin pour le mettre dans un tableau chaque nom de dossier
	     $dossier_du_fichier = explode('/', $dossier_du_fichier);
	     
   	     //on vérifie si nous somme dans le même dossier qu'au passage précédent
	     if($dossier_du_fichier_precedent!=$dossier_du_fichier_verif) {
  	        $i_c=0;
	      	//si nous étions dans un dossier nous revenons à la racine
  	     	while($i_c<$nb_ancien_enfant)
	     	{
			ftp_cdup($conn_id);
			$i_c++;
	     	}

 	     	$i_d=0;
	 	//nous dessandons dans l'arboresence pour copier un fichier
   	     	while(!empty($dossier_du_fichier[$i_d])) {
			//on vas dans le dossier principal
			@ftp_chdir($conn_id, $dossier_destination);
			if (@ftp_chdir($conn_id, $dossier_du_fichier[$i_d])) {
//		    	echo "Le dossier courant est maintenant : " . ftp_pwd($conn_id) . "\n<br />";
			} else {
	 		  	//si le dossier n'existe pas on le créer
				  if (@ftp_mkdir($conn_id, $dossier_du_fichier[$i_d])) {
					//on entre dans le dossier
					@ftp_chdir($conn_id, $dossier_du_fichier[$i_d]);
				   } else {
						$message_ftp['dossier'] = 'impossible à créer le dossier';
					  }
				}
			$i_d++;
		}
	$nb_ancien_enfant=$i_d;
      }

	//on charge le fichier
	$upload = ftp_put($conn_id, $nom_du_fichier, $tableau[$i_tab_ftp], FTP_BINARY);
	// Vérification de téléchargement
	if (!$upload) {
	    } else {
	    }
	//on entre dans une variable le dossier ou nous somme actuellement (chemin complet)
	$dossier_du_fichier_precedent = $dossier_du_fichier_verif;
	$i++;
 }

// Fermeture de la connexion FTP.
ftp_close($conn_id);
}
?>

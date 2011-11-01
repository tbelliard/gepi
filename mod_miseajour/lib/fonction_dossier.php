<?php

// fonction permettant de vérifier une url
// ex: url_exists("http://www.google.fr/test.txt") -> TRUE / FALSE
// appel de la fonction: url_exists(url);
function url_exists($url)
{
 $handle = @fopen($url, "r");
 if ($handle === false)
  return false;
 fclose($handle);
 return true;
}

// fonction permettant de retranscrire une date SQL en format FR
// ex: date_fr($var)
// appel de la fonction: date_fr(date sql);
function date_fr($var)
        {
        $var = explode("-",$var);
        $var = $var[2]."/".$var[1]."/".$var[0];
        return($var);
        }

// fonction permettant de retranscrire une date FR en format SQL
// ex: date_sql($var)
// appel de la fonction: date_sql(date sql);
function date_sql($var)
        {
        $var = explode("/",$var);
        $var = $var[2]."-".$var[1]."-".$var[0];
        return($var);
        }

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
						$GLOBALS['tab_fichier']['source_fichier'][$cpt_fichier] = $tab_fichier_select;
						//nom du fichier
						$GLOBALS['tab_fichier']['nom_fichier'][$cpt_fichier] = trim($nom);
						// source
						$source_emplacement= my_eregi_replace($dossier_racine,'',$tab_fichier_select);
						$source_emplacement= my_eregi_replace($nom,'',$source_emplacement);
						$GLOBALS['tab_fichier']['emplacement_fichier'][$cpt_fichier] = trim($source_emplacement);
						// destiné à
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
// ex: envoi_ftp($mon_tab, dossier cible principal sur le serveur)
// appel de la fonction: envoi_ftp(la variable du tableau);
function envoi_ftp($tableau, $destination)
 {

	// les informations du tableau
		//$tableau['source_fichier']['1'] -> ../documents/msj/temp/texte1.php
		//$tableau['nom_fichier']['1'] -> texte1.php
		//$tableau['emplacement_fichier']['1'] -> /documents/
		//$tableau['date_fichier']['1'] -> 21/11/2006
		//$tableau['heure_fichier']['1'] -> 12:00
		//$tableau['md5_fichier']['1'] -> md5
		//$tableau['status_fichier']['1'] -> état du fichier (pass)

	// on inclue le fichier d'information FTP
	include('info_connect.inc.php');

	$connexion_id=''; $login_result=''; $ftp_mode='';

	// on compte le nombre d'enregistrement du tableau
	$nb_valeur=count($tableau);

	// on essaye de ce connecter en ftp sécurisé
	$connexion_id = ftp_ssl_connect("$ftp_server", "$port_ssl", "$temps_max_response");
	if($connexion_id===FALSE)
         {
	       // si la connection sécurisé n'est pas possible alors on se connect en non sécurisé
	       $connexion_id = ftp_connect($ftp_server, $port_nonssl, $temps_max_response);
	       if($connexion_id===FALSE) { $message_ftp['connection']='Impossible de se connecter au serveur FTP: '.$ftp_server; exit(); } else { $message_ftp['connection']='Connecté au serveur FTP en transfert non sécurisé'; }
	 } else { $message_ftp['connection']='Connecté au serveur FTP en transfert sécurisé'; }

	// Identification avec un nom d'utilisateur et un mot de passe
	$login_result = ftp_login($connexion_id, $ftp_user_name, $ftp_user_pass);
	if($login_result===FALSE)
	 {
		// si le nom d'utilisateur ou le mot de passe n'est pas correct
		$message_ftp['authentification']='Le nom d\'utilisateur ou le mot de passe sont erroné pour l\'utilisateur: '.$ftp_user_name;
		exit();
	 } else { $message_ftp['authentification']='Nom d\'utilistaeur et mot de passe correct'; }

	// choix du mode passive si cela est possible
	$ftp_mode = ftp_pasv($connexion_id, true);
	if($ftp_mode===FALSE) 
         {
		// si le mode pasive n'est pas possible alors on ne l'active pas
		$ftp_mode = ftp_pasv($connexion_id, false);
		if($ftp_mode===FALSE) 
	         {
			//si aucun mode ne passe
			$message_ftp['mode']='Une erreur est intervenue dans le choix du mode';
			exit();
		 } else { $message_ftp['mode']='mode non passive'; }
	 } else { $message_ftp['mode']='mode passive'; }

	// on vas se mettre dans le dossier cible
	$dossier_du_fichier_precedent=@ftp_chdir($connexion_id, $destination);
	$dossier_du_fichier_precedent=ftp_pwd($connexion_id);

	// début de la gestion du téléchargement des fichiers du tableau
	$i_tab_ftp='1'; $nb_ancien_enfant='0';
	while(!empty($tableau['source_fichier'][$i_tab_ftp]))
	 {
	     $dossier_du_fichier=''; $source='';
	     // source du fichier
	     $source = $tableau['source_fichier'][$i_tab_ftp];
	     //nom du fichier
	     $nom_du_fichier     = $tableau['nom_fichier'][$i_tab_ftp];
	     //emplacement complet du fichier - on enléve le nom du fichier au chemin complet
	     $dossier_du_fichier = $tableau['emplacement_fichier'][$i_tab_ftp];
	     //on met dans une variable le chemin sans le nom du fichier pour une comparaisont par la suite
	     $dossier_du_fichier_actuel = $dossier_du_fichier;

	     //on explose le chemin pour le mettre dans un tableau chaque nom de dossier
		$premier_carct = $dossier_du_fichier{0};
		$dernier_carct = $dossier_du_fichier{mb_strlen($dossier_du_fichier)-1};
		if($premier_carct==='/' and $dernier_carct==='/') { $dossier_du_fichier=mb_substr("$dossier_du_fichier", 1); $dossier_du_fichier=mb_substr("$dossier_du_fichier", 0, -1);}
		if($premier_carct==='/' and $dernier_carct!='/') { $dossier_du_fichier=mb_substr("$dossier_du_fichier", 1); }
		if($premier_carct!='/' and $dernier_carct==='/') { $dossier_du_fichier=mb_substr("$dossier_du_fichier", 0, -1); }
	     $dossier_du_fichier = explode('/', $dossier_du_fichier);

	     //on vérifie si nous somme dans le même dossier qu'au passage précédent
	     if($dossier_du_fichier_precedent!=$dossier_du_fichier_actuel)
             {
  	        $i_c=0;
	      	//si nous étions dans un dossier nous revenons à la racine
  	     	while($i_c<$nb_ancien_enfant)
	     	{
			ftp_cdup($connexion_id);
			$i_c++;
	     	}

 	     	$i_d=0;
	 	//nous dessandons dans l'arboresence pour copier un fichier
   	     	while(!empty($dossier_du_fichier[$i_d]))
                {
			if (@ftp_chdir($connexion_id, $dossier_du_fichier[$i_d])) 
			{
				// le dossier existe
			} else {
	 		  	  //si le dossier n'existe pas on le créer
				  if (@ftp_mkdir($connexion_id, $dossier_du_fichier[$i_d])) 
				  {
					//puis on entre dans le dossier
					@ftp_chdir($connexion_id, $dossier_du_fichier[$i_d]);
				  } else {
						$message_ftp['dossier'] = 'impossible à créer le dossier';
					 }
				}
			$i_d++;
		}
	      $nb_ancien_enfant=$i_d;
              }

	//on charge le fichier
		//debugage
		//echo ftp_pwd($connexion_id)."<br />";
		//echo $source." ";
		//echo $nom_du_fichier."<br /><br />";

	$upload = ftp_put($connexion_id, $nom_du_fichier, $source, FTP_BINARY);
	// Vérification de téléchargement
	//if (!$upload) { } else { }
	//on entre dans une variable le dossier ou nous somme actuellement (chemin complet)
	$dossier_du_fichier_precedent = $dossier_du_fichier_actuel;
	$i_tab_ftp++;
        }

 // Fermeture de la connexion FTP.
 ftp_close($connexion_id);
 }

// fonction permettant l'envoie d'un tableau contenant les informations de mise à jour du logiciel fichier par fichier
// ex: info_miseajour_fichier($site_de_miseajour, $version_system)
// appel de la fonction: info_miseajour_fichier(le site_de_miseajour, version du logiciel);
function info_miseajour_fichier($site_de_miseajour, $version_system)
 {
	// définition des variables
	$ligne='';

	//on recherche le fichier de mise à jour sur le site du principal des fichiers d'après la version installé
	$version_system = my_eregi_replace('\.','',$version_system);
	if(url_exists($site_de_miseajour."version".$version_system.".msj"))
	{
	    if ($fp = fopen($site_de_miseajour."version".$version_system.".msj","r"))
 	     {
		$nb='1';
		while (!feof($fp)) //Jusqu'a la fin du fichier
		{             
		        $ligne = fgets($fp,4096);      // je lit la ligne
		        $liste = explode(";",$ligne);  // je transforme la ligne en liste tout les sepration avec un ;
		        if ($ligne == "") {break;}     
	        	//je distribue les variable
	              $info_fichier_serveur['date_fichier'][$nb] = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[0]));
	              $info_fichier_serveur['heure_fichier'][$nb] = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[1].':00'));
	              $info_fichier_serveur['nom_fichier'][$nb] = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[2]));
        	      $info_fichier_serveur['emplacement_fichier'][$nb] = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[3]));
	              $info_fichier_serveur['md5_fichier'][$nb] = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[4]));
        	      $info_fichier_serveur['descriptif_fichier'][$nb]  = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[5]));
        	      $info_fichier_serveur['source_fichier'][$nb]  = preg_replace("/(\r\n|\n|\r)/", " ", trim($liste[6]));
  	 	      $nb = $nb+1;
		}
	   	fclose($fp);
	    }
	}
 return($info_fichier_serveur);
 }

// fonction permettant l'envoie d'un tableau contenant les informations sur les mise à jour déjas effectué
// ex: info_miseajour_base()
// appel de la fonction: info_miseajour_base();
function info_miseajour_base()
 {
	$prefix_base=''; $info_miseajour_base='';

	//on recherche les mise à jour de fichier dans la base
	$requete_liste_miseajour = mysql_query("SELECT * FROM ".$prefix_base."miseajour");
        while ($donne_liste_miseajour = mysql_fetch_array($requete_liste_miseajour))
          {
	              $info_miseajour_base[$donne_liste_miseajour['emplacement_miseajour'].''.$donne_liste_miseajour['fichier_miseajour']]['date'] = date_fr($donne_liste_miseajour['date_miseajour']);
	              $info_miseajour_base[$donne_liste_miseajour['emplacement_miseajour'].''.$donne_liste_miseajour['fichier_miseajour']]['heure'] = $donne_liste_miseajour['heure_miseajour'];
    	  }
 return($info_miseajour_base);
 }

// fonction permettant de copié un fichier d'internet vers le dossier temporaire de mise à jour
// ex: copie_fichier_temp()
// appel de la fonction: copie_fichier_temp(tableau des données);
function copie_fichier_temp($tableau, $rep_temp)
 {

	//variable du tableau qu'on doit avoir
	// $tableau['source_fichier']['1'] -> source du fichier http://www.aaaaa.fr/text.txt
	// $tableau['nom_fichier']['1'] -> nom du fichier cible
	// $tableau['emplacement_fichier']['1'] -> emplacement du fichier cible
	// $tableau['date_fichier']['1'] -> md5 du fichier
	// $tableau['heure_fichier']['1'] -> md5 du fichier
	// $tableau['md5_fichier']['1'] -> md5 du fichier


	$source=''; $md5_de_verif=''; $cible='';

	//fichier source
	$source       = $tableau['source_fichier']['1'];
	$md5_de_verif = $tableau['md5_fichier']['1'];

	//fichier cible
	$nom_fichier  = $tableau['nom_fichier']['1'];
	$cible        = $rep_temp.$nom_fichier;

	// si le dossier de téléchargement de mise à jour n'existe pas on le créer
	if (!is_dir($rep_temp))
	 {
		$old = umask(0000); 
		mkdir($rep_temp, 0777);
		chmod($rep_temp, 0777);
		umask($old);
	 }

	// on copie le fichier
	$old = umask(0000); 
	if(!url_exists($source))
         {
	   // le téléchargement n'a pas réussi car le fichier de mise à jour n'est pas présent
	 } else {
		  // le fichier existe on le copie

		  if (!copy($source, $cible)) 
                  { 
		    // le téléchargement n'a pas réussi echec de connection au serveur de mise à jour
		  } else {
			    umask($old);
			    // le fichier à été copier
			    // on vérifie le md5
			    $md5_du_fichier_telecharge = md5_file($rep_temp.$nom_fichier);
			    if($md5_de_verif!=$md5_du_fichier_telecharge)
			    {
			      // si le md5 n'est pas bon, on supprime le fichier téléchargé
				$tableau_fichier['source_fichier']['1'] = $cible;
				$tableau_fichier['nom_fichier']['1'] = $tableau['nom_fichier']['1'];
				$tableau_fichier['emplacement_fichier']['1'] = $tableau['emplacement_fichier']['1'];
				$tableau_fichier['date_fichier']['1'] = $tableau['date_fichier']['1'];
				$tableau_fichier['heure_fichier']['1'] = $tableau['heure_fichier']['1'];
				$tableau_fichier['md5_fichier']['1'] = $tableau['md5_fichier']['1'];
				$tableau_fichier['status_fichier']['1'] = 'erreur fichier corrompus';
			     } else {
				      // si le md5 est bon on ne touche à rien
					$tableau_fichier['source_fichier']['1'] = $cible;
					$tableau_fichier['nom_fichier']['1'] = $tableau['nom_fichier']['1'];
					$tableau_fichier['emplacement_fichier']['1'] = $tableau['emplacement_fichier']['1'];
					$tableau_fichier['date_fichier']['1'] = $tableau['date_fichier']['1'];
					$tableau_fichier['heure_fichier']['1'] = $tableau['heure_fichier']['1'];
					$tableau_fichier['md5_fichier']['1'] = $tableau['md5_fichier']['1'];
					$tableau_fichier['status_fichier']['1'] = 'pass';
				     }
			 }
		}
	return($tableau_fichier);
 }

// fonction permettant de supprimer tout les dossier et sous dossier et fichiers d'une sélection de dossier d'un tableau
// ex: supprimer_rep(tableau des dossiers à supprimer)
// appel de la fonction: supprimer_rep(tableau des données);
// source: http://www.phpcs.com/codes/SUPPRIMER-PLUSIEURS-REPERTOIRES-TOUT-QU-DEDANS_33556.aspx
function supprimer_rep($tableau) { // fonction pour supprimer un ou plusieurs repertoires et tout ce qu'il y a dedans
    foreach ($tableau as $dir) {
    if (file_exists ($dir)) {
         $dh = opendir ($dir);
         while (($file = readdir ($dh)) !== false ) {
             if ($file !== '.' && $file !== '..') {
             if (is_dir ($dir.'/'.$file)) {
                 $tab = array ($dir.'/'.$file);
              supprimer_rep ($tab); // si on trouve un repertoire, on fait un appel recursif pour fouiller ce repertoire
             }
             else {
                 if (file_exists ($dir.'/'.$file)) {
                     unlink ($dir.'/'.$file); // si on trouve un fichier, on le supprime
                 }
             }
         }
     }
     closedir ($dh);
     if (is_dir ($dir)) {
         rmdir ($dir); // on supprime le repertoire courant
     }
 return true;
}
}
}
?>

<?php

$config = array(

	/*authentification admin saml en utilisant un profil administrateur gepi
	'admin' => array(
		'gepiauth:LocalDB',
		'required_statut' => 'administrateur',
		'name' => array(
		    'fr' => 'Admin simplesaml',
		),
		'description' => array(
		    'fr' => 'Authentification spéciale pour l\'administrateur simplesaml',
		),
	),*/



	//authentification gepi
	'local-gepi-db' => array(
		'gepiauth:LocalDB',
		'name' => array(
		    'fr' => 'Accès normal',
		),
		'description' => array(
		    'fr' => 'Authentification locale sur la base d\'utilisateur gepi',
		),
	/*
     * D�commenter cette partie pour ouvrir l'acces CAS
     ),
    
    
    // modifier login, validate, logout en fonction de votre serveur SSO
    // Nantes : 
    //         'login'    => 'https://cas.e-lyco.fr/login',
    //         'validate' => 'https://cas.e-lyco.fr/validate',
    //         'logout'   => 'https://cas.e-lyco.fr/saml/Logout?RelayState=beaussire.vendee',
      
      
    'example-gepicas' => array(
        'gepicas:GepiCAS',
        'cas' => array(
            'login' => 'https://votre.serveur.cas/login',
            'validate' => 'https://votre.serveur.cas/validate',
            'logout' => 'https://votre.serveur.cas/Logout',
        ),
        'search_table' => array(
            'name' => 'plugin_sso_table',
            'cas_uid_column' => 'login_sso',
            'gepi_login_column' => 'login_gepi',
            'champ_uid_retour' => 'username'
        ),
        'ldap'=>array(),
        ),
    
    
    //choix d'authentification entre utilisateur local et utilisateur cas
  'multi-local-cas-gepi' => array(
      'multiauth:MultiAuth',
      'sources' => array('local-gepi-db', 'example-gepicas')
     
     * 
     */
   )
    
    
);

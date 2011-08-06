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
	)
);

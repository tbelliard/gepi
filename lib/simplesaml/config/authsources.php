<?php

$config = array(

	//authentification gepi
	'Authentification locale gepi' => array(
		'gepiauth:LocalDB',
	),
    
	// authentification e-lyco SSO Nantes : 
	'Authentification cas e-lyco' => array(
		'gepicas:GepiCAS',
		'cas' => array(
		    'login' => 'https://cas.e-lyco.fr/login',
		    'validate' => 'https://cas.e-lyco.fr/validate',
		    'logout' => 'https://cas.e-lyco.fr/saml/Logout',
		),
		'search_table' => array(
		    'name' => 'sso_table_correspondance',
		    'cas_uid_column' => 'login_sso',
		    'gepi_login_column' => 'login_gepi',
		    'champ_cas_uid_retour' => 'username'
		),
		'portal_return_url' => 'http://cas.e-lyco.fr',
		'do_source_logout' => false,
	),


	//choix d'authentification entre utilisateur local et utilisateur cas e-lyco
	'Authentification au choix locale ou cas e-lyco' => array(
		'multiauth:MultiAuth',
		'sources' => array('Authentification locale gepi', 'Authentification cas e-lyco')
	),
    
	
);

//configuration d'un choix multiple avec toutes les sources configurées
$sources_array = array_keys($config);
if (!empty($sources_array)) {
	//la source définie ci dessous est utilisé par la classe SimpleSAML_Auth_GepiSimple dans les cas d'erreur de configuration de choix de source
	$config['Authentification au choix entre toutes les sources configurees'] = array('multiauth:MultiAuth', 'sources' => $sources_array);
}


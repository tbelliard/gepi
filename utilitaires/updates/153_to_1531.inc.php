<?php
/**
 * Fichier de mise à jour de la version 1.5.3 à la version 1.5.4
 * 
 * $Id$
 *
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= msj_ok("Champ XXX ajouté avec succès");
 * @copyright Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * @license GNU/GPL,
 * @package General
 * @subpackage mise_a jour
 * @see msj_ok()
 * @see msj_erreur()
 * @see msj_present()
 */

$result .= "<h3 class='titreMaJ'>Mise à jour vers la version 1.5.3.1" . $rc . $beta . " :</h3>";


$test = sql_query1("SHOW TABLES LIKE 'edt_semaines'");
if ($test == -1) {
	$result .= "<br />Création de la table 'edt_semaines'. ";
	$sql="CREATE TABLE edt_semaines (id_edt_semaine int(11) NOT NULL auto_increment,num_edt_semaine int(11) NOT NULL default '0',type_edt_semaine varchar(10) NOT NULL default '', num_semaines_etab int(11) NOT NULL default '0', PRIMARY KEY  (id_edt_semaine)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'edt_semaines': ".$result_inter."<br />";
	}
}

// Modification Eric
// ============= Insertion d'un champ pour le module discipline

$sql = "SELECT commentaire FROM s_incidents LIMIT 1";
$req_rank = mysqli_query($GLOBALS["mysqli"], $sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `s_incidents` ADD `commentaire` TEXT NOT NULL ";
    $req_add_rank = mysqli_query($GLOBALS["mysqli"], $sql_request);
    if ($req_add_rank) {
        $result .= "<p style=\"color:green;\">Ajout du champ commentaire dans la table <strong>s_incidents</strong> : ok.</p>";
    }
    else {
        $result .= "<p style=\"color:red;\">Ajout du champ commentaire à la table <strong>s_incidents</strong> : Erreur.</p>";
    }
}
else {
    $result .= "<p style=\"color:blue;\">Ajout du champ commentaire à la table <strong>s_incidents</strong> : déjà réalisé.</p>";
}

//==========================================================
// Modification Delineau
$result .= "<br /><br /><strong>Ajout d'une table pour les \"super-gestionnaires\" d'AID :</strong><br />";
$result .= "<br />&nbsp;->Tentative de création de la table j_aidcateg_super_gestionnaires.<br />";
$test = sql_query1("SHOW TABLES LIKE 'j_aidcateg_super_gestionnaires'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_aidcateg_super_gestionnaires (indice_aid INT NOT NULL ,id_utilisateur VARCHAR( 50 ) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;");
	if ($result_inter == '')
	$result .= msj_ok("La table j_aidcateg_super_gestionnaires a été créée !");
	else
	$result .= $result_inter."<br />";
} else {
		$result .= msj_present("La table j_aidcateg_super_gestionnaires existe déjà.");
}

$champ_courant=array('nom1', 'prenom1', 'nom2', 'prenom2');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'responsables'<br />";
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE responsables CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

$champ_courant=array('nom', 'prenom');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'resp_pers'<br />";
	$query = mysqli_query($GLOBALS["mysqli"], "ALTER TABLE resp_pers CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= msj_ok();
	} else {
			$result .= msj_erreur();
	}
}

?>

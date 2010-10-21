<?php
/*
 * $Id$
 *
 * Fichier de mise à jour de la version 1.5.3 à la version 1.5.4
 * Le code PHP présent ici est exécuté tel quel.
 * Pensez à conserver le code parfaitement compatible pour une application
 * multiple des mises à jour. Toute modification ne doit être réalisée qu'après
 * un test pour s'assurer qu'elle est nécessaire.
 *
 * Le résultat de la mise à jour est du html préformaté. Il doit être concaténé
 * dans la variable $result, qui est déjà initialisé.
 *
 * Exemple : $result .= "<font color='gree'>Champ XXX ajouté avec succès</font>";
 */

$result .= "<br /><br /><b>Mise à jour vers la version 1.5.3.1" . $rc . $beta . " :</b><br />";


$test = sql_query1("SHOW TABLES LIKE 'edt_semaines'");
if ($test == -1) {
	$result .= "<br />Création de la table 'edt_semaines'. ";
	$sql="CREATE TABLE edt_semaines (id_edt_semaine int(11) NOT NULL auto_increment,num_edt_semaine int(11) NOT NULL default '0',type_edt_semaine varchar(10) NOT NULL default '', num_semaines_etab int(11) NOT NULL default '0', PRIMARY KEY  (id_edt_semaine));";
	$result_inter = traite_requete($sql);
	if ($result_inter != '') {
		$result .= "<br />Erreur sur la création de la table 'edt_semaines': ".$result_inter."<br />";
	}
}

// Modification Eric
// ============= Insertion d'un champ pour le module discipline

$sql = "SELECT commentaire FROM s_incidents LIMIT 1";
$req_rank = mysql_query($sql);
if (!$req_rank){
    $sql_request = "ALTER TABLE `s_incidents` ADD `commentaire` TEXT NOT NULL ";
    $req_add_rank = mysql_query($sql_request);
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
$result .= "<br /><br /><b>Ajout d'une table pour les \"super-gestionnaires\" d'AID :</b><br />";
$result .= "<br />&nbsp;->Tentative de création de la table j_aidcateg_super_gestionnaires.<br />";
$test = sql_query1("SHOW TABLES LIKE 'j_aidcateg_super_gestionnaires'");
if ($test == -1) {
	$result_inter = traite_requete("CREATE TABLE IF NOT EXISTS j_aidcateg_super_gestionnaires (indice_aid INT NOT NULL ,id_utilisateur VARCHAR( 50 ) NOT NULL);");
	if ($result_inter == '')
	$result .= "<font color=\"green\">La table j_aidcateg_super_gestionnaires a été créée !</font><br />";
	else
	$result .= $result_inter."<br />";
} else {
		$result .= "<font color=\"blue\">La table j_aidcateg_super_gestionnaires existe déjà.</font><br />";
}

$champ_courant=array('nom1', 'prenom1', 'nom2', 'prenom2');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'responsables'<br />";
	$query = mysql_query("ALTER TABLE responsables CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

$champ_courant=array('nom', 'prenom');
for($loop=0;$loop<count($champ_courant);$loop++) {
	$result .= "&nbsp;->Extension à 50 caractères du champ '$champ_courant[$loop]' de la table 'resp_pers'<br />";
	$query = mysql_query("ALTER TABLE resp_pers CHANGE $champ_courant[$loop] $champ_courant[$loop] VARCHAR( 50 ) NOT NULL;");
	if ($query) {
			$result .= "<font color=\"green\">Ok !</font><br />";
	} else {
			$result .= "<font color=\"red\">Erreur</font><br />";
	}
}

?>

<?php

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/bulletin/test_modele_bull.php';";
$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_test)==0) {
	$sql="INSERT INTO droits VALUES ('/bulletin/test_modele_bull.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Test de modèle pour les bulletins PDF', '1');";
	$res_insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** EN-TETE **************************************
$titre_page = "Test";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

echo "<p>Page de test pour convertir la table 'model_bulletin' en une table à trois champs.<br />Pour les tests, la table 'model_bulletin' n'est pas supprimée.<br />Une table 'model<b>e</b>_bulletin' est créée à la place.</p>\n";

$sql="SELECT * FROM model_bulletin;";
$res_model=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_model)>0) {
	$cpt=0;
	/*
	while($lig_model=mysql_fetch_object($res_model)) {
		$tab_model[$cpt][]
		$cpt++;
	}
	*/
	while($tab_model[$cpt]=mysqli_fetch_assoc($res_model)) {
		$id_model[$cpt]=$tab_model[$cpt]['id_model_bulletin'];
		//echo "\$id_model[$cpt]=\$tab_model[$cpt]['id_model_bulletin']=".$tab_model[$cpt]['id_model_bulletin']."<br />";
		$cpt++;
	}

	for($i=0;$i<count($tab_model);$i++) {
		if(!empty($tab_model[$i])) {
			//echo "<p>\$tab_model[$i]</p>";
			echo "<p>Enregistrement \$tab_model[$i] de l'ancienne table 'model_bulletin'.</p>\n";
			echo "<table border='1'>\n";
			foreach($tab_model[$i] as $key => $value) {
				echo "<tr>\n";
				echo "<th>$key</th>\n";
				echo "<td>$value</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}

	//$sql="DROP TABLE model_bulletin;";
	//$nettoyage=mysql_query($sql);

	$sql="DROP TABLE modele_bulletin;";
	$nettoyage=mysqli_query($GLOBALS["mysqli"], $sql);

	$sql="CREATE TABLE IF NOT EXISTS modele_bulletin (
		id_model_bulletin INT( 11 ) NOT NULL ,
		nom VARCHAR( 255 ) NOT NULL ,
		valeur VARCHAR( 255 ) NOT NULL
		) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$res_model=mysqli_query($GLOBALS["mysqli"], $sql);
	if(!$res_model) {
		echo "<p>ERREUR sur $sql</p>\n";
	}
	else {
		for($i=0;$i<count($tab_model);$i++) {
			$cpt=0;
			//if(isset($tab_model[$i])) {
			if(!empty($tab_model[$i])) {
				//echo "<p>\$tab_model[$i]: ";
				echo "<p>Enregistrements d'après \$tab_model[$i] dans la nouvelle table 'modele_bulletin': ";
				foreach($tab_model[$i] as $key => $value) {
					if($cpt>0) {echo ", ";}

					$sql="INSERT INTO modele_bulletin SET id_model_bulletin='".$id_model[$i]."', nom='".$key."', valeur='".$value."';";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						echo "<span style='color:green;'>$key:$value</span> ";
					}
					else {
						echo "<span style='color:red;'>$key:$value</span> ";
					}
					$cpt++;
				}
				echo "</p>\n";
			}
		}
	}
}

echo "<p><br /></p>\n";

echo "<p><b>Test:</b><br />\n";
$num=1;
$sql="SELECT * FROM modele_bulletin WHERE id_model_bulletin='$num';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);

while($lig=mysqli_fetch_object($res)) {
   $nom=$lig->nom;
   $$nom=$lig->valeur;
   echo "$nom=".$$nom."<br />\n";
}

echo "<p><b>Vérification de la bonne affectation des variables/valeurs:</b><br />
cadre_adresse=$cadre_adresse<br />
largeur_matiere=$largeur_matiere</p>\n";

echo "<p><br /></p>\n";

echo "<p><b>NOTES:</b> Pour les modifications à effectuer ensuite:</p>
<ul>
<li>Pour les requêtes destinées à affecter les valeurs (<i>comme dans l'exemple ci-dessus</i>):<br />
<pre>\$sql=\"SELECT * FROM modele_bulletin WHERE id_model_bulletin='\$num';\";
\$res=mysql_query(\$sql);
while(\$lig=mysql_fetch_object(\$res)) {
   \$nom=\$lig->nom;
   \$\$nom=\$lig->valeur;
   echo \"\$nom=\".\$\$nom.\"&lt;br /&gt;\";
}
</pre>
</li>
<li>Pour les insertions, préfixer les noms de variables à enregistrer d'une chaine de caractères à choisir, par exemple 'reg_':<br />
(<i>de façon à n'insérer dans la table que les bonnes associations et pas tous les autres variables/champs de formulaire utilisés</i>)<br />
&nbsp;&nbsp;&nbsp;&lt;input type='text' name='reg_hauteur_bloc_adresse' value='...' /&gt;<br />
Et récupérer/traiter:
<pre>
\$id_model_bulletin=\$_POST['id_model_bulletin'];
foreach(\$_POST as \$key => \$value) {
   if(preg_match('/^reg_/',\$key)) {
      \$key_modif=preg_replace('/^reg_/','',\$key);
      \$sql=\"INSERT INTO modele_bulletin SET id_model_bulletin='\$id_model_bulletin',
                                              nom='\$key_modif',
                                              valeur='\$value';\";
      \$insert=mysql_query(\$sql);
   }
}
</pre>
</li>
<!--li></li-->
</ul>\n";

//echo "my_ereg_replace('^reg_','','reg_truc_reg_machin')=".my_ereg_replace('^reg_','','reg_truc_reg_machin')."<br />";

echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>

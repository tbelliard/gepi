
<?php

if ($_POST['csv_file_'.$recherche] != "choisissez un fichier") {
	$fichierCSV =  './bases/'.$_POST['csv_file_'.$recherche];
	$separateur = ';';
	
	if (($handle = fopen($fichierCSV, "r")) !== FALSE) {
		$_SESSION['msg_etab'] = "Établissement ".$recherche." non trouvé dans ".$_POST['csv_file_'.$recherche];
		while (($data = fgetcsv($handle, 10000, $separateur)) !== FALSE) {
			$num = count($data);

			if ($data[0] == $recherche) {
				//On a trouvé l'établissement, on l'ajoute dans la base
				$message = "Id : ".$data[0]." - nom : ".$data[1]." - "
				   . "niveau : ". $data[2]." - type : ".$data[3]." - "
				   . "cp : ".$data[4]." - ville : ".$data[5] ;
				if (enregistreEtab($data)) {
					$_SESSION['msg_etab'] = "Établissement enregistré → ".$message;
				} else {
					$_SESSION['msg_etab'] = "Échec de l'enregistrement de ".$message;
				}				   
				break;
			}
		}
		fclose($handle);
	}

} else {
	$_SESSION['msg_etab'] = "Vous devez choisir un fichier .csv où rechercher l'établissement";
}

	



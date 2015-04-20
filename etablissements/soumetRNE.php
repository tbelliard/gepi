
<?php

$nom = $_POST['nom'][$enregistrer];
$niveau = $_POST['niveau'][$enregistrer];
$type = $_POST['type'][$enregistrer];
$cp = $_POST['cp'][$enregistrer];
$ville = $_POST['ville'][$enregistrer];


//$data = array($enregistrer,$_POST['nom_'.$enregistrer],$_POST['niveau_'.$enregistrer],$_POST['type_'.$enregistrer],$_POST['cp_'.$enregistrer],$_POST['ville_'.$enregistrer]);
$data = array($enregistrer, $nom ,$niveau ,$type, $cp,$ville);

$message = "Id : ".$data[0]." - nom : ".$data[1]." - "
   . "niveau : ". $data[2]." - type : ".$data[3]." - "
   . "cp : ".$data[4]." - ville : ".$data[5] ;
if (enregistreEtab($data)) {
	$_SESSION['msg_etab'] = "Établissement enregistré → ".$message;
} else {
	$_SESSION['msg_etab'] = "Échec de l'enregistrement de ".$message;
}				   
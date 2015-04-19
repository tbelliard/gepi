
<?php
$data = array($enregistrer,$_POST['nom_0291552E'],$_POST['niveau_0291552E'],$_POST['type_0291552E'],$_POST['cp_0291552E'],$_POST['ville_0291552E']);
 
$message = "Id : ".$data[0]." - nom : ".$data[1]." - "
   . "niveau : ". $data[2]." - type : ".$data[3]." - "
   . "cp : ".$data[4]." - ville : ".$data[5] ;
if (enregistreEtab($data)) {
	$_SESSION['msg_etab'] = "Établissement enregistré → ".$message;
} else {
	$_SESSION['msg_etab'] = "Échec de l'enregistrement de ".$message;
}				   
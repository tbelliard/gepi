<?php

# on passe par des notifications virtuelles mais regrouper les notifications lors de l'impression par lot serait mieux.
	$loginEleves = array();
	$lastLogin='';
	# On recherche les élèves affichés
	foreach ($results as $traitement) {
		foreach ($traitement->getAbsenceEleveSaisies() as $saisie) {
			if ($saisie->getEleve() != null) {
				if (!in_array($saisie->getEleve()->getLogin(), $loginEleves)) {
					$lastLogin = $saisie->getEleve()->getLogin();
					$loginEleves[] = $saisie->getEleve()->getLogin();
				}
				
			}	
		}
	}
	
	# Pour chaque élève, on crée un traitement virtuel avec tous les traitements sélectionnés
	
	
	# Pour chaque traitement on crée une notification virtuelle
	
	
	# On imprime les notifications virtuelles et on enregistre une notification
	
	
	
	
	print_r($loginEleves);
	
	die ('On imprime');
?>

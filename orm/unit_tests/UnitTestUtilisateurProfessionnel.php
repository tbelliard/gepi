<?php

class UnitTestUtilisateurProfessionnel  {

	public static function getUtilisateurProfessionnel() {
		//Creation d'un utilisateur
		$utilisateur = new UtilisateurProfessionnel();
		$utilisateur->setLogin('UnitTestUtilisateur');
		return $utilisateur;
	}
}
?>

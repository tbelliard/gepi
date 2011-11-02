<?php
if (!isset($_SESSION["login"])) {
	Die();
}

/**
 * Fichier qui définit la classe utilisateur permettant de créer de nouveaux profils dans Gepi
 *
 */
class prof{

	public $login ;
	public $nom ;
	public $prenom ;
	public $civilite ;
	public $email ;
	public $statut ;
	public $etat ;
	public $change_mdp ;
	public $niveau_alerte ;

	/**
	 * Constructor
	 * @access protected
	 */

	function __construct($login_user){

		// On initialise en récupérant toutes les infos
		$sql = "SELECT nom, prenom, civilite, email, statut, etat, change_mdp, niveau_alerte FROM utilisateurs WHERE login = '".$login_user."'";
		$query = mysql_query($sql) OR trigger_error('erreur $query/construct : ', E_USER_NOTICE);
		if ($query) {
			$this->login = $login_user;

			$rep = mysql_fetch_array($query);

			$this->nom = $rep["nom"];
			$this->prenom = $rep["prenom"];
			$this->civilite = $rep["civilite"];
			$this->email = $rep["email"];
			$this->statut = $rep["statut"];
			$this->etat = $rep["etat"];
			$this->change_mdp = $rep["change_mdp"];
			$this->niveau_alerte = $rep["niveau_alerte"];

		}else{
			return '';
			trigger_error('Erreur dans __construct() : ', E_USER_ERROR);
		}
	}

	function prof($login_user){
		// On initialise en récupérant toutes les infos
		$sql = "SELECT nom, prenom, civilite, email, statut, etat, change_mdp, niveau_alerte FROM utilisateurs WHERE login = '".$login_user."'";
		$query = mysql_query($sql);
		if ($query) {
			$this->login = $login_user;

			$rep = mysql_fetch_array($query);

			$this->nom = $rep["nom"];
			$this->prenom = $rep["prenom"];
			$this->civilite = $rep["civilite"];
			$this->email = $rep["email"];
			$this->statut = $rep["statut"];
			$this->etat = $rep["etat"];
			$this->change_mdp = $rep["change_mdp"];
			$this->niveau_alerte = $rep["niveau_alerte"];

		}else{
			return '';
			trigger_error('Erreur dans user() : ', E_USER_ERROR);
		}
	}

	function vraiStatut(){
		if ($this->statut == 'autre') {

			// On récupère le statut de l'utilisateur dans la table droits_utilisateurs
			$sql = "SELECT nom_statut FROM droits_statut ds, droits_utilisateurs du WHERE login_user = '".$this->login."' AND id_statut = ds.id";
			$query = mysql_query($sql);
			$rep = mysql_fetch_array($query);

			return $rep["nom_statut"];

		}else{

			return $this->statut;

		}
	}

}
?>
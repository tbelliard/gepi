<?php

/**
 * Description de class_menu_abs2_inc
 *
 * Données nécessaire à l'affichage des titres des onglets
 *
 * @author regis
 */
class class_menu_abs2_inc {
  
  public  $texte="";
  public  $expli="";
  public  $lien="";
  public  $classe="";

/**
 *
 * Données nécessaire à l'affichage des titres des onglets
 *
 * @author regis
 * @var $statut : Statut de l'utilisateur
 * @var $texte : Texte à afficher
 * @var $page : Page à afficher
 * @var $title : explications lors du survol de l'onglet
 */
  function  __construct($statut, $texte, $page,$title,$classe)
  {
	if(($statut=='cpe')||
		($statut=='scolarite')) {
	  
	  if ($page==='absences_du_jour.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  
	  if ($page==='saisir_groupe.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='saisir_eleve.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='liste_saisies_selection_traitement.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  
	  if ($page==='visu_saisie.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='liste_traitements.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  
	  if ($page==='visu_traitement.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='liste_notifications.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='visu_notification.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($_SESSION['statut']=='scolarite') {
		if ($page==='stats.php'){
		  $this->creeItem($texte, $page,$title,$classe);
		}
	  }
	  
	}else if ($_SESSION['statut']=='professeur') {

	  if ($page==='saisir_groupe.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	  if ($page==='visu_saisie.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  if ($page==='liste_saisies.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  

	} else if ($_SESSION['statut']=='autre') {
	  
	  if ($page==='saisir_eleve.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  
	  if ($page==='visu_saisie.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }
	  
	  if ($page==='liste_saisies.php'){
		$this->creeItem($texte, $page,$title,$classe);
	  }

	}

  }
  
  private function creeItem($texte, $page,$title,$classe){
	$this->lien=$page;
	$this->texte=$texte;
	$this->expli=$title;
	$this->classe=$classe;
	return true;
  }

}
?>

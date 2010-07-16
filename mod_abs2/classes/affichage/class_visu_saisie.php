<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Données nécessaire à l'affichage de la visualisation des saisies
 *
 * @author regis
 */
class class_visu_saisie {

  private $modifiable=TRUE ;
  private $voir_fiche= FALSE;
  private $non_trouvee= FALSE;
  private $peut_traiter= FALSE;
  private $prof_decale= FALSE;
  private $login_eleve='';
  private $message_enregistrement='';
  private $cle='';
  private $tableauEleve=array();
  private $traiteNonModifiable=array();
  private $traiteModifiable=array();
  private $type_absence=array();
  private $traitement_autorise=array();
  private $photo='';

/**
 *passe $modifiable à faux
 */
  public function set_non_modifiable($valeur=FALSE){
	  $this->modifiable=$valeur;
	return TRUE;
  }
  public function get_modifiable(){
	return $this->modifiable;
  }
/**
 *passe $peut_traiter à $valeur
 */
  public function set_peut_traiter($valeur=TRUE){
	  $this->peut_traiter=$valeur;
	return TRUE;
  }
  public function get_peut_traiter(){
	return $this->peut_traiter;
  }
/**
 *passe $non_trouvee à $valeur
 */
  public function set_non_trouvee($valeur=TRUE){
	  $this->non_trouvee=$valeur;
	return TRUE;
  }
  public function get_non_trouvee(){
	return $this->non_trouvee;
  }
/**
 *Stocke $message_enregistrement
 */
  public function set_message_enregistrement($message){
	$this->message_enregistrement=$message;
	return TRUE;
  }
  public function get_message_enregistrement(){
	return $this->message_enregistrement;
  }
/**
 *Stocke $message_enregistrement
 */
  public function set_saisie($saisie){
	$this->saisie=$saisie;
	return TRUE;
  }
/**
 *Stocke $cle
 */
  public function set_cle($cle){
	$this->cle=$cle;
	return TRUE;
  }
  public function get_cle(){
	return $this->cle;
  }
/**
 *Stocke le lien vers la photo
 */
  public function set_photo($photo,$index){
	$this->photo[$index]=$photo;
	return TRUE;
  }
  public function get_photo($index){
	return $this->photo[$index];
  }
/**
 *doit on afficher ou pas le lien vers la fiche de l'élève
 */
  public function set_voir_fiche($voir=TRUE){
	$this->voir_fiche= $voir;
	return TRUE;
  }
  public function get_voir_fiche(){
	return $this->voir_fiche;
  }
/**
 *Login de l'élève pour accéder à sa fiche
 */
  public function set_voir_login($login){
	$this->login_eleve= $login;
	return TRUE;
  }
  public function get_voir_login(){
	return $this->login_eleve;
  }
/**
 * Tableau des lignes à afficher
 * @var $intitule : valeur de la 1ère colonne
 * @var $contenu : valeur de la 2ème colonne
 *
 */
  public function set_tableau_eleve($intitule,$contenu){
    static $a = 0;
	$this->tableauEleve[$a]['intitule']=$intitule;
	$this->tableauEleve[$a]['contenu']=$contenu;
    $a++;
	return TRUE;
  }
  public function get_tableau_eleve(){
	return $this->tableauEleve;
  }
/**
 * Initialise Tableau des lignes à afficher
 * @var $id : id du traitement modifiable
 * @var $description : description du traitement modifiable
 *
 */
  public function set_traitement_Non_modifiable($id,$description){
    static $b = 0;
	$this->traiteNonModifiable[$b]['id']=$id;
	$this->traiteNonModifiable[$b]['description']=$description;
    $b++;
	return TRUE;
  }
  public function get_traitement_Non_modifiable(){
	return $this->traiteNonModifiable;
  }
/**
 * Initialise Tableau des traitements autorises
 * @var $id : id du traitement modifiable
 *
 */
  public function set_traitement_autorise($id){
    static $b = 0;
	$this->traitement_autorise[$b]=$id ;
    $b++;
	return TRUE;
  }
  public function get_traitement_autorise(){
	return $this->traitement_autorise;
  }

/**
 * Initialise Tableau des traitements à afficher
 * @var $id : id du traitement modifiable
 * @var $description : description du traitement modifiable
 *
 */
  public function set_traitement_modifiable($id,$description,$selectionne= FALSE ){
    static $a = 0;
	$this->traiteModifiable[$a]['id']=$id;
	$this->traiteModifiable[$a]['description']=$description;
	$this->traiteModifiable[$a]['selection']=$selectionne;
    $a++;
	return TRUE;
  }
  public function get_traitement_modifiable(){
	return $this->traiteModifiable;
  }
/**
 * Initialise Tableau des types d'absences
 * @var $id : id du traitement modifiable
 * @var $description : description du traitement modifiable
 *
 */
  public function set_type_absence($id,$description){
    static $a = 0;
	$this->type_absence[$a]['id']=$id;
	$this->type_absence[$a]['description']=$description;
    $a++;
	return TRUE;
  }
  public function get_type_absence(){
	return $this->type_absence;
  }
/**
 * Initialise Tableau des types d'absences
 * @var $valeur
 *
 */
  public function set_prof_decale($valeur=TRUE){
	$this->prof_decale=$valeur;
	return TRUE;

  }
  public function get_prof_decale(){
	return $this->prof_decale;
  }



}
?>

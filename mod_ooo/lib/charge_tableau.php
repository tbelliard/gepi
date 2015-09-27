<?php
		
$tab_eleves_OOo[$nb_eleve]=array();

$tab_eleves_OOo[$nb_eleve]['login']=$lig->login;
$tab_eleves_OOo[$nb_eleve]['nom']=$lig->nom;
$tab_eleves_OOo[$nb_eleve]['prenom']=$lig->prenom;
$tab_eleves_OOo[$nb_eleve]['ine']=$lig->no_gep;
$tab_eleves_OOo[$nb_eleve]['elenoet']=$lig->elenoet;
$tab_eleves_OOo[$nb_eleve]['ele_id']=$lig->ele_id;
$tab_eleves_OOo[$nb_eleve]['sexe']=$lig->sexe;
$tab_eleves_OOo[$nb_eleve]['fille']="";
if($lig->sexe=='F') {$tab_eleves_OOo[$nb_eleve]['fille']="e";} // ajouter un e à née si l'élève est une fille
$tab_eleves_OOo[$nb_eleve]['date_nais']=formate_date($lig->naissance);
$tab_eleves_OOo[$nb_eleve]['lieu_nais']=""; // on initialise les champs pour ne pas avoir d'erreurs
if(getSettingValue('ele_lieu_naissance')=="y") {
	$tab_eleves_OOo[$nb_eleve]['lieu_nais']=preg_replace ( '@<[\/\!]*?[^<>]*?>@si'  , ''  , get_commune($lig->lieu_naissance,1)) ;
} // récupérer la commune

$nb_eleve++;
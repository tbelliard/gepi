<?php
/*
*
* Copyright 2001-2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Tableau des droits d'accès

// Déplacer le remplissage du tableau dans une autre page et l'appeler ici pour pouvoir aussi remplir le tableau dans la page RGPD
$tab_droits_acces=array();

//=======================================================================================

// DROITS ENSEIGNANT

$statutItem="enseignant";
$tab_droits_acces[$statutItem]=array();

if(getSettingAOui('active_carnets_notes')) {
	$titreItem='GepiAccesReleveProf';
	$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');
	// si les conditions ne sont pas remplies, afficher en gris.




	$titreItem='GepiAccesReleveProfTousEleves';
	$texteItem="a accès aux relevés de notes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
	  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');




	$titreItem='GepiAccesReleveProfToutesClasses';
	$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." de toutes les classes.<br /><em>(cela peut être utile pour des dispositifs d'aide aux devoirs, d'AP faisant intervenir des professeurs d'autres classes que celle de l'élève)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');




	$titreItem='GepiPeutCreerBoitesProf';
	$texteItem="a le droit de créer et paramétrer des ".$gepiSettings['gepi_denom_boite']."s dans ses carnets de notes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_bulletins')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesMoyennesProf';
	$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." des enseignements/classes dans lesquelles il enseigne";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesMoyennesProfTousEleves';
	$texteItem="a accès aux moyennes de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
	  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesMoyennesProfToutesClasses';
	$texteItem="a accès aux moyennes des ".$gepiSettings['denomination_eleves']." de toutes les classes.<br /><em>(cela peut être utile pour des dispositifs d'aide aux devoirs, d'AP faisant intervenir des professeurs d'autres classes que celle de l'élève)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	//+++++++++++++++++++++++++++

	$titreItem='';
	$texteItem="";




	$titreItem='GepiAccesBulletinSimpleProf';
	$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." des enseignements/classes dans lesquelles il enseigne";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesBulletinSimpleProfTousEleves';
	$texteItem="a accès aux bulletins simples de tous les ".$gepiSettings['denomination_eleves']." des classes dans lesquelles il enseigne
	  <br />(<em>si case non cochée, le ".$gepiSettings['denomination_professeur']." ne voit que les ".$gepiSettings['denomination_eleves']." de ses groupes d'enseignement et pas les autres ".$gepiSettings['denomination_eleves']." des classes concernées</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesBulletinSimpleProfToutesClasses';
	$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." de toutes les classes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='appreciations_types_profs';
	$texteItem="peut utiliser des appréciations-types sur les bulletins.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='autoriser_correction_bulletin';
	$texteItem="peut solliciter des corrections de ses appréciations sur les bulletins une fois la période (<em>partiellement</em>) close (<em>pour reformuler une appréciation, corriger des fautes... de frappe</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='autoriser_signalement_faute_app_prof';
	$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='visuCorrectionsAppProposeesProfs';
	$texteItem="peut voir, sur les bulletins simplifiés, les propositions de correction d'appréciation soumises et non encore validées.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='PeutAutoriserPPaCorrigerSesApp';
	$texteItem="peut autoriser le ".getSettingValue('gepi_prof_suivi')." à corriger les fautes de frappe;) dans ses appréciations.<br />(<em>l'autorisation se fait enseignement par enseignement, par le professeur lui-même dans 'Gérer mon compte'.<br />Le professeur concerné reçoit un mail l'informant d'une modification par le ".getSettingValue('gepi_prof_suivi').".</em>).<br />";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



}
//+++++++++++++++++++++++++++

if(getSettingAOui('active_cahiers_texte')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesCDTToutesClasses';
	$texteItem="a accès à la visualisation des cahiers de textes de toutes les classes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de texte';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiAccesVisuToutesEquipProf';
$texteItem="a accès à la Visualisation de toutes les équipes éducatives";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesGestElevesProf';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est professeur.<br />(<em>ce droit donne aussi accès à l'INE de l'élève, à l'établissement d'origine,...</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




// 20190101
$titreItem='GepiAccesAdresseParentsRespProf';
$texteItem="a accès aux adresses postales des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesAdresseTousParentsProf';
$texteItem="a accès aux adresses postales des responsables de tous les ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesTelParentsRespProf';
$texteItem="a accès aux numéros de téléphone des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesTelTousParentsProf';
$texteItem="a accès aux numéros de téléphone des responsables de tous les ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesMailParentsRespProf';
$texteItem="a accès aux adresses mail des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesMailTousParentsProf';
$texteItem="a accès aux adresses mail des responsables de tous les ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();





/*
// Non enregistrées dans Gepi pour le moment
$titreItem='GepiAccesAdresseElevesRespProf';
$texteItem="a accès aux adresses postales des ".$gepiSettings['denomination_eleves']." dont il est professeur.";



$titreItem='GepiAccesAdresseTousElevesProf';
$texteItem="a accès aux adresses postales de tous les ".$gepiSettings['denomination_eleves'].".";


*/

$titreItem='GepiAccesTelElevesRespProf';
$texteItem="a accès aux numéros de téléphone des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesTelTousElevesProf';
$texteItem="a accès aux numéros de téléphone de tous les ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesMailElevesRespProf';
$texteItem="a accès aux adresses mail des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesMailTousElevesProf';
$texteItem="a accès aux adresses mail de tous les ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();







$titreItem='AccesDerniereConnexionEleProfesseur';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesStatConnexionEleProfesseur';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDetailConnexionEleProfesseur';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDerniereConnexionRespProfesseur';
$texteItem="a accès à la date de la dernière connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesStatConnexionRespProfesseur';
$texteItem="a accès aux statistiques de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDetailConnexionRespProfesseur';
$texteItem="a accès au détail de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est professeur.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




if(getSettingAOui('autorise_edt')) {
	$titreItem='AccesProf_EdtProfs';
	$texteItem="a accès aux emplois du temps des autres professeurs";
	$texteItemComplement=" (<em>sous réserve que le <a href='../edt_organisation/edt.php' target='_blank'>module EDT soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('autorise_edt');



}

$titreItem='GepiPasswordReinitProf';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesModifMaPhotoProfesseur';
$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombino_pers');




$titreItem='AccesFicheBienvenueProfesseur';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();





if(getSettingValue('active_mod_ooo')=='y') {
	$titreItem='OOoUploadProf';
	$texteItem="a accès à l'upload de fichiers modèles openDocument personnels.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ooo');




	$titreItem='OOoAccesTousEleProf';
	$texteItem="a accès aux données (nom, prénom, naissance, INE,...) des élèves de toutes les classes pour les fichiers modèles openDocument à sa disposition.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ooo');



}

if(getSettingValue('active_mod_alerte')=='y') {
	$titreItem='PeutChoisirAlerteSansSonProfesseur';
	$texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_alerte');



}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	$titreItem='AAProfTout';
	$texteItem="a accès aux données d'années antérieures pour tous les ".$gepiSettings['denomination_eleves'];
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');




	$titreItem='AAProfClasses';
	$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des classes pour lesquelles il fournit un enseignement";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');




	$titreItem='AAProfGroupes';
	$texteItem="a accès aux données antérieures des ".$gepiSettings['denomination_eleves']." des groupes auxquels il enseigne";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');



}

//+++++++++++++++++++++++++++

$mod_disc_terme_avertissement_fin_periode=getSettingValue('mod_disc_terme_avertissement_fin_periode');

if(getSettingValue('active_mod_discipline')=='y') {

	$titreItem='';
	$texteItem="";



	$titreItem='visuDiscProfClasses';
	$texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses classes.
	<br />(<em>par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');




	$titreItem='visuDiscProfGroupes';
	$texteItem="peut visualiser dans le module Discipline les incidents concernant les élèves de ses enseignements.
	<br />(<em>par défaut un professeur ne voit que les incidents qu'il a déclaré ou le concernant directement comme protagoniste)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');




	if(getSettingValue('active_mod_ooo')=='y') {
		$titreItem='imprDiscProfRetenueOOo';
		$texteItem="peut imprimer dans le module Discipline une demande de Retenue au format openDocument pour un élève pour lequel le professeur saisit un incident";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	

		$titreItem='extractDiscProf';
		$texteItem="peut extraire au format ODS dans le module Discipline les incidents et sanctions pour les élèves de ses classes.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	

		$titreItem='imprDiscProfAvtOOo';
		$texteItem="peut imprimer dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour les élèves de ses classes.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	
	}
}

//+++++++++++++++++++++++++++
if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesSaisieEctsProf';
	$texteItem="a accès à la pré-saisie des mentions ECTS pour ses groupes.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');




	$titreItem='GepiAccesRecapitulatifEctsProf';
	$texteItem="a accès aux récapitulatifs globaux des crédits ECTS pour ses classes.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');



}
//=======================================================================================

// DROITS PROFESSEUR PRINCIPAL

$statutItem="professeur_principal";

if(getSettingAOui('active_bulletins')) {
	$titreItem='GepiRubConseilProf';
	$texteItem="peut saisir les avis du conseil de classe pour sa classe";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='CommentairesTypesPP';
	$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe
		  <br />(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiProfImprBul';
	$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
				(<em>par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)<br />
				(<em>ce droit donne aussi l'accès aux adresses postales des responsables associés</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiProfImprBulSettings';
	$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='autoriser_signalement_faute_app_pp';
	$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>à ses collègues professeurs</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='autoriser_valider_correction_app_pp';
	$texteItem="peut valider les propositions de correction d'appréciations de ses collègues pour les classes dont il est ".getSettingValue('gepi_prof_suivi').".";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesRestrAccesAppProfP';
	$texteItem="a accès au paramétrage des accès ".$gepiSettings['denomination_responsables']."/".$gepiSettings['denomination_eleves']." aux appréciations/avis des classes dont il est ".getSettingValue("gepi_prof_suivi");
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesBulletinSimplePP';
	$texteItem="a accès aux bulletins simples des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='imprimerConvocationConseilClassePP';
	$texteItem="a accès à l'impression des convocations au conseil de classe et à l'envoi par mail de ces convocations pour les classes dont il est ".getSettingValue("gepi_prof_suivi");
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



}
//+++++++++++++++++++++++++++

if(getSettingAOui('active_carnets_notes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesReleveProfP';
	$texteItem="a accès aux relevés des classes dont il est ".getSettingValue("gepi_prof_suivi");
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');




	$titreItem='GepiProfImprRelSettings';
	$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_discipline')) {
	if(getSettingAOui('active_mod_ooo')) {
		$titreItem='';
		$texteItem="";
	
	
		
		$titreItem='imprDiscProfPRapport';
		$texteItem="peut imprimer dans le module Discipline les rapports pour les élèves des classes dont il est ".getSettingValue("gepi_prof_suivi").".";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	

		$titreItem='extractDiscProfP';
		$texteItem="peut extraire au format ODS dans le module Discipline les incidents et sanctions pour les élèves des classes dont il est ".getSettingValue("gepi_prof_suivi").".";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	

		$titreItem='imprDiscProfPAvtOOo';
		$texteItem="peut imprimer dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour les élèves des classes dont il est ".getSettingValue("gepi_prof_suivi").".";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	

		$titreItem='saisieDiscProfPAvt';
		$texteItem="peut saisir dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour les élèves des classes dont il est ".getSettingValue("gepi_prof_suivi").".";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	
	
	}
}
//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiAccesPPTousElevesDeLaClasse';
$texteItem="dans le cas où il y a plusieurs '".getSettingValue('gepi_prof_suivi')."' dans la classe, donner les mêmes droits à chaque '".getSettingValue('gepi_prof_suivi')." sur l'ensemble des élèves de la classe<br />(<em>sinon un élève n'est associé qu'à un seul ".getSettingValue('gepi_prof_suivi')."</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesGestElevesProfP';
$texteItem="a accès aux fiches des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi")."<br />(<em>ce droit donne aussi accès à l'INE de l'élève, à l'établissement d'origine,...</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




// 20190101
$titreItem='GepiAccesAdresseParentsRespPP';
$texteItem="a accès aux adresses postales des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesTelParentsRespPP';
$texteItem="a accès aux numéros de téléphone des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='GepiAccesMailParentsRespPP';
$texteItem="a accès aux adresses mail des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




/*
$titreItem='GepiAccesAdresseElevesRespPP';
$texteItem="a accès aux adresses postales des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";


*/

if((getSettingAOui('ele_tel_pers'))||(getSettingAOui('ele_tel_port'))||(getSettingAOui('ele_tel_prof'))) {
	$titreItem='GepiAccesTelElevesRespPP';
	$texteItem="a accès aux numéros de téléphone des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



}

$titreItem='GepiAccesMailElevesRespPP';
$texteItem="a accès aux adresses mail des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi").".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();





if(getSettingAOui('active_module_trombinoscopes')) {
	$titreItem='GepiAccesGestPhotoElevesProfP';
	$texteItem="a accès à l'upload des photos de ses ".$gepiSettings['denomination_eleves']." si le module
	  		trombinoscope est activé et si le ".$gepiSettings['denomination_professeur']." a accès aux fiches
			".$gepiSettings['denomination_eleves']." (<em>ci-dessus</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombinoscopes');



}

$titreItem='AccesDerniereConnexionEleProfP';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesStatConnexionEleProfP';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDetailConnexionEleProfP';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDerniereConnexionRespProfP';
$texteItem="a accès à la date de la dernière connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesStatConnexionRespProfP';
$texteItem="a accès aux statistiques de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




$titreItem='AccesDetailConnexionRespProfP';
$texteItem="a accès au détail de connexion des responsables des ".$gepiSettings['denomination_eleves']." dont il est ".getSettingValue("gepi_prof_suivi");
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();





//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	$titreItem='AAProfPrinc';
	$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est ".$gepiSettings['denomination_professeur']." principal";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');



}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesSaisieEctsPP';
	$texteItem="peut saisir les crédits ECTS pour sa classe";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');




	$titreItem='GepiAccesEditionDocsEctsPP';
	$texteItem="peut éditer les relevés ECTS pour sa classe";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');



}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_examen_blanc')) {
	$titreItem='';
	$texteItem="";



	$titreItem='modExbPP';
	$texteItem="peut créer des examens blancs pour les classes dont il est ".getSettingValue('gepi_prof_suivi');
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Examens blancs';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_examen_blanc');


}

if(getSettingAOui('active_mod_genese_classes')) {
	$titreItem='geneseClassesSaisieProfilsPP';
	$texteItem="peut saisir les profils d'élèves en vue de la Genèse des futures classes pour les classes dont il est ".getSettingValue('gepi_prof_suivi');
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Genèse des classes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_genese_classes');


}
//=======================================================================================

// DROITS SCOLARITE

if(getSettingAOui('active_bulletins')) {
	$statutItem="scolarite";
	$titreItem='GepiRubConseilScol';
	$texteItem="peut saisir les avis du conseil de classe";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='CommentairesTypesScol';
	$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
				(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiScolImprBulSettings';
	$texteItem="a accès au paramétrage de l'impression des bulletins";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='autoriser_signalement_faute_app_scol';
	$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='PeutDonnerAccesBullNotePeriodeCloseScol';
	$texteItem="a accès à l'ouverture exceptionnelle de saisie/correction de notes du bulletin d'un enseignement particulier en période partiellement close<br />(<em>typiquement pour corriger une erreur sans devoir rouvrir complètement la période en saisie pour tous les professeurs, ni devoir passer par un compte secours pour faire la modification à la place du professeur.</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='PeutDonnerAccesBullAppPeriodeCloseScol';
	$texteItem="a accès à l'ouverture exceptionnelle de saisie/correction d'appréciations du bulletin d'un enseignement particulier en période partiellement close<br />(<em>typiquement pour corriger une erreur sans devoir rouvrir complètement la période en saisie pour tous les professeurs, ni devoir passer par un compte secours pour faire la modification à la place du professeur.</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='AccesModifAppreciationScol';
	$texteItem="peut corriger les appréciations des professeurs en période non close.<br />(<em>cela permet de corriger des fautes pendant le conseil de classe.<br />Le professeur concerné reçoit un mail l'informant de la modification.</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='ScolGererMEP';
	$texteItem="peut gérer les éléments de programmes apparaissant sur les bulletins.<br />(<em>cela permet de corriger des fautes dans les éléments de programmes ou de modifier des éléments trop longs.</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_carnets_notes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesReleveScol';
	$texteItem="a accès à tous les relevés de notes de toutes les classes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiScolImprRelSettings';
	$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='PeutDonnerAccesCNPeriodeCloseScol';
	$texteItem="a accès à l'ouverture exceptionnelle de saisie/correction dans le carnet de notes d'un enseignement particulier en période partiellement close<br />(<em>typiquement pour corriger une erreur sans devoir rouvrir complètement la période en saisie pour tous les professeurs</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');


}

//+++++++++++++++++++++++++++
if(getSettingAOui('active_cahiers_texte')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesCdtScol';
	$texteItem="a accès à tous les cahiers de textes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	$titreItem='GepiAccesCdtScolRestreint';
	$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
				<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	// ====== Visa des cahiers de texte =====
	$titreItem='GepiAccesCdtVisa';
	$texteItem="Peut viser les cahiers de textes ";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	// ====== Droits sur la page cahiers de texte =====
	if (getSettingValue('GepiAccesCdtScolRestreint') =='yes'
			||getSettingValue('GepiAccesCdtScol')=='yes'
			||getSettingValue('GepiAccesCdtVisa')=='yes') {
	  // il faut pouvoir voir les cahiers de textes
	  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'yes'))
		$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
	  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'yes'))
		$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
	} else {
	  // il ne faut pas pouvoir voir les cahiers de textes même en accès direct à la page
	  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte_2/see_all.php",'no'))
		$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte_2/see_all.php";
	  if (!$droitAffiche->ouvreDroits($statutItem, '', "/cahier_texte/see_all.php",'no'))
		$tbs_message = "Erreur lors de l'enregistrement des droits de /cahier_texte/see_all.php";
	}

	// ====== Droits sur la page Visa des cahiers de texte =====
	if (!$droitAffiche->ouvreDroits($statutItem, $titreItem, "/cahier_texte_admin/visa_ct.php"))
	  $tbs_message = "Erreur lors de l'enregistrement des droits de ".$titreItem;
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='saisieModalitesAccompagnementScol';
$texteItem="peut saisir les modalités d'accompagnement des élèves <em>(Segpa, Ulis, PPRE,...)</em>";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesVisuToutesEquipScol';
$texteItem="a accès à la Visualisation de toutes les équipes";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='ScolEditElevesGroupes';
$texteItem="peut modifier la liste des élèves participant à tel ou tel enseignement<br />
(<em>cela permet de prendre en compte les signalements d'erreurs d'affectation d'élèves remontés par les professeurs</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesTouteFicheEleveScolarite';
$texteItem="a le droit d'accéder à toutes les fiches élève";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDerniereConnexionEleScolarite';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesStatConnexionEleScolarite';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDetailConnexionEleScolarite';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDerniereConnexionRespScolarite';
$texteItem="a accès à la date de la dernière connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesStatConnexionRespScolarite';
$texteItem="a accès aux statistiques de connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDetailConnexionRespScolarite';
$texteItem="a accès au détail de connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesMajSconetScol';
$texteItem="a le droit d'effectuer les mises à jour des tables élèves et responsables d'après les fichiers XML de Siècle/Sconet";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_discipline')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiDiscDefinirLieuxScol';
	$texteItem="a accès à la définition des lieux d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirRolesScol';
	$texteItem="a accès à la définition des rôles dans les incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='GepiDiscDefinirMesuresScol';
	$texteItem="a accès à la définition des mesures prises ou demandées à la suite d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='GepiDiscDefinirSanctionsScol';
	$texteItem="a accès à la définition des sanctions";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='GepiDiscDefinirNaturesScol';
	$texteItem="a accès à la définition des natures d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='GepiDiscDefinirCategoriesScol';
	$texteItem="a accès à la définition des catégories d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='GepiDiscDefinirDestAlertesScol';
	$texteItem="a accès à la définition des destinataires d'alertes suite à des incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();


}

if(getSettingValue('active_mod_ooo')=='y') {
	//+++++++++++++++++++++++++++

	$titreItem='';
	$texteItem="";



	$titreItem='OOoUploadScol';
	$texteItem="a accès à l'upload de fichiers modèles openDocument personnels.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ooo');



	if(getSettingAOui('active_mod_discipline')) {
		$titreItem='OOoUploadScolDiscipline';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Discipline.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');
	

	}

	if(getSettingValue('active_module_absence')=='2') {
		$titreItem='OOoUploadScolAbs2';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Absences 2.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_absence');
	

	}

	if(getSettingAOui('active_notanet')) {
		$titreItem='OOoUploadScolNotanet';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Notanet.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_notanet');
	

	}

	if(getSettingAOui('active_mod_ects')) {
		$titreItem='OOoUploadScolECTS';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module ECTS.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');
	

	}
}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiPasswordReinitScolarite';
$texteItem="peut réinitialiser elle-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_module_trombino_pers')) {
	$titreItem='GepiAccesModifMaPhotoScolarite';
	$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombino_pers');


}

$titreItem='AccesFicheBienvenueScolarite';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='ScolResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='ScolResetPassEle';
$texteItem="peut réinitialiser les mots de passe des comptes de statut élève<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_mod_alerte')) {
	$titreItem='PeutChoisirAlerteSansSonScolarite';
	$texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_alerte');


}

$titreItem='droit_informer_evenement_scolarite';
$texteItem="peut informer par mail (sous réserve que le mail du destinataire soit renseigné) les personnes concernées par un événement.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_alerte');

//$tbs_message = 'Erreur lors du chargement de '.$titreItem;

//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	$titreItem='AAScolTout';
	$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves'];
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');



	$titreItem='AAScolResp';
	$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." des classes dont il est responsable";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_ects')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesSaisieEctsScolarite';
	$texteItem="peut saisir les crédits ECTS";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');



	$titreItem='GepiAccesEditionDocsEctsScolarite';
	$texteItem="peut éditer les relevés d'ECTS";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');



	$titreItem='GepiAccesRecapitulatifEctsScolarite';
	$texteItem="a accès aux récapitulatifs globaux des crédits ECTS.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='ECTS';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_genese_classes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='geneseClassesSaisieProfilsScol';
	$texteItem="peut saisir les profils d'élèves en vue de la Genèse des futures classes.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Genèse des classes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_genese_classes');


}

//=======================================================================================

// DROITS CPE

$statutItem="cpe";

if(getSettingAOui('active_carnets_notes')) {
	// Relevés de notes
	$titreItem='GepiAccesReleveCpe';
	$texteItem="a accès aux relevés de notes des élèves qu'il a en responsabilité";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesReleveCpeTousEleves';
	$texteItem="a accès à tous les relevés de notes de toutes les classes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiCpeImprRelSettings';
	$texteItem="a accès au paramétrage de l'impression des relevés de notes HTML";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_bulletins')) {
	$titreItem='';
	$texteItem="";



	// Bulletins
	$titreItem='GepiCpeImprBul';
	$texteItem="édite/imprime les bulletins périodiques des classes dont il a la charge.<br />
				(<em>Par défaut, seul un utilisateur ayant le statut scolarité peut éditer les bulletins</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiCpeImprBulSettings';
	$texteItem="a accès au paramétrage de l'impression des bulletins (<em>lorsqu'il est autorisé à éditer/imprimer les bulletins</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='autoriser_signalement_faute_app_cpe';
	$texteItem="peut signaler, en période ouverte ou partiellement close, (<em>aux professeurs concernés</em>) des fautes (<em>de frappe;</em>) dans les appréciations des bulletins (<em>pour leur permettre corriger avant impression des bulletins</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	// Avis du conseil de classe
	$titreItem='GepiRubConseilCpe';
	$texteItem="peut saisir les avis du conseil de classe (<em>pour les élèves qu'il a en responsabilité</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiRubConseilCpeTous';
	$texteItem="peut saisir les avis du conseil de classe (<em>pour tous les élèves de toutes les classes</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='CommentairesTypesCpe';
	$texteItem="peut utiliser des commentaires-types dans ses saisies d'avis du conseil de classe<br />
				(<em>sous réserve de pouvoir saisir les avis du conseil de classe</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='imprimerConvocationConseilClasseCpe';
	$texteItem="a accès à l'impression des convocations au conseil de classe et à l'envoi par mail de ces convocations pour les classes dont il est CPE";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_cahiers_texte')) {
	$titreItem='';
	$texteItem="";



	// CDT
	$titreItem='GepiAccesCdtCpe';
	$texteItem="a accès aux cahiers de textes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	$titreItem='GepiAccesCdtCpeRestreint';
	$texteItem="a accès aux cahiers de textes des ".$gepiSettings['denomination_eleves']." dont il a la responsabilité<br />
				<em>bloque l'affichage des cahiers de textes de toutes les classes</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	//Années antérieures
	$titreItem='AACpeTout';
	$texteItem="a accès aux données d'années antérieures de tous les ".$gepiSettings['denomination_eleves'];
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');



	$titreItem='AACpeResp';
	$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');


}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



// Mon compte
$titreItem='GepiPasswordReinitCpe';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_module_trombino_pers')) {
	$titreItem='GepiAccesModifMaPhotoCpe';
	$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();


}

$titreItem='AccesFicheBienvenueCpe';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='CpeResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='CpeResetPassEle';
$texteItem="peut réinitialiser les mots de passe des comptes de statut élève<br />(<em>sous réserve que le mode d'authentification (gepi/sso/ldap) du compte le permette</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++
/*

// Inutile: Pour le moment, le CPE n'a pas accès aux fiches responsables (seulement aux infos via Consultation d'un élève)

$titreItem='';
$texteItem="";



$titreItem='CpeResetPassResp';
$texteItem="peut réinitialiser les mots de passe des comptes de statut responsable";


*/
//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



// Divers
$titreItem='GepiAccesVisuToutesEquipCpe';
$texteItem="a accès à la Visualisation de toutes les équipes<br />
(<em>Par défaut, un CPE ne voit que les équipes des classes dans lesquelles il est responsable d'au moins un élève</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='CpeEditElevesGroupes';
$texteItem="peut modifier la liste des élèves participant à tel ou tel enseignement<br />
(<em>cela permet de prendre en compte les signalements d'erreurs d'affectation d'élèves remontés par les professeurs</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_module_trombinoscopes')) {
	// Photos
	$titreItem='GepiAccesTouteFicheEleveCpe';
	$texteItem="a le droit d'accéder à toutes les fiches élève<br />
	(<em>Par défaut, un CPE ne voit que les fiches des élèves dont il est responsable</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();


}

$titreItem='GepiAccesSaisieTelephoneResponsableCpe';
$texteItem="a le droit de saisir/corriger les numéros de téléphone et mail des responsables.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesSaisieTelephoneEleveCpe';
$texteItem="a le droit de saisir/corriger les numéros de téléphone et mail des élèves.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDerniereConnexionEleCpe';
$texteItem="a accès à la date de la dernière connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesStatConnexionEleCpe';
$texteItem="a accès aux statistiques de connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDetailConnexionEleCpe';
$texteItem="a accès au détail de connexion des ".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDerniereConnexionRespCpe';
$texteItem="a accès à la date de la dernière connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesStatConnexionRespCpe';
$texteItem="a accès aux statistiques de connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesDetailConnexionRespCpe';
$texteItem="a accès au détail de connexion des responsables d'".$gepiSettings['denomination_eleves'].".";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_module_trombinoscopes')) {
	$titreItem='CpeAccesUploadPhotosEleves';
	$texteItem="a accès à l'upload des photos des ".$gepiSettings['denomination_eleves'];
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();
	if(!getSettingAOui('active_module_trombinoscopes')) {
		$texteItem.="in";
	}
	$texteItem.="actif</em>)";


}

$titreItem='GepiAccesPanneauAffichageCpe';
$texteItem="a accès à la saisie de message dans le Panneau d'affichage";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='droit_informer_evenement_cpe';
$texteItem="peut informer par mail (sous réserve que le mail du destinataire soit renseigné) les personnes concernées par un événement.";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();

//$tbs_message = 'Erreur lors du chargement de '.$titreItem;

if(getSettingValue('active_mod_alerte')=='y') {
	$titreItem='PeutChoisirAlerteSansSonCpe';
	$texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_alerte');


}

//+++++++++++++++++++++++++++

if(getSettingValue('active_module_absence')=='2') {
	$titreItem='';
	$texteItem="";



	// Absences
	$titreItem='GepiAccesAbsTouteClasseCpe';
	$texteItem="a le droit d'accéder à toutes les classes pour saisir les absences";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Absences';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	$titreItem='AccesCpeAgregationAbs2';
	$texteItem="a le droit d'accéder au remplissage/vidage de la table agrégation des absences";


}

//+++++++++++++++++++++++++++

// Discipline
if(getSettingValue('active_mod_discipline')=='y') {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiDiscDefinirLieuxCpe';
	$texteItem="a accès à la définition des lieux d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirRolesCpe';
	$texteItem="a accès à la définition des rôles dans les incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirMesuresCpe';
	$texteItem="a accès à la définition des mesures prises ou demandées à la suite d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirSanctionsCpe';
	$texteItem="a accès à la définition des sanctions";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirNaturesCpe';
	$texteItem="a accès à la définition des natures d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirCategoriesCpe';
	$texteItem="a accès à la définition des catégories d'incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='GepiDiscDefinirDestAlertesCpe';
	$texteItem="a accès à la définition des destinataires d'alertes suite à des incidents";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='DisciplineCpeChangeDeclarant';
	$texteItem="a le droit de changer le déclarant d'un incident<br />(<em>pour saisir des incidents à la place d'un professeur,<br />sous réserve que le droit soit explicitement donné par le professeur dans '<strong>Gérer mon compte</strong>'</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	

	$titreItem='DisciplineCpeChangeDefaut';
	$texteItem="a, par défaut, le droit de changer le déclarant d'un incident<br />(<em>pour saisir des incidents à la place des professeurs qui ont autorisé/délégué la saisie de leurs incidents par un CPE<br />et pour ceux n'ont pas explicitement interdit la saisie par un CPE à leur place</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');

	

	$titreItem='saisieDiscCpeAvt';
	$texteItem="peut saisir dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour les élèves des classes dont il est CPE responsable.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	$titreItem='saisieDiscCpeAvtTous';
	$texteItem="peut saisir dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour les élèves de toutes classes.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');



	if(getSettingAOui('active_mod_ooo')) {
		$titreItem='imprDiscCpeAvtOOo';
		$texteItem="peut imprimer dans le module Discipline les '".$mod_disc_terme_avertissement_fin_periode."' pour tous les élèves de toutes les classes.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');
	
	
	}
}

// OOo
if(getSettingValue('active_mod_ooo')=='y') {
//+++++++++++++++++++++++++++

	$titreItem='';
	$texteItem="";



	$titreItem='OOoUploadCpe';
	$texteItem="a accès à l'upload de fichiers modèles openDocument personnels.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ooo');



	if(getSettingAOui('active_mod_discipline')) {
		$titreItem='OOoUploadCpeDiscipline';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Discipline.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');
	

	}

	if(getSettingValue('active_module_absence')=='2') {
		$titreItem='OOoUploadCpeAbs2';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Absences 2.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();
	

	}

	if(getSettingAOui('active_notanet')) {
		$titreItem='OOoUploadCpeNotanet';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module Notanet.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_notanet');
	

	}

	if(getSettingAOui('active_mod_ects')) {
		$titreItem='OOoUploadCpeECTS';
		$texteItem="a accès à l'upload de fichiers modèles openDocument pour le module ECTS.";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Modèles OpenDocument (OpenOffice.org)';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_ects');
	

	}
}



//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_genese_classes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='geneseClassesSaisieProfilsCpe';
	$texteItem="peut saisir les profils d'élèves en vue de la Genèse des futures classes.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Genèse des classes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();


}
//=======================================================================================

// DROITS ADMINISTRATEUR

$statutItem="administrateur";

if(getSettingAOui('active_bulletins')) {
	$titreItem='GepiAdminImprBulSettings';
	$texteItem="a accès au paramétrage de l'impression des bulletins";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAdminValidationCorrectionBulletins';
	$texteItem="a accès à la validation des propositions de corrections des bulletins";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');


}

$titreItem='GepiPasswordReinitAdmin';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



if(getSettingAOui('active_module_trombino_pers')) {
	$titreItem='GepiAccesModifMaPhotoAdministrateur';
	$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombino_pers');


}

if(getSettingValue('active_mod_alerte')=='y') {
	$titreItem='PeutChoisirAlerteSansSonAdministrateur';
	$texteItem="peut choisir s'il accepte ou non une alerte sonore quand une nouvelle alerte/message lui est envoyée.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_alerte');


}

//=======================================================================================

// DROITS ELEVE

$statutItem='eleve';

if(getSettingAOui('autorise_edt')) {
	$titreItem='autorise_edt_eleve';
	$texteItem="a accès à son emploi du temps (ouvre également le droit aux parents)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Emploi du temps';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('autorise_edt');


}
//+++++++++++++++++++++++++++

if(getSettingAOui('active_carnets_notes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesReleveEleve';
	$texteItem="a accès à ses relevés de notes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesColMoyReleveEleve';
	$texteItem="a accès à la colonne moyenne du carnet de notes.<br />Notez que tant que la période n'est pas close, cette moyenne peut évoluer (<em>ajout de notes, modifications de coefficients,...</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesOptionsReleveEleve';
	$texteItem="a accès au tableau des options du relevés de notes (<em>nom court, coef, date des devoirs, ...</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesMoyClasseReleveEleve';
	$texteItem="a accès à la moyenne de la classe pour chaque devoir";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesMoyMinClasseMaxReleveEleve';
	$texteItem="a accès aux moyennes min/classe/max de chaque devoir";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesEvalCumulEleve';
	$texteItem="peut voir les évaluations cumulées (ouvre également le droit aux parents)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_cahiers_texte')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesCahierTexteEleve';
	$texteItem="a accès à son cahier de texte";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	$titreItem='CDTPeutPointerTravailFaitEleve';
	$texteItem="peut pointer les travaux faits ou non du CDT";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');


}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiPasswordReinitEleve';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesFicheBienvenueEleve';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiAccesEquipePedaEleve';
$texteItem="a accès à l'équipe pédagogique le concernant";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesCpePPEmailEleve';
$texteItem="a accès aux adresses email de son CPE et de son professeur principal (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesEquipePedaEmailEleve';
$texteItem="a accès aux adresses email de l'équipe pédagogique le concernant";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

if(getSettingAOui('active_bulletins')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesBulletinSimpleEleve';
	$texteItem="a accès à ses bulletins simplifiés<br />";

	$acces_app_ele_resp=getSettingValue('acces_app_ele_resp');
	if($acces_app_ele_resp=="") {$acces_app_ele_resp='manuel';}
	$delais_apres_cloture=getSettingValue('delais_apres_cloture');
	if(!my_ereg("^[0-9]*$",$delais_apres_cloture)) {$delais_apres_cloture=0;}
	if($_SESSION['statut']=='administrateur') {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	else {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	$texteItemComplement= "<em>Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a></em>";

	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesBulletinSimpleClasseEleve';
	$texteItem="a accès au bulletin simplifié du groupe-classe";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleColonneMoyClasseEleve';
	$texteItem="a accès à la colonne moyenne de la classe pour les enseignements,... sur les bulletins simplifiés et sur les graphes<br />(<em>sous réserve que l'accès aux bulletins simplifiés ou aux graphes soit donné</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleColonneMoyClasseMinMaxEleve';
	$texteItem="a accès aux valeurs min/max des moyennes de la classe<br />(<em>sous réserve que l'accès aux bulletins simplifiés et à la colonne Moyenne de la classe soient donnés</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleMoyGenEleve';
	$texteItem="a accès à sa moyenne générale sur le bulletin simplifié (<em>et aux moyennes min/max/... selon les paramétrages ci-dessus</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleMoyCatEleve';
	$texteItem="a accès à ses moyennes de catégories sur le bulletin simplifié (<em>et aux moyennes min/max/... selon les paramétrages ci-dessus</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesGraphEleve';
	$texteItem="a accès à la visualisation graphique de ses résultats<br />";

	if($_SESSION['statut']=='administrateur') {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	else {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	$texteItemComplement= "<em>Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a></em>";

	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesGraphParamEleve';
	$texteItem="a accès aux paramètres des graphes";


	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');

	$titreItem='GepiAccesGraphRangEleve';
	$texteItem="a accès au choix permettant d'afficher son rang dans les graphes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	$titreItem='AAEleve';
	$texteItem="a accès à ses données d'années antérieures";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_module_trombinoscopes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesModifMaPhotoEleve';
	$texteItem="a le droit d'envoyer/modifier lui-même sa photo dans 'Gérer mon compte'
					<br /><em>(voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Trombinoscopes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombinoscopes');



	$titreItem='GepiAccesEleTrombiTousEleves';
	$texteItem="a accès au trombinoscope de tous les ".$gepiSettings['denomination_eleves']." de l'établissement.<br />
					<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Trombinoscopes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombinoscopes');



	$titreItem='GepiAccesEleTrombiElevesClasse';
	$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_eleves']." de sa classe.<br />
					<em>(sous réserve que le module Trombinoscope-élève soit activé.<br />
					voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Trombinoscopes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombinoscopes');



	if(getSettingAOui('active_module_trombino_pers')) {
		$titreItem='GepiAccesEleTrombiPersonnels';
		$texteItem="a accès au trombinoscope de tous les personnels de l'établissement.<br />
					<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
					voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Trombinoscopes';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombino_pers');
	
	

		$titreItem='GepiAccesEleTrombiProfsClasse';
		$texteItem="a accès au trombinoscope des ".$gepiSettings['denomination_professeurs']." de sa classe.<br />
						<em>(sous réserve que le module Trombinoscope-personnels soit activé.<br />
						voir aussi le module de gestion du trombinoscope pour une gestion plus fine des droits d'accès)</em>";
		$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Trombinoscopes';
		$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
		$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
		$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_module_trombino_pers');
	
	
	}
}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_discipline')) {
	$titreItem='';
	$texteItem="";



	$titreItem='visuEleDisc';
	$texteItem="a accès dans le module Discipline aux incidents le concernant.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');


}

//=======================================================================================

// DROITS RESPONSABLE

$statutItem='responsable';

if(getSettingValue('active_module_absence')=='2') {
	$titreItem='active_absences_parents';
	$texteItem="a accès aux absences des ".$gepiSettings['denomination_eleves']." dont il est responsable (affichage des saisies non traitées 4 heures après la création)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Absences';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



	/*
	$titreItem='abs2_ResponsablePeutJustifier';
	$texteItem="peut justifier les absences et retards pour les ".$gepiSettings['denomination_eleves']." dont il est responsable <em style='color:red'>(expérimental)</em>";


	*/
}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_carnets_notes')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesReleveParent';
	$texteItem="a accès aux relevés de notes des ".$gepiSettings['denomination_eleves']." dont il est responsable";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesColMoyReleveParent';
	$texteItem="a accès à la colonne moyenne du carnet de notes.<br />Notez que tant que la période n'est pas close, cette moyenne peut évoluer (<em>ajout de notes, modifications de coefficients,...</em>).";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesOptionsReleveParent';
	$texteItem="a accès au tableau des options du relevés de notes (<em>nom court, coef, date des devoirs,...</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesMoyClasseReleveParent';
	$texteItem="a accès à la moyenne de la classe pour chaque devoir";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');



	$titreItem='GepiAccesMoyMinClasseMaxReleveParent';
	$texteItem="a accès aux moyennes min/classe/max de chaque devoir";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Carnets de notes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_carnets_notes');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_cahiers_texte')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesCahierTexteParent';
	$texteItem="a accès au cahier de texte des ".$gepiSettings['denomination_eleves']." dont il est responsable";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');



	$titreItem='CDTPeutPointerTravailFaitResponsable';
	$texteItem="peut pointer les travaux faits ou non du CDT pour les ".$gepiSettings['denomination_eleves']." dont il est responsable.";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Cahiers de textes';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_cahiers_texte');


}

//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiPasswordReinitParent';
$texteItem="peut réinitialiser lui-même son mot de passe perdu (<em>si fonction activée</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='AccesFicheBienvenueResponsable';
$texteItem="a le droit d'imprimer sa Fiche Bienvenue depuis 'Gérer mon compte'";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();




//+++++++++++++++++++++++++++

$titreItem='';
$texteItem="";



$titreItem='GepiAccesEquipePedaParent';
$texteItem="a accès à l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesCpePPEmailParent';
$texteItem="a accès aux adresses email du CPE et du professeur principal responsables des ".$gepiSettings['denomination_eleves']." dont il est responsable (<em>paramètre utile seulement si le paramètre suivant est décoché</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='GepiAccesEquipePedaEmailParent';
$texteItem="a accès aux adresses email de l'équipe pédagogique concernant les ".$gepiSettings['denomination_eleves']." dont il est responsable";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



$titreItem='';
$texteItem="";



$titreItem='GepiMemesDroitsRespNonLegaux';
$texteItem="donner les mêmes droits aux responsables non légaux (resp_legal=0) qu'aux responsables légaux sous réserve que les responsables non légaux disposent d'un compte d'utilisateur et que vous cochiez une case dans la fiche de ces responsables.<br />(<em>Note&nbsp;: A l'heure actuelle, créer des comptes resp_legal=0 ne présente pas d'intérêt sans donner de droits, donc sans cocher cette case</em>)";
$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Informations générales, gestion,...';
$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
$tab_droits_acces[$statutItem][$titreItem]['conditions']=array();



//+++++++++++++++++++++++++++

if(getSettingAOui('active_bulletins')) {
	$titreItem='';
	$texteItem="";



	$titreItem='GepiAccesBulletinSimpleParent';
	$texteItem="a accès aux bulletins simplifiés des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";

	if($_SESSION['statut']=='administrateur') {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	else {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	$texteItemComplement= "<em>Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a></em>";

	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');





	$titreItem='GepiAccesBulletinSimpleClasseResp';
	$texteItem="a accès au bulletin simplifié du groupe-classe des élèves dont il est responsable";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleColonneMoyClasseResp';
	$texteItem="a accès à la colonne moyenne de la classe pour les enseignements,... sur les bulletins simplifiés et sur les graphes<br />(<em>sous réserve que l'accès aux bulletins simplifiés ou aux graphes soit donné</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleColonneMoyClasseMinMaxResp';
	$texteItem="a accès aux valeurs min/max des moyennes de la classe<br />(<em>sous réserve que l'accès aux bulletins simplifiés et à la colonne Moyenne de la classe soient donnés</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleMoyGenResp';
	$texteItem="a accès à la moyenne générale sur le bulletin simplifié des élèves dont il est responsable (<em>et aux moyennes min/max/... selon les paramétrages ci-dessus</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesBulletinSimpleMoyCatResp';
	$texteItem="a accès aux moyennes de catégories sur le bulletin simplifié des élèves dont il est responsable (<em>et aux moyennes min/max/... selon les paramétrages ci-dessus</em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesGraphParent';
	$texteItem="a accès à la visualisation graphique des résultats des ".$gepiSettings['denomination_eleves']." dont il est responsable<br />";
	if($_SESSION['statut']=='administrateur') {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <a href='../classes/acces_appreciations.php' onclick=\"return confirm_abandon(this, change, '$themessage')\">Accès aux appréciations et avis du conseil</a>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	else {
		$texteItem.="<em>";
		if(($acces_app_ele_resp=='manuel')||($acces_app_ele_resp=='manuel_individuel')) {
			$texteItem.="L'accès aux appréciations est donné manuellement dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='date') {
			$texteItem.="L'accès aux appréciations est ouvert à la date saisie dans <u>Accès aux appréciations et avis du conseil</u>.<br />";
		} elseif($acces_app_ele_resp=='periode_close') {
			$texteItem.= "L'accès aux appréciations est ouvert automatiquement ";
			if($delais_apres_cloture>0) {$texteItem.=$delais_apres_cloture." jours après ";}
			$texteItem.= "la clôture de la période par un compte scolarité.";
			$texteItem.= "<br />";
		}
		$texteItem.= "</em>";
	}
	$texteItemComplement= "<em>Le mode d'ouverture de l'accès se paramètre en <a href='param_gen.php#mode_ouverture_acces_appreciations'  onclick=\"return confirm_abandon(this, change, '$themessage')\">Gestion générale/Configuration générale</a></em>";

	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');




	$titreItem='GepiAccesGraphParamParent';
	$texteItem="a accès aux paramètres des graphes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');



	$titreItem='GepiAccesGraphRangParent';
	$texteItem="a accès au choix permettant d'afficher son rang dans les graphes";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Bulletins';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_bulletins');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_annees_anterieures')) {
	$titreItem='';
	$texteItem="";



	$titreItem='AAResponsable';
	$texteItem="a accès aux données d'années antérieures des ".$gepiSettings['denomination_eleves']." dont il est responsable";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_annees_anterieures/admin.php' target='_blank'>module Années antérieures soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Archivage années antérieures';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_annees_anterieures');


}

//+++++++++++++++++++++++++++

if(getSettingAOui('active_mod_discipline')) {
	$titreItem='';
	$texteItem="";



	$titreItem='visuRespDisc';
	$texteItem="a accès dans le module Discipline aux incidents concernant les enfants dont il est responsable.";
	$texteItemComplement="<br />(<em>sous réserve que le <a href='../mod_discipline/discipline_admin.php' target='_blank'>module Discipline soit activé</a></em>)";
	$tab_droits_acces[$statutItem][$titreItem]['rubrique']='Discipline';
	$tab_droits_acces[$statutItem][$titreItem]['texteItem']=$texteItem;
	$tab_droits_acces[$statutItem][$titreItem]['texteItemComplement']=$texteItemComplement;
	$tab_droits_acces[$statutItem][$titreItem]['visibilite']=array('administrateur', 'scolarite', 'cpe', 'professeur', 'eleve', 'responsable');
	$tab_droits_acces[$statutItem][$titreItem]['conditions']=array('active_mod_discipline');


}

//+++++++++++++++++++++++++++

//=======================================================================================


?>

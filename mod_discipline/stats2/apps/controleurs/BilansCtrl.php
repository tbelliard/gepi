<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
$niveau_arbo = 2;
require_once("../../lib/initialisations.inc.php");
require_once("Class.Date.php");
require_once("Modele.Incidents.php");
require_once ("Controleur.php");
require_once ("Class.Stats.php");
require_once("Class.Filter.php");
require_once("Class.Individu.php");

class BilansCtrl extends Controleur {

    private $protagonistes_from_db=Null;
    private $incidents_from_db=Null;
    private $modele_incidents=Null;
    private $modele_select=Null;
    private $tri=Null;
    private $libelles=Null;
    private $liste_incidents=Null;
    private $id=Null;
    private $crenaux=Null;
    private $infos_individus=Null;
    private $totaux_par_classe=Null;

    function  __construct() {
        $this->modele_incidents=new Modele_Incidents();
        $this->modele_select=new Modele_Select();
        $this->set_filtres_selected();
        $this->categories=$this->add_choix_null($this->modele_incidents->get_infos_categories(),'categorie','Non affecté','NA');
        $this->sanctions=$this->modele_incidents->get_types_sanctions();
        $this->roles=$this->modele_incidents->get_types_roles();
        $this->mesures=$this->modele_incidents->get_types_mesures();
    }

    function index($tri=Null) {
        if (!isset($_SESSION['etab_all'])&& !isset($_SESSION['eleve_all']) && !isset($_SESSION['pers_all'])  && !isset($_SESSION['stats_classes_selected']) && !isset($_SESSION['individus']))
            echo"<script type='text/javascript'>document.location.href='index.php?ctrl=error&action=select'</script>";
        else
            try {
                $this->affiche_resultats($tri);
            }
            catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }
    }
    private function add_choix_null($liste,$propriete,$valeur,$sigle=Null) {
        $objet=new stdClass();
        $objet->id='Null';
        $objet->$propriete=$valeur;
        if($sigle)$objet->sigle=$sigle;
        $liste[]=$objet;
        return($liste);
    }
    private function set_filtres_selected() {

        $this->objet_filtre=new ClassFilter();
        $this->filtres_roles=$this->objet_filtre->get_roles_selected();
        $this->filtres_categories=$this->objet_filtre->get_id_categories_selected();
        $this->filtres_mesures=$this->objet_filtre->get_id_mesures_selected();
        $this->filtres_sanctions=$this->objet_filtre->get_natures_sanctions_selected();
    }
    private function affiche_resultats($tri=Null) {
        $sanctions=Null;
        $protagonistes=Null;
        $mesures=Null;
        $affichage_etab=isset($_SESSION['etab_all'])? $_SESSION['etab_all']:null ;
        $tri_indiv=isset($_REQUEST['tri_indiv'])? $_REQUEST['tri_indiv']:null ;
        $filtres_roles=$this->filtres_roles;
        $filtres_mesures=$this->filtres_mesures;
        $filtres_sanctions=$this->filtres_sanctions;
        $filtres_categories=$this->filtres_categories;
        if($filtres_categories) $libelles_categories=$this->get_libelle_from_id('categorie',$this->filtres_categories,$this->categories);
        if($filtres_mesures) $libelles_mesures=$this->get_libelle_from_id('mesure',$this->filtres_mesures,$this->mesures);
        $mode_detaille=isset($_SESSION['mode_detaille'])?$_SESSION['mode_detaille']:Null;
        $this->incidents_from_db=$this->modele_incidents->get_incidents($tri,$filtres_categories,$filtres_mesures,$filtres_sanctions,$filtres_roles);
        $this->protagonistes_from_db=$this->modele_incidents->get_protagonistes();
        if (isset($this->incidents_from_db)) $incidents=$this->traite_donnees_incidents($this->incidents_from_db);
        if (isset($this->protagonistes_from_db)) $protagonistes=$this->traite_donnees_protagonistes($this->protagonistes_from_db);
        $this->mesures_from_db=$this->modele_incidents->get_mesures();
        if (isset( $this->mesures_from_db))$mesures=$this->traite_donnees_mesures( $this->mesures_from_db);
        $this->sanctions_from_db=$this->modele_incidents->get_sanctions();
        if (isset( $this->sanctions_from_db)) $sanctions=$this->traite_donnees_sanctions($this->sanctions_from_db);
        //$nombre_total_incidents=$this->modele_incidents->get_nombre_total_incidents();
        //$nombre_total_mesures=$this->modele_incidents->get_nombre_total_mesures();
        //$nombre_total_sanctions=$this->modele_incidents->get_nombre_total_sanctions();
        if(isset($incidents))$this->liste_incidents=$this->make_liste_incidents($incidents);
        if($this->liste_incidents) $totaux=$this->get_totaux_selections($this->liste_incidents,$sanctions);
        $liste_eleves=$this->modele_incidents->get_liste_eleves_par_classe();
        $infos_individus=$this->get_infos_individu($liste_eleves);
        if($liste_eleves) {
            $totaux_indiv=$this->get_totaux_indiv($liste_eleves,$this->liste_incidents,$protagonistes,$mesures,$sanctions,$tri_indiv);
            $totaux_par_classe=$this->get_totaux_par_classe($liste_eleves,$this->liste_incidents,$totaux_indiv);

        }

        include ('bilans.php');
      echo"<script type='text/javascript'>inittab();</script>";
    }

    private function make_liste_incidents($array_incidents) {
        foreach($array_incidents as $key=>$incident) {
            foreach ($incident as $value) {
                if($value!=='pas de résultats')
                    $this->id[$key][]=$value->id_incident;
            }
        }
        if($this->id) return($this->id);
    }
    private function get_totaux_selections($liste,$sanctions) {
        foreach($liste as $selection=>$incidents) {
            $this->totaux[$selection]['incidents']=$this->get_nbre_incidents($incidents);
            $this->totaux[$selection]['mesures']=$this->get_nbre_mesures($incidents);
            if($sanctions) {
                $this->totaux[$selection]['sanctions']=$this->get_nbre_sanctions($incidents);
                $this->totaux[$selection]['heures_retenues']=$this->get_nbre_heures_retenues($incidents,$sanctions);
                $this->totaux[$selection]['jours_exclusions']=$this->get_nbre_jours_exclusions($incidents,$sanctions);
            }else {
                $this->totaux[$selection]['sanctions']=0;
                $this->totaux[$selection]['heures_retenues']=0;
                $this->totaux[$selection]['jours_exclusions']=0;

            }
        }
        return $this->totaux;
    }

    private function get_totaux_indiv($liste_eleves,$liste,$protagonistes,$mesures,$sanctions,$tri_indiv) {
        $this->totaux_indiv=Null;
        $Object_individu=New ClassIndividu();

        foreach($liste_eleves as $classe=>$eleves) {
            if(isset($liste[$classe])) {
                foreach($eleves as $eleve) {
                    $this->info_indiv[$eleve]=$Object_individu->get_infos_individu($eleve,'eleves');
                    $this->totaux_indiv[$eleve]['nom']=$this->info_indiv[$eleve]['nom'];
                    $this->totaux_indiv[$eleve]['prenom']=$this->info_indiv[$eleve]['prenom'];
                    $this->totaux_indiv[$eleve]['classe']=$this->info_indiv[$eleve]['classe'];
                    $this->totaux_indiv[$eleve]['incidents']=$this->get_nbre_incidents_indiv($eleve,$liste[$classe],$protagonistes);
                    $this->totaux_indiv[$eleve]['mesures']=$this->get_nbre_mesures_indiv($eleve,$liste[$classe],$mesures);
                    if($sanctions) {
                        $this->totaux_indiv[$eleve]['sanctions']=$this->get_nbre_sanctions_indiv($eleve,$liste[$classe],$sanctions);
                        $this->totaux_indiv[$eleve]['heures_retenues']=$this->get_nbre_heures_retenues_indiv($eleve,$liste[$classe],$sanctions);
                        $this->totaux_indiv[$eleve]['jours_exclusions']=$this->get_nbre_jours_exclusions_indiv($eleve,$liste[$classe],$sanctions);
                    }
                }
            }
        }
        if($tri_indiv)$this->totaux_indiv=$this->make_tri($this->totaux_indiv,$tri_indiv);
        return $this->totaux_indiv;
    }
    private function get_totaux_par_classe($liste_eleves,$liste,$totaux_indiv) {

        foreach($liste_eleves as $classe=>$eleves) {
            $this->totaux_par_classe[$classe]['incidents']=0;
            $this->totaux_par_classe[$classe]['mesures']=0;
            $this->totaux_par_classe[$classe]['sanctions']=0;
            $this->totaux_par_classe[$classe]['heures_retenues']=0;
            $this->totaux_par_classe[$classe]['jours_exclusions']=0;
            if(isset($liste[$classe])) {
                foreach($eleves as $eleve) {
                    $this->totaux_par_classe[$classe]['incidents']=$this->totaux_par_classe[$classe]['incidents']+$totaux_indiv[$eleve]['incidents'];
                    $this->totaux_par_classe[$classe]['mesures']=$this->totaux_par_classe[$classe]['mesures']+$totaux_indiv[$eleve]['mesures'];
                    $this->totaux_par_classe[$classe]['sanctions']=$this->totaux_par_classe[$classe]['sanctions']+$totaux_indiv[$eleve]['sanctions'];
                    $this->totaux_par_classe[$classe]['heures_retenues']=$this->totaux_par_classe[$classe]['heures_retenues']+$totaux_indiv[$eleve]['heures_retenues'];
                    $this->totaux_par_classe[$classe]['jours_exclusions']=$this->totaux_par_classe[$classe]['jours_exclusions']+$totaux_indiv[$eleve]['jours_exclusions'];
                }
            }

        }
        return($this->totaux_par_classe);
    }
    private function make_tri($totaux_indiv,$tri) {
        foreach ($totaux_indiv as $key => $row) {
            $totaux_nom[$key] = $row['nom'];
            $totaux_prenom[$key] = $row['prenom'];
            $totaux_classe[$key] = $row['classe'];
            $totaux_incidents[$key] = $row['incidents'];
            $totaux_mesures[$key] = $row['mesures'];
            $totaux_sanctions[$key] = $row['sanctions'];
            $totaux_heures_retenues[$key] = $row['heures_retenues'];
            $totaux_jours_exclusions[$key] = $row['jours_exclusions'];
        }
        switch($tri) {

            case 'incidents':$tab=$totaux_incidents;
                $type_tri=SORT_DESC;
                break;
            case 'mesures': $tab=$totaux_mesures;
                $type_tri=SORT_DESC;
                break;
            case 'sanctions': $tab=$totaux_sanctions;
                $type_tri=SORT_DESC;
                break;
            case 'retenues': $tab=$totaux_heures_retenues;
                $type_tri=SORT_DESC;
                break;
            case 'exclusions':$tab=$totaux_jours_exclusions;
                $type_tri=SORT_DESC;
                break;
            case 'nom' :$tab=$totaux_nom;
                $type_tri=SORT_ASC;
                break;
        }
        array_multisort($tab, $type_tri,$totaux_nom, SORT_ASC,$totaux_indiv);

        return($totaux_indiv);
    }



    private function get_nbre_incidents($incidents) {
        return($this->nbre=$this->modele_incidents->get_nombre_total_incidents($incidents));
    }
    private function get_nbre_mesures($incidents) {
        return($this->nbre=$this->modele_incidents->get_nombre_total_mesures($incidents));
    }
    private function get_nbre_sanctions($incidents) {
        return($this->nbre=$this->modele_incidents->get_nombre_total_sanctions($incidents));
    }
    private function get_nbre_heures_retenues($id_incidents,$sanctions) {
        $this->nbre_heures=0;
        foreach ($id_incidents as $id_incident) {
            if (array_key_exists($id_incident,$sanctions)) {
                foreach($sanctions[$id_incident] as $id_sanctions) {
                    foreach ($id_sanctions as $sanction) {
                        if ($sanction->nature=='retenue') $this->nbre_heures=$this->nbre_heures+$sanction->ret_duree;
                    }
                }
            }
        }
        return $this->nbre_heures;
    }

    private function get_nbre_jours_exclusions($id_incidents,$sanctions) {
        $this->nbre_jours=0;
        foreach ($id_incidents as $id_incident) {
            if (array_key_exists($id_incident,$sanctions)) {
                foreach($sanctions[$id_incident] as $id_sanctions) {
                    foreach ($id_sanctions as $sanction) {
                        if ($sanction->nature=='exclusion') $this->nbre_jours=$this->nbre_jours+$sanction->exc_duree;
                    }
                }
            }
        }
        return $this->nbre_jours;
    }
    private function get_nbre_incidents_indiv($eleve,$incidents,$protagonistes) {
        $this->cpt=0;
        foreach ($incidents as $incident) {
            if (array_key_exists($eleve, $protagonistes[$incident])) $this->cpt++;
        }
        return($this->cpt);
    }
    private function get_nbre_mesures_indiv($eleve,$incidents,$mesures) {
        $this->cpt=0;
        foreach ($incidents as $incident) {
            if (isset($mesures[$incident][$eleve]))$this->cpt=$this->cpt+count($mesures[$incident][$eleve]) ;
        }
        return($this->cpt);
    }
    private function get_nbre_sanctions_indiv($eleve,$incidents,$sanctions) {
        $this->cpt=0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve]))$this->cpt=$this->cpt+count($sanctions[$incident][$eleve]) ;
        }
        return($this->cpt);

    }
    private function get_nbre_heures_retenues_indiv($eleve,$incidents,$sanctions) {
        $this->nbre_heures=0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve])) {
                foreach ($sanctions[$incident][$eleve] as $sanction) {
                    if ($sanction->nature=='retenue') $this->nbre_heures=$this->nbre_heures+$sanction->ret_duree;
                }
            }
        }
        return($this->nbre_heures);

    }
    private function get_nbre_jours_exclusions_indiv($eleve,$incidents,$sanctions) {
        $this->nbre_jours=0;
        foreach ($incidents as $incident) {
            if (isset($sanctions[$incident][$eleve])) {
                foreach ($sanctions[$incident][$eleve] as $sanction) {
                    if ($sanction->nature=='exclusion') $this->nbre_jours=$this->nbre_jours+$sanction->exc_duree;
                }
            }
        }
        return($this->nbre_jours);
    }

    private function get_libelle_from_id($type,$array_filtres,$array_libelles) {
        unset ($this->libelles);
        foreach($array_filtres as $value_filtre) {
            foreach ($array_libelles as $value_libelle) {
                if ($value_libelle->id==$value_filtre)
                    $this->libelles[]=$value_libelle->$type;
            }
        }
        return($this->libelles);
    }
    private function get_infos_individu($liste_eleves) {
        $Object_individu=new ClassIndividu();
        if (isset($_SESSION['individus'])) {
            foreach($_SESSION['individus'] as $individu) {
                $this->infos_individus[$individu[0]]=$Object_individu->get_infos_individu($individu[0],$individu[1]);
            }
        }
        if($liste_eleves) foreach($liste_eleves as $classe) {
                foreach($classe as $login) {
                    $this->infos_individus[$login]=$Object_individu->get_infos_individu($login,'eleves');
                }
            }
        return($this->infos_individus);
    }
    private function traite_donnees_incidents($tableau_incidents) {
        foreach($tableau_incidents as $key=>$incidents_from_db) {
            foreach ($incidents_from_db as $incident_from_db) {
                if (isset($incident_from_db->date)) {
                    $incident_from_db->date=$this->traite_date_tableau($incident_from_db->date);
                    $incident_from_db->declarant=$this->traite_login_declarant($incident_from_db->declarant);
                    if(!is_null($incident_from_db->id_categorie)) {
                        $this->infos_categorie=$this->traite_categorie($incident_from_db->id_categorie);
                        $incident_from_db->libelle_categorie=$this->infos_categorie[0]->categorie;
                        $incident_from_db->sigle_categorie=$this->infos_categorie[0]->sigle;
                    }
                }
            }
        }
        return $tableau_incidents;
    }
    private function traite_donnees_mesures($tableau_mesures) {
        foreach($tableau_mesures as $id_incident) {
            foreach ($id_incident as $id_mesure) {
                foreach($id_mesure as $protagoniste) {
                    $protagoniste->login_u=$this->traite_login_declarant($protagoniste->login_u);
                }
            }
        }
        return $tableau_mesures;
    }
    private function traite_donnees_sanctions($tableau_sanctions) {
        foreach($tableau_sanctions as $id_incident) {
            foreach ($id_incident as $id_sanction) {
                foreach($id_sanction as $protagoniste) {
                    if($protagoniste->nature=='exclusion') {
                        $this->crenaux=$this->modele_incidents->get_crenaux();
                        $protagoniste->exc_duree=Gepi_Date::calcule_duree_exclusion($protagoniste->exc_date_debut,$this->crenaux[$protagoniste->exc_heure_debut]->heuredebut_definie_periode,$protagoniste->exc_date_fin,$this->crenaux[$protagoniste->exc_heure_fin]->heurefin_definie_periode);
                    }
                    if(!is_null($protagoniste->ret_date)) $protagoniste->ret_date=$this->traite_date_tableau($protagoniste->ret_date);
                    if(!is_null($protagoniste->exc_date_debut)) $protagoniste->exc_date_debut=$this->traite_date_tableau($protagoniste->exc_date_debut);
                    if(!is_null($protagoniste->exc_date_fin)) $protagoniste->exc_date_fin=$this->traite_date_tableau($protagoniste->exc_date_fin);
                    if(!is_null($protagoniste->trv_date_retour)) $protagoniste->trv_date_retour=$this->traite_date_tableau($protagoniste->trv_date_retour);

                }
            }
        }
        return $tableau_sanctions;
    }

    private function traite_donnees_protagonistes($tableau_protagonistes) {
        foreach($tableau_protagonistes as $incident) {
            foreach($incident as $protagoniste) {
                if($protagoniste!=='pas de résultats') {
                    $this->statut=$this->test_statut($protagoniste->statut);
                    $this->infos_utilisateur=$this->modele_select->get_db_individu_identite($protagoniste->login, $this->statut);
                    $protagoniste->nom=$this->infos_utilisateur['nom'];
                    $protagoniste->prenom=$this->infos_utilisateur['prenom'];
                }
            }
        }
        return $tableau_protagonistes;
    }

    private function traite_login_declarant($login) {

        $this->infos_utilisateur=$this->modele_select->get_db_individu_identite($login, 'personnels');
        return($this->infos_utilisateur['prenom'].' '.$this->infos_utilisateur['nom']);
    }

    private function traite_categorie($id_categorie) {
        return($this->modele_incidents->get_infos_categories($id_categorie));
    }

    private function test_statut($statut) {
        if ($statut=='eleve') return 'eleves';
        else return 'personnels';
    }

    private function traite_date_tableau($date) {
        return(Gepi_Date::format_date_iso_fr($date));
    }

    public function affiche_details() {
        $_SESSION['mode_detaille']=isset($_REQUEST['value'])? $_REQUEST['value']:Null ;
        $this->index();
    }

    public function tri() {
        $this->choix=isset($_REQUEST['choix'])? $_REQUEST['choix']:Null ;
        if($this->choix) {
            switch($this->choix) {
                case 'date' : $this->tri='sin.date';
                    break;
                case 'declarant': $this->tri='sin.declarant';
                    break;
                case 'heure' :$this->tri='sin.declarant';
                    break;
                case 'nature' :$this->tri='sin.nature';
                    break;
                case 'categorie' :$this->tri='sin.id_categorie';
                    break;
            }
            $this->index($this->tri);
        }
    }

    public function choix_filtres() {
        $filtres_roles=$this->filtres_roles;
        $filtres_mesures=$this->filtres_mesures;
        $filtres_sanctions=$this->filtres_sanctions;
        $filtres_categories=$this->filtres_categories;
        $categories=$this->categories;
        $sanctions=$this->sanctions;
        $roles=$this->roles;
        $mesures=$this->mesures;
        include('filtre.php');

    }
    public function filtrer() {

        $_SESSION['filtre']['roles']=(isset($_REQUEST['roles'])?$_REQUEST['roles']:Null);
        $_SESSION['filtre']['categories']=(isset($_REQUEST['categories'])?$_REQUEST['categories']:Null);
        $_SESSION['filtre']['mesures']=(isset($_REQUEST['mesures'])?$_REQUEST['mesures']:Null);
        $_SESSION['filtre']['sanctions']=(isset($_REQUEST['sanctions'])?$_REQUEST['sanctions']:Null);
        $this->set_filtres_selected();
        $this->affiche_resultats();
    }

    public function maj_filtre() {
        $this->type=isset($_REQUEST['type'])?$_REQUEST['type']:Null;
        $this->choix=isset($_REQUEST['choix'])?$_REQUEST['choix']:Null;
        $this->traite_maj_filtre($this->type,$this->choix);
        $this->set_filtres_selected();
        $this->affiche_resultats();
    }

    private function traite_maj_filtre($type,$choix=Null) {
        if (!isset($choix)) unset($_SESSION['filtre'][$type]);
        else {
            switch ($type) {
                case'categories':
                    foreach($this->categories as $categorie) {
                        if ($categorie->categorie==$choix) {
                            foreach($_SESSION['filtre']['categories'] as $key=>$value) {
                                if($categorie->id==$value) {
                                    unset($_SESSION['filtre']['categories'][$key]);
                                }
                            }
                        }
                    }
                    break;
                case'mesures':
                    foreach($this->mesures as $mesure) {
                        if ($mesure->mesure==$choix) {
                            foreach($_SESSION['filtre']['mesures'] as $key=>$value) {
                                if($mesure->id==$value) {
                                    unset($_SESSION['filtre']['mesures'][$key]);
                                }
                            }
                        }
                    }
                    break;
                case'sanctions':
                    foreach($_SESSION['filtre']['sanctions'] as $key=>$value) {
                        if($choix==$value) {
                            unset($_SESSION['filtre']['sanctions'][$key]);
                        }
                    }
                    break;
                case'roles':
                    foreach($_SESSION['filtre']['roles'] as $key=>$value) {
                        if($choix==$value) {
                            unset($_SESSION['filtre']['roles'][$key]);
                        }
                    }
                    break;
            }
        }
    }
}
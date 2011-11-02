<?php
/*
 * Classe spécifique au serveur LDAP de Scribe NG, où sont stockées les
 * éléments de configuration et les requêtes spécifiques à ce système.
 * 
 * Il est peut-être possible de l'utiliser plus largement que pour Scribe,
 * dans la mesure où le schéma LDAP devrait être unifié progressivement
 * entre les différents ENT académiques.
 * 
 * L'objectif premier de cette classe est de faciliter la synchronisation
 * de données pour l'initialisation de l'année scolaire.
 * 
 */

class LDAPServerScribe extends LDAPServer {
    public $base_dn_extension = 'ou=RNE,ou=ac-XXXX,ou=education';
    
    
    #
    # Permet de récupérer tous les eleves du LDAP
    # Retourne la liste des eleves ou false si aucun eleve trouve
    #
    public function get_all_eleves() {
        $filter = "(entpersonprofils=eleve)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=eleves,ou=utilisateurs,'.$this->get_base_branch(), $filter);
        $eleves = array();
        $eleves = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $eleves)) {
            $eleves = false;
            error_log("Aucun eleve trouve");
        }
        return $eleves;
    }

    # Permet de récupérer tous les profs du LDAP
    # Retourne la liste des profs ou false si aucun prof trouvé
    #
    public function get_all_profs(){
        $filter = "(entpersonprofils=enseignant)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=personnels,ou=utilisateurs,'.$this->get_base_branch(), $filter);
        $profs = array();
        $profs = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $profs)) {
            $profs = false;
            error_log("Aucun professeur trouve");
        }
        return $profs;
    }

    # Permet de récupérer tous les profs du LDAP
    # Retourne la liste des profs ou false si aucun prof trouvé
    #
    public function get_all_personnels(){
        $filter = "(entpersonprofils=administratif)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=personnels,ou=utilisateurs,'.$this->get_base_branch(), $filter);
        $profs = array();
        $profs = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $profs)) {
            $profs = false;
            error_log("Aucun personnel trouve");
        }
        return $profs;
    }


    public function get_all_responsables(){
        $filter = "(entpersonprofils=responsable)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=responsables,ou=utilisateurs,'.$this->get_base_branch(), $filter);
        $resps = array();
        $resps = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $resps)) {
            $resps = false;
            error_log("Aucun reponsable trouve");
        }
        return $resps;
    }

    public function get_all_matieres(){
        $filter = "(type=Matiere)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=groupes,'.$this->get_base_branch(), $filter);
        $matieres = array();
        $matieres = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $matieres)) {
            $matieres = false;
            error_log("Aucune matiere trouvee");
        }
        return $matieres;
    }

    public function get_all_equipes(){
        $filter = "(type=Equipe)";
        error_log("Branche=".$this->get_base_branch()."<br>Filtre=".$filter);
        $sr = ldap_search($this->ds, 'ou=local,ou=groupes,'.$this->get_base_branch(), $filter);
        $equipes = array();
        $equipes = ldap_get_entries($this->ds, $sr);
        if (!array_key_exists(0, $equipes)) {
            $equipes = false;
            error_log("Aucune équipe trouvee");
        }
        return $equipes;
    }

    public function get_base_branch(){
        return $this->base_dn_extension.','.$this->base_dn;
    }
    
    # Renvoie les informations de l'utilisateur, au format correct Gepi
    # dans un tableau
    public function get_user_profile($_login) {
      $_login = my_ereg_replace("[^-@._[:space:][:alnum:]]", "", $_login); // securite
	    $search_filter = "(".$this->champ_login."=".$_login.")";
      $sr = ldap_search($this->ds,'ou=utilisateurs,'.$this->get_base_branch(),$search_filter);
      
	    $user = array();
	    $user = ldap_get_entries($this->ds,$sr);
        if (array_key_exists(0, $user)) {
          // Les infos de base
        	$infos = array();
          $infos['raw'] = $user[0];
        	$infos['dn'] = $user[0]['dn'];          
          $infos['nom'] = $user[0][$this->champ_nom][0];
          $infos['prenom'] = $user[0][$this->champ_prenom][0];
          $infos['civilite'] = $user[0]['personaltitle'][0];
          $infos['email'] = $user[0][$this->champ_email][0];
          
        	switch ($user[0]['entpersonprofils'][0]) {
        		case 'enseignant':
        			$infos['statut'] = 'professeur';
        		break;
        		default:
        			$infos['statut'] = $user[0]['entpersonprofils'][0];
        		break;
        	}          
			}
      return $infos;
    }
      
}

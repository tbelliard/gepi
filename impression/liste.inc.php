<?PHP
// Traitement des données pour une classe référencée par id_classe et id_periode
// retourne le nombre d'élèves
function traite_donnees_classe($id_classe,$id_periode)
{
global $login, $ereno, $nom, $prenom, $naissance, $classe, $doublant, $regime, $classe_id_eleve;
global $prefix_base ;

$prepa_requete = $prefix_base.'j_eleves_classes.id_classe = "'.$id_classe.'"';
    $requete='SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id  AND '.$prefix_base.'periode='.$id_periode. ' AND ' .$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.login = '.$prefix_base.'j_eleves_regime.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'; 		//on compte les élèves sélectionné
	//echo $requete;
	//echo "<br>";
	$call_eleve = mysql_query($requete);	
	$nb_eleves = @mysql_num_rows($call_eleve);
	$cpt_i = 0;
	while ( $donner = @mysql_fetch_array( $call_eleve ))
	{
		$login[$cpt_i] = $donner['login']; 
		$ereno[$cpt_i] = $donner['ereno']; 
		$nom[$cpt_i] = $donner['nom'];
		$prenom[$cpt_i] = $donner['prenom'];
		$naissance[$cpt_i] = $donner['naissance'];
		$classe[$cpt_i] = $donner['nom_complet'];
		$doublant[$cpt_i] = $donner['doublant'];
		$regime[$cpt_i] = $donner['regime'];
		$classe_id_eleve[$cpt_i] = $donner['id'];
		$ident_eleve_sel1=$login[$cpt_i];
	    
		$cpt_i++;
	
	/*if($ereno[$cpt_i]!='') 
		{
		 $call_resp = @mysql_query('SELECT * FROM responsables WHERE ereno = "'.$ereno[$cpt_i].'"');
		     $civilite_parents[$ident_eleve_sel1][0] = "M. et Mme";
			 $nom_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "nom1");
			 $prenom_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "prenom1");
			 $adresse1_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "adr1");
			 $adresse2_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "adr1_comp");
			 $ville_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "commune1");
			 $cp_parents[$ident_eleve_sel1][0] = @mysql_result($call_resp , 0, "cp1");
			 $nom_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "nom2");
			 $prenom_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "prenom2");
			 $adresse1_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "adr2");
			 $adresse2_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "adr2_comp");
			 $ville_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "commune2");
			 $cp_parents[$ident_eleve_sel1][1] = @mysql_result($call_resp , 0, "cp2");
		} else {
			 $civilite_parents[$ident_eleve_sel1][0] = '';
				 $nom_parents[$ident_eleve_sel1][0] = '';
				 $prenom_parents[$ident_eleve_sel1][0] = '';
				 $adresse1_parents[$ident_eleve_sel1][0] = '';
				 $adresse2_parents[$ident_eleve_sel1][0] = '';
				 $ville_parents[$ident_eleve_sel1][0] = '';
				 $cp_parents[$ident_eleve_sel1][0] = '';
				 $nom_parents[$ident_eleve_sel1][1] = '';
				 $prenom_parents[$ident_eleve_sel1][1] = '';
				 $adresse1_parents[$ident_eleve_sel1][1] = '';
				 $adresse2_parents[$ident_eleve_sel1][1] = '';
				 $ville_parents[$ident_eleve_sel1][1] = '';
				 $cp_parents[$ident_eleve_sel1][1] = '';
			}
			*/
	}
    return $nb_eleves;
}


// Traitement des données pour un groupe référencé par id_groupe et id_periode
// retourne le nombre d'élèves
function traite_donnees_groupe($id_groupe,$id_periode)
{
global $id_classe_groupe, $login, $ereno, $nom, $prenom, $naissance, $classe, $doublant, $regime, $classe_id_eleve;
global $prefix_base ;

    $current_group = get_group($id_groupe);
    $cpt_i=0;
  	foreach($current_group["eleves"][$id_periode]["users"] as $current_eleve) {
		$eleve_login = $current_eleve["login"];
		$eleve_nom = $current_eleve["nom"];
		$eleve_prenom = $current_eleve["prenom"];

		$sql="SELECT classe, nom_complet FROM classes WHERE id='".$current_eleve["classe"]."'";
		//echo $sql;
		$res_tmp=mysql_query($sql);
		if(mysql_num_rows($res_tmp)==0){
			die("$eleve_login ne serait dans aucune classe???</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_classe=$lig_tmp->classe;
			$eleve_classe_nom_complet=$lig_tmp->nom_complet;
		}
		// La fonction get_group() dans /lib/groupes.inc.php ne récupère pas le sexe et la date de naissance,ereno...
		$sql="SELECT id_classe,naissance,ereno,doublant,regime FROM eleves, j_eleves_classes, j_eleves_regime WHERE eleves.login='$eleve_login' AND j_eleves_classes.login='$eleve_login' AND j_eleves_regime.login='$eleve_login'";
	    //echo $sql;
		$res_tmp=mysql_query($sql);

		if(mysql_num_rows($res_tmp)==0){
			die("Problème avec les infos de $eleve_login</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_naissance=$lig_tmp->naissance;
			$eleve_ereno=$lig_tmp->ereno;
			$eleve_doublant=$lig_tmp->doublant;
			$eleve_regime=$lig_tmp->regime;
			$eleve_id_classe=$lig_tmp->id_classe;	
		}
        //pour rendre compatible groupe et classe  par la suite
		$id_classe_groupe[$cpt_i] = $eleve_id_classe;
		$login[$cpt_i] = $eleve_login; 
		$ereno[$cpt_i] = $eleve_ereno; 
		$nom[$cpt_i] = $eleve_nom;
		$prenom[$cpt_i] = $eleve_prenom;
		$naissance[$cpt_i] = $eleve_naissance;
		$classe[$cpt_i] = $eleve_classe_nom_complet;
		$doublant[$cpt_i] = $eleve_doublant;
		$regime[$cpt_i] = $eleve_regime;
		$classe_id_eleve[$cpt_i] = $eleve_classe;
		$ident_eleve_sel1=$login[$cpt_i];
		
        $cpt_i++;
	}
    
    return $cpt_i;
}
?>
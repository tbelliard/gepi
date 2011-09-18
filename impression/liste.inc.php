<?php
/*
*/

// Traitement des données pour une classe référencée par id_classe et id_periode
// retourne un tableau avec les données de la base
// modifie la variable nombre_eleve
function traite_donnees_classe($id_classe,$id_periode,&$nombre_eleves)
{
global $prefix_base ;

$cpt_i = 0;
for ($i=0; $i<sizeof($id_periode) ; $i++) {

    $prepa_requete = $prefix_base.'j_eleves_classes.id_classe = "'.$id_classe.'"';
    $requete='SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_regime WHERE '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id  AND '.$prefix_base.'periode='.$id_periode[$i]. ' AND ' .$prefix_base.'eleves.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.login = '.$prefix_base.'j_eleves_regime.login AND ('.$prepa_requete.') GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'j_eleves_classes.id_classe ASC, '.$prefix_base.'eleves.nom ASC, '.$prefix_base.'eleves.prenom ASC'; 		//on compte les élèves sélectionné
	//echo $requete;
	//echo sizeof($id_periode)." : $i : ==>$requete <br />";
	$call_eleve = mysql_query($requete);	
	$nb_eleves = @mysql_num_rows($call_eleve);
	
	//en réalité, le nombre de ligne
	$nombre_eleves = $nb_eleves*sizeof($id_periode); // parametre de la fonction

	/*
	echo "<table class='boireaus'>";
	echo "<tr>";
	echo "<td>Num</td>";
	echo "<th>Login</th>";
	echo "<th>Ereno</th>";
	echo "<th>Nom</th>";
	echo "<th>Prenom</th>";
	echo "<th>Naissance</th>";
	echo "<th>Nom_complet</th>";
	echo "<th>Doublant</th>";
	echo "<th>Regime</th>";
	echo "<th>id_classe</th>";
	echo "</tr>";
	*/
	
	while ($donner = @mysql_fetch_array( $call_eleve ))
	{
	    $donnees_eleves[$cpt_i]['login'] = $donner['login']; 
		$donnees_eleves[$cpt_i]['ereno'] = $donner['ereno']; 
		$donnees_eleves[$cpt_i]['nom'] = $donner['nom'];
		$donnees_eleves[$cpt_i]['prenom'] = $donner['prenom'];
		$donnees_eleves[$cpt_i]['naissance'] = $donner['naissance'];
		$donnees_eleves[$cpt_i]['nom_complet'] =  $donner['nom_complet'];
		$donnees_eleves[$cpt_i]['nom_court'] =  $donner['classe'];
		$donnees_eleves[$cpt_i]['doublant'] = $donner['doublant'];
		$donnees_eleves[$cpt_i]['regime'] = $donner['regime'];
		$donnees_eleves[$cpt_i]['id_classe'] = $donner['id']; // ID de la classe
		$donnees_eleves[$cpt_i]['id_periode'] = $id_periode[$i]; // ID de la période traitée
		
		 
		$ident_eleve_sel1=$donner['login'];
		/*
		echo "<tr>";
		echo "<td>$cpt_i</td>";
		foreach($donnees_eleves[$cpt_i] as $key => $value) {
			echo "<td>$value</td>";
		}
		echo "</tr>";
		*/
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
} //for
	//echo "</table>";
    return $donnees_eleves;
}


// Traitement des données pour un groupe référencé par id_groupe et id_periode
// retourne un tableau avec les données de la base
// modifie la variable nombre_eleve
//variable $tri  ==> 'classe' tri des groupe par classe puis nom er prénom / autrement par liste alpha
function traite_donnees_groupe($id_groupe,$id_periode,&$nombre_eleves,$tri)
{
global $prefix_base ;

    $current_group = get_group($id_groupe);
    $cpt_i=0;
  	foreach($current_group["eleves"][$id_periode]["users"] as $current_eleve) {
		$eleve_login = $current_eleve["login"];
		$eleve_nom = $current_eleve["nom"];
		$eleve_prenom = $current_eleve["prenom"];

		$sql="SELECT classe, nom_complet FROM classes WHERE id='".$current_eleve["classe"]."'";
	    //echo "$sql<br />";
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
		//$sql="SELECT id_classe,naissance,ereno,doublant,regime FROM eleves, j_eleves_classes, j_eleves_regime WHERE eleves.login='$eleve_login' AND j_eleves_classes.login='$eleve_login' AND j_eleves_regime.login='$eleve_login'";
		$sql="SELECT id_classe,naissance,ereno FROM eleves, j_eleves_classes WHERE eleves.login='$eleve_login' AND j_eleves_classes.login=eleves.login;";
	    //echo "$sql<br />";
		$res_tmp=mysql_query($sql);

		if(mysql_num_rows($res_tmp)==0){
			die("Problème avec les infos de $eleve_login</body></html>");
		}
		else{
			$lig_tmp=mysql_fetch_object($res_tmp);
			$eleve_naissance=$lig_tmp->naissance;
			$eleve_ereno=$lig_tmp->ereno;
			$eleve_id_classe=$lig_tmp->id_classe;	

			// A quoi servent les données ci-dessous? Je n'ai pas vu dans les pages appelant liste.inc.php
			$sql="SELECT doublant,regime FROM j_eleves_regime WHERE login='$eleve_login';";
			$res_regime=mysql_query($sql);
			if(mysql_num_rows($res_regime)==0) {
				$eleve_doublant='';
				$eleve_regime='';
			}
			else {
				$lig_tmp2=mysql_fetch_object($res_regime);
				$eleve_doublant=$lig_tmp2->doublant;
				$eleve_regime=$lig_tmp2->regime;
			}
		}
        //pour rendre compatible groupe et classe  par la suite
		$donnees_eleves[$cpt_i]['login'] = $eleve_login; 
		$donnees_eleves[$cpt_i]['ereno'] = $eleve_ereno;
		$donnees_eleves[$cpt_i]['nom'] = $eleve_nom;
		$donnees_eleves[$cpt_i]['prenom'] = $eleve_prenom;
		$donnees_eleves[$cpt_i]['naissance'] = $eleve_naissance;
		$donnees_eleves[$cpt_i]['nom_complet'] =  $eleve_classe_nom_complet;
		$donnees_eleves[$cpt_i]['nom_court'] =  $eleve_classe;
		$donnees_eleves[$cpt_i]['doublant'] = $eleve_doublant;
		$donnees_eleves[$cpt_i]['regime'] = $eleve_regime;
		$donnees_eleves[$cpt_i]['id_classe'] = $eleve_id_classe; 
		$donnees_eleves[$cpt_i]['id_periode'] = $id_periode; // ID de la période traitée
				
		$ident_eleve_sel1=$donnees_eleves[$cpt_i]['login'];
		
        $cpt_i++;
	}
    $nombre_eleves = $cpt_i; // parametre de la fonction

	/*
	echo "<pre>\$donnees_eleves\n";
	print_r($donnees_eleves);
	echo "</pre>";
	*/
	//echo "tri=$tri<br />";
    if ($tri=='classes') {
		$column=array();
		$column1=array();
		$column2=array();
		foreach($donnees_eleves as $sortarray)
		{
			//$column[] = $sortarray['id_classe'];
			//@array_multisort($column, SORT_ASC, $donnees_eleves);
			$column[] = $sortarray['nom_complet'];
			$column1[] = $sortarray['nom_court'];
			$column2[] = $sortarray['nom'];
		}
		@array_multisort($column, SORT_ASC, $column1, SORT_ASC, $column2, SORT_ASC, $donnees_eleves);
		/*
		echo "<pre>\$column\n";
		print_r($column);
		echo "</pre>";
		*/
	}
	/*
	echo "<pre>\$donnees_eleves\n";
	print_r($donnees_eleves);
	echo "</pre>";
	*/
	if(isset($donnees_eleves)) {
	    return $donnees_eleves;
	}
	else {
	    return array();
	}
}
?>
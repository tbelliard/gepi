<?php
/** Fonctions de manipulation des conteneurs
 * 
 * 
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
 * 
 * @package Notes
 * @subpackage Initialisation
 *
*/

/**
 * Détruit les conteneurs vides qui ne sont pas rattachés à un parent
 *
 * @param int $id_conteneur Id du conteneur
 * @param int $id_racine Id du conteneur racine
 * @todo à vérifier
 */
function test_conteneurs_vides($id_conteneur,$id_racine) {
        // On teste si le conteneur est vide
        if ($id_conteneur !=0) {
            $sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_devoirs WHERE id_conteneur='$id_conteneur'");
            $nb_dev = mysqli_num_rows($sql);
            $sql= mysqli_query($GLOBALS["mysqli"], "SELECT id FROM cn_conteneurs WHERE parent='$id_conteneur'");
            $nb_cont = mysqli_num_rows($sql);
            if (($nb_dev == 0) or ($nb_cont == 0)) {
                $query_parent = mysqli_query($GLOBALS["mysqli"], "SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
                $id_par = old_mysql_result($query_parent, 0, 'parent');
                $sql = mysqli_query($GLOBALS["mysqli"], "DELETE FROM cn_notes_conteneurs WHERE id_conteneur='$id_conteneur'");
                test_conteneurs_vides($id_par,$id_racine);
            }
        }
}

/**
 * Met à jour les moyennes des conteneurs
 *
 * @param array $_current_group les informations du groupes obtenues à partir de get_group()
 * @param int $periode_num le numéro de la période
 * @param int $id_racine Id du conteneur racine
 * @param int $id_conteneur Id du conteneur
 * @param string $arret si yes, on ne recalcule pas les sous-conteneurs
 * @see get_group()
 * @see calcule_moyenne()
 */
function mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_conteneur,&$arret) {
    //remarque : les variables $periode_num et id_racine auraient pus être récupérées
    //à partir de $id_conteneur, mais on évite ainsi trop de calculs !

	if(isset($_current_group["eleves"][$periode_num])) {
		foreach ($_current_group["eleves"][$periode_num]["list"] as $_eleve_login) {
			if($_eleve_login!=""){
				calcule_moyenne($_eleve_login, $id_racine, $id_conteneur);
			}
		}
	
		if ($arret != 'yes') {
			//
			// Détermination du conteneur parent
			$query_id_parent = mysqli_query($GLOBALS["mysqli"], "SELECT parent FROM cn_conteneurs WHERE id='$id_conteneur'");
			$id_parent = old_mysql_result($query_id_parent, 0, 'parent');
			if ($id_parent != 0) {
				$arret = 'no';
				mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_parent,$arret);
			} else {
				$arret = 'yes';
				mise_a_jour_moyennes_conteneurs($_current_group, $periode_num,$id_racine,$id_racine,$arret);
			}
	
		}
	}
}


/**
 * Remplit un fichier de suivi des actions
 * 
 * Passer la variable $local_debug à "y" pour activer le remplissage du fichier "/tmp/calcule_moyenne.txt" de debug
 * 
 * @param string $texte 
 */
function fdebug($texte){
	$local_debug="n";
	if($local_debug=="y") {
		$fich=fopen("/tmp/calcule_moyenne.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}


/**
 *Liste des sous-conteneurs d'un conteneur
 * 
 * Modifie les tableaux passer par référence
 * 
 * L'index de chaque tableau est identique  pour un sous-conteneur donné
 * 
 * Utilise fdebug() pour le suivi des actions
 * 
 * @param int $id_conteneur id du conteneur
 * @param int $nb_sous_cont nombre de sous-conteneurs (passé par référence)
 * @param array $nom_sous_cont nom des sous-conteneurs (passé par référence)
 * @param array $coef_sous_cont coef des sous-conteneurs (passé par référence)
 * @param array $id_sous_cont Id des sous-conteneurs
 * @param array $display_bulletin_sous_cont y si le sous conteneur doit être affiché (passé par référence)
 * @param text $type all pour rechercher aussi les sous-sous-conteneurs
 * @param array $ponderation_sous_cont ponderation des sous-conteneurs
 * @see fdebug()
 */
function sous_conteneurs($id_conteneur,&$nb_sous_cont,&$nom_sous_cont,&$coef_sous_cont,&$id_sous_cont,&$display_bulletin_sous_cont,$type,$ponderation_sous_cont) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE sous_conteneurs() SUR\n");
	fdebug("id_conteneur=$id_conteneur avec type=$type\n");

    $query_sous_cont = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE (parent ='$id_conteneur' and id!='$id_conteneur') order by nom_court");
    $nb = mysqli_num_rows($query_sous_cont);
    $i=0;
    while ($i < $nb) {
        $nom_sous_cont[$nb_sous_cont] = old_mysql_result($query_sous_cont, $i, 'nom_court');
        $coef_sous_cont[$nb_sous_cont] = old_mysql_result($query_sous_cont, $i, 'coef');
        $id_sous_cont[$nb_sous_cont] = old_mysql_result($query_sous_cont, $i, 'id');
        $display_bulletin_sous_cont[$nb_sous_cont] = old_mysql_result($query_sous_cont, $i, 'display_bulletin');
        $temp = $id_sous_cont[$nb_sous_cont];
        $nb_sous_cont++;
        if ($type=='all') {
            sous_conteneurs($temp,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all',$ponderation_sous_cont);
        }
        $i++;
    }
}


/**
 * Calcul la moyenne d'un conteneur
 * 
 * @param text $login login de l"élève
 * @param type $id_racine Id racine du conteneur
 * @param type $id_conteneur Id du conteneur
 * @see fdebug
 * @see sous_conteneurs()
 * @see getSettingValue()
 * @see number_format()
 */
function calcule_moyenne($login, $id_racine, $id_conteneur) {
	fdebug("===================================\n");
	fdebug("LANCEMENT DE calcule_moyenne SUR\n");
	fdebug("login=$login: id_racine=$id_racine - id_conteneur=$id_conteneur\n");

    $total_point = 0;
    $somme_coef = 0;
    $exist_dev_fac = '';
    // On efface les moyennes de la table
    $sql="DELETE FROM cn_notes_conteneurs WHERE (login='$login' and id_conteneur='$id_conteneur');";
	fdebug("$sql\n");
    $delete = mysqli_query($GLOBALS["mysqli"], $sql);

    // Appel des paramètres du conteneur
    $appel_conteneur = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_conteneurs WHERE id ='$id_conteneur'");
    $arrondir =  old_mysql_result($appel_conteneur, 0, 'arrondir');
    $mode =  old_mysql_result($appel_conteneur, 0, 'mode');
    $ponderation = old_mysql_result($appel_conteneur, 0, 'ponderation');

	fdebug("Conteneur n°$id_conteneur\n");
	fdebug("\$arrondir=$arrondir\n");
	fdebug("\$mode=$mode\n");
	fdebug("\$ponderation=$ponderation\n");

    // Détermination des sous-conteneurs à prendre en compte
    $nom_sous_cont = array();
    $id_sous_cont  = array();
    $coef_sous_cont = array();
    $ponderation_sous_cont=array();
    $nb_sous_cont = 0;
    if ($mode==1) {
        //la moyenne s'effectue sur toutes les notes contenues à la racine ou dans les sous-conteneurs
        // sans tenir compte des options définies dans cette(ces) boîte(s).

        // on s'intéresse à tous les conteneurs fils, petit-fils, ...
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'all',$ponderation_sous_cont);
        //
        // On fait la moyenne des devoirs du conteneur et des sous-conteneurs
        $nb_boucle = $nb_sous_cont+1;
        $id_cont[0] = $id_conteneur;
        $i=1;
        while ($i < $nb_boucle) {
            $id_cont[$i] = $id_sous_cont[$i-1];
            $i++;
        }

    } else {
        //la moyenne s'effectue sur toutes les notes contenues à la racine du conteneur
        //et sur les moyennes du ou des sous-conteneurs, en tenant compte des options dans ce(s) boîte(s).

        // On s'intéresse uniquement aux conteneurs fils
        sous_conteneurs($id_conteneur,$nb_sous_cont,$nom_sous_cont,$coef_sous_cont,$id_sous_cont,$display_bulletin_sous_cont,'',$ponderation_sous_cont);
        //
        // on ne fait la moyenne que des devoirs du conteneur
        $nb_boucle = 1;
        $id_cont[0] = $id_conteneur;

    }



    //
    // Prise en compte de la pondération
    // Calcul de l'indice du coefficient à pondérer
    //
    if ($ponderation != 0) {
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_conteneur' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysqli_query($GLOBALS["mysqli"], $sql);
        $nb_dev  = mysqli_num_rows($appel_dev);
		fdebug("\$nb_dev=$nb_dev\n");
        $max = 0;
        $indice_pond = 0;
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = old_mysql_result($appel_dev, $k, 'id');
            $coef[$k] = old_mysql_result($appel_dev, $k, 'coef');
			fdebug("\$id_dev=$id_dev : \$coef[$k]=$coef[$k]\n");
            $sql="SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')";
			fdebug("$sql\n");
            $note_query = mysqli_query($GLOBALS["mysqli"], $sql);
            $statut = @old_mysql_result($note_query, 0, "statut");
            $note = @old_mysql_result($note_query, 0, "note");
			fdebug("\$nb_dev=$nb_dev\n");
            if (($statut == '') and ($note!='')) {
                if (($note > $max) or (($note == $max) and ($coef[$k] > $coef[$indice_pond]))) {
                    $max = $note;
                    $indice_pond = $k;
                }
            }
            $k++;
        }
    }
	if(isset($indice_pond)) {
		fdebug("\$indice_pond=$indice_pond\n");
		fdebug("\$max=$max\n");
	}


    //
    // Calcul du total des points et de la somme des coefficients
    //
/**
 * @todo Pour $mode==1, pour les devoirs à Bonus, il faudrait faire la liste de tous les devoirs situés dans le conteneur et les sous-conteneurs triés par date et parcourir ici ces devoirs au lieu de faire une boucle sur la liste des sous-conteneurs (while ($j < $nb_boucle))
 */
    $j=0;
	//=========================
	// AJOUT: boireaus 20080202
	$m=0;
	//=========================
    while ($j < $nb_boucle) {
		//=========================
		// MODIF: boireaus 20080202
        $sql="SELECT * FROM cn_devoirs WHERE id_conteneur='$id_cont[$j]' ORDER BY date,id";
		fdebug("$sql\n");
        $appel_dev = mysqli_query($GLOBALS["mysqli"], $sql);
		//=========================
        $nb_dev  = mysqli_num_rows($appel_dev);
        $k = 0;
        while ($k < $nb_dev) {
            $id_dev = old_mysql_result($appel_dev, $k, 'id');
			fdebug("\n\$id_dev=$id_dev\n");

            $coef[$k] = old_mysql_result($appel_dev, $k, 'coef');
			fdebug("\$coef[$k]=$coef[$k]\n");

            $note_sur[$k] = old_mysql_result($appel_dev, $k, 'note_sur');
			fdebug("\$note_sur[$k]=$note_sur[$k]\n");

            $ramener_sur_referentiel[$k] = old_mysql_result($appel_dev, $k, 'ramener_sur_referentiel');
			fdebug("\$ramener_sur_referentiel[$k]=$ramener_sur_referentiel[$k]\n");

            // Prise en compte de la pondération
            if (($ponderation != 0) and ($j==0) and ($k==$indice_pond)) $coef[$k] = $coef[$k] + $ponderation;
			fdebug("\$ponderation=$ponderation\n");

            $facultatif[$k] = old_mysql_result($appel_dev, $k, 'facultatif');
			fdebug("\$facultatif[$k]=$facultatif[$k]\n");

            $note_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM cn_notes_devoirs WHERE (login='$login' AND id_devoir='$id_dev')");
            $statut = @old_mysql_result($note_query, 0, "statut");
			fdebug("\$statut=$statut\n");

            $note = @old_mysql_result($note_query, 0, "note");
			fdebug("\$note=$note\n");

            if (($statut == '') and ($note!='')) {
                if ($note_sur[$k] != getSettingValue("referentiel_note")) {
                    if ($ramener_sur_referentiel[$k] != 'V') {
                        //on ramene la note sur le referentiel mais on modifie le coefficient pour prendre en compte le référentiel
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                        $coef[$k] = $coef[$k] * $note_sur[$k] / getSettingValue("referentiel_note");
                    } else {
                        //on fait comme si c'était une note sur le referentiel avec une regle de trois ;)
                        $note = $note * getSettingValue("referentiel_note") / $note_sur[$k];
                    }
                }
                fdebug("Correction note autre que sur referentiel : \$note=$note\n");
                fdebug("Correction note autre que sur referentiel : \$coef[$k]=$coef[$k]\n");

                if ($facultatif[$k] == 'O') {
                    // le devoir n'est pas facultatif (Obligatoire) et entre systématiquement dans le calcul de la moyenne si le coef est différent de zéro
					fdebug("\$total_point = $total_point + $coef[$k] * $note = ");
                    $total_point = $total_point + $coef[$k]*$note;
					fdebug("$total_point\n");

					fdebug("\$somme_coef = $somme_coef + $coef[$k] = ");
                    $somme_coef = $somme_coef + $coef[$k];
					fdebug("$somme_coef\n");
                } else if ($facultatif[$k] == 'B') {
                    //le devoir est facultatif comme un bonus : seuls les points supérieurs à 10 sont pris en compte dans le calcul de la moyenne.
                    if ($note > ($note_sur[$k]/2)) {
                        $total_point = $total_point + $coef[$k]*$note;
                        $somme_coef = $somme_coef + $coef[$k];
                    }
                } else {
                    //$facultatif == 'N' le devoir est facultatif comme une note : Le devoir est pris en compte dans la moyenne uniquement s'il améliore la moyenne de l'élève.
                    $exist_dev_fac = 'yes';
					//=========================
					// MODIF: boireaus 20080202
					// On ne compte pas la note dans la moyenne pour le moment.
					// On regardera plus loin si cela améliore la moyenne ou non.
					$f_coef[$m]=$coef[$k];
					$points[$m] = $f_coef[$m]*$note;

					fdebug("\$points[$m]=$points[$m]\n");
					fdebug("\$f_coef[$m]=$f_coef[$m]\n");

					$m++;
					//=========================
                }
				fdebug("\$total_point=$total_point\n");
				fdebug("\$somme_coef=$somme_coef\n");
				
            }
            $k++;
        }
        $j++;
    }


    //
    // Prise en comptes des sous-conteneurs si mode=2
    //
    if ($mode == 2) {
		fdebug("\$mode=$mode\n");
        $j=0;
        while ($j < $nb_sous_cont) {
            $sql="SELECT coef FROM cn_conteneurs WHERE id='$id_sous_cont[$j]'";
			fdebug("$sql\n");
            $appel_cont = mysqli_query($GLOBALS["mysqli"], $sql);
            $coefficient = old_mysql_result($appel_cont, 0, 'coef');
			fdebug("\$coefficient=$coefficient\n");

            $sql="SELECT * FROM cn_notes_conteneurs WHERE (login='$login' AND id_conteneur='$id_sous_cont[$j]')";
			fdebug("$sql\n");
            $moyenne_query = mysqli_query($GLOBALS["mysqli"], $sql);
            $statut_moy = @old_mysql_result($moyenne_query, 0, "statut");
			fdebug("\$statut_moy=$statut_moy\n");

            if ($statut_moy == 'y') {
                $moy = @old_mysql_result($moyenne_query, 0, "note");
				fdebug("\$moy=$moy\n");

				fdebug("\$somme_coef = $somme_coef + $coefficient = ");
                $somme_coef = $somme_coef + $coefficient;
				fdebug("$somme_coef\n");

				fdebug("\$total_point = $total_point + $coefficient * $moy = ");
                $total_point = $total_point + $coefficient*$moy;
				fdebug("$total_point\n");
            }
            $j++;
        }
    }


    //
    // calcul de la moyenne des évaluations
    //
	//=========================
	// A FAIRE: boireaus 20080202
/**
 * @todo Il faudrait considérer le cas vicieux: présence de note à bonus et pas d'autre note...
 */

    if ($somme_coef != 0) {
	//=========================
		fdebug("\$moyenne= = $total_point / $somme_coef = ");
        $moyenne = $total_point/$somme_coef;
		fdebug($moyenne."\n");
        //
        // si un des devoirs a l'option "N", on prend la meilleure moyenne :
        //
		// Ca ne fonctionne bien que pour $mode==2
/**
 * @todo  Pour $mode==1, il faudrait faire la liste de tous les devoirs situés dans le conteneur et les sous-conteneurs triés par date et parcourir ces devoirs plus haut au lieu de faire une boucle sur la liste des sous-conteneurs
 */
        if ($exist_dev_fac == 'yes') {
			fdebug("\$exist_dev_fac=".$exist_dev_fac."\n");
			
			$m=0;
            while ($m<count($points)) {
				fdebug("count(\$points)=".count($points)."\n");
				if((isset($points[$m]))&&(isset($f_coef[$m]))) {
					fdebug("\$points[$m]=$points[$m] et \$f_coef[$m]=$f_coef[$m]\n");
					$tmp_moy=($total_point+$points[$m])/($somme_coef+$f_coef[$m]);
					fdebug("\$tmp_moy=$tmp_moy et \$moyenne=$moyenne\n");
					if($tmp_moy>$moyenne){
						$moyenne=$tmp_moy;
						$total_point=$total_point+$points[$m];
						$somme_coef=$somme_coef+$f_coef[$m];
					}
					fdebug("\$moyenne=$moyenne\n");
				}
				$m++;
			}
        }

		fdebug("Moyenne avant arrondi: $moyenne\n");

        //
        // Calcul des arrondis
        //
        if ($arrondir == 's1') {
            // s1 : arrondir au dixième de point supérieur
			fdebug("Mode s1:
   \$moyenne=$moyenne
   10*\$moyenne=".(10*$moyenne)."
   ceil(10*\$moyenne)=".ceil(10*$moyenne)."
   ceil(10*\$moyenne)/10=".(ceil(10*$moyenne)/10)."
   number_format(ceil(10*\$moyenne)/10,1,'.','')=".number_format(ceil(10*$moyenne)/10,1,'.','')."
   number_format(ceil(100*\$moyenne)/100,1,'.','')=".number_format(ceil(100*$moyenne)/100,1,'.','')."\n");
            //$moyenne = number_format(ceil(10*$moyenne)/10,1,'.','');
			$moyenne = number_format(ceil(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 's5') {
            // s5 : arrondir au demi-point supérieur
            $moyenne = number_format(ceil(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'se') {
            // se : arrondir au point entier supérieur
            $moyenne = number_format(ceil(strval($moyenne)),1,'.','');
        } else if ($arrondir == 'p1') {
            // s1 : arrondir au dixième le plus proche
            $moyenne = number_format(round(strval(10*$moyenne))/10,1,'.','');
        } else if ($arrondir == 'p5') {
            // s5 : arrondir au demi-point le plus proche
            $moyenne = number_format(round(strval(2*$moyenne))/2,1,'.','');
        } else if ($arrondir == 'pe') {
            // se : arrondir au point entier le plus proche
            $moyenne = number_format(round(strval($moyenne)),1,'.','');
        }

        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='$moyenne',statut='y',comment='';";
		fdebug("$sql\n");
        $register = mysqli_query($GLOBALS["mysqli"], $sql);

    } else {
        $sql="INSERT INTO cn_notes_conteneurs SET login='$login', id_conteneur='$id_conteneur',note='0',statut='',comment='';";
		fdebug("$sql\n");
        $register = mysqli_query($GLOBALS["mysqli"], $sql);

    }

}

/**
 * Vérifie qu'un carnet de notes appartient bien à enseignant
 *
 * @param text $_login Login de l'enseignant
 * @param int $_id_racine Id du carnet de notes
 * @return bool TRUE si l'enseignant peut accéder au carnet de notes
 */
function Verif_prof_cahier_notes ($_login,$_id_racine) {
    if(empty($_login) || empty($_id_racine)) {return FALSE;die();}
    $test_prof = mysqli_query($GLOBALS["mysqli"], "SELECT id_groupe FROM cn_cahier_notes WHERE id_cahier_notes ='" . $_id_racine . "'");
    $_id_groupe = old_mysql_result($test_prof, 0, 'id_groupe');

    $call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM j_groupes_professeurs WHERE (id_groupe='".$_id_groupe."' and login='" . $_login . "')");
    $nb = mysqli_num_rows($call_prof);

    if ($nb != 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Ajoute du code pour préparer un tableau pour calculer et afficher les statistiques sur les notes d'une classe 
 * 
 * Fonction à appeler avec une portion de code du type:
 * 
 * echo "<div style='position: fixed; top: 200px; right: 200px;'>\n";
 * 
 * javascript_tab_stat('tab_stat_',$cpt);
 * 
 * echo "</div>\n";
 * 
 * @param string $pref_id prefixe des Id des balises <td...>
 * @param int $cpt 
 */
function javascript_tab_stat($pref_id,$cpt) {
	echo "<table class='boireaus boireaus_alt'>\n";
	echo "<caption style='display:none;'>Statistiques</caption>";
	echo "<tr>\n";
	echo "<th>Moyenne</th>\n";
	echo "<td id='".$pref_id."moyenne'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>1er quartile</th>\n";
	echo "<td id='".$pref_id."q1'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Médiane</th>\n";
	echo "<td id='".$pref_id."mediane'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>3è quartile</th>\n";
	echo "<td id='".$pref_id."q3'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Min</th>\n";
	echo "<td id='".$pref_id."min'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Max</th>\n";
	echo "<td id='".$pref_id."max'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Nb.notes&ge;10</th>\n";
	echo "<td id='".$pref_id."nb_sup_egal_10'></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "<th>Nb.notes&lt;10</th>\n";
	echo "<td id='".$pref_id."nb_inf_10'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	echo "<script type='text/javascript' language='JavaScript'>

function calcul_moy_med() {
	var eff_utile=0;
	var total=0;
	var valeur;
	var tab_valeur=new Array();
	var i=0;
	var j=0;
	var n=0;
	var mediane;
	var moyenne;
	var q1;
	var q3;
	var rang=0;

	var nb_inf_10=0;
	var nb_sup_egal_10=0;

	for(i=0;i<$cpt;i++) {
		if(document.getElementById('n'+i)) {
			valeur=document.getElementById('n'+i).value;

			valeur=valeur.replace(',','.');

			if((valeur!='abs')&&(valeur!='disp')&&(valeur!='-')&&(valeur!='')) {
				if(valeur>=10) {
					nb_sup_egal_10++;
				}
				else {
					nb_inf_10++;
				}

				tab_valeur[j]=valeur;
				// Tambouille pour éviter que 'valeur' soit pris pour une chaine de caractères
				total=eval((total*100+valeur*100)/100);
				eff_utile++;
				j++;
			}
		}
	}
	if(eff_utile>0) {
		moyenne=Math.round(10*total/eff_utile)/10;
		document.getElementById('".$pref_id."moyenne').innerHTML=moyenne;

		tab_valeur.sort((function(a,b){return a - b}));
		n=tab_valeur.length;
		if(n/2==Math.round(n/2)) {
			// Les indices commencent à zéro
			// Tambouille pour éviter que 'valeur' soit pris pour une chaine de caractères
			mediane=((eval(100*tab_valeur[n/2-1]+100*tab_valeur[n/2]))/100)/2;
		}
		else {
			mediane=tab_valeur[(n-1)/2];
		}
		document.getElementById('".$pref_id."mediane').innerHTML=mediane;

		if(eff_utile>=4) {
			rang=Math.ceil(eff_utile/4);
			q1=tab_valeur[rang-1];

			rang=Math.ceil(3*eff_utile/4);
			q3=tab_valeur[rang-1];

			document.getElementById('".$pref_id."q1').innerHTML=q1;
			document.getElementById('".$pref_id."q3').innerHTML=q3;
		}
		else {
			document.getElementById('".$pref_id."q1').innerHTML='-';
			document.getElementById('".$pref_id."q3').innerHTML='-';
		}

		document.getElementById('".$pref_id."min').innerHTML=tab_valeur[0];
		document.getElementById('".$pref_id."max').innerHTML=tab_valeur[n-1];

		document.getElementById('".$pref_id."nb_sup_egal_10').innerHTML=nb_sup_egal_10;
		document.getElementById('".$pref_id."nb_inf_10').innerHTML=nb_inf_10;
	}
	else {
		document.getElementById('".$pref_id."moyenne').innerHTML='-';
		document.getElementById('".$pref_id."mediane').innerHTML='-';
		document.getElementById('".$pref_id."q1').innerHTML='-';
		document.getElementById('".$pref_id."q3').innerHTML='-';
		document.getElementById('".$pref_id."min').innerHTML='-';
		document.getElementById('".$pref_id."max').innerHTML='-';
		document.getElementById('".$pref_id."nb_sup_egal_10').innerHTML='-';
		document.getElementById('".$pref_id."nb_inf_10').innerHTML='-';
	}
}

calcul_moy_med();
</script>
";
}

/**
 * Calcule les statistiques d'un tableau de notes
 * 
 * - 'moyenne' -> moyenne des notes
 * - 'mediane' -> mediane des notes
 * - 'min'     -> note minimale
 * - 'max'     -> note maximale
 * - 'q1'      -> premier quartile
 * - 'q3'      -> troisième quartile
 *
 * @param array $tab Tableau de notes à traiter
 * @return array Tableau de statistiques
 */
function calcule_moy_mediane_quartiles($tab) {
	$tab2=array();

	/*
	echo "<p>";
	foreach($tab as $key => $value) {
		echo "\$tab[$key]=$value<br />";
	}
	*/

	$total=0;
	for($i=0;$i<count($tab);$i++) {
		if(isset($tab[$i])) {
			if(($tab[$i]!='')&&($tab[$i]!='-')&&($tab[$i]!='&nbsp;')&&($tab[$i]!='abs')&&($tab[$i]!='disp')) {
				$tab2[]=preg_replace('/,/','.',$tab[$i]);
				$total+=preg_replace('/,/','.',$tab[$i]);
			}
		}
		//else {
		//	echo "\$tab[$i] not set.<br />";
		//}
	}

	// Initialisation
	$tab_retour['moyenne']='-';
	$tab_retour['mediane']='-';
	$tab_retour['min']='-';
	$tab_retour['max']='-';
	$tab_retour['q1']='-';
	$tab_retour['q3']='-';
	$tab_retour['supegal10']="-";
	$tab_retour['inf10']="-";

	if(count($tab2)>0) {
		sort($tab2);

		$moyenne=round(10*$total/count($tab2))/10;

		if(count($tab2)%2==0) {
			$mediane=($tab2[count($tab2)/2-1]+$tab2[count($tab2)/2])/2;
		}
		else {
			$mediane=$tab2[(count($tab2)-1)/2];
		}

		$min=min($tab2);
		$max=max($tab2);

		for($i=0;$i<count($tab2);$i++) {
			if($tab2[$i]>=10) {
				if($tab_retour['supegal10']=="-") {
					$tab_retour['supegal10']=0;
				}
				$tab_retour['supegal10']++;
			}
			elseif($tab2[$i]<10) {
				if($tab_retour['inf10']=="-") {
					$tab_retour['inf10']=0;
				}
				$tab_retour['inf10']++;
			}
		}

		$q1="-";
		$q3="-";
		if(count($tab2)>=4) {
			$q1=$tab2[ceil(count($tab2)/4)-1];
			$q3=$tab2[ceil(3*count($tab2)/4)-1];
		}

		$tab_retour['moyenne']=$moyenne;
		$tab_retour['mediane']=$mediane;
		$tab_retour['min']=$min;
		$tab_retour['max']=$max;
		$tab_retour['q1']=$q1;
		$tab_retour['q3']=$q3;
	}

	return $tab_retour;
}

/**
 * Renvoie l'Id du carnet de notes d'un groupe pour une période
 *
 * @param int $id_groupe Id du groupe
 * @param int $periode_num numéro de la période
 * @return int Id du carnet de notes
 */
function get_cn_from_id_groupe_periode_num($id_groupe, $periode_num) {
	$id_cahier_notes="";

	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='$id_groupe' AND periode='$periode_num';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);
		$id_cahier_notes=$lig->id_cahier_notes;
	}
	return $id_cahier_notes;
}


// Fonction de recherche des conteneurs derniers enfants (sans enfants (non parents, en somme))
// avec recalcul des moyennes lancé...
/**
 *
 * @global int
 * @global int
 * @global int
 * @param int $id_parent_tmp 
 */
function recherche_enfant($id_parent_tmp){
	global $current_group, $periode_num, $id_racine;
	$sql="SELECT * FROM cn_conteneurs WHERE parent='$id_parent_tmp'";
	//echo "<!-- $sql -->\n";
	$res_enfant=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_enfant)>0){
		while($lig_conteneur_enfant=mysqli_fetch_object($res_enfant)){
			recherche_enfant($lig_conteneur_enfant->id);
		}
	}
	else{
		$arret = 'no';
		$id_conteneur_enfant=$id_parent_tmp;
		// Mise_a_jour_moyennes_conteneurs pour un enfant non parent...
		mise_a_jour_moyennes_conteneurs($current_group, $periode_num,$id_racine,$id_conteneur_enfant,$arret);
	}
}


/**
 * Enregistre les calculs de moyennes dans un fichier
 * 
 * Passer à 1 la variable $debug pour générer un fichier de debug...
 *
 * @param string $texte Le calcul à enregistrer
 * @see get_user_temp_directory()
 */
function calc_moy_debug($texte){
	$debug=0;
	if($debug==1){
		$tmp_dir=get_user_temp_directory();
		if((!$tmp_dir)||(!file_exists("../temp/".$tmp_dir))) {$tmp_dir="/tmp";} else {$tmp_dir="../temp/".$tmp_dir;}
		$fich=fopen($tmp_dir."/calc_moy_debug.txt","a+");
		fwrite($fich,$texte);
		fclose($fich);
	}
}

/**
 * Teste si un accès exceptionnel à la saisie de notes dans le CN est ouvert bien que la période soit close
 *
 * @param integer $id_groupe L'identifiant du groupe
 * @param integer $num_periode Le numéro de la période
 *
 * @return boolean true/false
 */
function acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $num_periode) {
	$sql="SELECT 1=1 FROM acces_cn WHERE id_groupe='$id_groupe' AND periode='$num_periode' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Enregistrer dans la table acces_cn les modifications effectuées lors d'un accès exceptionnel à la saisie de notes dans le CN alors que la période soit close
 *
 * @param integer $id_groupe L'identifiant du groupe
 * @param integer $num_periode Le numéro de la période
 * @param string $texte_ajoute Le texte à ajouter au log
 *
 * @return boolean true/false succès ou échec de l'enregistrement
 */
function log_modifs_acces_exceptionnel_saisie_cn_groupe_periode($id_groupe, $num_periode, $texte_ajoute) {
	$sql="SELECT * FROM acces_cn WHERE id_groupe='$id_groupe' AND periode='$num_periode';";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		// Il n'y a au plus qu'un enregistrement par (id_groupe;periode) dans acces_cn
		$lig=mysqli_fetch_object($res);
		$texte=$lig->commentaires."\n".$texte_ajoute;
		$sql="UPDATE acces_cn SET commentaires='".mysqli_real_escape_string($GLOBALS["mysqli"], $texte)."' WHERE id='$lig->id';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if($update) {
			return true;
		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}

/**
 * Récupérer l'identifiant du carner de notes associés à un groupe et une période
 * (et si nécessaire créer le CN)
 *
 * @param integer $id_groupe L'identifiant du groupe
 * @param integer $periode_num Le numéro de la période
 *
 * @return integer l'identifiant du CN
 *                 ou false en cas d'échec
 */
function creer_carnet_notes($id_groupe, $periode_num) {
	$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE (id_groupe='$id_groupe' and periode='$periode_num')";
	$appel_cahier_notes = mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_cahier_note = mysqli_num_rows($appel_cahier_notes);
	if ($nb_cahier_note == 0) {
		$current_group=get_group($id_groupe, array('matieres'));
		$nom_complet_matiere = $current_group["matiere"]["nom_complet"];
		$nom_court_matiere = $current_group["matiere"]["matiere"];

		// Création du conteneur
		$sql="INSERT INTO cn_conteneurs SET id_racine='',
				nom_court='".traitement_magic_quotes($current_group["description"])."',
				nom_complet='". traitement_magic_quotes($nom_complet_matiere)."',
				description = '',
				mode = '".getPref($_SESSION['login'],'cnBoitesModeMoy', (getSettingValue('cnBoitesModeMoy')!="" ? getSettingValue('cnBoitesModeMoy') : 2))."', 
				coef = '1.0',
				arrondir = 's1',
				ponderation = '0.0',
				display_parents = '0',
				display_bulletin = '1',
				parent = '0'";
		$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		if ($reg) {
			$id_racine = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

			// Mise à jour du conteneur
			$sql="UPDATE cn_conteneurs SET id_racine='$id_racine', parent = '0' WHERE id='$id_racine';";
			$reg = mysqli_query($GLOBALS["mysqli"], $sql);

			// Création du carnet de notes
			$sql="INSERT INTO cn_cahier_notes SET id_groupe = '$id_groupe', periode = '$periode_num', id_cahier_notes='$id_racine';";
			$reg = mysqli_query($GLOBALS["mysqli"], $sql);
		}
	} else {
		$id_racine = old_mysql_result($appel_cahier_notes, 0, 'id_cahier_notes');
	}

	if(isset($id_racine)) {
		return $id_racine;
	}
	else {
		return false;
	}
}

/**
 * Récupérer le nombre de notes de l'élève dans un groupe sur une période
 *
 * @param string $login_ele Le login de l'élève
 * @param integer $id_groupe L'identifiant du groupe
 * @param integer $periode Le numéro de la période
 *
 * @return integer nombre de notes
 */
function nb_notes_ele_dans_tel_enseignement($login_ele, $id_groupe, $periode) {
	$sql="SELECT DISTINCT id_devoir FROM cn_notes_devoirs cnd, cn_devoirs cd, cn_cahier_notes ccn WHERE (cnd.login = '".$login_ele."' AND cnd.statut='' AND cnd.id_devoir=cd.id AND cd.id_racine=ccn.id_cahier_notes AND ccn.id_groupe = '".$id_groupe."' AND ccn.periode = '".$periode."');";
	$test_cn=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_notes_cn=mysqli_num_rows($test_cn);
	return $nb_notes_cn;
}

/**
 * Tester si un professeur est proprio d'un devoir
 *
 * @param string $login Le login du professeur
 * @param integer $id_devoir L'identifiant du devoir
 *
 * @return boolean true ou false
 */
function test_prof_proprietaire_du_devoir($login, $id_devoir) {
	$sql="select login from j_groupes_professeurs jgp, cn_cahier_notes ccn, cn_devoirs cd where jgp.id_groupe=ccn.id_groupe AND ccn.id_cahier_notes=cd.id_racine and cd.id='$id_devoir' AND jgp.login='$login';";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * Retourne l'identifiant d'un cahier de notes associé à un groupe et une période
 *
 * @param integer $id_groupe identifant de groupe
 * @param integer $periode numero de periode (ou vide si toutes les périodes)
 *
 * @return integer ou array
 */
function get_id_cahier_notes($id_groupe,$periode) {
	if($periode!="") {
		$retour="";
		$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='".$id_groupe."' AND periode='".$periode."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$retour=$lig->id_cahier_notes;
		}
	}
	else {
		$retour=array();
		$sql="SELECT id_cahier_notes FROM cn_cahier_notes WHERE id_groupe='".$id_groupe."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$retour[$lig->periode]=$lig->id_cahier_notes;
			}
		}
	}
	return $retour;
}

/**
 * Retourne le tableau des notes de devoirs obtenues dans un groupe pour une période donnée
 *
 * @param integer $id_groupe identifant de groupe
 * @param integer $periode numero de periode
 *
 * @return array
 */
function get_tab_notes($id_groupe, $periode) {
	// En l'état ne pas laisser vide $periode
	$tab=array();

	$id_cahier_notes=get_id_cahier_notes($id_groupe,$periode);
	if($id_cahier_notes!="") {
		$sql="SELECT * FROM cn_conteneurs WHERE id_racine='".$id_cahier_notes."';";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_assoc($res)) {
				$tab['conteneur'][$lig['id']]=$lig;
			}
		}

		$sql="SELECT * FROM cn_devoirs WHERE id_racine='".$id_cahier_notes."' ORDER BY id_conteneur, date;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_assoc($res)) {
				$tab['devoir'][$lig['id']]=$lig;

				$sql="SELECT cnd.*,e.nom,e.prenom FROM cn_notes_devoirs cnd, eleves e WHERE id_devoir='".$lig['id']."' AND cnd.login=e.login ORDER BY e.nom,e.prenom;";
				//echo "$sql<br />";
				$res2=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res2)>0) {
					while($lig2=mysqli_fetch_assoc($res2)) {
						$tab['devoir'][$lig['id']]['note'][$lig2['login']]=$lig2;
					}
				}
			}
		}
	}
	return $tab;
}

/**
 * Retourne le tableau des notes de devoirs obtenues pour un élève donné, dans un groupe donné, pour une période donnée
 *
 * @param string $login_ele login de l'élève
 * @param integer $id_groupe identifant de groupe
 * @param integer $periode numero de periode
 *
 * @return array
 */
function get_tab_notes_ele($login_ele, $id_groupe, $periode) {
	// En l'état ne pas laisser vide $periode
	$tab=array();

	// On risque de donner accès à des notes non encore visibles
	$ajout_sql="";
	if(!in_array($_SESSION['statut'], array("scolarite", "professeur"))) {
		$ajout_sql=" AND cd.date_ele_resp<='".strftime("%Y-%m-%d %H:%M:%S")."'";
	}

	$sql="SELECT cnd.*,cd.*,
				cc.nom_court AS cc_nom_court,
				cc.nom_complet AS cc_nom_complet,
				cc.description AS cc_description,
				cc.mode AS cc_mode, 
				cc.coef AS cc_coef, 
				cc.arrondir AS cc_arrondir, 
				cc.ponderation AS cc_ponderation, 
				cc.display_parents AS cc_display_parents, 
				cc.display_bulletin AS display_bulletin, 
				cc.parent AS cc_parent 
			FROM cn_notes_devoirs cnd, 
				cn_devoirs cd, 
				cn_conteneurs cc, 
				cn_cahier_notes ccn 
			WHERE cnd.login='".$login_ele."' AND 
				cnd.id_devoir=cd.id AND 
				cc.id=cd.id_conteneur AND 
				cd.id_racine= ccn.id_cahier_notes AND 
				ccn.id_groupe='".$id_groupe."' AND 
				ccn.periode='".$periode."'".$ajout_sql."
			ORDER BY cd.id_conteneur, cd.date;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_assoc($res)) {
			$tab[$lig['id_devoir']]=$lig;
		}
	}

	return $tab;
}
?>

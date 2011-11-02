<?php
/*
 * @version: $Id: tab_profs_matieres.php 6576 2011-03-02 16:18:56Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// On indique qu'il faut crée des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


// INSERT INTO `droits` ( `id` , `administrateur` , `professeur` , `cpe` , `scolarite` , `eleve` , `secours` , `description` , `statut` ) VALUES ('/utilisateurs/tab_profs_matieres.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Affectation des matieres aux professeurs', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

// Initialisation du message signalant les enregistrements ou les problèmes.
$msg="";

// Initialisation des variables
//$user_login = isset($_POST["user_login"]) ? $_POST["user_login"] : (isset($_GET["user_login"]) ? $_GET["user_login"] : NULL);

if(isset($_POST['user_login'])){
	check_token();

	$user_login=$_POST['user_login'];
	$tab_matiere=$_POST['tab_matiere'];

	//echo "<!--\n";

	for($i=0;$i<count($user_login);$i++){
		//$check_matiere=$_POST['c_'.$i.'_'];
		$check_matiere=isset($_POST['c_'.$i.'_']) ? $_POST['c_'.$i.'_'] : NULL;

		//echo "$user_login[$i]\n";
		for($j=0;$j<count($tab_matiere);$j++){
			if(isset($check_matiere[$j]) and $check_matiere[$j] === "oui"){
				//echo "$tab_matiere[$j]\n";
				$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='$user_login[$i]' AND id_matiere='$tab_matiere[$j]'";
				$result_test=mysql_query($sql);
				if(mysql_num_rows($result_test)==0){
					$sql="INSERT INTO j_professeurs_matieres VALUES('$user_login[$i]','$tab_matiere[$j]','1')";
					$result_insert=mysql_query($sql);
				}
			}
			else{
				$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='$user_login[$i]' AND id_matiere='$tab_matiere[$j]'";
				$result_test=mysql_query($sql);
				if(mysql_num_rows($result_test)!=0){
					// On a décoché la matière pour ce professeur!

					// On vérifie que le professeur n'est pas associé à un groupe pour cette matière...
					$sql="SELECT jgm.id_groupe FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE jgp.login='$user_login[$i]' AND jgm.id_matiere='$tab_matiere[$j]' AND jgm.id_groupe=jgp.id_groupe";
					//echo "$sql\n";
					$result_test2=mysql_query($sql);
					if(mysql_num_rows($result_test2)==0){
						// ... puis on supprime l'entrée de la table 'j_professeurs_matieres'
						$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='$user_login[$i]' AND id_matiere='$tab_matiere[$j]'";
						$result_suppr=mysql_query($sql);
					}
					else{
						$lign_groupe=mysql_fetch_object($result_test2);
						$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='$lign_groupe->id_groupe'";
						$result_classe=mysql_query($sql);
						$lign_classe=mysql_fetch_object($result_classe);
						$msg.="L'utilisateur $user_login[$i] est associé à un <a href='../groupes/edit_class.php?id_classe=$lign_classe->id_classe'>groupe</a> pour $tab_matiere[$j]. La matière n'a pas pu être supprimée.<br />\n";
					}
				}
			}
		}
		//echo "================================\n";
	}

	//echo "-->\n";

	//c_".$i."_[".$j."]

	if($msg==""){
		$msg="Enregistrement réussi.";
	}
}



//**************** EN-TETE *****************
$titre_page = "Gestion des utilisateurs | Affectation des matières aux professeurs";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
/*
if($msg!=""){
	echo "<p align='center'><font color='red'>$msg</font></p>\n";
}
*/
?>
<p class=bold>
<a href="index.php?mode=personnels"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href='javascript:centrerpopup("help.php",600,480,"scrollbars=yes,statusbar=no,resizable=yes")'>Aide</a>
</p>
<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">

<!--span class = "norme"-->
<div class = "norme">

<?php
	echo add_token_field();

	// Fonction destinée à afficher verticalement, lettre par lettre, une chaine:
	function aff_vertical($texte){
		$chaine="";
		for($i=0;$i<strlen($texte);$i++){
			//echo substr($texte,$i,1)."<br />";
			$chaine=$chaine.substr($texte,$i,1)."<br />";
		}
		//echo "\n";
		$chaine=$chaine."\n";
		return $chaine;
	}

	// Tableau de la liste des matières:
	$tab_matiere=array();
	$sql="SELECT matiere FROM matieres ORDER BY matiere";
	$result_matieres=mysql_query($sql);
	while($ligne=mysql_fetch_object($result_matieres)){
		$tab_matiere[]=$ligne->matiere;
	}

	$order_by="nom, prenom";
	$calldata = mysql_query("SELECT * FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY $order_by");
	$nombreligne = mysql_num_rows($calldata);

	echo "<script type='text/javascript' language='javascript'>
	function colore(idcellule,idcheckbox){
		if(document.getElementById(idcheckbox).checked){
			document.getElementById(idcellule).style.background='green';
		}
		else{
			document.getElementById(idcellule).style.background='grey';
		}
	}

	function survol_colore(ligne){
		for(i=0;i<".count($tab_matiere).";i++){
			idcellule='td_'+ligne+'_'+i;
			eval('document.getElementById(\''+idcellule+'\').style.background=\'lightblue\'');
		}
	}

	function survol_colore_matiere(colonne) {
		for(i=0;i<".$nombreligne.";i=i+10){
			idcellule='col_tit_'+i+'_'+colonne;
			eval('document.getElementById(\''+idcellule+'\').style.background=\'lightblue\'');
		}
	}

	function retablit_couleurs(ligne){
		for(i=0;i<".count($tab_matiere).";i++){
			idcellule='td_'+ligne+'_'+i;
			idcheckbox='c_'+ligne+'_'+i;
			if(document.getElementById(idcheckbox).checked){
				//eval('document.getElementById(\''+idcellule+'\').style.background=\'lightblue\'');
				document.getElementById(idcellule).style.background='green';
			}
			else{
				if(i%2==0){
					document.getElementById(idcellule).style.background='silver';
				}
				else{
					document.getElementById(idcellule).style.background='white';
				}
			}

			for(j=0;j<".$nombreligne.";j=j+10){
				idcellule='col_tit_'+j+'_'+i;

				if(i%2==0){
					document.getElementById(idcellule).style.background='silver';
				}
				else{
					document.getElementById(idcellule).style.background='white';
				}
			}
		}

	}


	function survol_infobulle(texte) {
		/*
		for(i=0;i<".$nombreligne.";i=i+10){
			idcellule='col_tit_'+i+'_'+colonne;
			eval('document.getElementById(\''+idcellule+'\').style.background=\'lightblue\'');
		}
		*/

		if(document.getElementById('div_infobulle')) {
			document.getElementById('div_infobulle').innerHTML=texte;
			afficher_div('div_infobulle','y',20,20);
		}
	}




	function masquage(colonne){
		if(document.getElementById('c_col_'+colonne).checked){
			document.getElementById('td_col_'+colonne).style.background='red';
			for(j=0;j<colonne;j++){
				document.getElementById('c_col_'+j).checked=false;
				document.getElementById('d_col_'+j).style.display='none';
				for(i=0;i<$nombreligne;i++){
					if(i%10==0){
						document.getElementById('d_titre_'+i+'_'+j).style.display='none';
					}
					document.getElementById('d_'+i+'_'+j).style.display='none';
				}
			}
		}
		else{
			document.getElementById('td_col_'+colonne).style.background='white';
			for(j=0;j<colonne;j++){
				document.getElementById('c_col_'+j).checked=false;
				document.getElementById('d_col_'+j).style.display='block';
				for(i=0;i<$nombreligne;i++){
					if(i%10==0){
						document.getElementById('d_titre_'+i+'_'+j).style.display='block';
					}
					document.getElementById('d_'+i+'_'+j).style.display='block';
				}
			}
		}
	}
</script>\n";

/*
	// Section remontée: on a besoin de $nombreligne dans un fonction JavaScript.
	$order_by="nom, prenom";
	$calldata = mysql_query("SELECT * FROM utilisateurs WHERE statut='professeur' AND etat='actif' ORDER BY $order_by");
	$nombreligne = mysql_num_rows($calldata);
*/

	$cell_style[0]="background: silver";
	$cell_style[1]="background: white";

	for($i=0;$i<count($tab_matiere);$i++){
		echo "<input type='hidden' name='tab_matiere[$i]' value='$tab_matiere[$i]' />\n";
	}

	echo "<table class='boireaus' border='1' summary='Tableau des professeurs et matières'>\n";
	echo "<tr style='text-align:center; background: white;'>\n";
	echo "<td>Masquage</td>\n";
	for($i=0;$i<count($tab_matiere);$i++){
		echo "<td id='td_col_".$i."'><div id='d_col_".$i."'><input type='checkbox' name='c_col_".$i."' id='c_col_".$i."' value='coche' onchange='masquage($i)' /></div></td>\n";
	}
	echo "</tr>\n";

	$cpt=0;
	$alt=1;
	while ($cpt < $nombreligne){

		if($cpt/10-round($cpt/10)==0){
			echo "<tr valign='top'>\n";
			echo "<th>Professeur</th>\n";
			for($i=0;$i<count($tab_matiere);$i++){
				echo "<th style='".$cell_style[$i%2]."' id='col_tit_".$cpt."_".$i."'><div id='d_titre_".$cpt."_".$i."'>".aff_vertical($tab_matiere[$i])."</div></th>\n";
			}
			echo "</tr>\n";
		}

		$user_nom = mysql_result($calldata, $cpt, "nom");
		$user_prenom = mysql_result($calldata, $cpt, "prenom");
		//$user_statut = mysql_result($calldata, $cpt, "statut");
		$user_login = mysql_result($calldata, $cpt, "login");
		//$user_etat[$cpt] = mysql_result($calldata, $cpt, "etat");

		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='user_login[]' value=\"$user_login\" />\n";
		echo "$user_nom $user_prenom";
		echo "</td>\n";

		for($j=0;$j<count($tab_matiere);$j++){
			$sql="SELECT * FROM j_professeurs_matieres WHERE id_professeur='$user_login' AND id_matiere='".$tab_matiere[$j]."'";
			$result_matiere_prof=mysql_query($sql);
			if(mysql_num_rows($result_matiere_prof)!=0){
				$checked_ou_pas=" checked";
				$couleur=" background: lime;";
			}
			else{
				$checked_ou_pas="";
				//$couleur="";
				$couleur=$cell_style[$j%2];
			}

			//echo "<td id='td_".$cpt."_".$j."' style='text-align:center;$couleur' onMouseOver='survol_colore($cpt);' onMouseOut='retablit_couleurs($cpt);'>\n";
			echo "<td id='td_".$cpt."_".$j."' style='text-align:center;$couleur' onMouseOver='survol_colore($cpt); survol_colore_matiere($j);survol_infobulle(\"<p align=center>".preg_replace("/'/"," ",$user_nom)." ".substr($user_prenom,0,1).".<br />".$tab_matiere[$j]."</p>\")' onMouseOut='retablit_couleurs($cpt);cacher_div(\"div_infobulle\")'>\n";
			echo "<div id='d_".$cpt."_".$j."'>\n";
			echo "<input type='checkbox' id='c_".$cpt."_".$j."' name='c_".$cpt."_[".$j."]' value='oui' onchange='colore(\"td_".$cpt."_".$j."\",\"c_".$cpt."_".$j."\")' $checked_ou_pas />\n";
			echo "</div>\n";
			echo "</td>\n";
		}

		echo "</tr>\n";
		$cpt++;
	}
	echo "</table>\n";
?>
<input type='hidden' name='valid' value="yes" />
<center><input type='submit' value='Enregistrer' /></center>
<div id='div_infobulle' style='width:15em; color: #000000; border: 1px solid #000000; background-color:white; padding: 0px; position: absolute;'></div>
</div>
</form>
<?php require("../lib/footer.inc.php");?>
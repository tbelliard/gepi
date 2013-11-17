<?php
/*
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
}


// 1.4.4: INSERT INTO droits VALUES ('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'V', 'Recopie des moyennes', '');
// 1.5.x: INSERT INTO droits VALUES ('/saisie/recopie_moyennes.php', 'F', 'F', 'F', 'F', 'F', 'F', 'V', 'Recopie des moyennes', '');

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//On vérifie si le module est activé
if (getSettingValue("active_carnets_notes")!='y') {
	die("Le module n'est pas activé.");
}


if(isset($_POST['recopier'])) {
	check_token();

	$num_periode=$_POST['num_periode'];
	$id_classe=$_POST['id_classe'];

	// Vérification:
	$sql="SELECT 1=1 FROM periodes WHERE num_periode='$num_periode' AND id_classe='$id_classe' AND verouiller='O';";
	$test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($test)>0) {
		$msg="La période n°$num_periode est close.";
	}
	else {
		$login=$_POST['login'];
		$id_groupe=$_POST['id_groupe'];
		$moy=$_POST['moy'];
		//$coche=$_POST['coche'];
		$coche=isset($_POST['coche']) ? $_POST['coche'] : NULL;
		$cpt=$_POST['cpt'];

		//echo "<p>Classe $id_classe sur période $num_periode<br />\n";
		$nberr=0;
		$nbsucces=0;
		$msg="";
		for($i=0;$i<$cpt;$i++){
			if((isset($coche[$i]))&&($moy[$i]!='-')){
				//echo "$login[$i] $id_groupe[$i] $moy[$i] <br />\n";
				$sql="SELECT * FROM matieres_notes WHERE
					login='$login[$i]' AND
					id_groupe='$id_groupe[$i]' AND
					periode='$num_periode'";
				//echo "$sql<br />\n";
				$res_test=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_test)==0){
					$sql="INSERT INTO matieres_notes SET
						login='$login[$i]',
						id_groupe='$id_groupe[$i]',
						periode='$num_periode',
						note=$moy[$i]";
					//echo "$sql<br />\n";
					$res_insert=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$res_insert){
						$nberr++;
					}
					else{
						$nbsucces++;
					}
				}
				else{
					$sql="UPDATE matieres_notes SET
						note=$moy[$i] WHERE
						login='$login[$i]' AND
						id_groupe='$id_groupe[$i]' AND
						periode='$num_periode'";
					//echo "$sql<br />\n";
					$res_update=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
					if(!$res_update){
						$nberr++;
					}
					else{
						$nbsucces++;
					}
				}
			}
		}
		unset($num_periode);
		unset($id_classe);
		unset($login);
		unset($moy);
		unset($coche);
		unset($cpt);
		//echo "</p>\n";

		if($nberr>0){
			$msg="$nberr erreur(s) a(ont) eu lieu.<br />";
		}
		elseif($nbsucces>0){
			$msg="$nbsucces enregistrement(s) effectué(s).<br />";
		}
	}
}


//**************** EN-TETE *****************
$titre_page = "Carnet de notes - Recopie des moyennes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

/*
echo "<div class='norme'><p class=bold><a href='../accueil.php'>Accueil</a>";

$retour=isset($_GET['retour']) ? $_GET['retour'] : NULL;
if(isset($retour)){
	if($retour=="saisie_index"){
		$_SESSION['retour']="index.php";
	}
}

if(isset($_SESSION['retour'])){
	echo " | <a href='".$_SESSION['retour']."'>Retour</a>";
}
*/

//$choix_classe=isset($_POST['choix_classe']) ? $_POST['choix_classe'] : (isset($_GET['choix_classe']) ? $_GET['choix_classe'] : NULL);
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

//if(!isset($_POST['choix_classe'])){
//if(!isset($choix_classe)){
if(!isset($id_classe)){

	echo "<div class='norme'><p class=bold><a href='../accueil.php'>Accueil</a>";

	$retour=isset($_GET['retour']) ? $_GET['retour'] : NULL;
	if(isset($retour)){
		if($retour=="saisie_index"){
			$_SESSION['retour']="index.php";
		}
	}

	if(isset($_SESSION['retour'])){
		echo " | <a href='".$_SESSION['retour']."'>Retour</a>";
	}

	echo "</p></div>\n";

	//echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
	echo "<p>Cette page est destinée à effectuer la recopie des moyennes des carnets de notes vers les bulletins.<br />Ne seront recopiées que les moyennes autres que 'abs', 'disp' et '-'.</p>\n";
	$sql="SELECT DISTINCT id,classe FROM classes ORDER BY classe";
	$res_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

	if(mysqli_num_rows($res_classe)==0){
		echo "<p>Il semble qu'aucune classe ne soit définie.</p>\n";
		echo "<p><br /></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		/*
		echo "<p>Classe: <select name='id_classe'>\n";
		while($lig_classe=mysql_fetch_object($res_classe)){
			echo "<option value='$lig_classe->id'>$lig_classe->classe</option>\n";
		}
		echo "</select>\n";
		echo "<input type=\"submit\" name='choix_classe' value=\"Poursuivre\" /></p>\n";
		*/

		echo "<p><br /></p>\n";
		echo "<p class='bold'>Choisissez la classe:</p>\n";

		$nombreligne=mysqli_num_rows($res_classe);
		$i = 0;
		unset($tab_lien);
		unset($tab_txt);
		while ($i < $nombreligne){
			$tab_lien[$i] = $_SERVER['PHP_SELF']."?id_classe=".mysql_result($res_classe, $i, "id");
			$tab_txt[$i] = mysql_result($res_classe, $i, "classe");
			$i++;
		}
		tab_liste($tab_txt,$tab_lien,3);


	}
	//echo "</form>\n";
}
else{

	//$choix_periode=isset($_POST['choix_periode']) ? $_POST['choix_periode'] : (isset($_GET['choix_periode']) ? $_GET['choix_periode'] : NULL);
	//$num_periode=isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL;
	$num_periode=isset($_POST['num_periode']) ? $_POST['num_periode'] : (isset($_GET['num_periode']) ? $_GET['num_periode'] : NULL);

	echo "<div class='norme'>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

	echo "<p class='bold'><a href='../accueil.php'>Accueil</a>";

	$retour=isset($_GET['retour']) ? $_GET['retour'] : NULL;
	if(isset($retour)){
		if($retour=="saisie_index"){
			$_SESSION['retour']="index.php";
		}
	}

	if(isset($_SESSION['retour'])){
		echo " | <a href='".$_SESSION['retour']."'>Retour</a>";
	}

	//echo " | <a href='".$_SERVER['PHP_SELF']."'>Choisir une autre classe</a>\n";

	$sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";

	$chaine_options_classes="";

	$res_class_tmp=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
			}
			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}
		}
	}
	// =================================

	if($id_class_prec!=0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
		if(isset($num_periode)){
			echo "&amp;num_periode=$num_periode";
		}
		echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe précédente</a>";
	}
	if($chaine_options_classes!="") {
		echo " | <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if($id_class_suiv!=0) {
		echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
		if(isset($num_periode)){
			echo "&amp;num_periode=$num_periode";
		}
		echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";
	}

	echo "</p>\n";
	echo "</form>\n";
	echo "</div>\n";


	//if(!isset($_POST['choix_periode'])){
	//if(!isset($choix_periode)){
	if(!isset($num_periode)){
		//echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";
		echo "<p>Cette page est destinée à effectuer la recopie des moyennes des carnets de notes vers les bulletins.</p>\n";

		//$sql="SELECT classe FROM classes WHERE id='".$_POST['id_classe']."'";
		$sql="SELECT classe FROM classes WHERE id='".$id_classe."'";
		$res_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if(mysqli_num_rows($res_classe)==0){
			echo "<p>Il semble que la classe choisie n'existe pas.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else{

			$tmp_classe=mysqli_fetch_array($res_classe);
			echo "<table summary='Choix de la période'><tr><td valign='top'><p class='bold'>Choisissez une période pour la classe de $tmp_classe[0]: </p></td><td><p>\n";

			//echo "<input type='hidden' name='id_classe' value='".$_POST['id_classe']."' />\n";
			//echo "<input type='hidden' name='id_classe' value='".$id_classe."' />\n";
			//echo "<input type='hidden' name='choix_classe' value='oui' />\n";

			//$sql="SELECT * FROM periodes WHERE id_classe='".$_POST['id_classe']."' ORDER BY num_periode";
			$sql="SELECT * FROM periodes WHERE id_classe='".$id_classe."' ORDER BY num_periode";
			$res_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

			/*
			echo "<select name='num_periode'>\n";
			while($lig_per=mysql_fetch_object($res_per)){
				echo "<option value='$lig_per->num_periode'>$lig_per->nom_periode</option>\n";
			}
			echo "</select>\n";
			*/
			while($lig_per=mysqli_fetch_object($res_per)){
				//echo "<label for=''><input type='radio' name='num_periode' value='$lig_per->num_periode' /> $lig_per->nom_periode</label>\n";
				//if($lig_per->verouiller=='N'){
				if($lig_per->verouiller!='O'){
					echo "<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;num_periode=$lig_per->num_periode'>$lig_per->nom_periode</a><br />\n";
				}
				else{
					echo "$lig_per->nom_periode (<i>période close</i>)<br />\n";
				}
			}
			echo "</p></td></tr></table>\n";

			//echo "<input type=\"submit\" name='choix_periode' value=\"Poursuivre\" /></p>\n";
		}
		//echo "</form>\n";
	}
	else{
		//$num_periode=$_POST['num_periode'];
		//$num_periode=$_GET['num_periode'];
		//$id_classe=$_POST['id_classe'];

		$sql="SELECT classe FROM classes WHERE id='$id_classe'";
		$res_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_classe)==0){
			echo "<p>Il semble que la classe choisie n'existe pas.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else{
			$tmp_classe=mysqli_fetch_array($res_classe);
		}

		$sql="SELECT * FROM periodes WHERE id_classe='$id_classe' AND num_periode='$num_periode'";
		$res_per=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($res_per)==0){
			echo "<p>Il semble que la période choisie n'existe pas.</p>\n";
			echo "<p><br /></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
		else{
			$tmp_per=mysqli_fetch_array($res_per);
		}

		$sql="SELECT count(login) nb FROM j_eleves_classes jec WHERE
			jec.periode='$num_periode' AND
			jec.id_classe='$id_classe'";
		$res_eff_classe=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		$lig_nb=mysqli_fetch_object($res_eff_classe);
		$effectif_classe=$lig_nb->nb;

		//$sql="SELECT ccn.*,c.classe,g.description FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
		$sql="SELECT DISTINCT ccn.id_cahier_notes,ccn.id_groupe FROM cn_cahier_notes ccn,groupes g,j_groupes_classes jgc,classes c WHERE
			ccn.id_groupe=g.id AND
			jgc.id_groupe=g.id AND
			c.id=jgc.id_classe AND
			ccn.periode='$num_periode' AND
			c.id='$id_classe'
			ORDER BY g.description";
		//echo "$sql<br />\n";
		$resultat=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(mysqli_num_rows($resultat)==0){
			echo "<p>Il semble qu'aucun carnet de notes ne soit encore défini.</p>\n";
			die("</body></html>");
		}
		else{
			echo "<p>Recopie des moyennes des carnets de notes vers les bulletins.</p>\n";
			$nb_carnets_notes=mysqli_num_rows($resultat);
			if($nb_carnets_notes==1){
				echo "<p>".$nb_carnets_notes." carnet de note seulement est actuellement renseigné.<br />C'est le seul proposé dans la recopie ci-dessous.</p>\n";
			}
			else{
				echo "<p>".$nb_carnets_notes." carnets de notes sont actuellement renseignés.<br />Ce sont les seuls proposés dans la recopie ci-dessous.</p>\n";
			}

			echo "<form enctype=\"multipart/form-data\" name= \"formulaire\" action=\"".$_SERVER['PHP_SELF']."\" method='post'>\n";

			echo add_token_field();

			//echo "<p align='center'><input type=\"submit\" name='recopier' value=\"Recopier\" /></p>\n";
			echo "<p align='center'><input type=\"submit\" name='recopier' value=\"Valider la recopie\" /></p>\n";

			echo "<input type='hidden' name='id_classe' value='$id_classe' />\n";
			echo "<input type='hidden' name='num_periode' value='$num_periode' />\n";
			echo "<input type='hidden' name='choix_classe' value='oui' />\n";
			echo "<input type='hidden' name='choix_periode' value='oui' />\n";

			echo "<p>Recopie des moyennes pour la classe <b>$tmp_classe[0]</b> de sur la période <b>$tmp_per[0]</b>:</p>\n";

			//echo "<table border='1'>\n";
			echo "<table class='boireaus' width='100%' summary='Comparaisons'>\n";
			echo "<tr>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Classe</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Groupe</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Elève</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Moyenne<br />du carnet<br />de notes</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Moyenne<br />sur le<br />bulletin</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>Recopier</th>\n";
			echo "<th style='text-align:center; font-weight:bold;'>\n";
			echo "<a href='javascript:modif_toutes_cases(true)'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/\n";
			echo "<a href='javascript:modif_toutes_cases(false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>\n";
			echo "";
			echo "</th>\n";
			echo "</tr>\n";
			// Compteur des groupes
			$i=0;
			// Compteur de chaque ligne élève du tableau
			$cpt=0;
			$alt=1;
			while($ligne=mysqli_fetch_object($resultat)){
				$temoin_grp=0;
				$id_groupe=$ligne->id_groupe;
				$id_cahier_notes=$ligne->id_cahier_notes;
				$id_racine=$id_cahier_notes;

				//$sql="";
				//$res_classes=mysql_query();
				$current_group=get_group($id_groupe);

				$alt=$alt*(-1);
				echo "<tr class='lig$alt'>\n";
				//echo "<td>".$current_group["classlist_string"]."</td>\n";
				echo "<td>".$tmp_classe[0]."</td>\n";
				echo "<td>".htmlspecialchars($current_group['description'])."</td>\n";

				/*
				$sql="SELECT login FROM j_groupes_classes jgc,j_eleves_classes jec WHERE
					jgc.id_groupe='$id_groupe' AND
					jgc.id_classe='$id_classe' AND
					jec.periode='$num_periode' AND
					jgc.id_classe=jec.id_classe
					ORDER BY login";
				*/
				$sql="SELECT e.login,e.nom,e.prenom FROM j_groupes_classes jgc,j_eleves_classes jec, eleves e WHERE
					e.login=jec.login AND
					jgc.id_groupe='$id_groupe' AND
					jgc.id_classe='$id_classe' AND
					jec.periode='$num_periode' AND
					jgc.id_classe=jec.id_classe
					ORDER BY e.nom,e.prenom";
				//echo "<!-- $sql -->\n";
				$res_ele=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
				if(mysqli_num_rows($res_ele)==0){
					echo "<td colspan='3'>Aucun élève dans cette classe et ce groupe!</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					//echo "<p>Aucun élève dans cette classe et ce groupe!</p>\n";
					//die("</body></html>");
				}
				else{
					$j=0;
					while($lig_ele=mysqli_fetch_object($res_ele)){
						if($temoin_grp>0){
							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td>&nbsp;</td>\n";
							echo "<td>&nbsp;</td>\n";
						}
						//echo "<td>".$lig_ele->login."</td>\n";
						echo "<td>".my_strtoupper($lig_ele->nom)." ".casse_mot($lig_ele->prenom,'majf2')."</td>\n";

						$sql="SELECT * FROM cn_notes_conteneurs WHERE login='$lig_ele->login' AND id_conteneur='$id_racine' AND statut='y'";
						$res_moy_carnet=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy_carnet)==0){
							$moy_carnet="-";
						}
						else{
							$lig_moy_carnet=mysqli_fetch_object($res_moy_carnet);
							$moy_carnet=$lig_moy_carnet->note;
						}


						$sql="SELECT * FROM matieres_notes WHERE login='$lig_ele->login' AND id_groupe='$id_groupe' AND statut='' AND periode='$num_periode'";
						$res_moy_bull=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
						if(mysqli_num_rows($res_moy_bull)==0){
							// IL VA FALLOIR MODIFIER POUR GERER LES statut=abs, statut=disp
							$moy_bull="-";
						}
						else{
							$lig_moy_bull=mysqli_fetch_object($res_moy_bull);
							$moy_bull=$lig_moy_bull->note;
						}
						//if($moy_carnet!=$moy_bull){$chaine_couleur=" color:red;";}else{$chaine_couleur="";}
						if($moy_carnet!=$moy_bull){$chaine_couleur=" background-color:red;";}else{$chaine_couleur="";}

						echo "<td style='text-align:center;$chaine_couleur'>$moy_carnet\n";
						echo "<input type='hidden' name='id_groupe[$cpt]' value='$id_groupe' />\n";
						echo "<input type='hidden' name='login[$cpt]' value='$lig_ele->login' />\n";
						echo "<input type='hidden' name='moy[$cpt]' value='$moy_carnet' />\n";
						echo "</td>\n";

						/*
						$sql="SELECT * FROM matieres_notes WHERE login='$lig_ele->login' AND id_groupe='$id_groupe' AND statut='' AND periode='$num_periode'";
						$res_moy_bull=mysql_query($sql);
						if(mysql_num_rows($res_moy_bull)==0){
							// IL VA FALLOIR MODIFIER POUR GERER LES statut=abs, statut=disp
							$moy_bull="-";
						}
						else{
							$lig_moy_bull=mysql_fetch_object($res_moy_bull);
							$moy_bull=$lig_moy_bull->note;
						}
						//if($moy_carnet!=$moy_bull){$chaine_couleur=" color:red;";}else{$chaine_couleur="";}
						if($moy_carnet!=$moy_bull){$chaine_couleur=" background-color:red;";}else{$chaine_couleur="";}
						*/

						echo "<td style='text-align:center;$chaine_couleur'>$moy_bull</td>\n";

						//echo "<td><input type='checkbox' name='coche_".$i."_$j' value='recopier' /></td>";
						echo "<td style='text-align:center;'><input type='checkbox' name='coche[$cpt]' id='coche_".$i."_".$j."' value='recopier' /></td>\n";
						if($temoin_grp==0){
							echo "<td>";
							//Coche/décoche
							echo "<a href='javascript:modif_case($i,true)'><img src='../images/enabled.png' width='15' height='15' alt='Cocher tout le groupe' title='Cocher tout le groupe' /></a>/\n";
							echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Décocher tout le groupe' title='Décocher tout le groupe' /></a>\n";
							echo "</td>\n";
						}
						else{
							echo "<td>&nbsp;</td>\n";
						}
						echo "</tr>\n";
						$temoin_grp++;
						$j++;
						$cpt++;
					}
				}
				$i++;
			}
			echo "</table>\n";
			//echo "<p align='center'><input type=\"submit\" name='recopier' value=\"Recopier\" /></p>\n";
			echo "<p align='center'><input type=\"submit\" name='recopier' value=\"Valider la recopie\" /></p>\n";
			echo "<input type='hidden' name='cpt' value='$cpt' />\n";
			echo "</form>\n";

			echo "<p><i>NOTE:</i> Les cases en rouge mettent en évidence les moyennes non recopiées.</p>\n";

			echo "<script type='text/javascript' language='javascript'>
	function modif_case(n_grp,statut){
		for(k=0;k<$effectif_classe;k++){
			document.getElementById('coche_'+n_grp+'_'+k).checked=statut;
		}
	}

	function modif_toutes_cases(statut){
		for(m=0;m<$i;m++){
			modif_case(m,statut);
		}
	}

</script>\n";


		}

		/*
		if(isset($_POST['recopier'])){
			$num_periode=$_POST['num_periode'];
			$id_classe=$_POST['id_classe'];
			$login=$_POST['login'];
			$id_groupe=$_POST['id_groupe'];
			$moy=$_POST['moy'];
			$coche=$_POST['coche'];
			$cpt=$_POST['cpt'];

			echo "<p>Classe $id_classe sur période $num_periode<br />\n";
			for($i=0;$i<$cpt;$i++){
				if((isset($coche[$i]))&&($moy[$i]!='-')){
					//echo "$login[$i] $id_groupe[$i] $moy[$i] <br />\n";
					$sql="SELECT * FROM matieres_notes WHERE
						login='$login[$i]' AND
						id_groupe='$id_groupe[$i]' AND
						periode='$num_periode'";
					echo "$sql<br />\n";
					$res_test=mysql_query($sql);
					if(mysql_num_rows($res_test)==0){
						$sql="INSERT INTO matieres_notes SET
							login='$login[$i]',
							id_groupe='$id_groupe[$i]',
							periode='$num_periode',
							note=$moy[$i]";
						echo "$sql<br />\n";
						$res_insert=mysql_query($sql);
					}
					else{
						$sql="UPDATE matieres_notes SET
							note=$moy[$i] WHERE
							login='$login[$i]' AND
							id_groupe='$id_groupe[$i]' AND
							periode='$num_periode'";
						echo "$sql<br />\n";
						$res_update=mysql_query($sql);
					}
				}
			}
			echo "</p>\n";
		}
		*/
	}
}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>

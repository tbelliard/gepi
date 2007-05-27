<?php
/*
 * Last modification  : 04/10/2006
 *
 * Copyright 2001-2004 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
};


if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

//if(isset($suppr_resp)){
if((isset($suppr_resp1))||(isset($suppr_resp2))||(isset($suppr_resp0))){
	$msg="";

	if(isset($suppr_resp1)){
		$suppr_resp=$suppr_resp1;
		for($i=0;$i<count($suppr_resp);$i++){
			$sql="DELETE FROM responsables2 WHERE pers_id='$suppr_resp[$i]' AND resp_legal='1'";
			//echo "$sql<br />\n";
			$res0=mysql_query($sql);
			if($res0){
				// Est-ce que ce responsable est encore responsable de quelqu'un?
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$suppr_resp[$i]'";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)==0){
					// On vérifie que la personne existe et on en récupère l'identifiant d'adresse (éventuellement vide)
					$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
					//echo "$sql<br />\n";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)>0){
						$lig1=mysql_fetch_object($res1);
						$sql="DELETE FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(!$res2){
							$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'resp_pers'.<br />\n";
						}
						else{
							$sql="SELECT 1=1 FROM resp_pers WHERE adr_id='$lig1->adr_id'";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								$sql="DELETE FROM resp_adr WHERE adr_id='$lig1->adr_id'";
								//echo "$sql<br />\n";
								$res4=mysql_query($sql);
								if(!$res4){
									$msg.="Erreur lors de la suppression de l'adresse $lig1->adr_id de la table 'resp_adr'.<br />\n";
								}
							}
						}
					}
				}
			}
			else{
				$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'responsables2'.<br />";
			}
		}
	}

	if(isset($suppr_resp)){
		unset($suppr_resp);
	}

	if(isset($suppr_resp2)){
		$suppr_resp=$suppr_resp2;
		for($i=0;$i<count($suppr_resp);$i++){
			$sql="DELETE FROM responsables2 WHERE pers_id='$suppr_resp[$i]' AND resp_legal='2'";
			//echo "$sql<br />\n";
			$res0=mysql_query($sql);
			if($res0){
				// Est-ce que ce responsable est encore responsable de quelqu'un?
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$suppr_resp[$i]'";
				//echo "$sql<br />\n";
				$res_test=mysql_query($sql);
				if(mysql_num_rows($res_test)==0){
					// On vérifie que la personne existe et on en récupère l'identifiant d'adresse (éventuellement vide)
					$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
					//echo "$sql<br />\n";
					$res1=mysql_query($sql);
					if(mysql_num_rows($res1)>0){
						$lig1=mysql_fetch_object($res1);
						$sql="DELETE FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
						//echo "$sql<br />\n";
						$res2=mysql_query($sql);
						if(!$res2){
							$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'resp_pers'.<br />\n";
						}
						else{
							$sql="SELECT 1=1 FROM resp_pers WHERE adr_id='$lig1->adr_id'";
							//echo "$sql<br />\n";
							$res3=mysql_query($sql);
							if(mysql_num_rows($res3)==0){
								$sql="DELETE FROM resp_adr WHERE adr_id='$lig1->adr_id'";
								//echo "$sql<br />\n";
								$res4=mysql_query($sql);
								if(!$res4){
									$msg.="Erreur lors de la suppression de l'adresse $lig1->adr_id de la table 'resp_adr'.<br />\n";
								}
							}
						}
					}
				}
			}
			else{
				$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'responsables2'.<br />";
			}
		}
	}





	if(isset($suppr_resp)){
		unset($suppr_resp);
	}

	if(isset($suppr_resp0)){
		// Suppression des personnes non responsables d'élèves
		$suppr_resp=$suppr_resp0;
		for($i=0;$i<count($suppr_resp);$i++){
			// On vérifie que la personne existe et on en récupère l'identifiant d'adresse (éventuellement vide)
			$sql="SELECT adr_id FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
			//echo "$sql<br />\n";
			$res1=mysql_query($sql);
			if(mysql_num_rows($res1)>0){
				$lig1=mysql_fetch_object($res1);
				$sql="DELETE FROM resp_pers WHERE pers_id='$suppr_resp[$i]'";
				//echo "$sql<br />\n";
				$res2=mysql_query($sql);
				if(!$res2){
					$msg.="Erreur lors de la suppression du responsable $suppr_resp[$i] de la table 'resp_pers'.<br />\n";
				}
				else{
					$sql="SELECT 1=1 FROM resp_pers WHERE adr_id='$lig1->adr_id'";
					//echo "$sql<br />\n";
					$res3=mysql_query($sql);
					if(mysql_num_rows($res3)==0){
						$sql="DELETE FROM resp_adr WHERE adr_id='$lig1->adr_id'";
						//echo "$sql<br />\n";
						$res4=mysql_query($sql);
						if(!$res4){
							$msg.="Erreur lors de la suppression de l'adresse $lig1->adr_id de la table 'resp_adr'.<br />\n";
						}
					}
				}
			}
		}
	}





	if($msg==''){
		$msg="Suppression(s) réussie(s).";
	}
}

//**************** EN-TETE *****************
$titre_page = "Gestion des responsables élèves";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	//echo "mysql_num_rows($test)=".mysql_num_rows($test)."<br />";
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des données responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			if ($_SESSION['statut'] == 'administrateur'){
				echo "<p class='bold'><a href=\"../accueil_admin.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
			}
			else{
				echo "<p class='bold'><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
			}

			echo "<p>Une conversion des données élèves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}

echo "<p class=bold>";
if ($_SESSION['statut'] == 'administrateur'){
	echo "<a href=\"../accueil_admin.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href=\"modify_resp.php\">Ajouter un responsable</a>\n";
	if(getSettingValue("import_maj_xml_sconet")==1){
		echo " | <a href=\"maj_import.php\">Mettre à jour depuis Sconet</a>\n";
	}
	echo " | <a href=\"index.php?num_resp=0&amp;order_by=nom,prenom\">Personnes non associées</a>\n";
	echo " | <a href=\"gerer_adr.php\">Gérer les adresses</a>\n";
}
else{
	echo "<a href=\"../accueil.php\"> <img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
}
echo "</p>\n";

$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

//if (!isset($order_by)) {$order_by = "nom1,prenom1";}
if(!isset($order_by)) {$order_by = "nom,prenom";$num_resp=1;}

$num_resp=isset($_POST['num_resp']) ? $_POST['num_resp'] : (isset($_GET['num_resp']) ? $_GET['num_resp'] : 1);


unset($chaine_recherche);
if(!isset($val_rech)) {$val_rech="";}
//if(isset($val_rech)){
if($val_rech!=""){
	//echo "\$val_rech=$val_rech<br />";
	//$order_by=="nom,prenom";
	$limit="TOUS";
	if($val_rech!=""){
		// FILTRER LES CARACTERES DE $val_rech?
		switch($mode_rech){
			case "contient":
					$valeur_cherchee="%$val_rech%";
				break;
			case "commence par":
					$valeur_cherchee="$val_rech%";
				break;
			case "se termine par":
					$valeur_cherchee="%$val_rech";
				break;
		}

		switch($crit_rech){
			case "prenom":
					$crit_rech="prenom";
				break;
			default:
					$crit_rech="nom";
				break;
		}

		switch($champ_rech){
			case "resp1":
					$chaine_recherche="rp.$crit_rech LIKE '$valeur_cherchee'";
					$num_resp=1;
				break;
			case "resp2":
					$chaine_recherche="rp.$crit_rech LIKE '$valeur_cherchee'";
					$num_resp=2;
				break;
			case "eleves":
					$chaine_recherche="e.$crit_rech LIKE '$valeur_cherchee'";
					$num_resp="ele";
				break;
		}
	}
}
else{
	// Y a-t-il des responsables,... dans la base pour le mode choisi.
	$cpt=0;
	if(($order_by=="nom,prenom")&&($num_resp==0)){
		$cpt=0;
		$sql="SELECT DISTINCT pers_id FROM resp_pers";
		$res1=mysql_query($sql);
		if(mysql_num_rows($res1)>0){
			while($lig1=mysql_fetch_object($res1)){
				$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$lig1->pers_id'";
				$test=mysql_query($sql);
				if(mysql_num_rows($test)==0){
					$cpt++;
				}
			}
		}
	}
	elseif(($order_by=="nom,prenom")&&($num_resp==1)){
		// Pour ne récupérer qu'une seule occurence de pers_id:
		$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
				rp.pers_id=r.pers_id AND
				r.resp_legal='$num_resp'
			ORDER BY $order_by";
		$res1=mysql_query($sql);
		$cpt=mysql_num_rows($res1);
	}
	elseif(($order_by=="nom,prenom")&&($num_resp==2)){
		$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
				rp.pers_id=r.pers_id AND
				r.resp_legal='$num_resp'
			ORDER BY $order_by";
		$res1=mysql_query($sql);
		$cpt=mysql_num_rows($res1);
	}
	elseif(($order_by=="nom,prenom")&&($num_resp=="ele")){
		$sql="SELECT DISTINCT r.ele_id,e.nom,e.prenom,e.login FROM responsables2 r, eleves e WHERE e.ele_id=r.ele_id ORDER BY e.nom,e.prenom";
		$res1=mysql_query($sql);
		$cpt=mysql_num_rows($res1);
	}


	if($cpt==0){
		echo "<p>Aucun responsable trouvé.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}
}

//echo "cpt=$cpt<br />";

//echo "<p>\$chaine_recherche=$chaine_recherche et \$num_resp=$num_resp</p>";

echo "<form enctype='multipart/form-data' name='liste_resp' action='".$_SERVER['PHP_SELF']."' method='post'>\n";

echo "<p align='center'>";
if(!isset($debut)){
	$debut=0;
}
else{
	if(strlen(ereg_replace("[0-9]","",$debut))){
		$debut=0;
	}
}

if($debut > 0){
	echo "<input type='button' value='<<' onClick='precedent()' /> \n";
}

if(!isset($limit)){
	$limit=20;
}

//echo "Afficher <select name='limit'>\n";
//echo "<input type='submit' value='Afficher' />\n";
echo "<input type='button' value='Afficher' onClick='decoche_suppr_et_valide();' />\n";

echo "<select name='limit'>\n";
if($limit==20){$selected=" selected='true'";}else{$selected="";}
echo "<option value='20'$selected>20</option>\n";
if($limit==50){$selected=" selected='true'";}else{$selected="";}
echo "<option value='50'$selected>50</option>\n";
for($i=100;$i<=500;$i+=100){
	if($limit==$i){$selected=" selected='true'";}else{$selected="";}
	echo "<option value='$i'$selected>$i</option>\n";
}
echo "<option value='TOUS'>TOUS</option>\n";
echo "</select> enregistrements à partir de l'enregistrement n°\n";
echo "<input type='text' name='debut' value='$debut' size='5' /> \n";



if(isset($cpt)){
	//echo "<p>limit=$limit debut=$debut cpt=$cpt</p>";
	if($limit+$debut<$cpt){
		echo "<input type='button' value='>>' onClick='suivant()' /> \n";
	}
}
else{
	echo "<input type='button' value='>>' onClick='suivant()' /> \n";
}

echo "</p>\n";



echo "<script type='text/javascript'>
	function precedent(){
		debut=document.forms.liste_resp.debut.value;
		limit=document.forms.liste_resp.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.liste_resp.debut.value=0;
		}
		else{
			document.forms.liste_resp.debut.value=Math.max(debut-limit,0);
		}
		document.forms.liste_resp.submit();
	}

	function suivant(){
		debut=document.forms.liste_resp.debut.value;
		limit=document.forms.liste_resp.limit.value;
		//alert('debut='+debut+' et limit='+limit);

		if(limit=='TOUS'){
			document.forms.liste_resp.debut.value=0;
		}
		else{
			// Il faudrait récupérer le nombre de lignes du tableau...\n";

if(isset($cpt)){
	echo "			document.forms.liste_resp.debut.value=Math.min(eval(debut)+eval(limit),eval($cpt)-eval(limit));\n";
}
else{
	echo "			document.forms.liste_resp.debut.value=eval(debut)+eval(limit);\n";
}

echo "		}
		document.forms.liste_resp.submit();
	}
</script>\n";


echo "<div style='text-align:center;'>\n";
echo "<a href='#' onClick=\"document.getElementById('div_rech').style.display='';return false;\">Chercher</a>\n";
echo "<div id='div_rech' style='display:none;' align='center'>\n";
echo "<table border='0'><tr><td>parmi les </td>\n";
echo "<td>\n";
echo "<input type='radio' name='champ_rech' value='resp1' checked /> responsables (<i>légal 1</i>)\n";
echo "<br />\n";
echo "<input type='radio' name='champ_rech' value='resp2' /> responsables (<i>légal 2</i>)\n";
echo "<br />\n";
echo "<input type='radio' name='champ_rech' value='eleves' /> élèves\n";
echo "</td>\n";
echo "<td>\n";
echo " ceux dont le \n";
echo "</td>\n";
echo "<td>\n";
echo "<input type='radio' name='crit_rech' value='nom' checked /> nom\n";
echo "<br />\n";
echo "<input type='radio' name='crit_rech' value='prenom' /> prénom\n";
echo "</td>\n";
echo "<td>\n";
echo "<input type='radio' name='mode_rech' value='contient' checked /> contient \n";
echo "<br />\n";
echo "<input type='radio' name='mode_rech' value='commence par' /> commence par \n";
echo "<br />\n";
echo "<input type='radio' name='mode_rech' value='se termine par' /> se termine par \n";
echo "</td>\n";
echo "<td>\n";
echo "<input type='text' name='val_rech' value='' />\n";
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "<center><input type='submit' value='Valider' /></center>\n";
echo "</div>\n";
echo "</div>\n";

//echo "<center><input type='submit' value='Valider' /></center>\n";

echo "<table border='1' align='center'>\n";

$cpt_suppr=0;

//if($num_resp==0){
if("$num_resp"=="0"){
	// Afficher les personnes non associées à des élèves.

	//echo "<tr><td colspan='3'>TEMOIN: $num_resp</td></tr>";

	$ligne_titre="";
	$ligne_titre.="<tr>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Nom prénom</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Adresse</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Supprimer</td>\n";
	$ligne_titre.="</tr>\n";

	$cpt=0;
	$sql="SELECT DISTINCT pers_id,nom,prenom,adr_id FROM resp_pers";
	$res1=mysql_query($sql);
	if(mysql_num_rows($res1)>0){
		while($lig1=mysql_fetch_object($res1)){
			$sql="SELECT 1=1 FROM responsables2 r WHERE r.pers_id='$lig1->pers_id'";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)==0){

				if($cpt%10==0){
					echo $ligne_titre;
				}

				if($cpt%2==0){
					$alt='silver';
				}
				else{
					$alt='white';
				}

				echo "<tr style='background-color:".$alt.";'>\n";
				echo "<td style='text-align:center;'>\n";
				echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig1->nom $lig1->prenom</a>\n";
				echo "</td>\n";


					echo "<td style='text-align:center;'>\n";
				$sql="SELECT ra.* FROM resp_adr ra WHERE
								ra.adr_id='$lig1->adr_id'";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0){
					$lig2=mysql_fetch_object($res2);
					if($lig2->adr1!=''){echo "$lig2->adr1\n";}
					if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
					if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
					if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
					if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
					if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
				}
				echo "</td>\n";

				echo "<td style='text-align:center;'>\n";
				echo "<input type='checkbox' name='suppr_resp0[]' id='suppr_$cpt_suppr' value='$lig1->pers_id' />";
				$cpt_suppr++;
				echo "</td>\n";
				echo "</tr>\n";

				$cpt++;

/*
				$sql="SELECT rp.nom,rp.prenom,ra.* FROM resp_pers rp, resp_adr ra WHERE
										rp.adr_id=ra.adr_id AND
										rp.pers_id='$lig1->pers_id'
									ORDER BY $order_by";
				echo "<tr><td colspan='3'>$sql</td></tr>\n";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0){
					$lig2=mysql_fetch_object($res2);
					echo "<tr style='background-color:".$alt.";'>\n";
					echo "<td style='text-align:center;'>\n";
					echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					if($lig2->adr1!=''){echo "$lig2->adr1\n";}
					if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
					if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
					if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
					if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
					if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					echo "<input type='checkbox' name='suppr_resp1[]' value='$lig1->pers_id' />";
					echo "</td>\n";
					echo "</tr>\n";

					$cpt++;
				}
				*/

			}
		}
	}

}
else{
	$ligne_titre="";
	$ligne_titre.="<tr>\n";
	//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='2'>Responsable légal 1</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;' colspan='3'>Responsable légal 1</td>\n";
	//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#FAFABE;' rowspan='2'><a href='index.php?order_by=nom,prenom&amp;tri=ele'>Elève(s)</a></td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#FAFABE;' rowspan='2'><a href='index.php?order_by=nom,prenom&amp;num_resp=ele&amp;debut=$debut&amp;limit=$limit'>Elève(s)</a></td>\n";
	//$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' colspan='2'>Responsable légal 2</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;' colspan='3'>Responsable légal 2</td>\n";
	$ligne_titre.="</tr>\n";
	$ligne_titre.="<tr>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'><a href='index.php?order_by=nom,prenom&amp;num_resp=1&amp;debut=$debut&amp;limit=$limit'>Nom prénom</a></td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Adresse</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>Supprimer</td>\n";
	//$ligne_titre.="<td>Elève</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'><a href='index.php?order_by=nom,prenom&amp;num_resp=2&amp;debut=$debut&amp;limit=$limit'>Nom prénom</a></td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>Adresse</td>\n";
	$ligne_titre.="<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>Supprimer</td>\n";
	$ligne_titre.="</tr>\n";

	$max_cpt_res4=0;

	if(($order_by=="nom,prenom")&&($num_resp==1)){
		// Pour ne récupérer qu'une seule occurence de pers_id:
		$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
				rp.pers_id=r.pers_id AND
				r.resp_legal='$num_resp'";
		if(isset($chaine_recherche)){
			$sql.=" AND $chaine_recherche";
			echo "<!--$sql-->\n";
		}
		$sql.=" ORDER BY $order_by";
		if($limit!='TOUS'){
			$sql.=" LIMIT $debut,$limit";
		}
		$res1=mysql_query($sql);

		if(mysql_num_rows($res1)){
			$cpt=0;
			while($lig1=mysql_fetch_object($res1)){

				if($cpt%10==0){
					echo $ligne_titre;
				}

				if($cpt%2==0){
					$alt='silver';
				}
				else{
					$alt='white';
				}

				if($num_resp==1){$autre_resp=2;}else{$autre_resp=1;}

				$sql="SELECT rp.nom,rp.prenom,ra.* FROM resp_pers rp, resp_adr ra WHERE
										rp.adr_id=ra.adr_id AND
										rp.pers_id='$lig1->pers_id'
									ORDER BY $order_by";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0){
					while($lig2=mysql_fetch_object($res2)){
						$sql="SELECT DISTINCT e.ele_id,e.login,e.nom,e.prenom FROM responsables2 r, eleves e WHERE r.pers_id='$lig1->pers_id' AND r.resp_legal='$num_resp' AND r.ele_id=e.ele_id";
						$res3=mysql_query($sql);
						//if(mysql_num_rows($res3)>0){
							echo "<tr style='background-color:".$alt.";'>\n";
							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
							echo "</td>\n";

							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							if($lig2->adr1!=''){echo "$lig2->adr1\n";}
							if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
							if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
							if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
							if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
							if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
							echo "</td>\n";


							echo "<td style='text-align:center;'";
							if(mysql_num_rows($res3)>1){
								echo " rowspan='".mysql_num_rows($res3)."'";
							}
							echo ">\n";
							//echo "<input type='checkbox' name='suppr_resp1[]' id='suppr_resp1_$cpt' value='$lig1->pers_id' />";
							echo "<input type='checkbox' name='suppr_resp1[]' id='suppr_$cpt_suppr' value='$lig1->pers_id' />";
							$cpt_suppr++;
							echo "</td>\n";


							if(mysql_num_rows($res3)>0){
								$cpt_temoin=0;
								while($lig3=mysql_fetch_object($res3)){
									if($cpt_temoin>0){
										echo "<tr style='background-color:".$alt.";'>\n";
									}
									echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig3->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig3->nom $lig3->prenom</a></td>\n";

									$sql="SELECT rp.nom,rp.prenom,r.*,ra.* FROM resp_pers rp, responsables2 r, resp_adr ra WHERE
										rp.pers_id=r.pers_id AND
										rp.adr_id=ra.adr_id AND
										r.ele_id='$lig3->ele_id' AND
										r.resp_legal=$autre_resp";
									$res4=mysql_query($sql);
									if(mysql_num_rows($res4)>0){
										//$cpt_res4=0;
										while($lig4=mysql_fetch_object($res4)){
											echo "<td style='text-align:center;'>\n";
											echo "<a href='modify_resp.php?pers_id=$lig4->pers_id'>$lig4->nom $lig4->prenom</a>\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											if($lig4->adr1!=''){echo "$lig4->adr1\n";}
											if($lig4->adr2!=''){echo "<br />\n$lig4->adr2\n";}
											if($lig4->adr3!=''){echo "<br />\n$lig4->adr3\n";}
											if($lig4->adr4!=''){echo "<br />\n$lig4->adr4\n";}
											if(($lig4->commune!='')||($lig4->cp!='')){echo "<br />\n$lig4->cp $lig4->commune\n";}
											if($lig4->pays!=''){echo "<br />\n$lig4->pays\n";}
											echo "</td>\n";


											//echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp2[]' id='suppr_resp2_".$cpt."_".$cpt_res4."' value='$lig4->pers_id' /></td>\n";
											//$cpt_res4++;

											echo "<td style='text-align:center;'><input type='checkbox' name='suppr_resp2[]' id='suppr_$cpt_suppr' value='$lig4->pers_id' /></td>\n";
											$cpt_suppr++;
										}

										//if($max_cpt_res4<$cpt_res4){$max_cpt_res4=$cpt_res4;}
									}
									else{
										echo "<td>&nbsp;</td>\n";
										echo "<td>&nbsp;</td>\n";
										echo "<td>&nbsp;</td>\n";
									}
									echo "</tr>\n";
									$cpt_temoin++;
								}
							}
							else{
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";
								echo "</tr>\n";
							}
						//}
					}
				}
				$cpt++;
			}
		}
	}
	elseif(($order_by=="nom,prenom")&&($num_resp==2)){
		// Pour ne récupérer qu'une seule occurence de pers_id:
		$sql="SELECT DISTINCT r.pers_id FROM resp_pers rp, responsables2 r WHERE
				rp.pers_id=r.pers_id AND
				r.resp_legal='$num_resp'
			ORDER BY $order_by";
		if($limit!='TOUS'){
			$sql.=" LIMIT $debut,$limit";
		}
		$res1=mysql_query($sql);

		if(mysql_num_rows($res1)){
			$cpt=0;
			while($lig1=mysql_fetch_object($res1)){

				if($cpt%10==0){
					echo $ligne_titre;
				}

				if($cpt%2==0){
					$alt='silver';
				}
				else{
					$alt='white';
				}

				if($num_resp==1){$autre_resp=2;}else{$autre_resp=1;}

				$sql="SELECT rp.nom,rp.prenom,ra.* FROM resp_pers rp, resp_adr ra WHERE
										rp.adr_id=ra.adr_id AND
										rp.pers_id='$lig1->pers_id'
									ORDER BY $order_by";
				$res2=mysql_query($sql);
				if(mysql_num_rows($res2)>0){
					while($lig2=mysql_fetch_object($res2)){
						$sql="SELECT DISTINCT e.ele_id,e.login,e.nom,e.prenom FROM responsables2 r, eleves e WHERE r.pers_id='$lig1->pers_id' AND r.resp_legal='$num_resp' AND r.ele_id=e.ele_id";
						$res3=mysql_query($sql);
						//if(mysql_num_rows($res3)>0){
							echo "<tr style='background-color:".$alt.";'>\n";



							if(mysql_num_rows($res3)>0){
								$cpt_temoin=0;
								while($lig3=mysql_fetch_object($res3)){
									if($cpt_temoin>0){
										echo "<tr style='background-color:".$alt.";'>\n";
									}


									$sql="SELECT rp.nom,rp.prenom,r.*,ra.* FROM resp_pers rp, responsables2 r, resp_adr ra WHERE
										rp.pers_id=r.pers_id AND
										rp.adr_id=ra.adr_id AND
										r.ele_id='$lig3->ele_id' AND
										r.resp_legal=$autre_resp";
									$res4=mysql_query($sql);
									if(mysql_num_rows($res4)>0){
										while($lig4=mysql_fetch_object($res4)){
											echo "<td style='text-align:center;'>\n";
											echo "<a href='modify_resp.php?pers_id=$lig4->pers_id'>$lig4->nom $lig4->prenom</a>\n";
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											if($lig4->adr1!=''){echo "$lig4->adr1\n";}
											if($lig4->adr2!=''){echo "<br />\n$lig4->adr2\n";}
											if($lig4->adr3!=''){echo "<br />\n$lig4->adr3\n";}
											if($lig4->adr4!=''){echo "<br />\n$lig4->adr4\n";}
											if(($lig4->commune!='')||($lig4->cp!='')){echo "<br />\n$lig4->cp $lig4->commune\n";}
											if($lig4->pays!=''){echo "<br />\n$lig4->pays\n";}
											echo "</td>\n";

											echo "<td style='text-align:center;'>\n";
											echo "<input type='checkbox' name='suppr_resp1[]' id='suppr_$cpt_suppr' value='$lig4->pers_id' />";
											$cpt_suppr++;
											echo "</td>\n";
										}

										/*
										echo "<td style='text-align:center;'>\n";
										echo "<input type='checkbox' name='suppr_resp1[]' value='$lig4->pers_id' />";
										echo "</td>\n";
										*/

									}
									else{
										echo "<td>&nbsp;</td>\n";
										echo "<td>&nbsp;</td>\n";
										echo "<td>&nbsp;</td>\n";
									}

									echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig3->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig3->nom $lig3->prenom</a></td>\n";



									if($cpt_temoin==0){
										echo "<td style='text-align:center;'";
										if(mysql_num_rows($res3)>1){
											echo " rowspan='".mysql_num_rows($res3)."'";
										}
										echo ">\n";
										echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
										echo "</td>\n";

										echo "<td style='text-align:center;'";
										if(mysql_num_rows($res3)>1){
											echo " rowspan='".mysql_num_rows($res3)."'";
										}
										echo ">\n";
										if($lig2->adr1!=''){echo "$lig2->adr1\n";}
										if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
										if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
										if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
										if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
										if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
										echo "</td>\n";

										echo "<td style='text-align:center;'";
										if(mysql_num_rows($res3)>1){
											echo " rowspan='".mysql_num_rows($res3)."'";
										}
										echo ">\n";
										echo "<input type='checkbox' name='suppr_resp2[]' id='suppr_$cpt_suppr' value='$lig1->pers_id' />";
										$cpt_suppr++;
										echo "</td>\n";
									}

									echo "</tr>\n";
									$cpt_temoin++;
								}
							}
							else{
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";
								echo "<td>&nbsp;</td>\n";

								echo "<td>&nbsp;</td>\n";

								echo "<td style='text-align:center;'";
								if(mysql_num_rows($res3)>1){
									echo " rowspan='".mysql_num_rows($res3)."'";
								}
								echo ">\n";
								echo "<a href='modify_resp.php?pers_id=$lig1->pers_id'>$lig2->nom $lig2->prenom</a>\n";
								echo "</td>\n";

								echo "<td style='text-align:center;'";
								if(mysql_num_rows($res3)>1){
									echo " rowspan='".mysql_num_rows($res3)."'";
								}
								echo ">\n";
								if($lig2->adr1!=''){echo "$lig2->adr1\n";}
								if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
								if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
								if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
								if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
								if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
								echo "</td>\n";

								echo "<td style='text-align:center;'";
								if(mysql_num_rows($res3)>1){
									echo " rowspan='".mysql_num_rows($res3)."'";
								}
								echo ">\n";
								echo "<input type='checkbox' name='suppr_resp2[]' id='suppr_$cpt_suppr' value='$lig1->pers_id' />";
								$cpt_suppr++;
								echo "</td>\n";

								echo "</tr>\n";
							}






						//}
					}
				}
				$cpt++;
			}
		}



	}
	/*
	elseif(($order_by=="commune,adr1,adr2,adr3,adr4")&&($num_resp==1)){
	}
	elseif(($order_by=="commune,adr1,adr2,adr3,adr4")&&($num_resp==2)){
	}
	*/
	//elseif(($order_by=="nom,prenom")&&($_GET['tri']=="ele")){
	elseif(($order_by=="nom,prenom")&&($num_resp=="ele")){
		$sql="SELECT DISTINCT r.ele_id,e.nom,e.prenom,e.login FROM responsables2 r, eleves e WHERE e.ele_id=r.ele_id";
		if(isset($chaine_recherche)){
			$sql.=" AND $chaine_recherche";
			echo "<!--$sql-->\n";
		}
		$sql.=" ORDER BY e.nom,e.prenom";
		if($limit!='TOUS'){
			$sql.=" LIMIT $debut,$limit";
		}
		$res1=mysql_query($sql);

		//echo "<tr><td colspan='5'>AAA</td></tr>\n";
		if(mysql_num_rows($res1)>0){
			$cpt=0;
			while($lig1=mysql_fetch_object($res1)){

				if($cpt%10==0){
					echo $ligne_titre;
				}

				if($cpt%2==0){
					$alt='silver';
				}
				else{
					$alt='white';
				}

				$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM resp_pers rp, resp_adr ra, responsables2 r WHERE
						r.pers_id=rp.pers_id AND
						rp.adr_id=ra.adr_id AND
						r.resp_legal='1' AND
						r.ele_id='$lig1->ele_id'";
				$res2=mysql_query($sql);

				//echo "<tr>\n";
				echo "<tr style='background-color:".$alt.";'>\n";

				if(mysql_num_rows($res2)>0){
					//while($lig2=mysql_fetch_object($res2)){
						$lig2=mysql_fetch_object($res2);
						echo "<td style='text-align:center;'>\n";
						echo "<a href='modify_resp.php?pers_id=$lig2->pers_id'>$lig2->nom $lig2->prenom</a>\n";
						echo "</td>\n";

						echo "<td style='text-align:center;'>\n";
						if($lig2->adr1!=''){echo "$lig2->adr1\n";}
						if($lig2->adr2!=''){echo "<br />\n$lig2->adr2\n";}
						if($lig2->adr3!=''){echo "<br />\n$lig2->adr3\n";}
						if($lig2->adr4!=''){echo "<br />\n$lig2->adr4\n";}
						if(($lig2->commune!='')||($lig2->cp!='')){echo "<br />\n$lig2->cp $lig2->commune\n";}
						if($lig2->pays!=''){echo "<br />\n$lig2->pays\n";}
						echo "</td>\n";

						echo "<td style='text-align:center;'>\n";
						echo "<input type='checkbox' name='suppr_resp1[]' id='suppr_$cpt_suppr' value='$lig2->pers_id' />";
						$cpt_suppr++;
						echo "</td>\n";
					//}
				}
				else{
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
				}

				echo "<td style='text-align:center;'><a href='../eleves/modify_eleve.php?eleve_login=$lig1->login&amp;quelles_classes=toutes&amp;order_type=nom,prenom'>$lig1->nom $lig1->prenom</a></td>\n";


				$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM resp_pers rp, resp_adr ra, responsables2 r WHERE
						r.pers_id=rp.pers_id AND
						rp.adr_id=ra.adr_id AND
						r.resp_legal='2' AND
						r.ele_id='$lig1->ele_id'";
				$res3=mysql_query($sql);
				if(mysql_num_rows($res3)>0){
					$lig3=mysql_fetch_object($res3);
					echo "<td style='text-align:center;'>\n";
					echo "<a href='modify_resp.php?pers_id=$lig3->pers_id'>$lig3->nom $lig3->prenom</a>\n";
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					if($lig3->adr1!=''){echo "$lig3->adr1\n";}
					if($lig3->adr2!=''){echo "<br />\n$lig3->adr2\n";}
					if($lig3->adr3!=''){echo "<br />\n$lig3->adr3\n";}
					if($lig3->adr4!=''){echo "<br />\n$lig3->adr4\n";}
					if(($lig3->commune!='')||($lig3->cp!='')){echo "<br />\n$lig3->cp $lig3->commune\n";}
					if($lig3->pays!=''){echo "<br />\n$lig3->pays\n";}
					echo "</td>\n";

					echo "<td style='text-align:center;'>\n";
					echo "<input type='checkbox' name='suppr_resp2[]' id='suppr_$cpt_suppr' value='$lig3->pers_id' />";
					$cpt_suppr++;
					echo "</td>\n";
				}
				else{
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
					echo "<td>&nbsp;</td>\n";
				}

				echo "</tr>\n";
				$cpt++;
			}
		}
	}
}
echo "</table>\n";

echo "<script type='text/javascript'>
	function decoche_suppr_et_valide(){
		for(i=0;i<$cpt_suppr;i++){
			if(document.getElementById('suppr_'+i)){
				document.getElementById('suppr_'+i).checked=false;
			}
		}
		document.liste_resp.submit();
	}
</script>\n";
//echo "<input type='hidden' name='cpt' value='$cpt' />\n";
echo "<center><input type='submit' value='Valider' /></center>\n";
echo "</form>\n";
require("../lib/footer.inc.php");
?>

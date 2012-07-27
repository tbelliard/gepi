<?php
/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
// Modif Eric : Table s_alerte_mail à modifier : ajout champs
// ALTER TABLE `s_alerte_mail` ADD `adresse` VARCHAR( 250 ) NULL 
//INSERT INTO droits VALUES ('/mod_discipline/destinataires_alertes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Parametrage des destinataires de mail d alerte', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

if (isset($_POST['action']) and ($_POST['action'] == "reg_dest")) {
	check_token();

	$msg = '';
	$notok = false;

	$tab_statut=$_POST['tab_statut'];
	$tab_id_clas=$_POST['tab_id_clas'];
	
	for($j=0;$j<count($tab_id_clas);$j++){
		for($i=0;$i<count($tab_statut);$i++){
			if(isset($_POST['case_'.$i.'_'.$j])){
			    $requete= "SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'";
				//echo $requete; echo "</br>";
				$test=mysql_query($requete);
				if(mysql_num_rows($test)==0){
				    // Modif Eric Ajout Adresse autre
					if(isset($_POST['adresse_'.$i.'_'.$j]) and isset($_POST['case_'.$i.'_'.$j])){ 
					    $contenu_adresse = $_POST['adresse_'.$i.'_'.$j];
					    if ($contenu_adresse != '') {
						   $sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."', adresse='".$contenu_adresse."'";
						}
				    } else {
					    $sql="INSERT INTO s_alerte_mail SET id_classe='".$tab_id_clas[$j]."', destinataire='".$tab_statut[$i]."'";
					}
					// Fin modif
					$reg_data=mysql_query($sql);
					if(!$reg_data){
						$msg.= "Erreur lors de l'insertion d'un nouvel enregistrement $tab_id_clas[$j] pour $tab_statut[$i].";
						$notok = true;
					}
				}
				// Sinon: l'enregistrement est déjà présent.
			}
			else{
				$test=mysql_query("SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'");
				if(mysql_num_rows($test)>0){
					$sql="DELETE FROM s_alerte_mail WHERE id_classe='".$tab_id_clas[$j]."' AND destinataire='".$tab_statut[$i]."'";
					$reg_data=mysql_query($sql);
					if(!$reg_data){
						$msg.= "Erreur lors de la suppression de l'enregistrement $tab_id_clas[$j] pour $tab_statut[$i].";
						$notok = true;
					}
				}
			}
		}
	}


	if ($notok == true) {
		$msg .= "Il y a eu des erreurs lors de l'enregistrement des données";
	} else {
		$msg .= "L'enregistrement des données s'est bien passé.";
	}
}


$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
$titre_page = "Destinataires des alertes";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************
//debug_var();
// Cette page a été ouverte en target='blank' depuis une autre page (par exemple /eleves/modify_eleve.php)
// Après modification éventuelle, il faut quitter cette page.
echo "<p class='bold'>";
echo "<a href='index.php' onClick=\"if(confirm_abandon (this, change, '$themessage')){self.close()};return false;\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>\n";
?>

<p>Choisissez les destinataires des mails d'alerte pour des incidents dont des élèves sont protagonistes.</p>
<?php

	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo add_token_field();

	//Ajout Eric
	$contenu_adresse = "";

	$tab_statut=array('cpe', 'scolarite', 'pp', 'professeurs', 'administrateur', 'mail');

	//echo "<table border='1'>\n";
	echo "<table class='boireaus'>\n";
	//#96C8F0
	$ligne_statuts="<tr style='background-color:#FAFABE;'>\n";
	//$ligne_comptes_scol.="<td style='text-align:center; font-weight:bold;'>Comptes</td>\n";
	$ligne_statuts.="<th style='text-align:center; font-weight:bold;'>Statuts</th>\n";
	$ligne_statuts.="<th>CPE</th>\n";
	$ligne_statuts.="<th>Scolarité<br />responsable<br />de la classe</th>\n";
	$gepi_prof_suivi=ucfirst(getSettingValue("gepi_prof_suivi"));
	$ligne_statuts.="<th>".$gepi_prof_suivi."</th>\n";
	$ligne_statuts.="<th>Professeurs<br />de la classe</th>\n";
	$ligne_statuts.="<th>Administrateurs</th>\n";
	$ligne_statuts.="<th>Autre adresse <br/>(Cocher puis saisir directement l'adresse)</th>\n"; 
	$ligne_statuts.="<th>\n";
	$ligne_statuts.="&nbsp;\n";
	$ligne_statuts.="</th>\n";
	$ligne_statuts.="</tr>\n";
	echo $ligne_statuts;

	echo "<tr style='background-color:#FAFABE;'>\n";
	echo "<th style='text-align:center; font-weight:bold;'>Classes</th>\n";
	for($i=0;$i<count($tab_statut);$i++){
		echo "<th style='text-align:center;'>\n";

		echo "<a href=\"javascript:modif_case($i,true,'col');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
		echo "<a href=\"javascript:modif_case($i,false,'col');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";

		echo "<input type='hidden' name='tab_statut[$i]' value='$tab_statut[$i]' />";
		//echo "</td>\n";
		echo "</th>\n";
	}
	echo "<th>&nbsp;</th>\n";
	echo "</tr>\n";

	$call_data = mysql_query("SELECT * FROM classes ORDER BY classe");
	$nombre_lignes = mysql_num_rows($call_data);
	
	if ($nombre_lignes != 0) {
		// Lignes classes...
		$j=0;
		$alt=1;
		while($lig_clas=mysql_fetch_object($call_data)){
			if(($j%10==0)&&$j>0){echo $ligne_statuts;}

			$alt=$alt*(-1);

			//if($j%2==0){$bgcolor="style='background-color: gray;'";}else{$bgcolor='';}
			//echo "<tr $bgcolor>\n";
			echo "<tr class='lig$alt white_hover'>\n";
			echo "<td style='text-align:center;'>";
			echo "<input type='hidden' name='tab_id_clas[$j]' value='$lig_clas->id' />\n";
			echo "$lig_clas->classe";
			echo "</td>\n";
			for($i=0;$i<count($tab_statut);$i++){
				$sql="SELECT 1=1 FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='".$tab_statut[$i]."';";
				//echo "$sql<br />";
				$test=mysql_query($sql);
				//if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: #AAE6AA;";}
				if(mysql_num_rows($test)==0){$checked="";$bgcolor="";}else{$checked="checked ";$bgcolor="background-color: plum;";}

				echo "<td style='text-align:center;$bgcolor'>\n";
				echo "<input type='checkbox' name='case_".$i."_".$j."' id='case_".$i."_".$j."' value='y' onchange='changement();' $checked/>\n";
				//Ajout Eric traitement autre mail
				$sql="SELECT * FROM s_alerte_mail WHERE id_classe='".$lig_clas->id."' AND destinataire='mail';";
				//echo $sql;
				$test=mysql_query($sql);
				if(mysql_num_rows($test)!=0) {
					$contenu_requete=mysql_fetch_object($test);
					if ($tab_statut[$i]== 'mail') {
					    if ($contenu_requete->adresse != NULL) {
						    $contenu_adresse = $contenu_requete->adresse;
						} else { 
						    $contenu_adresse = '';
						}
						echo "Adresse : <input type='text' name='adresse_".$i."_".$j."' value='$contenu_adresse' onchange='changement();' />\n";    
					} 
				} else if ($tab_statut[$i]== 'mail') echo "Adresse : <input type='text' name='adresse_".$i."_".$j."' value='' onchange='changement();' />\n";
				echo "</td>\n";
			}
			echo "<td>\n";
			echo "<a href=\"javascript:modif_case($j,true,'lig');changement();\"><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' /></a>/\n";
			//echo "<a href='javascript:modif_case($i,false)'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "<a href=\"javascript:modif_case($j,false,'lig');changement();\"><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' /></a>\n";
			echo "</td>\n";
			echo "</tr>\n";
			$j++;
		}

		echo "</table>\n";
		echo "<input type='hidden' name='action' value='reg_dest' />\n";
		echo "<p align='center'><input type='submit' value='Enregistrer' /></p>\n";
	} else {
		echo "</table>\n";
		echo "<p class='grand'><b>Attention :</b> aucune classe n'a été définie dans la base GEPI !</p>\n";
	}






	//============================================
	// AJOUT: boireaus
	echo "<script type='text/javascript' language='javascript'>
		function modif_case(id,statut,mode){
			// id: numéro de:
			//					. colonne correspondant au login
			//					. ligne
			// statut: true ou false
			// mode: col ou lig
			if(mode=='col'){
				for(k=0;k<$nombre_lignes;k++){
					if(document.getElementById('case_'+id+'_'+k)){
						document.getElementById('case_'+id+'_'+k).checked=statut;
					}
				}
			}
			else{
				for(k=0;k<".count($tab_statut).";k++){
					if(document.getElementById('case_'+k+'_'+id)){
						document.getElementById('case_'+k+'_'+id).checked=statut;
					}
				}
			}
			changement();
		}
	</script>\n";
	//============================================
?>
</form>
<?php require("../lib/footer.inc.php");?>

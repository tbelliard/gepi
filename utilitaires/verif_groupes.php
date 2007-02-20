<?php
@set_time_limit(0);
/*
 * Last modification  : 20/11/2006
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

//INSERT INTO `droits` VALUES ('/utilitaires/verif_groupes.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Vérification des incohérences d appartenances à des groupes', '');
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

function affiche_debug($texte,$e_login){
	$debug=0;
	if($debug==1){
		//if($e_login=="BUNEL_M"){
		if(($e_login=="BUNEL_M")||($e_login=="BALESTA_M")){
			echo $texte."<br />\n";
		}
	}
}

//**************** EN-TETE *****************
$titre_page = "Vérification des affectations dans les groupes";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************

echo "<p class=bold><a href='../accueil.php'>Accueil</a>|<a href='clean_tables.php'>Retour</a>|</p>";

if(!isset($_GET['verif'])){
	echo "<h2>Vérification des groupes</h2>\n";
	echo "<p>Cette page est destinée à repérer la cause d'éventuelles erreurs du type:</p>\n";
	echo "<pre style='color:green;'>Warning: mysql_result(): Unable to jump to row 0
on MySQL result index 468 in /var/wwws/gepi/lib/groupes.inc.php on line 143</pre>\n";
	echo "<p>Pour procéder à la vérification, cliquez sur ce lien: <a href='".$_SERVER['PHP_SELF']."?verif=oui'>Vérification</a><br />(<i>l'opération peut être très longue</i>)</p>\n";
}
else{
	echo "<h2>Recherche des inscriptions erronées d'élèves</h2>\n";
	flush();
	$err_no=0;
	// On commence par ne récupérer que les login/periode pour ne pas risquer d'oublier d'élèves
	// (il peut y avoir des incohérences non détectées si on essaye de récupérer davantage d'infos dans un premier temps)
	$sql="SELECT DISTINCT login,periode FROM j_eleves_groupes ORDER BY login,periode";
	$res_ele=mysql_query($sql);
	//$ini="A";
	$ini="";
	//echo "<i>Parcours des login commençant par la lettre $ini</i>";
	while($lig_ele=mysql_fetch_object($res_ele)){

		if(strtoupper(substr($lig_ele->login,0,1))!=$ini){
			$ini=strtoupper(substr($lig_ele->login,0,1));
			//echo " - <i>$ini</i>";
			echo "<p>\n<i>Parcours des login commençant par la lettre $ini</i></p>\n";
		}

		// Récupération de la liste des groupes auxquels l'élève est inscrit sur la période en cours d'analyse:
		$sql="SELECT id_groupe FROM j_eleves_groupes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
		//echo "$sql<br />\n";
		affiche_debug($sql,$lig_ele->login);
		$res_jeg=mysql_query($sql);

		//while($lig_jeg=mysql_fetch_object($res_jeg)){
		if(mysql_num_rows($res_jeg)>0){
			// On vérifie si l'élève est dans une classe pour cette période:
			//$sql="SELECT 1=1 FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
			$sql="SELECT id_classe FROM j_eleves_classes WHERE login='$lig_ele->login' AND periode='$lig_ele->periode'";
			affiche_debug($sql,$lig_ele->login);
			$res_jec=mysql_query($sql);

			if(mysql_num_rows($res_jec)==0){
				// L'élève n'est dans aucune classe sur la période choisie.
				$sql="SELECT c.* FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND periode='$lig_ele->periode' AND jec.id_classe=c.id";
				affiche_debug($sql,$lig_ele->login);
				$res_class_test=mysql_query($sql);

				// Le test ci-dessous est forcément vrai si on est arrivé là!
				if(mysql_num_rows($res_class_test)==0){
					$sql="SELECT DISTINCT c.id,c.classe FROM classes c, j_eleves_classes jec WHERE jec.login='$lig_ele->login' AND jec.id_classe=c.id";
					affiche_debug($sql,$lig_ele->login);
					$res_class=mysql_query($sql);

					$chaine_msg="";
					$chaine_classes="";
					if(mysql_num_rows($res_class)!=0){
						while($lig_class=mysql_fetch_object($res_class)){
							$chaine_classes.=", $lig_class->classe";
							$chaine_msg.=",<br /><a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_class->id."' target='_blank'>Contrôler en $lig_class->classe</a>\n";
						}
						$chaine_msg=substr($chaine_msg,7);
						$chaine_classes=substr($chaine_classes,2);

						//echo "<br />\n";
						echo "<p>\n";
						echo "<b>$lig_ele->login</b> de <b>$chaine_classes</b> est inscrit à des groupes pour la période <b>$lig_ele->periode</b>, mais n'est pas dans la classe pour cette période.<br />\n";

						echo $chaine_msg;


						// Contrôler à quelles classes les groupes sont liés.
						unset($tab_tmp_grp);
						$tab_tmp_grp=array();
						if(isset($tab_tmp_clas)){unset($tab_tmp_clas);}
						$tab_tmp_clas=array();
						while($lig_grp=mysql_fetch_object($res_jeg)){
							$tab_tmp_grp[]=$lig_grp->id_groupe;
							$sql="SELECT DISTINCT c.id,c.classe FROM classes c,j_groupes_classes jgc WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig_grp->id_groupe'";
							$res_grp2=mysql_query($sql);
							while($lig_tmp_clas=mysql_fetch_object($res_grp2)){
								if(!in_array($lig_tmp_clas->classe,$tab_tmp_clas)){
									$tab_tmp_clas[]=$lig_tmp_clas->classe;
								}
							}
						}

						echo "<br />\n";
						echo "Les groupes dont <b>$lig_ele->login</b> est membre sont liés ";
						if(count($tab_tmp_clas)>1){
							echo "aux classes suivantes: ";
						}
						else{
							echo "à la classe suivante: ";
						}
						echo $tab_tmp_clas[0];
						for($i=1;$i<count($tab_tmp_clas);$i++){
							echo ", ".$tab_tmp_clas[$i];
						}
						echo "<br />\n";
						echo "Si <b>$lig_ele->login</b> n'est pas dans une de ces classes, il faudrait l'affecter dans la classe sur une période au moins pour pouvoir supprimer son appartenance à ces groupes, ou procéder à un nettoyage des tables de la base GEPI.";

						echo "</p>\n";
					}
					else{
						echo "<p>\n";
						echo "<b>$lig_ele->login</b> est inscrit à des groupes pour la période <b>$lig_ele->periode</b>, mais n'est dans aucune classe.<br />\n";
						// ... dans aucune classe sur aucune période.
						echo "Il va falloir l'affecter dans une classe pour pouvoir supprimer ses inscriptions à des groupes.<br />\n";
						echo "</p>\n";
					}
				}
				$err_no++;


				// Est-ce qu'en plus l'élève aurait des notes ou moyennes saisies sur la période?
				//$sql="SELECT * FROM matieres_notes WHERE id_groupe='$tab_tmp_grp[$i]' AND periode='$lig_ele->periode' AND login='$lig_ele->login'"
				$sql="SELECT * FROM matieres_notes WHERE periode='$lig_ele->periode' AND login='$lig_ele->login'";
				$res_mat_not=mysql_query($sql);
				if(mysql_num_rows($res_mat_not)>0){
					echo "<b>$lig_ele->login</b> a de plus des moyennes saisies pour le bulletin sur la période <b>$lig_ele->periode</b>";
					/*
					echo " en "
					$lig_tmp=mysql_fetch_object($res_mat_not);
					$sql="SELECT description FROM groupes WHERE id='$lig_tmp->id_groupe'"
					*/
				}

			}
			else{
				if(mysql_num_rows($res_jec)==1){
					$lig_clas=mysql_fetch_object($res_jec);
					//$lig_grp=mysql_fetch_object($res_jeg);
					while($lig_grp=mysql_fetch_object($res_jeg)){
						// On cherche si l'association groupe/classe existe:
						$sql="SELECT 1=1 FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe' AND id_classe='$lig_clas->id_classe'";
						affiche_debug($sql,$lig_ele->login);
						$res_test_grp_clas=mysql_query($sql);

						if(mysql_num_rows($res_test_grp_clas)==0){
							$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
							$res_tmp=mysql_query($sql);
							$lig_tmp=mysql_fetch_object($res_tmp);
							$clas_tmp=$lig_tmp->classe;

							$sql="SELECT description FROM groupes WHERE id='$lig_grp->id_groupe'";
							$res_tmp=mysql_query($sql);
							$lig_tmp=mysql_fetch_object($res_tmp);
							$grp_tmp=$lig_tmp->description;

							echo "<p>\n";
							//echo "Il semble que $lig_ele->login de la classe $lig_clas->id_classe soit inscrit dans le groupe $lig_grp->id_groupe alors que ce groupe n'est pas associé à la classe dans 'j_groupes_classes'.<br />\n";
							echo "<b>$lig_ele->login</b> est inscrit en période $lig_ele->periode dans le groupe <b>$grp_tmp</b> (<i>groupe n°$lig_grp->id_groupe</i>) alors que ce groupe n'est pas associé à la classe <b>$clas_tmp</b> dans 'j_groupes_classes'.<br />\n";

							// /groupes/edit_eleves.php?id_groupe=285&id_classe=8
							//$sql="SELECT id_classe FROM j_groupes_classes WHERE id_groupe='$lig_grp->id_groupe';";
							$sql="SELECT jgc.id_classe, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_groupe='$lig_grp->id_groupe' AND jgc.id_classe=c.id;";
							$res_tmp_clas=mysql_query($sql);
							if(mysql_num_rows($res_tmp_clas)>0){
								//$lig_tmp_clas=mysql_fetch_object($res_tmp_clas);
								//echo "Vous pouvez tenter de décocher l'élève de <b>$clas_tmp</b> du groupe <b>$grp_tmp</b> dans cette <a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>page</a> si il s'y trouve.<br />\n";
								echo "Vous pouvez tenter de décocher l'élève de <b>$clas_tmp</b> du groupe <b>$grp_tmp</b> dans l'une des pages suivantes ";
								$tab_tmp_class=array();
								$tab_tmp_classe=array();
								while($lig_tmp_clas=mysql_fetch_object($res_tmp_clas)){
									$tab_tmp_class[]=$lig_tmp_clas->id_classe;
									$tab_tmp_classe[]=$lig_tmp_clas->classe;
									echo "<a href='../groupes/edit_eleves.php?id_groupe=".$lig_grp->id_groupe."&amp;id_classe=".$lig_tmp_clas->id_classe."' target='_blank'>$lig_tmp_clas->classe</a>, ";
								}
								echo "si il s'y trouve.<br />\n";
							}

							echo "Si aucune erreur n'est relevée non plus dans la(es) classe(s) de ";
							echo "<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$lig_clas->id_classe."' target='_blank'>$clas_tmp</a>, \n";
							for($i=0;$i<count($tab_tmp_class);$i++){
								echo "<a href='../classes/eleve_options.php?login_eleve=".$lig_ele->login."&amp;id_classe=".$tab_tmp_class[$i]."' target='_blank'>".$tab_tmp_classe[$i]."</a>, \n";
							}
							echo "il faudra effectuer un <a href='clean_tables.php?maj=9'>nettoyage des tables de la base de données GEPI</a> (<i>après une <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>sauvegarde de la base</a></i>).<br />\n";
							echo "</p>\n";
							$err_no++;
						}
					}
				}
				else{
					echo "<p>\n";
					echo "<b>$lig_ele->login</b> est inscrit dans plusieurs classes sur la période $lig_ele->periode:<br />\n";
					while($lig_clas=mysql_fetch_object($res_jec)){
						$sql="SELECT classe FROM classes WHERE id='$lig_clas->id_classe'";
						$res_tmp=mysql_query($sql);
						$lig_tmp=mysql_fetch_object($res_tmp);
						$clas_tmp=$lig_tmp->classe;
						echo "Classe de <a href='../classes/classes_const.php?id_classe=$lig_clas->id_classe'>$clas_tmp</a> (<i>n°$lig_clas->id_classe</i>)<br />\n";
					}
					echo "Cela ne devrait pas être possible.<br />\n";
					echo "Faites le ménage dans les effectifs des classes ci-dessus.\n";
					echo "</p>\n";
					$err_no++;
				}
			}
		}
		// Pour envoyer ce qui a été écrit vers l'écran sans attendre la fin de la page...
		flush();
	}
	if($err_no==0){
		echo "<p>Aucune erreur d'affectation dans des groupes/classes n'a été détectée.</p>\n";
	}
	else{
		echo "<p>Une ou des erreurs ont été relevées.<br />\n";
		echo "Pour corriger, il faut passer par 'Gestion des bases/Gestion des classes/Gérer les élèves' et contrôler pour quelles périodes l'élève est dans la classe.<br />\n";
		echo "Puis, cliquer sur le lien 'Matières suivies' pour cet élève et décocher l'élève des périodes souhaitées appropriées.<br />\n";
		echo "</p>\n";
		echo "<p>Il se peut également qu'un <a href='clean_tables.php?maj=9'>nettoyage de la base (<i>étape des Groupes</i>)</a> soit nécessaire.<br />\n";
		echo "Prenez soin de faire une <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>sauvegarde de la base</a> auparavant par précaution.<br />\n";
	}

	echo "<hr />\n";

	echo "<h2>Recherche des références à des identifiants de groupes inexistants</h2>\n";

	$err_no=0;
	$table=array('j_groupes_classes','j_groupes_matieres','j_groupes_professeurs','j_eleves_groupes');
	$id_grp_suppr=array();

	for($i=0;$i<count($table);$i++){
		$sql="SELECT DISTINCT id_groupe FROM ".$table[$i]." ORDER BY id_groupe";
		$res_grp1=mysql_query($sql);

		if(mysql_num_rows($res_grp1)>0){
			echo "<p>On parcourt la table '".$table[$i]."'.</p>\n";
			while($ligne=mysql_fetch_array($res_grp1)){
				$sql="SELECT 1=1 FROM groupes WHERE id='".$ligne[0]."'";
				$res_test=mysql_query($sql);

				if(mysql_num_rows($res_test)==0){
					echo "<b>Erreur:</b> Le groupe d'identifiant $ligne[0] est utilisé dans $table[$i] alors que le groupe n'existe pas dans la table 'groupes'.<br />\n";
					$id_grp_suppr[]=$ligne[0];
					// FAIRE UNE SAUVEGARDE DE LA BASE AVANT DE DECOMMENTER LES 3 LIGNES CI-DESSOUS:
					/*
					$sql="DELETE FROM $table[$i] WHERE id_groupe='$ligne[0]'";
					echo "$sql<br />";
					$res_suppr=mysql_query($sql);
					*/
					$err_no++;
				}
				flush();
			}
		}
	}
	if($err_no==0){
		echo "<p>Aucune erreur d'identifiant de groupe n'a été relevée dans les tables 'j_groupes_classes', 'j_groupes_matieres', 'j_groupes_professeurs' et 'j_eleves_groupes'.</p>\n";
	}
	else{
		echo "<p>Une ou des erreurs ont été relevées.<br />\n";
		echo "Pour corriger, vous devriez procéder à un <a href='clean_tables.php?maj=9'>nettoyage de la base (<i>étape des Groupes</i>)</a>.<br />\n";
		echo "Prenez soin de faire une <a href='../gestion/accueil_sauve.php?action=dump' target='blank'>sauvegarde de la base</a> auparavant par précaution.<br />\n";
		echo "</p>\n";
	}

}
?>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
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
*
* ******************************************** *
* Appelle les sous-modèles                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/

/**
 *
 * @author regis
 */

/**
 * Vérifie que le dossier (et ses sous-dossiers) contient bien un fichier index.html
 *
 * @global int
 * @param string $dossier Le dossier
 * @return string Un message formaté
 */
function ajout_index_sous_dossiers($dossier) {
	global $niveau_arbo;

	$nb_creation=0;
	$nb_erreur=0;
	$nb_fich_existant=0;

	$retour="";

	//$dossier="../documents";
	$dir= opendir($dossier);
	if(!$dir) {
		$retour.="<p style='color:red'>Erreur lors de l'accès au dossier '$dossier'.</p>\n";
	}
	else {
		$retour.="<p style='color:green'>Succès de l'accès au dossier '$dossier'.</p>\n";
		while($entree=@readdir($dir)) {
			if(is_dir($dossier.'/'.$entree)&&($entree!='.')&&($entree!='..')) {
				if(!file_exists($dossier."/".$entree."/index.html")) {
					if ($f = @fopen($dossier.'/'.$entree."/index.html", "w")) {
						if((!isset($niveau_arbo))||($niveau_arbo==1)) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../login.php")</script>');
						}
						elseif($niveau_arbo==0) {
							@fputs($f, '<script type="text/javascript">document.location.replace("./login.php")</script>');
						}
						elseif($niveau_arbo==2) {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../login.php")</script>');
						}
						else {
							@fputs($f, '<script type="text/javascript">document.location.replace("../../../login.php")</script>');
						}
						@fclose($f);
						$nb_creation++;
					}
					else {
						$retour.="<span style='color:red'>Erreur lors de la création de '$dir/$entree/index.html'.</span><br />\n";
						$nb_erreur++;
					}
				}
				else {
					$nb_fich_existant++;
				}
			}
		}

		if($nb_erreur>0) {
			$retour.="<p style='color:red'>$nb_erreur erreur(s) lors du traitement.</p>\n";
		}
		else {
			$retour.="<p style='color:green'>Aucune erreur lors de la création des fichiers index.html</p>\n";
		}
	
		if($nb_creation>0) {
			$retour.="<p style='color:green'>Création de $nb_creation fichier(s) index.html</p>\n";
		}
		else {
			$retour.="<p style='color:green'>Aucune création de fichiers index.html n'a été effectuée.</p>\n";
		}
		$retour.="<p style='color:blue'>Il existait avant l'opération $nb_fich_existant fichier(s) index.html</p>\n";
	}

	return $retour;
}


?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

  <script type="text/javascript" src="../templates/origine/lib/fonction_change_ordre_menu.js"></script>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/gestion.css" media="screen" />

<!-- corrections internet Exploreur -->
	<!--[if lte IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie.css' media='screen' />
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/bandeau_ie.css' media='screen' />
	<![endif]-->
	<!--[if lte IE 6]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie6.css' media='screen' />
	<![endif]-->
	<!--[if IE 7]>
		<link title='bandeau' rel='stylesheet' type='text/css' href='../templates/origine/css/accueil_ie7.css' media='screen' />
	<![endif]-->


<!-- Style_screen_ajout.css -->
	<?php
		if (count($Style_CSS)) {
			foreach ($Style_CSS as $value) {
				if ($value!="") {
					echo "<link rel=\"$value[rel]\" type=\"$value[type]\" href=\"$value[fichier]\" media=\"$value[media]\" />\n";
				}
			}
		}
	?>

<!-- Fin des styles -->


</head>


<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<body onload="show_message_deconnexion();<?php echo $tbs_charger_observeur;?>">

<!-- on inclut le bandeau -->
	<?php include('../templates/origine/bandeau_template.php');?>

<!-- fin bandeau_template.html      -->

  <div id='container'>

<?php
	if(isset($_GET['ajout_index_documents'])) {
		echo ajout_index_sous_dossiers("../documents");

		$sql="SELECT * FROM infos_actions WHERE titre='Contrôle des index dans les documents des CDT requis';";
		$res_test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test)>0) {
			while($lig_ia=mysqli_fetch_object($res_test)) {
				$sql="DELETE FROM infos_actions_destinataires WHERE id_info='$lig_ia->id';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
				if($del) {
					$sql="DELETE FROM infos_actions WHERE id='$lig_ia->id';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
				}
			}
		}

	}
?>

	<form action="index.php" id="form1" method="post">
	  <p class="center">
<?php
echo add_token_field();
?>
		<input type="submit" value="Enregistrer" />
	  </p>
	<h2>Activation des cahiers de textes</h2>
	  <p class="italic">
		  La désactivation des cahiers de textes n'entraîne aucune suppression des données.
		  Lorsque le module est désactivé, les professeurs n'ont pas accès au module et la consultation
		  publique des cahiers de textes est impossible.
	  </p>
	<fieldset class="no_bordure">
	  <legend class="invisible">Activation</legend>

		<input type="radio"
				 name="activer"
				 id="activer_y"
				 value="y"
			 onchange='changement();'
				<?php if (getSettingValue("active_cahiers_texte")=='y') echo " checked='checked'"; ?> />
		<label for='activer_y' style='cursor: pointer;'>
		  Activer les cahiers de textes (<em>consultation et édition</em>)
		</label>
	  <br />
		<input type="radio" 
				 name="activer" 
				 id="activer_n" 
				 value="n"
			 onchange='changement();'
				<?php if (getSettingValue("active_cahiers_texte")=='n') echo " checked='checked'"; ?> />
		<label for='activer_n' style='cursor: pointer;'>
		  Désactiver les cahiers de textes (<em>consultation et édition</em>)
		</label>
	  </fieldset>
	  
	  
	  <h2>Version des cahiers de textes</h2>
<?php $extensions = get_loaded_extensions();
  if(!in_array('pdo_mysql',$extensions)) {
?>
	  <p>
		<span style='color:red'>
		  ATTENTION
		</span>
	  Il semble que l'extension php 'pdo_mysql' ne soit pas présente.
	  <br />
	  Cela risque de rendre impossible l'utilisation de la version 2 du cahier de texte";
	  </p>
<?php
  }
  ?>
	  <p class="italic">
		La version 2 du cahier de texte necessite php 5.2.x minimum
	  </p>
	  <fieldset class="no_bordure">
		<legend class="invisible">Version</legend>
		<input type="radio"
				 name="version"
				 id="version_1"
				 value="1"
			 onchange='changement();'
				<?php if (getSettingValue("GepiCahierTexteVersion")=='1') echo " checked='checked'"; ?> />
		<label for='version_1' style='cursor: pointer;'>
		  Cahier de texte version 1
		</label>
		<!--
		(<span class="italic">
		  le cahier de texte version 1 ne sera plus supporté dans la future version 1.5.3
		</span>)
		-->
		<br />
		  <input type="radio"
				 name="version"
				 id="version_2"
				 value="2"
			 onchange='changement();'
				<?php if (getSettingValue("GepiCahierTexteVersion")=='2') echo " checked='checked'"; ?> />
		<label for='version_2' style='cursor: pointer;'>
		  Cahier de texte version 2
		</label>
	  </fieldset>
	  
	  <h2>Début et fin des cahiers de textes</h2>
	  <p class="italic">
		Seules les rubriques dont la date est comprise entre la date de début et la date de fin des cahiers
		de textes sont visibles dans l'interface de consultation publique.
		<br />
		L'édition (<em>modification/suppression/ajout</em>) des cahiers de textes par les utilisateurs de GEPI
		n'est pas affectée par ces dates.
	  </p>
	  <fieldset class="no_bordure">
		<legend class="invisible">Version</legend>
        Date de début des cahiers de textes :
<?php
        $bday = strftime("%d", getSettingValue("begin_bookings"));
        $bmonth = strftime("%m", getSettingValue("begin_bookings"));
        $byear = strftime("%Y", getSettingValue("begin_bookings"));
        genDateSelector("begin_", $bday, $bmonth, $byear,"more_years")
?>
	  <br />
        Date de fin des cahiers de textes :
<?php
        $eday = strftime("%d", getSettingValue("end_bookings"));
        $emonth = strftime("%m", getSettingValue("end_bookings"));
        $eyear= strftime("%Y", getSettingValue("end_bookings"));
        genDateSelector("end_",$eday,$emonth,$eyear,"more_years")
?>
		<input type="hidden" name="is_posted" value="1" />
	  </fieldset>

	  <h2>Accès public</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">accès public</legend>
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_n' 
				 value='no'
			 onchange='changement();'
				<?php if (getSettingValue("cahier_texte_acces_public") == "no") echo " checked='checked'";?> /> 
		<label for='cahier_texte_acces_public_n' style='cursor: pointer;'>
		  Désactiver la consultation publique des cahiers de textes 
		  (<em>seuls des utilisateurs logués pourront y avoir accès en consultation, s'ils y sont autorisés</em>)
		</label>
	  <br />
		  <input type='radio' 
				 name='cahier_texte_acces_public' 
				 id='cahier_texte_acces_public_y' 
				 value='yes'
			 onchange='changement();'
				<?php if (getSettingValue("cahier_texte_acces_public") == "yes") echo " checked='checked'";?> /> 
		<label for='cahier_texte_acces_public_y' style='cursor: pointer;'>
		  Activer la consultation publique des cahiers de textes 
		  (<em>tous les cahiers de textes visibles directement, ou par la saisie d'un login/mdp global</em>)
		</label>
	  </fieldset>
	  <p>
		-&gt; Accès à l'<a href='../public/index.php?id_classe=-1'>interface publique de consultation des cahiers de textes</a>
	  </p>
	  <p class="italic">
		En l'absence de mot de passe et d'identifiant, l'accès à l'interface publique de consultation 
		des cahiers de textes est totalement libre.
	  </p>
	  <p>
		Identifiant :
		<input type="text" 
			   name="cahiers_texte_login_pub"
			 onchange='changement();'
			 title="Identifiant"
			   value="<?php echo getSettingValue("cahiers_texte_login_pub"); ?>" 
			   size="20" />
	  </p>
	  <p>
		Mot de passe :
		<input type="text" 
			   name="cahiers_texte_passwd_pub"
			 onchange='changement();'
			 title="Mot de passe"
			   value="<?php echo getSettingValue("cahiers_texte_passwd_pub"); ?>" 
			   size="20" />
	  </p>

	  <h2>Délai de visualisation des devoirs</h2>
	  <p class="italic">
		Indiquez ici le délai en jours pendant lequel les devoirs seront visibles, à compter du jour de
		visualisation sélectionné, dans l'interface publique de consultation des cahiers de textes.
		<br />
		Mettre la valeur 0 si vous ne souhaitez pas activer le module de remplissage des devoirs.
		Dans ce cas, les professeurs font figurer les devoirs à faire dans la même case que le contenu des
		séances.
	  </p>
	  <p>
		Délai :
		<input type="text"
			   name="delai_devoirs"
			   id="delai_devoirs"
			 onchange='changement();'
			 title="Délai des devoirs"
			 onKeyDown="clavier_2(this.id,event,0,365);"
			 AutoComplete="off"
			 title="Délai des devoirs : Vous pouvez le modifier à l'aide des flèches Up et Down du pavé de direction."
 			   value="<?php echo getSettingValue("delai_devoirs"); ?>"
			   size="2" />
		jours
	  </p>

	  <h2>Possibilités sur les documents joints</h2>
	  <p>
		<input type="checkbox"
			   name="cdt_possibilite_masquer_pj"
			   id="cdt_possibilite_masquer_pj"
			   onchange='changement();'
			   title="Visibilité des documents joints"
			   value="y"
		       <?php if(getSettingValue("cdt_possibilite_masquer_pj")=="y") {echo " checked";} ?>
			   />
		<label for='cdt_possibilite_masquer_pj'> Possibilité pour les professeurs de cacher aux élèves et responsables les documents joints aux Cahiers de textes.</label>
	  </p>

	  <p>
		<input type="checkbox"
			   name="cdt_afficher_volume_docs_joints"
			   id="cdt_afficher_volume_docs_joints"
			   onchange='changement();'
			   title="Volume total des documents joints"
			   value="y"
		       <?php if(getSettingAOui("cdt_afficher_volume_docs_joints")) {echo " checked";} ?>
			   />
		<label for='cdt_afficher_volume_docs_joints'> Afficher dans le CDT le volume total de documents joints.</label>
	  </p>

	  <h2>Visa des cahiers de texte</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">Visa</legend>
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_y'
				 value='yes'
			 onchange='changement();'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "yes") echo " checked='checked'";?> />
		<label for='visa_cdt_inter_modif_notices_visees_y' style='cursor: pointer;'>
		 Activer l'interdiction pour les enseignants de modifier une notice antérieure à la date fixée lors du visa de leur cahier de textes.
		</label>
	  <br />
		  <input type='radio'
				 name='visa_cdt_inter_modif_notices_visees'
				 id='visa_cdt_inter_modif_notices_visees_n'
				 value='no'
			 onchange='changement();'
			   <?php if (getSettingValue("visa_cdt_inter_modif_notices_visees") == "no") echo " checked='checked'";?> />
		<label for='visa_cdt_inter_modif_notices_visees_n' style='cursor: pointer;'>
		  Désactiver l'interdiction pour les enseignants de modifier une notice après la signature
		  des cahiers de textes
		</label>
	  </fieldset>


	  <h2>Cahiers de texte en commun</h2>
	  <fieldset class="no_bordure">
		<legend class="invisible">Cahiers de texte en commun</legend>
			<p>Dans le CDT2, par défaut, un professeur ne peut pas modifier une notice/devoir réalisé par un collègue, même si il s'agit d'un enseignement partagé (<i>plusieurs professeurs devant un même groupe d'élèves</i>).<br />
			Pour modifier ce paramétrage&nbsp;:</p>
		  <input type='radio'
				 name='cdt_autoriser_modif_multiprof'
				 id='cdt_autoriser_modif_multiprof_y'
				 value='yes'
			 onchange='changement();'
			   <?php if (getSettingValue("cdt_autoriser_modif_multiprof") == "yes") {echo " checked='checked'";}?> />
		<label for='cdt_autoriser_modif_multiprof_y' style='cursor: pointer;'>
		  Autoriser les collègues travaillant en binome sur un enseignement à modifier les notices/devoirs créés par leur collègue.
		</label>
	  <br />
		  <input type='radio'
				 name='cdt_autoriser_modif_multiprof'
				 id='cdt_autoriser_modif_multiprof_n'
				 value='no'
			 onchange='changement();'
			   <?php if ((getSettingValue("cdt_autoriser_modif_multiprof") == "no")||(getSettingValue("cdt_autoriser_modif_multiprof") == "")) {echo " checked='checked'";}?> />
		<label for='cdt_autoriser_modif_multiprof_n' style='cursor: pointer;'>
		  Interdire la modification de notice/devoir créés par leur collègue.
		</label>
	  </fieldset>


	  <p class="center">
		<input type="submit" value="Enregistrer" />
	  </p>
	</form>

	<hr />
	
	<h2>Gestion des cahiers de textes</h2>
	<ul>
	  <li><a href='modify_limites.php'>Espace disque maximal, taille maximale d'un fichier</a></li>
	  <li><a href='modify_type_doc.php'>Types de fichiers autorisés en téléchargement</a></li>
	  <li><a href='admin_ct.php'>Administration des cahiers de textes</a> (<em>recherche des incohérences, modifications, suppressions</em>)</li>
	  <li><a href='visa_ct.php'>Viser les cahiers de textes</a> (<em>Signer les cahiers de textes</em>)</li>
	  <li><a href='index.php?ajout_index_documents=y'>Protéger les sous-dossiers de 'documents/' contre des accès anormaux</a></li>
	  <li><a href='../cahier_texte_2/archivage_cdt.php'>Archivage des cahiers de textes en fin d'année scolaire</a></li>
	  <li><a href='../cahier_texte_2/export_cdt.php'>Export de cahiers de textes et accès inspecteur (<em>sans authentification</em>)</a></li>
	  <li><a href='../cahier_texte_2/correction_notices_cdt_formules_maths.php'>Téléchargement des images de formules mathématiques et correction des notices en conséquence</a></li>
	</ul>
	
	<hr />
	
	<h2>Astuce</h2>
	<p>
	  Si vous souhaitez n'utiliser que le module Cahier de textes dans Gepi, consultez la page suivante&nbsp;:
	  <br />
	  <a href='http://www.sylogix.org/projects/gepi/wiki/Use_only_cdt'>
		http://www.sylogix.org/projects/gepi/wiki/Use_only_cdt
	  </a>
	</p>

	<hr />

	<h2>Rappel du B.O.</h2>

	<?php
		require("../lib/textes.inc.php");
		echo $cdt_texte_bo;
	?>

<!-- Début du pied -->
	<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>


	<script type='text/javascript'>
		temporisation_chargement='ok';
	</script>

</div>

		<?php
			if ($tbs_microtime!="") {
				echo "
   <p class='microtime'>Page générée en ";
   			echo $tbs_microtime;
				echo " sec</p>
   			";
	}
?>

		<?php
			if ($tbs_pmv!="") {
				echo "
	<script type='text/javascript'>
		//<![CDATA[
   			";
				echo $tbs_pmv;
				echo "
		//]]>
	</script>
   			";
		}
?>

</body>
</html>



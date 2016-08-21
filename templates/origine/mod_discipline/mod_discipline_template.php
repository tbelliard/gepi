<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
/*
* $Id$
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
*
* ******************************************** *
* Appelle les sous-modèles                     *
* templates/origine/header_template.php        *
* templates/origine/bandeau_template.php       *
* ******************************************** *
*/
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">

<head>
<!-- on inclut l'entête -->
	<?php
	  $tbs_bouton_taille = "..";
	  include('../templates/origine/header_template.php');
	?>

	<link rel="stylesheet" type="text/css" href="../templates/origine/css/accueil.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../templates/origine/css/bandeau.css" media="screen" />


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


	<a name="contenu" class="invisible">Début de la page</a>
	
	<p class="center">
	  Ce module est destiné à saisir et suivre les <?php echo $mod_disc_terme_incident;?>s et <?php echo $mod_disc_terme_sanction;?>s.
	</p>

<!-- début corps menu	-->
<?php
	if (count($menuTitre)) {
		foreach ($menuTitre as $newEntreeMenu) {
?>
		<h2 class="<?php echo $newEntreeMenu->classe ?>">
			<img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?>
		</h2>


<?php
		if (count($menuPage)) {
			foreach ($menuPage as $newentree) {
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {

?>
				<div class='div_tableau'>
				  <h3 class="colonne ie_gauche">
					  <a href="<?php echo "../".mb_substr($newentree->chemin,1) ?>">
						  <?php echo $newentree->titre ?>
					  </a>
				  </h3>
				  <p class="colonne ie_droite">
					  <?php echo $newentree->expli ?>
				  </p>
				</div>
<?php
			  }
			}
		}

	  }
	}

	$sql="SELECT ssc.*, ss.*, sr.*, si.etat FROM s_sanctions_check ssc, s_sanctions ss, s_retenues sr, s_incidents si WHERE ssc.login='".$_SESSION['login']."' AND ss.id_sanction=ssc.id_sanction AND ss.id_sanction=sr.id_sanction AND ss.id_incident=si.id_incident AND si.etat!='clos' ORDER BY date, heure_debut;";
	//echo "$sql<br />";
	$res_sanction=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_sanction)>0) {
		require_once("sanctions_func_lib.php");
		echo "<div align='center'>
	<div class='fieldset_opacite50' style='margin:1em; padding:0.5em; width:70%; text-align:center;'>
		<p>La validation de l'état effectué ou non d'une ou plusieurs retenues (<em>ou ".$mod_disc_terme_sanction." assimilée</em>) vous est déléguée.<br />
		Lorsque la retenue aura été effectuée, signalez-le aux CPE, déclarant... en cliquant dans la colonne <strong>Effectuée</strong></p>";

		$retour="<div align='center'>";
		$retour.="<table class='boireaus boireaus_alt' border='1' summary='Retenues déléguées' style='margin:2px; text-align:center;'>\n";
		$retour.="<tr>\n";
		$retour.="<th>Élève</th>\n";
		$retour.="<th>Nature</th>\n";
		$retour.="<th>Date</th>\n";
		$retour.="<th>Heure</th>\n";
		$retour.="<th>Durée</th>\n";
		$retour.="<th>Lieu</th>\n";
		$retour.="<th>Travail</th>\n";
		$retour.="<th>Effectuée</th>\n";
		$retour.="</tr>\n";
		while($lig_sanction=mysqli_fetch_object($res_sanction)) {
			$id_incident=$lig_sanction->id_incident;
			$etat_incident=$lig_sanction->etat;

			$retour.="<tr>\n";
			$retour.="<td>".get_nom_prenom_eleve($lig_sanction->login, 'avec_classe')."</td>\n";
			if(($etat_incident!='clos')&&(($_SESSION['statut']!='professeur')&&($_SESSION['statut']!='autre'))) {
				$retour.="<td><a href='saisie_sanction.php?mode=modif&amp;valeur=$lig_sanction->id_nature_sanction&amp;id_sanction=$lig_sanction->id_sanction&amp;id_incident=$id_incident&amp;ele_login=$ele_login'>".ucfirst($lig_sanction->nature)."</a></td>\n";
			}
			else {
				$retour.="<td>".ucfirst($lig_sanction->nature)."</td>\n";
			}
			$retour.="<td>".formate_date($lig_sanction->date)."</td>\n";
			$retour.="<td>$lig_sanction->heure_debut</td>\n";
			$retour.="<td>$lig_sanction->duree</td>\n";
			$retour.="<td>$lig_sanction->lieu</td>\n";
			//$retour.="<td>".nl2br($lig_sanction->travail)."</td>\n";
		
			$retour.="<td>";

			$tmp_doc_joints=liste_doc_joints_sanction($lig_sanction->id_sanction);
			if(($lig_sanction->travail=="")&&($tmp_doc_joints=="")) {
				$texte="Aucun travail";
			}
			else {
				$texte=nl2br($lig_sanction->travail);
				if($tmp_doc_joints!="") {
					if($texte!="") {$texte.="<br />";}
					$texte.=$tmp_doc_joints;
				}
			}

			$tabdiv_infobulle[]=creer_div_infobulle("div_travail_sanction_$lig_sanction->id_sanction","Travail (".$mod_disc_terme_sanction." n°$lig_sanction->id_sanction)","",$texte,"",20,0,'y','y','n','n',2);

			$retour.=" <a href='#' onmouseover=\"document.getElementById('div_travail_sanction_$lig_sanction->id_sanction').style.zIndex=document.getElementById('sanctions_incident_$id_incident').style.zIndex+1;delais_afficher_div('div_travail_sanction_$lig_sanction->id_sanction','y',10,-40,$delais_affichage_infobulle,$largeur_survol_infobulle,$hauteur_survol_infobulle);\" onclick=\"return false;\">Détails</a>";
			$retour.="</td>\n";

			// Sanction effectuée
			if($etat_incident=='clos') {
				$retour.="<td>";
				if($lig_sanction->effectuee=="O") {$retour.="<span style='color:green'>O</span>";} else {$retour.="<span style='color:red'>N</span>";}
				$retour.="</td>\n";
			}
			else {
				$retour.="<td title=\"Cliquez pour marquer la sanction comme effectuée ou non effectuée\"";
				if($lig_sanction->effectuee=="O") {
					$valeur_alt="N";
				}
				else {
					$valeur_alt="O";
				}

				$retour.="<a href='#' onclick=\"maj_etat_sanction_effectuee_ou_non($lig_sanction->id_sanction, '$valeur_alt')\">";
				$retour.="<span id='span_sanction_effectuee_".$lig_sanction->id_sanction."'>";
				if($lig_sanction->effectuee=="O") {
					$retour.="<span style='color:green'> O </span>";
				}
				else {
					$retour.="<span style='color:red'> N </span>";
				}
				$retour.="</span>";
				$retour.="</a>";
				$retour.="</td>\n";
			}
			$retour.="</tr>\n";
		}
		$retour.="</table>\n";
		$retour.="</div>\n";
		echo $retour;

		echo "<script type='text/javascript'>
	function maj_etat_sanction_effectuee_ou_non(id_sanction) {
		new Ajax.Updater($('span_sanction_effectuee_'+id_sanction),'ajax_discipline.php?id_sanction='+id_sanction+'&modif_sanction=etat_effectuee".add_token_in_url(false)."',{method: 'get'});
	}
</script>";

		echo "
	</div>
</div>";
	}
?>

<!-- Fin menu	général -->
<p>
  <em>NOTES&nbsp;</em>
</p>
<ul>
  <li>
	<p>
	  Une fois un <?php echo $mod_disc_terme_incident;?> clos, il ne peut plus être modifié et aucune <?php echo $mod_disc_terme_sanction;?> liée ne peut être ajoutée/modifiée/supprimée.<br />
	  En revanche, il est possible de rouvrir si nécessaire l'<?php echo $mod_disc_terme_incident;?>.
	</p>
  </li>
  <li>
	<p>
	  Le module ne conserve pas un historique des modifications d'un <?php echo $mod_disc_terme_incident;?>.<br />Si plusieurs personnes modifient un <?php echo $mod_disc_terme_incident;?>, elles doivent le faire en bonne intelligence.
	</p>
  </li>
  <li>
	<p>Un professeur peut saisir un <?php echo $mod_disc_terme_incident;?>, mais ne peut pas saisir les <?php echo $mod_disc_terme_sanction;?>s sauf paramétrage contraire dans la page de définition des <?php echo $mod_disc_terme_sanction;?>s.<br />
Un professeur ne peut modifier que les <?php echo $mod_disc_terme_incident;?>s (<em>non clos</em>) qu'il a lui-même déclaré.<br />Il ne peut consulter que les <?php echo $mod_disc_terme_incident;?>s (<em>et leurs suites</em>) qu'il a déclarés, ou dont il est protagoniste, ou encore dont un des élèves, dont il est professeur principal, est protagoniste.
	</p>
  </li>
  <li>
	<p>
	  <em style='color:red'>A FAIRE:</em>
	  Ajouter des tests 'changement()' dans les pages de saisie pour ne pas quitter une étape sans enregistrer.
	  C'est fait au moins en partie... contrôler plus à fond.
	</p>
  </li>
  <li>
	<p>
	  <em style='color:red'>A FAIRE:</em>
	  Permettre d'archiver les <?php echo $mod_disc_terme_incident;?>s/<?php echo $mod_disc_terme_sanction;?>s d'une année.
	</p>
  </li>
</ul>

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



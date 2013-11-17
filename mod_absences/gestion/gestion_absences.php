<?php
/*
* $Id$
*
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
//require_once("../../lib/initialisations.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//************** FIN EN-TETE ***************


?>

<?php /* page liste absences */ 
include "gestion_absences_liste.php";?>
<?php /* fiche élève sélection */ ?>
<?php if ( $fiche_eleve != '' ) {

         $cpt_liste = 0;
         $requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.nom  LIKE '".$fiche_eleve."%' GROUP BY login ORDER BY nom, prenom";
         $execution_liste_fiche = mysqli_query($GLOBALS["mysqli"], $requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
         while ( $data_liste_fiche = mysqli_fetch_array($execution_liste_fiche))
          {
              $login_liste[$cpt_liste] = $data_liste_fiche['login'];
              $nom_liste[$cpt_liste] = strtoupper($data_liste_fiche['nom']);
              $prenom_liste[$cpt_liste] = ucfirst($data_liste_fiche['prenom']);
              $cpt_liste = $cpt_liste + 1;
          }
?>
<br />
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center; margin: auto;">
    <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
       <tr class="fond_rouge">
           <td class="titre_tableau_gestion"><b>Liste des élèves</b></td>
       </tr>
       <?php $cpt_aff_liste = 0; $ic = 1;
             while ($cpt_aff_liste < $cpt_liste)
              { if ($ic==1) { $ic=2; $couleur_cellule="td_tableau_absence_1"; } else { $couleur_cellule="td_tableau_absence_2"; $ic=1; }
              ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
                      <td class="norme_absence_min"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $login_liste[$cpt_aff_liste]; ?>"><?php echo $nom_liste[$cpt_aff_liste]." ".$prenom_liste[$cpt_aff_liste]." - ".classe_de($login_liste[$cpt_aff_liste]); ?></a></td>
                  </tr>
             <?php $cpt_aff_liste = $cpt_aff_liste + 1; } ?>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>
<?php /* fiche élève */ 
if ( $select_fiche_eleve != '' ) {

	$requete_liste_fiche = "SELECT * FROM ".$prefix_base."eleves WHERE ".$prefix_base."eleves.login = '".$select_fiche_eleve."'";
	$execution_liste_fiche = mysqli_query($GLOBALS["mysqli"], $requete_liste_fiche) or die('Erreur SQL !'.$requete_liste_fiche.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while ( $data_liste_fiche = mysqli_fetch_array($execution_liste_fiche))
	{

		$login_eleve = $data_liste_fiche['login'];
		$id_eleve_photo = $data_liste_fiche['elenoet'];
		$ele_id_eleve = $data_liste_fiche['ele_id'];
		$nom_eleve = strtoupper($data_liste_fiche['nom']);
		$prenom_eleve = ucfirst($data_liste_fiche['prenom']);
		$naissance_eleve = date_fr(date_sql(affiche_date_naissance($data_liste_fiche['naissance'])));
		$date_de_naissance = $data_liste_fiche['naissance'];
		$sexe_eleve = $data_liste_fiche['sexe'];
		$responsable_eleve = tel_responsable($ele_id_eleve);

	}
?>

    <br />

<?php /* fiche identitée de l'élève */ ?>
<a name="ident"></a>
<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Identité élève</div>
<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
	<div style="height: 175px; background: transparent url(../images/grid_10.png)">
		<div style="float: left; margin: 12.5px;">
	<?php
	if (getSettingValue("active_module_trombinoscopes")=='y') {
		$nom_photo = '';
		$nom_photo = nom_photo($id_eleve_photo,"eleves",2);
		$photos = $nom_photo;

		//if ( $nom_photo === '' or !file_exists($photos) ) {
		if ( $nom_photo === NULL or !file_exists($photos) ) {
			$photos = "../../mod_trombinoscopes/images/trombivide.jpg";
		}
		$valeur = redimensionne_image($photos);
    ?>
			<img src="<?php echo $photos; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" />
	<?php
    } ?>
		</div>
		<div style="float: left; margin: 12.5px 0px 0px 0px; width: 36%">
			Nom : <strong><?php echo $nom_eleve; ?></strong><br />
			Prénom : <strong><?php echo $prenom_eleve; ?></strong><br />
			Date de naissance : <?php echo $naissance_eleve; ?><br />
			Age : <strong><?php echo age($date_de_naissance); ?> ans</strong><br />
			<br />
			Classe : <a href="#" class="info"><?php echo classe_de($login_eleve); ?><span style="width: 300px;">(Suivi par : <?php echo pp(classe_court_de($login_eleve)); ?>)</span></a>
		</div>
		<div style="float: left; background-image: url(../images/responsable.png); background-repeat:no-repeat; height: 175px; width: 20px; margin-left: 10px;">&nbsp;</div>
		<div style="float: left; margin: 12.5px; overflow: auto;  width: 40%;">
	<?php
		// L'affichage des responsables : le 1 est en rouge et est identifié comme tel
		$cpt_responsable = 0;
		while ( !empty($responsable_eleve[$cpt_responsable]) )
		{
			if ($responsable_eleve[$cpt_responsable]['resp_legal'] == 1) {
				$style = ' style="color: red;"';
				$text = '(resp. 1)';
			}else {
				if ($responsable_eleve[$cpt_responsable]['resp_legal'] == 2) {
					$style='';
					$text = '(resp. 2)';
				} else{
					$style ='';
					$text = '(autres)';
			    }
			}
			echo '<span' . $style . '>' . $responsable_eleve[$cpt_responsable]['civilite']. ' '
					.strtoupper($responsable_eleve[$cpt_responsable]['nom']).' '
					.ucfirst($responsable_eleve[$cpt_responsable]['prenom']) . $text . '</span>';
			$telephone = '';
			if ( !empty($responsable_eleve[$cpt_responsable]['tel_pers']) ) {
				$telephone = $telephone.'Tél. <strong>'.present_tel($responsable_eleve[$cpt_responsable]['tel_pers']).'</strong> ';
			}
			if ( !empty($responsable_eleve[$cpt_responsable]['tel_prof']) ) {
				$telephone = $telephone.'Prof. <strong>'.present_tel($responsable_eleve[$cpt_responsable]['tel_prof']).'</strong> ';
			}
			if ( !empty($responsable_eleve[$cpt_responsable]['tel_port']) ) {
				$telephone = $telephone.'<br />Port.<img src="../images/attention.png" alt="Attention numéro surtaxé" title="Attention numéro surtaxé" border="0" height="14" width="14" /> '.present_tel($responsable_eleve[$cpt_responsable]['tel_port']);
			}
			//ajout adresse didier
			if ( !empty($responsable_eleve[$cpt_responsable]['adr1']) ) {
            	$telephone = $telephone.'<br />Adr. <strong>'.$responsable_eleve[$cpt_responsable]['adr1'].'&nbsp;</strong> '; }
			if ( !empty($responsable_eleve[$cpt_responsable]['cp']) ) {
        		$telephone = $telephone.'-&nbsp;<strong>'.$responsable_eleve[$cpt_responsable]['cp'].'&nbsp;</strong> '; }
			if ( !empty($responsable_eleve[$cpt_responsable]['commune']) ) {
			$telephone = $telephone.'<strong>'.$responsable_eleve[$cpt_responsable]['commune'].'<br /></strong> '; }

			echo '<div style="margin-left: 0px; font-size: 13px;">'.$telephone.'</div>';

			$cpt_responsable = $cpt_responsable + 1;
		}
			?>
		</div>
	</div>
</div>
<?php /* fin fiche identitée de l'élève */ ?>
<!--
Pour éviter un centrage bizarre:
-->
<div style='clear: both;'>&nbsp;</div>
<?php /* ajout impressions didier */ ?>
    <div style="text-align: center;">
	[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=suivieleve#suivieleve" title="consulter le suivi de l'élève">Suivi de l'élève</a> |
	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=abseleve#abseleve" title="consulter l'absentéisme non justifié">Absentéisme non justifié</a> | 
	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=tableauannuel" title="consulter la fiche de l'élève">Statistique annuelle</a> | 
	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=suivieleve#tab_sem_abs" title="Répartissement des absences">Répartition des absences</a> |
	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=impbilan#impbilan" title="Impression bilan des absences">Impression bilan des absences </a> |
	<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;aff_fiche=impfic#impfic" title="Impression fiche récapitulative">Impression fiche récapitulative </a> ]
    </div><br />

<?php /* DIV global */ ?>
<div style="margin: auto; position: relative;">

<?php /* DIV coté Gauche */ ?>
	<div style="float: left; width: 370px;">

<?php /* DIV du suivi de l'élève */ ?>
	   <?php if ( $aff_fiche==='suivieleve' or $aff_fiche==='' ) { ?>
		<a name="suivieleve"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Actualité élève</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">

			<?php /* formulaire pour l'ajout de l'actualitée de l'élève */ ?>
			<a name="formulaire"></a>
		            <form method="post" action="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>">
		               <fieldset>
		                 <legend>Ajouter un suivi</legend>
			                 <select id="info_suivi" onchange="data_info_suivi.value += info_suivi.options[info_suivi.selectedIndex].text + '\n'" style="width: 210px;">
			                   <option>Sélectionné un texte rapide</option>
					  		   <option>[Exclusion du cours] A été exclus du cours de:   à:</option>
			                   <option>Rencontre avec les parents</option>
			                 </select>
				         <input type="hidden" name="eleve_suivi_eleve_cpe" value="<?php echo $login_eleve; ?>" />
			                 <input type="hidden" name="debut_selection_suivi" value="<?php echo $debut_selection_suivi; ?>" />
			                 <input type="hidden" name="action_sql" value="<?php if($action == "modifier") { ?>modifier<?php } else { ?>ajouter<?php } ?>" />
			                 <?php if($action === 'modifier') { ?>
			                      <input type="hidden" name="id_suivi_eleve_cpe" value="<?php echo $id_suivi_eleve_cpe; ?>" />
			                 <?php } ?>
<?php /* 		   <input type="hidden" name="uid_post" value="<?php echo my_ereg_replace(' ','%20',$uid); ?>" /> */ ?>
			                 <input type="submit" name="submit8" value="Valider la saisie" />
			                 <br />
					 <table style="border: 0px" cellspacing="1" cellpadding="1">
					    <tr>
						<td>
						<textarea id="data_info_suivi" name="data_info_suivi" rows="3" cols="28" style="height: 70px;"><?php if($action == "modifier") { echo $data_modif_fiche['komenti_suivi_eleve_cpe']; } ?></textarea>
						</td>
						<td>
						<div style="font-family: Arial; font-size: 0.8em; background-color: #FFFFFF; border : 1px solid #0061BD; height: 70px; padding-left: 2px; width: 100px;">
							Niveau de priorité<br />
							<input name="niveau_urgent" id="nur1" value="1" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='1') { ?>checked="checked"<?php } else { ?>checked="checked"<?php } ?> /><label for="nur1" style="cursor: pointer;">Information</label><br />
							<input name="niveau_urgent" id="nur2" value="2" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='2') { ?>checked="checked"<?php } ?> /><label for="nur2" style="cursor: pointer;">Important</label><br />
							<input name="niveau_urgent" id="nur3" value="3" type="radio" <?php if(!empty($data_modif_fiche['niveau_message_suivi_eleve_cpe']) and $data_modif_fiche['niveau_message_suivi_eleve_cpe']==='3') { ?>checked="checked"<?php } ?> /><label for="nur3" style="cursor: pointer;">Prioritaire</label><br />
						</div>
						</td>
					    </tr>
				        </table>
				  	Entraine une action
					<select name="action_suivi" style="width: 218px;">
 	        		        <?php
					      $requete_liste_action = "SELECT init_absence_action, def_absence_action FROM ".$prefix_base."absences_actions ORDER BY init_absence_action ASC";
		        	              $resultat_liste_action = mysqli_query($GLOBALS["mysqli"], $requete_liste_action) or die('Erreur SQL !'.$requete_liste_action.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			                      while ( $data_liste_action = mysqli_fetch_array($resultat_liste_action)) { ?>
		                                     <option value="<?php echo $data_liste_action['init_absence_action']; ?>" <?php if(!empty($data_modif_fiche['action_suivi_eleve_cpe']) and $data_modif_fiche['action_suivi_eleve_cpe'] === $data_liste_action['init_absence_action']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_action['init_absence_action']." - ".$data_liste_action['def_absence_action']; ?></option>
		                              <?php } ?>
					</select><br />
					Méthode&nbsp;:
						<input name="support_suivi_eleve_cpe" id="ppar1" value="1" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '1') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar1" style="cursor: pointer;">Oralement</label>
						<input name="support_suivi_eleve_cpe" id="ppar2" value="2" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '2') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar2" style="cursor: pointer;">Tél.</label>
						<input name="support_suivi_eleve_cpe" id="ppar3" value="3" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '3') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar3" style="cursor: pointer;">Fax</label>
						<input name="support_suivi_eleve_cpe" id="ppar5" value="5" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '5') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar5" style="cursor: pointer;">Mel</label>
						<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input name="support_suivi_eleve_cpe" id="ppar4" value="4" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '4') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('afficher')" /><label for="ppar4" style="cursor: pointer;">Courrier</label>
						<input name="support_suivi_eleve_cpe" id="ppar6" value="6" type="radio" <?php if(!empty($data_modif_fiche['support_suivi_eleve_cpe']) and $data_modif_fiche['support_suivi_eleve_cpe'] === '6') { ?>checked="checked"<?php } ?> onclick="javascript:aff_lig_type_courrier('cacher')" /><label for="ppar6" style="cursor: pointer;">Document de liaison</label>


					<div id='ligne_type_courrier'>
					   <select name="lettre_type" size="6" style="width: 350px; border: 1px solid #000000;">
						<optgroup label="Type de lettre">
					        <?php
						$requete_lettre ="SELECT * FROM ".$prefix_base."lettres_types ORDER BY categorie_lettre_type ASC, titre_lettre_type ASC";
					        $execution_lettre = mysqli_query($GLOBALS["mysqli"], $requete_lettre) or die('Erreur SQL !'.$requete_lettre.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				  		while ($donner_lettre = mysqli_fetch_array($execution_lettre))
		  				{
						   ?><option value="<?php echo $donner_lettre['id_lettre_type']; ?>" <?php if (isset($lettre_type) and $lettre_type === $donner_lettre['id_lettre_type']) { ?>selected="selected"<?php } ?>><?php echo ucfirst($donner_lettre['titre_lettre_type']); ?></option><?php echo "\n";
						} ?>
						</optgroup>
					  </select>
					</div>

				<script type='text/javascript'>
					test='cacher';
					function aff_lig_type_courrier(mode){
						if(mode=='afficher'){
							document.getElementById('ligne_type_courrier').style.display='';
						} else {
								document.getElementById('ligne_type_courrier').style.display='none';
							}
					  	test=document.getElementById('type').value;
					}

					if(test=='cacher') {
						aff_lig_type_courrier('cacher');
					}
				</script>

              </fieldset>
            </form>



	<?php $cpt_komenti = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."'"),0); ?>
	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>
           <?php
	     $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.eleve_suivi_eleve_cpe = '".$login_eleve."' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC LIMIT ".$debut_selection_suivi.", 2";
             $execution_komenti = mysqli_query($GLOBALS["mysqli"], $requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
              while ( $data_komenti = mysqli_fetch_array($execution_komenti))
                {
			$action_pour_eleve = '';
			if(!empty($data_komenti['action_suivi_eleve_cpe']) and $data_komenti['action_suivi_eleve_cpe'] != 'A') { $action_pour_eleve = ', '.action_de($data_komenti['action_suivi_eleve_cpe']); }
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $couleur3='#FDFFEF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $couleur3='#FDFFEF'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $couleur3='#FDFFEF'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $couleur3='#FDFFEF'; $drapeau=''; } ?>
                    <div class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe'].$action_pour_eleve; ?><br /><br /><span class="dimi_texte">Ecrit par : <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />
			<?php
				// vérifie si on n'a le droit de supprimer la fiche on ne peut pas s'il y a un courrier attaché
				$autorise_supprimer = 'non';
			        $cpt_lettre_recus = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partde_lettre_suivi = 'suivi_eleve_cpe' AND partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."'"),0);
			          if( $cpt_lettre_recus === '0' ) { $autorise_supprimer = 'oui'; }
			?>
				[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action=modifier#formulaire">modifier</a> <?php if ( $autorise_supprimer === 'oui' ) { ?>|<a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action_sql=supprimer">supprimer</a><?php } ?> ] <?php /* [ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>">action</a> ] */ ?></span></div>
		<?php // courrier attaché
	        $courrier_existance = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."lettres_suivis WHERE partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."' AND partde_lettre_suivi = 'suivi_eleve_cpe'"),0);
	        if ($courrier_existance != '0') { ?>
			<?php
	               $requete_1 ="SELECT * FROM ".$prefix_base."lettres_suivis, ".$prefix_base."lettres_types WHERE partdenum_lettre_suivi = '".$data_komenti['id_suivi_eleve_cpe']."' AND partde_lettre_suivi = 'suivi_eleve_cpe' AND type_lettre_suivi = id_lettre_type";
	               $execution_1 = mysqli_query($GLOBALS["mysqli"], $requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	               while ( $data_1 = mysqli_fetch_array($execution_1)) {
			       $datation = ''; ?>
			    <div class="info_eleve_courrier" style="background: <?php echo $couleur3; ?>;"><?php if(empty($data_1['envoye_date_lettre_suivi']) or $data_1['envoye_date_lettre_suivi'] === '0000-00-00') { ?><div style="float: right; margin: 0;"><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;id_suivi_eleve_cpe=<?php echo $data_komenti['id_suivi_eleve_cpe']; ?>&amp;id_lettre_suivi=<?php echo $data_1['id_lettre_suivi']; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi; ?>&amp;action_sql=detacher_courrier">Supprimer</a></div><?php } ?><strong>Courrier attaché:</strong><br />
				Titre: <strong><?php echo $data_1['titre_lettre_type']; ?></strong><?php
				$datation = '<span title="par: '.qui($data_1['quiemet_lettre_suivi']).'">'.date_frl($data_1['emis_date_lettre_suivi']).'<small> à '.heure($data_1['emis_heure_lettre_suivi']).'</small></span>'; ?>
				<br />&nbsp;&nbsp;&nbsp;émis le: <?php echo $datation;
			      if($data_1['statu_lettre_suivi'] != 'annuler') {
				if($data_1['envoye_date_lettre_suivi'] != '0000-00-00') { $datation = '<span title="par: '.qui($data_1['quienvoi_lettre_suivi']).'">'.date_frl($data_1['envoye_date_lettre_suivi']).'<small> à '.heure($data_1['envoye_heure_lettre_suivi']).'</small></span>'; } else { $datation = 'en attente'; } ?>
				<br />&nbsp;&nbsp;&nbsp;expédié le: <?php echo $datation;
				if($data_1['reponse_date_lettre_suivi'] != '0000-00-00') { $datation = '<span title="par: '.qui($data_1['quireception_lettre_suivi']).'">'.date_frl($data_1['reponse_date_lettre_suivi']).'</span>'; } else { $datation = 'en attente'; } ?>
				<br />&nbsp;&nbsp;&nbsp;réponse reçus le: <?php echo $datation;
				       if ( !empty($data_1['reponse_remarque_lettre_suivi']) ) { ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remarque : <?php echo $data_1['reponse_remarque_lettre_suivi']; }
			        } else { ?><br />&nbsp;&nbsp;&nbsp;<span style="color: #EF000A;"><strong>Courrier annulé par <?php echo qui($data_1['quireception_lettre_suivi']); ?></strong></span><?php } ?>
			    </div>
		 <?php } ?>
		<?php } ?>
           <?php } ?>

           	<div style="text-align: center;">
	  <?php if($debut_selection_suivi!='0') { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi-'2'; ?>">Page précédente</a><?php } ?>
	  <?php $debut_selection_suivi_suivant = $debut_selection_suivi+'2'; if($debut_selection_suivi!='0' and $debut_selection_suivi_suivant<=$cpt_komenti) { ?> | <?php } ?>
	  <?php if($debut_selection_suivi_suivant<=$cpt_komenti) { ?><a href="gestion_absences.php?select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;debut_selection_suivi=<?php echo $debut_selection_suivi+'2'; ?>">Page suivant</a><?php } ?>
	</div>
		<br />
			</div>
		</div>
	</div>
	<?php } ?>
<?php /* fin du DIV du suivi de l'élève */ ?>


<?php /* DIV de l'absentéisme de l'élève */ ?>
	<?php if ( $aff_fiche === 'abseleve' ) { ?>
		<a name="abseleve"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Absentéisme de l'élève</div>
		<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">

	   <?php /* tableau des absences non justifiée */ ?>
           <?php $cpt_absences = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'A'"),0);
// modification du critère pour compter aussi les non justifs par telephone didier		
		$cpt_absences_nj = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'A' AND justify_absence_eleve != 'O'"),0);
           if ( $cpt_absences != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=A',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Absences"><?php echo $cpt_absences; ?></a></b> Absence(s)</p>
               <?php if ( $cpt_absences_nj != 0 ) { ?>
           	   Liste des absences non justifiée(s)<br />
		   <ul>
        	   <?php // modification du critère pour compter aussi les non justifs par telephone didier	
			   $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A' AND justify_absence_eleve != 'O' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
                	 $execution_absences_nr = mysqli_query($GLOBALS["mysqli"], $requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	                 while ($data_absences_nr = mysqli_fetch_array($execution_absences_nr))
        	         {
                	      ?><li><?php
                	       $cpt_lettre_absence_recus = lettre_absence_envoye($data_absences_nr['id_absence_eleve']);
                           if ( $cpt_lettre_absence_recus == 0 ) {?>
                	      <a href="ajout_abs.php?action=supprimer&amp;type=A&amp;id=<?php echo $data_absences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer l'absence" border="0" alt="" /></a>
                	      <?php
                          }else{?>
                          <img src="../images/icons/delete_imp.png" style="width: 16px; height: 16px;" title="Impossible de supprimer l'absence" border="0" alt="" />
                          <?php
                          }
                	      ?>                	      
                	      <a href="ajout_abs.php?action=modifier&amp;type=A&amp;id=<?php echo $data_absences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui">
			      <?php
					if ( $data_absences_nr['d_date_absence_eleve'] != $data_absences_nr['a_date_absence_eleve'] ) { echo 'du '.date_fr($data_absences_nr['d_date_absence_eleve']).' au '.date_fr($data_absences_nr['a_date_absence_eleve']); }
					elseif ( $data_absences_nr['d_date_absence_eleve'] === $data_absences_nr['a_date_absence_eleve'] ) { echo date_fr($data_absences_nr['d_date_absence_eleve'])." de ".$data_absences_nr['d_heure_absence_eleve']." à ".$data_absences_nr['a_heure_absence_eleve']; }
			      ?></a></li><?php
        	         }
	           ?>
		   </ul>
		<?php } ?>
	   <?php } ?>

	   <?php /* tableau des retards non justifiée */ ?>
	   <?php $cpt_retards = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'R'"),0);
		 // modification du critère pour compter aussi les non justifs par telephone didier
		 $cpt_retards_nj = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'R' AND justify_absence_eleve != 'O '"),0);

           if ( $cpt_retards != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=R',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Retards"><?php echo $cpt_retards; ?></a></b> Retards</p>
               <?php if($cpt_retards_nj != 0) { ?>
	           Liste des retards non justifié(s)<br />
		   <ul>
	           <?php // modification du critère pour compter aussi les non justifs par telephone didier	
			   $requete_retards_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R' AND justify_absence_eleve != 'O' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
        	         $execution_retards_nr = mysqli_query($GLOBALS["mysqli"], $requete_retards_nr) or die('Erreur SQL !'.$requete_retards_nr.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	                 while ($data_retards_nr = mysqli_fetch_array($execution_retards_nr))
	                 {
         	            //suppression d'un <li> inutile didier ?>
         	            <li>
         	            <a href="ajout_ret.php?action=supprimer&amp;type=A&amp;id=<?php echo $data_retards_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer le retard" border="0" alt="" /></a>
         	            <a href="ajout_ret.php?action=modifier&amp;type=R&amp;id=<?php echo $data_retards_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><?php echo date_fr($data_retards_nr['d_date_absence_eleve'])." ".$data_retards_nr['d_heure_absence_eleve']; ?></a></li><?php
                	 }
	           ?>
		   </ul>
	      <?php } ?>
	   <?php } ?>

           <?php $cpt_dispences = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'D'"),0);
		 $cpt_dispences_nj = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."' AND type_absence_eleve = 'D' AND justify_absence_eleve = 'N '"),0);

           if( $cpt_dispences != 0 ) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=D',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Dispences"><?php echo $cpt_dispences; ?></a></b> Dispenses</p>
               <?php if($cpt_dispences_nj != 0) { ?>
	           Liste des dispenses non justifiée(s)<br />
		   <ul>
	           <?php $requete_dispences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='D' AND justify_absence_eleve = 'N' ORDER BY d_date_absence_eleve DESC, d_heure_absence_eleve ASC";
        	         $execution_dispences_nr = mysqli_query($GLOBALS["mysqli"], $requete_dispences_nr) or die('Erreur SQL !'.$requete_dispences_nr.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	                 while ($data_dispences_nr = mysqli_fetch_array($execution_dispences_nr))
        	         {
                	      //suppression d'un <li> inutile didier?>
                	      <li>
                	      <a href="ajout_dip.php?action=supprimer&amp;type=A&amp;id=<?php echo $data_dispences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui" onClick="return confirm('Etes-vous sur de vouloir le supprimer...')"><img src="../images/icons/delete.png" style="width: 16px; height: 16px;" title="supprimer la dispense" border="0" alt="" /></a>
                	      <a href="ajout_dip.php?action=modifier&amp;type=D&amp;id=<?php echo $data_dispences_nr['id_absence_eleve']; ?>&amp;mode=eleve&amp;select_fiche_eleve=<?php echo $select_fiche_eleve; ?>&amp;fiche=oui"><?php echo date_fr($data_dispences_nr['d_date_absence_eleve'])." ".$data_dispences_nr['d_heure_absence_eleve']; ?></a></li><?php
	                 }
        	   ?>
		   </ul>
		<?php } ?>
	   <?php } ?>

           <?php $cpt_infirmeries = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='I'"),0);
           if($cpt_infirmeries != 0) { ?>
           <p class="titre_sous_menu"><b><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $select_fiche_eleve; ?>&amp;type=I',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title="Infirmerie"><?php echo $cpt_infirmeries; ?></a></b> Infirmeries</p>
           <br />
           <?php } ?>


		</div>
<?php /* fin du DIV de l'absentéisme de l'élève */ ?>
	</div>
	<?php } ?>
<?php /* DIV impression bilan  */ ?>
	   <?php if ( $aff_fiche==='impbilan') {	?>
		<a name="impbilan"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Impression bilan des absences</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">
<?php
$absencenj='';
$retardnj=''; 
?>
<div style="text-align: center;">
   <form method="post" action="bilan_absence.php?type_impr=bda&amp;choix=<?php echo $choix; ?>" target="blank" name="form3">
      <fieldset style="width: 340px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan général des absences</div>
            <div class="norme_absence" style="text-align: left;">
             <br />
			 <input type="hidden" name="classe" value="tous" />
			 <input type="hidden" name="eleve" value="<?php echo $select_fiche_eleve; ?>" />
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du2; ?>" /><a href="#calend" onClick="<?php  echo $cal_3->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form3')" /><a href="#calend" onClick="<?php  echo $cal_4->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		<br /><a href="#ao" onclick="affichercacher('div_1')" style="cursor: pointer;"><img style="border: 0px solid ; width: 13px; height: 13px; border: none; padding:2px; margin:2px; float: left;" name="img_1" alt="" title="Information" src="../../images/fleche_na.gif" align="middle" />Autres options</a>
		<div id="div_1" style="display: <?php if( $absencenj != '' or $retardnj != '' ) { ?>block<?php } else { ?>none<?php } ?>; border-top: solid 1px; border-bottom: solid 1px; padding: 10px; background-color: #E0EEEF"><a name="ao"></a>
		  <span style="font-family: Arial;">
			<input name="absencenj" id="absencenj" value="1" type="checkbox" onclick="activedesactive('retardnj','absencenj');" <?php if ( $absencenj === '1' ) { ?>checked="checked"<?php } ?> /><label for="absencenj" style="cursor: pointer;">Lister seulement les absences non justifi&eacute;es</label>
		  	<br /><input name="retardnj" id="retardnj" value="1" type="checkbox" onclick="activedesactive('absencenj','retardnj');" <?php if ( $retardnj === '1' ) { ?>checked="checked"<?php } ?> /><label for="retardnj" style="cursor: pointer;">Lister seulement les retards non justifi&eacute;s</label>
		  </span>
		</div>
		<br /><div style="text-align: right;"><input type="submit" name="Submit2" value="Valider la sélection" /></div>
            </div>
      </fieldset>
    </form>

     
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
	</div>	
	
<?php /* fin du DIV impression bilan*/ ?>
</div>
<?php } ?>
<?php /* DIV fiche recap  */ ?>
	   <?php if ( $aff_fiche==='impfic') { 
	   // mise en session du login de l'élève pour impression
	   $_SESSION['eleve_multiple'][0] = $select_fiche_eleve;
	   ?>
		<a name="impfic"></a>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Impression fiche récapitulative</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">
<?php
$absencenj='';
$retardnj=''; 
?>
<div style="text-align: center;">
   <form method="post" action="fiche_pdf.php" target="blank" name="form3">
      <fieldset style="width: 340px; margin: auto;" class="couleur_ligne_3">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Fiche récapitulative</div>
            <div class="norme_absence" style="text-align: left;">
             <br />
			    du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du2; ?>" /><a href="#calend" onClick="<?php  echo $cal_3->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
				au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form3')" /><a href="#calend" onClick="<?php  echo $cal_4->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>">
				<img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
				<br />
				<div id="div_1" style="display: <?php if( $absencenj != '' or $retardnj != '' ) { ?>block<?php } else { ?>none<?php } ?>; border-top: solid 1px; border-bottom: solid 1px; padding: 10px; background-color: #E0EEEF"><a name="ao"></a>
		</div>
		<br /><div style="text-align: right;"><input type="submit" name="Submit2" value="Valider la sélection" /></div>
            </div>
      </fieldset>
    </form>

     
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
	</div>	
	
<?php /* fin du DIV impression fiche recap*/ ?>
</div>
<?php } ?>
<?php /* fin DIV coté gauche */ ?>
</div>

<?php /* DIV coté droit */ ?>
<div style="float: left; width: 370px; margin-left: 2px;">

<?php /* DIV des statistique de l'élève ajout avec impression didier*/ ?>
	<?php if ( $aff_fiche === 'suivieleve' or  $aff_fiche === '' or $aff_fiche==='abseleve' or $aff_fiche==='impbilan' or $aff_fiche==='impfic') { ?>
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">Statistique élève</div>
		<div style="border-top: 2px solid #2C7E8F; border-bottom: 2px solid #2C7E8F;">
			<div style="background: transparent url(../images/grid_10.png); padding-top: 5px;">
			<?php
				// graphique
					// hauteur du graphique au maximum
					$h_graphique = '252'; //252
					// largeur du graphique au maximum
					$l_graphique = '370'; //536
// temporaire

//$du = '01/09/2008';
//$au = '30/06/2009';
$du = date("d/m/Y",getSettingValue('begin_bookings'));
$au = date("d/m/Y",getSettingValue('end_bookings'));

$du_explose = explode('/',$du);
	$au_explose = explode('/',$au);
		$jour_du = '1';
		$mois_du = $du_explose[1];
		$annee_du = $du_explose[2];
		$jour_au = '31';
		$mois_au = $au_explose[1];
		$annee_au = $au_explose[2];
		$mois= '';
		$du_sql = $annee_du.'-'.$mois_du.'-'.$du_explose[0];
		$au_sql = $annee_au.'-'.$mois_au.'-'.$au_explose[0];

	if ( $mois === '' ) { $mois = tableau_mois($mois_du, $annee_du, $mois_au, $annee_au); }


$info_absence = repartire_jour($login_eleve, 'A', $du_sql, $au_sql);
$info_retard = repartire_jour($login_eleve, 'R', $du_sql, $au_sql);

// pour test
/*$i = 0;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'aou. 2006'; $mois[$i]['num_mois'] = '08'; $mois[$i]['num_mois_simple'] = '8'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'sep. 2006'; $mois[$i]['num_mois'] = '09'; $mois[$i]['num_mois_simple'] = '9'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'oct. 2006'; $mois[$i]['num_mois'] = '10'; $mois[$i]['num_mois_simple'] = '10'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'nov. 2006'; $mois[$i]['num_mois'] = '11'; $mois[$i]['num_mois_simple'] = '11'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'dec. 2006'; $mois[$i]['num_mois'] = '12'; $mois[$i]['num_mois_simple'] = '12'; $mois[$i]['num_annee'] = '2006'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'jan 2007'; $mois[$i]['num_mois'] = '01'; $mois[$i]['num_mois_simple'] = '1'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'fev. 2007'; $mois[$i]['num_mois'] = '02'; $mois[$i]['num_mois_simple'] = '2'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'mar. 2007'; $mois[$i]['num_mois'] = '03'; $mois[$i]['num_mois_simple'] = '3'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'avr. 2007';  $mois[$i]['num_mois'] = '04'; $mois[$i]['num_mois_simple'] = '4'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'mai. 2007'; $mois[$i]['num_mois'] = '05'; $mois[$i]['num_mois_simple'] = '5'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'jui. 2007';  $mois[$i]['num_mois'] = '06'; $mois[$i]['num_mois_simple'] = '6'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
$mois[$i]['mois_court'] = 'aou. 2006'; $mois[$i]['mois'] = 'juil. 2007'; $mois[$i]['num_mois'] = '07'; $mois[$i]['num_mois_simple'] = '7'; $mois[$i]['num_annee'] = '2007'; $i = $i + 1;
*/


	//préparation des valeurs
	// axe x
	if ( !isset($valeur_x) ) {
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$valeur_x[$i] = $mois[$i]['mois_court'];
		$i = $i + 1;
		}
		$_SESSION['axe_x'] = $valeur_x;
	}
	// axe y des absences et des retards
		$i = '0';
		while ( !empty($mois[$i]) )
		{
			$mois_p = $mois[$i]['num_mois'];
			$annee_p = $mois[$i]['num_annee'];
				$total_abs = '0';
				if ( isset($info_absence[$annee_p.'-'.$mois_p]) and ($info_absence[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_absence[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_abs = $info_absence[$annee_p.'-'.$mois_p]['nb'];
				}
				$total_ret = '0';
				if ( isset($info_retard[$annee_p.'-'.$mois_p]) and ($info_retard[$annee_p.'-'.$mois_p]['nb_nj'] != '0' or $info_retard[$annee_p.'-'.$mois_p]['nb_j'] != '0') ) {
					// $total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb_j'] + $info_absence[$annee_p.'-'.$mois_p]['nb_nj'];
					$total_ret = $info_retard[$annee_p.'-'.$mois_p]['nb'];
				}
			$valeur_y_abs[$i] = $total_abs;
			$valeur_y_ret[$i] = $total_ret;
		$i = $i + 1;
		}
		$_SESSION['axe_y_abs'] = $valeur_y_abs;
		$_SESSION['axe_y_ret'] = $valeur_y_ret;


	// génération du graphique
	?><div style="font-size: 14px; text-align: center; margin: auto;"><strong>Graphique des absences et retard sur l'année</strong></div>
	<img src="../lib/graph_double_ligne_fiche.php" alt="Graphique" style="border: 0px; margin: 0px; padding: 0px;"/><?php


	// tableau des nombre d'absences par jour et par heure (période)
   $i = '0';

//	     $requete_comptage = old_mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."'  AND type_absence_eleve = 'A'"),0);
             $requete_komenti = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve = '".$select_fiche_eleve."'  AND type_absence_eleve = 'A' ORDER BY d_date_absence_eleve ASC, d_heure_absence_eleve DESC";
             $execution_komenti = mysqli_query($GLOBALS["mysqli"], $requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
              while ( $donnee_base = mysqli_fetch_array($execution_komenti))
                {
			$tableau[$i]['id'] = $i;
			$tableau[$i]['login'] = $donnee_base['eleve_absence_eleve'];
			$tableau[$i]['classe'] = '';
			$tableau[$i]['date_debut'] = $donnee_base['d_date_absence_eleve'];
			$tableau[$i]['date_fin'] = $donnee_base['a_date_absence_eleve'];
			$tableau[$i]['heure_debut'] = $donnee_base['d_heure_absence_eleve'];
			$tableau[$i]['heure_fin'] = $donnee_base['a_heure_absence_eleve'];
			$i = $i + 1;
		}

	// si aucune absences alors on initialise le tab à vide
	if ( $i > 0 ) {
	$tab = crer_tableau_jaj($tableau);
	} else { $tab = ''; }

	$i = '0';
	$requete_periode = 'SELECT * FROM '.$prefix_base.'edt_creneaux WHERE suivi_definie_periode = "1" ORDER BY heuredebut_definie_periode ASC';
        $execution_periode = mysqli_query($GLOBALS["mysqli"], $requete_periode) or die('Erreur SQL !'.$requete_periode.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while ( $donnee_periode = mysqli_fetch_array( $execution_periode ) ) {
		$Horaire[$i] = heure_texte_court($donnee_periode['heuredebut_definie_periode']).'-'.heure_texte_court($donnee_periode['heurefin_definie_periode']);
		$HorDeb[$i] = $donnee_periode['heuredebut_definie_periode'];
		$HorFin[$i] = $donnee_periode['heurefin_definie_periode'];
	$i = $i + 1;
	}

	if ( $i === '0' ) {
		$i = '0';
		$Horaire = Array(0 => "8h-9h", "9h-10h", "10h-11h", "11h-12h", "12h-13h", "13h-14h", "14h-15h", "15h-16h", "16h-17h", "17h-18h","18h-19h");
		$HorDeb = Array(0 => "08:00:00", "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00","18:00:00");
		$HorFin = Array(0 => "09:00:00", "10:00:00", "11:00:00", "12:00:00", "13:00:00", "14:00:00", "15:00:00", "16:00:00", "17:00:00", "18:00:00", "19:00:00");
	}

	$semaine = ouverture();


	?><br /><br /><a name="tab_sem_abs"></a>
	<table style="border-style: solid; border-width: 2px; border-color: black; width: 320px; border-collapse: collapse; margin: auto;">
		<caption style="font-size: 14px; text-align: center; margin: auto;"><strong>Nombre d'absences par créneau horaire</strong></caption>
		<tr style="background-color: #F0FFCF;">
			<td class="td_semaine_jour"></td>
			<?php if ( isset($semaine['lundi']['ouverture']) ) { ?><td class="td_semaine_jour">Lun.</td><?php } ?>
			<?php if ( isset($semaine['mardi']['ouverture']) ) { ?><td class="td_semaine_jour">Mar.</td><?php } ?>
			<?php if ( isset($semaine['mercredi']['ouverture']) ) { ?><td class="td_semaine_jour">Mer.</td><?php } ?>
			<?php if ( isset($semaine['jeudi']['ouverture']) ) { ?><td class="td_semaine_jour">Jeu.</td><?php } ?>
			<?php if ( isset($semaine['vendredi']['ouverture']) ) { ?><td class="td_semaine_jour">Ven.</td><?php } ?>
			<?php if ( isset($semaine['samedi']['ouverture']) ) { ?><td class="td_semaine_jour">Sam.</td><?php } ?>
		</tr>
	<?php
	$i = '0';

		// calcul du nombre de période à affiché dans la semaine
		$maxHor = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."edt_creneaux
							WHERE suivi_definie_periode = '1'"),0);

		// si il est égale à 0 alors on l'initialise à 11
		if ( $maxHor === '0' or $maxHor > '11' ) { $maxHor = '11'; }

	$icouleur = '1';
	$aff_chiffre = '0';
	while ($i < $maxHor) {
		if($icouleur === '1') { $couleur_cellule = '#FAFFEF'; } else { $couleur_cellule = '#F0FFCF'; }
	 ?><tr>
		<td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php echo $Horaire[$i]; ?></td>
	 	<?php if ( isset($semaine['lundi']['ouverture']) and $semaine['lundi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Mon", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['mardi']['ouverture']) and $semaine['mardi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Tue", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['mercredi']['ouverture']) and $semaine['mercredi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Wed", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['jeudi']['ouverture']) and $semaine['jeudi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Thu", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['vendredi']['ouverture']) and $semaine['vendredi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Fri", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } else { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"></td><?php } ?>
		<?php if ( isset($semaine['samedi']['ouverture']) and $semaine['samedi']['fermeture'] > $HorDeb[$i] ) { ?><td class="td_semaine_jour" style="background-color: <?php echo $couleur_cellule ?>;"><?php if ( $tab  != '' ) { $aff_chiffre = Absence("Sat", $HorDeb[$i], $HorFin[$i], $tab); } else { $aff_chiffre = '0'; } if($aff_chiffre != '0') { echo $aff_chiffre; } $aff_chiffre = '0'; ?></td><?php } ?>
	   </tr><?php
	if($icouleur==='2') { $icouleur = '1'; } else { $icouleur = '2'; }
	$i = $i +1;
	} ?>
	</table>
		<div style="text-align: center; margin: auto;"><strong><a href="#ident">remonter</a></strong></div>
		<br /><br />
	</div>
</div>
<?php /* fin du DIV des statistique de l'élève */ ?>
<?php } ?>

<?php /* fin du coté droit */ ?>
</div>

<?php /* fin du DIV global */ ?>
</div>

<?php if ( $aff_fiche === 'tableauannuel' ) { ?>
<div style="margin: auto; position: relative;">
	<div style="float: left; width: 781px">
		<div style="background-image: url(../images/haut_tab.png); font-size: 120%; font-weight: bold; color: #E8F1F4; text-align: left;">L'élève sur l'année</div>
		<div style="border-top: 2px solid #2C7E8F; /* #FF9F2F */ border-bottom: 2px solid #2C7E8F; width: 100%; margin: auto; padding: 0; position: relative;">
			<div style="background: transparent url(../images/grid_10.png)"><br />
		<?php
		 $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='A' ORDER BY d_date_absence_eleve DESC";
                 $execution_absences_nr = mysqli_query($GLOBALS["mysqli"], $requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                 while ($data_absences_nr = mysqli_fetch_array($execution_absences_nr))
                   {
			$date_debut = date_fr($data_absences_nr['d_date_absence_eleve']);
			$date_fin = date_fr($data_absences_nr['a_date_absence_eleve']);
			  $passage='oui';
			while($passage==='oui') {
		          $dateexplode = explode('/', $date_debut);
			    $date_debut_tableau_jour = my_eregi_replace('^0','',$dateexplode[0]);
			    $date_debut_tableau_mois = my_eregi_replace('^0','',$dateexplode[1]);
			    $date_debut_tableau= $date_debut_tableau_jour.'/'.$date_debut_tableau_mois.'/'.$dateexplode[2];
			    if(empty($tableau_info_donnee[$date_debut_tableau])) { $tableau_info_donnee[$date_debut_tableau]=''; }
			    $tableau_info_donnee[$date_debut_tableau]['absence'] = 'oui';
			    if($date_debut===$date_fin) { $passage='non'; } else { $passage='oui'; }
			    $date_debut = date("d/m/Y", mktime(0, 0, 0, $dateexplode[1], $dateexplode[0]+1,  $dateexplode[2]));
			}
                   }
		 $requete_absences_nr = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$select_fiche_eleve."' AND type_absence_eleve='R' ORDER BY d_date_absence_eleve DESC";
                 $execution_absences_nr = mysqli_query($GLOBALS["mysqli"], $requete_absences_nr) or die('Erreur SQL !'.$requete_absences_nr.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                 while ($data_absences_nr = mysqli_fetch_array($execution_absences_nr))
                   {
			$date_debut = date_fr($data_absences_nr['d_date_absence_eleve']);
			    $date_debut = my_eregi_replace('^0','',$date_debut);
			$date_fin = date_fr($data_absences_nr['a_date_absence_eleve']);
			    $date_fin = my_eregi_replace('^0','',$date_fin);
			if(empty($tableau_info_donnee[$date_debut])) { $tableau_info_donnee[$date_debut]=''; }
			$tableau_info_donnee[$date_debut]['retard'] = 'oui';
			if(empty($tableau_info_donnee[$date_fin])) { $tableau_info_donnee[$date_fin]=''; }
			$tableau_info_donnee[$date_fin]['retard'] = 'oui';
                   }
	?><div style="font-size: 14px; text-align: center; margin: auto;"><strong>Statistique sur une année</strong></div>
	<?php
		$gepiYear = getSettingValue('gepiYear');
		$annee_select = explode('-',$gepiYear);
		if ( empty($annee_select[1]) ) { $annee_select = explode('/',$gepiYear); }
		if ( empty($annee_select[1]) ) { $annee_select = explode(' ',$gepiYear); }
		echo @tableau_annuel($select_fiche_eleve, '8', '12', trim($annee_select[0]), $tableau_info_donnee);
	?>
			</div>
		</div>
	</div>
</div>
  <br />
<?php } ?>

<?php } ?>




<?php if ($choix === 'lemessager' and $fiche_eleve === '' and $select_fiche_eleve === '') { ?>
<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<br />
<form method="post" action="gestion_absences.php?choix=lemessager" name="form1">A la date du <input name="du" type="text" size="11" maxlength="11" value="<?php if(isset($du)) { echo $du; } ?>" /><a href="#calend" onclick="<?php echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php',350,170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> <input type="submit" name="Submit" value="&gt;&gt;" /></form>
    <table style="margin: auto; width: 700px;" border="0" cellspacing="1" cellpadding="0">
       <tr class="fond_rouge">
           <td colspan="2" class="titre_tableau_gestion"><b>Le messager</b></td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les prioritaires</td>
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les messages</td>
       </tr>
       <tr class="td_tableau_absence_2">
           <td class="norme_absence_min" valign="top">
	   <?php
             $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.date_suivi_eleve_cpe = '".$date_ce_jour."' AND niveau_message_suivi_eleve_cpe='3' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC";
             $execution_komenti = mysqli_query($GLOBALS["mysqli"], $requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
              while ( $data_komenti = mysqli_fetch_array($execution_komenti))
                {
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe']; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">Ecrit par : <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />Concerne : <strong><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>" title="consulter le suivi élève"><?php echo qui_eleve($data_komenti['eleve_suivi_eleve_cpe']); ?></a></strong> de <?php echo classe_de($data_komenti['eleve_suivi_eleve_cpe']) ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
           <?php } ?>
	   </td>
           <td class="norme_absence_min" valign="top">
           <?php
             $requete_komenti = "SELECT * FROM ".$prefix_base."suivi_eleve_cpe WHERE ".$prefix_base."suivi_eleve_cpe.date_suivi_eleve_cpe = '".$date_ce_jour."'  AND niveau_message_suivi_eleve_cpe!='3' ORDER BY date_suivi_eleve_cpe DESC, heure_suivi_eleve_cpe DESC";
             $execution_komenti = mysqli_query($GLOBALS["mysqli"], $requete_komenti) or die('Erreur SQL !'.$requete_komenti.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
              while ( $data_komenti = mysqli_fetch_array($execution_komenti))
                {
		   if(!empty($data_komenti['niveau_message_suivi_eleve_cpe'])) {
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='1') { $couleur='#FFFFFF'; $couleur2='#280FFF'; $drapeau='[information]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='2') { $couleur='#FFF3DF'; $couleur2='#FF782F'; $drapeau='[important]'; }
				if( $data_komenti['niveau_message_suivi_eleve_cpe']==='3') { $couleur='#FFDFDF'; $couleur2='#FF0000'; $drapeau='[prioritaire]'; }
			  } else { $couleur='#FFFFFF'; $couleur2='#4DFF2F'; $drapeau=''; } ?>
                    <p class="info_eleve" style="background: <?php echo $couleur; ?>;"><b><?php echo date_frl($data_komenti['date_suivi_eleve_cpe']).' - '.$data_komenti['heure_suivi_eleve_cpe'].' <span style="font-weight: bold; color: '.$couleur2.';">'.$drapeau.'</span>'; ?></b><br /><?php echo $data_komenti['komenti_suivi_eleve_cpe']; ?><br /><br /><span class="dimi_texte">Ecrit par: <?php echo qui($data_komenti['parqui_suivi_eleve_cpe']); ?><br />Concerne : <strong><a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>" title="consulter le suivi élève"><?php echo qui_eleve($data_komenti['eleve_suivi_eleve_cpe']); ?></a></strong> de <?php echo classe_de($data_komenti['eleve_suivi_eleve_cpe']) ?><br />[ <a href="gestion_absences.php?select_fiche_eleve=<?php echo $data_komenti['eleve_suivi_eleve_cpe']; ?>">lire</a> ]</span></p>
           <?php } ?>
	   </td>
       </tr>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php
if ($choix === 'alert' and $fiche_eleve === '' and $select_fiche_eleve === '') {
/* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
<br />
    <table style="margin: auto; width: 700px;" border="0" cellspacing="1" cellpadding="0">
       <tr class="fond_rouge">
           <td colspan="1" class="titre_tableau_alert"><b>Système d'alert</b></td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">Les alerts</td>
       </tr>
       <tr class="td_tableau_absence_1">
           <td class="norme_absence_min" style="text-align: center; width: 50%;">

<form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form3">
      <fieldset style="width: 450px; margin: auto;">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Profils des recherchers</div>
            <div class="norme_absence" style="text-align: center; background-color: #E8F1F4">
		<table style="border: 0px; text-align: center; width: 100%;"><tr><td>
                <select name="classe_multiple[]" size="5" multiple="multiple" tabindex="3">
		  <optgroup label="----- Listes des classes -----">
		    <?php
			if ($_SESSION["statut"] === 'cpe') {
	            $requete_classe = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'periodes WHERE '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			} else {
		        $requete_classe = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'classes, '.$prefix_base.'j_eleves_professeurs, '.$prefix_base.'j_eleves_classes, '.$prefix_base.'periodes WHERE ('.$prefix_base.'j_eleves_professeurs.professeur="'.$_SESSION['login'].'" AND '.$prefix_base.'j_eleves_professeurs.professeur.login = '.$prefix_base.'j_eleves_classes.login AND '.$prefix_base.'j_eleves_classes.id_classe = '.$prefix_base.'classes.id) AND '.$prefix_base.'periodes.id_classe = '.$prefix_base.'classes.id  GROUP BY id_classe ORDER BY '.$prefix_base.'classes.classe');
			}
	  		while ($donner_classe = mysqli_fetch_array($requete_classe))
		  	{
				$requete_cpt_nb_eleve_1 =  mysqli_query($GLOBALS["mysqli"], 'SELECT count(*) FROM '.$prefix_base.'eleves, '.$prefix_base.'classes, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'classes.id = "'.$donner_classe['id_classe'].'" AND '.$prefix_base.'j_eleves_classes.id_classe='.$prefix_base.'classes.id AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login');
				$requete_cpt_nb_eleve = mysqli_num_rows($requete_cpt_nb_eleve_1);
			   ?><option value="<?php echo $donner_classe['id_classe']; ?>" <?php if(!empty($classe_multiple) and in_array($donner_classe['id_classe'], $classe_multiple)) { ?>selected="selected"<?php } ?> onclick="javascript:document.form3.submit()"><?php echo $donner_classe['nom_complet']; ?> (eff. <?php echo $requete_cpt_nb_eleve; ?>)</option><?php
			}
			?>
		  </optgroup>
		  </select></td><td>
		  <select name="eleve_multiple[]" size="5" multiple="multiple" tabindex="4">
		  <optgroup label="----- Listes des &eacute;l&egrave;ves -----">
		    <?php
			// sélection des id eleves sélectionné.
			if(!empty($classe_multiple[0]))
			{
				$cpt_classe_selec = 0; $selection_classe = "";
				while(!empty($classe_multiple[$cpt_classe_selec])) { if($cpt_classe_selec == 0) { $selection_classe = $prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } else { $selection_classe = $selection_classe." OR ".$prefix_base."j_eleves_classes.id_classe = ".$classe_multiple[$cpt_classe_selec]; } $cpt_classe_selec = $cpt_classe_selec + 1; }
	                        $requete_eleve = mysqli_query($GLOBALS["mysqli"], 'SELECT * FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE ('.$selection_classe.') AND '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login GROUP BY '.$prefix_base.'eleves.login ORDER BY '.$prefix_base.'eleves.nom ASC');
		  		while ($donner_eleve = mysqli_fetch_array($requete_eleve))
			  	 {
				   ?><option value="<?php echo $donner_eleve['login']; ?>" <?php if(!empty($eleve_multiple) and in_array($donner_eleve['login'], $eleve_multiple)) { ?> selected="selected"<?php } ?>><?php echo strtoupper($donner_eleve['nom'])." ".ucfirst($donner_eleve['prenom']); ?></option><?php
				 }
			}
			?>
		     <?php if(empty($classe_multiple[0]) and empty($eleve_multiple[0])) { ?><option value="" disabled="disabled">Vide</option><?php } ?>
		  </optgroup>
		  </select></td>
		 </tr>
		 <tr>
		   <td colspan="2">
			<select name="ajout_type_alert" size="1" style="width: 200px;">
				<option>Type action</option>
				<option>Type lettre</option>
				<option>Fiche élève</option>
			</select>
			<input type="submit" name="Submit10" value="Ajouter" />
</td>
		 </tr>
		</table>
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onclick="<?php  echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
            </div>
      </fieldset>
    </form>


</td>
       </tr>
    </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
}
require("../../lib/footer.inc.php");
?>

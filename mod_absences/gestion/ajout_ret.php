<?php
/*
*
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
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

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

if (empty($_GET['mode']) and empty($_POST['mode'])) {$mode="";}
    else { if (isset($_GET['mode'])) {$mode=$_GET['mode'];} if (isset($_POST['mode'])) {$mode=$_POST['mode'];} }
if (empty($_GET['page']) and empty($_POST['page'])) {$page="";}
    else { if (isset($_GET['page'])) {$page=$_GET['page'];} if (isset($_POST['page'])) {$page=$_POST['page'];} }
if (empty($_POST['action_sql'])) {$action_sql = ''; } else {$action_sql=$_POST['action_sql']; }
if (empty($_GET['action'])) {$action = ''; } else {$action=$_GET['action']; }
if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_POST['nb_i'])) {$nb_i = ''; } else {$nb_i=$_POST['nb_i']; }
if (empty($_GET['type'])) {$type = ''; } else {$type=$_GET['type']; }
if (empty($_POST['id_absence_eleve'])) {$id_absence_eleve = ''; } else {$id_absence_eleve=$_POST['id_absence_eleve']; }
if (empty($_GET['id']) and empty($_POST['id'])) {$id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }


if (empty($_GET['fiche']) and empty($_POST['fiche'])) {$fiche="";}
    else { if (isset($_GET['fiche'])) {$fiche=$_GET['fiche'];} if (isset($_POST['fiche'])) {$fiche=$_POST['fiche'];} }

// si pas de sélection on retourne à la sélection
if((empty($classe_choix) or $classe_choix === 'tous') and empty($eleve_absent[0]) and empty($id) and $action_sql === '') { header("Location:select.php?type=$type"); }

// si sélection d'une classe complète
if(empty($eleve_absent[0])==true and $action_sql == '' and $mode !='eleve') { $mode='classe'; }
if(empty($eleve_absent[0])==true and $mode != 'eleve')
 {
      if (empty($_POST['classe_choix'])) {$classe_absent = ''; } else { $classe_absent=$_POST['classe_choix']; }
      $classe_choix_eleve = $classe_absent;
      $mode = 'classe';
 }

//si c'est une classe qui est sélectionné on sélectionne tous les élèves de cette classe.
  if($mode === 'classe')
   {
          //je compte les élève si = 0 alors on redirige
           $cpt_eleves = mysql_result(mysql_query("SELECT count(*) FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND id = '".$classe_choix_eleve."'"),0);
           	 // christian modif du 15/01/2007 if($cpt_eleves === '0') { header("Location:select.php?type=$type"); }
         //je recherche tous les élèves de la classe sélectionné
           //$requete_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND id = '".$classe_choix_eleve."' GROUP BY nom, prenom";
           $requete_eleve ="SELECT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."j_eleves_classes.login, ".$prefix_base."j_eleves_classes.id_classe, ".$prefix_base."j_eleves_classes.periode, ".$prefix_base."classes.classe, ".$prefix_base."classes.id, ".$prefix_base."classes.nom_complet FROM ".$prefix_base."eleves, ".$prefix_base."j_eleves_classes, ".$prefix_base."classes WHERE ".$prefix_base."eleves.login=".$prefix_base."j_eleves_classes.login AND ".$prefix_base."j_eleves_classes.id_classe=".$prefix_base."classes.id AND id = '".$classe_choix_eleve."' GROUP BY eleves.login, nom, prenom"; // 20100430
           $execution_eleve = mysql_query($requete_eleve) or die('Erreur SQL !'.$requete_eleve.'<br />'.mysql_error());
           $cpt_eleve = 0;
           while ($data_eleve = mysql_fetch_array($execution_eleve))
             {
                //insertion de l'élève dans la varibale $eleve_absent
                 $eleve_absent[$cpt_eleve] = $data_eleve['login'];
                 $cpt_eleve = $cpt_eleve + 1;
             }
   }



// christian modif du 15/01/2007 if($id == "" and $eleve_absent == "" and $id_absence_eleve == "") { header("Location:select.php?type=$type"); }

//$id_absence_eleve = $id;
$total = '0'; $erreur_valu = '0'; $erreur = '0';

//requête pour liste les motif d'absence
$requete_liste_motif = "SELECT init_motif_absence, def_motif_absence FROM absences_motifs ORDER BY init_motif_absence ASC";

if($action_sql === 'ajouter' or $action_sql === 'modifier')
{

	include "../lib/function_abs.php";

  $j = '0';

  while ($total < $nb_i)
   {
      $erreur_valu = '0'; $erreur = '0';

      $type_absence_eleve_form = $_POST['type'];
      $id_absence_eleve_form = $_POST['id_absence_eleve'][$total];
      $justify_absence_eleve_form = $_POST['justify_absence_eleve'][$total];
      $info_justify_absence_eleve_form = $_POST['info_justify_absence_eleve'][$total];
      $motif_absence_eleve_form = $_POST['motif_absence_eleve'][$total];
      $d_date_absence_eleve_form = date_sql($_POST['d_date_absence_eleve'][$total]);
      $d_heure_absence_eleve_form = $_POST['d_heure_absence_eleve'][$total];

      //attribution de certaine variable
      $a_date_absence_eleve_form = $d_date_absence_eleve_form;

	// réinitialise les variables de vérification
	$erreur_valu=''; $erreur='';

      if ($d_heure_absence_eleve_form != '00:00' and $d_heure_absence_eleve_form != '')
       {
           if(verif_date($d_date_absence_eleve_form) === 'pass')
            {
                if(my_eregi("[0-9]{2}:[0-9]{2}",$d_heure_absence_eleve_form))
                 {
                  } else { $erreur='1'; $erreur_valu='2'; $texte_erreur="Le format de l'heure n'est pas correct."; }
             } else { $erreur='1'; $erreur_valu='3'; $texte_erreur="La date n'est pas correct."; }
       } else { $erreur='1'; $erreur_valu='4'; $texte_erreur="Aucune heure n'a été saisie"; }

          if ($erreur === '1')
             {
                $type_absence_eleve_erreur[$j] = $type_absence_eleve_form;
                $id_absence_eleve_erreur[$j] = $id_absence_eleve_form;
                $justify_absence_eleve_erreur[$j] = $justify_absence_eleve_form;
                $info_justify_absence_eleve_erreur[$j] = $info_justify_absence_eleve_form;
                $motif_absence_eleve_erreur[$j] = $motif_absence_eleve_form;
                $d_date_absence_eleve_erreur[$j] = date_fr($d_date_absence_eleve_form);
                $d_heure_absence_eleve_erreur[$j] = $d_heure_absence_eleve_form;
		$texte_eleve_erreur[$j] = $texte_erreur;
                $j = $j + 1;
             } else {

        				/* ******************************************** */
        				/* gestion de l'ajout dans la table absences_rb */
				        /* gerer_absence($id,$eleve_id,$retard_absence,$groupe_id='',$edt_id='',$jour_semaine='',$creneau_id='',$debut_ts,$fin_ts,$date_saisie,$login_saisie) */

						$explode_heuredeb = explode(":", $d_heure_absence_eleve_form);
						$explode_heurefin = explode(":", $d_heure_absence_eleve_form);
						$explode_date_debut = explode("/", date_fr($d_date_absence_eleve_form));
						$explode_date_fin = explode("/", date_fr($d_date_absence_eleve_form));
						$debut_ts = mktime($explode_heuredeb[0], $explode_heuredeb[1], 0, $explode_date_debut[1], $explode_date_debut[0], $explode_date_debut[2]);
						$fin_ts = mktime($explode_heurefin[0], $explode_heurefin[1], 0, $explode_date_fin[1], $explode_date_fin[0], $explode_date_fin[2]);
						$date_saisie = mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y"));
						$login_saisie = $_SESSION['login'];
						$action = 'ajouter';

						if ( $action_sql === "ajouter" )
						{

							gerer_absence('',$id_absence_eleve_form,'R','','','','',$debut_ts,$fin_ts,$date_saisie,$login_saisie,$action);

						}

						if ( $action_sql === "modifier" )
						{

							modifier_absences_rb($id,$debut_ts,$fin_ts);

						}

        				/*                                              */
        				/* ******************************************** */

                      if ( $action_sql === "ajouter" ) { $requete="INSERT INTO absences_eleves (type_absence_eleve,eleve_absence_eleve,justify_absence_eleve,info_justify_absence_eleve,motif_absence_eleve,d_date_absence_eleve,a_date_absence_eleve,d_heure_absence_eleve,saisie_absence_eleve) values ('R','$id_absence_eleve_form','$justify_absence_eleve_form','$info_justify_absence_eleve_form','$motif_absence_eleve_form','$d_date_absence_eleve_form','$a_date_absence_eleve_form','$d_heure_absence_eleve_form','".$_SESSION['login']."')"; }
                      if ( $action_sql === "modifier" ) { $requete="UPDATE absences_eleves SET justify_absence_eleve = '$justify_absence_eleve_form', info_justify_absence_eleve = '$info_justify_absence_eleve_form', motif_absence_eleve = '$motif_absence_eleve_form', d_date_absence_eleve = '$d_date_absence_eleve_form', a_date_absence_eleve = '$a_date_absence_eleve_form', d_heure_absence_eleve = '$d_heure_absence_eleve_form', saisie_absence_eleve = '".$_SESSION['login']."' WHERE eleve_absence_eleve = '".$id_absence_eleve_form."' and id_absence_eleve = '".$id."'"; }
                      $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());

                   }
            $total = $total + 1;
        }

            // si tout est ok on redirige
            if(!isset($id_absence_eleve_erreur[0]) and empty($id_absence_eleve_erreur[0]))
               {
                  if ( $action_sql === "ajouter" ) { header("Location:select.php?type=R"); }
                  if ( $action_sql === "modifier" ) {
                                                      if($fiche==='oui') { header("Location:gestion_absences.php?type=R&select_fiche_eleve=$id_absence_eleve_form&aff_fiche=abseleve#abseleve"); } else { header("Location:gestion_absences.php?type=R"); }
                                                   }
               }
}

$datej = date('Y-m-d'); $annee_en_cours_t=annee_en_cours_t($datej);
$datejour = date('d/m/Y');

if ($action === "supprimer")
{

	include "../lib/function_abs.php";

	if (empty($_GET['date_ce_jour']) and empty($_POST['date_ce_jour'])) { $date_ce_jour = ''; }
	   else { if (isset($_GET['date_ce_jour'])) { $date_ce_jour = $_GET['date_ce_jour']; } if (isset($_POST['date_ce_jour'])) { $date_ce_jour = $_POST['date_ce_jour']; } }

    $id_absence_eleve = $_GET['id'];
    $requete_sup = "SELECT eleve_absence_eleve FROM ".$prefix_base."absences_eleves
								WHERE id_absence_eleve ='$id_absence_eleve'";
	$resultat_sup = mysql_query($requete_sup) or die('Erreur SQL !'.$requete_sup.'<br />'.mysql_error());
	$login_eleve = mysql_fetch_array($resultat_sup); 
    // Vérification des champs
    if ( $id_absence_eleve != '' )
    {

		// suppression dans la table absence_rb
		suppr_absences_rb($id_absence_eleve);

        //Requete d'insertion MYSQL
        $requete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id_absence_eleve."'";
        // Execution de cette requete
        mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
        if($fiche === 'oui') {
		 	header("Location:gestion_absences.php?type=R&select_fiche_eleve=$login_eleve[0]&aff_fiche=abseleve#abseleve");
			} else {
        header('Location:gestion_absences.php?type=R&date_ce_jour='.$date_ce_jour);
			}
    }

}

$i = '0';
if ($action === "modifier")
{
        $requete_modif = "SELECT * FROM absences_eleves WHERE id_absence_eleve ='$id'";
        $resultat_modif = mysql_query($requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.mysql_error());
        while ($data_modif = mysql_fetch_array($resultat_modif))
        {
            $type_absence_eleve[$i] = $data_modif['type_absence_eleve'];
            $eleve_absent[$i] = $data_modif['eleve_absence_eleve'];
            $justify_absence_eleve[$i] = $data_modif['justify_absence_eleve'];
            $info_justify_absence_eleve[$i] = $data_modif['info_justify_absence_eleve'];
            $motif_absence_eleve[$i] = $data_modif['motif_absence_eleve'];
            $d_date_absence_eleve[$i] = date_fr($data_modif['d_date_absence_eleve']);
            $a_date_absence_eleve[$i] = date_fr($data_modif['a_date_absence_eleve']);
            $d_heure_absence_eleve[$i] = $data_modif['d_heure_absence_eleve'];
            //$a_heure_absence_eleve[$i] = $data_modif['a_heure_absence_eleve'];
            $heure_retard_eleve[$i] = $data_modif['d_heure_absence_eleve'];
            $i = $i + 1;
        }
}

// s'il y a eu un problème alors on réaffect le donnée au nom des variables du formulaire
$i = '0';
if(isset($id_absence_eleve_erreur[0]) and !empty($id_absence_eleve_erreur[0]))
{
        while (isset($id_absence_eleve_erreur[$i]))
        {
            $type_absence_eleve[$i] = $type_absence_eleve_erreur[$i];
            $eleve_absent[$i] = $id_absence_eleve_erreur[$i];
            $justify_absence_eleve[$i] = $justify_absence_eleve_erreur[$i];
            $info_justify_absence_eleve[$i] = $info_justify_absence_eleve_erreur[$i];
            $motif_absence_eleve[$i] = $motif_absence_eleve_erreur[$i];
            $d_date_absence_eleve[$i] = $d_date_absence_eleve_erreur[$i];
            $a_date_absence_eleve[$i] = $d_date_absence_eleve_erreur[$i];
            $d_heure_absence_eleve[$i] = $d_heure_absence_eleve_erreur[$i];
            $heure_retard_eleve[$i] = $d_heure_absence_eleve_erreur[$i];
	    if(isset($id) and !empty($id)) { $action = 'modifier'; }
            $i = $i + 1;
        }
}

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class="bold"><a href='gestion_absences.php?type=<?php echo $type; ?><?php if($fiche==='oui') { ?>&select_fiche_eleve=<?php echo $eleve_absent[0];?>&aff_fiche=abseleve#abseleve<?php }?>'><img src="../../images/icons/back.png" alt="Retour" title="Retour" class="back_link" /> Retour</a>
</p><?php

$i = '0';
//Configuration du calendrier
//include("../../lib/calendrier/calendrier.class.php");
include("../../lib/calendrier/calendrier_id.class.php");
while(empty($eleve_absent[$i])== false or empty($id_absence_eleve_erreur[$i])== false) {
    //$cal_[$i] = new Calendrier("form1", "d_date_absence_eleve[$i]");
    $cal_[$i] = new Calendrier("form1", "d_date_absence_eleve_$i");
    $i = $i+1;
}

?>
<div style="text-align:center">
<form method="post" action="ajout_ret.php?type=<?php echo $type; ?>" name="form1">
 <fieldset class="fieldset_efface">
   <table class="entete_tableau_absence" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td colspan="3" class="titre_tableau_absence"><b><?php echo "Retards des élèves"; ?></b></td>
      <td class="titre_tableau_absence_valider"><input type="submit" name="submit" value="Valider" /></td>
    </tr>
    <tr class="tr_tableau_absence_titre">
      <td class="centre">Identit&eacute;</td>
      <td class="centre"></td>
      <td class="centre">Date/Horaire</td>
      <td class="centre">Indication</td>
    </tr>
   <?php  $i = '0'; $ic = '1';
       if(isset($texte_eleve_erreur[0])) { $erreur = '1'; } else { $erreur = '0'; }
       if ($erreur === '0') { $nb = count($eleve_absent); }
       if ($erreur === '1') { $nb = $j; }

       while($i < $nb) {

       // Alternance des couleurs
       if ($ic === '1') { $ic='2'; $couleur_cellule="td_tableau_absence_1"; } else { $couleur_cellule="td_tableau_absence_2"; $ic='1'; }
       ?>
            <?php if (isset($texte_eleve_erreur[$i])) { ?>
             <tr class="table_erreur">
              <td class="centre"><img src="../images/attention.png" width="28" height="28" alt="" /></td>
              <td colspan="4" class="erreur"><strong>Erreur:
              <?php  echo $texte_eleve_erreur[$i]; ?>
              </strong></td>
             </tr>
            <?php } ?>

       <tr class="<?php echo $couleur_cellule; ?>">
         <td style="text-align: center;">
         <?php
            if ($erreur === '0') { $requete_id="SELECT * FROM eleves WHERE login='".$eleve_absent[$i]."'"; }
            if ($erreur === '1') { $requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$id_absence_eleve_erreur[$i]."'"; }
            $resultat_id = mysql_query($requete_id) or die('Erreur SQL !'.$requete_id.'<br />'.mysql_error());
            while($data_id = mysql_fetch_array ($resultat_id)) {
                ?><b><?php echo strtoupper($data_id['nom']); ?></b><br /><?php echo ucfirst($data_id['prenom']); ?><br /><?php echo classe_de($data_id['login']); $id_eleve = $data_id['login']; $id_eleve_photo = $data_id['elenoet']; echo '<br />';
            }
          ?>
          <input type="hidden" name="id_absence_eleve[<?php echo $i; ?>]" value="<?php echo $id_eleve; ?>" />
          <input type="hidden" name="id" value="<?php echo $id; ?>" />
      </td>
      <td style="text-align: center;">
	 <?php
             $compte = mysql_result(mysql_query("SELECT COUNT(*) FROM ".$prefix_base."absences_eleves
                                                      WHERE eleve_absence_eleve='".$id_eleve."' AND type_absence_eleve='R'"),0);
                  if (getSettingValue("active_module_trombinoscopes")=='y') {
                  	  $nom_photo = '';
                      $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                      //if ( $nom_photo === '' or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                      if ( $nom_photo === NULL or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                      $valeur=redimensionne_image_petit($photo);
                      ?><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><br /><?php
                   }
                 ?>
                 <table class="tableau_info_compt" border="0" cellspacing="0" cellpadding="2">
                   <tr>
                     <td>El&egrave;ve arrivé<br /><strong><?php if ($compte != 0) { ?><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=<?php echo $type; ?>',260,320,'scrollbars=yes,statusbar=no,resizable=yes');" title=""><?php } ?><?php echo $compte; ?> fois<?php if ($compte != 0) { ?></a><?php } ?></strong><br />en retard</td>
                   </tr>
                 </table>
      </td>
      <td>le <input name="d_date_absence_eleve[<?php echo $i; ?>]" id="d_date_absence_eleve_<?php echo $i; ?>" onfocus="javascript:this.select()" type="text" size="12" maxlength="12" value="<?php if(isset($d_date_absence_eleve[$i]) and !empty($d_date_absence_eleve[$i])) { echo $d_date_absence_eleve[$i]; } else { echo $datejour; } ?>" /><a href="#calend" onClick="<?php echo $cal_[$i]->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br /><br />à <input name="d_heure_absence_eleve[<?php echo $i; ?>]" onfocus="javascript:this.select()" type="text" size="5" maxlength="5" value="<?php if(isset($d_heure_absence_eleve[$i]) and !empty($d_heure_absence_eleve[$i])) { echo $d_heure_absence_eleve[$i]; } else { ?>00:00<?php } ?>" /></td>
      <td><?php echo "Motif :"; ?><br />
          <select name="motif_absence_eleve[<?php echo $i; ?>]" >
          <?php
          $resultat_liste_motif = mysql_query($requete_liste_motif) or die('Erreur SQL !'.$requete_liste_classe.'<br />'.mysql_error());
          while ( $data_liste_motif = mysql_fetch_array ($resultat_liste_motif)) { ?>
              <option value="<?php echo $data_liste_motif['init_motif_absence']; ?>" <?php if(isset($motif_absence_eleve[$i]) and $motif_absence_eleve[$i] === $data_liste_motif['init_motif_absence']) { ?>selected="selected"<?php } ?>><?php echo $data_liste_motif['init_motif_absence']." - ".$data_liste_motif['def_motif_absence']; ?></option>
          <?php } ?>
          </select><br />
          Justification :<br />
         <select name="justify_absence_eleve[<?php echo $i; ?>]">
                      <option value="N" <?php if (isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === "N") { ?>selected<?php } else { ?>selected<?php } ?>>Aucune</option>
                      <option value="O" <?php if (isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === "O") { ?>selected<?php } ?>>Oui</option>
                      <option value="T" <?php if (isset($justify_absence_eleve[$i]) and $justify_absence_eleve[$i] === "T") { ?>selected<?php } ?>>Par t&eacute;l&eacute;phone</option>
                    </select><br />
         <?php echo "plus d'info" ?><br />
         <input name="info_justify_absence_eleve[<?php echo $i; ?>]" type="text" <?php if(isset($info_justify_absence_eleve[$i]) and !empty($info_justify_absence_eleve[$i])) { ?>value="<?php echo $info_justify_absence_eleve[$i]; ?>"<?php } ?>/>
      </td>
    </tr>
    <?php $i = $i+1;
   } ?>
  </table>
   <input type="hidden" name="type" value="<?php echo $type; ?>" />
   <input type="hidden" name="action_sql" <?php  if ($action == "modifier") {?>value="modifier"<?php } else {?>value="ajouter"<?php } ?> />
   <input type="hidden" name="fiche" value="<?php echo $fiche; ?>" />
   <input type="hidden" name="nb_i" value="<?php echo $i; ?>" />
 </fieldset>
</form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>

<?php mysql_close(); ?>

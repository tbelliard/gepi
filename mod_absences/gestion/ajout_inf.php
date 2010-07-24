<?php
/*
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

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

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

if (empty($_POST['action_sql'])) {$action_sql = ''; } else {$action_sql=$_POST['action_sql']; }
if (empty($_POST['eleve_absent'])) {$eleve_absent = ''; } else {$eleve_absent=$_POST['eleve_absent']; }
if (empty($_POST['nb_i'])) {$nb_i = ''; } else {$nb_i=$_POST['nb_i']; }
if (empty($_GET['action'])) {$action = ''; } else {$action=$_GET['action']; }
if (empty($_GET['type'])) {$type = ''; } else {$type=$_GET['type']; }
if (empty($_POST['id_absence_eleve'])) {$id_absence_eleve = ''; } else {$id_absence_eleve=$_POST['id_absence_eleve']; }
if (empty($_GET['id']) and empty($_POST['id'])) {$id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }


if (empty($_GET['fiche']) and empty($_POST['fiche'])) {$fiche="";}
    else { if (isset($_GET['fiche'])) {$fiche=$_GET['fiche'];} if (isset($_POST['fiche'])) {$fiche=$_POST['fiche'];} }

// si pas de sélection on retourne à la sélection
if((empty($classe_choix) or $classe_choix === 'tous') and empty($eleve_absent[0]) and empty($id) and $action_sql === '') { header("Location:select.php?type=$type"); }

// si aucune sélection on redirige
if(!isset($eleve_absent[0]) and empty($eleve_absent[0]) and $action_sql === '' and $id === '') { header("Location:select.php?type=$type"); }

//$id_absence_eleve = $id;
$total = '0'; $erreur_valu = '0'; $erreur = '0';

if($action_sql == "ajouter" or $action_sql == "modifier")
{
  $j = '0';
  while ($total < $nb_i)
   {
	$erreur_valu = '0'; $erreur = '0';
      $type_absence_eleve = $_POST['type_absence_eleve'];
      $eleve_absence_eleve = $_POST['eleve_absence_eleve'][$total];
      if($_POST['info_justify_absence_eleve'][$total] != "") { $justify_absence_eleve = "O"; } else { $justify_absence_eleve = "N"; }
      $info_justify_absence_eleve = $_POST['info_justify_absence_eleve'][$total];
      $motif_absence_eleve="IN";
      $d_date_absence_eleve = date_sql($_POST['d_date_absence_eleve'][$total]);
      $d_heure_absence_eleve = $_POST['d_heure_absence_eleve'][$total];
      $a_heure_absence_eleve = $_POST['a_heure_absence_eleve'][$total];

      $a_date_absence_eleve = $d_date_absence_eleve;

      $eleve_absent[$total] = $eleve_absence_eleve ;

           // vérification
              if (verif_date($a_date_absence_eleve) == "pass")
               {
                     if ($d_heure_absence_eleve != "00:00" and $d_heure_absence_eleve != "")
                      {
                            if ($a_heure_absence_eleve != "00:00" and $a_heure_absence_eleve != "")
                             {
                                  if(my_eregi("[0-9]{2}:[0-9]{2}",$d_heure_absence_eleve))
                                   {
                                         if(my_eregi("[0-9]{2}:[0-9]{2}",$a_heure_absence_eleve))
                                         {
                                                if( $d_heure_absence_eleve != $a_heure_absence_eleve )
                                                 {
                                                       $requete = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$eleve_absence_eleve."' AND d_date_absence_eleve = '".$d_date_absence_eleve."'  AND type_absence_eleve = 'I'";
                                                       $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
                                                       while ($data = mysql_fetch_array($resultat))
                                                        {
                                                              if ($d_heure_absence_eleve.":00" ==  $data['d_heure_absence_eleve'])
                                                               {
                                                                   $erreur=1;
                                                                   $erreur_valu=1;
                                                                   $erreur_aff_d_date_absence_eleve = date_fr($data['d_date_absence_eleve']);
                                                                   $erreur_aff_d_heure_absence_eleve = $data['d_heure_absence_eleve'];
                                                                   $erreur_aff_a_heure_absence_eleve = $data['a_heure_absence_eleve'];
                                                               }
                                                         }
                                                  } else { $erreur='1'; $erreur_valu='2'; $texte_erreur="L'heure de début ne pas être égale à l'heure de fin."; }
                                         } else { $erreur='1'; $erreur_valu='3'; $texte_erreur="Le format de l'heure (a) n'est pas correct."; }
                                   } else { $erreur='1'; $erreur_valu='4'; $texte_erreur="Le format de l'heure (de) n'est pas correct."; }
                            } else { $erreur='1'; $erreur_valu='5'; $texte_erreur="Aucune heure n'a été dans (a) saisie."; }
                     } else { $erreur='1'; $erreur_valu='6'; $texte_erreur="Aucune heure n'a été dans (de) saisie."; }
               } else { $erreur='1'; $erreur_valu='7'; $texte_erreur="La date n'est pas correct."; }

        if ($erreur === '1')
         {
              $type_absence_eleve_erreur[$j] = $type_absence_eleve;
              $eleve_absence_eleve_erreur[$j] = $eleve_absence_eleve;
              $info_justify_absence_eleve_erreur[$j] = $info_justify_absence_eleve;
              $d_date_absence_eleve_erreur[$j] = date_fr($d_date_absence_eleve);
              $d_heure_absence_eleve_erreur[$j] = $d_heure_absence_eleve;
              $a_heure_absence_eleve_erreur[$j] = $a_heure_absence_eleve;
              $j = $j + 1;
         } else{
                    if ( $action_sql == "ajouter" ) { $requete="INSERT INTO ".$prefix_base."absences_eleves (type_absence_eleve,eleve_absence_eleve, justify_absence_eleve,info_justify_absence_eleve,motif_absence_eleve,d_date_absence_eleve,a_date_absence_eleve,d_heure_absence_eleve,a_heure_absence_eleve,saisie_absence_eleve) values ('$type_absence_eleve','$eleve_absence_eleve','O','$info_justify_absence_eleve','$motif_absence_eleve','$d_date_absence_eleve','$a_date_absence_eleve','$d_heure_absence_eleve','$a_heure_absence_eleve','".$_SESSION['login']."')"; }
                    if ( $action_sql == "modifier" ) { $requete="UPDATE ".$prefix_base."absences_eleves SET info_justify_absence_eleve = '$info_justify_absence_eleve', d_date_absence_eleve = '$d_date_absence_eleve', a_date_absence_eleve = '$a_date_absence_eleve', d_heure_absence_eleve = '$d_heure_absence_eleve', a_heure_absence_eleve = '$a_heure_absence_eleve', saisie_absence_eleve = '".$_SESSION['login']."' WHERE id_absence_eleve = '".$id."'"; }
                    $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
               }
      $total = $total + 1;
    }

	if(!isset($eleve_absence_eleve_erreur[0]) and empty($eleve_absence_eleve_erreur[0]))
         {
           if ( $action_sql === "ajouter" ) { header("Location:select.php?type=I"); }
           if ( $action_sql === "modifier" ) { if($fiche === 'oui') { header("Location:gestion_absences.php?type=$type&select_fiche_eleve=$eleve_absence_eleve"); } else { header("Location:gestion_absences.php?type=$type"); } }
         }
}
 $datej = date('Y-m-d'); $annee_en_cours_t=annee_en_cours_t($datej);
 $datejour = date('d/m/Y');


if ($action === 'supprimer')
{

	if (empty($_GET['date_ce_jour']) and empty($_POST['date_ce_jour'])) { $date_ce_jour = ''; }
	   else { if (isset($_GET['date_ce_jour'])) { $date_ce_jour = $_GET['date_ce_jour']; } if (isset($_POST['date_ce_jour'])) { $date_ce_jour = $_POST['date_ce_jour']; } }

        $id_absence_eleve = $_GET['id'];
        // Vérification des champs
        if($id_absence_eleve != "")
        {
          //Requete d'insertion MYSQL
          $requete = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='$id_absence_eleve'";
          // Execution de cette requete
          mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
          header('Location:gestion_absences.php?type=I&date_ce_jour='.$date_ce_jour);
        }
}

$i = '0';
if ($action === 'modifier')
{
        $requete_modif = "SELECT * FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='$id'";
        $resultat_modif = mysql_query($requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.mysql_error());
        while ($data_modif = mysql_fetch_array($resultat_modif))
        {
            $type_absence_eleve[$i] = $data_modif['type_absence_eleve'];
            $eleve_absent[$i] = $data_modif['eleve_absence_eleve'];
            $info_justify_absence_eleve[$i] = $data_modif['info_justify_absence_eleve'];
            $d_date_absence_eleve[$i] = date_fr($data_modif['d_date_absence_eleve']);
            $a_date_absence_eleve[$i] = date_fr($data_modif['a_date_absence_eleve']);
            $d_heure_absence_eleve[$i] = $data_modif['d_heure_absence_eleve'];
            $a_heure_absence_eleve[$i] = $data_modif['a_heure_absence_eleve'];
          $i = $i + 1;
        }
}

$i = '0';
if(isset($eleve_absence_eleve_erreur[0]) and !empty($eleve_absence_eleve_erreur[0]))
{

          unset($type_absence_eleve);
          unset($eleve_absent);
          unset($info_justify_absence_eleve);
          unset($d_date_absence_eleve);
          unset($d_heure_absence_eleve);
          unset($a_heure_absence_eleve);

        while (isset($eleve_absence_eleve_erreur[$i]))
        {

            $type_absence_eleve[$i] = $type_absence_eleve_erreur[$i];
            $eleve_absent[$i] = $eleve_absence_eleve_erreur[$i];
            $info_justify_absence_eleve[$i] = $info_justify_absence_eleve_erreur[$i];
            $d_date_absence_eleve[$i] = $d_date_absence_eleve_erreur[$i];
            $d_heure_absence_eleve[$i] = $d_heure_absence_eleve_erreur[$i];
            $a_heure_absence_eleve[$i] = $a_heure_absence_eleve_erreur[$i];
	    if(isset($id) and !empty($id)) { $action = 'modifier'; }
            $i = $i + 1;
        }
}

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc");
//**************** FIN EN-TETE *****************
?>
<p class=bold>|<a href='gestion_absences.php?type=<?php echo $type; ?>'>Retour</a>|
</p><?php

if (empty($eleve_absent[1])==true) {

$i = 0;

        //Configuration du calendrier
          include("../../lib/calendrier/calendrier.class.php");
          $cal_1 = new Calendrier("form1", "d_date_absence_eleve[0]");
          $cal_2 = new Calendrier("form1", "a_date_absence_eleve[0]");

/* div de centrage du tableau pour ie5 */
?><div style="text-align:center"><?php

     //affichage des messages d'erreur
    if(isset($eleve_absence_eleve_erreur[0]) and !empty($eleve_absence_eleve_erreur[0])) { ?>
            <table class="table_erreur" border="0" cellpadding="1" cellspacing="2">
              <tr>
                <td><img src="../images/attention.png" width="28" height="28" alt="" /></td>
                <td class="erreur"><strong>Erreur: <?php echo $texte_erreur; ?></strong></td>
              </tr>
            </table>
      <?php }

      if($erreur_valu === '1') { ?>
              <div style="text-align:center">
              <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                 <tr class="fond_rouge">
                    <td class="norme_absence_blanc_min"><strong>Le</strong></td>
                    <td class="norme_absence_blanc_min"><strong>De</strong></td>
                    <td class="norme_absence_blanc_min"><strong>A</strong></td>
                 </tr>
                 <tr class="fond_rouge_2">
                     <td class="norme_absence_min"><?php echo $erreur_aff_d_date_absence_eleve; ?></td>
                     <td class="norme_absence_min"><?php echo $erreur_aff_d_heure_absence_eleve; ?></td>
                     <td class="norme_absence_min"><?php echo $erreur_aff_a_heure_absence_eleve; ?></td>
                 </tr>
              </table>
      <?php } ?>
    <form method="post" action="ajout_inf.php?type=<?php echo $type; ?>&amp;id=<?php echo $id; ?>" name="form1">
     <fieldset class="fieldset_efface">
      <table class="entete_tableau_absence" border="0" cellspacing="0" cellpadding="1">
        <tr style="background-image: url('../images/haut_tab.png');">
          <td class="titre_tableau_absence" nowrap><strong>Infirmerie</strong></td>
          <td class="titre_tableau_absence_valider"><input type="submit" name="submit" value="Valider" /></td>
        </tr>
        <tr class="tr_tableau_absence_titre">
          <td class="centre">Identit&eacute; de l'&eacute;l&egrave;ve</td>
          <td class="centre">Information de l'infirmerie</td>
        </tr>
        <tr class="td_tableau_absence_1">
          <td class="centre">
              <?php
               $requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$eleve_absent[$i]."'";
               $resultat_id = mysql_query($requete_id) or die('Erreur SQL !'.$requete_id.'<br />'.mysql_error());
                while($data_id = mysql_fetch_array ($resultat_id)) { ?>
                        <strong><?php echo strtoupper($data_id['nom']); ?></strong><br /><?php echo ucfirst($data_id['prenom']); $id_eleve = $data_id['login']; $id_eleve_photo = $data_id['elenoet']; ?><br /><span class="norme_absence_bleu"><strong><?php echo classe_de($data_id['login']); } ?></strong></span><br />
                <?php
                  $compte = mysql_result(mysql_query("SELECT COUNT(*) FROM ".$prefix_base."absences_eleves
                                                   WHERE eleve_absence_eleve='".$id_eleve."' AND type_absence_eleve='I'"),0);
                  if (getSettingValue("active_module_trombinoscopes")=='y') {
                  	  $nom_photo = '';
                      $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                      //$photo = "../../photos/eleves/".$nom_photo;
                      //if ( $nom_photo === '' or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                      if ( $nom_photo === NULL or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                      $valeur=redimensionne_image($photo);
                      ?><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><br /><?php
                   }
                 ?>
                 <table class="tableau_info_compt" border="0" cellspacing="0" cellpadding="2">
                   <tr>
                     <td>El&egrave;ve all&eacute;<br /><strong><?php if ($compte != 0) { ?><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&amp;type=<?php echo $type; ?>', 260, 320, 'scrollbars=yes,statusbar=no,resizable=yes');"><?php } ?><?php echo $compte; ?> fois<?php if ($compte != 0) { ?></a><?php } ?></strong><br />&agrave; l'infirmerie</td>
                   </tr>
                 </table>
          </td>
          <td>
              <table class="tableau_100" border="0" cellspacing="2" cellpadding="2">
                <tr class="tr_tableau_absence_titre">
                  <td><strong>Date</strong></td>
                  <td><strong>Heure</strong></td>
                </tr>
                <tr class="td_tableau_absence_1">
                  <td>le <input type="text" onfocus="javascript:this.select()" name="d_date_absence_eleve[<?php echo $i; ?>]" size="15" maxlength="15" value="<?php if(isset($d_date_absence_eleve[$i]) and !empty($d_date_absence_eleve[$i])) { echo $d_date_absence_eleve[$i]; } else { echo $datejour; } ?>" /><a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a></td>
                  <td>de <input onfocus="javascript:this.select()" name="d_heure_absence_eleve[<?php echo $i; ?>]" type="text" id="d_heure_absence_eleve" value="<?php if (isset($d_heure_absence_eleve[$i]) and !empty($d_heure_absence_eleve[$i])) { echo $d_heure_absence_eleve[$i]; } else { ?>00:00<?php } ?>" size="8" maxlength="8" /><br />&agrave; &nbsp;&nbsp;<input name="a_heure_absence_eleve[<?php echo $i; ?>]" onfocus="javascript:this.select()" type="text" id="a_heure_absence_eleve" value="<?php if (isset($a_heure_absence_eleve[$i]) and !empty($a_heure_absence_eleve[$i])) { echo $a_heure_absence_eleve[$i]; } else { ?>00:00<?php } ?>" size="8" maxlength="8" /></td>
                </tr>
                <tr class="tr_tableau_absence_titre">
                  <td colspan="2"><strong>Information de l'infirmerie</strong></td>
                </tr>
		<tr>
                  <td colspan="2">
                      <textarea name="info_justify_absence_eleve[<?php echo $i; ?>]" cols="20" rows="2" id="info_justify_absence_eleve"><?php if (isset($info_justify_absence_eleve[$i]) and !empty($info_justify_absence_eleve[$i])) { echo $info_justify_absence_eleve[$i]; } ?></textarea>
		      <input type="hidden" name="id" value="<?php echo $id; ?>" />
                      <input type="hidden" name="eleve_absence_eleve[<?php echo $i; ?>]" <?php  if ($action == "modifier") {?>value="<?php echo $eleve_absent[0]; ?>"<?php } else {?>value="<?php echo $eleve_absent[0]; ?>"<?php } ?> />
                      <input type="hidden" name="eleve_absence[<?php echo $i; ?>]" value="<?php echo $eleve_absent[$i]; ?>" />
                      <input type="hidden" name="type_absence_eleve" <?php  if ($action == "modifier") {?>value="<?php echo $type; ?>"<?php } else {?>value="<?php echo $type; ?>"<?php } ?> />
		      <input type="hidden" name="fiche" value="<?php echo $fiche; ?>" />
                      <input type="hidden" name="action_sql" <?php  if ($action == "modifier") {?>value="modifier"<?php } else {?>value="ajouter"<?php } ?> />
                      <input type="hidden" name="nb_i" value="<?php echo $i+1; ?>" />
                  <td>
                </tr>
                </table>
          </td>
        </tr>
      </table>
     </fieldset>
    </form>
<?php
        $requete = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$id_eleve."' AND d_date_absence_eleve = '".$datej."' AND type_absence_eleve = 'I'";
        $resultat = mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
?>
                <div class="norme_absence_rouge"><strong>liste des passages à l'infirmerie déjas enregistré pour cette date</strong></div>
                <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                  <tr class="fond_rouge">
                     <td class="norme_absence_blanc_min"><strong>Le</strong></td>
                     <td class="norme_absence_blanc_min"><strong>De</strong></td>
                     <td class="norme_absence_blanc_min"><strong>A</strong></td>
                 </tr>
         <?php while ($data = mysql_fetch_array($resultat))
               { ?>
                 <tr class="fond_rouge_2">
                    <td class="norme_absence_min"><?php echo date_fr($data['d_date_absence_eleve']); ?></td>
                    <td class="norme_absence_min"><?php echo $data['d_heure_absence_eleve']; ?></td>
                    <td class="norme_absence_min"><?php echo $data['a_heure_absence_eleve']; ?></td>
                 </tr>
         <?php } ?>
               </table>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>
<?php mysql_close(); ?>

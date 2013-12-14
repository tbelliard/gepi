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


//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal_1 = new Calendrier("form1", "du_dispense_eleve");
$cal_2 = new Calendrier("form1", "au_dispense_eleve");
  if (empty($_GET['page']) and empty($_POST['page'])) { $page=""; }
    else { if (isset($_GET['page'])) { $page=$_GET['page']; } if (isset($_POST['page'])) { $page=$_POST['page']; } }
  if (empty($_POST['action_sql'])) { $action_sql = ''; } else { $action_sql=$_POST['action_sql']; }
  if (empty($_POST['eleve_absent'])) { $eleve_absent = ''; } else { $eleve_absent=$_POST['eleve_absent']; }
  if (empty($_POST['eleve_dispense'])) { $eleve_dispense = ''; } else { $eleve_dispense=$_POST['eleve_dispense']; }
  if (empty($_GET['action'])) { $action = ''; } else { $action=$_GET['action']; }
  if (empty($_GET['type'])) { $type = ''; } else { $type=$_GET['type']; }
  if (empty($_POST['id'])) { $id_dispense_eleve = ''; } else { $id_dispense_eleve=$_POST['id']; }
  if (empty($_GET['id'])) { $id = ''; } else { $id=$_GET['id']; }

if (empty($_GET['fiche']) and empty($_POST['fiche'])) {$fiche="";}
    else { if (isset($_GET['fiche'])) {$fiche=$_GET['fiche'];} if (isset($_POST['fiche'])) {$fiche=$_POST['fiche'];} }

// si pas de sélection on retourne à la sélection
if((empty($classe_choix) or $classe_choix === 'tous') and empty($eleve_absent[0]) and empty($id) and $action_sql === '') { header("Location:select.php?type=$type"); }

if($id == "" and $eleve_absent == "" and $eleve_dispense == "") { header("Location:select.php?type=$type"); }

// id de la dispense
  if (empty($_GET['id_dispense']) and empty($_POST['id_dispense'])) {$id_dispense="";}
      else { if (isset($_GET['id_dispense'])) {$id_dispense=$_GET['id_dispense'];} if (isset($_POST['id_dispense'])) {$id_dispense=$_POST['id_dispense'];} }

$total = '0';
$erreur_valu = '0';
$erreur = '0';
$j = '0';
$verification = '0';

// christian 19
//if(!isset($eleve_absent[0]) and empty($eleve_absent[0]) and $action_sql === '' and $id === '' and $erreur === '') { header("Location:select.php?type=$type"); }

if($action_sql == "ajouter" OR $action_sql == "modifier")
{
       $eleve_absence_eleve=$_POST['eleve_dispense'];
       $d_date_absence_eleve=date_sql($_POST['du_dispense_eleve']);
       $a_date_absence_eleve=date_sql($_POST['au_dispense_eleve']);
       $info_justify_absence_eleve=$_POST['info_dispense_eleve'];
       $info_absence_eleve=$_POST['quand_dispense'];

       if($info_justify_absence_eleve!="") { $justify_absence_eleve = "O"; } else {  $justify_absence_eleve = "N"; }

    //si au est vide on copie du dans au
       if ($a_date_absence_eleve=="AAAA-MM-JJ" or $a_date_absence_eleve=="" or $a_date_absence_eleve=="--") { $a_date_absence_eleve=$d_date_absence_eleve; }

     // on explose la date en plusieur partie jour mois année
       $d_date_absence_eleve_verif = explode('-',$d_date_absence_eleve);
       $a_date_absence_eleve_verif = explode('-',$a_date_absence_eleve);


     // vérification des date saisies
       if(verif_date($d_date_absence_eleve) == "pass")
        {
              $verification = '1';
              if(verif_date($a_date_absence_eleve) == "pass")
               {
                     $verification = '1';
                     if ($d_date_absence_eleve <= $a_date_absence_eleve)
                      {
                            $verification = '1';
                            if (date("w", mktime(0, 0, 0, $d_date_absence_eleve_verif[1], $d_date_absence_eleve_verif[2], $d_date_absence_eleve_verif[0])) != 0)
                            {
                                   $verification = '1';
                                   if (date("w", mktime(0, 0, 0, $a_date_absence_eleve_verif[1], $a_date_absence_eleve_verif[2], $a_date_absence_eleve_verif[0])) != 0)
                                    {
                                        $verification = '1';
                                    } else { $verification = '7'; $erreur = '1'; $texte_erreur = "la date de fin tombe un dimanche."; }
                             } else { $verification = '6'; $erreur = '1'; $texte_erreur = "la date de debut tombe un dimanche."; }
                      } else { $verification = '8'; $erreur='1'; $texte_erreur = "La date du debut ne peut être plus grande que celle de fin."; }
               } else { $verification = '4'; $erreur = '1'; $texte_erreur = "La date de fin n'est pas correcte."; }
        } else { $verification = '5'; $erreur = '1'; $texte_erreur = "La date de debut n'est pas correcte."; }

        // si les date sont OK alors on vérifie quelle sont les jour données
          if ($verification === '1' and $erreur === '0')
                 {
                      $erreur_jour = '1';
                      for ($i = 0;$i <= mb_strlen($info_absence_eleve);$i++)
                       {
                           switch (mb_strtolower(mb_substr($info_absence_eleve,$i,5)))
                           {
                               case "lundi":
                               $erreur_jour = '0';
                               break;
                               case "mardi":
                               $erreur_jour = '0';
                               break;
                               case "mercr":
                               $erreur_jour = '0';
                               break;
                               case "jeudi":
                               $erreur_jour = '0';
                               break;
                               case "vendr":
                               $erreur_jour = '0';
                               break;
                               case "samed":
                               $erreur_jour = '0';
                               break;
                               default:
                               break;
                           }

                           // on vérifie que les périodes existe bien dans la base
                              $sql = "SELECT * from ".$prefix_base."edt_creneaux";
                              $resultat = mysqli_query($GLOBALS["mysqli"], $sql) or die('Erreur SQL !'.$sql.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                              while ($data_Per = mysqli_fetch_array($resultat))
                               {
                                   $p = $data_Per['nom_definie_periode'];
                                   for($i =0;$i<mb_strlen($info_absence_eleve);$i++)
                                    {
                                        if (mb_strtolower($p) == mb_strtolower(mb_substr($info_absence_eleve,$i,2)))
                                         {
                                           $verification = '12'; $texte_erreur = "Cette période n'est pas bonne!";
                                         }
                                    }
                               }
                        }

                      // s'il y a une erreur dans la saisie des jours
                        if ($erreur_jour != '1')
                         {
                              if ( $action_sql == "ajouter" ) { $requete="INSERT INTO ".$prefix_base."absences_eleves (type_absence_eleve,eleve_absence_eleve,justify_absence_eleve,info_justify_absence_eleve,motif_absence_eleve,info_absence_eleve,d_date_absence_eleve,a_date_absence_eleve,saisie_absence_eleve) values ('D','$eleve_absence_eleve','$justify_absence_eleve','$info_justify_absence_eleve','DI','$info_absence_eleve','$d_date_absence_eleve','$a_date_absence_eleve','".$_SESSION['login']."')"; }
                              if ( $action_sql == "modifier" ) { $requete="UPDATE ".$prefix_base."absences_eleves SET
                                                                                                justify_absence_eleve = '$justify_absence_eleve',
                                                                                                info_justify_absence_eleve = '$info_justify_absence_eleve',
                                                                                                motif_absence_eleve = 'DI',
                                                                                                info_absence_eleve = '$info_absence_eleve',
                                                                                                d_date_absence_eleve = '$d_date_absence_eleve',
                                                                                                a_date_absence_eleve = '$a_date_absence_eleve',
                                                                                                saisie_absence_eleve = '".$_SESSION['login']."'
                                                                                                WHERE id_absence_eleve = '".$id_absence_eleve."'"; }
                              $resultat = mysqli_query($GLOBALS["mysqli"], $requete) or die('Erreur SQL !'.$requete.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                         } else { $verification = '3'; $erreur='1'; $texte_erreur = "Il y a une erreur dans la  spécificationr des jours."; }
                 }

          if( $erreur!='1' ) { if( $fiche === 'oui' ) { header("Location:gestion_absences.php?type=$type&select_fiche_eleve=$eleve_absence_eleve&aff_fiche=abseleve#abseleve"); } else { header("Location:gestion_absences.php?type=$type"); } }

}

if ($action === 'supprimer')
{

	if (empty($_GET['date_ce_jour']) and empty($_POST['date_ce_jour'])) { $date_ce_jour = ''; }
	   else { if (isset($_GET['date_ce_jour'])) { $date_ce_jour = $_GET['date_ce_jour']; } if (isset($_POST['date_ce_jour'])) { $date_ce_jour = $_POST['date_ce_jour']; } }

        $id_dispense_eleve = $_GET['id'];
        // Vérification des champs
        $requete_sup = "SELECT eleve_absence_eleve FROM ".$prefix_base."absences_eleves
								WHERE id_absence_eleve ='$id_dispense_eleve'";
	   $resultat_sup = mysqli_query($GLOBALS["mysqli"], $requete_sup) or die('Erreur SQL !'.$requete_sup.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	   $login_eleve = mysqli_fetch_array($resultat_sup);
        if($id_dispense_eleve != '')
        {
          //Requete d'insertion MYSQL
            $requete_del = "DELETE FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='$id_dispense_eleve'";
          // Execution de cette requete
            mysqli_query($GLOBALS["mysqli"], $requete_del) or die('Erreur SQL !'.$requete_del.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
            if($fiche === 'oui') {
		 	header("Location:gestion_absences.php?type=A&select_fiche_eleve=$login_eleve[0]&aff_fiche=abseleve#abseleve");
			} else {
				header("Location:gestion_absences.php?type=A");
			}
        }
}


$i = '0';
if ($action === 'modifier')
{
        $requete_modif = "SELECT * FROM ".$prefix_base."absences_eleves WHERE id_absence_eleve ='".$id."'";
        $resultat_modif = mysqli_query($GLOBALS["mysqli"], $requete_modif) or die('Erreur SQL !'.$requete_modif.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
        while ($data_modif = mysqli_fetch_array($resultat_modif))
        {
          $eleve_dispense_eleve = $data_modif['eleve_absence_eleve'];
          $eleve_absent[0] = $data_modif['eleve_absence_eleve'];
          $du_dispense_eleve = date_fr($data_modif['d_date_absence_eleve']);
          $au_dispense_eleve = date_fr($data_modif['a_date_absence_eleve']);
          $quand_dispense = $data_modif['info_absence_eleve'];
          $info_dispense_eleve = $data_modif['info_justify_absence_eleve'];
          $i = $i + 1;
        }
}

 // définition des date et autres infos
 $datej = date('Y-m-d');
 $annee_en_cours_t=annee_en_cours_t($datej);
 $datejour = date('d/m/Y');

//**************** EN-TETE *****************
$titre_page = "Gestion des absences";
require_once("../../lib/header.inc.php");
//**************** FIN EN-TETE *****************
?>

<script type="text/javascript" language="javascript">
<!--
function getDate(input_pass,form_choix){
 var date_select=new Date();
 var jour=date_select.getDate(); if(jour<10){jour="0"+jour;}
 var mois=date_select.getMonth()+1; if(mois<10){mois="0"+mois;}
 var annee=date_select.getFullYear();
 var date_jour = jour+"/"+mois+"/"+annee;
// nom du formulaire
  var form_action = form_choix;
// id des élèments
  var input_pass_id = input_pass.id;
  var input_pass_value = input_pass.value;
// modifie le contenue de l'élèment
if(document.forms[form_action].elements[input_pass_id].value=='JJ/MM/AAAA' || document.forms[form_action].elements[input_pass_id].value=='') { document.forms[form_action].elements[input_pass_id].value=date_jour; }
}
 // -->
</script>

<p class=bold>|<a href='gestion_absences.php?type=<?php echo $type; ?><?php if($fiche==='oui') { ?>&select_fiche_eleve=<?php echo $eleve_absent[0];?>&aff_fiche=abseleve#abseleve<?php } ?>'>Retour</a>|
</p><?php

if ($action == "ajouter" or $action == "modifier" or $erreur = 1)
{

//affichage des messages d'erreur
        if ($erreur == 1) { ?>
            <?php /* div de centrage du tableau pour ie5 */ ?>
            <div style="text-align:center">
            <table class="table_erreur" border="0" cellpadding="2" cellspacing="2">
              <tr>
                <td><img src="../images/attention.png" alt="" /></td>
                <td class="erreur"><strong>Erreur : <?php echo $texte_erreur; ?></strong></td>
              </tr>
            </table>
            <?php /* fin du div de centrage du tableau pour ie5 */ ?>
            </div>
        <?php } ?>

<?php /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align:center">
  <form method="post" action="ajout_dip.php?type=<?php echo $type; ?>" name="form1">
    <fieldset class="fieldset_efface">
      <table class="entete_tableau_absence" border="0" cellspacing="0" cellpadding="1">
        <tr>
          <td class="titre_tableau_absence" nowrap><strong>Dispense</strong></td>
          <td class="titre_tableau_absence_valider"><input type="hidden" name="action_sql" <?php  if ($action == "modifier") {?>value="modifier"<?php } else {?>value="ajouter"<?php } ?> /><input type="submit" name="submit" value="Valider" /></td>
        </tr>
        <tr class="tr_tableau_absence_titre">
          <td class="centre">Identit&eacute; de l'&eacute;l&egrave;ve</td>
          <td class="centre">Information sur la dispense</td>
        </tr>
        <tr class="td_tableau_absence_1">
          <td class="centre">
              <?php
                $requete_id="SELECT * FROM ".$prefix_base."eleves WHERE login='".$eleve_absent[0]."'";
                $resultat_id = mysqli_query($GLOBALS["mysqli"], $requete_id) or die('Erreur SQL !'.$requete_id.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
                While ( $data_id = mysqli_fetch_array($resultat_id)) {
              ?>
                        <strong><?php echo strtoupper($data_id['nom']); ?></strong><br /><?php echo ucfirst($data_id['prenom']); $id_eleve = $data_id['login']; $id_eleve_photo = $data_id['elenoet']; ?><br /><span class="norme_absence_bleu"><strong><?php echo classe_de($data_id['login']); } ?></strong></span><br />
              <?php
              if (getSettingValue("active_module_trombinoscopes")=='y') {
                   	  $nom_photo = '';
                      $nom_photo = nom_photo($id_eleve_photo,"eleves",2);
                      //if ( $nom_photo === '' or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
					  if ( $nom_photo === NULL or !file_exists($photo) ) { $photo = "../../mod_trombinoscopes/images/trombivide.jpg"; }
                      $valeur=redimensionne_image($photo);
                      ?><img src="<?php echo $photo; ?>" style="width: <?php echo $valeur[0]; ?>px; height: <?php echo $valeur[1]; ?>px; border: 0px" alt="" title="" /><br /><?php
              }

                   $test_dispense = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$id_eleve."' AND type_absence_eleve='D'"),0);
                   if ($test_dispense != 0)
                    {  ?>
                        <table class="tableau_info_compt" border="0" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="tableau_info_compt"><a href="javascript:centrerpopup('../lib/liste_absences.php?id_eleve=<?php echo $id_eleve; ?>&type=<?php echo $type; ?>', 260, 320, 'scrollbars=yes,statusbar=no,resizable=yes');">voir ces dispenses</a></td>
                          </tr>
                        </table>
              <?php } ?>
         </td>
         <td>
          <table class="tableau_100" border="0" cellspacing="2" cellpadding="2">
            <tr class="tr_tableau_absence_titre">
              <td colspan="2"><strong>Date</strong></td>
            </tr>
            <tr>
              <td class="td_tableau_absence_1">du <input name="du_dispense_eleve" onfocus="javascript:this.select()" type="text" id="du_dispense_eleve" value="<?php if($action=="modifier") { echo $du_dispense_eleve; } else { if(isset($du_dispense_eleve)) { echo $du_dispense_eleve; } else { echo $datejour; } } ?>" size="12" maxlength="15" /><a href="#calend" onClick="<?php echo $cal_1->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a></td>
              <td class="td_tableau_absence_1">au <input name="au_dispense_eleve" onfocus="javascript:this.select()" onClick="getDate(au,'form1')" type="text" id="au" value="<?php if($action=="modifier") { echo $au_dispense_eleve; } else { if(isset($au_dispense_eleve)) { echo $au_dispense_eleve; } else { ?>JJ/MM/AAAA<?php } } ?>" size="12" maxlength="15" /><a href="#calend" onClick="<?php echo $cal_2->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a></td>
            </tr>
            <tr>
              <td colspan="2" class="norme_absence_bleu"><strong>!</strong>Si la date du et au sont identiques ne renseignez que "du"</td>
            </tr>
          </table>
        Sp&eacute;cifier les jours<br /><input name="quand_dispense" onfocus="javascript:this.select()" type="text" id="quand_dispense" <?php if($action=="modifier") { ?>value="<?php echo $quand_dispense; ?>"<?php } ?> />ex: lundi-M1-M2; mardi S2<br />
        Information sur la dispense<br /><textarea name="info_dispense_eleve" cols="40" rows="2" id="info_dispense_eleve"><?php  if($action=="modifier") { echo $info_dispense_eleve; }?></textarea>
        <input type="hidden" name="eleve_dispense" value="<?php if($action=="modifier") { echo $eleve_dispense_eleve; } else { echo $id_eleve; } ?>" />
        <input type="hidden" name="eleve_absent[0]" value="<?php if($action=="modifier") { echo $eleve_dispense_eleve; } else { echo $eleve_absent[0]; } ?>" />
        <input type="hidden" name="fiche" value="<?php echo $fiche; ?>" />
        <?php if($action=="modifier") { ?><input type="hidden" name="id_absence_eleve" id="id_absence_eleve" value="<?php echo $id; ?>" /><?php } ?>
        <input type="hidden" name="type_absence_eleve" value="<?php echo $type; ?>" />
      </td>
    </tr>
  </table>
 </fieldset>
</form>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
        $requete_t = "SELECT * FROM ".$prefix_base."absences_eleves WHERE eleve_absence_eleve='".$id_eleve."' AND  d_date_absence_eleve <= '".$datej."' AND   a_date_absence_eleve >= '".$datej."' and type_absence_eleve = 'D'";
        $resultat = mysqli_query($GLOBALS["mysqli"], $requete_t) or die('Erreur SQL !'.$requete_t.'<br />'.((is_object($GLOBALS["mysqli"])) ? mysqli_error($GLOBALS["mysqli"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>
                 <div class="norme_absence_rouge"><strong>liste des dispenses déjas enregistré pour cette date</strong></div>
                 <?php /* div de centrage du tableau pour ie5 */ ?>
                 <div style="text-align:center">
                 <table style="margin: auto; width: 500px;" border="0" cellspacing="2" cellpadding="0">
                 <tr class="fond_rouge">
                      <td class="norme_absence_blanc"><strong>Du</strong></td>
                      <td class="norme_absence_blanc"><strong>Au</strong></td>
                 </tr>
         <?php while ($data = mysqli_fetch_array($resultat))
               { ?>
                 <tr class="fond_rouge_2">
                      <td class="norme_absence_min"><?php echo $data['d_date_absence_eleve']; ?></td>
                      <td class="norme_absence_min"><?php echo $data['a_date_absence_eleve']; ?></td>
                 </tr>
         <?php } ?>
                 </table>
	         </div>
<?php } ?>

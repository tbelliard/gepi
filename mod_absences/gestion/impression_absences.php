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
$resultat_session = resumeSession();
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
require_once("../../lib/header.inc");
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
<?php
//Configuration du calendrier
include("../../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form1", "du");
$cal_3 = new Calendrier("form3", "du");
$cal_4 = new Calendrier("form3", "au");
$cal_5 = new Calendrier("form5", "du");
$cal_6 = new Calendrier("form5", "au");


    $date_ce_jour = date('d/m/Y');

   if (empty($_GET['type']) AND empty($_POST['type'])) {$type="A";}
    else { if (isset($_GET['type'])) {$type=$_GET['type'];} if (isset($_POST['type'])) {$type=$_POST['type'];} }
   if (empty($_GET['type_impr']) AND empty($_POST['type_impr'])) {$type_impr="laf";}
    else { if (isset($_GET['type_impr'])) {$type_impr=$_GET['type_impr'];} if (isset($_POST['type_impr'])) {$type_impr=$_POST['type_impr'];} }
   if (empty($_GET['choix']) AND empty($_POST['choix'])) {$choix="nonjustifie";}
    else { if (isset($_GET['choix'])) {$a_imprimer=$_GET['choix'];} if (isset($_POST['choix'])) {$choix=$_POST['choix'];} }
   if (empty($_GET['a_imprimer']) AND empty($_POST['a_imprimer'])) {$a_imprimer="";}
    else { if (isset($_GET['a_imprimer'])) {$a_imprimer=$_GET['a_imprimer'];} if (isset($_POST['a_imprimer'])) {$a_imprimer=$_POST['a_imprimer'];} }
   if (empty($_GET['classe']) AND empty($_POST['classe'])) {$classe="tous";}
    else { if (isset($_GET['classe'])) {$classe=$_GET['classe'];} if (isset($_POST['classe'])) {$classe=$_POST['classe'];} }
   if (empty($_GET['eleve']) AND empty($_POST['eleve'])) {$eleve="";}
    else { if (isset($_GET['eleve'])) {$eleve=$_GET['eleve'];} if (isset($_POST['eleve'])) {$eleve=$_POST['eleve'];} }
   if (empty($_GET['id_eleve']) AND empty($_POST['id_eleve'])) {$id_eleve="";}
    else { if (isset($_GET['id_eleve'])) {$id_eleve=$_GET['id_eleve'];} if (isset($_POST['id_eleve'])) {$id_eleve=$_POST['id_eleve'];} }
   if (empty($_GET['id_classe']) AND empty($_POST['id_classe'])) {$id_classe="";}
    else { if (isset($_GET['id_classe'])) {$id_classe=$_GET['id_classe'];} if (isset($_POST['id_classe'])) {$id_classe=$_POST['id_classe'];} }
   if (empty($_GET['du']) AND empty($_POST['du'])) {$du="$date_ce_jour";}
    else { if (isset($_GET['du'])) {$du=$_GET['du'];} if (isset($_POST['du'])) {$du=$_POST['du'];} }
   if (empty($_GET['au']) AND empty($_POST['au'])) {$au="JJ/MM/AAAA";}
    else { if (isset($_GET['au'])) {$au=$_GET['au'];} if (isset($_POST['au'])) {$au=$_POST['au'];} }
   if (empty($_GET['composer']) AND empty($_POST['composer'])) {$composer="";}
    else { if (isset($_GET['composer'])) {$composer=$_GET['composer'];} if (isset($_POST['composer'])) {$composer=$_POST['composer'];} }
   if (empty($_GET['id_absence']) AND empty($_POST['id_absence'])) {$id_absence="";}
    else { if (isset($_GET['id_absence'])) {$id_absence=$_GET['id_absence'];} if (isset($_POST['id_absence'])) {$id_absence=$_POST['id_absence'];} }
   if (empty($_GET['cocher']) AND empty($_POST['cocher'])) {$cocher="";}
    else { if (isset($_GET['cocher'])) {$cocher=$_GET['cocher'];} if (isset($_POST['cocher'])) {$cocher=$_POST['cocher'];} }

//requête :
$sql_cpe = 'SELECT '.$prefix_base.'utilisateurs.login, '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom, '.$prefix_base.'utilisateurs.civilite FROM '.$prefix_base.'utilisateurs WHERE '.$prefix_base.'utilisateurs.statut="cpe" ORDER BY '.$prefix_base.'utilisateurs.nom, '.$prefix_base.'utilisateurs.prenom ASC';
?>

<p class=bold>|<a href='../gestion/gestion_absences.php?type=<?php echo $type; ?>'>Retour</a>|
</p>
<div class="norme_absence centre">[ <a href="impression_absences.php?type_impr=laf">Lettres aux familles</a> | <a href="impression_absences.php?type_impr=bda">Bilan des absences</a> | <a href="impression_absences.php?type_impr=bpc">Bilan pour les conseils</a> ]</div><br />

<?php if($type_impr == "laf") { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
    <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form1">
      <fieldset style="width: 450px; margin: auto;">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Lettres d'absences aux familles</div>
            <div class="norme_absence" style="text-align:left">[<input type="radio" name="choix" value="nonjustifie" <?php if(isset($choix) and $choix == "nonjustifie") {?> checked<?php } ?> />Simple <input type="radio" name="choix" value="rappel" <?php if(isset($choix) and $choix == "rappel") {?> checked<?php } ?> />Rappel ] A la date du <input name="du" type="text" size="11" maxlength="11" value="<?php if(isset($du)) { echo $du; } ?>" /><a href="#calend" onClick="<?php echo $cal->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> <input type="submit" name="Submit" value="&gt;&gt;" /></div>
	    <?php /* <div class="norme_absence" style="text-align:left"><input type="checkbox" name="choix_du_type[]" value="1">Absences | <input type="checkbox" name="choix_du_type[]" value="1">Retard </div> */ ?>
      </fieldset>
    </form>

   <form method="post" action="lettre_aux_parents.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form2">
      <?php if($type_impr == "laf" and ($choix == "nonjustifie" or $choix == "justifie")) { ?>
        <table style="width: 600px; margin: auto;" border="0" cellpadding="0" cellspacing="1">
          <tr class="fond_vert">
            <td class="norme_absence_blanc" style="width: 50px;"><strong>Lettre</strong></td>
            <td class="norme_absence_blanc" style="width: 50px;"><strong>Mel</strong></td>
            <td class="norme_absence_blanc"><strong>Identit&eacute;</strong></td>
            <td class="norme_absence_blanc"><strong>Expéditeur</strong></td>
          </tr>
        <?php
           $i = 0;
           $ic = 1;
           if($choix == "nonjustifie") { $requete_1 ="SELECT DISTINCT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve='A' AND (d_date_absence_eleve = '".date_sql($du)."' OR (d_date_absence_eleve <= '".date_sql($du)."' AND a_date_absence_eleve >= '".date_sql($du)."')) AND justify_absence_eleve != 'O' AND eleve_absence_eleve=login"; }
           if($choix == "justifie") { $requete_1 ="SELECT DISTINCT ".$prefix_base."eleves.login, ".$prefix_base."eleves.nom, ".$prefix_base."eleves.prenom, ".$prefix_base."absences_eleves.type_absence_eleve, ".$prefix_base."absences_eleves.d_date_absence_eleve, ".$prefix_base."absences_eleves.a_date_absence_eleve, ".$prefix_base."absences_eleves.justify_absence_eleve, ".$prefix_base."absences_eleves.eleve_absence_eleve FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve='A' AND (d_date_absence_eleve = '".date_sql($du)."' OR (d_date_absence_eleve <= '".date_sql($du)."' AND a_date_absence_eleve >= '".date_sql($du)."')) AND justify_absence_eleve = 'O' AND eleve_absence_eleve=login"; }
           $execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
           while ( $data_1 = mysql_fetch_array($execution_1))
                 {
                    if ($ic==1) {
                                  $ic=2;
                                  $couleur_cellule="td_tableau_absence_1";
                               } else {
                                          $couleur_cellule="td_tableau_absence_2";
                                          $ic=1;
                                       }
                  ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
                    <td class="centre"><input type="checkbox" name="a_imprimer[<?php echo $i; ?>]" value="1"  <?php if(isset($a_imprimer[$i]) and $a_imprimer[$i] == "1" OR $cocher == 1) {?> checked<?php } ?> /><input type="hidden" name="id_eleve[<?php echo $i; ?>]" value="<?php echo $data_1['login']; ?>" /></td>
                    <td class="centre"><input type="checkbox" name="a_imprimer[<?php echo $i; ?>]" value="1"  <?php if(isset($a_imprimer[$i]) and $a_imprimer[$i] == "1" OR $cocher == 1) {?> checked<?php } ?>  disabled="disabled" /><input type="hidden" name="id_eleve[<?php echo $i; ?>]" value="<?php echo $data_1['login']; ?>" /></td>
                    <td class="norme_absence"><?php echo strtoupper($data_1['nom'])." ".ucfirst($data_1['prenom']); ?></td>
                    <td>
                        <select name="cpe[<?php echo $i; ?>]">
                        <?php if($i!=0) { ?><option value="idem">idem</option><?php } ?>
                        <?php
                             $req_cpe = mysql_query($sql_cpe) or die('Erreur SQL ! '.$sql_cpe.' '.mysql_error());

                              while($data_cpe = mysql_fetch_array($req_cpe))
                              {
                                echo "<option value=\"".$data_cpe['login']."\"";
                                if ($eleve == $data_cpe['login']) {echo " selected";}
                                echo " >".strtoupper($data_cpe['nom'])." ".ucfirst($data_cpe['prenom'])."</option>";
                              }
                        ?>
                        </select>
                    </td>
                  </tr>
              <?php $i = $i + 1; } ?>
          <tr>
            <td colspan="3" class="norme_absence"><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;du=<?php echo $du; ?>&amp;choix=<?php echo $choix; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a></td>
            <td class="centre">
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="nbi" value="<?php echo $i; ?>" />
                <input type="submit" name="Submit3" value="Composer" />
            </td>
          </tr>
        </table>


    <?php } if($type_impr == "laf" and $choix == "rappel") { ?>
        <table style="width: 600px; margin: auto;" border="0" cellpadding="0" cellspacing="1">
          <tr class="fond_vert">
            <td class="norme_absence_blanc"><strong>&agrave; imprimer</strong></td>
            <td class="norme_absence_blanc"><strong>Identit&eacute;e</strong></td>
            <td></td>
            <?php
               $i = 0;
               $ic = 1;
               $requete_1 ="SELECT DISTINCT login, nom, prenom FROM ".$prefix_base."absences_eleves, ".$prefix_base."eleves WHERE type_absence_eleve='A' AND (d_date_absence_eleve = '".date_sql($du)."' OR (d_date_absence_eleve <= '".date_sql($du)."' AND a_date_absence_eleve >= '".date_sql($du)."')) AND justify_absence_eleve != 'O' AND eleve_absence_eleve=login";
               $execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
               while ( $data_1 = mysql_fetch_array($execution_1))
                     {
                      if ($ic==1) {
                                      $ic=2;
                                      $couleur_cellule="td_tableau_absence_1";
                                   } else {
                                              $couleur_cellule="td_tableau_absence_2";
                                              $ic=1;
                                           }
             ?>
          </tr>
          <tr class="<?php echo $couleur_cellule; ?>">
            <td class="centre"><input type="checkbox" name="a_imprimer[<?php echo $i; ?>]" value="1"  <?php if(isset($a_imprimer[$i]) and $a_imprimer[$i] == "1" OR $cocher == 1) {?> checked<?php } ?> /><input type="hidden" name="id_eleve[<?php echo $i; ?>]" value="<?php echo $data_1['login']; ?>" /></td>
            <td class="norme_absence"><?php echo strtoupper($data_1['nom'])." ".ucfirst($data_1['prenom']); ?></td>
            <td>
                 <select name="cpe[<?php echo $i; ?>]">
                        <?php if($i!=0) { ?><option value="idem">idem</option><?php } ?>
                        <?php
                             $req_cpe = mysql_query($sql_cpe) or die('Erreur SQL ! '.$sql_cpe.' '.mysql_error());

                             while($data_cpe = mysql_fetch_array($req_cpe))
                              {
                                echo "<option value=\"".$data_cpe['login']."\"";
                                if ($eleve == $data_cpe['login']) {echo " selected";}
                                echo " >".strtoupper($data_cpe['nom'])." ".ucfirst($data_cpe['prenom'])."</option>";
                              }
                        ?>
                </select>
            </td>
          </tr>
          <?php $i = $i + 1; } ?>
          <tr>
            <td colspan="2" class="norme_absence"><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;du=<?php echo $du; ?>&amp;cocher=1&amp;choix=<?php echo $choix; ?>">Cocher toutes les cellules</a></td>
            <td class="centre">
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="nbi" value="<?php echo $i; ?>" />
                <input type="submit" name="Submit32" value="Composer" />
            </td>
          </tr>
        </table>
    <?php } ?>
       </form>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>


<?php if($type_impr == "bda") { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form3">
      <fieldset style="width: 450px; margin: auto;">
         <legend class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan des absences général</div>
            <div class="norme_absence" style="text-align:left">
            Classe
                <select name="classe">
                    <option value="tous">toutes</option>
                    <?php
                          $sql_classe = 'SELECT id, classe, nom_complet FROM '.$prefix_base.'classes ORDER BY nom_complet DESC';
                          $req_classe = mysql_query($sql_classe) or die('Erreur SQL ! '.$sql_classe.' '.mysql_error());

                          while($data_classe = mysql_fetch_array($req_classe))
                          {
                            echo "<option value=\"".$data_classe['id']."\"";
                            if ($classe == $data_classe['id']) {echo " selected";}
                            echo " onClick=\"javascript:document.form3.submit()\">".$data_classe['nom_complet']."</option>";
                          }
                    ?>
                </select><br />
                Elève
                <select name="eleve">
                    <option value="tous">tous</option>
                    <?php
                         if( $classe == "tous" ) { $sql_eleve = 'SELECT '.$prefix_base.'eleves.login, nom, prenom FROM '.$prefix_base.'eleves ORDER BY nom, prenom ASC'; }
                         if( $classe != "tous" ) { $sql_eleve = 'SELECT DISTINCT '.$prefix_base.'eleves.login, nom, prenom, '.$prefix_base.'j_eleves_classes.login, id_classe FROM '.$prefix_base.'eleves, '.$prefix_base.'j_eleves_classes WHERE '.$prefix_base.'j_eleves_classes.login='.$prefix_base.'eleves.login AND id_classe="'.$classe.'" ORDER BY nom, prenom ASC'; }

                          $req_eleve = mysql_query($sql_eleve) or die('Erreur SQL ! '.$sql_eleve.' '.mysql_error());

                          while($data_eleve = mysql_fetch_array($req_eleve))
                          {
                            echo "<option value=\"".$data_eleve['login']."\"";
                            if ($eleve == $data_eleve['login']) {echo " selected";}
                            echo " >".strtoupper($data_eleve['nom'])." ".ucfirst($data_eleve['prenom'])."</option>";
                          }
                    ?>
                </select>
                <br />
                du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_3->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form3')" /><a href="#calend" onClick="<?php  echo $cal_4->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>&nbsp;<input type="submit" name="Submit2" value="&gt;&gt;" />
            </div>
      </fieldset>
    </form>

     <form method="post" action="bilan_absence.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form4">
        <fieldset style="width: 400px; margin: auto;">
            <legend class="legend_texte">&nbsp;Action&nbsp;</legend>
                <input type="hidden" name="classe" value="<?php echo $classe; ?>" />
                <input type="hidden" name="eleve" value="<?php echo $eleve; ?>" />
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="au" value="<?php echo $au; ?>" />
                <input type="submit" name="Submit32" value="Composer" />
        </fieldset>
     </form>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php if($type_impr == "bpc") { ?>
<? /* div de centrage du tableau pour ie5 */ ?>
<div style="text-align: center;">
   <form method="post" action="impression_absences.php?type_impr=<?php echo $type_impr; ?>" name="form5">
      <fieldset style="width: 450px; margin: auto;">
        <legend style="clear: both" class="legend_texte">&nbsp;Sélection&nbsp;</legend>
            <div class="titre_tableau_gestion">Bilan des absences pour les conseils de classe</div>
            <div class="norme_absence" style="text-align:left">du <input name="du" type="text" size="11" maxlength="11" value="<?php echo $du; ?>" /><a href="#calend" onClick="<?php  echo $cal_5->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a> au <input name="au" id="au" type="text" size="11" maxlength="11" value="<?php echo $au; ?>" onClick="getDate(au,'form5')" /><a href="#calend" onClick="<?php  echo $cal_6->get_strPopup('../../lib/calendrier/pop.calendrier.php', 350, 170); ?>"><img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><input type="submit" name="Submit2" value="&gt;&gt;" /></div>
      </fieldset>
   </form>

       <form method="post" action="bilan.php?type_impr=<?php echo $type_impr; ?>&amp;choix=<?php echo $choix; ?>" name="form6">
        <table style="width: 600px; margin: auto;" border="0" cellpadding="0" cellspacing="1">
          <tr class="fond_vert">
            <td class="norme_absence_blanc" style="width: 80px"><strong>&agrave; imprimer</strong></td>
            <td class="norme_absence_blanc"><strong>Classe</strong></td>
            <td class="norme_absence_blanc"><strong>Expéditeur</strong></td>
          </tr>
        <?php
           $ic = '1';
           $i = '0';
           $niveau = "";
           $niveau_v = "";
           $requete_1 ="SELECT id, classe, nom_complet FROM ".$prefix_base."classes ORDER BY nom_complet DESC";
           $execution_1 = mysql_query($requete_1) or die('Erreur SQL !'.$requete_1.'<br />'.mysql_error());
           while ( $data_1 = mysql_fetch_array($execution_1))
               {
           if ($niveau == "") { if (substr($data_1['nom_complet'],0,1) != substr($niveau_v,0,1)) { ?><tr bgcolor="#5E938C"><td colspan="3"><div class="norme_absence_blanc"><strong><?php echo substr($data_1['nom_complet'],0,-1); $ic='2'; ?></strong></div></td></tr><?php $niveau_v =$data_1['nom_complet'];} }
           if ($niveau != "") { if (substr($niveau,0,1) != substr($niveau_v,0,1)) { ?><tr bgcolor="#5E938C"><td colspan="3"><div class="norme_absence_blanc"><strong><?php echo $niveau; ?></strong></div></td></tr><?php $niveau_v =$data_1['nom_complet'];} }
              if ($ic==1)
                 {
                   $ic=2;
                   $couleur_cellule="td_tableau_absence_1";
                 } else {
                          $couleur_cellule="td_tableau_absence_2";
                          $ic=1;
                         }
                ?>
                  <tr class="<?php echo $couleur_cellule; ?>">
                    <td class="centre"><input type="checkbox" name="a_imprimer[<?php echo $i; ?>]" value="1"  <?php if(isset($a_imprimer[$i]) and $a_imprimer[$i] == "1" OR $cocher == '1') {?> checked<?php } ?> /><input type="hidden" name="id_classe[<?php echo $i; ?>]" value="<?php echo $data_1['id']; ?>" /></td>
                    <td class="norme_absence"><?php echo $data_1['nom_complet']; ?></td>
                    <td>
                        <select name="cpe[<?php echo $i; ?>]">
                        <?php if($i!=0) { ?><option value="idem">idem</option><?php } ?>
                        <?php
                             $req_cpe = mysql_query($sql_cpe) or die('Erreur SQL ! '.$sql_cpe.' '.mysql_error());

                              while($data_cpe = mysql_fetch_array($req_cpe))
                              {
                                echo "<option value=\"".$data_cpe['login']."\"";
                                if ($eleve == $data_cpe['login']) {echo " selected";}
                                echo " >".strtoupper($data_cpe['nom'])." ".ucfirst($data_cpe['prenom'])."</option>";
                              }
                        ?>
                        </select>
                    </td>
                  </tr>
         <?php $i = $i + 1; } ?>
           <tr>
            <td colspan="2" class="norme_absence"><a href="impression_absences.php?type_impr=<?php echo $type_impr; ?>&amp;du=<?php echo $du; ?>&amp;au=<?php echo $au; ?>&amp;cocher=<?php if($cocher==1) { ?>0<?php } else { ?>1<?php  } ?>"><?php if($cocher==1) { ?>déc<?php } else { ?>C<?php  } ?>ocher toutes les cellules</a></td>
            <td class="centre">
                <input type="hidden" name="du" value="<?php echo $du; ?>" />
                <input type="hidden" name="au" value="<?php echo $au; ?>" />
                <input type="submit" name="Submit33" value="Composer" />
            </td>
          </tr>
        </table>
    </form>
<? /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php } ?>

<?php
/*
 * $Id$
 *
 * Copyright 2001, 2010 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
?>
<div id="result">
   <div id="wrap">
    <h3><font class='red'>Sélection des données à traiter et de la période de traitement:</font></h3>
    <div id="select">
        <div id="periode">
            <form action="index.php?ctrl=select&choix=<?php echo $_SESSION['stats_choix']; ?>" method="post"
                  name="select_donnees" id="select_donnees">
                <fieldset><legend class="legend">Période de traitement</legend>
                    <?php
                    if ($periodes_calendrier) {
                        for($i=0;$i<count($periodes_calendrier);$i++) {
                            echo '<p class="selected">'.$periodes_calendrier[$i]['nom_calendrier'];?>
                    <input type="radio" onclick="javascript:deselect('month');deselect('du');deselect('au');this.form.submit()" name="id_calendrier"
                           id="id_calendrier<?php echo $i; ?>" value="<?php echo $periodes_calendrier[$i]['id_calendrier']?>"
                                   <?php
                                   if (isset($_SESSION['stats_periodes']['periode'])){
                                     if($_SESSION['stats_periodes']['periode']==$periodes_calendrier[$i]['id_calendrier'])  echo 'checked';} ?>>
                                   <?php }
                               echo'ou:</p>';
                           }?>
                    <p class="selected">Choisir un mois de traitement
                        <select  name="month" id="month" size="0"  onFocus="javascript:uncheck('id_calendrier','<?php echo count($periodes_calendrier);?>') "
                                 onChange="this.form.submit()" >
                                     <?php
                                     foreach($months as $key=>$value) {
                                         if(isset($_SESSION['stats_periodes']['month'])&& $_SESSION['stats_periodes']['month']==$key) {?>
                            <option selected value="<?php echo $key;?>" onClick="this.form.submit()"><?php echo $value;?></option>
                                    <?php }else { ?>
                            <option value="<?php echo $key;?>" onClick="this.form.submit()"><?php echo $value;?></option>
                                    <?php }
                            }?>
                        </select>ou :</p>
                    du&nbsp;<input name="du"  id="du" onfocus="javascript:this.select();" type="text" tabindex="4"
                                   value="<?php  echo $_SESSION['stats_periodes']['du']; ?>"
                                   size="10" maxlength="10" style="border: 1px solid #000000;" />
                    <a href="#calend" onClick="javascript:deselect('month');uncheck('id_calendrier','<?php echo count($periodes_calendrier);?>');
                       <?php echo $cal_1->get_strPopup('lib/calendrier/pop.calendrier.php', 350, 170); ?>">
                        <img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a>
		au&nbsp;<input name="au"  id="au" onfocus="javascript:this.select();" type="text" tabindex="5"
                                   value="<?php echo $_SESSION['stats_periodes']['au']; ?>"
                                   size="10" maxlength="10" style="border: 1px solid #000000;" />
                    <a href="#calend" onClick="javascript:deselect('month');uncheck('id_calendrier','<?php echo count($periodes_calendrier);?>');
                       <?php echo $cal_2->get_strPopup('lib/calendrier/pop.calendrier.php', 350, 170); ?>">
                        <img src="../../lib/calendrier/petit_calendrier.gif" border="0" alt="" /></a><br />
                </fieldset>
                <p></p>
                <div>
                    <fieldset class="individus"><legend class="individus">Sélection</legend>
                        <p class="selected">Données complètes :  </p>
                        <p class="selected">
                            &Eacute;tablissement <input type="checkbox" onclick="javascript:deselect('month');this.form.submit()" name="etab_all" id="etab_all0"
                                                        <?php if (isset($_SESSION['etab_all']))echo'checked'; ?>/>
                                                        <?php if ($_SESSION['type']=='Discip') {?>
                            &Eacute;lèves <input type="checkbox" onclick="javascript:deselect('month');this.form.submit()" name="eleve_all"
                                                     <?php if (isset($_SESSION['eleve_all']))echo'checked'; ?>/>
                            Personnels <input type="checkbox" onclick="javascript:deselect('month');this.form.submit()" name="pers_all"
                                                  <?php if (isset($_SESSION['pers_all']))echo'checked'; ?>/>
                                                  <?php }?>
                            <input type="hidden" name='posted' value='ok'/>
                        </p>
                        <p class="selected">et/ou :</p>
                        <div class="sous_menu">
                            <span><a href="index.php?choix=eleves" onclick="javascript:deselect('month');" >Recherche individuelle</a></span>|
                            <span><a href="index.php?choix=classe" onclick="javascript:deselect('month');deselect('choix');" >Recherche par classe</a></span>
                        </div>
                        <br />
                        <?php if ($_SESSION['stats_choix']=='eleves'||$_SESSION['stats_choix']=='personnels') {?>
                        <div id="recherche_indiv">
                            <input type="radio" name="choix" id="choix"
                                   value="eleves" onclick="javascript:deselect('month');this.form.submit()"
                                       <?php if ($_SESSION['stats_choix']=='eleves'||!isset($_SESSION['choix']))echo'checked'; ?> />&Eacute;lèves
                                       <?php if ($_SESSION['type']=='Discip') {?>
                            <input type="radio" name="choix" value="personnels"
                                   onclick="javascript:deselect('month');this.form.submit()"
                                           <?php if ($_SESSION['stats_choix']=='personnels')echo'checked'; ?> />Personnels
                                           <?php }?>
                            <br />
                            <br />
                            <label for="nom"></label>
                            <input type="text" name="nom" id="nom" value="" />
                            <span id="indicateur" style="display: none;"><img src="apps/img/loader.gif" alt="loader"/></span>
                            <div class="update" id="nom_update"></div>
                            <input type="hidden" name="nom_login" id="nom_login" value="" />
                            <br />
                            <br />
                            <br />
                        </div>
                            <?php }else {?>
                        <div id="recherche_classe">
                                <?php $max=count($classes); ?>
                            <a href="javascript:modif_case('classes',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/
                            <a href="javascript:modif_case('classes',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher'/></a>
                            <br />
                                <?php
                                $cpt=0;
                                foreach($classes as $classe) {
                                    echo "<input type='checkbox' name='classes[]' id='classes_$cpt' value='$classe->id' /><label for='classes_$cpt'>$classe->classe ($classe->nom_complet)</label><br />\n";
                                    $cpt++;
                                } ?>
                            <input type="submit" name="action" value="Ajouter" onclick="javascript:deselect('month');" class="submit"/>
                        </div>
                            <?php }?>
                    </fieldset>
                </div>
            </form>
        </div>
    </div>
    <div id="selected">
        <p>Ce module permet d'effectuer des statistiques/suivies sur les incidents déclarés dans l'établissement </p>
        <p>Pour cela vous devez au préalable :</p>
        <ul>
            <li>ajuster si nécessaire la période de traitement</li>
            <li>sélectionner les données à  traiter (données globales , selection par individu ou classe)</li>
        </ul>
        <div >
            <p class="selected_titre">Période de Traitement:</p>
            <ul class="selected_titre">
                <li id="selected"><?php echo'Du '.$_SESSION['stats_periodes']['du'].' au '.$_SESSION['stats_periodes']['au'];?></li>
            </ul>
            <?php if (isset($_SESSION['etab_all'])|| isset($_SESSION['eleve_all'] )|| isset($_SESSION['pers_all'])) {?>
            <table><tr class="fond"><td>
                        <p class="selected_titre">Données complètes :</p></td><td>
                        <ul class="selected_titre">
                                <?php if (isset($_SESSION['etab_all']))echo'<li id="selected"> &Eacute;tablissement </li>';
                                if (isset($_SESSION['eleve_all']))echo'<li id="selected"> &Eacute;lèves </li>';
                                if (isset($_SESSION['pers_all']))echo'<li id="selected">Personnels </li>';
                            }?>
                        </ul></td></tr></table>
            <?php if (isset($_SESSION['individus'])) { ?>
            <table><tr class="fond"><td>
                        <p class="selected_titre">Personnes : Tout effacer <a href="index.php?ctrl=select&del_type=all_data&del=individus">
                                <img src="apps/img/close12.png" alt="close"/></a></p></td>
                    <td>
                        <ul class="selected_titre">
                                <?php
                                if (isset($_SESSION['individus'])) {
                                    foreach($individus_identites as $key=>$value) { ?>
                            <li id="selected"><?php echo $value['nom'].' '.$value['prenom'];?>
                                <a href="index.php?ctrl=select&del_type=individus&del=<?php echo $value['login'];?>">
                                    <img src="apps/img/close12.png" alt="close"></a></li>
                                        <?php }
                                }?>
                        </ul>
                    </td></tr></table>
                <?php  }
            if ( isset($_SESSION['stats_classes_selected'])) { ?>
            <table><tr class="fond"><td>
                        <p class="selected_titre">Classes: Tout effacer <a href="index.php?ctrl=select&del_type=all_data&del=classes">
                                <img src="apps/img/close12.png" alt="close"/></a></p>
                    </td>
                    <td>
                        <ul class="selected_titre">
                                <?php
                                if (isset($_SESSION['stats_classes_selected'])) {
                                    foreach($noms_classes as $value) {?>
                            <li id="selected"><?php  echo $value[0]['classe'].'( '.$value[0]['nom_complet'].' )' ; ?>
                                <a href="index.php?ctrl=select&del_type=classes&del=<?php echo $value[0]['id'];?>">
                                    <img src="apps/img/close12.png" alt="close"></a></li>
                                        <?php }
                                } ?>
                        </ul>
                    </td></tr></table>
                <?php } ?>
        </div>
    </div>
  </div>
</div>

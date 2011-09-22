<?php
/*
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
// On empêche l'accès direct au fichier
if (basename($_SERVER["SCRIPT_NAME"])==basename(__File__)){
    die();
};

?>
<div id='result'>
    <h3><font class='red'>Organisation des natures d'incidents en catégories:</font></h3>
    <div id='wrap'>
    <div id='natures'>
        <form action='index.php?ctrl=categories&action=save' method='post' name='save' id='save'>
            <?php echo add_token_field();?>
            <fieldset>
                <legend class='legend'>Affectation : </legend>
                <h3>1. Sélectionner les incidents</h3>
                <select  name='natures_incidents[]' multiple size=20 >
                    <?php  foreach($liste_natures as $nature) {?>
                    <option class='option' VALUE="<?php echo htmlspecialchars($nature->nature,ENT_QUOTES);?>">
                    <?php if  (!$nature->nature) echo ' Pas de nature renseignée ('.$nature->categorie_sigle.')'; else echo $nature->nature.' ('.$nature->categorie_sigle.')';?></option>
                        <?php }?>
                </select>
                <br />
                <h3>2. Choisir une Categorie</h3>
                <select  name='categorie' >
                    <?php  foreach($liste_categories as $categorie) {?>
                    <option class='option' VALUE='<?php echo $categorie->id;  ?>'><?php echo $categorie->categorie;  ?></option>
                        <?php }?>
                    <option class='option' VALUE='default'>Enlever les catégories</option>
                </select><br />
                <h3>3. Mise à jour des données</h3>
                <Input type='submit' value='Mettre à jour' class='submit'>
            </fieldset>
        </form>
    </div>
    <div id='categories'>
        <fieldset>
            <legend class='legend'>Catégories des incidents</legend>
            <div id='Categories_incidents1' >
                <?php                
                $total=count($liste_categories);
                $fin=ceil($total/2);
                for ($i=0;$i<$fin;$i++) {?>
                <div id='group<?php echo $i+2;?>' class='bloc'>
                    <h3><a href="index.php?ctrl=categories&action=delete&categorie_id=<?php echo $liste_categories[$i]->id.add_token_in_url() ?>" class="supp" title="cliquez pour vider"><?php echo $liste_categories[$i]->categorie; ?></a></h3>
                        <?php
                        
                        foreach($liste_natures as $nature) { ?>
                    <ul class='selected_titre'>
                                <?php if ($nature->categorie==$liste_categories[$i]->categorie) { ?>
                        <li id='selected'><a href="index.php?ctrl=categories&action=delete&nature=<?php echo htmlspecialchars($nature->nature,ENT_QUOTES).add_token_in_url() ?>" class="supp" title="cliquez pour supprimer"><?php if  (!$nature->nature) echo ' Pas de nature renseignée'; else echo $nature->nature;?></a></li>
                                    <?php }?>
                    </ul>
                            <?php }?>
                </div>
                    <?php } ?>
            </div>
            <div id='Categories_incidents2'>
                <?php
                for ($i=$fin;$i<$total;$i++) {?>
                <div id='group<?php echo $i+2;?>' class='bloc'>
                    <h3><a href="index.php?ctrl=categories&action=delete&categorie_id=<?php echo $liste_categories[$i]->id.add_token_in_url() ; ?>" class="supp" title="cliquez pour vider"><?php echo $liste_categories[$i]->categorie;?></a></h3>
                        <?php
                        foreach($liste_natures as $nature) {?>
                    <ul class='selected_titre'>
                                <?php if ($nature->categorie==$liste_categories[$i]->categorie) { ?>
                        <li id='selected'><a href="index.php?ctrl=categories&action=delete&nature=<?php echo htmlspecialchars($nature->nature,ENT_QUOTES).add_token_in_url()  ?>" class="supp" title="cliquez pour supprimer"><?php if  (!$nature->nature) echo ' Pas de nature renseignée'; else echo $nature->nature;?></a></li>
                                    <?php }?>
                    </ul>
                            <?php }?>
                </div>
                    <?php } ?>
            </div>    
        </fieldset>
    </div>
    </div>
</div>
<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer, Didier Blanqui
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
<div id="result">
    <h3 class="red">Choix des filtres pour les données</h3>

    <div id="filtres">
        <form action="index.php?ctrl=bilans&action=filtrer&action_from=<?php echo $action_from;?>" method="post"
                  name="filtres" id="filtre_donnees">
            <div id="wrap">
            <div id="filtre_categories">
                <fieldset>
                    <legend>Choix des catégories</legend>
                    <div id="recherche_classe">
                            <?php $max=count($categories);?>
                            <a href="javascript:modif_case('categories',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/
                            <a href="javascript:modif_case('categories',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher'/></a>
                            <br />
                            <?php
                          $cpt=0;
                                foreach($categories as $categorie) { ?>
                            <input type="checkbox" name="categories[]" id="categories_<?php echo $cpt;?>" value="<?php echo $categorie->id ?>" <?php if ($filtres_categories){if( in_array($categorie->id, $filtres_categories)) echo "checked";} ?> />
                            <label for="categories_<?php echo $cpt;?>"><?php echo $categorie->categorie ?></label><br />
                               <?php
                               $cpt++;
                                }
                            $cpt++;?>                            
                        </div>
                </fieldset>
            </div>
            <div id="filtre_categories">
                <fieldset>
                    <legend>Choix des mesures prises</legend>
                    <div id="recherche_classe">
                            <?php $max=count($mesures);?>
                            <a href="javascript:modif_case('mesures',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/
                            <a href="javascript:modif_case('mesures',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher'/></a>
                            <br />
                                <?php
                                $cpt=0;
                                foreach($mesures as $mesure) { ?>
                                    <input type="checkbox" name="mesures[]" id="mesures_<?php echo $cpt;?>" value="<?php echo $mesure->id ?>" <?php if ($filtres_mesures){if( in_array($mesure->id, $filtres_mesures)) echo "checked";} ?>/>
                                    <label for="mesures_<?php echo $cpt;?>"><?php echo $mesure->mesure ?></label><br />
                                <?php $cpt++;
                                } ?>
                        </div>
                </fieldset>
            </div>
            <div id="filtre_categories">
                <fieldset>
                    <legend>Choix des sanctions</legend>
                    <div id="recherche_classe">
                            <?php
                               //$max=count($sanctions)+3;
                               $max=count($sanctions);
                            ?>
                            <a href="javascript:modif_case('sanctions',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/
                            <a href="javascript:modif_case('sanctions',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher'/></a>
                            <br />
                                <?php //$cpt=0;?>
                               <!--input type='checkbox' name='sanctions[]' id='sanctions_<?php echo $cpt;?>' value='travail' <?php if ($filtres_sanctions){if( in_array('travail', $filtres_sanctions)) echo "checked";} ?>/><label for='sanction_<?php echo $cpt;?>'>Travail</label><br />
	                        <?php //$cpt++;?>
	                       <input type='checkbox' name='sanctions[]' id='sanctions_<?php echo $cpt;?>' value='retenue' <?php if ($filtres_sanctions){if( in_array('retenue', $filtres_sanctions)) echo "checked";} ?>/><label for='sanction_<?php echo $cpt;?>'>Retenue</label><br />
	                      <?php //$cpt++;?>
	                      <input type='checkbox' name='sanctions[]' id='sanctions_<?php echo $cpt;?>' value='exclusion' <?php if ($filtres_sanctions){if( in_array('exclusion', $filtres_sanctions)) echo "checked";} ?>/><label for='sanction_<?php echo $cpt;?>'>Exclusion</label><br /-->
	                      <?php  $cpt++;
                                foreach($sanctions as $sanction) { ?>
                                    <input type="checkbox" name="sanctions[]" id="sanctions_<?php echo $cpt;?>" value="<?php echo $sanction->nature ?>" <?php if ($filtres_sanctions){if( in_array($sanction->nature, $filtres_sanctions)) echo "checked";} ?>/>
                                    <label for="sanctions_<?php echo $cpt;?>"><?php echo $sanction->nature ?></label><br />
                                <?php $cpt++; } ?>
                        </div>

                </fieldset>
            </div>
                <div id="filtre_categories">
                <fieldset>
                    <legend>Rôle dans l'incident</legend>
                    <div id="recherche_classe">
                            <?php $max=count($roles);?>
                            <a href="javascript:modif_case('roles',true,<?php echo $max; ?>)"><img src='../../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a>/
                            <a href="javascript:modif_case('roles',false,<?php echo $max; ?>)"><img src='../../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher'/></a>
                            <br />
                                <?php
                                $cpt=0;
                                foreach($roles as $role) { ?>
                                    <input type="checkbox" name="roles[]" id="roles_<?php echo $cpt;?>" value="<?php echo $role->qualite ?>"  <?php if ($filtres_roles){if( in_array($role->qualite, $filtres_roles)) echo "checked";} ?>/>
                                    <label for="roles_<?php echo $cpt;?>"><?php echo $role->qualite ?>  </label><br />
                                <?php $cpt++;
                                } ?>
                        </div>
                </fieldset>
            </div>
            </div>
              <input type="submit" name="action" value="Filtrer" class="submit2" />
        </form>
    </div>
</div>

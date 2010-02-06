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
    <div id="wrap" >
    <h3><font class="red">Bilans des incidents pour la période du: <?php echo $_SESSION['stats_periodes']['du'];?> au <?php echo $_SESSION['stats_periodes']['au'];?> </font> </h3>
    <div class="bilans">
        <table class="boireaus"><tr><td  class="nouveau">Choisir le mode de représentation</td>
                <td><a href="index.php?ctrl=bilans&action=affiche_details"><img src="apps/img/simple.png" alt="simple" title="simplifié"/></a>&nbsp;
                    <a href="index.php?ctrl=bilans&action=affiche_details&value=ok"><img src="apps/img/details.png" title="détaillé" alt="détaillé"/></a>&nbsp;</td></tr>
            <tr><td class="nouveau"> Choisir les filtres </td><td><a href="index.php?ctrl=bilans&action=choix_filtres"<img src="apps/img/filtres.png" alt="filtres" title="filtrer" /></a></td></tr></table>
    </div>
 <div class="bilans">
        <table class="boireaus"><tr><td  class="nouveau">Mode de représentation actif</td>
                <td colspan="2"><?php if($mode_detaille){?>Détaillé<?php }else {?>Simplifié <?php }?> </td></tr>
            <tr><td rowspan="4" class="nouveau"><p>Filtres actifs :<br /> Cliquer sur les items activés pour les supprimer de la sélection.</p></td><td><?php if($filtres_categories) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=categories" class="supp_filtre" title="Cliquez pour vider" >Catégories</a><?php } else echo'Catégories'; ?></td>
                <td><?php if($filtres_categories) {
                     foreach($libelles_categories as $categorie){ ?>
                    <a href="index.php?ctrl=bilans&action=maj_filtre&type=categories&choix=<?php echo $categorie?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $categorie,' - '; ?></a>
             <?php    }
                }else {echo'Aucun';} ?></td></tr>
            <tr><td><?php if($filtres_mesures) { ?><a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures" class="supp_filtre" title="Cliquez pour vider" >Mesures prises</a><?php } else echo 'Mesures prises';?></td>
                <td>
            <?php if($filtres_mesures) {
                foreach($libelles_mesures as $mesure){?>
                    <a href="index.php?ctrl=bilans&action=maj_filtre&type=mesures&choix=<?php echo $mesure?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $mesure,' - '?></a>
             <?php    }
                }else {echo'Aucun';} ?></td></tr>
            <tr><td><?php if($filtres_sanctions) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions" class="supp_filtre" title="Cliquez pour vider" >Sanctions</a><?php } else echo 'Sanctions';?></td>
                <td>
            <?php if($filtres_sanctions) {
                foreach($filtres_sanctions as $sanction){ ?>
                    <a href="index.php?ctrl=bilans&action=maj_filtre&type=sanctions&choix=<?php echo $sanction?>" class="supp_filtre" title="Cliquez pour supprimer"><?php echo $sanction,' - '?></a>
             <?php    }
                }else {echo'Aucun';} ?></td></tr>
            <tr><td><?php if($filtres_roles) {?><a href="index.php?ctrl=bilans&action=maj_filtre&type=roles" class="supp_filtre" title="Cliquez pour vider" >Rôles</a><?php } else echo 'Rôles';?></td><td><?php if($filtres_roles) {
                foreach($filtres_roles as $role){?>
                    <a href="index.php?ctrl=bilans&action=maj_filtre&type=roles&choix=<?php echo $role;?>" class="supp_filtre" title="Cliquez pour supprimer"><?php if($role=="") echo "Aucun rôle affecté - "; else echo $role,' - ';?></a>
             <?php    }
                }else {echo'Aucun';} ?></td></tr>
        </table>
     </div>
    </div>

    <div id="tableaux">
        <div id="vertical_container" >
  <?php if (isset($incidents)) {
        foreach ($incidents as $titre=>$incidents_titre) {?>
        <h1 class="accordion_toggle"><?php echo $titre ?></h1>
        <div class="accordion_content">
   <?php
           if (isset($incidents_titre['error'])) {?>
    <table class="boireaus">
        <tr ><td class="nouveau"><font class='titre'>Bilan des incidents concernant :</font><?php echo $titre ?></td></tr>
        <tr><td class='nouveau'>Pas d'incidents avec les critères sélectionnés...</td></tr>
    </table><br /><br />
                <?php echo'</div>';}else { ?>

    <table class="boireaus">
        <tr ><td rowspan="3"  colspan="5" class='nouveau'><p><font class='titre'>Bilan des incidents concernant : </font><?php echo $titre;?></p><?php if($filtres_categories||$filtres_mesures||$filtres_roles||$filtres_sanctions){ ?><p>avec les filtres selectionnés</p><?php }?></td><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Nombres d'incidents sur la période:</font> <?php echo $totaux[$titre]['incidents']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% sur la période/Etab: </font> <?php echo round((100*($totaux[$titre]['incidents']/$nombre_total_incidents)),2);?></td><?php } ?></tr>
        <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Nombre total de mesures prises pour ces incidents :</font> <?php echo $totaux[$titre]['mesures']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% sur la période/Etab: </font> <?php echo round((100*($totaux[$titre]['mesures']/$nombre_total_mesures)),2);?></td><?php } ?></tr>
        <tr><td  <?php if ($titre=='L\'Etablissement' ) {?> colspan="2" <?php }?> class='nouveau'><font class='titre'>Nombre total de sanctions prises pour ces incidents:</font> <?php echo $totaux[$titre]['sanctions']; ?></td><?php if ($titre!=='L\'Etablissement' ) {?> <td  class='nouveau' > <font class='titre'>% sur la période/Etab: </font> <?php echo round((100*($totaux[$titre]['sanctions']/$nombre_total_sanctions)),2);?></td><?php } ?></tr>
                    <?php if($mode_detaille) { ?>
        <tr><th><a href='index.php?ctrl=bilans&action=tri&choix=date'><font class='titre'>Date</font></a></th><th><a href='index.php?ctrl=bilans&action=tri&choix=declarant'><font class='titre'>Déclarant</font></a></th><th><a href='index.php?ctrl=bilans&action=tri&choix=heure'><font class='titre'>Heure</font></a></th><th><a href='index.php?ctrl=bilans&action=tri&choix=nature'><font class='titre'>Nature</font></a></th>
            <th><a href='index.php?ctrl=bilans&action=tri&choix=categorie'><font class='titre'>Catégorie</font></a></th><th width="35%"><font class='titre'>Description</font></th><th width="35%"><font class='titre'>Protagonistes</font></th></tr>
                        <?php $alt_b=1;
                        foreach($incidents_titre as  $incident) {
                            $alt_b=$alt_b*(-1);?>
        <tr class='lig<?php echo $alt_b;?>'><td><?php echo $incident->date; ?></td><td><?php echo $incident->declarant; ?></td><td><?php echo $incident->heure; ?></td>
            <td><?php echo $incident->nature; ?></td><td><?php if(!is_null($incident->id_categorie))echo $incident->sigle_categorie;else echo'-'; ?></td><td><?php echo $incident->description; ?></td>
            <td><?php if(!isset($protagonistes[$incident->id_incident]))echo'<h3 class="red">Aucun protagoniste défini pour cet incident</h3>';
                          else  foreach($protagonistes[$incident->id_incident] as $protagoniste) {
                                        echo $protagoniste->prenom.' '.$protagoniste->nom.' ( ';
                                        echo $protagoniste->statut.' ';
                                        if($protagoniste->classe) echo $protagoniste->classe .' ) :'; else echo ' ) :' ;
                                        if($protagoniste->qualite=="") echo'<font class="red">Aucun rôle affecté.</font><br />';
                                        else echo $protagoniste->qualite.'<br />';
                                     }
                                     ?></td></tr>
                            <?php }

                    }?>
    </table>
    <br /><br />
        </div>
                <?php }
        }
    }else echo 'Sélectionnez en premier lieu des données à traiter'; ?>
      
        </div>
    </div>
</div>
<script type="text/javascript" >

	//
	// You can hide the accordions on page load like this, it maintains accessibility
	//
	// Special thanks go out to Will Shaver @ http://primedigit.com/
	//
	var verticalAccordions = $$('.accordion_toggle');
	verticalAccordions.each(function(accordion) {
		$(accordion.next(0)).setStyle({
		  height: '0px'
		});
	});


</script>


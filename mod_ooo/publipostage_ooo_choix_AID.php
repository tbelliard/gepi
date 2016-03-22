<?php
/*
 *
 * Copyright 2015-2016 Régis Bouguin
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
<p style="margin: .5em;">Ou</p>
<form method='post' enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>' id='formAID'>
	<fieldset class='fieldset_opacite50' >
		<p>
			Pour quel AID souhaitez-vous imprimer le document
			<strong><?php echo $tab_file[$num_fich]; ?></strong>&nbsp;?
			<a href="javascript:cocher_decocher('id_AID_', true)">Cocher</a>
			/
			<a href="javascript:cocher_decocher('id_AID_', false)">décocher</a>
			tous les AID
			<input type='hidden' name='num_fich' value='<?php echo $num_fich; ?>' />
		</p>
		<div  class="tableau_03">
			<div class="colonne03">
			
<?php
$cpt = 1;

$nombreligne = $res->num_rows;
$nbcol=3;
$nb_par_colonne=ceil($nombreligne/$nbcol);

while ($obj = $res->fetch_object()) {
	/*
	$sqlAID = "SELECT a.nom , c.nom_complet AS nom_famille FROM `aid` as a , `aid_config` as c "
	   . "WHERE a.id LIKE '".$obj->id_aid."' "
	   . "AND a.indice_aid = c.	indice_aid ";
	//echo $sqlAID ."<br />";
	$resAID = mysqli_query($mysqli, $sqlAID);
	 * 
	 */
	$resAID = get_AID ($obj->id_aid);
	
	$AIDactif = $resAID->fetch_object();
?>
				<p>
					<input type='checkbox' name='id_AID[]' id='id_AID_<?php echo $cpt; ?>' 
						   value='<?php echo $obj->id_aid; ?>'
						   onchange="checkbox_change('id_AID_<?php echo $cpt; ?>')"
						   />
					<label for='id_AID_<?php echo $cpt; ?>'>
						<span id='texte_id_AID_<?php echo $cpt; ?>'>
							<?php echo $AIDactif->nom; ?> <em>(<?php echo $AIDactif->nom_famille; ?>)</em>
						</span>
					</label>
				</p>
<?php
	if (!($cpt % $nb_par_colonne))  {
?>
		</div>
		<div class="colonne03">
<?php
	}	
	$cpt++;
}
?>
			</div>
		</div>
		
		
		<p>
			<input type='radio' name='mode_pub' id='mode_pub5' value='' 
				   checked='checked' onchange="change_style_radio();" />
			<label for='mode_pub5' id='texte_mode_pub5' style='font-weight:bold;'>
				Générer un seul fichier même si vous sélectionnez plusieurs AID
			</label>
			<br />ou<br />
			<input type='radio' name='mode_pub' id='mode_pub6' value='un_fichier_par_selection' 
				   onchange="change_style_radio();" />
			<label for='mode_pub6' id='texte_mode_pub6'>Générer un fichier par AID sélectionnée.</label>
			<br />
			<span style='margin-left:2em;'>
				<input type='checkbox' name='zipper' id='zipper3' value='y' 
					   onchange="checkbox_change(this.id); check_choix_zip('2');" />
				<label for='zipper3' id='texte_zipper3'>
					Dans ce deuxième cas, zipper l'ensemble de ces fichiers en une seule archive ZIP.
				</label>
			</span>
			<br />
		</p>
		
		<p class='center'>
			<input type='submit' value='Envoyer' id='bouton_submit3' />
			<input type='button' value='Envoyer' id='bouton_submit_js3' 
				   onclick="valider_publipostage('formAID', 'id_AID_')" style='display:none;' />
		</p>
	
	</fieldset>
</form>

<?php
if($nombreligne+1 > $cpt_js) {
	$cpt_js=$nombreligne+1;
}

?>


<script type='text/javascript'>
if(document.getElementById('bouton_submit3')) {
	document.getElementById('bouton_submit3').style.display='none';
}
if(document.getElementById('bouton_submit_js3')) {
	document.getElementById('bouton_submit_js3').style.display='';
}
</script>
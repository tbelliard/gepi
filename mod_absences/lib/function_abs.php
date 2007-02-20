<?php

// gestion des fonctions sur les absences, dispences, retard, infirmerie

// fonction qui permet de vérifier si la variable ne contient que des caractère
function verif_texte($texte_ver) {
	if(!ereg("^[a-zA-Z_]+$",$texte_ver)){ $texte_ver = FALSE; } else { $texte_ver = $texte_ver; }
	return $texte_ver;
 }

// fonction qui permet de vérifier si la variable ne contient que des chiffres
function verif_num($texte_ver) {
	if(!ereg("^[0-9]+$",$texte_ver)){ $texte_ver = FALSE; } else { $texte_ver = $texte_ver; }
	return $texte_ver;
 }

// fonction permettant de supprimer un ou plusieurs id dans une table donnée
// à partir d'un tableau qui contiendrais les ids
// $tableau_des_ids: tableau avec les numéro id
// $prefix_base: préfix de la base s'il y en a
// $table: nom de la table choisie
// $selection: avoir un variable sélection
function supprime_id($tableau_des_ids, $prefix_base, $table, $selection)
 {
	$id_init = '0';
	while(!empty($tableau_des_ids[$id_init]))
	 {
		// on attribue les variables
		   $id_selectionne = $tableau_des_ids[$id_init];
		   if(isset($selection[$id_init]) and $selection[$id_init]!='') { $cocher = 'oui'; } else { $cocher = 'non'; }

		// si les variables sont correct et non vide on continue
		if(verif_texte($table) and verif_num($id_selectionne) and $id_selectionne != '' and $table != '' and $cocher === 'oui')
		{
	           $requete = "DELETE FROM ".$prefix_base.$table." WHERE id_absence_eleve ='".$id_selectionne."'";
	           mysql_query($requete) or die('Erreur SQL !'.$requete.'<br />'.mysql_error());
                } 
	 $id_init = $id_init + 1;
	 }
 }

// fonction gérant l'insertion d'une absences ou plusieurs absence
// par rapport à un tableau d'information qui contient les informations ci-dessous
// du, au, de, a, motif, justification, justification plus d'info
function ajout_abs($tableau_des_donnees)
 {
	$id_init = '0';
	while(!empty($tableau_des_donnees[$id_init]['id']))
	 {
		
	 $id_init = $id_init + 1;
	 }
 }
?>

<?php
	// ========================================
	// Astuce http://www.ehow.com/how_2000413_convert-em-px-sizes.html
	// pour calculer le rapport em/px et corriger le positionnement des infobulles (taille fixée en 'em')
	echo "<div id='EmSize' style='visibility:hidden; position:absolute; left:1em; top:1em;'></div>

	<script type='text/javascript'>
		var ele=document.getElementById('EmSize');
		var em2px=ele.offsetLeft
		//alert('1em == '+em2px+'px');
	</script>\n";
	// ========================================

	//echo "<div id='temoin_messagerie_non_vide' style='display:none; position:fixed; right:1em; top:300px;width:16px;height:16px;'><a href='$gepiPath/mod_alerte/form_message.php?mode=afficher_messages_non_lus' target='_blank'><img src='$gepiPath/images/icons/new_mail.gif' width='16' height='16' title='Vous avez un ou des messages non lus' /></a></div>";
	//echo "<div id='temoin_messagerie_non_vide' style='position:fixed; right:1em; top:300px;'></div>\n";

	// On a stocké les DIV dans un tableau et on parcourt le tableau PHP en fin de page pour afficher les infobulles en dehors du coeur de la page.
	// Les infobulles apparaissent ainsi un peu comme des notes de bas de page.
	// On pourrait cependant insérer l'infobulle au milieu du texte avec:
	//       echo creer_div("div1","1er DIV","Test de petit texte",12,"y","y","n");
	// au risque de perturber l'affichage de la page si Javascript est désactivé.

	if(isset($tabdiv_infobulle)){
		// Pour éviter des cas de doublons...
		$temoin_infobulle=array();

		if(count($tabdiv_infobulle)>0){
			for($i=0;$i<count($tabdiv_infobulle);$i++){
				if((isset($tabid_infobulle[$i]))&&(!in_array($tabid_infobulle[$i],$temoin_infobulle))) {
					echo $tabdiv_infobulle[$i]."\n";
					$temoin_infobulle[]=$tabid_infobulle[$i];
				}
			}
		}
	}

	// Témoin destiné à tester la fin de chargement de la page pour éviter des erreurs JavaScript avant la fin de chargement de tous les élèments.
	// Par exemple: la fonction cacher_div() utilisée plus bas teste cette variable pour ne tenter les opérations que si la variable est à 'ok'
	echo "<script type='text/javascript'>
	temporisation_chargement='ok';
	//desactivation_infobulle='n';
</script>\n";

	if(isset($tabid_infobulle)){
		if(count($tabid_infobulle)>0){
			// On cache les DIV en fin de chargement de la page (il faut qu'ils existent pour qu'il soit possible de les cacher).
			// Il me semble qu'il n'est pas possible d'initialiser le 'display' à 'none' et de modifier ce display ensuite via JavaScript.
			echo "<script type='text/javascript'>\n";
			for($i=0;$i<count($tabid_infobulle);$i++){
				echo "cacher_div('".$tabid_infobulle[$i]."');\n";
			}
			echo "</script>\n";
		}
	}

	if(isset($tabid_infobulle_complement)){
		if(count($tabid_infobulle_complement)>0){
			echo "<script type='text/javascript'>\n";
			for($i=0;$i<count($tabid_infobulle_complement);$i++){
				echo "cacher_div('".$tabid_infobulle_complement[$i]."');\n";
			}
			echo "</script>\n";
		}
	}
?>

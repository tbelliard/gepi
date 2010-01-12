<?php

// Renvoie la première partie de l'année au format complet (2009/2010)
function apb_annee($_annee) {
  $expl = preg_split("/[^0-9]/", $_annee);
	return $expl[0];
}

?>

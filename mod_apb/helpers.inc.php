<?php

// Renvoie la première partie de l'année au format complet (2009/2010)
function apb_annee($_annee) {
	return explode('/', $_annee, 1);
}

?>

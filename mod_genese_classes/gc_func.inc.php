<?php

	function get_classe_fut() {
		global $projet;

		$classe_fut=array();
		$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				$classe_fut[]=$lig->classe;
			}
			$classe_fut[]="Red";
			$classe_fut[]="Dep";
			$classe_fut[]=""; // Vide pour les Non Affectés
		}

		return $classe_fut;
	}

	function get_tab_opt_exclue() {
		global $projet, $classe_fut;

		$tab_opt_exclue=array();
		for($loop=0;$loop<count($classe_fut);$loop++) {
			if(!in_array($classe_fut[$loop], array('Dep', 'Red', ''))) {
				$tab_opt_exclue[$classe_fut[$loop]]=array();
				//=========================
				// Options exlues pour la classe
				$sql="SELECT opt_exclue FROM gc_options_classes WHERE projet='$projet' AND classe_future='".$classe_fut[$loop]."';";
				$res_opt_exclues=mysqli_query($GLOBALS["mysqli"], $sql);
				while($lig_opt_exclue=mysqli_fetch_object($res_opt_exclues)) {
					$tab_opt_exclue[$classe_fut[$loop]][]=mb_strtoupper($lig_opt_exclue->opt_exclue);
				}
				//=========================
			}
		}

		return $tab_opt_exclue;
	}

?>

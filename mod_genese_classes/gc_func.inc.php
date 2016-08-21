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

	function verif_proportion_garcons_filles() {
		global $projet;

		$retour="";

		$sql="SELECT DISTINCT classe FROM gc_divisions WHERE projet='$projet' AND statut='future' ORDER BY classe;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {

				$sql="SELECT 1=1 FROM gc_eleves_options geo, eleves e WHERE projet='".$projet."' AND classe_future='".$lig->classe."' AND e.sexe='F' AND geo.login=e.login;";
				$res_f=mysqli_query($GLOBALS["mysqli"], $sql);
				$eff_f=mysqli_num_rows($res_f);

				$sql="SELECT 1=1 FROM gc_eleves_options geo, eleves e WHERE projet='".$projet."' AND classe_future='".$lig->classe."' AND e.sexe='M' AND geo.login=e.login;";
				$res_m=mysqli_query($GLOBALS["mysqli"], $sql);
				$eff_m=mysqli_num_rows($res_m);

				$eff_total=$eff_m+$eff_f;
				if($eff_total>=20) {
					if($eff_f/$eff_total>=2/3) {
						$retour.="<br /><strong>ATTENTION&nbsp;:</strong> La sélection courante de $lig->classe présente plus de 2/3 de filles.";
					}
					elseif($eff_m/$eff_total>=2/3) {
						$retour.="<br /><strong>ATTENTION&nbsp;:</strong> La sélection courante de $lig->classe présente plus de 2/3 de garçons.";
					}
				}

			}

		}

		return $retour;
	}
?>

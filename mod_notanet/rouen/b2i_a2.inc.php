<?php
	/* $Id$ */

	//=====================
	// SOCLES B2I ET A2
	$note_b2i="";
	$note_a2="";
	$lv_a2="";

	$sql="SELECT * FROM notanet_socles WHERE login='".$lig1->login."';";
	$res_soc=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	if(mysqli_num_rows($res_soc)>0) {
		$lig_soc=mysqli_fetch_object($res_soc);
		$note_b2i=$lig_soc->b2i;
		$note_a2=$lig_soc->a2;

		$sql="SELECT nom_complet FROM matieres WHERE matiere='".$lig_soc->lv."';";
		$res_nom_mat_a2=mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		if(mysqli_num_rows($res_nom_mat_a2)>0) {
			$lig_lv_a2=mysqli_fetch_object($res_nom_mat_a2);
			$lv_a2=$lig_lv_a2->nom_complet;
		}
		else {
			$lv_a2=$lig_soc->lv;
		}
	}

	echo "<tr>\n";

	echo "<td style='border: 1px solid black; text-align:left; font-weight:bold;'>\n";
	echo "<p class='discipline fb'>";
	echo "Socle B2i";
	echo "</p>";
	echo "</td>\n";

	echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td ";
	echo " class='fb' ";
	echo "style='border: 1px solid black; text-align:center;'>\n";
	if($num_fb_col==1){
		echo $note_b2i;
	}
	else {
		echo "&nbsp;";
	}
	echo "</td>\n";

	echo "<td ";
	echo " class='fb' ";
	echo "style='border: 1px solid black; text-align:center;'>\n";
	if($num_fb_col==2){
		echo $note_b2i;
	}
	else {
		echo "&nbsp;";
	}
	echo "</td>\n";

	echo "</tr>\n";
	//=====================
	echo "<tr>\n";

	echo "<td style='border: 1px solid black; text-align:left; font-weight:bold;'>\n";
	echo "<p class='discipline fb'>";
	echo "Socle Niveau A2 de langue";
	echo "</p>";
	echo "</td>\n";

	echo "<td colspan='3' style='border: 1px solid black; text-align:center; font-weight:bold;'>\n";
	echo "&nbsp;";
	echo "</td>\n";

	echo "<td ";
	echo " class='fb' ";
	echo "style='border: 1px solid black; text-align:center;'>\n";
	if($num_fb_col==1){
		echo $note_a2;
	}
	else {
		echo "&nbsp;";
	}
	echo "</td>\n";

	echo "<td ";
	echo " class='fb' ";
	echo "style='border: 1px solid black; text-align:center;'>\n";
	if($num_fb_col==2){
		echo $note_a2;
	}
	else {
		echo "&nbsp;";
	}
	echo "</td>\n";

	echo "</tr>\n";
	//=====================
?>
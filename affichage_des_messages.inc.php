<?php
//debug_var();
// suppression d'un message du panneau d'affichage
if (isset($_POST['supprimer_message'])) {
	$r_sql="DELETE FROM `messages` WHERE `id`='".$_POST['supprimer_message']."'";
           
        $resultat = mysqli_query($mysqli, $r_sql);
}


// ----- Affichage des messages -----
$today=mktime(0,0,0,date("m"),date("d"),date("Y"));
$now=time();

// on supprime les messages obsolètes
$sql="DELETE FROM `messages` WHERE ((`date_fin`+86400 <= ".$today.") && (`statuts_destinataires`='_') && (`login_destinataire`='".$_SESSION['login']."'));";
      
    $resultat = mysqli_query($mysqli, $sql);


$sql="SELECT id, texte, date_debut, date_fin, date_decompte, auteur, statuts_destinataires, login_destinataire FROM messages
	WHERE (
	texte != '' and
	date_debut <= '".$now."' and
	date_fin >= '".$now."'
	)
    order by date_debut DESC, id DESC;";

    $appel_messages = mysqli_query($mysqli, $sql);
    $nb_messages = $appel_messages->num_rows;
    $ind = 0;
    $texte_messages = '';
    $texte_messages_simpl_prof = ''; // variable uniquement utilisée dans accueil_simpl_prof.php
    $affiche_messages = 'no';    
    while ($obj = $appel_messages->fetch_object()) {
        $statuts_destinataires1 = $obj->statuts_destinataires;
        $login_destinataire1 = $obj->login_destinataire;
        $id_message1 = $obj->id;
        $autre_message = "";
        if ((strpos($statuts_destinataires1, mb_substr($_SESSION['statut'], 0, 1))) || ($_SESSION['login']==$login_destinataire1)) {
            if ($affiche_messages == 'yes') {
                $autre_message = "hr";
                //$texte_messages_simpl_prof .= "<hr />";
            }
            $affiche_messages = 'yes';
            $content = $obj->texte;
            // _DECOMPTE_
            if(strstr($content, '_DECOMPTE_')) {
                //$nb_sec=old_mysql_result($appel_messages, $ind, 'date_decompte')-time();
                $nb_sec=$obj->date_decompte-time();
                if($nb_sec>0) {
                    $decompte_remplace="";
                } elseif($nb_sec==0) {
                    $decompte_remplace=" <span style='color:red'>Vous êtes à l'instant T</span> ";
                } else {
                    $nb_sec=$nb_sec*(-1);
                    $decompte_remplace=" <span style='color:red'>date dépassée de</span> ";
                }

                $decompte_j=floor($nb_sec/(24*3600));
                $decompte_h=floor(($nb_sec-$decompte_j*24*3600)/3600);
                $decompte_m=floor(($nb_sec-$decompte_j*24*3600-$decompte_h*3600)/60);

                if($decompte_j==1) {$decompte_remplace.=$decompte_j." jour ";}
                elseif($decompte_j>1) {$decompte_remplace.=$decompte_j." jours ";}

                if($decompte_h==1) {$decompte_remplace.=$decompte_h." heure ";}
                elseif($decompte_h>1) {$decompte_remplace.=$decompte_h." heures ";}

                if($decompte_m==1) {$decompte_remplace.=$decompte_m." minute";}
                elseif($decompte_m>1) {$decompte_remplace.=$decompte_m." minutes";}

                $content=preg_replace("/_DECOMPTE_/",$decompte_remplace,$content);
            }
            // fin _DECOMPTE_
            // fin _DECOMPTE_

            // gestion du token (csrf_alea)
            // si elle est présente la variable _CRSF_ALEA_ est remplacée lors de l'affichage du message
            // par la valeur du token de l'utilisateur, par exemple on peut ainsi inclure dans un message
            // un lien appelant un script : <a href="module/script.php?id=33&csrf_alea=_CRSF_ALEA_">Vers le script</a>
            $pos_crsf_alea=strpos($content,"_CRSF_ALEA_");
            if($pos_crsf_alea!==false)
                $content=preg_replace("/_CRSF_ALEA_/",$_SESSION['gepi_alea'],$content);

            //$tbs_message[]=array("suite"=>$autre_message,"message"=>$content);

            // dans accueil.php
            if (isset($afficheAccueil) && is_object($afficheAccueil)) $afficheAccueil->message[]=array("id"=>$id_message1, "suite"=>$autre_message,"message"=>$content, "statuts_destinataires"=>$statuts_destinataires1);
            // dans accueil_simpl_prof.php
            $texte_messages_simpl_prof .= "<div class='postit'>".$content."</div>";
        }
        $ind++;
    }
    $appel_messages->close();

// pour accueil_simpl_prof.php
if (basename($_SERVER['SCRIPT_NAME'])=="accueil_simpl_prof.php") {
	if($affiche_messages == 'yes') {
		echo "<table id='messagerie' summary=\"Ce tableau contient les informations sur lesquelles on souhaite attirer l'attention\">\n";
		if((isset($liste_evenements))&&($liste_evenements!="")) {
			echo "<tr><td>".$liste_evenements."</td></tr>";
		}
		echo "<tr><td>".$texte_messages_simpl_prof;
		echo "</td></tr></table>\n";
	}
	elseif((isset($liste_evenements))&&($liste_evenements!="")) {
		echo "<table id='messagerie' summary=\"Ce tableau contient les informations sur lesquelles on souhaite attirer l'attention\">
	<tr><td>".$liste_evenements."</td></tr>
</table>\n";
	}

	if(($_SESSION['statut']=='professeur')&&(getSettingAOui('active_mod_abs_prof'))) {
		echo affiche_remplacements_confirmes($_SESSION['login']);
		echo affiche_remplacements_en_attente_de_reponse($_SESSION['login']);
	}
}
?>

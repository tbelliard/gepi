<?php

    // Faut-il prévoir de la sécu ici?
    // Test sur $_SERVER['SCRIPT_NAME'] pour voir dans quoi c'est inclus?

    // Essais de saisie:
    $mode_saisie_abs=isset($_POST['mode_saisie_abs']) ? $_POST['mode_saisie_abs'] : 2;

    // A MODIFIER:
    // Période courante... à choisir par la suite d'après les dates de périodes paramétrées?
    $num_periode=1;

    // A MODIFIER:
    // Récupérer le jour sélectionné...
    //$jour=0;
    $jour=isset($_POST['jour']) ? $_POST['jour'] : (isset($_GET['jour']) ? $_GET['jour'] : date('N')-1);
    // A FAIRE: pouvoir interdire de faire des saisies pour un autre jour que le jour courant... euh... ça me parait indispensable: a_saisies ne permet pas de savoir à quel jour correspond la saisie si la saisie n'a pas lieu le jour même
    //          mais alors... en cas de panne informatique une journée, comment rétablir le tir le lendemain?

    if(getSettingValue('abs2_saisie_jour_courant_seulement')=='y') {$jour=date('N')-1;}
    // Ou faut-il un paramètre différent selon le statut?
        
/*

mysql> show fields from a_saisies;
+--------------------------+--------------+------+-----+---------+----------------+
| Field                    | Type         | Null | Key | Default | Extra          |
+--------------------------+--------------+------+-----+---------+----------------+
| id                       | int(11)      | NO   | PRI | NULL    | auto_increment |
| utilisateur_id           | varchar(100) | YES  | MUL | NULL    |                |
| eleve_id                 | int(11)      | YES  | MUL | -1      |                |
| commentaire              | text         | YES  |     | NULL    |                |
| debut_abs                | time         | YES  |     | NULL    |                |
| fin_abs                  | time         | YES  |     | NULL    |                |
| id_edt_creneau           | int(12)      | YES  | MUL | -1      |                |
| id_edt_emplacement_cours | int(12)      | YES  | MUL | -1      |                |
| id_groupe                | int(11)      | YES  | MUL | -1      |                |
| id_classe                | int(11)      | YES  | MUL | -1      |                |
| created_at               | datetime     | YES  |     | NULL    |                |
| updated_at               | datetime     | YES  |     | NULL    |                |
+--------------------------+--------------+------+-----+---------+----------------+
12 rows in set (0.00 sec)

mysql>

mysql> show fields from a_traitements;
+---------------------+--------------+------+-----+---------+----------------+
| Field               | Type         | Null | Key | Default | Extra          |
+---------------------+--------------+------+-----+---------+----------------+
| id                  | int(11)      | NO   | PRI | NULL    | auto_increment |
| utilisateur_id      | varchar(100) | YES  | MUL | -1      |                |
| a_type_id           | int(4)       | YES  | MUL | -1      |                |
| a_motif_id          | int(4)       | YES  | MUL | -1      |                |
| a_justification_id  | int(4)       | YES  | MUL | -1      |                |
| texte_justification | varchar(250) | YES  |     | NULL    |                |
| a_action_id         | int(4)       | YES  | MUL | -1      |                |
| commentaire         | text         | YES  |     | NULL    |                |
| created_at          | datetime     | YES  |     | NULL    |                |
| updated_at          | datetime     | YES  |     | NULL    |                |
+---------------------+--------------+------+-----+---------+----------------+
10 rows in set (0.00 sec)

mysql>
 *
 */

    // Date pour des requêtes MySQL
    $date_jour_mysql=date("Y-m-d");
    // A MODIFIER... utiliser $jour pour les saisies antérieures
    if($jour!=date('N')-1) {
        $jour_trouve="n";
        $instant=time();
        for($i=1;$i<7;$i++) {
            $instant=$instant-24*3600;
            $test_jour=date('N',$instant);
            if($test_jour-1==$jour) {
                $date_jour_mysql=date("Y-m-d",$instant);
                $jour_trouve="y";
                break;
            }
        }

        if($jour_trouve=="n") {
            echo "<p style='color:red'>Le jour de saisie n'a pas été identifié.</p>";
            require_once("../lib/footer.inc.php");
            die();
        }
    }

    // A REVOIR
    $tab_couleur_type_abs=array("red", "blue", "green", "purple", "olive", "violet", "marroon", "magenta", "cyan", "navy", "black", "black", "black", "black", "black", "black", "black", "black", "black");

    $tab_jour=array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche');


    include("crob_func.lib.php");

    //echo "<link rel='stylesheet' type='text/css' href='../edt_organisation/style_edt.css' />";
    //echo "<link rel='stylesheet' type='text/css' href='../templates/DefaultEDT/css/small_edt.css' />";

    // Pour les paramètres et l'enregistrement EDT
    require_once("../edt_organisation/choix_langue.php");
    require_once("../edt_organisation/fonctions_edt.php");
    require_once("../edt_organisation/fonctions_cours.php");

    //debug_var();

    /******************************** AIDE AIDE *****************************************/
    // Truc bizarre avec l'aide affichée par F2: si on met le background-color dans style='', ce qui est mis en class ne parvient plus à outrepasser les paramètres.
    // Et si on met les display: none; position: absolute;... dans la class, ça ne fonctionne pas.
    echo '
            <div id="idAidAbs" class="info_abs" style="display: none; position: absolute; color: white; width: 600px;">
                <!--Aucune aide n\'est encore saisie pour cette page.-->
                <p>Pour passer d\'un champ de formulaire à un autre, vous pouvez utiliser la touche TAB(ulation).<br />
                (<i>ce n\'est pas propre à GEPI</i>)</p>
                <p>Pour saisir les heures de début/fin, il peut arriver qu\'il faille effectuer un double-clic, un clic-glissé ou utiliser la touche TAB (<i>constaté avec Firefox</i>)</p>
            </div>
            <p> - aide [F2] - </p>
    ';
    /******************************** FIN  AIDE *****************************************/
    /*
    echo "<p style='color:olive;'>";
    echo "\$_SERVER['SCRIPT_NAME']=".$_SERVER['SCRIPT_NAME']."<br />";
    echo "\$gepiPath=$gepiPath<br />";
    echo "Mettre un test sur ces deux valeurs pour sécuriser le include('saisie_abs.inc.php').<br />";
    echo "Avoir ici les tests de qui tente d'accéder,....<br />";
    echo "</p>";
    */
    if(($_SERVER['SCRIPT_NAME']!="$gepiPath/mod_abs2/index.php")&&
        ($_SERVER['SCRIPT_NAME']!="$gepiPath/mod_abs2/saisie_abs2.php")&&
        ($_SERVER['SCRIPT_NAME']!="$gepiPath/mod_abs2/saisie_abs2b.php")) {
        echo "<p style='color:red;'>Le chemin SCRIPT_NAME ".$_SERVER['SCRIPT_NAME']." ne coïncide pas avec $gepiPath/mod_abs2/index.php</p>\n";
        require_once("../lib/footer.inc.php");
        die();
    }

    $destination_form="saisie_abs2b.php";
    // Pour permettre de faire fonctionner encore le temps de tests le dispositif avec onglets+ajax (page chargée dans un onglet)
    if($_SERVER['SCRIPT_NAME']!="$gepiPath/mod_abs2/saisie_abs2b.php") {
        $destination_form="index.php";
    }
    /*
    else {
        $destination_form=$_SERVER['PHP_SELF'];
    }
    */
    //echo "\$destination_form=$destination_form<br />";

    // A VERIFIER: Le stockage en session de $utilisateur m'a parfois fait un catchable error...
    $login_prof=$_SESSION['login'];
    $utilisateur = UtilisateurProfessionnelPeer::retrieveByPK($_SESSION['login']);
    //$_SESSION['utilisateurProfessionnel'] = $utilisateur;


    $id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);
    $id_debut_creneau=isset($_POST['id_debut_creneau']) ? $_POST['id_debut_creneau'] : (isset($_GET['id_debut_creneau']) ? $_GET['id_debut_creneau'] : NULL);
    $id_fin_creneau=isset($_POST['id_fin_creneau']) ? $_POST['id_fin_creneau'] : (isset($_GET['id_fin_creneau']) ? $_GET['id_fin_creneau'] : NULL);
    $id_cours=isset($_POST['id_cours']) ? $_POST['id_cours'] : (isset($_GET['id_cours']) ? $_GET['id_cours'] : NULL);

    // Au cas où on ne récupèrerait pas l'id_groupe dans le $tab_data de l'EDT...
    if(isset($id_cours)) {
        $sql="SELECT id_groupe FROM edt_cours WHERE id_cours='$id_cours';";
        //echo "$sql<br />";
        $res_cours=mysql_query($sql);
        if(mysql_num_rows($res_cours)>0) {
            $lig_cours=mysql_fetch_object($res_cours);
            if(($lig_cours->id_groupe=='')||($lig_cours->id_groupe==0)) {
                // Ce doit être un AID...
                // FAIRE la recherche correspondante...

                //+++++++++++++
                //++ A FAIRE ++
                //+++++++++++++

            }
            else {
                $id_groupe=$lig_cours->id_groupe;
            }
        }
    }
    //debug_var();

    //====================================================================================
    // Liste des types pour affichage Javascript:
    $chaine_js="var att_info_abs=new Array();";

    // Recuperer les a_types existants
    $liste_a_types = AbsenceEleveTypeQuery::create()->find();
    $tab_a_types=array();
    foreach($liste_a_types as $a_type) {
        //$a_type=new AbsenceEleveType();
        //$tab_a_types[]=$a_type->getTypeSaisie();

        // Le type est-il autorisé pour le statut de l'utilisateur
        $test=AbsenceEleveTypeStatutAutoriseQuery::create()->
                filterByStatut($utilisateur->getStatut())->
                filterByIdAType($a_type->getId())->
                find()->isEmpty();
        if(!$test) {
            $tab_a_types[]=$a_type->getId();
            $tab_a_types_nom[$a_type->getId()]=$a_type->getNom();
            abs2_debug("\$tab_a_types[]=".$a_type->getId()."<br />");

            // Liste des types pour affichage Javascript:
            //$chaine_js.="var att_info_abs[".$a_type->getId()."]='".$a_type->getNom()."';";
            $chaine_js.="att_info_abs[".$a_type->getId()."]='".$a_type->getNom()."';";

        }
        else {
            abs2_debug("Type ".$a_type->getId()." non autorisé.<br />");
        }
    }
    //====================================================================================


    //====================================================================================
    // Enregistrement des saisies
    // Dispositif sur $msg et enregistrement à remonter.
    // Et mettre un header('Location:...') pour éviter d'insérer plusieurs fois les mêmes enregistrements lors de rafraichissements de la page avec F5
    if(!isset($msg)) {$msg="";}

    $enregistrer_abs=isset($_POST['enregistrer_abs']) ? $_POST['enregistrer_abs'] : NULL;
    if($enregistrer_abs=='y') {

        abs2_debug("\$id_groupe=".$id_groupe."<br />");

        // Parcourir les membres du groupe et voir les valeurs s'il y a des saisie_<IdEleve> non "present"
        $group=GroupePeer::retrieveByPK($id_groupe);
        $eleves=$group->getEleves($num_periode);
        foreach($eleves as $eleve) {
            abs2_debug("<p>\$eleve->getIdEleve()=".$eleve->getIdEleve()."<br />");
            abs2_debug("\$eleve->getNom()=".$eleve->getNom()."<br />");
            $saisie_a_type=isset($_POST['saisie_'.$eleve->getIdEleve()]) ? $_POST['saisie_'.$eleve->getIdEleve()] : NULL;
            abs2_debug("\$saisie_a_type=$saisie_a_type<br />");

            // Si le Type est vide, il faut quand même enregistrer... "ELEVE NON PRESENT"
            if((isset($saisie_a_type))&&($saisie_a_type!='present')&&($saisie_a_type!='')) {
                if((in_array($saisie_a_type,$tab_a_types))||($saisie_a_type=='non_present')) {
                    $debut_saisie=isset($_POST['debut_'.$eleve->getIdEleve().'_'.$saisie_a_type]) ? $_POST['debut_'.$eleve->getIdEleve().'_'.$saisie_a_type] : NULL;
                    $fin_saisie=isset($_POST['fin_'.$eleve->getIdEleve().'_'.$saisie_a_type]) ? $_POST['fin_'.$eleve->getIdEleve().'_'.$saisie_a_type] : NULL;
                    $commentaire_saisie=isset($_POST['commentaire_'.$eleve->getIdEleve().'_'.$saisie_a_type]) ? $_POST['commentaire_'.$eleve->getIdEleve().'_'.$saisie_a_type] : NULL;

                    abs2_debug("<p class='bold'>\$eleve->getIdEleve()=".$eleve->getIdEleve()."<br />");
                    abs2_debug("\$eleve->getNom()=".$eleve->getNom()."<br />");
                    abs2_debug("\$debut_saisie=".$debut_saisie."<br />");
                    abs2_debug("\$fin_saisie=".$fin_saisie."<br />");
                    abs2_debug("\$eleve->getClasse($num_periode)->getClasse()=".$eleve->getClasse($num_periode)->getClasse()."<br />");
                    abs2_debug("\$eleve->getClasse($num_periode)->getId()=".$eleve->getClasse($num_periode)->getId()."<br />");

                    // A FAIRE: Contrôler si des heures de début/fin/... sont attendus pour le type de saisie courant


                    // Créer un enregistrement dans a_saisies et a_traitements
                    //$eleve=new Eleve();
                    $saisie = new AbsenceEleveSaisie();
                    $saisie->setIdClasse($eleve->getClasse($num_periode)->getId());
                    //$eleve_id_classe=$eleve->getClasse($num_periode)->getId();
                    //$saisie->setIdClasse($eleve_id_classe);
                    $saisie->setIdGroupe($id_groupe);
                    $saisie->setUtilisateurId($_SESSION['login']);
                    $saisie->setEleveId($eleve->getIdEleve());
                    abs2_debug("et maintenant \$eleve->getIdEleve()=".$eleve->getIdEleve()."<br />");
                    if(isset($debut_saisie)) {$saisie->setDebutAbs($debut_saisie);}
                    if(isset($fin_saisie)) {$saisie->setFinAbs($fin_saisie);}
                    if(isset($commentaire_saisie)) {$saisie->setCommentaire($commentaire_saisie);}
                    //$edtCreneau = EdtCreneauPeer::getEdtCreneauActuel();
                    $edtCreneau=EdtCreneauPeer::retrieveByPK($id_debut_creneau);
                    $saisie->setEdtCreneau($edtCreneau);
                    //$saisie->save();
                    $_last_id=$saisie->save();

                    if($_last_id) {
                        if($saisie_a_type!='non_present') {
                            //$traitement = new AbsenceTraitement();
                            $traitement = new AbsenceEleveTraitement();
                            $traitement->setATypeId($saisie_a_type);
                            //$traitement->save();
                            if($traitement->save()) {
                                //$join = new JTraitementSaisie();
                                $join = new JTraitementSaisieEleve();
                                $join->setASaisieId($saisie->getId());
                                $join->setATraitementId($traitement->getId());
                                //$join->save();
                                if($join->save()) {
                                    $msg.="Enregistrement de ".$tab_a_types_nom[$saisie_a_type]." pour ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                                                                }
                                else {
                                    // Faudrait-il nettoyer a_traitements?
                                    $msg.="Echec de l'association du traitement (typage de l'absence) avec la saisie pour ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                                }
                            }
                            else {
                                $msg.="Echec du typage de l'absence pour ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                            }
                        }
                        else {
                            $msg.="Enregistrement de l'absence de ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                        }
                    }
                    else {
                        if($saisie_a_type!='non_present') {
                            $msg.="Erreur lors de l'enregistrement de type ".$tab_a_types_nom[$saisie_a_type]." pour l'élève ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                        }
                        else {
                            $msg.="Erreur lors de l'enregistrement de l'absence pour l'élève ".$eleve->getNom()." ".$eleve->getPrenom()."<br />";
                        }
                    }

                }
                else {
                    $msg.="Type d'absence n°$saisie_a_type inconnu pour l'élève ".$eleve->getNom()."<br />";
                }
            }
        }

        if(isset($_POST['enregistrer_cours_dans_edt'])) {
            $identite=$_SESSION['login'];

            $id_cours = isset($_GET["id_cours"]) ? $_GET["id_cours"] : (isset($_POST["id_cours"]) ? $_POST["id_cours"] : NULL);
            $type_edt = isset($_GET["type_edt"]) ? $_GET["type_edt"] : (isset($_POST["type_edt"]) ? $_POST["type_edt"] : NULL);
            //$identite = isset($_GET["identite"]) ? $_GET["identite"] : (isset($_POST["identite"]) ? $_POST["identite"] : NULL);
            $modifier_cours = isset($_POST["modifier_cours"]) ? $_POST["modifier_cours"] : NULL;
            //$enseignement = isset($_POST["enseignement"]) ? $_POST["enseignement"] : NULL;
            $enseignement = $id_groupe;
            // Ca pourrait être un AID? Non géré dans les choix actuels pour les absences, il me semble...

            $ch_jour_semaine = isset($_POST["ch_jour_semaine"]) ? $_POST["ch_jour_semaine"] : NULL;
            $ch_heure = isset($_POST["ch_heure"]) ? $_POST["ch_heure"] : NULL;
            $heure_debut = isset($_POST["heure_debut"]) ? $_POST["heure_debut"] : NULL;
            $duree = isset($_POST["duree"]) ? $_POST["duree"] : NULL;
            $choix_semaine = isset($_POST["choix_semaine"]) ? $_POST["choix_semaine"] : NULL;
            $login_salle = isset($_POST["login_salle"]) ? $_POST["login_salle"] : NULL;
            $periode_calendrier = isset($_POST["periode_calendrier"]) ? $_POST["periode_calendrier"] : NULL;
            $aid = isset($_POST["aid"]) ? $_POST["aid"] : NULL;
            $horaire = isset($_GET["horaire"]) ? $_GET["horaire"] : (isset($_POST["horaire"]) ? $_POST["horaire"] : NULL);
            $cours = isset($_GET["cours"]) ? $_GET["cours"] : (isset($_POST["cours"]) ? $_POST["cours"] : NULL);
            $period_id=isset($_GET['period_id']) ? $_GET['period_id'] : (isset($_POST['period_id']) ? $_POST['period_id'] : NULL);
            $message = "";
            $id_aid = "";
            $analyse = explode("|", $enseignement);
            if ($analyse[0] == "AID") {
                $id_aid = $analyse[1];
                $enseignement = "";
            }

            if (ProfDisponible($identite, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
                if (SalleDisponible($login_salle, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
                    if (GroupeDisponible($enseignement, $id_aid, $ch_jour_semaine, $ch_heure, $duree, $heure_debut, $choix_semaine, -1, $message, $periode_calendrier)) {
                        $sql="INSERT INTO edt_cours SET id_groupe = '$enseignement',
                                id_aid = '$id_aid',
                                 id_salle = '$login_salle',
                                 jour_semaine = '$ch_jour_semaine',
                                 id_definie_periode = '$ch_heure',
                                 duree = '$duree',
                                 heuredeb_dec = '$heure_debut',
                                 id_semaine = '$choix_semaine',
                                 id_calendrier = '$periode_calendrier',
                                 login_prof = '".$identite."'";
                        echo "$sql<br />";
                        $nouveau_cours = mysql_query($sql);
                        if($nouveau_cours) {
                            $msg.="Enregistrement du cours dans l'EDT effectué.<br />";
                        }
                        else {
                            $msg.="Echec de l'enregistrement du cours dans l'EDT.<br />";
                        }
                    }
                }
            }
        }

        // A PROPOSER/UTILISER...
        $afficher_lien_passage_cdt="y";
    }
    //====================================================================================

    echo "<div style='color:red; text-align:center;'>$msg</div>";

    if((!isset($id_groupe))||(!isset($id_debut_creneau))||(!isset($id_fin_creneau))) {
        if($_SESSION['statut']=='professeur') {
            // Choix de l'enseignement
            echo "<p>Choisissez le cours/créneau&nbsp;:</p>\n";
            //===============================================
            // Choix dans les enseignements du prof sur la journée courante:

            echo "<div style='margin-left: 5em; padding: 3px; border: 1px solid black;'>\n";
            // Afficher une ligne d'EDT
            $tab_data=ConstruireEDTProfDuJour($utilisateur->getLogin(), 0, $jour);
            /*
            echo "<pre>";
            print_r($tab_data);
            echo "</pre>";
            */
            $flags = NO_INFOBULLE + HORIZONTAL;
            // pour toutes les valeurs possibles, voir /edt_organisation/fonctions_affichage.php
            /*
            $flags = NO_INFOBULLE + HORIZONTAL;
            $flags = NO_INFOBULLE + VERTICAL;
            $flags = INFOBULLE + HORIZONTAL;
            $flags = INFOBULLE + VERTICAL;
            */

            for($i=0;$i<$tab_data['nb_creneaux'];$i++) {
                if($tab_data[$jour]['type'][$i]=='cours') {
                    if($tab_data[$jour]['id_groupe'][$i]!='') {
                        // J'ai bricolé le fonctions_edt_prof.php pour renseigner l'id_groupe... en remplaçant:
                        //    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], "", $rep_creneau['id_cours'], "cellule".$duree_max, "cadreCouleur", $contenu);
                        // par
                        //    RemplirBox($elapse_time,$tab_data[$jour], $index_box, "cours", $tab_id_creneaux[$j], $rep_creneau['id_groupe'], $rep_creneau['id_cours'], "cellule".$duree_max, "cadreCouleur", $contenu);

                        // Comment récupérer le créneau de fin: id_fin_creneau?
                        //$tab_data[$jour]['extras'][$i]="<a href='index.php?jour=$jour&amp;id_groupe=".$tab_data[$jour]['id_groupe'][$i]."&amp;id_cours=".$tab_data[$jour]['id_cours'][$i]."&amp;onglet_abs=saisie_abs&amp;id_debut_creneau=".$tab_data[$jour]['id_creneau'][$i]."&amp;id_fin_creneau=".$tab_data[$jour]['id_creneau'][$i]."'>Go</a>";
                        $tab_data[$jour]['extras'][$i]="<a href='".$destination_form."?jour=$jour";
                        if(isset($tab_data[$jour]['id_groupe'][$i])) {$tab_data[$jour]['extras'][$i].="&amp;id_groupe=".$tab_data[$jour]['id_groupe'][$i];}
                        $tab_data[$jour]['extras'][$i].="&amp;id_cours=".$tab_data[$jour]['id_cours'][$i]."&amp;onglet_abs=saisie_abs&amp;id_debut_creneau=".$tab_data[$jour]['id_creneau'][$i]."&amp;id_fin_creneau=".$tab_data[$jour]['id_creneau'][$i]."'><img src='../images/saisie.png' width='16' height='16' alt='Saisir les absences pour ce cours/créneau' title='Saisir les absences pour ce cours/créneau' /></a>";
                    }
                }
            }
            echo EdtDuJour($tab_data, $jour, $flags);

            /*
            $tab_id_creneaux = retourne_id_creneaux();
            echo "<pre>";
            print_r($tab_id_creneaux);
            echo "</pre>";
            */

            // Anomalie sur l'EDT... on doit avoir un DIV non refermé.
            echo "</div>\n";
            echo "<div style='clear:both;'></div>\n";
            echo "</div>\n";
            //===============================================

            //===============================================
            echo "<p>Ou choisissez librement un de vos enseignements&nbsp;:</p>\n";
            echo "<div style='margin-left: 5em; padding: 3px; border: 1px solid black;'>\n";
            // Choix libre dans les enseignements du prof:
            //echo "<form action='index.php' method='post'>\n";
            echo "<form action='".$destination_form."' method='post'>\n";
            echo "<input type='hidden' name='onglet_abs' value='saisie_abs' />\n";

            $groups=$utilisateur->getGroupes();
            /*
            echo "<pre>";
            print_r($groups);
            echo "</pre>";
            */
            echo "<p>Mes enseignements&nbsp;: <select name='id_groupe'>\n";
            foreach ($groups as $group) {
                    echo "<option value='".$group->getId()."'>".$group->getNameAvecClasses()."</option>\n";
            }
            echo "</select>\n";
            echo " <span style='color:red'> à revoir: on ne récupère pas les AID...</span>";
            /*
            $tab_aid=renvoieAid("prof", $_SESSION['login']);
            echo "<p>Mes AID&nbsp;: <select name='indice_aid'>\n";
            for($i=0;$i<count($tab_aid);$i++) {
                echo "<option value='A".$tab_aid['indice_aid']."'>".$tab_aid['nom']."</option>\n";
            }
            echo "</select>\n";
            */
            echo "<br />\n";

            echo "Jour&nbsp;: ";
            echo "<select name='jour'>\n";
            for($i=0;$i<count($tab_jour);$i++) {
                echo "<option value='$i'";
                if($i==date('N')-1) {echo " selected";}
                echo ">".$tab_jour[$i]."</option>\n";
            }
            echo "</select>\n";

            $c = new Criteria();
            $c->add(EdtCreneauPeer::JOUR_CRENEAU,'');
            //$c->add(CreneauPeer::JOUR_CRENEAU, "9");
            $creneaux_objects = EdtCreneauPeer::DoSelect($c);

            echo "De&nbsp;";
            echo "<select name='id_debut_creneau' id='debut_creneau' onchange=\"document.getElementById('fin_creneau').selectedIndex=document.getElementById('debut_creneau').selectedIndex\">\n";
            foreach($creneaux_objects as $creneau) {
                //$creneau=EdtCreneauPeer::retrieveByPK(1); // pour la complétion le temps du devel
                //echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".formate_heure($creneau->getHeuredebutDefiniePeriode()).")</option>\n";
                echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".$creneau->getHeuredebutDefiniePeriode().")</option>\n";
            }
            echo "</select>\n";


            echo " à&nbsp;";
            echo "<select name='id_fin_creneau' id='fin_creneau'>\n";
            foreach($creneaux_objects as $creneau) {
                //echo "<option value='".$creneau->getId()."'>".$creneau->getNomCreneau()." (".formate_heure($creneau->getFinCreneau()).")</option>\n";
                //echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".formate_heure($creneau->getHeurefinDefiniePeriode()).")</option>\n";
                echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".$creneau->getHeurefinDefiniePeriode().")</option>\n";
            }
            echo "</select>\n";

            echo "<span style='color:red;'>A FAIRE: Afficher le créneau courant par défaut...</span>";

            echo "<br />\n";
            echo "<input type='submit' value='Saisir les absences et retards' />\n";
            echo "</form>\n";
            echo "</div>\n";
        }

        //===============================================
        // Choix libre hors enseignements du prof... pour les remplacements,...

        echo "<p>";
        if($_SESSION['statut']=='professeur') {echo "Ou c";}else {echo "C";}
        echo "hoisissez librement un enseignement même en dehors de vos enseignements (<i>remplacements,...</i>)&nbsp;:</p>\n";

        echo "<div style='margin-left: 5em; padding: 3px; border: 1px solid black;'>\n";
        // Choix libre dans tous les enseignements:

        // Choix de la classe, puis de l'enseignement, du créneau,...
        if(!isset($_GET['id_classe'])) {
            $sql="SELECT DISTINCT c.id,c.classe FROM classes c ORDER BY c.classe";
            //echo "$sql<br />";
            $res_clas=mysql_query($sql);
            if(mysql_num_rows($res_clas)>0) {
                echo "<p>Commencez par choisir une classe:</p>\n";

                $tab_txt=array();
                $tab_lien=array();

                while($lig_clas=mysql_fetch_object($res_clas)) {
                    $tab_txt[]=$lig_clas->classe;
                    //$tab_lien[]=$_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
                    $tab_lien[]=$destination_form."?id_classe=".$lig_clas->id."&amp;onglet_abs=saisie_abs";
                }

                echo "<blockquote>\n";
                tab_liste($tab_txt,$tab_lien,4);
                echo "</blockquote>\n";
            }
            else {
                echo "<p style='color:red;'>Aucune classe n'a été trouvée???</p>\n";
            }
        }
        else {
            //echo "<form action='index.php' method='post'>\n";
            echo "<form action='".$destination_form."' method='post'>\n";
            echo "<input type='hidden' name='onglet_abs' value='saisie_abs' />\n";

            $classe=ClassePeer::retrieveByPK($_GET['id_classe']);
            $groups=$classe->getGroupes();
            /*
            echo "<pre>";
            print_r($groups);
            echo "</pre>";
            */
            echo "<p>Les enseignements de ".$classe->getClasse()." &nbsp;: <select name='id_groupe'>\n";
            foreach ($groups as $group) {
                    echo "<option value='".$group->getId()."'>".$group->getNameAvecClasses()."</option>\n";
            }
            echo "</select>\n";
            echo " <span style='color:red'> à revoir: on ne récupère pas les AID...</span>";
            /*
            $tab_aid=renvoieAid("prof", $_SESSION['login']);
            echo "<p>Mes AID&nbsp;: <select name='indice_aid'>\n";
            for($i=0;$i<count($tab_aid);$i++) {
                echo "<option value='A".$tab_aid['indice_aid']."'>".$tab_aid['nom']."</option>\n";
            }
            echo "</select>\n";
            */
            echo "<br />\n";

            echo "Jour&nbsp;: ";
            echo "<select name='jour'>\n";
            for($i=0;$i<count($tab_jour);$i++) {
                echo "<option value='$i'";
                if($i==date('N')-1) {echo " selected";}
                echo ">".$tab_jour[$i]."</option>\n";
            }
            echo "</select>\n";

            $c = new Criteria();
            $c->add(EdtCreneauPeer::JOUR_CRENEAU,'');
            //$c->add(CreneauPeer::JOUR_CRENEAU, "9");
            $creneaux_objects = EdtCreneauPeer::DoSelect($c);

            echo "De&nbsp;";
            echo "<select name='id_debut_creneau' id='debut_creneau' onchange=\"document.getElementById('fin_creneau').selectedIndex=document.getElementById('debut_creneau').selectedIndex\">\n";
            foreach($creneaux_objects as $creneau) {
                //$creneau=EdtCreneauPeer::retrieveByPK(1); // pour la complétion le temps du devel
                //echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".formate_heure($creneau->getHeuredebutDefiniePeriode()).")</option>\n";
                echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".$creneau->getHeuredebutDefiniePeriode().")</option>\n";
            }
            echo "</select>\n";


            echo " à&nbsp;";
            echo "<select name='id_fin_creneau' id='fin_creneau'>\n";
            foreach($creneaux_objects as $creneau) {
                //echo "<option value='".$creneau->getId()."'>".$creneau->getNomCreneau()." (".formate_heure($creneau->getFinCreneau()).")</option>\n";
                //echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".formate_heure($creneau->getHeurefinDefiniePeriode()).")</option>\n";
                echo "<option value='".$creneau->getIdDefiniePeriode()."'>".$creneau->getNomDefiniePeriode()." (".$creneau->getHeurefinDefiniePeriode().")</option>\n";
            }
            echo "</select>\n";

            echo "<span style='color:red;'>A FAIRE: Afficher le créneau courant par défaut...</span>";

            echo "<br />\n";
            echo "<input type='submit' value='Saisir les absences et retards' />\n";
            echo "</form>\n";
        }
        echo "</div>\n";
        //===============================================

    }
    else {
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // ++++++++++++++++++++ Passage à la saisie proprement dite +++++++++++++++++++++++++++++
        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        // ++++++++++++++++++++++++++++++++
        // Le groupe est maintenant choisi
        // ++++++++++++++++++++++++++++++++

        debug_var();

        // Récupération du groupe
        $group = GroupePeer::retrieveByPk($id_groupe);

        // Récupération des créneaux de saisie
        $creneau_debut=EdtCreneauPeer::retrieveByPK($id_debut_creneau);
        $nom_creneau=$creneau_debut->getNomDefiniePeriode();
        if($id_debut_creneau==$id_fin_creneau) {$creneau_fin=$creneau_debut;}
        else {
            $creneau_fin=EdtCreneauPeer::retrieveByPK($id_fin_creneau);
        }

        echo "<h2>Saisie des absences, retards,... pour ".$group->getNameAvecClasses()." le ".$tab_jour[$jour]." en créneau ".$nom_creneau.".</h2>\n";


        // +++++++++++++++++++++++++++++++++++++++++
        // A TESTER: Est-ce un enseignement du prof?
        // Si ce n'est pas un enseignement du prof, c'est un remplacement
        // Enregistre-t-on dans une autre table? ou teste-t-on la présence d'un flag?
        // Avec une autre table, on peut s'affranchir de préparatifs côté scolarité/cpe
        // +++++++++++++++++++++++++++++++++++++++++


        echo "<script type='text/javascript'>\n";
// Liste des types pour affichage Javascript:
echo $chaine_js;

echo "// Pour mettre à jour la colonne 'En attente de validation'
function maj_attente_validation(champ,IdEleve) {
    a_type_id=champ.value;
    //alert(a_type_id);
    if(att_info_abs[a_type_id]) {
        document.getElementById('cell_attente_valid_'+IdEleve).innerHTML=att_info_abs[a_type_id];
    }
    else {
        if(a_type_id=='non_present') {
            document.getElementById('cell_attente_valid_'+IdEleve).innerHTML='Non présent';
        }
        else {
            document.getElementById('cell_attente_valid_'+IdEleve).innerHTML='X';
        }
    }
}
</script>\n";


        //echo "<form name='form_saisie_abs' action='index.php' method='post'>\n";
        echo "<form name='form_saisie_abs' action='".$destination_form."' method='post'>\n";

        echo "<table class='boireaus' summary='Saisie des absences et retards'>\n";

        echo "<tr>\n";
        //echo "<th rowspan='2'>Elèves</th>\n";
        //echo "<th colspan='$nb_colspan'>Saisie</th>\n";
        echo "<th>Elèves</th>\n";
        echo "<th>Saisie</th>\n";
        echo "<th>En attente<br />de validation</th>\n";

        // Créneaux de la journée
        //$creneaux_objects = EdtCreneauPeer::getAllEdtCreneauxOrderByTime();
        $creneaux_objects = EdtCreneauPeer::retrieveAllEdtCreneauxOrderByTime();

        foreach($creneaux_objects as $creneau) {
            //echo "<th rowspan='2'>";
            echo "<th>";
            echo $creneau->getNomDefiniePeriode()."<br /><span style='font-size:x-small'>(".formate_time_mysql_to_heure_min($creneau->getHeuredebutDefiniePeriode())."<br />-".formate_time_mysql_to_heure_min($creneau->getHeurefinDefiniePeriode()).")</span>\n";
            echo "</th>\n";
        }
        //echo "<th rowspan='2'>Photo</th>\n";
        echo "<th>Photo";
        echo "<br /><span style='color:red'>Pour le moment, j'ai mis aussi l'EDT de l'élève, mais faire apparaitre les absences,... sur cet edt serait mieux... ou alors le faire apparaitre lors du survol/clic sur une image</span>";
        echo "</th>\n";
        echo "</tr>\n";

        $heure_courante=strftime("%H:%M");

        $alt=1;
        foreach($group->getEleves($num_periode) as $eleve) {
            //echo "\$eleve->getNom(),...=".$eleve->getNom()." ".$eleve->getPrenom()." (".$eleve->getIdEleve().")<br />";

            $alt=$alt*(-1);
            echo "<tr class='lig$alt whitehover'>\n";

            echo "<td>".$eleve->getNom()." ".$eleve->getPrenom()."</td>\n";

            echo "<td>\n";
            //echo "<span class='infobulle_crob'><input type='radio' name='etat_abs[".$eleve->getIdEleve()."]' value='".$tab_type[$loop]['id']."' />"."<div>".$tab_type[$loop]['nom']." pour ".$eleve->getNom()." ".$eleve->getPrenom()."</div></span>";

            if($mode_saisie_abs==0) {
                echo "<span class='conteneur_infobulle_css'><img src='../images/saisie.png' width='16 height='16' alt='Saisir les absence/retard/... pour cet élève' title='Saisir les absence/retard/... pour cet élève' />";
                echo "<div class='infobulle_css' style='text-align:left;'><b>".$eleve->getNom()." ".$eleve->getPrenom()."</b><br />\n";
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_present' value='present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' /><label for='saisie_".$eleve->getIdEleve()."_present'> Annuler</label> <span style='font-size:small;'>(erreur de saisie, élève présent)</span><br />\n";
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_non_present' value='non_present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' /><label for='saisie_".$eleve->getIdEleve()."_non_present'> Non présent</label><br />\n";
                //echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_present' value='present' onchange='maj_attente_validation(this)' /><label for='saisie_".$eleve->getIdEleve()."_present'> Annuler</label> <span style='font-size:small;'>(erreur de saisie, élève présent)</span><br />\n";
                //echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_non_present' value='non_present' onchange='maj_attente_validation(this)' /><label for='saisie_".$eleve->getIdEleve()."_non_present'> Non présent</label><br />\n";
                echo "Ou plus précisemment:<br />\n";

                foreach ($liste_a_types as $a_type) {
                    if(in_array($a_type->getId(),$tab_a_types)) {
                        $sql="SELECT * FROM a_types_statut WHERE id_a_type='".$a_type->getId()."' AND statut='".$_SESSION['statut']."';";
                        $test_statut=mysql_query($sql);
                        if(mysql_num_rows($test_statut)>0) {

                            //echo "<input type='radio' class='abs_crob' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$a_type->getId()."' onchange='maj_attente_validation(this)' /><label for='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."'> ".$a_type->getNom();
                            echo "<input type='radio' class='abs_crob' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$a_type->getId()."' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' /><label for='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."'> ".$a_type->getNom();
                            echo "<span class='details_saisie_abs'><br />\n";
                            //echo "<div class='details_saisie_abs'>\n";
                            // DEBUT_ABS, FIN_ABS, DEBUT_ET_FIN_ABS, NON_PRECISE, COMMENTAIRE_EXIGE

                            // Ce n'est pas génial d'avoir un TABLE dans un SPAN... mais ça ne veut pas fonctionner avec un DIV dans le LABEL
                            echo "<table style='margin-left:2em;'>";
                            if(strstr($a_type->getTypeSaisie(),'DEBUT_ET_FIN_ABS')) {
                                // Mettre début du créneau...
                                echo "<tr><td>Début&nbsp;:</td><td><input type='text' name='debut_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."' size='6' /></td></tr>\n";
                                echo "<tr><td>Fin&nbsp;:</td><td><input type='text' name='fin_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$creneau_fin->getHeurefinDefiniePeriode("%H:%M")."' size='6' /></td></tr>\n";
                                // Que mettre? si Infirmerie, on ne sait pas quand il va revenir... permettre de modifier...
                                // si Absence, on suppose que c'est la fin du créneau
                            }
                            elseif(strstr($a_type->getTypeSaisie(),'DEBUT_ABS')) {
                                // Mettre début du créneau...
                                echo "<tr><td>Début&nbsp;:</td><td><input type='text' name='debut_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."' size='6' /></td></tr>\n";
                            }
                            elseif(strstr($a_type->getTypeSaisie(),'FIN_ABS')) {
                                // Mettre début du créneau...
                                //$heure_courante=strftime("%H:%M");
                                echo "<tr><td>Fin&nbsp;:</td><td><input type='text' name='fin_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$heure_courante."' size='6' /></td></tr>\n";
                            }

                            if(strstr($a_type->getTypeSaisie(),'COMMENTAIRE_EXIGE')) {
                                // Mettre début du créneau...
                                echo "<tr><td>Commentaire&nbsp;:</td><td><textarea cols=10, rows=4 name='commentaire_".$eleve->getIdEleve()."_".$a_type->getId()."'></textarea></td></tr>\n";
                            }

                            echo "</table>";

                            // Alternative, mettre les champs en ligne, toujours visibles dans le DIV?
                            echo "</span>\n";
                            //echo "</div>\n";
                            echo "</label><br />\n";
                        }
                    }
                }
                echo "</div></span>";
            }
            elseif($mode_saisie_abs==1) {

                echo "<div class='conteneur_infobulle_css'>Saisie";

                echo "<div class='infobulle_css' style='text-align:left;'><b>".$eleve->getNom()." ".$eleve->getPrenom()."</b><br />\n";

                // Tableau des choix de saisie
                echo "<table class='boireaus'>\n";
                echo "<tr class='lig-1'>\n";
                echo "<td>\n";
                // 'present' n'est pas le bon terme...
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_present' value='present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                echo "</td>\n";
                echo "<td colspan='4'>\n";
                echo "<label for='saisie_".$eleve->getIdEleve()."_present'> Annuler</label> <span style='font-size:small;'>(erreur de saisie, élève présent)</span><br />\n";
                echo "</td>\n";
                echo "</tr>\n";

                echo "<tr class='lig1'>\n";
                echo "<td>\n";
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_non_present' value='non_present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "<label for='saisie_".$eleve->getIdEleve()."_non_present'> Non présent</label><br />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "de&nbsp;:";
                echo "<input type='text' name='debut_".$eleve->getIdEleve()."_non_present' value='".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."' size='6' />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo " à&nbsp;:";
                //echo "<input type='text' name='fin_".$eleve->getIdEleve()."_non_present' value='".$creneau_fin->getHeuredebutDefiniePeriode("%H:%M")."' size='6' />\n";
                echo "<input type='text' name='fin_".$eleve->getIdEleve()."_non_present' value='".$heure_courante."' size='6' />\n";
                echo "</td>\n";
                echo "<td>\n";
                echo "<textarea cols=10 rows=1 name='commentaire_".$eleve->getIdEleve()."_non_present'></textarea>";
                echo "</td>\n";
                echo "</tr>\n";

                echo "<tr><td colspan='5'>Ou plus précisemment:</td></tr>\n";

                $altb=1;
                foreach ($liste_a_types as $a_type) {
                    if(in_array($a_type->getId(),$tab_a_types)) {
                        $sql="SELECT * FROM a_types_statut WHERE id_a_type='".$a_type->getId()."' AND statut='".$_SESSION['statut']."';";
                        $test_statut=mysql_query($sql);
                        if(mysql_num_rows($test_statut)>0) {

                            $altb=$altb*(-1);
                            echo "<tr class='lig$altb'>\n";
                            echo "<td>\n";
                            echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$a_type->getId()."' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                            echo "</td>\n";
                            echo "<td>\n";
                            echo "<label for='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."'> ".$a_type->getNom()."</label>\n";
                            echo "</td>\n";
                            echo "<td>\n";
                            echo "de&nbsp;:";
                            echo "<input type='text' name='debut_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."' size='6' />\n";
                            echo "</td>\n";
                            echo "<td>\n";
                            echo " à&nbsp;:";
                            //echo "<input type='text' name='fin_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$creneau_fin->getHeuredebutDefiniePeriode("%H:%M")."' size='6' />\n";
                            echo "<input type='text' name='fin_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$heure_courante."' size='6' />\n";
                            echo "</td>\n";
                            echo "<td>\n";
                            echo "<textarea cols=10 rows=1 name='commentaire_".$eleve->getIdEleve()."_".$a_type->getId()."'></textarea>";
                            echo "</td>\n";
                            echo "</tr>\n";
                        }
                    }
                }
                echo "</table>\n";
                echo "</div>\n";
                echo "</div>\n";
            }
            elseif($mode_saisie_abs==2) {

                echo "<div class='conteneur_infobulle_css'>Saisie";

                echo "<div class='infobulle_css' style='text-align:left;'><b>".$eleve->getNom()." ".$eleve->getPrenom()."</b><br />\n";

                // Tableau des choix de saisie
                echo "<table class='boireaus'>\n";
                echo "<tr class='lig-1'>\n";
                echo "<td>\n";
                // 'present' n'est pas le bon terme...
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_present' value='present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                echo "</td>\n";
                echo "<td colspan='4' style='text-align:left;'>\n";
                echo "<label for='saisie_".$eleve->getIdEleve()."_present'> Annuler</label> <span style='font-size:small;'>(annuler une saisie non encore validée)</span><br />\n";
                echo "</td>\n";
                echo "</tr>\n";

                echo "<tr class='lig1'>\n";
                echo "<td>\n";
                echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_non_present' value='non_present' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                echo "</td>\n";
                echo "<td style='text-align:left;'>\n";
                echo "<label for='saisie_".$eleve->getIdEleve()."_non_present'> Non présent</label><br />\n";
                echo "</td>\n";

                /*
                // Ajouter un champ:
                echo "<td>\n";
                $chaine_tmp=$eleve->getIdEleve()."_non_present";
                echo "<a href='#' onclick=\"ajout_champ('cell_ajout_".$chaine_tmp."', 'debut_".$chaine_tmp."', '".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."', 'debut');return false;\">D</a>";
                echo "</td>\n";
                 */
                echo "<td id='cell_ajout_".$eleve->getIdEleve()."_non_present_de'>\n";
                echo "<a href='#' onclick=\"ajout_champ('de', '".$eleve->getIdEleve()."', 'non_present', '".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."', 'debut');return false;\">De</a>";
                echo "</td>\n";

                echo "<td id='cell_ajout_".$eleve->getIdEleve()."_non_present_a'>\n";
                //$chaine_tmp=$eleve->getIdEleve()."_non_present";
                echo "<a href='#' onclick=\"ajout_champ('a', '".$eleve->getIdEleve()."', 'non_present', '".$creneau_fin->getHeurefinDefiniePeriode("%H:%M")."', 'fin');return false;\">à</a>";
                echo "</td>\n";

                echo "<td id='cell_ajout_".$eleve->getIdEleve()."_non_present_c'>\n";
                //$chaine_tmp=$eleve->getIdEleve()."_non_present";
                echo "<a href='#' onclick=\"ajout_champ('c', '".$eleve->getIdEleve()."', 'non_present', '', 'commentaire');return false;\">Txt</a>";
                echo "</td>\n";

                echo "</tr>\n";

                echo "<tr><td colspan='5'>Ou plus précisemment:</td></tr>\n";

                $altb=1;
                foreach ($liste_a_types as $a_type) {
                    if(in_array($a_type->getId(),$tab_a_types)) {
                        $sql="SELECT * FROM a_types_statut WHERE id_a_type='".$a_type->getId()."' AND statut='".$_SESSION['statut']."';";
                        $test_statut=mysql_query($sql);
                        if(mysql_num_rows($test_statut)>0) {

                            $altb=$altb*(-1);
                            echo "<tr class='lig$altb'>\n";
                            echo "<td>\n";
                            echo "<input type='radio' name='saisie_".$eleve->getIdEleve()."' id='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."' value='".$a_type->getId()."' onchange='maj_attente_validation(this, ".$eleve->getIdEleve().")' />\n";
                            echo "</td>\n";
                            echo "<td style='text-align:left;'>\n";
                            echo "<label for='saisie_".$eleve->getIdEleve()."_".$a_type->getId()."'> ".$a_type->getNom()."</label><br />\n";
                            echo "</td>\n";

                            echo "<td id='cell_ajout_".$eleve->getIdEleve()."_".$a_type->getId()."_de'>\n";
                            echo "<a href='#' onclick=\"ajout_champ('de', '".$eleve->getIdEleve()."', '".$a_type->getId()."', '".$creneau_debut->getHeuredebutDefiniePeriode("%H:%M")."', 'debut');return false;\">De</a>";
                            echo "</td>\n";

                            echo "<td id='cell_ajout_".$eleve->getIdEleve()."_".$a_type->getId()."_a'>\n";
                            echo "<a href='#' onclick=\"ajout_champ('a', '".$eleve->getIdEleve()."', '".$a_type->getId()."', '".$creneau_fin->getHeurefinDefiniePeriode("%H:%M")."', 'fin');return false;\">à</a>";
                            echo "</td>\n";

                            echo "<td id='cell_ajout_".$eleve->getIdEleve()."_".$a_type->getId()."_c'>\n";
                            echo "<a href='#' onclick=\"ajout_champ('c', '".$eleve->getIdEleve()."', '".$a_type->getId()."', '', 'commentaire');return false;\">Txt</a>";
                            echo "</td>\n";

                            echo "</tr>\n";
                        }
                    }
                }
                echo "</table>\n";
                echo "</div>\n";
                echo "</div>\n";

            }

            // Remplacer les $id_debut_creneau et $id_fin_creneau:
            // $id_debut_creneau par l'heure de début du créneau choisi
            // $id_fin_creneau par l'heure courante... pour un retard, mais heure de fin de créneau pour une absence
            echo "</td>\n";

            echo "<td id='cell_attente_valid_".$eleve->getIdEleve()."'>\n";


            // Récupérer les infos déjà saisies pour affichage dans les cellules qui suivent
            $tab_saisies_prec=array();
            $sql="SELECT * FROM a_saisies WHERE eleve_id='".$eleve->getIdEleve()."' AND created_at LIKE '$date_jour_mysql %';";
            //echo "$sql<br />";
            $res_saisies_precedentes=mysql_query($sql);
            if(mysql_num_rows($res_saisies_precedentes)>0) {
                while($lig_sp=mysql_fetch_object($res_saisies_precedentes)) {
                    if(!isset($tab_saisies_prec[$lig_sp->id_edt_creneau])) {
                        $tab_saisies_prec[$lig_sp->id_edt_creneau]="";
                    }
                    else {$tab_saisies_prec[$lig_sp->id_edt_creneau].=", ";}
                    $tab_saisies_prec[$lig_sp->id_edt_creneau].="";
                    $sql="SELECT atr.*, at.nom, at.id as id_a_type FROM a_traitements atr, j_traitements_saisies jts, a_types at WHERE jts.a_saisie_id='".$lig_sp->id."' AND jts.a_traitement_id=atr.id AND atr.a_type_id=at.id;";
                    //echo "$sql<br />";
                    $res_tt=mysql_query($sql);
                    if(mysql_num_rows($res_tt)>0) {
                        while($lig_tt=mysql_fetch_object($res_tt)) {
                            $tab_saisies_prec[$lig_sp->id_edt_creneau].="<span style='color:".$tab_couleur_type_abs[$lig_tt->id_a_type]."' title='".$lig_tt->nom;
                            if(($lig_sp->debut_abs!='')&&($lig_sp->fin_abs!='')) {$tab_saisies_prec[$lig_sp->id_edt_creneau].=" (".formate_time_mysql_to_heure_min($lig_sp->debut_abs)."-".formate_time_mysql_to_heure_min($lig_sp->fin_abs).")";}
                            elseif($lig_sp->debut_abs!='') {$tab_saisies_prec[$lig_sp->id_edt_creneau].=" (".formate_time_mysql_to_heure_min($lig_sp->debut_abs).")";}
                            elseif($lig_sp->fin_abs!='') {$tab_saisies_prec[$lig_sp->id_edt_creneau].=" (".formate_time_mysql_to_heure_min($lig_sp->fin_abs).")";}

                            if($lig_sp->commentaire!='') {
                                $tab_saisies_prec[$lig_sp->id_edt_creneau].=" (".$lig_sp->commentaire.")";
                            }

                            $tab_saisies_prec[$lig_sp->id_edt_creneau].="'>".substr($lig_tt->nom,0,1)."</span>";

                                                    }
                    }
                    else {
                        $tab_saisies_prec[$lig_sp->id_edt_creneau].="<span style='color:black;' title='Absence non typée";
                        if($lig_sp->commentaire!='') {
                            $tab_saisies_prec[$lig_sp->id_edt_creneau].=" (".$lig_sp->commentaire.")";
                        }
                        $tab_saisies_prec[$lig_sp->id_edt_creneau].="'>Abs</span>";
                    }

                    // Faut-il aussi stocker le nom du cours/groupe et du prof?
                }
            }

            echo "</td>\n";


            foreach($creneaux_objects as $creneau) {
                // Afficher les infos déjà saisies
                echo "<td>";
                //echo $jour."_";
                if(isset($tab_saisies_prec[$creneau->getIdDefiniePeriode()])) {
                    echo $tab_saisies_prec[$creneau->getIdDefiniePeriode()];
                }
                echo "</td>\n";
            }

            // Photo
            echo "<td>";
                // A DEPLACER: pour mettre par la suite à la place des colonnes de tableau
                $tab_data = ConstruireEDTEleveDuJour($eleve->getLogin(), 0, $jour);
                $flags = NO_INFOBULLE + HORIZONTAL + CRENEAUX_INVISIBLES;
                echo EdtDuJour($tab_data, $jour, $flags);
            echo "</td>\n";
            echo "</tr>\n";
        }

        echo "</table>\n";

        // Ajouter Javascript pour montrer l'état actuel des saisies avant validation du formulaire.
        // Pouvoir modifier une saisie après validation (si aucun traitement encore effectué?) en plus d'en effectuer une autre.

        // Pour rouvrir l'onglet de saisie des absences dans index.php après validation
        echo "<input type='hidden' name='onglet_abs' value='saisie_abs' />\n";

        // Créneau(x)
        echo "<input type='hidden' name='id_fin_creneau' value='$id_fin_creneau' />\n";
        echo "<input type='hidden' name='id_debut_creneau' value='$id_debut_creneau' />\n";
        echo "<input type='hidden' name='jour' value='$jour' />\n";

        if(isset($id_cours)) {
            echo "<input type='hidden' name='id_cours' value='$id_cours' />\n";
        }

        echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";

        // Temoin pour provoquer dans index.php le traitement des données envoyées
        echo "<input type='hidden' name='enregistrer_abs' value='y' />\n";

        echo "Mode de saisie&nbsp;: <select name='mode_saisie_abs'>
<option value='2'>2</option>
<option value='1'>1</option>
<option value='0'>0</option>
</select> (<i>essais de modes de saisie (le mode 2 ne fonctionne pas sans JavaScript)</i>)<br />\n";

        echo "<input type='submit' value='Enregistrer les absences et retards' />\n";

        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Pour enregistrer le créneau dans l'EDT:
        if(!isset($id_cours)) {
            // On propose d'enregistrer le cours dans l'EDT
            echo "<p><input type='checkbox' name='enregistrer_cours_dans_edt' id='enregistrer_cours_dans_edt' value='y' onchange='display_params_reg_edt()' /><label for='enregistrer_cours_dans_edt'> Enregistrer le cours dans l'EDT</label>\n";

            echo "<div id='div_param_reg_edt'>\n";
            echo "<input type='hidden' name='ch_jour_semaine' value='".$tab_jour[$jour]."' />\n";
            echo "<input type='hidden' name='ch_heure' value='$id_debut_creneau' />\n";

            echo "<table class='boireaus' summary='Paramètres EDT'>\n";
            $alt=1;
            echo "<tr class='lig$alt'><td colspan='2'>\n";
            echo "<select name='heure_debut'>\n";
            echo "<option value='0'>".LESSON_START_AT_THE_BEGINNING."</option>\n";
            echo "<option value='0.5'>".LESSON_START_AT_THE_MIDDLE."</option>\n";
            echo "</select>\n";
            echo "</td></tr>\n";


            $alt=$alt*(-1);
            echo "<tr class='lig$alt'><td>\n";
            echo 'Durée du cours&nbsp;: ';
            echo "</td><td>\n";
            echo '<select name="duree">
    <option value="1">'.HOUR1.'</option>
    <option value="2" selected="selected">'.HOUR2.'</option>
    <option value="3">'.HOUR3.'</option>
    <option value="4">'.HOUR4.'</option>
    <option value="5">'.HOUR5.'</option>
    <option value="6">'.HOUR6.'</option>
    <option value="7">'.HOUR7.'</option>
    <option value="8">'.HOUR8.'</option>
    <option value="9">'.HOUR9.'</option>
    <option value="10">'.HOUR10.'</option>
    <option value="11">'.HOUR11.'</option>
    <option value="12">'.HOUR12.'</option>
    <option value="13">'.HOUR13.'</option>
    <option value="14">'.HOUR14.'</option>
    <option value="15">'.HOUR15.'</option>
    <option value="16">'.HOUR16.'</option>
</select>'."\n";
            echo "</td></tr>\n";

            $alt=$alt*(-1);
            echo "<tr class='lig$alt'><td>\n";
            echo 'Semaines&nbsp;: ';
            echo "</td><td>\n";
            echo '<select name="choix_semaine">
    <option value="0">'.ALL_WEEKS.'</option>'."\n";
            // on récupère les types de semaines
            $req_semaines = mysql_query("SELECT SQL_SMALL_RESULT DISTINCT type_edt_semaine FROM edt_semaines WHERE type_edt_semaine != '' LIMIT 5 ");
            $nbre_semaines = mysql_num_rows($req_semaines);
            for ($s=0; $s<$nbre_semaines; $s++) {
                $rep_semaines[$s]["type_edt_semaine"] = mysql_result($req_semaines, $s, "type_edt_semaine");
                echo '  <option value="'.$rep_semaines[$s]["type_edt_semaine"].'">Semaine '.$rep_semaines[$s]["type_edt_semaine"].'</option>'."\n";
            }
            echo '</select>'."\n";
            echo "</td></tr>\n";

            $alt=$alt*(-1);
            echo "<tr class='lig$alt'><td>\n";
            echo 'Salle&nbsp;: ';
            echo "</td><td>\n";
            echo '<select  name="login_salle">
    <option value="rien">'.CLASSROOM.'</option>'."\n";
            // Choix de la salle
            $tab_select_salle = renvoie_liste("salle");
            for($c=0;$c<count($tab_select_salle);$c++) {
                // On vérifie si le nom de la salle existe vraiment
                if ($tab_select_salle[$c]["nom_salle"] == "") {
                    $tab_select_salle[$c]["nom_salle"] = $tab_select_salle[$c]["numero_salle"];
                }
                echo "  <option value='".$tab_select_salle[$c]["id_salle"]."'>".$tab_select_salle[$c]["nom_salle"]."</option>\n";
            }
            echo '</select>'."\n";
            echo "</td></tr>\n";


            $req_calendrier = mysql_query("SELECT * FROM edt_calendrier WHERE etabferme_calendrier = '1' AND etabvacances_calendrier = '0'");
            $nbre_calendrier = mysql_num_rows($req_calendrier);
            if ($nbre_calendrier == 0) {
                echo '<input type="hidden" name="periode_calendrier" value="0" />'."\n";
            }
            else {
                $alt=$alt*(-1);
                echo "<tr class='lig$alt'><td>\n";
                echo 'Période&nbsp;: ';
                echo "</td><td>\n";
                echo '<select name="periode_calendrier">
    <option value="0">'.ENTIRE_YEAR.'</option>\n';
                // ================================================== Choix de la période définie dans le calendrier ================================
                $req_id_classe = mysql_query("SELECT id_classe FROM j_groupes_classes WHERE id_groupe = '".$id_groupe."' ");
                // ==== On récupère l'id de la classe concernée
                if ($rep_id_classe = mysql_fetch_array($req_id_classe)) {
                    $id_classe = $rep_id_classe['id_classe'];
                }
                else {
                    $id_classe = 0;
                }

                for ($a=0; $a<$nbre_calendrier; $a++) {
                    $rep_calendrier[$a]["id_calendrier"] = mysql_result($req_calendrier, $a, "id_calendrier");
                    $rep_calendrier[$a]["nom_calendrier"] = mysql_result($req_calendrier, $a, "nom_calendrier");
                    $rep_calendrier[$a]["classe_concerne_calendrier"] = mysql_result($req_calendrier, $a, "classe_concerne_calendrier");
                    $classes_concernes = explode(";", $rep_calendrier[$a]['classe_concerne_calendrier']);
                    if ((in_array($id_classe, $classes_concernes) AND ($id_classe != 0)) OR ($id_classe == 0)) {
                        echo '  <option value="'.$rep_calendrier[$a]["id_calendrier"].'">'.$rep_calendrier[$a]["nom_calendrier"].'</option>'."\n";
                    }
                    unset($classes_concernes);
                }
                echo '</select>'."\n";
                echo "</td></tr>\n";
            }
            echo "</table>\n";
            echo "</div>\n";

            echo "<script type='text/javascript'>
function display_params_reg_edt() {
    if(document.getElementById('enregistrer_cours_dans_edt').checked==true) {
        document.getElementById('div_param_reg_edt').style.display='block';
    }
    else {
        document.getElementById('div_param_reg_edt').style.display='none';
    }

    //alert('test');
}

display_params_reg_edt();
</script>";
        }
        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


        echo "<script type='text/javascript'>
// Pour ajouter/afficher des champs
function ajout_champ(suff_cell_id, id_eleve, a_type_id, valeur_champ, type) {
    nom_champ=type+'_'+id_eleve+'_'+a_type_id;
    cell_id='cell_ajout_'+id_eleve+'_'+a_type_id+'_'+suff_cell_id;

    document.getElementById('saisie_'+id_eleve+'_'+a_type_id).checked=true;

    if(type=='debut') {
        document.getElementById(cell_id).innerHTML=\" de&nbsp;:<input type='text' name='\"+nom_champ+\"' id='\"+nom_champ+\"' value='\"+valeur_champ+\"' size='6' />\";
    }

    if(type=='fin') {
        document.getElementById(cell_id).innerHTML=\" à&nbsp;:<input type='text' name='\"+nom_champ+\"' id='\"+nom_champ+\"' value='\"+valeur_champ+\"' size='6' />\";
    }

    if(type=='commentaire') {
        document.getElementById(cell_id).innerHTML=\" <textarea cols=10 rows=1 name='\"+nom_champ+\"' id='\"+nom_champ+\"'></textarea>\";
    }

    maj_attente_validation(document.getElementById('saisie_'+id_eleve+'_'+a_type_id), id_eleve);

    document.getElementById(nom_champ).focus();
}
</script>";

        echo "</form>\n";

        //echo "<p style='color:red'>A FAIRE: Mettre une case à cocher pour permettre de déplier un formulaire de paramètres pour enregistrer le cours dans edt_cours.</p>";

    }

    echo "<p><span style='color:red'>Et si l'EDT n'est pas actif? Mettre les créneaux M1, M2,... dans un tableau? ou faut-il quand même utiliser les fonctions EDT... ça me parait nécessaire.</span><br />";
    echo "<p><span style='color:red'>Les infobulles à ma sauce ne vont pas fonctionner sans récupérer une portion de code de lib/footer.inc.php: C'est fait avec index2.php et saisie_abs2b.php</span><br />";
    echo "<p><span style='color:red'>Si on désactive javascript, la navigation dans les onglets échoue.<br />Je serais partisan de remplacer cela par autre chose: c'est fait avec index2.php et saisie_abs2b.php<br />Il faudrait en revanche pouvoir passer d'un mode_saisie_abs à un autre si javascript est désactivé...</span><br />";
?>
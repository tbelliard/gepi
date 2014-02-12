
<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

require_once 'GepiTestBase.php';
require_once 'GepiDataPopulator.php';

/**
 * Base class contains some methods shared by subclass test cases.
 */
abstract class GepiEmptyTestBase extends GepiTestBase
{
    /**
     * This is run before each unit test; it empties the database.
     */
    protected function setUp()
    {
        GepiDataPopulator::depopulate($this->con);
        mysqli_query($GLOBALS["mysqli"], 'delete from setting');
        mysqli_query($GLOBALS["mysqli"], 'delete from droits');
        mysqli_query($GLOBALS["mysqli"], 'delete from droits_aid');
        mysqli_query($GLOBALS["mysqli"], 'delete from aid_productions');
        mysqli_query($GLOBALS["mysqli"], 'delete from edt_setting');
        mysqli_query($GLOBALS["mysqli"], 'delete from lettres_tcs');
        mysqli_query($GLOBALS["mysqli"], 'delete from etiquettes_formats');
        mysqli_query($GLOBALS["mysqli"], 'delete from lettres_types');
        mysqli_query($GLOBALS["mysqli"], 'delete from lettres_cadres');
        mysqli_query($GLOBALS["mysqli"], 'delete from ct_types_documents');
        mysqli_query($GLOBALS["mysqli"], 'delete from absences_motifs');
        mysqli_query($GLOBALS["mysqli"], 'delete from model_bulletin');
        mysqli_query($GLOBALS["mysqli"], 'delete from absences_actions');
        $fd = fopen(dirname(__FILE__) ."/../../../../sql/data_gepi.sql", "r");
        if (!$fd) {
            echo "Erreur : fichier sql/data_gepi.sql non trouve\n";
            die;
        }
        while (!feof($fd)) {
            $query = fgets($fd, 5000);
            $query = trim($query);
            if((substr($query,-1)==";")&&(substr($query,0,3)!="-- ")) {
                $reg = mysqli_query($GLOBALS["mysqli"], $query);
                if (!$reg) {
                    echo "ERROR : '$query' \n";
                    echo "Erreur retournée : ".mysqli_error($GLOBALS["mysqli"])."\n";
                    $result_ok = 'no';
                }
            }
        }
        fclose($fd);
         
        loadSettings();
        
        AbsenceEleveSaisiePeer::disableAgregation();
        AbsenceEleveTraitementPeer::disableAgregation();
        
        parent::setUp();
    }

}

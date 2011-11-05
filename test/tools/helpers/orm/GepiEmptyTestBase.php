
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
        mysql_query('delete from setting');
        mysql_query('delete from droits');
        mysql_query('delete from droits_aid');
        mysql_query('delete from aid_productions');
        mysql_query('delete from edt_setting');
        mysql_query('delete from lettres_tcs');
        mysql_query('delete from etiquettes_formats');
        mysql_query('delete from lettres_types');
        mysql_query('delete from lettres_cadres');
        mysql_query('delete from ct_types_documents');
        mysql_query('delete from absences_motifs');
        mysql_query('delete from model_bulletin');
        mysql_query('delete from absences_actions');
        $fd = fopen(dirname(__FILE__) ."/../../../../sql/data_gepi.sql", "r");
        if (!$fd) {
            echo "Erreur : fichier sql/data_gepi.sql non trouve\n";
            die;
        }
        while (!feof($fd)) {
            $query = fgets($fd, 5000);
            $query = trim($query);
            if((substr($query,-1)==";")&&(substr($query,0,3)!="-- ")) {
                $reg = mysql_query($query);
                if (!$reg) {
                    echo "ERROR : '$query' \n";
                    echo "Erreur retournée : ".mysql_error()."\n";
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

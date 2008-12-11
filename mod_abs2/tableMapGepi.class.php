<?php
/**
 * @version $Id$
 *
 * @copyright 2008
 *
 */

/**
 * Classe qui permet de dire à Gepi les liens qui existent entre plusieurs tables
 * Il faut préciser le champ de la table1 qui fait référence à la table2, puis table3,...
 *
 * @author Julien Jocal
 */
class tableMapGepi {
  /**
   * Propriété de l'objet qui stocke les différentes tables sous la forme d'un tableau php
   * exemple : $_tables = array(table2', 'table3');
   * où table2 et table3 sont des tables liées à une autre par un champ précis (avec ou sans contrainte de clé étrangère)
   *
   * @access protected
   * @property array $_tablesfk
   */
    protected $_tablesfk;

  /**
   * Propriété de l'objet qui stocke la clé étrangère (même si elle n'est pas définie comme telle dans la table)
   * exemple : $_fk[] = array('champ_table1_vers_table2', 'champ_table2');
   * $_fk[] = array('champ_table1_vers_table3', 'champ_table3');
   * Si la base est construite sous la forme champ id_utilisateurs vers la table utilisateurs
   * Cette propriété est remplie automatiquement par la méthode setFk.
   *
   * @access protected
   * @property array $_fk
   */
    protected $_fk;

  /**
   *¨Propriété de l'objet qui définit la table principale qui est liée avec $_tables et $_fk
   * Permet ensuite de construire dynamiquement les requêtes complètes
   *
   * @access protected
   * @property string $_table
   */
    protected $_table;

    /**
     * Propriétés pour garder quelque part le from, le where, order_by et le limit de la requête
     *
     * @access private
     */
    private $_from = NULL;
    private $_where = NULL;
    private $_order_by = NULL;
    private $_limit = NULL;


    /**
     * Constructeur
     * @param string $construct_table
     * @param array $construct_tablesfk
     * @param array $construct_fk
     */

    public function __construct($construct_table, array $construct_tablesfk , array $construct_fk){
        if (!is_string($construct_table) OR !is_array($construct_tablesfk) OR !is_array($construct_fk)){
            throw new Exception('Un des paramètres de construction de ' . __CLASS__ . ' n\'est pas conforme.');
        }else{
            $this->_table       = $construct_table;
            $this->_tablesfk    = $construct_tablesfk;
            $this->_fk          = $construct_fk;

            $this->constructFrom();
            $this->constructWhere();
        }
    }

    /**
     * Méthode qui renvoit la requête entière
     *
     * @access public
     */
    public function returnRequest(array $test_id = NULL){
        if (isset($test_id)){
            $this->constructWhere($test_id);
        }
        return 'SELECT * ' . $this->_from . $this->_where . $this->_order_by . $this->_limit;
    }

    public function addClauses($limit = NULL, array $_ordre = NULL){
        $this->constructLimit($limit);
        $this->constructOrderBy($_ordre);
    }

    /**
     * Méthode qui permet de construire le from de la requête
     *
     * @access private
     * @return void
     */
    private function constructFrom(){
        $from  = array();
        $from[] = $this->_table;
        $nbre = count($this->_tablesfk);
        $i = 0;
        for($i = 0 ; $i < $nbre ; $i++){
            $from[] = $this->_tablesfk[$i];
        }
        $this->_from = ' FROM ' . join(", ", $from);
    }

    /**
     * Méthode qui permet de construire la clause where en tenant compte de toutes les informations
     * 
     * @access private
     * @return void
     */
    private function constructWhere(array $test_id = NULL){

        $clause_where = array ();

        $nbre = count($this->_tablesfk);
        for($a = 0 ; $a < $nbre ; $a++){
            // Si la cle de jointure n'est pas précisée pour la table origine
            // alors elle est de la forme id_nomdelatableausingulier
            $_cle_1 = isset($this->_fk[$a][0]) ? $this->_fk[$a][0] : 'id_' . substr($this->_tablesfk[$a], 0, -1);
            // Si la clé de la table appelée n'est pas précisée, alors c'est id
            $_cle_2 = isset($this->_fk[$a][1]) ? $this->_fk[$a][1] : 'id';

            $clause_where[] = $this->_table . '.' . $_cle_1 . '=' . $this->_tablesfk[$a] . '.' . $_cle_2;

        }

        // Ici, on ajoute la demande précise de la requête
        // $test_id[0] est le champ de la table et $test-id[1] est sa valeur recherchée.
        if (isset($test_id) AND is_array($test_id)){
            $clause_where[] = $this->_table . '.' . $test_id[0] . ' = ' . stripcslashes($test_id[1]);
        }else{
            $clause_where[] = 'id.' . $this->_table . '= *';
        }

        $this->_where = ' WHERE ' . join(" AND ", $clause_where);
    }

    /**
     * Méthode qui permet de construire la clause order_by de la requête MySql
     * TODO Il manque la possibilité d'ajouter ASC/DESC
     *
     * @param array $_ordre
     * @return void
     */

    private function constructOrderBy(array $_ordre = NULL){
        if (!isset($_ordre)){
        }else{
            $this->_order_by = ' ORDER BY ' . join(', ', $_ordre);
        }
    }

    /**
     * Méthode qui renvoie le LIMIT de la requête
     *
     * @param integer $limite
     * @return string
     */
    private function constructLimit($limite = NULL){
        if (isset($limite) AND is_numeric($limite)){
            $this->_limit = ' LIMIT ' . $limite;
        }else{
        }
    }

}

/* ========= TESTS ==============

$tables = array('groupes', 'salle_cours');
$cles = array('', array('id_salle', 'id_salle'));
$test = new tableMapGepi('edt_cours', $tables, $cles);
$test->addClauses('1', array('id_definie_periode')); // On ajoute des éléments à la requête
try{
       include('lib/erreurs.php');
    if ($cnx = new PDO('mysql:host=localhost;dbname=gepi', 'root', 'matteo')){

    }else{
        throw new Exception('Impossible d\'ouvrir une connexion avec Mysql');
    }
    echo $test->returnRequest(array('id_groupe', '1')) . '<br />Le résultat est : <br />';
    if ($_query = $cnx->query($test->returnRequest(array('id_groupe', '1')))){
    //if ($_query = $cnx->query("SELECT * FROM edt_cours, groupes, salle_cours WHERE edt_cours.id_groupe=groupes.id AND edt_cours.id_salle=salle_cours.id_salle AND edt_cours.id_groupe = 1 ORDER BY id_definie_periode DESC")){
        // rien
    }else{
        throw new Exception("La requête ne doit pas être bonne || " . $test->returnRequest(array('id_groupe', '1')));
    }

    $testeur = $_query->fetchAll(PDO::FETCH_OBJ);
    echo '<pre>';
    print_r($testeur);
    echo '</pre>';
    
}catch (Exception $e){
    echo '<pre>';
    print_r($e);
    echo '</pre>';
    affExceptions($e);
}
*/
?>

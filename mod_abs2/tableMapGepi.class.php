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
   * où table1 est la table principale liée aux deux autres par le champs de $_fk;
   *
   * @access protected
   * @property array $_tables
   */
  protected $_tables;

  /**
   * Propriété de l'objet qui stocke la clé étrangère (même si elle n'est pas définie comme telle dans la table)
   * exemple : $_fk[] = array('champ_table1_vers_table2', 'champ_table2');
   * $_fk = array('champ_table1_vers_table3', 'champ_table3');
   * Si la bas est construite sous la forme champ id_utilisateurs vers la table utilisateurs
   * Cette propriété est remplie automatiquement par la méthode setFk.
   *
   * @access protected
   * @property array $_fk
   */
  protected $_fk;

  /**
   *¨Propriété de l'objet qui définit la table principale qui est liée avec $_tables et $_fk
   * Permet ensuite de construire dynamiquement les
   */

    public function __construct(){
        // Constructeur de la classe
    }

    /**
     * Méthode qui permet de 
     * 
     * 
     */
    protected function setFk(){

    }

}
?>

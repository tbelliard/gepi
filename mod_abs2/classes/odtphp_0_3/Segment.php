<?php
require 'SegmentIterator.php';
class SegmentException extends Exception
{}
/**
 * Classe de gestion des segments de templating pour fichier odt
 * You need PHP 5.2 at least
 * You need Zip Extension
 * Encoding : ISO-8859-1
 * Last commit by $Author: obooklage $
 * Date - $Date: 2009-02-15 12:01:37 +0100 $
 * SVN Revision - $Rev: 16 $
 * Id : $Id$
 *
 * @copyright  GPL License 2008 - Julien Pauli - Cyril PIERRE de GEYER - Anaska (http://www.anaska.com)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL License
 * @version 1.3
 */
class Segment implements IteratorAggregate, Countable
{
    const DELIMITER_LEFT = '{';
    const DELIMITER_RIGHT = '}';
    protected $xml;
    protected $xmlParsed = '';
    protected $name;
    protected $key = 0;
    protected $children = array();
    protected $vars = array();
    /**
     * Constructeur
     *
     * @param string $name nom du segment à construire
     * @param string $xml structure xml du segment
     */
    public function __construct($name, $xml)
    {
        $this->name = (string) $name;
        $this->xml = (string) $xml;
        $this->_analyseChildren($this->xml);
    }
    /**
     * Retourne le nom du segment
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Le segment a-t-il des enfants ?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->getIterator()->hasChildren();
    }
    /**
     * Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }
    /**
     * IteratorAggregate interface
     *
     * @return Iterator
     */
    public function getIterator()
    {
        return new RecursiveIteratorIterator(new SegmentIterator($this->children), 1);
    }
    /**
     * Remplace les variables de template dans le XML,
     * tous les enfants sont aussi appelés
     *
     * @return string
     */
    public function merge()
    {
        $this->xmlParsed .= str_replace(array_keys($this->vars), array_values($this->vars), $this->xml);
        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                $this->xmlParsed = str_replace($child->xml, ($child->xmlParsed=="")?$child->merge():$child->xmlParsed, $this->xmlParsed);
                $child->xmlParsed = '';
            }
        }
        $reg = "/\[!--\sBEGIN\s$this->name\s--\](.*)\[!--\sEND\s$this->name\s--\]/sm";
        $this->xmlParsed = preg_replace($reg, '$1', $this->xmlParsed);
        return $this->xmlParsed;
    }
    /**
     * Analyse le xml pour trouver des enfants
     *
     * @param string $xml
     * @return Segment
     */
    protected function _analyseChildren($xml)
    {
        $reg2 = "#\[!--\sBEGIN\s([\S]*)\s--\](?:<\/text:p>)?(.*)(?:<text:p\s.*>)?\[!--\sEND\s(\\1)\s--\]#sm";
        if (preg_match($reg2, $xml, $n) !== 0) {
            if ($n[1] != $this->name) {
                $this->children[$n[1]] = new self($n[1], $n[0]);
            } else {
                $this->_analyseChildren($n[2]);
            }
        }
        return $this;
    }
    /**
     * Affecte une variable de template à remplacer
     *
     * @param string $key
     * @param string $value
     * @throws SegmentException
     * @return Segment
     */
    public function setVar($key, $value)
    {
        if (strpos($this->xml, self::DELIMITER_LEFT . $key . self::DELIMITER_RIGHT) === false) {
            throw new SegmentException("var $key not found in {$this->getName()}");
        }
        $this->vars[self::DELIMITER_LEFT . $key . self::DELIMITER_RIGHT] = utf8_encode(htmlspecialchars($value));
        return $this;
    }
    /**
     * Raccourci pour récupérer un enfant
     *
     * @param string $prop
     * @return Segment
     * @throws SegmentException
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->children)) {
            return $this->children[$prop];
        } else {
            throw new SegmentException('child ' . $prop . ' does not exist');
        }
    }
    /**
     * Proxy vers setVar
     *
     * @param string $meth
     * @param array $args
     * @return Segment
     */
    public function __call($meth, $args)
    {
        try {
            return $this->setVar($meth, $args[0]);
        } catch (SegmentException $e) {
            throw new SegmentException("method $meth nor var $meth exist");
        }
    }
    /**
     * Retourne le XML parsé
     *
     * @return string
     */
    public function getXmlParsed()
    {
        return $this->xmlParsed;
    }
}

?>
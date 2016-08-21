<?php
require 'Segment.php';
class OdfException extends Exception
{}
/**
 * Templating class for odt file
 * You need PHP 5.2 at least
 * You need Zip Extension
 *
 * @copyright  GPL License 2008 - Julien Pauli - Cyril PIERRE de GEYER - Anaska (http://www.anaska.com)
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL License
 * @version 1.3
 */
class Odf
{
    const DELIMITER_LEFT = '{';
    const DELIMITER_RIGHT = '}';
    const PIXEL_TO_CM = 0.026458333;
    private $file;
    private $contentXml;
    private $tmpfile;
    private $images = array();
    private $vars = array();
    private $segments = array();
    /**
     * Constructeur de classe
     *
     * @param string $filename nom du fichier odt
     * @throws OdfException
     */
    public function __construct($filename)
    {
        if (! class_exists('ZipArchive')) {
            throw new OdfException('Zip extension not loaded - check your php settings, PHP5.2 minimum with zip extension
			 is required');
        }
        $this->file = new ZipArchive();
        if ($this->file->open($filename) !== true) {
            throw new OdfException("Error while Opening the file '$filename' - Check your odt file");
        }
        if (($this->contentXml = $this->file->getFromName('content.xml')) === false) {
            throw new OdfException("Nothing to parse - check that the content.xml file is correctly formed");
        }
        $tmp = tempnam(null, md5(uniqid())) . '.odt';
        copy($filename, $tmp);
        $this->tmpfile = $tmp;
    }
    /**
     * Affecte une variable de template
     *
     * @param string $key nom de la variable dans le template
     * @param string $value valeur de remplacement
     * @param bool $encode si true, les caractères spéciaux XML seront encodés
     * @throws OdfException
     * @return odf
     */
    public function setVars($key, $value, $encode = true)
    {
        if (strpos($this->contentXml, self::DELIMITER_LEFT . $key . self::DELIMITER_RIGHT) === false) {
            throw new OdfException("var $key not found in the document");
        }
        $value = $encode ? utf8_encode(htmlspecialchars($value)) : utf8_encode($value);
        $this->vars[self::DELIMITER_LEFT . $key . self::DELIMITER_RIGHT] = $value;
        return $this;
    }
    /**
     * Affecte une variable de template en tant qu'image
     *
     * @param string $key nom de la variable dans le template
     * @param string $value chemin vers une image
     * @throws OdfException
     * @return odf
     */
    public function setImage($key, $value)
    {
        $filename = strtok(strrchr($value, '/'), '/.');
        $file = mb_substr(strrchr($value, '/'), 1);
        $size = @getimagesize($value);
        if ($size === false) {
            throw new OdfException("Invalid image");
        }
        list ($width, $height) = $size;
        $width *= self::PIXEL_TO_CM;
        $height *= self::PIXEL_TO_CM;
        $xml = <<<IMG
<draw:frame draw:style-name="fr1" draw:name="$filename" text:anchor-type="char" svg:width="{$width}cm" svg:height="{$height}cm" draw:z-index="3"><draw:image xlink:href="Pictures/$file" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/></draw:frame>
IMG;
        $this->images[$value] = $file;
        $this->setVars($key, $xml, false);
        return $this;
    }
    /**
     * Fusionne les variables de template
     * Appelée automatiquement lors d'une sauvegarde
     *
     * @return void
     */
    private function _parse()
    {
        $this->contentXml = str_replace(array_keys($this->vars), array_values($this->vars), $this->contentXml);
    }
    /**
     * Rajoute le segment fusionné au document
     *
     * @param Segment $segment
     * @throws OdfException
     * @return odf
     */
    public function mergeSegment(Segment $segment)
    {
        if (! array_key_exists($segment->getName(), $this->segments)) {
            throw new OdfException($segment->getName() . 'cannot be parsed, has it been set yet ?');
        }
        $string = $segment->getName();
        $reg = '@<text:p[^>]*>\[!--\sBEGIN\s' . $string . '\s--\](.*)\[!--.+END\s' . $string . '\s--\]<\/text:p>@smU';
        $this->contentXml = preg_replace($reg, $segment->getXmlParsed(), $this->contentXml);
        return $this;
    }
    /**
     * Affiche toutes les variables de templates actuelles
     * 
     * @return string
     */
    public function printVars()
    {
        return print_r('<pre>' . print_r($this->vars, true) . '</pre>', true);
    }
    /**
     * Affiche le fichier de contenu xml du document odt
     * tel qu'il est à cet instant
     *
     * @return string
     */
    public function __toString()
    {
        return $this->contentXml;
    }
    /**
     * Affiche les segments de boucles déclarés avec setSegment()
     * 
     * @return string
     */
    public function printDeclaredSegments()
    {
        return '<pre>' . print_r(implode(' ', array_keys($this->segments)), true) . '</pre>';
    }
    /**
     * Déclare un segment pour une utilisation en boucle
     *
     * @param string $segment
     * @throws OdfException
     * @return Segment
     */
    public function setSegment($segment)
    {
        if (array_key_exists($segment, $this->segments)) {
            return $this->segments[$segment];
        }
        $reg = "#\[!--\sBEGIN\s$segment\s--\]<\/text:p>(.*)<text:p\s.*>\[!--\sEND\s$segment\s--\]#sm";
        if (preg_match($reg, html_entity_decode($this->contentXml), $m) == 0) {
            throw new OdfException("'$segment' segment not found in the document");
        }
        $this->segments[$segment] = new Segment($segment, $m[1]);
        return $this->segments[$segment];
    }
    /**
     * Sauvegarde le fichier odt sur le disque
     * 
     * @param string $file nom du fichier désiré
     * @return void
     */
    public function saveToDisk($file = null)
    {
        if ($file !== null && is_string($file)) {
            $this->file->open($this->tmpfile, ZIPARCHIVE::CREATE);
            $this->_save();
            copy($this->tmpfile, $file);
        } else {
            $this->_save();
        }
    }
    /**
     * Sauvegarde interne
     *
     * @throws OdfException
     * @return void
     */
    private function _save()
    {
        $this->_parse();
        if (! $this->file->addFromString('content.xml', $this->contentXml)) {
            throw new OdfException('Error during file export');
        }
        foreach ($this->images as $imageKey => $imageValue) {
            $this->file->addFile($imageKey, 'Pictures/' . $imageValue);
        }
        $this->file->close(); // seems to bug on windows CLI sometimes
    }
    /**
     * Exporte le fichier en fichier attaché via HTTP
     *
     * @throws OdfException
     * @return void
     */
    public function exportAsAttachedFile()
    {
        $this->file->open($this->tmpfile, ZIPARCHIVE::CREATE);
        $this->_save();
        if (headers_sent($filename, $linenum)) {
            throw new OdfException("headers already sent ($filename at $linenum)");
        }
        header('Content-type: multipart/x-zip');
        header("Content-Disposition: attachment; filename=" . md5(uniqid()) . ".odt");
        readfile($this->tmpfile);
    }
}
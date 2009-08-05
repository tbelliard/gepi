<?php
/*
********************************************************
TinyButStrongOOo (extend TinyButStrong to process OOo doc)
Author   : Olivier LOYNET (tbsooo@free.fr)
Version  : 0.7.9
Require  : PHP >= 4.0.6 and TBS >= 2.0.4
Date     : 2006-06-12
Web site : www.tinybutstrong.com
Doc      : http://www.tinybutstrong.com/apps/tbsooo/doc.html
Forum    : http://www.tinybutstrong.com/forum.php - see "TBS with OpenOffice"
Download : http://www.tinybutstrong.com/download/download.php?file=tbsooo.zip
********************************************************
Released under the GNU LGPL license
http://www.gnu.org/copyleft/lesser.html
********************************************************
Modification Eric ABGRALL pour Gepi :
Classe modifiée afin d'utiliser la classe PCLZIP au lieu de Zip et Unzip
Elagage de certaines méthodes (gestion du cache pour la suppression)
*/

class clsTinyButStrongOOo extends clsTinyButStrong
{
  // private properties

  var $_process_path = 'tmp/';
  var $_zip_bin = '';
  var $_unzip_bin = '';
  var $_charset = '';
  var $_ooo_basename = '';
  var $_ooo_file_ext = '';
  var $_xml_filename = '';

  var $archive = null;

  // public method

  function SetProcessDir($process_path)
  {

    // set the directory for processing temporary OOo files
    if ($process_path == '') {
      $this->meth_Misc_Alert('SetProcessDir method', 'Parameter is empty');
      return false;
    }
    // add a trailing / at the path
    $this->_process_path = $process_path.(substr($process_path, -1, 1) == '/' ? '' : '/');
    
    // test if 'dir' exists
    if (!is_dir($this->_process_path)) {
      $this->meth_Misc_Alert('SetProcessDir method', 'Directory not found : '.$this->_process_path);
      return false;
    }

    // test if 'dir' is writable
    if (!is_writable($this->_process_path)) {
      $this->meth_Misc_Alert('SetProcessDir method', 'Directory not writable : '.$this->_process_path);
      return false;
    }
    return true;
  }

  function SetDataCharset($charset)
  {
    $this->_charset = strtoupper($charset);
  }

  function NewDocFromTpl($ooo_template_filename)
  {
    // test if OOo source file exist
    if (!file_exists($ooo_template_filename)) {
      $this->meth_Misc_Alert('NewDocFromTpl method', 'File not found : '.$ooo_template_filename);
      return false;
    }
	
    // create unique ID
    $unique = md5(microtime());
    // find path, file and extension
    $a_pathinfo = pathinfo($ooo_template_filename);
    $this->_ooo_file_ext  = $a_pathinfo['extension'];
    $this->_ooo_basename = $this->_process_path.$unique;

    // create unique temporary basename dir
    if (!mkdir($this->_ooo_basename, 0777)) {
      $this->meth_Misc_Alert('NewDocFromTpl method', 'Can\'t create directory : '.$this->_ooo_basename);
      return false;
    }

    // copy the ooo template into the temporary basename dir
    if (!copy($ooo_template_filename, $this->_ooo_basename.'.'.$this->_ooo_file_ext)) {
      $this->meth_Misc_Alert('NewDocFromTpl method', 'Can\'t copy file to process dir : '.$ooo_template_filename);
      return false;
    }
	
    return $this->_ooo_basename.'.'.$this->_ooo_file_ext;
  }

  function LoadXmlFromDoc($xml_file)
  {
    
	$this->_xml_filename = $xml_file; //Le nom du fichier xml
	$this->_nom_fic_archive = $this->_ooo_basename.'.'.$this->_ooo_file_ext; //le nom du fichier ODF (copie unique ID)
    
    // unzip the XML files
	//Eric
    $archive = new PclZip($this->_nom_fic_archive);
     if ($archive->extract(PCLZIP_OPT_BY_NAME, $this->_xml_filename,  //on extrait content.xml
	                       PCLZIP_OPT_PATH, $this->_ooo_basename) == 0) { //de l'archive dans le dossier archive (id unique)
      echo "ERROR : ".$archive->errorInfo(true);
     }
    
	// test if XML file exist
   if (!file_exists($this->_ooo_basename.'/'.$this->_xml_filename)) {
      $this->meth_Misc_Alert('LoadXmlFromDoc method', 'File not found : '.$this->_ooo_basename.'/'.$this->_xml_filename);
      return false;
    }

    // load the template
    $this->ObjectRef = &$this;
    $this->LoadTemplate($this->_ooo_basename.'/'.$this->_xml_filename, '=~_CharsetEncode');

    // work around - convert apostrophe in XML file needed for TBS functions
    $this->Source = str_replace('&apos;', '\'', $this->Source);

    // return
    return true;
  }

  function SaveXmlToDoc()
  {
    // get the source result
    $this->Show(TBS_NOTHING);

    // store the merge result in place of the XML source file
    $fdw = fopen($this->_ooo_basename.'/'.$this->_xml_filename, "w");
    fwrite($fdw, $this->Source, strlen($this->Source));
    fclose ($fdw);

    // test if XML file exist
    if (!file_exists($this->_ooo_basename.'/'.$this->_xml_filename)) {
      $this->meth_Misc_Alert('SaveXmlToDoc method', 'File not found : '.$this->_ooo_basename.'/'.$this->_xml_filename);
      return false;
    }

    // zip and remove the file
	//ERIC
    //exec($this->_zip_bin.' -j -m '.$this->_ooo_basename.'.'.$this->_ooo_file_ext.' '.$this->_ooo_basename.'/'.$this->_xml_filename);
	$this->_nom_fic_archive = $this->_ooo_basename.'.'.$this->_ooo_file_ext; //le nom du fichier ODF (copie unique ID)
	
	$archive = new PclZip($this->_ooo_basename.'.'.$this->_ooo_file_ext);
	// il faut supprimer le fichier dans l'archive. Un add n'écrase pas le fichier !!
	$v_list = $archive->delete(PCLZIP_OPT_BY_NAME, $this->_xml_filename);  //on supprime content.xml
	                       
    if ($v_list == 0) {
      die("Error : ".$archive->errorInfo(true));
	}
    $v_list = $archive->add($this->_ooo_basename.'/'.$this->_xml_filename, //on ajoute le fichier content.xml
	                        PCLZIP_OPT_ADD_PATH, '', //on remplace le chemin par rien
                            PCLZIP_OPT_REMOVE_PATH, $this->_ooo_basename ); //on ne met pas le dossier dans l'archive
    if ($v_list == 0) {
      die("Error : ".$archive->errorInfo(true));
    }
    return true;
  }

  function GetPathnameDoc()
  {
	//echo $this->_ooo_basename.'.'.$this->_ooo_file_ext;
	// return path
    return $this->_ooo_basename.'.'.$this->_ooo_file_ext;
  }

  function GetMimetypeDoc()
  {
    switch($this->_ooo_file_ext) {
      case 'sxw': return 'application/vnd.sun.xml.writer'; break;
      case 'stw': return 'application/vnd.sun.xml.writer.template'; break;
      case 'sxg': return 'application/vnd.sun.xml.writer.global'; break;
      case 'sxc': return 'application/vnd.sun.xml.calc'; break;
      case 'stc': return 'application/vnd.sun.xml.calc.template'; break;
      case 'sxi': return 'application/vnd.sun.xml.impress'; break;
      case 'sti': return 'application/vnd.sun.xml.impress.template'; break;
      case 'sxd': return 'application/vnd.sun.xml.draw'; break;
      case 'std': return 'application/vnd.sun.xml.draw.template'; break;
      case 'sxm': return 'application/vnd.sun.xml.math'; break;
      case 'odt': return 'application/vnd.oasis.opendocument.text'; break;
      case 'ott': return 'application/vnd.oasis.opendocument.text-template'; break;
      case 'oth': return 'application/vnd.oasis.opendocument.text-web'; break;
      case 'odm': return 'application/vnd.oasis.opendocument.text-master'; break;
      case 'odg': return 'application/vnd.oasis.opendocument.graphics'; break;
      case 'otg': return 'application/vnd.oasis.opendocument.graphics-template'; break;
      case 'odp': return 'application/vnd.oasis.opendocument.presentation'; break;
      case 'otp': return 'application/vnd.oasis.opendocument.presentation-template'; break;
      case 'ods': return 'application/vnd.oasis.opendocument.spreadsheet'; break;
      case 'ots': return 'application/vnd.oasis.opendocument.spreadsheet-template'; break;
      case 'odc': return 'application/vnd.oasis.opendocument.chart'; break;
      case 'odf': return 'application/vnd.oasis.opendocument.formula'; break;
      case 'odb': return 'application/vnd.oasis.opendocument.database'; break;
      case 'odi': return 'application/vnd.oasis.opendocument.image'; break;
      default:    return ''; break;
    }
  }

  function FlushDoc()
  {
    // flush file
    $fp = @fopen($this->GetPathnameDoc(), 'rb'); // replace readfile()
    fpassthru($fp);
    fclose($fp);
	
	 // remove tmp dir
    $this->_RemoveTmpBasenameDir();
	
  }

  function RemoveDoc()
  {
    // remove file
    unlink($this->GetPathnameDoc());
  }

  // private method

  function _PathQuote($path_quote)
  {
    if (strpos($path_quote, ' ') !== false) {
      $path_quote = (strpos($path_quote, '"') === 0 ? '' : '"').$path_quote;
      $path_quote = $path_quote.((strrpos($path_quote, '"') == strlen($path_quote)-1) ? '' : '"');
    }
    return $path_quote;
  }

  function _CharsetEncode($string_encode)
  {
    $string_encode = str_replace('&'   ,'&amp;', $string_encode);
    $string_encode = str_replace('<'   ,'&lt;',  $string_encode);
    $string_encode = str_replace('>'   ,'&gt;',  $string_encode);
    //$string_encode = str_replace("\n", '</text:p><text:p>', $string_encode); // '\n' by XML tags
    $string_encode = str_replace("\n", '<text:line-break/>', $string_encode); // '\n' by XML tags

    switch($this->_charset) {
      // OOo XML charset is utf8
      case 'UTF8': // no encode
        break;
      case 'ISO 8859-1': // encode ISO 8859-1 to UTF8
      default:
        $string_encode = utf8_encode($string_encode); 
        break;
    }
    // work-around for EURO caracter
    $string_encode = str_replace(chr(0xC2).chr(0x80) , chr(0xE2).chr(0x82).chr(0xAC),  $string_encode); // €
    return $string_encode;
  }

    function _RemoveTmpBasenameDir()
  {
     /*
     // fonction reprise de Gepi faut-il inclure le fichier ???
     function vider_dir($dir){ 
		$statut=true;
		$handle = @opendir($dir);
		while ($file = @readdir ($handle)){
			if (my_eregi("^\.{1,2}$",$file)){
				continue;
			}
			if(is_dir("$dir/$file")){
				// On ne cherche pas à vider récursivement.
				$statut=false;

				echo "<!-- DOSSIER: $dir/$file -->\n";
				// En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
			}
			else{
				if(!unlink($dir."/".$file)) {
					$statut=false;
					echo "<!-- Echec suppression: $dir/$file -->\n";
					break;
				}
			}
		}
		@closedir($handle);
		return $statut;
    }

	
    vider_dir($this->_ooo_basename); //vider le dossier contenant le xml
    */
	 // fonction reprise de Gepi faut-il inclure le fichier ???
	$dir = $this->_ooo_basename; //vider le dossier contenant le xml
	$statut=true;
	$handle = @opendir($dir);
	while ($file = @readdir ($handle)){
		if (my_eregi("^\.{1,2}$",$file)){
			continue;
		}
		if(is_dir("$dir/$file")){
			// On ne cherche pas à vider récursivement.
			$statut=false;

			echo "<!-- DOSSIER: $dir/$file -->\n";
			// En ajoutant un paramètre à la fonction, on pourrait activer la suppression récursive (avec une profondeur par exemple) lancer ici vider_dir("$dir/$file");
		}
		else{
			if(!unlink($dir."/".$file)) {
				$statut=false;
				echo "<!-- Echec suppression: $dir/$file -->\n";
				break;
			}
		}
	}
	@closedir($handle);

	// remove the temporary directory
    if (is_dir($this->_ooo_basename) && !rmdir ($this->_ooo_basename)) {
      $this->meth_Misc_Alert('_RemoveTmpDir method', 'Can\'t remove directory : '.$this->_ooo_basename);
    } 
  }

}
?>
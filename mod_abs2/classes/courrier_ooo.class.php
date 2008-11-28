<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2008
 */

/**
 * classe permettant de modifier un fichier .odt en utilisant des codes pour les variables
 * Merci au site OO france pour ses conseils avisés
 *
 */
class odfDoc{
		private $file; // Nom du fichier .odt
		private $content; // le fichier content.xml contenu dans le fichier .odt est celui qui contient les écrits
		private $vars = array(); // Array with all the data to change

		public function __construct($file) {
			$this->file = $file;

		  if (!class_exists('ZipArchive')) {

        $this->useLibPcl();

			}else{
        $zip = new ZipArchive();
        if ($zip->open($this->file) === TRUE) {
				  $this->content = $zip->getFromName('content.xml');
				  $zip->close();
        }else{
          throw new Exception('Impossible de décompresser le fichier.');
        }

			}
		}

		private function useLibPcl(){

      require_once('../../lib/pclzip.lib.php');
      $zip = new PclZip($this->file);
      $aff_fichiers = $zip->listContent();


      if ($zip->content = $zip->extract(PCLZIP_OPT_BY_NAME, $aff_fichiers[9]['filename'],  //on extrait content.xml
	                       PCLZIP_OPT_PATH, $aff_fichiers[9]['stored_filename']) == 0) { //de l'archive dans le dossier archive (id unique)
      echo "ERROR : ".$zip->errorInfo(true);
     }

      /*
      echo '<pre>';
      print_r($aff_fichiers);
      echo '</pre>';
      exit();
      */

    }

		public function setVars($key, $value) {
			$this->vars[$key] = utf8_encode($value);
		}


		protected function parse() {
			if ($this->content != NULL) {
				$this->content = str_replace(array_keys($this->vars), array_values($this->vars), $this->content);
			} else {
				throw new Exception("Impossible de parser le fichier content.xml, vérifiez que l'intégrité du fichier a bien été respecté.\n" . $this->content);
			}
		}

		public function printVars() {
			echo '<pre>';
			print_r($this->vars);
			echo '</pre>';
		}

		public function save($newfile) {

		  $this->parse();

			if ($newfile != $this->file){
				copy($this->file, $newfile);
				$this->file = $newfile;
			}
			$zip = new ZipArchive();
			if ($zip->open($this->file, ZIPARCHIVE::CREATE) === TRUE) {
				if (!$zip->addFromString('content.xml', $this->content)){
          throw new Exception("Impossible d'enregistrer le fichier");
        }

				$zip->close();

			} else {
				throw new Exception("Impossible d'enregistrer le fichier");
			}
		}

		public function versNavigateur(){

      header('Content-type: multipart/x-zip');
			header('Content-disposition: attachment; filename=' . $this->file);
			readfile($this->file);

    }

    public function versDisque(){

      // A faire si on en a besoin

    }
}

?>

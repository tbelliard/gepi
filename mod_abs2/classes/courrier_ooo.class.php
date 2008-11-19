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

		function odfDoc($file) {
			$this->file = $file;
			$zip = new ZipArchive();
			if ($zip->open($this->file) === TRUE) {
				$this->content = $zip->getFromName('content.xml');
				$zip->close();
			} else {
				exit("Impossible d'ouvrir le fichier  '$file' - Vérifiez votre fichier .odt\n");
			}
		}

		function setVars($key, $value) {
			$this->vars[$key] = $value;
		}


		function parse() {
			if ($this->content != NULL) {
				$this->content = str_replace(array_keys($this->vars), array_values($this->vars), $this->content);
			} else {
				exit("Impossible de parser le fichier content.xml, vérifiez que l'intégrité du fichier a bien été respecté.\n");
			}
		}

		function printVars() {
			echo '<pre>';
			print_r($this->vars);
			echo '</pre>';
		}

		function save($newfile) {
			if ($newfile != $this->file){
				copy($this->file, $newfile);
				$this->file = $newfile;
			}
			$zip = new ZipArchive();
			if ($zip->open($this->file, ZIPARCHIVE::CREATE) === TRUE) {
				if (!$zip->addFromString('content.xml', $this->content))
					exit('Impossible d\'enregistrer le fichier');
				$zip->close();
				echo $newfile;
			} else {
				exit('Impossible d\'enregistrer le fichier');
			}
		}
}



   $odf = new odfDoc("test.odt");
   $odf->setVars("{titre1}", "Premier titre pour voir ;)");
   $odf->setVars("{titre2}", "deuxième titre avec un accent pour voir aussi");
   $odf->parse();
   $odf->save("fichierResultat.odt");
?>

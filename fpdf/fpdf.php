<?php
//ce fichier n'est pas fpdf, c'est un fichier spécifique gepi qui étend tfpdf (une version utf8 de fpdf).
//Le nom fpdf a été gardé pour des commodités d'include dans le fichiers gepi
/**
 * Version de FPDF
 */
define('FPDF_VERSION','1.7');
define('FPDF_FONTPATH',dirname(__FILE__).'/font/');

require(dirname(__FILE__).'/tfpdf.php');

/**
 * @package externe
 * @subpackage FPDF            
 */
class FPDF extends tFPDF
{
var $B;			// texte gras
var $I;			// texte italique
var $U;			// texte souligne
var $HREF;		// lien


function __construct($orientation='P',$unit='mm',$format='A4')
{
    @setlocale(LC_NUMERIC,'C');

    parent::__construct($orientation,$unit,$format);
    //ajout de la police DejaVu
    $this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $this->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
    $this->AddFont('DejaVu','I','DejaVuSansCondensed-Oblique.ttf',true);
    $this->AddFont('DejaVu','BI','DejaVuSansCondensed-BoldOblique.ttf',true);
    $this->setFont('DejaVu');
}



//===============================================
// Fonctions de fpdf 1.52
function PutLink($URL,$txt)
{
    //Place un hyperlien
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}

function WriteHTML($html)
{
    //Parseur HTML
    $html=str_replace("\n",' ',$html);
    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Texte
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Balise
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extraction des attributs
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
				if(function_exists("mb_ereg")) {
					foreach($a2 as $v) {
						if(mb_ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
							$attr[strtoupper($a3[1])]=$a3[2];
						}
					}
				}
				else {
					foreach($a2 as $v) {
						if(my_ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
							$attr[strtoupper($a3[1])]=$a3[2];
						}
					}
				}
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function OpenTag($tag,$attr)
{
    //Balise ouvrante
    if($tag=='B' or $tag=='I' or $tag=='U')
        //$this->MySetStyle($tag,true);
        $this->SetStyle($tag,true);
    if($tag=='A')
        $this->HREF=$attr['HREF'];
    if($tag=='BR')
        $this->Ln(5);
}

function CloseTag($tag)
{
    //Balise fermante
    if($tag=='B' or $tag=='I' or $tag=='U')
        //$this->MySetStyle($tag,false);
        $this->SetStyle($tag,false);
    if($tag=='A')
        $this->HREF='';
}

function SetStyle($tag,$enable)
{
    //Modifie le style et sélectionne la police correspondante
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('DejaVu',$style);
}


//===============================================
// Fonction adaptée pour ne gérer que les B, U et I
// La fonction normale WriteHTML() se comporte bizarrement sur les largeurs prises en compte
// On se retrouve avec des retours à la ligne non souhaités...
function myWriteHTML($html)
{
	global $my_echo_debug, $mode_my_echo_debug;

	//================================
	// Options de debug
	// Passer à 1 pour débugger
	$my_echo_debug=0;
	//$my_echo_debug=1;

	// Les modes sont 'fichier' ou n'importe quoi d'autre qui provoque des echo... donc un échec de la génération de PDF... à ouvrir avec un bloc-notes, pas avec un lecteur PDF
	// Voir la fonction my_echo_debug() pour l'emplacement du fichier généré
	$mode_my_echo_debug='fichier';
	//$mode_my_echo_debug='';
	//================================

	if($my_echo_debug==1) my_echo_debug("\n   =====================================\n");
	if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Lancement sur \"$html\" \n");

	//Parseur HTML
	$html=str_replace("\n",' ',$html);
	$html=str_replace("\r",'',$html);
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			// Il se passe un truc bizarre avec un saut de 10cm quasiment sur l'abscisse de retour après écriture d'une cellule vide.
			if($e!="") {
				if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Avant écriture de \"$e\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
				if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Largeur de \"$e\": ".$this->GetStringWidth($e)."\n");
				$this->Cell($this->GetStringWidth($e),5, $e, 0, 0,'');
				if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Après écriture de \"$e\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
			}
		}
		else
		{
			//Balise
			if($e{0}=='/') {
				$tag=strtoupper(substr($e,1));
				if($tag=='B' or $tag=='I' or $tag=='U') {
					if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Avant fermeture de \"$tag\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
					$this->MyCloseTag($tag);
					if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Après fermeture de \"$tag\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
				}
			}
			else
			{
				//Extraction des attributs
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				if(function_exists("mb_ereg")) {
					foreach($a2 as $v) {
						if(mb_ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
							$attr[strtoupper($a3[1])]=$a3[2];
						}
					}
				}
				else {
					foreach($a2 as $v) {
						if(my_ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3)) {
							$attr[strtoupper($a3[1])]=$a3[2];
						}
					}
				}
				if($tag=='B' or $tag=='I' or $tag=='U') {
					if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Avant ouverture de \"$tag\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
					$this->MyOpenTag($tag,$attr);
					if($my_echo_debug==1) my_echo_debug("   myWriteHTML: Après ouverture de \"$tag\"\n   myWriteHTML: x=".$this->GetX()." et y=".$this->GetY()."\n");
				}
			}
		}
	}
}

function MyOpenTag($tag,$attr)
{
	//Balise ouvrante
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->MySetStyle($tag,true);
	if($tag=='A')
		$this->HREF=$attr['HREF'];
	if($tag=='BR')
		$this->Ln(5);
}

function MyCloseTag($tag)
{
	//Balise fermante
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->MySetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
}

function MySetStyle($tag,$enable)
{
	//Modifie le style et sélectionne la police correspondante
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('DejaVu',$style);
}
//===============================================

}

?>

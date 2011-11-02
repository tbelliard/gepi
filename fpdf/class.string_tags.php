<?php
/****************************************************************************
* Software: Tag Extraction Class                                            *
*           Extracts the tags and corresponding text from a string          *
* Version:  1.2                                                             *
* Date:     2005/12/08                                                      *
* Author:   Bintintan Andrei  -- klodoma@ar-sd.net                          *
*                                                                           *
* $Id: class.string_tags.php 7774 2011-08-15 22:50:16Z regis $
*                                                                           *
*                                                                           *
* License:  Free for non-commercial use	                                    *
*                                                                           *
* You may use and modify this software as you wish.                         *
* PLEASE REPORT ANY BUGS TO THE AUTHOR. THANK YOU   
 * 
 * @package externe
 * @subpackage FPDF	                    *	                    *
****************************************************************************/

/**
 * Extracts the tags from a string
 * @package externe
 * @subpackage FPDF	
*/
class String_TAGS{
var $aTAGS;
var $aHREF;
var $iTagMaxElem;

	/**
    	Constructor
	*/
	function string_tags($p_tagmax = 2){
		$this->aTAGS = array();
		$this->aHREF = array();
		$this->iTagMaxElem = $p_tagmax;

	}

	/** returnes true if $p_tag is a "<open tag>"
		@param 	$p_tag - tag string
                $p_array - tag array;
        @return true/false
	*/
    function OpenTag($p_tag, $p_array){

        $aTAGS = & $this->aTAGS;
        $aHREF = & $this->aHREF;
        $maxElem = & $this->iTagMaxElem;
      
        if (!preg_match("/^<([a-zA-Z1-9]{1,$maxElem}) *(.*)>$/i", $p_tag, $reg)) return false;

        $p_tag = $reg[1];

        $sHREF = array();
        if (isset($reg[2])) {
            preg_match_all("|([^ ]*)=[\"'](.*)[\"']|U", $reg[2], $out, PREG_PATTERN_ORDER);
            for ($i=0; $i<count($out[0]); $i++){
                $out[2][$i] = preg_replace("/(\"|')/i", "", $out[2][$i]);
                array_push($sHREF, array($out[1][$i], $out[2][$i]));
            }           
        }

        if (in_array($p_tag, $aTAGS)) return false;//tag already opened

        if (in_array("</$p_tag>", $p_array)) {
        	array_push($aTAGS, $p_tag);
        	array_push($aHREF, $sHREF);
            return true;
        }
        return false;
    }

	/** returnes true if $p_tag is a "<close tag>"
		@param 	$p_tag - tag string
                $p_array - tag array;
        @return true/false
	*/
	function CloseTag($p_tag, $p_array){

	    $aTAGS = & $this->aTAGS;
	    $aHREF = & $this->aHREF;
	    $maxElem = & $this->iTagMaxElem;

	    if (!preg_match("#^</([a-zA-Z1-9]{1,$maxElem})>$#", $p_tag, $reg)) return false;

	    $p_tag = $reg[1];

	    if (in_array("$p_tag", $aTAGS)) {
	    	array_pop($aTAGS);
	    	array_pop($aHREF);
	    	return true;
		}
	    return false;
	}
    
    /**
    * @desc Expands the paramteres that are kept in Href field
    * @param        array of parameters
    * @return       string with concatenated results
    */
    
    function expand_parameters($pResult){
        $aTmp = $pResult['params'];
        if ($aTmp <> '')
            for ($i=0; $i<count($aTmp); $i++){
                $pResult[$aTmp[$i][0]] = $aTmp[$i][1];
            }
            
        unset($pResult['params']);
        
        return $pResult;
        
    }

	/** Optimieses the result of the tag
		In the result array there can be strings that are consecutive and have the same tag
		This is eliminated
		@param 	$result
		@return optimized array
	*/
	function optimize_tags($result){

		if (count($result) == 0) return $result;

		$res_result = array();
    	$current = $result[0];
    	$i = 1;

    	while ($i < count($result)){

    		//if they have the same tag then we concatenate them
			if (($current['tag'] == $result[$i]['tag']) && ($current['params'] == $result[$i]['params'])){
				$current['text'] .= $result[$i]['text'];
			}else{
                $current = $this->expand_parameters($current);
				array_push($res_result, $current);
				$current = $result[$i];
			}

			$i++;
    	}

        $current = $this->expand_parameters($current);
    	array_push($res_result, $current);
        
    	return $res_result;
    }

   	/** Parses a string and returnes the result
		@param 	$p_str - string
        @return array (
        			array (string1, tag1),
        			array (string2, tag2)
        		)
	*/
	function get_tags($p_str){

	    $aTAGS = & $this->aTAGS;
	    $aHREF = & $this->aHREF;
	    $aTAGS = array();
	    $result = array();

		$reg = preg_split('/(<.*>)/U', $p_str, -1, PREG_SPLIT_DELIM_CAPTURE);

	    $sTAG = "";
	    $sHREF = "";

        while (list($key, $val) = each($reg)) {
	    	if ($val == "") continue;

	        if ($this->OpenTag($val,$reg)){
	            $sTAG = (($temp = end($aTAGS)) != NULL) ? $temp : "";
	            $sHREF = (($temp = end($aHREF)) != NULL) ? $temp : "";
	        }elseif($this->CloseTag($val, $reg)){
	            $sTAG = (($temp = end($aTAGS)) != NULL) ? $temp : "";
	            $sHREF = (($temp = end($aHREF)) != NULL) ? $temp : "";
	        }else {
	        	if ($val != "")
	        		array_push($result, array('text'=>$val, 'tag'=>$sTAG, 'params'=>$sHREF));
	        }
	    }//while

	    return $this->optimize_tags($result);
	}

}//class String_TAGS{

?>
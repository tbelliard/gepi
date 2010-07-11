<?php
/*
********************************************************
tbsdb_php Database Plugin for TinyButStrong >= 3.0
------------------------
Version  : 1.0 for PHP >= 4.0.6
Date     : 2006-06-07
Author   : kapouer at melix.org
********************************************************
This library is free software.
You can redistribute and modify it even for commercial usage,
but you must accept and respect the LPGL License version 2.1.
*/
/*
PlugIn for function evaluation in dynamic sub queries

README :

1) please note the function must return an array, like db results.
2) FIRST : TEST YOUR FUNCTION OUTSIDE eval() (on failure eval returns false, not good for debugging).

USAGE :

function test($pStr) {
	return strtoupper($pStr);
}
$fCode = 'test("hello")';

$TBS->MergeBlock('result', 'php', $fCode);

PURPOSE :
	to call functions in subqueries :
	$TBS->MergeBlock('result', 'php', "get_my_array(%p1%)");

*/

function tbsdb_php_open(&$Source, &$Query) {
	$fQuery = "return ".$Query.";";
	$fResult = eval($fQuery);
	if (!is_array($fResult) && !($fResult instanceof ArrayObject)) {
		return false;
	}
	$fLen = count($fResult);
	$fIndex = 0;
	return array('result'=>$fResult, 'length'=>$fLen, 'index'=>$fIndex);
}

function tbsdb_php_fetch(&$Rs, $RecNum) {
	$fLen = $Rs['length'];
	$fIndex = $Rs['index'];
	if ($fIndex < $fLen) {
		$fData = $Rs['result'][$fIndex++];
		$Rs['index'] = $fIndex;
		return $fData;
	} else return false;
}

function tbsdb_php_close(&$Rs){}

?>
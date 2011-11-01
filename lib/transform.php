<?php
/*
 *
*/
$InlineImages = "png|jpg|gif";
$AllowedProtocols = "http|https|mailto|ftp|news|gopher";
$FieldSeparator = "\263";

$html = '';


$stack = new Stack;


$tab_content = explode("\n", $content);
$numlines = count($tab_content);
for ($index = 0; $index < $numlines; $index++) {
  unset($tokens);
  unset($replacements);
  $ntokens = 0;
  $replacements = array();
  $tmpline = $tab_content[$index];

  if (!mb_strlen($tmpline) || $tmpline == "\r") {
    // this is a blank line, send <p>
    $html .= SetHTMLOutputMode('', "0", 0);
    continue; //passage à l'itération suivante
  }

  // HTML AUTORISE OU INTERDIT ...
  // IL EST PRECISE QUE L'AUTORISATION DU HTML FACILITE LE PIRATAGE DE VOS PAGES
  //      /*
  //POUR INTERDIRE LE HTML, SUPPRIMER LES // A LA LIGNE CI-DESSUS (1/2)
  elseif (preg_match("/(^\|)(.*)/", $tmpline, $matches)) {
    // HTML mode
    $html .= SetHTMLOutputMode("", "0", 0);
    $html .= $matches[2];
    continue;  //passage à l'itération suivante
  }

  //      */
  //POUR INTERDIRE LE HTML, SUPPRIMER LES // A LA LIGNE CI-DESSUS (2/2)

  $oldn = $ntokens;
  $tmpline = tokenize($tmpline, '\[\[', $replacements, $ntokens);
  while ($oldn < $ntokens) $replacements[$oldn++] = '[';
  $oldn = $ntokens;
  $tmpline = tokenize($tmpline, '\[\s*\d+\s*\]', $replacements, $ntokens);
  while ($oldn < $ntokens) {
    $num = (int) mb_substr($replacements[$oldn], 1);
    if (! empty($embedded[$num]))
      $replacements[$oldn] = $embedded[$num];
    $oldn++;
  }
  $oldn = $ntokens;
  $tmpline = tokenize($tmpline, '\[.+?\]', $replacements, $ntokens);
  while ($oldn < $ntokens) {
    $link = ParseAndLink($replacements[$oldn]);
    $replacements[$oldn] = $link['link'];
    $oldn++;
  }
  /*
  $tmpline = tokenize($tmpline, "!?\b($AllowedProtocols):[^\s<>\[\]\"'()]*[^\s<>\[\]\"'(),.?]", $replacements, $ntokens);
  while ($oldn < $ntokens) {
    if($replacements[$oldn][0] == '!')
      $replacements[$oldn] = mb_substr($replacements[$oldn], 1);
    else
      $replacements[$oldn] = LinkURL($replacements[$oldn]);
    $oldn++;
  }
  */
  $oldn = $ntokens;

//  $tmpline = str_replace('&', '&amp;', $tmpline);
//  $tmpline = str_replace('>', '&gt;', $tmpline);
//  $tmpline = str_replace('<', '&lt;', $tmpline);
  $tmpline = str_replace('%%%', '<br />', $tmpline);
  $tmpline = preg_replace("|(''''')(.*?)(''''')|", "<strong><em>\\2</em></strong>", $tmpline);
  $tmpline = preg_replace("|(''')(.*?)(''')|", "<U>\\2</U>", $tmpline);
  $tmpline = preg_replace("|(__)(.*?)(__)|", "<strong>\\2</strong>", $tmpline);
  $tmpline = preg_replace("|(_c_)(.*?)(_c_)|", "<center>\\2</center>", $tmpline);
  $tmpline = preg_replace("|(_b_)(.*?)(_b_)|", "<font color='blue'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_v_)(.*?)(_v_)|", "<font color='green'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_r_)(.*?)(_r_)|", "<font color='red'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_o_)(.*?)(_o_)|", "<font color='orange'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_m_)(.*?)(_m_)|", "<font color='brown'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_j_)(.*?)(_j_)|", "<font color='yellow'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_p_)(.*?)(_p_)|", "<font color='purple'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|(_g_)(.*?)(_g_)|", "<font color='grey'>\\2</font>", $tmpline);
  $tmpline = preg_replace("|('')(.*?)('')|",  "<em>\\2</em>", $tmpline);
  if (preg_match("/(^\t+)(.*?)(:\t)(.*$)/", $tmpline, $matches)) {
    $numtabs = mb_strlen($matches[1]);
    $html .= SetHTMLOutputMode('dl', "1", $numtabs);
    $tmpline = '';
    if(trim($matches[2]))
        $tmpline = '<dt>' . $matches[2];
    $tmpline .= '<dd>' . $matches[4];
  } elseif (preg_match("/(^\t+)(\*|\d+|#)/", $tmpline, $matches)) {
      $numtabs = mb_strlen($matches[1]);
      if ($matches[2] == '*') {
         $listtag = 'ul';
      } else {
          $listtag = 'ol'; // a rather tacit assumption. oh well.
      }
      $tmpline = preg_replace("/^(\t+)(\*|\d+|#)/", "", $tmpline);
      $html .= SetHTMLOutputMode($listtag, "1", $numtabs);
      $html .= '<li>';
  } elseif (preg_match("/^([#*]*\*)[^#]/", $tmpline, $matches)) {
      $numtabs = mb_strlen($matches[1]);
      $tmpline = preg_replace("/^([#*]*\*)/", '', $tmpline);
      $html .= SetHTMLOutputMode('ul', "1", $numtabs);
      $html .= '<li>';
  } elseif (preg_match("/^([#*]*\#)/", $tmpline, $matches)) {
     $numtabs = mb_strlen($matches[1]);
     $tmpline = preg_replace("/^([#*]*\#)/", "", $tmpline);
     $html .= SetHTMLOutputMode('ol', "1", $numtabs);
     $html .= '<li>';
  } elseif (preg_match("/(^;+)(.*?):(.*$)/", $tmpline, $matches)) {
     $numtabs = mb_strlen($matches[1]);
     $html .= SetHTMLOutputMode('dl', "1", $numtabs);
     $tmpline = '';
     if(trim($matches[2]))
        $tmpline = '<dt>' . $matches[2];
      $tmpline .= '<dd>' . $matches[3];
  //} elseif (preg_match("/^\s+/", $tmpline)) {
     // this is preformatted text, i.e. <pre>
     //$html .= SetHTMLOutputMode('pre', "0", 0);
  } elseif (preg_match("/^(!{1,3})[^!]/", $tmpline, $whichheading)) {
    if($whichheading[1] == '!') $heading = 'h3';
    elseif($whichheading[1] == '!!') $heading = 'h2';
    elseif($whichheading[1] == '!!!') $heading = 'h1';
    $tmpline = preg_replace("/^!+/", '', $tmpline);
    $html .= SetHTMLOutputMode($heading, "0", 0);
  } elseif (preg_match('/^-{4,}\s*(.*?)\s*$/', $tmpline, $matches)) {
    $html .= SetHTMLOutputMode('', "0", 0) . "<hr />\n";
    if ( ($tmpline = $matches[1]) != '' ) {
      $html .= SetHTMLOutputMode('p', "0", 0);
    }
  //} elseif (preg_match('/^%{3,}\s*(.*?)\s*$/', $tmpline, $matches)) {
  //    $html .= SetHTMLOutputMode('', "0", 0) . "<br />\n";
  } else {
	//$html .= SetHTMLOutputMode('p', "0", 0);
	//correction Régis, enlever p pour ne plus injecter les balises <p>
	$html = preg_replace("|(<u>)(.*?)(</u>)|", "<ins>\\2</ins>", $html);
	$html .= SetHTMLOutputMode('', "0", 0);
  }
  for ($indice = 0; $indice < $ntokens; $indice++)
     $tmpline = str_replace($FieldSeparator.$FieldSeparator.$indice.$FieldSeparator, $replacements[$indice], $tmpline);
  $html .= $tmpline . "\n";
}
$html .= SetHTMLOutputMode('', "0", 0);

?>
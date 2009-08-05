<?php
/*
 * Last modification  : 18/03/2005
 *
 */

function ExitWiki($errormsg)
   {
      static $exitwiki = 0;

      if($exitwiki)
         exit();
      $exitwiki = 1;

      if($errormsg <> '') {
//
         print "<P><hr noshade><h2>" . gettext("ErreurWiki") . "</h2>\n";
         print $errormsg;
         print "\n</BODY></HTML>";
      }
      exit;
   }

   function LinkUnknown($word, $linktext='') {
      global $ScriptUrl;
      $enc_word = rawurlencode($word);
      if(empty($linktext))
         $linktext = htmlspecialchars($word);
      return "$linktext";
   }

   function LinkImage($url, $alt='') {
      global $ScriptUrl;
      if(my_ereg('[<>"]', $url)) {
         return "<b><u>Mauvaise URL -- supprimez tous les caractères &lt;, &gt;, &quot;</u></b>";
      }
      return "<img src=\"$url\" ALT=\"$alt\">";
   }


function ParseAndLink($bracketlink) {
      global $ScriptUrl, $AllowedProtocols, $InlineImages;
           
      preg_match("/(\[\s*)(.+?)(\s*\])/", $bracketlink, $match);
      
      preg_match("/([^|]+)(\|)?([^|]+)?/", $match[2], $matches);

      if (isset($matches[3])) {
         
         $URL = trim($matches[3]);
         $linkname = htmlspecialchars(trim($matches[1]));
     $linktype = 'named';
      } else {
         
         $URL = trim($matches[1]);
     $linkname = '';
     $linktype = 'simple';
      }

      if (preg_match("#^($AllowedProtocols):#", $URL)) {
        // if it's an image, embed it; otherwise, it's a regular link
         if (preg_match("/($InlineImages)$/i", $URL)) {
        $link['type'] = "image-$linktype";
            $link['link'] = LinkImage($URL, $linkname);
         } else {
        $link['type'] = "url-$linktype";
            $link['link'] = LinkURL($URL, $linkname);
         }
      } elseif (preg_match("#^\d+$#", $URL)) {
         $link['type'] = "reference-$linktype";
     $link['link'] = $URL;
      } else {
     $link['type'] = "wiki-unknown-$linktype";
         $link['link'] = LinkUnknown($URL, $linkname);
      }

      return $link;
   }


function LinkURL($url, $linktext='') {
  if(my_ereg("[<>\"]", $url)) {
    return "<b><u>BAD URL -- remove all of &lt;, &gt;, &quot;</u></b>";
  }
  if(empty($linktext))
    $linktext = htmlspecialchars($url);
  return "<a href=\"$url\">$linktext</a>";
}


class Stack {
  var $items = array();
  var $size = 0;

  function push($item) {
    $this->items[$this->size] = $item;
    $this->size++;
    return true;
  }  
   
  function pop() {
     if ($this->size == 0) {
        return false; // stack is empty
     }  
     $this->size--;
     return $this->items[$this->size];
  }  
  
  function cnt() {
     return $this->size;
  }  
  function top() {
    if($this->size)
        return $this->items[$this->size - 1];
    else
        return '';
   }  

}  

function SetHTMLOutputMode($tag, $tagtype, $level)
   {
      global $stack;
      $retvar = '';

      if ($level > 10) {
      $level = 10;
      }
      
      if ($tagtype == "0") {
         // empty the stack until $level == 0;
         if ($tag == $stack->top()) {
            return; // same tag? -> nothing to do
         }
         while ($stack->cnt() > 0) {
            $closetag = $stack->pop();
            $retvar .= "</$closetag>\n";
         }
   
         if ($tag) {
            $retvar .= "<$tag>\n";
            $stack->push($tag);
         }

      } elseif ($tagtype == "1") {
         if ($level < $stack->cnt()) {
            while ($stack->cnt() > $level) {
               $closetag = $stack->pop();
               if ($closetag == false) {
                  
                  break;
               }
               $retvar .= "</$closetag>\n";
            }
        
        if ($tag != $stack->top()) {
           $closetag = $stack->pop();
           $retvar .= "</$closetag><$tag>\n";
           $stack->push($tag);
        }
   
         } elseif ($level > $stack->cnt()) {
        if ($stack->cnt() == 1 and
            preg_match('/^(p|pre|h\d)$/i', $stack->top()))
        {
           $closetag = $stack->pop();
           $retvar .= "</$closetag>";
        }

        
        if ($stack->cnt() < $level) {
           while ($stack->cnt() < $level - 1) {
          
          $retvar .= "<dl><dd>";
          $stack->push('dl');
           }

           $retvar .= "<$tag>\n";
           $stack->push($tag);
            }
   
         } else { // $level == $stack->cnt()
            if ($tag == $stack->top()) {
               return; // same tag? -> nothing to do
            } else {
           // different tag - close old one, add new one
               $closetag = $stack->pop();
               $retvar .= "</$closetag>\n";
               $retvar .= "<$tag>\n";
               $stack->push($tag);
            }
         }
   
      } else { // unknown $tagtype
         ExitWiki ("Passed bad tag type value in SetHTMLOutputMode");
      }

      return $retvar;
}


function tokenize($str, $pattern, &$orig, &$ntokens) {
   global $FieldSeparator;
   $new = '';      
   while (preg_match("/^(.*?)($pattern)/", $str, $matches)) {
      $linktoken = $FieldSeparator . $FieldSeparator . ($ntokens++) . $FieldSeparator;
      $new .= $matches[1] . $linktoken;
      $orig[] = $matches[2];
      $str = substr($str, strlen($matches[0]));
   }
   $new .= $str;
   return $new;
}
?>
<?php
/*
 *
 * Copyright 2001, 2008 Thomas Belliard
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


# Cette classe sert à manipuler ou creer des fichiers CSV.


class CsvClass {

  private $name=Null;
  private $path=Null;
  private $delimiter=";";
  private $enclosure='"';
  private $filename=Null;
  private $exists=false;

  public function  __construct($name,$path,$delimiter=Null,$enclosure=Null) {

    $this->path=$path;
    $this->name=$name;
    $this->filename=$this->path.$this->name.'.csv';
    if($delimiter) $this->delimiter=$delimiter;
    if($enclosure) $this->enclosure=$enclosure;
    if (file_exists($this->filename)) {
      $this->exists=true;
    }
  }

  public function set_data($data) {
    //if($this->exists) $this->rename();
    $fp = fopen($this->filename, 'w');
    foreach($data as $line) {
      fputcsv($fp, split(',',$line),$this->delimiter,$this->enclosure);
    }
    fclose($fp);
  }

  private function rename() {
    $i=1;
    while($this->exists) {
      $this->filename=$this->path.$this->name.'_'.$i.'.csv';
      if (!file_exists($this->filename)) {
        $this->exists=false;
      }
      $i++;
    }
  }
}
?>

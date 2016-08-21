<?php
/*
 * Last modification  : 18/03/2005
 *
*/
// ----------------------------------------------------------------------
// HTACCESS class
// Copyright (c) 2003
// by Armand Turpel
// http://www.open-publisher.net/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

/**
 * HTACCESS class
 *
 * <p>Example: Create a htaccess file and a htpasswd file with 2 users</p>
 * <p></p>
 * <p>$ht = & new htaccess(TRUE);</p>
 * <p>$ht->set_user('Henry','Henrys password');</p>
 * <p>$ht->set_user('Mary','Marys password');</p> 
 * <p>$ht->set_htaccess();</p> 
 * <p>$ht->set_htpasswd();</p> 
 *
 * @link http://www.open-publisher.net/
 * @author Armand Turpel <contact@open-publisher.net>
 * @version $Revision$
 * @since 2003-05-09
 */
class htaccess
{
    /**
     * Directory of the htaccess and htpasswd file
     * var string $ht_dir
     */
    var $ht_dir;
    
    /**
     * Name of the htaccess file
     * var string $htaccess_file
     */
    var $htaccess_file = '.htaccess';

    /**
     * Name of the htpasswd file
     * var string $htpasswd_file
     */
    var $htpasswd_file = '.htpasswd';
    
    /**
     * Array of the htaccess vars
     * var array $option
     */    
    var $option;  

    /**
     * Accossiative Array of users name and passwords
     * var array $user
     */       
    var $user = array();

    /**
     * Password encryption methode
     * var string $crypt_method
     */           
    var $crypt_method = CRYPT_STD_DES;

    /**
     * Constructor
     * 
     * @var bool $default If TRUE set htaccess default data
     */           
    function htaccess($default = FALSE)
    {
		$dirname = getSettingValue("backup_directory");
        $this->ht_dir =  realpath("../backup/".$dirname);

        if($default == TRUE)
        {
            $this->option = array(
                     'AuthUserFile' => $this->ht_dir.'/'.$this->htpasswd_file,
                     'AuthName'     => '"Admin"',
                     'AuthType'     => 'Basic',
                     'require'      => 'valid-user',
                    );
        }
    }

    /**
     * Set user
     *
     * @var string $name User name
     * @var string $passwd User password
     * @return bool True if successfull else false
     */               
    function set_user($name, $passwd)
    {
        if(empty($name) || empty($passwd))
        {
            return false;
        }
        //$this->user[$name] = crypt($passwd, $this->crypt_method);      // PB
        //$this->user[$name] = crypt($passwd,base64_encode($passwd));    // OK
        $this->user[$name] = "{SHA}".base64_encode(sha1($passwd, TRUE)); // OK
        return TRUE;
    }

    /**
     * Delete user
     *
     * @var string $name User name
     */               
    function delete_user($name)
    {
        $this->user[$name] = '';
    }

    /**
     * Load vars from an existing htaccess file.
     *
     * @return array Accosiative array with the file data. False if it fails
     */               
    function & get_htaccess()
    {
        if($f = @fopen($this->ht_dir.'/'.$this->htaccess_file, "r"))
        {
            $option = array();
            flock($f,2);
            while($f && !feof($f))
            {
                $o = array();
                if(preg_match("/^([a-zA-Z]*) (.*)/",fgets($f, 1024),$o))
                {
                    $option[$o[1]] = $o[2];
                }
            }
            flock($f,3);
            fclose($f);
            ksort($option);
            return $option;
        }
        else
        {
            return FALSE;
        }    
    }
    
    /**
     * Save the htaccess file.
     *
     * @return bool True if successfull else false
     */               
    function set_htaccess()
    {
        $nl = chr(10);
        if($f = fopen($this->ht_dir.'/'.$this->htaccess_file, "w"))
        {
            flock($f,2);
            foreach($this->option as $key => $value)
            {
                fputs($f, $key.' '.$value.$nl);
            }
            flock($f,3);
            fclose($f); 
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Load vars from an existing htaccess file.
     *
     * @return array Accosiative array with the file data. False if it fails
     */
    //function & get_htpasswd()
    function get_htpasswd()
    {  
        if($f = @fopen($this->ht_dir.'/'.$this->htpasswd_file, "r"))
        {
            $users = array();
            flock($f,2);
            while($f && !feof($f))
            {
                $usr = array();
                if(preg_match("/^([a-zA-Z]*):(.*)/",fgets($f, 1024),$usr))
                {                    
                    $users[$usr[1]] = $usr[2];
                }
            }
            flock($f,3);
            fclose($f);
            ksort($users);
            return $users;
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * Save the htpasswd file.
     *
     * If this file exists all users are loaded and after added or 
     * modified user data resaved. If this file isn't exists
     * it is created.
     *
     * @return bool TRUE if successfull else FALSE
     */               
    function set_htpasswd()
    {
        $nl = chr(10);
        
        if(file_exists($this->ht_dir.'/'.$this->htpasswd_file))
        {  
            $users = array();
            if($f = fopen($this->ht_dir.'/'.$this->htpasswd_file, "r"))
            {
                flock($f,2);
                while($f && !feof($f))
                {
                    $usr = array();
                    if(preg_match("/^([a-zA-Z]*):(.*)/",fgets($f, 1024),$usr))
                    {
                        $users[$usr[1]] = $usr[2];
                    }
                }
                flock($f,3);
                fclose($f);
            }
            else
            {
                return FALSE;
            }
            
            foreach($this->user as $key => $value)
            {
                if(empty($value))
                {
                    unset($users[$key]);
                }
                else
                {
                    $users[$key] = $value;                  
                }
            }
            
            ksort($users);
            
            if($f = fopen($this->ht_dir.'/'.$this->htpasswd_file, "w"))
            {
                flock($f,2);

                foreach($users as $key => $value)
                {
                    $str = $key.':'.$value.$nl;
                    fputs($f, $str, mb_strlen($str));
                }
            
                flock($f,3);
                fclose($f);
                
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            ksort($this->user);
            
            if($f = fopen($this->ht_dir.'/'.$this->htpasswd_file, "w"))
            {
                flock($f,2);
                
                foreach($this->user as $key => $value)
                {
                    $str = $key.':'.$value.$nl;
                    fputs($f, $str, mb_strlen($str));
                }
                
                flock($f,3);
                fclose($f); 
                
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
    }

    /**
     * Destructor
     */
    function _destroy()
    {
        unset($this->user);
        unset($this->option);
        unset($this->ht_dir);
        unset($this->htaccess_file);
        unset($this->htpasswd_file);
        unset($this->crypt_method);        
    }
}
?>

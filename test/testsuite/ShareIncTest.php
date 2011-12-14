<?php

require_once dirname(__FILE__) . "/../../lib/share.inc.php";
require_once dirname(__FILE__) . "/../../mod_serveur/test_encoding_functions.php";

/**
 * Test class for lib/share.inc.php file
 *
 */
class ShareIncTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	    parent::setUp();
	    mb_internal_encoding("UTF-8");
	}

	//les tests sont effectuées dans le fichier mod_serveur/test_incoding_functions.php
	public function test_check_utf8()
	{
	    $this->assertTrue(test_check_utf8());

            $utf8_str = file_get_contents(dirname(__FILE__) . "/../tools/utf8_str.txt");
	    $this->assertTrue(check_utf8($utf8_str));
            $iso_str = file_get_contents(dirname(__FILE__) . "/../tools/iso-8859-1_str.txt");
	    $this->assertFalse(check_utf8($iso_str));
	}
	
	public function test_detect_encoding()
	{
	    $this->assertTrue(test_detect_encoding());
	    
            $utf8_str = file_get_contents(dirname(__FILE__) . "/../tools/utf8_str.txt");
            $this->assertEquals("UTF-8", detect_encoding($utf8_str));
            $iso_str = file_get_contents(dirname(__FILE__) . "/../tools/iso-8859-1_str.txt");
            $this->assertEquals("ISO-8859-15", detect_encoding($iso_str));
	    
	}
	
	public function test_ensure_utf8()
	{
	    $this->assertTrue(test_ensure_utf8());
            
            $utf8_str = file_get_contents(dirname(__FILE__) . "/../tools/utf8_str.txt");
	    $this->assertTrue(ensure_utf8($utf8_str) == $utf8_str);
            $iso_str = file_get_contents(dirname(__FILE__) . "/../tools/iso-8859-1_str.txt");
	    $this->assertTrue(ensure_utf8($iso_str) == $utf8_str);
	}
	
	public function test_remplace_accents()
	{
	    $this->assertTrue(test_remplace_accents());
	    
            $utf8_str = file_get_contents(dirname(__FILE__) . "/../tools/utf8_str.txt");
            $ascii_str = file_get_contents(dirname(__FILE__) . "/../tools/ascii_str.txt");
	    $this->assertEquals(remplace_accents($utf8_str), $ascii_str);
	}
	
	public function test_casse_mot()
	{
	    $this->assertTrue(test_casse_mot());
	}
	
}

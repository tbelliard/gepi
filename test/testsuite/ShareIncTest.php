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
	}

	//les tests sont effectuées dans le fichier mod_serveur/test_incoding_functions.php
	public function test_check_utf8()
	{
	    $this->assertTrue(test_check_utf8());
	}
	
	public function test_detect_encoding()
	{
	    $this->assertTrue(test_detect_encoding());
	}
	
	public function test_ensure_utf8()
	{
	    $this->assertTrue(test_ensure_utf8());
	}
	
	public function test_remplace_accents()
	{
	    $this->assertTrue(test_remplace_accents());
	}
	
}

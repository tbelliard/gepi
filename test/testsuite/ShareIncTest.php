<?php

require_once dirname(__FILE__) . "/../../lib/share.inc.php";

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

	public function test_check_utf8()
	{
	    $this->assertTrue(check_utf8("auie"));
	    $this->assertTrue(check_utf8("bépowǜdlj"));
		$this->assertTrue(check_utf8(";œ€âũ"));
		
	    //$examples = array(
        //    "Valid ASCII" => "a",
        //    "Valid 2 Octet Sequence" => "\xc3\xb1",
        //    "Invalid 2 Octet Sequence" => "\xc3\x28",
        //    "Invalid Sequence Identifier" => "\xa0\xa1",
        //    "Valid 3 Octet Sequence" => "\xe2\x82\xa1",
        //    "Invalid 3 Octet Sequence (in 2nd Octet)" => "\xe2\x28\xa1",
        //    "Invalid 3 Octet Sequence (in 3rd Octet)" => "\xe2\x82\x28",
        //    "Valid 4 Octet Sequence" => "\xf0\x90\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 2nd Octet)" => "\xf0\x28\x8c\xbc",
        //    "Invalid 4 Octet Sequence (in 3rd Octet)" => "\xf0\x90\x28\xbc",
        //    "Invalid 4 Octet Sequence (in 4th Octet)" => "\xf0\x28\x8c\x28",
        //    "Valid 5 Octet Sequence (but not Unicode!)" => "\xf8\xa1\xa1\xa1\xa1",
        //    "Valid 6 Octet Sequence (but not Unicode!)" => "\xfc\xa1\xa1\xa1\xa1\xa1",
        //);
		$this->assertTrue(check_utf8("a"));
		$this->assertTrue(check_utf8("\xc3\xb1"));
		$this->assertFalse(check_utf8("\xc3\x28"));
		$this->assertFalse(check_utf8("\xa0\xa1"));
		$this->assertTrue(check_utf8("\xe2\x82\xa1"));
		$this->assertFalse(check_utf8("\xe2\x28\xa1"));
		$this->assertFalse(check_utf8("\xe2\x82\x28"));
		$this->assertTrue(check_utf8("\xf0\x90\x8c\xbc"));
		$this->assertFalse(check_utf8("\xf0\x28\x8c\xbc"));
		$this->assertFalse(check_utf8("\xf0\x90\x28\xbc"));
		$this->assertFalse(check_utf8("\xf0\x28\x8c\x28"));
		$this->assertTrue(check_utf8("\xf8\xa1\xa1\xa1\xa1"));
		$this->assertTrue(check_utf8("\xfc\xa1\xa1\xa1\xa1\xa1"));
	}
	
	public function test_detect_encoding()
	{
		$this->assertEquals("UTF-8",detect_encoding("auie"));
	    $this->assertEquals("UTF-8",detect_encoding("bépowǜdlj"));
		$this->assertEquals("UTF-8",detect_encoding(";œ€âũ"));
		$this->assertEquals("UTF-8",detect_encoding("é"));
		$this->assertEquals("ISO-8859-15",detect_encoding("\xe9"));
		$this->assertEquals("ISO-8859-15",detect_encoding("\xa4"));
		
	}
	
	public function test_ensure_utf8()
	{
		$this->assertEquals("auie",ensure_utf8("auie", 'UTF-8'));
	    $this->assertEquals("auie",ensure_utf8("auie"));
		$this->assertEquals("bépowǜdlj",ensure_utf8("bépowǜdlj"));
		$this->assertEquals(";œ€âũ",ensure_utf8(";œ€âũ"));
		$this->assertEquals("é",ensure_utf8("é"));
		$this->assertEquals("é",ensure_utf8("\xe9"));
		$this->assertEquals("€",ensure_utf8("\xa4"));
		$this->assertEquals("€",ensure_utf8("\xa4",'ISO-8859-15'));
		$this->assertEquals("¤",ensure_utf8("\xa4",'ISO-8859-1'));
		
	}
	
}

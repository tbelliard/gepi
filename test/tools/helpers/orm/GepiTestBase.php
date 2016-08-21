<?php

if (date_default_timezone_get() == 'UTC') {
    date_default_timezone_set('Europe/Paris');
} else {
    //dans le cas où on détecte mal une zone UTC (donc non configurée à priori),
    //on fait comme si il y avait bien une conf de précisée pour éviter l'erreur
    //PHP Fatal error:  Uncaught exception 'Exception' with message 'DateTime::__construct(): It is not safe to rely on the system's timezone settings.
    date_default_timezone_set(date_default_timezone_get());
}

global $dbHost,$dbDb,$dbUser,$dbPass;
require dirname(__FILE__) . '/../../../fixtures/config/connect.test.inc.php';
require_once(dirname(__FILE__). '/../../../../lib/mysql.inc');
require_once(dirname(__FILE__). '/../../../../lib/settings.inc');

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../../../../orm/propel-build/classes');
require_once dirname(__FILE__) . '/../../../../orm/propel/Propel.php';
Propel::init(dirname(__FILE__) . '/../../../../orm/propel-build/conf/gepi-conf.php');

/**
 * Base class contains some methods shared by subclass test cases.
 */
abstract class GepiTestBase extends PHPUnit_Framework_TestCase
{
	protected $con;

	/**
	 * This is run before each unit test; it populates the database.
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->con = Propel::getConnection();
		$this->con->beginTransaction();
	}

	/**
	 * This is run after each unit test. It empties the database.
	 */
	protected function tearDown()
	{
		parent::tearDown();
		// Only commit if the transaction hasn't failed.
		// This is because tearDown() is also executed on a failed tests,
		// and we don't want to call PropelPDO::commit() in that case
		// since it will trigger an exception on its own
		// ('Cannot commit because a nested transaction was rolled back')
		if ($this->con != null && $this->con->isCommitable()) {
			$this->con->commit();
		}
	}
}

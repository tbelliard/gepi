<?php

require_once dirname(__FILE__) . '/../../tools/helpers/orm/GepiEmptyTestBase.php';
require_once dirname(__FILE__) . '/../../../orm/helpers/EdtEmplacementCoursHelper.php';

/**
 * Test class for EdtEmplacementCoursHelper.
 *
 */
class EdtEmplacementCoursHelperTest extends GepiEmptyTestBase
{
	protected function setUp()
	{
		parent::setUp();
                GepiDataPopulator::populate();
	}

	public function test_getEdtEmplacementCoursActuel()
	{
            $edtCoursCol = EdtEmplacementCoursQuery::create()->find();
            $edtCours = EdtEmplacementCoursHelper::getEdtEmplacementCoursActuel($edtCoursCol, '2012-11-09 11:20:00');
            $this->assertNotNull($edtCours);
        }
}

<?php
namespace CakeOffice\Test\TestCase\View\Helper;

use CakeOffice\View\Helper\ExcelHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * CakeOffice\View\Helper\ExcelHelper Test Case
 */
class ExcelHelperTest extends TestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Excel = new ExcelHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Excel);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

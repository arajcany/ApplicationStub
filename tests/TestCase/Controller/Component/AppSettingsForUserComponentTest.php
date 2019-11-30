<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\AppSettingsForUserComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\AppSettingsForUserComponent Test Case
 */
class AppSettingsForUserComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\AppSettingsForUserComponent
     */
    public $AppSettingsForUser;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->AppSettingsForUser = new AppSettingsForUserComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AppSettingsForUser);

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

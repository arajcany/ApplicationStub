<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\LoadTestsUrlMakerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\LoadTestsUrlMakerComponent Test Case
 */
class LoadTestsUrlMakerComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\LoadTestsUrlMakerComponent
     */
    public $LoadTestsUrlMaker;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->LoadTestsUrlMaker = new LoadTestsUrlMakerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->LoadTestsUrlMaker);

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

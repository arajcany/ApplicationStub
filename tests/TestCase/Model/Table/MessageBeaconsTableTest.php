<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MessageBeaconsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MessageBeaconsTable Test Case
 */
class MessageBeaconsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MessageBeaconsTable
     */
    public $MessageBeacons;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.MessageBeacons',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('MessageBeacons') ? [] : ['className' => MessageBeaconsTable::class];
        $this->MessageBeacons = TableRegistry::getTableLocator()->get('MessageBeacons', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->MessageBeacons);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\HeartbeatsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\HeartbeatsTable Test Case
 */
class HeartbeatsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\HeartbeatsTable
     */
    public $Heartbeats;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Heartbeats',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Heartbeats') ? [] : ['className' => HeartbeatsTable::class];
        $this->Heartbeats = TableRegistry::getTableLocator()->get('Heartbeats', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Heartbeats);

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

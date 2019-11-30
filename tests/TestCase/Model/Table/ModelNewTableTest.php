<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ModelNewTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ModelNewTable Test Case
 */
class ModelNewTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ModelNewTable
     */
    public $ModelNew;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ModelNew',
        'app.UserStatuses',
        'app.UserLocalizations',
        'app.Roles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ModelNew') ? [] : ['className' => ModelNewTable::class];
        $this->ModelNew = TableRegistry::getTableLocator()->get('ModelNew', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ModelNew);

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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

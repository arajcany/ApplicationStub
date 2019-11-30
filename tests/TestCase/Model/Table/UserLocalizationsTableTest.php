<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserLocalizationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserLocalizationsTable Test Case
 */
class UserLocalizationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UserLocalizationsTable
     */
    public $UserLocalizations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.UserLocalizations',
        'app.Users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('UserLocalizations') ? [] : ['className' => UserLocalizationsTable::class];
        $this->UserLocalizations = TableRegistry::getTableLocator()->get('UserLocalizations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserLocalizations);

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

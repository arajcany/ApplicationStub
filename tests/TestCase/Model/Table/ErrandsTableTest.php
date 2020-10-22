<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ErrandsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ErrandsTable Test Case
 */
class ErrandsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ErrandsTable
     */
    public $Errands;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Errands',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Errands') ? [] : ['className' => ErrandsTable::class];
        $this->Errands = TableRegistry::getTableLocator()->get('Errands', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Errands);

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

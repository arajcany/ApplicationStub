<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtifactsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArtifactsTable Test Case
 */
class ArtifactsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtifactsTable
     */
    public $Artifacts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Artifacts',
        'app.ArtifactMetadata',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Artifacts') ? [] : ['className' => ArtifactsTable::class];
        $this->Artifacts = TableRegistry::getTableLocator()->get('Artifacts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Artifacts);

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

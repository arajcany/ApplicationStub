<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArtifactMetadataTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArtifactMetadataTable Test Case
 */
class ArtifactMetadataTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ArtifactMetadataTable
     */
    public $ArtifactMetadata;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ArtifactMetadata',
        'app.Artifacts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ArtifactMetadata') ? [] : ['className' => ArtifactMetadataTable::class];
        $this->ArtifactMetadata = TableRegistry::getTableLocator()->get('ArtifactMetadata', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ArtifactMetadata);

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

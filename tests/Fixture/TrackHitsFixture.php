<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TrackHitsFixture
 */
class TrackHitsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true, 'precision' => null, 'comment' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null],
        'url' => ['type' => 'string', 'length' => 1024, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'scheme' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'host' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'port' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'path' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'query' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'app_execution_time' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'data' => ['type' => 'string', 'length' => 2048, 'null' => false, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        '_indexes' => [
            'track_hits_url_index' => ['type' => 'index', 'columns' => ['url'], 'length' => []],
            'track_hits_scheme_index' => ['type' => 'index', 'columns' => ['scheme'], 'length' => []],
            'track_hits_app_execution_time_index' => ['type' => 'index', 'columns' => ['app_execution_time'], 'length' => []],
            'track_hits_query_index' => ['type' => 'index', 'columns' => ['query'], 'length' => []],
            'track_hits_port_index' => ['type' => 'index', 'columns' => ['port'], 'length' => []],
            'track_hits_path_index' => ['type' => 'index', 'columns' => ['path'], 'length' => []],
            'track_hits_host_index' => ['type' => 'index', 'columns' => ['host'], 'length' => []],
            'track_hits_created_index' => ['type' => 'index', 'columns' => ['created'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'sqlite_autoindex_track_hits_1' => ['type' => 'unique', 'columns' => ['id'], 'length' => []],
        ],
    ];
    // @codingStandardsIgnoreEnd
    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'created' => '2021-01-19 04:54:16',
                'url' => 'Lorem ipsum dolor sit amet',
                'scheme' => 'Lorem ip',
                'host' => 'Lorem ipsum dolor sit amet',
                'port' => 'Lorem ip',
                'path' => 'Lorem ipsum dolor sit amet',
                'query' => 'Lorem ipsum dolor sit amet',
                'app_execution_time' => 'Lorem ip',
                'data' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}

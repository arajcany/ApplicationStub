<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MessageBeaconsFixture
 */
class MessageBeaconsFixture extends TestFixture
{
    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'autoIncrement' => true, 'precision' => null, 'comment' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'modified' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'beacon_hash' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'beacon_url' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'beacon_data' => ['type' => 'string', 'length' => 2048, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        '_indexes' => [
            'message_beacons_modified_index' => ['type' => 'index', 'columns' => ['modified'], 'length' => []],
            'message_beacons_created_index' => ['type' => 'index', 'columns' => ['created'], 'length' => []],
            'message_beacons_beacon_hash_index' => ['type' => 'index', 'columns' => ['beacon_hash'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
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
                'created' => 1615194413,
                'modified' => 1615194413,
                'beacon_hash' => 'Lorem ipsum dolor sit amet',
                'beacon_url' => 'Lorem ipsum dolor sit amet',
                'beacon_data' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}

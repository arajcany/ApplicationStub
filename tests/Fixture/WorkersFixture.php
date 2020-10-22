<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * WorkersFixture
 */
class WorkersFixture extends TestFixture
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
        'domain' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'type' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'errand_link' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'errand_name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'appointment_date' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'retirement_date' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'termination_date' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'force_retirement' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'pid' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
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
                'created' => 1603262987,
                'modified' => 1603262987,
                'domain' => 'Lorem ipsum dolor sit amet',
                'name' => 'Lorem ipsum dolor sit amet',
                'type' => 'Lorem ipsum dolor sit amet',
                'errand_link' => 1,
                'errand_name' => 'Lorem ipsum dolor sit amet',
                'appointment_date' => 1603262987,
                'retirement_date' => 1603262987,
                'termination_date' => 1603262987,
                'force_retirement' => 1,
                'pid' => 1,
            ],
        ];
        parent::init();
    }
}

<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ErrandsFixture
 */
class ErrandsFixture extends TestFixture
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
        'activation' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'expiration' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'auto_delete' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'wait_for_link' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'domain' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'worker_link' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'worker_name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'class' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'method' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'parameters' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'status' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'started' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'completed' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'progress_bar' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'priority' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'return_value' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'return_message' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'errors_thrown' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'errors_retry' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'errors_retry_limit' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
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
                'created' => 1603262986,
                'modified' => 1603262986,
                'activation' => 1603262986,
                'expiration' => 1603262986,
                'auto_delete' => 1,
                'wait_for_link' => 1,
                'domain' => 'Lorem ipsum dolor sit amet',
                'name' => 'Lorem ipsum dolor sit amet',
                'worker_link' => 1,
                'worker_name' => 'Lorem ipsum dolor sit amet',
                'class' => 'Lorem ipsum dolor sit amet',
                'method' => 'Lorem ipsum dolor sit amet',
                'parameters' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'status' => 'Lorem ipsum dolor sit amet',
                'started' => 1603262986,
                'completed' => 1603262986,
                'progress_bar' => 1,
                'priority' => 1,
                'return_value' => 1,
                'return_message' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'errors_thrown' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'errors_retry' => 1,
                'errors_retry_limit' => 1,
            ],
        ];
        parent::init();
    }
}

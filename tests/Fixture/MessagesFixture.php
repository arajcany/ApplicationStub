<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MessagesFixture
 */
class MessagesFixture extends TestFixture
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
        'type' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'name' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'description' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'activation' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'expiration' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'auto_delete' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'started' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'completed' => ['type' => 'timestamp', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null],
        'server' => ['type' => 'string', 'length' => 128, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'domain' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'transport' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'profile' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'layout' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'template' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'email_format' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'sender' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'email_from' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'email_to' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'email_cc' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'email_bcc' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'reply_to' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'read_receipt' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'subject' => ['type' => 'string', 'length' => 1024, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'view_vars' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'priority' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'headers' => ['type' => 'string', 'length' => 2048, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'smtp_code' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'smtp_message' => ['type' => 'string', 'length' => 2048, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'lock_code' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'errors_thrown' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'collate' => null],
        'errors_retry' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'errors_retry_limit' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'autoIncrement' => null],
        'beacon_hash' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        'hash_sum' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'precision' => null, 'comment' => null, 'fixed' => null, 'collate' => null],
        '_indexes' => [
            'messages_type_index' => ['type' => 'index', 'columns' => ['type'], 'length' => []],
            'messages_subject_index' => ['type' => 'index', 'columns' => ['subject'], 'length' => []],
            'messages_started_index' => ['type' => 'index', 'columns' => ['started'], 'length' => []],
            'messages_server_index' => ['type' => 'index', 'columns' => ['server'], 'length' => []],
            'messages_sender_index' => ['type' => 'index', 'columns' => ['sender'], 'length' => []],
            'messages_priority_index' => ['type' => 'index', 'columns' => ['priority'], 'length' => []],
            'messages_name_index' => ['type' => 'index', 'columns' => ['name'], 'length' => []],
            'messages_modified_index' => ['type' => 'index', 'columns' => ['modified'], 'length' => []],
            'messages_lock_code_index' => ['type' => 'index', 'columns' => ['lock_code'], 'length' => []],
            'messages_hash_sum_index' => ['type' => 'index', 'columns' => ['hash_sum'], 'length' => []],
            'messages_expiration_index' => ['type' => 'index', 'columns' => ['expiration'], 'length' => []],
            'messages_email_to_index' => ['type' => 'index', 'columns' => ['email_to'], 'length' => []],
            'messages_domain_index' => ['type' => 'index', 'columns' => ['domain'], 'length' => []],
            'messages_created_index' => ['type' => 'index', 'columns' => ['created'], 'length' => []],
            'messages_completed_index' => ['type' => 'index', 'columns' => ['completed'], 'length' => []],
            'messages_beacon_hash_index' => ['type' => 'index', 'columns' => ['beacon_hash'], 'length' => []],
            'messages_auto_delete_index' => ['type' => 'index', 'columns' => ['auto_delete'], 'length' => []],
            'messages_activation_index' => ['type' => 'index', 'columns' => ['activation'], 'length' => []],
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
                'created' => 1615194406,
                'modified' => 1615194406,
                'type' => 'Lorem ipsum dolor sit amet',
                'name' => 'Lorem ipsum dolor sit amet',
                'description' => 'Lorem ipsum dolor sit amet',
                'activation' => 1615194406,
                'expiration' => 1615194406,
                'auto_delete' => 1,
                'started' => 1615194406,
                'completed' => 1615194406,
                'server' => 'Lorem ipsum dolor sit amet',
                'domain' => 'Lorem ipsum dolor sit amet',
                'transport' => 'Lorem ipsum dolor sit amet',
                'profile' => 'Lorem ipsum dolor sit amet',
                'layout' => 'Lorem ipsum dolor sit amet',
                'template' => 'Lorem ipsum dolor sit amet',
                'email_format' => 'Lorem ipsum dolor sit amet',
                'sender' => 'Lorem ipsum dolor sit amet',
                'email_from' => 'Lorem ipsum dolor sit amet',
                'email_to' => 'Lorem ipsum dolor sit amet',
                'email_cc' => 'Lorem ipsum dolor sit amet',
                'email_bcc' => 'Lorem ipsum dolor sit amet',
                'reply_to' => 'Lorem ipsum dolor sit amet',
                'read_receipt' => 'Lorem ipsum dolor sit amet',
                'subject' => 'Lorem ipsum dolor sit amet',
                'view_vars' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'priority' => 1,
                'headers' => 'Lorem ipsum dolor sit amet',
                'smtp_code' => 1,
                'smtp_message' => 'Lorem ipsum dolor sit amet',
                'lock_code' => 1,
                'errors_thrown' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'errors_retry' => 1,
                'errors_retry_limit' => 1,
                'beacon_hash' => 'Lorem ipsum dolor sit amet',
                'hash_sum' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}

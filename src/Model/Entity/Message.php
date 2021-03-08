<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $type
 * @property string|null $name
 * @property string|null $description
 * @property \Cake\I18n\FrozenTime|null $activation
 * @property \Cake\I18n\FrozenTime|null $expiration
 * @property bool|null $auto_delete
 * @property \Cake\I18n\FrozenTime|null $started
 * @property \Cake\I18n\FrozenTime|null $completed
 * @property string|null $server
 * @property string|null $domain
 * @property string|null $transport
 * @property string|null $profile
 * @property string|null $layout
 * @property string|null $template
 * @property string|null $email_format
 * @property array|string|null $sender
 * @property array|string|null $email_from
 * @property array|string|null $email_to
 * @property array|string|null $email_cc
 * @property array|string|null $email_bcc
 * @property array|string|null $reply_to
 * @property array|string|null $read_receipt
 * @property string|null $subject
 * @property array|string|null $view_vars
 * @property int|null $priority
 * @property array|string|null $headers
 * @property int|null $smtp_code
 * @property string|null $smtp_message
 * @property int|null $lock_code
 * @property string|null $errors_thrown
 * @property int|null $errors_retry
 * @property int|null $errors_retry_limit
 * @property string|null $beacon_hash
 * @property string|null $hash_sum
 */
class Message extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'created' => true,
        'modified' => true,
        'type' => true,
        'name' => true,
        'description' => true,
        'activation' => true,
        'expiration' => true,
        'auto_delete' => true,
        'started' => true,
        'completed' => true,
        'server' => true,
        'domain' => true,
        'transport' => true,
        'profile' => true,
        'layout' => true,
        'template' => true,
        'email_format' => true,
        'sender' => true,
        'email_from' => true,
        'email_to' => true,
        'email_cc' => true,
        'email_bcc' => true,
        'reply_to' => true,
        'read_receipt' => true,
        'subject' => true,
        'view_vars' => true,
        'priority' => true,
        'headers' => true,
        'smtp_code' => true,
        'smtp_message' => true,
        'lock_code' => true,
        'errors_thrown' => true,
        'errors_retry' => true,
        'errors_retry_limit' => true,
        'beacon_hash' => true,
        'hash_sum' => true,
    ];
}

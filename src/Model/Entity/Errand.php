<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Errand Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \Cake\I18n\FrozenTime|null $activation
 * @property \Cake\I18n\FrozenTime|null $expiration
 * @property bool|null $auto_delete
 * @property int|null $wait_for_link
 * @property string|null $server
 * @property string|null $domain
 * @property string|null $name
 * @property int|null $worker_link
 * @property string|null $worker_name
 * @property string|null $class
 * @property string|null $method
 * @property string|null $parameters
 * @property string|null $status
 * @property \Cake\I18n\FrozenTime|null $started
 * @property \Cake\I18n\FrozenTime|null $completed
 * @property int|null $progress_bar
 * @property int|null $priority
 * @property int|null $return_value
 * @property string|null $return_message
 * @property string|null $errors_thrown
 * @property int|null $errors_retry
 * @property int|null $errors_retry_limit
 * @property string|null $hash_sum
 */
class Errand extends Entity
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
        'activation' => true,
        'expiration' => true,
        'auto_delete' => true,
        'wait_for_link' => true,
        'server' => true,
        'domain' => true,
        'name' => true,
        'worker_link' => true,
        'worker_name' => true,
        'class' => true,
        'method' => true,
        'parameters' => true,
        'status' => true,
        'started' => true,
        'completed' => true,
        'progress_bar' => true,
        'priority' => true,
        'return_value' => true,
        'return_message' => true,
        'errors_thrown' => true,
        'errors_retry' => true,
        'errors_retry_limit' => true,
        'hash_sum' => true,
    ];
}

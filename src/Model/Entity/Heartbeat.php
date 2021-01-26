<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Heartbeat Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $expiration
 * @property bool|null $auto_delete
 * @property string|null $server
 * @property string|null $domain
 * @property string|null $type
 * @property string|null $context
 * @property int|null $pid
 * @property string|null $name
 * @property string|null $description
 */
class Heartbeat extends Entity
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
        'expiration' => true,
        'auto_delete' => true,
        'server' => true,
        'domain' => true,
        'type' => true,
        'context' => true,
        'pid' => true,
        'name' => true,
        'description' => true,
    ];
}

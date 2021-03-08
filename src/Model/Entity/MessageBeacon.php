<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MessageBeacon Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $beacon_hash
 * @property string|null $beacon_url
 * @property string|null $beacon_data
 */
class MessageBeacon extends Entity
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
        'beacon_hash' => true,
        'beacon_url' => true,
        'beacon_data' => true,
    ];
}

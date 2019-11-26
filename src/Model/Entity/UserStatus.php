<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserStatus Entity
 *
 * @property int $id
 * @property int $rank
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string $description
 * @property string $alias
 * @property string $name_status_icon
 */
class UserStatus extends Entity
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
        'rank' => true,
        'created' => true,
        'modified' => true,
        'name' => true,
        'description' => true,
        'alias' => true,
        'name_status_icon' => true
    ];
}

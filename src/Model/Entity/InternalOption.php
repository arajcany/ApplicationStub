<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * InternalOption Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $option_key
 * @property string $option_value
 * @property bool $is_masked
 * @property bool $apply_mask
 */
class InternalOption extends Entity
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
        'option_key' => true,
        'option_value' => true,
        'is_masked' => true,
        'apply_mask' => true
    ];
}

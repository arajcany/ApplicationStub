<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setting Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $name
 * @property string|null $description
 * @property string $property_group
 * @property string $property_key
 * @property string|null $property_value
 * @property string|null $selections
 * @property string|null $html_select_type
 * @property string|null $match_pattern
 * @property bool|null $is_masked
 */
class Setting extends Entity
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
        'name' => true,
        'description' => true,
        'property_group' => true,
        'property_key' => true,
        'property_value' => true,
        'selections' => true,
        'html_select_type' => true,
        'match_pattern' => true,
        'is_masked' => true
    ];
}

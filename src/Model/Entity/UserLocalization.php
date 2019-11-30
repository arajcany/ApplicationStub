<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserLocalization Entity
 *
 * @property int $id
 * @property int|null $user_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $location
 * @property string|null $locale
 * @property string|null $timezone
 * @property string|null $time_format
 * @property string|null $date_format
 * @property string|null $datetime_format
 * @property string|null $week_start
 *
 * @property \App\Model\Entity\User $user
 */
class UserLocalization extends Entity
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
        'user_id' => true,
        'created' => true,
        'modified' => true,
        'location' => true,
        'locale' => true,
        'timezone' => true,
        'time_format' => true,
        'date_format' => true,
        'datetime_format' => true,
        'week_start' => true,
        'user' => true
    ];
}

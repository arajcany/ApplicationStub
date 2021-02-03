<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Worker Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $server
 * @property string|null $domain
 * @property string|null $name
 * @property string|null $type
 * @property int|null $errand_link
 * @property string|null $errand_name
 * @property \Cake\I18n\FrozenTime|null $appointment_date
 * @property \Cake\I18n\FrozenTime|null $retirement_date
 * @property \Cake\I18n\FrozenTime|null $termination_date
 * @property bool|null $force_retirement
 * @property bool|null $force_shutdown
 * @property int|null $pid
 * @property string|null $background_services_link
 */
class Worker extends Entity
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
        'server' => true,
        'domain' => true,
        'name' => true,
        'type' => true,
        'errand_link' => true,
        'errand_name' => true,
        'appointment_date' => true,
        'retirement_date' => true,
        'termination_date' => true,
        'force_retirement' => true,
        'force_shutdown' => true,
        'pid' => true,
        'background_services_link' => true,
    ];
}

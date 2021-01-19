<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TrackHit Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $url
 * @property string|null $scheme
 * @property string|null $host
 * @property string|null $port
 * @property string|null $path
 * @property string|null $query
 * @property float|null $response_time
 * @property string $data
 */
class TrackHit extends Entity
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
        'url' => true,
        'scheme' => true,
        'host' => true,
        'port' => true,
        'path' => true,
        'query' => true,
        'response_time' => true,
        'data' => true,
    ];
}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Seed Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $activation
 * @property \Cake\I18n\FrozenTime $expiration
 * @property string $token
 * @property string $url
 * @property int $bids
 * @property int $bid_limit
 * @property int $user_link
 * @property int $full_url
 */
class Seed extends Entity
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
        'token' => true,
        'url' => true,
        'bids' => true,
        'bid_limit' => true,
        'user_link' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token'
    ];

    /**
     * Get the Full URL
     *
     * @return string
     */
    protected function _getFullUrl()
    {
        $base = trim(Router::url("/", true), "/");
        $str = $base . $this->_properties['url'];
        return trim($str);
    }
}


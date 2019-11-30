<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $address_1
 * @property string $address_2
 * @property string $suburb
 * @property string $state
 * @property string $post_code
 * @property string $country
 * @property string $mobile
 * @property string $phone
 * @property \Cake\I18n\FrozenTime $activation
 * @property \Cake\I18n\FrozenTime $expiration
 * @property bool $is_confirmed
 * @property int $user_statuses_id
 * @property \Cake\I18n\FrozenTime $password_expiry
 *
 * @property \App\Model\Entity\UserStatus $user_status
 * @property \App\Model\Entity\UserLocalization $user_localization
 * @property \App\Model\Entity\Role[] $roles
 */
class User extends Entity
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
        'email' => true,
        'username' => true,
        'password' => true,
        'first_name' => true,
        'last_name' => true,
        'address_1' => true,
        'address_2' => true,
        'suburb' => true,
        'state' => true,
        'post_code' => true,
        'country' => true,
        'mobile' => true,
        'phone' => true,
        'activation' => true,
        'expiration' => true,
        'is_confirmed' => true,
        'user_statuses_id' => true,
        'password_expiry' => true,
        'user_status' => true,
        'user_localization' => true,
        'roles' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    /**
     * Automatically Hash Passwords
     *
     * @param $password
     * @return mixed
     */
    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * Get the Full Name of a User
     *
     * @return string
     */
    protected function _getFullName()
    {
        return trim($this->_properties['first_name'] . ' ' . $this->_properties['last_name']);
    }
}

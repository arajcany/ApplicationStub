<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TrackUpload Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property string|null $name
 * @property string|null $type
 * @property string|null $tmp_name
 * @property string|null $size
 * @property string|null $error
 * @property string|null $finfo_mime_type
 * @property string|null $username
 * @property string|null $rnd_hash
 * @property int|null $batch_reference
 */
class TrackUpload extends Entity
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
        'name' => true,
        'type' => true,
        'tmp_name' => true,
        'size' => true,
        'error' => true,
        'finfo_mime_type' => true,
        'username' => true,
        'rnd_hash' => true,
        'batch_reference' => true
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Artifact Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string|null $name
 * @property string|null $description
 * @property int|null $size
 * @property string|null $mime_type
 * @property \Cake\I18n\FrozenTime|null $activation
 * @property \Cake\I18n\FrozenTime|null $expiration
 * @property bool|null $auto_delete
 * @property string|null $token
 * @property string|null $url
 * @property string|null $unc
 *
 * @property \App\Model\Entity\ArtifactMetadata[] $artifact_metadata
 */
class Artifact extends Entity
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
        'size' => true,
        'mime_type' => true,
        'activation' => true,
        'expiration' => true,
        'auto_delete' => true,
        'token' => true,
        'url' => true,
        'unc' => true,
        'artifact_metadata' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token',
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ArtifactMetadata Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $artifact_id
 * @property int|null $width
 * @property int|null $height
 * @property string|null $exif
 *
 * @property \App\Model\Entity\Artifact $artifact
 */
class ArtifactMetadata extends Entity
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
        'artifact_id' => true,
        'width' => true,
        'height' => true,
        'exif' => true,
        'artifact' => true,
    ];
}

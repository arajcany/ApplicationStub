<?php

namespace App\Model\Table;

use App\Model\Entity\Artifact;
use arajcany\ToolBox\I18n\TimeMaker;
use arajcany\ToolBox\Utility\Security\Security;
use arajcany\ToolBox\Utility\TextFormatter;
use Cake\Core\Configure;
use Cake\Database\Driver\Sqlite;
use Cake\Datasource\EntityInterface;
use Cake\Filesystem\Folder;
use Cake\I18n\FrozenTime;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use finfo;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

/**
 * Artifacts Model
 *
 * @property \App\Model\Table\ArtifactMetadataTable&\Cake\ORM\Association\HasMany $ArtifactMetadata
 *
 * @property array $successAlerts
 * @property array $dangerAlerts
 * @property array $warningAlerts
 * @property array $infoAlerts
 * @property int $returnCode
 *
 * @method Artifact get($primaryKey, $options = [])
 * @method Artifact newEntity($data = null, array $options = [])
 * @method Artifact[] newEntities(array $data, array $options = [])
 * @method Artifact|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Artifact saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method Artifact patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method Artifact[] patchEntities($entities, array $data, array $options = [])
 * @method Artifact findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ArtifactsTable extends Table
{
    private $successAlerts = [];
    private $dangerAlerts = [];
    private $warningAlerts = [];
    private $infoAlerts = [];
    private $returnCode = 0;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('artifacts');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasOne('ArtifactMetadata', [
            'foreignKey' => 'artifact_id',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->allowEmptyString('name');

        $validator
            ->scalar('description')
            ->maxLength('description', 1024)
            ->allowEmptyString('description');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        $validator
            ->scalar('mime_type')
            ->maxLength('mime_type', 50)
            ->allowEmptyString('mime_type');

        $validator
            ->dateTime('activation')
            ->allowEmptyDateTime('activation');

        $validator
            ->dateTime('expiration')
            ->allowEmptyDateTime('expiration');

        $validator
            ->boolean('auto_delete')
            ->allowEmptyString('auto_delete');

        $validator
            ->scalar('token')
            ->maxLength('token', 50)
            ->allowEmptyString('token');

        $validator
            ->scalar('url')
            ->maxLength('url', 2048)
            ->allowEmptyString('url');

        $validator
            ->scalar('unc')
            ->maxLength('unc', 2048)
            ->allowEmptyString('unc');

        return $validator;
    }

    /**
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    /**
     * @return array
     */
    public function getAllAlerts(): array
    {
        return [
            'success' => $this->successAlerts,
            'danger' => $this->successAlerts,
            'warning' => $this->successAlerts,
            'info' => $this->successAlerts,
        ];
    }

    /**
     * @return array
     */
    public function getSuccessAlerts(): array
    {
        return $this->successAlerts;
    }

    /**
     * @return array
     */
    public function getDangerAlerts(): array
    {
        return $this->dangerAlerts;
    }

    /**
     * @return array
     */
    public function getWarningAlerts(): array
    {
        return $this->warningAlerts;
    }

    /**
     * @return array
     */
    public function getInfoAlerts(): array
    {
        return $this->infoAlerts;
    }

    /**
     * @param array|string $message
     * @return array
     */
    public function addSuccessAlerts($message): array
    {
        if (is_string($message)) {
            $message = [$message];
        }

        $this->successAlerts = array_merge($this->successAlerts, $message);

        return $this->successAlerts;
    }

    /**
     * @param array|string $message
     * @return array
     */
    public function addDangerAlerts($message): array
    {
        if (is_string($message)) {
            $message = [$message];
        }

        $this->dangerAlerts = array_merge($this->dangerAlerts, $message);

        return $this->dangerAlerts;
    }

    /**
     * @param array|string $message
     * @return array
     */
    public function addWarningAlerts($message): array
    {
        if (is_string($message)) {
            $message = [$message];
        }

        $this->warningAlerts = array_merge($this->warningAlerts, $message);

        return $this->warningAlerts;
    }

    /**
     * @param array|string $message
     * @return array
     */
    public function addInfoAlerts($message): array
    {
        if (is_string($message)) {
            $message = [$message];
        }

        $this->infoAlerts = array_merge($this->infoAlerts, $message);

        return $this->infoAlerts;
    }


    /**
     * Pass $data for saving to filesystem and db
     *
     * At its most basic, the $data can be POST array of $_FILE e.g.
     *  [
     *      'tmp_name' => '',
     *      'error' => '', //int
     *      'name' => '',
     *      'type' => '',
     *      'size' => '' //int
     *  ]
     *
     *
     * Which will be converted to the entity format below.
     *  [
     *      'blob' => '', // blob data
     *      'name' => '',
     *      'description' => '',
     *      'size' => '',
     *      'mime_type' => '',
     *      'activation' => [
     *          'year' => '',
     *          'month' => '',
     *          'day' => '',
     *          'hour' => '',
     *          'minute' => '',
     *          'second' => '',
     *      ],
     *      'expiration' => [
     *          'year' => '',
     *          'month' => '',
     *          'day' => '',
     *          'hour' => '',
     *          'minute' => '',
     *          'second' => '',
     *      ],
     *      'auto_delete' => '',
     *      'token' => '',
     *      'url' => '',
     *      'unc' => ''
     *  ]
     *
     * Some basic precedence rules
     * 1) 'tmp_name' overrides 'name'
     * 2) 'type' overrides 'mime_type'
     *
     *
     * @param array $data
     * @return Artifact|array|bool
     */
    public function createArtifact(array $data)
    {
        //check if Artifact already exists
        if (!empty($data['token'])) {
            $artifact = $this->find('all')->where(['token' => $data['token']])->first();
            if (!$artifact) {
                $artifact = $this->newEntity();
            }
        } else {
            $artifact = $this->newEntity();
        }

        $timeObjCurrent = new FrozenTime();

        $defaultData = $this->getDefaultData();

        $data = array_merge($defaultData, $data);


        //fix up activation
        if (is_array($data['activation'])) {
            $data['activation'] = TimeMaker::makeFrozenTimeFromUnknown($data['activation'], TZ, 'UTC');
        }

        //fix up expiration
        if (is_array($data['expiration'])) {
            $data['expiration'] = TimeMaker::makeFrozenTimeFromUnknown($data['expiration'], TZ, 'UTC');
        }

        //fix up token, unc and url
        //only do new unc/url if this is a new Artifact
        if (!empty($artifact->url) && !empty($artifact->unc)) {
            $data['url'] = $artifact->url;
            $data['unc'] = $artifact->unc;
        } elseif (is_string($data['token']) && strlen($data['token']) >= 40) {
            $chunks = $this->str_split_random($data['token'], 2, 3);
            $url = implode('/', $chunks) . '/';
            $unc = implode('\\', $chunks) . '\\';

            $data['url'] = $url;
            $data['unc'] = $unc;
        } elseif (is_string($data['token']) && strlen($data['token']) < 40) {
            $token = sha1($data['token'] . Security::randomBytes(16));
            $chunks = $this->str_split_random($token, 2, 3);
            $url = implode('/', $chunks) . '/';
            $unc = implode('\\', $chunks) . '\\';

            $data['token'] = $token;
            $data['url'] = $url;
            $data['unc'] = $unc;
        } else {
            $token = sha1(Security::randomBytes(1600));
            $chunks = $this->str_split_random($token, 2, 3);
            $url = implode('/', $chunks) . '/';
            $unc = implode('\\', $chunks) . '\\';

            $data['token'] = $token;
            $data['url'] = $url;
            $data['unc'] = $unc;
        }

        switch ($e = $data["error"]) {
            case 0:
                $this->infoAlerts[] = ["code" => $e, "message" => "There is no error, the file uploaded with success."];
                break;
            case 1:
                $uploadMaxFilesize = ini_get('upload_max_filesize');
                $this->dangerAlerts[] = ["code" => $e, "message" => "The uploaded file exceeds the {$uploadMaxFilesize} limit."];
                break;
            case 2:
                $this->dangerAlerts[] = ["code" => $e, "message" => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."];
                break;
            case 3:
                $this->dangerAlerts[] = ["code" => $e, "message" => "The uploaded file was only partially uploaded."];
                break;
            case 4:
                $this->dangerAlerts[] = ["code" => $e, "message" => "No file was uploaded."];
                break;
            case 5:
                $this->dangerAlerts[] = ["code" => $e, "message" => "Unknown error."];
                break;
            case 6:
                $this->dangerAlerts[] = ["code" => $e, "message" => "Missing a temporary folder."];
                break;
            case 7:
                $this->dangerAlerts[] = ["code" => $e, "message" => "Failed to write file to disk."];
                break;
            case 8:
                $this->dangerAlerts[] = ["code" => $e, "message" => "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help."];
                break;
        }


        if (isset($data['tmp_name']) && !empty($data['tmp_name'])) {
            //uploaded data takes precedence over blob data
            $src = $data['tmp_name'];
            $dir = TextFormatter::makeEndsWith(Configure::read('Settings.repo_unc'), "\\") . $data['unc'];
            $fso = new Folder($dir, true);

            if ($fso) {
                $dest = $dir . $data['name'];
                if (!is_dir(pathinfo($dest, PATHINFO_DIRNAME))) {
                    mkdir(pathinfo($dest, PATHINFO_DIRNAME), 0777, true);
                }
                $saveDataResult = move_uploaded_file($src, $dest);
                if ($saveDataResult) {
                    $this->infoAlerts[] = ["code" => 0, "message" => "The file was moved to the destination folder."];
                } else {
                    $this->dangerAlerts[] = ["code" => 1, "message" => "Failed to move the file to the destination folder."];
                }
            } else {
                $saveDataResult = false;
                $this->dangerAlerts[] = ["code" => 1, "message" => "Failed to create the destination folder."];
            }

            //mime type
            if ($data['type']) {
                $data['mime_type'] = $data['type'];
            } else {
                $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type
                $data['mime_type'] = finfo_file($finfo, $data['tmp_name']);
            }
        } elseif (isset($data['blob']) && !empty($data['blob'])) {
            //if a blob $data has been sent
            $src = $data['blob'];
            $dir = TextFormatter::makeEndsWith(Configure::read('Settings.repo_unc'), "\\") . $data['unc'];
            $fso = new Folder($dir, true);

            if ($fso) {
                $dest = $dir . $data['name'];
                if (!is_dir(pathinfo($dest, PATHINFO_DIRNAME))) {
                    mkdir(pathinfo($dest, PATHINFO_DIRNAME), 0777, true);
                }
                $saveDataResult = file_put_contents($dest, $src);
                if ($saveDataResult) {
                    $this->infoAlerts[] = ["code" => 0, "message" => "The file was saved to the destination folder."];
                } else {
                    $this->dangerAlerts[] = ["code" => 1, "message" => "Failed to save the file to the destination folder."];
                }
            } else {
                $saveDataResult = false;
                $this->dangerAlerts[] = ["code" => 1, "message" => "Failed to create the destination folder."];
            }

            //mime type
            if (!isset($data['mime_type']) || empty($data['mime_type'])) {
                $finfo = new finfo(FILEINFO_MIME);
                $data['mime_type'] = explode(";", $finfo->buffer($data['blob']))[0];
            }

            //size
            if (!$data['size']) {
                $data['size'] = mb_strlen($data['blob']);
            }
        } else {
            //no blob data to save
            $this->dangerAlerts[] = ["code" => 1, "message" => "No blob data to save."];
            $saveDataResult = false;
        }

        //save the Entity, only if blob was saved
        if ($saveDataResult) {
            $artifact = $this->patchEntity($artifact, $data);
            $saveEntityResult = $this->save($artifact);


            if ($saveEntityResult) {
                $this->infoAlerts = ["code" => 0, "message" => "Entity saved."];

                $exif = @$this->getCleanExifData($dest);
                if (empty($exif)) {
                    $exif = @getimagesize($dest);
                }

                if (empty($exif)) {
                    $exif = [];
                }

                $artifactMetadata = [
                    'artifact_id' => $artifact->id,
                    'width' => 0,
                    'height' => 0,
                ];

                if (isset($exif['COMPUTED'])) {
                    if (isset($exif['COMPUTED']['Width'])) {
                        $artifactMetadata['width'] = $exif['COMPUTED']['Width'];
                    }
                    if (isset($exif['COMPUTED']['Height'])) {
                        $artifactMetadata['height'] = $exif['COMPUTED']['Height'];
                    }
                } elseif (isset($exif[0]) && isset($exif[1])) {
                    $artifactMetadata['width'] = $exif[0];
                    $artifactMetadata['height'] = $exif[1];
                }

                $artifactMetadata = $this->ArtifactMetadata->newEntity($artifactMetadata);
                $artifactMetadata->exif = $exif;
                $this->ArtifactMetadata->save($artifactMetadata);

            } else {
                $this->dangerAlerts[] = ["code" => 1, "message" => "Entity could not be saved."];
            }
        } else {
            $saveEntityResult = false;
            $this->dangerAlerts[] = ["code" => 1, "message" => "Aborted saving the Entity due to error in saving data."];
        }

        if ($saveDataResult && $saveEntityResult) {
            return $artifact;
        } else {
            return false;
        }
    }

    /**
     * Convenience function, converts ImageResource to its stream
     *
     * @param mixed $name
     * @param Image $imageResource
     * @return Artifact
     */
    public function createArtifactFromImageResource($name, Image $imageResource)
    {
        if (!is_string($name)) {
            $name = $this->serialiseName($name);
            $token = $name;
        } else {
            $token = sha1($name);
        }

        $data = $this->getDefaultData();
        $data['name'] = $name;
        $data['token'] = $token;
        $data['blob'] = $imageResource->stream();

        return $this->createArtifact($data);
    }

    /**
     * @param $artifactOrIdOrToken
     * @param $params
     * @return Artifact|array|EntityInterface|false|null
     */
    public function recompressImage($artifactOrIdOrToken, $params)
    {
        if ($artifactOrIdOrToken instanceof Artifact) {
            $artifact = $artifactOrIdOrToken;
        } else {
            $query = $this->find('all');
            if (is_numeric($artifactOrIdOrToken)) {
                $query = $query->where(['id' => $artifactOrIdOrToken]);
            } elseif (is_string($artifactOrIdOrToken)) {
                $query = $query->where(['token' => $artifactOrIdOrToken]);
            } else {
                return false;
            }

            $artifact = $query->first();
            if (!$artifact) {
                return false;
            }
        }

        if (!exif_imagetype($artifact->full_unc)) {
            return false;
        }

        $defaultParams = [
            'size' => 640,
            'format' => 'jpg',
            'quality' => 90,
        ];

        $params = array_merge($defaultParams, $params);

        $im = new ImageManager();

        try {
            $image = $im->make($artifact->full_unc);
        } catch (\Throwable $e) {
            $image = $this->getImageResource();
        }

        $practicalScope = 1.1;
        $widthInsideScope = ($image->getWidth()) * $practicalScope;
        $heightInsideScope = ($image->getHeight()) * $practicalScope;

        //abort if no sizing is required
        if ($widthInsideScope < $params['size'] && $heightInsideScope < $params['size']) {
            return $artifact;
        }

        $image
            ->widen($params['size'], function ($constraint) {
                $constraint->upsize();
            })
            ->heighten($params['size'], function ($constraint) {
                $constraint->upsize();
            })
            ->encode($params['format'], $params['quality'])
            ->save();

        return $artifact;
    }

    /**
     * Split a string into random chunks
     *
     * @param string $string
     * @param int $minLength
     * @param int $maxLength
     * @return array
     */
    public function str_split_random($string = '', $minLength = 1, $maxLength = 4)
    {
        $l = strlen($string);
        $i = 0;

        $chunks = [];
        while ($i < $l) {
            $r = rand($minLength, $maxLength);
            $chunks[] = substr($string, $i, $r);
            $i += $r;
        }

        return $chunks;
    }

    /**
     * Wrapper function to saveArtifactData() but where the actual image data needs to be magically created.
     * Used when an Artifact is required (e.g. to serve up an image) but the data does not exist yet.
     *
     * @param array $imageData
     * @param array $metadata
     * @return mixed \App\Model\Entity\Artifact|bool
     */
    public function createPlaceholderArtifact(array $imageData = [])
    {
        //setup default $imageData
        $imageDataDefaults = [
            'width' => 64,
            'height' => 64,
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];
        $imageData = array_merge($imageDataDefaults, $imageData);

        //create a token
        $token = sha1(json_encode($imageData));

        //check if Artifact exists based on $token
        $artifact = $this->find('all')->where(['token' => $token])->first();
        if ($artifact) {
            return $artifact;
        }

        //generate the placeholder image
        $imageData['blob'] = $this->getImageResource($imageData);

        //setup $fullData
        $activation = new FrozenTime();
        $expiration = new FrozenTime('+ ' . Configure::read('Settings.data_purge') . ' months');
        $metadata = [
            'name' => "{$imageData['width']}x{$imageData['height']}.{$imageData['format']}",
            'description' => "Placeholder Image {$imageData['width']}px {$imageData['height']}px",
            'activation' => $activation,
            'expiration' => $expiration,
            'auto_delete' => true,
            'token' => $token,
        ];
        $fullData = array_merge($metadata, $imageData);

        $result = $this->createArtifact($fullData);
        return $result;
    }

    /**
     * Return an Intervention Image resource based on the settings
     *
     * @param array $settings
     * @return Image
     */
    public function getImageResource($settings = [])
    {
        $settingsDefault = [
            'width' => 64,
            'height' => 64,
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];
        $s = array_merge($settingsDefault, $settings);

        //mime type overrides the format
        if (isset($s['type'])) {
            $s['format'] = $this->getExtensionFromMimeType($s['type']);
        }

        $manager = new ImageManager();
        $imageResource = $manager
            ->canvas($s['width'], $s['height'], $s['background'])
            ->encode($s['format'], $s['quality']);

        return $imageResource;
    }

    /**
     * Overwrite the delete method so as to include the FSO deletion
     *
     * @param EntityInterface|Artifact $entity
     * @param array $options
     * @return bool|mixed
     */
    public function delete(EntityInterface $entity, $options = [])
    {
        if (is_file($entity->full_unc)) {
            unlink($entity->full_unc);
        }

        return parent::delete($entity, $options);
    }

    /**
     * Wrapper function
     *
     * @param int $limit
     * @return bool|mixed
     */
    public function deleteTopExpired($limit = 50)
    {
        $hardLimit = 500;
        $finalLimit = min($limit, $hardLimit);

        $currentDatetime = new FrozenTime();
        $artifactsToDelete = $this->find('all')
            ->where(['expiration <=' => $currentDatetime->format("Y-m-d H:i:s"), 'auto_delete' => true])
            ->limit($finalLimit);

        $counter = 0;
        foreach ($artifactsToDelete->toArray() as $artifact) {
            $result = $this->delete($artifact);
            if ($result) {
                $counter++;
            }
        }

        return $counter;
    }

    /**
     * Helps to keep the Artifacts table under control.
     * Get specified number of random records and then checks each one for an object in the FS
     * Deletes the Artifact record if no Artifact found.
     *
     * @param int $limit
     * @return bool|mixed
     */
    public function deleteHasMissingArtifact($limit = 200)
    {
        $hardLimit = 500;
        $finalLimit = min($limit, $hardLimit);

        $dbDriver = ($this->getConnection())->getDriver();
        if ($dbDriver instanceof Sqlite) {
            $rndSQL = "id IN (SELECT id FROM artifacts ORDER BY RANDOM() LIMIT {$finalLimit})";
        } else {
            $rndSQL = "id IN (SELECT TOP ({$finalLimit}) id FROM artifacts TABLESAMPLE (1 PERCENT) ORDER BY NEWID())";
        }

        $artifactsToDelete = $this->find('all')
            ->where([$rndSQL])
            ->limit($finalLimit);

        $counter = 0;
        /**
         * @var Artifact $artifact
         */
        foreach ($artifactsToDelete->toArray() as $artifact) {
            if (!is_file($artifact->full_unc)) {
                $result = $this->delete($artifact);
                if ($result) {
                    $counter++;
                }
            }
        }

        return $counter;
    }

    /**
     * Extract the extension from a MIME TYPE string
     *
     * @param string $mimeType
     * @return string
     */
    public function getExtensionFromMimeType($mimeType = "")
    {
        $mimeType = explode("/", $mimeType);
        if (isset($mimeType[1])) {
            $extension = $mimeType[1];
            $extension = strtolower($extension);
            $in = ["jpeg"];
            $out = ["jpg"];
            $extension = str_replace($in, $out, $extension);
            return $extension;
        } else {
            return '';
        }
    }


    /**
     * @param $stream
     * @param null $sections
     * @param false $arrays
     * @param false $thumbnail
     * @return array
     */
    private function getCleanExifData($stream, $sections = null, $arrays = false, $thumbnail = false)
    {
        $exif = @exif_read_data($stream, $sections, $arrays, $thumbnail);

        $allowedExifValues = $this->getAllowedExifValues();

        $exifClean = [];
        if ($exif) {
            foreach ($allowedExifValues as $allowedExifValue) {
                if (isset($exif[$allowedExifValue])) {
                    $exifClean[$allowedExifValue] = $exif[$allowedExifValue];
                }
            }
        }

        return $exifClean;
    }

    /**
     * @return string[]
     */
    private function getAllowedExifValues()
    {
        $exifValues = [
            'FileName',
            'FileDateTime',
            'FileSize',
            'FileType',
            'MimeType',
            'SectionsFound',
            'COMPUTED',
            'DateTime',
            'Artist',
            'Copyright',
            'Author',
            'Exif_IFD_Pointer',
            'THUMBNAIL',
            'DateTimeOriginal',
            'DateTimeDigitized',
            'SubSecTimeOriginal',
            'SubSecTimeDigitized',
            'Company',
        ];

        return $exifValues;
    }

    /**
     * @param mixed $name
     * @return Query
     */
    public function findByName($name)
    {
        $name = $this->serialiseName($name);
        return $this->find('all')->where(['name' => $name]);
    }

    /**
     * @param $name
     * @return string
     */
    public function serialiseName($name)
    {
        if (!is_string($name)) {
            $name = sha1(json_encode($name));
        }

        return $name;
    }

    /**
     * @return array
     */
    private function getDefaultData()
    {
        $timeObjCurrent = new FrozenTime();

        return $defaultData = [
            'tmp_name' => null,
            'blob' => null,
            'error' => 0,
            'name' => null,
            'description' => null,
            'type' => null,
            'size' => null,
            'activation' => (clone $timeObjCurrent),
            'expiration' => (clone $timeObjCurrent)->addMonths(Configure::read("Settings.data_purge")),
            'auto_delete' => true,
            'token' => null,
            'url' => null,
            'unc' => null
        ];
    }


}

<?php


namespace App\Model\Table;


use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Table;

/**
 * Class AppTable
 *
 * @property array $successAlerts
 * @property array $dangerAlerts
 * @property array $warningAlerts
 * @property array $infoAlerts
 * @property int $returnValue
 *
 * @package App\Model\Table
 */
class AppTable extends Table
{
    private $successAlerts = [];
    private $dangerAlerts = [];
    private $warningAlerts = [];
    private $infoAlerts = [];
    private $returnValue = 0;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    }

    /**
     * @param int $returnValue
     */
    public function setReturnValue(int $returnValue)
    {
        $this->returnValue = $returnValue;
    }

    /**
     * @return int
     */
    public function getReturnValue(): int
    {
        return $this->returnValue;
    }

    /**
     * @return array|false|string
     */
    public function getReturnMessage()
    {
        $return = [
            'success' => $this->successAlerts,
            'danger' => $this->successAlerts,
            'warning' => $this->successAlerts,
            'info' => $this->successAlerts,
        ];

        return json_encode($return, JSON_PRETTY_PRINT);
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

    public function massInsert($records)
    {
        $typeMap = $this->getSchema()->typeMap();
        if (isset($typeMap['id'])) {
            unset($typeMap['id']);
        }

        $typeMapUsed = [];
        //loop once to find what fields are being used
        foreach ($records as $i => $record) {
            foreach ($record as $fieldKey => $fieldValue) {
                if (isset($typeMap[$fieldKey])) {
                    $typeMapUsed[$fieldKey] = $typeMap[$fieldKey];
                }
            }
        }

        $defaultFieldsValuesUsed = array_fill_keys(array_keys($typeMapUsed), null);
        $cleanedRecords = [];
        //square up the array
        foreach ($records as $i => $record) {
            //add default columns
            $record = array_merge($defaultFieldsValuesUsed, $record);
            //filter extra columns
            $record = array_intersect_key($record, $defaultFieldsValuesUsed);
            $cleanedRecords[$i] = $record;
        }
        $timeObjCurrent = new FrozenTime();

        $totalCount = count($cleanedRecords);
        $counter = 1;
        $folderQueriesToExec = [];
        $query = null;
        $tableName = $this->getTable();
        $batchLimit = intval(floor(Configure::read("DatabaseLimits.bound_params") / count($typeMapUsed))); //based on bound param limit in SQL
        $batchLimit = max($batchLimit, 2); //cannot have a batch of 1

        foreach ($cleanedRecords as $data) {

            if ($counter % $batchLimit == 1) {
                $query = $this->query()
                    ->into($tableName)
                    ->insert(array_keys($typeMapUsed), $typeMapUsed);
            }

            if (isset($typeMap['created'])) {
                $data['created'] = $timeObjCurrent;
            }

            if (isset($typeMap['modified'])) {
                $data['modified'] = $timeObjCurrent;
            }

            $query->values($data);

            if ($counter % $batchLimit == 0 || $counter == $totalCount) {
                $folderQueriesToExec[] = $query;
            }

            $counter++;
        }

        foreach ($folderQueriesToExec as $query) {
            $query->execute()->closeCursor();
        }

    }

}

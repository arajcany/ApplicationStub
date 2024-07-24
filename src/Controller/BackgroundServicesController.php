<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Component\BackgroundServicesComponent;
use App\Utility\Install\VersionControl;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use App\Model\Table\HeartbeatsTable;
use Exception;
use ZipArchive;

/**
 * BackgroundServices Controller
 *
 * @property BackgroundServicesComponent $BackgroundServices
 * @property HeartbeatsTable $Heartbeats
 * @property string $batchLocation
 * @property string $nssm
 * @property string $isNssm
 */
class BackgroundServicesController extends AppController
{
    public $BackgroundServices;
    public $Heartbeats;
    public $batchLocation;
    public $nssm;
    public $isNssm;

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('BackgroundServices');
        $this->loadModel('Heartbeats');

        $this->batchLocation = ROOT . DS . 'bin' . DS . 'BackgroundServices' . DS;
        $this->nssm = $this->batchLocation . 'nssm.exe';
        if (is_file($this->nssm)) {
            $this->isNssm = true;
        } else {
            $this->isNssm = false;
        }
    }


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $services = $this->BackgroundServices->_getServices();
        $this->set('services', $services);

        $heartbeats = $this->Heartbeats->findLastHeartbeats();
        $this->set('heartbeats', $heartbeats);

        $this->set('isNssm', $this->isNssm);
        if (!$this->isNssm) {
            $this->viewBuilder()->setTemplate('index_nssm');
        }
    }

    /**
     * Create Batch files that aid with Install/Remove of the Windows Serevice
     * @return \Cake\Http\Response|null
     * @throws Exception
     */
    public function downloadNssm()
    {
        $this->set('isNssm', $this->isNssm);
        $nssmUrl = "https://nssm.cc/ci/nssm-2.24-103-gdee49fc.zip";
        $nssmUrlChecksum = "0722c8a775deb4a1460d1750088916f4f5951773";
        $nssmZipBasename = array_reverse(explode("/", $nssmUrl))[0];
        $nssmZipFilename = pathinfo($nssmZipBasename, PATHINFO_FILENAME);
        $nssmZipSaveLocation = $this->batchLocation . $nssmZipBasename;

        //check if download exists
        if (is_file($nssmZipSaveLocation)) {
            $nssmLocalChecksum = sha1_file($nssmZipSaveLocation);
            if ($nssmLocalChecksum == $nssmUrlChecksum) {
                $performDownload = false;
            } else {
                $performDownload = true;
            }
        } else {
            $performDownload = true;
        }

        //download (or not)
        if ($performDownload) {
            $nssmDownload = file_get_contents($nssmUrl);
            $nssmDownloadChecksum = sha1($nssmDownload);
        } else {
            $nssmDownloadChecksum = false;
        }

        //save if ok
        if ($performDownload && $nssmDownloadChecksum == $nssmUrlChecksum) {
            file_put_contents($nssmZipSaveLocation, $nssmDownload);
        } elseif ($performDownload && $nssmDownloadChecksum != $nssmUrlChecksum) {
            $this->Flash->error(__('Sorry, there appears to be an issue with downloading NSSM. Please try again later.'));
            return $this->redirect(['action' => 'index']);
        }

        $osBit = strlen(decbin(~0));
        $exeName = "{$nssmZipFilename}/win{$osBit}/nssm.exe";

        $zip = new ZipArchive();
        $zip->open($nssmZipSaveLocation);

        $filesToExtract = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($exeName == $stat['name']) {
                $filesToExtract[] = $stat['name'];
                $exe = $zip->getFromIndex($stat['index']);
                file_put_contents($this->nssm, $exe);
                break;
            }
        }
        $zip->close();
        unlink($nssmZipSaveLocation);

        $this->Flash->success(__('Downloaded in installed NSSM in {0}', $this->nssm));
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Create Batch files that aid with Install/Remove of the Windows Serevice
     * @return \Cake\Http\Response|null
     * @throws Exception
     */
    public function batch()
    {
        $this->set('isNssm', $this->isNssm);

        if ($this->request->is(['post'])) {

            $result = $this->BackgroundServices->createBackgroundServicesBatchFiles();

            if ($result) {
                $this->Flash->success(__('Batch files created in {0}. Run as Administrator to install Windows Services.', $this->batchLocation));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->success(__('Failed to create the batch files to install Windows Services.', $this->batchLocation));
            }
        }

        return null;
    }

    /**
     * @param $serviceName
     * @return \Cake\Http\Response|null
     */
    public function stop($serviceName)
    {
        $this->BackgroundServices->stop($serviceName);

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param $serviceName
     * @return \Cake\Http\Response|null
     */
    public function start($serviceName)
    {
        $this->BackgroundServices->start($serviceName);

        return $this->redirect(['action' => 'index']);
    }

}

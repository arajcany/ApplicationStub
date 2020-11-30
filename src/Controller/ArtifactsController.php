<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Intervention\Image\ImageManager;

/**
 * Artifacts Controller
 *
 * @property \App\Model\Table\ArtifactsTable $Artifacts
 *
 * @method \App\Model\Entity\Artifact[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArtifactsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $artifacts = $this->paginate($this->Artifacts);

        $this->set(compact('artifacts'));
    }

    /**
     * View method
     *
     * @param string|null $id Artifact id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $artifact = $this->Artifacts->get($id, [
            'contain' => ['ArtifactMetadata']
        ]);

        $this->set('artifact', $artifact);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $artifact = $this->Artifacts->newEntity();
        if ($this->request->is('post')) {

            if (isset($this->request->getData('file')[0])) {
                $files = $this->request->getData('file');
            } else {
                $files = [$this->request->getData('file')];
            }
            foreach ($files as $file) {
                $data = $this->request->getData();
                $data = array_merge($data, $file);

                $result = $this->Artifacts->createArtifact($data);
                if ($result) {
                    $this->Flash->success(__('The artifact has been saved.'));
                } else {
                    $this->Flash->error(__('The artifact could not be saved. Please, try again.'));
                    $errors = $this->Artifacts->getDangerAlerts();
                    $this->Flash->error(json_encode($errors, JSON_PRETTY_PRINT));
                }
            }

            return $this->redirect(['action' => 'index']);
        }
        $this->set(compact('artifact'));
        $this->set('_serialize', ['artifact']);

        return null;
    }

    /**
     * Delete method
     *
     * @param string|null $id Artifact id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $artifact = $this->Artifacts->get($id);
        if ($this->Artifacts->delete($artifact)) {
            $this->Flash->success(__('The artifact has been deleted.'));
        } else {
            $this->Flash->error(__('The artifact could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Fetch an Artifact from the Repository.
     * If Artifact not found, return a default image to avoid 404 errors.
     *
     * @param null $token
     * @return \Cake\Http\Response|null|static
     */
    public function fetch($token = null)
    {
        $settings = [
            'width' => 64,
            'height' => 64,
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];

        if ($token == null) {
            $imgRes = $this->Artifacts->getImageResource($settings);
            $this->response = $this->response->withType($imgRes->mime());
            $this->response = $this->response->withStringBody($imgRes->stream());

            return $this->response;
        } else {
            $artifact = $this->Artifacts->find('all')->where(['token' => $token])->first();

            if ($artifact) {
                $imageString = file_get_contents($artifact->full_unc);
                $this->response = $this->response->withType($artifact->mime_type);
                $this->response = $this->response->withStringBody($imageString);
            } else {
                $imgRes = $this->Artifacts->getImageResource($settings);
                $this->response = $this->response->withType($imgRes->mime());
                $this->response = $this->response->withStringBody($imgRes->stream());
            }

            return $this->response;
        }
    }


    /**
     * Fetch an Artifact from the Repository.
     * If Artifact not found, return a default image to avoid 404 errors.
     *
     * @param null $token
     * @param string $size
     * @param string $format
     * @param int $quality
     * @param null $namePlaceholder
     * @return \Cake\Http\Response|null|static
     */
    public function sample($token = null, $size = 'preview', $format = 'jpg', $quality = 90, $namePlaceholder = null)
    {
        $allowedFormats = [
            'jpeg',
            'jpg',
            'png',
        ];

        $format = strtolower($format);

        if (!in_array($format, $allowedFormats)) {
            $format = 'jpg';
        }

        if ($format == 'jpeg') {
            $format = 'jpg';
        }

        $allowedSizes = [
            'icon',
            'thumbnail',
            'preview',
            'lr',
            'mr',
            'hr',
        ];

        if (is_numeric($size)) {
            $sizePixels = intval($size);
            $sizePixels = min($sizePixels, Configure::read('Settings.repo_size_hr'));
        } else {
            if (!in_array($size, $allowedSizes)) {
                $size = 'preview';
            }
            $sizePixels = Configure::read('Settings.repo_size_' . $size);
        }

        $settings = [
            'width' => 64,
            'height' => 64,
            'background' => '#808080',
            'format' => 'png',
            'quality' => '90',
        ];

        if ($sizePixels) {
            $settings['width'] = $sizePixels;
        }
        if ($sizePixels) {
            $settings['height'] = $sizePixels;
        }
        if ($format) {
            $settings['format'] = $format;
        }
        if ($quality) {
            $settings['quality'] = intval($quality);
        }

        if ($token == null) {
            $imgRes = $this->Artifacts->getImageResource($settings);
            $this->response = $this->response->withType($imgRes->mime());
            $this->response = $this->response->withStringBody($imgRes->stream());

            return $this->response;
        } else {
            $artifact = $this->Artifacts->find('all')->where(['token' => $token])->first();

            if ($artifact) {
                $im = new ImageManager();
                $image = $im->make($artifact->full_unc)
                    ->encode($settings['format'], $settings['quality'])
                    ->resize($settings['width'], $settings['height'], function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });

                $this->response = $this->response->withType($image->mime());
                $this->response = $this->response->withStringBody($image->stream());
            } else {
                $imgRes = $this->Artifacts->getImageResource($settings);
                $this->response = $this->response->withType($imgRes->mime());
                $this->response = $this->response->withStringBody($imgRes->stream());
            }

            return $this->response;
        }
    }
}

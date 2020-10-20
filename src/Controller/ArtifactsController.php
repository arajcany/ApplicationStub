<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;

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
                $result = $this->Artifacts->createArtifact($file);
                if ($result) {
                    $this->Flash->success(__('The artifact has been saved.'));
                } else {
                    $this->Flash->error(__('The artifact could not be saved. Please, try again.'));
                    $errors = $this->Artifacts->getErrorMessages();
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
}

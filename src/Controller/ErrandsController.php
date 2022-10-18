<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Errands Controller
 *
 * @property \App\Model\Table\ErrandsTable $Errands
 *
 * @method \App\Model\Entity\Errand[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ErrandsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            //'contain' => [],
            'limit' => 10,
            'order' => [
                'Errands.id' => 'desc'
            ],
        ];
        $errands = $this->paginate($this->Errands);

        $readyToRun = $this->Errands->getReadyToRunCount();

        $this->set(compact('errands', 'readyToRun'));
    }

    /**
     * View method
     *
     * @param string|null $id Errand id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $errand = $this->Errands->get($id, [
            'contain' => []
        ]);

        $this->set('errand', $errand);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $errand = $this->Errands->newEntity();
        if ($this->request->is('post')) {
            $errand = $this->Errands->patchEntity($errand, $this->request->getData());
            if ($this->Errands->save($errand)) {
                $this->Flash->success(__('The errand has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The errand could not be saved. Please, try again.'));
        }
        $this->set(compact('errand'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Errand id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $errand = $this->Errands->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $errand = $this->Errands->patchEntity($errand, $this->request->getData());
            if ($this->Errands->save($errand)) {
                $this->Flash->success(__('The errand has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The errand could not be saved. Please, try again.'));
        }
        $this->set(compact('errand'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Errand id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $errand = $this->Errands->get($id);
        if ($this->Errands->delete($errand)) {
            $this->Flash->success(__('The errand has been deleted.'));
        } else {
            $this->Flash->error(__('The errand could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Workers Controller
 *
 * @property \App\Model\Table\WorkersTable $Workers
 *
 * @method \App\Model\Entity\Worker[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class WorkersController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $workers = $this->paginate($this->Workers);

        $this->set(compact('workers'));
    }

    /**
     * View method
     *
     * @param string|null $id Worker id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $worker = $this->Workers->get($id, [
            'contain' => []
        ]);

        $this->set('worker', $worker);
    }

    /**
     * Delete method
     *
     * @param string|null $id Worker id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $worker = $this->Workers->get($id);
        if ($this->Workers->delete($worker)) {
            $this->Flash->success(__('The worker has been deleted.'));
        } else {
            $this->Flash->error(__('The worker could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Retire method
     *
     * @param string|null $id Worker id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function retire($id = null)
    {
        if ($this->request->is(['post'])) {
            if (is_numeric($id)) {
                $id = intval($id);
                $result = $this->Workers->retire($id);
                if ($result === 0) {
                    $this->Flash->warning(__("Could not flag WorkerID {0} to retire.", $id));
                } elseif ($result === 1) {
                    $this->Flash->success(__("Flagged WorkerID {0} to retire.", $id));
                }
            } else {
                $this->Flash->error(__("Invalid ID of {0}", $id));
            }
        }

        if ($this->request->is(['get'])) {
            if ($id === 'all') {
                $result = $this->Workers->retireAll();
                if ($result === 0) {
                    $this->Flash->info(__("No Workers to retire."));
                } elseif ($result === 1) {
                    $this->Flash->success(__("Flagged 1 Worker to retire."));
                } elseif ($result > 1) {
                    $this->Flash->success(__("Flagged {0} Workers to retire.", $result));
                }
            } else {
                $this->Flash->error(__("Invalid ID of {0}", $id));
            }
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Clean method
     *
     * @param string|null $id Worker id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function clean()
    {
        if ($this->request->is(['get'])) {
            $result = $this->Workers->clean();
            if ($result === false) {
                $this->Flash->warning(__("There was an issue with cleaning out the Workers."));
            } elseif ($result === 0) {
                $this->Flash->info(__("No Workers to clean out."));
            } elseif ($result === 1) {
                $this->Flash->success(__("Cleaned out 1 Worker."));
            } elseif ($result > 1) {
                $this->Flash->success(__("Cleaned out {0} Workers.", $result));
            }
        }

        return $this->redirect(['action' => 'index']);
    }
}

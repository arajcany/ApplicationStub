<?php

namespace App\Controller;

use arajcany\ToolBox\Utility\Security\Security;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SettingsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $settings = $this->paginate($this->Settings);

        $this->set(compact('settings'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Setting id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $setting = $this->Settings->get($id, [
            'contain' => []
        ]);

        if ($setting->html_select_type == 'multiple') {
            $setting->property_value = explode(',', $setting->property_value);
        }

        if ($setting->is_masked == true) {
            $setting->property_value = Security::decrypt64($setting->property_value);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $dataToSave = $this->request->getData();

            if ($setting->html_select_type == 'multiple') {
                if (is_array($dataToSave['property_value'])) {
                    $dataToSave['property_value'] = implode(',', $dataToSave['property_value']);
                }
            }

            if ($setting->is_masked == true) {
                $dataToSave['property_value'] = Security::encrypt64($dataToSave['property_value']);
            }

            $setting = $this->Settings->patchEntity($setting, $dataToSave);
            if ($this->Settings->save($setting)) {
                $this->Flash->success(__('The setting has been saved.'));

                //update Configure
                $this->Settings->saveSettingsToConfigure(false);

                if (isset($dataToSave['forceRefererRedirect']) && strlen($dataToSave['forceRefererRedirect']) > 10) {
                    return $this->redirect($dataToSave['forceRefererRedirect']);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The setting could not be saved. Please, try again.'));
        }
        $this->set(compact('setting'));
        $this->set('_serialize', ['setting']);

        return null;
    }

    /**
     * Generate seed data
     * @param null $gte
     * @param null $lte
     */
    public function seed($gte = null, $lte = null)
    {
        $seeds = $this->Settings->find('all')
            ->orderAsc('id')
            ->enableHydration(false);

        if ($gte) {
            $seeds = $seeds->where(['id >=' => $gte]);
        }

        if ($lte) {
            $seeds = $seeds->where(['id <=' => $lte]);
        }

        $this->set('seeds', $seeds);
    }
}

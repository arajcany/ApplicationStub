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
            $saveResult = $this->Settings->setSetting($setting, $this->request->getData()['property_value']);

            if ($saveResult) {
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
     * Edit gorup method
     *
     * @param string|null $groupName
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function editGroup($groupName = null)
    {
        if ($groupName === null) {
            return $this->redirect(['action' => 'index']);
        }

        $groupName = strtolower($groupName);

        $settings = $this->Settings->find('all')->where(['property_group' => $groupName]);

        if ($settings->count() <= 0) {
            $this->Flash->error(__('Sorry, no settings found for the {0} group.', $groupName));
            return $this->redirect(['action' => 'index']);
        }

        $this->viewBuilder()->setTemplate('edit_group_' . $groupName);
        $this->set('settings', $settings);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $dataToSave = $this->request->getData();

            $saveResult = false;
            if ($groupName === 'repository') {
                $saveResult = $this->Settings->setRepositoryDetails($dataToSave);
            }

            if ($saveResult) {
                $this->Flash->success(__('The {0} settings has been saved.', $groupName));

                //update Configure
                $this->Settings->saveSettingsToConfigure(false);

                if (isset($dataToSave['forceRefererRedirect']) && strlen($dataToSave['forceRefererRedirect']) > 10) {
                    return $this->redirect($dataToSave['forceRefererRedirect']);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The {0} settings could not be saved. Please, try again.', $groupName));
        }

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

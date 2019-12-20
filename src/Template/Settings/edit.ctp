<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<div class="row">
    <div class="col-12 ml-auto mr-auto">

        <div class="settings form large-9 medium-8 columns content">
            <?php
            $formOpts = [
            ];

            $labelClass = 'form-control-label';
            $inputClass = 'form-control mb-4';

            $defaultOptions = [
                'label' => [
                    'class' => $labelClass,
                ],
                'options' => null,
                'class' => $inputClass,
                'disabled' => true,
            ];
            ?>
            <?= $this->Form->create($setting, $formOpts) ?>
            <h3><?= __("Edit {0}", $setting->name) ?></h3>
            <fieldset>
                <?php
                echo $this->Form->control('name', $defaultOptions);
                echo $this->Form->control('description', $defaultOptions);

                $selectOpts = json_decode($setting->selections, JSON_FORCE_OBJECT);

                if ($setting->property_key == 'datetime_format') {
                    $dtObj = new FrozenTime('now', TZ);
                    foreach ($selectOpts as $k => $selectOpt) {
                        $selectOpts[$k] = $dtObj->i18nFormat($k);
                    }
                }

                if ($setting->html_select_type == 'multiple') {
                    $multiple = true;
                    $size = count($selectOpts);
                } else {
                    $multiple = false;
                    $size = 1;
                }

                if ($setting->is_masked == true) {
                    $type = 'password';
                } else {
                    $type = null;
                }

                $opts = [
                    'select' => ['class' => "form-control"],
                    'options' => $selectOpts,
                    'multiple' => $multiple,
                    'size' => $size,
                    'type' => $type,
                    'required' => false,
                    'disabled' => false,
                    'hiddenField' => false,
                ];

                echo $this->Form->hidden('property_value', ['value' => '']);
                echo $this->Form->control('property_value', array_merge($defaultOptions, $opts));
                ?>
            </fieldset>
            <?php
            $options = [
                'class' => 'btn btn-secondary float-left'
            ];
            echo $this->Html->link(__('Back'), $this->request->referer(), $options);

            $options = [
                'class' => 'btn btn-primary float-right'
            ];
            echo $this->Form->button(__('Submit'), $options);
            ?>
            <?= $this->Form->end() ?>
        </div>

    </div>
</div>

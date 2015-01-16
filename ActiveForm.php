<?php

namespace dee\easyui;

use yii\helpers\Json;

/**
 * ActiveForm
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @inheritdoc
     */
    public $fieldClass = 'yii\widgets\ActiveField';

    /**
     * 
     * @var array 
     */
    public $clientOptions = [];

    /**
     * @inheritdoc
     */
    protected function getClientOptions()
    {
        return $this->clientOptions;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->enableClientScript) {
            $view = $this->getView();
            EasyuiAsset::register($view);
            $id = $this->options['id'];
            $options = Json::encode($this->getClientOptions());
            $view->registerJs("jQuery('#$id').form($options);");
        }
        $this->enableClientScript = false;
        parent::run();
    }
}
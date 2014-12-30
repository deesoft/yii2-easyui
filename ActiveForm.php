<?php

namespace mdm\easyui;

/**
 * ActiveForm
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class ActiveForm extends \yii\widgets\ActiveForm
{

    public function run()
    {
        if ($this->enableClientScript) {
            // my own lines
        }
        $this->enableClientScript = false;
        parent::run();
    }
}
<?php

namespace dee\easyui\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Html
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Html extends \yii\helpers\BaseHtml
{

    /**
     * @inheritdoc
     */
    public static function getInputName($model, $attribute)
    {
        return str_replace(['[]', '][', '[', ']', ' '], ['', '-', '-', '', '-'], $attribute);
    }
}
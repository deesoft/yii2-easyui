<?php

namespace dee\easyui;

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
    const PREFIX_ID = 'ei';

    private static $_count = 1;
    public static $plugins;

    public static function registerPlugin($id, $plugin, $options = [], $view = null)
    {
        $view = $view? : Yii::$app->getView();
        EasyuiAsset::register($view);
        $options = Json::encode($options);
        $js = "jQuery('#$id').$plugin($options);";
        $view->registerJs($js);
    }

    /**
     * @inheritdoc
     */
    public static function input($type, $name = null, $value = null, $options = [])
    {
        if ($easyui = ArrayHelper::remove($options, 'easyui')) {
            if (empty($options['id'])) {
                $options['id'] = static::PREFIX_ID . (self::$_count++);
            }
            if (is_string($easyui)) {
                $plugin = $easyui;
                $opts = [];
            } else {
                if (!($plugin = ArrayHelper::remove($easyui, '_plugin'))) {
                    $plugin = ArrayHelper::remove($easyui, 'plugin');
                }
                $opts = $easyui;
            }
            $view = ArrayHelper::getValue($options, 'view');
            if ($view && $view instanceof \yii\web\View) {
                unset($options['view']);
            } else {
                $view = null;
            }
            static::registerPlugin($options['id'], $plugin, $opts, $view);
        }
        return parent::input($type, $name, $value, $options);
    }

    public static function activeInput($type, $model, $attribute, $options = array())
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $value = isset($options['value']) ? $options['value'] : static::getAttributeValue($model, $attribute);
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }
        return static::input($type, $name, $value, $options);
    }

    public static function combobox($name, $value = null, $items = [], $options = [])
    {
        $data = [];
        foreach ($items as $key => $text) {
            $selected = !($value === null) && (is_scalar($value) ? $key == $value : in_array($key, $value));
            $data[] = ['id' => $key, 'value' => $text, 'selected' => $selected];
        }

        $options = ArrayHelper::merge($options, [
                'easyui' => [
                    'plugin' => 'combobox',
                    'data' => $data
                ]
        ]);
        return static::input('input', $name, $value, $options);
    }

    public static function activeCombobox($model, $attribute, $items, $options = array())
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $value = isset($options['value']) ? $options['value'] : static::getAttributeValue($model, $attribute);
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }
        static::combobox($name, $value, $items, $options);
    }

    public function plugin($plugin, $options = [], $htmlOptions = [])
    {
        if (!isset(static::$plugins[$plugin])) {
            
        }
        $tag = static::$plugins[$plugin]['tag'];
        if (empty($htmlOptions['id'])) {
            $htmlOptions['id'] = static::PREFIX_ID . (self::$_count++);
        }
        static::registerPlugin($htmlOptions['id'], $plugin, $options);
        return static::tag($tag, $content, $htmlOptions);
    }
}
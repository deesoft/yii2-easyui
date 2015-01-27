<?php

namespace dee\easyui;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Easyui
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class Easyui
{
    const PREFIX_ID = 'ee';

    public static $plugins;

    public static function normalizeOptions(&$options)
    {
// url
        foreach (['url', 'href'] as $key) {
            if (isset($options[$key])) {
                $options[$key] = Url::to($options[$key]);
            }
        }
    }

    public static function registerPlugin($id, $plugin, $options = [], $view = null)
    {
        $view = $view? : Yii::$app->getView();
        EasyuiAsset::register($view);
        static::normalizeOptions($options);
        $options = Json::encode($options);
        $js = "jQuery('#$id').$plugin($options);";
        $view->registerJs($js);
    }

    public static function input($plugin, $name, $value = null, $options = [])
    {
        $htmlOptions = ArrayHelper::remove($options, 'htmlOptions', []);
        if (empty($htmlOptions['id'])) {
            $htmlOptions['id'] = static::PREFIX_ID . (self::$_count++);
        }
        static::registerPlugin($htmlOptions['id'], $plugin, $options);
        return Html::input('input', $name, $value, $htmlOptions);
    }

    public static function textbox($name, $value = null, $options = [])
    {
        return static::input('textbox', $name, $value, $options);
    }

    public static function password($name, $value = null, $options = [])
    {
        $options['htmlOptions']['type'] = 'password';
        return static::input('textbox', $name, $value, $options);
    }

    public static function combobox($name, $value = null, $options = [])
    {

        return static::input('combobox', $name, $value, $options, $htmlOptions);
    }

    public static function datebox($name, $value = null, $options = [])
    {
        return static::input('datebox', $name, $value, $options);
    }
    private static $_states = [];

    public static function beginPlugin($plugin, $options = [])
    {
        if (isset(static::$plugins[$plugin])) {
            $opts = static::$plugins[$plugin];
            $tag = ArrayHelper::getValue($opts, 'tag', 'div');
            if (!empty($opts['options'])) {
                $options = ArrayHelper::merge($opts['options'], $options);
            }
            $htmlOptions = ArrayHelper::remove($options, 'htmlOptions', []);
            if (empty($htmlOptions['id'])) {
                $htmlOptions['id'] = static::PREFIX_ID . (self::$_count++);
            }
            static::registerPlugin($htmlOptions['id'], $plugin, $options);
            array_push(self::$_states, [$plugin, $tag]);
            return Html::beginTag($tag, $htmlOptions);
        }
    }

    public static function endPlugin($plugin)
    {
        $class = get_called_class();
        if (!empty(self::$_states)) {
            list($name, $tag) = array_pop(self::$_states);
            if ($plugin === $name) {
                return Html::endTag($tag);
            } else {
                throw new InvalidCallException("Expecting {$class}::endPlugin({$name}), found {$class}::endPlugin({$plugin})");
            }
        } else {
            throw new InvalidCallException("Unexpected {$class}::endPlugin() call. A matching beginPlugin() is not found.");
        }
    }

    public static function beginPanel($options = [])
    {
        return static::beginPlugin('panel', $options);
    }

    public static function endPanel()
    {
        return static::endPlugin('panel');
    }
}
<?php

namespace dee\easyui\helpers;

use Yii;
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

    /**
     * @var array 
     */
    public static $plugins;

    /**
     * @var array 
     */
    private static $_states = [];

    public static function normalizeListItem($items = [])
    {
        if (ArrayHelper::isIndexed($items, true)) {
            return $items;
        }
        $data = [];
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $_k => $_v) {
                    $data[] = [
                        'value' => $_k,
                        'text' => $_v,
                        'group' => $key,
                    ];
                }
            } else {
                $data[] = [
                    'value' => $key,
                    'text' => $value,
                ];
            }
        }
        return $data;
    }

    public static function normalizeOptions(&$options)
    {
        foreach (['url', 'href'] as $key) {
            if (isset($options[$key])) {
                $options[$key] = Url::to($options[$key]);
            }
        }
    }

    public static function registerPlugin(&$options, $name, $clientOptions = [], $view = null)
    {
        $view = $view? : Yii::$app->getView();
        EasyuiAsset::register($view);
        if (!empty($clientOptions)) {
            if (empty($options['id'])) {
                $options['id'] = static::PREFIX_ID . (self::$_count++);
            }
            static::normalizeOptions($clientOptions);
            $s = Json::encode($clientOptions);
            $js = "jQuery('#{$options['id']}').$name($s);";
            $view->registerJs($js);
        } else {
            Html::addCssClass($options, 'easyui-' . $name);
        }
    }

    public static function input($plugin, $name, $value = null, $options = [])
    {
        $clientOptions = ArrayHelper::remove($options, 'clientOptions', []);
        static::registerPlugin($options, $plugin, $clientOptions);
        $type = isset($options['type']) ? $options['type'] : null;
        return Html::input($type, $name, $value, $options);
    }

    public static function textbox($name, $value = null, $options = [])
    {
        $options['type'] = 'text';
        return static::input('textbox', $name, $value, $options);
    }

    public static function password($name, $value = null, $options = [])
    {
        $options['type'] = 'password';
        return static::input('textbox', $name, $value, $options);
    }

    public static function combobox($name, $value = null, $items = null, $options = [])
    {
        if ($items !== null) {
            $options['clientOptions']['data'] = static::normalizeListItem($items);
        }
        return static::input('combobox', $name, $value, $options);
    }

    public static function datebox($name, $value = null, $options = [])
    {
        return static::input('datebox', $name, $value, $options);
    }

    public static function activeInput($type, $model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : Html::getInputName($model, $attribute);
        $value = isset($options['value']) ? $options['value'] : Html::getAttributeValue($model, $attribute);
        if (!array_key_exists('id', $options)) {
            $options['id'] = Html::getInputId($model, $attribute);
        }
        return static::input($type, $name, $value, $options);
    }

    public static function activeTextbox($model, $attribute, $options = [])
    {
        return static::activeInput('textbox', $model, $attribute, $options);
    }

    public static function activePassword($model, $attribute, $options = [])
    {
        $options['type'] = 'password';
        return static::activeInput('textbox', $model, $attribute, $options);
    }

    public static function activeCombobox($model, $attribute, $items = null, $options = [])
    {
        if ($items !== null) {
            $options['clientOptions']['data'] = static::normalizeListItem($items);
        }
        return static::activeInput('combobox', $model, $attribute, $options);
    }

    public static function activeDatebox($model, $attribute, $options = [])
    {
        return static::activeInput('datebox', $model, $attribute, $options);
    }

    public static function plugin($name, $content = '', $options = [])
    {
        if (isset(static::$plugins[$name])) {
            $opts = static::$plugins[$name];
            $tag = ArrayHelper::getValue($opts, 'tag', 'div');
            $clientOptions = ArrayHelper::remove($options, 'clientOptions', []);
            if (!empty($opts['options'])) {
                $clientOptions = ArrayHelper::merge($opts['options'], $clientOptions);
            }

            static::registerPlugin($options, $name, $clientOptions);
            return Html::tag($tag, $content, $options);
        }
    }

    public static function beginPlugin($name, $options = [])
    {
        if (isset(static::$plugins[$name])) {
            $opts = static::$plugins[$name];
            $tag = ArrayHelper::getValue($opts, 'tag', 'div');
            $clientOptions = ArrayHelper::remove($options, 'clientOptions', []);
            if (!empty($opts['options'])) {
                $clientOptions = ArrayHelper::merge($opts['options'], $clientOptions);
            }

            static::registerPlugin($options, $name, $clientOptions);
            array_push(self::$_states, [$name, $tag]);
            return Html::beginTag($tag, $options);
        }
    }

    public static function endPlugin($name)
    {
        $class = get_called_class();
        if (!empty(self::$_states)) {
            list($state, $tag) = array_pop(self::$_states);
            if ($name === $state) {
                return Html::endTag($tag);
            } else {
                throw new InvalidCallException("Expecting {$class}::endPlugin({$state}), found {$class}::endPlugin({$name})");
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
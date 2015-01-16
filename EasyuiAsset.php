<?php

namespace dee\easyui;

use Yii;

/**
 * EasyuiAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class EasyuiAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dee/easyui/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'themes/icon.css',
        'themes/color.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'jquery.easyui.min.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    /**
     * @var string|\Closure 
     */
    public $theme = 'default';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->applyTheme();
        parent::init();
    }

    protected function applyTheme()
    {
        if (is_string($this->theme) || $this->theme === false) {
            $theme = $this->theme;
        } else {
            $theme = call_user_func($this->theme, $this);
        }
        if (!empty($theme)) {
            array_unshift($this->css, strtr('themes/{theme}/easyui.css', ['{theme}' => $theme]));
        }
    }
}
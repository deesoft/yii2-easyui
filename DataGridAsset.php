<?php

namespace dee\easyui;

/**
 * DataGridAsset
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DataGridAsset extends \yii\web\AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@dee/easyui/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'datagrid-filter.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'dee\easyui\EasyuiAsset',
    ];

}
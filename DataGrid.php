<?php

namespace dee\easyui;

use Yii;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * DataGrid
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DataGrid extends Widget
{
    public $columns = [];
    public $url = ['source'];
    public $enableFilter = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $view = $this->getView();
        $this->registerWidget('datagrid');
        $jsFiles = [];
        if ($this->enableFilter) {
            $jsFiles[] = '/datagrid-filter.js';
        }
        if (count($jsFiles)) {
            list(, $baseUrl) = $view->getAssetManager()->publish('@dee/easyui/assets');
            foreach ($jsFiles as $js) {
                $view->registerJsFile($baseUrl . $js, ['depends' => 'dee\easyui\EasyuiAsset']);
            }
        }
        if ($this->enableFilter) {
            $view->registerJs("jQuery('#{$this->options['id']}').datagrid('enableFilter');");
        }
        echo Html::tag('table', '', $this->options);
    }

    /**
     * @inheritdoc
     */
    protected function getClientOptions()
    {
        $this->normalizeColumns();
        $this->clientOptions['url'] = Url::to($this->url);
        return array_merge([
            'method'=>'get',
            
        ], $this->clientOptions);
    }

    /**
     * 
     * @return array
     */
    protected function normalizeColumns()
    {
        $columns = [];
        $frozenColumns = [];
        $filters = [];
        foreach ($this->columns as $column) {
            if (is_string($column)) {
                $column = $this->createColumn($column);
            }
            $field = $column['field'];
            $filter = ArrayHelper::remove($column, 'filter', []);
            if ($filter !== false) {
                $filter['field'] = $field;
                $filters[] = $filter;
            }

            if (ArrayHelper::remove($column, 'frozen')) {
                $frozenColumns[] = $column;
            } else {
                $columns[] = $column;
            }
        }
        $this->clientOptions['columns'] = $columns;
        $this->clientOptions['frozenColumns'] = $frozenColumns;
        if ($this->enableFilter) {
            $this->clientOptions['filterRules'] = $filters;
        }
    }

    protected function createColumn($text)
    {
        if (!preg_match('/^([^:]+)(:(.*))?$/', $text, $matches)) {
            throw new InvalidConfigException('The column must be specified in the format of "attribute" or "attribute:label"');
        }
        return[
            'field' => $matches[1],
            'title' => isset($matches[3]) ? $matches[3] : $matches[1],
        ];
    }
}